<?php
/**
 * VIBEDAYBKK Admin - Edit Homepage Section
 * แก้ไข Section ในหน้าแรก
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_admin();

$page_title = 'แก้ไข Section';
$current_page = 'homepage';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];
$success = false;

// Get section data
$section = db_get_row($conn, "SELECT * FROM homepage_sections WHERE id = ?", [$id]);

if (!$section) {
    header('Location: index.php');
    exit;
}

// Extract section key for use in form processing
$section_key = $section['section_key'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../includes/notification.php';
    
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $title = clean_input($_POST['title'] ?? '');
        $subtitle = clean_input($_POST['subtitle'] ?? '');
        $content = $_POST['content'] ?? '';
        $button1_text = clean_input($_POST['button1_text'] ?? '');
        $button1_link = clean_input($_POST['button1_link'] ?? '');
        $button2_text = clean_input($_POST['button2_text'] ?? '');
        $button2_link = clean_input($_POST['button2_link'] ?? '');
        $background_color = clean_input($_POST['background_color'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        
        // Background settings
        $background_type = clean_input($_POST['background_type'] ?? 'color');
        $background_position = clean_input($_POST['background_position'] ?? 'center');
        $background_size = clean_input($_POST['background_size'] ?? 'cover');
        $background_repeat = clean_input($_POST['background_repeat'] ?? 'no-repeat');
        $background_attachment = clean_input($_POST['background_attachment'] ?? 'scroll');
        
        // Handle background image upload
        $background_image = $section['background_image'];
        if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] === UPLOAD_ERR_OK) {
            // Delete old image
            if (!empty($background_image)) {
                delete_image($background_image);
            }
            
            $upload_result = upload_image($_FILES['background_image'], 'homepage');
            if ($upload_result['success']) {
                $background_image = $upload_result['file_path'];
            } else {
                $errors[] = 'Background Image: ' . ($upload_result['error'] ?? $upload_result['message'] ?? 'Unknown error');
            }
        }
        
        // Handle left image upload (for About section)
        $left_image = $section['left_image'] ?? '';
        if (isset($_FILES['left_image']) && $_FILES['left_image']['error'] === UPLOAD_ERR_OK) {
            // Delete old image
            if (!empty($left_image)) {
                delete_image($left_image);
            }
            
            $upload_result = upload_image($_FILES['left_image'], 'homepage');
            if ($upload_result['success']) {
                $left_image = $upload_result['file_path'];
            } else {
                $errors[] = 'Left Image: ' . ($upload_result['error'] ?? $upload_result['message'] ?? 'Unknown error');
            }
        }
        
        // Handle right image upload (for How to Book section)
        $right_image = $section['right_image'] ?? '';
        if (isset($_FILES['right_image']) && $_FILES['right_image']['error'] === UPLOAD_ERR_OK) {
            // Delete old image
            if (!empty($right_image)) {
                delete_image($right_image);
            }
            
            $upload_result = upload_image($_FILES['right_image'], 'homepage');
            if ($upload_result['success']) {
                $right_image = $upload_result['file_path'];
            } else {
                $errors[] = 'Right Image: ' . ($upload_result['error'] ?? $upload_result['message'] ?? 'Unknown error');
            }
        }
        
        // สำหรับ How to Book section - จัดการ steps
        $steps = [];
        if ($section_key === 'how-to-book' && isset($_POST['steps'])) {
            foreach ($_POST['steps'] as $step) {
                if (!empty($step['title']) && !empty($step['description'])) {
                    $steps[] = [
                        'title' => clean_input($step['title']),
                        'description' => clean_input($step['description'])
                    ];
                }
            }
        }
        
        // สำหรับ About section - จัดการ features list
        $features = [];
        if ($section_key === 'about' && isset($_POST['features'])) {
            foreach ($_POST['features'] as $feature) {
                if (!empty($feature['text'])) {
                    $features[] = [
                        'text' => clean_input($feature['text']),
                        'icon' => clean_input($feature['icon'] ?? 'fa-check-circle')
                    ];
                }
            }
        }
        
        // Build settings JSON
        $settings = [];
        if (isset($_POST['settings']) && is_array($_POST['settings'])) {
            foreach ($_POST['settings'] as $key => $value) {
                $settings[$key] = $value;
            }
        }
        
        // เพิ่มการตั้งค่าสีฟอนต์สำหรับ VIBEDAYBKK
        if ($section_key === 'hero' && isset($_POST['title_colors'])) {
            $settings['title_colors'] = $_POST['title_colors'];
        }
        
        $settings_json = json_encode($settings);
        
        if (empty($errors)) {
            // ตรวจสอบว่าคอลัมน์ left_image มีอยู่หรือไม่
            $check_column = $conn->query("SHOW COLUMNS FROM homepage_sections LIKE 'left_image'");
            $has_left_image = $check_column->num_rows > 0;
            
            // ตรวจสอบว่าคอลัมน์ right_image และ steps มีอยู่หรือไม่
            $check_right_image = $conn->query("SHOW COLUMNS FROM homepage_sections LIKE 'right_image'");
            $has_right_image = $check_right_image->num_rows > 0;
            
            $check_steps = $conn->query("SHOW COLUMNS FROM homepage_sections LIKE 'steps'");
            $has_steps = $check_steps->num_rows > 0;
            
            // UPDATE with correct column names
            $sql = "UPDATE homepage_sections SET 
                    title = ?, 
                    subtitle = ?, 
                    content = ?,
                    button1_text = ?, 
                    button1_link = ?,
                    button2_text = ?, 
                    button2_link = ?,
                    background_image = ?, 
                    background_color = ?, 
                    left_image = ?,
                    background_type = ?, 
                    background_position = ?, 
                    background_size = ?, 
                    background_repeat = ?, 
                    background_attachment = ?,
                    sort_order = ?";
            
            $params = [
                $title, $subtitle, $content,
                $button1_text, $button1_link,
                $button2_text, $button2_link,
                $background_image, $background_color, $left_image,
                $background_type, $background_position, $background_size, $background_repeat, $background_attachment,
                $sort_order
            ];
            
            // เพิ่ม right_image ถ้ามีคอลัมน์
            if ($has_right_image) {
                $sql = str_replace('sort_order = ?', 'right_image = ?, sort_order = ?', $sql);
                array_splice($params, -1, 0, $right_image);
            }
            
            // เพิ่ม steps ถ้ามีคอลัมน์
            if ($has_steps) {
                $sql = str_replace('sort_order = ?', 'steps = ?, sort_order = ?', $sql);
                $steps_json = json_encode($steps, JSON_UNESCAPED_UNICODE);
                array_splice($params, -1, 0, $steps_json);
            }
            
            // เพิ่ม features สำหรับ About section
            $check_features = $conn->query("SHOW COLUMNS FROM homepage_sections LIKE 'features'");
            $has_features = $check_features->num_rows > 0;
            if ($has_features && $section_key === 'about') {
                $sql = str_replace('sort_order = ?', 'features = ?, sort_order = ?', $sql);
                $features_json = json_encode($features, JSON_UNESCAPED_UNICODE);
                array_splice($params, -1, 0, $features_json);
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $id;
            
            if (db_execute($conn, $sql, $params)) {
                set_success_message('บันทึกข้อมูล ' . ($section['title'] ?? 'Section') . ' เรียบร้อยแล้ว');
                // Refresh section data
                $section = db_get_row($conn, "SELECT * FROM homepage_sections WHERE id = ?", [$id]);
                
                // Redirect to prevent form resubmission
                header("Location: edit.php?id={$id}");
                exit;
            } else {
                set_error_message('เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $conn->error);
            }
        }
    }
}

// Parse settings
$settings = []; // settings column ไม่มีใน database

include '../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-edit mr-3 text-red-600"></i>แก้ไข <?php echo $section['title'] ?? ucfirst($section['section_key']); ?>
        </h2>
        <p class="text-gray-600 mt-1">Section: <?php echo strtoupper($section['section_key']); ?></p>
    </div>
    <a href="index.php" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
        <i class="fas fa-arrow-left mr-2"></i>กลับ
    </a>
</div>

<?php 
// แสดง errors ถ้ามี (ใช้ SweetAlert2)
if (!empty($errors)): 
    $error_message = implode('\n', array_map('addslashes', $errors));
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'error',
        title: '❌ เกิดข้อผิดพลาด!',
        html: '<?php echo str_replace("\n", "<br>", $error_message); ?>',
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'ตกลง',
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'rounded-lg px-6 py-2.5 font-medium'
        }
    });
});
</script>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
    <!-- Content Section -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-align-left mr-3"></i>เนื้อหา
            </h5>
        </div>
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">หัวข้อหลัก</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($section['title']); ?>" 
                       class="w-full px-4 py-3 text-lg border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                
                <?php if ($section['section_key'] === 'hero'): ?>
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">สีฟอนต์ VIBEDAYBKK</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">VIBE (สีแดง)</label>
                            <div class="flex items-center space-x-2">
                                <input type="color" name="title_colors[vibe]" value="<?php echo $settings['title_colors']['vibe'] ?? '#DC2626'; ?>" 
                                       class="w-10 h-10 border border-gray-300 rounded cursor-pointer">
                                <input type="text" name="title_colors[vibe_text]" value="<?php echo $settings['title_colors']['vibe'] ?? '#DC2626'; ?>" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="#DC2626">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">DAY (สีขาว)</label>
                            <div class="flex items-center space-x-2">
                                <input type="color" name="title_colors[day]" value="<?php echo $settings['title_colors']['day'] ?? '#FFFFFF'; ?>" 
                                       class="w-10 h-10 border border-gray-300 rounded cursor-pointer">
                                <input type="text" name="title_colors[day_text]" value="<?php echo $settings['title_colors']['day'] ?? '#FFFFFF'; ?>" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="#FFFFFF">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">BKK (สีแดง)</label>
                            <div class="flex items-center space-x-2">
                                <input type="color" name="title_colors[bkk]" value="<?php echo $settings['title_colors']['bkk'] ?? '#DC2626'; ?>" 
                                       class="w-10 h-10 border border-gray-300 rounded cursor-pointer">
                                <input type="text" name="title_colors[bkk_text]" value="<?php echo $settings['title_colors']['bkk'] ?? '#DC2626'; ?>" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="#DC2626">
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 p-3 bg-white border border-gray-200 rounded">
                        <div class="text-sm text-gray-600 mb-2">ตัวอย่าง:</div>
                        <div class="text-2xl font-bold">
                            <span style="color: <?php echo $settings['title_colors']['vibe'] ?? '#DC2626'; ?>;">VIBE</span>
                            <span style="color: <?php echo $settings['title_colors']['day'] ?? '#FFFFFF'; ?>;">DAY</span>
                            <span style="color: <?php echo $settings['title_colors']['bkk'] ?? '#DC2626'; ?>;">BKK</span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">หัวข้อรอง</label>
                <input type="text" name="subtitle" value="<?php echo htmlspecialchars($section['subtitle']); ?>" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">เนื้อหา</label>
                <textarea name="content" rows="5" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"><?php echo htmlspecialchars($section['content'] ?? ''); ?></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ข้อความปุ่ม</label>
                    <input type="text" name="button1_text" value="<?php echo htmlspecialchars($section['button1_text'] ?? ''); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ลิงก์ปุ่มที่ 1</label>
                    <input type="text" name="button1_link" value="<?php echo htmlspecialchars($section['button1_link'] ?? ''); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ข้อความปุ่มที่ 2 (ถ้ามี)</label>
                    <input type="text" name="button2_text" value="<?php echo htmlspecialchars($section['button2_text'] ?? ''); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ลิงก์ปุ่มที่ 2</label>
                    <input type="text" name="button2_link" value="<?php echo htmlspecialchars($section['button2_link'] ?? ''); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Design Section -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-palette mr-3"></i>การออกแบบ
            </h5>
        </div>
        <div class="p-6 space-y-6">
            <!-- Background Type Toggle -->
            <div class="p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border-2 border-purple-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <span class="text-gray-900 font-semibold text-base">ประเภทพื้นหลัง</span>
                        <p class="text-sm text-gray-600 mt-1">
                            เลือกใช้ <span class="font-semibold text-purple-600">สี</span> หรือ <span class="font-semibold text-pink-600">รูปภาพ</span><br>
                            <span class="text-xs">💡 ถ้าเลือกรูปภาพ: คอนเทนต์ (ข้อความ, ปุ่ม) จะถูกซ่อน</span>
                        </p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="background_type_checkbox"
                               <?php echo ($section['background_type'] ?? 'color') == 'image' ? 'checked' : ''; ?>
                               class="sr-only peer"
                               onchange="toggleBackgroundType(this)">
                        <div class="w-16 h-8 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-7 after:w-7 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-purple-600 peer-checked:to-pink-600"></div>
                    </label>
                </div>
                <div class="mt-3 flex items-center space-x-2 text-sm">
                    <span id="background_type_label" class="font-semibold px-3 py-1 rounded-full <?php echo ($section['background_type'] ?? 'color') == 'image' ? 'bg-pink-100 text-pink-700' : 'bg-purple-100 text-purple-700'; ?>">
                        <?php echo ($section['background_type'] ?? 'color') == 'image' ? '🖼️ รูปภาพ' : '🎨 สี'; ?>
                    </span>
                </div>
                <input type="hidden" id="background_type_value" name="background_type"
                       value="<?php echo $section['background_type'] ?? 'color'; ?>">
            </div>
            
            <!-- Left Image Upload (สำหรับ About Section) -->
            <?php if ($section['section_key'] === 'about'): ?>
            <?php 
            // ตรวจสอบว่าคอลัมน์ left_image มีอยู่หรือไม่
            $check_column = $conn->query("SHOW COLUMNS FROM homepage_sections LIKE 'left_image'");
            $has_left_image = $check_column->num_rows > 0;
            ?>
            <?php if ($has_left_image): ?>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">รูปภาพด้านซ้าย</label>
                <?php if (!empty($section['left_image'])): ?>
                <div class="mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <?php 
                    $left_preview_url = (strpos($section['left_image'], 'uploads/') === 0) 
                        ? BASE_URL . '/' . $section['left_image'] 
                        : UPLOADS_URL . '/' . $section['left_image'];
                    ?>
                    <img src="<?php echo $left_preview_url; ?>" 
                         class="h-32 w-32 object-cover rounded mb-2">
                    <p class="text-xs text-gray-500">รูปปัจจุบัน: <?php echo htmlspecialchars($section['left_image']); ?></p>
                </div>
                <?php endif; ?>
                <input type="file" name="left_image" accept="image/*" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                <p class="text-sm text-gray-500 mt-2">แนะนำขนาด: 1200x1200px หรือ 600x600px (รูปสี่เหลี่ยมจัตุรัส)</p>
            </div>
            <?php else: ?>
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                    <div>
                        <p class="text-sm font-medium text-yellow-800">ต้องรัน SQL Script ก่อน</p>
                        <p class="text-xs text-yellow-600">รันไฟล์ <code>setup-about-section-complete.sql</code> เพื่อเพิ่มคอลัมน์ left_image</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
            
            <!-- Right Image Upload (for How to Book section) -->
            <?php if ($section_key === 'how-to-book'): ?>
            <?php 
            $check_right_image = $conn->query("SHOW COLUMNS FROM homepage_sections LIKE 'right_image'");
            $has_right_image = $check_right_image->num_rows > 0;
            ?>
            <?php if ($has_right_image): ?>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">รูปภาพทางขวา</label>
                <?php if (!empty($section['right_image'])): ?>
                <div class="mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <?php 
                    $preview_url = (strpos($section['right_image'], 'uploads/') === 0) 
                        ? BASE_URL . '/' . $section['right_image'] 
                        : UPLOADS_URL . '/' . $section['right_image'];
                    ?>
                    <img src="<?php echo $preview_url; ?>" 
                         class="h-32 object-cover rounded mb-2">
                    <p class="text-xs text-gray-500">รูปปัจจุบัน: <?php echo htmlspecialchars($section['right_image']); ?></p>
                </div>
                <?php endif; ?>
                <input type="file" name="right_image" accept="image/*" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                <p class="text-sm text-gray-500 mt-2">แนะนำขนาด: 400x600px หรือ 1:1.5</p>
            </div>
            <?php endif; ?>
            <?php endif; ?>
            
            <!-- Background Image Upload -->
            <div id="background_image_section" class="<?php echo ($section['background_type'] ?? 'color') == 'image' ? '' : 'hidden'; ?>">
                <label class="block text-sm font-semibold text-gray-700 mb-2">รูปพื้นหลัง</label>
                <?php if (!empty($section['background_image'])): ?>
                <div class="mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <?php 
                    // ถ้า path มี uploads/ อยู่แล้ว ใช้ BASE_URL แทน UPLOADS_URL
                    $preview_url = (strpos($section['background_image'], 'uploads/') === 0) 
                        ? BASE_URL . '/' . $section['background_image'] 
                        : UPLOADS_URL . '/' . $section['background_image'];
                    ?>
                    <img src="<?php echo $preview_url; ?>" 
                         class="h-32 object-cover rounded mb-2">
                    <p class="text-xs text-gray-500">รูปปัจจุบัน: <?php echo htmlspecialchars($section['background_image']); ?></p>
                </div>
                <?php endif; ?>
                <input type="file" name="background_image" accept="image/*" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                <p class="text-sm text-gray-500 mt-2">แนะนำขนาด: 1920x1080px</p>
            </div>
            
            <!-- Background Settings Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Background Position -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ตำแหน่งพื้นหลัง</label>
                    <select name="background_position" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="center" <?php echo ($section['background_position'] ?? 'center') == 'center' ? 'selected' : ''; ?>>ตรงกลาง (Center)</option>
                        <option value="top" <?php echo ($section['background_position'] ?? '') == 'top' ? 'selected' : ''; ?>>บน (Top)</option>
                        <option value="bottom" <?php echo ($section['background_position'] ?? '') == 'bottom' ? 'selected' : ''; ?>>ล่าง (Bottom)</option>
                        <option value="left" <?php echo ($section['background_position'] ?? '') == 'left' ? 'selected' : ''; ?>>ซ้าย (Left)</option>
                        <option value="right" <?php echo ($section['background_position'] ?? '') == 'right' ? 'selected' : ''; ?>>ขวา (Right)</option>
                        <option value="top left" <?php echo ($section['background_position'] ?? '') == 'top left' ? 'selected' : ''; ?>>บนซ้าย</option>
                        <option value="top right" <?php echo ($section['background_position'] ?? '') == 'top right' ? 'selected' : ''; ?>>บนขวา</option>
                        <option value="bottom left" <?php echo ($section['background_position'] ?? '') == 'bottom left' ? 'selected' : ''; ?>>ล่างซ้าย</option>
                        <option value="bottom right" <?php echo ($section['background_position'] ?? '') == 'bottom right' ? 'selected' : ''; ?>>ล่างขวา</option>
                    </select>
                </div>
                
                <!-- Background Size -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ขนาดพื้นหลัง</label>
                    <select name="background_size" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="cover" <?php echo ($section['background_size'] ?? 'cover') == 'cover' ? 'selected' : ''; ?>>เต็มพื้นที่ (Cover)</option>
                        <option value="contain" <?php echo ($section['background_size'] ?? '') == 'contain' ? 'selected' : ''; ?>>พอดีกรอบ (Contain)</option>
                        <option value="auto" <?php echo ($section['background_size'] ?? '') == 'auto' ? 'selected' : ''; ?>>ขนาดจริง (Auto)</option>
                    </select>
                </div>
                
                <!-- Background Repeat -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">การซ้ำพื้นหลัง</label>
                    <select name="background_repeat" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="no-repeat" <?php echo ($section['background_repeat'] ?? 'no-repeat') == 'no-repeat' ? 'selected' : ''; ?>>ไม่ซ้ำ (No Repeat)</option>
                        <option value="repeat" <?php echo ($section['background_repeat'] ?? '') == 'repeat' ? 'selected' : ''; ?>>ซ้ำทั้งหมด (Repeat)</option>
                        <option value="repeat-x" <?php echo ($section['background_repeat'] ?? '') == 'repeat-x' ? 'selected' : ''; ?>>ซ้ำแนวนอน (Repeat-X)</option>
                        <option value="repeat-y" <?php echo ($section['background_repeat'] ?? '') == 'repeat-y' ? 'selected' : ''; ?>>ซ้ำแนวตั้ง (Repeat-Y)</option>
                    </select>
                </div>
                
                <!-- Background Attachment -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">การเลื่อนพื้นหลัง</label>
                    <select name="background_attachment" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="scroll" <?php echo ($section['background_attachment'] ?? 'scroll') == 'scroll' ? 'selected' : ''; ?>>เลื่อนตาม (Scroll)</option>
                        <option value="fixed" <?php echo ($section['background_attachment'] ?? '') == 'fixed' ? 'selected' : ''; ?>>ติดหน้าจอ (Fixed/Parallax)</option>
                    </select>
                </div>
            </div>
            
            <!-- Features Management (for About section) -->
            <?php if ($section_key === 'about'): ?>
            <?php 
            $check_features = $conn->query("SHOW COLUMNS FROM homepage_sections LIKE 'features'");
            $has_features = $check_features->num_rows > 0;
            ?>
            <?php if ($has_features): ?>
            <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-700 mb-4">รายการคุณสมบัติ</label>
                
                <?php 
                // ดึง features จากฐานข้อมูล
                $features = [];
                if (!empty($section['features'])) {
                    $features = json_decode($section['features'], true);
                }
                
                // ถ้าไม่มี features ให้ใช้ default
                if (empty($features)) {
                    $features = [
                        ['text' => 'โมเดลมืออาชีพที่ผ่านการคัดสรร', 'icon' => 'fa-check-circle'],
                        ['text' => 'บริการครบวงจรในราคาที่เหมาะสม', 'icon' => 'fa-check-circle'],
                        ['text' => 'ทีมงานมืออาชีพพร้อมให้คำปรึกษา', 'icon' => 'fa-check-circle'],
                        ['text' => 'รองรับงานทุกประเภทและขนาด', 'icon' => 'fa-check-circle']
                    ];
                }
                ?>
                
                <div id="features-container">
                    <?php foreach ($features as $index => $feature): ?>
                    <div class="feature-item bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <h6 class="text-lg font-semibold text-gray-700">คุณสมบัติที่ <?php echo $index + 1; ?></h6>
                            <button type="button" class="remove-feature text-red-600 hover:text-red-800 font-bold" <?php echo count($features) <= 1 ? 'disabled' : ''; ?>>
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">ข้อความ</label>
                                <input type="text" name="features[<?php echo $index; ?>][text]" 
                                       value="<?php echo htmlspecialchars($feature['text']); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="เช่น: โมเดลมืออาชีพที่ผ่านการคัดสรร">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">ไอคอน</label>
                                <div class="flex items-center space-x-2">
                                    <input type="text" name="features[<?php echo $index; ?>][icon]" 
                                           value="<?php echo htmlspecialchars($feature['icon']); ?>"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="fa-check-circle">
                                    <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas <?php echo htmlspecialchars($feature['icon']); ?> text-gray-600"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <button type="button" id="add-feature" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>เพิ่มคุณสมบัติ
                </button>
                
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    let featureIndex = <?php echo count($features); ?>;
                    
                    // Add feature
                    document.getElementById('add-feature').addEventListener('click', function() {
                        const container = document.getElementById('features-container');
                        const featureHtml = `
                            <div class="feature-item bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h6 class="text-lg font-semibold text-gray-700">คุณสมบัติที่ ${featureIndex + 1}</h6>
                                    <button type="button" class="remove-feature text-red-600 hover:text-red-800 font-bold">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">ข้อความ</label>
                                        <input type="text" name="features[${featureIndex}][text]" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="เช่น: โมเดลมืออาชีพที่ผ่านการคัดสรร">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">ไอคอน</label>
                                        <div class="flex items-center space-x-2">
                                            <input type="text" name="features[${featureIndex}][icon]" 
                                                   value="fa-check-circle"
                                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                   placeholder="fa-check-circle">
                                            <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-check-circle text-gray-600"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        container.insertAdjacentHTML('beforeend', featureHtml);
                        featureIndex++;
                        updateFeatureNumbers();
                    });
                    
                    // Remove feature
                    document.addEventListener('click', function(e) {
                        if (e.target.closest('.remove-feature')) {
                            const featureItems = document.querySelectorAll('.feature-item');
                            if (featureItems.length > 1) {
                                e.target.closest('.feature-item').remove();
                                updateFeatureNumbers();
                            }
                        }
                    });
                    
                    // Update feature numbers
                    function updateFeatureNumbers() {
                        const featureItems = document.querySelectorAll('.feature-item');
                        featureItems.forEach((item, index) => {
                            const title = item.querySelector('h6');
                            title.textContent = `คุณสมบัติที่ ${index + 1}`;
                            
                            // Update input names
                            const textInput = item.querySelector('input[name*="[text]"]');
                            const iconInput = item.querySelector('input[name*="[icon]"]');
                            textInput.name = `features[${index}][text]`;
                            iconInput.name = `features[${index}][icon]`;
                        });
                        
                        // Enable/disable remove buttons
                        const removeButtons = document.querySelectorAll('.remove-feature');
                        removeButtons.forEach(btn => {
                            btn.disabled = featureItems.length <= 1;
                        });
                    }
                });
                </script>
            </div>
            <?php else: ?>
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                    <div>
                        <p class="text-sm font-medium text-yellow-800">ต้องรัน SQL Script ก่อน</p>
                        <p class="text-xs text-yellow-600">รันไฟล์ <code>setup-about-section-complete.sql</code> เพื่อเพิ่มคอลัมน์ features</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
            
            <!-- Steps Management (for How to Book section) -->
            <?php if ($section_key === 'how-to-book'): ?>
            <?php 
            $check_steps = $conn->query("SHOW COLUMNS FROM homepage_sections LIKE 'steps'");
            $has_steps = $check_steps->num_rows > 0;
            ?>
            <?php if ($has_steps): ?>
            <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-700 mb-4">ขั้นตอนการจองบริการ</label>
                
                <?php 
                // ดึง steps จากฐานข้อมูล
                $steps = [];
                if (!empty($section['steps'])) {
                    $steps = json_decode($section['steps'], true);
                }
                
                // ถ้าไม่มี steps ให้ใช้ default
                if (empty($steps)) {
                    $steps = [
                        ['title' => 'เลือกบริการ', 'description' => 'เลือกประเภทโมเดลและบริการที่ต้องการ'],
                        ['title' => 'ติดต่อเรา', 'description' => 'ติดต่อผ่าน Line หรือโทรศัพท์เพื่อปรึกษารายละเอียด'],
                        ['title' => 'ยืนยันการจอง', 'description' => 'ยืนยันรายละเอียดและชำระเงินมัดจำ'],
                        ['title' => 'เริ่มงาน', 'description' => 'โมเดลจะมาถึงสถานที่ตามเวลาที่กำหนด']
                    ];
                }
                ?>
                
                <div id="steps-container">
                    <?php foreach ($steps as $index => $step): ?>
                    <div class="step-item bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <h6 class="text-lg font-semibold text-gray-700">ขั้นตอนที่ <?php echo $index + 1; ?></h6>
                            <button type="button" class="remove-step text-red-600 hover:text-red-800 font-bold" <?php echo count($steps) <= 1 ? 'disabled' : ''; ?>>
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">ชื่อขั้นตอน</label>
                                <input type="text" name="steps[<?php echo $index; ?>][title]" 
                                       value="<?php echo htmlspecialchars($step['title']); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="เช่น: เลือกบริการ">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">คำอธิบาย</label>
                                <input type="text" name="steps[<?php echo $index; ?>][description]" 
                                       value="<?php echo htmlspecialchars($step['description']); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="เช่น: เลือกประเภทโมเดลและบริการที่ต้องการ">
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <button type="button" id="add-step" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>เพิ่มขั้นตอน
                </button>
                
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    let stepIndex = <?php echo count($steps); ?>;
                    
                    // Add step
                    document.getElementById('add-step').addEventListener('click', function() {
                        const container = document.getElementById('steps-container');
                        const stepHtml = `
                            <div class="step-item bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h6 class="text-lg font-semibold text-gray-700">ขั้นตอนที่ ${stepIndex + 1}</h6>
                                    <button type="button" class="remove-step text-red-600 hover:text-red-800 font-bold">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">ชื่อขั้นตอน</label>
                                        <input type="text" name="steps[${stepIndex}][title]" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="เช่น: เลือกบริการ">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">คำอธิบาย</label>
                                        <input type="text" name="steps[${stepIndex}][description]" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="เช่น: เลือกประเภทโมเดลและบริการที่ต้องการ">
                                    </div>
                                </div>
                            </div>
                        `;
                        container.insertAdjacentHTML('beforeend', stepHtml);
                        stepIndex++;
                        updateStepNumbers();
                    });
                    
                    // Remove step
                    document.addEventListener('click', function(e) {
                        if (e.target.closest('.remove-step')) {
                            const stepItems = document.querySelectorAll('.step-item');
                            if (stepItems.length > 1) {
                                e.target.closest('.step-item').remove();
                                updateStepNumbers();
                            }
                        }
                    });
                    
                    // Update step numbers
                    function updateStepNumbers() {
                        const stepItems = document.querySelectorAll('.step-item');
                        stepItems.forEach((item, index) => {
                            const title = item.querySelector('h6');
                            title.textContent = `ขั้นตอนที่ ${index + 1}`;
                            
                            // Update input names
                            const titleInput = item.querySelector('input[name*="[title]"]');
                            const descInput = item.querySelector('input[name*="[description]"]');
                            titleInput.name = `steps[${index}][title]`;
                            descInput.name = `steps[${index}][description]`;
                        });
                        
                        // Enable/disable remove buttons
                        const removeButtons = document.querySelectorAll('.remove-step');
                        removeButtons.forEach(btn => {
                            btn.disabled = stepItems.length <= 1;
                        });
                    }
                });
                </script>
            </div>
            <?php endif; ?>
            <?php endif; ?>
            
            <!-- Colors Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">สีพื้นหลัง</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" name="background_color" value="<?php echo $section['background_color'] ?: '#ffffff'; ?>" 
                               class="h-12 w-20 border border-gray-300 rounded-lg cursor-pointer">
                        <input type="text" name="background_color_text" value="<?php echo $section['background_color']; ?>" 
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                               placeholder="#ffffff">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">สีตัวอักษร (ไม่ได้ใช้งาน)</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" name="text_color" value="#000000" disabled
                               class="h-12 w-20 border border-gray-300 rounded-lg cursor-not-allowed opacity-50">
                        <input type="text" name="text_color_text" value="#000000" disabled
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                               placeholder="ไม่ได้ใช้งาน (column ไม่มี)">
                    </div>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">ลำดับการแสดงผล</label>
                <input type="number" name="sort_order" value="<?php echo $section['sort_order']; ?>" min="0" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                <p class="text-sm text-gray-500 mt-2">ตัวเลขน้อยจะแสดงก่อน</p>
            </div>
        </div>
    </div>
    
    <!-- Advanced Settings -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-cog mr-3"></i>การตั้งค่าขั้นสูง
            </h5>
        </div>
        <div class="p-6 space-y-4">
            <!-- Custom CSS -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Custom CSS</label>
                <textarea name="settings[custom_css]" rows="4" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 font-mono text-sm"
                          placeholder="/* CSS ที่ต้องการเพิ่มสำหรับ section นี้ */"><?php echo htmlspecialchars($settings['custom_css'] ?? ''); ?></textarea>
                <p class="text-sm text-gray-500 mt-1">CSS เพิ่มเติมสำหรับ section นี้</p>
            </div>
            
            <!-- Animation Class -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Animation Class</label>
                <input type="text" name="settings[animation_class]" value="<?php echo htmlspecialchars($settings['animation_class'] ?? ''); ?>" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                       placeholder="animate-fade-in, animate-slide-up, etc.">
                <p class="text-sm text-gray-500 mt-1">คลาส Animation สำหรับ section</p>
            </div>
            
            <!-- Section ID -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Section ID</label>
                <input type="text" name="settings[section_id]" value="<?php echo htmlspecialchars($settings['section_id'] ?? $section['section_key']); ?>" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                       placeholder="home, about, services, contact">
                <p class="text-sm text-gray-500 mt-1">ID สำหรับ section (ใช้สำหรับ navigation)</p>
            </div>
            
            <!-- Section Class -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Section Class</label>
                <input type="text" name="settings[section_class]" value="<?php echo htmlspecialchars($settings['section_class'] ?? ''); ?>" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                       placeholder="py-20 bg-dark-light, hero-gradient min-h-screen">
                <p class="text-sm text-gray-500 mt-1">คลาสสำหรับ section (เช่น py-20 bg-dark-light)</p>
            </div>
            
            <!-- Container Class -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Container Class</label>
                <input type="text" name="settings[container_class]" value="<?php echo htmlspecialchars($settings['container_class'] ?? 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8'); ?>" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                       placeholder="max-w-6xl mx-auto px-8">
                <p class="text-sm text-gray-500 mt-1">คลาสสำหรับ container</p>
            </div>
            
            <!-- Padding Settings -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Padding Top</label>
                    <input type="text" name="settings[padding_top]" value="<?php echo htmlspecialchars($settings['padding_top'] ?? 'py-20'); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                           placeholder="py-20, py-32, pt-16">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Padding Bottom</label>
                    <input type="text" name="settings[padding_bottom]" value="<?php echo htmlspecialchars($settings['padding_bottom'] ?? ''); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                           placeholder="pb-20, pb-32, pb-16">
                </div>
            </div>
            
            <!-- Show/Hide Settings -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="settings[show_title]" value="1" <?php echo ($settings['show_title'] ?? true) ? 'checked' : ''; ?> 
                               class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <span class="ml-2 text-gray-700">แสดงหัวข้อ</span>
                    </label>
                </div>
                <div>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="settings[show_subtitle]" value="1" <?php echo ($settings['show_subtitle'] ?? true) ? 'checked' : ''; ?> 
                               class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <span class="ml-2 text-gray-700">แสดงหัวข้อรอง</span>
                    </label>
                </div>
                <div>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="settings[show_description]" value="1" <?php echo ($settings['show_description'] ?? true) ? 'checked' : ''; ?> 
                               class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <span class="ml-2 text-gray-700">แสดงคำอธิบาย</span>
                    </label>
                </div>
                <div>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="settings[show_button]" value="1" <?php echo ($settings['show_button'] ?? true) ? 'checked' : ''; ?> 
                               class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <span class="ml-2 text-gray-700">แสดงปุ่ม</span>
                    </label>
                </div>
                
                <?php if ($section['section_key'] === 'services'): ?>
                <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        <div>
                            <p class="font-semibold">การแสดงราคาโมเดล</p>
                            <p class="text-sm">สามารถตั้งค่าได้ที่: 
                                <a href="<?php echo ADMIN_URL; ?>/settings/" class="underline hover:text-blue-900">ตั้งค่าระบบ → การตั้งค่าระบบ → แสดงราคาโมเดล</a>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Display existing custom settings -->
            <?php if (!empty($settings)): ?>
            <?php foreach ($settings as $key => $value): ?>
            <?php if (!in_array($key, ['custom_css', 'animation_class', 'section_id', 'container_class', 'padding_top', 'padding_bottom', 'show_title', 'show_subtitle', 'show_description', 'show_button', 'title_colors'])): ?>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <?php echo ucfirst(str_replace('_', ' ', $key)); ?>
                </label>
                <?php if (is_bool($value)): ?>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="settings[<?php echo $key; ?>]" value="1" <?php echo $value ? 'checked' : ''; ?> 
                           class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                    <span class="ml-2 text-gray-700">เปิดใช้งาน</span>
                </label>
                <?php else: ?>
                <input type="text" name="settings[<?php echo $key; ?>]" value="<?php echo htmlspecialchars($value); ?>" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Submit Button -->
    <div class="flex justify-end space-x-4">
        <a href="index.php" class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-times mr-2"></i>ยกเลิก
        </a>
        <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 font-medium">
            <i class="fas fa-save mr-2"></i>บันทึกการเปลี่ยนแปลง
        </button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>

