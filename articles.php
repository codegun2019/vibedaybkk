<?php
/**
 * VIBEDAYBKK - Articles Page
 * หน้าบทความทั้งหมด
 */
require_once 'includes/config.php';
require_once 'includes/functions.php';

// ดึงข้อมูลจาก settings
$global_settings = [];
$result = db_get_rows($conn, "SELECT * FROM settings");
foreach ($result as $row) {
    $global_settings[$row['setting_key']] = $row['setting_value'];
}

// ดึงข้อมูลเมนู
$main_menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC");

// ดึงข้อมูลโซเชียลมีเดีย (ตรงตามหลังบ้าน)
$social_platforms = [
    'facebook' => ['color' => 'bg-blue-600 hover:bg-blue-700', 'default_icon' => 'fa-facebook-f', 'title' => 'Facebook'],
    'instagram' => ['color' => 'bg-pink-500 hover:bg-pink-600', 'default_icon' => 'fa-instagram', 'title' => 'Instagram'],
    'twitter' => ['color' => 'bg-black hover:bg-gray-800', 'default_icon' => 'fa-twitter', 'title' => 'X (Twitter)'],
    'line' => ['color' => 'bg-green-500 hover:bg-green-600', 'default_icon' => 'fa-line', 'title' => 'LINE'],
    'youtube' => ['color' => 'bg-red-600 hover:bg-red-700', 'default_icon' => 'fa-youtube', 'title' => 'YouTube'],
    'tiktok' => ['color' => 'bg-gray-800 hover:bg-gray-900', 'default_icon' => 'fa-tiktok', 'title' => 'TikTok']
];

