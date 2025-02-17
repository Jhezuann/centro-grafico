<?php
session_start(); // Iniciar sesión PHP si no se ha iniciado aún

// Destruir todas las variables de sesión
$_SESSION = array();

// Si se desea eliminar la sesión, también se debe eliminar la cookie de sesión.
// Nota: Esto destruirá la sesión y no la información de la sesión.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión.
session_destroy();

// Redirigir al usuario a la página de inicio de sesión u otra página adecuada
header("Location: index.php");
exit;
?>
