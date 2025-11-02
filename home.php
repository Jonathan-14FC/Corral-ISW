<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Bienvenido - Potros ITSON</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="home">
    <h1>Bienvenido, <?php echo $_SESSION['nombre']; ?> 🐎</h1>
    <p>Has iniciado sesión correctamente en Potros ITSON.</p>
    <a href="logout.php">Cerrar sesión</a>
  </div>
</body>
</html>
