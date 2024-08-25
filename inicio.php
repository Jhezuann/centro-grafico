<?php
session_start(); // Iniciar sesión PHP

// Verificar si el usuario no está autenticado, redirigir al inicio de sesión si es así
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/logo.png">
    <title>Tienda de Vinil e Impresión</title>
    <link rel="stylesheet" href="css/style_inicio.css">
</head>
<style>
    /* estilo para img logo */
    img {
        width: 20%;
        height: auto;
        margin: 10px auto 50px;
    }
</style>
<body>
    <div class="wrapper">
        <header>
            <nav>
                <div class="logo">
                    Centro Grafico
                </div>
                <ul class="nav-links">
                    <li><a href="inicio.php">Inicio</a></li>
                    <li><a href="ver_productos.php">Productos</a></li>
                    <li><a href="ver_ventas.php">Ventas</a></li>
                    <li><a href="salir.php">Salir</a></li>
                </ul>
            </nav>
        </header>
        <main>
            <section class="hero">
                <h1>Bienvenido a Nuestra Tienda de Vinil e Impresión</h1>
                <img src="img/logo.png">
                <p>Encuentra una amplia variedad de vinilos y servicios de impresión personalizados.</p>
            </section>
            <section class="about">
                <h2>Sobre Nosotros</h2>
                <p>Somos expertos en vinilos e impresión. Ofrecemos productos de alta calidad y servicios de impresión personalizados para todas tus necesidades.</p>
            </section>
            <section class="services">
                <h2>Servicios</h2>
                <ul>
                    <li>Venta de vinilos por metro</li>
                    <li>Impresión personalizada en vinil</li>
                    <li>Asesoría y soporte técnico</li>
                </ul>
            </section>
        </main>
        <footer>
            <p>&copy; 2024 Tienda de Vinil e Impresión. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>
