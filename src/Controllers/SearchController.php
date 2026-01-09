<?php

namespace App\Controllers;

use App\Models\Tour;
use App\Models\Article;
use App\Config\Database;
use PDO;

class SearchController
{
    public function search()
    {
        header('Content-Type: application/json');

        $query = $_GET['q'] ?? '';
        $type = $_GET['type'] ?? 'all'; // all, tours, blog

        if (strlen($query) < 2) {
            echo json_encode([]);
            exit;
        }

        $results = [];
        $db = Database::getConnection();
        $searchTerm = "%{$query}%";

        // 1. Search Tours
        if ($type === 'all' || $type === 'tours') {
            $sql = "SELECT id, title, slug, price_adult as price, 
                    (SELECT image_path FROM tour_images WHERE tour_id = tours.id AND is_cover = 1 LIMIT 1) as thumb,
                    'tour' as type
                    FROM tours 
                    WHERE is_active = 1 
                    AND (title LIKE :q OR description_short LIKE :q)
                    LIMIT 5";
            $stmt = $db->prepare($sql);
            $stmt->execute([':q' => $searchTerm]);
            $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Format thumb path if needed
            foreach ($tours as &$t) {
                if (!$t['thumb'])
                    $t['thumb'] = 'assets/images/placeholder.jpg';
                else
                    $t['thumb'] = 'assets/uploads/' . $t['thumb']; // Ensure full path relative to public
            }
            $results = array_merge($results, $tours);
        }

        // 2. Search Articles (Blog)
        if ($type === 'all' || $type === 'blog') {
            // Only if we haven't filled up 10 results yet
            $sql = "SELECT id, title, slug, '0' as price, image_path as thumb, 'blog' as type
                     FROM articles 
                     WHERE is_published = 1
                     AND (title LIKE :q OR summary LIKE :q)
                     LIMIT 5";
            $stmt = $db->prepare($sql);
            $stmt->execute([':q' => $searchTerm]);
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $results = array_merge($results, $articles);
        }

        echo json_encode($results);
        exit;
    }
}
