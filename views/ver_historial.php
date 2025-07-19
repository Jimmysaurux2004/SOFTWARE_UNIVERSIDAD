<?php
/**
 * HU03 - Ver historial académico y de tutorías
 * Sistema de Tutorías Universitarias
 */

session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'estudiante') {
    header('Location: ../login.php');
    exit;
}

require_once '../config/db.php';

$db = getDB();
$usuario_id = $_SESSION['usuario_id'];

// Obtener historial académico
$stmt = $db->query(
    "SELECT h.*, c.codigo, c.nombre as curso_nombre, c.semestre 
     FROM historial_academico h 
     JOIN cursos c ON h.curso_id = c.id 
     WHERE h.estudiante_id = ? 
     ORDER BY c.semestre DESC, c.codigo",
    [$usuario_id]
);
$historial_academico = $stmt->fetchAll();

// Obtener historial de tutorías
$stmt = $db->query(
    "SELECT t.*, c.codigo, c.nombre as curso_nombre, 
            u.nombre as docente_nombre, u.apellido as docente_apellido 
     FROM tutorias t 
     JOIN cursos c ON t.curso_id = c.id 
     LEFT JOIN usuarios u ON t.docente_id = u.id 
     WHERE t.estudiante_id = ? 
     ORDER BY t.fecha_solicitud DESC",
    [$usuario_id]
);
$historial_tutorias = $stmt->fetchAll();

// Identificar cursos llevados más de una vez
$cursos_repetidos = array_filter($historial_academico, function($item) {
    return $item['veces_llevado'] > 1;
});
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Historial - Sistema de Tutorías</title>
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
                <li><a href="agendar_sesion.php">Agendar Sesión</a></li>
                <li><a href="ver_historial.php" class="active">Ver Historial</a></li>
                <li><a href="../controllers/logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
        
        <main class="main-content">
            <header class="page-header">
                <h1>Historial Académico y Tutorías</h1>
                <p>Consulta tu progreso académico y sesiones de tutoría</p>
            </header>
            
            <!-- Cursos Repetidos -->
            <?php if (!empty($cursos_repetidos)): ?>
                <div class="content-section">
                    <h2>⚠️ Cursos Llevados Más de Una Vez</h2>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Curso</th>
                                    <th>Semestre</th>
                                    <th>Veces Llevado</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cursos_repetidos as $curso): ?>
                                    <tr class="warning-row">
                                        <td><?php echo htmlspecialchars($curso['codigo']); ?></td>
                                        <td><?php echo htmlspecialchars($curso['curso_nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($curso['semestre']); ?></td>
                                        <td><?php echo $curso['veces_llevado']; ?></td>
                                        <td>
                                            <span class="status status-<?php echo $curso['aprobado'] ? 'approved' : 'failed'; ?>">
                                                <?php echo $curso['aprobado'] ? 'Aprobado' : 'No Aprobado'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Historial Académico Completo -->
            <div class="content-section">
                <h2>Historial Académico Completo</h2>
                <?php if (empty($historial_academico)): ?>
                    <div class="alert alert-info">
                        No hay registros académicos disponibles.
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Curso</th>
                                    <th>Semestre</th>
                                    <th>Veces Llevado</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historial_academico as $curso): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($curso['codigo']); ?></td>
                                        <td><?php echo htmlspecialchars($curso['curso_nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($curso['semestre']); ?></td>
                                        <td><?php echo $curso['veces_llevado']; ?></td>
                                        <td>
                                            <span class="status status-<?php echo $curso['aprobado'] ? 'approved' : 'failed'; ?>">
                                                <?php echo $curso['aprobado'] ? 'Aprobado' : 'No Aprobado'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Historial de Tutorías -->
            <div class="content-section">
                <h2>Historial de Tutorías</h2>
                <?php if (empty($historial_tutorias)): ?>
                    <div class="alert alert-info">
                        No hay tutorías registradas. <a href="solicitar_tutoria.php">Solicita tu primera tutoría</a>.
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Curso</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Fecha Solicitud</th>
                                    <th>Fecha Asignada</th>
                                    <th>Docente</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historial_tutorias as $tutoria): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($tutoria['codigo']); ?></td>
                                        <td class="description-cell">
                                            <?php echo htmlspecialchars(substr($tutoria['descripcion'], 0, 100)); ?>
                                            <?php if (strlen($tutoria['descripcion']) > 100): ?>...<?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status status-<?php echo $tutoria['estado']; ?>">
                                                <?php echo ucfirst($tutoria['estado']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($tutoria['fecha_solicitud'])); ?></td>
                                        <td>
                                            <?php if ($tutoria['fecha_asignada']): ?>
                                                <?php echo date('d/m/Y H:i', strtotime($tutoria['fecha_asignada'])); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Pendiente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($tutoria['docente_nombre']): ?>
                                                Prof. <?php echo htmlspecialchars($tutoria['docente_nombre'] . ' ' . $tutoria['docente_apellido']); ?>
                                            <?php else: ?>
                                                <span class="text-muted">No asignado</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <script>
        // Polling para actualizar estado de tutorías cada 30 segundos
        setInterval(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>