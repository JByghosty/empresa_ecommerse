<?php
// app/views/admin/stats.php
require_once __DIR__ . '/../../config/config.php';

// Verificar que es admin
if (!isAdmin()) {
    $_SESSION['error'] = "No tienes permisos para acceder a esta página.";
    header('Location: ' . APP_URL . '/public/login');
    exit();
}

require_once __DIR__ . '/../../models/Database.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/Category.php';

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);
$category = new Category($db);

// Obtener estadísticas
$total_products = $product->getTotalCount();
$total_categories = $category->read()->rowCount();

// Productos por categoría
$categories_with_count = $category->readWithProductCount();

// Productos destacados
$featured_products = $product->readFeatured()->rowCount();

// Productos sin stock
$query = "SELECT COUNT(*) as count FROM productos WHERE stock = 0 AND activo = 1";
$stmt = $db->prepare($query);
$stmt->execute();
$out_of_stock = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Productos con descuento
$query = "SELECT COUNT(*) as count FROM productos WHERE precio_original > precio AND activo = 1";
$stmt = $db->prepare($query);
$stmt->execute();
$discount_products = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$page_title = "Estadísticas";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/public/">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/public/profile">Mi Perfil</a></li>
            <li class="breadcrumb-item active">Estadísticas</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary">
            <i class="fas fa-chart-bar me-2"></i>Estadísticas de la Tienda
        </h1>
        <div class="text-muted">
            <i class="fas fa-calendar me-1"></i>
            <?php echo date('d/m/Y'); ?>
        </div>
    </div>

    <!-- Resumen General -->
    <div class="row mb-5">
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-primary shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-boxes fa-2x text-primary mb-2"></i>
                    <h3 class="text-primary"><?php echo $total_products; ?></h3>
                    <p class="text-muted mb-0">Total Productos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-success shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-tags fa-2x text-success mb-2"></i>
                    <h3 class="text-success"><?php echo $total_categories; ?></h3>
                    <p class="text-muted mb-0">Categorías</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-warning shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-2x text-warning mb-2"></i>
                    <h3 class="text-warning"><?php echo $featured_products; ?></h3>
                    <p class="text-muted mb-0">Destacados</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-info shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-percentage fa-2x text-info mb-2"></i>
                    <h3 class="text-info"><?php echo $discount_products; ?></h3>
                    <p class="text-muted mb-0">En Oferta</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Productos por Categoría -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Productos por Categoría</h5>
                </div>
                <div class="card-body">
                    <?php if($categories_with_count->rowCount() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Categoría</th>
                                        <th class="text-center">Productos</th>
                                        <th class="text-center">Porcentaje</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total_all_products = $total_products > 0 ? $total_products : 1; // Evitar división por cero
                                    while($cat = $categories_with_count->fetch(PDO::FETCH_ASSOC)): 
                                        $percentage = round(($cat['total_productos'] / $total_all_products) * 100, 1);
                                    ?>
                                    <tr>
                                        <td>
                                            <i class="fas fa-folder me-2 text-muted"></i>
                                            <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary"><?php echo $cat['total_productos']; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" 
                                                     role="progressbar" 
                                                     style="width: <?php echo $percentage; ?>%"
                                                     aria-valuenow="<?php echo $percentage; ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    <?php echo $percentage; ?>%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay datos de categorías disponibles.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Estado del Inventario -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Estado del Inventario</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3 bg-light">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <h4 class="text-success"><?php echo $total_products - $out_of_stock; ?></h4>
                                <small class="text-muted">Con Stock</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3 bg-light">
                                <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                                <h4 class="text-danger"><?php echo $out_of_stock; ?></h4>
                                <small class="text-muted">Sin Stock</small>
                            </div>
                        </div>
                    </div>
                    
                    <?php if($out_of_stock > 0): ?>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>¡Atención!</strong> Tienes <?php echo $out_of_stock; ?> producto(s) sin stock.
                        <a href="<?php echo APP_URL; ?>/public/admin/manage-products" class="alert-link">Revisar inventario</a>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>¡Excelente!</strong> Todos los productos tienen stock disponible.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6 mb-2">
                            <a href="<?php echo APP_URL; ?>/public/admin/add-product" class="btn btn-outline-success btn-sm w-100">
                                <i class="fas fa-plus me-1"></i>Agregar Producto
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <a href="<?php echo APP_URL; ?>/public/admin/manage-products" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-edit me-1"></i>Gestionar Productos
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <a href="<?php echo APP_URL; ?>/public/admin/update-images" class="btn btn-outline-warning btn-sm w-100">
                                <i class="fas fa-image me-1"></i>Actualizar Imágenes
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <a href="<?php echo APP_URL; ?>/public/products" class="btn btn-outline-info btn-sm w-100">
                                <i class="fas fa-eye me-1"></i>Ver Tienda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>