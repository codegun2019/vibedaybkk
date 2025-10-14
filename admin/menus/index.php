<?php
/**
 * VIBEDAYBKK Admin - Menus List
 * รายการเมนูทั้งหมด
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$page_title = 'จัดการเมนู';
$current_page = 'menus';

// Get all menus
$sql = "SELECT m.*,
        (SELECT title FROM menus WHERE id = m.parent_id) as parent_title
        FROM menus m
        ORDER BY m.parent_id ASC, m.sort_order ASC";
$menus = db_get_rows($conn, $sql);

include '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-bars me-2"></i>จัดการเมนู</h2>
        <p class="text-muted">จำนวนเมนูทั้งหมด: <?php echo count($menus); ?> รายการ</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="add.php" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>เพิ่มเมนูใหม่
        </a>
    </div>
</div>

<!-- Menus Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($menus)): ?>
            <div class="text-center py-5">
                <i class="fas fa-bars fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">ยังไม่มีเมนู</h5>
                <a href="add.php" class="btn btn-primary mt-3">
                    <i class="fas fa-plus-circle me-2"></i>เพิ่มเมนูใหม่
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">ไอคอน</th>
                            <th>ชื่อเมนู</th>
                            <th>URL</th>
                            <th>เมนูหลัก</th>
                            <th>Target</th>
                            <th>ลำดับ</th>
                            <th>สถานะ</th>
                            <th width="120" class="text-center">การกระทำ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($menus as $menu): ?>
                        <tr>
                            <td>
                                <?php if ($menu['icon']): ?>
                                <i class="fas <?php echo $menu['icon']; ?> fa-lg"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($menu['parent_id']): ?>
                                    <span class="ms-3">└─</span>
                                <?php endif; ?>
                                <strong><?php echo $menu['title']; ?></strong>
                            </td>
                            <td><code><?php echo $menu['url']; ?></code></td>
                            <td><?php echo $menu['parent_title'] ?: '-'; ?></td>
                            <td>
                                <span class="badge bg-secondary"><?php echo $menu['target']; ?></span>
                            </td>
                            <td><?php echo $menu['sort_order']; ?></td>
                            <td><?php echo get_status_badge($menu['status']); ?></td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="edit.php?id=<?php echo $menu['id']; ?>" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $menu['id']; ?>" 
                                       class="btn btn-sm btn-danger btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
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

<?php include '../includes/footer.php'; ?>

