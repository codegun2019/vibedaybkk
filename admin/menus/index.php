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
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ไอคอน</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ชื่อเมนู</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">URL</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">เมนูหลัก</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Target</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ลำดับ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">สถานะ</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">การกระทำ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($menus as $menu): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <?php if ($menu['icon']): ?>
                                <i class="fas <?php echo $menu['icon']; ?> text-lg text-gray-600"></i>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <?php if ($menu['parent_id']): ?>
                                        <span class="text-gray-400 mr-3">└─</span>
                                    <?php endif; ?>
                                    <span class="font-semibold text-gray-900"><?php echo $menu['title']; ?></span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <code class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm"><?php echo $menu['url']; ?></code>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo $menu['parent_title'] ?: '-'; ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <?php echo $menu['target']; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo $menu['sort_order']; ?></td>
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
                                $status_class = $status_classes[$menu['status']] ?? 'bg-gray-100 text-gray-800';
                                $status_text = $status_texts[$menu['status']] ?? $menu['status'];
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="edit.php?id=<?php echo $menu['id']; ?>" 
                                       class="inline-flex items-center px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-lg transition-colors duration-200" 
                                       title="แก้ไข">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $menu['id']; ?>" 
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
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

