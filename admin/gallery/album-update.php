<?php
define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

// Permission check
if (!has_permission('gallery', 'edit')) {
    $response['message'] = 'คุณไม่มีสิทธิ์แก้ไขอัลบั้ม';
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $title = clean_input($_POST['name'] ?? '');
    $description = clean_input($_POST['description'] ?? '');
    $is_active = isset($_POST['status']) && $_POST['status'] === 'active' ? 1 : 0;
    
    if (empty($title)) {
        $response['message'] = 'กรุณากรอกชื่ออัลบั้ม';
    } else {
        $data = [
            'title' => $title,
            'description' => $description,
            'is_active' => $is_active
        ];
        
        if (db_update($conn, 'gallery_albums', $data, 'id = ?', [$id])) {
            log_activity($conn, $_SESSION['user_id'], 'update', 'gallery_albums', $id);
            $response['success'] = true;
            $response['message'] = 'อัปเดตอัลบั้มสำเร็จ';
        } else {
            $response['message'] = 'เกิดข้อผิดพลาด: ' . $conn->error;
        }
    }
}

echo json_encode($response);




