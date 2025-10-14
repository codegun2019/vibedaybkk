<?php
/**
 * VIBEDAYBKK Admin - Delete Model
 * ลบโมเดล
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Get model ID
$model_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$model_id) {
    set_message('error', 'ไม่พบข้อมูลโมเดล');
    redirect(ADMIN_URL . '/models/');
}

// Get model data
$model = get_model($conn, $model_id);
if (!$model) {
    set_message('error', 'ไม่พบข้อมูลโมเดล');
    redirect(ADMIN_URL . '/models/');
}

// Get model images
$model_images = get_model_images($conn, $model_id);

// Delete model
try {
    // Delete images from folder
    foreach ($model_images as $img) {
        delete_image($img['image_path']);
    }
    
    // Delete from database (cascade will delete images records)
    if (db_delete($pdo, 'models', 'id = :id', ['id' => $model_id])) {
        // Log activity
        log_activity($pdo, $_SESSION['user_id'], 'delete', 'models', $model_id, $model);
        
        set_message('success', 'ลบโมเดล "' . $model['name'] . '" สำเร็จ');
    } else {
        set_message('error', 'เกิดข้อผิดพลาดในการลบข้อมูล');
    }
} catch (Exception $e) {
    set_message('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
}

redirect(ADMIN_URL . '/models/');
?>

