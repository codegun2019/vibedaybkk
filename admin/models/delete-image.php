<?php
/**
 * VIBEDAYBKK Admin - Delete Model Image
 * ลบรูปภาพโมเดล
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$image_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$model_id = isset($_GET['model_id']) ? (int)$_GET['model_id'] : 0;

if (!$image_id || !$model_id) {
    set_message('error', 'ข้อมูลไม่ถูกต้อง');
    redirect(ADMIN_URL . '/models/');
}

// Get image data
$stmt = $conn->prepare("SELECT * FROM model_images WHERE id = ? AND model_id = ?");
$stmt->bind_param('ii', $image_id, $model_id);
$stmt->execute();
$result = $stmt->get_result();
$image = $result->fetch_assoc();
$stmt->close();

if (!$image) {
    set_message('error', 'ไม่พบรูปภาพ');
    redirect(ADMIN_URL . '/models/edit.php?id=' . $model_id);
}

// Delete image file
delete_image($image['image_path']);

// Delete from database
if (db_delete($conn, 'model_images', 'id = ?', [$image_id])) {
    // If deleted primary image, set another image as primary
    if ($image['is_primary']) {
        $stmt = $conn->prepare("UPDATE model_images SET is_primary = 1 WHERE model_id = ? ORDER BY sort_order ASC LIMIT 1");
        $stmt->bind_param('i', $model_id);
        $stmt->execute();
        $stmt->close();
    }
    
    // Log activity
    log_activity($conn, $_SESSION['user_id'], 'delete_image', 'model_images', $image_id, $image);
    
    set_message('success', 'ลบรูปภาพสำเร็จ');
} else {
    set_message('error', 'เกิดข้อผิดพลาดในการลบรูปภาพ');
}

redirect(ADMIN_URL . '/models/edit.php?id=' . $model_id);
?>




