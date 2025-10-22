<?php
/**
 * สร้าง How to Book Section ในฐานข้อมูล
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

// ตรวจสอบว่า section มีอยู่แล้วหรือไม่
$existing = db_get_row($conn, "SELECT * FROM homepage_sections WHERE section_key = 'how-to-book'");

if ($existing) {
    echo "<h2>✅ How to Book Section มีอยู่แล้ว!</h2>";
    echo "<p>Section ID: " . $existing['id'] . "</p>";
    echo "<p>Title: " . htmlspecialchars($existing['title']) . "</p>";
    echo "<p><a href='admin/homepage/edit.php?id={$existing['id']}' target='_blank'>แก้ไข How to Book Section</a></p>";
} else {
    // สร้าง How to Book section ใหม่
    $default_steps = [
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
    
    $data = [
        'section_key' => 'how-to-book',
        'title' => 'วิธีการจองบริการ',
        'subtitle' => '',
        'content' => 'ขั้นตอนการจองบริการที่ง่ายและรวดเร็ว เพียง 4 ขั้นตอนคุณก็สามารถจองโมเดลมืออาชีพได้แล้ว',
        'button1_text' => 'เริ่มจองเลย',
        'button1_link' => '#contact',
        'button2_text' => '',
        'button2_link' => '',
        'background_type' => 'color',
        'background_color' => '',
        'background_image' => '',
        'left_image' => '',
        'right_image' => '',
        'background_position' => 'center',
        'background_size' => 'cover',
        'background_repeat' => 'no-repeat',
        'background_attachment' => 'scroll',
        'sort_order' => 40,
        'is_active' => 1,
        'steps' => json_encode($default_steps, JSON_UNESCAPED_UNICODE),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    // เพิ่ม columns ที่อาจไม่มี
    $alter_queries = [
        "ALTER TABLE homepage_sections ADD COLUMN IF NOT EXISTS steps TEXT AFTER right_image",
        "ALTER TABLE homepage_sections ADD COLUMN IF NOT EXISTS settings TEXT AFTER steps"
    ];
    
    foreach ($alter_queries as $query) {
        try {
            $conn->query($query);
        } catch (Exception $e) {
            // Column อาจมีอยู่แล้ว
        }
    }
    
    $result = db_insert($conn, 'homepage_sections', $data);
    
    if ($result) {
        $new_id = $conn->insert_id;
        echo "<h2>✅ สร้าง How to Book Section สำเร็จ!</h2>";
        echo "<p>Section ID: " . $new_id . "</p>";
        echo "<p>Title: " . htmlspecialchars($data['title']) . "</p>";
        echo "<p><a href='admin/homepage/edit.php?id={$new_id}' target='_blank'>แก้ไข How to Book Section</a></p>";
        echo "<p><a href='index.php#how-to-book' target='_blank'>ดูผลลัพธ์ในหน้าแรก</a></p>";
    } else {
        echo "<h2>❌ เกิดข้อผิดพลาด!</h2>";
        echo "<p>Error: " . $conn->error . "</p>";
    }
}

// แสดงข้อมูลทั้งหมด
echo "<hr>";
echo "<h3>📋 ข้อมูล Homepage Sections ทั้งหมด:</h3>";
$sections = db_get_rows($conn, "SELECT * FROM homepage_sections ORDER BY sort_order");
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ID</th><th>Section Key</th><th>Title</th><th>Sort Order</th><th>Active</th><th>Actions</th></tr>";
foreach ($sections as $section) {
    $active_text = $section['is_active'] ? '✅' : '❌';
    echo "<tr>";
    echo "<td>{$section['id']}</td>";
    echo "<td>{$section['section_key']}</td>";
    echo "<td>" . htmlspecialchars($section['title']) . "</td>";
    echo "<td>{$section['sort_order']}</td>";
    echo "<td>{$active_text}</td>";
    echo "<td><a href='admin/homepage/edit.php?id={$section['id']}' target='_blank'>แก้ไข</a></td>";
    echo "</tr>";
}
echo "</table>";
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
    margin: 20px 0 10px;
}
table {
    background: #fff;
    border-radius: 5px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
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
    color: #DC2626;
    text-decoration: none;
    font-weight: bold;
}
a:hover {
    text-decoration: underline;
}
</style>
