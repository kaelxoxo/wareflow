<?php
class SettingsController {
    public function index(): void {
        Auth::guard('manage_settings');
        $tid     = Auth::tenantId();
        $tenant  = Tenant::find($tid);
        $user    = Auth::user();
        $categories = Category::all($tid);
        view('settings/index', compact('tenant', 'user', 'categories'));
    }

    public function updateTenant(): void {
        Auth::guard('manage_settings');
        Auth::verifyCsrf();
        $tid  = Auth::tenantId();
        $name = trim($_POST['company_name'] ?? '');
        if (!$name || strlen($name) > 100) {
            flash('error', 'Company name must be 1–100 characters.');
            redirect('/settings');
        }
        Tenant::update($tid, ['name' => $name]);
        ActivityLog::log($tid, Auth::id(), 'settings.updated', 'tenant', $tid);
        flash('success', 'Company settings updated.');
        redirect('/settings');
    }

    public function updateProfile(): void {
        Auth::guard();
        Auth::verifyCsrf();
        $tid   = Auth::tenantId();
        $uid   = Auth::id();
        $name  = trim($_POST['name']  ?? '');
        $email = trim($_POST['email'] ?? '');

        $errors = [];
        if (!$name || strlen($name) > 100) $errors[] = 'Name must be 1–100 characters.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150)
            $errors[] = 'A valid email address is required (max 150 characters).';

        if ($errors) { flash('error', implode(' ', $errors)); redirect('/settings'); }

        // Prevent email conflict with another member in this workspace
        $conflict = User::byEmail($email, $tid);
        if ($conflict && (int)$conflict['id'] !== $uid) {
            flash('error', 'This email is already used by another member in this workspace.');
            redirect('/settings');
        }

        User::update($uid, $tid, ['name' => $name, 'email' => $email]);
        flash('success', 'Profile updated.');
        redirect('/settings');
    }

    public function updatePassword(): void {
        Auth::guard();
        Auth::verifyCsrf();
        $tid     = Auth::tenantId();
        $uid     = Auth::id();
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $user = Auth::user();
        if (!password_verify($current, $user['password_hash'])) {
            flash('error', 'Current password is incorrect.');
            redirect('/settings');
        }
        if (strlen($new) < 8) { flash('error', 'New password must be at least 8 characters.'); redirect('/settings'); }
        if ($new !== $confirm){ flash('error', 'Passwords do not match.'); redirect('/settings'); }

        User::update($uid, $tid, ['password_hash' => password_hash($new, PASSWORD_BCRYPT)]);
        flash('success', 'Password changed.');
        redirect('/settings');
    }

    public function createCategory(): void {
        Auth::guard('manage_settings');
        Auth::verifyCsrf();
        $tid   = Auth::tenantId();
        $name  = trim($_POST['cat_name']  ?? '');
        $color = $_POST['cat_color'] ?? '#6b7280';

        if (!$name || strlen($name) > 100) {
            flash('error', 'Category name must be 1–100 characters.');
            redirect('/settings');
        }
        // Validate hex color
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            $color = '#6b7280';
        }

        Category::create($tid, $name, $color);
        flash('success', 'Category added.');
        redirect('/settings');
    }

    public function deleteCategory(int $id): void {
        Auth::guard('manage_settings');
        Auth::verifyCsrf();
        $tid = Auth::tenantId();

        $category = Category::find($id, $tid);
        if (!$category) {
            flash('error', 'Category not found.');
            redirect('/settings');
        }

        $itemCount = (int)DB::scalar(
            'SELECT COUNT(*) FROM items WHERE category_id = ? AND tenant_id = ?', [$id, $tid]
        );
        if ($itemCount > 0) {
            flash('error', "Cannot delete \"{$category['name']}\" — {$itemCount} item(s) are using it. Reassign them first.");
            redirect('/settings');
        }

        Category::delete($id, $tid);
        flash('success', 'Category removed.');
        redirect('/settings');
    }
}
