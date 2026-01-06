<?php

spl_autoload_register(function ($class) {
    // Prefijo del namespace base
    $prefix = 'App\\';

    // Directorio base donde están las clases
    $base_dir = __DIR__ . '/';

    // Verificar si la clase usa el prefijo
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Obtener el nombre relativo de la clase
    $relative_class = substr($class, $len);

    // Reemplazar el prefijo con el directorio base, reemplazar los separadores de namespace con separadores de directorio y añadir .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Si el archivo existe, requerirlo
    if (file_exists($file)) {
        require $file;
    }
});
