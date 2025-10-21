<?php
// app/views/products/show.php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Database.php';
require_once __DIR__ . '/../../models/Product.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);

// Obtener ID del producto
$product_id = $_GET['id'] ?? 0;

if ($product_id > 0) {
    $product->id_producto = $product_id;
    $product_data = $product->readOne();
} else {
    $product_data = false;
}

// Si no encuentra el producto, redirigir
if (!$product_data) {
    $_SESSION['error'] = "El producto que buscas no existe.";
    header('Location: ' . APP_URL . '/public/products');
    exit();
}

$page_title = $product_data['nombre_producto'];
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/public/">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/public/products">Productos</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($product_data['nombre_producto']); ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-6">
            <img src="<?php echo APP_URL; ?>/uploads/<?php echo $product_data['imagen_principal'] ?: 'default.jpg'; ?>" 
                 class="img-fluid rounded shadow" 
                 alt="<?php echo htmlspecialchars($product_data['nombre_producto']); ?>"
                 style="max-height: 500px; object-fit: cover;">
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <?php if(isset($product_data['precio_original']) && $product_data['precio_original'] > $product_data['precio']): ?>
                        <span class="badge bg-danger fs-6 mb-3">
                            -<?php echo number_format((($product_data['precio_original'] - $product_data['precio']) / $product_data['precio_original']) * 100, 0); ?>% DESCUENTO
                        </span>
                    <?php endif; ?>
                    
                    <h1 class="card-title fw-bold text-primary mb-3"><?php echo htmlspecialchars($product_data['nombre_producto']); ?></h1>
                    
                    <p class="card-text text-muted mb-4 fs-5"><?php echo htmlspecialchars($product_data['descripcion'] ?? 'Descripción no disponible'); ?></p>
                    
                    <div class="price mb-4">
                        <span class="h2 text-primary fw-bold">$<?php echo number_format($product_data['precio'], 2); ?></span>
                        <?php if(isset($product_data['precio_original']) && $product_data['precio_original'] > 0): ?>
                            <small class="text-muted text-decoration-line-through ms-3 fs-4">
                                $<?php echo number_format($product_data['precio_original'], 2); ?>
                            </small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="stock mb-4">
                        <?php if($product_data['stock'] > 0): ?>
                            <span class="badge bg-success fs-6 p-2">
                                <i class="fas fa-check me-1"></i> En stock (<?php echo $product_data['stock']; ?> disponibles)
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger fs-6 p-2">
                                <i class="fas fa-times me-1"></i> Sin stock
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex">
                        <?php if($product_data['stock'] > 0): ?>
                            <a href="<?php echo APP_URL; ?>/public/cart/add/<?php echo $product_data['id_producto']; ?>" 
                               class="btn btn-primary btn-lg flex-fill">
                                <i class="fas fa-cart-plus me-2"></i> Agregar al Carrito
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-lg flex-fill" disabled>
                                <i class="fas fa-times me-2"></i> Sin Stock
                            </button>
                        <?php endif; ?>
                        
                        <a href="<?php echo APP_URL; ?>/public/products" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i> Seguir Comprando
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>