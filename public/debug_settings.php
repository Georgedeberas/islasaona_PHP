<?php
require __DIR__ . '/../src/Config/Database.php';
use App\Config\Database;

$db = Database::getConnection();
echo "<pre>";
$res = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_ASSOC);
print_r($res);
echo "</pre>";
