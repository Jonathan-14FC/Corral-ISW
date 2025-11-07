<?php
include '../componentes/db.php';
session_start();

if (!isset($_SESSION['nombre']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id'];

// Consulta de preguntas con nombre del autor
$sql = "SELECT p.id, p.titulo, p.contenido, p.fecha, u.nombre, p.id_usuario 
        FROM preguntas p
        JOIN usuarios u ON p.id_usuario = u.id
        ORDER BY p.fecha DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Foro ISW ITSON</title>
    <link rel="stylesheet" href="../recursos/style.css?v=<?php echo time(); ?>">
</head>
<body>
<div class="layout">

    <!-- 🔹 Barra latera donde agregaré las clases de todos los semestres y sus apartados -->
    <aside class="sidebar">
    <div>
        <h2>Corral ISW</h2>
        <nav>
            <ul>
                <li><a href="home.php">Inicio</a></li> <!-- Boton para cerrar sesion -->
                <li><a href="perfil.php">Mi Perfil</a></li> <!-- Boton para acceder al perfil -->
            </ul>
        </nav>
    </div>
    <a href="../acciones/logout.php" class="logout">Cerrar sesión</a>
</aside>


    <!-- 🔹 Contenido principal -->
    <main class="home">
        <h2>Hacer una nueva pregunta</h2>
        <form method="POST" action="../acciones/crear_pregunta.php">
            <input type="text" name="titulo" placeholder="Título de la pregunta" required>
            <textarea name="contenido" placeholder="Escribe tu pregunta..." required></textarea>
            <button type="submit">Publicar pregunta</button>
        </form>

        <h2>Preguntas recientes</h2>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='pregunta'>";
                echo "<h3>" . htmlspecialchars($row['titulo']) . "</h3>";
                echo "<p>" . nl2br(htmlspecialchars($row['contenido'])) . "</p>";
                echo "<small>Por: " . htmlspecialchars($row['nombre']) . " | " . $row['fecha'] . "</small>";
                echo "<p><a href='../acciones/ver_pregunta.php?id=" . $row['id'] . "'>Ver respuestas</a></p>";

                if ($row['id_usuario'] == $id_usuario) {
                    echo "<form method='POST' action='../acciones/eliminar_pregunta.php' onsubmit='return confirm(\"¿Seguro que deseas eliminar esta pregunta?\");'>";
                    echo "<input type='hidden' name='id_pregunta' value='" . $row['id'] . "'>";
                    echo "<button type='submit' class='btn-eliminar'>Eliminar</button>";
                    echo "</form>";
                }

                echo "</div>";
            }
        } else {
            echo "<p>No hay preguntas aún.</p>";
        }
        ?>
    </main>
</div>
</body>
</html>
