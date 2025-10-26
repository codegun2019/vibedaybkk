<?php
/**
 * Final Scan - ครั้งสุดท้าย
 */
error_reporting(0);
$baseUrl = 'http://localhost:8888/vibedaybkk';
$urls = [
    'admin/',
    'admin/models/', 'admin/models/add.php',
    'admin/categories/', 'admin/categories/add.php',
    'admin/articles/', 'admin/articles/add.php',
    'admin/article-categories/',
    'admin/users/', 'admin/users/add.php',
    'admin/menus/', 'admin/menus/add.php',
    'admin/bookings/',
    'admin/contacts/',
    'admin/gallery/', 'admin/gallery/albums.php', 'admin/gallery/upload.php',
    'admin/homepage/', 'admin/homepage/edit.php?id=1', 'admin/homepage/content.php',
    'admin/settings/', 'admin/settings/seo.php', 'admin/settings/social.php',
    'admin/roles/',
    'admin/reviews/',
];

$errors = [];
foreach ($urls as $path) {
    $ch = curl_init($baseUrl . '/' . $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    curl_close($ch);
    
    if (preg_match('/(Fatal|Parse|Warning.*Undefined|Deprecated)/i', $response)) {
        preg_match('/<b>(Fatal error|Parse error|Warning|Deprecated)<\/b>:\s*([^<]+)/', $response, $match);
        $errors[$path] = $match[2] ?? 'Unknown error';
    }
}

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><style>
body{font-family:Arial;background:#000;color:#0f0;padding:20px;}
h1{color:#0ff;font-size:2rem;}
.ok{background:#0f0;color:#000;padding:20px;border-radius:10px;margin:20px 0;}
.error{background:#f00;color:#fff;padding:20px;border-radius:10px;margin:20px 0;}
table{width:100%;border-collapse:collapse;margin:20px 0;}
th{background:#0ff;color:#000;padding:10px;text-align:left;}
td{padding:10px;border-bottom:1px solid #333;}
</style></head><body>";

echo "<h1>🔬 Final Scan Results</h1>";

if (empty($errors)) {
    echo "<div class='ok'>";
    echo "<h2 style='color:#000;margin:0 0 10px 0;'>🎉 ผ่านทั้งหมด!</h2>";
    echo "<p style='font-size:1.5rem;margin:0;'>✅ ทดสอบ " . count($urls) . " หน้า - ผ่าน 100%</p>";
    echo "<p style='margin:20px 0 0 0;'>❌ Error: 0 | ⚠️ Warning: 0</p>";
    echo "</div>";
    echo "<a href='admin/' style='display:inline-block;padding:20px 40px;background:#0ff;color:#000;text-decoration:none;border-radius:10px;font-weight:bold;font-size:1.2rem;'>👨‍💼 เข้าระบบ Admin</a>";
} else {
    echo "<div class='error'>";
    echo "<h2>❌ พบ Error " . count($errors) . " หน้า:</h2>";
    echo "<table>";
    echo "<tr><th>URL</th><th>Error</th></tr>";
    foreach ($errors as $url => $err) {
        echo "<tr><td>{$url}</td><td>" . htmlspecialchars($err) . "</td></tr>";
    }
    echo "</table>";
    echo "</div>";
}

echo "</body></html>";
?>




