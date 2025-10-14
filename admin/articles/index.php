<?php
/**
 * VIBEDAYBKK Admin - Articles List
 * รายการบทความทั้งหมด
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$page_title = 'จัดการบทความ';
$current_page = 'articles';

// Filter
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = ADMIN_ITEMS_PER_PAGE;
$offset = ($page - 1) * $per_page;

$where = [];
$params = [];

if (!empty($status_filter)) {
    $where[] = "status = ?";
    $params[] = $status_filter;
}

if (!empty($search)) {
    $where[] = "(title LIKE ? OR content LIKE ?)";
    $search_term = "%{$search}%";
    $params[] = $search_term;
    $params[] = $search_term;
}

$where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Count total
$count_sql = "SELECT COUNT(*) as total FROM articles {$where_sql}";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_articles = $stmt->fetch()['total'];
$total_pages = ceil($total_articles / $per_page);

// Get articles
$sql = "SELECT a.*, u.full_name as author_name
        FROM articles a
        LEFT JOIN users u ON a.author_id = u.id
        {$where_sql}
        ORDER BY a.created_at DESC
        LIMIT ? OFFSET ?";

$params[] = $per_page;
$params[] = $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-newspaper mr-3 text-red-600"></i>จัดการบทความ
        </h2>
        <p class="text-gray-600 mt-1">จำนวนบทความทั้งหมด: <?php echo $total_articles; ?> บทความ</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="add.php" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-plus-circle mr-2"></i>เพิ่มบทความใหม่
        </a>
    </div>
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <input type="text" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" 
                   name="search" 
                   placeholder="ค้นหาบทความ..." 
                   value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div>
            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="status">
                <option value="">ทุกสถานะ</option>
                <option value="draft" <?php echo $status_filter == 'draft' ? 'selected' : ''; ?>>แบบร่าง</option>
                <option value="published" <?php echo $status_filter == 'published' ? 'selected' : ''; ?>>เผยแพร่แล้ว</option>
                <option value="archived" <?php echo $status_filter == 'archived' ? 'selected' : ''; ?>>เก็บถาวร</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 font-medium">
                <i class="fas fa-search mr-2"></i> ค้นหา
            </button>
        </div>
    </form>
</div>

<!-- Articles Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="p-6">
        <?php if (empty($articles)): ?>
            <div class="text-center py-12">
                <i class="fas fa-newspaper text-6xl text-gray-400 mb-4"></i>
                <h5 class="text-gray-600 text-xl font-medium mb-4">ไม่พบบทความ</h5>
                <a href="add.php" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-plus-circle mr-2"></i>เพิ่มบทความใหม่
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">รูปภาพ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">หัวข้อ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">หมวดหมู่</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ผู้เขียน</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Views</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">วันที่</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">สถานะ</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">การกระทำ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($articles as $article): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <?php if ($article['featured_image']): ?>
                                    <img src="<?php echo UPLOADS_URL . '/' . $article['featured_image']; ?>" 
                                         class="w-15 h-15 rounded-lg object-cover">
                                <?php else: ?>
                                    <div class="w-15 h-15 bg-gray-200 text-gray-500 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-image text-lg"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900"><?php echo truncate_text($article['title'], 50); ?></div>
                                <div class="text-sm text-gray-600 mt-1"><?php echo truncate_text($article['excerpt'], 60); ?></div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo $article['category'] ?: '-'; ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo $article['author_name'] ?: '-'; ?></td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center text-sm text-gray-600">
                                    <i class="fas fa-eye mr-1"></i> <?php echo $article['view_count']; ?>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900"><?php echo format_date_thai($article['created_at'], 'd/m/Y'); ?></div>
                                <div class="text-xs text-gray-500"><?php echo time_ago($article['created_at']); ?></div>
                            </td>
                            <td class="px-4 py-3">
                                <?php 
                                $status_classes = [
                                    'draft' => 'bg-gray-100 text-gray-800',
                                    'published' => 'bg-green-100 text-green-800',
                                    'archived' => 'bg-red-100 text-red-800'
                                ];
                                $status_texts = [
                                    'draft' => 'แบบร่าง',
                                    'published' => 'เผยแพร่แล้ว',
                                    'archived' => 'เก็บถาวร'
                                ];
                                $status_class = $status_classes[$article['status']] ?? 'bg-gray-100 text-gray-800';
                                $status_text = $status_texts[$article['status']] ?? $article['status'];
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="edit.php?id=<?php echo $article['id']; ?>" 
                                       class="inline-flex items-center px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-lg transition-colors duration-200" 
                                       title="แก้ไข">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $article['id']; ?>" 
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

