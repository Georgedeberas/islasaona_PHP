<?php
require __DIR__ . '/src/Config/Database.php';
use App\Config\Database;

try {
    echo "Starting Forced Migration...\n";
    $db = Database::getConnection();

    // Attempt 1: Add template
    try {
        $db->exec("ALTER TABLE pages ADD COLUMN template VARCHAR(50) DEFAULT 'classic'");
        echo "[OK] Added 'template' column.\n";
    } catch (PDOException $e) {
        echo "[INFO] Could not add 'template' (maybe exists?): " . $e->getMessage() . "\n";
    }

    // Attempt 2: Add meta_data
    try {
        $db->exec("ALTER TABLE pages ADD COLUMN meta_data LONGTEXT NULL"); // LONGTEXT for safety
        echo "[OK] Added 'meta_data' column.\n";
    } catch (PDOException $e) {
        echo "[INFO] Could not add 'meta_data' (maybe exists?): " . $e->getMessage() . "\n";
    }

    // Verification
    $stmt = $db->query("SHOW COLUMNS FROM pages");
    $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Current Columns: " . implode(", ", $cols) . "\n";

    if (in_array('template', $cols) && in_array('meta_data', $cols)) {
        echo "VERIFICATION SUCCESS: Columns present.\n";
        exit(0);
    } else {
        echo "VERIFICATION FAILED: Columns still missing.\n";
        exit(1);
    }

} catch (Exception $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
