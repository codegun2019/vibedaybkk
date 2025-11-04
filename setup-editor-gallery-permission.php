<?php
/**
 * ตั้งค่าสิทธิ์ให้ Editor สำหรับ Gallery
 */
require_once 'includes/config.php';

echo "<h2>⚙️ ตั้งค่าสิทธิ์ Gallery สำหรับ Editor</h2>";

// ตรวจสอบว่ามี role editor หรือไม่
$stmt = $conn->prepare("SELECT * FROM roles WHERE role_key = 'editor'");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<p style='color: red;'>❌ ไม่พบ Role Editor ในระบบ</p>";
    exit;
}

$editor_role = $result->fetch_assoc();
echo "<p>✅ พบ Editor Role (ID: {$editor_role['id']}, Key: {$editor_role['role_key']})</p>";
$stmt->close();

// ตรวจสอบว่ามีสิทธิ์ gallery อยู่แล้วหรือไม่
$stmt = $conn->prepare("
    SELECT * FROM permissions 
    WHERE role_key = 'editor' AND feature = 'gallery'
");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<p>⚠️ มีสิทธิ์ gallery สำหรับ editor อยู่แล้ว</p>";
    echo "<p>📝 กำลังอัปเดตสิทธิ์...</p>";
    
    // อัปเดตสิทธิ์
    $update_stmt = $conn->prepare("
        UPDATE permissions 
        SET can_view = 1, 
            can_create = 1, 
            can_edit = 1, 
            can_delete = 1, 
            can_export = 1,
            updated_at = NOW()
        WHERE role_key = 'editor' AND feature = 'gallery'
    ");
    
    if ($update_stmt->execute()) {
        echo "<p style='color: green;'>✅ อัปเดตสิทธิ์สำเร็จ!</p>";
    } else {
        echo "<p style='color: red;'>❌ ไม่สามารถอัปเดตสิทธิ์ได้: " . $update_stmt->error . "</p>";
    }
    $update_stmt->close();
} else {
    echo "<p>📝 กำลังสร้างสิทธิ์ใหม่...</p>";
    
    // สร้างสิทธิ์ใหม่
    $insert_stmt = $conn->prepare("
        INSERT INTO permissions (role_key, feature, can_view, can_create, can_edit, can_delete, can_export, created_at)
        VALUES ('editor', 'gallery', 1, 1, 1, 1, 1, NOW())
    ");
    
    if ($insert_stmt->execute()) {
        echo "<p style='color: green;'>✅ สร้างสิทธิ์สำเร็จ!</p>";
    } else {
        echo "<p style='color: red;'>❌ ไม่สามารถสร้างสิทธิ์ได้: " . $insert_stmt->error . "</p>";
    }
    $insert_stmt->close();
}

$stmt->close();

// แสดงสิทธิ์ที่อัปเดตแล้ว
echo "<hr>";
echo "<h3>📋 สิทธิ์ Gallery ของ Editor หลังอัปเดต:</h3>";

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
}

$stmt->close();
$conn->close();

echo "<hr>";
echo "<p><a href='admin/gallery/'>ไปที่หน้าจัดการแกลเลอรี่</a></p>";
echo "<p><a href='check-editor-gallery-permission.php'>ตรวจสอบสิทธิ์</a></p>";
?>

