<?php
echo "<!DOCTYPE html>
<html>
<head>
    <title>Test de Conexión - Lo de Escalante</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        ul { background: #f5f5f5; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🧪 Test de Conexión a Base de Datos</h1>";

try {
    // Intentar conexión directa al servidor MySQL
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p class='success'>✅ Conexión al servidor MySQL exitosa</p>";
    
    // Verificar si la base de datos existe
    $result = $pdo->query("SHOW DATABASES LIKE 'empresa_ecommerce'");
    if ($result->rowCount() > 0) {
        echo "<p class='success'>✅ Base de datos 'empresa_ecommerce' existe</p>";
        
        // Verificar tablas
        $pdo->exec("USE empresa_ecommerce");
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tables) > 0) {
            echo "<p class='success'>✅ Tablas encontradas: " . implode(', ', $tables) . "</p>";
        } else {
            echo "<p class='error'>❌ No hay tablas en la base de datos</p>";
        }
        
    } else {
        echo "<p class='error'>❌ Base de datos 'empresa_ecommerce' NO existe</p>";
        echo "<p class='info'>Creando base de datos y tablas...</p>";
        
        // Crear base de datos
        $pdo->exec("CREATE DATABASE empresa_ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "<p class='success'>✅ Base de datos creada</p>";
        
        // Usar la base de datos y crear tablas
        $pdo->exec("USE empresa_ecommerce");
        
        // SQL para crear todas las tablas
        $sql = "
        CREATE TABLE usuarios (
            id_usuario INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            apellido VARCHAR(100) NOT NULL,
            correo VARCHAR(150) NOT NULL UNIQUE,
            contrasena VARCHAR(255) NOT NULL,
            telefono VARCHAR(20),
            direccion TEXT,
            rol ENUM('admin', 'cliente') DEFAULT 'cliente',
            activo TINYINT(1) DEFAULT 1,
            fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE categorias (
            id_categoria INT AUTO_INCREMENT PRIMARY KEY,
            nombre_categoria VARCHAR(100) NOT NULL UNIQUE,
            descripcion TEXT,
            activa TINYINT(1) DEFAULT 1
        );
        
        CREATE TABLE productos (
            id_producto INT AUTO_INCREMENT PRIMARY KEY,
            id_categoria INT,
            nombre_producto VARCHAR(150) NOT NULL,
            descripcion TEXT,
            precio DECIMAL(10,2) NOT NULL,
            precio_original DECIMAL(10,2),
            stock INT DEFAULT 0,
            imagen_principal VARCHAR(255),
            destacado TINYINT(1) DEFAULT 0,
            activo TINYINT(1) DEFAULT 1,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
        );
        
        CREATE TABLE pedidos (
            id_pedido INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario INT,
            total DECIMAL(10,2) NOT NULL,
            estado ENUM('pendiente', 'confirmado', 'enviado', 'entregado', 'cancelado') DEFAULT 'pendiente',
            fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        );
        
        CREATE TABLE reseñas (
            id_reseña INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario INT,
            id_producto INT,
            calificacion INT CHECK (calificacion BETWEEN 1 AND 5),
            comentario TEXT,
            fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
            FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
        );
        ";
        
        $pdo->exec($sql);
        echo "<p class='success'>✅ Todas las tablas creadas</p>";
        
        // Insertar datos de prueba
        $pdo->exec("
        INSERT INTO categorias (nombre_categoria, descripcion) VALUES 
        ('Tecnología', 'Productos electrónicos y gadgets'),
        ('Ropa', 'Moda para hombres y mujeres'),
        ('Hogar', 'Artículos para el hogar');
        
        INSERT INTO usuarios (nombre, apellido, correo, contrasena, rol) VALUES 
        ('Admin', 'Sistema', 'admin@empresa.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
        ('Juan', 'Pérez', 'juan@gmail.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente');
        
        INSERT INTO productos (id_categoria, nombre_producto, descripcion, precio, stock, destacado) VALUES 
        (1, 'Laptop HP Pavilion', 'Laptop de alto rendimiento', 65000.00, 10, 1),
        (1, 'Smartphone Samsung Galaxy', 'Teléfono inteligente con cámara de 108MP', 18500.00, 25, 1),
        (2, 'Camiseta Nike Dri-FIT', 'Camiseta deportiva de alta calidad', 1200.00, 30, 0),
        (3, 'Silla Ergonómica Oficina', 'Silla ergonómica ideal para oficina', 8500.00, 15, 1);
        ");
        
        echo "<p class='success'>✅ Datos de prueba insertados</p>";
        echo "<p class='info'>🔑 Credenciales para probar:</p>";
        echo "<ul>
        <li><strong>Admin:</strong> admin@empresa.com / admin123</li>
        <li><strong>Cliente:</strong> juan@gmail.com / 123456</li>
        </ul>";
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>❌ Error de conexión: " . $e->getMessage() . "</p>";
    echo "<p class='info'>🔧 Posibles soluciones:</p>";
    echo "<ul>
    <li>Verifica que MySQL esté ejecutándose en el panel de control de XAMPP</li>
    <li>Abre php.ini y activa estas líneas (elimina el ;):
        <br><code>;extension=mysqli</code> → <code>extension=mysqli</code>
        <br><code>;extension=pdo_mysql</code> → <code>extension=pdo_mysql</code>
    </li>
    <li>Reinicia Apache después de modificar php.ini</li>
    <li>Verifica que el usuario 'root' no tenga contraseña en MySQL</li>
    </ul>";
    
    // Información adicional de diagnóstico
    echo "<p class='info'>📋 Información del sistema:</p>";
    echo "<ul>
    <li>PHP Version: " . phpversion() . "</li>
    <li>Extensiones cargadas: " . implode(', ', get_loaded_extensions()) . "</li>
    </ul>";
}

echo "</body></html>";
?>