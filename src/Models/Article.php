<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Article
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll($publishedOnly = true)
    {
        $sql = "SELECT * FROM articles";
        if ($publishedOnly) {
            $sql .= " WHERE is_published = 1";
        }
        $sql .= " ORDER BY created_at DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBySlug($slug)
    {
        $stmt = $this->db->prepare("SELECT * FROM articles WHERE slug = :slug LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM articles WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO articles (title, slug, image_path, content, excerpt, is_published, seo_title, seo_description) 
                VALUES (:title, :slug, :image_path, :content, :excerpt, :is_published, :seo_title, :seo_description)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':title' => $data['title'],
            ':slug' => $data['slug'],
            ':image_path' => $data['image_path'] ?? '',
            ':content' => $data['content'] ?? '',
            ':excerpt' => $data['excerpt'] ?? '',
            ':is_published' => $data['is_published'] ?? 1,
            ':seo_title' => $data['seo_title'] ?? '',
            ':seo_description' => $data['seo_description'] ?? ''
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE articles SET 
                title = :title, 
                slug = :slug,
                content = :content, 
                excerpt = :excerpt,
                is_published = :is_published,
                seo_title = :seo_title, 
                seo_description = :seo_description";

        // Update image only if provided
        if (!empty($data['image_path'])) {
            $sql .= ", image_path = :image_path";
        }

        $sql .= " WHERE id = :id";

        $params = [
            ':id' => $id,
            ':title' => $data['title'],
            ':slug' => $data['slug'],
            ':content' => $data['content'],
            ':excerpt' => $data['excerpt'],
            ':is_published' => $data['is_published'],
            ':seo_title' => $data['seo_title'],
            ':seo_description' => $data['seo_description']
        ];

        if (!empty($data['image_path'])) {
            $params[':image_path'] = $data['image_path'];
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM articles WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
