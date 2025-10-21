<?php
// app/controllers/CartController.php

// Incluir configuración primero
require_once __DIR__ . '/../config/config.php';

// Incluir modelos
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Product.php';

class CartController {
    private $db;
    private $product;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->product = new Product($this->db);
    }

    public function add() {
        if(!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Debes iniciar sesión para agregar productos al carrito.";
            header("Location: " . APP_URL . "/public/login");
            exit();
        }

        $product_id = $_GET['id'] ?? 0;
        
        if($product_id > 0) {
            $this->product->id_producto = $product_id;
            $product_data = $this->product->readOne();
            
            if($product_data) {
                // Inicializar carrito si no existe
                if(!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }

                // Agregar producto al carrito
                if(isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id] += 1;
                } else {
                    $_SESSION['cart'][$product_id] = 1;
                }

                $_SESSION['success'] = "Producto '{$product_data['nombre_producto']}' agregado al carrito.";
            } else {
                $_SESSION['error'] = "Producto no encontrado.";
            }
        } else {
            $_SESSION['error'] = "ID de producto inválido.";
        }

        // Redirigir a la página anterior o a productos
        $redirect_url = $_SERVER['HTTP_REFERER'] ?? APP_URL . '/public/products';
        header("Location: " . $redirect_url);
        exit();
    }

    public function remove() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: " . APP_URL . "/public/login");
            exit();
        }

        $product_id = $_GET['id'] ?? 0;
        
        if($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
            // Obtener nombre del producto antes de eliminarlo
            $this->product->id_producto = $product_id;
            $product_data = $this->product->readOne();
            $product_name = $product_data ? $product_data['nombre_producto'] : 'Producto';
            
            unset($_SESSION['cart'][$product_id]);
            $_SESSION['success'] = "Producto '$product_name' eliminado del carrito.";
        } else {
            $_SESSION['error'] = "Producto no encontrado en el carrito.";
        }

        header("Location: " . APP_URL . "/public/cart");
        exit();
    }

    public function index() {
        require_once __DIR__ . '/../views/cart/index.php';
    }

    public function update() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: " . APP_URL . "/public/login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($_POST['quantity'] as $product_id => $quantity) {
                if ($quantity > 0) {
                    $_SESSION['cart'][$product_id] = $quantity;
                } else {
                    unset($_SESSION['cart'][$product_id]);
                }
            }
            
            $_SESSION['success'] = "Carrito actualizado correctamente.";
            header("Location: " . APP_URL . "/public/cart");
            exit();
        }
    }
}

// Si se accede directamente al archivo (no a través del enrutador)
if (basename($_SERVER['SCRIPT_FILENAME']) == 'CartController.php') {
    $action = $_GET['action'] ?? '';
    $controller = new CartController();

    if($action == 'add') {
        $controller->add();
    } elseif($action == 'remove') {
        $controller->remove();
    } elseif($action == 'update') {
        $controller->update();
    } elseif($action == 'index') {
        $controller->index();
    }
}
?>