<?php
// app/views/admin/upload_image.php
require_once __DIR__ . '/../../config/config.php';

// Verificar que es admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . APP_URL . '/public/login');
    exit();
}

$page_title = "Subir Imagen";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <h1 class="text-center mb-4">Subir Imagen de Producto</h1>
    
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-body">
                    <form action="<?php echo APP_URL; ?>/public/admin/process-upload" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Producto</label>
                            <select class="form-select" id="product_id" name="product_id" required>
                                <option value="">Seleccionar producto...</option>
                                <?php
                                require_once __DIR__ . '/../../models/Database.php';
                                require_once __DIR__ . '/../../models/Product.php';
                                
                                $database = new Database();
                                $db = $database->getConnection();
                                $product = new Product($db);
                                
                                $products = $product->read();
                                while($row = $products->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$row['id_producto']}'>{$row['nombre_producto']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="product_image" class="form-label">Imagen del Producto</label>
                            <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*" required>
                            <div class="form-text">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB</div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Subir Imagen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>