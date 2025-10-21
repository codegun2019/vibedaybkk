<?php
/**
 * Toggle Go to Top Status
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Permission + CSRF
    if (!has_permission('settings', 'edit')) {
        $response['message'] = 'Permission denied';
        echo json_encode($response); exit;
    }
    if (!verify_csrf_token($data['csrf_token'] ?? '')) {
        $response['message'] = 'Invalid CSRF token';
        echo json_encode($response); exit;
    }
    
    $enabled = $data['enabled'] ?? false;
    
    $settingKey = 'gototop_enabled';
    $settingValue = $enabled ? '1' : '0';
    
    try {
        $stmt = $conn->prepare("INSERT INTO settings (`setting_key`, `setting_value`, `setting_type`) VALUES (?, ?, 'text') ON DUPLICATE KEY UPDATE `setting_value` = ?");
        $stmt->bind_param('sss', $settingKey, $settingValue, $settingValue);
        
        if ($stmt->execute()) {
            $stmt->close();
            
            log_activity($conn, $_SESSION['user_id'], 'update', 'settings', 0, "Toggle Go to Top: " . ($enabled ? 'enabled' : 'disabled'));
            
            $response['success'] = true;
            $response['status'] = $enabled ? 'active' : 'inactive';
            $response['message'] = $enabled ? 'เปิดใช้งาน Go to Top เรียบร้อยแล้ว' : 'ปิดใช้งาน Go to Top เรียบร้อยแล้ว';
        } else {
            $response['message'] = 'ไม่สามารถอัพเดทได้';
        }
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
?>



