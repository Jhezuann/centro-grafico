<?php
session_start(); // Iniciar sesión PHP

// Verificar si el usuario no está autenticado, redirigir al inicio de sesión si es así
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}
require_once "conn/conne.php";

// Obtener lista de productos
$sql_productos = "SELECT id, codigo, tipo_producto, cantidad_disponible FROM Productos";
$result_productos = $conn->query($sql_productos);

// Manejar la solicitud POST para editar la venta
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_venta = $_POST['id'];
    $fecha = $_POST['fecha'];
    $id_producto = $_POST['id_producto'];
    $cantidad_vendida_nueva = $_POST['cantidad_vendida'];
    $metodo_de_pago = $_POST['metodo_de_pago'];
    $descripcion = $_POST['descripcion']; // Nuevo campo descripción

    // Obtener la venta actual para obtener la cantidad vendida anterior y la descripción anterior
    $sql_venta = "SELECT Ventas.id, Ventas.fecha, Ventas.id_producto, Ventas.cantidad_vendida, Ventas.precio_total, Ventas.metodo_de_pago, Ventas.descripcion, Productos.codigo AS nombre_producto 
                  FROM Ventas 
                  INNER JOIN Productos ON Ventas.id_producto = Productos.id 
                  WHERE Ventas.id = $id_venta";
    $result_venta = $conn->query($sql_venta);
    
    if ($result_venta->num_rows > 0) {
        $row_venta = $result_venta->fetch_assoc();
        $id_producto = $row_venta['id_producto'];
        $cantidad_vendida_anterior = $row_venta['cantidad_vendida'];

        // Obtener la cantidad disponible actual del producto
        $sql_producto = "SELECT cantidad_disponible FROM Productos WHERE id = $id_producto";
        $result_producto = $conn->query($sql_producto);
        
        if ($result_producto->num_rows > 0) {
            $row_producto = $result_producto->fetch_assoc();
            $cantidad_disponible = $row_producto['cantidad_disponible'];

            // Calcular la diferencia de cantidad vendida
            $diferencia_cantidad = $cantidad_vendida_nueva - $cantidad_vendida_anterior;

            // Validar si la nueva cantidad vendida excede la cantidad disponible
            if ($diferencia_cantidad > $cantidad_disponible) {
                echo '<script>alert("La cantidad vendida excede la cantidad disponible del producto."); history.go(-1);</script>';
                exit; // Detener la ejecución si la nueva cantidad excede la disponible
            }

            // Actualizar la venta
            $sql_update = "UPDATE Ventas 
                           SET fecha = '$fecha', id_producto = '$id_producto', cantidad_vendida = '$cantidad_vendida_nueva', 
                               metodo_de_pago = '$metodo_de_pago', descripcion = '$descripcion' 
                           WHERE id = $id_venta";

            if ($conn->query($sql_update) === TRUE) {
                // Actualizar la cantidad disponible del producto correspondiente
                $sql_update_producto = "UPDATE Productos 
                                       SET cantidad_disponible = cantidad_disponible + $cantidad_vendida_anterior - $cantidad_vendida_nueva
                                       WHERE id = $id_producto";
                
                if ($conn->query($sql_update_producto) === TRUE) {
                    echo '<script>alert("Venta actualizada exitosamente"); window.location.href = "ver_ventas.php";</script>';
                } else {
                    echo "Error al actualizar la cantidad disponible del producto: " . $conn->error;
                }
            } else {
                echo "Error al actualizar la venta: " . $conn->error;
            }
        } else {
            echo "Producto no encontrado.";
        }
    } else {
        echo "No se encontró la venta.";
    }
}

