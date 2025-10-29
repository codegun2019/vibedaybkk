<?php
// Prevent any output before setting headers
ob_start();

// session_start() is already called in includes/config.php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    // Send JSON error for AJAX
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Check permissions
require_permission('homepage', 'edit');

// Set JSON header for AJAX response
header('Content-Type: application/json');

// Clean output buffer after headers
ob_clean();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    
    // Get current status
    $stmt = $conn->prepare("SELECT is_active FROM homepage_sections WHERE id = ?");
    if (!$stmt) {
        echo json_encode([
            'success' => false,
            'message' => 'Prepare failed: ' . $conn->error
        ]);
        exit;
    }
    
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $section = $result->fetch_assoc();
    $stmt->close();
    
    if ($section) {
        // Toggle status
        $new_status = $section['is_active'] ? 0 : 1;
        
        $stmt = $conn->prepare("UPDATE homepage_sections SET is_active = ? WHERE id = ?");
        if (!$stmt) {
            echo json_encode([
                'success' => false,
                'message' => 'Prepare failed: ' . $conn->error
            ]);
            exit;
        }
        
        $stmt->bind_param('ii', $new_status, $id);
        
        if ($stmt->execute()) {
            // Log activity
            if (function_exists('log_activity') && isset($_SESSION['user_id'])) {
                log_activity(
                    $conn,
                    $_SESSION['user_id'],
                    'toggle_section',
                    'homepage_sections',
                    $id,
                    $new_status ? 'เปิดใช้งาน section' : 'ปิดการใช้งาน section'
                );
            }
            
            // Return success JSON
            echo json_encode([
                'success' => true,
                'is_active' => $new_status,
                'message' => $new_status ? 'เปิดใช้งาน section สำเร็จ' : 'ปิดการใช้งาน section สำเร็จ'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            // Return error JSON
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการอัปเดต: ' . $conn->error
            ], JSON_UNESCAPED_UNICODE);
        }
        
        $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'ไม่พบ section ที่ต้องการ'
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ข้อมูลไม่ครบถ้วน'
    ], JSON_UNESCAPED_UNICODE);
}
exit;


