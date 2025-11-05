<?php
include '../componentes/db.php';
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../vistas/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pregunta = intval($_POST['id_pregunta']);
    $contenido = $_POST['contenido'];

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE nombre=?");
    $stmt->bind_param("s", $_SESSION['nombre']);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    $id_usuario = $user['id'];

    $stmt = $conn->prepare("INSERT INTO respuestas (id_pregunta, id_usuario, contenido) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $id_pregunta, $id_usuario, $contenido);
    $stmt->execute();

    header("Location: ver_pregunta.php?id=" . $id_pregunta);
    exit();
}
?>
