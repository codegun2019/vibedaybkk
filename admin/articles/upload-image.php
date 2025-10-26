<?php
/**
 * CKEditor Image Upload Handler
 * จัดการการอัพโหลดรูปภาพจาก CKEditor
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => ['message' => 'Unauthorized']]);
    exit;
}

// ตรวจสอบว่ามีไฟล์ถูกอัพโหลดหรือไม่
if (!isset($_FILES['upload']) || $_FILES['upload']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => ['message' => 'ไม่พบไฟล์หรือเกิดข้อผิดพลาดในการอัพโหลด']]);
    exit;
}

$file = $_FILES['upload'];

// ตรวจสอบประเภทไฟล์
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime_type, $allowed_types)) {
    http_response_code(400);
    echo json_encode(['error' => ['message' => 'ประเภทไฟล์ไม่ถูกต้อง อนุญาตเฉพาะ JPG, PNG, GIF, WebP']]);
    exit;
}

// ตรวจสอบขนาดไฟล์ (สูงสุด 5MB)
$max_size = 5 * 1024 * 1024; // 5MB
if ($file['size'] > $max_size) {
    http_response_code(400);
    echo json_encode(['error' => ['message' => 'ขนาดไฟล์ใหญ่เกินไป (สูงสุด 5MB)']]);
    exit;
}

// สร้างโฟลเดอร์ถ้ายังไม่มี
$upload_dir = UPLOADS_PATH . '/articles/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// สร้างชื่อไฟล์ใหม่ที่ไม่ซ้ำกัน
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid('article_') . '_' . time() . '.' . $extension;
$filepath = $upload_dir . $filename;

// อัพโหลดไฟล์
if (move_uploaded_file($file['tmp_name'], $filepath)) {
    // ปรับขนาดรูปภาพถ้าใหญ่เกินไป (optional)
    $max_width = 1200;
    $max_height = 1200;
    
    list($width, $height) = getimagesize($filepath);
    
    if ($width > $max_width || $height > $max_height) {
        // คำนวณขนาดใหม่โดยรักษาอัตราส่วน
        $ratio = min($max_width / $width, $max_height / $height);
        $new_width = (int)($width * $ratio);
        $new_height = (int)($height * $ratio);
        
        // สร้างรูปภาพใหม่
        $source = null;
        switch ($mime_type) {
            case 'image/jpeg':
            case 'image/jpg':
                $source = imagecreatefromjpeg($filepath);
                break;
            case 'image/png':
                $source = imagecreatefrompng($filepath);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($filepath);
                break;
            case 'image/webp':
                $source = imagecreatefromwebp($filepath);
                break;
        }
        
        if ($source) {
            $destination = imagecreatetruecolor($new_width, $new_height);
            
            // รักษาความโปร่งใส (สำหรับ PNG และ GIF)
            if ($mime_type === 'image/png' || $mime_type === 'image/gif') {
                imagealphablending($destination, false);
                imagesavealpha($destination, true);
            }
            
            // ปรับขนาด
            imagecopyresampled($destination, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            
            // บันทึกรูปภาพใหม่
            switch ($mime_type) {
                case 'image/jpeg':
                case 'image/jpg':
                    imagejpeg($destination, $filepath, 85);
                    break;
                case 'image/png':
                    imagepng($destination, $filepath, 8);
                    break;
                case 'image/gif':
                    imagegif($destination, $filepath);
                    break;
                case 'image/webp':
                    imagewebp($destination, $filepath, 85);
                    break;
            }
            
            imagedestroy($source);
            imagedestroy($destination);
        }
    }
    
    // ส่ง URL กลับไปให้ CKEditor
    $url = UPLOADS_URL . '/articles/' . $filename;
    
    // CKEditor 5 response format
    echo json_encode([
        'url' => $url,
        'uploaded' => 1,
        'fileName' => $filename
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => ['message' => 'เกิดข้อผิดพลาดในการบันทึกไฟล์']]);
}
?>




