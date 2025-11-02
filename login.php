<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE correo='$correo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['nombre'] = $user['nombre'];
            header("Location: home.php");
            exit();
        } else {
            $error = "Contraseña incorrecta";
        }
    } else {
        $error = "Correo no registrado";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login - Potros ITSON</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <form class="form" method="POST">
      <h2>Iniciar Sesión</h2>
      <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
      <input type="email" name="correo" placeholder="Correo electrónico" required>
      <input type="password" name="password" placeholder="Contraseña" required>
      <button type="submit">Entrar</button>
      <p>¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
    </form>
  </div>
</body>
</html>
