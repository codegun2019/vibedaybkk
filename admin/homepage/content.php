<?php
/**
 * Homepage Content Management
 * Redirect to index.php (ตาราง homepage_content ไม่มีในฐานข้อมูล)
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Redirect to main homepage management
redirect(ADMIN_URL . '/homepage/index.php');
?>
