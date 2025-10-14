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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'full_name' => clean_input($_POST['full_name']),
        'email' => clean_input($_POST['email']),
        'role' => $_POST['role'],
        'status' => $_POST['status']
    ];
    
    // Change password if provided
    if (!empty($_POST['new_password'])) {
        if ($_POST['new_password'] === $_POST['confirm_password']) {
            $data['password'] = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        } else {
            $errors[] = 'รหัสผ่านไม่ตรงกัน';
        }
    }
    
    if (empty($errors)) {
        if (db_update($pdo, 'users', $data, 'id = :id', ['id' => $user_id])) {
            log_activity($pdo, $_SESSION['user_id'], 'update', 'users', $user_id);
            set_message('success', 'อัพเดทข้อมูลสำเร็จ');
            redirect(ADMIN_URL . '/users/');
        }
    }
}

include '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6"><h2><i class="fas fa-edit me-2"></i>แก้ไขผู้ใช้</h2></div>
    <div class="col-md-6 text-end"><a href="index.php" class="btn btn-secondary">กลับ</a></div>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger"><?php foreach ($errors as $error): ?><div><?php echo $error; ?></div><?php endforeach; ?></div>
<?php endif; ?>

<form method="POST">
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">ชื่อผู้ใช้</label>
                    <input type="text" class="form-control" value="<?php echo $user['username']; ?>" disabled>
                    <small class="text-muted">ไม่สามารถเปลี่ยนได้</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ชื่อเต็ม</label>
                    <input type="text" class="form-control" name="full_name" value="<?php echo $user['full_name']; ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">อีเมล</label>
                <input type="email" class="form-control" name="email" value="<?php echo $user['email']; ?>" required>
            </div>
            
            <hr>
            <h5>เปลี่ยนรหัสผ่าน (ถ้าต้องการ)</h5>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">รหัสผ่านใหม่</label>
                    <input type="password" class="form-control" name="new_password" minlength="6">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ยืนยันรหัสผ่าน</label>
                    <input type="password" class="form-control" name="confirm_password">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">บทบาท</label>
                    <select class="form-select" name="role">
                        <option value="editor" <?php echo $user['role'] == 'editor' ? 'selected' : ''; ?>>ผู้แก้ไข</option>
                        <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>ผู้ดูแลระบบ</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">สถานะ</label>
                    <select class="form-select" name="status">
                        <option value="active" <?php echo $user['status'] == 'active' ? 'selected' : ''; ?>>ใช้งาน</option>
                        <option value="inactive" <?php echo $user['status'] == 'inactive' ? 'selected' : ''; ?>>ไม่ใช้งาน</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-end">
        <a href="index.php" class="btn btn-secondary me-2">ยกเลิก</a>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>บันทึก</button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>

