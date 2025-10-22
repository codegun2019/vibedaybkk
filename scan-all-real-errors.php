<?php
/**
 * สแกนหา error ทุกไฟล์จริงจัง
 */
error_reporting(0);
set_time_limit(300);

$baseUrl = 'http://localhost:8888/vibedaybkk';
$errors = [];
$tested = 0;

// ไฟล์ใน admin ที่ต้องทดสอบ
$adminFiles = [
    // Core
    'admin/index.php',
    'admin/login.php',
    'admin/dashboard.php',
    'admin/logout.php',
    
    // Models
    'admin/models/index.php',
    'admin/models/add.php',
    
    // Categories
    'admin/categories/index.php',
    'admin/categories/add.php',
    
    // Articles
    'admin/articles/index.php',
    'admin/articles/add.php',
    
    // Article Categories
    'admin/article-categories/index.php',
    'admin/article-categories/add.php',
    
    // Users
    'admin/users/index.php',
    'admin/users/add.php',
    
    // Menus
    'admin/menus/index.php',
    'admin/menus/add.php',
    
    // Bookings
    'admin/bookings/index.php',
    
    // Contacts
    'admin/contacts/index.php',
    
    // Gallery
    'admin/gallery/index.php',
    'admin/gallery/albums.php',
    'admin/gallery/upload.php',
    
    // Homepage
    'admin/homepage/index.php',
    'admin/homepage/edit.php?id=1',
    'admin/homepage/content.php',
    'admin/homepage/features.php',
    'admin/homepage/gallery.php',
    
    // Settings
    'admin/settings/index.php',
    'admin/settings/seo.php',
    'admin/settings/social.php',
    
    // Roles
    'admin/roles/index.php',
    
    // Reviews
    'admin/reviews/index.php',
];

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; background: #000; color: #0f0; padding: 20px; }";
echo "h1 { color: #0ff; }";
echo ".error { background: #f00; color: #fff; padding: 10px; margin: 5px 0; border-radius: 5px; }";
echo ".success { background: #0f0; color: #000; padding: 10px; margin: 5px 0; border-radius: 5px; }";
echo ".testing { background: #333; padding: 10px; margin: 5px 0; border-left: 3px solid #0ff; }";
echo "pre { background: #222; padding: 10px; border-radius: 5px; overflow-x: auto; font-size: 0.9rem; }";
echo "</style></head><body>";

echo "<h1>🔍 สแกนหา Error ทุกไฟล์จริงจัง</h1>";

foreach ($adminFiles as $file) {
    $tested++;
    $url = $baseUrl . '/' . $file;
    
    echo "<div class='testing'>";
    echo "<strong>[$tested] Testing:</strong> $file<br>";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $hasError = false;
    $errorDetails = [];
    
    // ตรวจหา Fatal errors
    if (preg_match('/<b>Fatal error<\/b>:\s*(.+?)<br/', $response, $matches)) {
        $hasError = true;
        $errorDetails[] = 'Fatal: ' . strip_tags($matches[1]);
    }
    
    // ตรวจหา Warnings
    if (preg_match_all('/<b>Warning<\/b>:\s*(.+?)<br/', $response, $matches)) {
        $hasError = true;
        foreach ($matches[1] as $warn) {
            $errorDetails[] = 'Warning: ' . strip_tags($warn);
        }
    }
    
    // ตรวจหา Deprecated
    if (preg_match_all('/<b>Deprecated<\/b>:\s*(.+?)<br/', $response, $matches)) {
        $hasError = true;
        foreach ($matches[1] as $dep) {
            $errorDetails[] = 'Deprecated: ' . strip_tags($dep);
        }
    }
    
    // ตรวจหน้าว่าง
    if (strlen($response) < 100) {
        $hasError = true;
        $errorDetails[] = 'Blank page (' . strlen($response) . ' bytes)';
    }
    
    if ($hasError) {
        echo "<div class='error'>❌ ERROR FOUND!</div>";
        foreach ($errorDetails as $err) {
            echo "<pre>" . htmlspecialchars($err) . "</pre>";
        }
        
        $errors[] = [
            'file' => $file,
            'url' => $url,
            'errors' => $errorDetails
        ];
    } else {
        echo "<span style='color: #0f0;'>✅ OK</span>";
    }
    
    echo "</div>";
    
    flush();
}

// สรุป
echo "<h1 style='margin-top: 40px;'>📊 สรุปผลการสแกน</h1>";

echo "<div style='background: #333; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2 style='color: #0ff;'>สถิติ:</h2>";
echo "<p>✅ ทดสอบทั้งหมด: <strong>{$tested}</strong> ไฟล์</p>";
echo "<p>❌ พบ Error: <strong>" . count($errors) . "</strong> ไฟล์</p>";
$rate = $tested > 0 ? round((($tested - count($errors)) / $tested) * 100) : 0;
echo "<p>📊 อัตราผ่าน: <strong>{$rate}%</strong></p>";
echo "</div>";

if (count($errors) > 0) {
    echo "<div class='error'>";
    echo "<h2>❌ ไฟล์ที่มีปัญหา (" . count($errors) . " ไฟล์):</h2>";
    echo "<ol>";
    foreach ($errors as $err) {
        echo "<li>";
        echo "<strong>{$err['file']}</strong><br>";
        echo "URL: <code>{$err['url']}</code><br>";
        echo "Errors:<br>";
        foreach ($err['errors'] as $e) {
            echo "<pre>" . htmlspecialchars($e) . "</pre>";
        }
        echo "</li>";
    }
    echo "</ol>";
    echo "</div>";
} else {
    echo "<div class='success'>";
    echo "<h2>🎉 ไม่พบ Error!</h2>";
    echo "<p>ทุกไฟล์ทำงานได้ปกติ</p>";
    echo "</div>";
}

echo "</body></html>";
?>


