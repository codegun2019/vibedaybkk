<?php
/**
 * Authentication Check for Admin Panel
 */

// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    // If this is an AJAX request, return JSON error
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'กรุณาเข้าสู่ระบบ']);
        exit;
    }
    
    // For regular requests, redirect to login
    $login_url = ADMIN_URL . '/login.php';
    header('Location: ' . $login_url);
    exit;
}

// Check session timeout (2 hours)
if (isset($_SESSION['logged_in_at']) && (time() - $_SESSION['logged_in_at'] > 7200)) {
    session_unset();
    session_destroy();
    $login_url = ADMIN_URL . '/login.php?timeout=1';
    header('Location: ' . $login_url);
    exit;
}

// Update last activity
$_SESSION['last_activity'] = time();
?>


