<?php
session_start(); // Iniciar sesión PHP

// Verificar si el usuario ya está autenticado, redirigir a la página principal si es así
if (isset($_SESSION['usuario'])) {
    header("Location: inicio.php");
    exit;
}

require_once "conn/conne.php";

// Manejar la solicitud POST para iniciar sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $contraseña = $_POST['contraseña'];
    
    // Cifrar la contraseña proporcionada por el usuario
    $contraseña_cifrada = hash('sha256', $contraseña);

    // Consulta para verificar las credenciales del usuario
    $sql = "SELECT * FROM Usuario WHERE nombre = '$nombre' AND contraseña = '$contraseña_cifrada'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Iniciar sesión y redirigir a la página de ventas
        $_SESSION['usuario'] = $nombre;
        echo '<script>alert("Inicio de sesión exitoso"); window.location.href = "inicio.php";</script>';
        exit;
    } else {
        echo '<script>alert("Nombre de usuario o contraseña incorrectos.");</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/logo.png">
    <title>Iniciar Sesión - Tienda de Vinil e Impresión</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="wrapper">
        <div class="logo">Centro Gráfico</div>
        <form method="post" class="form">
            <?php if (isset($error)) : ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <label for="nombre">Nombre de Usuario:</label>
            <input type="text" id="nombre" name="nombre" required>
            
            <label for="contraseña">Contraseña:</label>
            <input type="password" id="contraseña" name="contraseña" required>
            
            <input type="submit" value="Iniciar Sesión">
        </form>
        <footer class="footer">
            <p>&copy; 2024 Tienda de Vinil e Impresión. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>
