<?php
$servername = "localhost";
$username = "root"; // cambia si tu usuario es distinto
$password = ""; // cambia si tienes contraseña
$dbname = "potrosdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
