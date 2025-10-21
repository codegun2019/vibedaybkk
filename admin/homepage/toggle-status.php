<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: ' . ADMIN_URL . '/login.php');
    exit;
}

// Check permissions
require_permission('homepage', 'edit');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    
    // Get current status
    $stmt = $conn->prepare("SELECT is_active FROM homepage_sections WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $section = $result->fetch_assoc();
    $stmt->close();
    
    if ($section) {
        // Toggle status
        $new_status = $section['is_active'] ? 0 : 1;
        
        $stmt = $conn->prepare("UPDATE homepage_sections SET is_active = ? WHERE id = ?");
        $stmt->bind_param('ii', $new_status, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = $new_status ? 'เปิดใช้งาน section สำเร็จ' : 'ปิดการใช้งาน section สำเร็จ';
            
            // Log activity
            log_activity(
                $conn,
                $_SESSION['user_id'],
                'toggle_section',
                'homepage_sections',
                $id,
                $new_status ? 'เปิดใช้งาน section' : 'ปิดการใช้งาน section'
            );
        } else {
            $_SESSION['error'] = 'เกิดข้อผิดพลาดในการอัปเดต';
        }
        
        $stmt->close();
    }
}

header('Location: index.php');
exit;


