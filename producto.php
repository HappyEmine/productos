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
    public function crearProducto($nombre, $precio, $stock) {
        $sql = "INSERT INTO productos (nombre, precio, stock) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sdd", $nombre, $precio, $stock);
        return $stmt->execute();
    }

    public function actualizarProducto($id, $nombre, $precio, $stock) {
        $sql = "UPDATE productos SET nombre = ?, precio = ?, stock = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sddi", $nombre, $precio, $stock, $id);
        return $stmt->execute();
    }

    public function eliminarProducto($id) {
        $sql = "DELETE FROM productos WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>