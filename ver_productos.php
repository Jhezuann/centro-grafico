<?php
session_start(); // Iniciar sesión PHP

// Verificar si el usuario no está autenticado, redirigir al inicio de sesión si es así
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

require_once "conn/conne.php";

// Configuración de la paginación
$resultados_por_pagina = 10; // Número de productos por página

// Obtener número total de productos
$sql_total = "SELECT COUNT(*) AS total FROM Productos";
$result_total = $conn->query($sql_total);
$total_registros = $result_total->fetch_assoc()['total'];

// Calcular total de páginas
$total_paginas = ceil($total_registros / $resultados_por_pagina);

// Obtener número de página actual
$pagina_actual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
$inicio = ($pagina_actual - 1) * $resultados_por_pagina;

// Consultas SQL para obtener los productos por tipo
$sql_vinil = "SELECT id, codigo, descripcion, bobina, precio_por_metro, cantidad_disponible, rollos_disponibles, color, cantidad_minima FROM Productos WHERE tipo_producto = 'vinil' ORDER BY codigo LIMIT $inicio, $resultados_por_pagina";
$sql_lamina_acrilico = "SELECT id, codigo, descripcion, precio, cantidad_disponible, color, grosor_lamina_acrilico, cantidad_minima FROM Productos WHERE tipo_producto = 'lamina_acrilico' ORDER BY codigo LIMIT $inicio, $resultados_por_pagina";
$sql_impresion_tabloide = "SELECT id, codigo, descripcion, precio, cantidad_disponible, formato_impresion_tabloide, cantidad_minima FROM Productos WHERE tipo_producto = 'impresion_tabloide' ORDER BY codigo LIMIT $inicio, $resultados_por_pagina";
$sql_lamina_pvc = "SELECT id, codigo, descripcion, cantidad_disponible, precio, color, grosor_lamina_pvc, cantidad_minima FROM Productos WHERE tipo_producto = 'lamina_pvc' ORDER BY codigo LIMIT $inicio, $resultados_por_pagina";

$result_vinil = $conn->query($sql_vinil);
$result_lamina_acrilico = $conn->query($sql_lamina_acrilico);
$result_impresion_tabloide = $conn->query($sql_impresion_tabloide);
$result_lamina_pvc = $conn->query($sql_lamina_pvc);

