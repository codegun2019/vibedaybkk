<?php
/**
 * VIBEDAYBKK - Service Detail Page
 * หน้ารายละเอียดบริการและโมเดล - ดึงข้อมูลจาก Database
 */
define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

// ดึง settings
$settings = [];
$result = db_get_rows($conn, "SELECT * FROM settings");
foreach ($result as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Get category code from URL
$category_code = clean_input($_GET['category'] ?? '');

if (empty($category_code)) {
    header('Location: services.php');
    exit;
}

// ดึงข้อมูลหมวดหมู่
$stmt = $conn->prepare("SELECT * FROM categories WHERE code = ? AND status = 'active'");
$stmt->execute([$category_code]);
$category = $stmt->fetch();

if (!$category) {
    header('Location: services.php');
    exit;
}

// ดึงโมเดลในหมวดหมู่นี้
$models = get_models_by_category($conn, $category['id'], 'available');

// ดึง requirements
$requirements = db_get_rows($conn, "SELECT * FROM model_requirements WHERE category_id = {$category['id']} ORDER BY sort_order ASC");

// นับจำนวนโมเดล
$model_count = count($models);

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
    <title><?php echo $category['name']; ?> - <?php echo $settings['site_name'] ?? 'VIBEDAYBKK'; ?></title>
    <?php echo get_favicon($settings); ?>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: {'kanit': ['Kanit', 'sans-serif']}, colors: {'dark': '#0a0a0a', 'dark-light': '#1a1a1a', 'red-primary': '#DC2626', 'red-light': '#EF4444', 'accent': '#FBBF24'} } }
        }
    </script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { box-sizing: border-box; overflow-x: hidden; }
        .mobile-menu { transform: translateX(100%); transition: transform 0.3s ease; }
        .mobile-menu.open { transform: translateX(0); }
        .model-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .model-card:hover { transform: translateY(-8px); box-shadow: 0 12px 25px rgba(220, 38, 38, 0.25); }
        .model-image { overflow: hidden; position: relative; }
        .model-image img { transition: transform 0.5s ease; width: 100%; height: 320px; object-fit: cover; }
        .model-card:hover .model-image img { transform: scale(1.1); }
        .model-badge { position: absolute; top: 0.75rem; left: 0.75rem; z-index: 10; }
        .go-to-top { position: fixed; bottom: 30px; right: 30px; width: 50px; height: 50px; border-radius: 50%; z-index: 1000; opacity: 0; visibility: hidden; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); }
        .go-to-top.show { opacity: 1; visibility: visible; }
        .go-to-top:hover { transform: translateY(-5px); box-shadow: 0 6px 12px rgba(220, 38, 38, 0.5); }
        @media (max-width: 768px) { .go-to-top { width: 45px; height: 45px; bottom: 20px; right: 20px; } }
        .nav-link.active { color: #DC2626 !important; font-weight: 600; }
    </style>
</head>
<body class="bg-dark text-white font-kanit">
    <nav class="fixed top-0 w-full bg-dark/95 backdrop-blur-sm z-50 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="index.php" class="text-2xl font-bold text-red-primary"><?php echo get_logo($settings); ?></a>
                </div>
                <?php include 'includes/menu.php'; ?>
            </div>
        </div>
        <?php include 'includes/mobile-menu.php'; ?>
    </nav>

    <button id="go-to-top" class="go-to-top bg-red-primary hover:bg-red-light text-white" title="กลับขึ้นด้านบน">
        <i class="fas fa-arrow-up text-lg"></i>
    </button>

    <section class="pt-24 pb-16 bg-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="services.php" class="inline-flex items-center text-gray-400 hover:text-red-primary transition-colors mb-8">
                <i class="fas fa-arrow-left mr-2"></i> กลับไปหน้าบริการ
            </a>

            <div class="text-center mb-12">
                <div class="inline-block mb-4">
                    <div class="bg-gradient-to-r <?php echo $category['color']; ?> w-20 h-20 rounded-full flex items-center justify-center mx-auto">
                        <i class="fas <?php echo $category['icon']; ?> text-4xl text-white"></i>
                    </div>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold mb-2"><?php echo $category['name']; ?></h1>
                <p class="text-xl text-gray-400 mb-6"><?php echo $category['name_en']; ?></p>
                <p class="text-gray-300 max-w-2xl mx-auto"><?php echo $category['description']; ?></p>
            </div>

            <div class="bg-dark-light rounded-xl p-8 mb-12">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-2xl font-semibold mb-4 text-red-primary"><i class="fas fa-info-circle mr-2"></i>ข้อมูลบริการ</h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <i class="fas fa-tag text-accent mr-3"></i>
                                <span class="text-xl font-semibold">฿<?php echo number_format($category['price_min']); ?>-<?php echo number_format($category['price_max']); ?>/วัน</span>
                            </div>
                            <div class="flex items-center text-gray-400">
                                <i class="fas fa-users mr-3"></i>
                                <span>มีโมเดลให้เลือก <?php echo $model_count; ?> คน</span>
                            </div>
                            <div class="flex items-center text-gray-400">
                                <i class="fas fa-clock mr-3"></i>
                                <span>จองล่วงหน้าอย่างน้อย <?php echo $settings['booking_advance_days'] ?? 3; ?> วัน</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-2xl font-semibold mb-4 text-red-primary"><i class="fas fa-clipboard-check mr-2"></i>คุณสมบัติ</h3>
                        <?php if (!empty($requirements)): ?>
                        <ul class="space-y-2">
                            <?php foreach ($requirements as $req): ?>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                                <span class="text-gray-300"><?php echo $req['requirement']; ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if (empty($models)): ?>
            <div class="text-center py-12 bg-dark-light rounded-xl">
                <i class="fas fa-user-slash fa-3x text-gray-600 mb-4"></i>
                <h3 class="text-2xl font-semibold mb-2">ยังไม่มีโมเดลในหมวดหมู่นี้</h3>
                <p class="text-gray-400 mb-6">กรุณาติดต่อเราเพื่อสอบถามข้อมูลเพิ่มเติม</p>
                <a href="index.php#contact" class="inline-block bg-red-primary hover:bg-red-light px-8 py-3 rounded-lg font-medium transition-all duration-300">
                    <i class="fas fa-phone mr-2"></i>ติดต่อเรา
                </a>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                <?php foreach ($models as $model): 
                    $primary_img = get_primary_image($conn, $model['id']);
                ?>
                <div class="model-card bg-dark-light rounded-lg overflow-hidden shadow-lg">
                    <?php if ($model['featured']): ?>
                    <div class="model-badge"><span class="bg-accent text-dark text-xs font-bold px-2 py-1 rounded-full">แนะนำ</span></div>
                    <?php endif; ?>
                    <div class="model-image">
                        <?php if ($primary_img): ?>
                            <img src="<?php echo UPLOADS_URL . '/' . $primary_img['image_path']; ?>" alt="<?php echo $model['name']; ?>">
                        <?php else: ?>
                            <div class="h-80 bg-gradient-to-br <?php echo $category['color']; ?> flex flex-col items-center justify-center">
                                <i class="fas <?php echo $category['icon']; ?> text-9xl text-white opacity-30 mb-4"></i>
                                <div class="text-center">
                                    <div class="text-5xl font-bold text-white mb-2"><?php echo $model['name']; ?></div>
                                    <div class="text-white opacity-75"><?php echo $model['code']; ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-semibold mb-4"><?php echo $model['name']; ?></h3>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <div class="text-sm text-gray-400 mb-1">รหัส</div>
                                <div class="font-semibold"><?php echo $model['code']; ?></div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-400 mb-1">ส่วนสูง</div>
                                <div class="font-semibold"><?php echo $model['height'] ? $model['height'] . ' cm' : '-'; ?></div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-400 mb-1">ประสบการณ์</div>
                                <div class="font-semibold"><?php echo $model['experience_years'] ? $model['experience_years'] . ' ปี' : '-'; ?></div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-400 mb-1">สถานะ</div>
                                <div class="text-green-500 font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i><?php echo $model['status'] == 'available' ? 'ว่าง' : 'ไม่ว่าง'; ?>
                                </div>
                            </div>
                        </div>
                        <?php if ($model['price_min'] && $model['price_max']): ?>
                        <div class="mb-4 text-center">
                            <span class="text-accent text-lg font-bold">฿<?php echo number_format($model['price_min']); ?>-<?php echo number_format($model['price_max']); ?></span>
                            <span class="text-gray-400">/วัน</span>
                        </div>
                        <?php endif; ?>
                        <a href="index.php#contact" class="block w-full bg-red-primary hover:bg-red-light text-center py-3 rounded-lg font-medium transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="fas fa-calendar-check mr-2"></i>จองเลย
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="bg-gradient-to-r from-red-primary to-red-light rounded-2xl p-8 md:p-12 text-center">
                <h3 class="text-2xl md:text-3xl font-bold mb-4">สนใจจองโมเดลหรือไม่?</h3>
                <p class="text-lg mb-6 opacity-90">ติดต่อเราเพื่อขอข้อมูลเพิ่มเติมและจองโมเดลที่คุณต้องการ</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="index.php#contact" class="bg-white text-red-primary hover:bg-gray-100 px-8 py-3 rounded-lg font-medium transition-all duration-300 shadow-lg">
                        <i class="fas fa-phone mr-2"></i>ติดต่อเราทันที
                    </a>
                    <a href="https://line.me/ti/p/<?php echo str_replace('@', '', $settings['site_line'] ?? '@vibedaybkk'); ?>" target="_blank" class="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-lg font-medium transition-all duration-300 shadow-lg">
                        <i class="fab fa-line mr-2"></i>แชท Line
                    </a>
                </div>
            </div>
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
                        <?php
                        $footer_cats = db_get_rows($conn, "SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order LIMIT 4");
                        foreach ($footer_cats as $cat):
                        ?>
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
    </script>
</body>
</html>



