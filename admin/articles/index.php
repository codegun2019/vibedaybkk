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

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-newspaper me-2"></i>จัดการบทความ</h2>
        <p class="text-muted">จำนวนบทความทั้งหมด: <?php echo $total_articles; ?> บทความ</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="add.php" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>เพิ่มบทความใหม่
        </a>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" class="form-control" name="search" placeholder="ค้นหาบทความ..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-4">
                <select class="form-select" name="status">
                    <option value="">ทุกสถานะ</option>
                    <option value="draft" <?php echo $status_filter == 'draft' ? 'selected' : ''; ?>>แบบร่าง</option>
                    <option value="published" <?php echo $status_filter == 'published' ? 'selected' : ''; ?>>เผยแพร่แล้ว</option>
                    <option value="archived" <?php echo $status_filter == 'archived' ? 'selected' : ''; ?>>เก็บถาวร</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> ค้นหา
                </button>
            </div>
        </form>
    </div>
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

