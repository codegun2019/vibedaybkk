<?php
/**
 * VIBEDAYBKK Admin - Roles Management
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Only programmer can access
if (!is_programmer()) {
    set_message('error', 'เฉพาะโปรแกรมเมอร์เท่านั้นที่เข้าถึงได้');
    redirect(ADMIN_URL . '/index.php');
}

$page_title = 'จัดการ Roles & Permissions';
$current_page = 'roles';

// Get all roles
$roles = db_get_rows($conn, "SELECT * FROM roles ORDER BY level DESC");

// Get user count per role
$role_stats = [];
$result = $conn->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
while ($row = $result->fetch_assoc()) {
    $role_stats[$row['role']] = $row['count'];
}

include '../includes/header.php';
?>

<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-900 flex items-center">
        <i class="fas fa-user-shield mr-3 text-red-600"></i>จัดการ Roles & Permissions
    </h2>
    <p class="text-gray-600 mt-1">กำหนดสิทธิ์การเข้าถึงสำหรับแต่ละบทบาท</p>
</div>

<!-- Roles Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <?php foreach ($roles as $role): ?>
    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-shadow duration-300">
        <div class="bg-gradient-to-r <?php echo $role['color']; ?> p-6">
            <div class="text-white">
                <div class="flex items-center justify-between mb-4">
                    <i class="fas <?php echo $role['icon']; ?> text-4xl"></i>
                    <span class="text-2xl font-bold"><?php echo $role['level']; ?></span>
                </div>
                <h3 class="text-xl font-bold mb-2"><?php echo $role['display_name']; ?></h3>
                <p class="text-white/80 text-sm"><?php echo $role['description']; ?></p>
            </div>
        </div>
        
        <div class="p-6">
            <div class="space-y-3 mb-6">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">จำนวนผู้ใช้:</span>
                    <span class="font-semibold text-gray-900"><?php echo $role_stats[$role['role_key']] ?? 0; ?> คน</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">ราคา:</span>
                    <span class="font-semibold text-gray-900"><?php echo $role['price'] > 0 ? '฿' . number_format($role['price']) : 'ฟรี'; ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">สถานะ:</span>
                    <span class="<?php echo $role['is_active'] ? 'text-green-600' : 'text-gray-400'; ?> font-semibold">
                        <?php echo $role['is_active'] ? '✓ เปิดใช้งาน' : '✗ ปิด'; ?>
                    </span>
                </div>
            </div>
            
            <a href="edit.php?role=<?php echo $role['role_key']; ?>" 
               class="block w-full bg-gradient-to-r <?php echo $role['color']; ?> hover:opacity-90 text-white text-center px-4 py-3 rounded-lg transition-all duration-200 font-medium">
                <i class="fas fa-edit mr-2"></i>แก้ไขสิทธิ์
            </a>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Permission Summary -->
<div class="mt-8 bg-white rounded-xl shadow-lg p-6">
    <h3 class="text-xl font-bold text-gray-900 mb-4">
        <i class="fas fa-chart-bar mr-2 text-red-600"></i>สรุปสิทธิ์
    </h3>
    
    <?php
    $features = db_get_rows($conn, "SELECT DISTINCT feature FROM permissions ORDER BY feature");
    ?>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-red-600"><?php echo count($roles); ?></div>
            <div class="text-gray-600 mt-1">Roles</div>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-blue-600"><?php echo count($features); ?></div>
            <div class="text-gray-600 mt-1">Features</div>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <?php
            $totalPerms = $conn->query("SELECT COUNT(*) as c FROM permissions")->fetch_assoc()['c'];
            ?>
            <div class="text-3xl font-bold text-green-600"><?php echo $totalPerms; ?></div>
            <div class="text-gray-600 mt-1">Permissions</div>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <?php
            $totalUsers = $conn->query("SELECT COUNT(*) as c FROM users WHERE status = 'active'")->fetch_assoc()['c'];
            ?>
            <div class="text-3xl font-bold text-purple-600"><?php echo $totalUsers; ?></div>
            <div class="text-gray-600 mt-1">Active Users</div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>


