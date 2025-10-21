<?php
// Script de instalación automática
echo "<h2>Instalando Base de Datos E-Commerce</h2>";

try {
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Crear base de datos
    $pdo->exec("CREATE DATABASE IF NOT EXISTS empresa_ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE empresa_ecommerce");
    
    // Ejecutar script SQL (aquí iría tu base de datos completa)
    $sql = file_get_contents('database.sql');
    $pdo->exec($sql);
    
    echo "<p style='color: green;'>✅ Base de datos instalada correctamente!</p>";
    echo "<p><a href='app/views/home.php'>Ir al sitio web</a></p>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>