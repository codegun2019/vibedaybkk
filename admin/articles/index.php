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
<div class="card">
    <div class="card-body">
        <?php if (empty($articles)): ?>
            <div class="text-center py-5">
                <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">ไม่พบบทความ</h5>
                <a href="add.php" class="btn btn-primary mt-3">
                    <i class="fas fa-plus-circle me-2"></i>เพิ่มบทความใหม่
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="80">รูปภาพ</th>
                            <th>หัวข้อ</th>
                            <th>หมวดหมู่</th>
                            <th>ผู้เขียน</th>
                            <th class="text-center">Views</th>
                            <th>วันที่</th>
                            <th>สถานะ</th>
                            <th width="120" class="text-center">การกระทำ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $article): ?>
                        <tr>
                            <td>
                                <?php if ($article['featured_image']): ?>
                                    <img src="<?php echo UPLOADS_URL . '/' . $article['featured_image']; ?>" 
                                         class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-secondary text-white text-center" style="width: 60px; height: 60px; line-height: 60px; border-radius: 5px;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo truncate_text($article['title'], 50); ?></strong>
                                <br>
                                <small class="text-muted"><?php echo truncate_text($article['excerpt'], 60); ?></small>
                            </td>
                            <td><?php echo $article['category'] ?: '-'; ?></td>
                            <td><?php echo $article['author_name'] ?: '-'; ?></td>
                            <td class="text-center">
                                <i class="fas fa-eye text-muted"></i> <?php echo $article['view_count']; ?>
                            </td>
                            <td>
                                <?php echo format_date_thai($article['created_at'], 'd/m/Y'); ?>
                                <br>
                                <small class="text-muted"><?php echo time_ago($article['created_at']); ?></small>
                            </td>
                            <td><?php echo get_status_badge($article['status']); ?></td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="edit.php?id=<?php echo $article['id']; ?>" 
                                       class="btn btn-sm btn-warning" 
                                       title="แก้ไข">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $article['id']; ?>" 
                                       class="btn btn-sm btn-danger btn-delete" 
                                       title="ลบ">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

