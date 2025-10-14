<?php
/**
 * Update Settings - เพิ่ม Logo และ Favicon Settings
 * รันไฟล์นี้ครั้งเดียวเพื่อเพิ่ม settings ใหม่
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

echo "<h2>🔧 Update Settings - เพิ่ม Logo และ Favicon</h2>";
echo "<hr>";

// Settings ที่ต้องเพิ่ม
$new_settings = [
    ['logo_type', 'text', 'text', 'ประเภทโลโก้ (text, image)'],
    ['logo_text', 'VIBEDAYBKK', 'text', 'ข้อความโลโก้'],
    ['logo_image', '', 'text', 'รูปภาพโลโก้'],
    ['favicon', '', 'text', 'Favicon'],
    ['facebook_url', '', 'text', 'Facebook URL'],
    ['instagram_url', '', 'text', 'Instagram URL'],
    ['twitter_url', '', 'text', 'Twitter/X URL']
];

$added = 0;
$skipped = 0;

foreach ($new_settings as $setting) {
    list($key, $value, $type, $desc) = $setting;
    
    // ตรวจสอบว่ามีอยู่แล้วหรือไม่
    $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $exists = $stmt->fetch()['c'] > 0;
    
    if (!$exists) {
        // เพิ่ม setting ใหม่
        $stmt = $pdo->prepare("
            INSERT INTO settings (setting_key, setting_value, setting_type, description) 
            VALUES (?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$key, $value, $type, $desc])) {
            echo "✅ เพิ่ม setting: <code>{$key}</code> = '{$value}'<br>";
            $added++;
        } else {
            echo "❌ เพิ่ม <code>{$key}</code> ไม่สำเร็จ<br>";
        }
    } else {
        echo "⏭️ มีอยู่แล้ว: <code>{$key}</code><br>";
        $skipped++;
    }
}

echo "<hr>";
echo "<h3>📊 สรุป:</h3>";
echo "<ul>";
echo "<li>✅ เพิ่มใหม่: <strong>{$added}</strong> รายการ</li>";
echo "<li>⏭️ มีอยู่แล้ว: <strong>{$skipped}</strong> รายการ</li>";
echo "</ul>";

if ($added > 0) {
    echo "<br><div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px;'>";
    echo "<h4 style='color: #155724; margin: 0;'>✅ อัพเดทสำเร็จ!</h4>";
    echo "<p style='color: #155724; margin: 10px 0 0 0;'>ตอนนี้คุณสามารถไปที่ <a href='admin/settings/'>Admin → Settings</a> เพื่อจัดการ Logo และ Favicon ได้แล้ว</p>";
    echo "</div>";
}

echo "<br><hr>";
echo "<h3>🔍 ตรวจสอบ Settings ปัจจุบัน:</h3>";

$all_settings = db_get_rows($conn, "SELECT * FROM settings ORDER BY setting_key ASC");
echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-top: 10px;'>";
echo "<tr style='background: #f8f9fa;'><th style='padding: 10px;'>Key</th><th style='padding: 10px;'>Value</th><th style='padding: 10px;'>Type</th><th style='padding: 10px;'>Description</th></tr>";
foreach ($all_settings as $s) {
    echo "<tr>";
    echo "<td style='padding: 8px;'><code>{$s['setting_key']}</code></td>";
    echo "<td style='padding: 8px;'>" . ($s['setting_value'] ?: '<em>ว่าง</em>') . "</td>";
    echo "<td style='padding: 8px;'>{$s['setting_type']}</td>";
    echo "<td style='padding: 8px;'>{$s['description']}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<br><hr>";
echo "<div style='text-align: center; margin-top: 20px;'>";
echo "<a href='admin/settings/' style='display: inline-block; background: #DC2626; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold;'>";
echo "<i class='fas fa-cog'></i> ไปที่หน้าตั้งค่า";
echo "</a>";
echo "</div>";

echo "<style>
body { font-family: 'Arial', sans-serif; padding: 20px; background: #f5f5f5; }
code { background: #e9ecef; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
h2 { color: #DC2626; }
h3 { color: #495057; margin-top: 20px; }
table { font-size: 14px; }
th { text-align: left; }
</style>";
?>

