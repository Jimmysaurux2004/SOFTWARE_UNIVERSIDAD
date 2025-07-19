<?php
/**
 * Controlador para cerrar sesión
 * Sistema de Tutorías Universitarias
 */

session_start();
session_destroy();
header('Location: ../login.php');
exit;
?>