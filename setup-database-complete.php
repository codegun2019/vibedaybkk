<?php
/**
 * ติดตั้งฐานข้อมูล VIBEDAYBKK แบบสมบูรณ์
 * ใช้ไฟล์ database.sql (สะอาด) + ข้อมูลเพิ่มเติม
 */

set_time_limit(600);
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'>";
echo "<link href='https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap' rel='stylesheet'>";
echo "<style>
body { font-family: 'Kanit', sans-serif; padding: 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
.container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
.header { background: linear-gradient(135deg, #DC2626 0%, #991b1b 100%); color: white; padding: 40px; text-align: center; }
.header h1 { font-size: 2.5rem; margin: 0; }
.content { padding: 40px; }
.success { background: #d4edda; color: #155724; padding: 20px; border-radius: 10px; margin: 15px 0; border-left: 5px solid #28a745; }
.error { background: #f8d7da; color: #721c24; padding: 20px; border-radius: 10px; margin: 15px 0; border-left: 5px solid #dc3545; }
.info { background: #d1ecf1; color: #0c5460; padding: 20px; border-radius: 10px; margin: 15px 0; border-left: 5px solid #17a2b8; }
.warning { background: #fff3cd; color: #856404; padding: 20px; border-radius: 10px; margin: 15px 0; border-left: 5px solid #ffc107; }
h2 { color: #DC2626; margin: 30px 0 20px 0; padding-bottom: 10px; border-bottom: 3px solid #DC2626; }
.step { background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 15px 0; border-left: 4px solid #DC2626; }
.step-number { display: inline-block; width: 40px; height: 40px; background: #DC2626; color: white; border-radius: 50%; text-align: center; line-height: 40px; font-weight: bold; margin-right: 15px; font-size: 18px; }
table { width: 100%; background: white; border-collapse: collapse; margin: 20px 0; }
th { background: #DC2626; color: white; padding: 15px; }
td { padding: 12px 15px; border-bottom: 1px solid #eee; }
</style></head><body>";

echo "<div class='container'>";
echo "<div class='header'><h1>🗄️ ติดตั้งฐานข้อมูล VIBEDAYBKK</h1><p style='margin-top: 10px;'>Version 4.0 - แบบสมบูรณ์และปลอดภัย</p></div>";
echo "<div class='content'>";

// ==================== เชื่อมต่อ ====================
echo "<div class='step'><span class='step-number'>1</span><strong style='font-size: 1.2rem;'>เชื่อมต่อ MySQL</strong></div>";

$socket = '/Applications/MAMP/tmp/mysql/mysql.sock';
$conn = new mysqli('localhost', 'root', 'root', '', 0, $socket);

if ($conn->connect_error) {
    die("<div class='error'>❌ เชื่อมต่อล้มเหลว: " . $conn->connect_error . "</div></div></div></body></html>");
}

echo "<div class='success'>✅ เชื่อมต่อสำเร็จ! MySQL v" . $conn->server_info . "</div>";

// ==================== สำรอง ====================
echo "<div class='step'><span class='step-number'>2</span><strong style='font-size: 1.2rem;'>สำรองฐานข้อมูลเดิม</strong></div>";

$result = $conn->query("SHOW DATABASES LIKE 'vibedaybkk'");
if ($result && $result->num_rows > 0) {
    $backup = "vibedaybkk_old_" . date('Ymd_His');
    $conn->query("CREATE DATABASE `{$backup}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    $tables = [];
    $r = $conn->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'vibedaybkk'");
    while ($row = $r->fetch_array()) $tables[] = $row[0];
    
    foreach ($tables as $t) {
        @$conn->query("CREATE TABLE `{$backup}`.`{$t}` LIKE `vibedaybkk`.`{$t}`");
        @$conn->query("INSERT INTO `{$backup}`.`{$t}` SELECT * FROM `vibedaybkk`.`{$t}`");
    }
    echo "<div class='success'>✅ สำรองเป็น: <strong>{$backup}</strong></div>";
} else {
    echo "<div class='info'>📌 ไม่พบฐานข้อมูลเดิม</div>";
}

// ==================== ลบ + สร้างใหม่ ====================
echo "<div class='step'><span class='step-number'>3</span><strong style='font-size: 1.2rem;'>สร้างฐานข้อมูลใหม่</strong></div>";

$conn->query("DROP DATABASE IF EXISTS vibedaybkk");
$conn->query("CREATE DATABASE vibedaybkk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db('vibedaybkk');
$conn->set_charset('utf8mb4');

echo "<div class='success'>✅ สร้าง 'vibedaybkk' สำเร็จ</div>";

// ==================== Import จาก database.sql ====================
echo "<div class='step'><span class='step-number'>4</span><strong style='font-size: 1.2rem;'>Import โครงสร้างจาก database.sql</strong></div>";

$sqlFile = __DIR__ . '/database.sql';
if (file_exists($sqlFile)) {
    $sql = file_get_contents($sqlFile);
    
    // ลบ CREATE DATABASE และ USE statements
    $sql = preg_replace('/CREATE DATABASE.*?;/i', '', $sql);
    $sql = preg_replace('/USE `?vibedaybkk`?;/i', '', $sql);
    
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
    
    if ($conn->multi_query($sql)) {
        while ($conn->more_results() && $conn->next_result()) {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        }
    }
    
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "<div class='success'>✅ Import โครงสร้างพื้นฐานสำเร็จ</div>";
} else {
    echo "<div class='warning'>⚠️ ไม่พบ database.sql - ข้าม</div>";
}

// ==================== เพิ่มข้อมูลเพิ่มเติม ====================
echo "<div class='step'><span class='step-number'>5</span><strong style='font-size: 1.2rem;'>เพิ่มข้อมูลเสริม</strong></div>";

// เพิ่ม column is_active ให้ categories
@$conn->query("ALTER TABLE categories ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER sort_order");

// อัพเดท categories
$conn->query("UPDATE categories SET is_active = 1 WHERE status = 'active'");

// ตรวจสอบและสร้างตาราง roles (ถ้ายังไม่มี)
$checkRoles = $conn->query("SHOW TABLES LIKE 'roles'");
if (!$checkRoles || $checkRoles->num_rows == 0) {
    $conn->query("
        CREATE TABLE roles (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Insert roles
    $conn->query("
        INSERT INTO roles (role_key, display_name, description, level, icon, color, price) VALUES
        ('programmer', 'โปรแกรมเมอร์', 'สิทธิ์สูงสุด ไม่สามารถลบได้', 100, 'fa-code', 'bg-purple-600', 0),
        ('admin', 'ผู้ดูแลระบบ', 'จัดการระบบทั้งหมด', 80, 'fa-user-shield', 'bg-red-600', 0),
        ('editor', 'บรรณาธิการ', 'จัดการเนื้อหา', 50, 'fa-edit', 'bg-yellow-500', 0),
        ('viewer', 'ผู้ชม', 'ดูข้อมูลอย่างเดียว', 10, 'fa-eye', 'bg-gray-500', 0)
    ");
    
    echo "<div class='success'>✅ สร้างตาราง roles</div>";
}

// สร้างตาราง permissions
$checkPerms = $conn->query("SHOW TABLES LIKE 'permissions'");
if (!$checkPerms || $checkPerms->num_rows == 0) {
    $conn->query("
        CREATE TABLE permissions (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Insert permissions สำหรับ admin และ editor
    $features = ['models', 'categories', 'articles', 'article_categories', 'bookings', 'contacts', 'menus', 'users', 'gallery', 'settings', 'homepage'];
    
    foreach ($features as $feature) {
        // Admin - ทุกสิทธิ์
        $conn->query("INSERT INTO permissions (role_key, feature, can_view, can_create, can_edit, can_delete, can_export) 
                      VALUES ('admin', '{$feature}', 1, 1, 1, 1, 1)");
        
        // Editor - ส่วนใหญ่ยกเว้น users
        if ($feature != 'users' && $feature != 'roles') {
            $conn->query("INSERT INTO permissions (role_key, feature, can_view, can_create, can_edit, can_delete, can_export) 
                          VALUES ('editor', '{$feature}', 1, 1, 1, 0, 0)");
        }
        
        // Viewer - ดูอย่างเดียว
        $conn->query("INSERT INTO permissions (role_key, feature, can_view, can_create, can_edit, can_delete, can_export) 
                      VALUES ('viewer', '{$feature}', 1, 0, 0, 0, 0)");
    }
    
    echo "<div class='success'>✅ สร้างตาราง permissions</div>";
}

// อัพเดท users ให้มี role ใหม่
@$conn->query("ALTER TABLE users MODIFY COLUMN role ENUM('programmer', 'admin', 'editor', 'viewer') DEFAULT 'viewer'");

// สร้าง activity_logs
$checkLogs = $conn->query("SHOW TABLES LIKE 'activity_logs'");
if (!$checkLogs || $checkLogs->num_rows == 0) {
    $conn->query("
        CREATE TABLE activity_logs (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<div class='success'>✅ สร้างตาราง activity_logs</div>";
}

// สร้าง homepage_sections
$checkHP = $conn->query("SHOW TABLES LIKE 'homepage_sections'");
if (!$checkHP || $checkHP->num_rows == 0) {
    $conn->query("
        CREATE TABLE homepage_sections (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Insert sections
    $conn->query("INSERT INTO homepage_sections (section_key, title, subtitle, is_active, sort_order) VALUES
        ('hero', 'VIBEDAYBKK', 'บริการโมเดลและนางแบบมืออาชีพ', 1, 1),
        ('about', 'เกี่ยวกับเรา', 'ทำไมต้องเลือกเรา', 1, 2),
        ('services', 'บริการของเรา', 'บริการที่หลากหลายสำหรับทุกความต้องการ', 1, 3),
        ('gallery', 'ผลงานของเรา', 'แกลเลอรี่ผลงานที่ผ่านมา', 0, 4),
        ('testimonials', 'รีวิวจากลูกค้า', 'ความคิดเห็นจากลูกค้าที่ใช้บริการ', 1, 5),
        ('contact', 'ติดต่อเรา', 'ติดต่อสอบถามและจองบริการ', 1, 6)
    ");
    
    echo "<div class='success'>✅ สร้างตาราง homepage_sections</div>";
}

// สร้าง customer_reviews (สำหรับ frontend)
$checkReviews = $conn->query("SHOW TABLES LIKE 'customer_reviews'");
if (!$checkReviews || $checkReviews->num_rows == 0) {
    $conn->query("
        CREATE TABLE customer_reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_name VARCHAR(100),
            image VARCHAR(255),
            rating INT DEFAULT 5,
            content TEXT,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Insert sample reviews
    $conn->query("INSERT INTO customer_reviews (customer_name, rating, content, is_active) VALUES
        ('คุณสมชาย', 5, 'บริการดีมาก โมเดลมืออาชีพ', 1),
        ('คุณสมหญิง', 5, 'ประทับใจการให้บริการ', 1),
        ('คุณวิภา', 4, 'โมเดลสวย ทำงานได้ดี', 1)
    ");
    
    echo "<div class='success'>✅ สร้างตาราง customer_reviews</div>";
}

// สร้าง contacts
$checkContacts = $conn->query("SHOW TABLES LIKE 'contacts'");
if (!$checkContacts || $checkContacts->num_rows == 0) {
    $conn->query("
        CREATE TABLE contacts (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<div class='success'>✅ สร้างตาราง contacts</div>";
}

// สร้าง article_categories
$checkArtCat = $conn->query("SHOW TABLES LIKE 'article_categories'");
if (!$checkArtCat || $checkArtCat->num_rows == 0) {
    $conn->query("
        CREATE TABLE article_categories (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    $conn->query("INSERT INTO article_categories (name, slug, icon, color, sort_order) VALUES
        ('แฟชั่น', 'fashion', 'fa-tshirt', 'from-pink-500 to-red-500', 1),
        ('การถ่ายภาพ', 'photography', 'fa-camera', 'from-blue-500 to-purple-500', 2),
        ('เคล็ดลับ', 'tips', 'fa-lightbulb', 'from-yellow-500 to-orange-500', 3)
    ");
    
    echo "<div class='success'>✅ สร้างตาราง article_categories</div>";
}

// สร้าง gallery_albums และ gallery_images
$checkAlbums = $conn->query("SHOW TABLES LIKE 'gallery_albums'");
if (!$checkAlbums || $checkAlbums->num_rows == 0) {
    $conn->query("
        CREATE TABLE gallery_albums (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            cover_image VARCHAR(255),
            sort_order INT DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            view_count INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<div class='success'>✅ สร้างตาราง gallery_albums</div>";
}

$checkImages = $conn->query("SHOW TABLES LIKE 'gallery_images'");
if (!$checkImages || $checkImages->num_rows == 0) {
    $conn->query("
        CREATE TABLE gallery_images (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<div class='success'>✅ สร้างตาราง gallery_images</div>";
}

echo "<div class='info'>✅ ข้อมูลเสริมเรียบร้อย</div>";

// ==================== ตรวจสอบผล ====================
echo "<div class='step'><span class='step-number'>6</span><strong style='font-size: 1.2rem;'>ตรวจสอบผลลัพธ์</strong></div>";

$tables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) $tables[] = $row[0];

echo "<div class='success'>✅ ตาราง: <strong>" . count($tables) . "</strong></div>";

echo "<table><tr><th>#</th><th>ตาราง</th><th>จำนวนข้อมูล</th></tr>";
$totalRecords = 0;
foreach ($tables as $idx => $t) {
    $c = $conn->query("SELECT COUNT(*) as cnt FROM `{$t}`")->fetch_assoc();
    $totalRecords += $c['cnt'];
    echo "<tr><td>" . ($idx+1) . "</td><td><strong>{$t}</strong></td><td>" . number_format($c['cnt']) . "</td></tr>";
}
echo "</table>";

echo "<div class='info'>📊 ข้อมูลทั้งหมด: <strong>" . number_format($totalRecords) . " records</strong></div>";

// ตรวจสอบ admin
$admin = $conn->query("SELECT username, role FROM users WHERE role IN ('admin', 'programmer') LIMIT 1");
if ($admin && $admin->num_rows > 0) {
    $a = $admin->fetch_assoc();
    echo "<div class='success'>✅ Admin: <strong>{$a['username']}</strong> ({$a['role']})</div>";
} else {
    echo "<div class='error'>❌ ไม่พบ admin - ต้องสร้างใหม่!</div>";
}

// สรุป
echo "<h2>✨ สรุป</h2>";

if (count($tables) >= 10 && $totalRecords >= 10) {
    echo "<div class='success' style='padding: 40px; text-align: center;'>";
    echo "<h2 style='color: #155724; margin: 0 0 20px 0;'>🎉 ติดตั้งฐานข้อมูลสำเร็จ!</h2>";
    echo "<p style='font-size: 1.2rem;'>✅ ตาราง: " . count($tables) . " | ✅ ข้อมูล: " . number_format($totalRecords) . "</p>";
    echo "<div style='margin-top: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px;'>";
    echo "<a href='verify-database-structure.php' style='padding: 20px; background: #17a2b8; color: white; text-decoration: none; border-radius: 10px; text-align: center; font-weight: 600;'>🔍 ตรวจสอบ DB</a>";
    echo "<a href='admin/' style='padding: 20px; background: #DC2626; color: white; text-decoration: none; border-radius: 10px; text-align: center; font-weight: 600;'>👨‍💼 Admin</a>";
    echo "</div>";
    echo "<div style='margin-top: 20px; padding: 20px; background: #fff3cd; border-radius: 10px;'>";
    echo "<p style='font-size: 1.1rem;'>👤 Login: <strong>admin</strong> / <strong>admin123</strong></p>";
    echo "</div>";
    echo "</div>";
} else {
    echo "<div class='warning'>⚠️ ฐานข้อมูลอาจไม่สมบูรณ์</div>";
}

$conn->close();
echo "</div></div></body></html>";
?>

