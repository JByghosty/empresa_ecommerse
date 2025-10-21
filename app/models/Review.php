<?php
class Review {
    private $conn;
    private $table = 'reseñas';

    public $id_reseña;
    public $id_usuario;
    public $id_producto;
    public $calificacion;
    public $titulo;
    public $comentario;
    public $aprobada;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener reseñas por producto
    public function getByProduct($product_id) {
        $query = "SELECT r.*, u.nombre 
                  FROM " . $this->table . " r 
                  JOIN usuarios u ON r.id_usuario = u.id_usuario 
                  WHERE r.id_producto = ? AND r.aprobada = 1 
                  ORDER BY r.fecha DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $product_id);
        $stmt->execute();
        return $stmt;
    }

    // Crear reseña
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET id_usuario=:id_usuario, id_producto=:id_producto, 
                      calificacion=:calificacion, titulo=:titulo, comentario=:comentario";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id_usuario", $this->id_usuario);
        $stmt->bindParam(":id_producto", $this->id_producto);
        $stmt->bindParam(":calificacion", $this->calificacion);
        $stmt->bindParam(":titulo", $this->titulo);
        $stmt->bindParam(":comentario", $this->comentario);

        return $stmt->execute();
    }
}
?>