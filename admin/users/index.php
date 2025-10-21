<?php
/**
 * VIBEDAYBKK Admin - Users List
 * จัดการผู้ใช้งาน (Admin Only)
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check - allow view for editor and above
require_permission('users', 'view');

$page_title = 'จัดการผู้ใช้';
$current_page = 'users';

// Check if user has edit/delete permissions
$can_create = has_permission('users', 'create');
$can_edit = has_permission('users', 'edit');
$can_delete = has_permission('users', 'delete');

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Count total
$total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$total_pages = ceil($total_users / $per_page);

// Get users with pagination
$users = db_get_rows($conn, "SELECT * FROM users ORDER BY created_at DESC LIMIT {$per_page} OFFSET {$offset}");

include '../includes/header.php';
require_once '../includes/readonly-notice.php';
?>

<?php if (!$can_edit && !$can_create && !$can_delete): ?>
    <?php show_readonly_notice('ผู้ใช้'); ?>
<?php endif; ?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-user-shield mr-3 text-red-600"></i>จัดการผู้ใช้
        </h2>
        <p class="text-gray-600 mt-1">จำนวนผู้ใช้: <?php echo count($users); ?> คน</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <?php if ($can_create): ?>
        <a href="add.php" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-user-plus mr-2"></i>เพิ่มผู้ใช้ใหม่
        </a>
        <?php else: ?>
        <button disabled class="inline-flex items-center px-6 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed opacity-60">
            <i class="fas fa-lock mr-2"></i>ไม่มีสิทธิ์เพิ่ม
        </button>
        <?php endif; ?>
    </div>
</div>

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ชื่อผู้ใช้</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ชื่อเต็ม</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">อีเมล</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">บทบาท</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Login ล่าสุด</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">สถานะ</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">การกระทำ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo $user['id']; ?></td>
                        <td class="px-4 py-3">
                            <span class="font-semibold text-gray-900"><?php echo $user['username']; ?></span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo $user['full_name']; ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo $user['email']; ?></td>
                        <td class="px-4 py-3">
                            <?php
                            $role_info = get_user_role_info($user['role']);
                            $role_colors = [
                                'programmer' => 'bg-purple-100 text-purple-800',
                                'admin' => 'bg-red-100 text-red-800',
                                'editor' => 'bg-blue-100 text-blue-800',
                                'viewer' => 'bg-gray-100 text-gray-800'
                            ];
                            $role_color = $role_colors[$user['role']] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $role_color; ?>">
                                <i class="<?php echo $role_info['icon']; ?> mr-1"></i>
                                <?php echo $role_info['display_name']; ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            <?php if ($user['last_login']): ?>
                                <?php echo time_ago($user['last_login']); ?>
                            <?php else: ?>
                                <span class="text-gray-500">ยังไม่เคย</span>
                            <?php endif; ?>
                        </td>
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
                            $status_class = $status_classes[$user['status']] ?? 'bg-gray-100 text-gray-800';
                            $status_text = $status_texts[$user['status']] ?? $user['status'];
                            ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $status_class; ?>">
                                <?php echo $status_text; ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <div class="flex items-center justify-center space-x-2">
                                <?php if ($can_edit): ?>
                                <a href="edit.php?id=<?php echo $user['id']; ?>" 
                                   class="inline-flex items-center px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-lg transition-colors duration-200" 
                                   title="แก้ไข">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if ($can_delete): ?>
                                <a href="delete.php?id=<?php echo $user['id']; ?>" 
                                   class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-800 rounded-lg transition-colors duration-200 btn-delete" 
                                   title="ลบ">
                                    <i class="fas fa-trash text-sm"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (!$can_edit && !$can_delete): ?>
                                <span class="text-gray-400 text-sm">
                                    <i class="fas fa-eye"></i> ดูอย่างเดียว
                                </span>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-user-circle mr-1"></i>ตัวคุณเอง
                            </span>
                            <?php endif; ?>
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
        </div>
    </div>

<?php include '../includes/footer.php'; ?>



