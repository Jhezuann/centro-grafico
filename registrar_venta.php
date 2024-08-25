<?php
session_start(); // Iniciar sesión PHP

// Verificar si el usuario no está autenticado, redirigir al inicio de sesión si es así
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}
require_once "conn/conne.php";

// Obtener lista de productos
$sql_productos = "SELECT id, codigo, tipo_producto, cantidad_disponible, rollos_disponibles, bobina, color, grosor_lamina_acrilico, grosor_lamina_pvc, formato_impresion_tabloide, precio, precio_por_metro FROM Productos";
$result_productos = $conn->query($sql_productos);

// Manejar la solicitud POST para registrar una nueva venta
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha = $_POST['fecha'];
    $id_producto = $_POST['id_producto'];
    $cantidad_vendida = floatval($_POST['cantidad_vendida']);
    $metodo_de_pago = $_POST['metodo_de_pago'];
    $descripcion = $_POST['descripcion']; // Nuevo campo descripción

    // Obtener el precio y el precio por metro del producto seleccionado
    $sql_precio = "SELECT precio_por_metro, precio, cantidad_disponible, tipo_producto FROM Productos WHERE id = $id_producto";
    $result_precio = $conn->query($sql_precio);

    if ($result_precio->num_rows > 0) {
        $row = $result_precio->fetch_assoc();
        $precio_por_metro = floatval($row['precio_por_metro']);
        $precio = floatval($row['precio']);
        $cantidad_disponible = $row['cantidad_disponible'];
        $tipo_producto = $row['tipo_producto'];

        // Validar si la cantidad vendida es mayor que la cantidad disponible
        if ($cantidad_vendida > $cantidad_disponible) {
            echo '<script>alert("La cantidad disponible es insuficiente para completar la venta."); history.go(-1);</script>';
            exit; // Detener la ejecución si la cantidad vendida es mayor que la disponible
        }

        // Calcular el precio total
        if ($tipo_producto == 'vinil') {
            $precio_total = $cantidad_vendida * $precio_por_metro;
        } else {
            $precio_total = $cantidad_vendida * $precio;
        }

        // Registrar la venta
        $sql_venta = "INSERT INTO Ventas (fecha, id_producto, cantidad_vendida, precio_total, metodo_de_pago, descripcion) 
                      VALUES ('$fecha', '$id_producto', '$cantidad_vendida', '$precio_total', '$metodo_de_pago', '$descripcion')";

        if ($conn->query($sql_venta) === TRUE) {
            // Actualizar la cantidad disponible del producto
            $nueva_cantidad_disponible = $cantidad_disponible - $cantidad_vendida;
            $sql_update_producto = "UPDATE Productos SET cantidad_disponible = $nueva_cantidad_disponible WHERE id = $id_producto";

            if ($conn->query($sql_update_producto) === TRUE) {
                echo '<script>alert("Venta registrada exitosamente"); window.location.href = "ver_ventas.php";</script>';
            } else {
                echo "Error al actualizar el producto: " . $conn->error;
            }
        } else {
            echo "Error al registrar la venta: " . $sql_venta . "<br>" . $conn->error;
        }
    } else {
        echo "Producto no encontrado";
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
    <title>Registrar Venta - Tienda de Vinil e Impresión</title>
    <link rel="stylesheet" href="css/styles_ver.css">
    <script>
        function mostrarCampos() {
            var productoSeleccionado = document.getElementById('id_producto').value;
            var tipoProducto = document.querySelector('option[value="' + productoSeleccionado + '"]').dataset.tipo;

            var todosLosCampos = document.querySelectorAll('.campo');
            todosLosCampos.forEach(campo => campo.style.display = 'none');

            if (tipoProducto === 'vinil') {
                document.getElementById('vinil_campos').style.display = 'block';
            } else if (tipoProducto === 'lamina_acrilico') {
                document.getElementById('acrilico_campos').style.display = 'block';
            } else if (tipoProducto === 'lamina_pvc') {
                document.getElementById('pvc_campos').style.display = 'block';
            } else if (tipoProducto === 'impresion_tabloide') {
                document.getElementById('tabloide_campos').style.display = 'block';
            }
        }

        // Llamar a mostrarCampos al cargar la página
        window.onload = function() {
            mostrarCampos();
        };
    </script>
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
            <h1>Registrar Venta</h1>
            <form method="post" class="form">
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" required max="<?php echo date('Y-m-d'); ?>">

                <label for="id_producto">Producto:</label>
                <select id="id_producto" name="id_producto" onchange="mostrarCampos()" required>
                    <option value="">Seleccione una opción</option>
                    <?php
                    if ($result_productos->num_rows > 0) {
                        while ($row = $result_productos->fetch_assoc()) {
                            echo "<option value='" . $row["id"] . "' data-tipo='" . $row["tipo_producto"] . "'>" . $row["codigo"] . " (" . $row["tipo_producto"] . ") - Disponible: " . $row["cantidad_disponible"] . " metros</option>";
                        }
                    } else {
                        echo "<option value=''>No hay productos disponibles</option>";
                    }
                    ?>
                </select>

                <div id="vinil_campos" class="campo" style="display: none;">
                    <label for="cantidad_vendida">Cantidad Vendida (en metros):</label>
                    <input type="number" step="0.01" id="cantidad_vendida" name="cantidad_vendida">
                </div>

                <div id="acrilico_campos" class="campo" style="display: none;">
                    <label for="cantidad_vendida">Cantidad Vendida:</label>
                    <input type="number" step="0.01" id="cantidad_vendida" name="cantidad_vendida">
                </div>

                <div id="pvc_campos" class="campo" style="display: none;">
                    <label for="cantidad_vendida">Cantidad Vendida:</label>
                    <input type="number" step="0.01" id="cantidad_vendida" name="cantidad_vendida">
                </div>

                <div id="tabloide_campos" class="campo" style="display: none;">
                    <label for="cantidad_vendida">Cantidad Vendida:</label>
                    <input type="number" step="0.01" id="cantidad_vendida" name="cantidad_vendida">
                </div>

                <label for="metodo_de_pago">Método de Pago:</label>
                <select id="metodo_de_pago" name="metodo_de_pago" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Dollar">Dollar</option>
                    <option value="Efectivo Bs">Efectivo Bs</option>
                    <option value="Pago Movil">Pago Móvil</option>
                </select>

                <label for="descripcion">Descripción de la venta:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required></textarea>

                <input type="submit" value="Registrar Venta">
            </form>
        </main>
        <footer>
            <p>&copy; 2024 Tienda de Vinil e Impresión. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>
