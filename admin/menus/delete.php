<?php
/**
 * VIBEDAYBKK Admin - Delete Menu
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_permission('menus', 'delete');

$menu_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($menu_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM menus WHERE id = ?");
    $stmt->bind_param('i', $menu_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $menu = $result->fetch_assoc();
    $stmt->close();
    
    if ($menu) {
        // ลบเมนูย่อย (ถ้ามี)
        $conn->query("DELETE FROM menus WHERE parent_id = {$menu_id}");
        
        // ลบเมนูหลัก
        db_delete($conn, 'menus', 'id = ?', [$menu_id]);
        
        log_activity($conn, $_SESSION['user_id'], 'delete', 'menus', $menu_id, json_encode($menu), null);
        
        set_message('success', 'ลบเมนูสำเร็จ');
    } else {
        set_message('error', 'ไม่พบข้อมูลเมนู');
    }
} else {
    set_message('error', 'ข้อมูลไม่ถูกต้อง');
}

redirect(ADMIN_URL . '/menus/');
?>


