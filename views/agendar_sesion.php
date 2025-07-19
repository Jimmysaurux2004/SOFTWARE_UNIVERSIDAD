<?php
/**
 * HU02 - Agendar sesión con docente
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
    $disponibilidad_id = $_POST['disponibilidad_id'] ?? '';
    $tutoria_id = $_POST['tutoria_id'] ?? '';
    
    if (empty($disponibilidad_id) || empty($tutoria_id)) {
        $mensaje = 'Por favor, seleccione una tutoría y un horario disponible.';
        $tipo_mensaje = 'error';
    } else {
        try {
            $db = getDB();
            
            // Verificar que la disponibilidad esté libre
            $stmt = $db->query(
                "SELECT d.*, u.nombre, u.apellido FROM disponibilidad_docentes d 
                 JOIN usuarios u ON d.docente_id = u.id 
                 WHERE d.id = ? AND d.disponible = TRUE",
                [$disponibilidad_id]
            );
            $disponibilidad = $stmt->fetch();
            
            if (!$disponibilidad) {
                $mensaje = 'El horario seleccionado ya no está disponible.';
                $tipo_mensaje = 'error';
            } else {
                // Marcar disponibilidad como ocupada y actualizar tutoría
                $fecha_sesion = $disponibilidad['fecha'] . ' ' . $disponibilidad['hora_inicio'];
                
                $db->getConnection()->beginTransaction();
                
                // Actualizar disponibilidad
                $db->query(
                    "UPDATE disponibilidad_docentes SET disponible = FALSE WHERE id = ?",
                    [$disponibilidad_id]
                );
                
                // Actualizar tutoría
                $db->query(
                    "UPDATE tutorias SET estado = 'confirmada', fecha_asignada = ?, docente_id = ? WHERE id = ? AND estudiante_id = ?",
                    [$fecha_sesion, $disponibilidad['docente_id'], $tutoria_id, $_SESSION['usuario_id']]
                );
                
                $db->getConnection()->commit();
                
                $mensaje = 'Sesión agendada exitosamente con ' . $disponibilidad['nombre'] . ' ' . $disponibilidad['apellido'] . ' para el ' . date('d/m/Y H:i', strtotime($fecha_sesion));
                $tipo_mensaje = 'success';
            }
        } catch (Exception $e) {
            $db->getConnection()->rollBack();
            $mensaje = 'Error al agendar la sesión. Intente nuevamente.';
            $tipo_mensaje = 'error';
            error_log($e->getMessage());
        }
    }
}

// Obtener tutorías pendientes del estudiante
$db = getDB();
$stmt = $db->query(
    "SELECT t.id, c.codigo, c.nombre as curso_nombre, t.descripcion 
     FROM tutorias t 
     JOIN cursos c ON t.curso_id = c.id 
     WHERE t.estudiante_id = ? AND t.estado = 'pendiente' 
     ORDER BY t.fecha_solicitud DESC",
    [$_SESSION['usuario_id']]
);
$tutorias_pendientes = $stmt->fetchAll();

// Obtener disponibilidad de docentes
$stmt = $db->query(
    "SELECT d.id, d.fecha, d.hora_inicio, d.hora_fin, u.nombre, u.apellido 
     FROM disponibilidad_docentes d 
     JOIN usuarios u ON d.docente_id = u.id 
     WHERE d.disponible = TRUE AND d.fecha >= CURDATE() 
     ORDER BY d.fecha, d.hora_inicio"
);
$disponibilidades = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Sesión - Sistema de Tutorías</title>
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
                <li><a href="solicitar_tutoria.php">Solicitar Tutoría</a></li>
                <li><a href="agendar_sesion.php" class="active">Agendar Sesión</a></li>
                <li><a href="ver_historial.php">Ver Historial</a></li>
                <li><a href="../controllers/logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
        
        <main class="main-content">
            <header class="page-header">
                <h1>Agendar Sesión</h1>
                <p>Programa una sesión con un docente disponible</p>
            </header>
            
            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>
            
            <?php if (empty($tutorias_pendientes)): ?>
                <div class="alert alert-info">
                    No tienes tutorías pendientes. <a href="solicitar_tutoria.php">Solicita una tutoría</a> primero.
                </div>
            <?php else: ?>
                <div class="content-section">
                    <form id="agendarForm" method="POST" class="form-container">
                        <div class="form-group">
                            <label for="tutoria_id">Selecciona la tutoría a agendar <span class="required">*</span></label>
                            <select id="tutoria_id" name="tutoria_id" required>
                                <option value="">Seleccione una tutoría</option>
                                <?php foreach ($tutorias_pendientes as $tutoria): ?>
                                    <option value="<?php echo $tutoria['id']; ?>">
                                        <?php echo htmlspecialchars($tutoria['codigo'] . ' - ' . $tutoria['curso_nombre']); ?>
                                        (<?php echo htmlspecialchars(substr($tutoria['descripcion'], 0, 50) . '...'); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="form-error" id="tutoriaError"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="disponibilidad_id">Horarios disponibles <span class="required">*</span></label>
                            <div class="availability-grid">
                                <?php if (empty($disponibilidades)): ?>
                                    <p class="no-availability">No hay horarios disponibles en este momento.</p>
                                <?php else: ?>
                                    <?php foreach ($disponibilidades as $disp): ?>
                                        <label class="availability-option">
                                            <input type="radio" name="disponibilidad_id" value="<?php echo $disp['id']; ?>" required>
                                            <div class="availability-card">
                                                <div class="availability-date">
                                                    <?php echo date('d/m/Y', strtotime($disp['fecha'])); ?>
                                                </div>
                                                <div class="availability-time">
                                                    <?php echo date('H:i', strtotime($disp['hora_inicio'])); ?> - 
                                                    <?php echo date('H:i', strtotime($disp['hora_fin'])); ?>
                                                </div>
                                                <div class="availability-teacher">
                                                    Prof. <?php echo htmlspecialchars($disp['nombre'] . ' ' . $disp['apellido']); ?>
                                                </div>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <span class="form-error" id="disponibilidadError"></span>
                        </div>
                        
                        <?php if (!empty($disponibilidades)): ?>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    Confirmar Agendamiento
                                </button>
                                <a href="../dashboard.php" class="btn btn-secondary">
                                    Cancelar
                                </a>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <script src="../public/js/agendar_sesion.js"></script>
</body>
</html>