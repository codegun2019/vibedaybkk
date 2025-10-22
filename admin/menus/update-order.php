<?php
/**
 * VIBEDAYBKK Admin - Update Menu Order (AJAX)
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

header('Content-Type: application/json');

if (!has_permission('menus', 'edit')) {
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$order = $data['order'] ?? [];

if (empty($order)) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
    exit;
}

$success = true;
foreach ($order as $index => $menu_id) {
    $sort_order = $index + 1;
    $stmt = $conn->prepare("UPDATE menus SET sort_order = ? WHERE id = ?");
    $stmt->bind_param('ii', $sort_order, $menu_id);
    if (!$stmt->execute()) {
        $success = false;
    }
    $stmt->close();
}

if ($success) {
    log_activity($conn, $_SESSION['user_id'], 'update', 'menus', 0, "Updated menu order");
    echo json_encode(['success' => true, 'message' => 'อัพเดทลำดับสำเร็จ']);
} else {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด']);
}
?>


