<?php 
include '../componentes/db.php';
session_start();

// 🔒 Verificar sesión
if (!isset($_SESSION['nombre'])) {
    header("Location: ../vistas/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $contenido = $_POST['contenido'];
    $materia_id = $_POST['materia_id']; // ← Nuevo campo

    // Obtener ID del usuario logueado
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE nombre=?");
    $stmt->bind_param("s", $_SESSION['nombre']);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    $id_usuario = $user['id'];

    // Insertar pregunta con materia_id
    $stmt = $conn->prepare("INSERT INTO preguntas (id_usuario, titulo, contenido, materia_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $id_usuario, $titulo, $contenido, $materia_id);
    $stmt->execute();

    header("Location: ../vistas/home.php");
    exit();
}
?>
