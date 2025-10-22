<?php
/**
 * Setup About Section ในฐานข้อมูล
 */
require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<h2>ตรวจสอบ About Section:</h2>";

// ตรวจสอบว่ามีข้อมูล about section หรือไม่
$check = $conn->query("SELECT * FROM homepage_sections WHERE section_key = 'about'");

if ($check && $check->num_rows > 0) {
    echo "<p style='color: green;'>✅ มีข้อมูล About Section อยู่แล้ว</p>";
    $about = $check->fetch_assoc();
    echo "<pre>";
    print_r($about);
    echo "</pre>";
} else {
    echo "<p style='color: orange;'>⚠️ ยังไม่มีข้อมูล About Section</p>";
    echo "<p>กำลังสร้างข้อมูลเริ่มต้น...</p>";
    
    // สร้างข้อมูล about section
    $sql = "INSERT INTO homepage_sections (
        section_key, 
        title, 
        subtitle, 
        content,
        button1_text,
        button1_link,
        background_type,
        background_color,
        background_position,
        background_size,
        background_repeat,
        background_attachment,
        is_active,
        sort_order
    ) VALUES (
        'about',
        'เกี่ยวกับ VIBEDAYBKK',
        'บริษัทชั้นนำด้านบริการโมเดลและนางแบบ',
        'VIBEDAYBKK เป็นบริษัทชั้นนำด้านบริการโมเดลและนางแบบในกรุงเทพฯ เราให้บริการครบวงจรตั้งแต่การคัดสรรโมเดล การจัดการงาน ไปจนถึงการประสานงานในวันถ่ายทำ',
        'ติดต่อเรา',
        '#contact',
        'color',
        '#1a1a1a',
        'center',
        'cover',
        'no-repeat',
        'scroll',
        1,
        2
    )";
    
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>✅ สร้างข้อมูล About Section สำเร็จ!</p>";
        
        // แสดงข้อมูลที่สร้าง
        $about = $conn->query("SELECT * FROM homepage_sections WHERE section_key = 'about'")->fetch_assoc();
        echo "<pre>";
        print_r($about);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>❌ เกิดข้อผิดพลาด: " . $conn->error . "</p>";
    }
}

// ตรวจสอบว่ามี column left_image หรือไม่
echo "<hr>";
echo "<h3>ตรวจสอบ column left_image:</h3>";
$check_column = $conn->query("SHOW COLUMNS FROM homepage_sections LIKE 'left_image'");
if ($check_column && $check_column->num_rows > 0) {
    echo "<p style='color: green;'>✅ Column left_image มีอยู่แล้ว</p>";
} else {
    echo "<p style='color: orange;'>⚠️ ยังไม่มี column left_image</p>";
    echo "<p>กำลังเพิ่ม column...</p>";
    
    $add_column = "ALTER TABLE homepage_sections ADD COLUMN left_image VARCHAR(255) DEFAULT NULL AFTER background_image";
    if ($conn->query($add_column)) {
        echo "<p style='color: green;'>✅ เพิ่ม column left_image สำเร็จ!</p>";
    } else {
        echo "<p style='color: red;'>❌ เกิดข้อผิดพลาด: " . $conn->error . "</p>";
    }
}

echo "<hr>";
echo "<h3>ทดสอบการทำงาน:</h3>";
echo "<p>✅ ตอนนี้สามารถแก้ไข About Section ได้ที่: <a href='admin/homepage/edit.php?id=" . ($about['id'] ?? 2) . "' target='_blank'>แก้ไข About Section</a></p>";
echo "<p>✅ ดูหน้าแรกที่: <a href='index.php#about' target='_blank'>หน้าแรก - About Section</a></p>";

echo "<hr>";
echo "<h3>📝 คำแนะนำ:</h3>";
echo "<ul>";
echo "<li><strong>Title:</strong> สามารถใช้ 'เกี่ยวกับ VIBEDAYBKK' หรือเปลี่ยนได้</li>";
echo "<li><strong>Subtitle:</strong> หัวข้อรอง (optional)</li>";
echo "<li><strong>Content:</strong> เนื้อหาหลักของ About Section</li>";
echo "<li><strong>Left Image:</strong> รูปภาพด้านซ้าย (แนะนำ 600x600px หรือ 1200x1200px)</li>";
echo "<li><strong>Background:</strong> สามารถเลือกเป็นสีหรือรูปภาพได้</li>";
echo "<li><strong>Check List:</strong> รายการ bullet points (hard-coded สำหรับตอนนี้)</li>";
echo "</ul>";

$conn->close();
?>

<style>
body {
    font-family: Arial, sans-serif;
    padding: 20px;
    background: #f5f5f5;
    line-height: 1.6;
}
h2, h3 {
    color: #333;
}
pre {
    background: #fff;
    padding: 15px;
    border-radius: 5px;
    border: 1px solid #ddd;
    overflow-x: auto;
}
a {
    color: #DC2626;
    text-decoration: none;
    font-weight: bold;
}
a:hover {
    text-decoration: underline;
}
ul {
    background: #fff;
    padding: 20px 40px;
    border-radius: 5px;
    border: 1px solid #ddd;
}
li {
    margin-bottom: 10px;
}
</style>

