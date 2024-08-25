<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "centro_grafico";

$conn = new mysqli($servername, $username, $password, $dbname);

if (!$conn) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}
?>