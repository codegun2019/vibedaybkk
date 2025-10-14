<?php
/**
 * VIBEDAYBKK Admin - Dashboard (Tailwind CSS)
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../includes/config.php';

$page_title = 'Dashboard';
$current_page = 'dashboard';

// ดึงสถิติ
$stats = [];
$stats['total_models'] = $conn->query("SELECT COUNT(*) as c FROM models")->fetch_assoc()['c'];
$stats['available_models'] = $conn->query("SELECT COUNT(*) as c FROM models WHERE status = 'available'")->fetch_assoc()['c'];
$stats['total_categories'] = $conn->query("SELECT COUNT(*) as c FROM categories WHERE status = 'active'")->fetch_assoc()['c'];
$stats['published_articles'] = $conn->query("SELECT COUNT(*) as c FROM articles WHERE status = 'published'")->fetch_assoc()['c'];
$stats['new_contacts'] = $conn->query("SELECT COUNT(*) as c FROM contacts WHERE status = 'new'")->fetch_assoc()['c'];
$stats['pending_bookings'] = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status = 'pending'")->fetch_assoc()['c'];
$stats['total_revenue'] = $conn->query("SELECT COALESCE(SUM(total_price), 0) as c FROM bookings WHERE status = 'completed'")->fetch_assoc()['c'];

// โมเดลยอดนิยม
$top_models = db_get_rows($conn, "
    SELECT m.*, c.name as category_name, COUNT(b.id) as booking_count
    FROM models m
    LEFT JOIN categories c ON m.category_id = c.id
    LEFT JOIN bookings b ON m.id = b.model_id AND b.status = 'completed'
    GROUP BY m.id
    ORDER BY booking_count DESC, m.view_count DESC
    LIMIT 5
");

// ข้อความล่าสุด
$recent_contacts = db_get_rows($conn, "SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5");

// การจองล่าสุด
$recent_bookings = db_get_rows($conn, "
    SELECT b.*, m.name as model_name
    FROM bookings b
    LEFT JOIN models m ON b.model_id = m.id
    ORDER BY b.created_at DESC
    LIMIT 5
");

include 'includes/header.php';
?>

<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-900 flex items-center">
        <i class="fas fa-tachometer-alt mr-3 text-red-600"></i>Dashboard
    </h2>
    <p class="text-gray-600 mt-1">ภาพรวมของระบบ VIBEDAYBKK</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-600 hover:shadow-xl transition-all duration-200">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-semibold text-red-600 uppercase tracking-wide">โมเดลทั้งหมด</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $stats['total_models']; ?> <span class="text-lg text-gray-600">คน</span></p>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <i class="fas fa-users text-2xl text-red-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-600 hover:shadow-xl transition-all duration-200">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-semibold text-green-600 uppercase tracking-wide">โมเดลว่าง</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $stats['available_models']; ?> <span class="text-lg text-gray-600">คน</span></p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-user-check text-2xl text-green-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-600 hover:shadow-xl transition-all duration-200">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-semibold text-yellow-600 uppercase tracking-wide">จองรอดำเนินการ</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $stats['pending_bookings']; ?> <span class="text-lg text-gray-600">รายการ</span></p>
            </div>
            <div class="bg-yellow-100 p-3 rounded-full">
                <i class="fas fa-calendar-check text-2xl text-yellow-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-600 hover:shadow-xl transition-all duration-200">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-semibold text-blue-600 uppercase tracking-wide">รายได้รวม</p>
                <p class="text-2xl font-bold text-gray-900 mt-2"><?php echo format_price($stats['total_revenue']); ?></p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-dollar-sign text-2xl text-blue-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- More Stats -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-th-large text-2xl text-purple-600"></i>
        </div>
        <h3 class="text-3xl font-bold text-gray-900"><?php echo $stats['total_categories']; ?></h3>
        <p class="text-gray-600 mt-1">หมวดหมู่บริการ</p>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
        <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-newspaper text-2xl text-green-600"></i>
        </div>
        <h3 class="text-3xl font-bold text-gray-900"><?php echo $stats['published_articles']; ?></h3>
        <p class="text-gray-600 mt-1">บทความเผยแพร่</p>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
        <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-envelope text-2xl text-orange-600"></i>
        </div>
        <h3 class="text-3xl font-bold text-gray-900"><?php echo $stats['new_contacts']; ?></h3>
        <p class="text-gray-600 mt-1">ข้อความใหม่</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Top Models -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-red-600 to-red-500 text-white p-4">
            <h3 class="text-lg font-semibold flex items-center">
                <i class="fas fa-star mr-2"></i>โมเดลยอดนิยม (Top 5)
            </h3>
        </div>
        <div class="p-6">
            <?php if (empty($top_models)): ?>
                <p class="text-gray-500 text-center py-4">ยังไม่มีข้อมูล</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($top_models as $model): ?>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900"><?php echo $model['name']; ?></p>
                            <p class="text-sm text-gray-600"><?php echo $model['code']; ?> - <?php echo $model['category_name']; ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600"><i class="fas fa-calendar-check mr-1"></i><?php echo $model['booking_count']; ?> จอง</p>
                            <p class="text-sm text-gray-600"><i class="fas fa-eye mr-1"></i><?php echo $model['view_count']; ?> views</p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Contacts -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 text-white p-4">
            <h3 class="text-lg font-semibold flex items-center">
                <i class="fas fa-envelope mr-2"></i>ข้อความติดต่อล่าสุด
            </h3>
        </div>
        <div class="p-6">
            <?php if (empty($recent_contacts)): ?>
                <p class="text-gray-500 text-center py-4">ยังไม่มีข้อความ</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($recent_contacts as $contact): ?>
                    <a href="<?php echo ADMIN_URL; ?>/contacts/view.php?id=<?php echo $contact['id']; ?>" 
                       class="block p-4 bg-gray-50 rounded-lg hover:bg-blue-50 transition-colors">
                        <div class="flex items-start justify-between mb-2">
                            <p class="font-semibold text-gray-900"><?php echo $contact['name']; ?></p>
                            <span class="text-xs text-gray-500"><?php echo time_ago($contact['created_at']); ?></span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2"><?php echo truncate_text($contact['message'], 50); ?></p>
                        <span class="inline-block px-2 py-1 text-xs rounded-full <?php echo $contact['status'] == 'new' ? 'bg-red-100 text-red-700' : 'bg-gray-200 text-gray-700'; ?>">
                            <?php echo $contact['status'] == 'new' ? 'ใหม่' : $contact['status']; ?>
                        </span>
                    </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Recent Bookings -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
    <div class="bg-gradient-to-r from-purple-600 to-purple-500 text-white p-4">
        <h3 class="text-lg font-semibold flex items-center">
            <i class="fas fa-calendar-check mr-2"></i>การจองล่าสุด
        </h3>
    </div>
    <div class="p-6">
        <?php if (empty($recent_bookings)): ?>
            <p class="text-gray-500 text-center py-4">ยังไม่มีการจอง</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">รหัส</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">โมเดล</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ลูกค้า</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">วันที่จอง</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ราคา</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($recent_bookings as $booking): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">#<?php echo $booking['id']; ?></td>
                            <td class="px-4 py-3 text-sm text-gray-700"><?php echo $booking['model_name']; ?></td>
                            <td class="px-4 py-3 text-sm text-gray-700"><?php echo $booking['customer_name']; ?></td>
                            <td class="px-4 py-3 text-sm text-gray-700"><?php echo format_date_thai($booking['booking_date']); ?></td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo format_price($booking['total_price']); ?></td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    <?php 
                                    echo $booking['status'] == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                        ($booking['status'] == 'confirmed' ? 'bg-green-100 text-green-700' : 
                                        ($booking['status'] == 'completed' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'));
                                    ?>">
                                    <?php 
                                    echo $booking['status'] == 'pending' ? 'รอดำเนินการ' : 
                                        ($booking['status'] == 'confirmed' ? 'ยืนยันแล้ว' : 
                                        ($booking['status'] == 'completed' ? 'เสร็จสิ้น' : $booking['status']));
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-gray-800 to-gray-700 text-white p-4">
        <h3 class="text-lg font-semibold flex items-center">
            <i class="fas fa-bolt mr-2"></i>Quick Actions
        </h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="<?php echo ADMIN_URL; ?>/models/add.php" 
               class="group bg-gradient-to-br from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white p-6 rounded-xl text-center transition-all duration-200 transform hover:scale-105 shadow-lg">
                <i class="fas fa-plus-circle text-3xl mb-3 group-hover:scale-110 transition-transform"></i>
                <p class="font-medium">เพิ่มโมเดล</p>
            </a>
            
            <a href="<?php echo ADMIN_URL; ?>/categories/add.php" 
               class="group bg-gradient-to-br from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white p-6 rounded-xl text-center transition-all duration-200 transform hover:scale-105 shadow-lg">
                <i class="fas fa-plus-circle text-3xl mb-3 group-hover:scale-110 transition-transform"></i>
                <p class="font-medium">เพิ่มหมวดหมู่</p>
            </a>
            
            <a href="<?php echo ADMIN_URL; ?>/articles/add.php" 
               class="group bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white p-6 rounded-xl text-center transition-all duration-200 transform hover:scale-105 shadow-lg">
                <i class="fas fa-plus-circle text-3xl mb-3 group-hover:scale-110 transition-transform"></i>
                <p class="font-medium">เพิ่มบทความ</p>
            </a>
            
            <a href="<?php echo ADMIN_URL; ?>/models/" 
               class="group bg-gradient-to-br from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white p-6 rounded-xl text-center transition-all duration-200 transform hover:scale-105 shadow-lg">
                <i class="fas fa-users text-3xl mb-3 group-hover:scale-110 transition-transform"></i>
                <p class="font-medium">ดูโมเดลทั้งหมด</p>
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
