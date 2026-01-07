<?php
require __DIR__ . '/src/Config/Database.php';
use App\Config\Database;

$db = Database::getConnection();
$stmt = $db->query("SHOW COLUMNS FROM pages");
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "Columns in 'pages' table:\n";
foreach ($columns as $col) {
    echo "- $col\n";
}

if (in_array('template', $columns) && in_array('meta_data', $columns)) {
    echo "\nSUCCESS: Required columns found.";
    exit(0);
} else {
    echo "\nERROR: Missing columns.";
    exit(1);
}
