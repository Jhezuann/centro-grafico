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

    // Eliminar el producto y manejar la restricción de clave externa
    try {
        // Iniciar transacción
        $conn->begin_transaction();

        // Eliminar primero las ventas asociadas al producto
        $sql_eliminar_ventas = "DELETE FROM Ventas WHERE id_producto = $id_producto";
        if (!$conn->query($sql_eliminar_ventas)) {
            throw new Exception("Error al eliminar las ventas asociadas al producto.");
        }

        // Luego eliminar el producto
        $sql_eliminar_producto = "DELETE FROM Productos WHERE id = $id_producto";
        if (!$conn->query($sql_eliminar_producto)) {
            throw new Exception("Error al eliminar el producto.");
        }

        // Confirmar la transacción si todo fue exitoso
        $conn->commit();

        // Mostrar mensaje de éxito y redirigir
        echo '<script>alert("El producto y las ventas asociadas han sido eliminados correctamente.");';
        echo 'window.location = "ver_productos.php";</script>';

    } catch (Exception $e) {
        // Revertir la transacción si hubo algún error
        $conn->rollback();

        // Mostrar mensaje de error
        echo '<script>alert("Error: ' . $e->getMessage() . '");';
        echo 'window.location = "ver_productos.php";</script>';
    }
}

$conn->close(); // Cerrar la conexión al final del script
?>
