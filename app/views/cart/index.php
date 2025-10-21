<?php
// app/views/cart/index.php
require_once __DIR__ . '/../../config/config.php';

$page_title = "Carrito de Compras";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <h1 class="text-center mb-4">Carrito de Compras</h1>
    
    <?php if(empty($_SESSION['cart'])): ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-shopping-cart fa-2x mb-3"></i>
            <h4>Tu carrito está vacío</h4>
            <p>Agrega algunos productos para continuar con tu compra.</p>
            <a href="<?php echo APP_URL; ?>/public/products" class="btn btn-primary">
                <i class="fas fa-shopping-bag me-2"></i>Ir a Productos
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Productos en el Carrito</h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        require_once __DIR__ . '/../../models/Database.php';
                        require_once __DIR__ . '/../../models/Product.php';
                        
                        $database = new Database();
                        $db = $database->getConnection();
                        $productModel = new Product($db);
                        
                        $total = 0;
                        $cart_has_items = false;
                        
                        foreach($_SESSION['cart'] as $product_id => $quantity): 
                            $productModel->id_producto = $product_id;
                            $product = $productModel->readOne();
                            
                            if($product && is_array($product)):
                                $cart_has_items = true;
                                $subtotal = $product['precio'] * $quantity;
                                $total += $subtotal;
                        ?>
                            <div class="card mb-3 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <img src="<?php echo APP_URL; ?>/uploads/<?php echo $product['imagen_principal'] ?: 'default.jpg'; ?>" 
                                                 class="img-fluid rounded" 
                                                 alt="<?php echo htmlspecialchars($product['nombre_producto']); ?>"
                                                 style="height: 80px; object-fit: cover;">
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="card-title mb-1"><?php echo htmlspecialchars($product['nombre_producto']); ?></h6>
                                            <small class="text-muted">
                                                <?php echo substr(htmlspecialchars($product['descripcion']), 0, 50); ?>...
                                            </small>
                                        </div>
                                        <div class="col-md-2">
                                            <span class="h6 text-primary">$<?php echo number_format($product['precio'], 2); ?></span>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Cant:</span>
                                                <input type="number" class="form-control" value="<?php echo $quantity; ?>" min="1" max="<?php echo $product['stock']; ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <span class="h6">$<?php echo number_format($subtotal, 2); ?></span>
                                            <a href="<?php echo APP_URL; ?>/public/cart/remove/<?php echo $product_id; ?>" class="btn btn-sm btn-outline-danger ms-2" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                        
                        <?php if (!$cart_has_items): ?>
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                                <h5>Productos no disponibles</h5>
                                <p>Los productos en tu carrito ya no están disponibles.</p>
                                <a href="<?php echo APP_URL; ?>/public/products" class="btn btn-primary">
                                    <i class="fas fa-shopping-bag me-2"></i>Ver Productos Disponibles
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <?php if($cart_has_items): ?>
            <div class="col-md-4">
                <div class="card shadow border-0">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Resumen del Pedido</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Envío:</span>
                            <span>$0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="h5 text-success">$<?php echo number_format($total, 2); ?></strong>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="<?php echo APP_URL; ?>/public/products" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Seguir Comprando
                            </a>
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <button class="btn btn-success">
                                    <i class="fas fa-credit-card me-2"></i>Proceder al Pago
                                </button>
                            <?php else: ?>
                                <a href="<?php echo APP_URL; ?>/public/login" class="btn btn-warning">
                                    <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión para Comprar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>