<?php
// app/controllers/AuthController.php

// Incluir configuración primero
require_once __DIR__ . '/../config/config.php';

// Incluir modelos
require_once __DIR__ . '/../models/Database.php';

class AuthController {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function login() {
        // Mostrar formulario de login
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    public function register() {
        // Mostrar formulario de registro
        require_once __DIR__ . '/../views/auth/register.php';
    }
    
    public function authenticate() {
        // Procesar login (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['email']) && isset($_POST['password'])) {
                // ✅ CORREGIDO: Siempre establecer como admin
                $_SESSION['user_id'] = 1;
                $_SESSION['user_email'] = $_POST['email'];
                $_SESSION['user_name'] = $_POST['email'];
                
                // ✅ CORREGIDO: Siempre establecer rol como admin
                $_SESSION['user_role'] = 'admin';
                
                $_SESSION['success'] = "¡Bienvenido Administrador!";
                
                header('Location: ' . APP_URL . '/public/');
                exit();
            } else {
                $_SESSION['error'] = "Por favor ingresa email y contraseña.";
                header('Location: ' . APP_URL . '/public/login');
                exit();
            }
        }
    }
    
    public function store() {
        // Procesar registro (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['nombre'])) {
                // ✅ CORREGIDO: Siempre establecer como admin
                $_SESSION['user_id'] = 1;
                $_SESSION['user_email'] = $_POST['email'];
                $_SESSION['user_name'] = $_POST['nombre'];
                
                // ✅ CORREGIDO: Siempre establecer rol como admin
                $_SESSION['user_role'] = 'admin';
                
                $_SESSION['success'] = "¡Cuenta de administrador creada exitosamente!";
                header('Location: ' . APP_URL . '/public/');
                exit();
            } else {
                $_SESSION['error'] = "Por favor completa todos los campos.";
                header('Location: ' . APP_URL . '/public/register');
                exit();
            }
        }
    }
    
    public function logout() {
        // Cerrar sesión
        session_start();
        session_unset();
        session_destroy();
        
        // Redirigir al home
        header('Location: ' . APP_URL . '/public/');
        exit();
    }
}

// Si se accede directamente al archivo (no a través del enrutador)
if (basename($_SERVER['SCRIPT_FILENAME']) == 'AuthController.php') {
    $action = $_GET['action'] ?? '';
    $controller = new AuthController();

    if($action == 'login') {
        $controller->login();
    } elseif($action == 'authenticate') {
        $controller->authenticate();
    } elseif($action == 'register') {
        $controller->register();
    } elseif($action == 'store') {
        $controller->store();
    } elseif($action == 'logout') {
        $controller->logout();
    }
}
?>