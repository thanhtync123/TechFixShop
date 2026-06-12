<?php

/**
 * Database.php — Singleton PDO wrapper
 */
class Database
{
    private static ?Database $instance = null;
    private \PDO $pdo;

    private function __construct()
    {
        $cfg = require ROOT . '/config/database.php';

        $dsn = "mysql:host={$cfg['host']};dbname={$cfg['dbname']};charset=utf8mb4;port={$cfg['port']}";

        $this->pdo = new \PDO($dsn, $cfg['user'], $cfg['pass'], [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    /** Shortcut: prepare + execute, trả về PDOStatement */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /** Trả về nhiều dòng */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /** Trả về 1 dòng */
    public function fetchOne(string $sql, array $params = []): array|false
    {
        return $this->query($sql, $params)->fetch();
    }

    /** Trả về giá trị cột đầu tiên */
    public function fetchValue(string $sql, array $params = []): mixed
    {
        return $this->query($sql, $params)->fetchColumn();
    }

    /** INSERT / UPDATE / DELETE — trả về số dòng ảnh hưởng */
    public function execute(string $sql, array $params = []): int
    {
        return $this->query($sql, $params)->rowCount();
    }

    /** Trả về last insert id */
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}
