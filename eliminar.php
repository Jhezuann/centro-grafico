<?php
session_start(); // Iniciar sesión PHP

// Verificar si el usuario no está autenticado, redirigir al inicio de sesión si es así
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

require_once "conn/conne.php";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_producto = $_GET['id'];

    // Mostrar mensaje de advertencia antes de eliminar
    echo '<script>
            if (confirm("¿Estás seguro de eliminar este producto? Al hacerlo también se eliminarán las facturas de ventas asociadas.")) {
                window.location = "eliminar_confirmado.php?id=' . $id_producto . '"; // Redirigir a la confirmación de eliminación
            } else {
                window.location = "ver_productos.php"; // Redirigir de vuelta a la lista de productos
            }
          </script>';
}

$conn->close(); // Cerrar la conexión al final del script
?>
