<?php
/**
 * VIBEDAYBKK Admin - Edit Model
 * แก้ไขข้อมูลโมเดล
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$page_title = 'แก้ไขโมเดล';
$current_page = 'models';

// Get model ID
$model_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$model_id) {
    set_message('error', 'ไม่พบข้อมูลโมเดล');
    redirect(ADMIN_URL . '/models/');
}

// Get model data
$model = get_model($conn, $model_id);
if (!$model) {
    set_message('error', 'ไม่พบข้อมูลโมเดล');
    redirect(ADMIN_URL . '/models/');
}

// Get model images
$model_images = get_model_images($conn, $model_id);

// Get categories
$categories = get_categories($conn);

$errors = [];

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
        if (empty($data['code'])) {
            $errors[] = 'กรุณากรอกรหัสโมเดล';
        }
        if (empty($data['name'])) {
            $errors[] = 'กรุณากรอกชื่อโมเดล';
        }
        
        // Check duplicate code (except current)
        if (!empty($data['code'])) {
            $stmt = $pdo->prepare("SELECT id FROM models WHERE code = ? AND id != ?");
            $stmt->execute([$data['code'], $model_id]);
            if ($stmt->fetch()) {
                $errors[] = 'รหัสโมเดลนี้มีอยู่แล้ว';
            }
        }
        
        // If no errors, update
        if (empty($errors)) {
            if (db_update($pdo, 'models', $data, 'id = :id', ['id' => $model_id])) {
                
                // Handle new image upload
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
                                    'is_primary' => empty($model_images) ? 1 : 0,
                                    'sort_order' => count($model_images)
                                ];
                                db_insert($pdo, 'model_images', $image_data);
                            }
                        }
                    }
                }
                
                // Log activity
                log_activity($pdo, $_SESSION['user_id'], 'update', 'models', $model_id, $model, $data);
                
                set_message('success', 'อัพเดทข้อมูลโมเดลสำเร็จ');
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
        <h2><i class="fas fa-edit me-2"></i>แก้ไขโมเดล: <?php echo $model['name']; ?></h2>
        <small class="text-muted">รหัส: <?php echo $model['code']; ?></small>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>กลับ
        </a>
        <a href="delete.php?id=<?php echo $model_id; ?>" class="btn btn-danger btn-delete">
            <i class="fas fa-trash me-2"></i>ลบโมเดล
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
                        <option value="<?php echo $cat['id']; ?>" <?php echo $model['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo $cat['name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">รหัสโมเดล <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="code" value="<?php echo $model['code']; ?>" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">ชื่อ (ไทย) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="<?php echo $model['name']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ชื่อ (อังกฤษ)</label>
                    <input type="text" class="form-control" name="name_en" value="<?php echo $model['name_en']; ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">คำอธิบาย</label>
                <textarea class="form-control" name="description" rows="4"><?php echo $model['description']; ?></textarea>
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
                    <input type="number" class="form-control" name="height" value="<?php echo $model['height']; ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">น้ำหนัก (kg)</label>
                    <input type="number" class="form-control" name="weight" value="<?php echo $model['weight']; ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">อายุ (ปี)</label>
                    <input type="number" class="form-control" name="age" value="<?php echo $model['age']; ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">ประสบการณ์ (ปี)</label>
                    <input type="number" class="form-control" name="experience_years" value="<?php echo $model['experience_years']; ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">รอบอก (inch)</label>
                    <input type="number" class="form-control" name="bust" value="<?php echo $model['bust']; ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">รอบเอว (inch)</label>
                    <input type="number" class="form-control" name="waist" value="<?php echo $model['waist']; ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">รอบสะโพก (inch)</label>
                    <input type="number" class="form-control" name="hips" value="<?php echo $model['hips']; ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">สีผิว</label>
                    <input type="text" class="form-control" name="skin_tone" value="<?php echo $model['skin_tone']; ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">สีผม</label>
                    <input type="text" class="form-control" name="hair_color" value="<?php echo $model['hair_color']; ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">สีตา</label>
                    <input type="text" class="form-control" name="eye_color" value="<?php echo $model['eye_color']; ?>">
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
                    <input type="number" class="form-control" name="price_min" step="100" value="<?php echo $model['price_min']; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ราคาสูงสุด (บาท/วัน)</label>
                    <input type="number" class="form-control" name="price_max" step="100" value="<?php echo $model['price_max']; ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">ภาษาที่พูดได้</label>
                <input type="text" class="form-control" name="languages" value="<?php echo $model['languages']; ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">ความสามารถพิเศษ</label>
                <textarea class="form-control" name="skills" rows="3"><?php echo $model['skills']; ?></textarea>
            </div>
        </div>
    </div>
    
    <!-- Current Images -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-images me-2"></i>รูปภาพปัจจุบัน</h5>
        </div>
        <div class="card-body">
            <?php if (empty($model_images)): ?>
                <p class="text-muted text-center py-3">ยังไม่มีรูปภาพ</p>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($model_images as $img): ?>
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <img src="<?php echo UPLOADS_URL . '/' . $img['image_path']; ?>" 
                                 class="card-img-top" 
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body p-2">
                                <?php if ($img['is_primary']): ?>
                                    <span class="badge bg-success w-100 mb-2">รูปหลัก</span>
                                <?php endif; ?>
                                <div class="d-grid gap-2">
                                    <?php if (!$img['is_primary']): ?>
                                    <a href="set-primary.php?id=<?php echo $img['id']; ?>&model_id=<?php echo $model_id; ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-star"></i> ตั้งเป็นรูปหลัก
                                    </a>
                                    <?php endif; ?>
                                    <a href="delete-image.php?id=<?php echo $img['id']; ?>&model_id=<?php echo $model_id; ?>" 
                                       class="btn btn-sm btn-danger btn-delete">
                                        <i class="fas fa-trash"></i> ลบ
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <hr>
            
            <div class="mb-3">
                <label class="form-label">อัพโหลดรูปภาพเพิ่ม</label>
                <input type="file" class="form-control" name="images[]" accept="image/*" multiple>
                <small class="text-muted">รองรับไฟล์: JPG, PNG, GIF, WEBP (สูงสุด 5MB)</small>
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
                        <option value="available" <?php echo $model['status'] == 'available' ? 'selected' : ''; ?>>ว่าง</option>
                        <option value="busy" <?php echo $model['status'] == 'busy' ? 'selected' : ''; ?>>ไม่ว่าง</option>
                        <option value="inactive" <?php echo $model['status'] == 'inactive' ? 'selected' : ''; ?>>ไม่ใช้งาน</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ลำดับการแสดง</label>
                    <input type="number" class="form-control" name="sort_order" value="<?php echo $model['sort_order']; ?>">
                </div>
            </div>
            
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="featured" id="featured" <?php echo $model['featured'] ? 'checked' : ''; ?>>
                <label class="form-check-label" for="featured">
                    <i class="fas fa-star text-warning me-1"></i>โมเดลแนะนำ (Featured)
                </label>
            </div>
        </div>
    </div>
    
    <!-- Statistics -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>สถิติ</h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3">
                    <h4><?php echo $model['view_count']; ?></h4>
                    <p class="text-muted mb-0">จำนวนครั้งที่ดู</p>
                </div>
                <div class="col-md-3">
                    <h4><?php echo $model['booking_count']; ?></h4>
                    <p class="text-muted mb-0">จำนวนการจอง</p>
                </div>
                <div class="col-md-3">
                    <h4><?php echo $model['rating']; ?>/5</h4>
                    <p class="text-muted mb-0">คะแนนเฉลี่ย</p>
                </div>
                <div class="col-md-3">
                    <h4><?php echo count($model_images); ?></h4>
                    <p class="text-muted mb-0">จำนวนรูปภาพ</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Submit -->
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

