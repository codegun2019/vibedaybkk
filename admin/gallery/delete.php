<?php
/**
 * Delete Gallery Image
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';


// Permission check
require_permission('gallery', 'delete');
header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$response = ['success' => false, 'message' => ''];

if ($id > 0) {
    // Get image info
    $stmt = $conn->prepare("SELECT * FROM gallery_images WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $image = $result->fetch_assoc();
    $stmt->close();
    
    if ($image) {
        // Delete file
        if (file_exists(ROOT_PATH . '/' . $image['file_path'])) {
            unlink(ROOT_PATH . '/' . $image['file_path']);
        }
        
        // Delete thumbnail if exists
        if ($image['thumbnail_path'] && file_exists(ROOT_PATH . '/' . $image['thumbnail_path'])) {
            unlink(ROOT_PATH . '/' . $image['thumbnail_path']);
        }
        
        // Delete from database
        $stmt = $conn->prepare("DELETE FROM gallery_images WHERE id = ?");
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $stmt->close();
            log_activity($conn, $_SESSION['user_id'], 'delete', 'gallery_images', $id);
            $response['success'] = true;
            $response['message'] = 'ลบรูปภาพสำเร็จ';
        }
    } else {
        $response['message'] = 'ไม่พบรูปภาพ';
    }
} else {
    $response['message'] = 'Invalid ID';
}

echo json_encode($response);




