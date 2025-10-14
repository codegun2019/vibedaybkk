<?php
/**
 * VIBEDAYBKK Admin - Add Model
 * เพิ่มโมเดลใหม่
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$page_title = 'เพิ่มโมเดลใหม่';
$current_page = 'models';

$errors = [];
$success = false;

// Get categories
$categories = get_categories($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        // Collect data
        $data = [
            'category_id' => (int)$_POST['category_id'],
            'code' => clean_input($_POST['code']),
            'name' => clean_input($_POST['name']),
            'name_en' => clean_input($_POST['name_en']),
            'description' => $_POST['description'],
            'price_min' => !empty($_POST['price_min']) ? (float)$_POST['price_min'] : null,
            'price_max' => !empty($_POST['price_max']) ? (float)$_POST['price_max'] : null,
            'height' => !empty($_POST['height']) ? (int)$_POST['height'] : null,
            'weight' => !empty($_POST['weight']) ? (int)$_POST['weight'] : null,
            'bust' => !empty($_POST['bust']) ? (int)$_POST['bust'] : null,
            'waist' => !empty($_POST['waist']) ? (int)$_POST['waist'] : null,
            'hips' => !empty($_POST['hips']) ? (int)$_POST['hips'] : null,
            'experience_years' => !empty($_POST['experience_years']) ? (int)$_POST['experience_years'] : 0,
            'age' => !empty($_POST['age']) ? (int)$_POST['age'] : null,
            'skin_tone' => clean_input($_POST['skin_tone']),
            'hair_color' => clean_input($_POST['hair_color']),
            'eye_color' => clean_input($_POST['eye_color']),
            'languages' => clean_input($_POST['languages']),
            'skills' => $_POST['skills'],
            'featured' => isset($_POST['featured']) ? 1 : 0,
            'status' => $_POST['status'],
            'sort_order' => !empty($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0,
        ];
        
        // Validate
        if (empty($data['category_id'])) {
            $errors[] = 'กรุณาเลือกหมวดหมู่';
        }
        if (empty($data['code'])) {
            $errors[] = 'กรุณากรอกรหัสโมเดล';
        }
        if (empty($data['name'])) {
            $errors[] = 'กรุณากรอกชื่อโมเดล';
        }
        
        // Check duplicate code
        if (!empty($data['code'])) {
            $stmt = $pdo->prepare("SELECT id FROM models WHERE code = ?");
            $stmt->execute([$data['code']]);
            if ($stmt->fetch()) {
                $errors[] = 'รหัสโมเดลนี้มีอยู่แล้ว';
            }
        }
        
        // If no errors, insert
        if (empty($errors)) {
            if (db_insert($pdo, 'models', $data)) {
                $model_id = $pdo->lastInsertId();
                
                // Handle image upload
                if (!empty($_FILES['images']['name'][0])) {
                    foreach ($_FILES['images']['name'] as $key => $name) {
                        if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                            $file = [
                                'name' => $_FILES['images']['name'][$key],
                                'type' => $_FILES['images']['type'][$key],
                                'tmp_name' => $_FILES['images']['tmp_name'][$key],
                                'error' => $_FILES['images']['error'][$key],
                                'size' => $_FILES['images']['size'][$key]
                            ];
                            
                            $upload_result = upload_image($file, 'models');
                            if ($upload_result['success']) {
                                $image_data = [
                                    'model_id' => $model_id,
                                    'image_path' => $upload_result['path'],
                                    'image_type' => 'portfolio',
                                    'is_primary' => $key == 0 ? 1 : 0,
                                    'sort_order' => $key
                                ];
                                db_insert($pdo, 'model_images', $image_data);
                            }
                        }
                    }
                }
                
                // Log activity
                log_activity($pdo, $_SESSION['user_id'], 'create', 'models', $model_id, null, $data);
                
                set_message('success', 'เพิ่มโมเดลสำเร็จ');
                redirect(ADMIN_URL . '/models/edit.php?id=' . $model_id);
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
        <h2><i class="fas fa-user-plus me-2"></i>เพิ่มโมเดลใหม่</h2>
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

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
    <!-- Basic Info -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>ข้อมูลพื้นฐาน</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">หมวดหมู่ <span class="text-danger">*</span></label>
                    <select class="form-select" name="category_id" required>
                        <option value="">เลือกหมวดหมู่</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">รหัสโมเดล <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="code" required placeholder="เช่น FM001">
                    <small class="text-muted">รหัสเฉพาะของโมเดล (ไม่ซ้ำกัน)</small>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">ชื่อ (ไทย) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ชื่อ (อังกฤษ)</label>
                    <input type="text" class="form-control" name="name_en">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">คำอธิบาย</label>
                <textarea class="form-control" name="description" rows="4" placeholder="รายละเอียดเกี่ยวกับโมเดล..."></textarea>
            </div>
        </div>
    </div>
    
    <!-- Physical Info -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-ruler me-2"></i>ข้อมูลรูปร่าง</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">ส่วนสูง (cm)</label>
                    <input type="number" class="form-control" name="height" placeholder="170">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">น้ำหนัก (kg)</label>
                    <input type="number" class="form-control" name="weight" placeholder="50">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">อายุ (ปี)</label>
                    <input type="number" class="form-control" name="age" placeholder="25">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">ประสบการณ์ (ปี)</label>
                    <input type="number" class="form-control" name="experience_years" placeholder="5">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">รอบอก (inch)</label>
                    <input type="number" class="form-control" name="bust" placeholder="34">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">รอบเอว (inch)</label>
                    <input type="number" class="form-control" name="waist" placeholder="24">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">รอบสะโพก (inch)</label>
                    <input type="number" class="form-control" name="hips" placeholder="34">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">สีผิว</label>
                    <input type="text" class="form-control" name="skin_tone" placeholder="ขาว, สองสี, ขาวเหลือง">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">สีผม</label>
                    <input type="text" class="form-control" name="hair_color" placeholder="ดำ, น้ำตาล">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">สีตา</label>
                    <input type="text" class="form-control" name="eye_color" placeholder="น้ำตาล, ดำ">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Price & Skills -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>ราคาและความสามารถ</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">ราคาขั้นต่ำ (บาท/วัน)</label>
                    <input type="number" class="form-control" name="price_min" step="100" placeholder="3000">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ราคาสูงสุด (บาท/วัน)</label>
                    <input type="number" class="form-control" name="price_max" step="100" placeholder="5000">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">ภาษาที่พูดได้</label>
                <input type="text" class="form-control" name="languages" placeholder="ไทย, อังกฤษ, จีน">
            </div>
            
            <div class="mb-3">
                <label class="form-label">ความสามารถพิเศษ</label>
                <textarea class="form-control" name="skills" rows="3" placeholder="เดินแบบ, แสดง, ร้องเพลง, เต้น..."></textarea>
            </div>
        </div>
    </div>
    
    <!-- Images -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-images me-2"></i>รูปภาพ</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">อัพโหลดรูปภาพ</label>
                <input type="file" class="form-control" name="images[]" accept="image/*" multiple>
                <small class="text-muted">รูปแรกจะเป็นรูปหลัก, รองรับไฟล์: JPG, PNG, GIF, WEBP (สูงสุด 5MB)</small>
            </div>
        </div>
    </div>
    
    <!-- Settings -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-cog me-2"></i>การตั้งค่า</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">สถานะ</label>
                    <select class="form-select" name="status">
                        <option value="available">ว่าง</option>
                        <option value="busy">ไม่ว่าง</option>
                        <option value="inactive">ไม่ใช้งาน</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ลำดับการแสดง</label>
                    <input type="number" class="form-control" name="sort_order" value="0">
                </div>
            </div>
            
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="featured" id="featured">
                <label class="form-check-label" for="featured">
                    <i class="fas fa-star text-warning me-1"></i>โมเดลแนะนำ (Featured)
                </label>
            </div>
        </div>
    </div>
    
    <!-- Submit -->
    <div class="text-end">
        <a href="index.php" class="btn btn-secondary me-2">
            <i class="fas fa-times me-2"></i>ยกเลิก
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>บันทึก
        </button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>

