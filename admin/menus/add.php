<?php
/**
 * VIBEDAYBKK Admin - Add Menu
 * เพิ่มเมนูใหม่
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$page_title = 'เพิ่มเมนูใหม่';
$current_page = 'menus';

$errors = [];

// Get parent menus
$parent_menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL ORDER BY sort_order ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $data = [
            'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
            'title' => clean_input($_POST['title']),
            'url' => clean_input($_POST['url']),
            'icon' => clean_input($_POST['icon']),
            'target' => $_POST['target'],
            'sort_order' => (int)$_POST['sort_order'],
            'status' => $_POST['status']
        ];
        
        if (empty($data['title'])) $errors[] = 'กรุณากรอกชื่อเมนู';
        if (empty($data['url'])) $errors[] = 'กรุณากรอก URL';
        
        if (empty($errors)) {
            if (db_insert($pdo, 'menus', $data)) {
                $menu_id = $pdo->lastInsertId();
                log_activity($pdo, $_SESSION['user_id'], 'create', 'menus', $menu_id, null, $data);
                set_message('success', 'เพิ่มเมนูสำเร็จ');
                redirect(ADMIN_URL . '/menus/');
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
        <h2><i class="fas fa-plus-circle me-2"></i>เพิ่มเมนูใหม่</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>กลับ
        </a>
    </div>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">ชื่อเมนู <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">URL <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="url" required placeholder="index.html หรือ #section">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">เมนูหลัก (Parent)</label>
                    <select class="form-select" name="parent_id">
                        <option value="">ไม่มี (เมนูหลัก)</option>
                        <?php foreach ($parent_menus as $parent): ?>
                        <option value="<?php echo $parent['id']; ?>"><?php echo $parent['title']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ไอคอน</label>
                    <input type="text" class="form-control" name="icon" placeholder="fa-home">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Target</label>
                    <select class="form-select" name="target">
                        <option value="_self">เปิดหน้าเดิม (_self)</option>
                        <option value="_blank">เปิดหน้าใหม่ (_blank)</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">ลำดับ</label>
                    <input type="number" class="form-control" name="sort_order" value="0">
                </div>
                <div class="col-md-4 mb-3">
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

