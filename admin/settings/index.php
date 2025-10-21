<?php
/**
 * VIBEDAYBKK Admin - Settings
 * ตั้งค่าระบบ
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check - allow view for editor and above
require_permission('settings', 'view');
$can_edit = has_permission('settings', 'edit');

$page_title = 'ตั้งค่าระบบ';
$current_page = 'settings';

$errors = [];

// Get all settings
$settings = [];
$result = db_get_rows($conn, "SELECT * FROM settings ORDER BY setting_key ASC");
foreach ($result as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $can_edit) {
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
                $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'logo_image'");
                $stmt->bind_param('s', $upload_result['path']);
                $stmt->execute();
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
                $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'favicon'");
                $stmt->bind_param('s', $upload_result['path']);
                $stmt->execute();
            } else {
                $errors[] = 'Favicon: ' . $upload_result['message'];
            }
        }
        
        // Update text settings
        $processed_keys = [];
        foreach ($_POST as $key => $value) {
            if ($key != 'csrf_token' && strpos($key, 'setting_') === 0) {
                $setting_key = str_replace('setting_', '', $key);
                
                // ข้ามถ้า key นี้ถูก process แล้ว (กรณี checkbox ที่มี hidden field)
                if (in_array($setting_key, $processed_keys)) {
                    continue;
                }
                
                // สำหรับ checkbox ใช้ค่าสุดท้าย
                if (is_array($value)) {
                    $setting_value = end($value);
                } else {
                    $setting_value = clean_input($value);
                }
                
                $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->bind_param('ss', $setting_value, $setting_key);
                $stmt->execute();
                $stmt->close();
                
                $processed_keys[] = $setting_key;
            }
        }
        
        if (empty($errors)) {
            log_activity($conn, $_SESSION['user_id'], 'update_settings', 'settings', null);
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
require_once '../includes/readonly-notice.php';
require_once '../includes/locked-form.php';
?>

<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-900 flex items-center">
        <i class="fas fa-cog mr-3 text-red-600"></i>ตั้งค่าระบบ
        <?php if (!$can_edit): ?>
        <span class="ml-3 text-lg text-yellow-600">
            <i class="fas fa-eye"></i> ดูอย่างเดียว
        </span>
        <?php endif; ?>
    </h2>
    <p class="text-gray-600 mt-1">จัดการการตั้งค่าต่างๆ ของเว็บไซต์</p>
</div>

<?php if (!$can_edit): ?>
    <?php show_readonly_notice('ตั้งค่าระบบ'); ?>
<?php endif; ?>

<!-- Settings Menu -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <a href="index.php" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-200 border-2 border-blue-500">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-cog text-white text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">ตั้งค่าทั่วไป</h3>
        </div>
        <p class="text-gray-600 text-sm">Logo, ชื่อเว็บไซต์, ข้อมูลติดต่อ</p>
    </a>
    
    <a href="appearance.php" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-200 hover:border-2 hover:border-purple-500">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-palette text-white text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">ฟอนต์และรูปลักษณ์</h3>
        </div>
        <p class="text-gray-600 text-sm">ตั้งค่าฟอนต์, ขนาดตัวอักษร, สีสัน</p>
    </a>
    
    <a href="seo.php" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-200 hover:border-2 hover:border-orange-500">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-search text-white text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">SEO & Analytics</h3>
        </div>
        <p class="text-gray-600 text-sm">SEO, Meta Tags, Google Analytics</p>
    </a>
    
    <a href="social.php" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-200 hover:border-2 hover:border-pink-500">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-share-alt text-white text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">โซเชียล & Go to Top</h3>
        </div>
        <p class="text-gray-600 text-sm">ไอคอนโซเชียล, ปุ่ม Go to Top, สีธีม</p>
    </a>
    
    <a href="<?php echo ADMIN_URL; ?>/homepage/" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-200 hover:border-2 hover:border-green-500">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-gradient-to-r from-green-600 to-green-700 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-home text-white text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">จัดการหน้าแรก</h3>
        </div>
        <p class="text-gray-600 text-sm">Section, Gallery, Features</p>
    </a>
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">จำนวนรายการต่อหน้า</label>
                    <input type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200" name="setting_items_per_page" value="<?php echo $settings['items_per_page'] ?? 12; ?>">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">จองล่วงหน้าอย่างน้อย (วัน)</label>
                    <input type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200" name="setting_booking_advance_days" value="<?php echo $settings['booking_advance_days'] ?? 3; ?>">
                </div>
            </div>
            
            <div class="border-t border-gray-200 my-6"></div>
            
            <!-- Display Settings -->
            <div class="space-y-4">
                <h6 class="text-sm font-semibold text-gray-700 mb-3">การแสดงผลในหน้าแรก</h6>
                
                <div class="p-4 bg-purple-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <span class="text-gray-900 font-semibold">แสดงราคาโมเดล</span>
                            <p class="text-sm text-gray-600">แสดงราคาของโมเดลในหน้าแรกและหน้ารายละเอียด</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="show_model_price_checkbox" 
                                   <?php echo ($settings['show_model_price'] ?? '1') == '1' ? 'checked' : ''; ?>
                                   class="sr-only peer"
                                   onchange="togglePriceSetting(this)">
                            <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                    </div>
                    <input type="hidden" id="show_model_price_value" name="setting_show_model_price" 
                           value="<?php echo ($settings['show_model_price'] ?? '1'); ?>">
                </div>
                
                <div class="p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <span class="text-gray-900 font-semibold">แสดงรายละเอียดส่วนตัว</span>
                            <p class="text-sm text-gray-600">แสดงข้อมูลส่วนตัวของโมเดล (น้ำหนัก, ส่วนสูง, วันเกิด, ประสบการณ์)</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="show_model_details_checkbox" 
                                   <?php echo ($settings['show_model_details'] ?? '1') == '1' ? 'checked' : ''; ?>
                                   class="sr-only peer"
                                   onchange="toggleDetailsSetting(this)">
                            <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    <input type="hidden" id="show_model_details_value" name="setting_show_model_details" 
                           value="<?php echo ($settings['show_model_details'] ?? '1'); ?>">
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
        <?php if (!$can_edit): ?>
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-6 py-3 rounded-lg mr-4 flex items-center">
            <i class="fas fa-lock mr-2"></i>
            <span>คุณไม่มีสิทธิ์แก้ไขการตั้งค่า</span>
        </div>
        <?php endif; ?>
        <button type="submit" 
                id="submit-btn"
                <?php echo !$can_edit ? 'disabled' : ''; ?>
                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-lg font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 <?php echo !$can_edit ? 'opacity-50 cursor-not-allowed' : ''; ?>">
            <i class="fas fa-save mr-3"></i>บันทึกการตั้งค่า
        </button>
    </div>
</form>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Debug form submission
document.querySelector('form').addEventListener('submit', function(e) {
    console.log('Form submitted');
    console.log('Can edit:', <?php echo $can_edit ? 'true' : 'false'; ?>);
    
    <?php if (!$can_edit): ?>
    e.preventDefault();
    Swal.fire({
        icon: 'warning',
        title: 'ไม่มีสิทธิ์',
        text: 'คุณไม่มีสิทธิ์แก้ไขการตั้งค่า กรุณาติดต่อผู้ดูแลระบบ',
        confirmButtonColor: '#dc2626'
    });
    return false;
    <?php endif; ?>
});

// Toggle price setting with notification
function togglePriceSetting(checkbox) {
    const hiddenInput = document.getElementById('show_model_price_value');
    const newValue = checkbox.checked ? '1' : '0';
    hiddenInput.value = newValue;
    
    // Show notification
    const icon = checkbox.checked ? 'success' : 'info';
    const title = checkbox.checked ? 'เปิดการแสดงราคา' : 'ปิดการแสดงราคา';
    const text = checkbox.checked 
        ? 'ราคาจะแสดงในหน้าแรกและหน้ารายละเอียดโมเดล' 
        : 'ราคาจะถูกซ่อนจากหน้าเว็บทั้งหมด';
    
    Swal.fire({
        icon: icon,
        title: title,
        text: text,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        showClass: {
            popup: 'animate__animated animate__fadeInRight'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutRight'
        }
    });
}

// Toggle details setting with notification
function toggleDetailsSetting(checkbox) {
    const hiddenInput = document.getElementById('show_model_details_value');
    const newValue = checkbox.checked ? '1' : '0';
    hiddenInput.value = newValue;
    
    // Show notification
    const icon = checkbox.checked ? 'success' : 'info';
    const title = checkbox.checked ? 'เปิดการแสดงรายละเอียด' : 'ปิดการแสดงรายละเอียด';
    const text = checkbox.checked 
        ? 'รายละเอียดส่วนตัวจะแสดง (น้ำหนัก, ส่วนสูง, วันเกิด, ประสบการณ์)' 
        : 'รายละเอียดส่วนตัวจะถูกซ่อนจากหน้าเว็บทั้งหมด';
    
    Swal.fire({
        icon: icon,
        title: title,
        text: text,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        showClass: {
            popup: 'animate__animated animate__fadeInRight'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutRight'
        }
    });
}

// Show success/error messages
<?php if (isset($_SESSION['success'])): ?>
Swal.fire({
    icon: 'success',
    title: 'สำเร็จ!',
    text: '<?php echo $_SESSION['success']; ?>',
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    showClass: {
        popup: 'animate__animated animate__fadeInRight'
    }
});
<?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
Swal.fire({
    icon: 'error',
    title: 'ผิดพลาด!',
    text: '<?php echo $_SESSION['error']; ?>',
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    showClass: {
        popup: 'animate__animated animate__fadeInRight'
    }
});
<?php unset($_SESSION['error']); ?>
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>



