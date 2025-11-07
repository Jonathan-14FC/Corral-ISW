<?php
include '../componentes/db.php';
session_start();

if (!isset($_SESSION['nombre'])) {
    header("Location: login.php");
    exit();
}

// Obtener datos del usuario logueado
$nombre = $_SESSION['nombre'];
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE nombre = ?");
$stmt->bind_param("s", $nombre);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - Foro ISW</title>
    <link rel="stylesheet" href="../recursos/style.css?v=<?php echo time(); ?>">
</head>
<body>
<div class="layout">

    <!-- 🔹 Barra lateral -->
    <aside class="sidebar">
        <div>
            <h2>Foro ISW</h2>
            <nav>
                <ul>
                    <li><a href="home.php">Inicio</a></li>
                    <li><a href="perfil.php">Mi Perfil</a></li>
                </ul>
            </nav>
        </div>
        <a href="../acciones/logout.php" class="logout">Cerrar sesión</a>
    </aside>

    <!-- 🔹 Contenido principal -->
    <main class="perfil">
        <h2>Mi Perfil</h2>

        <div class="perfil-card">
            <div class="perfil-foto">
                <img src="<?php echo $user['foto_perfil'] ? '../uploads/' . htmlspecialchars($user['foto_perfil']) : '../uploads/default.png'; ?>" alt="Foto de perfil">
            </div>
            <div class="perfil-info">
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($user['nombre']); ?></p>
                <p><strong>Correo:</strong> <?php echo htmlspecialchars($user['correo']); ?></p>
            </div>
        </div>

        <h3>Actualizar foto de perfil</h3>
        <form action="../acciones/subir_foto.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="foto" accept="image/*" required>
            <button type="submit">Subir nueva foto</button>
        </form>
    </main>
</div>
</body>
</html>
