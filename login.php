<?php
/**
 * Página de login
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

require_once 'config/db.php';

$error = '';

if ($_POST) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Por favor, complete todos los campos.';
    } else {
        try {
            $db = getDB();
            $stmt = $db->query(
                "SELECT id, email, password_hash, nombre, apellido, rol FROM usuarios WHERE email = ?",
                [$email]
            );
            $usuario = $stmt->fetch();
            
            if ($usuario && password_verify($password, $usuario['password_hash'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_apellido'] = $usuario['apellido'];
                $_SESSION['usuario_rol'] = $usuario['rol'];
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Credenciales incorrectas.';
            }
        } catch (Exception $e) {
            $error = 'Error en el sistema. Intente nuevamente.';
            error_log($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Tutorías</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Sistema de Tutorías</h1>
                <p>Universidad - Plataforma Académica</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form id="loginForm" method="POST" class="login-form">
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    <span class="form-error" id="emailError"></span>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                    <span class="form-error" id="passwordError"></span>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">
                    Iniciar Sesión
                </button>
            </form>
            
            <div class="login-help">
                <p><strong>Usuarios de prueba:</strong></p>
                <ul>
                    <li>Estudiante: juan.perez@universidad.edu / password</li>
                    <li>Estudiante: maria.garcia@universidad.edu / password</li>
                    <li>Docente: prof.rodriguez@universidad.edu / password</li>
                </ul>
            </div>
        </div>
    </div>
    
    <script src="public/js/login.js"></script>
</body>
</html>