$active_socials = [];
foreach ($social_platforms as $platform => $data) {
    $enabled = $global_settings["social_{$platform}_enabled"] ?? '0';
    if ($enabled == '1') {
        $url = $global_settings["social_{$platform}_url"] ?? '';
        // เฉพาะที่มี URL จริงๆ ถึงจะแสดง (ไม่ใช่ "text" หรือค่าว่าง)
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

// ดึงข้อมูลติดต่อ (ตรงตามหลังบ้าน)
$contact_info = [
    'phone' => $global_settings['site_phone'] ?? $global_settings['contact_phone'] ?? '02-123-4567',
    'email' => $global_settings['site_email'] ?? $global_settings['contact_email'] ?? 'info@vibedaybkk.com',
    'line_id' => $global_settings['site_line'] ?? $global_settings['contact_line'] ?? '@vibedaybkk',
    'address' => $global_settings['site_address'] ?? $global_settings['contact_address'] ?? '123 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110'
];

// Go to Top settings (ตรงตามหลังบ้าน)
$go_to_top_enabled = $global_settings['gototop_enabled'] ?? '1';
$go_to_top_icon = $global_settings['gototop_icon'] ?? 'fa-arrow-up';
$go_to_top_bg_color = $global_settings['gototop_bg_color'] ?? 'bg-red-primary';
$go_to_top_text_color = $global_settings['gototop_text_color'] ?? 'text-white';
$go_to_top_position = $global_settings['gototop_position'] ?? 'right';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Filter by category
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build WHERE clause
$where_conditions = ["a.status = 'published'"];
$params = [];
$types = '';

if ($category_id) {
    $where_conditions[] = "a.category_id = ?";
    $params[] = $category_id;
    $types .= 'i';
}

if ($search) {
    $where_conditions[] = "(a.title LIKE ? OR a.excerpt LIKE ? OR a.content LIKE ?)";
    $search_param = "%{$search}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

$where_sql = implode(' AND ', $where_conditions);

// Count total articles
$count_sql = "SELECT COUNT(*) as total FROM articles a WHERE {$where_sql}";
$stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total_result = $stmt->get_result()->fetch_assoc();
$total_articles = $total_result['total'];
$total_pages = ceil($total_articles / $per_page);

// Fetch articles
$sql = "
    SELECT a.*, ac.name as category_name, ac.color as category_color, ac.icon as category_icon, u.full_name as author_name
    FROM articles a
    LEFT JOIN article_categories ac ON a.category_id = ac.id
    LEFT JOIN users u ON a.author_id = u.id
    WHERE {$where_sql}
    ORDER BY a.published_at DESC, a.created_at DESC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);
$params[] = $per_page;
$params[] = $offset;
$types .= 'ii';
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$articles = [];
while ($row = $result->fetch_assoc()) {
    $articles[] = $row;
}

// Fetch categories for filter
$categories = db_get_rows($conn, "SELECT * FROM article_categories WHERE status = 'active' ORDER BY sort_order ASC, name ASC");

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บทความและข่าวสาร - <?php echo h($global_settings['site_name'] ?? 'VIBEDAYBKK'); ?></title>
    
    <!-- Meta Tags -->
    <meta name="description" content="อ่านบทความและข่าวสารล่าสุดจาก <?php echo h($global_settings['site_name'] ?? 'VIBEDAYBKK'); ?>">
    
    <!-- Fonts -->
    <?php 
    $font_family = $global_settings['font_family'] ?? 'Noto Sans Thai';
    $font_family_url = str_replace(' ', '+', $font_family);
    ?>
    <link href="https://fonts.googleapis.com/css2?family=<?php echo urlencode($font_family); ?>:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'noto': ['<?php echo addslashes($font_family); ?>', 'sans-serif'],
                    },
                    colors: {
                        'dark': '#0a0a0a',
                        'dark-light': '#1a1a1a',
                        'red-primary': '<?php echo htmlspecialchars($global_settings["theme_primary_color"] ?? "#DC2626"); ?>',
                        'red-light': '#EF4444',
                        'accent': '<?php echo htmlspecialchars($global_settings["theme_accent_color"] ?? "#FBBF24"); ?>',
                    }
                }
            }
        }
    </script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: '<?php echo addslashes($font_family); ?>', sans-serif;
            box-sizing: border-box;
            overflow-x: hidden;
            <?php 
            $font_size_base = $global_settings['font_size_base'] ?? 14;
            $font_size_scale = $global_settings['font_size_scale'] ?? 0.875;
            $final_font_size = $font_size_base * $font_size_scale;
            ?>
            font-size: <?php echo $final_font_size; ?>px;
        }
        
        /* Keep Font Awesome icons */
        i, .fa, .fas, .far, .fal, .fab {
            font-family: 'Font Awesome 6 Free', 'Font Awesome 6 Brands' !important;
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
        }
        
        .social-sidebar {
            position: fixed;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .social-sidebar a {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }
        
        .social-sidebar a:hover {
            transform: translateX(-5px) scale(1.1);
            box-shadow: 0 6px 12px rgba(220, 38, 38, 0.4);
        }
        
        .go-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        
        .go-to-top.show {
            opacity: 1;
            visibility: visible;
        }
        
        .go-to-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(220, 38, 38, 0.5);
        }
        
        .mobile-menu {
            transform: translateX(100%);
            transition: transform 0.3s ease;
            background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%) !important;
        }
        
        .mobile-menu.open {
            transform: translateX(0);
        }
        
        /* Mobile Menu Links Styling */
        .mobile-menu-link {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .mobile-menu-link:hover {
            background: rgba(220, 38, 38, 0.2) !important;
        }
        
        .hover-scale {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .hover-scale:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(220, 38, 38, 0.3);
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .go-to-top { position: fixed; bottom: 30px; right: 30px; width: 50px; height: 50px; border-radius: 50%; z-index: 1000; opacity: 0; visibility: hidden; transition: all 0.3s ease; }
        .go-to-top.show { opacity: 1; visibility: visible; }
        .go-to-top:hover { transform: translateY(-5px); }
    </style>
</head>
<body class="bg-dark text-white">

    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-dark/95 backdrop-blur-sm z-50 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="<?php echo BASE_URL; ?>" class="text-2xl font-bold text-red-primary flex items-center">
                        <?php 
                        $logo_type = $global_settings['logo_type'] ?? 'text';
                        $logo_text = $global_settings['logo_text'] ?? 'VIBEDAYBKK';
                        $logo_image = $global_settings['logo_image'] ?? '';
                        
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
                        <?php 
                        // ดึงเมนูทั้งหมดจาก database เท่านั้น (ไม่ hard-coded)
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
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="mobile-menu fixed top-0 right-0 h-full w-80 bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 shadow-2xl md:hidden z-50 backdrop-blur-md">
            <div class="p-6">
                <!-- Header Mobile Menu -->
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
                <div class="space-y-2">
                    <?php 
                    // ดึงเมนูจาก database เท่านั้น
                    if (!empty($main_menus)): 
                        foreach ($main_menus as $index => $menu): 
                    ?>
                        <a href="<?php echo htmlspecialchars($menu['url']); ?>" class="mobile-menu-link group flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white hover:bg-red-primary/20 transition-all duration-300 border border-transparent hover:border-red-primary/30">
                            <?php if (!empty($menu['icon'])): ?>
                                <i class="fas <?php echo htmlspecialchars($menu['icon']); ?> text-lg group-hover:text-red-primary transition-colors"></i>
                            <?php else: ?>
                                <i class="fas fa-circle text-xs group-hover:text-red-primary transition-colors"></i>
                            <?php endif; ?>
                            <span class="font-medium"><?php echo htmlspecialchars($menu['title']); ?></span>
                            <i class="fas fa-chevron-right text-xs ml-auto opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </a>
                    <?php 
                        endforeach;
                    endif; 
                    ?>
                </div>
                
                <!-- Social Media in Mobile Menu -->
                <?php if (!empty($active_socials)): ?>
                <div class="mt-8 pt-6 border-t border-gray-700">
                    <p class="text-gray-400 text-sm mb-4 font-semibold">ติดตามเรา</p>
                    <div class="flex flex-wrap gap-3">
                        <?php foreach ($active_socials as $platform => $social): ?>
                        <a href="<?php echo htmlspecialchars($social['url']); ?>" 
                           class="w-12 h-12 <?php echo str_replace('hover:bg-', 'bg-', explode(' ', $social['color'])[0]); ?> rounded-full flex items-center justify-center text-white hover:scale-110 transition-transform shadow-lg" 
                           title="<?php echo $social['title']; ?>" 
                           target="_blank" 
                           rel="noopener">
                            <i class="fab <?php echo $social['icon']; ?>"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Contact Info in Mobile Menu -->
                <div class="mt-6 pt-6 border-t border-gray-700">
                    <p class="text-gray-400 text-sm mb-4 font-semibold">ติดต่อเรา</p>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center gap-3 text-gray- cached">
                            <i class="fas fa-phone text-red-primary"></i>
                            <span><?php echo htmlspecialchars($contact_info['phone']); ?></span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-300">
                            <i class="fab fa-line text-red-primary"></i>
                            <span><?php echo htmlspecialchars($contact_info['line_id']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Social Sidebar -->
    <?php if (!empty($active_socials)): ?>
    <div class="social-sidebar hidden lg:flex">
        <?php foreach ($active_socials as $platform => $social): ?>
        <a href="<?php echo htmlspecialchars($social['url']); ?>" class="<?php echo $social['color']; ?> text-white" title="<?php echo $social['title']; ?>" target="_blank" rel="noopener">
            <i class="fab <?php echo $social['icon']; ?> text-lg"></i>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Go to Top Button -->
    <?php if ($go_to_top_enabled == '1'): ?>
    <button id="go-to-top" class="go-to-top <?php echo htmlspecialchars($go_to_top_bg_color); ?> hover:opacity-90 <?php echo htmlspecialchars($go_to_top_text_color); ?>" title="กลับขึ้นด้านบน" style="<?php echo $go_to_top_position === 'left' ? 'left: 30px; right: auto;' : ''; ?>">
        <i class="fas <?php echo htmlspecialchars($go_to_top_icon); ?> text-lg"></i>
    </button>
    <?php endif; ?>

    <!-- Hero Section -->
    <section class="pt-24 pb-12 bg-gradient-to-b from-dark-light to-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">
                    บทความและ<span class="text-red-primary">ข่าวสาร</span>
                </h1>
                <p class="text-gray-400 text-lg">อัพเดทเทรนด์และเรื่องราวน่าสนใจจากโลกของเรา</p>
            </div>
        </div>
    </section>

    

    <!-- Articles Grid -->
    <section class="py-16 bg-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php if (!empty($articles)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($articles as $article): ?>
                <article class="bg-dark-light rounded-lg overflow-hidden hover-scale group">
                    <?php if (!empty($article['featured_image'])): ?>
                    <div class="relative overflow-hidden h-48">
                        <img src="<?php echo UPLOADS_URL . '/' . $article['featured_image']; ?>" alt="<?php echo h($article['title']); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <?php if (!empty($article['category_name'])): ?>
                        <span class="absolute top-4 left-4 px-3 py-1 text-xs font-semibold rounded-full text-white" style="background-color: <?php echo h($article['category_color'] ?? '#DC2626'); ?>">
                            <?php if (!empty($article['category_icon'])): ?>
                                <i class="<?php echo h($article['category_icon']); ?> mr-1"></i>
                            <?php endif; ?>
                            <?php echo h($article['category_name']); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <div class="relative overflow-hidden h-48 bg-gradient-to-br from-red-primary to-red-light flex items-center justify-center">
                        <i class="fas fa-newspaper text-6xl text-white/30"></i>
                        <?php if (!empty($article['category_name'])): ?>
                        <span class="absolute top-4 left-4 px-3 py-1 text-xs font-semibold rounded-full text-white" style="background-color: <?php echo h($article['category_color'] ?? '#DC2626'); ?>">
                            <?php if (!empty($article['category_icon'])): ?>
                                <i class="<?php echo h($article['category_icon']); ?> mr-1"></i>
                            <?php endif; ?>
                            <?php echo h($article['category_name']); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div class="p-6">
                        <div class="flex items-center text-sm text-gray-500 mb-3">
                            <i class="far fa-calendar mr-2"></i>
                            <span><?php echo date('d M Y', strtotime($article['published_at'] ?? $article['created_at'])); ?></span>
                            <?php if (!empty($article['read_time'])): ?>
                            <i class="far fa-clock ml-4 mr-2"></i>
                            <span><?php echo h($article['read_time']); ?> นาที</span>
                            <?php endif; ?>
                        </div>

                        <h3 class="text-xl font-bold mb-3 line-clamp-2 group-hover:text-red-primary transition-colors">
                            <?php echo h($article['title']); ?>
                        </h3>

                        <?php if (!empty($article['excerpt'])): ?>
                        <p class="text-gray-400 text-sm mb-4 line-clamp-3">
                            <?php echo h($article['excerpt']); ?>
                        </p>
                        <?php endif; ?>

                        <a href="article-detail.php?slug=<?php echo h($article['slug']); ?>" class="inline-flex items-center text-red-primary hover:text-red-light transition-colors font-semibold">
                            อ่านเพิ่มเติม
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="mt-12 flex justify-center">
                <nav class="flex items-center space-x-2">
                    <!-- Previous Button -->
                    <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                       class="px-4 py-2 bg-dark-light border border-gray-700 rounded-lg text-white hover:border-red-primary transition-colors">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <?php endif; ?>

                    <!-- Page Numbers -->
                    <?php
                    $start = max(1, $page - 2);
                    $end = min($total_pages, $page + 2);
                    
                    if ($start > 1): ?>
                        <a href="?page=1<?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                           class="px-4 py-2 bg-dark-light border border-gray-700 rounded-lg text-white hover:border-red-primary transition-colors">
                            1
                        </a>
                        <?php if ($start > 2): ?>
                        <span class="px-2 text-gray-500">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start; $i <= $end; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                       class="px-4 py-2 rounded-lg <?php echo $i == $page ? 'bg-red-primary text-white' : 'bg-dark-light border border-gray-700 text-white hover:border-red-primary'; ?> transition-colors">
                        <?php echo $i; ?>
                    </a>
                    <?php endfor; ?>

                    <?php if ($end < $total_pages): ?>
                        <?php if ($end < $total_pages - 1): ?>
                        <span class="px-2 text-gray-500">...</span>
                        <?php endif; ?>
                        <a href="?page=<?php echo $total_pages; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                           class="px-4 py-2 bg-dark-light border border-gray-700 rounded-lg text-white hover:border-red-primary transition-colors">
                            <?php echo $total_pages; ?>
                        </a>
                    <?php endif; ?>

                    <!-- Next Button -->
                    <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                       class="px-4 py-2 bg-dark-light border border-gray-700 rounded-lg text-white hover:border-red-primary transition-colors">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <?php endif; ?>
                </nav>
            </div>
            <?php endif; ?>

            <?php else: ?>
            <!-- No Articles Found -->
            <div class="text-center py-20">
                <i class="fas fa-newspaper text-6xl text-gray-700 mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-400 mb-2">ไม่พบบทความ</h3>
                <p class="text-gray-500">ลองค้นหาด้วยคำอื่น หรือเลือกหมวดหมู่อื่น</p>
                <a href="articles.php" class="inline-block mt-6 px-6 py-3 bg-red-primary hover:bg-red-light text-white rounded-lg transition-colors">
                    ดูบทความทั้งหมด
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark-light py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-gray-400">
                © <?php echo date('Y'); ?> <?php echo h($global_settings['site_name'] ?? 'VIBEDAYBKK'); ?>. All rights reserved.
            </p>
        </div>
    </footer>

    <!-- Go to Top Button -->
    <?php if (($global_settings['show_go_to_top'] ?? '1') == '1'): ?>
    <button id="go-to-top" class="go-to-top bg-red-primary hover:bg-red-light text-white">
        <i class="fas fa-arrow-up"></i>
    </button>
    <?php endif; ?>

    <script>
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const closeMenu = document.getElementById('close-menu');

        mobileMenuBtn?.addEventListener('click', () => {
            mobileMenu.classList.add('open');
        });

        closeMenu?.addEventListener('click', () => {
            mobileMenu.classList.remove('open');
        });

        // Go to Top Button
        const goToTopBtn = document.getElementById('go-to-top');
        if (goToTopBtn) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    goToTopBtn.classList.add('show');
                } else {
                    goToTopBtn.classList.remove('show');
                }
            });

            goToTopBtn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        // Auto-submit search form on Enter
        const searchInput = document.querySelector('input[name="search"]');
        searchInput?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.target.form.submit();
            }
        });
    </script>

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
                    <p class="text-gray-400 mb-4">บริการจัดหาเด็กเอนเตอร์เทน ชงเหล้า<br>24 ชั่วโมง</p>
                    <?php if (!empty($active_socials)): ?>
                    <div class="flex space-x-3">
                        <?php foreach ($active_socials as $platform => $social): ?>
                        <a href="<?php echo htmlspecialchars($social['url']); ?>" class="bg-gray-800 <?php echo str_replace('bg-', 'hover:bg-', explode(' ', $social['color'])[0]); ?> w-10 h-10 rounded-full text-gray-400 hover:text-white transition-all duration-300 flex items-center justify-center hover:scale-110" title="<?php echo $social['title']; ?>" target="_blank" rel="noopener">
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
                        <li><a href="<?php echo BASE_URL; ?>#about" class="text-gray-400 hover:text-red-primary transition-colors duration-300">เกี่ยวกับเรา</a></li>
                        <li><a href="<?php echo BASE_URL; ?>#services" class="text-gray-400 hover:text-red-primary transition-colors duration-300">บริการ</a></li>
                        <li><a href="models.php" class="text-gray-400 hover:text-red-primary transition-colors duration-300">โมเดล</a></li>
                        <li><a href="articles.php" class="text-gray-400 hover:text-red-primary transition-colors duration-300">บทความ</a></li>
                        <li><a href="gallery.php" class="text-gray-400 hover:text-red-primary transition-colors duration-300">ผลงาน</a></li>
                        <li><a href="<?php echo BASE_URL; ?>#contact" class="text-gray-400 hover:text-red-primary transition-colors duration-300">ติดต่อ</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">บริการของเรา</h4>
                    <ul class="space-y-2">
                        <li><a href="models.php" class="text-gray-400 hover:text-red-primary transition-colors duration-300">โมเดล</a></li>
                        <li><a href="articles.php" class="text-gray-400 hover:text-red-primary transition-colors duration-300">บทความ</a></li>
                        <li><a href="gallery.php" class="text-gray-400 hover:text-red-primary transition-colors duration-300">แกลลอรี่</a></li>
                        <li><a href="<?php echo BASE_URL; ?>#contact" class="text-gray-400 hover:text-red-primary transition-colors duration-300">ติดต่อสอบถาม</a></li>
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
                <p>&copy; 2025 lollipop24hours. สงวนลิขสิทธิ์.</p>
            </div>
        </div>
    </footer>
</body>
</html>
<?php $conn->close(); ?>
