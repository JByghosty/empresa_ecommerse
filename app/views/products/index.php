<?php
// app/views/products/index.php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Database.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/Category.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
$category = new Category($db);

// Configuración de paginación
$products_per_page = 8;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $products_per_page;

// Manejar filtro de categoría
$selected_category = isset($_GET['category']) ? intval($_GET['category']) : null;
$category_name = "Todos los productos";

// Obtener productos según el filtro
if ($selected_category && $selected_category > 0) {
    // Obtener nombre de la categoría seleccionada
    $cat_stmt = $category->readOne($selected_category);
    if ($cat_stmt) {
        $category_data = $cat_stmt->fetch(PDO::FETCH_ASSOC);
        $category_name = $category_data['nombre_categoria'] ?? "Categoría";
    }
    
    $products = $product->readByCategoryPaginated($selected_category, $products_per_page, $offset);
    $total_products = $product->getCountByCategory($selected_category);
} else {
    $products = $product->readPaginated($products_per_page, $offset);
    $total_products = $product->getTotalCount();
}

$total_pages = ceil($total_products / $products_per_page);
$categories = $category->read();

$page_title = "Productos" . ($selected_category ? " - " . $category_name : "");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php require_once __DIR__ . '/../layouts/header.php'; ?>
    
    <!-- Header de Productos -->
    <section class="bg-primary text-white py-4 mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-2"><?php echo $category_name; ?></h1>
                    <p class="lead mb-0">
                        Mostrando <?php echo $products->rowCount(); ?> de <?php echo $total_products; ?> productos
                        <?php if($selected_category): ?>
                            en esta categoría
                        <?php endif; ?>
                        <?php if($total_pages > 1): ?>
                            - Página <?php echo $current_page; ?> de <?php echo $total_pages; ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="badge bg-light text-primary fs-6 p-2">
                        <i class="fas fa-boxes me-2"></i>
                        <?php echo $total_products; ?> productos
                    </span>
                </div>
            </div>
        </div>
    </section>

    <div class="container mb-5">
        <!-- Filtros por categoría -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-filter me-2 text-primary"></i>Filtrar por categoría
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?php echo APP_URL; ?>/public/products" 
                               class="btn <?php echo !$selected_category ? 'btn-primary' : 'btn-outline-primary'; ?> btn-sm">
                                <i class="fas fa-th-large me-1"></i> Todos
                            </a>
                            <?php 
                            $categories->execute(); // Reiniciar el cursor
                            while($cat = $categories->fetch(PDO::FETCH_ASSOC)): 
                                $is_active = $selected_category == $cat['id_categoria'];
                            ?>
                                <a href="<?php echo APP_URL; ?>/public/products?category=<?php echo $cat['id_categoria']; ?>" 
                                   class="btn <?php echo $is_active ? 'btn-secondary' : 'btn-outline-secondary'; ?> btn-sm">
                                    <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de productos -->
        <div class="row">
            <?php if($products->rowCount() > 0): ?>
                <?php while($row = $products->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow border-0 product-card">
                        <?php if($row['precio_original'] && $row['precio_original'] > $row['precio']): ?>
                            <span class="position-absolute top-0 end-0 m-2 badge bg-danger fs-6">
                                -<?php echo number_format((($row['precio_original'] - $row['precio']) / $row['precio_original']) * 100, 0); ?>%
                            </span>
                        <?php endif; ?>
                        
                        <!-- Imagen del producto -->
                        <img src="<?php echo APP_URL; ?>/uploads/<?php echo $row['imagen_principal'] ?: 'default.jpg'; ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($row['nombre_producto']); ?>"
                             style="height: 250px; object-fit: cover;">
                        
                        <div class="card-body d-flex flex-column">
                            <!-- Nombre y descripción -->
                            <h5 class="card-title fw-bold text-dark mb-2">
                                <?php echo htmlspecialchars($row['nombre_producto']); ?>
                            </h5>
                            
                            <p class="card-text text-muted small flex-grow-1 mb-3">
                                <?php echo substr(htmlspecialchars($row['descripcion']), 0, 100); ?>
                                <?php if(strlen($row['descripcion']) > 100): ?>...<?php endif; ?>
                            </p>

                            <!-- Stock -->
                            <div class="mb-3">
                                <?php if($row['stock'] > 0): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>En stock
                                    </span>
                                    <small class="text-muted ms-2">(<?php echo $row['stock']; ?> disponibles)</small>
                                <?php else: ?>
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times me-1"></i>Sin stock
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Precio -->
                            <div class="price mb-3">
                                <span class="h4 text-primary fw-bold">$<?php echo number_format($row['precio'], 2); ?></span>
                                <?php if($row['precio_original'] && $row['precio_original'] > $row['precio']): ?>
                                    <small class="text-muted text-decoration-line-through ms-2 fs-6">
                                        $<?php echo number_format($row['precio_original'], 2); ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Botones de acción -->
                            <div class="d-grid gap-2">
                                <a href="<?php echo APP_URL; ?>/public/products/show/<?php echo $row['id_producto']; ?>" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i> Ver Detalles
                                </a>
                                <?php if($row['stock'] > 0): ?>
                                    <a href="<?php echo APP_URL; ?>/public/cart/add/<?php echo $row['id_producto']; ?>" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-cart-plus me-1"></i> Agregar al Carrito
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>
                                        <i class="fas fa-times me-1"></i> Sin Stock
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <div class="card shadow border-0">
                        <div class="card-body py-5">
                            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                            <h3 class="text-muted">
                                <?php if($selected_category): ?>
                                    No hay productos en esta categoría
                                <?php else: ?>
                                    No hay productos disponibles
                                <?php endif; ?>
                            </h3>
                            <p class="text-muted mb-4">
                                <?php if($selected_category): ?>
                                    Próximamente agregaremos productos a esta categoría.
                                <?php else: ?>
                                    Próximamente agregaremos más productos a nuestra tienda.
                                <?php endif; ?>
                            </p>
                            <a href="<?php echo APP_URL; ?>/public/products" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i>Volver a Todos los Productos
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Paginación con filtros -->
        <?php if($total_pages > 1): ?>
        <nav aria-label="Paginación de productos" class="mt-5">
            <ul class="pagination justify-content-center">
                <!-- Botón Anterior -->
                <li class="page-item <?php echo $current_page == 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo APP_URL; ?>/public/products?<?php echo $selected_category ? "category=$selected_category&" : ''; ?>page=<?php echo $current_page - 1; ?>" aria-label="Anterior">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                
                <!-- Números de página -->
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                        <a class="page-link" href="<?php echo APP_URL; ?>/public/products?<?php echo $selected_category ? "category=$selected_category&" : ''; ?>page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <!-- Botón Siguiente -->
                <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo APP_URL; ?>/public/products?<?php echo $selected_category ? "category=$selected_category&" : ''; ?>page=<?php echo $current_page + 1; ?>" aria-label="Siguiente">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>

    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>

    <style>
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }
    .card-img-top {
        border-bottom: 1px solid #dee2e6;
    }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>