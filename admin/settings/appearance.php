<?php
/**
 * VIBEDAYBKK Admin - Appearance Settings
 * ตั้งค่าฟอนต์และรูปลักษณ์
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_permission('settings', 'edit');

$page_title = 'ตั้งค่าฟอนต์และรูปลักษณ์';
$current_page = 'settings';

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $font_family = clean_input($_POST['font_family']);
        $font_size_base = (int)$_POST['font_size_base'];
        $font_size_scale = (float)$_POST['font_size_scale'];
        
        // Validate
        if ($font_size_base < 10 || $font_size_base > 24) {
            $errors[] = 'ขนาดฟอนต์พื้นฐานต้องอยู่ระหว่าง 10-24 px';
        }
        
        if ($font_size_scale < 0.7 || $font_size_scale > 1.3) {
            $errors[] = 'สเกลต้องอยู่ระหว่าง 0.7-1.3';
        }
        
        if (empty($errors)) {
            // Update settings
            $conn->query("UPDATE settings SET setting_value = '{$font_family}' WHERE setting_key = 'font_family'");
            $conn->query("UPDATE settings SET setting_value = '{$font_size_base}' WHERE setting_key = 'font_size_base'");
            $conn->query("UPDATE settings SET setting_value = '{$font_size_scale}' WHERE setting_key = 'font_size_scale'");
            
            log_activity($conn, $_SESSION['user_id'], 'update', 'settings', 0, 'Updated appearance settings');
            
            $success = true;
            set_message('success', 'บันทึกการตั้งค่าสำเร็จ');
        }
    }
}

// Get current settings
$result = $conn->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('font_family', 'font_size_base', 'font_size_scale')");
$current_settings = [];
while ($row = $result->fetch_assoc()) {
    $current_settings[$row['setting_key']] = $row['setting_value'];
}

$font_family = $current_settings['font_family'] ?? 'Noto Sans Thai';
$font_size_base = $current_settings['font_size_base'] ?? '14';
$font_size_scale = $current_settings['font_size_scale'] ?? '0.875';

include '../includes/header.php';
?>

<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-900">
        <i class="fas fa-palette mr-3 text-red-600"></i>ตั้งค่าฟอนต์และรูปลักษณ์
    </h2>
    <p class="text-gray-600 mt-1">ปรับแต่งฟอนต์และขนาดตัวอักษรทั้งระบบ</p>
</div>

<?php if ($success): ?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
    <h5 class="font-bold mb-2 flex items-center">
        <i class="fas fa-check-circle mr-2"></i>บันทึกสำเร็จ!
    </h5>
    <p>การตั้งค่าจะมีผลทันทีในหน้าถัดไป (รีเฟรชหน้า)</p>
</div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
    <h5 class="font-bold mb-2 flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>พบข้อผิดพลาด:
    </h5>
    <ul class="list-disc list-inside">
        <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
    <!-- Font Settings -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
            <h3 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-font mr-3"></i>การตั้งค่าฟอนต์
            </h3>
        </div>
        <div class="p-6 space-y-6">
            <!-- Font Family -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    ฟอนต์หลัก
                </label>
                <select name="font_family" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="Noto Sans Thai" <?php echo $font_family == 'Noto Sans Thai' ? 'selected' : ''; ?>>Noto Sans Thai (แนะนำ)</option>
                    <option value="Kanit" <?php echo $font_family == 'Kanit' ? 'selected' : ''; ?>>Kanit</option>
                    <option value="Sarabun" <?php echo $font_family == 'Sarabun' ? 'selected' : ''; ?>>Sarabun</option>
                    <option value="Prompt" <?php echo $font_family == 'Prompt' ? 'selected' : ''; ?>>Prompt</option>
                    <option value="Mitr" <?php echo $font_family == 'Mitr' ? 'selected' : ''; ?>>Mitr</option>
                </select>
                <p class="text-sm text-gray-500 mt-2">
                    <i class="fas fa-info-circle"></i> Noto Sans Thai - ฟอนต์จาก Google ที่อ่านง่าย รองรับภาษาไทยเต็มรูปแบบ
                </p>
            </div>
            
            <!-- Font Size Base -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    ขนาดฟอนต์พื้นฐาน
                </label>
                <div class="flex items-center space-x-4">
                    <input type="range" name="font_size_base" 
                           min="10" max="24" step="1" 
                           value="<?php echo $font_size_base; ?>"
                           class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                           oninput="document.getElementById('fontSizeValue').textContent = this.value">
                    <div class="flex items-center bg-gray-100 px-4 py-2 rounded-lg">
                        <span id="fontSizeValue" class="text-lg font-bold text-gray-900 w-8 text-center"><?php echo $font_size_base; ?></span>
                        <span class="text-gray-600 ml-1">px</span>
                    </div>
                </div>
                <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-lightbulb mr-1"></i> 
                        <strong>คำแนะนำ:</strong> 14px = ขนาดมาตรฐาน | 12px = เล็กกว่า | 16px = ใหญ่กว่า
                    </p>
                </div>
            </div>
            
            <!-- Font Scale -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    สเกลขนาดฟอนต์
                </label>
                <div class="flex items-center space-x-4">
                    <input type="range" name="font_size_scale" 
                           min="0.7" max="1.3" step="0.05" 
                           value="<?php echo $font_size_scale; ?>"
                           class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                           oninput="document.getElementById('scaleValue').textContent = this.value">
                    <div class="flex items-center bg-gray-100 px-4 py-2 rounded-lg">
                        <span id="scaleValue" class="text-lg font-bold text-gray-900 w-12 text-center"><?php echo $font_size_scale; ?></span>
                        <span class="text-gray-600 ml-1">x</span>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 mt-3">
                    <button type="button" onclick="setScale(0.8)" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm transition">
                        เล็ก (0.8x)
                    </button>
                    <button type="button" onclick="setScale(0.875)" class="px-3 py-2 bg-blue-100 hover:bg-blue-200 rounded-lg text-sm transition">
                        กลาง (0.875x)
                    </button>
                    <button type="button" onclick="setScale(1.0)" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm transition">
                        ปกติ (1.0x)
                    </button>
                </div>
                <p class="text-sm text-gray-500 mt-2">
                    <i class="fas fa-info-circle"></i> สเกลจะคูณกับขนาดพื้นฐาน (0.875 = เล็กกว่าปกติ 12.5%)
                </p>
            </div>
            
            <!-- Preview -->
            <div class="border-t pt-6">
                <label class="block text-sm font-semibold text-gray-700 mb-4">
                    <i class="fas fa-eye mr-2"></i>ตัวอย่าง
                </label>
                <div class="p-6 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl border-2 border-purple-200"
                     style="font-family: '<?php echo $font_family; ?>', sans-serif; font-size: calc(<?php echo $font_size_base; ?>px * <?php echo $font_size_scale; ?>);">
                    <h1 class="text-3xl font-bold text-gray-900 mb-3" style="font-size: calc(<?php echo $font_size_base; ?>px * <?php echo $font_size_scale; ?> * 2);">
                        VIBEDAYBKK
                    </h1>
                    <h2 class="text-2xl font-semibold text-gray-800 mb-2" style="font-size: calc(<?php echo $font_size_base; ?>px * <?php echo $font_size_scale; ?> * 1.75);">
                        บริการโมเดลและนางแบบมืออาชีพ
                    </h2>
                    <p class="text-gray-700 mb-2">
                        นี่คือตัวอย่างข้อความปกติ (Body Text) ที่จะแสดงในระบบ
                    </p>
                    <p class="text-sm text-gray-600">
                        นี่คือข้อความขนาดเล็ก (Small Text) สำหรับรายละเอียดเพิ่มเติม
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="flex items-center space-x-4">
        <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white rounded-lg transition-all duration-200 font-semibold shadow-lg hover:shadow-xl">
            <i class="fas fa-save mr-2"></i>บันทึกการตั้งค่า
        </button>
        <a href="index.php" class="inline-flex items-center px-8 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-semibold">
            <i class="fas fa-arrow-left mr-2"></i>กลับ
        </a>
    </div>
</form>

<script>
function setScale(value) {
    const input = document.querySelector('input[name="font_size_scale"]');
    input.value = value;
    document.getElementById('scaleValue').textContent = value;
}
</script>

<?php include '../includes/footer.php'; ?>