<script>
// Sync color picker with text input
document.querySelectorAll('input[type="color"]').forEach(colorInput => {
    const textInput = colorInput.nextElementSibling.nextElementSibling;
    
    colorInput.addEventListener('input', function() {
        textInput.value = this.value;
        updateTitlePreview();
    });
    
    textInput.addEventListener('input', function() {
        if (/^#[0-9A-F]{6}$/i.test(this.value)) {
            colorInput.value = this.value;
            updateTitlePreview();
        }
    });
});

// Update title preview
function updateTitlePreview() {
    const vibeColor = document.querySelector('input[name="title_colors[vibe]"]')?.value || '#DC2626';
    const dayColor = document.querySelector('input[name="title_colors[day]"]')?.value || '#FFFFFF';
    const bkkColor = document.querySelector('input[name="title_colors[bkk]"]')?.value || '#DC2626';
    
    const previewElement = document.querySelector('.text-2xl.font-bold');
    if (previewElement) {
        previewElement.innerHTML = `
            <span style="color: ${vibeColor};">VIBE</span>
            <span style="color: ${dayColor};">DAY</span>
            <span style="color: ${bkkColor};">BKK</span>
        `;
    }
}

// Initialize preview
document.addEventListener('DOMContentLoaded', function() {
    updateTitlePreview();
});

