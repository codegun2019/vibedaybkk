<?php
/**
 * VIBEDAYBKK - Article Detail Page
 * หน้าอ่านบทความ - ดึงข้อมูลจาก Database
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

// ดึงเมนูจาก database (สำหรับ navigation.php)
$main_menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC");

// Get article ID
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$article_id) {
    header('Location: articles.php');
    exit;
}

// ดึงข้อมูลบทความ
$stmt = $conn->prepare("
    SELECT a.*, u.full_name as author_name
    FROM articles a
    LEFT JOIN users u ON a.author_id = u.id
    WHERE a.id = ? AND a.status = 'published'
");
$stmt->bind_param('i', $article_id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();
$stmt->close();

if (!$article) {
    header('Location: articles.php');
    exit;
}

// นับจำนวน views
$stmt = $conn->prepare("UPDATE articles SET view_count = view_count + 1 WHERE id = ?");
$stmt->bind_param('i', $article_id);
$stmt->execute();
$stmt->close();

// ดึงบทความที่เกี่ยวข้อง
$stmt = $conn->prepare("
    SELECT * FROM articles 
    WHERE id != ? AND status = 'published' 
    ORDER BY RAND() 
    LIMIT 3
");
$stmt->bind_param('i', $article_id);
$stmt->execute();
$result = $stmt->get_result();
$related_articles = [];
while ($row = $result->fetch_assoc()) {
    $related_articles[] = $row;
}
$stmt->close();

// ดึงเมนูจาก database
$main_menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC");
$sub_menus = [];
foreach ($main_menus as $menu) {
    $subs = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id = {$menu['id']} AND status = 'active' ORDER BY sort_order ASC");
    if (!empty($subs)) {
        $sub_menus[$menu['id']] = $subs;
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $article['title']; ?> - <?php echo $settings['site_name'] ?? 'VIBEDAYBKK'; ?></title>
    <?php echo get_favicon($settings); ?>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: {'kanit': ['Kanit', 'sans-serif']}, colors: {'dark': '#0a0a0a', 'dark-light': '#1a1a1a', 'red-primary': '#DC2626', 'red-light': '#EF4444', 'accent': '#FBBF24'} } } }
    </script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { box-sizing: border-box; overflow-x: hidden; }
        .mobile-menu { transform: translateX(100%); transition: transform 0.3s ease; }
        .mobile-menu.open { transform: translateX(0); }
        .article-content { line-height: 1.8; }
        .article-content h2 { font-size: 1.75rem; font-weight: 600; margin-top: 2rem; margin-bottom: 1rem; color: #DC2626; }
        .article-content h3 { font-size: 1.5rem; font-weight: 600; margin-top: 1.5rem; margin-bottom: 0.75rem; color: #EF4444; }
        .article-content p { margin-bottom: 1.25rem; color: #D1D5DB; }
        .article-content ul, .article-content ol { margin-left: 1.5rem; margin-bottom: 1.25rem; color: #D1D5DB; }
        .article-content li { margin-bottom: 0.5rem; }
        .article-content blockquote { border-left: 4px solid #DC2626; padding-left: 1.5rem; margin: 1.5rem 0; font-style: italic; color: #9CA3AF; }
        .go-to-top { position: fixed; bottom: 30px; right: 30px; width: 50px; height: 50px; border-radius: 50%; z-index: 1000; opacity: 0; visibility: hidden; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); }
        .go-to-top.show { opacity: 1; visibility: visible; }
        .go-to-top:hover { transform: translateY(-5px); box-shadow: 0 6px 12px rgba(220, 38, 38, 0.5); }
        .related-article { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .related-article:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(220, 38, 38, 0.2); }
        @media (max-width: 768px) { .go-to-top { width: 45px; height: 45px; bottom: 20px; right: 20px; } .article-content h2 { font-size: 1.5rem; } .article-content h3 { font-size: 1.25rem; } }
        .nav-link.active { color: #DC2626 !important; font-weight: 600; }
    </style>
</head>
<body class="bg-dark text-white font-kanit">
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

    <article class="pt-24 pb-16 bg-dark">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="articles.php" class="inline-flex items-center text-gray-400 hover:text-red-primary transition-colors mb-8">
                <i class="fas fa-arrow-left mr-2"></i> กลับไปหน้าบทความ
            </a>

            <div class="mb-8">
                <?php if ($article['category']): ?>
                <div class="mb-4">
                    <span class="inline-block px-3 py-1 bg-red-primary text-white text-sm rounded-full mb-4"><?php echo $article['category']; ?></span>
                </div>
                <?php endif; ?>
                <h1 class="text-3xl md:text-4xl font-bold mb-4"><?php echo $article['title']; ?></h1>
            </div>

            <?php if ($article['featured_image']): ?>
            <div class="mb-8 rounded-lg overflow-hidden">
                <img src="<?php echo UPLOADS_URL . '/' . $article['featured_image']; ?>" alt="<?php echo $article['title']; ?>" class="w-full h-96 object-cover">
            </div>
            <?php endif; ?>

            <div class="flex items-center justify-between border-b border-gray-700 pb-6 mb-8">
                <div class="flex items-center space-x-6 text-gray-400">
                    <div class="flex items-center">
                        <i class="far fa-calendar mr-2"></i>
                        <span><?php echo format_date_thai($article['published_at'] ?? $article['created_at'], 'd M Y'); ?></span>
                    </div>
                    <div class="flex items-center">
                        <i class="far fa-clock mr-2"></i>
                        <span><?php echo $article['read_time']; ?> นาที</span>
                    </div>
                    <div class="flex items-center">
                        <i class="far fa-user mr-2"></i>
                        <span><?php echo $article['author_name'] ?? 'ทีมงาน VIBEDAYBKK'; ?></span>
                    </div>
                    <div class="flex items-center">
                        <i class="far fa-eye mr-2"></i>
                        <span><?php echo number_format($article['view_count']); ?></span>
                    </div>
                </div>
            </div>

            <div class="article-content mb-12">
                <?php echo $article['content']; ?>
            </div>

            <div class="border-t border-b border-gray-700 py-8 mb-12">
                <h3 class="text-xl font-semibold mb-4">แชร์บทความนี้</h3>
                <div class="flex space-x-4">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '/article-detail.php?id=' . $article_id); ?>" target="_blank" class="bg-blue-600 hover:bg-blue-700 w-12 h-12 rounded-full text-white transition-all duration-300 flex items-center justify-center hover:scale-110" title="แชร์บน Facebook"><i class="fab fa-facebook-f text-lg"></i></a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . '/article-detail.php?id=' . $article_id); ?>&text=<?php echo urlencode($article['title']); ?>" target="_blank" class="bg-black hover:bg-gray-800 w-12 h-12 rounded-full text-white transition-all duration-300 flex items-center justify-center hover:scale-110" title="แชร์บน X"><i class="fab fa-x-twitter text-lg"></i></a>
                    <a href="https://social-plugins.line.me/lineit/share?url=<?php echo urlencode(SITE_URL . '/article-detail.php?id=' . $article_id); ?>" target="_blank" class="bg-green-500 hover:bg-green-600 w-12 h-12 rounded-full text-white transition-all duration-300 flex items-center justify-center hover:scale-110" title="แชร์บน Line"><i class="fab fa-line text-lg"></i></a>
                    <button onclick="copyLink()" class="bg-gray-700 hover:bg-gray-600 w-12 h-12 rounded-full text-white transition-all duration-300 flex items-center justify-center hover:scale-110" title="คัดลอกลิงก์"><i class="fas fa-link text-lg"></i></button>
                </div>
            </div>

            <?php if (!empty($related_articles)): ?>
            <div>
                <h3 class="text-2xl font-bold mb-6">บทความที่เกี่ยวข้อง</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php foreach ($related_articles as $related): ?>
                    <article class="related-article bg-dark-light rounded-lg overflow-hidden shadow-lg">
                        <?php if ($related['featured_image']): ?>
                            <img src="<?php echo UPLOADS_URL . '/' . $related['featured_image']; ?>" class="w-full h-40 object-cover" alt="<?php echo $related['title']; ?>">
                        <?php else: ?>
                            <div class="h-40 bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                                <i class="fas fa-newspaper text-4xl text-white opacity-50"></i>
                            </div>
                        <?php endif; ?>
                        <div class="p-4">
                            <h4 class="font-semibold mb-2 hover:text-red-primary transition-colors">
                                <a href="article-detail.php?id=<?php echo $related['id']; ?>"><?php echo truncate_text($related['title'], 60); ?></a>
                            </h4>
                            <div class="flex items-center text-sm text-gray-400 mb-3">
                                <i class="far fa-clock mr-2"></i>
                                <span><?php echo $related['read_time']; ?> นาที</span>
                            </div>
                            <a href="article-detail.php?id=<?php echo $related['id']; ?>" class="text-red-primary hover:text-red-light text-sm">
                                อ่านเพิ่มเติม <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </article>

    <footer class="bg-dark py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="text-2xl font-bold text-red-primary mb-4"><?php echo get_logo($settings); ?></div>
                    <p class="text-gray-400 mb-4">บริการโมเดลและนางแบบมืออาชีพ ครบวงจร คุณภาพสูง</p>
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
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const closeMenuBtn = document.getElementById('close-menu');
        mobileMenuBtn.addEventListener('click', () => { mobileMenu.classList.add('open'); });
        closeMenuBtn.addEventListener('click', () => { mobileMenu.classList.remove('open'); });
        mobileMenu.querySelectorAll('a').forEach(link => { link.addEventListener('click', () => { mobileMenu.classList.remove('open'); }); });
        const goToTopBtn = document.getElementById('go-to-top');
        window.addEventListener('scroll', () => { if (window.pageYOffset > 300) { goToTopBtn.classList.add('show'); } else { goToTopBtn.classList.remove('show'); } });
        goToTopBtn.addEventListener('click', () => { window.scrollTo({ top: 0, behavior: 'smooth' }); });
        
        function copyLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                alert('คัดลอกลิงก์เรียบร้อย!');
            });
        }
    </script>
</body>
</html>