// Mostrar el formulario para editar la venta
if (isset($_GET['id'])) {
    $id_venta = $_GET['id'];

    // Obtener los detalles de la venta para mostrar en el formulario
    $sql = "SELECT Ventas.id, Ventas.fecha, Ventas.id_producto, Ventas.cantidad_vendida, Ventas.precio_total, Ventas.metodo_de_pago, Ventas.descripcion, Productos.codigo, Productos.tipo_producto AS nombre_producto 
            FROM Ventas 
            INNER JOIN Productos ON Ventas.id_producto = Productos.id 
            WHERE Ventas.id = $id_venta";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $venta = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/logo.png">
    <title>Editar Venta - Tienda de Vinil e Impresión</title>
    <link rel="stylesheet" href="css/styles_ver.css">
</head>
<style>
    /* Estilos para los labels */
    label {
        display: block;
        margin-bottom: 8px;
    }
    /* Estilos para los campos input y select */
    input[type="date"],
    select {
        padding: 8px; /* Ajusta el padding según sea necesario */
        font-size: 14px; /* Tamaño de la fuente */
        border: 1px solid #ccc; /* Borde del campo */
        border-radius: 4px; /* Borde redondeado */
        background-color: #fff; /* Color de fondo */
        color: #333; /* Color del texto */
        width: 100%; /* Ancho completo del contenedor */
        box-sizing: border-box; /* Incluir padding y border en el ancho total */
        margin-bottom: 15px; /* Espacio entre campos */
    }

    /* Estilos cuando los campos están enfocados */
    input[type="date"]:focus,
    select:focus {
        border-color: #999; /* Color del borde al enfocar */
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1); /* Sombra al enfocar */
    }

    /* Estilos para las opciones del select */
    select option {
        background-color: #fff; /* Color de fondo de las opciones */
        color: #333; /* Color del texto de las opciones */
        padding: 8px; /* Espaciado interno de las opciones */
    }
</style>
<body>
    <div class="wrapper">
        <header>
            <nav>
                <div class="logo">Centro Grafico</div>
                <ul class="nav-links">
                    <li><a href="inicio.php">Inicio</a></li>
                    <li><a href="ver_productos.php">Productos</a></li>
                    <li><a href="ver_ventas.php">Ventas</a></li>
                    <li><a href="salir.php">Salir</a></li>
                </ul>
            </nav>
        </header>
        <main>
            <h1>Editar Venta</h1>
            <form method="post" class="form">
                <input type="hidden" name="id" value="<?php echo $venta['id']; ?>">
                
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" required>
                
                <label for="id_producto">Producto:</label>
                <select id="id_producto" name="id_producto" required>
                    <?php
                    if ($result_productos->num_rows > 0) {
                        while($row = $result_productos->fetch_assoc()) {
                            $selected = ($row['id'] == $venta['id_producto']) ? 'selected' : '';
                            echo "<option value='" . $row["id"] . "' $selected>" . $row["codigo"] . " (" . $row["tipo_producto"] . ") (Disponible: " . $row["cantidad_disponible"] . " metros)</option>";
                        }
                    } else {
                        echo "<option value=''>No hay productos disponibles</option>";
                    }
                    ?>
                </select>
                
                <label for="cantidad_vendida">Cantidad Vendida (en metros):</label>
                <input type="number" step="0.01" id="cantidad_vendida" name="cantidad_vendida" value="<?php echo $venta['cantidad_vendida']; ?>" required>
                
                <label for="metodo_de_pago">Método de Pago:</label>
                <select id="metodo_de_pago" name="metodo_de_pago" required>
                    <option value="Dollar" <?php echo ($venta['metodo_de_pago'] === 'Dollar') ? 'selected' : 'Dollar'; ?>>Dollar</option>
                    <option value="Efectivo Bs" <?php echo ($venta['metodo_de_pago'] === 'Efectivo Bs') ? 'selected' : 'Efectivo Bs'; ?>>Efectivo Bs</option>
                    <option value="Pago Movil" <?php echo ($venta['metodo_de_pago'] === 'Pago Movil') ? 'selected' : ''; ?>>Pago Movil</option>
                </select>
                
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required><?php echo $venta['descripcion']; ?></textarea>

                <input type="submit" value="Guardar Cambios">
            </form>
        </main>
        <footer>
            <p>&copy; 2024 Tienda de Vinil e Impresión. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>
<?php
    } else {
        echo "No se encontró la venta.";
    }
} else {
    echo "ID de venta no especificado.";
}

$conn->close(); // Cerrar la conexión al final del script
?>
