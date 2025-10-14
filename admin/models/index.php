<?php
/**
 * VIBEDAYBKK Admin - Models List
 * รายการโมเดลทั้งหมด
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$page_title = 'จัดการโมเดล';
$current_page = 'models';

// ค้นหาและกรอง
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';
$status_filter = $_GET['status'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = ADMIN_ITEMS_PER_PAGE;
$offset = ($page - 1) * $per_page;

// Build query
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(m.name LIKE ? OR m.code LIKE ? OR m.description LIKE ?)";
    $search_term = "%{$search}%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

if (!empty($category_filter)) {
    $where[] = "m.category_id = ?";
    $params[] = $category_filter;
}

if (!empty($status_filter)) {
    $where[] = "m.status = ?";
    $params[] = $status_filter;
}

$where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Count total
$count_sql = "SELECT COUNT(*) as total FROM models m {$where_sql}";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_models = $stmt->fetch()['total'];
$total_pages = ceil($total_models / $per_page);

// Get models
$sql = "SELECT m.*, c.name as category_name, c.code as category_code,
        (SELECT COUNT(*) FROM model_images WHERE model_id = m.id) as image_count
        FROM models m
        LEFT JOIN categories c ON m.category_id = c.id
        {$where_sql}
        ORDER BY m.featured DESC, m.sort_order ASC, m.created_at DESC
        LIMIT ? OFFSET ?";

$params[] = $per_page;
$params[] = $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$models = $stmt->fetchAll();

// Get categories for filter
$categories = get_categories($conn);

include '../includes/header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-users mr-3 text-red-600"></i>จัดการโมเดล
        </h2>
        <p class="text-gray-600 mt-1">จำนวนโมเดลทั้งหมด: <?php echo $total_models; ?> คน</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="add.php" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-plus-circle mr-2"></i>เพิ่มโมเดลใหม่
        </a>
    </div>
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <form method="GET" action="" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">ค้นหา</label>
            <input type="text" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" 
                   name="search" 
                   placeholder="ชื่อ, รหัส, คำอธิบาย..." 
                   value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">หมวดหมู่</label>
            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="category">
                <option value="">ทั้งหมด</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo $category_filter == $cat['id'] ? 'selected' : ''; ?>>
                    <?php echo $cat['name']; ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">สถานะ</label>
            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="status">
                <option value="">ทั้งหมด</option>
                <option value="available" <?php echo $status_filter == 'available' ? 'selected' : ''; ?>>ว่าง</option>
                <option value="busy" <?php echo $status_filter == 'busy' ? 'selected' : ''; ?>>ไม่ว่าง</option>
                <option value="inactive" <?php echo $status_filter == 'inactive' ? 'selected' : ''; ?>>ไม่ใช้งาน</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 font-medium">
                <i class="fas fa-search mr-2"></i>ค้นหา
            </button>
        </div>
    </form>
</div>

<!-- Models Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="p-6">
        <?php if (empty($models)): ?>
            <div class="text-center py-12">
                <i class="fas fa-user-slash text-6xl text-gray-400 mb-4"></i>
                <h5 class="text-gray-600 text-xl font-medium mb-4">ไม่พบข้อมูลโมเดล</h5>
                <a href="add.php" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-plus-circle mr-2"></i>เพิ่มโมเดลใหม่
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full" id="modelsTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">รูปภาพ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">รหัส</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ชื่อ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">หมวดหมู่</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ส่วนสูง</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ราคา</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ประสบการณ์</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Featured</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">สถานะ</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">การกระทำ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($models as $model): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <?php 
                                $primary_img = get_primary_image($conn, $model['id']);
                                if ($primary_img): 
                                ?>
                                    <img src="<?php echo UPLOADS_URL . '/' . $primary_img['image_path']; ?>" 
                                         class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                                <?php else: ?>
                                    <div class="w-16 h-16 bg-gray-200 text-gray-500 flex items-center justify-center rounded-lg border border-gray-200">
                                        <i class="fas fa-user text-xl"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900"><?php echo $model['code']; ?></div>
                                <div class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-eye mr-1"></i><?php echo $model['view_count']; ?>
                                    <i class="fas fa-calendar-check ml-2 mr-1"></i><?php echo $model['booking_count']; ?>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900"><?php echo $model['name']; ?></div>
                                <?php if ($model['name_en']): ?>
                                    <div class="text-sm text-gray-500"><?php echo $model['name_en']; ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <?php echo $model['category_name']; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo $model['height'] ? $model['height'] . ' cm' : '-'; ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <?php if ($model['price_min'] && $model['price_max']): ?>
                                    <div><?php echo format_price($model['price_min']); ?></div>
                                    <div class="text-gray-500">- <?php echo format_price($model['price_max']); ?></div>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo $model['experience_years'] ? $model['experience_years'] . ' ปี' : '-'; ?></td>
                            <td class="px-4 py-3 text-center">
                                <?php if ($model['featured']): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-star mr-1"></i> แนะนำ
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php 
                                $status_classes = [
                                    'available' => 'bg-green-100 text-green-800',
                                    'busy' => 'bg-yellow-100 text-yellow-800',
                                    'inactive' => 'bg-gray-100 text-gray-800'
                                ];
                                $status_texts = [
                                    'available' => 'ว่าง',
                                    'busy' => 'ไม่ว่าง',
                                    'inactive' => 'ไม่ใช้งาน'
                                ];
                                $status_class = $status_classes[$model['status']] ?? 'bg-gray-100 text-gray-800';
                                $status_text = $status_texts[$model['status']] ?? $model['status'];
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="edit.php?id=<?php echo $model['id']; ?>" 
                                       class="inline-flex items-center px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-lg transition-colors duration-200" 
                                       title="แก้ไข">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $model['id']; ?>" 
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

<?php
$extra_js = "<script>
$(document).ready(function() {
    // DataTables (optional - comment out pagination if using)
    // $('#modelsTable').DataTable();
});
</script>";

include '../includes/footer.php';
?>

