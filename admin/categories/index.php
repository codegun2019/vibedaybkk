<?php
/**
 * VIBEDAYBKK Admin - Categories List
 * รายการหมวดหมู่ทั้งหมด
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check
require_permission('categories', 'view');
$can_create = has_permission('categories', 'create');
$can_edit = has_permission('categories', 'edit');
$can_delete = has_permission('categories', 'delete');

$page_title = 'จัดการหมวดหมู่';
$current_page = 'categories';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = ADMIN_ITEMS_PER_PAGE;
$offset = ($page - 1) * $per_page;

// Count total
$total_categories = $conn->query("SELECT COUNT(*) as total FROM categories")->fetch_assoc()['total'];
$total_pages = ceil($total_categories / $per_page);

// Get categories with pagination
$sql = "SELECT c.*, 
        (SELECT COUNT(*) FROM models WHERE category_id = c.id) as model_count
        FROM categories c
        ORDER BY c.sort_order ASC, c.name ASC
        LIMIT {$per_page} OFFSET {$offset}";
$categories = db_get_rows($conn, $sql);

include '../includes/header.php';
require_once '../includes/readonly-notice.php';
?>

<?php if (!$can_create && !$can_edit && !$can_delete): ?>
    <?php show_readonly_notice('หมวดหมู่'); ?>
<?php endif; ?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-th-large mr-3 text-red-600"></i>จัดการหมวดหมู่
            <?php if (!$can_edit && !$can_delete): ?>
            <span class="ml-3 text-lg text-yellow-600">
                <i class="fas fa-eye"></i> ดูอย่างเดียว
            </span>
            <?php endif; ?>
        </h2>
        <p class="text-gray-600 mt-1">จำนวนหมวดหมู่ทั้งหมด: <?php echo count($categories); ?> หมวด</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <?php if ($can_create): ?>
        <a href="add.php" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-plus-circle mr-2"></i>เพิ่มหมวดหมู่ใหม่
        </a>
        <?php else: ?>
        <button disabled class="inline-flex items-center px-6 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed opacity-60">
            <i class="fas fa-lock mr-2"></i>ไม่มีสิทธิ์เพิ่ม
        </button>
        <?php endif; ?>
    </div>
</div>

<!-- Categories Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="p-6">
        <?php if (empty($categories)): ?>
            <div class="text-center py-12">
                <i class="fas fa-folder-open text-6xl text-gray-400 mb-4"></i>
                <h5 class="text-gray-600 text-xl font-medium mb-4">ยังไม่มีหมวดหมู่</h5>
                <a href="add.php" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-plus-circle mr-2"></i>เพิ่มหมวดหมู่ใหม่
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ไอคอน</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">รหัส</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ชื่อหมวดหมู่</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ชื่ออังกฤษ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">เพศ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ช่วงราคา</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">จำนวนโมเดล</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ลำดับ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">สถานะ</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">การกระทำ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($categories as $cat): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="w-12 h-12 bg-gradient-to-br <?php echo $cat['color']; ?> text-white flex items-center justify-center rounded-lg">
                                    <i class="fas <?php echo $cat['icon']; ?>"></i>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900"><?php echo $cat['code']; ?></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900"><?php echo $cat['name']; ?></div>
                                <?php if ($cat['description']): ?>
                                    <div class="text-sm text-gray-500 mt-1"><?php echo truncate_text($cat['description'], 50); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo $cat['name_en']; ?></td>
                            <td>
                                <?php
                                $gender_badges = [
                                    'female' => '<span class="badge bg-pink">หญิง</span>',
                                    'male' => '<span class="badge bg-primary">ชาย</span>',
                                    'all' => '<span class="badge bg-secondary">ทั้งหมด</span>'
                                ];
                                echo $gender_badges[$cat['gender']] ?? '';
                                ?>
                            </td>
                            <td>
                                <?php if ($cat['price_min'] && $cat['price_max']): ?>
                                    <?php echo format_price($cat['price_min']); ?> - <?php echo format_price($cat['price_max']); ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?php echo $cat['model_count']; ?> คน
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo $cat['sort_order']; ?></td>
                            <td class="px-4 py-3">
                                <?php 
                                $status_classes = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'inactive' => 'bg-gray-100 text-gray-800'
                                ];
                                $status_texts = [
                                    'active' => 'ใช้งาน',
                                    'inactive' => 'ไม่ใช้งาน'
                                ];
                                $status_class = $status_classes[$cat['status']] ?? 'bg-gray-100 text-gray-800';
                                $status_text = $status_texts[$cat['status']] ?? $cat['status'];
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="edit.php?id=<?php echo $cat['id']; ?>" 
                                       class="inline-flex items-center px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-lg transition-colors duration-200" 
                                       title="แก้ไข">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <?php if ($cat['model_count'] == 0): ?>
                                    <a href="delete.php?id=<?php echo $cat['id']; ?>" 
                                       class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-800 rounded-lg transition-colors duration-200 btn-delete" 
                                       title="ลบ">
                                        <i class="fas fa-trash text-sm"></i>
                                    </a>
                                    <?php else: ?>
                                    <button class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed" disabled title="มีโมเดลอยู่ในหมวดหมู่นี้">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Pagination
require_once '../includes/pagination.php';
render_pagination($page, $total_pages, 'index.php', []);
?>

<?php
$extra_css = "<style>
.bg-gradient-to-br {
    background: linear-gradient(135deg, #DC2626 0%, #EF4444 100%);
}
.badge.bg-pink {
    background-color: #EC4899 !important;
}
</style>";

include '../includes/footer.php';
?>



