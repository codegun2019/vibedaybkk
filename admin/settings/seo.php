<?php
/**
 * VIBEDAYBKK Admin - SEO & Analytics Settings
 * ตั้งค่า SEO และ Analytics
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check - allow view for editor and above
require_permission('settings', 'view');
$can_edit = has_permission('settings', 'edit');

$page_title = 'ตั้งค่า SEO & Analytics';
$current_page = 'settings';

$success = false;
$errors = [];

// Get current settings
$settings = [];
$result = $conn->query("SELECT `setting_key`, `setting_value` FROM settings WHERE 
    `setting_key` LIKE 'seo_%' OR 
    `setting_key` LIKE 'og_%' OR 
    `setting_key` LIKE 'twitter_%' OR 
    `setting_key` LIKE 'robots_%' OR 
    `setting_key` LIKE 'google_%' OR 
    `setting_key` LIKE 'facebook_%' OR 
    `setting_key` LIKE 'meta_%' OR 
    `setting_key` LIKE 'schema_%' OR
    `setting_key` LIKE 'sitemap_%'");
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $updates = [];
        
        // Basic SEO
        $updates['seo_title'] = clean_input($_POST['seo_title'] ?? '');
        $updates['seo_description'] = clean_input($_POST['seo_description'] ?? '');
        $updates['seo_keywords'] = clean_input($_POST['seo_keywords'] ?? '');
        $updates['seo_author'] = clean_input($_POST['seo_author'] ?? '');
        $updates['seo_canonical_url'] = clean_input($_POST['seo_canonical_url'] ?? '');
        
        // Open Graph
        $updates['og_title'] = clean_input($_POST['og_title'] ?? '');
        $updates['og_description'] = clean_input($_POST['og_description'] ?? '');
        $updates['og_image'] = clean_input($_POST['og_image'] ?? '');
        $updates['og_type'] = clean_input($_POST['og_type'] ?? 'website');
        $updates['og_locale'] = clean_input($_POST['og_locale'] ?? 'th_TH');
        $updates['og_site_name'] = clean_input($_POST['og_site_name'] ?? '');
        
        // Twitter Card
        $updates['twitter_card'] = clean_input($_POST['twitter_card'] ?? 'summary_large_image');
        $updates['twitter_site'] = clean_input($_POST['twitter_site'] ?? '');
        $updates['twitter_creator'] = clean_input($_POST['twitter_creator'] ?? '');
        $updates['twitter_title'] = clean_input($_POST['twitter_title'] ?? '');
        $updates['twitter_description'] = clean_input($_POST['twitter_description'] ?? '');
        $updates['twitter_image'] = clean_input($_POST['twitter_image'] ?? '');
        
        // Robots
        $updates['robots_index'] = isset($_POST['robots_index']) ? '1' : '0';
        $updates['robots_follow'] = isset($_POST['robots_follow']) ? '1' : '0';
        $updates['robots_txt_custom'] = clean_input($_POST['robots_txt_custom'] ?? '');
        $updates['sitemap_enabled'] = isset($_POST['sitemap_enabled']) ? '1' : '0';
        $updates['sitemap_frequency'] = clean_input($_POST['sitemap_frequency'] ?? 'weekly');
        
        // Google Analytics
        $updates['google_analytics_enabled'] = isset($_POST['google_analytics_enabled']) ? '1' : '0';
        $updates['google_analytics_id'] = clean_input($_POST['google_analytics_id'] ?? '');
        $updates['google_tag_manager_id'] = clean_input($_POST['google_tag_manager_id'] ?? '');
        
        // Google Search Console
        $updates['google_site_verification'] = clean_input($_POST['google_site_verification'] ?? '');
        $updates['google_search_console_enabled'] = isset($_POST['google_search_console_enabled']) ? '1' : '0';
        
        // Facebook Pixel
        $updates['facebook_pixel_enabled'] = isset($_POST['facebook_pixel_enabled']) ? '1' : '0';
        $updates['facebook_pixel_id'] = clean_input($_POST['facebook_pixel_id'] ?? '');
        
        // Other Meta
        $updates['meta_theme_color'] = clean_input($_POST['meta_theme_color'] ?? '#DC2626');
        $updates['meta_apple_mobile_capable'] = isset($_POST['meta_apple_mobile_capable']) ? '1' : '0';
        $updates['meta_apple_status_bar_style'] = clean_input($_POST['meta_apple_status_bar_style'] ?? 'black-translucent');
        
        // Schema.org
        $updates['schema_org_type'] = clean_input($_POST['schema_org_type'] ?? 'Organization');
        $updates['schema_org_enabled'] = isset($_POST['schema_org_enabled']) ? '1' : '0';
        
        // Update database
        foreach ($updates as $key => $value) {
            $stmt = $conn->prepare("INSERT INTO settings (`setting_key`, `setting_value`, `setting_type`) VALUES (?, ?, 'text') ON DUPLICATE KEY UPDATE `setting_value` = ?");
            $stmt->bind_param('sss', $key, $value, $value);
            $stmt->execute();
            $stmt->close();
        }
        
        log_activity($conn, $_SESSION['user_id'], 'update', 'settings', 0, 'Updated SEO and Analytics settings');
        
        $success = true;
        $settings = $updates;
    }
}

include '../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h2 class="text-3xl font-bold text-gray-900 flex items-center">
        <i class="fas fa-search mr-3 text-blue-600"></i>ตั้งค่า SEO & Analytics
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
    
    <!-- Basic SEO -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-search mr-3"></i>SEO พื้นฐาน
            </h5>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">SEO Title <span class="text-red-500">*</span></label>
                <input type="text" name="seo_title" value="<?php echo htmlspecialchars($settings['seo_title'] ?? ''); ?>" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                <p class="text-sm text-gray-500 mt-1">แนะนำ: 50-60 ตัวอักษร</p>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Meta Description <span class="text-red-500">*</span></label>
                <textarea name="seo_description" rows="3" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required><?php echo htmlspecialchars($settings['seo_description'] ?? ''); ?></textarea>
                <p class="text-sm text-gray-500 mt-1">แนะนำ: 150-160 ตัวอักษร</p>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Keywords</label>
                <input type="text" name="seo_keywords" value="<?php echo htmlspecialchars($settings['seo_keywords'] ?? ''); ?>" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="keyword1, keyword2, keyword3">
                <p class="text-sm text-gray-500 mt-1">คั่นด้วยเครื่องหมายจุลภาค (,)</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Author</label>
                    <input type="text" name="seo_author" value="<?php echo htmlspecialchars($settings['seo_author'] ?? ''); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Canonical URL</label>
                    <input type="url" name="seo_canonical_url" value="<?php echo htmlspecialchars($settings['seo_canonical_url'] ?? ''); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="https://vibedaybkk.com">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Open Graph (Facebook) -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fab fa-facebook mr-3"></i>Open Graph (Facebook, LinkedIn)
            </h5>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">OG Title</label>
                <input type="text" name="og_title" value="<?php echo htmlspecialchars($settings['og_title'] ?? ''); ?>" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">OG Description</label>
                <textarea name="og_description" rows="2" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($settings['og_description'] ?? ''); ?></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">OG Image URL</label>
                    <input type="text" name="og_image" value="<?php echo htmlspecialchars($settings['og_image'] ?? ''); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="/assets/images/og-image.jpg">
                    <p class="text-sm text-gray-500 mt-1">แนะนำ: 1200x630px</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">OG Type</label>
                    <select name="og_type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="website" <?php echo ($settings['og_type'] ?? '') == 'website' ? 'selected' : ''; ?>>Website</option>
                        <option value="article" <?php echo ($settings['og_type'] ?? '') == 'article' ? 'selected' : ''; ?>>Article</option>
                        <option value="business.business" <?php echo ($settings['og_type'] ?? '') == 'business.business' ? 'selected' : ''; ?>>Business</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">OG Locale</label>
                    <input type="text" name="og_locale" value="<?php echo htmlspecialchars($settings['og_locale'] ?? 'th_TH'); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">OG Site Name</label>
                    <input type="text" name="og_site_name" value="<?php echo htmlspecialchars($settings['og_site_name'] ?? ''); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Twitter Card -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-400 to-blue-600 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fab fa-twitter mr-3"></i>Twitter Card
            </h5>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Card Type</label>
                    <select name="twitter_card" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="summary" <?php echo ($settings['twitter_card'] ?? '') == 'summary' ? 'selected' : ''; ?>>Summary</option>
                        <option value="summary_large_image" <?php echo ($settings['twitter_card'] ?? '') == 'summary_large_image' ? 'selected' : ''; ?>>Summary Large Image</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Twitter Site Handle</label>
                    <input type="text" name="twitter_site" value="<?php echo htmlspecialchars($settings['twitter_site'] ?? ''); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="@vibedaybkk">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Twitter Title</label>
                <input type="text" name="twitter_title" value="<?php echo htmlspecialchars($settings['twitter_title'] ?? ''); ?>" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Twitter Description</label>
                <textarea name="twitter_description" rows="2" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($settings['twitter_description'] ?? ''); ?></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Twitter Image URL</label>
                <input type="text" name="twitter_image" value="<?php echo htmlspecialchars($settings['twitter_image'] ?? ''); ?>" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="/assets/images/twitter-image.jpg">
                <p class="text-sm text-gray-500 mt-1">แนะนำ: 1200x600px</p>
            </div>
        </div>
    </div>
    
    <!-- Robots & Sitemap -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-gray-700 to-gray-900 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-robot mr-3"></i>Robots & Sitemap
            </h5>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <label class="text-sm font-semibold text-gray-700">Allow Search Engine Indexing</label>
                    <p class="text-sm text-gray-500">อนุญาตให้ Search Engine จัดทำดัชนีเว็บไซต์</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="robots_index" value="1" class="sr-only peer" <?php echo ($settings['robots_index'] ?? '1') == '1' ? 'checked' : ''; ?>>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
            
            <div class="flex items-center justify-between">
                <div>
                    <label class="text-sm font-semibold text-gray-700">Allow Following Links</label>
                    <p class="text-sm text-gray-500">อนุญาตให้ Search Engine ติดตามลิงก์</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="robots_follow" value="1" class="sr-only peer" <?php echo ($settings['robots_follow'] ?? '1') == '1' ? 'checked' : ''; ?>>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
            
            <div class="flex items-center justify-between">
                <div>
                    <label class="text-sm font-semibold text-gray-700">Enable XML Sitemap</label>
                    <p class="text-sm text-gray-500">สร้าง XML Sitemap อัตโนมัติ</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="sitemap_enabled" value="1" class="sr-only peer" <?php echo ($settings['sitemap_enabled'] ?? '1') == '1' ? 'checked' : ''; ?>>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sitemap Update Frequency</label>
                <select name="sitemap_frequency" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="always" <?php echo ($settings['sitemap_frequency'] ?? '') == 'always' ? 'selected' : ''; ?>>Always</option>
                    <option value="hourly" <?php echo ($settings['sitemap_frequency'] ?? '') == 'hourly' ? 'selected' : ''; ?>>Hourly</option>
                    <option value="daily" <?php echo ($settings['sitemap_frequency'] ?? '') == 'daily' ? 'selected' : ''; ?>>Daily</option>
                    <option value="weekly" <?php echo ($settings['sitemap_frequency'] ?? 'weekly') == 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                    <option value="monthly" <?php echo ($settings['sitemap_frequency'] ?? '') == 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                    <option value="yearly" <?php echo ($settings['sitemap_frequency'] ?? '') == 'yearly' ? 'selected' : ''; ?>>Yearly</option>
                    <option value="never" <?php echo ($settings['sitemap_frequency'] ?? '') == 'never' ? 'selected' : ''; ?>>Never</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Google Analytics -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-orange-500 to-red-600 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fab fa-google mr-3"></i>Google Analytics & Tag Manager
            </h5>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <label class="text-sm font-semibold text-gray-700">Enable Google Analytics</label>
                    <p class="text-sm text-gray-500">เปิดใช้งาน Google Analytics 4 (GA4)</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="google_analytics_enabled" value="1" class="sr-only peer" <?php echo ($settings['google_analytics_enabled'] ?? '0') == '1' ? 'checked' : ''; ?>>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                </label>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Google Analytics Measurement ID</label>
                <input type="text" name="google_analytics_id" value="<?php echo htmlspecialchars($settings['google_analytics_id'] ?? ''); ?>" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                       placeholder="G-XXXXXXXXXX">
                <p class="text-sm text-gray-500 mt-1">รูปแบบ: G-XXXXXXXXXX</p>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Google Tag Manager ID</label>
                <input type="text" name="google_tag_manager_id" value="<?php echo htmlspecialchars($settings['google_tag_manager_id'] ?? ''); ?>" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                       placeholder="GTM-XXXXXXX">
                <p class="text-sm text-gray-500 mt-1">รูปแบบ: GTM-XXXXXXX</p>
            </div>
        </div>
    </div>
    
    <!-- Google Search Console -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-search-plus mr-3"></i>Google Search Console
            </h5>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <label class="text-sm font-semibold text-gray-700">Enable Google Search Console</label>
                    <p class="text-sm text-gray-500">เปิดใช้งาน Google Search Console Verification</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="google_search_console_enabled" value="1" class="sr-only peer" <?php echo ($settings['google_search_console_enabled'] ?? '0') == '1' ? 'checked' : ''; ?>>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                </label>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Google Site Verification Code</label>
                <input type="text" name="google_site_verification" value="<?php echo htmlspecialchars($settings['google_site_verification'] ?? ''); ?>" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       placeholder="abcdefghijklmnopqrstuvwxyz">
                <p class="text-sm text-gray-500 mt-1">ใส่เฉพาะ content value จาก meta tag</p>
            </div>
        </div>
    </div>
    
    <!-- Facebook Pixel -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fab fa-facebook-square mr-3"></i>Facebook Pixel
            </h5>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <label class="text-sm font-semibold text-gray-700">Enable Facebook Pixel</label>
                    <p class="text-sm text-gray-500">เปิดใช้งาน Facebook Pixel สำหรับติดตามการแปลง</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="facebook_pixel_enabled" value="1" class="sr-only peer" <?php echo ($settings['facebook_pixel_enabled'] ?? '0') == '1' ? 'checked' : ''; ?>>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Facebook Pixel ID</label>
                <input type="text" name="facebook_pixel_id" value="<?php echo htmlspecialchars($settings['facebook_pixel_id'] ?? ''); ?>" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="123456789012345">
                <p class="text-sm text-gray-500 mt-1">รหัส Pixel ID ที่ได้จาก Facebook Business Manager</p>
            </div>
        </div>
    </div>
    
    <!-- Other Meta Tags -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-mobile-alt mr-3"></i>Meta Tags อื่นๆ
            </h5>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Theme Color (Mobile Browser)</label>
                <div class="flex items-center space-x-3">
                    <input type="color" name="meta_theme_color" value="<?php echo htmlspecialchars($settings['meta_theme_color'] ?? '#DC2626'); ?>" 
                           class="w-16 h-12 rounded-lg border-2 border-gray-300 cursor-pointer">
                    <input type="text" readonly value="<?php echo htmlspecialchars($settings['meta_theme_color'] ?? '#DC2626'); ?>" 
                           class="flex-1 px-4 py-3 border border-gray-300 rounded-lg bg-gray-50">
                </div>
                <p class="text-sm text-gray-500 mt-1">สีของ address bar บนมือถือ</p>
            </div>
            
            <div class="flex items-center justify-between">
                <div>
                    <label class="text-sm font-semibold text-gray-700">Apple Mobile Web App Capable</label>
                    <p class="text-sm text-gray-500">เปิดใช้งานโหมด Standalone บน iOS</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="meta_apple_mobile_capable" value="1" class="sr-only peer" <?php echo ($settings['meta_apple_mobile_capable'] ?? '1') == '1' ? 'checked' : ''; ?>>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                </label>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Apple Status Bar Style</label>
                <select name="meta_apple_status_bar_style" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="default" <?php echo ($settings['meta_apple_status_bar_style'] ?? '') == 'default' ? 'selected' : ''; ?>>Default</option>
                    <option value="black" <?php echo ($settings['meta_apple_status_bar_style'] ?? '') == 'black' ? 'selected' : ''; ?>>Black</option>
                    <option value="black-translucent" <?php echo ($settings['meta_apple_status_bar_style'] ?? 'black-translucent') == 'black-translucent' ? 'selected' : ''; ?>>Black Translucent</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Schema.org -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-yellow-500 to-orange-600 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-code mr-3"></i>Schema.org Structured Data
            </h5>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <label class="text-sm font-semibold text-gray-700">Enable Schema.org Markup</label>
                    <p class="text-sm text-gray-500">เปิดใช้งาน Structured Data สำหรับ Rich Snippets</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="schema_org_enabled" value="1" class="sr-only peer" <?php echo ($settings['schema_org_enabled'] ?? '1') == '1' ? 'checked' : ''; ?>>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-600"></div>
                </label>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Schema Type</label>
                <select name="schema_org_type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                    <option value="Organization" <?php echo ($settings['schema_org_type'] ?? 'Organization') == 'Organization' ? 'selected' : ''; ?>>Organization</option>
                    <option value="LocalBusiness" <?php echo ($settings['schema_org_type'] ?? '') == 'LocalBusiness' ? 'selected' : ''; ?>>Local Business</option>
                    <option value="WebSite" <?php echo ($settings['schema_org_type'] ?? '') == 'WebSite' ? 'selected' : ''; ?>>Website</option>
                </select>
            </div>
        </div>
    </div>
    
    <div class="flex justify-end">
        <button type="submit" 
                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 font-medium">
            <i class="fas fa-save mr-2"></i>บันทึกการตั้งค่า
        </button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>



