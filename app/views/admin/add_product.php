<?php
// app/views/admin/add_product.php
require_once __DIR__ . '/../../config/config.php';

// Verificar que es admin
if (!isAdmin()) {
    $_SESSION['error'] = "No tienes permisos para acceder a esta página.";
    header('Location: ' . APP_URL . '/public/login');
    exit();
}

$page_title = "Agregar Producto";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/public/">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/public/profile">Mi Perfil</a></li>
            <li class="breadcrumb-item active">Agregar Producto</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Agregar Nuevo Producto</h4>
                </div>
                <div class="card-body p-4">
                    <form action="<?php echo APP_URL; ?>/public/admin/process-add-product" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre_producto" class="form-label">Nombre del Producto *</label>
                                <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="id_categoria" class="form-label">Categoría *</label>
                                <select class="form-select" id="id_categoria" name="id_categoria" required>
                                    <option value="">Seleccionar categoría...</option>
                                    <?php
                                    require_once __DIR__ . '/../../models/Database.php';
                                    require_once __DIR__ . '/../../models/Category.php';
                                    
                                    $database = new Database();
                                    $db = $database->getConnection();
                                    $category = new Category($db);
                                    
                                    $categories = $category->read();
                                    while($cat = $categories->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='{$cat['id_categoria']}'>{$cat['nombre_categoria']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción *</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="precio" class="form-label">Precio *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="precio" name="precio" step="0.01" min="0" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="precio_original" class="form-label">Precio Original (opcional)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="precio_original" name="precio_original" step="0.01" min="0">
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="stock" class="form-label">Stock *</label>
                                <input type="number" class="form-control" id="stock" name="stock" min="0" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="imagen_principal" class="form-label">Nombre de la Imagen</label>
                                <input type="text" class="form-control" id="imagen_principal" name="imagen_principal" placeholder="ej: producto.jpg">
                                <div class="form-text">Nombre del archivo de imagen en la carpeta uploads</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Opciones</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="destacado" name="destacado" value="1">
                                    <label class="form-check-label" for="destacado">
                                        Producto Destacado
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" checked>
                                    <label class="form-check-label" for="activo">
                                        Producto Activo
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo APP_URL; ?>/public/profile" class="btn btn-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Guardar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>