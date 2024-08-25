<?php
session_start(); // Iniciar sesión PHP

// Verificar si el usuario no está autenticado, redirigir al inicio de sesión si es así
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}
require_once "conn/conne.php";

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $rollos_a_agregar = $_POST['rollos_a_agregar'];

    // Obtener la cantidad actual de rollos y cantidad disponible del producto
    $sql = "SELECT rollos_disponibles, cantidad_disponible FROM Productos WHERE id = $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $rollos_disponibles = $row['rollos_disponibles'] + $rollos_a_agregar;
        $cantidad_disponible = $row['cantidad_disponible'] + ($rollos_a_agregar * 30);

        // Actualizar la base de datos con los nuevos valores
        $sql = "UPDATE Productos SET rollos_disponibles = $rollos_disponibles, cantidad_disponible = $cantidad_disponible WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            echo '<script>alert("Rollos agregados exitosamente"); window.location.href = "ver_productos.php";</script>';
        } else {
            echo "Error al actualizar el producto: " . $conn->error;
        }
    } else {
        echo "Producto no encontrado";
    }

    $conn->close();
} else {
    // Obtener el ID del producto desde la URL
    $id = $_GET['id'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/logo.png">
    <title>Agregar Rollos - Tienda de Vinil e Impresión</title>
    <link rel="stylesheet" href="css/styles_ver.css">
</head>
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
            <h1 style="margin-left: 15%;">Agregar Rollos</h1>
            <form action="agregar_rollos.php" method="post" class="form">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                
                <label for="rollos_a_agregar">Rollos a Agregar:</label>
                <input type="number" id="rollos_a_agregar" name="rollos_a_agregar" required>
                
                <input type="submit" value="Agregar">
            </form>
        </main>
        <footer>
            <p>&copy; 2024 Tienda de Vinil e Impresión. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>
