<?php
/**
 * VIBEDAYBKK Admin - Set Primary Image
 * ตั้งรูปภาพหลัก
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$image_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$model_id = isset($_GET['model_id']) ? (int)$_GET['model_id'] : 0;

if (!$image_id || !$model_id) {
    set_message('error', 'ข้อมูลไม่ถูกต้อง');
    redirect(ADMIN_URL . '/models/');
}

try {
    // Remove primary from all images of this model
    $conn->query("UPDATE model_images SET is_primary = 0 WHERE model_id = {$model_id}");
    
    // Set new primary
    $conn->query("UPDATE model_images SET is_primary = 1 WHERE id = {$image_id}");
    
    // Log activity
    log_activity($pdo, $_SESSION['user_id'], 'set_primary_image', 'model_images', $image_id);
    
    set_message('success', 'ตั้งรูปหลักสำเร็จ');
} catch (Exception $e) {
    set_message('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
}

redirect(ADMIN_URL . '/models/edit.php?id=' . $model_id);
?>

