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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $title = clean_input($_POST['title'] ?? '');
        $subtitle = clean_input($_POST['subtitle'] ?? '');
        $description = clean_input($_POST['description'] ?? '');
        $button_text = clean_input($_POST['button_text'] ?? '');
        $button_link = clean_input($_POST['button_link'] ?? '');
        $background_color = clean_input($_POST['background_color'] ?? '');
        $text_color = clean_input($_POST['text_color'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        
        // Handle background image upload
        $background_image = $section['background_image'];
        if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] === UPLOAD_ERR_OK) {
            // Delete old image
            if (!empty($background_image)) {
                delete_image($background_image);
            }
            
            $upload_result = upload_image($_FILES['background_image'], 'homepage');
            if ($upload_result['success']) {
                $background_image = $upload_result['path'];
            } else {
                $errors[] = 'Background Image: ' . $upload_result['message'];
            }
        }
        
        // Build settings JSON
        $settings = [];
        if (isset($_POST['settings']) && is_array($_POST['settings'])) {
            foreach ($_POST['settings'] as $key => $value) {
                $settings[$key] = $value;
            }
        }
        $settings_json = json_encode($settings);
        
        if (empty($errors)) {
            $sql = "UPDATE homepage_sections SET 
                    title = ?, subtitle = ?, description = ?,
                    button_text = ?, button_link = ?,
                    background_image = ?, background_color = ?, text_color = ?,
                    sort_order = ?, settings = ?
                    WHERE id = ?";
            
            $params = [
                $title, $subtitle, $description,
                $button_text, $button_link,
                $background_image, $background_color, $text_color,
                $sort_order, $settings_json, $id
            ];
            
            if (db_execute($conn, $sql, $params)) {
                $success = true;
                // Refresh section data
                $section = db_get_row($conn, "SELECT * FROM homepage_sections WHERE id = ?", [$id]);
            } else {
                $errors[] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
            }
        }
    }
}

// Parse settings
$settings = json_decode($section['settings'], true) ?: [];

include '../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-edit mr-3 text-red-600"></i>แก้ไข <?php echo $section['section_name']; ?>
        </h2>
        <p class="text-gray-600 mt-1">ประเภท: <?php echo ucfirst($section['section_type']); ?></p>
    </div>
    <a href="index.php" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
        <i class="fas fa-arrow-left mr-2"></i>กลับ
    </a>
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
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
    <?php foreach ($errors as $error): ?>
    <div class="flex items-center mb-1">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <span><?php echo $error; ?></span>
    </div>
    <?php endforeach; ?>
</div>
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
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">หัวข้อรอง</label>
                <input type="text" name="subtitle" value="<?php echo htmlspecialchars($section['subtitle']); ?>" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">คำอธิบาย</label>
                <textarea name="description" rows="5" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"><?php echo htmlspecialchars($section['description']); ?></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ข้อความปุ่ม</label>
                    <input type="text" name="button_text" value="<?php echo htmlspecialchars($section['button_text']); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ลิงก์ปุ่ม</label>
                    <input type="text" name="button_link" value="<?php echo htmlspecialchars($section['button_link']); ?>" 
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
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">รูปพื้นหลัง</label>
                <?php if (!empty($section['background_image'])): ?>
                <div class="mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <img src="<?php echo UPLOADS_URL . '/' . $section['background_image']; ?>" 
                         class="h-32 object-cover rounded mb-2">
                    <p class="text-xs text-gray-500">รูปปัจจุบัน</p>
                </div>
                <?php endif; ?>
                <input type="file" name="background_image" accept="image/*" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                <p class="text-sm text-gray-500 mt-2">แนะนำขนาด: 1920x1080px</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                    <label class="block text-sm font-semibold text-gray-700 mb-2">สีตัวอักษร</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" name="text_color" value="<?php echo $section['text_color'] ?: '#000000'; ?>" 
                               class="h-12 w-20 border border-gray-300 rounded-lg cursor-pointer">
                        <input type="text" name="text_color_text" value="<?php echo $section['text_color']; ?>" 
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                               placeholder="#000000">
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
    <?php if (!empty($settings)): ?>
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-cog mr-3"></i>การตั้งค่าขั้นสูง
            </h5>
        </div>
        <div class="p-6 space-y-4">
            <?php foreach ($settings as $key => $value): ?>
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
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
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
    });
    
    textInput.addEventListener('input', function() {
        if (/^#[0-9A-F]{6}$/i.test(this.value)) {
            colorInput.value = this.value;
        }
    });
});
</script>

