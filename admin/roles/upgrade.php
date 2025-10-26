<?php
/**
 * VIBEDAYBKK Admin - Upgrade Role
 * อัพเกรดบทบาทผู้ใช้
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

if (!is_admin()) {
    set_message('error', 'คุณไม่มีสิทธิ์');
    redirect(ADMIN_URL . '/index.php');
}

$page_title = 'อัพเกรดบทบาท';
$current_page = 'roles';

// Get all roles
$roles = db_get_rows($conn, "SELECT * FROM roles WHERE is_active = 1 ORDER BY level ASC");

include '../includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-level-up-alt mr-3 text-red-600"></i>อัพเกรดบทบาท
        </h2>
        <p class="text-gray-600 mt-2">เลือกบทบาทที่คุณต้องการอัพเกรด</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php foreach ($roles as $role): ?>
        <?php if ($role['role_key'] == 'viewer') continue; // Skip viewer ?>
        
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r <?php echo $role['color']; ?> p-6 text-white">
                <div class="flex items-center mb-4">
                    <i class="fas <?php echo $role['icon']; ?> text-3xl mr-4"></i>
                    <div>
                        <h3 class="text-xl font-bold"><?php echo $role['display_name']; ?></h3>
                        <p class="text-white/80 text-sm">Level <?php echo $role['level']; ?></p>
                    </div>
                </div>
                <p class="text-white/90"><?php echo $role['description']; ?></p>
            </div>
            
            <div class="p-6">
                <div class="mb-6">
                    <div class="text-3xl font-bold text-gray-900 mb-2">
                        <?php echo $role['price'] > 0 ? '฿' . number_format($role['price']) : 'ฟรี'; ?>
                    </div>
                    <p class="text-gray-600">ราคาอัพเกรด</p>
                </div>
                
                <?php if ($role['role_key'] == $_SESSION['user_role']): ?>
                <button disabled class="w-full bg-gray-400 text-white px-4 py-3 rounded-lg cursor-not-allowed">
                    <i class="fas fa-check mr-2"></i>บทบาทปัจจุบัน
                </button>
                <?php elseif ($role['price'] > 0): ?>
                <button class="w-full bg-gradient-to-r <?php echo $role['color']; ?> hover:opacity-90 text-white px-4 py-3 rounded-lg font-semibold transition-all">
                    <i class="fas fa-shopping-cart mr-2"></i>อัพเกรด
                </button>
                <?php else: ?>
                <button class="w-full bg-gradient-to-r <?php echo $role['color']; ?> hover:opacity-90 text-white px-4 py-3 rounded-lg font-semibold">
                    <i class="fas fa-gift mr-2"></i>รับฟรี
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>




