<?php
/**
 * VIBEDAYBKK - Gallery Page
 * หน้าแกลลอรี่รูปภาพ
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

// ดึงข้อมูลแกลลอรี่
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

$category_filter = isset($_GET['category']) ? clean_input($_GET['category']) : '';
$search_query = isset($_GET['search']) ? clean_input($_GET['search']) : '';

// ใช้ตาราง gallery ที่มีข้อมูลอยู่แล้ว
$check_gallery = $conn->query("SHOW TABLES LIKE 'gallery'");

if ($check_gallery->num_rows == 0) {
    // ถ้าไม่มีตาราง gallery ให้ใช้ข้อมูลแบบง่าย
    $gallery_images = [];
    $categories = [];
    $total_images = 0;
    $total_pages = 0;
} else {
    // ตรวจสอบฟิลด์ในตาราง gallery
    $gallery_structure = $conn->query("DESCRIBE gallery");
    $gallery_fields = [];
    while ($row = $gallery_structure->fetch_assoc()) {
        $gallery_fields[] = $row['Field'];
    }
    
    // สร้างเงื่อนไขการค้นหา
    $where_conditions = ["1=1"]; // ใช้เงื่อนไขพื้นฐาน
    $params = [];
    $types = '';
    
    // ตรวจสอบว่ามีฟิลด์ is_active หรือไม่
    if (in_array('is_active', $gallery_fields)) {
        $where_conditions[0] = "is_active = 1";
    }
    
    // เฉพาะรูปภาพที่อัปโหลดจริง ไม่ใช่ตัวอย่าง
    $where_conditions[] = "image IS NOT NULL AND image != '' AND image NOT LIKE '%unsplash%' AND image NOT LIKE '%placeholder%'";
    
    if (!empty($category_filter)) {
        if (in_array('category', $gallery_fields)) {
            $where_conditions[] = "category = ?";
            $params[] = $category_filter;
            $types .= 's';
        }
    }
    
    if (!empty($search_query)) {
        $search_conditions = [];
        if (in_array('title', $gallery_fields)) {
            $search_conditions[] = "title LIKE ?";
            $params[] = "%{$search_query}%";
            $types .= 's';
        }
        if (in_array('description', $gallery_fields)) {
            $search_conditions[] = "description LIKE ?";
            $params[] = "%{$search_query}%";
            $types .= 's';
        }
        if (in_array('tags', $gallery_fields)) {
            $search_conditions[] = "tags LIKE ?";
            $params[] = "%{$search_query}%";
            $types .= 's';
        }
        
        if (!empty($search_conditions)) {
            $where_conditions[] = "(" . implode(' OR ', $search_conditions) . ")";
        }
    }
    
    $where_sql = implode(' AND ', $where_conditions);
    
    // นับจำนวนรูปภาพทั้งหมด
    $count_sql = "SELECT COUNT(*) as total FROM gallery WHERE {$where_sql}";
    $count_stmt = $conn->prepare($count_sql);
    if (!empty($params)) {
        $count_stmt->bind_param($types, ...$params);
    }
    $count_stmt->execute();
    $total_result = $count_stmt->get_result()->fetch_assoc();
    $total_images = $total_result['total'];
    $total_pages = ceil($total_images / $per_page);
    $count_stmt->close();
    
    // ดึงรูปภาพ
    $sql = "SELECT * FROM gallery WHERE {$where_sql} ORDER BY sort_order ASC, created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $params[] = $per_page;
    $params[] = $offset;
    $types .= 'ii';
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $gallery_images = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    // ดึงหมวดหมู่สำหรับ filter
    if (in_array('category', $gallery_fields)) {
        $categories_sql = "SELECT DISTINCT category FROM gallery WHERE category IS NOT NULL AND category != ''";
        if (in_array('is_active', $gallery_fields)) {
            $categories_sql .= " AND is_active = 1";
        }
        $categories_sql .= " ORDER BY category ASC";
        $categories = db_get_rows($conn, $categories_sql);
    } else {
        $categories = [];
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Basic -->
    <title><?php echo htmlspecialchars($global_settings['seo_title'] ?? $global_settings['site_name'] ?? 'VIBEDAYBKK'); ?> - แกลลอรี่</title>
    <meta name="description" content="แกลลอรี่รูปภาพและผลงานของ VIBEDAYBKK">
    
    <!-- Robots -->
    <meta name="robots" content="<?php echo (($global_settings['robots_index'] ?? '1') == '1' ? 'index' : 'noindex') . ',' . (($global_settings['robots_follow'] ?? '1') == '1' ? 'follow' : 'nofollow'); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($global_settings['site_name'] ?? 'VIBEDAYBKK'); ?> - แกลลอรี่">
    <meta property="og:description" content="แกลลอรี่รูปภาพและผลงานของ VIBEDAYBKK">
    <meta property="og:url" content="<?php echo BASE_URL; ?>/gallery.php">
    <meta property="og:image" content="<?php echo BASE_URL; ?>/<?php echo $global_settings['logo_image'] ?? 'assets/images/logo.png'; ?>">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($global_settings['site_name'] ?? 'VIBEDAYBKK'); ?> - แกลลอรี่">
    <meta name="twitter:description" content="แกลลอรี่รูปภาพและผลงานของ VIBEDAYBKK">
    <meta name="twitter:image" content="<?php echo BASE_URL; ?>/<?php echo $global_settings['logo_image'] ?? 'assets/images/logo.png'; ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL; ?>/favicon.ico">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'red-primary': '#DC2626',
                        'red-light': '#EF4444',
                        'dark': '#1a1a1a',
                        'dark-light': '#2d2d2d',
                    },
                    fontFamily: {
                        'thai': ['Noto Sans Thai', 'sans-serif'],
                        'inter': ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Custom Styles -->
    <style>
        /* Font Awesome Icons */
        .fa, .fas, .far, .fal, .fab {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands" !important;
            font-weight: 900;
        }
        
        .far {
            font-weight: 400;
        }
        
        .fab {
            font-weight: 400;
        }
        
        /* Ensure icons display properly */
        i[class^="fa-"], i[class*=" fa-"] {
            display: inline-block;
            font-style: normal;
            font-variant: normal;
            text-rendering: auto;
            line-height: 1;
        }
        
        body {
            font-family: 'Noto Sans Thai', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
        }
        
        .hover-scale {
            transition: transform 0.3s ease;
        }
        
        .hover-scale:hover {
            transform: scale(1.05);
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
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            aspect-ratio: 4/3;
            background: #2d2d2d;
        }
        
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .gallery-item:hover img {
            transform: scale(1.1);
        }
        
        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            padding: 1.5rem;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }
        
        .gallery-item:hover .gallery-overlay {
            transform: translateY(0);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin: 2rem 0;
        }
        
        .pagination a, .pagination span {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            color: #9ca3af;
            background: #2d2d2d;
            border: 1px solid #374151;
            transition: all 0.3s ease;
        }
        
        .pagination a:hover {
            background: #DC2626;
            color: white;
            border-color: #DC2626;
        }
        
        .pagination .current {
            background: #DC2626;
            color: white;
            border-color: #DC2626;
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
    </style>
</head>
<body class="bg-dark text-white">
    <!-- Header -->
    <header class="bg-dark/95 backdrop-blur-sm sticky top-0 z-50 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="<?php echo BASE_URL; ?>" class="flex items-center">
                        <?php if (!empty($global_settings['logo_image'])): ?>
                            <img src="<?php echo UPLOADS_URL . '/' . $global_settings['logo_image']; ?>" alt="<?php echo htmlspecialchars($global_settings['site_name'] ?? 'VIBEDAYBKK'); ?>" class="h-10 w-auto">
                        <?php else: ?>
                            <span class="text-2xl font-bold text-white">
                                <?php echo htmlspecialchars($global_settings['site_name'] ?? 'VIBEDAYBKK'); ?>
                            </span>
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
                        <?php 
                        $logo_type = $global_settings['logo_type'] ?? 'text';
                        $logo_text = $global_settings['logo_text'] ?? 'VIBEDAYBKK';
                        $logo_image = $global_settings['logo_image'] ?? '';
                        
                        if ($logo_type === 'image' && !empty($logo_image)): 
                        ?>
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
                        <div class="flex items-center gap-3 text-gray-300">
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
    </header>

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

    <!-- Main Content -->
    <main class="min-h-screen">
        <!-- Hero Section -->
        <section class="py-20 bg-gradient-to-br from-red-primary to-red-light">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    <i class="fas fa-images mr-4"></i>แกลลอรี่
                </h1>
                <p class="text-xl md:text-2xl text-white/90 mb-8">
                    รูปภาพและผลงานของเรา
                </p>
                
                <!-- Search and Filter -->
                <div class="max-w-2xl mx-auto">
                    <form method="GET" class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="ค้นหารูปภาพ..." class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/50">
                        </div>
                        <div class="md:w-48">
                            <select name="category" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-white/50">
                                <option value="">ทุกหมวดหมู่</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat['category']); ?>" <?php echo $category_filter === $cat['category'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['category']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="px-6 py-3 bg-white text-red-primary rounded-lg font-semibold hover:bg-white/90 transition-colors">
                            <i class="fas fa-search mr-2"></i>ค้นหา
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Gallery Section -->
        <section class="py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Results Count -->
                <div class="mb-8">
                    <p class="text-gray-400 text-lg">
                        พบ <?php echo number_format($total_images); ?> รูปภาพ
                        <?php if (!empty($category_filter)): ?>
                            ในหมวดหมู่ "<?php echo htmlspecialchars($category_filter); ?>"
                        <?php endif; ?>
                    </p>
                </div>

                <!-- Gallery Grid -->
                <?php if (!empty($gallery_images)): ?>
                    <div class="gallery-grid">
                        <?php foreach ($gallery_images as $index => $image): ?>
                            <div class="gallery-item hover-scale cursor-pointer" onclick="openModal(<?php echo $index; ?>)">
                                <?php
                                // ตรวจสอบว่าเป็น URL หรือ path
                                $image_src = $image['image'] ?? '';
                                if (filter_var($image_src, FILTER_VALIDATE_URL)) {
                                    // External URL
                                    $final_image_src = $image_src;
                                } else {
                                    // Local path - ตรวจสอบว่า path มี 'uploads/' หน้าหรือไม่
                                    if (strpos($image_src, 'uploads/') === 0) {
                                        // ถ้ามี uploads/ อยู่แล้ว ใช้ BASE_URL
                                        $final_image_src = BASE_URL . '/' . $image_src;
                                    } else {
                                        // ถ้าไม่มี ใช้ UPLOADS_URL
                                        $final_image_src = UPLOADS_URL . '/' . $image_src;
                                    }
                                }
                                ?>
                                <img src="<?php echo $final_image_src; ?>" alt="<?php echo htmlspecialchars($image['title']); ?>" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=300&fit=crop'">
                                <div class="gallery-overlay">
                                    <div class="absolute top-4 right-4">
                                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white/30 transition-all">
                                            <i class="fas fa-search-plus text-white text-lg"></i>
                                        </div>
                                    </div>
                                    <h3 class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($image['title']); ?></h3>
                                    <?php if (!empty($image['description'])): ?>
                                        <p class="text-sm text-gray-300 line-clamp-2"><?php echo htmlspecialchars($image['description']); ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($image['category'])): ?>
                                        <span class="inline-block mt-2 px-3 py-1 bg-red-primary text-white text-xs rounded-full">
                                            <?php echo htmlspecialchars($image['category']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?><?php echo !empty($category_filter) ? '&category=' . urlencode($category_filter) : ''; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>">
                                    <i class="fas fa-chevron-left"></i> ก่อนหน้า
                                </a>
                            <?php endif; ?>

                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <?php if ($i == $page): ?>
                                    <span class="current"><?php echo $i; ?></span>
                                <?php else: ?>
                                    <a href="?page=<?php echo $i; ?><?php echo !empty($category_filter) ? '&category=' . urlencode($category_filter) : ''; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?php echo $page + 1; ?><?php echo !empty($category_filter) ? '&category=' . urlencode($category_filter) : ''; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>">
                                    ถัดไป <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-20">
                        <i class="fas fa-images text-6xl text-gray-600 mb-4"></i>
                        <h3 class="text-2xl font-semibold text-gray-400 mb-2">ไม่พบรูปภาพ</h3>
                        <p class="text-gray-500 mb-8">ลองเปลี่ยนคำค้นหาหรือหมวดหมู่ดูนะคะ</p>
                        <a href="gallery.php" class="inline-block bg-red-primary hover:bg-red-light text-white font-bold py-3 px-8 rounded-full transition-all duration-300 hover:scale-105">
                            <i class="fas fa-refresh mr-2"></i>ดูทั้งหมด
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

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
                        <a href="<?php echo htmlspecialchars($social['url']); ?>" class="<?php echo $social['color']; ?> text-white" title="<?php echo $social['title']; ?>" target="_blank" rel="noopener">
                            <i class="fab <?php echo $social['icon']; ?> text-lg"></i>
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
                        $service_categories = array_slice($categories, 0, 4);
                        foreach ($service_categories as $category): 
                        ?>
                        <li><a href="#services" class="text-gray-400 hover:text-red-primary transition-colors duration-300"><?php echo htmlspecialchars($category['name']); ?></a></li>
                        <?php endforeach; ?>
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

    <!-- Go to Top Button -->
    <?php if ($go_to_top_enabled == '1'): ?>
        <button id="go-to-top" class="fixed <?php echo $go_to_top_position === 'left' ? 'left-6' : 'right-6'; ?> bottom-6 w-12 h-12 rounded-full <?php echo $go_to_top_bg_color; ?> <?php echo $go_to_top_text_color; ?> shadow-lg hover:shadow-xl transition-all duration-300 opacity-0 pointer-events-none z-50">
            <i class="fas <?php echo $go_to_top_icon; ?>"></i>
        </button>
    <?php endif; ?>

    <!-- JavaScript -->
    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black/95 backdrop-blur-sm z-50 hidden items-center justify-center p-4" onclick="closeModal(event)">
        <div class="relative max-w-7xl w-full h-full flex items-center justify-center">
            <!-- Close Button -->
            <button onclick="closeModal()" class="absolute top-4 right-4 z-50 w-12 h-12 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all">
                <i class="fas fa-times text-xl"></i>
            </button>
            
            <!-- Previous Button -->
            <button onclick="previousImage(event)" class="absolute left-4 z-50 w-12 h-12 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all">
                <i class="fas fa-chevron-left text-xl"></i>
            </button>
            
            <!-- Next Button -->
            <button onclick="nextImage(event)" class="absolute right-4 z-50 w-12 h-12 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all">
                <i class="fas fa-chevron-right text-xl"></i>
            </button>
            
            <!-- Image Container -->
            <div class="relative max-h-[90vh] max-w-full flex items-center justify-center">
                <img id="modalImage" src="" alt="Gallery Image" class="max-h-[90vh] max-w-full w-auto h-auto object-contain rounded-lg shadow-2xl">
                <div id="modalError" class="hidden flex-col items-center justify-center text-center p-10">
                    <i class="fas fa-image-slash text-8xl mb-6 text-white/30"></i>
                    <p class="text-2xl text-white/70">ไม่สามารถโหลดรูปภาพได้</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Gallery Images Data
        const galleryImages = <?php 
        $images_data = array_map(function($img) {
            $image_src = $img['image'] ?? '';
            if (filter_var($image_src, FILTER_VALIDATE_URL)) {
                // External URL
                $final_src = $image_src;
            } else {
                // Local path - ตรวจสอบว่า path มี 'uploads/' หน้าหรือไม่
                if (strpos($image_src, 'uploads/') === 0) {
                    // ถ้ามี uploads/ อยู่แล้ว ใช้ BASE_URL
                    $final_src = BASE_URL . '/' . $image_src;
                } else {
                    // ถ้าไม่มี ใช้ UPLOADS_URL
                    $final_src = UPLOADS_URL . '/' . $image_src;
                }
            }
            return [
                'src' => $final_src,
                'title' => $img['title'] ?? '',
                'description' => $img['description'] ?? '',
                'category' => $img['category'] ?? ''
            ];
        }, !empty($gallery_images) ? $gallery_images : []);
        echo json_encode($images_data, JSON_UNESCAPED_SLASHES);
        ?>;
        
        let currentImageIndex = 0;
        
        // Open Modal
        function openModal(index) {
            if (!galleryImages || galleryImages.length === 0) return;
            if (index < 0 || index >= galleryImages.length) return;
            
            currentImageIndex = index;
            
            const modal = document.getElementById('imageModal');
            if (!modal) return;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            
            updateModalContent();
        }
        
        // Close Modal
        function closeModal(event) {
            if (event && event.target.id !== 'imageModal') {
                return;
            }
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
        
        // Previous Image
        function previousImage(event) {
            event.stopPropagation();
            currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
            updateModalContent();
        }
        
        // Next Image
        function nextImage(event) {
            event.stopPropagation();
            currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
            updateModalContent();
        }
        
        // Update Modal Content
        function updateModalContent() {
            const image = galleryImages[currentImageIndex];
            const modalImage = document.getElementById('modalImage');
            const modalError = document.getElementById('modalError');
            
            if (!modalImage || !modalError || !image || !image.src) return;
            
            // ซ่อน error แสดงรูปภาพ
            modalError.style.display = 'none';
            modalImage.style.display = 'block';
            modalImage.src = image.src;
            
            // ตรวจสอบการโหลด
            modalImage.onload = function() {
                modalError.style.display = 'none';
                modalImage.style.display = 'block';
            };
            
            modalImage.onerror = function() {
                // ซ่อนรูปภาพ แสดง error
                modalImage.style.display = 'none';
                modalError.style.display = 'flex';
            };
        }
        
        // Keyboard Navigation
        document.addEventListener('keydown', function(event) {
            const modal = document.getElementById('imageModal');
            if (!modal.classList.contains('hidden')) {
                if (event.key === 'Escape') {
                    closeModal();
                } else if (event.key === 'ArrowLeft') {
                    previousImage(event);
                } else if (event.key === 'ArrowRight') {
                    nextImage(event);
                }
            }
        });
        
        // Initialize gallery items click events
        document.addEventListener('DOMContentLoaded', function() {
            const galleryItems = document.querySelectorAll('.gallery-item');
            galleryItems.forEach((item, index) => {
                item.addEventListener('click', function() {
                    openModal(index);
                });
            });
        });

        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const closeMenuBtn = document.getElementById('close-menu');

        mobileMenuBtn?.addEventListener('click', () => {
            mobileMenu.classList.add('open');
        });

        closeMenuBtn?.addEventListener('click', () => {
            mobileMenu.classList.remove('open');
        });

        const mobileMenuLinks = mobileMenu.querySelectorAll('a');
        mobileMenuLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('open');
            });
        });

        // Go to Top Button
        <?php if ($go_to_top_enabled == '1'): ?>
        const goToTopBtn = document.getElementById('go-to-top');

        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                goToTopBtn.classList.add('show');
                goToTopBtn.classList.remove('opacity-0', 'pointer-events-none');
                goToTopBtn.classList.add('opacity-100');
            } else {
                goToTopBtn.classList.remove('show');
                goToTopBtn.classList.add('opacity-0', 'pointer-events-none');
                goToTopBtn.classList.remove('opacity-100');
            }
        });

        goToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        <?php endif; ?>
    </script>

</body>
</html>

<?php $conn->close(); ?>