// Toggle Background Type (Color/Image)
function toggleBackgroundType(checkbox) {
    const hiddenInput = document.getElementById('background_type_value');
    const imageSection = document.getElementById('background_image_section');
    const label = document.getElementById('background_type_label');
    
    const newValue = checkbox.checked ? 'image' : 'color';
    hiddenInput.value = newValue;
    
    // Update label
    if (checkbox.checked) {
        label.textContent = '🖼️ รูปภาพ';
        label.className = 'font-semibold px-3 py-1 rounded-full bg-pink-100 text-pink-700';
        imageSection.classList.remove('hidden');
    } else {
        label.textContent = '🎨 สี';
        label.className = 'font-semibold px-3 py-1 rounded-full bg-purple-100 text-purple-700';
        imageSection.classList.add('hidden');
    }
    
    // Show notification
    Swal.fire({
        icon: checkbox.checked ? 'image' : 'palette',
        title: checkbox.checked ? 'ใช้รูปภาพเป็นพื้นหลัง' : 'ใช้สีเป็นพื้นหลัง',
        text: checkbox.checked 
            ? 'คอนเทนต์ (ข้อความ, ปุ่ม) จะถูกซ่อน' 
            : 'คอนเทนต์จะแสดงปกติ',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        background: '#fff',
        iconColor: checkbox.checked ? '#EC4899' : '#9333EA'
    });
}
</script>




