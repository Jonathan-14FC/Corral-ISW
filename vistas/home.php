<?php
include '../componentes/db.php';
session_start();

// Verificar sesión
if (!isset($_SESSION['nombre']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id'];

// Obtener preguntas
$sql = "SELECT p.id, p.titulo, p.contenido, p.fecha, u.nombre, p.id_usuario, p.archivo,
               m.nombre AS materia, s.nombre AS semestre
        FROM preguntas p
        JOIN usuarios u ON p.id_usuario = u.id
        JOIN materias m ON p.materia_id = m.id
        JOIN semestres s ON m.semestre_id = s.id
        ORDER BY p.fecha DESC";
$result = $conn->query($sql);

// Obtener semestres
$semestres = $conn->query("SELECT * FROM semestres ORDER BY id");

// Obtener materias agrupadas por semestre
$materias_result = $conn->query("SELECT * FROM materias ORDER BY semestre_id, id");
$materias_array = [];
while($m = $materias_result->fetch_assoc()) {
    $materias_array[$m['semestre_id']][] = $m;
}
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

    <!-- Sidebar -->
    <aside class="sidebar">
        <div>
            <h2>Corral ISW</h2>
            <nav>
                <ul>
                    <li><a href="home.php">Inicio</a></li>
                    <li><a href="perfil.php">Mi Perfil</a></li>
                </ul>
            </nav>

            <h3>Materias</h3>
            <ul>
                <?php foreach ($materias_array as $semestre_id => $materias): ?>
                    <?php foreach ($materias as $m): ?>
                        <li>
                            <a href="materia.php?materia_id=<?= $m['id'] ?>">
                                <?= htmlspecialchars($m['nombre']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>

        <a href="../acciones/logout.php" class="logout">Cerrar sesión</a>
    </aside>

    <!-- Contenido -->
    <main class="home">
        <h2>Hacer una nueva pregunta</h2>

        <form method="POST" action="../acciones/crear_pregunta.php" enctype="multipart/form-data">
            <input type="text" name="titulo" placeholder="Título de la pregunta" required>
            <textarea name="contenido" placeholder="Escribe tu pregunta..." required></textarea>

            <select name="semestre" id="semestre" required>
                <option value="">Selecciona un semestre</option>
                <?php while($s = $semestres->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>"><?= $s['nombre'] ?></option>
                <?php endwhile; ?>
            </select>

            <select name="materia_id" id="materia" required>
                <option value="">Selecciona una materia</option>
            </select>

            <label>Adjuntar archivo (imagen o documento):</label>
            <input type="file" name="archivo" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.ppt,.pptx">

            <button type="submit">Publicar pregunta</button>
        </form>

        <h2>Preguntas recientes</h2>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='pregunta'>";
                echo "<h3>" . htmlspecialchars($row['titulo']) . "</h3>";
                echo "<p>" . nl2br(htmlspecialchars($row['contenido'])) . "</p>";
                echo "<p><strong>Materia:</strong> " . htmlspecialchars($row['materia']) . 
                     " | <strong>Semestre:</strong> " . htmlspecialchars($row['semestre']) . "</p>";

                if (!empty($row['archivo'])) {
                    echo "<p><a href='../uploads/" . htmlspecialchars($row['archivo']) . "' target='_blank'>
                            📎 Ver archivo adjunto
                          </a></p>";
                }

                echo "<small>Por: " . htmlspecialchars($row['nombre']) . " | " . $row['fecha'] . "</small>";
                
                // Enlace correcto a ver_pregunta.php en acciones
                echo "<p><a href='../acciones/ver_pregunta.php?id=" . $row['id'] . "'>Ver respuestas</a></p>";

                if ($row['id_usuario'] == $id_usuario) {
                    echo "<form method='POST' action='../acciones/eliminar_pregunta.php' 
                          onsubmit='return confirm(\"¿Seguro que deseas eliminar esta pregunta?\");'>";
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

<script>
const materias = <?php echo json_encode($materias_array); ?>;
const semestreSelect = document.getElementById('semestre');
const materiaSelect = document.getElementById('materia');

semestreSelect.addEventListener('change', () => {
    const semestre_id = semestreSelect.value;
    materiaSelect.innerHTML = '<option value="">Selecciona una materia</option>';
    if(materias[semestre_id]) {
        materias[semestre_id].forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.id;
            opt.textContent = m.nombre;
            materiaSelect.appendChild(opt);
        });
    }
});
</script>

</body>
</html>
