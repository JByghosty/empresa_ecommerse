<?php
// app/views/auth/profile.php
require_once __DIR__ . '/../../config/config.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/public/login');
    exit();
}

$page_title = "Mi Perfil";
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.profile-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 60px 0;
    margin-bottom: 30px;
}
.profile-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: white;
    margin: -60px auto 20px;
    border: 5px solid white;
}
.admin-badge {
    background: linear-gradient(45deg, #ff6b6b, #ee5a24);
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: bold;
    display: inline-block;
    margin-top: 10px;
}
</style>

<!-- Header del Perfil -->
<section class="profile-header text-center">
    <div class="container">
        <h1 class="display-5 fw-bold">Mi Perfil</h1>
        <p class="lead">Gestiona tu información personal</p>
        <?php if(isAdmin()): ?>
            <span class="admin-badge">
                <i class="fas fa-crown me-1"></i> Administrador
            </span>
        <?php endif; ?>
    </div>
</section>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Tarjeta Principal del Perfil -->
            <div class="card profile-card">
                <div class="card-body text-center p-5">
                    <!-- Avatar -->
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    
                    <h2 class="card-title fw-bold text-primary mb-2">
                        <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?>
                    </h2>
                    <p class="text-muted fs-5 mb-4">
                        <?php echo htmlspecialchars($_SESSION['user_email'] ?? 'Email no disponible'); ?>
                    </p>
                    
                    <!-- Sección de Administrador -->
                    <?php if(isAdmin()): ?>
                    <div class="row mb-5">
                        <div class="col-12">
                            <h4 class="text-center mb-4 text-success">
                                <i class="fas fa-tools me-2"></i>Panel de Administración
                            </h4>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card border-success h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-plus-circle fa-3x text-success mb-3"></i>
                                            <h5>Agregar Producto</h5>
                                            <p class="text-muted">Crear nuevo producto en la tienda</p>
                                            <a href="<?php echo APP_URL; ?>/public/admin/add-product" class="btn btn-success btn-sm">
                                                <i class="fas fa-plus me-2"></i>Agregar Producto
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card border-primary h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-edit fa-3x text-primary mb-3"></i>
                                            <h5>Gestionar Productos</h5>
                                            <p class="text-muted">Ver, editar y eliminar productos</p>
                                            <a href="<?php echo APP_URL; ?>/public/admin/manage-products" class="btn btn-primary btn-sm">
                                                <i class="fas fa-cog me-2"></i>Gestionar Productos
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card border-warning h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-image fa-3x text-warning mb-3"></i>
                                            <h5>Actualizar Imágenes</h5>
                                            <p class="text-muted">Asignar imágenes a productos</p>
                                            <a href="<?php echo APP_URL; ?>/public/admin/update-images" class="btn btn-warning btn-sm">
                                                <i class="fas fa-sync me-2"></i>Actualizar Imágenes
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Acciones rápidas adicionales -->
                            <div class="row mt-3">
                                <div class="col-md-6 mb-3">
                                    <div class="card border-info h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-database fa-3x text-info mb-3"></i>
                                            <h5>Datos de Prueba</h5>
                                            <p class="text-muted">Agregar productos de ejemplo</p>
                                            <a href="<?php echo APP_URL; ?>/public/admin/add-products" class="btn btn-info btn-sm">
                                                <i class="fas fa-download me-2"></i>Cargar Datos
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card border-secondary h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-chart-bar fa-3x text-secondary mb-3"></i>
                                            <h5>Estadísticas</h5>
                                            <p class="text-muted">Ver reportes de la tienda</p>
                                            <a href="#" class="btn btn-secondary btn-sm">
                                                <i class="fas fa-chart-line me-2"></i>Ver Estadísticas
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4">
                    <?php endif; ?>

                    <!-- Estadísticas del usuario -->
                    <div class="row mb-4">
                        <div class="col-md-3 col-6 mb-3">
                            <div class="border rounded p-3 bg-light">
                                <h4 class="text-primary fw-bold mb-1">0</h4>
                                <small class="text-muted">Pedidos</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="border rounded p-3 bg-light">
                                <h4 class="text-primary fw-bold mb-1">
                                    <?php 
                                    $cart_count = 0;
                                    if(isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                                        $cart_count = array_sum($_SESSION['cart']);
                                    }
                                    echo $cart_count;
                                    ?>
                                </h4>
                                <small class="text-muted">En Carrito</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="border rounded p-3 bg-light">
                                <h4 class="text-primary fw-bold mb-1">0</h4>
                                <small class="text-muted">Favoritos</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="border rounded p-3 bg-light">
                                <h4 class="text-primary fw-bold mb-1">0</h4>
                                <small class="text-muted">Comentarios</small>
                            </div>
                        </div>
                    </div>

                    <!-- Opciones del Perfil para todos los usuarios -->
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-shopping-bag fa-2x text-primary mb-3"></i>
                                    <h5>Mis Pedidos</h5>
                                    <p class="text-muted">Gestiona tus compras</p>
                                    <a href="#" class="btn btn-outline-primary btn-sm">Ver Pedidos</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-heart fa-2x text-danger mb-3"></i>
                                    <h5>Favoritos</h5>
                                    <p class="text-muted">Productos guardados</p>
                                    <a href="#" class="btn btn-outline-danger btn-sm">Ver Favoritos</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-address-card fa-2x text-success mb-3"></i>
                                    <h5>Información Personal</h5>
                                    <p class="text-muted">Actualiza tus datos</p>
                                    <a href="#" class="btn btn-outline-success btn-sm">Editar Perfil</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-lock fa-2x text-warning mb-3"></i>
                                    <h5>Seguridad</h5>
                                    <p class="text-muted">Cambia tu contraseña</p>
                                    <a href="#" class="btn btn-outline-warning btn-sm">Cambiar Clave</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="<?php echo APP_URL; ?>/public/" class="btn btn-primary me-2">
                            <i class="fas fa-home me-2"></i> Volver al Inicio
                        </a>
                        <a href="<?php echo APP_URL; ?>/public/products" class="btn btn-outline-primary me-2">
                            <i class="fas fa-shopping-bag me-2"></i> Ir a Productos
                        </a>
                        <a href="<?php echo APP_URL; ?>/public/logout" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>