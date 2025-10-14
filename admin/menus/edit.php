<?php
/**
 * VIBEDAYBKK Admin - Edit Menu
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$page_title = 'แก้ไขเมนู';
$current_page = 'menus';

$menu_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$menu_id) {
    set_message('error', 'ไม่พบข้อมูล');
    redirect(ADMIN_URL . '/menus/');
}

$stmt = $pdo->prepare("SELECT * FROM menus WHERE id = ?");
$stmt->execute([$menu_id]);
$menu = $stmt->fetch();

if (!$menu) {
    set_message('error', 'ไม่พบข้อมูล');
    redirect(ADMIN_URL . '/menus/');
}

$parent_menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL AND id != {$menu_id} ORDER BY sort_order ASC");

$errors = [];

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
            if (db_update($pdo, 'menus', $data, 'id = :id', ['id' => $menu_id])) {
                log_activity($pdo, $_SESSION['user_id'], 'update', 'menus', $menu_id, $menu, $data);
                set_message('success', 'อัพเดทเมนูสำเร็จ');
                redirect(ADMIN_URL . '/menus/');
            } else {
                $errors[] = 'เกิดข้อผิดพลาด';
            }
        }
    }
    
    $stmt = $pdo->prepare("SELECT * FROM menus WHERE id = ?");
    $stmt->execute([$menu_id]);
    $menu = $stmt->fetch();
}

include '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-edit me-2"></i>แก้ไขเมนู</h2>
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
                    <label class="form-label">ชื่อเมนู <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" value="<?php echo $menu['title']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">URL <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="url" value="<?php echo $menu['url']; ?>" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">เมนูหลัก</label>
                    <select class="form-select" name="parent_id">
                        <option value="">ไม่มี (เมนูหลัก)</option>
                        <?php foreach ($parent_menus as $parent): ?>
                        <option value="<?php echo $parent['id']; ?>" <?php echo $menu['parent_id'] == $parent['id'] ? 'selected' : ''; ?>>
                            <?php echo $parent['title']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ไอคอน</label>
                    <input type="text" class="form-control" name="icon" value="<?php echo $menu['icon']; ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Target</label>
                    <select class="form-select" name="target">
                        <option value="_self" <?php echo $menu['target'] == '_self' ? 'selected' : ''; ?>>_self</option>
                        <option value="_blank" <?php echo $menu['target'] == '_blank' ? 'selected' : ''; ?>>_blank</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">ลำดับ</label>
                    <input type="number" class="form-control" name="sort_order" value="<?php echo $menu['sort_order']; ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">สถานะ</label>
                    <select class="form-select" name="status">
                        <option value="active" <?php echo $menu['status'] == 'active' ? 'selected' : ''; ?>>ใช้งาน</option>
                        <option value="inactive" <?php echo $menu['status'] == 'inactive' ? 'selected' : ''; ?>>ไม่ใช้งาน</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-end">
        <a href="index.php" class="btn btn-secondary me-2">ยกเลิก</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>บันทึกการแก้ไข
        </button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>

