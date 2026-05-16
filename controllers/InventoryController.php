<?php
class InventoryController {
    public function index(): void {
        Auth::guard('view_inventory');
        $tid     = Auth::tenantId();
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $filters = [
            'search'       => trim($_GET['search']      ?? ''),
            'category_id'  => $_GET['category_id']  ?? '',
            'warehouse_id' => $_GET['warehouse_id']  ?? '',
            'status'       => $_GET['status']        ?? '',
            'low_stock'    => $_GET['low_stock']     ?? '',
        ];

        $pagination = Item::paginated($tid, $filters, $page);
        $fuzzyMode  = false;
        $fuzzyQuery = '';

        // Fuzzy fallback: exact search returned nothing → score all candidates in PHP
        if (!empty($filters['search']) && $pagination['total'] === 0) {
            $fuzzyMatches = Item::fuzzySearch($tid, $filters['search'], $filters);
            if (!empty($fuzzyMatches)) {
                $fuzzyMode  = true;
                $fuzzyQuery = $filters['search'];
                // Wrap in the same shape DB::paginate() returns so the view is unchanged
                $pagination = [
                    'data'         => $fuzzyMatches,
                    'total'        => count($fuzzyMatches),
                    'current_page' => 1,
                    'last_page'    => 1,
                    'per_page'     => count($fuzzyMatches),
                ];
            }
        }

        $categories   = Category::all($tid);
        $warehouses   = Warehouse::all($tid, true);
        $customFields = CustomField::all($tid, 'item');

        view('inventory/index', compact(
            'pagination', 'filters', 'categories', 'warehouses',
            'customFields', 'fuzzyMode', 'fuzzyQuery'
        ));
    }

    public function createForm(): void {
        Auth::guard('manage_inventory');
        $tid = Auth::tenantId();
        $categories  = Category::all($tid);
        $warehouses  = Warehouse::all($tid, true);
        $customFields = CustomField::all($tid, 'item');
        view('inventory/form', compact('categories', 'warehouses', 'customFields'));
    }

    public function create(): void {
        Auth::guard('manage_inventory');
        Auth::verifyCsrf();
        $tid = Auth::tenantId();

        ['errors' => $errors, 'data' => $d] = $this->validate($_POST, $tid);
        if ($errors) {
            flash('error', implode(' ', $errors));
            set_old($_POST);
            redirect('/inventory/create');
        }

        $id = Item::create($tid, $d, Auth::id());

        if (!empty($_POST['custom_fields'])) {
            CustomField::saveValues($id, $tid, $_POST['custom_fields']);
        }

        ActivityLog::log($tid, Auth::id(), 'item.created', 'item', $id, ['name' => $d['name'], 'sku' => $d['sku']]);
        flash('success', "Item '{$d['name']}' added successfully.");
        redirect('/inventory');
    }

    public function editForm(int $id): void {
        Auth::guard('manage_inventory');
        $tid  = Auth::tenantId();
        $item = Item::find($id, $tid);
        if (!$item) { flash('error', 'Item not found.'); redirect('/inventory'); }

        $categories   = Category::all($tid);
        $warehouses   = Warehouse::all($tid, true);
        $customFields = CustomField::valuesFor($id, $tid, 'item');
        view('inventory/form', compact('item', 'categories', 'warehouses', 'customFields'));
    }

    public function update(int $id): void {
        Auth::guard('manage_inventory');
        Auth::verifyCsrf();
        $tid  = Auth::tenantId();
        $item = Item::find($id, $tid);
        if (!$item) { flash('error', 'Item not found.'); redirect('/inventory'); }

        ['errors' => $errors, 'data' => $d] = $this->validate($_POST, $tid, $id);
        if ($errors) {
            flash('error', implode(' ', $errors));
            set_old($_POST);
            redirect('/inventory/' . $id . '/edit');
        }

        Item::update($id, $tid, $d);

        if (!empty($_POST['custom_fields'])) {
            CustomField::saveValues($id, $tid, $_POST['custom_fields']);
        }

        ActivityLog::log($tid, Auth::id(), 'item.updated', 'item', $id, ['name' => $d['name']]);
        flash('success', "Item '{$d['name']}' updated.");
        redirect('/inventory');
    }

    public function delete(int $id): void {
        Auth::guard('manage_inventory');
        Auth::verifyCsrf();
        $tid  = Auth::tenantId();
        $item = Item::find($id, $tid);
        if (!$item) { flash('error', 'Item not found.'); redirect('/inventory'); }

        Item::delete($id, $tid);
        ActivityLog::log($tid, Auth::id(), 'item.deleted', 'item', $id, ['name' => $item['name']]);
        flash('success', "Item deleted.");
        redirect('/inventory');
    }

    private function validate(array $post, int $tid, ?int $excludeId = null): array {
        $errors = [];
        $d = [
            'sku'           => trim($post['sku']           ?? ''),
            'name'          => trim($post['name']          ?? ''),
            'description'   => trim($post['description']   ?? ''),
            'category_id'   => $post['category_id']        ?? '',
            'warehouse_id'  => $post['warehouse_id']       ?? '',
            'quantity'      => (int)($post['quantity']     ?? 0),
            'unit_price'    => (float)($post['unit_price'] ?? 0),
            'reorder_point' => (int)($post['reorder_point']?? 10),
            'unit'          => trim($post['unit']          ?? 'pcs'),
            'status'        => $post['status']             ?? 'active',
        ];
        if (!$d['sku'])  $errors[] = 'SKU is required.';
        if (!$d['name']) $errors[] = 'Name is required.';
        if (Item::skuExists($d['sku'], $tid, $excludeId)) $errors[] = 'SKU already exists.';
        return ['errors' => $errors, 'data' => $d];
    }
}
