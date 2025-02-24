<?php
class ClienteProducto {
    private $token;
    private $apiUrl;

    public function __construct($url, $token) {
        $this->apiUrl = $url;
        $this->token = $token;
    }

    public function obtenerProductos($nombre = null, $precio_mayor_que = null) {
        $url = $this->apiUrl . "?token=" . $this->token;

        if (!empty($nombre)) {
            $url .= "&nombre=" . urlencode($nombre);
        }
        if (!empty($precio_mayor_que)) {
            $url .= "&precio_mayor_que=" . urlencode($precio_mayor_que);
        }

        return $this->procesarXML($url);
    }

    private function procesarXML($url) {
        $xml = simplexml_load_file($url);

        if (!$xml) {
            die("Error al cargar XML");
        }

        return $xml;
    }
}

// Inicialización del cliente
$cliente = new ClienteProducto("http://localhost/webservices/clase3/servidor.php", "123456");

$filtro_nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
$filtro_precio = isset($_POST['precio_mayor_que']) ? $_POST['precio_mayor_que'] : '';

$xml = $cliente->obtenerProductos($filtro_nombre, $filtro_precio);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obtener Productos XML</title>
    <style>
        table { width: 60%; border-collapse: collapse; margin: 20px auto; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        form { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>

    <h2 style="text-align: center;">Buscar Productos</h2>

    <form method="post">
        Nombre: <input type="text" name="nombre" value="<?php echo htmlspecialchars($filtro_nombre); ?>">
        Precio mayor que: <input type="number" name="precio_mayor_que" value="<?php echo htmlspecialchars($filtro_precio); ?>">
        <button type="submit">Buscar</button>
    </form>

    <h2 style="text-align: center;">Lista de Productos</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock</th>
        </tr>
        <?php foreach ($xml->producto as $producto): ?>
        <tr>
            <td><?php echo $producto['id']; ?></td>
            <td><?php echo $producto['nombre']; ?></td>
            <td><?php echo $producto['precio']; ?></td>
            <td><?php echo $producto['stock']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>