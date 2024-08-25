<?php
session_start(); // Iniciar sesión PHP

// Verificar si el usuario no está autenticado, redirigir al inicio de sesión si es así
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

require_once "conn/conne.php";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_venta = $_GET['id'];

    // Consultar la venta para obtener los detalles de los productos vendidos
    $sql_consulta_venta = "SELECT * FROM Ventas WHERE id = $id_venta";
    $result = $conn->query($sql_consulta_venta);

    if ($result->num_rows > 0) {
        // Inicializar un array para almacenar los productos y las cantidades vendidas
        $productos_vendidos = [];

        while ($row = $result->fetch_assoc()) {
            $id_producto = $row['id_producto'];
            $cantidad_vendida = $row['cantidad_vendida'];

            // Guardar los productos y cantidades vendidas en un array
            if (!isset($productos_vendidos[$id_producto])) {
                $productos_vendidos[$id_producto] = 0;
            }
            $productos_vendidos[$id_producto] += $cantidad_vendida;
        }

        // Eliminar la venta
        $sql_eliminar_venta = "DELETE FROM Ventas WHERE id = $id_venta";

        if ($conn->query($sql_eliminar_venta)) {
            // Restaurar la cantidad disponible de los productos
            foreach ($productos_vendidos as $id_producto => $cantidad_vendida) {
                $sql_actualizar_producto = "UPDATE Productos SET cantidad_disponible = cantidad_disponible + $cantidad_vendida WHERE id = $id_producto";
                $conn->query($sql_actualizar_producto);
            }

            // Mostrar mensaje de éxito
            echo '<script>alert("La factura de la venta ha sido eliminada exitosamente.");';
            echo 'window.location = "ver_ventas.php";</script>';
        } else {
            // Mostrar mensaje de error si falla la eliminación
            echo '<script>alert("Error al eliminar la factura de la venta.");';
            echo 'window.location = "ver_ventas.php";</script>';
        }
    } else {
        // Mostrar mensaje si no se encuentra la venta
        echo '<script>alert("No se encontró la factura de la venta.");';
        echo 'window.location = "ver_ventas.php";</script>';
    }
}

$conn->close(); // Cerrar la conexión al final del script
?>
