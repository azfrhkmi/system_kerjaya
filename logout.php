<?php
require_once 'config/db.php';
require_once 'includes/logger.php';

if (isset($_SESSION['user_email'])) {
    log_threat($pdo, 'ADMIN_LOGOUT', "Pengguna {$_SESSION['user_email']} telah log keluar.");
}

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();
header('Location: index.php');
exit;
