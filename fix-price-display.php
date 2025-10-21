<?php
/**
 * แก้ไขปัญหาการแสดงราคา - บังคับปิดการแสดงราคา
 */

require_once 'includes/config.php';

echo "<h1>🔧 แก้ไขปัญหาการแสดงราคา</h1>";

// อัพเดทการตั้งค่าทั้งหมดที่เกี่ยวข้องกับราคา
$settings_to_update = [
    'homepage_show_price' => '0',
    'models_list_show_price' => '0', 
    'model_detail_show_price' => '0',
    'model_detail_show_price_range' => '0',
    'price_hidden_text' => 'ติดต่อสอบถาม'
];

echo "<h2>📝 อัพเดท Settings:</h2>";

foreach ($settings_to_update as $key => $value) {
    $sql = "INSERT INTO settings (setting_key, setting_value, setting_type, category) 
            VALUES (?, ?, 'boolean', 'homepage') 
            ON DUPLICATE KEY UPDATE setting_value = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $key, $value, $value);
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ {$key} = {$value}</p>";
    } else {
        echo "<p style='color: red;'>❌ {$key}: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// ตรวจสอบผลลัพธ์
echo "<h2>🔍 ตรวจสอบผลลัพธ์:</h2>";

$check_query = "SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE '%price%' OR setting_key LIKE '%homepage%'";
$result = $conn->query($check_query);

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Setting Key</th><th>Value</th><th>สถานะ</th></tr>";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $status = '';
        if ($row['setting_key'] == 'homepage_show_price') {
            $status = ($row['setting_value'] == '0') ? 
                '<span style="color: green;">✅ ปิด (ไม่แสดงราคา)</span>' : 
                '<span style="color: red;">❌ เปิด (แสดงราคา)</span>';
        } elseif ($row['setting_key'] == 'price_hidden_text') {
            $status = '<span style="color: blue;">📝 ข้อความแทน</span>';
        } else {
            $status = ($row['setting_value'] == '0') ? 
                '<span style="color: green;">✅ ปิด</span>' : 
                '<span style="color: red;">❌ เปิด</span>';
        }
        
        echo "<tr>";
        echo "<td>" . $row['setting_key'] . "</td>";
        echo "<td>" . $row['setting_value'] . "</td>";
        echo "<td>" . $status . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3'>ไม่พบข้อมูล</td></tr>";
}

echo "</table>";

// ทดสอบการดึง settings
echo "<h2>🧪 ทดสอบการดึง Settings:</h2>";

require_once 'includes/functions.php';
$global_settings = get_all_settings($conn);

$homepage_show_price = ($global_settings['homepage_show_price'] ?? '1') == '1';

echo "<p><strong>homepage_show_price:</strong> ";
echo var_export($global_settings['homepage_show_price'] ?? 'NOT SET', true);
echo "</p>";

echo "<p><strong>เงื่อนไข (== '1'):</strong> ";
echo $homepage_show_price ? 
    '<span style="color: red;">❌ TRUE (จะแสดงราคา)</span>' : 
    '<span style="color: green;">✅ FALSE (ไม่แสดงราคา)</span>';
echo "</p>";

echo "<p><strong>price_hidden_text:</strong> ";
echo htmlspecialchars($global_settings['price_hidden_text'] ?? 'NOT SET');
echo "</p>";

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>แก้ไขปัญหาการแสดงราคา</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        h1 { color: #667eea; }
        h2 { color: #DC2626; border-bottom: 2px solid #DC2626; padding-bottom: 10px; }
        table { margin: 20px 0; background: white; border-radius: 10px; overflow: hidden; }
        th, td { padding: 12px; text-align: left; }
        th { background: #667eea; color: white; }
        .btn { display: inline-block; background: #667eea; color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; margin: 10px 5px; }
        .btn:hover { background: #5558d9; }
        .btn.danger { background: #dc3545; }
        .btn.danger:hover { background: #c82333; }
        .alert { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 8px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="alert">
        <h3>🎯 ขั้นตอนต่อไป:</h3>
        <ol>
            <li><strong>Hard Refresh:</strong> กด <code>Ctrl+Shift+R</code> (Windows) หรือ <code>Cmd+Shift+R</code> (Mac)</li>
            <li><strong>Clear Browser Cache:</strong> ลบ cache ของเบราว์เซอร์</li>
            <li><strong>ตรวจสอบหน้าแรก:</strong> ต้องเห็น "ติดต่อสอบถาม" แทนราคา</li>
        </ol>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="/" class="btn" onclick="clearCacheAndGo()">ดูหน้าแรก (Clear Cache)</a>
        <a href="debug-price-settings.php" class="btn">ตรวจสอบ Settings</a>
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
        
        // บังคับ reload ด้วย timestamp
        setTimeout(function() {
            if (window.location.search.indexOf('reload=') === -1) {
                window.location.href = window.location.href + '?reload=' + Date.now();
            }
        }, 1000);
    </script>
</body>
</html>

<?php $conn->close(); ?>
