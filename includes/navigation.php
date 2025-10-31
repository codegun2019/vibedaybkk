<?php
/**
 * Navigation Component
 * ใช้ร่วมกันได้ทุกหน้า
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

// Logo
$logo_type = $global_settings['logo_type'] ?? 'text';
$logo_text = $global_settings['logo_text'] ?? 'lollipop24hours';
$logo_image = $global_settings['logo_image'] ?? '';

// Social
$social_platforms = [
    'facebook' => ['color' => 'bg-blue-600 hover:bg-blue-700', 'default_icon' => 'fa-facebook-f', 'title' => 'Facebook'],
    'instagram' => ['color' => 'bg-pink-500 hover:bg-pink-600', 'default_icon' => 'fa-instagram', 'title' => 'Instagram'],
    'twitter' => ['color' => 'bg-black hover:bg-gray-800', 'default_icon' => 'fa-x-twitter', 'title' => 'X (Twitter)'],
    'line' => ['color' => 'bg-green-500 hover:bg-green-600', 'default_icon' => 'fa-line', 'title' => 'LINE'],
    'youtube' => ['color' => 'bg-red-600 hover:bg-red-700', 'default_icon' => 'fa-youtube', 'title' => 'YouTube'],
    'tiktok' => ['color' => 'bg-gray-800 hover:bg-gray-900', 'default_icon' => 'fa-tiktok', 'title' => 'TikTok']
];

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
?>

<!-- Navigation -->
<nav class="fixed top-0 w-full bg-dark/95 backdrop-blur-sm z-50 border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="<?php echo BASE_URL; ?>" class="text-2xl font-bold text-red-primary flex items-center">
                    <?php if ($logo_type === 'image' && !empty($logo_image)): ?>
                        <img src="<?php echo UPLOADS_URL . '/' . $logo_image; ?>" alt="<?php echo htmlspecialchars($logo_text); ?>" class="h-10 object-contain">
                    <?php else: ?>
                        <i class="fas fa-star mr-2"></i><?php echo htmlspecialchars($logo_text); ?>
                    <?php endif; ?>
                </a>
            </div>
            
            <!-- Desktop Menu -->
            <div class="hidden md:block">
                <div class="ml-10 flex items-baseline space-x-8">
                    <?php 
                    if (!empty($main_menus)): 
                        foreach ($main_menus as $menu): 
                    ?>
                        <a href="<?php echo htmlspecialchars($menu['url']); ?>" class="nav-link text-gray-300 hover:text-red-primary transition-colors duration-300">
                            <?php if (!empty($menu['icon'])): ?>
                                <i class="fas <?php echo htmlspecialchars($menu['icon']); ?> mr-1"></i>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($menu['title']); ?>
                        </a>
                    <?php 
                        endforeach;
                    endif; 
                    ?>
                </div>
            </div>
            
            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button id="mobile-menu-btn" class="text-gray-300 hover:text-white">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile Backdrop -->
    <div id="mobile-backdrop" class="fixed inset-0 bg-black/50 opacity-0 invisible transition-opacity duration-300 md:hidden z-40"></div>
    
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu fixed top-0 right-0 h-full w-96 bg-gradient-to-b from-gray-950 via-gray-900 to-gray-950 shadow-2xl md:hidden z-50 backdrop-blur-md pointer-events-auto">
        <div class="p-6 bg-black">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-700">
                <div class="flex items-center gap-3">
                    <?php if ($logo_type === 'image' && !empty($logo_image)): ?>
                        <img src="<?php echo UPLOADS_URL . '/' . $logo_image; ?>" alt="Logo" class="h-8 object-contain">
                    <?php else: ?>
                        <i class="fas fa-star text-red-primary text-xl"></i>
                        <span class="text-white font-bold text-lg"><?php echo htmlspecialchars($logo_text); ?></span>
                    <?php endif; ?>
                </div>
                <button id="close-menu" class="text-gray-300 hover:text-white transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <!-- Menu Items -->
            <div class="space-y-3 mb-10">
                <?php 
                if (!empty($main_menus)): 
                    foreach ($main_menus as $index => $menu): 
                ?>
                    <a href="<?php echo htmlspecialchars($menu['url']); ?>" 
                       class="group flex items-center gap-4 px-5 py-4 rounded-xl text-gray-200 hover:text-white transition-all duration-300 bg-white/5 hover:bg-white/10 border border-white/10">
                        <div class="w-10 h-10 rounded-lg bg-white/10 group-hover:bg-red-primary/20 flex items-center justify-center transition-colors">
                            <?php if (!empty($menu['icon'])): ?>
                                <i class="fas <?php echo htmlspecialchars($menu['icon']); ?> text-lg group-hover:text-red-primary transition-colors"></i>
                            <?php else: ?>
                                <i class="fas fa-circle text-xs group-hover:text-red-primary transition-colors"></i>
                            <?php endif; ?>
                        </div>
                        <span class="font-semibold flex-1 text-base tracking-wide"><?php echo htmlspecialchars($menu['title']); ?></span>
                        <i class="fas fa-chevron-right text-sm opacity-0 group-hover:opacity-100 group-hover:text-red-primary transition-all"></i>
                    </a>
                <?php 
                    endforeach;
                endif; 
                ?>
            </div>
            
            <!-- Social Media -->
            <?php if (!empty($active_socials)): ?>
            <div class="mb-8 pb-6 border-t border-gray-700 pt-6">
                <h4 class="text-sm font-semibold text-gray-400 mb-4">ติดตามเรา</h4>
                <div class="flex space-x-3">
                    <?php foreach ($active_socials as $platform => $social): ?>
                    <a href="<?php echo htmlspecialchars($social['url']); ?>" 
                       class="w-10 h-10 rounded-full bg-gray-800 hover:bg-red-primary flex items-center justify-center text-gray-400 hover:text-white transition-all duration-300 hover:scale-110" 
                       title="<?php echo $social['title']; ?>" target="_blank" rel="noopener">
                        <i class="fab <?php echo $social['icon']; ?>"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
// Mobile Menu Toggle + Backdrop & z-index management
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const backdrop = document.getElementById('mobile-backdrop');
    const closeMenuBtn = document.getElementById('close-menu');

    function openMenu() {
        mobileMenu.classList.add('open');
        backdrop.classList.remove('invisible');
        backdrop.classList.add('opacity-100');
        document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
        mobileMenu.classList.remove('open');
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('invisible');
        document.body.style.overflow = '';
    }

    if (mobileMenuBtn && mobileMenu && closeMenuBtn && backdrop) {
        mobileMenuBtn.addEventListener('click', openMenu);
        closeMenuBtn.addEventListener('click', closeMenu);
        backdrop.addEventListener('click', closeMenu);

        // Close menu when pressing Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileMenu.classList.contains('open')) {
                closeMenu();
            }
        });
    }
});
</script>
