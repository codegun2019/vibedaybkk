<?php
/**
 * ทดสอบการแสดงโมเดลในหน้าแรก
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

// ดึงการตั้งค่า
$global_settings = get_all_settings($conn);

echo "<pre>";
echo "=== การตั้งค่าที่ดึงมา ===\n\n";

echo "1. homepage_show_price: ";
echo var_export($global_settings['homepage_show_price'] ?? 'NOT SET', true);
echo "\n   เงื่อนไข (== '1'): ";
echo (($global_settings['homepage_show_price'] ?? '1') == '1') ? 'TRUE (แสดงราคา)' : 'FALSE (ไม่แสดง)';
echo "\n\n";

echo "2. price_hidden_text: ";
echo var_export($global_settings['price_hidden_text'] ?? 'NOT SET', true);
echo "\n\n";

echo "3. model_detail_show_personal_info: ";
echo var_export($global_settings['model_detail_show_personal_info'] ?? 'NOT SET', true);
echo "\n   เงื่อนไข (== '1'): ";
echo (($global_settings['model_detail_show_personal_info'] ?? '1') == '1') ? 'TRUE (แสดง)' : 'FALSE (ไม่แสดง)';
echo "\n\n";

// ดึงโมเดล 1 คน
echo "\n=== ทดสอบดึงโมเดล ===\n\n";

$test_model_query = "
    SELECT m.*, c.name as category_name 
    FROM models m 
    LEFT JOIN categories c ON m.category_id = c.id 
    WHERE m.status = 'available'
    LIMIT 1
";
$test_result = $conn->query($test_model_query);

if ($test_result && $model = $test_result->fetch_assoc()) {
    echo "โมเดลที่ทดสอบ:\n";
    echo "  ID: " . $model['id'] . "\n";
    echo "  Name: " . $model['name'] . "\n";
    echo "  Code: " . ($model['code'] ?? '-') . "\n";
    echo "  Price: " . ($model['price'] ?? 0) . "\n";
    echo "  Height: " . ($model['height'] ?? '-') . "\n";
    echo "  Weight: " . ($model['weight'] ?? '-') . "\n";
    echo "  Status: " . ($model['status'] ?? '-') . "\n";
    echo "\n";
    
    // ทดสอบการแสดงราคา
    echo "=== การแสดงราคา ===\n\n";
    
    $homepage_show_price = ($global_settings['homepage_show_price'] ?? '1') == '1';
    
    echo "homepage_show_price = " . ($homepage_show_price ? 'TRUE' : 'FALSE') . "\n";
    echo "model price = " . ($model['price'] ?? 0) . "\n";
    echo "model price > 0 = " . (($model['price'] ?? 0) > 0 ? 'TRUE' : 'FALSE') . "\n\n";
    
    if ($homepage_show_price && !empty($model['price']) && $model['price'] > 0) {
        echo "✅ จะแสดง: " . number_format($model['price']) . " ฿\n";
    } elseif (!$homepage_show_price) {
        echo "❌ จะแสดง: " . ($global_settings['price_hidden_text'] ?? 'ติดต่อสอบถาม') . "\n";
    } else {
        echo "⚠️ ไม่แสดงอะไร (ไม่มีราคา)\n";
    }
    
    // ทดสอบการแสดงข้อมูลส่วนตัว
    echo "\n=== การแสดงข้อมูลส่วนตัว ===\n\n";
    
    $show_personal_info = ($global_settings['model_detail_show_personal_info'] ?? '1') == '1';
    
    echo "model_detail_show_personal_info = " . ($show_personal_info ? 'TRUE' : 'FALSE') . "\n";
    
    if ($show_personal_info) {
        echo "✅ จะแสดง:\n";
        if (!empty($model['height'])) echo "  - ส่วนสูง: " . $model['height'] . " cm\n";
        if (!empty($model['weight'])) echo "  - น้ำหนัก: " . $model['weight'] . " kg\n";
        if (!empty($model['age'])) echo "  - อายุ: " . $model['age'] . " ปี\n";
        if (!empty($model['birth_date'])) echo "  - วันเกิด: " . $model['birth_date'] . "\n";
    } else {
        echo "❌ ไม่แสดงข้อมูลส่วนตัว\n";
    }
    
} else {
    echo "❌ ไม่พบโมเดลในระบบ\n";
}

echo "\n=== SQL Query ตรวจสอบ Settings ===\n\n";
$check_sql = "SELECT * FROM settings WHERE setting_key IN ('homepage_show_price', 'model_detail_show_personal_info', 'price_hidden_text')";
echo "Query: {$check_sql}\n\n";
$check_result = $conn->query($check_sql);
if ($check_result && $check_result->num_rows > 0) {
    while ($row = $check_result->fetch_assoc()) {
        echo "Key: " . $row['setting_key'] . "\n";
        echo "Value: " . $row['setting_value'] . "\n";
        echo "Type: " . $row['setting_type'] . "\n";
        echo "---\n";
    }
} else {
    echo "❌ ไม่พบ settings เหล่านี้ในฐานข้อมูล\n";
}

echo "</pre>";

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ทดสอบการแสดงโมเดล</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        pre { background: #1e1e1e; color: #0f0; padding: 20px; border-radius: 10px; overflow-x: auto; line-height: 1.6; }
        a { display: inline-block; background: #667eea; color: white; padding: 12px 25px; border-radius: 8px; text-decoration: none; margin: 10px 5px; }
        a:hover { background: #5558d9; }
    </style>
</head>
<body>
    <h1>🔍 ทดสอบการแสดงโมเดลในหน้าแรก</h1>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="debug-price-settings.php">ดูการตั้งค่าแบบละเอียด</a>
        <a href="admin/settings/price-display.php">ตั้งค่าการแสดงราคา</a>
        <a href="/">ดูหน้าแรก</a>
        <a href="javascript:location.reload()">รีเฟรช</a>
    </div>
</body>
</html>

<?php $conn->close(); ?>


