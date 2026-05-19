<?php
class StripeApi {
    private static function sk(): string { return STRIPE_SK; }

    public static function post(string $endpoint, array $params): array {
        $ch = curl_init('https://api.stripe.com/v1' . $endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($params),
            CURLOPT_USERPWD        => self::sk() . ':',
            CURLOPT_HTTPHEADER     => ['Stripe-Version: 2024-06-20'],
            CURLOPT_TIMEOUT        => 15,
        ]);
        $body = curl_exec($ch);
        curl_close($ch);
        return json_decode($body ?: '{}', true) ?: [];
    }

    public static function get(string $endpoint): array {
        $ch = curl_init('https://api.stripe.com/v1' . $endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD        => self::sk() . ':',
            CURLOPT_HTTPHEADER     => ['Stripe-Version: 2024-06-20'],
            CURLOPT_TIMEOUT        => 15,
        ]);
        $body = curl_exec($ch);
        curl_close($ch);
        return json_decode($body ?: '{}', true) ?: [];
    }
}

class BillingController {
    public function index(): void {
        Auth::guard('view_inventory');
        $tenant = Tenant::find(Auth::tenantId());
        view('billing/index', compact('tenant'));
    }

    public function checkout(): void {
        Auth::guard('view_inventory');
        Auth::verifyCsrf();

        $plan = $_POST['plan'] ?? '';
        if (!isset(PLANS[$plan]) || $plan === 'starter') {
            flash('error', 'Invalid plan selected.');
            redirect('/billing');
        }
        $planConfig = PLANS[$plan];

        $tid    = Auth::tenantId();
        $tenant = Tenant::find($tid);

        // Already has an active subscription — nothing to do
        if (in_array($tenant['subscription_status'] ?? 'none', ['active', 'trialing'])) {
            flash('error', 'You already have an active subscription.');
            redirect('/billing');
        }

        // Ensure Stripe customer exists
        if (empty($tenant['stripe_customer_id'])) {
            $user     = Auth::user();
            $customer = StripeApi::post('/customers', [
                'email'                => $user['email'],
                'name'                 => $tenant['name'],
                'metadata[tenant_id]'  => $tid,
            ]);
            if (empty($customer['id'])) {
                flash('error', 'Could not create billing account. Please try again.');
                redirect('/billing');
            }
            Tenant::setStripeCustomer($tid, $customer['id']);
            $customerId = $customer['id'];
        } else {
            $customerId = $tenant['stripe_customer_id'];
        }

        // Create Stripe Checkout Session (hosted page)
        $session = StripeApi::post('/checkout/sessions', [
            'mode'                                               => 'subscription',
            'customer'                                           => $customerId,
            'success_url'                                        => url('/billing/success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'                                         => url('/billing/cancel'),
            'line_items[0][price_data][currency]'                => 'usd',
            'line_items[0][price_data][product_data][name]'      => $planConfig['stripe_label'],
            'line_items[0][price_data][unit_amount]'             => $planConfig['cents'],
            'line_items[0][price_data][recurring][interval]'     => 'month',
            'line_items[0][quantity]'                            => 1,
            'metadata[tenant_id]'                                => $tid,
            'metadata[plan]'                                     => $plan,
        ]);

        if (empty($session['url'])) {
            flash('error', 'Could not start checkout. Please try again.');
            redirect('/billing');
        }

        header('Location: ' . $session['url']);
        exit;
    }

    public function success(): void {
        Auth::guard('view_inventory');
        $sessionId = $_GET['session_id'] ?? '';
        if ($sessionId) {
            $session = StripeApi::get('/checkout/sessions/' . rawurlencode($sessionId));
            $tid = Auth::tenantId();
            if (!empty($session['customer'])) {
                $tenant = Tenant::find($tid);
                if (empty($tenant['stripe_customer_id'])) {
                    Tenant::setStripeCustomer($tid, $session['customer']);
                }
            }
            if (!empty($session['subscription'])) {
                $sub  = StripeApi::get('/subscriptions/' . rawurlencode($session['subscription']));
                $plan = $session['metadata']['plan'] ?? 'pro';
                if (!empty($sub['id'])) {
                    Tenant::updateSubscription($tid, $this->subPayload($sub, null, $plan));
                }
            }
        }
        flash('success', 'Payment received! Your subscription is now active.');
        redirect('/billing');
    }

    public function cancel(): void {
        Auth::guard('view_inventory');
        flash('error', 'Checkout cancelled. No charge was made.');
        redirect('/billing');
    }

    public function portal(): void {
        Auth::guard('view_inventory');
        Auth::verifyCsrf();

        $tenant = Tenant::find(Auth::tenantId());
        if (empty($tenant['stripe_customer_id'])) {
            redirect('/billing');
        }

        $session = StripeApi::post('/billing_portal/sessions', [
            'customer'   => $tenant['stripe_customer_id'],
            'return_url' => url('/billing'),
        ]);

        if (empty($session['url'])) {
            flash('error', 'Could not open billing portal. Please try again.');
            redirect('/billing');
        }

        header('Location: ' . $session['url']);
        exit;
    }

    // Stripe sends webhooks here — no CSRF, no session auth
    public function webhook(): void {
        $payload   = file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $secret    = STRIPE_WEBHOOK_SECRET;

        if ($secret && !$this->verifySignature($payload, $sigHeader, $secret)) {
            http_response_code(400);
            exit('Invalid signature');
        }

        $event = json_decode($payload, true) ?: [];
        $type  = $event['type'] ?? '';
        $obj   = $event['data']['object'] ?? [];

        switch ($type) {
            case 'checkout.session.completed':
                if (($obj['mode'] ?? '') === 'subscription') {
                    $tenantId = (int)($obj['metadata']['tenant_id'] ?? 0);
                    $subId    = $obj['subscription'] ?? '';
                    $plan     = $obj['metadata']['plan'] ?? 'pro';
                    if ($tenantId && $subId) {
                        $sub = StripeApi::get('/subscriptions/' . $subId);
                        Tenant::updateSubscription($tenantId, $this->subPayload($sub, null, $plan));
                    }
                }
                break;

            case 'customer.subscription.updated':
                $this->syncFromSub($obj);
                break;

            case 'customer.subscription.deleted':
                $this->syncFromSub($obj, 'canceled');
                break;

            case 'invoice.payment_failed':
                $cid    = $obj['customer'] ?? '';
                $tenant = $cid ? Tenant::findByStripeCustomer($cid) : null;
                if ($tenant) {
                    Tenant::updateSubscription($tenant['id'], [
                        'stripe_subscription_id' => $obj['subscription'] ?? null,
                        'subscription_status'    => 'past_due',
                        'subscription_period_end' => null,
                    ]);
                }
                break;
        }

        http_response_code(200);
        exit('ok');
    }

    private function syncFromSub(array $sub, ?string $forceStatus = null): void {
        $cid    = $sub['customer'] ?? '';
        $tenant = $cid ? Tenant::findByStripeCustomer($cid) : null;
        if (!$tenant) return;
        $forcePlan = ($forceStatus === 'canceled') ? 'starter' : null;
        Tenant::updateSubscription($tenant['id'], $this->subPayload($sub, $forceStatus, $forcePlan));
    }

    private function subPayload(array $sub, ?string $forceStatus = null, ?string $forcePlan = null): array {
        $payload = [
            'stripe_subscription_id'  => $sub['id'] ?? null,
            'subscription_status'     => $forceStatus ?? ($sub['status'] ?? 'none'),
            'subscription_period_end' => isset($sub['current_period_end'])
                ? date('Y-m-d H:i:s', (int)$sub['current_period_end']) : null,
        ];
        if ($forcePlan !== null) {
            $payload['plan'] = $forcePlan;
        }
        return $payload;
    }

    private function verifySignature(string $payload, string $sigHeader, string $secret): bool {
        if (!preg_match('/t=(\d+).*?v1=([a-f0-9]+)/', $sigHeader, $m)) return false;
        $ts  = $m[1];
        $sig = $m[2];
        if (abs(time() - (int)$ts) > 300) return false; // 5-min tolerance
        return hash_equals(hash_hmac('sha256', $ts . '.' . $payload, $secret), $sig);
    }
}
