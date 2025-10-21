<?php
/**
 * Contact Form Submission API
 * รับข้อมูลจากฟอร์มติดต่อและบันทึกลงฐานข้อมูล
 */
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

try {
    // ตรวจสอบว่าเป็น POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // ดึงข้อมูลจากฟอร์ม
    $name = clean_input($_POST['name'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $job_type = clean_input($_POST['job_type'] ?? '');
    $message = clean_input($_POST['message'] ?? '');
    
    // ตรวจสอบข้อมูล
    if (empty($name) || empty($email) || empty($phone) || empty($job_type) || empty($message)) {
        throw new Exception('กรุณากรอกข้อมูลให้ครบถ้วน');
    }
    
    // ตรวจสอบรูปแบบอีเมล
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('รูปแบบอีเมลไม่ถูกต้อง');
    }
    
    // บันทึกลงฐานข้อมูล
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, job_type, message, created_at, status) 
                            VALUES (?, ?, ?, ?, ?, NOW(), 'pending')");
    
    if (!$stmt) {
        throw new Exception('เกิดข้อผิดพลาดในการเตรียมคำสั่ง: ' . $conn->error);
    }
    
    $stmt->bind_param('sssss', $name, $email, $phone, $job_type, $message);
    
    if (!$stmt->execute()) {
        throw new Exception('เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error);
    }
    
    $stmt->close();
    
    // ส่งอีเมลแจ้งเตือน (ถ้ามีการตั้งค่า)
    // TODO: Implement email notification
    
    echo json_encode([
        'success' => true,
        'message' => 'ส่งข้อความสำเร็จ! เราจะติดต่อกลับโดยเร็วที่สุด'
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

