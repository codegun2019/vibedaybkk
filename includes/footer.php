<?php
/**
 * Footer Component
 * Footer ที่ใช้ร่วมกันได้ทุกหน้า
 */

// ดึงข้อมูล settings ถ้ายังไม่มี
if (!isset($global_settings)) {
    $global_settings = [];
    $result = db_get_rows($conn, "SELECT * FROM settings");
    foreach ($result as $row) {
        $global_settings[$row['setting_key']] = $row['setting_value'];
    }
}

// ดึงเมนู
if (!isset($main_menus)) {
    $main_menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC");
}

// ดึงหมวดหมู่บริการ
if (!isset($service_categories)) {
    $service_categories = db_get_rows($conn, "SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order ASC LIMIT 4");
}

// โซเชียลมีเดีย
$social_platforms = [
    'facebook' => ['color' => 'bg-blue-600 hover:bg-blue-700', 'default_icon' => 'fa-facebook-f', 'title' => 'Facebook'],
    'instagram' => ['color' => 'bg-pink-500 hover:bg-pink-600', 'default_icon' => 'fa-instagram', 'title' => 'Instagram'],
    'twitter' => ['color' => 'bg-black hover:bg-gray-800', 'default_icon' => 'fa-twitter', 'title' => 'X (Twitter)'],
    'line' => ['color' => 'bg-green-500 hover:bg-green-600', 'default_icon' => 'fa-line', 'title' => 'LINE'],
    'youtube' => ['color' => 'bg-red-600 hover:bg-red-700', 'default_icon' => 'fa-youtube', 'title' => 'YouTube'],
    'tiktok' => ['color' => 'bg-gray-800 hover:bg-gray-900', 'default_icon' => 'fa-tiktok', 'title' => 'TikTok']
];

if (!isset($active_socials)) {
    $active_socials = [];
    foreach ($social_platforms as $platform => $data) {
        $enabled = $global_settings["social_{$platform}_enabled"] ?? '0';
        if ($enabled == '1') {
            $url = $global_settings["social_{$platform}_url"] ?? '';
            if (!empty($url) && $url !== 'text' && filter_var($url, FILTER_VALIDATE_URL)) {
                $active_socials[$platform] = [
                    'url' => $url,
                    'color' => $data['color'],
                    'icon' => $global_settings["social_{$platform}_icon"] ?? $data['default_icon'],
                    'title' => $data['title']
                ];
            }
        }
    }
}

// ข้อมูลติดต่อ
if (!isset($contact_info)) {
    $contact_info = [
        'phone' => $global_settings['site_phone'] ?? $global_settings['contact_phone'] ?? '02-123-4567',
        'email' => $global_settings['site_email'] ?? $global_settings['contact_email'] ?? 'info@vibedaybkk.com',
        'line_id' => $global_settings['site_line'] ?? $global_settings['contact_line'] ?? '@vibedaybkk',
        'address' => $global_settings['site_address'] ?? $global_settings['contact_address'] ?? '123 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110'
    ];
}
?>

<!-- Footer -->
<footer class="bg-dark py-12 border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <div class="text-2xl font-bold text-red-primary mb-4 flex items-center">
                    <?php 
                    $footer_logo_type = $global_settings['logo_type'] ?? 'text';
                    $footer_logo_text = $global_settings['logo_text'] ?? 'VIBEDAYBKK';
                    $footer_logo_image = $global_settings['logo_image'] ?? '';
                    
                    if ($footer_logo_type === 'image' && !empty($footer_logo_image)): 
                    ?>
                        <img src="<?php echo UPLOADS_URL . '/' . $footer_logo_image; ?>" alt="<?php echo htmlspecialchars($footer_logo_text); ?>" class="h-12 object-contain">
                    <?php else: ?>
                        <i class="fas fa-star mr-2"></i><?php echo htmlspecialchars($footer_logo_text); ?>
                    <?php endif; ?>
                </div>
                <p class="text-gray-400 mb-4">บริการโมเดลและนางแบบมืออาชีพ ครบวงจร คุณภาพสูง</p>
                <?php if (!empty($active_socials)): ?>
                <div class="flex space-x-3">
                    <?php foreach ($active_socials as $platform => $social): ?>
                    <a href="<?php echo htmlspecialchars($social['url']); ?>" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 hover:scale-110 <?php echo $social['color']; ?> text-white" title="<?php echo $social['title']; ?>" target="_blank" rel="noopener">
                        <i class="fab <?php echo $social['icon']; ?>"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div>
                <h4 class="text-lg font-semibold mb-4">เมนูหลัก</h4>
                <ul class="space-y-2">
                    <?php 
                    // ใช้เมนูจากฐานข้อมูล (ไม่มีไอคอนใน footer)
                    if (!empty($main_menus)): 
                        foreach ($main_menus as $menu): 
                    ?>
                    <li>
                        <a href="<?php echo htmlspecialchars($menu['url']); ?>" class="text-gray-400 hover:text-red-primary transition-colors duration-300">
                            <?php echo htmlspecialchars($menu['title']); ?>
                        </a>
                    </li>
                    <?php 
                        endforeach;
                    else:
                        // Fallback ถ้าไม่มีเมนูในฐานข้อมูล
                    ?>
                    <li><a href="<?php echo BASE_URL; ?>" class="text-gray-400 hover:text-red-primary transition-colors duration-300">หน้าแรก</a></li>
                    <li><a href="#about" class="text-gray-400 hover:text-red-primary transition-colors duration-300">เกี่ยวกับเรา</a></li>
                    <li><a href="#services" class="text-gray-400 hover:text-red-primary transition-colors duration-300">บริการ</a></li>
                    <li><a href="models.php" class="text-gray-400 hover:text-red-primary transition-colors duration-300">โมเดล</a></li>
                    <li><a href="articles.php" class="text-gray-400 hover:text-red-primary transition-colors duration-300">บทความ</a></li>
                    <li><a href="gallery.php" class="text-gray-400 hover:text-red-primary transition-colors duration-300">ผลงาน</a></li>
                    <li><a href="#contact" class="text-gray-400 hover:text-red-primary transition-colors duration-300">ติดต่อ</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div>
                <h4 class="text-lg font-semibold mb-4">บริการของเรา</h4>
                <ul class="space-y-2">
                    <?php 
                    if (!empty($service_categories)): 
                        foreach ($service_categories as $category): 
                    ?>
                    <li><a href="#services" class="text-gray-400 hover:text-red-primary transition-colors duration-300"><?php echo htmlspecialchars($category['name'] ?? ''); ?></a></li>
                    <?php 
                        endforeach;
                    else:
                    ?>
                    <li><a href="#services" class="text-gray-400 hover:text-red-primary transition-colors duration-300">บริการของเรา</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div>
                <h4 class="text-lg font-semibold mb-4">ติดต่อเรา</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><i class="fas fa-phone mr-2"></i><?php echo htmlspecialchars($contact_info['phone']); ?></li>
                    <li><i class="fas fa-envelope mr-2"></i><?php echo htmlspecialchars($contact_info['email']); ?></li>
                    <li><i class="fab fa-line mr-2"></i><?php echo htmlspecialchars($contact_info['line_id']); ?></li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; <?php echo date('Y'); ?> VIBEDAYBKK. สงวนลิขสิทธิ์.</p>
        </div>
    </div>
</footer>
