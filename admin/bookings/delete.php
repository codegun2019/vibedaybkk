<?php
/**
 * VIBEDAYBKK Admin - Delete Booking
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$booking_id) {
    set_message('error', 'ไม่พบข้อมูล');
    redirect(ADMIN_URL . '/bookings/');
}

$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch();

if (!$booking) {
    set_message('error', 'ไม่พบข้อมูล');
    redirect(ADMIN_URL . '/bookings/');
}

if (db_delete($pdo, 'bookings', 'id = :id', ['id' => $booking_id])) {
    log_activity($pdo, $_SESSION['user_id'], 'delete', 'bookings', $booking_id, $booking);
    set_message('success', 'ลบการจอง #' . $booking_id . ' สำเร็จ');
} else {
    set_message('error', 'เกิดข้อผิดพลาด');
}

redirect(ADMIN_URL . '/bookings/');
?>

