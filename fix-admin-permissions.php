<?php
/**
 * Fix Admin Permissions
 * ตรวจสอบและแก้ไขสิทธิ์ในระบบแอดมิน
 */

require_once 'includes/config.php';

echo "<h1>VIBEDAYBKK - Fix Admin Permissions</h1>";

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    echo "❌ ไม่สามารถเชื่อมต่อฐานข้อมูลได้";
    exit;
}

echo "<h2>1. ตรวจสอบตาราง roles</h2>";
$roles = db_get_rows($conn, "SELECT * FROM roles ORDER BY level ASC");
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Name</th><th>Key</th><th>Level</th></tr>";
foreach ($roles as $role) {
    echo "<tr>";
    echo "<td>" . ($role['id'] ?? '') . "</td>";
    echo "<td>" . ($role['name'] ?? $role['role_key'] ?? '') . "</td>";
    echo "<td>" . ($role['role_key'] ?? '') . "</td>";
    echo "<td>" . ($role['level'] ?? '') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>2. ตรวจสอบตาราง permissions</h2>";
$permissions = db_get_rows($conn, "SELECT * FROM permissions ORDER BY role_key, feature");
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Role Key</th><th>Feature</th><th>View</th><th>Create</th><th>Edit</th><th>Delete</th><th>Export</th></tr>";
foreach ($permissions as $perm) {
    echo "<tr>";
    echo "<td>" . $perm['role_key'] . "</td>";
    echo "<td>" . $perm['feature'] . "</td>";
    echo "<td>" . ($perm['can_view'] ? '✓' : '✗') . "</td>";
    echo "<td>" . ($perm['can_create'] ? '✓' : '✗') . "</td>";
    echo "<td>" . ($perm['can_edit'] ? '✓' : '✗') . "</td>";
    echo "<td>" . ($perm['can_delete'] ? '✓' : '✗') . "</td>";
    echo "<td>" . ($perm['can_export'] ? '✓' : '✗') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>3. แก้ไขสิทธิ์สำหรับ Editor และ Editor+</h2>";

// Features ที่ต้องแก้ไข
$features_to_fix = [
    'menus' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
    'homepage' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
    'reviews' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
    'gallery' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
    'categories' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true]
];

// Roles ที่ต้องแก้ไข
$roles_to_fix = ['editor', 'editor+'];

$updated_count = 0;

foreach ($roles_to_fix as $role_key) {
    echo "<h3>กำลังแก้ไข: $role_key</h3>";
    
    foreach ($features_to_fix as $feature => $perms) {
        // ตรวจสอบว่ามี record อยู่แล้วหรือไม่
        $check_sql = "SELECT * FROM permissions WHERE role_key = ? AND feature = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param('ss', $role_key, $feature);
        $stmt->execute();
        $result = $stmt->get_result();
        $existing = $result->fetch_assoc();
        
        if ($existing) {
            // Update existing record
            $update_sql = "UPDATE permissions SET 
                           can_view = ?,
                           can_create = ?,
                           can_edit = ?,
                           can_delete = ?,
                           can_export = 0
                           WHERE role_key = ? AND feature = ?";
            $stmt = $conn->prepare($update_sql);
            $view = $perms['view'] ? 1 : 0;
            $create = $perms['create'] ? 1 : 0;
            $edit = $perms['edit'] ? 1 : 0;
            $delete = $perms['delete'] ? 1 : 0;
            $stmt->bind_param('iiiiss', $view, $create, $edit, $delete, $role_key, $feature);
            $stmt->execute();
            
            echo "✅ แก้ไข: $role_key -> $feature<br>";
        } else {
            // Insert new record
            $insert_sql = "INSERT INTO permissions (role_key, feature, can_view, can_create, can_edit, can_delete, can_export) 
                           VALUES (?, ?, ?, ?, ?, ?, 0)";
            $stmt = $conn->prepare($insert_sql);
            $view = $perms['view'] ? 1 : 0;
            $create = $perms['create'] ? 1 : 0;
            $edit = $perms['edit'] ? 1 : 0;
            $delete = $perms['delete'] ? 1 : 0;
            $stmt->bind_param('ssiiii', $role_key, $feature, $view, $create, $edit, $delete);
            $stmt->execute();
            
            echo "✅ เพิ่มใหม่: $role_key -> $feature<br>";
        }
        
        $updated_count++;
        $stmt->close();
    }
}

echo "<h2>4. ผลลัพธ์</h2>";
echo "✅ อัปเดตทั้งหมด: $updated_count รายการ<br>";

echo "<h3>สิทธิ์หลังการแก้ไข:</h3>";
$updated_permissions = db_get_rows($conn, "SELECT * FROM permissions WHERE role_key IN ('editor', 'editor+') ORDER BY role_key, feature");
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Role Key</th><th>Feature</th><th>View</th><th>Create</th><th>Edit</th><th>Delete</th><th>Export</th></tr>";
foreach ($updated_permissions as $perm) {
    echo "<tr>";
    echo "<td>" . $perm['role_key'] . "</td>";
    echo "<td>" . $perm['feature'] . "</td>";
    echo "<td>" . ($perm['can_view'] ? '✓' : '✗') . "</td>";
    echo "<td>" . ($perm['can_create'] ? '✓' : '✗') . "</td>";
    echo "<td>" . ($perm['can_edit'] ? '✓' : '✗') . "</td>";
    echo "<td>" . ($perm['can_delete'] ? '✓' : '✗') . "</td>";
    echo "<td>" . ($perm['can_export'] ? '✓' : '✗') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>5. สรุป</h2>";
echo "<p>✅ การแก้ไขเสร็จสิ้นแล้ว</p>";
echo "<p>ตอนนี้คุณสามารถ:</p>";
echo "<ul>";
echo "<li>แก้ไขเมนูได้แล้ว</li>";
echo "<li>แก้ไขรีวิวได้แล้ว</li>";
echo "<li>เพิ่มรูปในแกลเลอรี่ได้แล้ว</li>";
echo "<li>แก้ไขหน้าแรกได้แล้ว</li>";
echo "<li>แก้ไขหมวดหมู่แฟชั่นได้แล้ว</li>";
echo "</ul>";

$conn->close();
?>
