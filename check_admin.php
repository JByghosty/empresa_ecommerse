<?php
// check_admin.php
require_once 'app/config/config.php';

echo "<h1>Estado de Sesión</h1>";
echo "<pre>";
echo "user_id: " . ($_SESSION['user_id'] ?? 'NO') . "\n";
echo "user_role: " . ($_SESSION['user_role'] ?? 'NO') . "\n";
echo "isAdmin(): " . (isAdmin() ? 'SÍ' : 'NO') . "\n";
echo "</pre>";

if (isAdmin()) {
    echo "<p style='color: green;'>✅ Eres administrador</p>";
} else {
    echo "<p style='color: red;'>❌ NO eres administrador</p>";
    echo "<p>Para ser admin, usa un email que contenga 'admin' como: admin@test.com</p>";
}
?>