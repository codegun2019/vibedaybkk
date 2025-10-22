<?php
/**
 * VIBEDAYBKK Admin - Delete Contact
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_permission('contacts', 'delete');

$id = (int)$_GET['id'] ?? 0;

if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM contacts WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $contact = $result->fetch_assoc();
    $stmt->close();
    
    if ($contact) {
        db_delete($conn, 'contacts', 'id = ?', [$id]);
        log_activity($conn, $_SESSION['user_id'], 'delete', 'contacts', $id, json_encode($contact));
        set_message('success', 'ลบข้อความสำเร็จ');
    } else {
        set_message('error', 'ไม่พบข้อมูล');
    }
}

redirect(ADMIN_URL . '/contacts/');
?>


