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

    public function getBySlug($slug)
    {
        $sql = "SELECT * FROM pages WHERE slug = :slug LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function update($slug, $title, $content)
    {
        $sql = "UPDATE pages SET title = :title, content = :content WHERE slug = :slug";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':slug', $slug);
        return $stmt->execute();
    }
}
