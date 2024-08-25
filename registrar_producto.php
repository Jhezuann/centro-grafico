<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Iniciar sesión PHP

// Verificar si el usuario no está autenticado, redirigir al inicio de sesión si es así
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}
require_once "conn/conne.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = $_POST['codigo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio_por_metro = $_POST['precio_por_metro'] ?? null;
    $precio = $_POST['precio'] ?? null; // Nuevo campo precio
    $rollos_disponibles = intval($_POST['rollos_disponibles'] ?? 0);
    $bobina = intval($_POST['bobina'] ?? 0);
    $tipo_producto = $_POST['tipo_producto'] ?? '';
    $color = $_POST['color'] ?? '';
    $grosor_lamina_acrilico = $_POST['grosor_lamina_acrilico'] ?? null;
    $grosor_lamina_pvc = $_POST['grosor_lamina_pvc'] ?? null;
    $formato_impresion_tabloide = $_POST['formato_impresion_tabloide'] ?? null;
    $cantidad_minima = $_POST['cantidad_minima'] ?? 0;

    // Calcular cantidad disponible basada en los rollos y la bobina
    $cantidad_disponible = $rollos_disponibles * $bobina;

    // Verificar y asignar valores para campos de productos específicos
    if ($tipo_producto === 'lamina_acrilico') {
        $codigo = $_POST['codigo_acrilico'] ?? $codigo;
        $descripcion = $_POST['descripcion_acrilico'] ?? $descripcion;
        $color = $_POST['color_acrilico'] ?? $color;
        $cantidad_disponible = intval($_POST['cantidad_disponible_acrilico'] ?? 0);
        $cantidad_minima = intval($_POST['cantidad_minima_acrilico'] ?? 0);
        $precio = $_POST['precio_acrilico'] ?? $precio; // Nuevo campo precio
        $grosor_lamina_acrilico = $_POST['grosor_lamina_acrilico'] ?? $grosor_lamina_acrilico;
    } elseif ($tipo_producto === 'lamina_pvc') {
        $codigo = $_POST['codigo_pvc'] ?? $codigo;
        $descripcion = $_POST['descripcion_pvc'] ?? $descripcion;
        $color = $_POST['color_pvc'] ?? $color;
        $grosor_lamina_pvc = $_POST['grosor_lamina_pvc'] ?? $grosor_lamina_pvc;
        $cantidad_disponible = intval($_POST['cantidad_disponible_pvc'] ?? 0);
        $cantidad_minima = intval($_POST['cantidad_minima_pvc'] ?? 0);
        $precio = $_POST['precio_pvc'] ?? $precio; // Nuevo campo precio
    } elseif ($tipo_producto === 'impresion_tabloide') {
        $codigo = $_POST['codigo_tabloide'] ?? $codigo;
        $descripcion = $_POST['descripcion_tabloide'] ?? $descripcion;
        $formato_impresion_tabloide = $_POST['formato_impresion_tabloide'] ?? $formato_impresion_tabloide;
        $cantidad_disponible = intval($_POST['cantidad_disponible_tabloide'] ?? 0);
        $cantidad_minima = intval($_POST['cantidad_minima_tabloide'] ?? 0);
        $precio = $_POST['precio_tabloide'] ?? $precio; // Nuevo campo precio
    }

    $sql = "INSERT INTO Productos (codigo, descripcion, precio_por_metro, precio, cantidad_disponible, rollos_disponibles, bobina, tipo_producto, color, grosor_lamina_acrilico, grosor_lamina_pvc, formato_impresion_tabloide, cantidad_minima) 
            VALUES ('$codigo', '$descripcion', '$precio_por_metro', '$precio', '$cantidad_disponible', '$rollos_disponibles', '$bobina', '$tipo_producto', '$color', '$grosor_lamina_acrilico', '$grosor_lamina_pvc', '$formato_impresion_tabloide', '$cantidad_minima')";

    if ($conn->query($sql) === TRUE) {
        echo '<script>alert("Producto agregado exitosamente"); window.location.href = "ver_productos.php";</script>';
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/logo.png">
    <title>Registrar Producto - Tienda de Vinil e Impresión</title>
    <link rel="stylesheet" href="css/styles_ver.css">
    <script>
        function mostrarCampos() {
            var tipoProducto = document.getElementById('tipo_producto').value;
            var todosLosCampos = document.querySelectorAll('.campo');
            todosLosCampos.forEach(campo => campo.style.display = 'none');

            document.querySelectorAll('.campo input, .campo textarea, .campo select').forEach(input => input.required = false);

            if (tipoProducto === 'vinil') {
                document.getElementById('vinil_campos').style.display = 'block';
                document.querySelectorAll('#vinil_campos input, #vinil_campos textarea, #vinil_campos select').forEach(input => input.required = true);
            } else if (tipoProducto === 'lamina_acrilico') {
                document.getElementById('acrilico_campos').style.display = 'block';
                document.querySelectorAll('#acrilico_campos input, #acrilico_campos textarea, #acrilico_campos select').forEach(input => input.required = true);
            } else if (tipoProducto === 'lamina_pvc') {
                document.getElementById('pvc_campos').style.display = 'block';
                document.querySelectorAll('#pvc_campos input, #pvc_campos textarea, #pvc_campos select').forEach(input => input.required = true);
            } else if (tipoProducto === 'impresion_tabloide') {
                document.getElementById('tabloide_campos').style.display = 'block';
                document.querySelectorAll('#tabloide_campos input, #tabloide_campos textarea, #tabloide_campos select').forEach(input => input.required = true);
            }
        }

        window.onload = function() {
            mostrarCampos();
        };
    </script>
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
            <h1 style="margin-left: 15%;">Registrar Producto</h1>
            <form method="post" class="form">
                <label for="tipo_producto">Tipo de Producto:</label>
                <select id="tipo_producto" name="tipo_producto" onchange="mostrarCampos()" required>
                    <option value="">Seleccione una opción</option>
                    <option value="vinil">Vinil</option>
                    <option value="lamina_acrilico">Lámina de Acrílico</option>
                    <option value="lamina_pvc">Lámina de PVC</option>
                    <option value="impresion_tabloide">Impresión en Tabloide</option>
                </select>

                <div id="vinil_campos" class="campo" style="display: none;">
                    <label for="codigo">Código:</label>
                    <input type="text" id="codigo" name="codigo" required>

                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" required></textarea>

                    <label for="precio_por_metro">Precio por Metro:</label>
                    <input type="number" step="0.01" id="precio_por_metro" name="precio_por_metro" required>

                    <label for="rollos_disponibles">Rollos Disponibles:</label>
                    <input type="number" id="rollos_disponibles" name="rollos_disponibles" required>

                    <label for="bobina">Metros por Bobina:</label>
                    <select id="bobina" name="bobina" class="campo-input" required>
                        <option value="">Seleccione una opción</option>
                        <option value="30">30 metros</option>
                        <option value="50">50 metros</option>
                    </select>

                    <label for="color">Color:</label>
                    <input type="text" id="color" name="color" required>

                    <label for="cantidad_minima">Cantidad Mínima:</label>
                    <input type="number" step="0.01" id="cantidad_minima" name="cantidad_minima" required>
                </div>

                <div id="acrilico_campos" class="campo" style="display: none;">
                    <label for="codigo_acrilico">Código:</label>
                    <input type="text" id="codigo_acrilico" name="codigo_acrilico" required>

                    <label for="descripcion_acrilico">Descripción:</label>
                    <textarea id="descripcion_acrilico" name="descripcion_acrilico" required></textarea>

                    <label for="color_acrilico">Color:</label>
                    <input type="text" id="color_acrilico" name="color_acrilico" required>

                    <label for="cantidad_disponible_acrilico">Cantidad Disponible:</label>
                    <input type="number" id="cantidad_disponible_acrilico" name="cantidad_disponible_acrilico" required>

                    <label for="cantidad_minima_acrilico">Cantidad Mínima:</label>
                    <input type="number" step="0.01" id="cantidad_minima_acrilico" name="cantidad_minima_acrilico" required>

                    <label for="grosor_lamina_acrilico">Grosor de la Lámina:</label>
                    <select id="grosor_lamina_acrilico" name="grosor_lamina_acrilico" required>
                        <option value="">Seleccione una opción</option>
                        <option value="2mm">2mm</option>
                        <option value="3mm">3mm</option>
                        <option value="4mm">4mm</option>
                    </select>

                    <label for="precio_acrilico">Precio:</label>
                    <input type="number" step="0.01" id="precio_acrilico" name="precio_acrilico">
                </div>

                <div id="pvc_campos" class="campo" style="display: none;">
                    <label for="codigo_pvc">Código:</label>
                    <input type="text" id="codigo_pvc" name="codigo_pvc" required>

                    <label for="descripcion_pvc">Descripción:</label>
                    <textarea id="descripcion_pvc" name="descripcion_pvc" required></textarea>

                    <label for="color_pvc">Color:</label>
                    <input type="text" id="color_pvc" name="color_pvc" required>

                    <label for="cantidad_disponible_pvc">Cantidad Disponible:</label>
                    <input type="number" id="cantidad_disponible_pvc" name="cantidad_disponible_pvc" required>

                    <label for="cantidad_minima_pvc">Cantidad Mínima:</label>
                    <input type="number" step="0.01" id="cantidad_minima_pvc" name="cantidad_minima_pvc" required>

                    <label for="grosor_lamina_pvc">Grosor de la Lámina:</label>
                    <select id="grosor_lamina_pvc" name="grosor_lamina_pvc" required>
                        <option value="">Seleccione una opción</option>
                        <option value="3mm">3mm</option>
                        <option value="5mm">5mm</option>
                    </select>

                    <label for="precio_pvc">Precio:</label>
                    <input type="number" step="0.01" id="precio_pvc" name="precio_pvc">
                </div>

                <div id="tabloide_campos" class="campo" style="display: none;">
                    <label for="codigo_tabloide">Código:</label>
                    <input type="text" id="codigo_tabloide" name="codigo_tabloide" required>

                    <label for="descripcion_tabloide">Descripción:</label>
                    <textarea id="descripcion_tabloide" name="descripcion_tabloide" required></textarea>

                    <label for="formato_impresion_tabloide">Formato de Impresión:</label>
                    <select id="formato_impresion_tabloide" name="formato_impresion_tabloide" required>
                        <option value="">Seleccione una opción</option>
                        <option value="Papel boom">Papel boom</option>
                        <option value="Glasee 150">Glasee 150</option>
                        <option value="Glasee 115">Glasee 115</option>
                        <option value="Glasee 300">Glasee 300</option>
                        <option value="Glasee adhesivo">Glasee adhesivo</option>
                    </select>

                    <label for="cantidad_disponible_tabloide">Cantidad Disponible:</label>
                    <input type="number" id="cantidad_disponible_tabloide" name="cantidad_disponible_tabloide" required>

                    <label for="cantidad_minima_tabloide">Cantidad Mínima:</label>
                    <input type="number" step="0.01" id="cantidad_minima_tabloide" name="cantidad_minima_tabloide" required>

                    <label for="precio_tabloide">Precio:</label>
                    <input type="number" step="0.01" id="precio_tabloide" name="precio_tabloide">
                </div>

                <input type="submit" value="Registrar">
            </form>
        </main>
        <footer>
            <p>&copy; 2024 Tienda de Vinil e Impresión. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>
