<?php
session_start(); // Iniciar sesión PHP

// Verificar si el usuario no está autenticado, redirigir al inicio de sesión si es así
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

require_once "conn/conne.php";

// Obtener el ID del producto a editar
$id = isset($_GET['id']) ? $_GET['id'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $tipo_producto = $_POST['tipo_producto'];
    $codigo = $_POST['codigo'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio']; // General para todos los tipos
    $cantidad_disponible = $_POST['cantidad_disponible'];
    $color = $_POST['color'];
    $grosor_lamina_acrilico = isset($_POST['grosor_lamina_acrilico']) ? $_POST['grosor_lamina_acrilico'] : null;
    $grosor_lamina_pvc = isset($_POST['grosor_lamina_pvc']) ? $_POST['grosor_lamina_pvc'] : null;
    $formato_impresion_tabloide = isset($_POST['formato_impresion_tabloide']) ? $_POST['formato_impresion_tabloide'] : null;
    $cantidad_minima = $_POST['cantidad_minima'];

    // Construir consulta SQL en función del tipo de producto
    if ($tipo_producto == 'vinil') {
        $sql = "UPDATE Productos SET 
                    codigo='$codigo', 
                    descripcion='$descripcion', 
                    precio_por_metro='$precio', 
                    cantidad_disponible='$cantidad_disponible', 
                    color='$color', 
                    cantidad_minima='$cantidad_minima' 
                WHERE id='$id'";
    } elseif ($tipo_producto == 'lamina_acrilico') {
        $sql = "UPDATE Productos SET 
                    codigo='$codigo', 
                    descripcion='$descripcion', 
                    precio='$precio', 
                    cantidad_disponible='$cantidad_disponible', 
                    color='$color', 
                    grosor_lamina_acrilico='$grosor_lamina_acrilico', 
                    cantidad_minima='$cantidad_minima' 
                WHERE id='$id'";
    } elseif ($tipo_producto == 'lamina_pvc') {
        $sql = "UPDATE Productos SET 
                    codigo='$codigo', 
                    descripcion='$descripcion', 
                    precio='$precio', 
                    cantidad_disponible='$cantidad_disponible', 
                    color='$color', 
                    grosor_lamina_pvc='$grosor_lamina_pvc', 
                    cantidad_minima='$cantidad_minima' 
                WHERE id='$id'";
    } elseif ($tipo_producto == 'impresion_tabloide') {
        $sql = "UPDATE Productos SET 
                    codigo='$codigo', 
                    descripcion='$descripcion', 
                    precio='$precio', 
                    cantidad_disponible='$cantidad_disponible', 
                    formato_impresion_tabloide='$formato_impresion_tabloide', 
                    cantidad_minima='$cantidad_minima' 
                WHERE id='$id'";
    }

    if ($conn->query($sql) === TRUE) {
        echo '<script>alert("Producto actualizado exitosamente"); window.location.href = "ver_productos.php";</script>';
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
} else {
    $sql = "SELECT * FROM Productos WHERE id='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $producto = $result->fetch_assoc();
    } else {
        echo "Producto no encontrado";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/logo.png">
    <title>Editar Producto - Tienda de Vinil e Impresión</title>
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
            <h1 style="margin-left: 15%;">Editar Producto</h1>
            <form method="post" class="form">
                <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                <label for="tipo_producto">Tipo de Producto:</label>
                <select id="tipo_producto" name="tipo_producto" onchange="mostrarCampos()" required>
                    <option value="vinil" <?php if($producto['tipo_producto'] == 'vinil') echo 'selected'; ?>>Vinil</option>
                    <option value="lamina_acrilico" <?php if($producto['tipo_producto'] == 'lamina_acrilico') echo 'selected'; ?>>Lámina de Acrílico</option>
                    <option value="lamina_pvc" <?php if($producto['tipo_producto'] == 'lamina_pvc') echo 'selected'; ?>>Lámina de PVC</option>
                    <option value="impresion_tabloide" <?php if($producto['tipo_producto'] == 'impresion_tabloide') echo 'selected'; ?>>Impresión en Tabloide</option>
                </select>

                <div id="vinil_campos" class="campo" style="display: none;">
                    <label for="codigo">Código:</label>
                    <input type="text" id="codigo" name="codigo" value="<?php echo $producto['codigo']; ?>" required>

                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" required><?php echo $producto['descripcion']; ?></textarea>

                    <label for="precio_por_metro">Precio por Metro:</label>
                    <input type="number" step="0.01" id="precio_por_metro" name="precio_por_metro" value="<?php echo $producto['precio_por_metro']; ?>" required>

                    <label for="color">Color:</label>
                    <input type="text" id="color" name="color" value="<?php echo $producto['color']; ?>" required>

                    <label for="cantidad_minima">Cantidad Mínima:</label>
                    <input type="number" step="0.01" id="cantidad_minima" name="cantidad_minima" value="<?php echo $producto['cantidad_minima']; ?>" required>
                </div>

                <div id="acrilico_campos" class="campo" style="display: none;">
                    <label for="codigo_acrilico">Código:</label>
                    <input type="text" id="codigo_acrilico" name="codigo_acrilico" value="<?php echo $producto['codigo']; ?>" required>

                    <label for="descripcion_acrilico">Descripción:</label>
                    <textarea id="descripcion_acrilico" name="descripcion_acrilico" required><?php echo $producto['descripcion']; ?></textarea>

                    <label for="color_acrilico">Color:</label>
                    <input type="text" id="color_acrilico" name="color_acrilico" value="<?php echo $producto['color']; ?>" required>

                    <label for="grosor_lamina_acrilico">Grosor de Lámina (mm):</label>
                    <select id="grosor_lamina_acrilico" name="grosor_lamina_acrilico">
                        <option value="3" <?php if($producto['grosor_lamina_acrilico'] == '3') echo 'selected'; ?>>3</option>
                        <option value="5" <?php if($producto['grosor_lamina_acrilico'] == '5') echo 'selected'; ?>>5</option>
                    </select>

                    <label for="precio_acrilico">Precio:</label>
                    <input type="number" step="0.01" id="precio_acrilico" name="precio" value="<?php echo $producto['precio']; ?>" required>

                    <label for="cantidad_disponible_acrilico">Cantidad Disponible:</label>
                    <input type="number" step="0.01" id="cantidad_disponible_acrilico" name="cantidad_disponible" value="<?php echo $producto['cantidad_disponible']; ?>" required>

                    <label for="cantidad_minima_acrilico">Cantidad Mínima:</label>
                    <input type="number" step="0.01" id="cantidad_minima_acrilico" name="cantidad_minima" value="<?php echo $producto['cantidad_minima']; ?>" required>
                </div>

                <div id="pvc_campos" class="campo" style="display: none;">
                    <label for="codigo_pvc">Código:</label>
                    <input type="text" id="codigo_pvc" name="codigo_pvc" value="<?php echo $producto['codigo']; ?>" required>

                    <label for="descripcion_pvc">Descripción:</label>
                    <textarea id="descripcion_pvc" name="descripcion_pvc" required><?php echo $producto['descripcion']; ?></textarea>

                    <label for="color_pvc">Color:</label>
                    <input type="text" id="color_pvc" name="color_pvc" value="<?php echo $producto['color']; ?>" required>

                    <label for="grosor_lamina_pvc">Grosor de Lámina (mm):</label>
                    <select id="grosor_lamina_pvc" name="grosor_lamina_pvc">
                        <option value="3" <?php if($producto['grosor_lamina_pvc'] == '3') echo 'selected'; ?>>3</option>
                        <option value="5" <?php if($producto['grosor_lamina_pvc'] == '5') echo 'selected'; ?>>5</option>
                    </select>

                    <label for="precio_pvc">Precio:</label>
                    <input type="number" step="0.01" id="precio_pvc" name="precio" value="<?php echo $producto['precio']; ?>" required>

                    <label for="cantidad_disponible_pvc">Cantidad Disponible:</label>
                    <input type="number" step="0.01" id="cantidad_disponible_pvc" name="cantidad_disponible" value="<?php echo $producto['cantidad_disponible']; ?>" required>

                    <label for="cantidad_minima_pvc">Cantidad Mínima:</label>
                    <input type="number" step="0.01" id="cantidad_minima_pvc" name="cantidad_minima" value="<?php echo $producto['cantidad_minima']; ?>" required>
                </div>

                <div id="tabloide_campos" class="campo" style="display: none;">
                    <label for="codigo_tabloide">Código:</label>
                    <input type="text" id="codigo_tabloide" name="codigo_tabloide" value="<?php echo $producto['codigo']; ?>" required>

                    <label for="descripcion_tabloide">Descripción:</label>
                    <textarea id="descripcion_tabloide" name="descripcion_tabloide" required><?php echo $producto['descripcion']; ?></textarea>

                    <label for="formato_impresion_tabloide">Formato de Impresión:</label>
                    <input type="text" id="formato_impresion_tabloide" name="formato_impresion_tabloide" value="<?php echo $producto['formato_impresion_tabloide']; ?>" required>

                    <label for="precio_tabloide">Precio:</label>
                    <input type="number" step="0.01" id="precio_tabloide" name="precio" value="<?php echo $producto['precio']; ?>" required>

                    <label for="cantidad_disponible_tabloide">Cantidad Disponible:</label>
                    <input type="number" step="0.01" id="cantidad_disponible_tabloide" name="cantidad_disponible" value="<?php echo $producto['cantidad_disponible']; ?>" required>

                    <label for="cantidad_minima_tabloide">Cantidad Mínima:</label>
                    <input type="number" step="0.01" id="cantidad_minima_tabloide" name="cantidad_minima" value="<?php echo $producto['cantidad_minima']; ?>" required>
                </div>

                <button type="submit">Actualizar Producto</button>
            </form>
        </main>
    </div>
</body>
</html>
