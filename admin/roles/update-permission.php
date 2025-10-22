<?php
/**
 * VIBEDAYBKK Admin - Update Permission (AJAX)
 * อัพเดทสิทธิ์แบบ real-time
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
$permission_type = $data['permission_type'] ?? '';
$enabled = $data['enabled'] ?? false;

if (empty($role_key) || empty($feature) || empty($permission_type)) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

// Map permission type to column
$column_map = [
    'view' => 'can_view',
    'create' => 'can_create',
    'edit' => 'can_edit',
    'delete' => 'can_delete',
    'export' => 'can_export'
];

$column = $column_map[$permission_type] ?? null;

if (!$column) {
    echo json_encode(['success' => false, 'message' => 'ประเภทสิทธิ์ไม่ถูกต้อง']);
    exit;
}

// Auto-enable view if enabling other permissions
$auto_enabled_view = false;
if ($enabled && $permission_type != 'view') {
    $conn->query("UPDATE permissions SET can_view = 1 WHERE role_key = '{$role_key}' AND feature = '{$feature}'");
    $auto_enabled_view = true;
}

// Update permission
$value = $enabled ? 1 : 0;
$sql = "INSERT INTO permissions (role_key, feature, {$column}) 
        VALUES ('{$role_key}', '{$feature}', {$value})
        ON DUPLICATE KEY UPDATE {$column} = {$value}";

if ($conn->query($sql)) {
    log_activity($conn, $_SESSION['user_id'], 'update', 'permissions', 0, "Updated {$role_key} - {$feature} - {$permission_type}");
    
    echo json_encode([
        'success' => true,
        'message' => 'อัพเดทสิทธิ์สำเร็จ',
        'auto_enabled_view' => $auto_enabled_view
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $conn->error]);
}
?>


