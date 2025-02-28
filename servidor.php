<?php

require_once "Producto.php";

class ProductoService {
    private $tokenCorrecto = "123456";
    private $producto;

    public function __construct() {
        $this->producto = new Producto();
    }

    public function validarAcceso() {
        if (!isset($_GET['token']) || $_GET['token'] !== $this->tokenCorrecto) {
            $this->enviarError("Acceso no autorizado");
        }
    }

    public function obtenerProductos() {
        $nombre = isset($_GET['nombre']) ? $_GET['nombre'] : null;
        $precio_mayor_que = isset($_GET['precio_mayor_que']) ? $_GET['precio_mayor_que'] : null;

        $resultado = $this->producto->obtenerProductos($nombre, $precio_mayor_que);
        $this->generarXML($resultado);
    }

    public function crearProducto() {
        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];
        $stock = $_POST['stock'];

        if ($this->producto->crearProducto($nombre, $precio, $stock)) {
            $this->enviarRespuesta("Producto creado con éxito");
        } else {
            $this->enviarError("Error al crear el producto");
        }
    }

    public function actualizarProducto() {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];
        $stock = $_POST['stock'];

        if ($this->producto->actualizarProducto($id, $nombre, $precio, $stock)) {
            $this->enviarRespuesta("Producto actualizado con éxito");
        } else {
            $this->enviarError("Error al actualizar el producto");
        }
    }

    public function eliminarProducto() {
        $id = $_POST['id'];

        if ($this->producto->eliminarProducto($id)) {
            $this->enviarRespuesta("Producto eliminado con éxito");
        } else {
            $this->enviarError("Error al eliminar el producto");
        }
    }

    private function enviarError($mensaje, $dom = null, $root = null) {
        if (!$dom) {
            header("Content-Type: application/xml; charset=UTF-8");
            $dom = new DOMDocument("1.0", "UTF-8");
            $dom->formatOutput = true;
            $root = $dom->createElement("error");
            $dom->appendChild($root);
        }

        $mensajeNode = $dom->createElement("mensaje", $mensaje);
        $root->appendChild($mensajeNode);

        echo $dom->saveXML();
        exit;
    }
    private function enviarRespuesta($mensaje, $dom = null, $root = null) {
        if (!$dom) {
            header("Content-Type: application/xml; charset=UTF-8");
            $dom = new DOMDocument("1.0", "UTF-8");
            $dom->formatOutput = true;
            $root = $dom->createElement("respuesta");
            $dom->appendChild($root);
        }

        $mensajeNode = $dom->createElement("mensaje", $mensaje);
        $root->appendChild($mensajeNode);

        echo $dom->saveXML();
        exit;
    }

    private function generarXML($resultado) {
        header("Content-Type: application/xml; charset=UTF-8");

        $dom = new DOMDocument("1.0", "UTF-8");
        $dom->formatOutput = true;
        $root = $dom->createElement("productos");
        $dom->appendChild($root);

        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $producto = $dom->createElement("producto");
                $producto->setAttribute("id", $fila["id"]);
                $producto->setAttribute("nombre", $fila["nombre"]);
                $producto->setAttribute("precio", $fila["precio"]);
                $producto->setAttribute("stock", $fila["stock"]);
                $root->appendChild($producto);
            }
        } else {
            $this->enviarError("No se encontraron productos", $dom, $root);
        }

        echo $dom->saveXML();
    }
}

$service = new ProductoService();
$service->validarAcceso();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'crear':
                $service->crearProducto();
                break;
            case 'actualizar':
                $service->actualizarProducto();
                break;
            case 'eliminar':
                $service->eliminarProducto();
                break;
            default:
                $service->enviarError("Acción no válida");
        }
    } else {
        $service->enviarError("Acción no especificada");
    }
} else {
    $service->obtenerProductos();
}

?>