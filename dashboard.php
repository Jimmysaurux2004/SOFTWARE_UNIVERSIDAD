<?php
/**
 * Dashboard principal
 * Sistema de Tutorías Universitarias
 */

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/db.php';

// Obtener estadísticas para el dashboard
$db = getDB();
$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['usuario_rol'];

$stats = [];

if ($rol === 'estudiante') {
    // Estadísticas del estudiante
    $stmt = $db->query(
        "SELECT COUNT(*) as total FROM tutorias WHERE estudiante_id = ?",
        [$usuario_id]
    );
    $stats['tutorias_solicitadas'] = $stmt->fetch()['total'];
    
    $stmt = $db->query(
        "SELECT COUNT(*) as total FROM tutorias WHERE estudiante_id = ? AND estado = 'confirmada'",
        [$usuario_id]
    );
    $stats['tutorias_confirmadas'] = $stmt->fetch()['total'];
    
    $stmt = $db->query(
        "SELECT COUNT(*) as total FROM historial_academico WHERE estudiante_id = ? AND veces_llevado > 1",
        [$usuario_id]
    );
    $stats['cursos_repetidos'] = $stmt->fetch()['total'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Tutorías</title>
    <link rel="stylesheet" href="public/css/style.css">
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
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <?php if ($rol === 'estudiante'): ?>
                    <li><a href="views/solicitar_tutoria.php">Solicitar Tutoría</a></li>
                    <li><a href="views/agendar_sesion.php">Agendar Sesión</a></li>
                    <li><a href="views/ver_historial.php">Ver Historial</a></li>
                <?php endif; ?>
                <li><a href="controllers/logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
        
        <main class="main-content">
            <header class="page-header">
                <h1>Dashboard</h1>
                <p>Bienvenido al Sistema de Tutorías Universitarias</p>
            </header>
            
            <?php if ($rol === 'estudiante'): ?>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3><?php echo $stats['tutorias_solicitadas']; ?></h3>
                        <p>Tutorías Solicitadas</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $stats['tutorias_confirmadas']; ?></h3>
                        <p>Tutorías Confirmadas</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $stats['cursos_repetidos']; ?></h3>
                        <p>Cursos Repetidos</p>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="content-section">
                <h2>Acciones Rápidas</h2>
                <div class="action-cards">
                    <?php if ($rol === 'estudiante'): ?>
                        <a href="views/solicitar_tutoria.php" class="action-card">
                            <h3>Solicitar Tutoría</h3>
                            <p>Solicita ayuda académica en un curso específico</p>
                        </a>
                        <a href="views/agendar_sesion.php" class="action-card">
                            <h3>Agendar Sesión</h3>
                            <p>Programa una sesión con un docente disponible</p>
                        </a>
                        <a href="views/ver_historial.php" class="action-card">
                            <h3>Ver Historial</h3>
                            <p>Consulta tu historial académico y de tutorías</p>
                        </a>
                    <?php else: ?>
                        <div class="action-card">
                            <h3>Panel de <?php echo ucfirst($rol); ?></h3>
                            <p>Funciones específicas para tu rol en desarrollo</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>