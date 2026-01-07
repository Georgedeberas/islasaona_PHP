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

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'title',
        'slug',
        'description_short',
        'description_long',
        'price_adult',
        'price_child',
        'duration',
        'location',
        'includes',
        'not_included',
        'is_active',
        // SEO Fields
        'seo_title',
        'seo_description',
        'keywords',
        'schema_type',
        'rating_score',
        'review_count',
        'tour_highlights',
        // Extended Info Fields (2026 Update)
        'info_cost',
        'info_dates_text',
        'info_duration',
        'info_includes',
        'info_visiting',
        'info_not_included',
        'info_departure',
        'info_parking',
        'info_important',
        'info_what_to_bring',
        'frequency_type',
        'specific_dates',
        'price_rules',
        // Phase 4
        'private_notes',
        'sort_order',
        'deleted_at'
    ];

    public function getAll($activeOnly = true, $includeDeleted = false)
    {
        $sql = "SELECT t.*, (SELECT image_path FROM tour_images WHERE tour_id = t.id AND is_cover = 1 LIMIT 1) as cover_image 
                FROM tours t WHERE 1=1";

        if (!$includeDeleted) {
            // EMERGENCY PATCH 2: Keeping disabled until confirmed
            // $sql .= " AND deleted_at IS NULL";
        }

        if ($activeOnly) {
            $sql .= " AND is_active = 1";
        }

        // Priority to manually ordered items
        $sql .= " ORDER BY sort_order ASC, created_at DESC";

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
            $defaults = [
                "Isla Saona",
                "Excursiones Saona",
                "Tours Punta Cana",
                "Saona Island VIP",
                "Mochileros RD",
                "Turismo República Dominicana",
                "Playa Abanico",
                "Canto de la Playa"
            ];
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
                    seo_title, seo_description, keywords, schema_type, rating_score, review_count, tour_highlights, price_rules,
                    private_notes, sort_order
                ) VALUES (
                    :title, :slug, :description_short, :description_long, :price_adult, :price_child, :duration, :includes, :not_included, :display_style, :is_active,
                    :seo_title, :seo_description, :keywords, :schema_type, :rating_score, :review_count, :tour_highlights, :price_rules,
                    :private_notes, :sort_order
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
            ':includes' => is_array($data['includes'] ?? null) ? json_encode($data['includes']) : ($data['includes'] ?? '[]'),
            ':not_included' => is_array($data['not_included'] ?? null) ? json_encode($data['not_included']) : ($data['not_included'] ?? '[]'),
            ':display_style' => $data['display_style'] ?? 'classic',
            ':is_active' => $data['is_active'] ?? 1,
            // SEO Fields
            ':seo_title' => $data['seo_title'] ?? $data['title'],
            ':seo_description' => $data['seo_description'] ?? $data['description_short'],
            ':keywords' => $data['keywords'] ?? '',
            ':schema_type' => $data['schema_type'] ?? 'TouristTrip',
            ':rating_score' => $data['rating_score'] ?? 4.8,
            ':review_count' => $data['review_count'] ?? 0,
            ':tour_highlights' => is_array($data['tour_highlights'] ?? null) ? json_encode($data['tour_highlights']) : ($data['tour_highlights'] ?? '[]'),
            ':price_rules' => $data['price_rules'] ?? null,
            ':private_notes' => $data['private_notes'] ?? null,
            ':sort_order' => $data['sort_order'] ?? 0
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

        // Usar $this->fillable si está definido, sino fallback a los antiguos
        // Como acabamos de agregarlo, usamos una lista segura combinada aquí para garantizar funcionamiento
        $allowedFields = $this->fillable ?? [
            'title',
            'slug',
            'description_short',
            'description_long',
            'price_adult',
            'price_child',
            'duration',
            'location',
            'includes',
            'not_included',
            'is_active',
            'seo_title',
            'seo_description',
            'keywords',
            'schema_type',
            'rating_score',
            'review_count',
            'tour_highlights'
        ];

        foreach ($allowedFields as $key) {
            if (isset($data[$key])) {
                $fields[] = "$key = :$key";
                $value = $data[$key];

                // Arrays a JSON
                if (is_array($value) || in_array($key, ['includes', 'not_included', 'tour_highlights', 'specific_dates'])) {
                    if (is_array($value))
                        $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                }

                $params[":$key"] = $value;
            }
        }

        if (empty($fields)) {
            return true; // No changes needed
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

    // --- Phase 4 Methods ---

    public function softDelete($id)
    {
        $sql = "UPDATE tours SET deleted_at = NOW(), is_active = 0 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function restore($id)
    {
        $sql = "UPDATE tours SET deleted_at = NULL WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function forceDelete($id)
    {
        $sql = "DELETE FROM tours WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function updateField($id, $field, $value)
    {
        // Whitelist safe fields
        $allowed = ['price_adult', 'price_child', 'title', 'is_active', 'sort_order', 'private_notes'];
        if (!in_array($field, $allowed))
            return false;

        $sql = "UPDATE tours SET $field = :value WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':value' => $value, ':id' => $id]);
    }
}
