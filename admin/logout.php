<?php
/**
 * VIBEDAYBKK Admin - Logout
 * ออกจากระบบ
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../includes/config.php';

// Log activity before logout
if (isset($_SESSION['user_id'])) {
    log_activity($pdo, $_SESSION['user_id'], 'logout', 'users', $_SESSION['user_id']);
}

// Destroy session
session_unset();
session_destroy();

// Clear remember me cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Redirect to login
redirect(ADMIN_URL . '/login.php');
?>

