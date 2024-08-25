<?php
session_start(); // Iniciar sesión PHP

// Verificar si el usuario no está autenticado, redirigir al inicio de sesión si es así
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}
require_once "conn/conne.php";

// Función para obtener las ventas agrupadas por fecha
function obtenerVentasPorFecha($conn, $fecha) {
    $sql = "SELECT Ventas.id, Ventas.fecha, Productos.codigo AS nombre, Ventas.cantidad_vendida, Ventas.precio_total, Ventas.metodo_de_pago, Ventas.descripcion 
            FROM Ventas 
            INNER JOIN Productos ON Ventas.id_producto = Productos.id 
            WHERE Ventas.fecha = '$fecha'";
    $result = $conn->query($sql);
    $ventas = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $ventas[] = $row;
        }
    }
    return $ventas;
}

// Función para obtener las fechas únicas de ventas registradas
function obtenerFechasUnicas($conn) {
    $sql = "SELECT DISTINCT fecha FROM Ventas ORDER BY fecha DESC";
    $result = $conn->query($sql);
    $fechas = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $fechas[] = $row['fecha'];
        }
    }
    return $fechas;
}

// Verificar si se ha seleccionado una fecha específica para mostrar las ventas
$ventas = [];
$fechas = obtenerFechasUnicas($conn);

if (isset($_GET['fecha'])) {
    $fecha_seleccionada = $_GET['fecha'];
    $ventas = obtenerVentasPorFecha($conn, $fecha_seleccionada);
} else {
    // Obtener ventas del día más reciente por defecto
    if (!empty($fechas)) {
        $fecha_seleccionada = $fechas[0];
        $ventas = obtenerVentasPorFecha($conn, $fecha_seleccionada);
    }
}

// Cerrar la conexión
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/logo.png">
    <title>Ver Ventas - Tienda de Vinil e Impresión</title>
    <link rel="stylesheet" href="css/styles_ver.css">
</head>

<style>
    /* Estilos para el formulario y el campo select */
    .select-form {
        display: inline-block; /* Para que el formulario no ocupe toda la línea */
    }

    .select-field {
        padding: 8px; /* Ajusta el padding según sea necesario */
        font-size: 14px; /* Tamaño de la fuente */
        border: 1px solid #ccc; /* Borde del campo */
        border-radius: 4px; /* Borde redondeado */
        background-color: #fff; /* Color de fondo */
        color: #333; /* Color del texto */
        cursor: pointer; /* Cambia el cursor al pasar sobre el select */
        outline: none; /* Quita el borde de enfoque predeterminado */
        width: 200px; /* Ancho del campo select */
    }

    /* Estilos cuando el select está enfocado */
    .select-field:focus {
        border-color: #999; /* Color del borde al enfocar */
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1); /* Sombra al enfocar */
    }

    /* Estilos para las opciones del select */
    .select-field option {
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
            <h1>Ventas:</h1>
            <a href="registrar_venta.php" class="button">Registrar una venta</a>
            <div class="ventas-lista">
                <h2>Seleccionar Fecha:</h2>
                <form action="ver_ventas.php" method="get" class="select-form">
                    <select name="fecha" onchange="this.form.submit()" class="select-field">
                        <?php foreach ($fechas as $fecha) : ?>
                            <option value="<?php echo $fecha; ?>" <?php echo ($fecha === $fecha_seleccionada) ? 'selected' : ''; ?>><?php echo $fecha; ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <h2>Ventas del <?php echo isset($fecha_seleccionada) ? $fecha_seleccionada : 'hoy'; ?></h2>
                <?php if (!empty($ventas)) : ?>
                    <table>
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Descripción</th>
                            <th>Cantidad Vendida</th>
                            <th>Precio Total</th>
                            <th>Método de Pago</th>
                            <th style="width: 12%">Opciones</th>
                        </tr>
                        <?php foreach ($ventas as $venta) : ?>
                            <tr>
                                <td><?php echo $venta['fecha']; ?></td>
                                <td><?php echo $venta['nombre']; ?></td>
                                <td><?php echo $venta['descripcion']; ?></td>
                                <td><?php echo $venta['cantidad_vendida']; ?></td>
                                <td><?php echo $venta['precio_total']; ?></td>
                                <td><?php echo $venta['metodo_de_pago']; ?></td>
                                <td>
                                    <a class="accion-link" href='editar_venta.php?id=<?php echo $venta["id"]; ?>'>Editar</a> | 
                                    <a class="accion-link" href='eliminar_venta.php?id=<?php echo $venta["id"]; ?>' onclick="return confirmarEliminacion()">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else : ?>
                    <p>No hay ventas registradas para esta fecha.</p>
                <?php endif; ?>
            </div>
        </main>
        <footer>
            <p>&copy; 2024 Tienda de Vinil e Impresión. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>

<script>
function confirmarEliminacion() {
    return confirm("¿Estás seguro que deseas borrar esta venta?");
}
</script>
