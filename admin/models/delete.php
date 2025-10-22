<?php
/**
 * VIBEDAYBKK Admin - Delete Model
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_permission('models', 'delete');

$model_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($model_id > 0) {
    // Get model info
    $stmt = $conn->prepare("SELECT * FROM models WHERE id = ?");
    $stmt->bind_param('i', $model_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $model = $result->fetch_assoc();
    $stmt->close();
    
    if ($model) {
        // Delete model images
        $imgs = db_get_rows($conn, "SELECT * FROM model_images WHERE model_id = ?", [$model_id]);
        foreach ($imgs as $img) {
            if (file_exists(ROOT_PATH . '/' . $img['image_path'])) {
                @unlink(ROOT_PATH . '/' . $img['image_path']);
            }
        }
        
        // Delete model (cascade will delete images)
        db_delete($conn, 'models', 'id = ?', [$model_id]);
        
        log_activity($conn, $_SESSION['user_id'], 'delete', 'models', $model_id, json_encode($model), null);
        
        set_message('success', 'ลบโมเดลสำเร็จ');
    } else {
        set_message('error', 'ไม่พบข้อมูลโมเดล');
    }
} else {
    set_message('error', 'ข้อมูลไม่ถูกต้อง');
}

redirect(ADMIN_URL . '/models/');
?>


