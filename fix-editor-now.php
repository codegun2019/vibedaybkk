<?php
/**
 * แก้ไขสิทธิ์ editor ทันที
 */
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<h1>🔧 แก้ไขสิทธิ์ Editor ทันที</h1>";

// 1. ตรวจสอบ session ปัจจุบัน
echo "<h2>1️⃣ ข้อมูล Session ปัจจุบัน:</h2>";
if (isset($_SESSION['user_id'])) {
    echo "<p>✅ Logged in</p>";
    echo "<ul>";
    echo "<li><strong>User ID:</strong> {$_SESSION['user_id']}</li>";
    echo "<li><strong>Username:</strong> {$_SESSION['username']}</li>";
    echo "<li><strong>Role:</strong> {$_SESSION['user_role']}</li>";
    echo "</ul>";
} else {
    echo "<p>❌ ไม่ได้ล็อกอิน</p>";
}

// 2. ตรวจสอบข้อมูล permissions ปัจจุบัน
echo "<h2>2️⃣ สิทธิ์ปัจจุบันของ editor สำหรับ homepage:</h2>";
$perm = $conn->query("SELECT * FROM permissions WHERE role_key = 'editor' AND feature = 'homepage'");
if ($perm && $perm->num_rows > 0) {
    $row = $perm->fetch_assoc();
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Feature</th><th>View</th><th>Create</th><th>Edit</th><th>Delete</th></tr>";
    echo "<tr>";
    echo "<td><strong>homepage</strong></td>";
    echo "<td>" . ($row['can_view'] ? '✅' : '❌') . "</td>";
    echo "<td>" . ($row['can_create'] ? '✅' : '❌') . "</td>";
    echo "<td>" . ($row['can_edit'] ? '✅' : '❌') . "</td>";
    echo "<td>" . ($row['can_delete'] ? '✅' : '❌') . "</td>";
    echo "</tr>";
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ ไม่พบข้อมูล permissions สำหรับ editor/homepage</p>";
}

// 3. อัปเดตสิทธิ์ทันที
echo "<h2>3️⃣ กำลังอัปเดตสิทธิ์...</h2>";

$updates = [
    ['role' => 'editor', 'feature' => 'homepage', 'view' => 1, 'create' => 0, 'edit' => 1, 'delete' => 0],
    ['role' => 'editor', 'feature' => 'models', 'view' => 1, 'create' => 1, 'edit' => 1, 'delete' => 0],
    ['role' => 'editor', 'feature' => 'gallery', 'view' => 1, 'create' => 1, 'edit' => 1, 'delete' => 0],
    ['role' => 'editor', 'feature' => 'articles', 'view' => 1, 'create' => 1, 'edit' => 1, 'delete' => 0],
    ['role' => 'editor', 'feature' => 'reviews', 'view' => 1, 'create' => 1, 'edit' => 1, 'delete' => 0],
];

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Feature</th><th>View</th><th>Create</th><th>Edit</th><th>Delete</th><th>Status</th></tr>";

foreach ($updates as $perm) {
    $role = $perm['role'];
    $feature = $perm['feature'];
    
    // ตรวจสอบว่ามีอยู่แล้วหรือไม่
    $check = $conn->query("SELECT id FROM permissions WHERE role_key = '$role' AND feature = '$feature'");
    
    if ($check && $check->num_rows > 0) {
        // Update
        $sql = "UPDATE permissions SET 
                can_view = {$perm['view']}, 
                can_create = {$perm['create']}, 
                can_edit = {$perm['edit']}, 
                can_delete = {$perm['delete']}
                WHERE role_key = '$role' AND feature = '$feature'";
        $result = $conn->query($sql);
        $status = $result ? '✅ Updated' : '❌ Failed: ' . $conn->error;
    } else {
        // Insert
        $sql = "INSERT INTO permissions (role_key, feature, can_view, can_create, can_edit, can_delete) 
                VALUES ('$role', '$feature', {$perm['view']}, {$perm['create']}, {$perm['edit']}, {$perm['delete']})";
        $result = $conn->query($sql);
        $status = $result ? '✅ Created' : '❌ Failed: ' . $conn->error;
    }
    
    echo "<tr>";
    echo "<td><strong>$feature</strong></td>";
    echo "<td>" . ($perm['view'] ? '✅' : '❌') . "</td>";
    echo "<td>" . ($perm['create'] ? '✅' : '❌') . "</td>";
    echo "<td>" . ($perm['edit'] ? '✅' : '❌') . "</td>";
    echo "<td>" . ($perm['delete'] ? '✅' : '❌') . "</td>";
    echo "<td>$status</td>";
    echo "</tr>";
}

echo "</table>";

// 4. ทดสอบฟังก์ชัน has_permission
echo "<h2>4️⃣ ทดสอบฟังก์ชัน has_permission():</h2>";
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'editor') {
    $test_result = has_permission('homepage', 'view');
    echo "<p>has_permission('homepage', 'view') = " . ($test_result ? '✅ TRUE' : '❌ FALSE') . "</p>";
    
    $test_result2 = has_permission('homepage', 'edit');
    echo "<p>has_permission('homepage', 'edit') = " . ($test_result2 ? '✅ TRUE' : '❌ FALSE') . "</p>";
} else {
    echo "<p>⚠️ ไม่ได้ล็อกอินด้วย editor หรือ role ไม่ถูกต้อง</p>";
}

// 5. สรุป
echo "<hr>";
echo "<h2>✅ เสร็จสิ้น!</h2>";
echo "<p><strong>ขั้นตอนต่อไป:</strong></p>";
echo "<ol>";
echo "<li>ล็อกเอาท์และล็อกอินใหม่ด้วย editor</li>";
echo "<li><a href='admin/homepage/' target='_blank'>ทดสอบเข้าหน้า Homepage Management</a></li>";
echo "<li>ถ้ายังไม่ได้ ให้ clear cache บราวเซอร์</li>";
echo "</ol>";

$conn->close();
?>

