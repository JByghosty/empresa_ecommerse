-- =====================================================
-- 🚀 BASE DE DATOS MEJORADA: Empresa de Comercio Electrónico
-- 💻 Compatible con XAMPP / MySQL
-- 👨‍💻 Autor: Abraham Ortiz - Versión Mejorada
-- =====================================================

-- 1️⃣ CREAR BASE DE DATOS CON CODIFICACIÓN
CREATE DATABASE IF NOT EXISTS empresa_ecommerce 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE empresa_ecommerce;

-- 2️⃣ TABLA: usuarios (MEJORADA)
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    direccion TEXT,
    ciudad VARCHAR(100),
    codigo_postal VARCHAR(10),
    pais VARCHAR(50) DEFAULT 'México',
    rol ENUM('admin', 'cliente', 'vendedor') DEFAULT 'cliente',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_login TIMESTAMP NULL,
    activo TINYINT(1) DEFAULT 1,
    INDEX idx_correo (correo),
    INDEX idx_rol (rol)
);

-- 3️⃣ TABLA: categorias (MEJORADA)
CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    imagen VARCHAR(255),
    activa TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre_categoria)
);

-- 4️⃣ TABLA: productos (MEJORADA)
CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    id_categoria INT NOT NULL,
    nombre_producto VARCHAR(150) NOT NULL,
    descripcion LONGTEXT,
    precio DECIMAL(10,2) NOT NULL CHECK (precio >= 0),
    precio_original DECIMAL(10,2) NULL,
    sku VARCHAR(50) UNIQUE,
    stock INT DEFAULT 0 CHECK (stock >= 0),
    stock_minimo INT DEFAULT 5,
    imagen_principal VARCHAR(255),
    imagenes TEXT, -- JSON para múltiples imágenes
    peso DECIMAL(8,2),
    dimensiones VARCHAR(50),
    activo TINYINT(1) DEFAULT 1,
    destacado TINYINT(1) DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria) ON DELETE RESTRICT,
    INDEX idx_categoria (id_categoria),
    INDEX idx_precio (precio),
    INDEX idx_stock (stock),
    INDEX idx_destacado (destacado),
    INDEX idx_sku (sku)
);

-- 5️⃣ TABLA: carrito (MEJORADA)
CREATE TABLE carrito (
    id_carrito INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT DEFAULT 1 CHECK (cantidad > 0),
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE,
    UNIQUE KEY unique_carrito_item (id_usuario, id_producto),
    INDEX idx_usuario (id_usuario)
);

-- 6️⃣ TABLA: pedidos (MEJORADA)
CREATE TABLE pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    numero_pedido VARCHAR(20) UNIQUE,
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL CHECK (total >= 0),
    subtotal DECIMAL(10,2) NOT NULL,
    impuestos DECIMAL(10,2) DEFAULT 0,
    envio DECIMAL(10,2) DEFAULT 0,
    direccion_envio TEXT NOT NULL,
    ciudad_envio VARCHAR(100),
    codigo_postal_envio VARCHAR(10),
    telefono_contacto VARCHAR(20),
    instrucciones_especiales TEXT,
    estado ENUM('pendiente', 'confirmado', 'procesando', 'enviado', 'entregado', 'cancelado') DEFAULT 'pendiente',
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT,
    INDEX idx_usuario (id_usuario),
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha_pedido),
    INDEX idx_numero_pedido (numero_pedido)
);

-- 7️⃣ TABLA: detalle_pedidos (MEJORADA)
CREATE TABLE detalle_pedidos (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL CHECK (cantidad > 0),
    precio_unitario DECIMAL(10,2) NOT NULL CHECK (precio_unitario >= 0),
    subtotal DECIMAL(10,2) GENERATED ALWAYS AS (cantidad * precio_unitario) STORED,
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE RESTRICT,
    INDEX idx_pedido (id_pedido),
    INDEX idx_producto (id_producto)
);

-- 8️⃣ TABLA: reseñas (MEJORADA)
CREATE TABLE reseñas (
    id_reseña INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_producto INT NOT NULL,
    calificacion TINYINT NOT NULL CHECK (calificacion BETWEEN 1 AND 5),
    titulo VARCHAR(150),
    comentario TEXT,
    aprobada TINYINT(1) DEFAULT 0,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE,
    UNIQUE KEY unique_reseña (id_usuario, id_producto),
    INDEX idx_producto (id_producto),
    INDEX idx_calificacion (calificacion),
    INDEX idx_aprobada (aprobada)
);

-- 9️⃣ TABLA: pagos (MEJORADA)
CREATE TABLE pagos (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    metodo_pago ENUM('tarjeta_credito', 'tarjeta_debito', 'paypal', 'transferencia', 'efectivo') NOT NULL,
    monto DECIMAL(10,2) NOT NULL CHECK (monto >= 0),
    referencia_pago VARCHAR(100),
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'completado', 'fallido', 'reembolsado') DEFAULT 'pendiente',
    detalles TEXT,
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido) ON DELETE RESTRICT,
    INDEX idx_pedido (id_pedido),
    INDEX idx_estado (estado),
    INDEX idx_referencia (referencia_pago)
);

-- 🔟 NUEVA TABLA: wishlist (LISTA DE DESEOS)
CREATE TABLE wishlist (
    id_wishlist INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_producto INT NOT NULL,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (id_usuario, id_producto),
    INDEX idx_usuario (id_usuario)
);

-- 1️⃣1️⃣ NUEVA TABLA: cupones (SISTEMA DE DESCUENTOS)
CREATE TABLE cupones (
    id_cupon INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    tipo_descuento ENUM('porcentaje', 'monto_fijo') NOT NULL,
    valor_descuento DECIMAL(10,2) NOT NULL,
    monto_minimo DECIMAL(10,2) DEFAULT 0,
    usos_maximos INT DEFAULT 1,
    usos_actuales INT DEFAULT 0,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_codigo (codigo),
    INDEX idx_fechas (fecha_inicio, fecha_fin)
);

