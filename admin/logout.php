<?php
/**
 * VIBEDAYBKK Admin - Logout
 * ออกจากระบบ
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../includes/config.php';

if (is_logged_in()) {
    // Log activity
    log_activity($conn, $_SESSION['user_id'], 'logout', 'users', $_SESSION['user_id']);
    
    // Destroy session
    session_unset();
    session_destroy();
    
    // Delete remember me cookie
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
}

// Redirect to login
redirect(ADMIN_URL . '/login.php?logout=1');
?>




