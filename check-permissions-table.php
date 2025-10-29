<?php
require_once 'includes/config.php';

echo "<h1>🔍 ตรวจสอบโครงสร้างตาราง Permissions</h1>";

// 1. โครงสร้างตาราง
echo "<h2>1️⃣ โครงสร้างตาราง permissions:</h2>";
$result = $conn->query("DESCRIBE permissions");
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td><strong>{$row['Field']}</strong></td>";
    echo "<td>{$row['Type']}</td>";
    echo "<td>{$row['Null']}</td>";
    echo "<td>{$row['Key']}</td>";
    echo "<td>{$row['Default']}</td>";
    echo "<td>{$row['Extra']}</td>";
    echo "</tr>";
}
echo "</table><br>";

// 2. ข้อมูลในตาราง
echo "<h2>2️⃣ ข้อมูลทั้งหมดในตาราง permissions:</h2>";
$result = $conn->query("SELECT * FROM permissions LIMIT 20");
echo "<table border='1' style='border-collapse: collapse;'>";
$first = true;
while ($row = $result->fetch_assoc()) {
    if ($first) {
        echo "<tr>";
        foreach (array_keys($row) as $col) {
            echo "<th>$col</th>";
        }
        echo "</tr>";
        $first = false;
    }
    echo "<tr>";
    foreach ($row as $value) {
        echo "<td>$value</td>";
    }
    echo "</tr>";
}
echo "</table><br>";

// 3. ข้อมูล roles
echo "<h2>3️⃣ ตาราง roles:</h2>";
$result = $conn->query("SELECT * FROM roles");
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Role Key</th><th>Name</th><th>Level</th><th>Color</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['role_key']}</td>";
    echo "<td>{$row['name']}</td>";
    echo "<td>{$row['level']}</td>";
    echo "<td>{$row['color']}</td>";
    echo "</tr>";
}
echo "</table>";

$conn->close();
?>

