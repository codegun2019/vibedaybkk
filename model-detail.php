<?php
/**
 * หน้ารายละเอียดโมเดล
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

// ดึงการตั้งค่า
$global_settings = get_all_settings($conn);

// ตรวจสอบว่าเปิดการแสดงรายละเอียดหรือไม่
$detail_enabled = ($global_settings['model_detail_enabled'] ?? '1') == '1';

if (!$detail_enabled) {
    // ถ้าปิดการแสดงรายละเอียด
    set_message('error', 'ขออภัย ฟีเจอร์นี้ปิดการใช้งานชั่วคราว');
    header('Location: models.php');
    exit;
}

// รับ ID หรือ slug
$model_id = 0;
$slug = '';

if (isset($_GET['id'])) {
    $model_id = (int)$_GET['id'];
} elseif (isset($_GET['slug'])) {
    $slug = clean_input($_GET['slug']);
}

// ดึงข้อมูลโมเดล
if ($model_id > 0) {
    $stmt = $conn->prepare("
        SELECT m.*, c.name as category_name, c.icon as category_icon
        FROM models m 
        LEFT JOIN categories c ON m.category_id = c.id 
        WHERE m.id = ?
    ");
    $stmt->bind_param('i', $model_id);
} elseif (!empty($slug)) {
    $stmt = $conn->prepare("
        SELECT m.*, c.name as category_name, c.icon as category_icon
        FROM models m 
        LEFT JOIN categories c ON m.category_id = c.id 
        WHERE m.slug = ?
    ");
    $stmt->bind_param('s', $slug);
} else {
    header('Location: models.php');
    exit;
}

$stmt->execute();
$result = $stmt->get_result();
$model = $result->fetch_assoc();
$stmt->close();

if (!$model) {
    set_message('error', 'ไม่พบข้อมูลโมเดล');
    header('Location: models.php');
    exit;
}

// นับจำนวนการดู
if (isset($model['id'])) {
    $conn->query("UPDATE models SET view_count = view_count + 1 WHERE id = " . (int)$model['id']);
}

// ดึงข้อมูลเมนู
$main_menus = [];
$menu_result = $conn->query("SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC");
if ($menu_result) {
    while ($menu = $menu_result->fetch_assoc()) {
        $main_menus[] = $menu;
    }
}

// ดึงรูปภาพเพิ่มเติม (ถ้ามี)
$model_images = [];
$images_check = $conn->query("SHOW TABLES LIKE 'model_images'");
if ($images_check && $images_check->num_rows > 0) {
    $images_stmt = $conn->prepare("SELECT * FROM model_images WHERE model_id = ? ORDER BY sort_order ASC");
    $images_stmt->bind_param('i', $model['id']);
    $images_stmt->execute();
    $images_result = $images_stmt->get_result();
    while ($img = $images_result->fetch_assoc()) {
        $model_images[] = $img;
    }
}

// ดึงโมเดลที่เกี่ยวข้อง
$related_models = [];
$related_stmt = $conn->prepare("
    SELECT m.*, c.name as category_name 
    FROM models m 
    LEFT JOIN categories c ON m.category_id = c.id 
    WHERE m.category_id = ? AND m.id != ? AND m.status = 'available'
    ORDER BY RAND() 
    LIMIT 4
");
$related_stmt->bind_param('ii', $model['category_id'], $model['id']);
$related_stmt->execute();
$related_result = $related_stmt->get_result();
while ($rel = $related_result->fetch_assoc()) {
    $related_models[] = $rel;
}

// ตรวจสอบฟิลด์ที่แสดงได้ (ตาม settings)
$show_price = ($global_settings['show_model_price'] ?? '1') == '1';
$show_price_range = ($global_settings['model_detail_show_price_range'] ?? '1') == '1';
$show_measurements = ($global_settings['model_detail_show_measurements'] ?? '1') == '1';
$show_personal_info = ($global_settings['show_model_details'] ?? '1') == '1';
$show_experience = ($global_settings['show_model_details'] ?? '1') == '1';
$show_portfolio = ($global_settings['model_detail_show_portfolio'] ?? '1') == '1';
$show_contact = ($global_settings['model_detail_show_contact'] ?? '1') == '1';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($model['name']); ?> - <?php echo $global_settings['site_name'] ?? 'VIBEDAY'; ?></title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo htmlspecialchars($model['description'] ?? $model['name']); ?>">
    <meta name="keywords" content="<?php echo $model['name']; ?>, โมเดล, นางแบบ, <?php echo $model['category_name'] ?? ''; ?>">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Kanit', sans-serif; }
        .bg-dark { background: #1a1a2e; }
        .bg-dark-light { background: #16213e; }
        .text-red-primary { color: #DC2626; }
        .bg-red-primary { background: #DC2626; }
        .hover-scale { transition: transform 0.3s; }
        .hover-scale:hover { transform: scale(1.05); }
        .gallery-img {
            cursor: pointer;
            transition: all 0.3s;
        }
        .gallery-img:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(220, 38, 38, 0.3);
        }
    </style>
</head>
<body class="bg-dark text-white">

    <!-- Navigation -->
    <nav class="bg-dark-light shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-20">
                <a href="/" class="text-3xl font-bold text-red-primary">
                    <?php echo $global_settings['logo_text'] ?? 'VIBEDAY'; ?>
                </a>
                
                <div class="hidden md:flex space-x-8">
                    <?php foreach ($main_menus as $menu): ?>
                        <a href="<?php echo $menu['url']; ?>" class="text-gray-300 hover:text-red-primary transition">
                            <?php if (!empty($menu['icon'])): ?>
                                <i class="fas <?php echo $menu['icon']; ?> mr-2"></i>
                            <?php endif; ?>
                            <?php echo $menu['title']; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Model Detail -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <!-- Breadcrumb -->
            <div class="mb-8 text-sm">
                <a href="/" class="text-gray-400 hover:text-red-primary">หน้าแรก</a>
                <span class="mx-2 text-gray-600">/</span>
                <a href="models.php" class="text-gray-400 hover:text-red-primary">โมเดล</a>
                <span class="mx-2 text-gray-600">/</span>
                <span class="text-red-primary"><?php echo htmlspecialchars($model['name']); ?></span>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Left - Image Gallery -->
                <div>
                    <!-- Main Image -->
                    <div class="mb-6 rounded-2xl overflow-hidden bg-gray-800">
                        <?php if (!empty($model['featured_image'])): ?>
                            <img src="<?php echo htmlspecialchars($model['featured_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($model['name']); ?>"
                                 class="w-full h-[600px] object-cover">
                        <?php else: ?>
                            <div class="w-full h-[600px] flex items-center justify-center bg-gradient-to-br from-gray-700 to-gray-900">
                                <i class="fas fa-user text-9xl text-gray-600"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Additional Images -->
                    <?php if (!empty($model_images)): ?>
                        <div class="grid grid-cols-4 gap-4">
                            <?php foreach ($model_images as $img): ?>
                                <div class="gallery-img rounded-lg overflow-hidden">
                                    <img src="<?php echo BASE_URL . '/' . $img['image_path']; ?>" 
                                         alt="Gallery"
                                         class="w-full h-24 object-cover">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Right - Details -->
                <div>
                    <!-- Header -->
                    <div class="mb-6">
                        <?php if (!empty($model['category_name'])): ?>
                            <span class="inline-block bg-red-primary px-4 py-1 rounded-full text-sm font-medium mb-4">
                                <i class="fas <?php echo $model['category_icon'] ?? 'fa-tag'; ?> mr-2"></i>
                                <?php echo $model['category_name']; ?>
                            </span>
                        <?php endif; ?>
                        
                        <h1 class="text-4xl font-bold mb-2"><?php echo htmlspecialchars($model['name']); ?></h1>
                        
                        <?php if (!empty($model['name_en'])): ?>
                            <p class="text-xl text-gray-400 mb-2"><?php echo htmlspecialchars($model['name_en']); ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($model['code'])): ?>
                            <p class="text-gray-500">รหัส: <span class="font-mono"><?php echo $model['code']; ?></span></p>
                        <?php endif; ?>
                        
                        <!-- Status -->
                        <div class="mt-4">
                            <?php if ($model['status'] == 'available'): ?>
                                <span class="inline-block bg-green-500 text-white px-4 py-2 rounded-full text-sm font-medium">
                                    <i class="fas fa-check-circle mr-2"></i>พร้อมรับงาน
                                </span>
                            <?php else: ?>
                                <span class="inline-block bg-gray-600 text-white px-4 py-2 rounded-full text-sm font-medium">
                                    <i class="fas fa-times-circle mr-2"></i>ไม่ว่าง
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Price -->
                    <?php if ($show_price && !empty($model['price']) && $model['price'] > 0): ?>
                        <div class="bg-dark-light rounded-xl p-6 mb-6">
                            <h3 class="text-lg font-semibold mb-2">
                                <i class="fas fa-dollar-sign mr-2 text-red-primary"></i>ค่าบริการ
                            </h3>
                            <p class="text-4xl font-bold text-red-primary"><?php echo number_format($model['price']); ?> ฿</p>
                            <?php if ($show_price_range && !empty($model['price_min']) && !empty($model['price_max'])): ?>
                                <p class="text-gray-400 text-sm mt-2">
                                    <i class="fas fa-chart-line mr-2"></i>
                                    ช่วงราคา: <?php echo number_format($model['price_min']); ?> - <?php echo number_format($model['price_max']); ?> บาท
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php elseif (!$show_price): ?>
                        <div class="bg-dark-light rounded-xl p-6 mb-6">
                            <h3 class="text-lg font-semibold mb-2">
                                <i class="fas fa-dollar-sign mr-2 text-red-primary"></i>ค่าบริการ
                            </h3>
                            <p class="text-2xl font-medium text-gray-400">
                                <i class="fas fa-phone mr-2"></i>
                                <?php echo $global_settings['price_hidden_text'] ?? 'ติดต่อสอบถาม'; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Measurements (ข้อมูลส่วนตัว) -->
                    <?php if ($show_personal_info && ($show_measurements || !empty($model['height']) || !empty($model['weight']))): ?>
                        <div class="bg-dark-light rounded-xl p-6 mb-6">
                            <h3 class="text-lg font-semibold mb-4">
                                <i class="fas fa-ruler mr-2 text-red-primary"></i>ข้อมูลส่วนตัว
                            </h3>
                            <div class="grid grid-cols-2 gap-4">
                                <?php if (!empty($model['height'])): ?>
                                    <div>
                                        <p class="text-gray-400 text-sm">ส่วนสูง</p>
                                        <p class="text-xl font-semibold"><?php echo $model['height']; ?> cm</p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($model['weight'])): ?>
                                    <div>
                                        <p class="text-gray-400 text-sm">น้ำหนัก</p>
                                        <p class="text-xl font-semibold"><?php echo $model['weight']; ?> kg</p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($model['bust'])): ?>
                                    <div>
                                        <p class="text-gray-400 text-sm">รอบอก</p>
                                        <p class="text-xl font-semibold"><?php echo $model['bust']; ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($model['waist'])): ?>
                                    <div>
                                        <p class="text-gray-400 text-sm">รอบเอว</p>
                                        <p class="text-xl font-semibold"><?php echo $model['waist']; ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($model['hips'])): ?>
                                    <div>
                                        <p class="text-gray-400 text-sm">รอบสะโพก</p>
                                        <p class="text-xl font-semibold"><?php echo $model['hips']; ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($model['age'])): ?>
                                    <div>
                                        <p class="text-gray-400 text-sm">อายุ</p>
                                        <p class="text-xl font-semibold"><?php echo $model['age']; ?> ปี</p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($model['birth_date'])): ?>
                                    <div>
                                        <p class="text-gray-400 text-sm">วันเกิด</p>
                                        <p class="text-xl font-semibold"><?php echo date('d/m/Y', strtotime($model['birth_date'])); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Description -->
                    <?php if (!empty($model['description'])): ?>
                        <div class="bg-dark-light rounded-xl p-6 mb-6">
                            <h3 class="text-lg font-semibold mb-4">
                                <i class="fas fa-info-circle mr-2 text-red-primary"></i>รายละเอียด
                            </h3>
                            <p class="text-gray-300 leading-relaxed"><?php echo nl2br(htmlspecialchars($model['description'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Experience -->
                    <?php if ($show_experience && !empty($model['experience'])): ?>
                        <div class="bg-dark-light rounded-xl p-6 mb-6">
                            <h3 class="text-lg font-semibold mb-4">
                                <i class="fas fa-briefcase mr-2 text-red-primary"></i>ประสบการณ์
                            </h3>
                            <p class="text-gray-300 leading-relaxed"><?php echo nl2br(htmlspecialchars($model['experience'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Portfolio -->
                    <?php if ($show_portfolio && !empty($model['portfolio'])): ?>
                        <div class="bg-dark-light rounded-xl p-6 mb-6">
                            <h3 class="text-lg font-semibold mb-4">
                                <i class="fas fa-star mr-2 text-red-primary"></i>ผลงาน
                            </h3>
                            <p class="text-gray-300 leading-relaxed"><?php echo nl2br(htmlspecialchars($model['portfolio'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Additional Info -->
                    <div class="bg-dark-light rounded-xl p-6 mb-6">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <?php if (!empty($model['skin_tone'])): ?>
                                <div>
                                    <p class="text-gray-400">สีผิว</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($model['skin_tone']); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($model['hair_color'])): ?>
                                <div>
                                    <p class="text-gray-400">สีผม</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($model['hair_color']); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($model['languages'])): ?>
                                <div>
                                    <p class="text-gray-400">ภาษา</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($model['languages']); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($model['view_count'])): ?>
                                <div>
                                    <p class="text-gray-400">ยอดดู</p>
                                    <p class="font-medium"><?php echo number_format($model['view_count']); ?> ครั้ง</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Contact Buttons -->
                    <?php if ($show_contact): ?>
                        <div class="space-y-3">
                            <a href="#contact" class="block w-full bg-red-primary hover:bg-red-700 text-white text-center py-4 rounded-xl font-medium transition text-lg">
                                <i class="fas fa-calendar-check mr-2"></i>จองบริการ
                            </a>
                            
                            <?php if (!empty($global_settings['contact_line'])): ?>
                                <a href="https://line.me/ti/p/<?php echo $global_settings['contact_line']; ?>" 
                                   target="_blank"
                                   class="block w-full bg-green-500 hover:bg-green-600 text-white text-center py-4 rounded-xl font-medium transition">
                                    <i class="fab fa-line mr-2"></i>ติดต่อผ่าน LINE
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Models -->
    <?php if (!empty($related_models)): ?>
    <section class="py-16 bg-dark-light">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold mb-8 text-center">โมเดลที่เกี่ยวข้อง</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($related_models as $rel): ?>
                    <a href="model-detail.php?id=<?php echo $rel['id']; ?>" class="group">
                        <div class="bg-dark rounded-2xl overflow-hidden hover-scale">
                            <div class="relative h-64 overflow-hidden">
                                <?php if (!empty($rel['featured_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($rel['featured_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($rel['name']); ?>"
                                         class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center bg-gray-800">
                                        <i class="fas fa-user text-5xl text-gray-600"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-bold mb-2"><?php echo htmlspecialchars($rel['name']); ?></h3>
                                <?php if (!empty($rel['category_name'])): ?>
                                    <p class="text-sm text-gray-400"><?php echo $rel['category_name']; ?></p>
                                <?php endif; ?>
                                <?php if ($show_price && !empty($rel['price'])): ?>
                                    <p class="text-red-primary font-bold mt-2"><?php echo number_format($rel['price']); ?> ฿</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="bg-dark-light py-12">
        <div class="container mx-auto px-4 text-center">
            <h3 class="text-2xl font-bold mb-4"><?php echo $global_settings['logo_text'] ?? 'VIBEDAY'; ?></h3>
            <p class="text-gray-400">&copy; <?php echo date('Y'); ?> All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
<?php $conn->close(); ?>
