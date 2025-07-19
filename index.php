<?php
/**
 * Punto de entrada principal
 * Sistema de Tutorías Universitarias
 */

session_start();

// Cargar variables de entorno
if (file_exists('.env')) {
    $env = parse_ini_file('.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// Verificar si hay sesión activa
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
} else {
    header('Location: login.php');
    exit;
}
?>