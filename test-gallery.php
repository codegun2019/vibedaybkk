<?php
/**
 * Test Gallery Page - ตรวจสอบการทำงานของหน้า gallery.php
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<h1>VIBEDAYBKK Gallery Test</h1>";

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
echo "<h2>1. ตรวจสอบการเชื่อมต่อฐานข้อมูล</h2>";
if ($conn) {
    echo "✅ เชื่อมต่อฐานข้อมูลสำเร็จ<br>";
} else {
    echo "❌ เชื่อมต่อฐานข้อมูลล้มเหลว<br>";
    exit;
}

// ตรวจสอบตาราง gallery
echo "<h2>2. ตรวจสอบตาราง gallery</h2>";
$check_gallery = $conn->query("SHOW TABLES LIKE 'gallery'");
if ($check_gallery->num_rows > 0) {
    echo "✅ ตาราง gallery มีอยู่<br>";
    
    // ตรวจสอบโครงสร้างตาราง
    $gallery_structure = $conn->query("DESCRIBE gallery");
    echo "<h3>โครงสร้างตาราง gallery:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $gallery_structure->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // ตรวจสอบจำนวนข้อมูล
    $count_result = $conn->query("SELECT COUNT(*) as total FROM gallery");
    $total_images = $count_result->fetch_assoc()['total'];
    echo "<br>📊 จำนวนรูปภาพทั้งหมด: " . $total_images . " รูป<br>";
    
    // ตรวจสอบข้อมูลตัวอย่าง
    if ($total_images > 0) {
        echo "<h3>ข้อมูลตัวอย่าง (5 รายการแรก):</h3>";
        $sample_result = $conn->query("SELECT * FROM gallery LIMIT 5");
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Image</th><th>Category</th><th>Created At</th></tr>";
        while ($row = $sample_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . ($row['title'] ?? 'N/A') . "</td>";
            echo "<td>" . ($row['image'] ?? 'N/A') . "</td>";
            echo "<td>" . ($row['category'] ?? 'N/A') . "</td>";
            echo "<td>" . ($row['created_at'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "❌ ตาราง gallery ไม่มีอยู่<br>";
}

// ตรวจสอบการตั้งค่า
echo "<h2>3. ตรวจสอบการตั้งค่า</h2>";
$settings_result = $conn->query("SELECT * FROM settings");
$settings_count = $settings_result->num_rows;
echo "📊 จำนวนการตั้งค่า: " . $settings_count . " รายการ<br>";

if ($settings_count > 0) {
    echo "<h3>การตั้งค่าที่เกี่ยวข้อง:</h3>";
    $relevant_settings = ['site_name', 'logo_type', 'logo_text', 'logo_image', 'gototop_enabled'];
    while ($row = $settings_result->fetch_assoc()) {
        if (in_array($row['setting_key'], $relevant_settings)) {
            echo "• " . $row['setting_key'] . ": " . $row['setting_value'] . "<br>";
        }
    }
}

// ตรวจสอบเมนู
echo "<h2>4. ตรวจสอบเมนู</h2>";
$menus_result = $conn->query("SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC");
$menus_count = $menus_result->num_rows;
echo "📊 จำนวนเมนูหลัก: " . $menus_count . " รายการ<br>";

if ($menus_count > 0) {
    echo "<h3>เมนูที่มีอยู่:</h3>";
    while ($row = $menus_result->fetch_assoc()) {
        echo "• " . $row['title'] . " (" . $row['url'] . ")<br>";
    }
}

// ตรวจสอบโซเชียลมีเดีย
echo "<h2>5. ตรวจสอบโซเชียลมีเดีย</h2>";
$social_platforms = ['facebook', 'instagram', 'twitter', 'line', 'youtube', 'tiktok'];
foreach ($social_platforms as $platform) {
    $enabled = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'social_{$platform}_enabled'")->fetch_assoc();
    $url = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'social_{$platform}_url'")->fetch_assoc();
    
    if ($enabled && $enabled['setting_value'] == '1' && $url && !empty($url['setting_value'])) {
        echo "✅ {$platform}: " . $url['setting_value'] . "<br>";
    } else {
        echo "❌ {$platform}: ไม่เปิดใช้งานหรือไม่มี URL<br>";
    }
}

// ตรวจสอบไฟล์ที่จำเป็น
echo "<h2>6. ตรวจสอบไฟล์ที่จำเป็น</h2>";
$required_files = [
    'includes/config.php',
    'includes/functions.php',
    'gallery.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✅ {$file}: มีอยู่<br>";
    } else {
        echo "❌ {$file}: ไม่มีอยู่<br>";
    }
}

// ตรวจสอบโฟลเดอร์ uploads
echo "<h2>7. ตรวจสอบโฟลเดอร์ uploads</h2>";
if (is_dir('uploads')) {
    echo "✅ โฟลเดอร์ uploads มีอยู่<br>";
    $upload_files = scandir('uploads');
    $image_files = array_filter($upload_files, function($file) {
        return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    });
    echo "📊 จำนวนไฟล์รูปภาพใน uploads: " . count($image_files) . " ไฟล์<br>";
} else {
    echo "❌ โฟลเดอร์ uploads ไม่มีอยู่<br>";
}

echo "<h2>8. สรุป</h2>";
echo "<p>การทดสอบเสร็จสิ้น กรุณาตรวจสอบผลลัพธ์ด้านบน</p>";
echo "<p><a href='gallery.php'>ไปที่หน้า Gallery</a></p>";

$conn->close();
?>
