<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../src/Config/Database.php';
use App\Config\Database;

try {
    echo "<h1>Migration: Seasonal Pricing (Phase 1)</h1>";
    $db = Database::getConnection();

    // Check if column exists
    $columns = $db->query("SHOW COLUMNS FROM tours")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('price_rules', $columns)) {
        echo "Creating `price_rules` column... ";
        $sql = "ALTER TABLE tours ADD COLUMN price_rules JSON NULL COMMENT 'Seasonal pricing rules'";
        $db->exec($sql);
        echo "<span style='color:green'>SUCCESS</span><br>";
    } else {
        echo "`price_rules` column already exists.<br>";
    }

    echo "<hr>";
    echo "<h3>Current Columns in `tours`:</h3><ul>";
    $columns = $db->query("SHOW COLUMNS FROM tours")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($columns as $col) {
        echo "<li>" . $col . "</li>";
    }
    echo "</ul>";

} catch (Exception $e) {
    echo "<h2 style='color:red'>Error: " . $e->getMessage() . "</h2>";
}
