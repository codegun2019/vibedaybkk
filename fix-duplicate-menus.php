<?php
/**
 * แก้ไขเมนูซ้ำซ้อนในหน้าแรก
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<h2>🔧 แก้ไขเมนูซ้ำซ้อน</h2>";

// ตรวจสอบเมนูในฐานข้อมูล
$menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC");

echo "<h3>📋 เมนูในฐานข้อมูล:</h3>";
echo "<table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>";
echo "<tr style='background: #DC2626; color: white;'>";
echo "<th style='padding: 10px; border: 1px solid #ddd;'>ID</th>";
echo "<th style='padding: 10px; border: 1px solid #ddd;'>Title</th>";
echo "<th style='padding: 10px; border: 1px solid #ddd;'>URL</th>";
echo "<th style='padding: 10px; border: 1px solid #ddd;'>Icon</th>";
echo "<th style='padding: 10px; border: 1px solid #ddd;'>Sort Order</th>";
echo "<th style='padding: 10px; border: 1px solid #ddd;'>Status</th>";
echo "</tr>";

foreach ($menus as $menu) {
    $status_color = $menu['status'] == 'active' ? 'green' : 'red';
    echo "<tr>";
    echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$menu['id']}</td>";
    echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($menu['title']) . "</td>";
    echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($menu['url']) . "</td>";
    echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($menu['icon']) . "</td>";
    echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$menu['sort_order']}</td>";
    echo "<td style='padding: 10px; border: 1px solid #ddd; color: {$status_color};'>{$menu['status']}</td>";
    echo "</tr>";
}

echo "</table>";

// แนะนำเมนูที่ควรมี
echo "<h3>💡 เมนูที่แนะนำ:</h3>";
echo "<ul>";
echo "<li>หน้าแรก - index.php</li>";
echo "<li>โมเดล - models.php</li>";
echo "<li>บทความ - articles.php</li>";
echo "<li>ผลงาน - gallery.php</li>";
echo "<li>ติดต่อ - #contact</li>";
echo "</ul>";

echo "<hr>";

// สร้างเมนูที่แนะนำ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_recommended_menus'])) {
    echo "<h3>🚀 กำลังสร้างเมนูที่แนะนำ...</h3>";
    
    // ลบเมนูเก่า
    $conn->query("DELETE FROM menus WHERE parent_id IS NULL");
    
    // สร้างเมนูใหม่
    $recommended_menus = [
        ['title' => 'หน้าแรก', 'url' => 'index.php', 'icon' => 'fa-home', 'sort_order' => 1],
        ['title' => 'โมเดล', 'url' => 'models.php', 'icon' => 'fa-users', 'sort_order' => 2],
        ['title' => 'บทความ', 'url' => 'articles.php', 'icon' => 'fa-newspaper', 'sort_order' => 3],
        ['title' => 'ผลงาน', 'url' => 'gallery.php', 'icon' => 'fa-images', 'sort_order' => 4],
        ['title' => 'ติดต่อ', 'url' => '#contact', 'icon' => 'fa-envelope', 'sort_order' => 5]
    ];
    
    foreach ($recommended_menus as $menu) {
        $sql = "INSERT INTO menus (title, url, icon, sort_order, status, created_at, updated_at) VALUES (?, ?, ?, ?, 'active', NOW(), NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$menu['title'], $menu['url'], $menu['icon'], $menu['sort_order']]);
        echo "<p>✅ สร้างเมนู: " . htmlspecialchars($menu['title']) . "</p>";
    }
    
    echo "<p><strong>🎉 สร้างเมนูเสร็จแล้ว!</strong></p>";
    echo "<p><a href='index.php' target='_blank' style='background: #DC2626; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>ดูผลลัพธ์ในหน้าแรก</a></p>";
}

echo "<form method='POST' style='margin: 20px 0;'>";
echo "<button type='submit' name='create_recommended_menus' style='background: #DC2626; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;'>สร้างเมนูที่แนะนำ</button>";
echo "</form>";

echo "<hr>";
echo "<h3>📝 หมายเหตุ:</h3>";
echo "<ul>";
echo "<li>เมนูในหน้าแรกจะดึงจากฐานข้อมูลเท่านั้น</li>";
echo "<li>ไม่ควรมีเมนู hard-coded ในโค้ด</li>";
echo "<li>เมนูใน footer จะใช้เมนูหลัก + เมนูเพิ่มเติม</li>";
echo "<li>ควรตรวจสอบเมนูให้สอดคล้องกันทั้งหน้า</li>";
echo "</ul>";
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
h2, h3 {
    color: #333;
    margin: 20px 0 10px;
}
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 5px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
th, td {
    padding: 10px;
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
ul {
    background: white;
    padding: 20px 40px;
    border-radius: 5px;
    border-left: 4px solid #DC2626;
}
li {
    margin: 8px 0;
}
a {
    color: white;
    text-decoration: none;
    font-weight: bold;
}
a:hover {
    opacity: 0.8;
}
button:hover {
    opacity: 0.8;
}
</style>