<?php
/**
 * VIBEDAYBKK - Articles Page
 * หน้าบทความ - ดึงข้อมูลจาก Database
 */
define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

// ดึง settings และกำหนดเป็น $global_settings สำหรับ navigation.php
$global_settings = [];
$result = db_get_rows($conn, "SELECT * FROM settings");
foreach ($result as $row) {
    $global_settings[$row['setting_key']] = $row['setting_value'];
}

// กำหนด $settings สำหรับใช้ในหน้านี้
$settings = $global_settings;

// ดึงบทความที่เผยแพร่แล้ว
$articles = db_get_rows($conn, "
    SELECT a.*, u.full_name as author_name
    FROM articles a
    LEFT JOIN users u ON a.author_id = u.id
    WHERE a.status = 'published'
    ORDER BY a.published_at DESC, a.created_at DESC
    LIMIT 12
");

// ดึงเมนูจาก database (สำหรับ navigation.php)
$main_menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC");
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
    <script>
        tailwind.config = { theme: { extend: { fontFamily: {'noto': ['Noto Sans Thai', 'sans-serif']}, colors: {'dark': '#0a0a0a', 'dark-light': '#1a1a1a', 'red-primary': '#DC2626', 'red-light': '#EF4444', 'accent': '#FBBF24'} } } }
    </script>
    <?php require_once 'includes/navigation-styles.php'; ?>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Noto Sans Thai', sans-serif; background: #0a0a0a; color: white; }
        html { scroll-behavior: smooth; }
        body { box-sizing: border-box; overflow-x: hidden; }
        #preloader { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0a0a0a 100%); display: flex; justify-content: center; align-items: center; z-index: 9999; transition: opacity 0.5s ease, visibility 0.5s ease; }
        #preloader.hidden { opacity: 0; visibility: hidden; }
        .loader-content { text-align: center; }
        .loader-logo { font-size: 3rem; font-weight: bold; color: #DC2626; margin-bottom: 2rem; animation: pulse 1.5s ease-in-out infinite; }
        .loader-spinner { width: 60px; height: 60px; border: 4px solid #1a1a1a; border-top: 4px solid #DC2626; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @keyframes pulse { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.1); opacity: 0.8; } }
        .mobile-menu { transform: translateX(100%); transition: transform 0.3s ease; }
        .mobile-menu.open { transform: translateX(0); }
        .article-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .article-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(220, 38, 38, 0.3); }
        .article-image { overflow: hidden; }
        .article-image img { transition: transform 0.5s ease; width: 100%; height: 192px; object-fit: cover; }
        .article-card:hover .article-image img { transform: scale(1.1); }
        .go-to-top { position: fixed; bottom: 30px; right: 30px; width: 50px; height: 50px; border-radius: 50%; z-index: 1000; opacity: 0; visibility: hidden; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); }
        .go-to-top.show { opacity: 1; visibility: visible; }
        .go-to-top:hover { transform: translateY(-5px); box-shadow: 0 6px 12px rgba(220, 38, 38, 0.5); }
        @media (max-width: 768px) { .go-to-top { width: 45px; height: 45px; bottom: 20px; right: 20px; } .loader-logo { font-size: 2rem; } }
        .nav-link.active { color: #DC2626 !important; font-weight: 600; }
    </style>
</head>
<body class="bg-dark text-white font-kanit">
    <div id="preloader">
        <div class="loader-content">
            <div class="loader-logo"><i class="fas fa-star"></i> VIBEDAYBKK</div>
            <div class="loader-spinner"></div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-dark/95 backdrop-blur-sm z-50 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="<?php echo BASE_URL; ?>" class="text-2xl font-bold text-red-primary flex items-center">
                        <?php 
                        $logo_type = $settings['logo_type'] ?? 'text';
                        $logo_text = $settings['logo_text'] ?? 'VIBEDAYBKK';
                        $logo_image = $settings['logo_image'] ?? '';
                        
                        if ($logo_type === 'image' && !empty($logo_image)): 
                        ?>
                            <img src="<?php echo UPLOADS_URL . '/' . $logo_image; ?>" alt="<?php echo htmlspecialchars($logo_text); ?>" class="h-10 object-contain">
                        <?php else: ?>
                            <i class="fas fa-star mr-2"></i><?php echo htmlspecialchars($logo_text); ?>
                        <?php endif; ?>
                    </a>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <?php if (!empty($main_menus)): 
                            foreach ($main_menus as $menu): 
                        ?>
                            <a href="<?php echo htmlspecialchars($menu['url']); ?>" class="nav-link text-gray-300 hover:text-red-primary transition-colors duration-300">
                                <?php if (!empty($menu['icon'])): ?>
                                    <i class="fas <?php echo htmlspecialchars($menu['icon']); ?> mr-1"></i>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($menu['title']); ?>
                            </a>
                        <?php endforeach; endif; ?>
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
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="mobile-menu fixed top-0 right-0 h-full w-80 shadow-2xl md:hidden z-50" style="background: linear-gradient(135deg, rgba(10,10,10,0.98) 0%, rgba(26,26,26,0.98) 50%, rgba(10,10,10,0.98) 100%); backdrop-filter: blur(20px);">
            <div class="p-6 h-full overflow-y-auto">
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
                    <button id="close-menu" class="text-gray-300 hover:text-white transition-colors p-2 hover:bg-red-primary/20 rounded-lg">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                
                <!-- Menu Items -->
                <div class="space-y-2 mb-8">
                    <?php if (!empty($main_menus)): 
                        foreach ($main_menus as $menu): 
                    ?>
                        <a href="<?php echo htmlspecialchars($menu['url']); ?>" 
                           class="mobile-menu-link group flex items-center gap-3 px-4 py-3.5 rounded-xl text-gray-300 hover:text-white transition-all duration-300 border border-gray-700/50 hover:border-red-primary/50"
                           style="background: rgba(255, 255, 255, 0.03);">
                            <div class="w-10 h-10 rounded-lg bg-gray-800/50 group-hover:bg-red-primary/20 flex items-center justify-center transition-colors">
                                <?php if (!empty($menu['icon'])): ?>
                                    <i class="fas <?php echo htmlspecialchars($menu['icon']); ?> text-lg group-hover:text-red-primary transition-colors"></i>
                                <?php else: ?>
                                    <i class="fas fa-circle text-xs group-hover:text-red-primary transition-colors"></i>
                                <?php endif; ?>
                            </div>
                            <span class="font-medium flex-1"><?php echo htmlspecialchars($menu['title']); ?></span>
                            <i class="fas fa-chevron-right text-xs opacity-0 group-hover:opacity-100 group-hover:text-red-primary transition-all"></i>
                        </a>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <script>
    // Mobile Menu Toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const closeMenuBtn = document.getElementById('close-menu');

    if (mobileMenuBtn && mobileMenu && closeMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.add('open');
        });

        closeMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.remove('open');
        });

        const mobileMenuLinks = mobileMenu.querySelectorAll('a');
        mobileMenuLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('open');
            });
        });
    }
    </script>

    <button id="go-to-top" class="go-to-top bg-red-primary hover:bg-red-light text-white" title="กลับขึ้นด้านบน">
        <i class="fas fa-arrow-up text-lg"></i>
    </button>

    <section class="pt-24 pb-12 bg-gradient-to-b from-dark-light to-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4"><span class="text-red-primary">บทความ</span>และความรู้</h1>
                <p class="text-gray-400 text-lg max-w-2xl mx-auto">ค้นพบเคล็ดลับ เทคนิค และความรู้ในวงการโมเดลและนางแบบ</p>
            </div>
        </div>
    </section>

    <section class="py-16 bg-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php if (empty($articles)): ?>
            <div class="text-center py-12">
                <i class="fas fa-newspaper fa-3x text-gray-600 mb-4"></i>
                <h3 class="text-2xl mb-2">ยังไม่มีบทความ</h3>
                <p class="text-gray-400">กรุณารอบทความใหม่ๆ เร็วๆ นี้</p>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($articles as $article): ?>
                <article class="article-card bg-dark-light rounded-lg overflow-hidden shadow-lg">
                    <div class="article-image">
                        <?php if ($article['featured_image']): ?>
                            <img src="<?php echo UPLOADS_URL . '/' . $article['featured_image']; ?>" alt="<?php echo $article['title']; ?>">
                        <?php else: ?>
                            <div class="h-48 bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                                <i class="fas fa-newspaper text-6xl text-white opacity-50"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center text-sm text-gray-400 mb-3">
                            <i class="far fa-calendar mr-2"></i>
                            <span><?php echo format_date_thai($article['published_at'] ?? $article['created_at'], 'd M Y'); ?></span>
                            <span class="mx-2">•</span>
                            <i class="far fa-clock mr-2"></i>
                            <span><?php echo $article['read_time']; ?> นาที</span>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 hover:text-red-primary transition-colors">
                            <a href="article-detail.php?id=<?php echo $article['id']; ?>"><?php echo $article['title']; ?></a>
                        </h3>
                        <p class="text-gray-400 mb-4 line-clamp-3">
                            <?php echo $article['excerpt'] ? truncate_text($article['excerpt'], 120) : truncate_text(strip_tags($article['content']), 120); ?>
                        </p>
                        <a href="article-detail.php?id=<?php echo $article['id']; ?>" class="inline-flex items-center text-red-primary hover:text-red-light transition-colors">
                            อ่านเพิ่มเติม <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <footer class="bg-dark py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="text-2xl font-bold text-red-primary mb-4"><?php echo get_logo($settings); ?></div>
                    <p class="text-gray-400 mb-4">บริการโมเดลและนางแบบมืออาชีพ ครบวงจร คุณภาพสูง</p>
                    <div class="flex space-x-3">
                        <a href="#" class="bg-gray-800 hover:bg-blue-600 w-10 h-10 rounded-full text-gray-400 hover:text-white transition-all duration-300 flex items-center justify-center hover:scale-110"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="bg-gray-800 hover:bg-pink-500 w-10 h-10 rounded-full text-gray-400 hover:text-white transition-all duration-300 flex items-center justify-center hover:scale-110"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="bg-gray-800 hover:bg-red-primary w-10 h-10 rounded-full text-gray-400 hover:text-white transition-all duration-300 flex items-center justify-center hover:scale-110"><i class="fab fa-x-twitter"></i></a>
                        <a href="#" class="bg-gray-800 hover:bg-green-500 w-10 h-10 rounded-full text-gray-400 hover:text-white transition-all duration-300 flex items-center justify-center hover:scale-110"><i class="fab fa-line"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">เมนูหลัก</h4>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-400 hover:text-red-primary transition-colors duration-300">หน้าแรก</a></li>
                        <li><a href="index.php#about" class="text-gray-400 hover:text-red-primary transition-colors duration-300">เกี่ยวกับเรา</a></li>
                        <li><a href="services.php" class="text-gray-400 hover:text-red-primary transition-colors duration-300">บริการ</a></li>
                        <li><a href="articles.php" class="text-gray-400 hover:text-red-primary transition-colors duration-300">บทความ</a></li>
                        <li><a href="index.php#contact" class="text-gray-400 hover:text-red-primary transition-colors duration-300">ติดต่อ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">บริการของเรา</h4>
                    <ul class="space-y-2">
                        <?php $footer_cats = db_get_rows($conn, "SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order LIMIT 4");
                        foreach ($footer_cats as $cat): ?>
                        <li><a href="services-detail.php?category=<?php echo $cat['code']; ?>" class="text-gray-400 hover:text-red-primary transition-colors duration-300"><?php echo $cat['name']; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">ติดต่อเรา</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><i class="fas fa-phone mr-2"></i><?php echo $settings['site_phone'] ?? '02-123-4567'; ?></li>
                        <li><i class="fas fa-envelope mr-2"></i><?php echo $settings['site_email'] ?? 'info@vibedaybkk.com'; ?></li>
                        <li><i class="fab fa-line mr-2"></i><?php echo $settings['site_line'] ?? '@vibedaybkk'; ?></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> VIBEDAYBKK. สงวนลิขสิทธิ์.</p>
            </div>
        </div>
    </footer>

    <script>
        window.addEventListener('load', function() { const preloader = document.getElementById('preloader'); setTimeout(() => { preloader.classList.add('hidden'); setTimeout(() => { preloader.remove(); }, 500); }, 800); });
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const closeMenuBtn = document.getElementById('close-menu');
        mobileMenuBtn.addEventListener('click', () => { mobileMenu.classList.add('open'); });
        closeMenuBtn.addEventListener('click', () => { mobileMenu.classList.remove('open'); });
        mobileMenu.querySelectorAll('a').forEach(link => { link.addEventListener('click', () => { mobileMenu.classList.remove('open'); }); });
        const goToTopBtn = document.getElementById('go-to-top');
        window.addEventListener('scroll', () => { if (window.pageYOffset > 300) { goToTopBtn.classList.add('show'); } else { goToTopBtn.classList.remove('show'); } });
        goToTopBtn.addEventListener('click', () => { window.scrollTo({ top: 0, behavior: 'smooth' }); });
    </script>
</body>
</html>



