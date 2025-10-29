<?php
/**
 * ตั้งค่าสิทธิ์ให้ Editor
 */
require_once 'includes/config.php';

echo "<h1>⚙️ ตั้งค่าสิทธิ์ให้ Editor</h1>";

$role_key = 'editor';
echo "<p>✅ กำลังตั้งค่าสิทธิ์สำหรับ: <strong>$role_key</strong></p>";

// สิทธิ์ที่ต้องการให้ editor
$permissions_to_set = [
    'homepage' => ['view' => 1, 'create' => 0, 'edit' => 1, 'delete' => 0],
    'models' => ['view' => 1, 'create' => 1, 'edit' => 1, 'delete' => 0],
    'gallery' => ['view' => 1, 'create' => 1, 'edit' => 1, 'delete' => 0],
    'articles' => ['view' => 1, 'create' => 1, 'edit' => 1, 'delete' => 0],
    'article_categories' => ['view' => 1, 'create' => 0, 'edit' => 0, 'delete' => 0],
    'reviews' => ['view' => 1, 'create' => 1, 'edit' => 1, 'delete' => 0],
    'categories' => ['view' => 1, 'create' => 0, 'edit' => 0, 'delete' => 0],
    'menus' => ['view' => 1, 'create' => 0, 'edit' => 0, 'delete' => 0],
    'bookings' => ['view' => 1, 'create' => 0, 'edit' => 1, 'delete' => 0],
    'contacts' => ['view' => 1, 'create' => 0, 'edit' => 0, 'delete' => 0],
];

echo "<h2>📝 กำลังตั้งค่าสิทธิ์...</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Feature</th><th>View</th><th>Create</th><th>Edit</th><th>Delete</th><th>Status</th></tr>";

foreach ($permissions_to_set as $feature => $perms) {
    // ตรวจสอบว่ามีอยู่แล้วหรือไม่
    $existing = db_get_row($conn, "SELECT id FROM permissions WHERE role_key = '$role_key' AND feature = '$feature'");
    
    if ($existing) {
        // Update
        $sql = "UPDATE permissions SET 
                can_view = {$perms['view']}, 
                can_create = {$perms['create']}, 
                can_edit = {$perms['edit']}, 
                can_delete = {$perms['delete']}
                WHERE role_key = '$role_key' AND feature = '$feature'";
        $result = $conn->query($sql);
        $status = $result ? '✅ Updated' : '❌ Failed';
    } else {
        // Insert
        $sql = "INSERT INTO permissions (role_key, feature, can_view, can_create, can_edit, can_delete) 
                VALUES ('$role_key', '$feature', {$perms['view']}, {$perms['create']}, {$perms['edit']}, {$perms['delete']})";
        $result = $conn->query($sql);
        $status = $result ? '✅ Created' : '❌ Failed';
    }
    
    echo "<tr>";
    echo "<td><strong>$feature</strong></td>";
    echo "<td>" . ($perms['view'] ? '✅' : '❌') . "</td>";
    echo "<td>" . ($perms['create'] ? '✅' : '❌') . "</td>";
    echo "<td>" . ($perms['edit'] ? '✅' : '❌') . "</td>";
    echo "<td>" . ($perms['delete'] ? '✅' : '❌') . "</td>";
    echo "<td>$status</td>";
    echo "</tr>";
}

echo "</table>";

echo "<hr>";
echo "<h2>✅ เสร็จสิ้น!</h2>";
echo "<p><a href='test-editor-access.php'>ทดสอบสิทธิ์ที่ตั้งค่า</a></p>";
echo "<p><a href='admin/homepage/'>ทดสอบเข้าหน้า Homepage Management</a></p>";
?>

