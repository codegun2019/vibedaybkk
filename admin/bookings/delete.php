<?php
/**
 * VIBEDAYBKK Admin - Delete Booking
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_permission('bookings', 'delete');

$id = (int)$_GET['id'] ?? 0;

if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();
    
    if ($booking) {
        db_delete($conn, 'bookings', 'id = ?', [$id]);
        log_activity($conn, $_SESSION['user_id'], 'delete', 'bookings', $id, json_encode($booking));
        set_message('success', 'ลบการจองสำเร็จ');
    } else {
        set_message('error', 'ไม่พบข้อมูล');
    }
}

redirect(ADMIN_URL . '/bookings/');
?>




