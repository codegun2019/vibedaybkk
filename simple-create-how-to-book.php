<?php
/**
 * สร้าง How to Book Section แบบง่าย (ไม่เพิ่มคอลัมน์ใหม่)
 */

// การตั้งค่าฐานข้อมูล
$host = 'localhost';
$dbname = 'vibedaybkk';
$username = 'root';
$password = 'root';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🚀 กำลังสร้าง How to Book Section...</h2>";
    
    // ตรวจสอบว่า section มีอยู่แล้วหรือไม่
    $stmt = $conn->prepare("SELECT * FROM homepage_sections WHERE section_key = 'how-to-book'");
    $stmt->execute();
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        echo "<h3>✅ How to Book Section มีอยู่แล้ว!</h3>";
        echo "<p><strong>Section ID:</strong> " . $existing['id'] . "</p>";
        echo "<p><strong>Title:</strong> " . htmlspecialchars($existing['title']) . "</p>";
        echo "<p><strong>Sort Order:</strong> " . $existing['sort_order'] . "</p>";
        echo "<p><strong>Active:</strong> " . ($existing['is_active'] ? '✅ เปิดใช้งาน' : '❌ ปิดใช้งาน') . "</p>";
        echo "<p><a href='admin/homepage/edit.php?id={$existing['id']}' target='_blank' style='background: #DC2626; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>แก้ไข How to Book Section</a></p>";
        echo "<p><a href='index.php#how-to-book' target='_blank' style='background: #059669; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>ดูผลลัพธ์ในหน้าแรก</a></p>";
    } else {
        echo "<h3>🔧 กำลังสร้าง How to Book Section ใหม่...</h3>";
        
        // ตรวจสอบโครงสร้างตาราง
        $stmt = $conn->query("DESCRIBE homepage_sections");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<h4>📋 คอลัมน์ที่มีอยู่ในตาราง:</h4>";
        echo "<ul>";
        foreach ($columns as $column) {
            echo "<li>$column</li>";
        }
        echo "</ul>";
        
        // สร้าง How to Book section ใหม่ (ใช้เฉพาะคอลัมน์พื้นฐาน)
        $sql = "INSERT INTO homepage_sections (
            section_key, title, subtitle, content, 
            button1_text, button1_link, button2_text, button2_link,
            background_type, background_color, background_image,
            background_position, background_size, background_repeat, background_attachment,
            sort_order, is_active,
            created_at, updated_at
        ) VALUES (
            :section_key, :title, :subtitle, :content,
            :button1_text, :button1_link, :button2_text, :button2_link,
            :background_type, :background_color, :background_image,
            :background_position, :background_size, :background_repeat, :background_attachment,
            :sort_order, :is_active,
            :created_at, :updated_at
        )";
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([
            ':section_key' => 'how-to-book',
            ':title' => 'วิธีการจองบริการ',
            ':subtitle' => '',
            ':content' => 'ขั้นตอนการจองบริการที่ง่ายและรวดเร็ว เพียง 4 ขั้นตอนคุณก็สามารถจองโมเดลมืออาชีพได้แล้ว

ขั้นตอนที่ 1: เลือกบริการ - เลือกประเภทโมเดลและบริการที่ต้องการ
ขั้นตอนที่ 2: ติดต่อเรา - ติดต่อผ่าน Line หรือโทรศัพท์เพื่อปรึกษารายละเอียด  
ขั้นตอนที่ 3: ยืนยันการจอง - ยืนยันรายละเอียดและชำระเงินมัดจำ
ขั้นตอนที่ 4: เริ่มงาน - โมเดลจะมาถึงสถานที่ตามเวลาที่กำหนด',
            ':button1_text' => 'เริ่มจองเลย',
            ':button1_link' => '#contact',
            ':button2_text' => '',
            ':button2_link' => '',
            ':background_type' => 'color',
            ':background_color' => '',
            ':background_image' => '',
            ':background_position' => 'center',
            ':background_size' => 'cover',
            ':background_repeat' => 'no-repeat',
            ':background_attachment' => 'scroll',
            ':sort_order' => 40,
            ':is_active' => 1,
            ':created_at' => date('Y-m-d H:i:s'),
            ':updated_at' => date('Y-m-d H:i:s')
        ]);
        
        if ($result) {
            $new_id = $conn->lastInsertId();
            echo "<h3>✅ สร้าง How to Book Section สำเร็จ!</h3>";
            echo "<p><strong>Section ID:</strong> " . $new_id . "</p>";
            echo "<p><strong>Title:</strong> วิธีการจองบริการ</p>";
            echo "<p><strong>Content:</strong> รวมขั้นตอนทั้ง 4 ขั้นตอนในเนื้อหา</p>";
            echo "<p><a href='admin/homepage/edit.php?id={$new_id}' target='_blank' style='background: #DC2626; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>แก้ไข How to Book Section</a></p>";
            echo "<p><a href='index.php#how-to-book' target='_blank' style='background: #059669; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>ดูผลลัพธ์ในหน้าแรก</a></p>";
        } else {
            echo "<h3>❌ เกิดข้อผิดพลาด!</h3>";
            echo "<p>ไม่สามารถสร้าง section ได้</p>";
        }
    }
    
    // แสดงข้อมูล Homepage Sections ทั้งหมด
    echo "<hr>";
    echo "<h3>📋 ข้อมูล Homepage Sections ทั้งหมด:</h3>";
    
    $stmt = $conn->prepare("SELECT * FROM homepage_sections ORDER BY sort_order");
    $stmt->execute();
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($sections)) {
        echo "<p>❌ ไม่พบข้อมูล sections</p>";
    } else {
        echo "<table style='width: 100%; border-collapse: collapse; margin-top: 20px;'>";
        echo "<tr style='background: #DC2626; color: white;'>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>ID</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Section Key</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Title</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Sort Order</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Active</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Actions</th>";
        echo "</tr>";
        
        foreach ($sections as $section) {
            $active_text = $section['is_active'] ? '✅' : '❌';
            echo "<tr style='border-bottom: 1px solid #ddd;'>";
            echo "<td style='padding: 12px; border: 1px solid #ddd;'>{$section['id']}</td>";
            echo "<td style='padding: 12px; border: 1px solid #ddd;'>{$section['section_key']}</td>";
            echo "<td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($section['title']) . "</td>";
            echo "<td style='padding: 12px; border: 1px solid #ddd;'>{$section['sort_order']}</td>";
            echo "<td style='padding: 12px; border: 1px solid #ddd;'>{$active_text}</td>";
            echo "<td style='padding: 12px; border: 1px solid #ddd;'>";
            echo "<a href='admin/homepage/edit.php?id={$section['id']}' target='_blank' style='background: #DC2626; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 12px;'>แก้ไข</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (PDOException $e) {
    echo "<h3>❌ เกิดข้อผิดพลาด!</h3>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>แก้ไข:</strong> ตรวจสอบการตั้งค่าฐานข้อมูลใน MAMP</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    padding: 20px;
    background: #f5f5f5;
    line-height: 1.6;
    max-width: 1200px;
    margin: 0 auto;
}
h2, h3, h4 {
    color: #333;
    margin: 20px 0 10px;
}
table {
    background: #fff;
    border-radius: 5px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-top: 20px;
}
th, td {
    padding: 12px;
    text-align: left;
    border: 1px solid #ddd;
}
th {
    background: #DC2626;
    color: white;
    font-weight: bold;
}
tr:hover {
    background: #f9f9f9;
}
a {
    color: white;
    text-decoration: none;
    font-weight: bold;
}
a:hover {
    opacity: 0.8;
}
hr {
    border: none;
    border-top: 2px solid #DC2626;
    margin: 30px 0;
}
ul {
    background: #fff;
    padding: 15px 30px;
    border-radius: 5px;
    border-left: 4px solid #DC2626;
}
li {
    margin: 5px 0;
    font-family: monospace;
    color: #666;
}
</style>
