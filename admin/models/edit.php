<?php
/**
 * VIBEDAYBKK Admin - Edit Model
 * แก้ไขข้อมูลโมเดล
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';


// Permission check
require_permission('models', 'edit');
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

$errors = [];
$success = false;

// Get categories
$categories = get_categories($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        // ตรวจสอบฟิลด์ที่มีในตาราง models
        $columns_result = $conn->query("SHOW COLUMNS FROM models");
        $existing_columns = [];
        while ($col = $columns_result->fetch_assoc()) {
            $existing_columns[] = $col['Field'];
        }
        
        // Collect data - เฉพาะฟิลด์ที่มีในตาราง
        $data = [];
        
        // ฟิลด์พื้นฐาน (ต้องมีเสมอ)
        $data['category_id'] = (int)$_POST['category_id'];
        if (in_array('code', $existing_columns)) $data['code'] = clean_input($_POST['code']);
        $data['name'] = clean_input($_POST['name']);
        
        // ฟิลด์เสริม (ตรวจสอบว่ามีในตารางก่อน)
        if (in_array('name_en', $existing_columns)) $data['name_en'] = clean_input($_POST['name_en']);
        if (in_array('description', $existing_columns)) $data['description'] = $_POST['description'];
        if (in_array('price', $existing_columns)) $data['price'] = !empty($_POST['price']) ? (float)$_POST['price'] : null;
        if (in_array('price_min', $existing_columns)) $data['price_min'] = !empty($_POST['price_min']) ? (float)$_POST['price_min'] : null;
        if (in_array('price_max', $existing_columns)) $data['price_max'] = !empty($_POST['price_max']) ? (float)$_POST['price_max'] : null;
        if (in_array('height', $existing_columns)) $data['height'] = !empty($_POST['height']) ? (int)$_POST['height'] : null;
        if (in_array('weight', $existing_columns)) $data['weight'] = !empty($_POST['weight']) ? (int)$_POST['weight'] : null;
        if (in_array('bust', $existing_columns)) $data['bust'] = !empty($_POST['bust']) ? (int)$_POST['bust'] : null;
        if (in_array('waist', $existing_columns)) $data['waist'] = !empty($_POST['waist']) ? (int)$_POST['waist'] : null;
        if (in_array('hips', $existing_columns)) $data['hips'] = !empty($_POST['hips']) ? (int)$_POST['hips'] : null;
        if (in_array('experience_years', $existing_columns)) $data['experience_years'] = !empty($_POST['experience_years']) ? (int)$_POST['experience_years'] : 0;
        if (in_array('experience', $existing_columns)) $data['experience'] = $_POST['experience'] ?? '';
        if (in_array('portfolio', $existing_columns)) $data['portfolio'] = $_POST['portfolio'] ?? '';
        if (in_array('age', $existing_columns)) $data['age'] = !empty($_POST['age']) ? (int)$_POST['age'] : null;
        if (in_array('birth_date', $existing_columns)) $data['birth_date'] = !empty($_POST['birth_date']) ? $_POST['birth_date'] : null;
        if (in_array('skin_tone', $existing_columns)) $data['skin_tone'] = clean_input($_POST['skin_tone'] ?? '');
        if (in_array('hair_color', $existing_columns)) $data['hair_color'] = clean_input($_POST['hair_color'] ?? '');
        if (in_array('eye_color', $existing_columns)) $data['eye_color'] = clean_input($_POST['eye_color'] ?? '');
        if (in_array('languages', $existing_columns)) $data['languages'] = clean_input($_POST['languages'] ?? '');
        if (in_array('skills', $existing_columns)) $data['skills'] = $_POST['skills'] ?? '';
        if (in_array('featured', $existing_columns)) $data['featured'] = isset($_POST['featured']) ? 1 : 0;
        if (in_array('status', $existing_columns)) $data['status'] = $_POST['status'];
        if (in_array('sort_order', $existing_columns)) $data['sort_order'] = !empty($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0;
        
        // Validate
        if (empty($data['category_id'])) {
            $errors[] = 'กรุณาเลือกหมวดหมู่';
        }
        if (isset($data['code']) && empty($data['code'])) {
            $errors[] = 'กรุณากรอกรหัสโมเดล';
        }
        if (empty($data['name'])) {
            $errors[] = 'กรุณากรอกชื่อโมเดล';
        }
        
        // Check duplicate code (except current) - ถ้ามีฟิลด์ code
        if (isset($data['code']) && !empty($data['code'])) {
            $stmt = $conn->prepare("SELECT id FROM models WHERE code = ? AND id != ?");
            $stmt->bind_param('si', $data['code'], $model_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $errors[] = 'รหัสโมเดลนี้มีอยู่แล้ว';
            }
            $stmt->close();
        }
        
        // If no errors, update
        if (empty($errors)) {
            try {
                // อัพเดทข้อมูลด้วย SQL ง่ายๆ
                $update_fields = [];
                $update_values = [];
                $update_types = '';
                
                foreach ($data as $field => $value) {
                    $update_fields[] = "`{$field}` = ?";
                    $update_values[] = $value;
                    
                    if (is_int($value)) {
                        $update_types .= 'i';
                    } elseif (is_float($value)) {
                        $update_types .= 'd';
                    } else {
                        $update_types .= 's';
                    }
                }
                
                // เพิ่ม updated_at ถ้ามี
                if (in_array('updated_at', $existing_columns)) {
                    $update_fields[] = "`updated_at` = NOW()";
                }
                
                // สร้าง SQL
                $sql = "UPDATE models SET " . implode(', ', $update_fields) . " WHERE id = ?";
                $update_values[] = $model_id;
                $update_types .= 'i';
                
                // Execute
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                
                $stmt->bind_param($update_types, ...$update_values);
                
                if (!$stmt->execute()) {
                    throw new Exception("Execute failed: " . $stmt->error);
                }
                
                $stmt->close();
                
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
                                // ตรวจสอบว่ามีตาราง model_images
                                $table_check = $conn->query("SHOW TABLES LIKE 'model_images'");
                                if ($table_check && $table_check->num_rows > 0) {
                                    $image_data = [
                                        'model_id' => $model_id,
                                        'image_path' => $upload_result['path'],
                                        'image_type' => 'portfolio',
                                        'is_primary' => $key == 0 ? 1 : 0,
                                        'sort_order' => $key
                                    ];
                                    db_insert($conn, 'model_images', $image_data);
                                }
                            }
                        }
                    }
                }
                
                // Log activity
                log_activity($conn, $_SESSION['user_id'], 'update', 'models', $model_id);
                
                $success = true;
                set_message('success', 'บันทึกข้อมูลสำเร็จ');
                
                // Refresh model data
                $model = get_model($conn, $model_id);
                
            } catch (Exception $e) {
                $errors[] = 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage();
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-edit mr-3 text-blue-600"></i>แก้ไขโมเดล
        </h2>
        <p class="text-sm text-gray-500 mt-1">รหัส: <?php echo $model['code']; ?> | ID: #<?php echo $model_id; ?></p>
    </div>
    <div class="mt-4 sm:mt-0 flex space-x-3">
        <a href="index.php" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>กลับ
        </a>
        <a href="delete.php?id=<?php echo $model_id; ?>" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium btn-delete">
            <i class="fas fa-trash mr-2"></i>ลบ
        </a>
    </div>
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
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg" role="alert">
    <div class="flex items-center mb-2">
        <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
        <h5 class="font-semibold">พบข้อผิดพลาด:</h5>
    </div>
    <ul class="list-disc list-inside">
        <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
    <!-- Basic Info -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-red-600 to-red-500 text-white p-4">
            <h5 class="text-lg font-semibold flex items-center">
                <i class="fas fa-info-circle mr-2"></i>ข้อมูลพื้นฐาน
            </h5>
        </div>
        <div class="p-6">
            <!-- Existing Images -->
            <?php if (!empty($model_images)): ?>
            <div class="mb-6">
                <h6 class="text-sm font-semibold text-gray-700 mb-3">รูปภาพปัจจุบัน</h6>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php foreach ($model_images as $img): ?>
                    <div class="relative group">
                        <img src="<?php echo UPLOADS_URL . '/' . $img['image_path']; ?>" 
                             class="w-full h-32 object-cover rounded-lg border-2 <?php echo $img['is_primary'] ? 'border-red-500' : 'border-gray-300'; ?>">
                        <?php if ($img['is_primary']): ?>
                        <span class="absolute top-2 left-2 bg-red-600 text-white text-xs px-2 py-1 rounded">หลัก</span>
                        <?php endif; ?>
                        <div class="absolute top-2 right-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <?php if (!$img['is_primary']): ?>
                            <a href="set-primary.php?id=<?php echo $img['id']; ?>&model_id=<?php echo $model_id; ?>" 
                               class="p-1 bg-blue-600 text-white rounded text-xs" title="ตั้งเป็นรูปหลัก">
                                <i class="fas fa-star"></i>
                            </a>
                            <?php endif; ?>
                            <a href="delete-image.php?id=<?php echo $img['id']; ?>&model_id=<?php echo $model_id; ?>" 
                               class="p-1 bg-red-600 text-white rounded text-xs btn-delete" title="ลบ">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">หมวดหมู่ <span class="text-red-500">*</span></label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="category_id" required>
                        <option value="">เลือกหมวดหมู่</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $model['category_id'] == $cat['id'] ? 'selected' : ''; ?>><?php echo $cat['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">รหัสโมเดล <span class="text-red-500">*</span></label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="code" required placeholder="เช่น FM001" value="<?php echo htmlspecialchars($model['code'] ?? ''); ?>">
                    <small class="text-gray-500 text-sm mt-1">รหัสเฉพาะของโมเดล (ไม่ซ้ำกัน)</small>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ชื่อ (ไทย) <span class="text-red-500">*</span></label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="name" required value="<?php echo htmlspecialchars($model['name'] ?? ''); ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ชื่อ (อังกฤษ)</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="name_en" value="<?php echo htmlspecialchars($model['name_en'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">คำอธิบาย</label>
                <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="description" rows="4" placeholder="รายละเอียดเกี่ยวกับโมเดล..."></textarea>
            </div>
        </div>
    </div>
    
    <!-- Physical Info -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-600 to-green-500 text-white p-4">
            <h5 class="text-lg font-semibold flex items-center">
                <i class="fas fa-ruler mr-2"></i>ข้อมูลรูปร่าง
            </h5>
        </div>
        <div class="p-6">
            <!-- Existing Images -->
            <?php if (!empty($model_images)): ?>
            <div class="mb-6">
                <h6 class="text-sm font-semibold text-gray-700 mb-3">รูปภาพปัจจุบัน</h6>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php foreach ($model_images as $img): ?>
                    <div class="relative group">
                        <img src="<?php echo UPLOADS_URL . '/' . $img['image_path']; ?>" 
                             class="w-full h-32 object-cover rounded-lg border-2 <?php echo $img['is_primary'] ? 'border-red-500' : 'border-gray-300'; ?>">
                        <?php if ($img['is_primary']): ?>
                        <span class="absolute top-2 left-2 bg-red-600 text-white text-xs px-2 py-1 rounded">หลัก</span>
                        <?php endif; ?>
                        <div class="absolute top-2 right-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <?php if (!$img['is_primary']): ?>
                            <a href="set-primary.php?id=<?php echo $img['id']; ?>&model_id=<?php echo $model_id; ?>" 
                               class="p-1 bg-blue-600 text-white rounded text-xs" title="ตั้งเป็นรูปหลัก">
                                <i class="fas fa-star"></i>
                            </a>
                            <?php endif; ?>
                            <a href="delete-image.php?id=<?php echo $img['id']; ?>&model_id=<?php echo $model_id; ?>" 
                               class="p-1 bg-red-600 text-white rounded text-xs btn-delete" title="ลบ">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ส่วนสูง (cm)</label>
                    <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="height" placeholder="170" value="<?php echo $model['height']; ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">น้ำหนัก (kg)</label>
                    <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="weight" placeholder="50" value="<?php echo $model['weight']; ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">อายุ (ปี)</label>
                    <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="age" placeholder="25" value="<?php echo $model['age']; ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ประสบการณ์ (ปี)</label>
                    <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="experience_years" placeholder="5">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">รอบอก (inch)</label>
                    <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="bust" placeholder="34" value="<?php echo $model['bust']; ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">รอบเอว (inch)</label>
                    <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="waist" placeholder="24" value="<?php echo $model['waist']; ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">รอบสะโพก (inch)</label>
                    <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="hips" placeholder="34">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">สีผิว</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="skin_tone" placeholder="ขาว, สองสี, ขาวเหลือง">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">สีผม</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="hair_color" placeholder="ดำ, น้ำตาล">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">สีตา</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="eye_color" placeholder="น้ำตาล, ดำ">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Price & Skills -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 text-white p-4">
            <h5 class="text-lg font-semibold flex items-center">
                <i class="fas fa-dollar-sign mr-2"></i>ราคาและความสามารถ
            </h5>
        </div>
        <div class="p-6">
            <!-- Existing Images -->
            <?php if (!empty($model_images)): ?>
            <div class="mb-6">
                <h6 class="text-sm font-semibold text-gray-700 mb-3">รูปภาพปัจจุบัน</h6>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php foreach ($model_images as $img): ?>
                    <div class="relative group">
                        <img src="<?php echo UPLOADS_URL . '/' . $img['image_path']; ?>" 
                             class="w-full h-32 object-cover rounded-lg border-2 <?php echo $img['is_primary'] ? 'border-red-500' : 'border-gray-300'; ?>">
                        <?php if ($img['is_primary']): ?>
                        <span class="absolute top-2 left-2 bg-red-600 text-white text-xs px-2 py-1 rounded">หลัก</span>
                        <?php endif; ?>
                        <div class="absolute top-2 right-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <?php if (!$img['is_primary']): ?>
                            <a href="set-primary.php?id=<?php echo $img['id']; ?>&model_id=<?php echo $model_id; ?>" 
                               class="p-1 bg-blue-600 text-white rounded text-xs" title="ตั้งเป็นรูปหลัก">
                                <i class="fas fa-star"></i>
                            </a>
                            <?php endif; ?>
                            <a href="delete-image.php?id=<?php echo $img['id']; ?>&model_id=<?php echo $model_id; ?>" 
                               class="p-1 bg-red-600 text-white rounded text-xs btn-delete" title="ลบ">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ราคาขั้นต่ำ (บาท/วัน)</label>
                    <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="price_min" step="100" placeholder="3000" value="<?php echo $model['price_min']; ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ราคาสูงสุด (บาท/วัน)</label>
                    <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="price_max" step="100" placeholder="5000" value="<?php echo $model['price_max']; ?>">
                </div>
            </div>
            
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">ภาษาที่พูดได้</label>
                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="languages" placeholder="ไทย, อังกฤษ, จีน">
            </div>
            
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">ความสามารถพิเศษ</label>
                <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="skills" rows="3" placeholder="เดินแบบ, แสดง, ร้องเพลง, เต้น..."></textarea>
            </div>
        </div>
    </div>
    
    <!-- Images -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-purple-600 to-purple-500 text-white p-4">
            <h5 class="text-lg font-semibold flex items-center">
                <i class="fas fa-images mr-2"></i>รูปภาพ
            </h5>
        </div>
        <div class="p-6">
            <!-- Existing Images -->
            <?php if (!empty($model_images)): ?>
            <div class="mb-6">
                <h6 class="text-sm font-semibold text-gray-700 mb-3">รูปภาพปัจจุบัน</h6>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php foreach ($model_images as $img): ?>
                    <div class="relative group">
                        <img src="<?php echo UPLOADS_URL . '/' . $img['image_path']; ?>" 
                             class="w-full h-32 object-cover rounded-lg border-2 <?php echo $img['is_primary'] ? 'border-red-500' : 'border-gray-300'; ?>">
                        <?php if ($img['is_primary']): ?>
                        <span class="absolute top-2 left-2 bg-red-600 text-white text-xs px-2 py-1 rounded">หลัก</span>
                        <?php endif; ?>
                        <div class="absolute top-2 right-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <?php if (!$img['is_primary']): ?>
                            <a href="set-primary.php?id=<?php echo $img['id']; ?>&model_id=<?php echo $model_id; ?>" 
                               class="p-1 bg-blue-600 text-white rounded text-xs" title="ตั้งเป็นรูปหลัก">
                                <i class="fas fa-star"></i>
                            </a>
                            <?php endif; ?>
                            <a href="delete-image.php?id=<?php echo $img['id']; ?>&model_id=<?php echo $model_id; ?>" 
                               class="p-1 bg-red-600 text-white rounded text-xs btn-delete" title="ลบ">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">อัพโหลดรูปภาพ</label>
                <input type="file" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="images[]" accept="image/*" multiple>
                <small class="text-gray-500 text-sm mt-1">รูปแรกจะเป็นรูปหลัก, รองรับไฟล์: JPG, PNG, GIF, WEBP (สูงสุด 5MB)</small>
            </div>
        </div>
    </div>
    
    <!-- Settings -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-gray-600 to-gray-500 text-white p-4">
            <h5 class="text-lg font-semibold flex items-center">
                <i class="fas fa-cog mr-2"></i>การตั้งค่า
            </h5>
        </div>
        <div class="p-6">
            <!-- Existing Images -->
            <?php if (!empty($model_images)): ?>
            <div class="mb-6">
                <h6 class="text-sm font-semibold text-gray-700 mb-3">รูปภาพปัจจุบัน</h6>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php foreach ($model_images as $img): ?>
                    <div class="relative group">
                        <img src="<?php echo UPLOADS_URL . '/' . $img['image_path']; ?>" 
                             class="w-full h-32 object-cover rounded-lg border-2 <?php echo $img['is_primary'] ? 'border-red-500' : 'border-gray-300'; ?>">
                        <?php if ($img['is_primary']): ?>
                        <span class="absolute top-2 left-2 bg-red-600 text-white text-xs px-2 py-1 rounded">หลัก</span>
                        <?php endif; ?>
                        <div class="absolute top-2 right-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <?php if (!$img['is_primary']): ?>
                            <a href="set-primary.php?id=<?php echo $img['id']; ?>&model_id=<?php echo $model_id; ?>" 
                               class="p-1 bg-blue-600 text-white rounded text-xs" title="ตั้งเป็นรูปหลัก">
                                <i class="fas fa-star"></i>
                            </a>
                            <?php endif; ?>
                            <a href="delete-image.php?id=<?php echo $img['id']; ?>&model_id=<?php echo $model_id; ?>" 
                               class="p-1 bg-red-600 text-white rounded text-xs btn-delete" title="ลบ">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">สถานะ</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="status">
                        <option value="available">ว่าง</option>
                        <option value="busy">ไม่ว่าง</option>
                        <option value="inactive" <?php echo $model['status'] == 'inactive' ? 'selected' : ''; ?>>ไม่ใช้งาน</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ลำดับการแสดง</label>
                    <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="sort_order" value="<?php echo $model['sort_order']; ?>">
                </div>
            </div>
            
            <div class="mt-6">
                <div class="flex items-center space-x-3">
                    <label class="toggle-switch">
                        <input type="checkbox" name="featured" id="featured">
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="text-sm text-gray-700">
                        <i class="fas fa-star text-yellow-500 mr-1"></i>โมเดลแนะนำ (Featured)
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Submit -->
    <div class="flex flex-col sm:flex-row sm:justify-end gap-4">
        <a href="index.php" class="inline-flex items-center justify-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-times mr-2"></i>ยกเลิก
        </a>
        <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-save mr-2"></i>บันทึก
        </button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>



