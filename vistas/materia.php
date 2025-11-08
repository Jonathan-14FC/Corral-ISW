<?php
include '../componentes/db.php';
session_start();

// 🔒 Verificar sesión
if (!isset($_SESSION['nombre']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id'];

// 🔹 Obtener materia y semestre de la DB
if (!isset($_GET['materia_id'])) {
    header("Location: home.php");
    exit();
}

$materia_id = $_GET['materia_id'];

// Obtener datos de la materia y semestre
$stmt = $conn->prepare("SELECT m.nombre AS materia, s.nombre AS semestre 
                        FROM materias m
                        JOIN semestres s ON m.semestre_id = s.id
                        WHERE m.id = ?");
$stmt->bind_param("i", $materia_id);
$stmt->execute();
$res = $stmt->get_result();
$materia_info = $res->fetch_assoc();

// 🔹 Obtener preguntas de esta materia
$stmt = $conn->prepare("SELECT p.id, p.titulo, p.contenido, p.fecha, u.nombre, p.id_usuario
                        FROM preguntas p
                        JOIN usuarios u ON p.id_usuario = u.id
                        WHERE p.materia_id = ?
                        ORDER BY p.fecha DESC");
$stmt->bind_param("i", $materia_id);
$stmt->execute();
$preguntas = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Preguntas de <?= htmlspecialchars($materia_info['materia']) ?></title>
    <link rel="stylesheet" href="../recursos/style.css?v=<?php echo time(); ?>">
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div>
            <h2>Corral ISW</h2>
            <nav>
                <ul>
                    <li><a href="home.php">Inicio</a></li>
                    <li><a href="perfil.php">Mi Perfil</a></li>
                </ul>
            </nav>
        </div>
        <a href="../acciones/logout.php" class="logout">Cerrar sesión</a>
    </aside>

    <main class="home">
        <h2>Preguntas de <?= htmlspecialchars($materia_info['materia']) ?> (<?= htmlspecialchars($materia_info['semestre']) ?>)</h2>

        <?php
        if ($preguntas->num_rows > 0) {
            while ($row = $preguntas->fetch_assoc()) {
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
            echo "<p>No hay preguntas aún para esta materia.</p>";
        }
        ?>
    </main>
</div>
</body>
</html>
