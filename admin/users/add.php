<?php
/**
 * VIBEDAYBKK Admin - Add User
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check
require_permission('users', 'create');

$page_title = 'เพิ่มผู้ใช้ใหม่';
$current_page = 'users';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $username = clean_input($_POST['username']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validate
        if (empty($username)) $errors[] = 'กรุณากรอกชื่อผู้ใช้';
        if (empty($password)) $errors[] = 'กรุณากรอกรหัสผ่าน';
        if ($password !== $confirm_password) $errors[] = 'รหัสผ่านไม่ตรงกัน';
        if (strlen($password) < 6) $errors[] = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
        
        // Check duplicate username
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
            $stmt->execute();
        if ($stmt->fetch()) {
            $errors[] = 'ชื่อผู้ใช้นี้มีอยู่แล้ว';
        }
        
        if (empty($errors)) {
            $data = [
                'username' => $username,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'full_name' => clean_input($_POST['full_name']),
                'email' => clean_input($_POST['email']),
                'role' => $_POST['role'],
                'status' => $_POST['status']
            ];
            
            if (db_insert($conn, 'users', $data)) {
                $user_id = $conn->insert_id;
                log_activity($conn, $_SESSION['user_id'], 'create', 'users', $user_id);
                set_message('success', 'เพิ่มผู้ใช้สำเร็จ');
                redirect(ADMIN_URL . '/users/');
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
        <i class="fas fa-user-plus mr-3 text-green-600"></i>เพิ่มผู้ใช้ใหม่
    </h2>
    <a href="index.php" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
        <i class="fas fa-arrow-left mr-2"></i>กลับ
    </a>
</div>

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
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        ชื่อผู้ใช้ <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="username" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        ชื่อเต็ม <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="full_name" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    อีเมล <span class="text-red-600">*</span>
                </label>
                <input type="email" name="email" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        รหัสผ่าน <span class="text-red-600">*</span>
                    </label>
                    <input type="password" name="password" required minlength="6" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    <p class="text-sm text-gray-500 mt-2">อย่างน้อย 6 ตัวอักษร</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        ยืนยันรหัสผ่าน <span class="text-red-600">*</span>
                    </label>
                    <input type="password" name="confirm_password" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">บทบาท</label>
                    <select name="role" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <?php
                        $available_roles = get_available_roles();
                        foreach ($available_roles as $role_key => $role_info):
                        ?>
                        <option value="<?php echo $role_key; ?>">
                            <?php echo $role_info['display_name']; ?> (<?php echo ucfirst($role_key); ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">สถานะ</label>
                    <select name="status" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <option value="active">ใช้งาน</option>
                        <option value="inactive">ไม่ใช้งาน</option>
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
                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 font-medium">
            <i class="fas fa-save mr-2"></i>บันทึก
        </button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>


