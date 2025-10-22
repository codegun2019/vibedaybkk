<?php
/**
 * อัปเดต Notifications ทุกหน้าให้ใช้ SweetAlert2
 */

$files_to_update = [
    'admin/models/edit.php',
    'admin/categories/edit.php',
    'admin/menus/edit.php',
    'admin/articles/edit.php',
    'admin/reviews/edit.php',
    'admin/article-categories/edit.php',
    'admin/users/edit.php',
    'admin/settings/appearance.php'
];

echo "<h2>🔄 กำลังอัปเดต Notification System...</h2>";
echo "<p>จำนวนไฟล์ที่ต้องอัปเดต: " . count($files_to_update) . " ไฟล์</p>";
echo "<hr>";

$updated = 0;
$skipped = 0;

foreach ($files_to_update as $file) {
    $file_path = __DIR__ . '/' . $file;
    
    if (!file_exists($file_path)) {
        echo "<p style='color: orange;'>⚠️ ข้าม: {$file} (ไม่พบไฟล์)</p>";
        $skipped++;
        continue;
    }
    
    $content = file_get_contents($file_path);
    $original_content = $content;
    
    // Pattern 1: แทนที่ success alert box
    $content = preg_replace(
        '/<\?php if \(\$success\): \?>\s*<div class="bg-green-100 border border-green-400[^>]*>.*?<\/div>\s*<\?php endif; \?>/s',
        '<!-- Success notification จะแสดงผ่าน SweetAlert2 -->',
        $content
    );
    
    // Pattern 2: แทนที่ error alert box
    $content = preg_replace(
        '/<\?php if \(!empty\(\$errors\)\): \?>\s*<div class="bg-red-100 border border-red-400[^>]*>.*?<\/div>\s*<\?php endif; \?>/s',
        '<!-- Error notification จะแสดงผ่าน SweetAlert2 -->',
        $content
    );
    
    if ($content !== $original_content) {
        file_put_contents($file_path, $content);
        echo "<p style='color: green;'>✅ อัปเดต: {$file}</p>";
        $updated++;
    } else {
        echo "<p style='color: gray;'>➖ ไม่มีการเปลี่ยนแปลง: {$file}</p>";
    }
}

echo "<hr>";
echo "<h3>📊 สรุปผลการอัปเดต:</h3>";
echo "<ul>";
echo "<li><strong>อัปเดตแล้ว:</strong> {$updated} ไฟล์</li>";
echo "<li><strong>ข้าม:</strong> {$skipped} ไฟล์</li>";
echo "<li><strong>ทั้งหมด:</strong> " . count($files_to_update) . " ไฟล์</li>";
echo "</ul>";

echo "<hr>";
echo "<h3>✨ Notification System ใหม่:</h3>";
echo "<ul>";
echo "<li>✅ ใช้ <strong>SweetAlert2</strong> แทน alert box แบบเดิม</li>";
echo "<li>✅ แสดง <strong>Toast notification</strong> มุมขวาบน</li>";
echo "<li>✅ มี <strong>Animation</strong> สวยงาม (Animate.css)</li>";
echo "<li>✅ มี <strong>Progress bar</strong> แสดงเวลาที่เหลือ</li>";
echo "<li>✅ รองรับ: Success, Error, Warning, Info</li>";
echo "<li>✅ หยุด timer เมื่อ hover (UX ดีขึ้น)</li>";
echo "</ul>";

echo "<hr>";
echo "<h3>🎯 วิธีใช้งาน:</h3>";
echo "<pre>";
echo htmlspecialchars('
// ในไฟล์ PHP
require_once __DIR__ . \'/../includes/notification.php\';

// ตั้งค่า notification
set_success_message(\'บันทึกข้อมูลสำเร็จ!\');
set_error_message(\'เกิดข้อผิดพลาด!\');
set_warning_message(\'คำเตือน!\');
set_info_message(\'ข้อมูล\');

// Redirect
header("Location: index.php");
exit;
');
echo "</pre>";

echo "<p><a href='admin/homepage/edit.php?id=1' target='_blank'>ทดสอบ Notification ที่หน้า Edit Homepage</a></p>";
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
ul {
    background: #fff;
    padding: 20px 40px;
    border-radius: 5px;
    margin: 10px 0;
}
li {
    margin: 8px 0;
}
pre {
    background: #2d2d2d;
    color: #fff;
    padding: 15px;
    border-radius: 5px;
    overflow-x: auto;
}
a {
    color: #DC2626;
    font-weight: bold;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style>

