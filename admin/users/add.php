<?php
/**
 * VIBEDAYBKK Admin - Add User
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_admin();

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
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
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
            
            if (db_insert($pdo, 'users', $data)) {
                $user_id = $pdo->lastInsertId();
                log_activity($pdo, $_SESSION['user_id'], 'create', 'users', $user_id);
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

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-user-plus me-2"></i>เพิ่มผู้ใช้ใหม่</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php" class="btn btn-secondary">กลับ</a>
    </div>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul class="mb-0"><?php foreach ($errors as $error): ?><li><?php echo $error; ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">ชื่อผู้ใช้ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="username" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ชื่อเต็ม <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="full_name" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">อีเมล <span class="text-danger">*</span></label>
                <input type="email" class="form-control" name="email" required>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">รหัสผ่าน <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" name="password" required minlength="6">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ยืนยันรหัสผ่าน <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" name="confirm_password" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">บทบาท</label>
                    <select class="form-select" name="role">
                        <option value="editor">ผู้แก้ไข (Editor)</option>
                        <option value="admin">ผู้ดูแลระบบ (Admin)</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">สถานะ</label>
                    <select class="form-select" name="status">
                        <option value="active">ใช้งาน</option>
                        <option value="inactive">ไม่ใช้งาน</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-end">
        <a href="index.php" class="btn btn-secondary me-2">ยกเลิก</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>บันทึก
        </button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>

