<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/models/Database.php';
require_once __DIR__ . '/../app/models/Product.php';

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

echo "<h1>Productos en la base de datos:</h1>";
$products = $product->read();
while($row = $products->fetch(PDO::FETCH_ASSOC)) {
    echo "<p>ID: {$row['id_producto']} - Nombre: {$row['nombre_producto']}</p>";
}
?>