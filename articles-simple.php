<?php
/**
 * VIBEDAYBKK - Articles Page (Simple Version)
 * หน้าบทความแบบง่าย
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

// ดึง settings
$settings = [];
$result = db_get_rows($conn, "SELECT * FROM settings");
foreach ($result as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// ดึงเมนู
$main_menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC");

// ดึงบทความ
$articles = db_get_rows($conn, "
    SELECT a.*, u.full_name as author_name
    FROM articles a
    LEFT JOIN users u ON a.author_id = u.id
    WHERE a.status = 'published'
    ORDER BY a.published_at DESC, a.created_at DESC
    LIMIT 12
");

// Logo
$logo_type = $settings['logo_type'] ?? 'text';
$logo_text = $settings['logo_text'] ?? 'VIBEDAYBKK';
$logo_image = $settings['logo_image'] ?? '';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บทความ - <?php echo $settings['site_name'] ?? 'VIBEDAYBKK'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Noto Sans Thai', sans-serif;
            background: #0a0a0a;
            color: white;
        }
        .mobile-menu {
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }
        .mobile-menu.open {
            transform: translateX(0);
        }
        .mobile-menu-link:hover {
            background: rgba(220, 38, 38, 0.2) !important;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-black/95 backdrop-blur-sm z-50 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="<?php echo BASE_URL; ?>" class="text-2xl font-bold text-red-600 flex items-center">
                        <?php if ($logo_type === 'image' && !empty($logo_image)): ?>
                            <img src="<?php echo UPLOADS_URL . '/' . $logo_image; ?>" alt="<?php echo htmlspecialchars($logo_text); ?>" class="h-10 object-contain">
                        <?php else: ?>
                            <i class="fas fa-star mr-2"></i><?php echo htmlspecialchars($logo_text); ?>
                        <?php endif; ?>
                    </a>
                </div>
                
                <div class="hidden md:flex items-baseline space-x-8">
                    <?php if (!empty($main_menus)): 
                        foreach ($main_menus as $menu): 
                    ?>
                        <a href="<?php echo htmlspecialchars($menu['url']); ?>" class="text-gray-300 hover:text-red-600 transition-colors">
                            <?php if (!empty($menu['icon'])): ?>
                                <i class="fas <?php echo htmlspecialchars($menu['icon']); ?> mr-1"></i>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($menu['title']); ?>
                        </a>
                    <?php endforeach; endif; ?>
                </div>
                
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="text-gray-300 hover:text-white">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="mobile-menu fixed top-0 right-0 h-full w-80 shadow-2xl md:hidden z-50" style="background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%); backdrop-filter: blur(20px);">
            <div class="p-6">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-700">
                    <span class="text-white font-bold text-lg">
                        <i class="fas fa-star text-red-600 mr-2"></i><?php echo htmlspecialchars($logo_text); ?>
                    </span>
                    <button id="close-menu" class="text-gray-300 hover:text-white">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                
                <div class="space-y-2">
                    <?php if (!empty($main_menus)): 
                        foreach ($main_menus as $menu): 
                    ?>
                        <a href="<?php echo htmlspecialchars($menu['url']); ?>" 
                           class="mobile-menu-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white transition-all"
                           style="background: rgba(255, 255, 255, 0.05);">
                            <?php if (!empty($menu['icon'])): ?>
                                <i class="fas <?php echo htmlspecialchars($menu['icon']); ?>"></i>
                            <?php endif; ?>
                            <span><?php echo htmlspecialchars($menu['title']); ?></span>
                        </a>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <script>
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const closeMenuBtn = document.getElementById('close-menu');
    
    mobileMenuBtn?.addEventListener('click', () => mobileMenu.classList.add('open'));
    closeMenuBtn?.addEventListener('click', () => mobileMenu.classList.remove('open'));
    mobileMenu?.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => mobileMenu.classList.remove('open'));
    });
    </script>

    <!-- Content -->
    <section class="pt-24 pb-12 bg-gradient-to-b from-gray-900 to-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">
                    <span class="text-red-600">บทความ</span>และความรู้
                </h1>
                <p class="text-gray-400 text-lg">ค้นพบเคล็ดลับและความรู้ในวงการโมเดล</p>
            </div>
        </div>
    </section>

    <section class="py-16 bg-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php if (empty($articles)): ?>
            <div class="text-center py-12">
                <i class="fas fa-newspaper text-6xl text-gray-600 mb-4"></i>
                <h3 class="text-2xl mb-2">ยังไม่มีบทความ</h3>
                <p class="text-gray-400">กรุณารอบทความใหม่ๆ เร็วๆ นี้</p>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($articles as $article): ?>
                <div class="bg-gray-900 rounded-lg overflow-hidden shadow-lg hover:shadow-2xl transition-all hover:-translate-y-2">
                    <div class="h-48 bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                        <i class="fas fa-newspaper text-6xl text-white opacity-50"></i>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2 text-white"><?php echo htmlspecialchars($article['title']); ?></h3>
                        <p class="text-gray-400 mb-4 line-clamp-3"><?php echo htmlspecialchars(substr(strip_tags($article['content']), 0, 150)); ?>...</p>
                        <a href="article-detail.php?id=<?php echo $article['id']; ?>" class="text-red-600 hover:text-red-500 font-medium">
                            อ่านเพิ่มเติม <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <script>
        window.addEventListener('load', () => {
            document.getElementById('preloader')?.classList.add('hidden');
        });
    </script>
</body>
</html>




