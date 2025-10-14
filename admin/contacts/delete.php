<?php
/**
 * VIBEDAYBKK Admin - Delete Contact
 * ลบข้อความติดต่อ
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$contact_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$contact_id) {
    set_message('error', 'ไม่พบข้อมูล');
    redirect(ADMIN_URL . '/contacts/');
}

$stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
$stmt->execute([$contact_id]);
$contact = $stmt->fetch();

if (!$contact) {
    set_message('error', 'ไม่พบข้อมูล');
    redirect(ADMIN_URL . '/contacts/');
}

if (db_delete($pdo, 'contacts', 'id = :id', ['id' => $contact_id])) {
    log_activity($pdo, $_SESSION['user_id'], 'delete', 'contacts', $contact_id, $contact);
    set_message('success', 'ลบข้อความจาก ' . $contact['name'] . ' สำเร็จ');
} else {
    set_message('error', 'เกิดข้อผิดพลาด');
}

redirect(ADMIN_URL . '/contacts/');
?>

