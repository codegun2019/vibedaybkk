<?php
/**
 * VIBEDAYBKK Admin - Bookings List
 * รายการการจองทั้งหมด
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check
require_permission('bookings', 'view');
$can_create = has_permission('bookings', 'create');
$can_edit = has_permission('bookings', 'edit');
$can_delete = has_permission('bookings', 'delete');

$page_title = 'จัดการการจอง';
$current_page = 'bookings';

// Filter
$status_filter = $_GET['status'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = ADMIN_ITEMS_PER_PAGE;
$offset = ($page - 1) * $per_page;

$where = $status_filter ? "WHERE b.status = '{$status_filter}'" : '';

// Count total
$count_sql = "SELECT COUNT(*) as total FROM bookings b {$where}";
$result = $conn->query($count_sql);
$total_bookings = $result->fetch_assoc()['total'];
$total_pages = ceil($total_bookings / $per_page);

// Get bookings
$sql = "SELECT b.*, m.name as model_name, m.code as model_code
        FROM bookings b
        LEFT JOIN models m ON b.model_id = m.id
        {$where}
        ORDER BY b.created_at DESC
        LIMIT {$per_page} OFFSET {$offset}";
$bookings = db_get_rows($conn, $sql);

// Calculate stats
$stats = [
    'total' => $conn->query("SELECT COUNT(*) as c FROM bookings")->fetch_assoc()['c'],
    'pending' => $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status = 'pending'")->fetch_assoc()['c'],
    'confirmed' => $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status = 'confirmed'")->fetch_assoc()['c'],
    'completed' => $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status = 'completed'")->fetch_assoc()['c'],
    'revenue' => $conn->query("SELECT COALESCE(SUM(total_price), 0) as c FROM bookings WHERE status = 'completed'")->fetch_assoc()['c']
];

include '../includes/header.php';
require_once '../includes/readonly-notice.php';
?>

<?php if (!$can_create && !$can_edit && !$can_delete): ?>
    <?php show_readonly_notice('การจอง'); ?>
<?php endif; ?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-calendar-check mr-3 text-red-600"></i>จัดการการจอง
        </h2>
        <p class="text-gray-600 mt-1">จำนวนการจองทั้งหมด: <?php echo $stats['total']; ?> รายการ</p>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-lg p-6 text-center">
        <h3 class="text-3xl font-bold text-yellow-600 mb-2"><?php echo $stats['pending']; ?></h3>
        <p class="text-gray-600 font-medium">รอดำเนินการ</p>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-6 text-center">
        <h3 class="text-3xl font-bold text-green-600 mb-2"><?php echo $stats['confirmed']; ?></h3>
        <p class="text-gray-600 font-medium">ยืนยันแล้ว</p>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-6 text-center">
        <h3 class="text-3xl font-bold text-blue-600 mb-2"><?php echo $stats['completed']; ?></h3>
        <p class="text-gray-600 font-medium">เสร็จสิ้น</p>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-6 text-center">
        <h3 class="text-3xl font-bold text-red-600 mb-2"><?php echo format_price($stats['revenue']); ?></h3>
        <p class="text-gray-600 font-medium">รายได้รวม</p>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="flex flex-wrap gap-2">
        <a href="?" class="inline-flex items-center px-4 py-2 rounded-lg font-medium transition-colors duration-200 <?php echo empty($status_filter) ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            ทั้งหมด
        </a>
        <a href="?status=pending" class="inline-flex items-center px-4 py-2 rounded-lg font-medium transition-colors duration-200 <?php echo $status_filter == 'pending' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            รอดำเนินการ
        </a>
        <a href="?status=confirmed" class="inline-flex items-center px-4 py-2 rounded-lg font-medium transition-colors duration-200 <?php echo $status_filter == 'confirmed' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            ยืนยันแล้ว
        </a>
        <a href="?status=completed" class="inline-flex items-center px-4 py-2 rounded-lg font-medium transition-colors duration-200 <?php echo $status_filter == 'completed' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            เสร็จสิ้น
        </a>
        <a href="?status=cancelled" class="inline-flex items-center px-4 py-2 rounded-lg font-medium transition-colors duration-200 <?php echo $status_filter == 'cancelled' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            ยกเลิก
        </a>
    </div>
</div>

<!-- Bookings Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="p-6">
        <?php if (empty($bookings)): ?>
            <div class="text-center py-12">
                <i class="fas fa-calendar-times text-6xl text-gray-400 mb-4"></i>
                <h5 class="text-gray-600 text-xl font-medium">ไม่มีการจอง</h5>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">รหัส</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">โมเดล</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ลูกค้า</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">โทรศัพท์</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">วันที่จอง</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">จำนวนวัน</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ราคา</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">สถานะ</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">การกระทำ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($bookings as $booking): ?>
                        <tr class="hover:bg-gray-50 <?php echo $booking['status'] == 'pending' ? 'bg-yellow-50' : ''; ?>">
                            <td class="px-4 py-3">
                                <span class="font-semibold text-gray-900">#<?php echo $booking['id']; ?></span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900"><?php echo $booking['model_name']; ?></div>
                                <div class="text-sm text-gray-500"><?php echo $booking['model_code']; ?></div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo $booking['customer_name']; ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo $booking['customer_phone']; ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo format_date_thai($booking['booking_date']); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo $booking['booking_days']; ?> วัน</td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-green-600"><?php echo format_price($booking['total_price']); ?></span>
                            </td>
                            <td class="px-4 py-3">
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
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="view.php?id=<?php echo $booking['id']; ?>" 
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-800 rounded-lg transition-colors duration-200" 
                                       title="ดู">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $booking['id']; ?>" 
                                       class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-800 rounded-lg transition-colors duration-200 btn-delete" 
                                       title="ลบ">
                                        <i class="fas fa-trash text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php 
            // Pagination
            if ($total_pages > 1):
                include '../includes/pagination.php';
                render_pagination($page, $total_pages);
            endif;
            ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>



