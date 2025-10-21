<?php
class User {
    private $conn;
    private $table = 'usuarios';

    public $id_usuario;
    public $nombre;
    public $apellido;
    public $correo;
    public $contrasena;
    public $telefono;
    public $direccion;
    public $rol;
    public $activo;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Registrar usuario
    public function register() {
        $query = "INSERT INTO " . $this->table . " 
                 SET nombre=:nombre, apellido=:apellido, correo=:correo, 
                     contrasena=:contrasena, telefono=:telefono, direccion=:direccion";
        
        $stmt = $this->conn->prepare($query);
        
        // Hash de contraseña
        $hashed_password = password_hash($this->contrasena, PASSWORD_DEFAULT);
        
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":apellido", $this->apellido);
        $stmt->bindParam(":correo", $this->correo);
        $stmt->bindParam(":contrasena", $hashed_password);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":direccion", $this->direccion);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Login
    public function login() {
        $query = "SELECT id_usuario, nombre, apellido, correo, contrasena, rol, activo 
                  FROM " . $this->table . " 
                  WHERE correo = :correo AND activo = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":correo", $this->correo);
        $stmt->execute();

        if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(password_verify($this->contrasena, $row['contrasena'])) {
                $this->id_usuario = $row['id_usuario'];
                $this->nombre = $row['nombre'];
                $this->apellido = $row['apellido'];
                $this->correo = $row['correo'];
                $this->rol = $row['rol'];
                return true;
            }
        }
        return false;
    }

    // Verificar si el correo existe
    public function emailExists() {
        $query = "SELECT id_usuario FROM " . $this->table . " WHERE correo = :correo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":correo", $this->correo);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Obtener total de usuarios
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }
}
?>