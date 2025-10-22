<?php
/**
 * ตรวจสอบไอคอนหมวดหมู่บทความ
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<h1>🔍 ตรวจสอบไอคอนหมวดหมู่บทความ</h1>";

// ตรวจสอบตาราง article_categories
$categories = db_get_rows($conn, "SELECT * FROM article_categories ORDER BY id ASC");

echo "<h2>📋 ข้อมูลหมวดหมู่ทั้งหมด:</h2>";

if (!empty($categories)) {
    echo "<p>พบหมวดหมู่ <strong>" . count($categories) . "</strong> รายการ</p>";
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Icon</th><th>ตัวอย่างไอคอน</th><th>Color</th><th>Status</th></tr>";
    
    foreach ($categories as $cat) {
        $icon_display = !empty($cat['icon']) ? $cat['icon'] : '❌ ไม่มี';
        $icon_preview = !empty($cat['icon']) ? '<i class="' . htmlspecialchars($cat['icon']) . '" style="font-size: 24px;"></i>' : '-';
        $color_display = !empty($cat['color']) ? '<span style="background: ' . htmlspecialchars($cat['color']) . '; color: white; padding: 5px 10px; border-radius: 5px;">' . htmlspecialchars($cat['color']) . '</span>' : '❌ ไม่มี';
        
        echo "<tr>";
        echo "<td>" . $cat['id'] . "</td>";
        echo "<td>" . htmlspecialchars($cat['name']) . "</td>";
        echo "<td>" . htmlspecialchars($icon_display) . "</td>";
        echo "<td>" . $icon_preview . "</td>";
        echo "<td>" . $color_display . "</td>";
        echo "<td>" . ($cat['status'] == 'active' ? '✅ Active' : '❌ Inactive') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>🔧 ตัวอย่างโค้ดที่ใช้แสดงไอคอน:</h2>";
    echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
    echo htmlspecialchars('<?php if (!empty($cat[\'icon\'])): ?>
    <i class="<?php echo h($cat[\'icon\']); ?>"></i>
<?php endif; ?>');
    echo "</pre>";
    
    echo "<h2>📝 หมวดหมู่ที่ไม่มีไอคอน:</h2>";
    $no_icon = array_filter($categories, function($cat) {
        return empty($cat['icon']);
    });
    
    if (!empty($no_icon)) {
        echo "<ul style='color: red;'>";
        foreach ($no_icon as $cat) {
            echo "<li><strong>" . htmlspecialchars($cat['name']) . "</strong> (ID: " . $cat['id'] . ")</li>";
        }
        echo "</ul>";
        echo "<p>⚠️ หมวดหมู่เหล่านี้ต้องเพิ่มไอคอนในหลังบ้าน</p>";
    } else {
        echo "<p style='color: green;'>✅ ทุกหมวดหมู่มีไอคอนแล้ว</p>";
    }
    
    echo "<h2>🎨 ตัวอย่างไอคอน Font Awesome ที่แนะนำ:</h2>";
    $suggested_icons = [
        'ข่าวสาร' => 'fas fa-newspaper',
        'แฟชั่น' => 'fas fa-shirt',
        'ไลฟ์สไตล์' => 'fas fa-heart',
        'ความสวยความงาม' => 'fas fa-spa',
        'การถ่ายภาพ' => 'fas fa-camera',
        'อีเวนต์' => 'fas fa-calendar',
        'เบื้องหลัง' => 'fas fa-film',
        'เคล็ดลับ' => 'fas fa-lightbulb'
    ];
    
    echo "<div style='display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;'>";
    foreach ($suggested_icons as $name => $icon) {
        echo "<div style='border: 1px solid #ddd; padding: 15px; border-radius: 8px; text-align: center;'>";
        echo "<i class='$icon' style='font-size: 36px; color: #667eea; margin-bottom: 10px;'></i>";
        echo "<p style='margin: 5px 0; font-weight: bold;'>$name</p>";
        echo "<code style='background: #f0f0f0; padding: 3px 8px; border-radius: 3px; font-size: 12px;'>$icon</code>";
        echo "</div>";
    }
    echo "</div>";
    
} else {
    echo "<p style='color: red;'>❌ ไม่พบหมวดหมู่ในฐานข้อมูล</p>";
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ตรวจสอบไอคอนหมวดหมู่</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        h1 { color: #667eea; }
        h2 { color: #DC2626; border-bottom: 2px solid #DC2626; padding-bottom: 10px; margin-top: 30px; }
        table { background: white; }
        th, td { padding: 12px; text-align: left; }
        th { background: #667eea; color: white; }
        .btn { display: inline-block; background: #667eea; color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; margin: 10px 5px; }
        .btn:hover { background: #5558d9; }
    </style>
</head>
<body>
    <div style="text-align: center; margin: 30px 0;">
        <a href="admin/article-categories/" class="btn">จัดการหมวดหมู่บทความ</a>
        <a href="articles.php" class="btn">ดูหน้าบทความ</a>
        <a href="/" class="btn">กลับหน้าแรก</a>
    </div>
</body>
</html>


