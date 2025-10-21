<?php
/**
 * สร้างฐานข้อมูล VIBEDAYBKK ใหม่ทั้งหมด
 * ไม่ใช้ไฟล์ SQL เก่า - เขียนใหม่ทั้งหมด
 */

set_time_limit(600);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ฟังก์ชันแสดงผล
function showMessage($type, $message) {
    $colors = [
        'success' => ['bg' => '#d4edda', 'text' => '#155724', 'border' => '#28a745'],
        'error' => ['bg' => '#f8d7da', 'text' => '#721c24', 'border' => '#dc3545'],
        'info' => ['bg' => '#d1ecf1', 'text' => '#0c5460', 'border' => '#17a2b8'],
        'warning' => ['bg' => '#fff3cd', 'text' => '#856404', 'border' => '#ffc107']
    ];
    $c = $colors[$type] ?? $colors['info'];
    echo "<div style='background: {$c['bg']}; color: {$c['text']}; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid {$c['border']};'>{$message}</div>";
    flush();
}

?>
<!DOCTYPE html>
<html><head>
<meta charset='UTF-8'>
<link href='https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap' rel='stylesheet'>
<style>
body { font-family: 'Kanit', sans-serif; margin: 0; padding: 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
.container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
.header { background: linear-gradient(135deg, #DC2626 0%, #991b1b 100%); color: white; padding: 50px; text-align: center; }
.header h1 { font-size: 3rem; margin: 0; }
.content { padding: 40px; }
h2 { color: #DC2626; margin: 30px 0 15px 0; padding-bottom: 10px; border-bottom: 3px solid #DC2626; }
table { width: 100%; border-collapse: collapse; margin: 15px 0; background: white; border-radius: 8px; overflow: hidden; }
th { background: #DC2626; color: white; padding: 12px; text-align: left; }
td { padding: 10px; border-bottom: 1px solid #eee; }
tr:hover { background: #f8f9fa; }
</style>
</head>
<body>
<div class='container'>
<div class='header'>
<h1>🚀 ติดตั้งฐานข้อมูล</h1>
<p style='font-size: 1.2rem; margin-top: 10px; opacity: 0.9;'>VIBEDAYBKK - Fresh Install</p>
</div>
<div class='content'>

<?php

// เชื่อมต่อ
showMessage('info', '<strong>🔌 กำลังเชื่อมต่อ MySQL...</strong>');

$socket = '/Applications/MAMP/tmp/mysql/mysql.sock';
$conn = new mysqli('localhost', 'root', 'root', '', 0, $socket);

if ($conn->connect_error) {
    showMessage('error', '❌ เชื่อมต่อล้มเหลว: ' . $conn->connect_error);
    die('</div></div></body></html>');
}

showMessage('success', '✅ เชื่อมต่อสำเร็จ! MySQL v' . $conn->server_info);

// สำรองฐานข้อมูลเดิม
showMessage('info', '<strong>📦 กำลังตรวจสอบฐานข้อมูลเดิม...</strong>');

$result = $conn->query("SHOW DATABASES LIKE 'vibedaybkk'");
if ($result && $result->num_rows > 0) {
    $backup = "vibedaybkk_backup_" . date('Ymd_His');
    $conn->query("CREATE DATABASE `{$backup}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // ดึงเฉพาะ BASE TABLE (ไม่รวม VIEW)
    $tables = [];
    $r = $conn->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'vibedaybkk' AND table_type = 'BASE TABLE'");
    while ($row = $r->fetch_array()) $tables[] = $row[0];
    
    $backupCount = 0;
    foreach ($tables as $t) {
        if (@$conn->query("CREATE TABLE `{$backup}`.`{$t}` LIKE `vibedaybkk`.`{$t}`")) {
            @$conn->query("INSERT INTO `{$backup}`.`{$t}` SELECT * FROM `vibedaybkk`.`{$t}`");
            $backupCount++;
        }
    }
    
    showMessage('success', "✅ สำรอง {$backupCount} ตารางเป็น: <strong>{$backup}</strong>");
}

// ลบและสร้างใหม่
showMessage('info', '<strong>🗄️ กำลังสร้างฐานข้อมูลใหม่...</strong>');

$conn->query("DROP DATABASE IF EXISTS vibedaybkk");
$conn->query("CREATE DATABASE vibedaybkk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db('vibedaybkk');
$conn->set_charset('utf8mb4');

showMessage('success', '✅ สร้างฐานข้อมูล vibedaybkk ใหม่สำเร็จ');

// ปิด foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

echo "<h2>📋 กำลังสร้างตาราง...</h2>";

// ==================== สร้างตารางทั้งหมด ====================

$tablesCreated = 0;

// 1. users
if ($conn->query("CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role ENUM('programmer', 'admin', 'editor', 'viewer') DEFAULT 'viewer',
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci")) {
    $tablesCreated++;
    showMessage('success', '✅ สร้างตาราง users');
}

// Insert admin
$conn->query("INSERT INTO users (username, password, full_name, email, role) VALUES 
    ('admin', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ผู้ดูแลระบบ', 'admin@vibedaybkk.com', 'admin')");

// 2. roles
if ($conn->query("CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_key VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    description TEXT,
    level INT DEFAULT 0,
    icon VARCHAR(50) DEFAULT 'fa-user',
    color VARCHAR(100) DEFAULT 'bg-gray-600',
    price DECIMAL(10,2) DEFAULT 0.00,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci")) {
    $tablesCreated++;
    showMessage('success', '✅ สร้างตาราง roles');
}

$conn->query("INSERT INTO roles (role_key, display_name, description, level, icon, color, price) VALUES
    ('programmer', 'โปรแกรมเมอร์', 'สิทธิ์สูงสุด', 100, 'fa-code', 'bg-purple-600', 0),
    ('admin', 'ผู้ดูแลระบบ', 'จัดการทั้งหมด', 80, 'fa-user-shield', 'bg-red-600', 0),
    ('editor', 'บรรณาธิการ', 'จัดการเนื้อหา', 50, 'fa-edit', 'bg-yellow-500', 0),
    ('viewer', 'ผู้ชม', 'ดูอย่างเดียว', 10, 'fa-eye', 'bg-gray-500', 0)");

// 3. permissions
if ($conn->query("CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_key VARCHAR(50) NOT NULL,
    feature VARCHAR(50) NOT NULL,
    can_view TINYINT(1) DEFAULT 0,
    can_create TINYINT(1) DEFAULT 0,
    can_edit TINYINT(1) DEFAULT 0,
    can_delete TINYINT(1) DEFAULT 0,
    can_export TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_role_feature (role_key, feature)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci")) {
    $tablesCreated++;
    showMessage('success', '✅ สร้างตาราง permissions');
}

// Insert permissions
$features = ['models', 'categories', 'articles', 'article_categories', 'bookings', 'contacts', 'menus', 'users', 'gallery', 'settings', 'homepage'];
foreach ($features as $f) {
    $conn->query("INSERT IGNORE INTO permissions (role_key, feature, can_view, can_create, can_edit, can_delete, can_export) VALUES ('admin', '{$f}', 1, 1, 1, 1, 1)");
    $conn->query("INSERT IGNORE INTO permissions (role_key, feature, can_view, can_create, can_edit, can_delete, can_export) VALUES ('editor', '{$f}', 1, 1, 1, 0, 0)");
    $conn->query("INSERT IGNORE INTO permissions (role_key, feature, can_view, can_create, can_edit, can_delete, can_export) VALUES ('viewer', '{$f}', 1, 0, 0, 0, 0)");
}

// 4. settings
if ($conn->query("CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type VARCHAR(50) DEFAULT 'text',
    category VARCHAR(50) DEFAULT 'general',
    description TEXT,
    is_public TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci")) {
    $tablesCreated++;
    showMessage('success', '✅ สร้างตาราง settings');
}

$conn->query("INSERT IGNORE INTO settings (setting_key, setting_value, category) VALUES
    ('site_name', 'VIBEDAY', 'general'),
    ('site_description', 'บริการโมเดลและนางแบบมืออาชีพ', 'general'),
    ('site_logo', '', 'general'),
    ('site_favicon', '', 'general'),
    ('contact_phone', '02-123-4567', 'contact'),
    ('contact_email', 'info@vibedaybkk.com', 'contact'),
    ('social_facebook_enabled', '1', 'social'),
    ('social_facebook_url', 'https://facebook.com', 'social'),
    ('social_instagram_enabled', '1', 'social'),
    ('social_instagram_url', 'https://instagram.com', 'social'),
    ('social_line_enabled', '1', 'social'),
    ('social_line_url', '@vibedaybkk', 'social'),
    ('gototop_enabled', '1', 'general'),
    ('gototop_icon', 'fa-arrow-up', 'general'),
    ('gototop_bg_color', 'bg-red-primary', 'general')");

// 5. categories
if ($conn->query("CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    name_en VARCHAR(100),
    description TEXT,
    icon VARCHAR(50),
    color VARCHAR(50),
    gender ENUM('female', 'male', 'all') DEFAULT 'all',
    price_min DECIMAL(10,2),
    price_max DECIMAL(10,2),
    price_range VARCHAR(100),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci")) {
    $tablesCreated++;
    showMessage('success', '✅ สร้างตาราง categories');
}

$conn->query("INSERT INTO categories (code, name, name_en, description, icon, color, gender, price_min, price_max, price_range, sort_order) VALUES
    ('female-fashion', 'โมเดลแฟชั่นหญิง', 'Fashion Models', 'งานถ่ายแฟชั่น แคตตาล็อก รันเวย์', 'fa-female', 'from-pink-500 to-red-primary', 'female', 3000, 5000, '3,000-5,000 บาท/วัน', 1),
    ('female-photography', 'โมเดลถ่ายภาพหญิง', 'Photography Models', 'งานถ่ายภาพโฆษณา แคตตาล็อก', 'fa-camera', 'from-purple-500 to-pink-500', 'female', 2500, 4000, '2,500-4,000 บาท/วัน', 2),
    ('female-event', 'นางแบบอีเวนต์', 'Event Models', 'งานแสดงสินค้า มอเตอร์โชว์', 'fa-star', 'from-red-primary to-red-light', 'female', 2000, 3500, '2,000-3,500 บาท/วัน', 3),
    ('male-fashion', 'โมเดลแฟชั่นชาย', 'Male Fashion Models', 'งานถ่ายแฟชั่นผู้ชาย', 'fa-male', 'from-blue-500 to-indigo-600', 'male', 3500, 6000, '3,500-6,000 บาท/วัน', 4),
    ('male-fitness', 'โมเดลฟิตเนส', 'Fitness Models', 'งานโฆษณาฟิตเนส อาหารเสริม', 'fa-dumbbell', 'from-green-500 to-teal-600', 'male', 3000, 5000, '3,000-5,000 บาท/วัน', 5),
    ('male-business', 'โมเดลธุรกิจ', 'Business Models', 'งานโฆษณาธุรกิจ คอร์ปอเรต', 'fa-briefcase', 'from-orange-500 to-red-500', 'male', 2500, 4500, '2,500-4,500 บาท/วัน', 6)");

// 6. models
if ($conn->query("CREATE TABLE models (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    name_en VARCHAR(100),
    description TEXT,
    price_min DECIMAL(10,2),
    price_max DECIMAL(10,2),
    height INT,
    weight INT,
    bust INT,
    waist INT,
    hips INT,
    experience_years INT DEFAULT 0,
    age INT,
    skin_tone VARCHAR(50),
    hair_color VARCHAR(50),
    eye_color VARCHAR(50),
    languages TEXT,
    skills TEXT,
    featured TINYINT(1) DEFAULT 0,
    status ENUM('available', 'busy', 'inactive') DEFAULT 'available',
    view_count INT DEFAULT 0,
    booking_count INT DEFAULT 0,
    rating DECIMAL(3,2) DEFAULT 0.00,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci")) {
    $tablesCreated++;
    showMessage('success', '✅ สร้างตาราง models');
}

// 7. homepage_sections
if ($conn->query("CREATE TABLE homepage_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(50) NOT NULL UNIQUE,
    section_id VARCHAR(50),
    section_class TEXT,
    title TEXT,
    subtitle TEXT,
    content LONGTEXT,
    background_type ENUM('color', 'image', 'gradient') DEFAULT 'color',
    background_color VARCHAR(50),
    background_image VARCHAR(255),
    background_position VARCHAR(50) DEFAULT 'center',
    background_size VARCHAR(50) DEFAULT 'cover',
    background_repeat VARCHAR(50) DEFAULT 'no-repeat',
    background_attachment VARCHAR(50) DEFAULT 'scroll',
    left_image VARCHAR(255),
    button1_text VARCHAR(100),
    button1_link VARCHAR(255),
    button2_text VARCHAR(100),
    button2_link VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci")) {
    $tablesCreated++;
    showMessage('success', '✅ สร้างตาราง homepage_sections');
}

$conn->query("INSERT INTO homepage_sections (section_key, title, subtitle, button1_text, button1_link, button2_text, button2_link, is_active, sort_order) VALUES
    ('hero', 'VIBEDAYBKK', 'บริการโมเดลและนางแบบมืออาชีพ', 'จองเลย', '#contact', 'ดูผลงาน', '#services', 1, 1),
    ('about', 'เกี่ยวกับเรา', 'ทำไมต้องเลือกเรา', NULL, NULL, NULL, NULL, 1, 2),
    ('services', 'บริการของเรา', 'บริการที่หลากหลาย', NULL, NULL, NULL, NULL, 1, 3),
    ('gallery', 'ผลงาน', 'แกลเลอรี่', NULL, NULL, NULL, NULL, 0, 4),
    ('testimonials', 'รีวิว', 'ความคิดเห็นจากลูกค้า', NULL, NULL, NULL, NULL, 1, 5),
    ('stats', 'ตัวเลข', 'สถิติที่น่าประทับใจ', NULL, NULL, NULL, NULL, 0, 6),
    ('cta', 'Call to Action', 'พร้อมเริ่มโปรเจกต์', NULL, NULL, NULL, NULL, 0, 7),
    ('contact', 'ติดต่อเรา', 'ติดต่อสอบถาม', NULL, NULL, NULL, NULL, 1, 8)");

// 8. articles
if ($conn->query("CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT,
    content LONGTEXT NOT NULL,
    featured_image VARCHAR(255),
    category VARCHAR(100),
    category_id INT,
    author_id INT,
    read_time INT DEFAULT 5,
    view_count INT DEFAULT 0,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    published_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci")) {
    $tablesCreated++;
    showMessage('success', '✅ สร้างตาราง articles');
}

// 9. article_categories
if ($conn->query("CREATE TABLE article_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    color VARCHAR(50),
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci")) {
    $tablesCreated++;
    showMessage('success', '✅ สร้างตาราง article_categories');
}

$conn->query("INSERT INTO article_categories (name, slug, icon, color) VALUES
    ('แฟชั่น', 'fashion', 'fa-tshirt', 'from-pink-500 to-red-500'),
    ('การถ่ายภาพ', 'photography', 'fa-camera', 'from-blue-500 to-purple-500'),
    ('เคล็ดลับ', 'tips', 'fa-lightbulb', 'from-yellow-500 to-orange-500')");

// 10. bookings
if ($conn->query("CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model_id INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    booking_date DATE NOT NULL,
    booking_days INT DEFAULT 1,
    service_type VARCHAR(100),
    location TEXT,
    message TEXT,
    total_price DECIMAL(10,2),
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    payment_status ENUM('unpaid', 'deposit', 'paid', 'refunded') DEFAULT 'unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (model_id) REFERENCES models(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci")) {
    $tablesCreated++;
    showMessage('success', '✅ สร้างตาราง bookings');
}

// 11. contacts
if ($conn->query("CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    service_type VARCHAR(100),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
    ip_address VARCHAR(45),
    user_agent TEXT,
    replied_at DATETIME,
    replied_by INT,
    reply_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci")) {
    $tablesCreated++;
    showMessage('success', '✅ สร้างตาราง contacts');
}

// 12. customer_reviews
if ($conn->query("CREATE TABLE customer_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100),
    image VARCHAR(255),
    rating INT DEFAULT 5,
    content TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci")) {
    $tablesCreated++;
    showMessage('success', '✅ สร้างตาราง customer_reviews');
}

$conn->query("INSERT INTO customer_reviews (customer_name, rating, content) VALUES
    ('คุณสมชาย ใจดี', 5, 'บริการดีมาก โมเดลมืออาชีพ ประทับใจครับ'),
    ('คุณสมหญิง สวยงาม', 5, 'โมเดลสวย ทำงานได้ดีมาก แนะนำเลยค่ะ'),
    ('คุณวิภา เจริญ', 4, 'ราคาเหมาะสม คุณภาพดี')");

// 13. menus
if ($conn->query("CREATE TABLE menus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NULL,
    title VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon VARCHAR(50),
    target VARCHAR(20) DEFAULT '_self',
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci")) {
    $tablesCreated++;
    showMessage('success', '✅ สร้างตาราง menus');
}

$conn->query("INSERT INTO menus (title, url, icon, sort_order) VALUES
    ('เกี่ยวกับ', 'index.php#about', 'fa-info-circle', 1),
    ('บริการ', 'index.php#services', 'fa-briefcase', 2),
    ('ผลงาน', 'gallery.php', 'fa-images', 3),
    ('บทความ', 'articles.php', 'fa-newspaper', 4),
    ('ติดต่อ', 'index.php#contact', 'fa-envelope', 5)");

// 14. gallery_albums
if ($conn->query("CREATE TABLE gallery_albums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    cover_image VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    view_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci")) {
    $tablesCreated++;
    showMessage('success', '✅ สร้างตาราง gallery_albums');
}

// 15. gallery_images
if ($conn->query("CREATE TABLE gallery_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    album_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    thumbnail_path VARCHAR(255),
    title VARCHAR(255),
    description TEXT,
    sort_order INT DEFAULT 0,
    view_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (album_id) REFERENCES gallery_albums(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci")) {
    $tablesCreated++;
    showMessage('success', '✅ สร้างตาราง gallery_images');
}

// 16. activity_logs
if ($conn->query("CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values TEXT,
    new_values TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci")) {
    $tablesCreated++;
    showMessage('success', '✅ สร้างตาราง activity_logs');
}

// เปิด foreign key ใหม่
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

showMessage('info', "📊 สร้างตารางทั้งหมด: <strong>{$tablesCreated} ตาราง</strong>");

// ==================== ตรวจสอบผล ====================
echo "<h2>🔍 ตรวจสอบผลลัพธ์</h2>";

$tables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) $tables[] = $row[0];

echo "<table><tr><th>#</th><th>ตาราง</th><th>จำนวนข้อมูล</th></tr>";
$totalRecords = 0;
foreach ($tables as $idx => $t) {
    $c = $conn->query("SELECT COUNT(*) as cnt FROM `{$t}`")->fetch_assoc();
    $totalRecords += $c['cnt'];
    echo "<tr><td>" . ($idx+1) . "</td><td><strong>{$t}</strong></td><td>" . number_format($c['cnt']) . "</td></tr>";
}
echo "</table>";

showMessage('info', "📊 ตาราง: <strong>" . count($tables) . "</strong> | ข้อมูล: <strong>{$totalRecords} records</strong>");

// ตรวจสอบ admin
$admin = $conn->query("SELECT username, role FROM users WHERE role IN ('admin', 'programmer') LIMIT 1");
if ($admin && $admin->num_rows > 0) {
    $a = $admin->fetch_assoc();
    showMessage('success', "✅ Admin: <strong>{$a['username']}</strong> ({$a['role']})");
}

// ตรวจสอบ roles & permissions
$rolesCount = $conn->query("SELECT COUNT(*) as c FROM roles")->fetch_assoc()['c'];
$permsCount = $conn->query("SELECT COUNT(*) as c FROM permissions")->fetch_assoc()['c'];

showMessage('info', "👥 Roles: <strong>{$rolesCount}</strong> | 🔐 Permissions: <strong>{$permsCount}</strong>");

// สรุป
echo "<h2>✨ สรุป</h2>";

if (count($tables) >= 15 && $totalRecords >= 20) {
    echo "<div style='background: #d4edda; color: #155724; padding: 50px; border-radius: 15px; text-align: center; margin: 30px 0;'>";
    echo "<h2 style='color: #155724; font-size: 2.5rem; margin: 0 0 30px 0;'>🎉 สำเร็จ!</h2>";
    echo "<p style='font-size: 1.3rem; margin: 15px 0;'>✅ ติดตั้งฐานข้อมูลเรียบร้อย</p>";
    echo "<p style='font-size: 1.1rem;'>ตาราง: <strong>" . count($tables) . "</strong> | ข้อมูล: <strong>{$totalRecords}</strong></p>";
    
    echo "<div style='margin-top: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;'>";
    echo "<a href='verify-database-structure.php' style='padding: 25px; background: #17a2b8; color: white; text-decoration: none; border-radius: 12px; font-size: 1.1rem; font-weight: 600;'>🔍 ตรวจสอบ DB</a>";
    echo "<a href='admin/' style='padding: 25px; background: #DC2626; color: white; text-decoration: none; border-radius: 12px; font-size: 1.1rem; font-weight: 600;'>👨‍💼 Admin Panel</a>";
    echo "<a href='test-connection-all.php' style='padding: 25px; background: #28a745; color: white; text-decoration: none; border-radius: 12px; font-size: 1.1rem; font-weight: 600;'>✅ Test Connection</a>";
    echo "<a href='index.php' style='padding: 25px; background: #6f42c1; color: white; text-decoration: none; border-radius: 12px; font-size: 1.1rem; font-weight: 600;'>🏠 Frontend</a>";
    echo "</div>";
    
    echo "<div style='margin-top: 30px; padding: 25px; background: #fff3cd; border-radius: 12px;'>";
    echo "<h3 style='margin: 0 0 15px 0; color: #856404;'>👤 ข้อมูลเข้าสู่ระบบ</h3>";
    echo "<p style='font-size: 1.2rem; margin: 0;'>";
    echo "Username: <strong style='color: #DC2626;'>admin</strong><br>";
    echo "Password: <strong style='color: #DC2626;'>admin123</strong>";
    echo "</p>";
    echo "</div>";
    echo "</div>";
} else {
    showMessage('warning', "⚠️ ฐานข้อมูลสร้างแล้ว แต่อาจไม่สมบูรณ์<br>ตาราง: " . count($tables) . " | ข้อมูล: {$totalRecords}");
}

$conn->close();

?>

</div>
</div>
</body>
</html>

