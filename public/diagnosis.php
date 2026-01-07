<?php
// public/diagnosis.php
// Script de Diagnóstico del Sistema - Mochileros RD
// NO DEJAR EN PRODUCCIÓN PERMANENTEMENTE

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html><html><head><title>System Diagnosis</title>";
echo "<style>body{font-family:monospace; padding:20px; background:#f0f0f0;} .card{background:white; padding:20px; margin-bottom:20px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1);} h2{color:#333; border-bottom:2px solid #ddd; padding-bottom:5px;} .ok{color:green;} .err{color:red; font-weight:bold;} code{background:#eee; padding:2px 5px; border-radius:3px;}</style>";
echo "</head><body><h1>🕵️ System Diagnosis - Mochileros RD</h1>";

// 1. Environment
echo "<div class='card'><h2>1. Environment</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Loaded Extensions: " . implode(", ", get_loaded_extensions());
echo "</div>";

// 2. Database
echo "<div class='card'><h2>2. Database Connection</h2>";
require_once __DIR__ . '/../src/Config/Database.php';
try {
    $db = \App\Config\Database::getConnection();
    echo "<span class='ok'>✅ Connection Successful</span><br>";
    echo "<h3>Tables:</h3><ul>";
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $t) {
        $count = $db->query("SELECT COUNT(*) FROM $t")->fetchColumn();
        echo "<li><strong>$t</strong> ($count rows)</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "<span class='err'>❌ Connection Failed: " . $e->getMessage() . "</span>";
}
echo "</div>";

// 3. Filesystem & Images
echo "<div class='card'><h2>3. Filesystem Check</h2>";
$paths = [
    'assets/uploads',
    'assets/images',
    '../error_log.php'
];

foreach ($paths as $p) {
    $fullPath = __DIR__ . '/' . $p;
    $exists = file_exists($fullPath) ? "<span class='ok'>Yes</span>" : "<span class='err'>No</span>";
    $writable = is_writable($fullPath) ? "<span class='ok'>Yes</span>" : "<span class='err'>No</span>";
    $perms = substr(sprintf('%o', fileperms($fullPath)), -4);

    echo "<strong>$p</strong>: Exists: $exists | Writable: $writable | Perms: $perms<br>";

    if (is_dir($fullPath)) {
        $files = array_diff(scandir($fullPath), ['.', '..']);
        echo "<details><summary>List files (" . count($files) . ")</summary><pre>" . implode("\n", array_slice($files, 0, 20)) . (count($files) > 20 ? "\n...and more" : "") . "</pre></details><br>";
    }
}
echo "</div>";

// 4. Last Errors
echo "<div class='card'><h2>4. Last 50 Errors</h2>";
$logFile = __DIR__ . '/../error_log.php';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $lastMany = array_slice($lines, -50);
    echo "<pre style='background:#333; color:#f88; padding:10px; overflow-x:auto;'>" . implode("", $lastMany) . "</pre>";
} else {
    echo "Log file not found.";
}
echo "</div>";

// 5. Config Dump (Safe)
echo "<div class='card'><h2>5. Settings Table Dump</h2>";
if (isset($db)) {
    $settings = $db->query("SELECT setting_key, setting_value FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
    echo "<table>";
    foreach ($settings as $k => $v)
        echo "<tr><td><strong>$k</strong></td><td>" . htmlspecialchars(substr($v, 0, 50)) . (strlen($v) > 50 ? "..." : "") . "</td></tr>";
    echo "</table>";
}
echo "</div>";

echo "</body></html>";
?>