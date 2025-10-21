<?php
require_once __DIR__ . '/../app/config/config.php';

echo "<h1>Debug del Carrito</h1>";

echo "<h3>Contenido del carrito:</h3>";
echo "<pre>";
print_r($_SESSION['cart'] ?? 'Carrito vacío');
echo "</pre>";

echo "<h3>Productos en la base de datos:</h3>";
require_once __DIR__ . '/../app/models/Database.php';
require_once __DIR__ . '/../app/models/Product.php';

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

$products = $product->read();
while($row = $products->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['id_producto']} - {$row['nombre_producto']}<br>";
}
?>