-- 1️⃣2️⃣ NUEVA TABLA: pedido_cupones (CUPONES APLICADOS)
CREATE TABLE pedido_cupones (
    id_pedido_cupon INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_cupon INT NOT NULL,
    descuento_aplicado DECIMAL(10,2) NOT NULL,
    fecha_aplicado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido) ON DELETE CASCADE,
    FOREIGN KEY (id_cupon) REFERENCES cupones(id_cupon) ON DELETE RESTRICT,
    UNIQUE KEY unique_pedido_cupon (id_pedido, id_cupon)
);

-- 🔄 TRIGGERS PARA MANTENER INTEGRIDAD
DELIMITER //

-- Trigger para actualizar stock al realizar pedido
CREATE TRIGGER after_detalle_pedido_insert
AFTER INSERT ON detalle_pedidos
FOR EACH ROW
BEGIN
    UPDATE productos 
    SET stock = stock - NEW.cantidad,
        fecha_actualizacion = CURRENT_TIMESTAMP
    WHERE id_producto = NEW.id_producto;
END//

-- Trigger para generar número de pedido único
CREATE TRIGGER before_pedido_insert
BEFORE INSERT ON pedidos
FOR EACH ROW
BEGIN
    IF NEW.numero_pedido IS NULL THEN
        SET NEW.numero_pedido = CONCAT('PED-', DATE_FORMAT(NOW(), '%Y%m%d-'), LPAD(FLOOR(RAND() * 10000), 4, '0'));
    END IF;
END//

DELIMITER ;

-- 📊 VISTAS ÚTILES
CREATE VIEW vista_productos_categorias AS
SELECT 
    p.*,
    c.nombre_categoria,
    c.descripcion as descripcion_categoria
FROM productos p
LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
WHERE p.activo = 1;

CREATE VIEW vista_reseñas_productos AS
SELECT 
    r.*,
    u.nombre as nombre_usuario,
    p.nombre_producto,
    p.imagen_principal
FROM reseñas r
JOIN usuarios u ON r.id_usuario = u.id_usuario
JOIN productos p ON r.id_producto = p.id_producto
WHERE r.aprobada = 1;

-- 🎯 DATOS DE EJEMPLO MEJORADOS
INSERT INTO categorias (nombre_categoria, descripcion, imagen) VALUES
('Tecnología', 'Productos electrónicos y gadgets de última generación', 'tecnologia.jpg'),
('Ropa', 'Moda para hombres, mujeres y niños', 'ropa.jpg'),
('Hogar', 'Artículos para el hogar y decoración', 'hogar.jpg'),
('Deportes', 'Equipamiento y ropa deportiva', 'deportes.jpg');

INSERT INTO usuarios (nombre, apellido, correo, contrasena, telefono, direccion, ciudad, codigo_postal, rol) VALUES
('Administrador', 'Sistema', 'admin@empresa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-1234', 'Av. Principal 123', 'CDMX', '03100', 'admin'),
('Juan', 'Pérez', 'juan@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-5678', 'Calle Secundaria 456', 'Guadalajara', '44100', 'cliente'),
('María', 'García', 'maria@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-9012', 'Privada Norte 789', 'Monterrey', '64000', 'cliente');

INSERT INTO productos (id_categoria, nombre_producto, descripcion, precio, precio_original, sku, stock, imagen_principal, destacado) VALUES
(1, 'Laptop HP Pavilion 15', 'Laptop de alto rendimiento con procesador Intel i7, 16GB RAM, 512GB SSD', 65000.00, 72000.00, 'LAP-HP-PAV-001', 10, 'laptop_hp_pavilion.jpg', 1),
(1, 'Smartphone Samsung Galaxy S23', 'Teléfono inteligente con cámara de 108MP y 256GB almacenamiento', 18500.00, NULL, 'CEL-SAM-S23-001', 25, 'samsung_galaxy_s23.jpg', 1),
(2, 'Camiseta Nike Dri-FIT', 'Camiseta deportiva de alta calidad con tecnología Dri-FIT', 1200.00, 1500.00, 'CAM-NIK-DF-001', 30, 'camiseta_nike_drifit.jpg', 0),
(3, 'Silla Ergonómica Oficina', 'Silla ergonómica ideal para oficina y estudio, ajustable en altura', 8500.00, 9500.00, 'SIL-ERG-OF-001', 15, 'silla_ergonomica_oficina.jpg', 1);

-- ✅ CONSULTAS DE PRUEBA MEJORADAS
-- SELECT * FROM vista_productos_categorias WHERE destacado = 1;
-- SELECT * FROM usuarios WHERE activo = 1;
-- SELECT nombre_categoria, COUNT(*) as total_productos FROM categorias c JOIN productos p ON c.id_categoria = p.id_categoria GROUP BY c.id_categoria;

-- =====================================================
-- 🎯 MEJORAS IMPLEMENTADAS:
-- 1. Codificación UTF8MB4 para emojis y caracteres especiales
-- 2. Campos adicionales para mejor gestión (apellidos, direcciones completas)
-- 3. Índices optimizados para mejor rendimiento
-- 4. Sistema de wishlist (lista de deseos)
-- 5. Sistema de cupones y descuentos
-- 6. Triggers para automatización
-- 7. Vistas para consultas comunes
-- 8. Validaciones con CHECK constraints
-- 9. Campos de auditoría (fecha_actualizacion)
-- 10. Mejor manejo de imágenes múltiples
-- 11. Estados más detallados en pedidos y pagos
-- 12. Sistema de reseñas aprobadas
-- =====================================================