<?php
/**
 * VIBEDAYBKK Admin - Edit Category
 * แก้ไขหมวดหมู่
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$page_title = 'แก้ไขหมวดหมู่';
$current_page = 'categories';

$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$category_id) {
    set_message('error', 'ไม่พบข้อมูลหมวดหมู่');
    redirect(ADMIN_URL . '/categories/');
}

$category = get_category($conn, $category_id);
if (!$category) {
    set_message('error', 'ไม่พบข้อมูลหมวดหมู่');
    redirect(ADMIN_URL . '/categories/');
}

// Get current requirements
$requirements = db_get_rows($conn, "SELECT * FROM model_requirements WHERE category_id = {$category_id} ORDER BY sort_order ASC");
$requirements_text = implode("\n", array_column($requirements, 'requirement'));

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $data = [
            'code' => clean_input($_POST['code']),
            'name' => clean_input($_POST['name']),
            'name_en' => clean_input($_POST['name_en']),
            'description' => $_POST['description'],
            'icon' => clean_input($_POST['icon']),
            'color' => clean_input($_POST['color']),
            'gender' => $_POST['gender'],
            'price_min' => !empty($_POST['price_min']) ? (float)$_POST['price_min'] : null,
            'price_max' => !empty($_POST['price_max']) ? (float)$_POST['price_max'] : null,
            'sort_order' => !empty($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0,
            'status' => $_POST['status']
        ];
        
        // Validate
        if (empty($data['code'])) $errors[] = 'กรุณากรอกรหัสหมวดหมู่';
        if (empty($data['name'])) $errors[] = 'กรุณากรอกชื่อหมวดหมู่';
        
        // Check duplicate code
        if (!empty($data['code'])) {
            $stmt = $pdo->prepare("SELECT id FROM categories WHERE code = ? AND id != ?");
            $stmt->execute([$data['code'], $category_id]);
            if ($stmt->fetch()) {
                $errors[] = 'รหัสหมวดหมู่นี้มีอยู่แล้ว';
            }
        }
        
        if (empty($errors)) {
            if (db_update($pdo, 'categories', $data, 'id = :id', ['id' => $category_id])) {
                
                // Update requirements
                $conn->query("DELETE FROM model_requirements WHERE category_id = {$category_id}");
                if (!empty($_POST['requirements'])) {
                    $requirements = explode("\n", $_POST['requirements']);
                    $sort = 1;
                    foreach ($requirements as $req) {
                        $req = trim($req);
                        if (!empty($req)) {
                            $req_data = [
                                'category_id' => $category_id,
                                'requirement' => $req,
                                'sort_order' => $sort++
                            ];
                            db_insert($pdo, 'model_requirements', $req_data);
                        }
                    }
                }
                
                log_activity($pdo, $_SESSION['user_id'], 'update', 'categories', $category_id, $category, $data);
                
                set_message('success', 'อัพเดทหมวดหมู่สำเร็จ');
                redirect(ADMIN_URL . '/categories/');
            } else {
                $errors[] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-edit me-2"></i>แก้ไขหมวดหมู่</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>กลับ
        </a>
    </div>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <h5><i class="fas fa-exclamation-circle me-2"></i>พบข้อผิดพลาด:</h5>
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
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>ข้อมูลหมวดหมู่</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">รหัสหมวดหมู่ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="code" value="<?php echo $category['code']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">เพศ</label>
                    <select class="form-select" name="gender">
                        <option value="all" <?php echo $category['gender'] == 'all' ? 'selected' : ''; ?>>ทั้งหมด</option>
                        <option value="female" <?php echo $category['gender'] == 'female' ? 'selected' : ''; ?>>หญิง</option>
                        <option value="male" <?php echo $category['gender'] == 'male' ? 'selected' : ''; ?>>ชาย</option>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">ชื่อหมวดหมู่ (ไทย) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="<?php echo $category['name']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ชื่อหมวดหมู่ (อังกฤษ)</label>
                    <input type="text" class="form-control" name="name_en" value="<?php echo $category['name_en']; ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">คำอธิบาย</label>
                <textarea class="form-control" name="description" rows="3"><?php echo $category['description']; ?></textarea>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-palette me-2"></i>การแสดงผล</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">ไอคอน Font Awesome</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas <?php echo $category['icon']; ?>"></i></span>
                        <input type="text" class="form-control" name="icon" value="<?php echo $category['icon']; ?>">
                    </div>
                    <small class="text-muted">เช่น: fa-female, fa-male, fa-camera</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">สี Gradient</label>
                    <input type="text" class="form-control" name="color" value="<?php echo $category['color']; ?>">
                    <small class="text-muted">Tailwind CSS gradient classes</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>ราคาและคุณสมบัติ</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">ราคาขั้นต่ำ (บาท/วัน)</label>
                    <input type="number" class="form-control" name="price_min" step="100" value="<?php echo $category['price_min']; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ราคาสูงสุด (บาท/วัน)</label>
                    <input type="number" class="form-control" name="price_max" step="100" value="<?php echo $category['price_max']; ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">คุณสมบัติที่ต้องการ (แยกแต่ละบรรทัด)</label>
                <textarea class="form-control" name="requirements" rows="5"><?php echo $requirements_text; ?></textarea>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-cog me-2"></i>การตั้งค่า</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">ลำดับการแสดง</label>
                    <input type="number" class="form-control" name="sort_order" value="<?php echo $category['sort_order']; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">สถานะ</label>
                    <select class="form-select" name="status">
                        <option value="active" <?php echo $category['status'] == 'active' ? 'selected' : ''; ?>>ใช้งาน</option>
                        <option value="inactive" <?php echo $category['status'] == 'inactive' ? 'selected' : ''; ?>>ไม่ใช้งาน</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-end">
        <a href="index.php" class="btn btn-secondary me-2">
            <i class="fas fa-times me-2"></i>ยกเลิก
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>บันทึกการแก้ไข
        </button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>

