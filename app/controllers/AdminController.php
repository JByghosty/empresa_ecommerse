<?php
// app/controllers/AdminController.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

class AdminController {
    private $db;
    private $product;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->product = new Product($this->db);
    }

    public function updateImages() {
        if (!isAdmin()) {
            $_SESSION['error'] = "No tienes permisos para acceder a esta función.";
            header('Location: ' . APP_URL . '/public/login');
            exit();
        }

        try {
            $query = "UPDATE productos SET imagen_principal = 
                        CASE 
                            WHEN id_producto = 1 THEN 'laptop-hp.jpg'
                            WHEN id_producto = 2 THEN 'samsung-galaxy.jpg'
                            WHEN id_producto = 3 THEN 'nike-camiseta.jpg'
                            WHEN id_producto = 4 THEN 'silla-ergonomica.jpg'
                        END
                      WHERE id_producto IN (1, 2, 3, 4)";
            
            $stmt = $this->db->prepare($query);
            
            if ($stmt->execute()) {
                $affected_rows = $stmt->rowCount();
                $_SESSION['success'] = "✅ Imágenes actualizadas correctamente. $affected_rows productos modificados.";
            } else {
                $_SESSION['error'] = "❌ Error al actualizar las imágenes en la base de datos.";
            }
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
        }

        header('Location: ' . APP_URL . '/public/products');
        exit();
    }

    public function addProducts() {
        if (!isAdmin()) {
            $_SESSION['error'] = "No tienes permisos para acceder a esta función.";
            header('Location: ' . APP_URL . '/public/login');
            exit();
        }

        try {
            $query = "INSERT INTO productos (id_categoria, nombre_producto, descripcion, precio, precio_original, stock, imagen_principal, destacado, activo) VALUES
                    (4, 'Tablet iPad Air', 'Tablet iPad Air con chip M1, pantalla Liquid Retina de 10.9 pulgadas, 64GB', 14999.00, 16999.00, 12, 'ipad-air.jpg', 1, 1),
                    (4, 'Smartwatch Apple Watch Series 8', 'Reloj inteligente con monitor de salud, GPS y resistencia al agua', 8999.00, 10999.00, 8, 'apple-watch.jpg', 0, 1),
                    (4, 'Auriculares Sony WH-1000XM4', 'Auriculares inalámbricos con cancelación de ruido y 30h de batería', 6999.00, 8499.00, 15, 'sony-headphones.jpg', 1, 1),
                    (1, 'Mesa de Centro Moderna', 'Mesa de centro de vidrio templado con base de metal, diseño moderno', 4500.00, 5200.00, 6, 'mesa-centro.jpg', 0, 1),
                    (1, 'Lámpara de Pie LED', 'Lámpara de pie con luz LED ajustable y control táctil', 2200.00, 2800.00, 10, 'lampara-pie.jpg', 1, 1),
                    (1, 'Juego de Sábanas Queen', 'Juego de sábanas de algodón egipcio 600 hilos, tamaño queen', 1800.00, 2200.00, 20, 'sabanas-queen.jpg', 0, 1),
                    (2, 'Zapatos Deportivos Adidas', 'Zapatos deportivos para running con tecnología Boost', 3200.00, 3800.00, 25, 'adidas-shoes.jpg', 1, 1),
                    (2, 'Chaqueta Impermeable North Face', 'Chaqueta impermeable para lluvia y viento, tallas S-XXL', 4200.00, 4900.00, 12, 'chaqueta-northface.jpg', 0, 1),
                    (2, 'Vestido Casual Verano', 'Vestido casual de verano, tejido ligero, varios colores', 1500.00, 1900.00, 30, 'vestido-verano.jpg', 1, 1),
                    (3, 'Pelota de Fútbol Oficial', 'Pelota de fútbol tamaño 5, oficial para partidos profesionales', 800.00, 1200.00, 40, 'pelota-futbol.jpg', 0, 1),
                    (3, 'Raqueta de Tenis Wilson', 'Raqueta de tenis profesional, grip cómodo, peso balanceado', 3500.00, 4200.00, 8, 'raqueta-tenis.jpg', 1, 1),
                    (3, 'Bicicleta Montaña Trek', 'Bicicleta de montaña con 21 velocidades y suspensión delantera', 12500.00, 14500.00, 5, 'bicicleta-montaña.jpg', 1, 1)";
            
            $stmt = $this->db->prepare($query);
            
            if ($stmt->execute()) {
                $affected_rows = $stmt->rowCount();
                $_SESSION['success'] = "✅ $affected_rows nuevos productos agregados correctamente a la base de datos.";
            } else {
                $_SESSION['error'] = "❌ Error al agregar los productos a la base de datos.";
            }
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
        }

        header('Location: ' . APP_URL . '/public/products');
        exit();
    }

    public function addProductForm() {
        if (!isAdmin()) {
            $_SESSION['error'] = "No tienes permisos para acceder a esta página.";
            header('Location: ' . APP_URL . '/public/login');
            exit();
        }
        require_once __DIR__ . '/../views/admin/add_product.php';
    }

    public function processAddProduct() {
        if (!isAdmin()) {
            $_SESSION['error'] = "No tienes permisos para realizar esta acción.";
            header('Location: ' . APP_URL . '/public/login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre_producto = $_POST['nombre_producto'] ?? '';
                $descripcion = $_POST['descripcion'] ?? '';
                $precio = floatval($_POST['precio'] ?? 0);
                $precio_original = floatval($_POST['precio_original'] ?? 0);
                $stock = intval($_POST['stock'] ?? 0);
                $id_categoria = intval($_POST['id_categoria'] ?? 0);
                $imagen_principal = $_POST['imagen_principal'] ?? 'default.jpg';
                $destacado = isset($_POST['destacado']) ? 1 : 0;
                $activo = isset($_POST['activo']) ? 1 : 1;

                // Validaciones básicas
                if (empty($nombre_producto) || empty($descripcion) || $precio <= 0 || $id_categoria <= 0) {
                    $_SESSION['error'] = "Por favor completa todos los campos obligatorios correctamente.";
                    header('Location: ' . APP_URL . '/public/admin/add-product');
                    exit();
                }

                $query = "INSERT INTO productos (id_categoria, nombre_producto, descripcion, precio, precio_original, stock, imagen_principal, destacado, activo, fecha_creacion) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                
                $stmt = $this->db->prepare($query);
                $success = $stmt->execute([
                    $id_categoria, $nombre_producto, $descripcion, $precio, $precio_original, 
                    $stock, $imagen_principal, $destacado, $activo
                ]);

                if ($success) {
                    $_SESSION['success'] = "✅ Producto '$nombre_producto' agregado correctamente.";
                    header('Location: ' . APP_URL . '/public/products');
                } else {
                    $_SESSION['error'] = "❌ Error al agregar el producto.";
                    header('Location: ' . APP_URL . '/public/admin/add-product');
                }
                
            } catch (PDOException $e) {
                $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
                header('Location: ' . APP_URL . '/public/admin/add-product');
            }
            exit();
        }
    }

    public function deleteProduct() {
        if (!isAdmin()) {
            $_SESSION['error'] = "No tienes permisos para realizar esta acción.";
            header('Location: ' . APP_URL . '/public/login');
            exit();
        }

        $product_id = $_GET['id'] ?? 0;
        
        if ($product_id > 0) {
            try {
                // Primero obtener el nombre del producto para el mensaje
                $query = "SELECT nombre_producto FROM productos WHERE id_producto = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $product_id, PDO::PARAM_INT);
                $stmt->execute();
                
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                $product_name = $product['nombre_producto'] ?? 'Producto';
                
                // Eliminar el producto
                $query = "DELETE FROM productos WHERE id_producto = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $product_id, PDO::PARAM_INT);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = "✅ Producto '$product_name' eliminado correctamente.";
                } else {
                    $_SESSION['error'] = "❌ Error al eliminar el producto.";
                }
                
            } catch (PDOException $e) {
                $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "ID de producto inválido.";
        }

        header('Location: ' . APP_URL . '/public/admin/manage-products');
        exit();
    }

    public function editProductForm() {
        if (!isAdmin()) {
            $_SESSION['error'] = "No tienes permisos para acceder a esta página.";
            header('Location: ' . APP_URL . '/public/login');
            exit();
        }

        $product_id = $_GET['id'] ?? 0;
        
        if ($product_id > 0) {
            $this->product->id_producto = $product_id;
            $product_data = $this->product->readOne();
            
            if ($product_data) {
                require_once __DIR__ . '/../views/admin/edit_product.php';
            } else {
                $_SESSION['error'] = "Producto no encontrado.";
                header('Location: ' . APP_URL . '/public/admin/manage-products');
                exit();
            }
        } else {
            $_SESSION['error'] = "ID de producto inválido.";
            header('Location: ' . APP_URL . '/public/admin/manage-products');
            exit();
        }
    }

    public function processEditProduct() {
        if (!isAdmin()) {
            $_SESSION['error'] = "No tienes permisos para realizar esta acción.";
            header('Location: ' . APP_URL . '/public/login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $product_id = intval($_POST['product_id'] ?? 0);
                $nombre_producto = $_POST['nombre_producto'] ?? '';
                $descripcion = $_POST['descripcion'] ?? '';
                $precio = floatval($_POST['precio'] ?? 0);
                $precio_original = floatval($_POST['precio_original'] ?? 0);
                $stock = intval($_POST['stock'] ?? 0);
                $id_categoria = intval($_POST['id_categoria'] ?? 0);
                $imagen_principal = $_POST['imagen_principal'] ?? 'default.jpg';
                $destacado = isset($_POST['destacado']) ? 1 : 0;
                $activo = isset($_POST['activo']) ? 1 : 0;

                // Validaciones básicas
                if (empty($nombre_producto) || empty($descripcion) || $precio <= 0 || $id_categoria <= 0) {
                    $_SESSION['error'] = "Por favor completa todos los campos obligatorios correctamente.";
                    header('Location: ' . APP_URL . '/public/admin/edit-product/' . $product_id);
                    exit();
                }

                $query = "UPDATE productos SET 
                          id_categoria = ?, 
                          nombre_producto = ?, 
                          descripcion = ?, 
                          precio = ?, 
                          precio_original = ?, 
                          stock = ?, 
                          imagen_principal = ?, 
                          destacado = ?, 
                          activo = ?,
                          fecha_actualizacion = NOW()
                          WHERE id_producto = ?";
                
                $stmt = $this->db->prepare($query);
                $success = $stmt->execute([
                    $id_categoria, $nombre_producto, $descripcion, $precio, $precio_original, 
                    $stock, $imagen_principal, $destacado, $activo, $product_id
                ]);

                if ($success) {
                    $_SESSION['success'] = "✅ Producto '$nombre_producto' actualizado correctamente.";
                    header('Location: ' . APP_URL . '/public/admin/manage-products');
                } else {
                    $_SESSION['error'] = "❌ Error al actualizar el producto.";
                    header('Location: ' . APP_URL . '/public/admin/edit-product/' . $product_id);
                }
                
            } catch (PDOException $e) {
                $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
                header('Location: ' . APP_URL . '/public/admin/edit-product/' . $product_id);
            }
            exit();
        }
    }

    public function manageProducts() {
        if (!isAdmin()) {
            $_SESSION['error'] = "No tienes permisos para acceder a esta página.";
            header('Location: ' . APP_URL . '/public/login');
            exit();
        }
        require_once __DIR__ . '/../views/admin/manage_products.php';
    }

    // ✅ NUEVO: Método para estadísticas
    public function stats() {
        if (!isAdmin()) {
            $_SESSION['error'] = "No tienes permisos para acceder a esta página.";
            header('Location: ' . APP_URL . '/public/login');
            exit();
        }
        require_once __DIR__ . '/../views/admin/stats.php';
    }
}
?>