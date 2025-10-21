<?php
require_once '../config/config.php';

// Verificar si es admin
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../home.php");
    exit();
}

require_once '../../models/Database.php';
require_once '../../models/Product.php';
require_once '../../models/User.php';
require_once '../../models/Order.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
$user = new User($db);
$order = new Order($db);

// Estadísticas
$total_products = $product->getTotalCount();
$total_users = $user->getTotalCount();
$total_orders = $order->getTotalCount();
$recent_orders = $order->getRecentOrders();

$page_title = "Panel de Administración";
require_once '../layouts/header.php';
?>

<div class="container-fluid mt-4">
    <h1><i class="fas fa-tachometer-alt"></i> Panel de Administración</h1>
    
    <!-- Estadísticas -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $total_products; ?></h4>
                            <p>Productos</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $total_users; ?></h4>
                            <p>Usuarios</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $total_orders; ?></h4>
                            <p>Pedidos</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pedidos Recientes -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Pedidos Recientes</h5>
                </div>
                <div class="card-body">
                    <?php if($recent_orders && $recent_orders->rowCount() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($order_row = $recent_orders->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td>#<?php echo $order_row['id_pedido']; ?></td>
                                        <td><?php echo htmlspecialchars($order_row['nombre'] ?? 'N/A'); ?></td>
                                        <td>$<?php echo number_format($order_row['total'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                switch($order_row['estado']) {
                                                    case 'pendiente': echo 'warning'; break;
                                                    case 'confirmado': echo 'info'; break;
                                                    case 'enviado': echo 'primary'; break;
                                                    case 'entregado': echo 'success'; break;
                                                    case 'cancelado': echo 'danger'; break;
                                                    default: echo 'secondary';
                                                }
                                            ?>"><?php echo ucfirst($order_row['estado']); ?></span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($order_row['fecha_pedido'])); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No hay pedidos recientes.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Acciones Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2 d-md-flex">
                        <a href="products.php" class="btn btn-primary me-2">
                            <i class="fas fa-box"></i> Gestionar Productos
                        </a>
                        <a href="orders.php" class="btn btn-success me-2">
                            <i class="fas fa-shopping-cart"></i> Ver Pedidos
                        </a>
                        <a href="users.php" class="btn btn-info me-2">
                            <i class="fas fa-users"></i> Gestionar Usuarios
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../layouts/footer.php'; ?>