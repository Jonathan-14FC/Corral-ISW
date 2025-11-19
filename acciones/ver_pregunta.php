<?php
include '../componentes/db.php';
session_start();
if (!isset($_SESSION['nombre']) || !isset($_SESSION['id'])) {
    header("Location: ../vistas/login.php");
    exit();
}

$id_usuario = $_SESSION['id'];

// Validar que venga el ID de la pregunta
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de la pregunta no especificado.");
}

$id = intval($_GET['id']);

// Obtener la pregunta
$stmt = $conn->prepare("SELECT p.titulo, p.contenido, p.fecha, u.nombre, p.id_usuario 
                        FROM preguntas p 
                        JOIN usuarios u ON p.id_usuario=u.id 
                        WHERE p.id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$pregunta = $stmt->get_result()->fetch_assoc();

if (!$pregunta) {
    die("Pregunta no encontrada.");
}

// Obtener respuestas
$stmt = $conn->prepare("SELECT r.contenido, r.fecha, u.nombre 
                        FROM respuestas r 
                        JOIN usuarios u ON r.id_usuario=u.id 
                        WHERE r.id_pregunta=? 
                        ORDER BY r.fecha ASC");
$stmt->bind_param("i", $id);
$stmt->execute();
$respuestas = $stmt->get_result();

// Obtener semestres y materias para el sidebar
$semestres = $conn->query("SELECT * FROM semestres ORDER BY id");
$materias_result = $conn->query("SELECT * FROM materias ORDER BY semestre_id, id");
$materias_array = [];
while($m = $materias_result->fetch_assoc()) {
    $materias_array[$m['semestre_id']][] = $m;
}

// Ajusta la ruta base según tu localhost
$base_url = '/Corralisw'; // Cambia a la carpeta de tu proyecto en htdocs
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($pregunta['titulo']); ?></title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/recursos/style.css?v=<?php echo time(); ?>">
</head>
<body>
<div class="layout">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div>
            <h2>Corral ISW</h2>
            <nav>
                <ul>
                    <li><a href="<?php echo $base_url; ?>/vistas/home.php">Inicio</a></li>
                    <li><a href="<?php echo $base_url; ?>/vistas/perfil.php">Mi Perfil</a></li>
                </ul>
            </nav>

            <h3>Materias</h3>
            <ul>
                <?php foreach ($materias_array as $semestre_id => $materias): ?>
                    <?php foreach ($materias as $m): ?>
                        <li>
                            <a href="<?php echo $base_url; ?>/vistas/materia.php?materia_id=<?= $m['id'] ?>">
                                <?= htmlspecialchars($m['nombre']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>

        <a href="<?php echo $base_url; ?>/acciones/logout.php" class="logout">Cerrar sesión</a>
    </aside>

    <!-- Contenido -->
    <main class="home">
        <a href="<?php echo $base_url; ?>/vistas/home.php">← Volver</a>

        <h1><?php echo htmlspecialchars($pregunta['titulo']); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($pregunta['contenido'])); ?></p>
        <small>Por: <?php echo htmlspecialchars($pregunta['nombre']); ?> | <?php echo $pregunta['fecha']; ?></small>

        <h2>Respuestas</h2>
        <?php if ($respuestas->num_rows > 0): ?>
            <?php while ($r = $respuestas->fetch_assoc()): ?>
                <div class="respuesta">
                    <p><?php echo nl2br(htmlspecialchars($r['contenido'])); ?></p>
                    <small>Por: <?php echo htmlspecialchars($r['nombre']); ?> | <?php echo $r['fecha']; ?></small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No hay respuestas aún.</p>
        <?php endif; ?>

        <h3>Responder</h3>
        <form method="POST" action="<?php echo $base_url; ?>/acciones/responder.php">
            <textarea name="contenido" placeholder="Escribe tu respuesta..." required></textarea>
            <input type="hidden" name="id_pregunta" value="<?php echo $id; ?>">
            <button type="submit">Responder</button>
        </form>
    </main>
</div>
</body>
</html>

