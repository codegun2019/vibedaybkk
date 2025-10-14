<?php
/**
 * VIBEDAYBKK Admin - Delete Menu
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$menu_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$menu_id) {
    set_message('error', 'ไม่พบข้อมูล');
    redirect(ADMIN_URL . '/menus/');
}

$stmt = $pdo->prepare("SELECT * FROM menus WHERE id = ?");
$stmt->execute([$menu_id]);
$menu = $stmt->fetch();

if (!$menu) {
    set_message('error', 'ไม่พบข้อมูล');
    redirect(ADMIN_URL . '/menus/');
}

if (db_delete($pdo, 'menus', 'id = :id', ['id' => $menu_id])) {
    log_activity($pdo, $_SESSION['user_id'], 'delete', 'menus', $menu_id, $menu);
    set_message('success', 'ลบเมนู "' . $menu['title'] . '" สำเร็จ');
} else {
    set_message('error', 'เกิดข้อผิดพลาด');
}

redirect(ADMIN_URL . '/menus/');
?>

