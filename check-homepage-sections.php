<?php
/**
 * ตรวจสอบข้อมูล homepage_sections
 */
require_once 'includes/config.php';
require_once 'includes/functions.php';

// ตรวจสอบโครงสร้างตาราง
echo "<h2>โครงสร้างตาราง homepage_sections:</h2>";
$columns = $conn->query("SHOW COLUMNS FROM homepage_sections");
echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
while ($col = $columns->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$col['Field']}</td>";
    echo "<td>{$col['Type']}</td>";
    echo "<td>{$col['Null']}</td>";
    echo "<td>{$col['Key']}</td>";
    echo "<td>{$col['Default']}</td>";
    echo "</tr>";
}
echo "</table>";

// ดึงข้อมูลทั้งหมด
echo "<h2>ข้อมูลใน homepage_sections:</h2>";
$sections = $conn->query("SELECT * FROM homepage_sections ORDER BY sort_order");
echo "<table border='1' style='border-collapse: collapse; margin: 20px 0; width: 100%;'>";
echo "<tr>";
echo "<th>ID</th>";
echo "<th>Section Key</th>";
echo "<th>Title</th>";
echo "<th>Subtitle</th>";
echo "<th>Content</th>";
echo "<th>Background Type</th>";
echo "<th>Background Color</th>";
echo "<th>Background Image</th>";
echo "<th>Button 1</th>";
echo "<th>Button 2</th>";
echo "<th>Active</th>";
echo "<th>Sort Order</th>";
echo "</tr>";

while ($section = $sections->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$section['id']}</td>";
    echo "<td><strong>{$section['section_key']}</strong></td>";
    echo "<td>" . htmlspecialchars(substr($section['title'] ?? '', 0, 50)) . "</td>";
    echo "<td>" . htmlspecialchars(substr($section['subtitle'] ?? '', 0, 50)) . "</td>";
    echo "<td>" . htmlspecialchars(substr($section['content'] ?? '', 0, 50)) . "</td>";
    echo "<td>{$section['background_type']}</td>";
    echo "<td>{$section['background_color']}</td>";
    echo "<td>{$section['background_image']}</td>";
    echo "<td>{$section['button1_text']}</td>";
    echo "<td>{$section['button2_text']}</td>";
    echo "<td>" . ($section['is_active'] ? 'Yes' : 'No') . "</td>";
    echo "<td>{$section['sort_order']}</td>";
    echo "</tr>";
}
echo "</table>";

// แสดงข้อมูลแบบละเอียด
echo "<h2>ข้อมูลละเอียดแต่ละ Section:</h2>";
$sections = $conn->query("SELECT * FROM homepage_sections ORDER BY sort_order");
while ($section = $sections->fetch_assoc()) {
    echo "<div style='border: 2px solid #ccc; padding: 20px; margin: 20px 0; background: #f9f9f9;'>";
    echo "<h3>Section: {$section['section_key']} (ID: {$section['id']})</h3>";
    echo "<pre>";
    print_r($section);
    echo "</pre>";
    echo "</div>";
}

$conn->close();
?>

<style>
body {
    font-family: Arial, sans-serif;
    padding: 20px;
    background: #fff;
    color: #333;
}
table {
    font-size: 12px;
}
th {
    background: #4CAF50;
    color: white;
    padding: 8px;
}
td {
    padding: 6px;
    border: 1px solid #ddd;
}
tr:nth-child(even) {
    background: #f2f2f2;
}
</style>

