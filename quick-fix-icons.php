<?php
/**
 * แก้ไขไอคอนหมวดหมู่บทความแบบเร็ว
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

// กำหนดไอคอนให้แต่ละหมวดหมู่
$icon_updates = [
    1 => 'fas fa-newspaper',      // ข่าวสาร
    2 => 'fas fa-shirt',          // แฟชั่น
    3 => 'fas fa-heart',          // ไลฟ์สไตล์
    4 => 'fas fa-spa',            // ความสวยความงาม
    5 => 'fas fa-camera',         // การถ่ายภาพ
    6 => 'fas fa-calendar',       // อีเวนต์
    7 => 'fas fa-film',           // เบื้องหลัง
    8 => 'fas fa-lightbulb'       // เคล็ดลับ
];

echo "<h1>🔧 แก้ไขไอคอนหมวดหมู่บทความ</h1>";

$success_count = 0;
$error_count = 0;

foreach ($icon_updates as $category_id => $icon) {
    try {
        $stmt = $conn->prepare("UPDATE article_categories SET icon = ? WHERE id = ?");
        $stmt->bind_param("si", $icon, $category_id);
        
        if ($stmt->execute()) {
            echo "<p>✅ อัพเดทหมวดหมู่ ID {$category_id}: {$icon}</p>";
            $success_count++;
        } else {
            echo "<p>❌ เกิดข้อผิดพลาดหมวดหมู่ ID {$category_id}: " . $stmt->error . "</p>";
            $error_count++;
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo "<p>❌ Exception หมวดหมู่ ID {$category_id}: " . $e->getMessage() . "</p>";
        $error_count++;
    }
}

echo "<hr>";
echo "<h2>📊 ผลลัพธ์:</h2>";
echo "<p>✅ สำเร็จ: {$success_count} รายการ</p>";
echo "<p>❌ ล้มเหลว: {$error_count} รายการ</p>";

if ($success_count > 0) {
    echo "<p style='color: green; font-weight: bold;'>🎉 แก้ไขไอคอนสำเร็จ! ตอนนี้ไอคอนควรแสดงในหน้าบทความแล้ว</p>";
}

echo "<hr>";
echo "<h2>🔍 ตรวจสอบผลลัพธ์:</h2>";

// ตรวจสอบว่าอัพเดทสำเร็จหรือไม่
$categories = db_get_rows($conn, "SELECT id, name, icon FROM article_categories ORDER BY id ASC");

echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
echo "<tr><th>ID</th><th>ชื่อหมวดหมู่</th><th>ไอคอน</th><th>สถานะ</th></tr>";

foreach ($categories as $cat) {
    $icon_status = !empty($cat['icon']) ? '✅ มีไอคอน' : '❌ ไม่มีไอคอน';
    echo "<tr>";
    echo "<td>" . $cat['id'] . "</td>";
    echo "<td>" . htmlspecialchars($cat['name']) . "</td>";
    echo "<td>" . htmlspecialchars($cat['icon'] ?? 'ไม่มี') . "</td>";
    echo "<td>{$icon_status}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";
echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='articles.php' style='display: inline-block; background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin: 10px;'>ดูหน้าบทความ</a>";
echo "<a href='debug-article-categories-icons.php' style='display: inline-block; background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin: 10px;'>ตรวจสอบไอคอน</a>";
echo "</div>";

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>แก้ไขไอคอน</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        h1 { color: #667eea; }
        h2 { color: #DC2626; }
        table { background: white; }
        th, td { padding: 10px; text-align: left; }
        th { background: #667eea; color: white; }
    </style>
</head>
</html>


