<?php
/**
 * VIBEDAYBKK Admin - Settings
 * ตั้งค่าระบบ
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_admin(); // Only admin can access

$page_title = 'ตั้งค่าระบบ';
$current_page = 'settings';

$errors = [];

// Get all settings
$settings = [];
$result = db_get_rows($conn, "SELECT * FROM settings ORDER BY setting_key ASC");
foreach ($result as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        // Handle logo image upload
        if (isset($_FILES['logo_image']) && $_FILES['logo_image']['error'] === UPLOAD_ERR_OK) {
            // Delete old logo
            if (!empty($settings['logo_image'])) {
                delete_image($settings['logo_image']);
            }
            
            $upload_result = upload_image($_FILES['logo_image'], 'general');
            if ($upload_result['success']) {
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'logo_image'");
                $stmt->execute([$upload_result['path']]);
            } else {
                $errors[] = 'Logo: ' . $upload_result['message'];
            }
        }
        
        // Handle favicon upload
        if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
            // Delete old favicon
            if (!empty($settings['favicon'])) {
                delete_image($settings['favicon']);
            }
            
            $upload_result = upload_image($_FILES['favicon'], 'general');
            if ($upload_result['success']) {
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'favicon'");
                $stmt->execute([$upload_result['path']]);
            } else {
                $errors[] = 'Favicon: ' . $upload_result['message'];
            }
        }
        
        // Update text settings
        foreach ($_POST as $key => $value) {
            if ($key != 'csrf_token' && strpos($key, 'setting_') === 0) {
                $setting_key = str_replace('setting_', '', $key);
                $setting_value = clean_input($value);
                
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->execute([$setting_value, $setting_key]);
            }
        }
        
        if (empty($errors)) {
            log_activity($pdo, $_SESSION['user_id'], 'update_settings', 'settings', null);
            set_message('success', 'บันทึกการตั้งค่าสำเร็จ');
            redirect(ADMIN_URL . '/settings/');
        }
    }
    
    // Refresh settings
    $settings = [];
    $result = db_get_rows($conn, "SELECT * FROM settings");
    foreach ($result as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

include '../includes/header.php';
?>

<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-900 flex items-center">
        <i class="fas fa-cog mr-3 text-red-600"></i>ตั้งค่าระบบ
    </h2>
    <p class="text-gray-600 mt-1">จัดการการตั้งค่าต่างๆ ของเว็บไซต์</p>
</div>

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
    
    <!-- Logo & Branding -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-image mr-3"></i>โลโก้และไอคอน
            </h5>
        </div>
        <div class="p-6">
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">ประเภทโลโก้</label>
                <div class="flex gap-4">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="setting_logo_type" id="logo_text" value="text" class="peer sr-only" <?php echo ($settings['logo_type'] ?? 'text') == 'text' ? 'checked' : ''; ?>>
                        <div class="flex items-center justify-center px-6 py-3 bg-white border-2 border-gray-300 rounded-lg peer-checked:border-red-600 peer-checked:bg-red-50 hover:border-red-400 transition-all duration-200">
                            <i class="fas fa-font text-lg mr-3 text-gray-600 peer-checked:text-red-600"></i>
                            <span class="font-medium text-gray-700">ข้อความ</span>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="setting_logo_type" id="logo_image" value="image" class="peer sr-only" <?php echo ($settings['logo_type'] ?? 'text') == 'image' ? 'checked' : ''; ?>>
                        <div class="flex items-center justify-center px-6 py-3 bg-white border-2 border-gray-300 rounded-lg peer-checked:border-red-600 peer-checked:bg-red-50 hover:border-red-400 transition-all duration-200">
                            <i class="fas fa-image text-lg mr-3 text-gray-600 peer-checked:text-red-600"></i>
                            <span class="font-medium text-gray-700">รูปภาพ</span>
                        </div>
                    </label>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ข้อความโลโก้</label>
                    <input type="text" class="w-full px-4 py-3 text-lg border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" name="setting_logo_text" value="<?php echo $settings['logo_text'] ?? 'VIBEDAYBKK'; ?>">
                    <p class="text-sm text-gray-500 mt-2">ใช้เมื่อเลือกประเภทโลโก้เป็น "ข้อความ"</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">รูปภาพโลโก้</label>
                    <?php if (!empty($settings['logo_image'])): ?>
                    <div class="mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <img src="<?php echo UPLOADS_URL . '/' . $settings['logo_image']; ?>" class="h-20 object-contain mb-2">
                        <p class="text-xs text-gray-500">รูปปัจจุบัน</p>
                    </div>
                    <?php endif; ?>
                    <input type="file" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-red-50 file:text-red-700 hover:file:bg-red-100" name="logo_image" accept="image/*">
                    <p class="text-sm text-gray-500 mt-2">แนะนำขนาด: 200x60px, PNG มีพื้นหลังโปร่งใส</p>
                </div>
            </div>
            
            <div class="border-t border-gray-200 my-6"></div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Favicon</label>
                    <?php if (!empty($settings['favicon'])): ?>
                    <div class="mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200 flex items-center">
                        <img src="<?php echo UPLOADS_URL . '/' . $settings['favicon']; ?>" class="h-8 w-8 object-contain mr-3">
                        <p class="text-xs text-gray-500">Favicon ปัจจุบัน</p>
                    </div>
                    <?php endif; ?>
                    <input type="file" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-red-50 file:text-red-700 hover:file:bg-red-100" name="favicon" accept="image/x-icon,image/png,image/svg+xml">
                    <p class="text-sm text-gray-500 mt-2">แนะนำขนาด: 32x32px หรือ 64x64px (.ico, .png)</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Site Info -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-globe mr-3"></i>ข้อมูลเว็บไซต์
            </h5>
        </div>
        <div class="p-6">
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">ชื่อเว็บไซต์</label>
                <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" name="setting_site_name" value="<?php echo $settings['site_name'] ?? ''; ?>">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">อีเมล</label>
                    <input type="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" name="setting_site_email" value="<?php echo $settings['site_email'] ?? ''; ?>">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">เบอร์โทรศัพท์</label>
                    <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" name="setting_site_phone" value="<?php echo $settings['site_phone'] ?? ''; ?>">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">LINE ID</label>
                    <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" name="setting_site_line" value="<?php echo $settings['site_line'] ?? ''; ?>">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">ที่อยู่</label>
                <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" name="setting_site_address" rows="3"><?php echo $settings['site_address'] ?? ''; ?></textarea>
            </div>
        </div>
    </div>
    
    <!-- System Settings -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-sliders-h mr-3"></i>การตั้งค่าระบบ
            </h5>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">จำนวนรายการต่อหน้า</label>
                    <input type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200" name="setting_items_per_page" value="<?php echo $settings['items_per_page'] ?? 12; ?>">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">จองล่วงหน้าอย่างน้อย (วัน)</label>
                    <input type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200" name="setting_booking_advance_days" value="<?php echo $settings['booking_advance_days'] ?? 3; ?>">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Social Media -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-pink-600 to-pink-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-share-alt mr-3"></i>โซเชียลมีเดีย
            </h5>
        </div>
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fab fa-facebook mr-2 text-blue-600"></i>Facebook
                </label>
                <input type="url" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200" name="setting_facebook_url" value="<?php echo $settings['facebook_url'] ?? ''; ?>" placeholder="https://facebook.com/vibedaybkk">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fab fa-instagram mr-2 text-pink-600"></i>Instagram
                </label>
                <input type="url" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200" name="setting_instagram_url" value="<?php echo $settings['instagram_url'] ?? ''; ?>" placeholder="https://instagram.com/vibedaybkk">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fab fa-twitter mr-2 text-blue-400"></i>Twitter / X
                </label>
                <input type="url" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200" name="setting_twitter_url" value="<?php echo $settings['twitter_url'] ?? ''; ?>" placeholder="https://x.com/vibedaybkk">
            </div>
        </div>
    </div>
    
    <div class="flex justify-end">
        <button type="submit" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-lg font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
            <i class="fas fa-save mr-3"></i>บันทึกการตั้งค่า
        </button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>

