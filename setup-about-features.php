<?php
/**
 * Setup About Features - เพิ่มคอลัมน์ features สำหรับ About section
 */

require_once 'includes/config.php';

echo "<h1>VIBEDAYBKK - Setup About Features</h1>";

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    echo "❌ ไม่สามารถเชื่อมต่อฐานข้อมูลได้";
    exit;
}

echo "<h2>1. ตรวจสอบตาราง homepage_sections</h2>";

// ตรวจสอบว่าตารางมีอยู่หรือไม่
$check_table = $conn->query("SHOW TABLES LIKE 'homepage_sections'");
if ($check_table->num_rows == 0) {
    echo "❌ ตาราง homepage_sections ไม่มีอยู่";
    exit;
}

echo "✅ ตาราง homepage_sections มีอยู่<br>";

// ตรวจสอบโครงสร้างตารางปัจจุบัน
echo "<h3>โครงสร้างตารางปัจจุบัน:</h3>";
$structure = $conn->query("DESCRIBE homepage_sections");
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = $structure->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "<td>" . $row['Extra'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>2. ตรวจสอบคอลัมน์ features</h2>";

// ตรวจสอบว่าคอลัมน์ features มีอยู่หรือไม่
$check_column = $conn->query("SHOW COLUMNS FROM homepage_sections LIKE 'features'");
if ($check_column->num_rows > 0) {
    echo "✅ คอลัมน์ features มีอยู่แล้ว<br>";
} else {
    echo "❌ คอลัมน์ features ไม่มีอยู่ กำลังเพิ่ม...<br>";
    
    // เพิ่มคอลัมน์ features
    $sql = "ALTER TABLE homepage_sections ADD COLUMN features TEXT NULL AFTER steps";
    if ($conn->query($sql)) {
        echo "✅ เพิ่มคอลัมน์ features สำเร็จ<br>";
    } else {
        echo "❌ เกิดข้อผิดพลาด: " . $conn->error . "<br>";
        exit;
    }
}

echo "<h2>3. ตรวจสอบโครงสร้างตารางหลังการแก้ไข</h2>";
$structure_after = $conn->query("DESCRIBE homepage_sections");
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = $structure_after->fetch_assoc()) {
    $highlight = ($row['Field'] == 'features') ? 'style="background-color: #90EE90;"' : '';
    echo "<tr $highlight>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "<td>" . $row['Extra'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>4. ตรวจสอบข้อมูล About section</h2>";

// ตรวจสอบข้อมูล About section
$about_section = $conn->query("SELECT * FROM homepage_sections WHERE section_key = 'about'");
if ($about_section->num_rows > 0) {
    $about = $about_section->fetch_assoc();
    echo "✅ พบข้อมูล About section<br>";
    echo "<h3>ข้อมูลปัจจุบัน:</h3>";
    echo "<ul>";
    echo "<li><strong>ID:</strong> " . $about['id'] . "</li>";
    echo "<li><strong>Title:</strong> " . htmlspecialchars($about['title']) . "</li>";
    echo "<li><strong>Section Key:</strong> " . $about['section_key'] . "</li>";
    echo "<li><strong>Features:</strong> " . ($about['features'] ? 'มีข้อมูล' : 'ไม่มีข้อมูล') . "</li>";
    echo "</ul>";
    
    if ($about['features']) {
        $features = json_decode($about['features'], true);
        if ($features) {
            echo "<h4>รายการคุณสมบัติปัจจุบัน:</h4>";
            echo "<ol>";
            foreach ($features as $feature) {
                echo "<li>" . htmlspecialchars($feature['text']) . " <em>(" . htmlspecialchars($feature['icon']) . ")</em></li>";
            }
            echo "</ol>";
        }
    }
} else {
    echo "❌ ไม่พบข้อมูล About section<br>";
    echo "<p>กรุณาเพิ่ม About section ใน admin/homepage/ ก่อน</p>";
}

echo "<h2>5. สรุป</h2>";
echo "<p>✅ การตั้งค่าเสร็จสิ้น</p>";
echo "<p>ตอนนี้คุณสามารถ:</p>";
echo "<ul>";
echo "<li>ไปที่ <a href='admin/homepage/edit.php?id=2'>admin/homepage/edit.php?id=2</a> เพื่อแก้ไข About section</li>";
echo "<li>เพิ่ม/แก้ไขรายการคุณสมบัติได้</li>";
echo "<li>ตั้งค่าไอคอนสำหรับแต่ละคุณสมบัติได้</li>";
echo "</ul>";

$conn->close();
?>
