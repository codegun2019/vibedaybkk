<?php
define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';
require_admin();

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$user_id || $user_id == $_SESSION['user_id']) {
    set_message('error', 'ไม่สามารถลบตัวเองได้');
    redirect(ADMIN_URL . '/users/');
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    set_message('error', 'ไม่พบข้อมูล');
    redirect(ADMIN_URL . '/users/');
}

if (db_delete($pdo, 'users', 'id = :id', ['id' => $user_id])) {
    log_activity($pdo, $_SESSION['user_id'], 'delete', 'users', $user_id, $user);
    set_message('success', 'ลบผู้ใช้ "' . $user['username'] . '" สำเร็จ');
} else {
    set_message('error', 'เกิดข้อผิดพลาด');
}

redirect(ADMIN_URL . '/users/');
?>

