<?php
/**
 * VIBEDAYBKK Admin - Contacts List
 * รายการข้อความติดต่อ
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$page_title = 'ข้อความติดต่อ';
$current_page = 'contacts';

// Filter
$status_filter = $_GET['status'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = ADMIN_ITEMS_PER_PAGE;
$offset = ($page - 1) * $per_page;

$where = $status_filter ? "WHERE status = '{$status_filter}'" : '';

// Count total
$count_sql = "SELECT COUNT(*) as total FROM contacts {$where}";
$result = $conn->query($count_sql);
$total_contacts = $result->fetch_assoc()['total'];
$total_pages = ceil($total_contacts / $per_page);

// Get contacts
$sql = "SELECT * FROM contacts {$where} ORDER BY created_at DESC LIMIT {$per_page} OFFSET {$offset}";
$contacts = db_get_rows($conn, $sql);

include '../includes/header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-envelope mr-3 text-red-600"></i>ข้อความติดต่อ
        </h2>
        <p class="text-gray-600 mt-1">จำนวนข้อความทั้งหมด: <?php echo $total_contacts; ?> ข้อความ</p>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="flex flex-wrap gap-2">
        <a href="?" class="inline-flex items-center px-4 py-2 rounded-lg font-medium transition-colors duration-200 <?php echo empty($status_filter) ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            ทั้งหมด
        </a>
        <a href="?status=new" class="inline-flex items-center px-4 py-2 rounded-lg font-medium transition-colors duration-200 <?php echo $status_filter == 'new' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            <i class="fas fa-circle text-red-500 mr-2"></i>ใหม่
        </a>
        <a href="?status=read" class="inline-flex items-center px-4 py-2 rounded-lg font-medium transition-colors duration-200 <?php echo $status_filter == 'read' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            อ่านแล้ว
        </a>
        <a href="?status=replied" class="inline-flex items-center px-4 py-2 rounded-lg font-medium transition-colors duration-200 <?php echo $status_filter == 'replied' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            ตอบกลับแล้ว
        </a>
        <a href="?status=closed" class="inline-flex items-center px-4 py-2 rounded-lg font-medium transition-colors duration-200 <?php echo $status_filter == 'closed' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            ปิด
        </a>
    </div>
</div>

<!-- Contacts Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="p-6">
        <?php if (empty($contacts)): ?>
            <div class="text-center py-12">
                <i class="fas fa-inbox text-6xl text-gray-400 mb-4"></i>
                <h5 class="text-gray-600 text-xl font-medium">ไม่มีข้อความ</h5>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">วันที่</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ชื่อ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">อีเมล</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">โทรศัพท์</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ประเภทงาน</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ข้อความ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">สถานะ</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">การกระทำ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($contacts as $contact): ?>
                        <tr class="hover:bg-gray-50 <?php echo $contact['status'] == 'new' ? 'bg-yellow-50' : ''; ?>">
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900"><?php echo time_ago($contact['created_at']); ?></div>
                                <div class="text-xs text-gray-500"><?php echo format_date_thai($contact['created_at'], 'd/m/Y H:i'); ?></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <span class="font-semibold text-gray-900"><?php echo $contact['name']; ?></span>
                                    <?php if ($contact['status'] == 'new'): ?>
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            ใหม่
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo $contact['email']; ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo $contact['phone']; ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo $contact['service_type'] ?: '-'; ?></td>
                            <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate"><?php echo truncate_text($contact['message'], 60); ?></td>
                            <td class="px-4 py-3">
                                <?php 
                                $status_classes = [
                                    'new' => 'bg-red-100 text-red-800',
                                    'read' => 'bg-blue-100 text-blue-800',
                                    'replied' => 'bg-green-100 text-green-800',
                                    'closed' => 'bg-gray-100 text-gray-800'
                                ];
                                $status_texts = [
                                    'new' => 'ใหม่',
                                    'read' => 'อ่านแล้ว',
                                    'replied' => 'ตอบกลับแล้ว',
                                    'closed' => 'ปิด'
                                ];
                                $status_class = $status_classes[$contact['status']] ?? 'bg-gray-100 text-gray-800';
                                $status_text = $status_texts[$contact['status']] ?? $contact['status'];
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="view.php?id=<?php echo $contact['id']; ?>" 
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-800 rounded-lg transition-colors duration-200" 
                                       title="ดู">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $contact['id']; ?>" 
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

