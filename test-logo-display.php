<?php
/**
 * ทดสอบการแสดง Logo
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<h1>🔍 ทดสอบการแสดง Logo</h1>";

// ดึงการตั้งค่า
$global_settings = [];
$result = db_get_rows($conn, "SELECT * FROM settings");
foreach ($result as $row) {
    $global_settings[$row['setting_key']] = $row['setting_value'];
}

echo "<h2>📋 การตั้งค่า Logo:</h2>";

echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
echo "<tr><th>Setting Key</th><th>Value</th><th>Status</th></tr>";

// ตรวจสอบการตั้งค่า logo
$logo_keys = ['logo_image', 'logo_text', 'site_name'];
foreach ($logo_keys as $key) {
    $value = $global_settings[$key] ?? 'NOT SET';
    $status = !empty($value) && $value !== 'NOT SET' ? '✅ มีค่า' : '❌ ไม่มีค่า';
    
    echo "<tr>";
    echo "<td>" . $key . "</td>";
    echo "<td>" . htmlspecialchars($value) . "</td>";
    echo "<td>" . $status . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>🖼️ ทดสอบการแสดง Logo:</h2>";

// ทดสอบ logo image
if (!empty($global_settings['logo_image'])) {
    $logo_path = UPLOADS_URL . '/' . $global_settings['logo_image'];
    echo "<div style='margin: 20px 0;'>";
    echo "<h3>Logo Image:</h3>";
    echo "<p><strong>Path:</strong> " . htmlspecialchars($logo_path) . "</p>";
    echo "<p><strong>File exists:</strong> " . (file_exists($_SERVER['DOCUMENT_ROOT'] . '/vibedaybkk/uploads/' . $global_settings['logo_image']) ? '✅ ใช่' : '❌ ไม่') . "</p>";
    echo "<div style='border: 2px solid #ccc; padding: 20px; display: inline-block;'>";
    echo "<img src='" . htmlspecialchars($logo_path) . "' alt='Logo' style='max-height: 100px; max-width: 300px;' onerror='this.style.display=\"none\"; this.nextElementSibling.style.display=\"block\";'>";
    echo "<div style='display: none; color: red;'>❌ ไม่สามารถโหลดรูปภาพได้</div>";
    echo "</div>";
    echo "</div>";
} else {
    echo "<p>❌ ไม่มีการตั้งค่า logo_image</p>";
}

// ทดสอบ logo text
if (!empty($global_settings['logo_text'])) {
    echo "<div style='margin: 20px 0;'>";
    echo "<h3>Logo Text:</h3>";
    echo "<div style='border: 2px solid #ccc; padding: 20px; display: inline-block;'>";
    echo "<h1 style='color: #DC2626; font-size: 2rem; font-weight: bold; margin: 0;'>" . htmlspecialchars($global_settings['logo_text']) . "</h1>";
    echo "</div>";
    echo "</div>";
} else {
    echo "<p>❌ ไม่มีการตั้งค่า logo_text</p>";
}

// ทดสอบ site_name
if (!empty($global_settings['site_name'])) {
    echo "<div style='margin: 20px 0;'>";
    echo "<h3>Site Name (Fallback):</h3>";
    echo "<div style='border: 2px solid #ccc; padding: 20px; display: inline-block;'>";
    echo "<h1 style='color: #DC2626; font-size: 2rem; font-weight: bold; margin: 0;'>" . htmlspecialchars($global_settings['site_name']) . "</h1>";
    echo "</div>";
    echo "</div>";
} else {
    echo "<p>❌ ไม่มีการตั้งค่า site_name</p>";
}

echo "<h2>🔧 ข้อมูลเพิ่มเติม:</h2>";
echo "<p><strong>UPLOADS_URL:</strong> " . (defined('UPLOADS_URL') ? UPLOADS_URL : 'NOT DEFINED') . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Current Directory:</strong> " . __DIR__ . "</p>";

// ตรวจสอบโฟลเดอร์ uploads
$uploads_dir = $_SERVER['DOCUMENT_ROOT'] . '/vibedaybkk/uploads/';
echo "<p><strong>Uploads Directory:</strong> " . $uploads_dir . "</p>";
echo "<p><strong>Uploads Directory exists:</strong> " . (is_dir($uploads_dir) ? '✅ ใช่' : '❌ ไม่') . "</p>";

if (is_dir($uploads_dir)) {
    $files = scandir($uploads_dir);
    $image_files = array_filter($files, function($file) {
        return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    });
    
    echo "<p><strong>ไฟล์รูปภาพใน uploads:</strong></p>";
    if (!empty($image_files)) {
        echo "<ul>";
        foreach ($image_files as $file) {
            echo "<li>" . htmlspecialchars($file) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ ไม่พบไฟล์รูปภาพ</p>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ทดสอบการแสดง Logo</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        h1 { color: #667eea; }
        h2 { color: #DC2626; border-bottom: 2px solid #DC2626; padding-bottom: 10px; }
        table { margin: 20px 0; background: white; border-radius: 10px; overflow: hidden; }
        th, td { padding: 12px; text-align: left; }
        th { background: #667eea; color: white; }
        .btn { display: inline-block; background: #667eea; color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; margin: 10px 5px; }
        .btn:hover { background: #5558d9; }
    </style>
</head>
<body>
    <div style="text-align: center; margin: 30px 0;">
        <a href="models.php?category=2" class="btn">ดูหน้า Models</a>
        <a href="/" class="btn">ดูหน้าแรก</a>
        <a href="admin/settings/" class="btn">ตั้งค่า Logo</a>
        <a href="javascript:location.reload()" class="btn">รีเฟรช</a>
    </div>
</body>
</html>

<?php $conn->close(); ?>
