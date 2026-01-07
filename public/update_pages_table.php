<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

try {
    echo "Connecting to DB...<br>";
    $db = Database::getConnection();

    // 1. Check if columns exist
    echo "Checking columns...<br>";
    $columns = $db->query("SHOW COLUMNS FROM pages")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('template', $columns)) {
        echo "Adding 'template' column...<br>";
        $db->exec("ALTER TABLE pages ADD COLUMN template VARCHAR(50) DEFAULT 'classic' AFTER title");
        echo "Added 'template'.<br>";
    } else {
        echo "'template' column already exists.<br>";
    }

    if (!in_array('meta_data', $columns)) {
        echo "Adding 'meta_data' column...<br>";
        $db->exec("ALTER TABLE pages ADD COLUMN meta_data TEXT NULL AFTER content");
        echo "Added 'meta_data'.<br>";
    } else {
        echo "'meta_data' column already exists.<br>";
    }

    echo "<h1>Migration Completed Successfully!</h1>";
    echo "<a href='/admin/dashboard'>Go back to Dashboard</a>";

} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    exit(1);
}
