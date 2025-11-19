<?php
include '../componentes/db.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../paginas/login.php");
    exit();
}

$titulo = $_POST['titulo'];
$contenido = $_POST['contenido'];
$materia_id = $_POST['materia_id'];
$id_usuario = $_SESSION['id'];

$archivo_nombre = null;

// Procesar archivo si existe
if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === 0) {

    $permitidos = ['jpg','jpeg','png','pdf','doc','docx','ppt','pptx'];
    $ext = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));

    if (in_array($ext, $permitidos)) {

        $nuevo_nombre = uniqid() . "." . $ext;
        $ruta = "../uploads/" . $nuevo_nombre;

        if (!is_dir("../uploads")) {
            mkdir("../uploads", 0777, true);
        }

        move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta);

        $archivo_nombre = $nuevo_nombre;
    }
}

// Insertar pregunta
$sql = "INSERT INTO preguntas (titulo, contenido, fecha, id_usuario, materia_id, archivo)
        VALUES (?, ?, NOW(), ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssiis", $titulo, $contenido, $id_usuario, $materia_id, $archivo_nombre);
$stmt->execute();

header("Location: ../vistas/home.php");
exit();
