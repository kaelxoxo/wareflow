<?php
class UserController {
    private static function roleRank(string $role): int {
        return ['owner' => 4, 'admin' => 3, 'manager' => 2, 'viewer' => 1][$role] ?? 0;
    }
    public function index(): void {
        Auth::guard('manage_users');
        $tid   = Auth::tenantId();
        $users = User::all($tid);
        view('users/index', compact('users'));
    }

    public function invite(): void {
        Auth::guard('manage_users');
        Auth::verifyCsrf();
        $tid   = Auth::tenantId();
        $email = trim($_POST['email'] ?? '');
        $role  = $_POST['role'] ?? 'viewer';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Valid email is required.');
            redirect('/users');
        }
        $validRoles = ['admin','manager','viewer'];
        if (!in_array($role, $validRoles) || self::roleRank(Auth::role()) <= self::roleRank($role)) {
            flash('error', 'Invalid role.');
            redirect('/users');
        }
        $existing = User::byEmail($email, $tid);
        if ($existing) {
            $msg = $existing['status'] === 'invited'
                ? 'An invite is already pending for this email address.'
                : 'This email is already a member of this workspace.';
            flash('error', $msg);
            redirect('/users');
        }

        $tenant  = Tenant::find($tid);
        $planCfg = PLANS[$tenant['plan'] ?? 'starter'] ?? PLANS['starter'];
        if (Tenant::memberCount($tid) >= $planCfg['member_limit']) {
            flash('error', "Your {$planCfg['name']} plan allows up to {$planCfg['member_limit']} team members. Upgrade your plan to invite more.");
            redirect('/users');
        }

        $result = User::invite($tid, $email, $role);
        $inviteLink = url('/invite/' . $result['token']);
        ActivityLog::log($tid, Auth::id(), 'user.invited', 'user', $result['id'], ['email' => $email, 'role' => $role]);
        flash('success', "Invite sent! Link: <a href=\"{$inviteLink}\" class=\"underline\" target=\"_blank\">Copy link</a>");
        redirect('/users');
    }

    public function assignRole(int $id): void {
        Auth::guard('manage_users');
        Auth::verifyCsrf();
        $tid  = Auth::tenantId();
        $user = User::find($id, $tid);
        if (!$user) { flash('error', 'User not found.'); redirect('/users'); }
        if ($user['role'] === 'owner') { flash('error', 'Cannot change owner role.'); redirect('/users'); }

        if (self::roleRank(Auth::role()) <= self::roleRank($user['role'])) {
            flash('error', 'You cannot change the role of a user with equal or higher rank.');
            redirect('/users');
        }

        $role = $_POST['role'] ?? '';
        $validRoles = ['admin','manager','viewer'];
        if (!in_array($role, $validRoles) || self::roleRank(Auth::role()) <= self::roleRank($role)) {
            flash('error', 'Invalid role.');
            redirect('/users');
        }

        User::update($id, $tid, ['role' => $role]);
        ActivityLog::log($tid, Auth::id(), 'user.role_changed', 'user', $id, ['role' => $role]);
        flash('success', "Role updated for {$user['name']}.");
        redirect('/users');
    }

    public function suspend(int $id): void {
        Auth::guard('manage_users');
        Auth::verifyCsrf();
        $tid  = Auth::tenantId();
        $user = User::find($id, $tid);
        if (!$user || $id === Auth::id() || self::roleRank(Auth::role()) <= self::roleRank($user['role'])) {
            flash('error', 'Cannot suspend this user.');
            redirect('/users');
        }
        $newStatus = $user['status'] === 'suspended' ? 'active' : 'suspended';
        User::update($id, $tid, ['status' => $newStatus]);
        flash('success', "User {$newStatus}.");
        redirect('/users');
    }

    public function delete(int $id): void {
        Auth::guard('manage_users');
        Auth::verifyCsrf();
        $tid  = Auth::tenantId();
        $user = User::find($id, $tid);
        if (!$user || $id === Auth::id() || self::roleRank(Auth::role()) <= self::roleRank($user['role'])) {
            flash('error', 'Cannot delete this user.');
            redirect('/users');
        }
        User::delete($id, $tid);
        ActivityLog::log($tid, Auth::id(), 'user.deleted', 'user', $id, ['email' => $user['email']]);
        flash('success', 'User removed.');
        redirect('/users');
    }
}
