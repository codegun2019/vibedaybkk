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

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-bars mr-3 text-red-600"></i>จัดการเมนู
        </h2>
        <p class="text-gray-600 mt-1">จำนวนเมนูทั้งหมด: <?php echo count($menus); ?> รายการ</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="add.php" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-plus-circle mr-2"></i>เพิ่มเมนูใหม่
        </a>
    </div>
</div>

<!-- Menus Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="p-6">
        <?php if (empty($menus)): ?>
            <div class="text-center py-12">
                <i class="fas fa-bars text-6xl text-gray-400 mb-4"></i>
                <h5 class="text-gray-600 text-xl font-medium mb-4">ยังไม่มีเมนู</h5>
                <a href="add.php" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-plus-circle mr-2"></i>เพิ่มเมนูใหม่
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

