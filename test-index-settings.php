<?php
/**
 * ทดสอบการดึง settings ในหน้าแรก
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

// ดึงการตั้งค่าแบบเดียวกับ index.php
$global_settings = get_all_settings($conn);

echo "<h1>🧪 ทดสอบการดึง Settings ในหน้าแรก</h1>";

echo "<h2>📊 การตั้งค่าที่ดึงมา:</h2>";

// ตรวจสอบ homepage_show_price
$homepage_show_price = ($global_settings['homepage_show_price'] ?? '0') == '1';

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h3>homepage_show_price:</h3>";
echo "<p><strong>ค่าดิบ:</strong> " . var_export($global_settings['homepage_show_price'] ?? 'NOT SET', true) . "</p>";
echo "<p><strong>Default (ถ้าไม่มี):</strong> '0'</p>";
echo "<p><strong>เงื่อนไข (== '1'):</strong> ";
echo $homepage_show_price ? 
    '<span style="color: red; font-weight: bold;">TRUE (จะแสดงราคา)</span>' : 
    '<span style="color: green; font-weight: bold;">FALSE (ไม่แสดงราคา)</span>';
echo "</p>";
echo "</div>";

echo "<div style='background: #e3f2fd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h3>price_hidden_text:</h3>";
echo "<p><strong>ค่าดิบ:</strong> " . var_export($global_settings['price_hidden_text'] ?? 'NOT SET', true) . "</p>";
echo "<p><strong>Default (ถ้าไม่มี):</strong> 'ติดต่อสอบถาม'</p>";
echo "<p><strong>จะแสดง:</strong> " . htmlspecialchars($global_settings['price_hidden_text'] ?? 'ติดต่อสอบถาม') . "</p>";
echo "</div>";

// ทดสอบดึงโมเดล 1 คน
echo "<h2>👤 ทดสอบดึงโมเดล:</h2>";

$test_query = "
    SELECT m.*, c.name as category_name 
    FROM models m 
    LEFT JOIN categories c ON m.category_id = c.id 
    WHERE m.status = 'available'
    LIMIT 1
";
$result = $conn->query($test_query);

if ($result && $model = $result->fetch_assoc()) {
    echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>โมเดลทดสอบ:</h3>";
    echo "<p><strong>ชื่อ:</strong> " . htmlspecialchars($model['name']) . "</p>";
    echo "<p><strong>ราคา:</strong> " . ($model['price'] ?? 0) . " ฿</p>";
    echo "<p><strong>มีราคา:</strong> " . (!empty($model['price']) && $model['price'] > 0 ? 'ใช่' : 'ไม่') . "</p>";
    echo "</div>";
    
    // ทดสอบเงื่อนไขการแสดงราคา
    echo "<h2>🔍 ทดสอบเงื่อนไขการแสดงราคา:</h2>";
    
    echo "<div style='background: #d1ecf1; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>เงื่อนไขใน index.php:</h3>";
    
    echo "<p><strong>if (\$homepage_show_price && !empty(\$model['price']) && \$model['price'] > 0):</strong></p>";
    echo "<ul>";
    echo "<li>homepage_show_price: " . ($homepage_show_price ? 'TRUE' : 'FALSE') . "</li>";
    echo "<li>!empty(\$model['price']): " . (!empty($model['price']) ? 'TRUE' : 'FALSE') . "</li>";
    echo "<li>\$model['price'] > 0: " . (($model['price'] ?? 0) > 0 ? 'TRUE' : 'FALSE') . "</li>";
    
    $all_conditions = $homepage_show_price && !empty($model['price']) && $model['price'] > 0;
    echo "<li><strong>ผลรวม (AND):</strong> " . ($all_conditions ? 'TRUE' : 'FALSE') . "</li>";
    echo "</ul>";
    
    echo "<p><strong>ผลลัพธ์:</strong> ";
    if ($all_conditions) {
        echo '<span style="color: red; font-weight: bold;">จะแสดงราคา: ' . number_format($model['price']) . ' ฿</span>';
    } else {
        echo '<span style="color: green; font-weight: bold;">จะแสดงข้อความ: ' . htmlspecialchars($global_settings['price_hidden_text'] ?? 'ติดต่อสอบถาม') . '</span>';
    }
    echo "</p>";
    echo "</div>";
    
} else {
    echo "<p style='color: red;'>❌ ไม่พบโมเดลในระบบ</p>";
}

// แสดง settings ทั้งหมดที่เกี่ยวข้อง
echo "<h2>📋 Settings ทั้งหมดที่เกี่ยวข้อง:</h2>";

$relevant_settings = [];
foreach ($global_settings as $key => $value) {
    if (strpos($key, 'price') !== false || strpos($key, 'homepage') !== false) {
        $relevant_settings[$key] = $value;
    }
}

if (!empty($relevant_settings)) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Key</th><th>Value</th><th>Type</th></tr>";
    foreach ($relevant_settings as $key => $value) {
        echo "<tr>";
        echo "<td>" . $key . "</td>";
        echo "<td>" . var_export($value, true) . "</td>";
        echo "<td>" . gettype($value) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ ไม่พบ settings ที่เกี่ยวข้อง</p>";
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ทดสอบ Settings หน้าแรก</title>
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
        <a href="/" class="btn" onclick="clearCacheAndGo()">ดูหน้าแรก (Clear Cache)</a>
        <a href="fix-price-display.php" class="btn">แก้ไขปัญหาการแสดงราคา</a>
        <a href="admin/settings/price-display.php" class="btn">ตั้งค่าการแสดงราคา</a>
        <a href="javascript:location.reload()" class="btn">รีเฟรชหน้านี้</a>
    </div>
    
    <script>
        function clearCacheAndGo() {
            // ล้าง cache แล้วไปหน้าแรก
            if ('caches' in window) {
                caches.keys().then(function(names) {
                    for (let name of names) {
                        caches.delete(name);
                    }
                });
            }
            
            // ไปหน้าแรกด้วย timestamp เพื่อบังคับ reload
            window.location.href = '/?t=' + Date.now();
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>

