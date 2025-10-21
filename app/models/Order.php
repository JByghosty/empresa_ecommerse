<?php
class Order {
    private $conn;
    private $table = 'pedidos';

    public $id_pedido;
    public $id_usuario;
    public $total;
    public $estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener total de pedidos
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    // Obtener pedidos recientes
    public function getRecentOrders() {
        try {
            $query = "SELECT p.*, u.nombre 
                      FROM " . $this->table . " p 
                      JOIN usuarios u ON p.id_usuario = u.id_usuario 
                      ORDER BY p.fecha_pedido DESC 
                      LIMIT 5";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (Exception $e) {
            return null;
        }
    }
}
?>