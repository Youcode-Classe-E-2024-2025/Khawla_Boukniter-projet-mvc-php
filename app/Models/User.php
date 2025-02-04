<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected $table = 'users';

    public function findByEmail(string $email)
    {
        return $this->db->find("SELECT * FROM {$this->table} WHERE email = ?", [$email]);
    }

    public function findWithArticles(int $id)
    {
        return $this->db->find("
            SELECT users.*, COUNT(articles.id) as article_count 
            FROM {$this->table}
            LEFT JOIN articles ON users.id = articles.user_id 
            WHERE users.id = ?
            GROUP BY users.id
        ", [$id]);
    }
}
