<?php
class Product {
    private $conn;
    private $table = 'productos';

    public $id_producto;
    public $id_categoria;
    public $nombre_producto;
    public $descripcion;
    public $precio;
    public $precio_original;
    public $stock;
    public $imagen_principal;
    public $destacado;
    public $activo;
    public $nombre_categoria;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener un producto por ID
    public function readOne() {
        $query = "SELECT p.*, c.nombre_categoria 
                  FROM " . $this->table . " p 
                  LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                  WHERE p.id_producto = ? AND p.activo = 1 
                  LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_producto);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            // Asignar propiedades
            $this->id_producto = $row['id_producto'];
            $this->id_categoria = $row['id_categoria'];
            $this->nombre_producto = $row['nombre_producto'];
            $this->descripcion = $row['descripcion'];
            $this->precio = $row['precio'];
            $this->precio_original = $row['precio_original'];
            $this->stock = $row['stock'];
            $this->imagen_principal = $row['imagen_principal'];
            $this->destacado = $row['destacado'];
            $this->activo = $row['activo'];
            $this->nombre_categoria = $row['nombre_categoria'];
            
            return $row;
        }
        return false;
    }

    // Obtener productos paginados
    public function readPaginated($limit, $offset) {
        $query = "SELECT p.*, c.nombre_categoria 
                  FROM " . $this->table . " p 
                  LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                  WHERE p.activo = 1 
                  ORDER BY p.fecha_creacion DESC 
                  LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->bindParam(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // ✅ NUEVO: Obtener productos por categoría con paginación
    public function readByCategoryPaginated($category_id, $limit, $offset) {
        $query = "SELECT p.*, c.nombre_categoria 
                  FROM " . $this->table . " p 
                  LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                  WHERE p.id_categoria = ? AND p.activo = 1 
                  ORDER BY p.fecha_creacion DESC 
                  LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $category_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->bindParam(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // ✅ NUEVO: Contar productos por categoría
    public function getCountByCategory($category_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE id_categoria = ? AND activo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $category_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    // Obtener todos los productos (sin paginación)
    public function read() {
        $query = "SELECT p.*, c.nombre_categoria 
                  FROM " . $this->table . " p 
                  LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                  WHERE p.activo = 1 
                  ORDER BY p.fecha_creacion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Buscar productos
    public function search($keywords) {
        $query = "SELECT p.*, c.nombre_categoria 
                  FROM " . $this->table . " p 
                  LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                  WHERE p.activo = 1 AND
                  (p.nombre_producto LIKE ? OR p.descripcion LIKE ? OR c.nombre_categoria LIKE ?) 
                  ORDER BY p.fecha_creacion DESC";
        
        $stmt = $this->conn->prepare($query);
        
        $keywords = "%{$keywords}%";
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        
        $stmt->execute();
        return $stmt;
    }

    // Obtener productos destacados
    public function readFeatured() {
        $query = "SELECT p.*, c.nombre_categoria 
                  FROM " . $this->table . " p 
                  LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                  WHERE p.destacado = 1 AND p.activo = 1 
                  ORDER BY p.fecha_creacion DESC 
                  LIMIT 8";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtener productos por categoría (sin paginación)
    public function readByCategory($category_id) {
        $query = "SELECT p.*, c.nombre_categoria 
                  FROM " . $this->table . " p 
                  LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                  WHERE p.id_categoria = ? AND p.activo = 1 
                  ORDER BY p.fecha_creacion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $category_id);
        $stmt->execute();
        return $stmt;
    }

    // Obtener conteo total de productos
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE activo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }
}
?>