<?php
class ApiController {
    public function kpis(): void {
        Auth::guard();
        $tid = Auth::tenantId();
        json_response([
            'total_items'       => Item::count($tid),
            'low_stock'         => Item::lowStockCount($tid),
            'inventory_value'   => Item::totalValue($tid),
            'active_warehouses' => Warehouse::count($tid, true),
            'total_users'       => User::count($tid),
        ]);
    }

    public function items(): void {
        Auth::guard();
        $tid   = Auth::tenantId();
        $items = Item::all($tid, [
            'search'  => $_GET['search']  ?? '',
            'status'  => $_GET['status']  ?? '',
        ]);
        json_response(['data' => $items, 'total' => count($items)]);
    }

    public function activityLogs(): void {
        Auth::guard();
        $tid  = Auth::tenantId();
        $page = max(1, (int)($_GET['page'] ?? 1));
        json_response(ActivityLog::all($tid, $page));
    }

    public function stockTrend(): void {
        Auth::guard();
        $tid  = Auth::tenantId();
        $days = min(90, max(7, (int)($_GET['days'] ?? 30)));
        $data = DB::query(
            "SELECT DATE(created_at) AS date,
                    SUM(CASE WHEN movement_type='in'  THEN quantity ELSE 0 END) AS stock_in,
                    SUM(CASE WHEN movement_type='out' THEN quantity ELSE 0 END) AS stock_out,
                    COUNT(*) AS movements
             FROM stock_movements
             WHERE tenant_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             GROUP BY DATE(created_at) ORDER BY date",
            [$tid, $days]
        );
        json_response(['data' => $data]);
    }
}
