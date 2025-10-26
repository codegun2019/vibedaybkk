<?php
/**
 * หน้าแสดงรายการโมเดลทั้งหมด - Dark Theme
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

// ดึงข้อมูลจาก settings
$global_settings = [];
$result = db_get_rows($conn, "SELECT * FROM settings");
foreach ($result as $row) {
    $global_settings[$row['setting_key']] = $row['setting_value'];
}

// ดึงข้อมูลเมนู (แบบเดียวกับหน้าแรก)
$main_menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC");

// Debug: ตรวจสอบเมนู (ปิดแล้ว)
// echo "<!-- DEBUG: เมนูที่ดึงมา " . count($main_menus) . " รายการ -->";
// foreach ($main_menus as $menu) {
//     echo "<!-- DEBUG: " . ($menu['name'] ?? 'N/A') . " -> " . ($menu['url'] ?? 'N/A') . " -->";
// }

// รับพารามิเตอร์
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// สร้าง SQL Query
$where = ["1=1"];
$params = [];
$types = '';

if ($category_id > 0) {
    $where[] = "m.category_id = ?";
    $params[] = $category_id;
    $types .= 'i';
}

if (!empty($search)) {
    $where[] = "(m.name LIKE ? OR m.description LIKE ?)";
    $search_param = "%{$search}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ss';
}

$where_sql = implode(' AND ', $where);

// นับจำนวนทั้งหมด
$count_sql = "SELECT COUNT(*) as total FROM models m WHERE {$where_sql}";
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_models = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_models / $per_page);

// ดึงข้อมูลโมเดล
$models_sql = "SELECT m.*, c.name as category_name 
               FROM models m 
               LEFT JOIN categories c ON m.category_id = c.id 
               WHERE {$where_sql}
               ORDER BY m.created_at DESC 
               LIMIT ? OFFSET ?";

$models_stmt = $conn->prepare($models_sql);
$params_with_limit = array_merge($params, [$per_page, $offset]);
$types_with_limit = $types . 'ii';
$models_stmt->bind_param($types_with_limit, ...$params_with_limit);
$models_stmt->execute();
$models_result = $models_stmt->get_result();

// ดึงข้อมูลหมวดหมู่
$categories_result = db_get_rows($conn, "SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order ASC");

// ดึงข้อมูลหมวดหมู่บริการสำหรับ footer
$service_categories = array_slice($categories_result, 0, 4);

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

// Go to Top settings
$go_to_top_enabled = $global_settings['gototop_enabled'] ?? '1';
$go_to_top_icon = $global_settings['gototop_icon'] ?? 'fa-arrow-up';
$go_to_top_bg_color = $global_settings['gototop_bg_color'] ?? 'bg-red-primary';
$go_to_top_text_color = $global_settings['gototop_text_color'] ?? 'text-white';
$go_to_top_position = $global_settings['gototop_position'] ?? 'right';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Basic -->
    <title><?php echo htmlspecialchars($global_settings['seo_title'] ?? $global_settings['site_name'] ?? 'VIBEDAYBKK'); ?> - รายการโมเดล</title>
    <meta name="description" content="<?php echo htmlspecialchars($global_settings['seo_description'] ?? $global_settings['site_description'] ?? 'รายการโมเดลและนางแบบมืออาชีพ'); ?>">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: <?php echo $global_settings['primary_color'] ?? '#DC2626'; ?>;
            --secondary-color: <?php echo $global_settings['secondary_color'] ?? '#1F2937'; ?>;
            --accent-color: <?php echo $global_settings['accent_color'] ?? '#F59E0B'; ?>;
        }
        
        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(135deg, #1F2937 0%, #111827 100%);
            color: #F9FAFB;
        }
        
        .bg-dark { background: #1F2937; }
        .bg-dark-light { background: #374151; }
        .bg-red-primary { background: var(--primary-color); }
        .text-red-primary { color: var(--primary-color); }
        .border-red-primary { border-color: var(--primary-color); }
        
        .mobile-menu {
            background: rgba(31, 41, 55, 0.95);
            backdrop-filter: blur(10px);
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.1) 0%, rgba(31, 41, 55, 0.9) 100%);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="bg-dark/90 backdrop-blur-md fixed w-full top-0 z-50 border-b border-gray-700">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <?php if (!empty($global_settings['logo_image'])): ?>
                        <img src="<?php echo UPLOADS_URL . '/' . htmlspecialchars($global_settings['logo_image']); ?>" 
                             alt="<?php echo htmlspecialchars($global_settings['site_name'] ?? 'VIBEDAYBKK'); ?>"
                             class="h-10 w-auto">
                    <?php else: ?>
                        <h1 class="text-2xl font-bold text-red-primary">
                            <?php echo htmlspecialchars($global_settings['logo_text'] ?? $global_settings['site_name'] ?? 'VIBEDAYBKK'); ?>
                        </h1>
                    <?php endif; ?>
                </div>
                
                <!-- Desktop Menu -->
                <nav class="hidden lg:flex items-center space-x-8">
                    <?php foreach ($main_menus as $menu): ?>
                        <a href="<?php echo h($menu['url'] ?? ''); ?>" 
                           class="text-gray-300 hover:text-red-primary transition-colors duration-300 font-medium">
                            <?php echo h($menu['title'] ?? ''); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
                
                <!-- Mobile Menu Button -->
                <button class="lg:hidden text-gray-300 hover:text-red-primary transition-colors duration-300" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="lg:hidden mobile-menu border-t border-gray-700 hidden">
            <div class="container mx-auto px-4 py-4">
                <?php foreach ($main_menus as $menu): ?>
                    <a href="<?php echo h($menu['url'] ?? ''); ?>" 
                       class="block py-3 text-gray-300 hover:text-red-primary transition-colors duration-300 font-medium border-b border-gray-700 last:border-b-0">
                        <?php echo h($menu['title'] ?? ''); ?>
                    </a>
                <?php endforeach; ?>
                
                <!-- Social Media in Mobile Menu -->
                <?php if (!empty($active_socials)): ?>
                    <div class="mt-6 pt-6 border-t border-gray-700">
                        <h4 class="text-sm font-medium text-gray-400 mb-4">ติดตามเรา</h4>
                        <div class="flex space-x-4">
                            <?php foreach ($active_socials as $platform => $data): ?>
                                <a href="<?php echo htmlspecialchars($data['url']); ?>" 
                                   target="_blank" 
                                   class="<?php echo $data['color']; ?> text-white w-12 h-12 rounded-full flex items-center justify-center transition-all duration-300 hover:scale-110">
                                    <i class="fab <?php echo $data['icon']; ?>"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Contact Info in Mobile Menu -->
                <div class="mt-6 pt-6 border-t border-gray-700">
                    <h4 class="text-sm font-medium text-gray-400 mb-4">ติดต่อเรา</h4>
                    <div class="space-y-2 text-sm text-gray-300">
                        <?php if (!empty($contact_info['phone'])): ?>
                            <p><i class="fas fa-phone mr-2 text-red-primary"></i><?php echo htmlspecialchars($contact_info['phone']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($contact_info['email'])): ?>
                            <p><i class="fas fa-envelope mr-2 text-red-primary"></i><?php echo htmlspecialchars($contact_info['email']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($contact_info['line_id'])): ?>
                            <p><i class="fab fa-line mr-2 text-red-primary"></i><?php echo htmlspecialchars($contact_info['line_id']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-gradient pt-24 pb-16">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-6">
                    <span class="text-white">รายการ</span>
                    <span class="text-red-primary">โมเดล</span>
                </h1>
                <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
                    ค้นพบโมเดลและนางแบบมืออาชีพสำหรับงานของคุณ
                </p>
                
                <!-- Search and Filter -->
                <div class="max-w-4xl mx-auto">
                    <form method="GET" class="flex flex-col md:flex-row gap-4 mb-8">
                        <div class="flex-1">
                            <input type="text" 
                                   name="search" 
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   placeholder="ค้นหาโมเดล..." 
                                   class="w-full px-6 py-4 bg-white/10 border border-gray-600 rounded-lg focus:border-red-primary focus:outline-none text-white placeholder-gray-400">
                        </div>
                        <div class="md:w-64">
                            <select name="category" class="w-full px-6 py-4 bg-white/10 border border-gray-600 rounded-lg focus:border-red-primary focus:outline-none text-white">
                                <option value="">ทุกหมวดหมู่</option>
                                <?php foreach ($categories_result as $category): ?>
                                    <option value="<?php echo h($category['id'] ?? ''); ?>" <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo h($category['name'] ?? ''); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="bg-red-primary hover:bg-red-600 text-white px-8 py-4 rounded-lg font-medium transition-all duration-300">
                            <i class="fas fa-search mr-2"></i>ค้นหา
                        </button>
                    </form>
                </div>
                
                <!-- Results Info -->
                <div class="text-gray-400">
                    <p>พบโมเดล <span class="text-red-primary font-bold"><?php echo number_format($total_models); ?></span> รายการ</p>
                    <?php if ($total_pages > 1): ?>
                        <p>หน้า <?php echo $page; ?> จาก <?php echo $total_pages; ?> หน้า</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Models Grid -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <?php if ($models_result->num_rows > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <?php while ($model = $models_result->fetch_assoc()): ?>
                        <div class="group relative bg-dark-light rounded-2xl overflow-hidden hover:transform hover:scale-105 transition-all duration-300 shadow-xl">
                            <!-- Model Image -->
                            <div class="relative h-80 overflow-hidden bg-gray-800">
                                <?php if (!empty($model['featured_image'])): ?>
                                    <img src="<?php echo h($model['featured_image'] ?? ''); ?>" 
                                         alt="<?php echo h($model['name'] ?? ''); ?>"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-700 to-gray-900">
                                        <div class="w-24 h-24 bg-gray-600 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-4xl text-gray-300"></i>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                
                                <!-- Category Badge -->
                                <?php if (!empty($model['category_name'])): ?>
                                    <div class="absolute top-4 left-4 bg-red-primary px-4 py-1 rounded-full text-sm font-medium">
                                        <?php echo h($model['category_name'] ?? ''); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Status Badge -->
                                <div class="absolute top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                                    <?php echo ($model['status'] ?? '') == 'available' ? 'พร้อมรับงาน' : 'ไม่ว่าง'; ?>
                                </div>
                            </div>
                            
                            <!-- Model Info -->
                            <div class="p-6">
                                <h3 class="text-xl font-bold mb-2 text-white"><?php echo h($model['name'] ?? ''); ?></h3>
                                
                                <?php if (!empty($model['code'])): ?>
                                    <p class="text-sm text-gray-400 mb-3">รหัส: <?php echo h($model['code'] ?? ''); ?></p>
                                <?php endif; ?>
                                
                                <?php 
                                // ตรวจสอบการตั้งค่าการแสดงรายละเอียดส่วนตัว
                                $show_model_details = ($global_settings['show_model_details'] ?? '1') == '1';
                                
                                if ($show_model_details): 
                                ?>
                                <div class="flex items-center justify-between text-sm text-gray-400 mb-4">
                                    <?php if (!empty($model['height'])): ?>
                                        <span><i class="fas fa-ruler-vertical mr-1 text-red-primary"></i><?php echo h($model['height'] ?? ''); ?> cm</span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($model['weight'])): ?>
                                        <span><i class="fas fa-weight mr-1 text-red-primary"></i><?php echo h($model['weight'] ?? ''); ?> kg</span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                
                                <?php 
                                // ตรวจสอบการตั้งค่าการแสดงราคา
                                $show_model_price = ($global_settings['show_model_price'] ?? '1') == '1';
                                
                                if ($show_model_price && !empty($model['price']) && $model['price'] > 0): 
                                ?>
                                    <div class="text-2xl font-bold text-red-primary mb-4">
                                        <?php echo number_format($model['price'] ?? 0); ?> ฿
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="flex justify-center items-center gap-2 mt-12">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $category_id > 0 ? '&category=' . $category_id : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                               class="px-4 py-2 bg-dark-light border border-gray-600 text-white rounded-lg hover:border-red-primary transition">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);
                        
                        for ($i = $start; $i <= $end; $i++):
                        ?>
                            <a href="?page=<?php echo $i; ?><?php echo $category_id > 0 ? '&category=' . $category_id : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                               class="px-4 py-2 rounded-lg transition <?php echo $i == $page ? 'bg-red-primary text-white' : 'bg-dark-light border border-gray-600 text-white hover:border-red-primary'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $category_id > 0 ? '&category=' . $category_id : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                               class="px-4 py-2 bg-dark-light border border-gray-600 text-white rounded-lg hover:border-red-primary transition">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <!-- No Models -->
                <div class="text-center py-16">
                    <div class="w-32 h-32 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-users text-6xl text-gray-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-400 mb-4">ไม่พบโมเดลที่ค้นหา</h3>
                    <p class="text-gray-500 mb-6">ลองปรับเปลี่ยนคำค้นหาหรือหมวดหมู่</p>
                    <a href="models.php" class="inline-block bg-red-primary hover:bg-red-600 text-white px-8 py-3 rounded-full font-medium transition-all duration-300">
                        <i class="fas fa-refresh mr-2"></i>ดูโมเดลทั้งหมด
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

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

    <!-- Go to Top Button -->
    <?php if ($go_to_top_enabled == '1'): ?>
        <button id="goToTop" 
                class="fixed <?php echo $go_to_top_position === 'left' ? 'left-6' : 'right-6'; ?> bottom-6 <?php echo $go_to_top_bg_color; ?> <?php echo $go_to_top_text_color; ?> p-3 rounded-full shadow-lg hover:scale-110 transition-all duration-300 opacity-0 invisible z-50">
            <i class="fas <?php echo $go_to_top_icon; ?>"></i>
        </button>
    <?php endif; ?>

    <!-- Scripts -->
    <script>
        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        }

        // Go to Top Button
        <?php if ($go_to_top_enabled == '1'): ?>
        window.addEventListener('scroll', function() {
            const goToTopBtn = document.getElementById('goToTop');
            if (window.pageYOffset > 300) {
                goToTopBtn.classList.remove('opacity-0', 'invisible');
                goToTopBtn.classList.add('opacity-100', 'visible');
            } else {
                goToTopBtn.classList.add('opacity-0', 'invisible');
                goToTopBtn.classList.remove('opacity-100', 'visible');
            }
        });

        document.getElementById('goToTop').addEventListener('click', function() {
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