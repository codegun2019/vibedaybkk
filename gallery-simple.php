<?php
/**
 * VIBEDAYBKK - Gallery Page (Simple Version)
 * หน้าแกลลอรี่รูปภาพแบบง่าย
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

// ดึงข้อมูลโซเชียลมีเดีย
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

// ดึงข้อมูลติดต่อ
$contact_info = [
    'phone' => $global_settings['site_phone'] ?? $global_settings['contact_phone'] ?? '02-123-4567',
    'email' => $global_settings['site_email'] ?? $global_settings['contact_email'] ?? 'info@vibedaybkk.com',
    'line_id' => $global_settings['site_line'] ?? $global_settings['contact_line'] ?? '@vibedaybkk',
    'address' => $global_settings['site_address'] ?? $global_settings['contact_address'] ?? '123 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110'
];

// ข้อมูลแกลลอรี่แบบ Hard-coded
$gallery_images = [
    [
        'title' => 'การถ่ายภาพแฟชั่น',
        'description' => 'การถ่ายภาพแฟชั่นชุดสวยงามในสตูดิโอ',
        'category' => 'การถ่ายภาพ',
        'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=300&fit=crop'
    ],
    [
        'title' => 'การถ่ายภาพธรรมชาติ',
        'description' => 'การถ่ายภาพในบรรยากาศธรรมชาติที่สวยงาม',
        'category' => 'การถ่ายภาพ',
        'image' => 'https://images.unsplash.com/photo-1506905925346-04b8e0d7c0c0?w=400&h=300&fit=crop'
    ],
    [
        'title' => 'การถ่ายภาพสตรีท',
        'description' => 'การถ่ายภาพสไตล์สตรีทแฟชั่นในเมือง',
        'category' => 'การถ่ายภาพ',
        'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=300&fit=crop'
    ],
    [
        'title' => 'การถ่ายภาพคู่รัก',
        'description' => 'การถ่ายภาพคู่รักในบรรยากาศโรแมนติก',
        'category' => 'การถ่ายภาพ',
        'image' => 'https://images.unsplash.com/photo-1516589091380-5d8e87dfe2c0?w=400&h=300&fit=crop'
    ],
    [
        'title' => 'แฟชั่นชุดยูนิฟอร์ม',
        'description' => 'การแสดงแฟชั่นชุดยูนิฟอร์มสไตล์เกาหลี',
        'category' => 'แฟชั่น',
        'image' => 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=400&h=300&fit=crop'
    ],
    [
        'title' => 'แฟชั่นชุดว่ายน้ำ',
        'description' => 'การแสดงแฟชั่นชุดว่ายน้ำสไตล์บิกินี่',
        'category' => 'แฟชั่น',
        'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=300&fit=crop'
    ],
    [
        'title' => 'แฟชั่นชุดทำงาน',
        'description' => 'การแสดงแฟชั่นชุดทำงานสไตล์ออฟฟิศ',
        'category' => 'แฟชั่น',
        'image' => 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=400&h=300&fit=crop'
    ],
    [
        'title' => 'แฟชั่นชุดออกงาน',
        'description' => 'การแสดงแฟชั่นชุดออกงานสไตล์อีฟนิง',
        'category' => 'แฟชั่น',
        'image' => 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=400&h=300&fit=crop'
    ],
    [
        'title' => 'ไลฟ์สไตล์คาเฟ่',
        'description' => 'การถ่ายภาพไลฟ์สไตล์ในบรรยากาศคาเฟ่',
        'category' => 'ไลฟ์สไตล์',
        'image' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=400&h=300&fit=crop'
    ],
    [
        'title' => 'ไลฟ์สไตล์ฟิตเนส',
        'description' => 'การถ่ายภาพไลฟ์สไตล์การออกกำลังกาย',
        'category' => 'ไลฟ์สไตล์',
        'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=300&fit=crop'
    ],
    [
        'title' => 'ไลฟ์สไตล์บ้าน',
        'description' => 'การถ่ายภาพไลฟ์สไตล์ในบ้านพักอาศัย',
        'category' => 'ไลฟ์สไตล์',
        'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=300&fit=crop'
    ],
    [
        'title' => 'ไลฟ์สไตล์ท่องเที่ยว',
        'description' => 'การถ่ายภาพไลฟ์สไตล์การท่องเที่ยว',
        'category' => 'ไลฟ์สไตล์',
        'image' => 'https://images.unsplash.com/photo-1506905925346-04b8e0d7c0c0?w=400&h=300&fit=crop'
    ],
    [
        'title' => 'ความสวยความงามสกินแคร์',
        'description' => 'การถ่ายภาพความสวยความงามด้านสกินแคร์',
        'category' => 'ความสวยความงาม',
        'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=300&fit=crop'
    ],
    [
        'title' => 'ความสวยความงามเมคอัพ',
        'description' => 'การถ่ายภาพความสวยความงามด้านเมคอัพ',
        'category' => 'ความสวยความงาม',
        'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=300&fit=crop'
    ],
    [
        'title' => 'ความสวยความงามผม',
        'description' => 'การถ่ายภาพความสวยความงามด้านผม',
        'category' => 'ความสวยความงาม',
        'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=300&fit=crop'
    ],
    [
        'title' => 'อีเวนต์แฟชั่นโชว์',
        'description' => 'การถ่ายภาพในงานแฟชั่นโชว์',
        'category' => 'อีเวนต์',
        'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=300&fit=crop'
    ],
    [
        'title' => 'อีเวนต์เปิดตัวผลิตภัณฑ์',
        'description' => 'การถ่ายภาพในงานเปิดตัวผลิตภัณฑ์',
        'category' => 'อีเวนต์',
        'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=300&fit=crop'
    ],
    [
        'title' => 'อีเวนต์ปาร์ตี้',
        'description' => 'การถ่ายภาพในงานปาร์ตี้',
        'category' => 'อีเวนต์',
        'image' => 'https://images.unsplash.com/photo-1516589091380-5d8e87dfe2c0?w=400&h=300&fit=crop'
    ],
    [
        'title' => 'อีเวนต์คอนเสิร์ต',
        'description' => 'การถ่ายภาพในงานคอนเสิร์ต',
        'category' => 'อีเวนต์',
        'image' => 'https://images.unsplash.com/photo-1516589091380-5d8e87dfe2c0?w=400&h=300&fit=crop'
    ],
    [
        'title' => 'อีเวนต์เวิร์กช็อป',
        'description' => 'การถ่ายภาพในงานเวิร์กช็อป',
        'category' => 'อีเวนต์',
        'image' => 'https://images.unsplash.com/photo-1506905925346-04b8e0d7c0c0?w=400&h=300&fit=crop'
    ]
];

// หมวดหมู่สำหรับ filter
$categories = ['การถ่ายภาพ', 'แฟชั่น', 'ไลฟ์สไตล์', 'ความสวยความงาม', 'อีเวนต์'];

// ฟิลเตอร์
$category_filter = isset($_GET['category']) ? clean_input($_GET['category']) : '';
$search_query = isset($_GET['search']) ? clean_input($_GET['search']) : '';

// กรองรูปภาพ
$filtered_images = $gallery_images;
if (!empty($category_filter)) {
    $filtered_images = array_filter($filtered_images, function($image) use ($category_filter) {
        return $image['category'] === $category_filter;
    });
}
if (!empty($search_query)) {
    $filtered_images = array_filter($filtered_images, function($image) use ($search_query) {
        return stripos($image['title'], $search_query) !== false || 
               stripos($image['description'], $search_query) !== false;
    });
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
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
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
                
                <!-- Desktop Navigation -->
                <nav class="hidden md:flex space-x-8">
                    <?php foreach ($main_menus as $menu): ?>
                        <a href="<?php echo BASE_URL . '/' . $menu['url']; ?>" class="text-gray-300 hover:text-white transition-colors font-medium">
                            <?php if (!empty($menu['icon'])): ?>
                                <i class="<?php echo htmlspecialchars($menu['icon']); ?> mr-2"></i>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($menu['title']); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
                
                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-gray-300 hover:text-white focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Navigation -->
        <div id="mobile-menu" class="md:hidden hidden bg-dark/98 backdrop-blur-lg border-t border-gray-800">
            <div class="px-4 py-4 space-y-4">
                <?php foreach ($main_menus as $menu): ?>
                    <a href="<?php echo BASE_URL . '/' . $menu['url']; ?>" class="block text-gray-300 hover:text-white transition-colors font-medium py-2">
                        <?php if (!empty($menu['icon'])): ?>
                            <i class="<?php echo htmlspecialchars($menu['icon']); ?> mr-2"></i>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($menu['title']); ?>
                    </a>
                <?php endforeach; ?>
                
                <!-- Social Media Links -->
                <?php if (!empty($active_socials)): ?>
                    <div class="border-t border-gray-700 pt-4">
                        <div class="flex space-x-4">
                            <?php foreach ($active_socials as $platform => $social): ?>
                                <a href="<?php echo htmlspecialchars($social['url']); ?>" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full flex items-center justify-center transition-colors <?php echo $social['color']; ?>">
                                    <i class="fab <?php echo $social['icon']; ?>"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

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
                                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category_filter === $cat ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat); ?>
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
                        พบ <?php echo count($filtered_images); ?> รูปภาพ
                        <?php if (!empty($category_filter)): ?>
                            ในหมวดหมู่ "<?php echo htmlspecialchars($category_filter); ?>"
                        <?php endif; ?>
                    </p>
                </div>

                <!-- Gallery Grid -->
                <?php if (!empty($filtered_images)): ?>
                    <div class="gallery-grid">
                        <?php foreach ($filtered_images as $image): ?>
                            <div class="gallery-item hover-scale">
                                <img src="<?php echo $image['image']; ?>" alt="<?php echo htmlspecialchars($image['title']); ?>" loading="lazy">
                                <div class="gallery-overlay">
                                    <h3 class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($image['title']); ?></h3>
                                    <p class="text-sm text-gray-300 line-clamp-2"><?php echo htmlspecialchars($image['description']); ?></p>
                                    <span class="inline-block mt-2 px-3 py-1 bg-red-primary text-white text-xs rounded-full">
                                        <?php echo htmlspecialchars($image['category']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-20">
                        <i class="fas fa-images text-6xl text-gray-600 mb-4"></i>
                        <h3 class="text-2xl font-semibold text-gray-400 mb-2">ไม่พบรูปภาพ</h3>
                        <p class="text-gray-500 mb-8">ลองเปลี่ยนคำค้นหาหรือหมวดหมู่ดูนะคะ</p>
                        <a href="gallery-simple.php" class="inline-block bg-red-primary hover:bg-red-light text-white font-bold py-3 px-8 rounded-full transition-all duration-300 hover:scale-105">
                            <i class="fas fa-refresh mr-2"></i>ดูทั้งหมด
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-dark-light py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="md:col-span-2">
                    <div class="flex items-center mb-6">
                        <?php if (!empty($global_settings['logo_image'])): ?>
                            <img src="<?php echo UPLOADS_URL . '/' . $global_settings['logo_image']; ?>" alt="<?php echo htmlspecialchars($global_settings['site_name'] ?? 'VIBEDAYBKK'); ?>" class="h-12 w-auto mr-4">
                        <?php else: ?>
                            <span class="text-3xl font-bold text-white mr-4">
                                <?php echo htmlspecialchars($global_settings['site_name'] ?? 'VIBEDAYBKK'); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <p class="text-gray-400 mb-6 leading-relaxed">
                        <?php echo htmlspecialchars($global_settings['site_description'] ?? 'บริการโมเดลและนางแบบมืออาชีพ'); ?>
                    </p>
                    
                    <!-- Social Media -->
                    <?php if (!empty($active_socials)): ?>
                        <div class="flex space-x-4">
                            <?php foreach ($active_socials as $platform => $social): ?>
                                <a href="<?php echo htmlspecialchars($social['url']); ?>" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full flex items-center justify-center transition-colors <?php echo $social['color']; ?>">
                                    <i class="fab <?php echo $social['icon']; ?>"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-6">ลิงก์ด่วน</h3>
                    <ul class="space-y-3">
                        <?php foreach (array_slice($main_menus, 0, 5) as $menu): ?>
                            <li>
                                <a href="<?php echo BASE_URL . '/' . $menu['url']; ?>" class="text-gray-400 hover:text-white transition-colors">
                                    <?php echo htmlspecialchars($menu['title']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-6">ติดต่อเรา</h3>
                    <div class="space-y-3 text-gray-400">
                        <div class="flex items-center">
                            <i class="fas fa-phone mr-3 text-red-primary"></i>
                            <span><?php echo htmlspecialchars($contact_info['phone']); ?></span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-red-primary"></i>
                            <span><?php echo htmlspecialchars($contact_info['email']); ?></span>
                        </div>
                        <div class="flex items-center">
                            <i class="fab fa-line mr-3 text-red-primary"></i>
                            <span><?php echo htmlspecialchars($contact_info['line_id']); ?></span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt mr-3 text-red-primary mt-1"></i>
                            <span><?php echo htmlspecialchars($contact_info['address']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="border-t border-gray-700 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($global_settings['site_name'] ?? 'VIBEDAYBKK'); ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Mobile Menu Toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>

