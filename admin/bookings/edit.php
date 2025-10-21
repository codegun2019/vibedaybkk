<?php
/**
 * VIBEDAYBKK Admin - Edit Booking  
 * Redirect to view (แก้ไขผ่าน view.php)
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$id = (int)$_GET['id'] ?? 0;
redirect(ADMIN_URL . '/bookings/view.php?id=' . $id);
?>

