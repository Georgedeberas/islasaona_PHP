<?php

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

    public function create($data)
    {
        $sql = "INSERT INTO tours (title, slug, description_short, description_long, price_adult, price_child, duration, includes, not_included, display_style, meta_title, meta_description) 
                VALUES (:title, :slug, :description_short, :description_long, :price_adult, :price_child, :duration, :includes, :not_included, :display_style, :meta_title, :meta_description)";

        $stmt = $this->db->prepare($sql);
        // Bind parameters simple
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function update($id, $data)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }
        $sql = "UPDATE tours SET " . implode(', ', $fields) . " WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;

        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        return $stmt->execute();
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
