<?php
define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

// Permission check
if (!has_permission('gallery', 'create')) {
    $response['message'] = 'คุณไม่มีสิทธิ์สร้างอัลบั้ม';
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean_input($_POST['name'] ?? '');
    $description = clean_input($_POST['description'] ?? '');
    
    if (empty($title)) {
        $response['message'] = 'กรุณากรอกชื่ออัลบั้ม';
    } else {
        // Get max sort_order
        $max_order = db_get_row($conn, "SELECT MAX(sort_order) as max_order FROM gallery_albums");
        $sort_order = ($max_order['max_order'] ?? 0) + 1;
        
        $data = [
            'title' => $title,
            'description' => $description,
            'sort_order' => $sort_order,
            'is_active' => 1
        ];
        
        if (db_insert($conn, 'gallery_albums', $data)) {
            $album_id = $conn->insert_id;
            log_activity($conn, $_SESSION['user_id'], 'create', 'gallery_albums', $album_id);
            $response['success'] = true;
            $response['album_id'] = $album_id;
            $response['message'] = 'สร้างอัลบั้มสำเร็จ';
        } else {
            $response['message'] = 'เกิดข้อผิดพลาด: ' . $conn->error;
        }
    }
}

echo json_encode($response);




