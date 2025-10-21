<?php
/**
 * VIBEDAYBKK Admin - Social Media & Go to Top Settings
 * ตั้งค่าไอคอนโซเชียลและปุ่ม Go to Top
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check - allow view for editor and above
require_permission('settings', 'view');
$can_edit = has_permission('settings', 'edit');

$page_title = 'ตั้งค่าโซเชียลมีเดีย & Go to Top';
$current_page = 'settings';

$success = false;
$errors = [];

// Social platforms
$social_platforms = [
    'facebook' => ['name' => 'Facebook', 'default_icon' => 'fa-facebook-f', 'color' => 'from-blue-600 to-blue-700'],
    'instagram' => ['name' => 'Instagram', 'default_icon' => 'fa-instagram', 'color' => 'from-pink-600 to-purple-600'],
    'twitter' => ['name' => 'Twitter', 'default_icon' => 'fa-twitter', 'color' => 'from-blue-400 to-blue-500'],
    'line' => ['name' => 'LINE', 'default_icon' => 'fa-line', 'color' => 'from-green-500 to-green-600'],
    'youtube' => ['name' => 'YouTube', 'default_icon' => 'fa-youtube', 'color' => 'from-red-600 to-red-700'],
    'tiktok' => ['name' => 'TikTok', 'default_icon' => 'fa-tiktok', 'color' => 'from-gray-800 to-gray-900']
];

// Get current settings
$settings = [];
$result = $conn->query("SELECT `setting_key`, `setting_value` FROM settings WHERE `setting_key` LIKE 'social_%' OR `setting_key` LIKE 'gototop_%' OR `setting_key` LIKE 'theme_%'");
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $updates = [];
        
        // Social media settings
        foreach ($social_platforms as $platform => $info) {
            $updates["social_{$platform}_url"] = clean_input($_POST["social_{$platform}_url"] ?? '');
            $updates["social_{$platform}_icon"] = clean_input($_POST["social_{$platform}_icon"] ?? $info['default_icon']);
            $updates["social_{$platform}_enabled"] = isset($_POST["social_{$platform}_enabled"]) ? '1' : '0';
        }
        
        // Go to Top settings
        $updates['gototop_enabled'] = isset($_POST['gototop_enabled']) ? '1' : '0';
        $updates['gototop_icon'] = clean_input($_POST['gototop_icon'] ?? 'fa-arrow-up');
        $updates['gototop_bg_color'] = clean_input($_POST['gototop_bg_color'] ?? 'bg-red-primary');
        $updates['gototop_text_color'] = clean_input($_POST['gototop_text_color'] ?? 'text-white');
        $updates['gototop_position'] = $_POST['gototop_position'] ?? 'right';
        
        // Theme colors
        $updates['theme_primary_color'] = clean_input($_POST['theme_primary_color'] ?? '#DC2626');
        $updates['theme_secondary_color'] = clean_input($_POST['theme_secondary_color'] ?? '#1F2937');
        $updates['theme_accent_color'] = clean_input($_POST['theme_accent_color'] ?? '#F59E0B');
        $updates['theme_background_color'] = clean_input($_POST['theme_background_color'] ?? '#0F172A');
        $updates['theme_text_color'] = clean_input($_POST['theme_text_color'] ?? '#F3F4F6');
        
        // Update database
        foreach ($updates as $key => $value) {
            $stmt = $conn->prepare("INSERT INTO settings (`setting_key`, `setting_value`, `setting_type`) VALUES (?, ?, 'text') ON DUPLICATE KEY UPDATE `setting_value` = ?");
            $stmt->bind_param('sss', $key, $value, $value);
            $stmt->execute();
            $stmt->close();
        }
        
        log_activity($conn, $_SESSION['user_id'], 'update', 'settings', 0, 'Updated social and go to top settings');
        
        $success = true;
        $settings = $updates;
    }
}

include '../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h2 class="text-3xl font-bold text-gray-900 flex items-center">
        <i class="fas fa-share-alt mr-3 text-purple-600"></i>ตั้งค่าโซเชียลมีเดีย & Go to Top
    </h2>
    <a href="index.php" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
        <i class="fas fa-arrow-left mr-2"></i>กลับ
    </a>
</div>

<?php if ($success): ?>
<script>
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});
Toast.fire({
    icon: 'success',
    title: 'บันทึกการตั้งค่าเรียบร้อยแล้ว'
});
</script>
<?php endif; ?>

<form method="POST" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    <input type="hidden" id="ajax_csrf" value="<?php echo generate_csrf_token(); ?>">
    
    <!-- Social Media Icons -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-share-alt mr-3"></i>ไอคอนโซเชียลมีเดีย
            </h5>
        </div>
        <div class="p-6 space-y-6">
            <?php foreach ($social_platforms as $platform => $info): ?>
            <div class="border-b border-gray-200 pb-6 last:border-0 last:pb-0">
                <div class="flex items-center justify-between mb-4">
                    <h6 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-r <?php echo $info['color']; ?> rounded-lg flex items-center justify-center mr-3">
                            <i class="fab <?php echo $settings["social_{$platform}_icon"] ?? $info['default_icon']; ?> text-white"></i>
                        </div>
                        <?php echo $info['name']; ?>
                    </h6>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="social_<?php echo $platform; ?>_enabled" value="1" 
                               class="sr-only peer" <?php echo ($settings["social_{$platform}_enabled"] ?? '0') == '1' ? 'checked' : ''; ?> data-platform="<?php echo $platform; ?>">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">เปิดใช้งาน</span>
                    </label>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">URL ลิงก์</label>
                        <input type="url" name="social_<?php echo $platform; ?>_url" 
                               value="<?php echo htmlspecialchars($settings["social_{$platform}_url"] ?? ''); ?>" 
                               placeholder="https://<?php echo $platform; ?>.com/vibedaybkk"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">ไอคอน</label>
                        <div class="flex items-center space-x-3">
                            <input type="text" name="social_<?php echo $platform; ?>_icon" 
                                   id="icon-<?php echo $platform; ?>" 
                                   value="<?php echo htmlspecialchars($settings["social_{$platform}_icon"] ?? $info['default_icon']); ?>" 
                                   class="icon-picker-input flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                            <div id="icon-<?php echo $platform; ?>_preview" class="w-12 h-12 bg-gradient-to-br from-blue-100 to-purple-100 rounded-lg flex items-center justify-center border-2 border-blue-300">
                                <i class="fab <?php echo $settings["social_{$platform}_icon"] ?? $info['default_icon']; ?> text-2xl text-blue-600"></i>
                            </div>
                            <button type="button" class="icon-picker-btn px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 font-medium shadow-lg">
                                <i class="fas fa-icons mr-2"></i>เลือกไอคอน
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Go to Top Button -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-arrow-up mr-3"></i>ปุ่ม Go to Top
            </h5>
        </div>
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between">
                <h6 class="text-lg font-semibold text-gray-900">เปิดใช้งานปุ่ม Go to Top</h6>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="gototop_enabled" value="1" 
                           class="sr-only peer" <?php echo ($settings['gototop_enabled'] ?? '1') == '1' ? 'checked' : ''; ?>>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                </label>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ไอคอน</label>
                    <div class="flex items-center space-x-3">
                        <input type="text" name="gototop_icon" id="gototop-icon" 
                               value="<?php echo htmlspecialchars($settings['gototop_icon'] ?? 'fa-arrow-up'); ?>" 
                               class="icon-picker-input flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200">
                        <div id="gototop-icon_preview" class="w-12 h-12 bg-gradient-to-br from-red-100 to-orange-100 rounded-lg flex items-center justify-center border-2 border-red-300">
                            <i class="fas <?php echo $settings['gototop_icon'] ?? 'fa-arrow-up'; ?> text-2xl text-red-600"></i>
                        </div>
                        <button type="button" class="icon-picker-btn px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium shadow-lg">
                            <i class="fas fa-icons mr-2"></i>เลือกไอคอน
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">สีพื้นหลัง (Tailwind Class)</label>
                    <input type="text" name="gototop_bg_color" 
                           value="<?php echo htmlspecialchars($settings['gototop_bg_color'] ?? 'bg-red-primary'); ?>" 
                           placeholder="bg-red-primary"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200">
                    <p class="text-sm text-gray-500 mt-2">เช่น: bg-red-600, bg-blue-500</p>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">สีข้อความ (Tailwind Class)</label>
                    <input type="text" name="gototop_text_color" 
                           value="<?php echo htmlspecialchars($settings['gototop_text_color'] ?? 'text-white'); ?>" 
                           placeholder="text-white"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200">
                    <p class="text-sm text-gray-500 mt-2">เช่น: text-white, text-gray-900</p>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">ตำแหน่ง</label>
                <select name="gototop_position" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200">
                    <option value="right" <?php echo ($settings['gototop_position'] ?? 'right') == 'right' ? 'selected' : ''; ?>>ขวามือ</option>
                    <option value="left" <?php echo ($settings['gototop_position'] ?? 'right') == 'left' ? 'selected' : ''; ?>>ซ้ายมือ</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Theme Colors -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-palette mr-3"></i>สีธีม
            </h5>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">สีหลัก (Primary)</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" name="theme_primary_color" 
                               value="<?php echo htmlspecialchars($settings['theme_primary_color'] ?? '#DC2626'); ?>" 
                               class="w-16 h-12 rounded-lg border-2 border-gray-300 cursor-pointer">
                        <input type="text" readonly 
                               value="<?php echo htmlspecialchars($settings['theme_primary_color'] ?? '#DC2626'); ?>" 
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">สีรอง (Secondary)</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" name="theme_secondary_color" 
                               value="<?php echo htmlspecialchars($settings['theme_secondary_color'] ?? '#1F2937'); ?>" 
                               class="w-16 h-12 rounded-lg border-2 border-gray-300 cursor-pointer">
                        <input type="text" readonly 
                               value="<?php echo htmlspecialchars($settings['theme_secondary_color'] ?? '#1F2937'); ?>" 
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">สีเน้น (Accent)</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" name="theme_accent_color" 
                               value="<?php echo htmlspecialchars($settings['theme_accent_color'] ?? '#F59E0B'); ?>" 
                               class="w-16 h-12 rounded-lg border-2 border-gray-300 cursor-pointer">
                        <input type="text" readonly 
                               value="<?php echo htmlspecialchars($settings['theme_accent_color'] ?? '#F59E0B'); ?>" 
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">สีพื้นหลัง</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" name="theme_background_color" 
                               value="<?php echo htmlspecialchars($settings['theme_background_color'] ?? '#0F172A'); ?>" 
                               class="w-16 h-12 rounded-lg border-2 border-gray-300 cursor-pointer">
                        <input type="text" readonly 
                               value="<?php echo htmlspecialchars($settings['theme_background_color'] ?? '#0F172A'); ?>" 
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">สีข้อความ</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" name="theme_text_color" 
                               value="<?php echo htmlspecialchars($settings['theme_text_color'] ?? '#F3F4F6'); ?>" 
                               class="w-16 h-12 rounded-lg border-2 border-gray-300 cursor-pointer">
                        <input type="text" readonly 
                               value="<?php echo htmlspecialchars($settings['theme_text_color'] ?? '#F3F4F6'); ?>" 
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Submit Button -->
    <div class="flex justify-end">
        <button type="submit" 
                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 font-medium">
            <i class="fas fa-save mr-2"></i>บันทึกการตั้งค่า
        </button>
    </div>
</form>

<!-- Icon Picker Modal -->
<div id="icon-picker-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-icons mr-3"></i>เลือกไอคอน
            </h5>
            <button type="button" id="close-icon-picker" class="text-white hover:text-gray-200">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <div class="p-6">
            <input type="text" id="icon-search" placeholder="ค้นหาไอคอน..." 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-4">
            
            <div id="icon-grid" class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-3 overflow-y-auto max-h-96"></div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="../includes/icon-picker.js"></script>

<script>
// Icon Picker for multiple inputs
let currentTarget = null;
let currentPreview = null;

const modal = document.getElementById('icon-picker-modal');
const closeBtn = document.getElementById('close-icon-picker');
const iconGrid = document.getElementById('icon-grid');
const iconSearch = document.getElementById('icon-search');

// Social and general icons
const allIcons = [
    // Social
    'fa-facebook-f', 'fa-facebook', 'fa-instagram', 'fa-twitter', 'fa-line', 'fa-youtube', 'fa-tiktok',
    'fa-linkedin', 'fa-pinterest', 'fa-whatsapp', 'fa-telegram', 'fa-snapchat', 'fa-discord', 'fa-twitch',
    // Arrows
    'fa-arrow-up', 'fa-arrow-down', 'fa-arrow-left', 'fa-arrow-right', 'fa-arrow-circle-up', 'fa-chevron-up',
    'fa-chevron-down', 'fa-angle-up', 'fa-angle-down', 'fa-caret-up', 'fa-long-arrow-alt-up',
    // General
    'fa-home', 'fa-user', 'fa-heart', 'fa-star', 'fa-envelope', 'fa-phone', 'fa-share', 'fa-share-alt'
];

// Open modal
document.querySelectorAll('.btn-icon-picker').forEach(btn => {
    btn.addEventListener('click', function() {
        currentTarget = document.getElementById(this.dataset.target);
        currentPreview = document.getElementById(this.dataset.preview);
        modal.classList.remove('hidden');
        loadIcons();
        iconSearch.focus();
    });
});

// Load icons
function loadIcons(filter = '') {
    iconGrid.innerHTML = '';
    const filteredIcons = filter 
        ? allIcons.filter(icon => icon.toLowerCase().includes(filter.toLowerCase()))
        : allIcons;
    
    filteredIcons.forEach(icon => {
        const div = document.createElement('div');
        div.className = 'icon-item flex flex-col items-center justify-center p-3 bg-gray-50 hover:bg-blue-100 rounded-lg cursor-pointer transition-all duration-200 border-2 border-transparent hover:border-blue-500';
        
        // Determine if it's a brand icon (fab) or regular icon (fas)
        const iconClass = icon.includes('facebook') || icon.includes('instagram') || icon.includes('twitter') || 
                         icon.includes('line') || icon.includes('youtube') || icon.includes('tiktok') ||
                         icon.includes('linkedin') || icon.includes('pinterest') || icon.includes('whatsapp') ||
                         icon.includes('telegram') || icon.includes('snapchat') || icon.includes('discord') || icon.includes('twitch')
                         ? 'fab' : 'fas';
        
        div.innerHTML = `<i class="${iconClass} ${icon} text-2xl text-gray-700 mb-1"></i><span class="text-xs text-gray-600">${icon.replace('fa-', '')}</span>`;
        
        div.addEventListener('click', function() {
            if (currentTarget && currentPreview) {
                currentTarget.value = icon;
                currentPreview.innerHTML = `<i class="${iconClass} ${icon} text-2xl text-gray-700"></i>`;
                modal.classList.add('hidden');
                
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: 'เลือกไอคอน: ' + icon
                });
            }
        });
        
        iconGrid.appendChild(div);
    });
    
    if (filteredIcons.length === 0) {
        iconGrid.innerHTML = '<div class="col-span-full text-center py-8 text-gray-500">ไม่พบไอคอนที่ค้นหา</div>';
    }
}

// Close modal
closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.add('hidden');
});

// Search
iconSearch.addEventListener('input', function() {
    loadIcons(this.value);
});

// ESC key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
        modal.classList.add('hidden');
    }
});

// Color picker sync
document.querySelectorAll('input[type="color"]').forEach(colorInput => {
    const textInput = colorInput.nextElementSibling.nextElementSibling;
    colorInput.addEventListener('input', function() {
        textInput.value = this.value.toUpperCase();
    });
});

// Social Media Toggle with Toast
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up toggles...');
    
    // Social media toggles
    const socialToggles = document.querySelectorAll('input[name*="social_"][name*="_enabled"]');
    console.log('Found social toggles:', socialToggles.length);
    
    socialToggles.forEach((checkbox, index) => {
        console.log(`Setting up toggle ${index + 1}:`, checkbox.name, checkbox.dataset.platform);
        
        checkbox.addEventListener('change', async function(e) {
            e.preventDefault();
            console.log('Toggle changed:', this.name, 'checked:', this.checked);
            
            const platform = this.dataset.platform || this.name.replace('social_', '').replace('_enabled', '');
            const isEnabled = this.checked;
            const originalState = !isEnabled;
            
            console.log('Platform:', platform, 'Enabled:', isEnabled);
            
            try {
                const csrfToken = document.getElementById('ajax_csrf');
                if (!csrfToken) {
                    throw new Error('CSRF token not found');
                }
                
                const response = await fetch('toggle-social.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        platform: platform,
                        enabled: isEnabled,
                        csrf_token: csrfToken.value
                    })
                });
                
                console.log('Response status:', response.status);
                const result = await response.json();
                console.log('Toggle response:', result);
                
                if (result.success) {
                    showSuccess(result.message);
                } else {
                    this.checked = originalState;
                    showError(result.message || 'ไม่สามารถเปลี่ยนสถานะได้');
                }
            } catch (error) {
                this.checked = originalState;
                console.error('Toggle error:', error);
                showError('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message);
            }
        });
    });
    
    // Go to Top toggle
    const gototopCheckbox = document.querySelector('input[name="gototop_enabled"]');
    if (gototopCheckbox) {
        console.log('Setting up Go to Top toggle...');
        gototopCheckbox.addEventListener('change', function(e) {
            e.preventDefault();
            console.log('Go to Top toggle changed:', this.checked);
            handleGotoTopToggle(this);
        });
    }
});

// Toast notification functions
function showSuccess(message) {
    const Toast = Swal.mixin({
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
    Toast.fire({
        icon: 'success',
        title: message
    });
}

function showError(message) {
    const Toast = Swal.mixin({
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
    Toast.fire({
        icon: 'error',
        title: message
    });
}

// Go to Top Toggle with Toast
async function handleGotoTopToggle(checkbox) {
    const isEnabled = checkbox.checked;
    const originalState = !isEnabled;
    
    try {
        const response = await fetch('toggle-gototop.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                enabled: isEnabled,
                csrf_token: document.getElementById('ajax_csrf').value
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess(result.message);
        } else {
            checkbox.checked = originalState;
            showError(result.message || 'ไม่สามารถเปลี่ยนสถานะได้');
        }
    } catch (error) {
        checkbox.checked = originalState;
        console.error('Toggle error:', error);
        showError('เกิดข้อผิดพลาดในการเชื่อมต่อ');
    }
}
</script>



