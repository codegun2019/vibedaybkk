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

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-users me-2"></i>จัดการโมเดล</h2>
        <p class="text-muted">จำนวนโมเดลทั้งหมด: <?php echo $total_models; ?> คน</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="add.php" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>เพิ่มโมเดลใหม่
        </a>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">ค้นหา</label>
                <input type="text" class="form-control" name="search" placeholder="ชื่อ, รหัส, คำอธิบาย..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">หมวดหมู่</label>
                <select class="form-select" name="category">
                    <option value="">ทั้งหมด</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $category_filter == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo $cat['name']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">สถานะ</label>
                <select class="form-select" name="status">
                    <option value="">ทั้งหมด</option>
                    <option value="available" <?php echo $status_filter == 'available' ? 'selected' : ''; ?>>ว่าง</option>
                    <option value="busy" <?php echo $status_filter == 'busy' ? 'selected' : ''; ?>>ไม่ว่าง</option>
                    <option value="inactive" <?php echo $status_filter == 'inactive' ? 'selected' : ''; ?>>ไม่ใช้งาน</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>ค้นหา
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Models Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($models)): ?>
            <div class="text-center py-5">
                <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">ไม่พบข้อมูลโมเดล</h5>
                <a href="add.php" class="btn btn-primary mt-3">
                    <i class="fas fa-plus-circle me-2"></i>เพิ่มโมเดลใหม่
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="modelsTable">
                    <thead>
                        <tr>
                            <th width="80">รูปภาพ</th>
                            <th>รหัส</th>
                            <th>ชื่อ</th>
                            <th>หมวดหมู่</th>
                            <th>ส่วนสูง</th>
                            <th>ราคา</th>
                            <th>ประสบการณ์</th>
                            <th class="text-center">Featured</th>
                            <th>สถานะ</th>
                            <th width="120" class="text-center">การกระทำ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($models as $model): ?>
                        <tr>
                            <td>
                                <?php 
                                $primary_img = get_primary_image($conn, $model['id']);
                                if ($primary_img): 
                                ?>
                                    <img src="<?php echo UPLOADS_URL . '/' . $primary_img['image_path']; ?>" 
                                         class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-secondary text-white text-center" style="width: 60px; height: 60px; line-height: 60px; border-radius: 5px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo $model['code']; ?></strong>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-eye me-1"></i><?php echo $model['view_count']; ?>
                                    <i class="fas fa-calendar-check ms-2 me-1"></i><?php echo $model['booking_count']; ?>
                                </small>
                            </td>
                            <td>
                                <strong><?php echo $model['name']; ?></strong>
                                <?php if ($model['name_en']): ?>
                                    <br><small class="text-muted"><?php echo $model['name_en']; ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?php echo $model['category_name']; ?></span>
                            </td>
                            <td><?php echo $model['height'] ? $model['height'] . ' cm' : '-'; ?></td>
                            <td>
                                <?php if ($model['price_min'] && $model['price_max']): ?>
                                    <small><?php echo format_price($model['price_min']); ?><br>- <?php echo format_price($model['price_max']); ?></small>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?php echo $model['experience_years'] ? $model['experience_years'] . ' ปี' : '-'; ?></td>
                            <td class="text-center">
                                <?php if ($model['featured']): ?>
                                    <span class="badge bg-warning"><i class="fas fa-star"></i> แนะนำ</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo get_status_badge($model['status']); ?></td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="edit.php?id=<?php echo $model['id']; ?>" 
                                       class="btn btn-sm btn-warning" 
                                       title="แก้ไข">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $model['id']; ?>" 
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
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo ($page-1); ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>">
                            ก่อนหน้า
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo ($page+1); ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>">
                            ถัดไป
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
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

