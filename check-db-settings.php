<?php
/**
 * ตรวจสอบ settings ในฐานข้อมูล
 */

require_once 'includes/config.php';

echo "<h2>ตรวจสอบ Settings ในฐานข้อมูล</h2>";

// ตรวจสอบการตั้งค่าที่เกี่ยวข้องกับราคา
$query = "SELECT * FROM settings WHERE setting_key LIKE '%price%' OR setting_key LIKE '%homepage%' ORDER BY setting_key";
$result = $conn->query($query);

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Setting Key</th><th>Value</th><th>Type</th><th>Category</th></tr>";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['setting_key'] . "</td>";
        echo "<td>" . $row['setting_value'] . "</td>";
        echo "<td>" . ($row['setting_type'] ?? '-') . "</td>";
        echo "<td>" . ($row['category'] ?? '-') . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>ไม่พบข้อมูล settings ที่เกี่ยวข้อง</td></tr>";
}

echo "</table>";

// ตรวจสอบเฉพาะ homepage_show_price
echo "<h3>ตรวจสอบ homepage_show_price โดยตรง:</h3>";
$direct_query = "SELECT * FROM settings WHERE setting_key = 'homepage_show_price'";
$direct_result = $conn->query($direct_query);

if ($direct_result && $row = $direct_result->fetch_assoc()) {
    echo "<p><strong>พบ:</strong> " . $row['setting_key'] . " = " . $row['setting_value'] . "</p>";
    echo "<p><strong>Type:</strong> " . gettype($row['setting_value']) . "</p>";
    echo "<p><strong>== '1':</strong> " . (($row['setting_value'] == '1') ? 'TRUE' : 'FALSE') . "</p>";
    echo "<p><strong>=== '1':</strong> " . (($row['setting_value'] === '1') ? 'TRUE' : 'FALSE') . "</p>";
} else {
    echo "<p><strong>ไม่พบ:</strong> homepage_show_price ในฐานข้อมูล</p>";
}

// อัพเดทด้วยตัวเองถ้าไม่มี
echo "<h3>ႋอัพเดท homepage_show_price เป็น 0:</h3>";
$update_query = "INSERT INTO settings (setting_key, setting_value, setting_type, category) 
                 VALUES ('homepage_show_price', '0', 'boolean', 'homepage') 
                 ON DUPLICATE KEY UPDATE setting_value = '0'";
if ($conn->query($update_query)) {
    echo "<p style='color: green;'>✅ อัพเดท homepage_show_price = '0' สำเร็จ</p>";
} else {
    echo "<p style='color: red;'>❌ อัพเดทล้มเหลว: " . $conn->error . "</p>";
}

// ตรวจสอบอีกครั้ง
echo "<h3>ตรวจสอบหลังอัพเดท:</h3>";
$check_again = $conn->query("SELECT * FROM settings WHERE setting_key = 'homepage_show_price'");
if ($check_again && $row = $check_again->fetch_assoc()) {
    echo "<p><strong>หลังอัพเดท:</strong> " . $row['setting_key'] . " = " . $row['setting_value'] . "</p>";
    echo "<p><strong>== '1':</strong> " . (($row['setting_value'] == '1') ? 'TRUE (จะแสดงราคา)' : 'FALSE (ไม่แสดงราคา)') . "</p>";
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ตรวจสอบ Database Settings</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        table { margin: 20px 0; }
        th, td { padding: 10px; text-align: left; }
        th { background: #667eea; color: white; }
        .btn { display: inline-block; background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
        .btn:hover { background: #5558d9; }
    </style>
</head>
<body>
    <div style="text-align: center; margin: 30px 0;">
        <a href="debug-price-settings.php" class="btn">ดู Debug Settings</a>
        <a href="/" class="btn">ดูหน้าแรก</a>
        <a href="admin/settings/price-display.php" class="btn">ตั้งค่าการแสดงราคา</a>
        <a href="javascript:location.reload()" class="btn">รีเฟรช</a>
    </div>
</body>
</html>

<?php $conn->close(); ?>
