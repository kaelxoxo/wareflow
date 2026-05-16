<?php
class DB {
    private static ?PDO $pdo = null;

    public static function get(): PDO {
        if (self::$pdo === null) {
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
            self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$pdo;
    }

    public static function query(string $sql, array $p = []): array {
        $s = self::get()->prepare($sql);
        $s->execute($p);
        return $s->fetchAll();
    }

    public static function row(string $sql, array $p = []): ?array {
        $s = self::get()->prepare($sql);
        $s->execute($p);
        $r = $s->fetch();
        return $r ?: null;
    }

    public static function execute(string $sql, array $p = []): int {
        $s = self::get()->prepare($sql);
        $s->execute($p);
        return $s->rowCount();
    }

    public static function insert(string $sql, array $p = []): string {
        $s = self::get()->prepare($sql);
        $s->execute($p);
        return self::get()->lastInsertId();
    }

    public static function scalar(string $sql, array $p = []): mixed {
        $s = self::get()->prepare($sql);
        $s->execute($p);
        $r = $s->fetch(PDO::FETCH_NUM);
        return $r ? $r[0] : null;
    }

    public static function paginate(string $sql, array $p, int $page, int $perPage = PER_PAGE): array {
        $total  = (int) self::scalar("SELECT COUNT(*) FROM ({$sql}) _c", $p);
        $offset = ($page - 1) * $perPage;
        $rows   = self::query("{$sql} LIMIT {$perPage} OFFSET {$offset}", $p);
        return [
            'data'        => $rows,
            'total'       => $total,
            'per_page'    => $perPage,
            'current_page'=> $page,
            'last_page'   => max(1, (int) ceil($total / $perPage)),
        ];
    }
}
