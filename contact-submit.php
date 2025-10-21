<?php
/**
 * VIBEDAYBKK - Contact Form Submission
 * ประมวลผลฟอร์มติดต่อ
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

// ตั้งค่า Response
header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

try {
    // ตรวจสอบ Request Method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // ตรวจสอบ CSRF Token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        throw new Exception('Invalid CSRF token');
    }
    
    // รับข้อมูลจากฟอร์ม
    $name = clean_input($_POST['name'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $service_type = clean_input($_POST['service_type'] ?? '');
    $message = clean_input($_POST['message'] ?? '');
    
    // Validate ข้อมูล
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'กรุณากรอกชื่อ-นามสกุล';
    }
    
    if (empty($email)) {
        $errors[] = 'กรุณากรอกอีเมล';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'รูปแบบอีเมลไม่ถูกต้อง';
    }
    
    if (empty($phone)) {
        $errors[] = 'กรุณากรอกเบอร์โทรศัพท์';
    }
    
    if (empty($service_type)) {
        $errors[] = 'กรุณาเลือกประเภทงาน';
    }
    
    if (empty($message)) {
        $errors[] = 'กรุณากรอกรายละเอียดงาน';
    }
    
    if (!empty($errors)) {
        throw new Exception(implode(', ', $errors));
    }
    
    // บันทึกข้อมูลลงตาราง contacts
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $status = 'new';
    
    $stmt = $conn->prepare("
        INSERT INTO contacts (name, email, phone, service_type, message, ip_address, user_agent, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param('ssssssss', $name, $email, $phone, $service_type, $message, $ip_address, $user_agent, $status);
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $inserted_id = $conn->insert_id;
    $stmt->close();
    
    // ส่ง Response สำเร็จ
    $response['success'] = true;
    $response['message'] = 'ส่งข้อความสำเร็จ! เราจะติดต่อกลับโดยเร็วที่สุด';
    
    // Log activity (ถ้ามีผู้ใช้ login)
    if (isset($_SESSION['user_id'])) {
        log_activity($conn, $_SESSION['user_id'], 'create', 'contacts', $conn->insert_id);
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    
    // Log error
    error_log('Contact Form Error: ' . $e->getMessage());
}

// ส่ง Response
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>

