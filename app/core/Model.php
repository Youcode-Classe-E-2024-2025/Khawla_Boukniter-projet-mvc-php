<?php

namespace App\Core;

abstract class Model
{
    protected $db;
    protected $table;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findAll()
    {
        return $this->db->findAll("SELECT * FROM {$this->table}");
    }

    public function findById($id)
    {
        return $this->db->find("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function create(array $data)
    {
        $columns = implode(',', array_keys($data));
        $values = implode(',', array_fill(0, count($data), '?'));

        return $this->db->query(
            "INSERT INTO {$this->table} ($columns) VALUES ($values)",
            array_values($data)
        );
    }
}
