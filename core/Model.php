<?php

/**
 * Model.php — Base model
 * Tất cả Model kế thừa class này
 */
abstract class Model
{
    protected Database $db;
    protected string $table = '';
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Lấy tất cả bản ghi */
    public function all(string $orderBy = ''): array
    {
        $sql = "SELECT * FROM `{$this->table}`";
        if ($orderBy) $sql .= " ORDER BY $orderBy";
        return $this->db->fetchAll($sql);
    }

    /** Tìm theo ID */
    public function find(int $id): array|false
    {
        return $this->db->fetchOne(
            "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?",
            [$id]
        );
    }

    /** Tìm theo điều kiện (1 cột) */
    public function findBy(string $column, mixed $value): array|false
    {
        return $this->db->fetchOne(
            "SELECT * FROM `{$this->table}` WHERE `$column` = ? LIMIT 1",
            [$value]
        );
    }

    /** Lấy nhiều bản ghi theo điều kiện */
    public function where(string $column, mixed $value, string $orderBy = ''): array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `$column` = ?";
        if ($orderBy) $sql .= " ORDER BY $orderBy";
        return $this->db->fetchAll($sql, [$value]);
    }

    /** Insert — trả về ID mới */
    public function insert(array $data): int
    {
        $cols   = implode('`, `', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $this->db->execute(
            "INSERT INTO `{$this->table}` (`$cols`) VALUES ($placeholders)",
            array_values($data)
        );
        return (int) $this->db->lastInsertId();
    }

    /** Update theo ID */
    public function update(int $id, array $data): int
    {
        $sets = implode(' = ?, ', array_map(fn($k) => "`$k`", array_keys($data))) . ' = ?';
        return $this->db->execute(
            "UPDATE `{$this->table}` SET $sets WHERE `{$this->primaryKey}` = ?",
            [...array_values($data), $id]
        );
    }

    /** Delete theo ID */
    public function delete(int $id): int
    {
        return $this->db->execute(
            "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?",
            [$id]
        );
    }

    /** Đếm bản ghi */
    public function count(string $where = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}`";
        if ($where) $sql .= " WHERE $where";
        return (int) $this->db->fetchValue($sql, $params);
    }
}
