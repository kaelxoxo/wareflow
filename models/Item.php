<?php
class Item {
    public static function all(int $tenantId, array $filters = []): array {
        $where = 'WHERE i.tenant_id = ?';
        $params = [$tenantId];

        if (!empty($filters['search'])) {
            $where .= ' AND (i.name LIKE ? OR i.sku LIKE ?)';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['category_id'])) {
            $where .= ' AND i.category_id = ?';
            $params[] = $filters['category_id'];
        }
        if (!empty($filters['warehouse_id'])) {
            $where .= ' AND i.warehouse_id = ?';
            $params[] = $filters['warehouse_id'];
        }
        if (!empty($filters['status'])) {
            $where .= ' AND i.status = ?';
            $params[] = $filters['status'];
        }
        if (isset($filters['low_stock']) && $filters['low_stock']) {
            $where .= ' AND i.quantity <= i.reorder_point';
        }

        $sql = "SELECT i.*, c.name AS category_name, w.name AS warehouse_name
                FROM items i
                LEFT JOIN categories c ON c.id = i.category_id
                LEFT JOIN warehouses w ON w.id = i.warehouse_id
                {$where}
                ORDER BY i.updated_at DESC";

        return DB::query($sql, $params);
    }

    public static function paginated(int $tenantId, array $filters, int $page): array {
        $where = 'i.tenant_id = ?';
        $params = [$tenantId];

        if (!empty($filters['search'])) {
            $where .= ' AND (i.name LIKE ? OR i.sku LIKE ?)';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['category_id'])) {
            $where .= ' AND i.category_id = ?';
            $params[] = $filters['category_id'];
        }
        if (!empty($filters['warehouse_id'])) {
            $where .= ' AND i.warehouse_id = ?';
            $params[] = $filters['warehouse_id'];
        }
        if (!empty($filters['status'])) {
            $where .= ' AND i.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['low_stock'])) {
            $where .= ' AND i.quantity <= i.reorder_point';
        }

        $sql = "SELECT i.*, c.name AS category_name, w.name AS warehouse_name
                FROM items i
                LEFT JOIN categories c ON c.id = i.category_id
                LEFT JOIN warehouses w ON w.id = i.warehouse_id
                WHERE {$where}
                ORDER BY i.updated_at DESC";

        return DB::paginate($sql, $params, $page);
    }

    public static function find(int $id, int $tenantId): ?array {
        return DB::row(
            'SELECT i.*, c.name AS category_name, w.name AS warehouse_name
             FROM items i
             LEFT JOIN categories c ON c.id = i.category_id
             LEFT JOIN warehouses w ON w.id = i.warehouse_id
             WHERE i.id = ? AND i.tenant_id = ?',
            [$id, $tenantId]
        );
    }

    public static function create(int $tenantId, array $d, int $userId): string {
        return DB::insert(
            'INSERT INTO items (tenant_id,sku,name,description,category_id,warehouse_id,
              quantity,unit_price,reorder_point,unit,status,created_by)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?)',
            [$tenantId, $d['sku'], $d['name'], $d['description'] ?? null,
             $d['category_id'] ?: null, $d['warehouse_id'] ?: null,
             (int)($d['quantity'] ?? 0), (float)($d['unit_price'] ?? 0),
             (int)($d['reorder_point'] ?? 10), $d['unit'] ?? 'pcs',
             $d['status'] ?? 'active', $userId]
        );
    }

    public static function update(int $id, int $tenantId, array $d): void {
        DB::execute(
            'UPDATE items SET sku=?,name=?,description=?,category_id=?,warehouse_id=?,
              quantity=?,unit_price=?,reorder_point=?,unit=?,status=? WHERE id=? AND tenant_id=?',
            [$d['sku'], $d['name'], $d['description'] ?? null,
             $d['category_id'] ?: null, $d['warehouse_id'] ?: null,
             (int)($d['quantity'] ?? 0), (float)($d['unit_price'] ?? 0),
             (int)($d['reorder_point'] ?? 10), $d['unit'] ?? 'pcs',
             $d['status'] ?? 'active', $id, $tenantId]
        );
    }

    public static function delete(int $id, int $tenantId): void {
        DB::execute('DELETE FROM items WHERE id = ? AND tenant_id = ?', [$id, $tenantId]);
    }

    public static function count(int $tenantId): int {
        return (int) DB::scalar('SELECT COUNT(*) FROM items WHERE tenant_id = ?', [$tenantId]);
    }

    public static function lowStockCount(int $tenantId): int {
        return (int) DB::scalar(
            "SELECT COUNT(*) FROM items WHERE tenant_id = ? AND quantity <= reorder_point AND status = 'active'",
            [$tenantId]
        );
    }

    public static function totalValue(int $tenantId): float {
        return (float) DB::scalar(
            "SELECT COALESCE(SUM(quantity * unit_price), 0) FROM items WHERE tenant_id = ? AND status = 'active'",
            [$tenantId]
        );
    }

    public static function skuExists(string $sku, int $tenantId, ?int $excludeId = null): bool {
        $sql = 'SELECT id FROM items WHERE sku = ? AND tenant_id = ?';
        $p   = [$sku, $tenantId];
        if ($excludeId) { $sql .= ' AND id != ?'; $p[] = $excludeId; }
        return (bool) DB::row($sql, $p);
    }

    /**
     * Fuzzy search using trigram similarity (Jaccard index) + space-normalized
     * comparison + token-level Levenshtein. Mirrors PostgreSQL pg_trgm behaviour.
     *
     * Returns items scored >= 0.30 similarity, sorted best-first, capped at $limit.
     * All non-search filters (category, warehouse, status, low_stock) are preserved.
     */
    public static function fuzzySearch(
        int $tenantId,
        string $query,
        array $otherFilters = [],
        int $limit = 25
    ): array {
        // Fetch candidates with all filters except 'search'
        $candidateFilters = $otherFilters;
        unset($candidateFilters['search']);
        $candidates = static::all($tenantId, $candidateFilters);

        // Normalize query once
        $q        = strtolower(trim(preg_replace('/\s+/', ' ', $query)));
        $qStrip   = str_replace(' ', '', $q);

        $scored = [];
        foreach ($candidates as $item) {
            $name      = strtolower(trim(preg_replace('/\s+/', ' ', $item['name'])));
            $sku       = strtolower(trim($item['sku']));
            $nameStrip = str_replace(' ', '', $name);

            // Three independent signals; take the maximum
            $score = max(
                static::trigramJaccard($q, $name),          // normal full-string
                static::trigramJaccard($qStrip, $nameStrip), // space-insensitive (catches concatenated words)
                static::tokenLevenshtein($q, $name)          // per-word edit distance
            );

            // Small SKU bonus — boosts exact/near-exact SKU matches
            $skuScore = static::trigramJaccard($q, $sku);
            if ($skuScore > 0.40) {
                $score = max($score, $skuScore * 0.85);
            }

            if ($score >= 0.30) {
                $item['_fuzzy_score'] = $score;
                $scored[] = $item;
            }
        }

        usort($scored, static fn($a, $b) => $b['_fuzzy_score'] <=> $a['_fuzzy_score']);
        return array_slice($scored, 0, $limit);
    }

    // ── Scoring helpers ──────────────────────────────────────────────────────

    /**
     * Jaccard similarity over character trigrams.
     * Identical to PostgreSQL's pg_trgm: pads with two leading spaces for
     * boundary trigrams, one trailing space.
     */
    private static function trigramJaccard(string $a, string $b): float {
        if ($a === $b) return 1.0;
        if (strlen($a) < 1 || strlen($b) < 1) return 0.0;

        $ta = static::trigrams($a);
        $tb = static::trigrams($b);
        if (empty($ta) || empty($tb)) return 0.0;

        $intersection = count(array_intersect($ta, $tb));
        $union        = count(array_unique(array_merge($ta, $tb)));

        return $union > 0 ? $intersection / $union : 0.0;
    }

    /** Extract all unique 3-character substrings from a padded string. */
    private static function trigrams(string $s): array {
        $s   = '  ' . $s . ' '; // pg_trgm-style boundary padding
        $tg  = [];
        $len = strlen($s);
        for ($i = 0; $i <= $len - 3; $i++) {
            $tg[] = substr($s, $i, 3);
        }
        return array_unique($tg);
    }

    /**
     * For each query token, find the best-matching target token by normalized
     * Levenshtein distance. Returns the average best score across query tokens.
     * Handles word reordering and per-word typos.
     */
    private static function tokenLevenshtein(string $query, string $target): float {
        $qTokens = preg_split('/\s+/', $query, -1, PREG_SPLIT_NO_EMPTY);
        $tTokens = preg_split('/\s+/', $target, -1, PREG_SPLIT_NO_EMPTY);

        if (empty($qTokens) || empty($tTokens)) return 0.0;

        $totalScore = 0.0;
        foreach ($qTokens as $qt) {
            $best = 0.0;
            foreach ($tTokens as $tt) {
                $maxLen = max(strlen($qt), strlen($tt));
                if ($maxLen === 0) continue;
                $score = 1.0 - levenshtein($qt, $tt) / $maxLen;
                if ($score > $best) $best = $score;
            }
            $totalScore += $best;
        }

        return $totalScore / count($qTokens);
    }
}
