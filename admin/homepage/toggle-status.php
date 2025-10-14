<?php
/**
 * Toggle Section Status
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }
    
    $id = (int)($_POST['id'] ?? 0);
    
    // Toggle status
    $sql = "UPDATE homepage_sections SET is_active = NOT is_active WHERE id = ?";
    db_execute($conn, $sql, [$id]);
}

header('Location: index.php');
exit;

