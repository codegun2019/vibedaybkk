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
$stmt = $pdo->prepare("SELECT * FROM model_images WHERE id = ? AND model_id = ?");
$stmt->execute([$image_id, $model_id]);
$image = $stmt->fetch();

if (!$image) {
    set_message('error', 'ไม่พบรูปภาพ');
    redirect(ADMIN_URL . '/models/edit.php?id=' . $model_id);
}

// Delete image file
delete_image($image['image_path']);

// Delete from database
if (db_delete($pdo, 'model_images', 'id = :id', ['id' => $image_id])) {
    // If deleted primary image, set another image as primary
    if ($image['is_primary']) {
        $conn->query("UPDATE model_images SET is_primary = 1 WHERE model_id = {$model_id} ORDER BY sort_order ASC LIMIT 1");
    }
    
    // Log activity
    log_activity($pdo, $_SESSION['user_id'], 'delete_image', 'model_images', $image_id, $image);
    
    set_message('success', 'ลบรูปภาพสำเร็จ');
} else {
    set_message('error', 'เกิดข้อผิดพลาดในการลบรูปภาพ');
}

redirect(ADMIN_URL . '/models/edit.php?id=' . $model_id);
?>

