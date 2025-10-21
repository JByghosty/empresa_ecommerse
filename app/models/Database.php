<?php
class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username, 
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // Mostrar error detallado
            $error_message = "Error de conexión a la base de datos: " . $exception->getMessage();
            $error_message .= "<br><br>Verifica que:";
            $error_message .= "<br>- MySQL esté ejecutándose en XAMPP";
            $error_message .= "<br>- La base de datos '" . DB_NAME . "' exista";
            $error_message .= "<br>- Las credenciales sean correctas";
            $error_message .= "<br>- Las extensiones mysqli y pdo_mysql estén activas en php.ini";
            
            die($error_message);
        }
        return $this->conn;
    }
}
?>