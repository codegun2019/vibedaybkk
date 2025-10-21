<?php
/**
 * VIBEDAYBKK Admin - Delete User
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_permission('users', 'delete');

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ป้องกันการลบตัวเอง
if ($user_id == $_SESSION['user_id']) {
    set_message('error', 'ไม่สามารถลบบัญชีของตัวเองได้');
    redirect(ADMIN_URL . '/users/');
    exit;
}

if ($user_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if ($user) {
        // ป้องกันการลบ programmer
        if ($user['role'] == 'programmer') {
            set_message('error', 'ไม่สามารถลบ Programmer ได้');
            redirect(ADMIN_URL . '/users/');
            exit;
        }
        
        db_delete($conn, 'users', 'id = ?', [$user_id]);
        
        log_activity($conn, $_SESSION['user_id'], 'delete', 'users', $user_id, json_encode($user), null);
        
        set_message('success', 'ลบผู้ใช้สำเร็จ');
    } else {
        set_message('error', 'ไม่พบข้อมูลผู้ใช้');
    }
} else {
    set_message('error', 'ข้อมูลไม่ถูกต้อง');
}

redirect(ADMIN_URL . '/users/');
?>

