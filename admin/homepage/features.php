<?php
/**
 * Homepage Features - Redirect to Edit
 * ไฟล์นี้ไม่ได้ใช้งาน redirect ไป edit.php แทน
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$section_id = isset($_GET['section_id']) ? (int)$_GET['section_id'] : 0;

if ($section_id > 0) {
    redirect(ADMIN_URL . '/homepage/edit.php?id=' . $section_id);
} else {
    redirect(ADMIN_URL . '/homepage/');
}
?>


