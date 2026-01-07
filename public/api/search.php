<?php
// public/api_search.php
// Endpoint ligero para Live Search

require_once __DIR__ . '/../src/Config/Database.php';

header('Content-Type: application/json');

$q = $_GET['q'] ?? '';

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $db = \App\Config\Database::getConnection();

    // Buscar en título y descripción corta
    $sql = "SELECT id, title, slug, price_adult as price, 
            (SELECT image_path FROM tour_images WHERE tour_id = tours.id AND is_cover = 1 LIMIT 1) as thumb 
            FROM tours 
            WHERE is_active = 1 
            AND (title LIKE :q OR description_short LIKE :q OR keywords LIKE :q)
            LIMIT 5";

    $stmt = $db->prepare($sql);
    $term = "%$q%";
    $stmt->bindValue(':q', $term);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Normalizar thumb
    foreach ($results as &$row) {
        if (empty($row['thumb'])) {
            $row['thumb'] = 'assets/images/placeholder_thumb.png';
        }
    }

    echo json_encode($results);

} catch (Exception $e) {
    echo json_encode(['error' => 'Server error']);
}
