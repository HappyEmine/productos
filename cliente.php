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
    public function enviarPeticion($accion, $datos) {
        $url = $this->apiUrl . "?token=" . $this->token;
        $datos['accion'] = $accion;

        $opciones = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query($datos),
            ],
        ];

        $contexto = stream_context_create($opciones);
        $resultado = file_get_contents($url, false, $contexto);

        return simplexml_load_string($resultado);
    }
}

// Inicialización del cliente
$cliente = new ClienteProducto("http://localhost/webservices/clase3/servidor.php", "123456");

$filtro_nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
$filtro_precio = isset($_POST['precio_mayor_que']) ? $_POST['precio_mayor_que'] : '';
if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    if ($accion === 'crear') {
        $resultado = $cliente->enviarPeticion('crear', $_POST);
        echo "<p>" . $resultado->mensaje . "</p>";
    } elseif ($accion === 'actualizar') {
        $resultado = $cliente->enviarPeticion('actualizar', $_POST);
        echo "<p>" . $resultado->mensaje . "</p>";
    } elseif ($accion === 'eliminar') {
        $resultado = $cliente->enviarPeticion('eliminar', $_POST);
        echo "<p>" . $resultado->mensaje . "</p>";
    }
}
$xml = $cliente->obtenerProductos($filtro_nombre, $filtro_precio);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <style>
        table { width: 60%; border-collapse: collapse; margin: 20px auto; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        form { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>

    <h2 style="text-align: center;">Gestión de Productos</h2>

    <form method="post">
        Nombre: <input type="text" name="nombre" value="<?php echo htmlspecialchars($filtro_nombre); ?>">
        Precio mayor que: <input type="number" name="precio_mayor_que" value="<?php echo htmlspecialchars($filtro_precio); ?>">
        <button type="submit">Buscar</button>
    </form>

    <h3 style="text-align: center;">Agregar Producto</h3>
    <form method="post">
        <input type="hidden" name="accion" value="crear">
        Nombre: <input type="text" name="nombre" required>
        Precio: <input type="number" name="precio" required>
        Stock: <input type="number" name="stock" required>
        <button type="submit">Agregar</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock</th>
            <th style="text-align: center;">Acciones</th>
        </tr>
        <?php foreach ($xml->producto as $producto): ?>
        <tr>
            <td><?php echo $producto['id']; ?></td>
            <td><?php echo $producto['nombre']; ?></td>
            <td><?php echo $producto['precio']; ?></td>
            <td><?php echo $producto['stock']; ?></td>
            <td style="text-align: right;">
                <form method="post" style="display: inline;">
                    <input type="hidden" name="accion" value="actualizar">
                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                    Nombre: <input type="text" name="nombre" value="<?php echo $producto['nombre']; ?>">
                    Precio: <input type="number" name="precio" value="<?php echo $producto['precio']; ?>">
                    Stock: <input type="number" name="stock" value="<?php echo $producto['stock']; ?>">
                    <button type="submit">Actualizar</button>
                </form>
                <form method="post" style="display: inline;">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                    <button type="submit">Eliminar</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>