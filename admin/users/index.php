<?php
/**
 * VIBEDAYBKK Admin - Users List
 * จัดการผู้ใช้งาน (Admin Only)
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_admin(); // Only admin can access

$page_title = 'จัดการผู้ใช้';
$current_page = 'users';

// Get all users
$users = db_get_rows($conn, "SELECT * FROM users ORDER BY created_at DESC");

include '../includes/header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-user-shield mr-3 text-red-600"></i>จัดการผู้ใช้
        </h2>
        <p class="text-gray-600 mt-1">จำนวนผู้ใช้: <?php echo count($users); ?> คน</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="add.php" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-user-plus mr-2"></i>เพิ่มผู้ใช้ใหม่
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ชื่อผู้ใช้</th>
                        <th>ชื่อเต็ม</th>
                        <th>อีเมล</th>
                        <th>บทบาท</th>
                        <th>Login ล่าสุด</th>
                        <th>สถานะ</th>
                        <th width="120" class="text-center">การกระทำ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><strong><?php echo $user['username']; ?></strong></td>
                        <td><?php echo $user['full_name']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td>
                            <span class="badge <?php echo $user['role'] == 'admin' ? 'bg-danger' : 'bg-primary'; ?>">
                                <?php echo $user['role'] == 'admin' ? 'ผู้ดูแลระบบ' : 'ผู้แก้ไข'; ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($user['last_login']): ?>
                                <?php echo time_ago($user['last_login']); ?>
                            <?php else: ?>
                                <span class="text-muted">ยังไม่เคย</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo get_status_badge($user['status']); ?></td>
                        <td class="text-center">
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <div class="btn-group">
                                <a href="edit.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger btn-delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                            <?php else: ?>
                            <span class="badge bg-info">ตัวคุณเอง</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

