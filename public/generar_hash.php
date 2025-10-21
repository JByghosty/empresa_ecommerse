<?php
// Cambia 'MiPassSuperSegura123!' por la contraseña que quieras
$newpass = '1234';
$hash = password_hash($newpass, PASSWORD_DEFAULT);
echo "Nuevo hash: " . $hash;
?>
