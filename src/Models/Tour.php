<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Tour
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll($activeOnly = true)
    {
        $sql = "SELECT t.*, (SELECT image_path FROM tour_images WHERE tour_id = t.id AND is_cover = 1 LIMIT 1) as cover_image 
                FROM tours t";
        if ($activeOnly) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBySlug($slug)
    {
        $sql = "SELECT * FROM tours WHERE slug = :slug AND is_active = 1 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getImages($tourId)
    {
        $sql = "SELECT * FROM tour_images WHERE tour_id = :tour_id ORDER BY is_cover DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tour_id', $tourId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function prepareSeoData(&$data)
    {
        // Fallback para keywords si vienen vacias
        if (empty($data['keywords'])) {
            $defaults = ["Isla Saona", "Saona Island", "excursiones en República Dominicana", "tours en República Dominicana", "experiencias en República Dominicana", "Punta Cana", "Bávaro"];
            $data['keywords'] = implode(', ', $defaults);
        }
        // Fallback Schema Type
        if (empty($data['schema_type'])) {
            $data['schema_type'] = 'TouristTrip';
        }
        // Fallback Ratings (Fake social proof initial)
        if (empty($data['rating_score']))
            $data['rating_score'] = 4.8;
        if (empty($data['review_count']))
            $data['review_count'] = 120;
    }

    public function create($data)
    {
        $this->prepareSeoData($data);

        $sql = "INSERT INTO tours (
                    title, slug, description_short, description_long, price_adult, price_child, duration, includes, not_included, display_style, is_active,
                    seo_title, seo_description, keywords, schema_type, rating_score, review_count, tour_highlights
                ) VALUES (
                    :title, :slug, :description_short, :description_long, :price_adult, :price_child, :duration, :includes, :not_included, :display_style, :is_active,
                    :seo_title, :seo_description, :keywords, :schema_type, :rating_score, :review_count, :tour_highlights
                )";

        $stmt = $this->db->prepare($sql);

        $params = [
            ':title' => $data['title'],
            ':slug' => $data['slug'],
            ':description_short' => $data['description_short'],
            ':description_long' => $data['description_long'],
            ':price_adult' => $data['price_adult'],
            ':price_child' => $data['price_child'],
            ':duration' => $data['duration'],
            ':includes' => is_array($data['includes']) ? json_encode($data['includes']) : $data['includes'],
            ':not_included' => is_array($data['not_included']) ? json_encode($data['not_included']) : $data['not_included'],
            ':display_style' => $data['display_style'],
            ':is_active' => $data['is_active'],
            // SEO Fields
            ':seo_title' => $data['seo_title'] ?? $data['title'],
            ':seo_description' => $data['seo_description'] ?? $data['description_short'],
            ':keywords' => $data['keywords'],
            ':schema_type' => $data['schema_type'],
            ':rating_score' => $data['rating_score'],
            ':review_count' => $data['review_count'],
            ':tour_highlights' => is_array($data['tour_highlights']) ? json_encode($data['tour_highlights']) : $data['tour_highlights']
        ];

        if ($stmt->execute($params)) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function update($id, $data)
    {
        $this->prepareSeoData($data);

        $fields = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            if (in_array($key, ['includes', 'not_included', 'tour_highlights']) && is_array($value)) {
                $params[':' . $key] = json_encode($value);
            } else {
                $params[':' . $key] = $value;
            }
        }

        $sql = "UPDATE tours SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    public function addImage($tourId, $imagePath, $isCover = 0)
    {
        $sql = "INSERT INTO tour_images (tour_id, image_path, is_cover) VALUES (:tour_id, :image_path, :is_cover)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':tour_id', $tourId);
        $stmt->bindValue(':image_path', $imagePath);
        $stmt->bindValue(':is_cover', $isCover);
        return $stmt->execute();
    }
}
