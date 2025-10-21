<?php
// app/views/home.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
$category = new Category($db);

$featured_products = $product->readFeatured();
$categories = $category->read();

$page_title = "Inicio";
require_once __DIR__ . '/layouts/header.php';
?>

<!-- Hero Section con Bootstrap -->
<section class="bg-primary text-white py-5 mb-5">
    <div class="container text-center py-5">
        <h1 class="display-4 fw-bold mb-4">Bienvenido a <?php echo APP_NAME; ?></h1>
        <p class="lead fs-4 mb-4">Descubre los mejores productos al mejor precio</p>
        <a href="<?php echo APP_URL; ?>/public/products" class="btn btn-light btn-lg px-4 py-2 fw-bold">
            <i class="fas fa-shopping-bag me-2"></i> Comprar Ahora
        </a>
    </div>
</section>

<!-- Featured Products -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5 display-5 fw-bold text-primary">Productos Destacados</h2>
        <div class="row">
            <?php if($featured_products->rowCount() > 0): ?>
                <?php while($row = $featured_products->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card h-100 shadow border-0">
                        <?php if($row['precio_original'] && $row['precio_original'] > $row['precio']): ?>
                            <span class="position-absolute top-0 end-0 m-2 badge bg-danger">
                                -<?php echo number_format((($row['precio_original'] - $row['precio']) / $row['precio_original']) * 100, 0); ?>%
                            </span>
                        <?php endif; ?>
                        
                        <img src="<?php echo APP_URL; ?>/uploads/<?php echo $row['imagen_principal'] ?: 'default.jpg'; ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($row['nombre_producto']); ?>"
                             style="height: 200px; object-fit: cover;">
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($row['nombre_producto']); ?></h5>
                            <p class="card-text text-muted small flex-grow-1">
                                <?php echo substr($row['descripcion'], 0, 100); ?>...
                            </p>
                            
                            <div class="mt-auto">
                                <div class="price mb-3">
                                    <span class="h4 text-primary fw-bold">$<?php echo number_format($row['precio'], 2); ?></span>
                                    <?php if($row['precio_original']): ?>
                                        <small class="text-muted text-decoration-line-through ms-2 fs-6">
                                            $<?php echo number_format($row['precio_original'], 2); ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <a href="<?php echo APP_URL; ?>/public/products/show/<?php echo $row['id_producto']; ?>" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i> Ver Detalles
                                    </a>
                                    <?php if($row['stock'] > 0): ?>
                                        <a href="<?php echo APP_URL; ?>/public/cart/add/<?php echo $row['id_producto']; ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-cart-plus me-1"></i> Agregar
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
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <div class="alert alert-info border-0 shadow">
                        <i class="fas fa-info-circle me-2"></i> No hay productos destacados disponibles.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>