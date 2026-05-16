<?php
class WarehouseController {
    public function index(): void {
        Auth::guard('view_warehouses');
        $tid = Auth::tenantId();
        $warehouses = Warehouse::all($tid);
        $users      = User::all($tid);
        view('warehouses/index', compact('warehouses', 'users'));
    }

    public function create(): void {
        Auth::guard('manage_warehouses');
        Auth::verifyCsrf();
        $tid = Auth::tenantId();
        $d = $this->extractData($_POST);
        $errors = $this->validate($d);
        if ($errors) { flash('error', implode(' ', $errors)); redirect('/warehouses'); }
        $id = Warehouse::create($tid, $d);
        ActivityLog::log($tid, Auth::id(), 'warehouse.created', 'warehouse', $id, ['name' => $d['name']]);
        flash('success', "Warehouse '{$d['name']}' created.");
        redirect('/warehouses');
    }

    public function update(int $id): void {
        Auth::guard('manage_warehouses');
        Auth::verifyCsrf();
        $tid = Auth::tenantId();
        $wh  = Warehouse::find($id, $tid);
        if (!$wh) { flash('error', 'Warehouse not found.'); redirect('/warehouses'); }
        $d = $this->extractData($_POST);
        $errors = $this->validate($d);
        if ($errors) { flash('error', implode(' ', $errors)); redirect('/warehouses'); }
        Warehouse::update($id, $tid, $d);
        ActivityLog::log($tid, Auth::id(), 'warehouse.updated', 'warehouse', $id, ['name' => $d['name']]);
        flash('success', "Warehouse updated.");
        redirect('/warehouses');
    }

    public function delete(int $id): void {
        Auth::guard('manage_warehouses');
        Auth::verifyCsrf();
        $tid = Auth::tenantId();
        $wh  = Warehouse::find($id, $tid);
        if (!$wh) { flash('error', 'Warehouse not found.'); redirect('/warehouses'); }
        if (!Warehouse::delete($id, $tid)) {
            flash('error', 'Cannot delete warehouse with assigned items.');
        } else {
            ActivityLog::log($tid, Auth::id(), 'warehouse.deleted', 'warehouse', $id, ['name' => $wh['name']]);
            flash('success', "Warehouse '{$wh['name']}' deleted.");
        }
        redirect('/warehouses');
    }

    private function extractData(array $p): array {
        return [
            'name'       => trim($p['name']       ?? ''),
            'code'       => trim($p['code']       ?? ''),
            'location'   => trim($p['location']   ?? ''),
            'capacity'   => (int)($p['capacity']  ?? 0),
            'manager_id' => $p['manager_id']      ?? '',
            'status'     => $p['status']          ?? 'active',
        ];
    }

    private function validate(array $d): array {
        $errors = [];
        if (!$d['name']) $errors[] = 'Warehouse name is required.';
        return $errors;
    }
}
