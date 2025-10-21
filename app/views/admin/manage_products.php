<?php
// app/views/admin/manage_products.php
require_once __DIR__ . '/../../config/config.php';

// Verificar que es admin
if (!isAdmin()) {
    $_SESSION['error'] = "No tienes permisos para acceder a esta página.";
    header('Location: ' . APP_URL . '/public/login');
    exit();
}

require_once __DIR__ . '/../../models/Database.php';
require_once __DIR__ . '/../../models/Product.php';

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

// Obtener todos los productos
$products = $product->read();

$page_title = "Gestionar Productos";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/public/">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/public/profile">Mi Perfil</a></li>
            <li class="breadcrumb-item active">Gestionar Productos</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary">
            <i class="fas fa-boxes me-2"></i>Gestionar Productos
        </h1>
        <a href="<?php echo APP_URL; ?>/public/admin/add-product" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Agregar Producto
        </a>
    </div>

    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Lista de Productos (<?php echo $products->rowCount(); ?>)</h5>
        </div>
        <div class="card-body p-0">
            <?php if($products->rowCount() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Imagen</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Categoría</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $products->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td class="fw-bold"><?php echo $row['id_producto']; ?></td>
                                <td>
                                    <img src="<?php echo APP_URL; ?>/uploads/<?php echo $row['imagen_principal'] ?: 'default.jpg'; ?>" 
                                         class="rounded" 
                                         alt="<?php echo htmlspecialchars($row['nombre_producto']); ?>"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['nombre_producto']); ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo substr(htmlspecialchars($row['descripcion']), 0, 50); ?>...
                                    </small>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">$<?php echo number_format($row['precio'], 2); ?></span>
                                    <?php if($row['precio_original'] > $row['precio']): ?>
                                        <br>
                                        <small class="text-muted text-decoration-line-through">
                                            $<?php echo number_format($row['precio_original'], 2); ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($row['stock'] > 0): ?>
                                        <span class="badge bg-success"><?php echo $row['stock']; ?> unidades</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Sin stock</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($row['nombre_categoria']); ?></span>
                                </td>
                                <td>
                                    <?php if($row['activo'] == 1): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactivo</span>
                                    <?php endif; ?>
                                    <?php if($row['destacado'] == 1): ?>
                                        <br>
                                        <span class="badge bg-warning mt-1">Destacado</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo APP_URL; ?>/public/products/show/<?php echo $row['id_producto']; ?>" 
                                           class="btn btn-outline-primary" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" 
                                           class="btn btn-outline-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo APP_URL; ?>/public/admin/delete-product/<?php echo $row['id_producto']; ?>" 
                                           class="btn btn-outline-danger" 
                                           title="Eliminar"
                                           onclick="return confirm('¿Estás seguro de que quieres eliminar el producto: <?php echo addslashes($row['nombre_producto']); ?>?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No hay productos</h4>
                    <p class="text-muted mb-4">Comienza agregando tu primer producto.</p>
                    <a href="<?php echo APP_URL; ?>/public/admin/add-product" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Agregar Primer Producto
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>