<?php
// public/index.php
require_once __DIR__ . '/../app/config/config.php';

// Front Controller
$request = $_GET['url'] ?? '';

// Limpiar la URL (remover .php si existe)
$request = str_replace('.php', '', $request);

// Dividir la URL en partes
$url_parts = explode('/', $request);
$main_route = $url_parts[0];

switch($main_route) {
    case '':
    case 'home':
        require_once __DIR__ . '/../app/views/home.php';
        break;
        
    case 'login':
        // Manejar login
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->authenticate();
        } else {
            $controller->login();
        }
        break;
        
    case 'register':
        // Manejar registro
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store();
        } else {
            $controller->register();
        }
        break;
        
    case 'logout':
        // Manejar logout
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;
        
    case 'profile':
        // Manejar perfil de usuario
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . APP_URL . '/public/login');
            exit();
        }
        require_once __DIR__ . '/../app/views/auth/profile.php';
        break;
        
    case 'products':
        // Manejar rutas de productos
        if (isset($url_parts[1]) && $url_parts[1] == 'show' && isset($url_parts[2])) {
            // Pasar el ID del producto como parámetro GET
            $_GET['id'] = $url_parts[2];
            require_once __DIR__ . '/../app/views/products/show.php';
        } else {
            require_once __DIR__ . '/../app/views/products/index.php';
        }
        break;
        
    case 'cart':
        // Manejar rutas del carrito
        if (isset($url_parts[1])) {
            if ($url_parts[1] == 'add' && isset($url_parts[2])) {
                // Agregar producto al carrito
                $_GET['id'] = $url_parts[2];
                require_once __DIR__ . '/../app/controllers/CartController.php';
                $controller = new CartController();
                $controller->add();
            } elseif ($url_parts[1] == 'remove' && isset($url_parts[2])) {
                // Eliminar producto del carrito
                $_GET['id'] = $url_parts[2];
                require_once __DIR__ . '/../app/controllers/CartController.php';
                $controller = new CartController();
                $controller->remove();
            } else {
                // Ver carrito
                require_once __DIR__ . '/../app/views/cart/index.php';
            }
        } else {
            // Ver carrito
            require_once __DIR__ . '/../app/views/cart/index.php';
        }
        break;
        
    case 'admin':
        // Manejar rutas de administración
        if (isset($url_parts[1]) && $url_parts[1] == 'update-images') {
            // Actualizar imágenes en la base de datos
            require_once __DIR__ . '/../app/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->updateImages();
        } elseif (isset($url_parts[1]) && $url_parts[1] == 'add-products') {
            // Agregar productos de prueba
            require_once __DIR__ . '/../app/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->addProducts();
        } elseif (isset($url_parts[1]) && $url_parts[1] == 'add-product') {
            // Mostrar formulario para agregar producto
            require_once __DIR__ . '/../app/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->addProductForm();
        } elseif (isset($url_parts[1]) && $url_parts[1] == 'process-add-product') {
            // Procesar formulario de agregar producto
            require_once __DIR__ . '/../app/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->processAddProduct();
        } else {
            // Redirigir a home si no es una ruta admin válida
            header('Location: ' . APP_URL . '/public/');
            exit();
        }
        break;
        
    default:
        http_response_code(404);
        echo "Página no encontrada: " . htmlspecialchars($request);
        break;


// En el caso 'admin', actualiza con estas rutas:
case 'admin':
    // Manejar rutas de administración
    if (isset($url_parts[1]) && $url_parts[1] == 'update-images') {
        require_once __DIR__ . '/../app/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->updateImages();
    } elseif (isset($url_parts[1]) && $url_parts[1] == 'add-products') {
        require_once __DIR__ . '/../app/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->addProducts();
    } elseif (isset($url_parts[1]) && $url_parts[1] == 'add-product') {
        require_once __DIR__ . '/../app/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->addProductForm();
    } elseif (isset($url_parts[1]) && $url_parts[1] == 'process-add-product') {
        require_once __DIR__ . '/../app/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->processAddProduct();
    } elseif (isset($url_parts[1]) && $url_parts[1] == 'manage-products') {
        // ✅ NUEVA RUTA: Gestionar productos
        require_once __DIR__ . '/../app/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->manageProducts();
    } elseif (isset($url_parts[1]) && $url_parts[1] == 'delete-product' && isset($url_parts[2])) {
        // ✅ NUEVA RUTA: Eliminar producto
        $_GET['id'] = $url_parts[2];
        require_once __DIR__ . '/../app/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->deleteProduct();
    } elseif (isset($url_parts[1]) && $url_parts[1] == 'edit-product' && isset($url_parts[2])) {
        // ✅ NUEVA RUTA: Editar producto (formulario)
        $_GET['id'] = $url_parts[2];
        require_once __DIR__ . '/../app/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->editProductForm();
    } elseif (isset($url_parts[1]) && $url_parts[1] == 'process-edit-product') {
        // ✅ NUEVA RUTA: Procesar edición de producto
        require_once __DIR__ . '/../app/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->processEditProduct();
    } else {
        header('Location: ' . APP_URL . '/public/');
        exit();
    }
    break;

    

}
?>