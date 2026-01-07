<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Page
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM pages ORDER BY order_index ASC, title ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getBySlug($slug)
    {
        $sql = "SELECT * FROM pages WHERE slug = :slug LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($title, $slug, $content, $order_index = 0)
    {
        $sql = "INSERT INTO pages (title, slug, content, order_index) VALUES (:title, :slug, :content, :order_index)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':slug', $slug);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':order_index', $order_index);
        return $stmt->execute();
    }

    public function update($id, $title, $slug, $content, $order_index = 0)
    {
        $sql = "UPDATE pages SET title = :title, slug = :slug, content = :content, order_index = :order_index WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':slug', $slug);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':order_index', $order_index);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM pages WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
