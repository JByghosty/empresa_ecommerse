<?php
// Verificar si las constantes ya están definidas para evitar duplicados
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', 'empresa_ecommerce');
}

if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}

if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}

if (!defined('APP_NAME')) {
    define('APP_NAME', 'Lo de Escalante');
}

if (!defined('APP_URL')) {
    define('APP_URL', 'http://localhost/lo-de-escalante');
}

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Manejo de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Función para verificar autenticación
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Función para verificar si es admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}
?>