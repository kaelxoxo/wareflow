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
        if (!$name) { flash('error', 'Company name required.'); redirect('/settings'); }
        Tenant::update($tid, ['name' => $name]);
        ActivityLog::log($tid, Auth::id(), 'settings.updated', 'tenant', $tid);
        flash('success', 'Company settings updated.');
        redirect('/settings');
    }

    public function updateProfile(): void {
        Auth::guard();
        Auth::verifyCsrf();
        $tid  = Auth::tenantId();
        $uid  = Auth::id();
        $name = trim($_POST['name'] ?? '');
        $email= trim($_POST['email'] ?? '');

        if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Valid name and email required.');
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
        if (!$name) { flash('error', 'Category name required.'); redirect('/settings'); }
        Category::create($tid, $name, $color);
        flash('success', 'Category added.');
        redirect('/settings');
    }

    public function deleteCategory(int $id): void {
        Auth::guard('manage_settings');
        Auth::verifyCsrf();
        $tid = Auth::tenantId();
        Category::delete($id, $tid);
        flash('success', 'Category removed.');
        redirect('/settings');
    }
}
