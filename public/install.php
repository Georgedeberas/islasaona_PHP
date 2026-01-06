<?php
// Script de instalación simple
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getConnection();

    $sql = file_get_contents(__DIR__ . '/../database/schema.sql');

    // Separar queries si es necesario, o ejecutar directo si PDO lo soporta (PDO soporta multi-query en MySQL por defecto en algunas configs, pero mejor loop)
    // Para simplificar, PDO->exec puede correr múltiples comandos separados por punto y coma.

    $db->exec($sql);

    echo "<h1>Instalación Exitosa</h1>";
    echo "<p>Las tablas han sido creadas correctamente.</p>";
    echo "<p>Usuario Admin por defecto creado: <strong>admin@mochilerosrd.com</strong></p>";
    echo "<p>Contraseña (Hasheada en SQL, update si es necesario): <strong>admin123</strong> (Si usaste el hash del schema)</p>";
    echo "<br><p style='color:red'>POR FAVOR BORRA ESTE ARCHIVO (install.php) DESPUÉS DE USARLO.</p>";

} catch (Exception $e) {
    die("Error durante la instalación: " . $e->getMessage());
}
