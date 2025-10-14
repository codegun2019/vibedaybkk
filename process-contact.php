<?php
/**
 * VIBEDAYBKK - Contact Form Processor
 * ประมวลผลฟอร์มติดต่อจากหน้าบ้าน
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

try {
    // Collect data
    $data = [
        'name' => clean_input($_POST['name'] ?? ''),
        'email' => clean_input($_POST['email'] ?? ''),
        'phone' => clean_input($_POST['phone'] ?? ''),
        'service_type' => clean_input($_POST['service_type'] ?? ''),
        'message' => clean_input($_POST['message'] ?? ''),
        'status' => 'new',
        'ip_address' => get_client_ip(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
    ];
    
    // Validate
    if (empty($data['name'])) {
        throw new Exception('กรุณากรอกชื่อ');
    }
    if (empty($data['email']) || !validate_email($data['email'])) {
        throw new Exception('กรุณากรอกอีเมลที่ถูกต้อง');
    }
    if (empty($data['phone'])) {
        throw new Exception('กรุณากรอกเบอร์โทรศัพท์');
    }
    if (empty($data['message'])) {
        throw new Exception('กรุณากรอกข้อความ');
    }
    
    // Insert to database
    if (db_insert($pdo, 'contacts', $data)) {
        $response['success'] = true;
        $response['message'] = 'ส่งข้อความสำเร็จ! เราจะติดต่อกลับโดยเร็ว';
        
        // ในระบบจริงควรส่งอีเมลแจ้งเตือน admin
        // mail($admin_email, 'มีข้อความใหม่จาก ' . $data['name'], ...);
        
    } else {
        throw new Exception('เกิดข้อผิดพลาดในการบันทึกข้อมูล');
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>

