<?php
/**
 * VIBEDAYBKK Admin - Categories List
 * รายการหมวดหมู่ทั้งหมด
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$page_title = 'จัดการหมวดหมู่';
$current_page = 'categories';

// Get all categories
$sql = "SELECT c.*, 
        (SELECT COUNT(*) FROM models WHERE category_id = c.id) as model_count
        FROM categories c
        ORDER BY c.sort_order ASC, c.name ASC";
$categories = db_get_rows($conn, $sql);

include '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-th-large me-2"></i>จัดการหมวดหมู่</h2>
        <p class="text-muted">จำนวนหมวดหมู่ทั้งหมด: <?php echo count($categories); ?> หมวด</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="add.php" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>เพิ่มหมวดหมู่ใหม่
        </a>
    </div>
</div>

<!-- Categories Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($categories)): ?>
            <div class="text-center py-5">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">ยังไม่มีหมวดหมู่</h5>
                <a href="add.php" class="btn btn-primary mt-3">
                    <i class="fas fa-plus-circle me-2"></i>เพิ่มหมวดหมู่ใหม่
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="80">ไอคอน</th>
                            <th>รหัส</th>
                            <th>ชื่อหมวดหมู่</th>
                            <th>ชื่ออังกฤษ</th>
                            <th>เพศ</th>
                            <th>ช่วงราคา</th>
                            <th class="text-center">จำนวนโมเดล</th>
                            <th>ลำดับ</th>
                            <th>สถานะ</th>
                            <th width="120" class="text-center">การกระทำ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td>
                                <div class="bg-gradient-to-br <?php echo $cat['color']; ?> text-white text-center rounded" 
                                     style="width: 50px; height: 50px; line-height: 50px;">
                                    <i class="fas <?php echo $cat['icon']; ?>"></i>
                                </div>
                            </td>
                            <td><strong><?php echo $cat['code']; ?></strong></td>
                            <td>
                                <strong><?php echo $cat['name']; ?></strong>
                                <?php if ($cat['description']): ?>
                                    <br><small class="text-muted"><?php echo truncate_text($cat['description'], 50); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $cat['name_en']; ?></td>
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
                            <td class="text-center">
                                <span class="badge bg-info"><?php echo $cat['model_count']; ?> คน</span>
                            </td>
                            <td><?php echo $cat['sort_order']; ?></td>
                            <td><?php echo get_status_badge($cat['status']); ?></td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="edit.php?id=<?php echo $cat['id']; ?>" 
                                       class="btn btn-sm btn-warning" 
                                       title="แก้ไข">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($cat['model_count'] == 0): ?>
                                    <a href="delete.php?id=<?php echo $cat['id']; ?>" 
                                       class="btn btn-sm btn-danger btn-delete" 
                                       title="ลบ">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled title="มีโมเดลอยู่ในหมวดหมู่นี้">
                                        <i class="fas fa-trash"></i>
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

