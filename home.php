<?php
include 'db.php';
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT p.id, p.titulo, p.contenido, p.fecha, u.nombre 
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
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
<div class="home">
    <h1>Bienvenido, <?php echo $_SESSION['nombre']; ?> 🐎</h1>
    <a href="logout.php">Cerrar sesión</a>

    <h2>Hacer una nueva pregunta</h2>
    <form method="POST" action="crear_pregunta.php">
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
            echo "<p><a href='ver_pregunta.php?id=" . $row['id'] . "'>Ver respuestas</a></p>";
            echo "</div>";
        }
    } else {
        echo "<p>No hay preguntas aún.</p>";
    }
    ?>
</div>
</body>
</html>

