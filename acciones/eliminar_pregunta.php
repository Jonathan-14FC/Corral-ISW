<?php
include '../componentes/db.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../vistas/login.php");
    exit();
}

$id_usuario = $_SESSION['id'];
$id_pregunta = $_POST['id_pregunta'];

// Verificamos que la pregunta pertenece al usuario antes de eliminarla
$sql = "DELETE FROM preguntas WHERE id = ? AND id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_pregunta, $id_usuario);
$stmt->execute();

$stmt->close();
$conn->close();

// 🔹 Redirige a tu página principal (ajusta según tu estructura)
header("Location: ../vistas/home.php");
exit();
?>
