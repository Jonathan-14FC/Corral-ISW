<?php
include 'db.php';
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: login.php");
    exit();
}

$id = intval($_GET['id']);

// Obtener la pregunta
$stmt = $conn->prepare("SELECT p.titulo, p.contenido, p.fecha, u.nombre FROM preguntas p JOIN usuarios u ON p.id_usuario=u.id WHERE p.id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$pregunta = $stmt->get_result()->fetch_assoc();

// Obtener respuestas
$stmt = $conn->prepare("SELECT r.contenido, r.fecha, u.nombre FROM respuestas r JOIN usuarios u ON r.id_usuario=u.id WHERE r.id_pregunta=? ORDER BY r.fecha ASC");
$stmt->bind_param("i", $id);
$stmt->execute();
$respuestas = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($pregunta['titulo']); ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <a href="home.php">← Volver</a>
  <h1><?php echo htmlspecialchars($pregunta['titulo']); ?></h1>
  <p><?php echo nl2br(htmlspecialchars($pregunta['contenido'])); ?></p>
  <small>Por: <?php echo htmlspecialchars($pregunta['nombre']); ?> | <?php echo $pregunta['fecha']; ?></small>

  <h2>Respuestas</h2>
  <?php
  while ($r = $respuestas->fetch_assoc()) {
      echo "<div class='respuesta'>";
      echo "<p>" . nl2br(htmlspecialchars($r['contenido'])) . "</p>";
      echo "<small>Por: " . htmlspecialchars($r['nombre']) . " | " . $r['fecha'] . "</small>";
      echo "</div>";
  }
  ?>

  <h3>Responder</h3>
  <form method="POST" action="responder.php">
      <textarea name="contenido" placeholder="Escribe tu respuesta..." required></textarea>
      <input type="hidden" name="id_pregunta" value="<?php echo $id; ?>">
      <button type="submit">Responder</button>
  </form>
</body>
</html>
