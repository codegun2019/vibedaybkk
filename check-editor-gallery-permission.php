<?php
require_once 'includes/config.php';

echo "<h2>ตรวจสอบสิทธิ์ของ Editor สำหรับ Gallery</h2>";

// ตรวจสอบสิทธิ์ใน permissions table
$stmt = $conn->prepare("
    SELECT p.*, r.name as role_name 
    FROM permissions p 
    JOIN roles r ON p.role_key = r.role_key 
    WHERE r.role_key = 'editor' AND p.feature = 'gallery'
");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Role</th><th>Feature</th><th>View</th><th>Create</th><th>Edit</th><th>Delete</th><th>Export</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['role_name']) . " (" . $row['role_key'] . ")</td>";
        echo "<td>" . htmlspecialchars($row['feature']) . "</td>";
        echo "<td>" . ($row['can_view'] ? '✅' : '❌') . "</td>";
        echo "<td>" . ($row['can_create'] ? '✅' : '❌') . "</td>";
        echo "<td>" . ($row['can_edit'] ? '✅' : '❌') . "</td>";
        echo "<td>" . ($row['can_delete'] ? '✅' : '❌') . "</td>";
        echo "<td>" . ($row['can_export'] ? '✅' : '❌') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ ไม่พบสิทธิ์ของ Editor สำหรับ Gallery</p>";
    
    // ลองเช็คว่ามี role editor หรือไม่
    $stmt2 = $conn->prepare("SELECT * FROM roles WHERE role_key = 'editor'");
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    
    if ($result2->num_rows > 0) {
        echo "<p>✅ พบ Role Editor ในระบบ</p>";
        echo "<p>⚠️ แต่ยังไม่มีสิทธิ์สำหรับ Gallery</p>";
        echo "<p>🔧 <strong>แนะนำ:</strong> ต้องเพิ่มสิทธิ์ gallery สำหรับ editor ในตาราง permissions</p>";
    } else {
        echo "<p style='color: red;'>❌ ไม่พบ Role Editor ในระบบ</p>";
    }
}

$stmt->close();
$conn->close();
?>

