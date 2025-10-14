<?php
/**
 * VIBEDAYBKK Admin - View Booking
 * ดูและจัดการการจอง
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$page_title = 'รายละเอียดการจอง';
$current_page = 'bookings';

$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$booking_id) {
    set_message('error', 'ไม่พบข้อมูล');
    redirect(ADMIN_URL . '/bookings/');
}

$stmt = $pdo->prepare("
    SELECT b.*, m.name as model_name, m.code as model_code
    FROM bookings b
    LEFT JOIN models m ON b.model_id = m.id
    WHERE b.id = ?
");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch();

if (!$booking) {
    set_message('error', 'ไม่พบข้อมูล');
    redirect(ADMIN_URL . '/bookings/');
}

// Handle status change
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action == 'confirm' && $booking['status'] == 'pending') {
        $data = [
            'status' => 'confirmed',
            'confirmed_at' => date('Y-m-d H:i:s'),
            'confirmed_by' => $_SESSION['user_id']
        ];
        db_update($pdo, 'bookings', $data, 'id = :id', ['id' => $booking_id]);
        set_message('success', 'ยืนยันการจองสำเร็จ');
        redirect(ADMIN_URL . '/bookings/view.php?id=' . $booking_id);
    }
    
    if ($action == 'complete' && $booking['status'] == 'confirmed') {
        $conn->query("UPDATE bookings SET status = 'completed' WHERE id = {$booking_id}");
        set_message('success', 'เปลี่ยนสถานะเป็นเสร็จสิ้น');
        redirect(ADMIN_URL . '/bookings/view.php?id=' . $booking_id);
    }
    
    if ($action == 'cancel') {
        $conn->query("UPDATE bookings SET status = 'cancelled', cancelled_at = NOW() WHERE id = {$booking_id}");
        set_message('warning', 'ยกเลิกการจอง');
        redirect(ADMIN_URL . '/bookings/view.php?id=' . $booking_id);
    }
}

// Refresh data
$stmt = $pdo->prepare("
    SELECT b.*, m.name as model_name, m.code as model_code
    FROM bookings b
    LEFT JOIN models m ON b.model_id = m.id
    WHERE b.id = ?
");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch();

include '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-calendar-check me-2"></i>การจอง #<?php echo $booking_id; ?></h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php" class="btn btn-secondary">กลับ</a>
    </div>
</div>

<!-- Status Actions -->
<?php if ($booking['status'] == 'pending'): ?>
<div class="alert alert-warning">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>การจองนี้รอการยืนยัน</strong>
        </div>
        <div>
            <a href="?id=<?php echo $booking_id; ?>&action=confirm" class="btn btn-success me-2">
                <i class="fas fa-check me-1"></i>ยืนยันการจอง
            </a>
            <a href="?id=<?php echo $booking_id; ?>&action=cancel" class="btn btn-danger" onclick="return confirm('ต้องการยกเลิกการจอง?')">
                <i class="fas fa-times me-1"></i>ยกเลิก
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($booking['status'] == 'confirmed'): ?>
<div class="alert alert-success">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-check-circle me-2"></i>
            <strong>การจองยืนยันแล้ว</strong>
        </div>
        <div>
            <a href="?id=<?php echo $booking_id; ?>&action=complete" class="btn btn-primary">
                <i class="fas fa-check-double me-1"></i>เปลี่ยนเป็นเสร็จสิ้น
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <!-- Booking Info -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>ข้อมูลการจอง</h5>
                <?php echo get_status_badge($booking['status']); ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">โมเดล:</th>
                                <td>
                                    <strong><?php echo $booking['model_name']; ?></strong>
                                    <br><small class="text-muted"><?php echo $booking['model_code']; ?></small>
                                </td>
                            </tr>
                            <tr>
                                <th>วันที่จอง:</th>
                                <td><strong><?php echo format_date_thai($booking['booking_date'], 'd/m/Y'); ?></strong></td>
                            </tr>
                            <tr>
                                <th>จำนวนวัน:</th>
                                <td><?php echo $booking['booking_days']; ?> วัน</td>
                            </tr>
                            <tr>
                                <th>ประเภทงาน:</th>
                                <td><?php echo $booking['service_type'] ?: '-'; ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">ราคารวม:</th>
                                <td><h4 class="text-primary"><?php echo format_price($booking['total_price']); ?></h4></td>
                            </tr>
                            <tr>
                                <th>สถานะการชำระ:</th>
                                <td><?php echo get_status_badge($booking['payment_status']); ?></td>
                            </tr>
                            <tr>
                                <th>วันที่สร้าง:</th>
                                <td><?php echo format_date_thai($booking['created_at'], 'd/m/Y H:i'); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <?php if ($booking['location']): ?>
                <hr>
                <div>
                    <strong><i class="fas fa-map-marker-alt me-2"></i>สถานที่:</strong>
                    <p class="mb-0"><?php echo nl2br($booking['location']); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($booking['message']): ?>
                <hr>
                <div>
                    <strong><i class="fas fa-comment me-2"></i>ข้อความเพิ่มเติม:</strong>
                    <p class="mb-0"><?php echo nl2br($booking['message']); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Customer Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>ข้อมูลลูกค้า</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="150">ชื่อ:</th>
                        <td><?php echo $booking['customer_name']; ?></td>
                    </tr>
                    <tr>
                        <th>อีเมล:</th>
                        <td><a href="mailto:<?php echo $booking['customer_email']; ?>"><?php echo $booking['customer_email']; ?></a></td>
                    </tr>
                    <tr>
                        <th>โทรศัพท์:</th>
                        <td><a href="tel:<?php echo $booking['customer_phone']; ?>"><?php echo $booking['customer_phone']; ?></a></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Timeline -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Timeline</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="mb-3">
                        <i class="fas fa-plus-circle text-primary"></i>
                        <strong>สร้างการจอง</strong>
                        <br><small class="text-muted"><?php echo format_date_thai($booking['created_at'], 'd/m/Y H:i'); ?></small>
                    </div>
                    
                    <?php if ($booking['confirmed_at']): ?>
                    <div class="mb-3">
                        <i class="fas fa-check-circle text-success"></i>
                        <strong>ยืนยันแล้ว</strong>
                        <br><small class="text-muted"><?php echo format_date_thai($booking['confirmed_at'], 'd/m/Y H:i'); ?></small>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($booking['cancelled_at']): ?>
                    <div class="mb-3">
                        <i class="fas fa-times-circle text-danger"></i>
                        <strong>ยกเลิกแล้ว</strong>
                        <br><small class="text-muted"><?php echo format_date_thai($booking['cancelled_at'], 'd/m/Y H:i'); ?></small>
                        <?php if ($booking['cancel_reason']): ?>
                        <br><small><?php echo $booking['cancel_reason']; ?></small>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

