<?php
include '../componentes/db.php';
session_start();

if (!isset($_SESSION['nombre'])) {
    header("Location: ../paginas/login.php");
    exit();
}

$nombre = $_SESSION['nombre'];

// Verifica si se subió la imagen
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $nombreArchivo = time() . "_" . basename($_FILES['foto']['name']);
    $rutaDestino = "../uploads/" . $nombreArchivo;

    // Crea el directorio si no existe
    if (!is_dir("../uploads")) {
        mkdir("../uploads", 0777, true);
    }

    // Mueve el archivo al servidor
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
        // Actualiza en la base de datos
        $stmt = $conn->prepare("UPDATE usuarios SET foto_perfil = ? WHERE nombre = ?");
        $stmt->bind_param("ss", $nombreArchivo, $nombre);
        $stmt->execute();

        // ✅ Redirige de forma segura y recarga el perfil
        echo "<script>
            alert('Foto actualizada correctamente');
            window.location.href = '../paginas/perfil.php';
        </script>";
        exit();
    } else {
        echo "<script>
            alert('Error al mover la imagen al servidor');
            window.history.back();
        </script>";
    }
} else {
    echo "<script>
        alert('No se seleccionó ningún archivo o hubo un error en la subida.');
        window.history.back();
    </script>";
}
