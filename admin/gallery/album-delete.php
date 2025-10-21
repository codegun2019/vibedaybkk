<?php
define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$response = ['success' => false, 'message' => ''];

// Permission check
if (!has_permission('gallery', 'delete')) {
    $response['message'] = 'คุณไม่มีสิทธิ์ลบอัลบั้ม';
    echo json_encode($response);
    exit;
}

if ($id > 0) {
    // Check if album has images
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM gallery_images WHERE album_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    if ($row['count'] > 0) {
        $response['message'] = 'ไม่สามารถลบอัลบั้มที่มีรูปภาพอยู่';
    } else {
        $stmt = $conn->prepare("DELETE FROM gallery_albums WHERE id = ?");
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $stmt->close();
            log_activity($conn, $_SESSION['user_id'], 'delete', 'gallery_albums', $id);
            $response['success'] = true;
        }
    }
}

echo json_encode($response);




