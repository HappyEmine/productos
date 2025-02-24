<?php
require_once "Database.php";

class Producto {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function obtenerProductos($nombre = null, $precio_mayor_que = null) {
        $sql = "SELECT * FROM productos WHERE 1=1";

        if ($nombre) {
            $sql .= " AND nombre LIKE '%" . $this->conn->real_escape_string($nombre) . "%'";
        }
        if ($precio_mayor_que != null) {
            $sql .= " AND precio > " . (float) $this->conn->real_escape_string($precio_mayor_que);
        }

        $resultado = $this->conn->query($sql);
        return $resultado;
    }
}
?>