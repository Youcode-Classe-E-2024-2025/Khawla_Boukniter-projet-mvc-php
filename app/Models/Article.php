<?php

namespace App\Models;

use App\Core\Model;

class Article extends Model
{
    protected $table = 'articles';

    public function articlesWithUsers()
    {
        return $this->db->findAll("
            SELECT articles.*, users.username 
            FROM {$this->table}
            JOIN users ON articles.user_id = users.id 
            ORDER BY created_at DESC
        ");
    }

    public function articlesByUserId($userId)
    {
        return $this->db->findAll(
            "SELECT articles.*, users.username FROM articles 
         JOIN users ON articles.user_id = users.id 
         WHERE user_id = ? 
         ORDER BY created_at DESC",
            [$userId]
        );
    }

    public function find($id)
    {
        return $this->db->find("SELECT * FROM articles WHERE id = ?", [$id]);
    }

    public function create($data)
    {
        return $this->db->query(
            "INSERT INTO articles (title, content, user_id) VALUES (?, ?, ?)",
            [$data['title'], $data['content'], $_SESSION['user_id']]
        );
    }

    public function delete($id)
    {
        return $this->db->query("DELETE FROM articles WHERE id = ?", [$id]);
    }
}
