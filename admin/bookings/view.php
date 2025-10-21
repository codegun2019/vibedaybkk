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

$stmt = $conn->prepare("
    SELECT b.*, m.name as model_name, m.code as model_code
    FROM bookings b
    LEFT JOIN models m ON b.model_id = m.id
    WHERE b.id = ?
");
$stmt->bind_param('i', $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

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
        db_update($conn, 'bookings', $data, 'id = ?', [$booking_id]);
        set_message('success', 'ยืนยันการจองสำเร็จ');
        redirect(ADMIN_URL . '/bookings/view.php?id=' . $booking_id);
    }
    
    if ($action == 'complete' && $booking['status'] == 'confirmed') {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'completed' WHERE id = ?");
        $stmt->bind_param('i', $booking_id);
        $stmt->execute();
        $stmt->close();
        set_message('success', 'เปลี่ยนสถานะเป็นเสร็จสิ้น');
        redirect(ADMIN_URL . '/bookings/view.php?id=' . $booking_id);
    }
    
    if ($action == 'cancel') {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled', cancelled_at = NOW() WHERE id = ?");
        $stmt->bind_param('i', $booking_id);
        $stmt->execute();
        $stmt->close();
        set_message('warning', 'ยกเลิกการจอง');
        redirect(ADMIN_URL . '/bookings/view.php?id=' . $booking_id);
    }
}

