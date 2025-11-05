<?php 
include '../componentes/db.php';
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../vistas/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $contenido = $_POST['contenido'];

    // Obtener ID del usuario logueado
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE nombre=?");
    $stmt->bind_param("s", $_SESSION['nombre']);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    $id_usuario = $user['id'];

    // Insertar pregunta
    $stmt = $conn->prepare("INSERT INTO preguntas (id_usuario, titulo, contenido) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $id_usuario, $titulo, $contenido);
    $stmt->execute();

    header("Location: home.php");
    exit();
}
?>
