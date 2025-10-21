<?php
/**
 * ทดสอบ error ในหน้า models.php
 */

// เปิด error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 ทดสอบ Error ในหน้า Models</h1>";

echo "<h2>📋 ตรวจสอบ PHP Version และ Error Reporting:</h2>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Error Reporting:</strong> " . error_reporting() . "</p>";
echo "<p><strong>Display Errors:</strong> " . ini_get('display_errors') . "</p>";

echo "<h2>🧪 ทดสอบ htmlspecialchars() กับ null:</h2>";

// ทดสอบ htmlspecialchars กับ null
$test_values = [
    'string' => 'Hello World',
    'null' => null,
    'empty' => '',
    'number' => 123,
    'array' => ['test']
];

foreach ($test_values as $key => $value) {
    echo "<p><strong>{$key}:</strong> ";
    try {
        $result = htmlspecialchars($value ?? '');
        echo "✅ สำเร็จ: " . var_export($result, true);
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage();
    }
    echo "</p>";
}

echo "<h2>🗄️ ทดสอบการดึงข้อมูลเมนู:</h2>";

require_once 'includes/config.php';

// ทดสอบการดึงเมนู
$menu_query = "SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC LIMIT 5";
$menu_result = $conn->query($menu_query);

if ($menu_result) {
    echo "<p>✅ Query เมนูสำเร็จ</p>";
    echo "<p><strong>จำนวนเมนู:</strong> " . $menu_result->num_rows . "</p>";
    
    if ($menu_result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Name</th><th>URL</th><th>Status</th></tr>";
        
        while ($menu = $menu_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . ($menu['id'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($menu['name'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($menu['url'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($menu['status'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>⚠️ ไม่พบเมนูในฐานข้อมูล</p>";
    }
} else {
    echo "<p>❌ Query เมนูล้มเหลว: " . $conn->error . "</p>";
}

echo "<h2>👤 ทดสอบการดึงข้อมูลโมเดล:</h2>";

// ทดสอบการดึงโมเดล
$model_query = "SELECT m.*, c.name as category_name FROM models m LEFT JOIN categories c ON m.category_id = c.id LIMIT 3";
$model_result = $conn->query($model_query);

if ($model_result) {
    echo "<p>✅ Query โมเดลสำเร็จ</p>";
    echo "<p><strong>จำนวนโมเดล:</strong> " . $model_result->num_rows . "</p>";
    
    if ($model_result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Code</th><th>Price</th><th>Status</th></tr>";
        
        while ($model = $model_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . ($model['id'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($model['name'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($model['code'] ?? 'N/A') . "</td>";
            echo "<td>" . ($model['price'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($model['status'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>⚠️ ไม่พบโมเดลในฐานข้อมูล</p>";
    }
} else {
    echo "<p>❌ Query โมเดลล้มเหลว: " . $conn->error . "</p>";
}

echo "<h2>🔧 ทดสอบ Helper Functions:</h2>";

// ทดสอบ db_get_rows
if (function_exists('db_get_rows')) {
    echo "<p>✅ Function db_get_rows มีอยู่</p>";
    
    $test_menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC LIMIT 3");
    echo "<p><strong>ผลลัพธ์ db_get_rows:</strong> " . count($test_menus) . " รายการ</p>";
    
    if (!empty($test_menus)) {
        foreach ($test_menus as $menu) {
            echo "<p>- " . htmlspecialchars($menu['name'] ?? 'N/A') . " (" . htmlspecialchars($menu['url'] ?? 'N/A') . ")</p>";
        }
    }
} else {
    echo "<p>❌ Function db_get_rows ไม่มีอยู่</p>";
}

echo "<h2>✅ สรุป:</h2>";
echo "<p>หากไม่มี error แสดงในหน้านี้ แสดงว่าการแก้ไขสำเร็จ</p>";

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ทดสอบ Error Models</title>
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
        <a href="models.php" class="btn">ดูหน้า Models</a>
        <a href="javascript:location.reload()" class="btn">รีเฟรช</a>
    </div>
</body>
</html>

<?php $conn->close(); ?>
