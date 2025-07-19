<?php
/**
 * HU01 - Solicitar tutoría en línea
 * Sistema de Tutorías Universitarias
 */

session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'estudiante') {
    header('Location: ../login.php');
    exit;
}

require_once '../config/db.php';

$mensaje = '';
$tipo_mensaje = '';

if ($_POST) {
    $curso_id = $_POST['curso_id'] ?? '';
    $descripcion = trim($_POST['descripcion'] ?? '');
    
    if (empty($curso_id) || empty($descripcion)) {
        $mensaje = 'Por favor, complete todos los campos.';
        $tipo_mensaje = 'error';
    } else {
        try {
            $db = getDB();
            $stmt = $db->query(
                "INSERT INTO tutorias (estudiante_id, curso_id, descripcion, estado, fecha_solicitud) VALUES (?, ?, ?, 'pendiente', NOW())",
                [$_SESSION['usuario_id'], $curso_id, $descripcion]
            );
            
            $mensaje = 'Solicitud de tutoría enviada exitosamente. Pronto recibirás confirmación.';
            $tipo_mensaje = 'success';
        } catch (Exception $e) {
            $mensaje = 'Error al procesar la solicitud. Intente nuevamente.';
            $tipo_mensaje = 'error';
            error_log($e->getMessage());
        }
    }
}

// Obtener lista de cursos
$db = getDB();
$stmt = $db->query("SELECT id, codigo, nombre FROM cursos ORDER BY nombre");
$cursos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Tutoría - Sistema de Tutorías</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>Tutorías</h2>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($_SESSION['usuario_nombre'] . ' ' . $_SESSION['usuario_apellido']); ?></span>
                    <small><?php echo ucfirst($_SESSION['usuario_rol']); ?></small>
                </div>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="solicitar_tutoria.php" class="active">Solicitar Tutoría</a></li>
                <li><a href="agendar_sesion.php">Agendar Sesión</a></li>
                <li><a href="ver_historial.php">Ver Historial</a></li>
                <li><a href="../controllers/logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
        
        <main class="main-content">
            <header class="page-header">
                <h1>Solicitar Tutoría</h1>
                <p>Solicita ayuda académica en el curso que necesites</p>
            </header>
            
            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>
            
            <div class="content-section">
                <form id="tutoriaForm" method="POST" class="form-container">
                    <div class="form-group">
                        <label for="curso_id">Curso <span class="required">*</span></label>
                        <select id="curso_id" name="curso_id" required>
                            <option value="">Seleccione un curso</option>
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?php echo $curso['id']; ?>" 
                                        <?php echo (($_POST['curso_id'] ?? '') == $curso['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($curso['codigo'] . ' - ' . $curso['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="form-error" id="cursoError"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion">Descripción de la necesidad <span class="required">*</span></label>
                        <textarea id="descripcion" name="descripcion" rows="5" required 
                                  placeholder="Describe qué temas específicos necesitas reforzar o qué dificultades tienes..."><?php echo htmlspecialchars($_POST['descripcion'] ?? ''); ?></textarea>
                        <span class="form-error" id="descripcionError"></span>
                        <small class="form-help">Mínimo 20 caracteres. Sé específico para recibir mejor ayuda.</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            Enviar Solicitud
                        </button>
                        <a href="../dashboard.php" class="btn btn-secondary">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <script src="../public/js/solicitar_tutoria.js"></script>
</body>
</html>