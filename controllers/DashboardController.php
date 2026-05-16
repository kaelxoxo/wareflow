<?php
class DashboardController {
    public function index(): void {
        Auth::guard();
        $tid = Auth::tenantId();

        $kpis = [
            'total_items'       => Item::count($tid),
            'low_stock'         => Item::lowStockCount($tid),
            'inventory_value'   => Item::totalValue($tid),
            'active_warehouses' => Warehouse::count($tid, true),
            'total_users'       => User::count($tid),
        ];

        $recentMovements = StockMovement::recentByTenant($tid, 8);
        $activityLogs    = ActivityLog::recent($tid, 10);
        $lowStockItems   = Item::all($tid, ['low_stock' => true, 'status' => 'active']);
        $lowStockItems   = array_slice($lowStockItems, 0, 6);
        $warehouses      = Warehouse::all($tid, true);

        // Value trend (last 7 days, split by in vs out)
        $trend = DB::query(
            "SELECT DATE(created_at) AS d,
                    SUM(CASE WHEN movement_type='in'  THEN quantity ELSE 0 END) AS stock_in,
                    SUM(CASE WHEN movement_type='out' THEN quantity ELSE 0 END) AS stock_out
             FROM stock_movements WHERE tenant_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
             GROUP BY DATE(created_at) ORDER BY d",
            [$tid]
        );

        // Top categories by item count
        $topCategories = DB::query(
            'SELECT c.name, COUNT(i.id) AS cnt, COALESCE(SUM(i.quantity * i.unit_price),0) AS value
             FROM categories c
             LEFT JOIN items i ON i.category_id = c.id AND i.tenant_id = c.tenant_id
             WHERE c.tenant_id = ? GROUP BY c.id ORDER BY cnt DESC LIMIT 5',
            [$tid]
        );

        view('dashboard/index', compact('kpis', 'recentMovements', 'activityLogs',
            'lowStockItems', 'warehouses', 'trend', 'topCategories'));
    }
}
