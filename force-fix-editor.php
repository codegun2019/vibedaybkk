<?php
/**
 * บังคับแก้ไขสิทธิ์ Editor ให้สมบูรณ์
 */
session_start();
require_once 'includes/config.php';

echo "<h1>🔧 บังคับแก้ไขสิทธิ์ Editor</h1>";
echo "<style>
table { border-collapse: collapse; width: 100%; margin: 20px 0; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
th { background: #f0f0f0; }
.success { color: green; }
.error { color: red; }
</style>";

// ลบ permissions เก่าทั้งหมดของ editor
echo "<h2>1️⃣ ลบ permissions เก่า...</h2>";
$delete_result = $conn->query("DELETE FROM permissions WHERE role_key = 'editor'");
echo $delete_result ? "<p class='success'>✅ ลบสำเร็จ</p>" : "<p class='error'>❌ ล้มเหลว: " . $conn->error . "</p>";

// เพิ่ม permissions ใหม่ทั้งหมด
echo "<h2>2️⃣ เพิ่ม permissions ใหม่...</h2>";

$permissions = [
    // Feature            View  Create  Edit  Delete
    ['homepage',          1,    0,      1,    0],
    ['models',            1,    1,      1,    0],
    ['gallery',           1,    1,      1,    0],
    ['articles',          1,    1,      1,    0],
    ['article_categories',1,    0,      0,    0],
    ['reviews',           1,    1,      1,    0],
    ['categories',        1,    1,      1,    0],
    ['menus',             1,    0,      0,    0],
    ['bookings',          1,    0,      1,    0],
    ['contacts',          1,    0,      0,    0],
    ['users',             1,    0,      0,    0],
    ['settings',          1,    0,      0,    0],
];

echo "<table>";
echo "<tr><th>Feature</th><th>View</th><th>Create</th><th>Edit</th><th>Delete</th><th>Status</th></tr>";

foreach ($permissions as $perm) {
    list($feature, $view, $create, $edit, $delete) = $perm;
    
    $sql = "INSERT INTO permissions (role_key, feature, can_view, can_create, can_edit, can_delete) 
            VALUES ('editor', '$feature', $view, $create, $edit, $delete)";
    
    $result = $conn->query($sql);
    $status = $result ? '<span class="success">✅ OK</span>' : '<span class="error">❌ Error: ' . $conn->error . '</span>';
    
    echo "<tr>";
    echo "<td><strong>$feature</strong></td>";
    echo "<td>" . ($view ? '✅' : '❌') . "</td>";
    echo "<td>" . ($create ? '✅' : '❌') . "</td>";
    echo "<td>" . ($edit ? '✅' : '❌') . "</td>";
    echo "<td>" . ($delete ? '✅' : '❌') . "</td>";
    echo "<td>$status</td>";
    echo "</tr>";
}

echo "</table>";

// ตรวจสอบผลลัพธ์
echo "<h2>3️⃣ ตรวจสอบผลลัพธ์...</h2>";
$check = $conn->query("SELECT * FROM permissions WHERE role_key = 'editor' ORDER BY feature");
echo "<p>พบ permissions ทั้งหมด: <strong>" . $check->num_rows . "</strong> รายการ</p>";

// ตรวจสอบ homepage โดยเฉพาะ
$homepage_check = $conn->query("SELECT * FROM permissions WHERE role_key = 'editor' AND feature = 'homepage'");
if ($homepage_check && $homepage_check->num_rows > 0) {
    $row = $homepage_check->fetch_assoc();
    echo "<p class='success'>✅ พบ permission สำหรับ homepage:</p>";
    echo "<ul>";
    echo "<li>can_view: " . ($row['can_view'] ? '✅ 1' : '❌ 0') . "</li>";
    echo "<li>can_create: " . ($row['can_create'] ? '✅ 1' : '❌ 0') . "</li>";
    echo "<li>can_edit: " . ($row['can_edit'] ? '✅ 1' : '❌ 0') . "</li>";
    echo "<li>can_delete: " . ($row['can_delete'] ? '✅ 1' : '❌ 0') . "</li>";
    echo "</ul>";
} else {
    echo "<p class='error'>❌ ยังไม่พบ permission สำหรับ homepage!</p>";
}

echo "<hr>";
echo "<h2>✅ เสร็จสิ้น!</h2>";
echo "<h3>🎯 ขั้นตอนต่อไป:</h3>";
echo "<ol>";
echo "<li><strong>ล็อกเอาท์</strong> จากระบบ: <a href='admin/logout.php' target='_blank'>คลิกที่นี่</a></li>";
echo "<li><strong>ล็อกอิน</strong>ใหม่ด้วย editor (username: 0900000020)</li>";
echo "<li><strong>ทดสอบ</strong>เข้าหน้า: <a href='admin/homepage/' target='_blank'>จัดการหน้าแรก</a></li>";
echo "</ol>";

echo "<p><a href='debug-permissions.php'>🔍 ดู Debug Information</a></p>";

$conn->close();
?>

