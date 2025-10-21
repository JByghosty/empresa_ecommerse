<?php
// debug_categories.php
require_once 'app/config/config.php';
require_once 'app/models/Database.php';
require_once 'app/models/Category.php';
require_once 'app/models/Product.php';

$database = new Database();
$db = $database->getConnection();

$category = new Category($db);
$product = new Product($db);

echo "<h1>Debug de Categorías y Productos</h1>";

echo "<h3>Categorías existentes:</h3>";
$categories = $category->read();
while($cat = $categories->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$cat['id_categoria']} - {$cat['nombre_categoria']}<br>";
}

echo "<h3>Productos y sus categorías:</h3>";
$products = $product->read();
while($prod = $products->fetch(PDO::FETCH_ASSOC)) {
    echo "Producto: {$prod['nombre_producto']} - Categoría ID: {$prod['id_categoria']}<br>";
}
?>