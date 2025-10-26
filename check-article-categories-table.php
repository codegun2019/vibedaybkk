<?php
/**
 * ตรวจสอบโครงสร้างตาราง article_categories
 */

require_once 'includes/config.php';

echo "<h1>🔍 ตรวจสอบตาราง article_categories</h1>";

// ตรวจสอบว่าตารางมีอยู่หรือไม่
$table_check = $conn->query("SHOW TABLES LIKE 'article_categories'");

if ($table_check->num_rows > 0) {
    echo "<p>✅ ตาราง article_categories มีอยู่</p>";
    
    // ดูโครงสร้างตาราง
    echo "<h2>โครงสร้างตาราง:</h2>";
    $columns = $conn->query("DESCRIBE article_categories");
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    $existing_columns = [];
    while ($col = $columns->fetch_assoc()) {
        $existing_columns[] = $col['Field'];
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // ตรวจสอบคอลัมน์ที่ต้องการ
    echo "<h2>ตรวจสอบคอลัมน์ที่จำเป็น:</h2>";
    $required_columns = ['id', 'name', 'name_en', 'slug', 'description', 'icon', 'color', 'image', 'parent_id', 'sort_order', 'status', 'created_at', 'updated_at'];
    
    foreach ($required_columns as $col) {
        if (in_array($col, $existing_columns)) {
            echo "<p>✅ คอลัมน์ <strong>$col</strong> มีอยู่</p>";
        } else {
            echo "<p>❌ คอลัมน์ <strong>$col</strong> ไม่มี</p>";
        }
    }
    
} else {
    echo "<p>❌ ตาราง article_categories ไม่มีอยู่</p>";
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ตรวจสอบตาราง</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        h1 { color: #667eea; }
        h2 { color: #DC2626; }
        table { background: white; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; }
        th { background: #667eea; color: white; }
    </style>
</head>
</html>




