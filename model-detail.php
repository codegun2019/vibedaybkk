<?php
/**
 * VIBEDAYBKK - Model Detail Page
 * หน้ารายละเอียดโมเดล
 */
define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

$model_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$model_id) {
    header('Location: index.php');
    exit;
}

// ดึงข้อมูลโมเดล
$stmt = $conn->prepare("
    SELECT 
        m.*,
        c.name as category_name,
        c.gender as category_gender
    FROM models m
    LEFT JOIN categories c ON m.category_id = c.id
    WHERE m.id = ?
");
$stmt->bind_param('i', $model_id);
$stmt->execute();
$result = $stmt->get_result();
$model = $result->fetch_assoc();
$stmt->close();

if (!$model) {
    header('Location: index.php');
    exit;
}

// อัปเดตจำนวนการดู
$conn->query("UPDATE models SET view_count = view_count + 1 WHERE id = $model_id");

// ดึงรูปภาพทั้งหมดของโมเดล
$images_stmt = $conn->prepare("
    SELECT * FROM model_images 
    WHERE model_id = ? 
    ORDER BY is_primary DESC, sort_order ASC
");
$images_stmt->bind_param('i', $model_id);
$images_stmt->execute();
$images_result = $images_stmt->get_result();
$model_images = $images_result->fetch_all(MYSQLI_ASSOC);
$images_stmt->close();

// ดึงโมเดลที่เกี่ยวข้อง
$related_stmt = $conn->prepare("
    SELECT 
        m.*,
        c.name as category_name,
        mi.image_path
    FROM models m
    LEFT JOIN categories c ON m.category_id = c.id
    LEFT JOIN model_images mi ON m.id = mi.model_id AND mi.is_primary = 1
    WHERE m.category_id = ? AND m.id != ? AND m.status = 'available'
    ORDER BY m.featured DESC, m.sort_order ASC
    LIMIT 3
");
$related_stmt->bind_param('ii', $model['category_id'], $model_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();
$related_models = $related_result->fetch_all(MYSQLI_ASSOC);
$related_stmt->close();

// ดึงข้อมูล settings
$global_settings = [];
$settings_result = db_get_rows($conn, "SELECT * FROM settings");
foreach ($settings_result as $row) {
    $global_settings[$row['setting_key']] = $row['setting_value'];
}

// ตั้งค่าการแสดงผล
$show_price = ($global_settings['show_model_price'] ?? '1') == '1';
$show_details = ($global_settings['show_model_details'] ?? '1') == '1';

// ดึงข้อมูลเมนู
$main_menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC");

$page_title = $model['name'] . ' - ' . ($global_settings['site_name'] ?? 'VIBEDAYBKK');

// ดึงข้อมูลโซเชียลมีเดีย
$social_platforms = [
    'facebook' => ['color' => 'bg-blue-600 hover:bg-blue-700', 'hover_color' => 'hover:bg-blue-600'],
    'instagram' => ['color' => 'bg-pink-500 hover:bg-pink-600', 'hover_color' => 'hover:bg-pink-500'],
    'twitter' => ['color' => 'bg-black hover:bg-gray-800', 'hover_color' => 'hover:bg-red-primary'],
    'line' => ['color' => 'bg-green-500 hover:bg-green-600', 'hover_color' => 'hover:bg-green-500'],
    'youtube' => ['color' => 'bg-red-600 hover:bg-red-700', 'hover_color' => 'hover:bg-red-600'],
    'tiktok' => ['color' => 'bg-gray-900 hover:bg-black', 'hover_color' => 'hover:bg-gray-900']
];

$active_socials = [];
foreach ($social_platforms as $platform => $colors) {
    $enabled = $global_settings["social_{$platform}_enabled"] ?? '0';
    if ($enabled == '1') {
        $active_socials[$platform] = [
            'url' => $global_settings["social_{$platform}_url"] ?? '#',
            'icon' => $global_settings["social_{$platform}_icon"] ?? "fa-{$platform}",
            'color' => $colors['color'],
            'hover_color' => $colors['hover_color'],
            'name' => ucfirst($platform)
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($model['description'] ?? ''); ?>">
    <?php echo get_favicon($global_settings); ?>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'kanit': ['Kanit', 'sans-serif'],
                    },
                    colors: {
                        'red-primary': '#DC2626',
                        'red-light': '#EF4444',
                        'dark': '#0a0a0a',
                        'dark-light': '#1a1a1a',
                        'accent': '#FBBF24',
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #0a0a0a;
            color: #ffffff;
        }
        
        .hover-scale {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .hover-scale:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(220, 38, 38, 0.3);
        }
    </style>
</head>
<body class="bg-dark text-white font-kanit">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-dark/95 backdrop-blur-sm z-50 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="index.php" class="text-2xl font-bold text-red-primary">
                        <?php echo get_logo($global_settings); ?>
                    </a>
                </div>
                
                <?php include 'includes/menu.php'; ?>
            </div>
        </div>
        
        <?php include 'includes/mobile-menu.php'; ?>
    </nav>

    <!-- Social Sidebar -->
    <?php if (!empty($active_socials)): ?>
    <div class="fixed right-6 top-1/2 transform -translate-y-1/2 z-40 hidden lg:flex flex-col space-y-3">
        <?php foreach ($active_socials as $platform => $social): ?>
        <a href="<?php echo htmlspecialchars($social['url']); ?>" 
           class="w-12 h-12 flex items-center justify-center <?php echo $social['color']; ?> text-white rounded-full shadow-lg transition-all duration-300 hover:scale-110" 
           title="<?php echo $social['name']; ?>"
           target="_blank"
           rel="noopener noreferrer">
            <i class="fab <?php echo $social['icon']; ?> text-lg"></i>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Model Detail -->
    <div class="min-h-screen py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumb -->
            <nav class="mb-8 text-sm">
                <ol class="flex items-center space-x-2 text-gray-400">
                    <li><a href="index.php" class="hover:text-red-primary">หน้าแรก</a></li>
                    <li><i class="fas fa-chevron-right text-xs"></i></li>
                    <li><a href="index.php#services" class="hover:text-red-primary">โมเดล</a></li>
                    <li><i class="fas fa-chevron-right text-xs"></i></li>
                    <li class="text-white"><?php echo htmlspecialchars($model['name']); ?></li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left Column - Images -->
                <div class="lg:col-span-2">
                    <?php if (!empty($model_images)): ?>
                    <!-- Main Image -->
                    <div class="mb-4 rounded-xl overflow-hidden">
                        <img id="mainImage" 
                             src="<?php echo UPLOADS_URL . '/' . $model_images[0]['image_path']; ?>" 
                             alt="<?php echo htmlspecialchars($model['name']); ?>"
                             class="w-full h-[600px] object-cover">
                    </div>
                    
                    <!-- Thumbnail Gallery -->
                    <?php if (count($model_images) > 1): ?>
                    <div class="grid grid-cols-4 gap-4">
                        <?php foreach ($model_images as $img): ?>
                        <div class="cursor-pointer rounded-lg overflow-hidden hover:opacity-75 transition-opacity"
                             onclick="changeMainImage('<?php echo UPLOADS_URL . '/' . $img['image_path']; ?>')">
                            <img src="<?php echo UPLOADS_URL . '/' . $img['image_path']; ?>" 
                                 alt="<?php echo htmlspecialchars($img['title'] ?? ''); ?>"
                                 class="w-full h-24 object-cover">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <?php else: ?>
                    <div class="bg-gradient-to-br from-gray-700 to-gray-800 rounded-xl h-[600px] flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-user text-9xl text-gray-600 mb-4"></i>
                            <p class="text-gray-400">ยังไม่มีรูปภาพ</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Right Column - Info -->
                <div class="lg:col-span-1">
                    <div class="bg-dark-light rounded-xl p-6 sticky top-24">
                        
                        <!-- Name -->
                        <h1 class="text-3xl font-bold mb-2"><?php echo htmlspecialchars($model['name']); ?></h1>
                        
                        <!-- Category -->
                        <div class="flex items-center text-gray-400 mb-4">
                            <i class="fas fa-briefcase mr-2"></i>
                            <span><?php echo htmlspecialchars($model['category_name']); ?></span>
                        </div>

                        <!-- Stats -->
                        <?php if ($show_details): ?>
                        <div class="grid grid-cols-2 gap-4 mb-6 pb-6 border-b border-gray-700">
                            <?php if ($model['height']): ?>
                            <div>
                                <div class="text-gray-400 text-sm">ส่วนสูง</div>
                                <div class="text-xl font-semibold"><?php echo $model['height']; ?> cm</div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($model['weight']): ?>
                            <div>
                                <div class="text-gray-400 text-sm">น้ำหนัก</div>
                                <div class="text-xl font-semibold"><?php echo $model['weight']; ?> kg</div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($model['age']): ?>
                            <div>
                                <div class="text-gray-400 text-sm">อายุ</div>
                                <div class="text-xl font-semibold"><?php echo $model['age']; ?> ปี</div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($model['experience_years']): ?>
                            <div>
                                <div class="text-gray-400 text-sm">ประสบการณ์</div>
                                <div class="text-xl font-semibold"><?php echo $model['experience_years']; ?> ปี</div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Body Measurements -->
                        <?php if ($model['bust'] || $model['waist'] || $model['hips']): ?>
                        <div class="mb-6 pb-6 border-b border-gray-700">
                            <h3 class="font-semibold mb-3">รอบวัด</h3>
                            <div class="grid grid-cols-3 gap-4">
                                <?php if ($model['bust']): ?>
                                <div>
                                    <div class="text-gray-400 text-sm">อก</div>
                                    <div class="font-semibold"><?php echo $model['bust']; ?>"</div>
                                </div>
                                <?php endif; ?>
                                <?php if ($model['waist']): ?>
                                <div>
                                    <div class="text-gray-400 text-sm">เอว</div>
                                    <div class="font-semibold"><?php echo $model['waist']; ?>"</div>
                                </div>
                                <?php endif; ?>
                                <?php if ($model['hips']): ?>
                                <div>
                                    <div class="text-gray-400 text-sm">สะโพก</div>
                                    <div class="font-semibold"><?php echo $model['hips']; ?>"</div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Price -->
                        <?php if ($show_price && ($model['price_min'] || $model['price_max'])): ?>
                        <div class="mb-6 pb-6 border-b border-gray-700">
                            <h3 class="text-gray-400 text-sm mb-2">ราคา</h3>
                            <div class="text-3xl font-bold text-red-primary">
                                ฿<?php echo number_format($model['price_min'] ?? 0); ?>
                                <?php if ($model['price_max'] && $model['price_max'] != $model['price_min']): ?>
                                - ฿<?php echo number_format($model['price_max']); ?>
                                <?php endif; ?>
                                <span class="text-lg text-gray-400">/วัน</span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Contact Buttons -->
                        <div class="space-y-3">
                            <a href="booking.php?model_id=<?php echo $model['id']; ?>" 
                               class="block w-full bg-red-primary hover:bg-red-light text-white text-center py-3 rounded-lg font-semibold transition-colors">
                                <i class="fas fa-calendar-check mr-2"></i>จองเลย
                            </a>
                            <a href="contact.php?subject=สอบถามเกี่ยวกับ <?php echo urlencode($model['name']); ?>" 
                               class="block w-full bg-gray-700 hover:bg-gray-600 text-white text-center py-3 rounded-lg font-semibold transition-colors">
                                <i class="fas fa-envelope mr-2"></i>สอบถามข้อมูล
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description & Details -->
            <?php if ($model['description']): ?>
            <div class="mt-12">
                <h2 class="text-2xl font-bold mb-6">เกี่ยวกับ<?php echo htmlspecialchars($model['name']); ?></h2>
                <div class="bg-dark-light rounded-xl p-8">
                    <p class="text-gray-300 leading-relaxed whitespace-pre-line"><?php echo htmlspecialchars($model['description']); ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Skills -->
            <?php if ($model['skills']): ?>
            <div class="mt-12">
                <h2 class="text-2xl font-bold mb-6">ทักษะ</h2>
                <div class="bg-dark-light rounded-xl p-8">
                    <div class="flex flex-wrap gap-3">
                        <?php 
                        $skills = explode(',', $model['skills']);
                        foreach ($skills as $skill): 
                        ?>
                        <span class="px-4 py-2 bg-red-primary/20 text-red-primary rounded-full">
                            <?php echo htmlspecialchars(trim($skill)); ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Related Models -->
            <?php if (!empty($related_models)): ?>
            <div class="mt-12">
                <h2 class="text-2xl font-bold mb-6">โมเดลที่เกี่ยวข้อง</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php foreach ($related_models as $related): ?>
                    <a href="model-detail.php?id=<?php echo $related['id']; ?>" class="bg-dark-light rounded-lg overflow-hidden hover-scale group">
                        <div class="relative h-64 overflow-hidden">
                            <?php if ($related['image_path']): ?>
                            <img src="<?php echo UPLOADS_URL . '/' . $related['image_path']; ?>" 
                                 alt="<?php echo htmlspecialchars($related['name']); ?>"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            <?php else: ?>
                            <div class="bg-gradient-to-br from-gray-700 to-gray-800 h-full flex items-center justify-center">
                                <i class="fas fa-user text-5xl text-gray-600"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-6">
                            <h4 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($related['name']); ?></h4>
                            <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($related['category_name']); ?></p>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark-light border-t border-gray-800 py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- About -->
                <div class="md:col-span-2">
                    <h3 class="text-2xl font-bold text-red-primary mb-4">
                        <?php echo get_logo($global_settings); ?>
                    </h3>
                    <p class="text-gray-400 mb-4">
                        <?php echo htmlspecialchars($global_settings['site_description'] ?? 'บริการโมเดลและนางแบบมืออาชีพ'); ?>
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">เมนูด่วน</h4>
                    <ul class="space-y-2">
                        <li><a href="index.php#home" class="text-gray-400 hover:text-red-primary transition-colors">หน้าแรก</a></li>
                        <li><a href="index.php#about" class="text-gray-400 hover:text-red-primary transition-colors">เกี่ยวกับเรา</a></li>
                        <li><a href="index.php#services" class="text-gray-400 hover:text-red-primary transition-colors">บริการ</a></li>
                        <li><a href="index.php#contact" class="text-gray-400 hover:text-red-primary transition-colors">ติดต่อเรา</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">ติดต่อเรา</h4>
                    <ul class="space-y-2 text-gray-400">
                        <?php if (!empty($global_settings['site_phone'])): ?>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-2 text-red-primary"></i>
                            <a href="tel:<?php echo $global_settings['site_phone']; ?>" class="hover:text-red-primary transition-colors">
                                <?php echo $global_settings['site_phone']; ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($global_settings['site_email'])): ?>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2 text-red-primary"></i>
                            <a href="mailto:<?php echo $global_settings['site_email']; ?>" class="hover:text-red-primary transition-colors">
                                <?php echo $global_settings['site_email']; ?>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                    
                    <!-- Social Links -->
                    <?php if (!empty($active_socials)): ?>
                    <div class="flex space-x-3 mt-4">
                        <?php foreach ($active_socials as $platform => $social): ?>
                        <a href="<?php echo htmlspecialchars($social['url']); ?>" 
                           class="w-10 h-10 flex items-center justify-center <?php echo $social['color']; ?> rounded-full transition-all duration-300 hover:scale-110"
                           target="_blank" rel="noopener noreferrer" title="<?php echo $social['name']; ?>">
                            <i class="fab <?php echo $social['icon']; ?>"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="border-t border-gray-800 pt-8 text-center">
                <p class="text-gray-500">
                    &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($global_settings['site_name'] ?? 'VIBEDAYBKK'); ?>. สงวนลิขสิทธิ์
                </p>
            </div>
        </div>
    </footer>

    <!-- Go to Top Button -->
    <?php 
    $gototop_enabled = $global_settings['gototop_enabled'] ?? '1';
    if ($gototop_enabled == '1'):
        $gototop_icon = $global_settings['gototop_icon'] ?? 'fa-arrow-up';
        $gototop_bg_color = $global_settings['gototop_bg_color'] ?? 'bg-red-primary';
        $gototop_text_color = $global_settings['gototop_text_color'] ?? 'text-white';
        $gototop_position = $global_settings['gototop_position'] ?? 'right';
        $position_class = $gototop_position === 'left' ? 'left-6' : 'right-6';
    ?>
    <button id="goToTop" class="fixed bottom-6 <?php echo $position_class; ?> w-12 h-12 <?php echo $gototop_bg_color . ' ' . $gototop_text_color; ?> rounded-full shadow-lg opacity-0 pointer-events-none transition-all duration-300 hover:scale-110 z-50">
        <i class="fas <?php echo $gototop_icon; ?>"></i>
    </button>
    <?php endif; ?>

    <script>
        // Change main image
        function changeMainImage(src) {
            document.getElementById('mainImage').src = src;
        }
        
        // Go to top button
        const goToTopBtn = document.getElementById('goToTop');
        if (goToTopBtn) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 300) {
                    goToTopBtn.classList.remove('opacity-0', 'pointer-events-none');
                    goToTopBtn.classList.add('opacity-100');
                } else {
                    goToTopBtn.classList.add('opacity-0', 'pointer-events-none');
                    goToTopBtn.classList.remove('opacity-100');
                }
            });
            
            goToTopBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    </script>
</body>
</html>