// Refresh data
$stmt = $conn->prepare("
    SELECT b.*, m.name as model_name, m.code as model_code
    FROM bookings b
    LEFT JOIN models m ON b.model_id = m.id
    WHERE b.id = ?
");
$stmt->bind_param('i', $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

include '../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-calendar-check mr-3 text-blue-600"></i>การจอง #<?php echo $booking_id; ?>
        </h2>
        <p class="text-sm text-gray-500 mt-1">
            <?php 
            $status_texts = [
                'pending' => 'รอดำเนินการ',
                'confirmed' => 'ยืนยันแล้ว',
                'completed' => 'เสร็จสิ้น',
                'cancelled' => 'ยกเลิก'
            ];
            echo $status_texts[$booking['status']] ?? $booking['status'];
            ?>
        </p>
    </div>
    <a href="index.php" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
        <i class="fas fa-arrow-left mr-2"></i>กลับ
    </a>
</div>

<!-- Status Actions -->
<?php if ($booking['status'] == 'pending'): ?>
<div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 mb-6 rounded-r-lg">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl mr-3"></i>
            <div>
                <h5 class="font-bold text-yellow-800">การจองนี้รอการยืนยัน</h5>
                <p class="text-sm text-yellow-700">กรุณายืนยันหรือยกเลิกการจอง</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="?id=<?php echo $booking_id; ?>&action=confirm" 
               class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200 font-medium shadow-lg">
                <i class="fas fa-check mr-2"></i>ยืนยันการจอง
            </a>
            <a href="?id=<?php echo $booking_id; ?>&action=cancel" 
               class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium shadow-lg btn-delete">
                <i class="fas fa-times mr-2"></i>ยกเลิก
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($booking['status'] == 'confirmed'): ?>
<div class="bg-green-50 border-l-4 border-green-500 p-6 mb-6 rounded-r-lg">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-600 text-2xl mr-3"></i>
            <div>
                <h5 class="font-bold text-green-800">การจองยืนยันแล้ว</h5>
                <p class="text-sm text-green-700">พร้อมให้บริการ</p>
            </div>
        </div>
        <a href="?id=<?php echo $booking_id; ?>&action=complete" 
           class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 font-medium shadow-lg">
            <i class="fas fa-check-double mr-2"></i>เปลี่ยนเป็นเสร็จสิ้น
        </a>
    </div>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <!-- Booking Info -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between">
                <h5 class="text-white text-lg font-semibold flex items-center">
                    <i class="fas fa-info-circle mr-3"></i>ข้อมูลการจอง
                </h5>
                <?php 
                $status_classes = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'confirmed' => 'bg-green-100 text-green-800',
                    'completed' => 'bg-blue-100 text-blue-800',
                    'cancelled' => 'bg-red-100 text-red-800'
                ];
                $status_texts = [
                    'pending' => 'รอดำเนินการ',
                    'confirmed' => 'ยืนยันแล้ว',
                    'completed' => 'เสร็จสิ้น',
                    'cancelled' => 'ยกเลิก'
                ];
                $status_class = $status_classes[$booking['status']] ?? 'bg-gray-100 text-gray-800';
                $status_text = $status_texts[$booking['status']] ?? $booking['status'];
                ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $status_class; ?>">
                    <?php echo $status_text; ?>
                </span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-semibold text-gray-500">โมเดล</label>
                            <div class="mt-1">
                                <div class="font-bold text-gray-900 text-lg"><?php echo $booking['model_name']; ?></div>
                                <div class="text-sm text-gray-500"><?php echo $booking['model_code']; ?></div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="text-sm font-semibold text-gray-500">วันที่จอง</label>
                            <div class="mt-1 font-semibold text-gray-900"><?php echo format_date_thai($booking['booking_date'], 'd/m/Y'); ?></div>
                        </div>
                        
                        <div>
                            <label class="text-sm font-semibold text-gray-500">จำนวนวัน</label>
                            <div class="mt-1 text-gray-900"><?php echo $booking['booking_days']; ?> วัน</div>
                        </div>
                        
                        <div>
                            <label class="text-sm font-semibold text-gray-500">ประเภทงาน</label>
                            <div class="mt-1 text-gray-900"><?php echo $booking['service_type'] ?: '-'; ?></div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-semibold text-gray-500">ราคารวม</label>
                            <div class="mt-1 text-3xl font-bold text-green-600"><?php echo format_price($booking['total_price']); ?></div>
                        </div>
                        
                        <div>
                            <label class="text-sm font-semibold text-gray-500">สถานะการชำระ</label>
                            <div class="mt-1">
                                <?php 
                                $payment_classes = [
                                    'unpaid' => 'bg-red-100 text-red-800',
                                    'deposit' => 'bg-yellow-100 text-yellow-800',
                                    'paid' => 'bg-green-100 text-green-800',
                                    'refunded' => 'bg-gray-100 text-gray-800'
                                ];
                                $payment_texts = [
                                    'unpaid' => 'ยังไม่ชำระ',
                                    'deposit' => 'มัดจำแล้ว',
                                    'paid' => 'ชำระแล้ว',
                                    'refunded' => 'คืนเงินแล้ว'
                                ];
                                $payment_class = $payment_classes[$booking['payment_status']] ?? 'bg-gray-100 text-gray-800';
                                $payment_text = $payment_texts[$booking['payment_status']] ?? $booking['payment_status'];
                                ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $payment_class; ?>">
                                    <?php echo $payment_text; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <label class="text-sm font-semibold text-gray-500">วันที่สร้าง</label>
                            <div class="mt-1 text-sm text-gray-900"><?php echo format_date_thai($booking['created_at'], 'd/m/Y H:i'); ?></div>
                        </div>
                    </div>
                </div>
                
                <?php if ($booking['location']): ?>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <label class="text-sm font-semibold text-gray-500 flex items-center mb-2">
                        <i class="fas fa-map-marker-alt mr-2"></i>สถานที่
                    </label>
                    <p class="text-gray-900"><?php echo nl2br(htmlspecialchars($booking['location'])); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($booking['message']): ?>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <label class="text-sm font-semibold text-gray-500 flex items-center mb-2">
                        <i class="fas fa-comment mr-2"></i>ข้อความเพิ่มเติม
                    </label>
                    <p class="text-gray-900"><?php echo nl2br(htmlspecialchars($booking['message'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Customer Info -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                <h5 class="text-white text-lg font-semibold flex items-center">
                    <i class="fas fa-user mr-3"></i>ข้อมูลลูกค้า
                </h5>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="text-sm font-semibold text-gray-500">ชื่อ</label>
                    <div class="mt-1 text-gray-900 font-medium"><?php echo $booking['customer_name']; ?></div>
                </div>
                
                <div>
                    <label class="text-sm font-semibold text-gray-500">อีเมล</label>
                    <div class="mt-1">
                        <a href="mailto:<?php echo $booking['customer_email']; ?>" 
                           class="text-blue-600 hover:text-blue-800 hover:underline">
                            <?php echo $booking['customer_email']; ?>
                        </a>
                    </div>
                </div>
                
                <div>
                    <label class="text-sm font-semibold text-gray-500">โทรศัพท์</label>
                    <div class="mt-1">
                        <a href="tel:<?php echo $booking['customer_phone']; ?>" 
                           class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                            <?php echo $booking['customer_phone']; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="space-y-6">
        <!-- Timeline -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
                <h5 class="text-white text-lg font-semibold flex items-center">
                    <i class="fas fa-history mr-3"></i>Timeline
                </h5>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-plus-circle text-blue-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h6 class="font-semibold text-gray-900">สร้างการจอง</h6>
                            <p class="text-sm text-gray-500"><?php echo format_date_thai($booking['created_at'], 'd/m/Y H:i'); ?></p>
                        </div>
                    </div>
                    
                    <?php if ($booking['confirmed_at']): ?>
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h6 class="font-semibold text-gray-900">ยืนยันแล้ว</h6>
                            <p class="text-sm text-gray-500"><?php echo format_date_thai($booking['confirmed_at'], 'd/m/Y H:i'); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($booking['status'] == 'completed'): ?>
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-double text-blue-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h6 class="font-semibold text-gray-900">เสร็จสิ้น</h6>
                            <p class="text-sm text-gray-500">งานเสร็จเรียบร้อย</p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($booking['cancelled_at']): ?>
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-times-circle text-red-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h6 class="font-semibold text-gray-900">ยกเลิกแล้ว</h6>
                            <p class="text-sm text-gray-500"><?php echo format_date_thai($booking['cancelled_at'], 'd/m/Y H:i'); ?></p>
                            <?php if ($booking['cancel_reason']): ?>
                            <p class="text-sm text-gray-600 mt-1"><?php echo $booking['cancel_reason']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                <h5 class="text-white text-lg font-semibold flex items-center">
                    <i class="fas fa-bolt mr-3"></i>การกระทำ
                </h5>
            </div>
            <div class="p-6 space-y-3">
                <a href="index.php" 
                   class="w-full inline-flex items-center justify-center px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-list mr-2"></i>รายการทั้งหมด
                </a>
                
                <a href="delete.php?id=<?php echo $booking_id; ?>" 
                   class="w-full inline-flex items-center justify-center px-4 py-3 bg-red-100 hover:bg-red-200 text-red-800 rounded-lg transition-colors duration-200 font-medium btn-delete">
                    <i class="fas fa-trash mr-2"></i>ลบการจอง
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>


