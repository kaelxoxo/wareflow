<?php
class Tenant {
    public static function create(string $name): string {
        $slug = self::slug($name);
        return DB::insert('INSERT INTO tenants (name, slug) VALUES (?, ?)', [$name, $slug]);
    }

    public static function find(int $id): ?array {
        return DB::row('SELECT * FROM tenants WHERE id = ?', [$id]);
    }

    public static function update(int $id, array $data): void {
        DB::execute('UPDATE tenants SET name = ?, settings = ? WHERE id = ?',
            [$data['name'], isset($data['settings']) ? json_encode($data['settings']) : null, $id]);
    }

    public static function setStripeCustomer(int $id, string $customerId): void {
        DB::execute('UPDATE tenants SET stripe_customer_id = ? WHERE id = ?', [$customerId, $id]);
    }

    public static function updateSubscription(int $id, array $d): void {
        DB::execute(
            'UPDATE tenants SET stripe_subscription_id = ?, subscription_status = ?, subscription_period_end = ? WHERE id = ?',
            [$d['stripe_subscription_id'] ?? null, $d['subscription_status'] ?? 'none', $d['subscription_period_end'] ?? null, $id]
        );
    }

    public static function findByStripeCustomer(string $customerId): ?array {
        return DB::row('SELECT * FROM tenants WHERE stripe_customer_id = ?', [$customerId]);
    }

    private static function slug(string $name): string {
        $s = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
        $s = trim($s, '-');
        $base = $s; $i = 2;
        while (DB::row('SELECT id FROM tenants WHERE slug = ?', [$s])) {
            $s = $base . '-' . $i++;
        }
        return $s;
    }
}
