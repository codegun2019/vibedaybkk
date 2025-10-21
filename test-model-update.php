<?php
/**
 * ทดสอบการอัพเดทโมเดล
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';
require_once 'includes/functions.php';

// เปิด error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$model_id = 92;

echo "<h1>ทดสอบการอัพเดทโมเดล ID: {$model_id}</h1>";

// 1. ดึงข้อมูลโมเดล
echo "<h2>1. ตรวจสอบข้อมูลโมเดล:</h2>";
$stmt = $conn->prepare("SELECT * FROM models WHERE id = ?");
$stmt->bind_param('i', $model_id);
$stmt->execute();
$result = $stmt->get_result();

if ($model = $result->fetch_assoc()) {
    echo "<pre>";
    print_r($model);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>❌ ไม่พบโมเดล ID: {$model_id}</p>";
    exit;
}

// 2. ตรวจสอบโครงสร้างตาราง
echo "<h2>2. โครงสร้างตาราง models:</h2>";
$columns = $conn->query("SHOW COLUMNS FROM models");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
while ($col = $columns->fetch_assoc()) {
    echo "<tr>";
    echo "<td><strong>{$col['Field']}</strong></td>";
    echo "<td>{$col['Type']}</td>";
    echo "<td>{$col['Null']}</td>";
    echo "<td>{$col['Key']}</td>";
    echo "<td>{$col['Default']}</td>";
    echo "</tr>";
}
echo "</table>";

// 3. ทดสอบ UPDATE ข้อมูลง่ายๆ
echo "<h2>3. ทดสอบ UPDATE:</h2>";

try {
    // ตรวจสอบว่ามีฟิลด์อะไรบ้าง
    $test_fields = ['name', 'description', 'height', 'weight', 'price'];
    $existing_fields = [];
    
    foreach ($test_fields as $field) {
        $check = $conn->query("SHOW COLUMNS FROM models LIKE '{$field}'");
        if ($check && $check->num_rows > 0) {
            $existing_fields[] = $field;
            echo "✅ มีฟิลด์ '{$field}'<br>";
        } else {
            echo "❌ ไม่มีฟิลด์ '{$field}'<br>";
        }
    }
    
    echo "<br><strong>ฟิลด์ที่มีในตาราง:</strong> " . implode(', ', $existing_fields);
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// 4. ทดสอบ db_update function
echo "<h2>4. ทดสอบฟังก์ชัน db_update:</h2>";

try {
    // ข้อมูลทดสอบ - ใช้เฉพาะฟิลด์ที่มีจริง
    $test_data = [
        'name' => $model['name'], // ไม่เปลี่ยน
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    echo "<strong>Data to update:</strong><pre>";
    print_r($test_data);
    echo "</pre>";
    
    // ลองอัพเดท
    if (db_update($conn, 'models', $test_data, 'id = ?', [$model_id])) {
        echo "<p style='color: green;'>✅ อัพเดทสำเร็จ!</p>";
    } else {
        echo "<p style='color: red;'>❌ อัพเดทล้มเหลว</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

$conn->close();
?>

<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    h1 { color: #DC2626; }
    h2 { color: #667eea; margin-top: 30px; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
    pre { background: #1e1e1e; color: #0f0; padding: 15px; border-radius: 8px; overflow-x: auto; }
    table { border-collapse: collapse; margin: 15px 0; background: white; }
    th { background: #667eea; color: white; padding: 10px; }
    td { padding: 8px; border: 1px solid #ddd; }
</style>

