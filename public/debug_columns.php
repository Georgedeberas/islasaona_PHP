<?php
// public/debug_columns.php
require __DIR__ . '/../src/Config/Database.php';
use App\Config\Database;

$db = Database::getConnection();
echo "<pre>";
try {
    echo "<h1>Debug Columnas Tabla Tours</h1>";
    $stmt = $db->query("SHOW COLUMNS FROM tours");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($columns as $col) {
        echo $col['Field'] . " (" . $col['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
echo "</pre>";
