<?php
/**
 * VIBEDAYBKK Admin - Quick Set Permissions (AJAX)
 * ตั้งค่าสิทธิ์แบบเร็ว (เปิดทั้งหมด/ดูอย่างเดียว/ปิดทั้งหมด)
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

header('Content-Type: application/json');

if (!is_programmer()) {
    echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$role_key = $data['role_key'] ?? '';
$feature = $data['feature'] ?? '';
$action = $data['action'] ?? '';

if (empty($role_key) || empty($feature) || empty($action)) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

$permissions = [];

switch ($action) {
    case 'enable_all':
        $permissions = [
            'can_view' => 1,
            'can_create' => 1,
            'can_edit' => 1,
            'can_delete' => 1,
            'can_export' => 1
        ];
        break;
        
    case 'view_only':
        $permissions = [
            'can_view' => 1,
            'can_create' => 0,
            'can_edit' => 0,
            'can_delete' => 0,
            'can_export' => 0
        ];
        break;
        
    case 'disable_all':
        $permissions = [
            'can_view' => 0,
            'can_create' => 0,
            'can_edit' => 0,
            'can_delete' => 0,
            'can_export' => 0
        ];
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'การกระทำไม่ถูกต้อง']);
        exit;
}

// Update permissions
$sql = "INSERT INTO permissions (role_key, feature, can_view, can_create, can_edit, can_delete, can_export)
        VALUES ('{$role_key}', '{$feature}', {$permissions['can_view']}, {$permissions['can_create']}, {$permissions['can_edit']}, {$permissions['can_delete']}, {$permissions['can_export']})
        ON DUPLICATE KEY UPDATE
        can_view = {$permissions['can_view']},
        can_create = {$permissions['can_create']},
        can_edit = {$permissions['can_edit']},
        can_delete = {$permissions['can_delete']},
        can_export = {$permissions['can_export']}";

if ($conn->query($sql)) {
    log_activity($conn, $_SESSION['user_id'], 'update', 'permissions', 0, "Quick set {$role_key} - {$feature} - {$action}");
    
    echo json_encode([
        'success' => true,
        'message' => 'ตั้งค่าสำเร็จ',
        'permissions' => $permissions
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $conn->error]);
}
?>




