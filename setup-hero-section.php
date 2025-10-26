<?php
/**
 * Setup Hero Section ในฐานข้อมูล
 */
require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<h2>ตรวจสอบ Hero Section:</h2>";

// ตรวจสอบว่ามีข้อมูล hero section หรือไม่
$check = $conn->query("SELECT * FROM homepage_sections WHERE section_key = 'hero'");

if ($check && $check->num_rows > 0) {
    echo "<p style='color: green;'>✅ มีข้อมูล Hero Section อยู่แล้ว</p>";
    $hero = $check->fetch_assoc();
    echo "<pre>";
    print_r($hero);
    echo "</pre>";
} else {
    echo "<p style='color: orange;'>⚠️ ยังไม่มีข้อมูล Hero Section</p>";
    echo "<p>กำลังสร้างข้อมูลเริ่มต้น...</p>";
    
    // สร้างข้อมูล hero section
    $sql = "INSERT INTO homepage_sections (
        section_key, 
        title, 
        subtitle, 
        content,
        button1_text,
        button1_link,
        button2_text,
        button2_link,
        background_type,
        background_color,
        background_position,
        background_size,
        background_repeat,
        background_attachment,
        is_active,
        sort_order
    ) VALUES (
        'hero',
        'VIBEDAYBKK',
        'บริการโมเดลและนางแบบมืออาชีพ',
        'เราคือผู้เชี่ยวชาญด้านบริการโมเดลและนางแบบคุณภาพสูง พร้อมให้บริการสำหรับงานถ่ายภาพ งานแฟชั่น และงานอีเวนต์ต่างๆ ด้วยทีมงานมืออาชีพและโมเดลที่ผ่านการคัดสรรอย่างดี',
        'จองบริการตอนนี้',
        '#contact',
        'ดูผลงาน',
        '#services',
        'color',
        '',
        'center',
        'cover',
        'no-repeat',
        'scroll',
        1,
        1
    )";
    
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>✅ สร้างข้อมูล Hero Section สำเร็จ!</p>";
        
        // แสดงข้อมูลที่สร้าง
        $hero = $conn->query("SELECT * FROM homepage_sections WHERE section_key = 'hero'")->fetch_assoc();
        echo "<pre>";
        print_r($hero);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>❌ เกิดข้อผิดพลาด: " . $conn->error . "</p>";
    }
}

echo "<hr>";
echo "<h3>ทดสอบการทำงาน:</h3>";
echo "<p>✅ ตอนนี้สามารถแก้ไข Hero Section ได้ที่: <a href='admin/homepage/edit.php?id=" . ($hero['id'] ?? 1) . "' target='_blank'>แก้ไข Hero Section</a></p>";
echo "<p>✅ ดูหน้าแรกที่: <a href='index.php' target='_blank'>หน้าแรก</a></p>";

$conn->close();
?>

<style>
body {
    font-family: Arial, sans-serif;
    padding: 20px;
    background: #f5f5f5;
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
</style>



