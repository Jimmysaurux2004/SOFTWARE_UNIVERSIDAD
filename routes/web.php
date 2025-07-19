<?php
/**
 * Router simple para rutas amigables
 * Sistema de Tutorías Universitarias
 */

$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Rutas principales
switch ($path) {
    case '/':
        require_once '../index.php';
        break;
    case '/login':
        require_once '../login.php';
        break;
    case '/dashboard':
        require_once '../dashboard.php';
        break;
    case '/solicitar-tutoria':
        require_once '../views/solicitar_tutoria.php';
        break;
    case '/agendar-sesion':
        require_once '../views/agendar_sesion.php';
        break;
    case '/ver-historial':
        require_once '../views/ver_historial.php';
        break;
    case '/logout':
        require_once '../controllers/logout.php';
        break;
    default:
        http_response_code(404);
        echo "Página no encontrada";
        break;
}
?>