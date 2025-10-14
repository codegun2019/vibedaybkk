<?php
define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';
require_admin();

$page_title = 'แก้ไขผู้ใช้';
$current_page = 'users';

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$user_id) redirect(ADMIN_URL . '/users/');

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if (!$user) redirect(ADMIN_URL . '/users/');

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $data = [
            'full_name' => clean_input($_POST['full_name']),
            'email' => clean_input($_POST['email']),
            'role' => $_POST['role'],
            'status' => $_POST['status']
        ];
        
        // Change password if provided
        if (!empty($_POST['new_password'])) {
            if ($_POST['new_password'] === $_POST['confirm_password']) {
                if (strlen($_POST['new_password']) >= 6) {
                    $data['password'] = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
                } else {
                    $errors[] = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
                }
            } else {
                $errors[] = 'รหัสผ่านไม่ตรงกัน';
            }
        }
        
        if (empty($errors)) {
            if (db_update($pdo, 'users', $data, 'id = :id', ['id' => $user_id])) {
                log_activity($pdo, $_SESSION['user_id'], 'update', 'users', $user_id);
                $success = true;
                
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
            } else {
                $errors[] = 'เกิดข้อผิดพลาด';
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h2 class="text-3xl font-bold text-gray-900 flex items-center">
        <i class="fas fa-edit mr-3 text-blue-600"></i>แก้ไขผู้ใช้
    </h2>
    <a href="index.php" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
        <i class="fas fa-arrow-left mr-2"></i>กลับ
    </a>
</div>

<?php if ($success): ?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
    <div class="flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <span>บันทึกข้อมูลเรียบร้อยแล้ว</span>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
    <ul class="list-disc list-inside">
        <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-user mr-3"></i>ข้อมูลผู้ใช้
            </h5>
        </div>
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ชื่อผู้ใช้</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled 
                           class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed">
                    <p class="text-sm text-gray-500 mt-2">ไม่สามารถเปลี่ยนได้</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        ชื่อเต็ม <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    อีเมล <span class="text-red-600">*</span>
                </label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-yellow-600 to-yellow-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-key mr-3"></i>เปลี่ยนรหัสผ่าน (ถ้าต้องการ)
            </h5>
        </div>
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">รหัสผ่านใหม่</label>
                    <input type="password" name="new_password" minlength="6" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-200">
                    <p class="text-sm text-gray-500 mt-2">อย่างน้อย 6 ตัวอักษร (เว้นว่างถ้าไม่เปลี่ยน)</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ยืนยันรหัสผ่าน</label>
                    <input type="password" name="confirm_password" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-200">
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-cog mr-3"></i>การตั้งค่า
            </h5>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">บทบาท</label>
                    <select name="role" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                        <option value="editor" <?php echo $user['role'] == 'editor' ? 'selected' : ''; ?>>ผู้แก้ไข (Editor)</option>
                        <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>ผู้ดูแลระบบ (Admin)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">สถานะ</label>
                    <select name="status" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                        <option value="active" <?php echo $user['status'] == 'active' ? 'selected' : ''; ?>>ใช้งาน</option>
                        <option value="inactive" <?php echo $user['status'] == 'inactive' ? 'selected' : ''; ?>>ไม่ใช้งาน</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <div class="flex justify-end space-x-4">
        <a href="index.php" 
           class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-times mr-2"></i>ยกเลิก
        </a>
        <button type="submit" 
                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 font-medium">
            <i class="fas fa-save mr-2"></i>บันทึกการเปลี่ยนแปลง
        </button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>
