<?php
class StockController {
    public function index(): void {
        Auth::guard('view_reports');
        $tid      = Auth::tenantId();
        $page     = max(1, (int)($_GET['page'] ?? 1));
        $filters  = [
            'type'         => $_GET['type']         ?? '',
            'warehouse_id' => $_GET['warehouse_id'] ?? '',
            'item_id'      => $_GET['item_id']      ?? '',
            'date_from'    => $_GET['date_from']    ?? '',
            'date_to'      => $_GET['date_to']      ?? '',
        ];

        $pagination = StockMovement::all($tid, $filters, $page);
        $warehouses = Warehouse::all($tid, true);
        $items      = Item::all($tid);

        // Summary stats
        $stats = DB::row(
            "SELECT
               COALESCE(SUM(CASE WHEN movement_type='in' THEN quantity END),0) AS total_in,
               COALESCE(SUM(CASE WHEN movement_type='out' THEN quantity END),0) AS total_out,
               COALESCE(SUM(CASE WHEN movement_type='transfer' THEN quantity END),0) AS total_transfer,
               COUNT(*) AS total_movements
             FROM stock_movements WHERE tenant_id = ?",
            [$tid]
        );

        view('stock/index', compact('pagination', 'filters', 'warehouses', 'items', 'stats'));
    }

    public function transfer(): void {
        Auth::guard('manage_inventory');
        Auth::verifyCsrf();
        $tid = Auth::tenantId();

        $d = [
            'item_id'          => (int)($_POST['item_id']          ?? 0),
            'movement_type'    => $_POST['movement_type']           ?? '',
            'quantity'         => (int)($_POST['quantity']          ?? 0),
            'new_quantity'     => (int)($_POST['new_quantity']      ?? 0),
            'from_warehouse_id'=> $_POST['from_warehouse_id']       ?? '',
            'to_warehouse_id'  => $_POST['to_warehouse_id']         ?? '',
            'reference'        => trim($_POST['reference']          ?? ''),
            'notes'            => trim($_POST['notes']              ?? ''),
        ];

        $allowedTypes = ['in','out','transfer','adjustment'];
        if (!$d['item_id'] || !in_array($d['movement_type'], $allowedTypes)) {
            flash('error', 'Invalid movement data.');
            redirect('/stock');
        }
        if ($d['movement_type'] !== 'adjustment' && $d['quantity'] <= 0) {
            flash('error', 'Quantity must be positive.');
            redirect('/stock');
        }

        try {
            StockMovement::transfer($tid, $d, Auth::id());
            ActivityLog::log($tid, Auth::id(), 'stock.' . $d['movement_type'], 'item', $d['item_id'],
                ['qty' => $d['quantity'], 'ref' => $d['reference']]);
            flash('success', 'Stock movement recorded.');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
        }

        redirect('/stock');
    }
}
