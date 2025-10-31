<?php
/**
 * VIBEDAYBKK - Homepage
 * หน้าแรกของเว็บไซต์
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

// ดึงข้อมูล categories สำหรับ Services Section
$categories = db_get_rows($conn, "SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order ASC");

// ดึงข้อมูล reviews
$reviews = db_get_rows($conn, "
    SELECT 
        id,
        customer_name,
        rating,
        image,
        COALESCE(content, '') AS content
    FROM customer_reviews 
    WHERE is_active = 1 
    ORDER BY sort_order ASC 
    LIMIT 6
");

// ดึงข้อมูลบทความ
$articles = db_get_rows($conn, "
    SELECT a.*, ac.name as category_name, ac.color as category_color
    FROM articles a
    LEFT JOIN article_categories ac ON a.category_id = ac.id
    WHERE a.status = 'published'
    ORDER BY a.published_at DESC
    LIMIT 6
");

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

// ดึงข้อมูล Hero Section จากฐานข้อมูล
require_once 'includes/homepage-functions.php';
$hero_section = get_homepage_section('hero');
if (!$hero_section) {
    // ถ้าไม่มีข้อมูล ให้ใช้ค่า default
    $hero_section = [
        'title' => 'lollipop24hours',
        'subtitle' => 'บริการโมเดลและนางแบบมืออาชีพ',
        'content' => 'เราคือผู้เชี่ยวชาญด้านบริการโมเดลและนางแบบคุณภาพสูง พร้อมให้บริการสำหรับงานถ่ายภาพ งานแฟชั่น และงานอีเวนต์ต่างๆ ด้วยทีมงานมืออาชีพและโมเดลที่ผ่านการคัดสรรอย่างดี',
        'button1_text' => 'จองบริการตอนนี้',
        'button1_link' => '#contact',
        'button2_text' => 'ดูผลงาน',
        'button2_link' => '#services',
        'background_type' => 'color',
        'background_color' => '',
        'background_image' => ''
    ];
}

// ดึงข้อมูล About Section จากฐานข้อมูล
$about_section = get_homepage_section('about');
if (!$about_section) {
    // ถ้าไม่มีข้อมูล ให้ใช้ค่า default
    $about_section = [
        'title' => 'เกี่ยวกับ lollipop24hours',
        'subtitle' => '',
        'content' => 'VIBEDAYBKK เป็นบริษัทชั้นนำด้านบริการโมเดลและนางแบบในกรุงเทพฯ เราให้บริการครบวงจรตั้งแต่การคัดสรรโมเดล การจัดการงาน ไปจนถึงการประสานงานในวันถ่ายทำ',
        'left_image' => '',
        'background_type' => 'color',
        'background_color' => '#1a1a1a',
        'background_image' => ''
    ];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Basic -->
    <title><?php echo htmlspecialchars($global_settings['seo_title'] ?? $global_settings['site_name'] ?? 'lollipop24hours'); ?> - บริการโมเดลและนางแบบมืออาชีพ</title>
    <meta name="description" content="<?php echo htmlspecialchars($global_settings['seo_description'] ?? $global_settings['site_description'] ?? 'บริการโมเดลและนางแบบมืออาชีพ'); ?>">
    <?php if (!empty($global_settings['seo_keywords'])): ?>
    <meta name="keywords" content="<?php echo htmlspecialchars($global_settings['seo_keywords']); ?>">
    <?php endif; ?>
    <?php if (!empty($global_settings['seo_author'])): ?>
    <meta name="author" content="<?php echo htmlspecialchars($global_settings['seo_author']); ?>">
    <?php endif; ?>
    <?php if (!empty($global_settings['seo_canonical_url'])): ?>
    <link rel="canonical" href="<?php echo htmlspecialchars($global_settings['seo_canonical_url']); ?>">
    <?php endif; ?>
    
    <!-- Robots -->
    <meta name="robots" content="<?php echo (($global_settings['robots_index'] ?? '1') == '1' ? 'index' : 'noindex') . ',' . (($global_settings['robots_follow'] ?? '1') == '1' ? 'follow' : 'nofollow'); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?php echo htmlspecialchars($global_settings['og_type'] ?? 'website'); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($global_settings['seo_canonical_url'] ?? SITE_URL); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($global_settings['og_title'] ?? $global_settings['seo_title'] ?? $global_settings['site_name'] ?? 'lollipop24hours'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($global_settings['og_description'] ?? $global_settings['seo_description'] ?? ''); ?>">
    <?php if (!empty($global_settings['og_image'])): ?>
    <meta property="og:image" content="<?php echo htmlspecialchars($global_settings['og_image']); ?>">
    <?php endif; ?>
    <meta property="og:locale" content="<?php echo htmlspecialchars($global_settings['og_locale'] ?? 'th_TH'); ?>">
    <?php if (!empty($global_settings['og_site_name'])): ?>
    <meta property="og:site_name" content="<?php echo htmlspecialchars($global_settings['og_site_name']); ?>">
    <?php endif; ?>
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="<?php echo htmlspecialchars($global_settings['twitter_card'] ?? 'summary_large_image'); ?>">
    <?php if (!empty($global_settings['twitter_site'])): ?>
    <meta name="twitter:site" content="<?php echo htmlspecialchars($global_settings['twitter_site']); ?>">
    <?php endif; ?>
    <?php if (!empty($global_settings['twitter_creator'])): ?>
    <meta name="twitter:creator" content="<?php echo htmlspecialchars($global_settings['twitter_creator']); ?>">
    <?php endif; ?>
    <meta name="twitter:title" content="<?php echo htmlspecialchars($global_settings['twitter_title'] ?? $global_settings['seo_title'] ?? ''); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($global_settings['twitter_description'] ?? $global_settings['seo_description'] ?? ''); ?>">
    <?php if (!empty($global_settings['twitter_image'])): ?>
    <meta name="twitter:image" content="<?php echo htmlspecialchars($global_settings['twitter_image']); ?>">
    <?php endif; ?>
    
    <!-- Mobile Meta Tags -->
    <meta name="theme-color" content="<?php echo htmlspecialchars($global_settings['meta_theme_color'] ?? '#DC2626'); ?>">
    <?php if (($global_settings['meta_apple_mobile_capable'] ?? '1') == '1'): ?>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="<?php echo htmlspecialchars($global_settings['meta_apple_status_bar_style'] ?? 'black-translucent'); ?>">
    <?php endif; ?>
    
    <!-- Favicon: ใช้ไฟล์เดียวกับโลโก้ ถ้ามี -->
    <?php 
    $logo_image_for_favicon = $global_settings['logo_image'] ?? '';
    if (!empty($logo_image_for_favicon)):
    ?>
    <link rel="icon" type="image/png" href="<?php echo UPLOADS_URL . '/' . htmlspecialchars($logo_image_for_favicon); ?>">
    <link rel="apple-touch-icon" href="<?php echo UPLOADS_URL . '/' . htmlspecialchars($logo_image_for_favicon); ?>">
    <?php elseif (!empty($global_settings['favicon'])): ?>
    <link rel="icon" type="image/x-icon" href="<?php echo UPLOADS_URL . '/' . htmlspecialchars($global_settings['favicon']); ?>">
    <?php endif; ?>
    
    <!-- Google Analytics -->
    <?php if (($global_settings['google_analytics_enabled'] ?? '0') == '1' && !empty($global_settings['google_analytics_id'])): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($global_settings['google_analytics_id']); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo htmlspecialchars($global_settings['google_analytics_id']); ?>');
    </script>
    <?php endif; ?>
    
    <!-- Google Tag Manager -->
    <?php if (!empty($global_settings['google_tag_manager_id'])): ?>
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?php echo htmlspecialchars($global_settings['google_tag_manager_id']); ?>');</script>
    <?php endif; ?>
    
    <!-- Google Site Verification -->
    <?php if (($global_settings['google_search_console_enabled'] ?? '0') == '1' && !empty($global_settings['google_site_verification'])): ?>
    <meta name="google-site-verification" content="<?php echo htmlspecialchars($global_settings['google_site_verification']); ?>">
    <?php endif; ?>
    
    <!-- Facebook Pixel -->
    <?php if (($global_settings['facebook_pixel_enabled'] ?? '0') == '1' && !empty($global_settings['facebook_pixel_id'])): ?>
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?php echo htmlspecialchars($global_settings['facebook_pixel_id']); ?>');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?php echo htmlspecialchars($global_settings['facebook_pixel_id']); ?>&ev=PageView&noscript=1"/></noscript>
    <?php endif; ?>
    
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
        
        /* Preloader Styles */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0a0a0a 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        
        #preloader.hidden {
            opacity: 0;
            visibility: hidden;
        }
        
        .loader-content {
            text-align: center;
        }
        
        .loader-logo {
            font-size: 3rem;
            font-weight: bold;
            color: #DC2626;
            margin-bottom: 2rem;
            animation: pulse 1.5s ease-in-out infinite;
        }
        
        .loader-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid #1a1a1a;
            border-top: 4px solid #DC2626;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        .loader-text {
            color: #DC2626;
            margin-top: 1.5rem;
            font-size: 1.2rem;
            animation: fadeInOut 1.5s ease-in-out infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        
        @keyframes fadeInOut {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
        }
        
        .carousel-container {
            overflow: hidden;
            position: relative;
        }
        
        .carousel-track {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }
        
        .carousel-slide {
            min-width: 100%;
            flex-shrink: 0;
        }
        
        @media (min-width: 768px) {
            .carousel-slide {
                min-width: 33.333%;
            }
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
        
        .animate-fade-in {
            animation: fadeIn 1s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
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
        
        button, .btn {
            cursor: pointer;
            user-select: none;
            outline: none;
        }
        
        button:active {
            transform: scale(0.98);
        }
        
        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .social-sidebar {
                right: 15px;
            }
            
            .social-sidebar a {
                width: 45px;
                height: 45px;
            }
            
            .loader-logo {
                font-size: 2.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .social-sidebar {
                display: none;
            }
            
            .go-to-top {
                width: 45px;
                height: 45px;
                bottom: 20px;
                right: 20px;
            }
            
            .loader-logo {
                font-size: 2rem;
            }
            
            .loader-text {
                font-size: 1rem;
            }
            
            #home h1 {
                font-size: 2.5rem;
            }
            
            #home h2 {
                font-size: 1.5rem;
            }
            
            .mobile-menu {
                backdrop-filter: blur(10px);
                background-color: rgba(26, 26, 26, 0.98);
            }
        }
        
        @media (max-width: 480px) {
            .loader-logo {
                font-size: 1.75rem;
            }
            
            #home h1 {
                font-size: 2rem;
            }
            
            #home h2 {
                font-size: 1.25rem;
            }
        }
        
        a, button {
            -webkit-tap-highlight-color: transparent;
        }
        
        .card-shadow {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .card-shadow:hover {
            box-shadow: 0 10px 20px rgba(220, 38, 38, 0.2);
        }

        .nav-link.active {
            color: #DC2626 !important;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-dark text-white font-noto">
    <!-- Preloader -->
    <div id="preloader">
        <div class="loader-content">
            <div class="loader-logo">
                <?php 
                $preloader_logo_type = $global_settings['logo_type'] ?? 'text';
                $preloader_logo_text = $global_settings['logo_text'] ?? 'lollipop24hours';
                $preloader_logo_image = $global_settings['logo_image'] ?? '';
                
                if ($preloader_logo_type === 'image' && !empty($preloader_logo_image)): 
                ?>
                    <img src="<?php echo UPLOADS_URL . '/' . $preloader_logo_image; ?>" alt="<?php echo htmlspecialchars($preloader_logo_text); ?>" style="max-height: 80px; object-fit: contain;">
                <?php else: ?>
                    <i class="fas fa-star"></i> <?php echo htmlspecialchars($preloader_logo_text); ?>
                <?php endif; ?>
            </div>
            <div class="loader-spinner"></div>
            <div class="loader-text">กำลังโหลด...</div>
        </div>
    </div>

    <!-- Navigation -->
    <?php include 'includes/navigation.php'; ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="<?php echo BASE_URL; ?>" class="text-2xl font-bold text-red-primary flex items-center">
                        <?php 
                        $logo_type = $global_settings['logo_type'] ?? 'text';
                        $logo_text = $global_settings['logo_text'] ?? 'lollipop24hours';
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
    <section id="home" class="hero-gradient min-h-screen flex items-center pt-16"
             <?php if (($hero_section['background_type'] ?? 'color') === 'image' && !empty($hero_section['background_image'])): ?>
             style="background-image: url('<?php echo UPLOADS_URL . '/' . $hero_section['background_image']; ?>'); 
                    background-position: <?php echo $hero_section['background_position'] ?? 'center'; ?>; 
                    background-size: <?php echo $hero_section['background_size'] ?? 'cover'; ?>; 
                    background-repeat: <?php echo $hero_section['background_repeat'] ?? 'no-repeat'; ?>; 
                    background-attachment: <?php echo $hero_section['background_attachment'] ?? 'scroll'; ?>;"
             <?php elseif (!empty($hero_section['background_color'])): ?>
             style="background-color: <?php echo $hero_section['background_color']; ?>;"
             <?php endif; ?>>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <?php if (($hero_section['background_type'] ?? 'color') !== 'image'): ?>
                <div class="animate-fade-in">
                    <h1 class="text-4xl md:text-6xl font-bold mb-6">
                        <?php 
                        $title = $hero_section['title'] ?? 'lollipop24hours';
                        // แยก VIBEDAYBKK เป็น VIBE DAY BKK
                        if ($title === 'VIBEDAYBKK') {
                            echo '<span class="text-red-primary">VIBE</span>DAY<span class="text-red-primary">BKK</span>';
                        } else {
                            echo htmlspecialchars($title);
                        }
                        ?>
                    </h1>
                    <?php if (!empty($hero_section['subtitle'])): ?>
                    <h2 class="text-2xl md:text-3xl font-medium mb-6 text-gray-300">
                        <?php echo htmlspecialchars($hero_section['subtitle']); ?>
                    </h2>
                    <?php endif; ?>
                    <?php if (!empty($hero_section['content'])): ?>
                    <p class="text-lg mb-8 text-gray-400 leading-relaxed">
                        <?php echo nl2br(htmlspecialchars($hero_section['content'])); ?>
                    </p>
                    <?php endif; ?>
                    <?php if (!empty($hero_section['button1_text']) || !empty($hero_section['button2_text'])): ?>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <?php if (!empty($hero_section['button1_text'])): ?>
                        <a href="<?php echo htmlspecialchars($hero_section['button1_link'] ?? '#'); ?>" 
                           class="bg-red-primary hover:bg-red-light px-8 py-3 rounded-lg font-medium transition-all duration-300 hover-scale text-center shadow-lg">
                            <i class="fas fa-calendar-check mr-2"></i><?php echo htmlspecialchars($hero_section['button1_text']); ?>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($hero_section['button2_text'])): ?>
                        <a href="<?php echo htmlspecialchars($hero_section['button2_link'] ?? '#'); ?>" 
                           class="border-2 border-red-primary text-red-primary hover:bg-red-primary hover:text-white px-8 py-3 rounded-lg font-medium transition-all duration-300 text-center shadow-lg">
                            <i class="fas fa-images mr-2"></i><?php echo htmlspecialchars($hero_section['button2_text']); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Right Content - Models Images -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="space-y-4">
                        <div class="bg-gradient-to-br from-red-primary to-red-light h-64 rounded-lg flex items-center justify-center hover-scale">
                            <i class="fas fa-user text-4xl text-white"></i>
                        </div>
                    </div>
                    <div class="space-y-4 mt-8">
                        <div class="bg-gradient-to-br from-gray-700 to-gray-800 h-72 rounded-lg flex items-center justify-center hover-scale">
                            <i class="fas fa-user text-4xl text-white"></i>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-gradient-to-br from-red-light to-red-primary h-64 rounded-lg flex items-center justify-center hover-scale">
                            <i class="fas fa-user text-4xl text-white"></i>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20"
             <?php if (($about_section['background_type'] ?? 'color') === 'image' && !empty($about_section['background_image'])): ?>
             style="background-image: url('<?php echo UPLOADS_URL . '/' . $about_section['background_image']; ?>'); 
                    background-position: <?php echo $about_section['background_position'] ?? 'center'; ?>; 
                    background-size: <?php echo $about_section['background_size'] ?? 'cover'; ?>; 
                    background-repeat: <?php echo $about_section['background_repeat'] ?? 'no-repeat'; ?>; 
                    background-attachment: <?php echo $about_section['background_attachment'] ?? 'scroll'; ?>;"
             <?php elseif (!empty($about_section['background_color'])): ?>
             style="background-color: <?php echo $about_section['background_color']; ?>;"
             <?php else: ?>
             class="bg-dark-light"
             <?php endif; ?>>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php if (($about_section['background_type'] ?? 'color') !== 'image'): ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left - Image -->
                <div class="flex justify-center">
                    <?php if (!empty($about_section['left_image'])): ?>
                    <div class="w-full max-w-md rounded-3xl overflow-hidden shadow-2xl hover-scale">
                        <img src="<?php echo UPLOADS_URL . '/' . $about_section['left_image']; ?>" 
                             alt="<?php echo htmlspecialchars($about_section['title'] ?? 'About'); ?>"
                             class="w-full h-auto object-cover">
                    </div>
                    <?php else: ?>
                    <div class="bg-gradient-to-br from-gray-800 to-gray-900 w-64 h-96 rounded-3xl flex items-center justify-center shadow-2xl hover-scale">
                        <div class="text-center">
                            <i class="fas fa-mobile-alt text-6xl text-red-primary mb-4"></i>
                            <p class="text-gray-400">lollipop24hours App</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Right - Content -->
                <div>
                    <?php if (!empty($about_section['title'])): ?>
                    <h2 class="text-3xl md:text-4xl font-bold mb-6">
                        <?php 
                        $about_title = $about_section['title'];
                        // ชื่อแบรนด์
                        if (strpos($about_title, 'lollipop24hours') !== false) {
                            echo htmlspecialchars($about_title);
                        } else {
                            echo htmlspecialchars($about_title);
                        }
                        ?>
                    </h2>
                    <?php endif; ?>
                    
                    <?php if (!empty($about_section['subtitle'])): ?>
                    <h3 class="text-xl md:text-2xl font-medium mb-6 text-gray-300">
                        <?php echo htmlspecialchars($about_section['subtitle']); ?>
                    </h3>
                    <?php endif; ?>
                    
                    <?php if (!empty($about_section['content'])): ?>
                    <div class="text-gray-400 mb-6 leading-relaxed">
                        <?php echo nl2br(htmlspecialchars($about_section['content'])); ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- ================================
                         SECTION: ABOUT - FEATURES LIST
                         ดึงรายการคุณสมบัติจากฐานข้อมูล
                         จัดการที่: admin/homepage/edit.php?id=2
                         ================================ -->
                    <?php 
                    // ดึงรายการคุณสมบัติจากฐานข้อมูล (features column)
                    $about_features = [];
                    if (!empty($about_section['features'])) {
                        $about_features = json_decode($about_section['features'], true);
                    }
                    
                    // ถ้าไม่มีข้อมูลในฐานข้อมูล ให้ใช้ default
                    if (empty($about_features)) {
                        $about_features = [
                            ['text' => 'โมเดลมืออาชีพที่ผ่านการคัดสรร', 'icon' => 'fa-check-circle'],
                            ['text' => 'บริการครบวงจรในราคาที่เหมาะสม', 'icon' => 'fa-check-circle'],
                            ['text' => 'ทีมงานมืออาชีพพร้อมให้คำปรึกษา', 'icon' => 'fa-check-circle'],
                            ['text' => 'รองรับงานทุกประเภทและขนาด', 'icon' => 'fa-check-circle']
                        ];
                    }
                    ?>
                    <div class="space-y-4">
                        <?php foreach ($about_features as $feature): ?>
                        <div class="flex items-center">
                            <i class="fas <?php echo htmlspecialchars($feature['icon']); ?> text-red-primary mr-3"></i>
                            <span><?php echo htmlspecialchars($feature['text']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (!empty($about_section['button1_text'])): ?>
                    <div class="mt-8">
                        <a href="<?php echo htmlspecialchars($about_section['button1_link'] ?? '#'); ?>" 
                           class="inline-block bg-red-primary hover:bg-red-light px-8 py-3 rounded-lg font-medium transition-all duration-300 hover-scale shadow-lg">
                            <?php echo htmlspecialchars($about_section['button1_text']); ?>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Services Section -->
    <?php
    // ตรวจสอบว่าต้องแสดง section โมเดลหรือไม่
    $homepage_models_enabled = ($global_settings['homepage_models_enabled'] ?? '1') == '1';
    ?>
    <?php if ($homepage_models_enabled): ?>
    <section id="services" class="py-20 bg-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php
            // ดึงการตั้งค่าสำหรับ section โมเดล
            $services_models_title = $global_settings['homepage_models_title'] ?? 'โมเดลของเรา';
            $services_models_subtitle = $global_settings['homepage_models_subtitle'] ?? 'โมเดลและนางแบบมืออาชีพ คัดสรรคุณภาพ';
            $services_models_limit = (int)($global_settings['homepage_models_limit'] ?? 12);
            $services_models_category = (int)($global_settings['homepage_models_category'] ?? 0);
            $services_models_sort = $global_settings['homepage_models_sort'] ?? 'latest';
            
            // สร้าง WHERE clause
            $services_where = "WHERE m.status = 'available'";
            if ($services_models_category > 0) {
                $services_where .= " AND m.category_id = " . $services_models_category;
            }
            
            // สร้าง ORDER clause
            $services_order = "ORDER BY m.created_at DESC";
            switch ($services_models_sort) {
                case 'random':
                    $services_order = "ORDER BY RAND()";
                    break;
                case 'popular':
                    $services_order = "ORDER BY m.view_count DESC";
                    break;
                case 'price_low':
                    $services_order = "ORDER BY m.price ASC";
                    break;
                case 'price_high':
                    $services_order = "ORDER BY m.price DESC";
                    break;
            }
            
            // ดึงข้อมูลโมเดล
            $services_models_query = "
                SELECT m.*, c.name as category_name 
                FROM models m 
                LEFT JOIN categories c ON m.category_id = c.id 
                {$services_where}
                {$services_order}
                LIMIT {$services_models_limit}
            ";
            $services_models_result = $conn->query($services_models_query);
            ?>
            
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">
                    <?php echo htmlspecialchars($services_models_title); ?>
                </h2>
                <p class="text-gray-400 text-lg"><?php echo htmlspecialchars($services_models_subtitle); ?></p>
            </div>
            
            <?php if ($services_models_result && $services_models_result->num_rows > 0): ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <?php while ($model = $services_models_result->fetch_assoc()): ?>
                <div class="group relative bg-dark-light rounded-2xl overflow-hidden hover:transform hover:scale-105 transition-all duration-300 shadow-xl">
                    <!-- Model Image -->
                    <div class="relative h-80 overflow-hidden bg-gray-800">
                        <?php if (!empty($model['featured_image'])): ?>
                            <img src="<?php echo htmlspecialchars($model['featured_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($model['name']); ?>"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-700 to-gray-900">
                                <i class="fas fa-user text-6xl text-gray-600"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <!-- Category Badge -->
                        <?php if (!empty($model['category_name'])): ?>
                            <div class="absolute top-4 left-4 bg-red-primary px-4 py-1 rounded-full text-sm font-medium">
                                <?php echo $model['category_name']; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Status Badge -->
                        <div class="absolute top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                            <?php echo $model['status'] == 'available' ? 'พร้อมรับงาน' : 'ไม่ว่าง'; ?>
                        </div>
                    </div>
                    
                    <!-- Model Info -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($model['name']); ?></h3>
                        
                        <?php if (!empty($model['code'])): ?>
                            <p class="text-sm text-gray-500 mb-3">รหัส: <?php echo $model['code']; ?></p>
                        <?php endif; ?>
                        
                        <?php 
                        // ตรวจสอบการตั้งค่าการแสดงรายละเอียดส่วนตัว
                        $show_model_details = ($global_settings['show_model_details'] ?? '1') == '1';
                        
                        if ($show_model_details): 
                        ?>
                        <div class="flex items-center justify-between text-sm text-gray-400 mb-4">
                            <?php if (!empty($model['height'])): ?>
                                <span><i class="fas fa-ruler-vertical mr-1 text-red-primary"></i><?php echo $model['height']; ?> cm</span>
                            <?php endif; ?>
                            
                            <?php if (!empty($model['weight'])): ?>
                                <span><i class="fas fa-weight mr-1 text-red-primary"></i><?php echo $model['weight']; ?> kg</span>
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
            
            <!-- View All Button -->
            <div class="text-center mt-12">
                <a href="models.php" class="inline-block bg-red-primary hover:bg-red-light text-white px-8 py-4 rounded-full font-medium text-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-users mr-2"></i>ดูโมเดลทั้งหมด
                </a>
            </div>
            
            <?php else: ?>
            
            <!-- No Models -->
            <div class="text-center py-16">
                <i class="fas fa-users text-6xl text-gray-700 mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-400 mb-4">ยังไม่มีข้อมูลโมเดล</h3>
                <p class="text-gray-500 mb-6">กรุณาเพิ่มข้อมูลโมเดลในระบบ</p>
                <a href="seed-models.php" class="inline-block bg-red-primary hover:bg-red-light text-white px-8 py-3 rounded-full font-medium transition-all duration-300">
                    <i class="fas fa-magic mr-2"></i>สร้างโมเดลตัวอย่าง 100 รายการ
                </a>
            </div>
            
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- How to Book Section -->
    <section id="how-to-book" class="py-20 bg-dark-light">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left - Steps -->
                <div>
                    <?php 
                    // ดึงข้อมูล How to Book Section จากฐานข้อมูล
                    $how_to_book_section = get_homepage_section('how-to-book');
                    if (!$how_to_book_section) {
                        // ถ้าไม่มีข้อมูล ให้ใช้ค่า default
                        $how_to_book_section = [
                            'title' => 'วิธีการจองบริการ',
                            'subtitle' => '',
                            'content' => '',
                            'button1_text' => '',
                            'button1_link' => '',
                            'background_type' => 'color',
                            'background_color' => '',
                            'background_image' => '',
                            'right_image' => ''
                        ];
                    }
                    ?>
                    <h2 class="text-3xl md:text-4xl font-bold mb-8">
                        <?php 
                        $title = $how_to_book_section['title'] ?? 'วิธีการจองบริการ';
                        // ถ้ามี "บริการ" ในชื่อ ให้เปลี่ยนสีแดง
                        if (strpos($title, 'บริการ') !== false) {
                            echo str_replace('บริการ', '<span class="text-red-primary">บริการ</span>', htmlspecialchars($title));
                        } else {
                            echo htmlspecialchars($title);
                        }
                        ?>
                    </h2>
                    <?php if (!empty($how_to_book_section['content'])): ?>
                    <div class="text-gray-400 mb-6 leading-relaxed">
                        <?php echo nl2br(htmlspecialchars($how_to_book_section['content'])); ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php
                    // ดึงข้อมูล steps จากฐานข้อมูล
                    $steps = [];
                    if (!empty($how_to_book_section['steps'])) {
                        $steps = json_decode($how_to_book_section['steps'], true);
                    }
                    
                    // ถ้าไม่มี steps ในฐานข้อมูล ให้ใช้ default
                    if (empty($steps)) {
                        $steps = [
                            [
                                'title' => 'เลือกบริการ',
                                'description' => 'เลือกประเภทโมเดลและบริการที่ต้องการ'
                            ],
                            [
                                'title' => 'ติดต่อเรา',
                                'description' => 'ติดต่อผ่าน Line หรือโทรศัพท์เพื่อปรึกษารายละเอียด'
                            ],
                            [
                                'title' => 'ยืนยันการจอง',
                                'description' => 'ยืนยันรายละเอียดและชำระเงินมัดจำ'
                            ],
                            [
                                'title' => 'เริ่มงาน',
                                'description' => 'โมเดลจะมาถึงสถานที่ตามเวลาที่กำหนด'
                            ]
                        ];
                    }
                    ?>
                    
                    <div class="space-y-6">
                        <?php foreach ($steps as $index => $step): ?>
                        <div class="flex items-start">
                            <div class="bg-red-primary text-white rounded-full w-8 h-8 flex items-center justify-center font-bold mr-4 mt-1"><?php echo $index + 1; ?></div>
                            <div>
                                <h4 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($step['title']); ?></h4>
                                <p class="text-gray-400"><?php echo htmlspecialchars($step['description']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Right - Image -->
                <div class="flex justify-center">
                    <?php if (!empty($how_to_book_section['right_image'])): ?>
                    <div class="w-full max-w-md rounded-3xl overflow-hidden shadow-2xl hover-scale">
                        <img src="<?php echo UPLOADS_URL . '/' . $how_to_book_section['right_image']; ?>" 
                             alt="<?php echo htmlspecialchars($how_to_book_section['title'] ?? 'How to Book'); ?>"
                             class="w-full h-auto object-cover">
                    </div>
                    <?php else: ?>
                    <div class="bg-gradient-to-br from-gray-800 to-gray-900 w-64 h-96 rounded-3xl flex items-center justify-center shadow-2xl hover-scale">
                        <div class="text-center">
                            <i class="fas fa-calendar-check text-6xl text-red-primary mb-4"></i>
                            <p class="text-gray-400">จองง่าย รวดเร็ว</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Reviews Carousel -->
    <?php if (!empty($reviews)): ?>
    <section class="py-20 bg-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">
                    รีวิวจาก<span class="text-red-primary">ลูกค้า</span>
                </h2>
                <p class="text-gray-400 text-lg">ความพึงพอใจของลูกค้าคือสิ่งสำคัญที่สุดสำหรับเรา</p>
            </div>
            
            <div class="carousel-container relative">
                <div id="carousel-track" class="carousel-track">
                    <?php 
                    $colors = ['green-500', 'blue-500', 'purple-500', 'red-500', 'orange-500', 'teal-500'];
                    foreach ($reviews as $index => $review): 
                        $color = $colors[$index % count($colors)];
                    ?>
                    <!-- Review <?php echo $index + 1; ?> -->
                    <div class="carousel-slide px-4">
                        <div class="bg-dark-light rounded-lg p-6 mx-auto max-w-sm hover-scale">
                            <?php 
                            $img_url = '';
                            if (!empty($review['image'])) {
                                if (preg_match('/^https?:\/\//', $review['image'])) {
                                    $img_url = $review['image'];
                                } elseif (strpos($review['image'], 'uploads/') === 0) {
                                    $img_url = BASE_URL . '/' . $review['image'];
                                } else {
                                    $img_url = UPLOADS_URL . '/' . $review['image'];
                                }
                            }
                            ?>
                            <?php if (!empty($img_url)): ?>
                            <div class="bg-<?php echo $color; ?> h-64 rounded-lg mb-4 overflow-hidden">
                                <img src="<?php echo htmlspecialchars($img_url); ?>" alt="<?php echo htmlspecialchars($review['customer_name']); ?>" class="w-full h-full object-cover">
                            </div>
                            <?php else: ?>
                            <div class="bg-<?php echo $color; ?> h-64 rounded-lg mb-4 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <i class="fab fa-line text-4xl mb-2"></i>
                                    <p class="text-sm">Line Chat Review</p>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="text-center">
                                <div class="flex justify-center mb-2">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i < $review['rating'] ? 'text-yellow-400' : 'text-gray-600'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="text-gray-400 text-sm mb-2"><?php echo htmlspecialchars($review['content']); ?></p>
                                <p class="text-gray-500 text-xs">- <?php echo htmlspecialchars($review['customer_name']); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Navigation Buttons -->
                <button id="prev-btn" class="absolute left-2 md:left-4 top-1/2 transform -translate-y-1/2 bg-red-primary hover:bg-red-light w-12 h-12 rounded-full text-white transition-all duration-300 shadow-lg hover:scale-110 flex items-center justify-center z-10">
                    <i class="fas fa-chevron-left text-lg"></i>
                </button>
                <button id="next-btn" class="absolute right-2 md:right-4 top-1/2 transform -translate-y-1/2 bg-red-primary hover:bg-red-light w-12 h-12 rounded-full text-white transition-all duration-300 shadow-lg hover:scale-110 flex items-center justify-center z-10">
                    <i class="fas fa-chevron-right text-lg"></i>
                </button>
                
                <!-- Dots Indicator -->
                <div id="dots-container" class="flex justify-center mt-8 space-x-2">
                    <!-- Dots will be generated by JavaScript -->
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Articles Section -->
    <?php if (!empty($articles)): ?>
    <section class="py-20 bg-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">
                    บทความและ<span class="text-red-primary">ข่าวสาร</span>
                </h2>
                <p class="text-gray-400 text-lg">อัพเดทเทรนด์และเรื่องราวน่าสนใจจากโลกของเรา</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($articles as $article): ?>
                <article class="bg-dark-light rounded-lg overflow-hidden hover-scale group">
                    <?php if (!empty($article['featured_image'])): ?>
                    <div class="relative overflow-hidden h-48">
                        <img src="<?php echo UPLOADS_URL . '/' . $article['featured_image']; ?>" alt="<?php echo h($article['title']); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <?php if (!empty($article['category_name'])): ?>
                        <span class="absolute top-4 left-4 px-3 py-1 text-xs font-semibold rounded-full" style="background-color: <?php echo $article['category_color'] ?? '#DC2626'; ?>; color: white;">
                            <?php echo h($article['category_name']); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <div class="relative overflow-hidden h-48 bg-gradient-to-br from-red-primary to-red-light flex items-center justify-center">
                        <i class="fas fa-newspaper text-6xl text-white/30"></i>
                        <?php if (!empty($article['category_name'])): ?>
                        <span class="absolute top-4 left-4 px-3 py-1 text-xs font-semibold rounded-full" style="background-color: <?php echo $article['category_color'] ?? '#DC2626'; ?>; color: white;">
                            <?php echo h($article['category_name']); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div class="p-6">
                        <div class="flex items-center text-sm text-gray-500 mb-3">
                            <i class="far fa-calendar mr-2"></i>
                            <span><?php echo date('d M Y', strtotime($article['published_at'] ?? $article['created_at'])); ?></span>
                            <?php if (!empty($article['author_name'])): ?>
                            <i class="far fa-user ml-4 mr-2"></i>
                            <span><?php echo h($article['author_name']); ?></span>
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

            <div class="text-center mt-12">
                <a href="articles.php" class="inline-block bg-red-primary hover:bg-red-light text-white font-bold py-3 px-8 rounded-full transition-all duration-300 hover:scale-105">
                    ดูบทความทั้งหมด
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-dark-light">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">
                    ติดต่อเรา
                </h2>
                <p class="text-gray-400 text-lg">พร้อมให้คำปรึกษาและรับจองบริการ</p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div>
                    <form id="contact-form" method="POST" action="<?php echo BASE_URL; ?>/contact-submit.php" class="space-y-6">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <div>
                            <label class="block text-sm font-medium mb-2">ชื่อ-นามสกุล</label>
                            <input type="text" name="name" class="w-full px-4 py-3 bg-dark border border-gray-600 rounded-lg focus:border-red-primary focus:outline-none transition-colors duration-300" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">อีเมล</label>
                            <input type="email" name="email" class="w-full px-4 py-3 bg-dark border border-gray-600 rounded-lg focus:border-red-primary focus:outline-none transition-colors duration-300" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">เบอร์โทรศัพท์</label>
                            <input type="tel" name="phone" class="w-full px-4 py-3 bg-dark border border-gray-600 rounded-lg focus:border-red-primary focus:outline-none transition-colors duration-300" required>
                        </div>
                        <!-- ซ่อนประเภทงานออกจากฟอร์ม แต่ยังส่งค่าเริ่มต้นเพื่อความเข้ากันได้ -->
                        <input type="hidden" name="service_type" value="ทั่วไป">
                        <div>
                            <label class="block text-sm font-medium mb-2">รายละเอียดงาน</label>
                            <textarea name="message" rows="4" class="w-full px-4 py-3 bg-dark border border-gray-600 rounded-lg focus:border-red-primary focus:outline-none transition-colors duration-300" placeholder="กรุณาระบุรายละเอียดงาน วันที่ เวลา และสถานที่" required></textarea>
                        </div>
                        <button type="submit" class="w-full bg-red-primary hover:bg-red-light py-3 rounded-lg font-medium transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
                            <i class="fas fa-paper-plane mr-2"></i>ส่งข้อความ
                        </button>
                    </form>
                </div>
                
                <!-- Contact Info -->
                <div class="space-y-8">
                    <div>
                        <h3 class="text-2xl font-bold mb-6">ข้อมูลการติดต่อ</h3>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <i class="fas fa-phone text-red-primary mr-4"></i>
                                <span><?php echo htmlspecialchars($contact_info['phone']); ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-red-primary mr-4"></i>
                                <span><?php echo htmlspecialchars($contact_info['email']); ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="fab fa-line text-red-primary mr-4"></i>
                                <span><?php echo htmlspecialchars($contact_info['line_id']); ?></span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-map-marker-alt text-red-primary mr-4 mt-1"></i>
                                <span><?php echo htmlspecialchars($contact_info['address']); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-xl font-semibold mb-4">เวลาทำการ</h4>
                        <div class="space-y-2 text-gray-400">
                            <p>พร้อมบริการตลอด 24 ชั่วโมงไม่มีวันหยุด</p>
                            <!-- <p>เสาร์ - อาทิตย์: 10:00 - 16:00</p> -->
                        </div>
                    </div>
                    
                    <?php if (!empty($active_socials)): ?>
                    <div>
                        <h4 class="text-xl font-semibold mb-4">ติดตามเรา</h4>
                        <div class="flex space-x-3">
                            <?php foreach ($active_socials as $platform => $social): ?>
                            <a href="<?php echo htmlspecialchars($social['url']); ?>" class="<?php echo $social['color']; ?> w-12 h-12 rounded-full text-white transition-all duration-300 flex items-center justify-center shadow-lg hover:scale-110 hover:shadow-xl" title="<?php echo $social['title']; ?>" target="_blank" rel="noopener">
                                <i class="fab <?php echo $social['icon']; ?> text-lg"></i>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
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
                        $footer_logo_text = $global_settings['logo_text'] ?? 'lollipop24hours';
                        $footer_logo_image = $global_settings['logo_image'] ?? '';
                        
                        if ($footer_logo_type === 'image' && !empty($footer_logo_image)): 
                        ?>
                            <img src="<?php echo UPLOADS_URL . '/' . $footer_logo_image; ?>" alt="<?php echo htmlspecialchars($footer_logo_text); ?>" class="h-12 object-contain">
                        <?php else: ?>
                            <i class="fas fa-star mr-2"></i><?php echo htmlspecialchars($footer_logo_text); ?>
                        <?php endif; ?>
                    </div>
                    <p class="text-gray-400 mb-4">บริการจัดหาเด็กเอนเตอร์เทน ชงเหล้า
                    24 ชั่วโมง</p>
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
                        // ใช้เมนูจากฐานข้อมูลแทนการ hard-coded (ไม่มีไอคอนใน footer)
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
                <p>&copy; 2025 lollipop24hours. สงวนลิขสิทธิ์.</p>
            </div>
        </div>
    </footer>

    <script>
        // Preloader
        window.addEventListener('load', function() {
            const preloader = document.getElementById('preloader');
            setTimeout(() => {
                preloader.classList.add('hidden');
                setTimeout(() => {
                    preloader.remove();
                }, 500);
            }, 1000);
        });

        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const closeMenuBtn = document.getElementById('close-menu');

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

        // Go to Top Button
        <?php if ($go_to_top_enabled == '1'): ?>
        const goToTopBtn = document.getElementById('go-to-top');

        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                goToTopBtn.classList.add('show');
            } else {
                goToTopBtn.classList.remove('show');
            }
        });

        goToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        <?php endif; ?>

        // Smooth Scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Reviews Carousel
        <?php if (!empty($reviews)): ?>
        class ReviewsCarousel {
            constructor() {
                this.track = document.getElementById('carousel-track');
                this.slides = this.track.querySelectorAll('.carousel-slide');
                this.prevBtn = document.getElementById('prev-btn');
                this.nextBtn = document.getElementById('next-btn');
                this.dotsContainer = document.getElementById('dots-container');
                
                this.currentIndex = 0;
                this.slidesToShow = window.innerWidth >= 768 ? 3 : 1;
                this.totalSlides = this.slides.length;
                this.maxIndex = Math.max(0, this.totalSlides - this.slidesToShow);
                
                this.init();
            }
            
            init() {
                this.createDots();
                this.updateCarousel();
                this.bindEvents();
                this.startAutoPlay();
            }
            
            createDots() {
                this.dotsContainer.innerHTML = '';
                
                for (let i = 0; i <= this.maxIndex; i++) {
                    const dot = document.createElement('button');
                    dot.className = `w-3 h-3 rounded-full transition-all duration-300 hover:scale-125 ${i === 0 ? 'bg-red-primary scale-125' : 'bg-gray-600'}`;
                    dot.addEventListener('click', () => this.goToSlide(i));
                    this.dotsContainer.appendChild(dot);
                }
            }
            
            updateCarousel() {
                const translateX = -(this.currentIndex * (100 / this.slidesToShow));
                this.track.style.transform = `translateX(${translateX}%)`;
                
                const dots = this.dotsContainer.querySelectorAll('button');
                dots.forEach((dot, index) => {
                    dot.className = `w-3 h-3 rounded-full transition-all duration-300 hover:scale-125 ${index === this.currentIndex ? 'bg-red-primary scale-125' : 'bg-gray-600'}`;
                });
            }
            
            nextSlide() {
                this.currentIndex = this.currentIndex >= this.maxIndex ? 0 : this.currentIndex + 1;
                this.updateCarousel();
            }
            
            prevSlide() {
                this.currentIndex = this.currentIndex <= 0 ? this.maxIndex : this.currentIndex - 1;
                this.updateCarousel();
            }
            
            goToSlide(index) {
                this.currentIndex = index;
                this.updateCarousel();
            }
            
            bindEvents() {
                this.nextBtn.addEventListener('click', () => {
                    this.nextSlide();
                    this.resetAutoPlay();
                });
                
                this.prevBtn.addEventListener('click', () => {
                    this.prevSlide();
                    this.resetAutoPlay();
                });
                
                let startX = 0;
                let endX = 0;
                
                this.track.addEventListener('touchstart', (e) => {
                    startX = e.touches[0].clientX;
                });
                
                this.track.addEventListener('touchend', (e) => {
                    endX = e.changedTouches[0].clientX;
                    const diff = startX - endX;
                    
                    if (Math.abs(diff) > 50) {
                        if (diff > 0) {
                            this.nextSlide();
                        } else {
                            this.prevSlide();
                        }
                        this.resetAutoPlay();
                    }
                });
                
                window.addEventListener('resize', () => {
                    const newSlidesToShow = window.innerWidth >= 768 ? 3 : 1;
                    if (newSlidesToShow !== this.slidesToShow) {
                        this.slidesToShow = newSlidesToShow;
                        this.maxIndex = Math.max(0, this.totalSlides - this.slidesToShow);
                        this.currentIndex = Math.min(this.currentIndex, this.maxIndex);
                        this.createDots();
                        this.updateCarousel();
                    }
                });
            }
            
            startAutoPlay() {
                this.autoPlayInterval = setInterval(() => {
                    this.nextSlide();
                }, 3000);
            }
            
            resetAutoPlay() {
                clearInterval(this.autoPlayInterval);
                this.startAutoPlay();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            new ReviewsCarousel();
        });
        <?php endif; ?>

        // Contact Form with AJAX
        document.getElementById('contact-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>กำลังส่ง...';
            submitBtn.disabled = true;
            
            try {
                const formData = new FormData(this);
                const response = await fetch('<?php echo BASE_URL; ?>/contact-submit.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // แสดง notification สำเร็จ
                    Swal.fire({
                        icon: 'success',
                        title: 'ส่งข้อความสำเร็จ!',
                        text: result.message || 'เราจะติดต่อกลับโดยเร็วที่สุด',
                        confirmButtonColor: '#DC2626',
                        confirmButtonText: 'ตกลง'
                    });
                    
                    // ล้างฟอร์ม
                    this.reset();
                } else {
                    // แสดง error
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด!',
                        text: result.message || 'ไม่สามารถส่งข้อความได้ กรุณาลองใหม่อีกครั้ง',
                        confirmButtonColor: '#DC2626',
                        confirmButtonText: 'ตกลง'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด!',
                    text: 'ไม่สามารถเชื่อมต่อได้ กรุณาลองใหม่อีกครั้ง',
                    confirmButtonColor: '#DC2626',
                    confirmButtonText: 'ตกลง'
                });
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });

        // Scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, observerOptions);

        document.querySelectorAll('section').forEach(section => {
            observer.observe(section);
        });
    </script>
    
    <!-- SweetAlert2 for Notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