if (!$result_vinil || !$result_lamina_acrilico || !$result_impresion_tabloide || !$result_lamina_pvc) {
    die("Error al obtener los productos: " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/logo.png">
    <title>Productos - Tienda de Vinil e Impresión</title>
    <link rel="stylesheet" href="css/styles_ver.css">
    <style>
        .pagination {
            margin-top: 20px;
        }
        .pagination a {
            padding: 5px 10px;
            margin: 0 5px;
            border: 1px solid #ccc;
            text-decoration: none;
            color: #000;
        }
        .pagination a.active {
            background-color: #000;
            color: #fff;
        }
        .product-type-header {
            margin-top: 20px;
            font-size: 1.5em;
            font-weight: bold;
        }
    </style>
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
            <h1>Productos</h1>
            <a href="registrar_producto.php" class="button">Registrar Producto</a>

            <!-- Vinil -->
            <div class="product-type-header">Vinil</div>
            <table>
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Bobina</th>
                    <th>Precio por Metro</th>
                    <th>Cantidad Disponible (Metros)</th>
                    <th>Rollos Disponibles</th>
                    <th>Color</th>
                    <th>Cantidad Mínima</th>
                    <th style="width: 24%">Opciones</th>
                </tr>
                <?php
                if ($result_vinil->num_rows > 0) {
                    while($row = $result_vinil->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["codigo"] . "</td>";
                        echo "<td>" . $row["descripcion"] . "</td>";
                        echo "<td>" . $row["bobina"] . "</td>";
                        echo "<td>" . $row["precio_por_metro"] . "</td>";
                        echo "<td>" . $row["cantidad_disponible"] . "</td>";

                        // Calcular cantidad de rollos disponibles
                        $metros_por_rollo = 30;
                        $cantidad_metros = $row["cantidad_disponible"];
                        $rollos_disponibles = floor($cantidad_metros / $metros_por_rollo);

                        echo "<td>" . $rollos_disponibles . "</td>";
                        echo "<td>" . $row["color"] . "</td>";
                        // Verifica si la cantidad mínima está establecida y no es NULL
                        echo "<td>" . $row["cantidad_minima"] . "</td>";

                        echo "<td class='options'>";
                        echo "<a href='editar.php?id=" . $row["id"] . "' class='accion-link'>Editar</a> | ";
                        echo "<a href='agregar_rollos.php?id=" . $row["id"] . "' class='accion-link'>Sumar Rollos</a> | ";
                        echo "<a href='eliminar_rollos.php?id=" . $row["id"] . "' class='accion-link'>Restar Rollo</a> | ";
                        echo "<a href='eliminar.php?id=" . $row["id"] . "' class='accion-link' onclick='return confirmarEliminacion()'>Eliminar</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No hay productos disponibles</td></tr>";
                }
                ?>
            </table>

            <!-- Lámina Acrílico -->
            <div class="product-type-header">Lámina Acrílico</div>
            <table>
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Cantidad Disponible</th>
                    <th>Color</th>
                    <th>Grosor</th>
                    <th>Cantidad Mínima</th>
                    <th>Precio</th>
                    <th style="width: 24%">Opciones</th>
                </tr>
                <?php
                if ($result_lamina_acrilico->num_rows > 0) {
                    while($row = $result_lamina_acrilico->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["codigo"] . "</td>";
                        echo "<td>" . $row["descripcion"] . "</td>";
                        echo "<td>" . $row["cantidad_disponible"] . "</td>";
                        echo "<td>" . $row["color"] . "</td>";
                        echo "<td>" . $row["grosor_lamina_acrilico"] . "</td>";
                        echo "<td>" . $row["cantidad_minima"] . "</td>";
                        echo "<td>" . $row["precio"] . "</td>";

                        echo "<td class='options'>";
                        echo "<a href='editar.php?id=" . $row["id"] . "' class='accion-link'>Editar</a> | ";
                        echo "<a href='eliminar.php?id=" . $row["id"] . "' class='accion-link' onclick='return confirmarEliminacion()'>Eliminar</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No hay productos disponibles</td></tr>";
                }
                ?>
            </table>

            <!-- Impresión Tabloide -->
            <div class="product-type-header">Impresión Tabloide</div>
            <table>
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Cantidad Disponible</th>
                    <th>Formato</th>
                    <th>Cantidad Mínima</th>
                    <th>Precio</th>
                    <th style="width: 24%">Opciones</th>
                </tr>
                <?php
                if ($result_impresion_tabloide->num_rows > 0) {
                    while ($row = $result_impresion_tabloide->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["codigo"] . "</td>";
                        echo "<td>" . $row["descripcion"] . "</td>";
                        echo "<td>" . $row["cantidad_disponible"] . "</td>"; // Muestra la cantidad disponible
                        echo "<td>" . $row["formato_impresion_tabloide"] . "</td>";
                        echo "<td>" . (isset($row["cantidad_minima"]) ? number_format($row["cantidad_minima"], 2) : 'N/A') . "</td>"; // Muestra la cantidad mínima
                        echo "<td>" . $row["precio"] . "</td>";

                        echo "<td class='options'>";
                        echo "<a href='editar.php?id=" . $row["id"] . "' class='accion-link'>Editar</a> | ";
                        echo "<a href='eliminar.php?id=" . $row["id"] . "' class='accion-link' onclick='return confirmarEliminacion()'>Eliminar</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No hay productos disponibles</td></tr>";
                }
                ?>
            </table>

            <!-- Lámina PVC -->
            <div class="product-type-header">Lámina PVC</div>
            <table>
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Cantidad Disponible</th>
                    <th>Color</th>
                    <th>Grosor</th>
                    <th>Cantidad Mínima</th>
                    <th>Precio</th>
                    <th style="width: 24%">Opciones</th>
                </tr>
                <?php
                if ($result_lamina_pvc->num_rows > 0) {
                    while($row = $result_lamina_pvc->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["codigo"] . "</td>";
                        echo "<td>" . $row["descripcion"] . "</td>";
                        echo "<td>" . $row["cantidad_disponible"] . "</td>";
                        echo "<td>" . $row["color"] . "</td>";
                        echo "<td>" . $row["grosor_lamina_pvc"] . "</td>";
                        echo "<td>" . $row["cantidad_minima"] . "</td>";
                        echo "<td>" . $row["precio"] . "</td>";

                        echo "<td class='options'>";
                        echo "<a href='editar.php?id=" . $row["id"] . "' class='accion-link'>Editar</a> | ";
                        echo "<a href='eliminar.php?id=" . $row["id"] . "' class='accion-link' onclick='return confirmarEliminacion()'>Eliminar</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No hay productos disponibles</td></tr>";
                }
                ?>
            </table>

        </main>
        <footer>
            <p>&copy; 2024 Tienda de Vinil e Impresión. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>
