<?php
class Category {
    private $conn;
    private $table = 'categorias';

    public $id_categoria;
    public $nombre_categoria;
    public $descripcion;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todas las categorías
    public function read() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY nombre_categoria";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Método para obtener una categoría por ID
    public function readOne($category_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_categoria = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $category_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // ✅ NUEVO: Obtener categorías con conteo de productos
    public function readWithProductCount() {
        $query = "SELECT c.*, COUNT(p.id_producto) as total_productos 
                  FROM " . $this->table . " c 
                  LEFT JOIN productos p ON c.id_categoria = p.id_categoria AND p.activo = 1 
                  GROUP BY c.id_categoria 
                  ORDER BY c.nombre_categoria";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>