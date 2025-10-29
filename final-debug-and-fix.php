<?php
/**
 * ตรวจสอบและแก้ไขปัญหาครั้งสุดท้าย
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$_SESSION['user_id'] = 5;
$_SESSION['username'] = '0900000020';
$_SESSION['user_role'] = 'editor';

require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<h1>🔍 ตรวจสอบและแก้ไขปัญหาครั้งสุดท้าย</h1>";
echo "<style>
body { font-family: Arial; padding: 20px; background: #f5f5f5; }
.box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.success { color: #22c55e; font-weight: bold; }
.error { color: #ef4444; font-weight: bold; }
table { border-collapse: collapse; width: 100%; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
th { background: #f3f4f6; }
pre { background: #f9fafb; padding: 15px; border-radius: 4px; overflow: auto; }
.button { display: inline-block; padding: 10px 20px; background: #3b82f6; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
.button:hover { background: #2563eb; }
</style>";

// 1. ตรวจสอบ Session
echo "<div class='box'>";
echo "<h2>1️⃣ Session Information</h2>";
echo "<table>";
echo "<tr><td><strong>user_id</strong></td><td>{$_SESSION['user_id']}</td></tr>";
echo "<tr><td><strong>username</strong></td><td>{$_SESSION['username']}</td></tr>";
echo "<tr><td><strong>user_role</strong></td><td><span class='success'>{$_SESSION['user_role']}</span></td></tr>";
echo "</table>";
echo "</div>";

// 2. ตรวจสอบ Database
echo "<div class='box'>";
echo "<h2>2️⃣ Database Permissions</h2>";
$check_sql = "SELECT * FROM permissions WHERE role_key = 'editor' AND feature = 'homepage'";
echo "<p><strong>SQL:</strong> <code>$check_sql</code></p>";

$result = $conn->query($check_sql);
if ($result && $result->num_rows > 0) {
    $perm = $result->fetch_assoc();
    echo "<table>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    echo "<tr><td>id</td><td>{$perm['id']}</td></tr>";
    echo "<tr><td>role_key</td><td>{$perm['role_key']}</td></tr>";
    echo "<tr><td>feature</td><td>{$perm['feature']}</td></tr>";
    echo "<tr><td>can_view</td><td>" . ($perm['can_view'] == 1 ? "<span class='success'>✅ 1 (TRUE)</span>" : "<span class='error'>❌ 0 (FALSE)</span>") . "</td></tr>";
    echo "<tr><td>can_create</td><td>" . ($perm['can_create'] == 1 ? "✅ 1" : "❌ 0") . "</td></tr>";
    echo "<tr><td>can_edit</td><td>" . ($perm['can_edit'] == 1 ? "✅ 1" : "❌ 0") . "</td></tr>";
    echo "<tr><td>can_delete</td><td>" . ($perm['can_delete'] == 1 ? "✅ 1" : "❌ 0") . "</td></tr>";
    echo "</table>";
    
    if ($perm['can_view'] != 1) {
        echo "<p class='error'>⚠️ ปัญหา: can_view ไม่ได้เป็น 1!</p>";
        echo "<p>กำลังแก้ไข...</p>";
        $conn->query("UPDATE permissions SET can_view = 1 WHERE role_key = 'editor' AND feature = 'homepage'");
        echo "<p class='success'>✅ แก้ไขแล้ว</p>";
    }
} else {
    echo "<p class='error'>❌ ไม่พบ permission record!</p>";
    echo "<p>กำลังสร้างใหม่...</p>";
    $conn->query("INSERT INTO permissions (role_key, feature, can_view, can_create, can_edit, can_delete) VALUES ('editor', 'homepage', 1, 0, 1, 0)");
    echo "<p class='success'>✅ สร้างใหม่แล้ว</p>";
}
echo "</div>";

// 3. ทดสอบฟังก์ชัน is_logged_in()
echo "<div class='box'>";
echo "<h2>3️⃣ Test is_logged_in()</h2>";
$logged_in = is_logged_in();
echo "<p>Result: " . ($logged_in ? "<span class='success'>✅ TRUE</span>" : "<span class='error'>❌ FALSE</span>") . "</p>";
if (!$logged_in) {
    echo "<p class='error'>⚠️ ฟังก์ชัน is_logged_in() คืนค่า FALSE!</p>";
    echo "<p>ตรวจสอบว่า session มี user_id และ user_role หรือไม่</p>";
}
echo "</div>";

// 4. ทดสอบฟังก์ชัน has_permission()
echo "<div class='box'>";
echo "<h2>4️⃣ Test has_permission('homepage', 'view')</h2>";

// Test โดยตรงก่อน
$direct_sql = "SELECT can_view FROM permissions WHERE role_key = 'editor' AND feature = 'homepage'";
$direct_result = $conn->query($direct_sql);
if ($direct_result && $direct_result->num_rows > 0) {
    $direct_row = $direct_result->fetch_assoc();
    echo "<p>SQL คืนค่า: <strong>can_view = " . $direct_row['can_view'] . "</strong></p>";
}

// ทดสอบฟังก์ชัน
$has_perm = has_permission('homepage', 'view');
echo "<p>has_permission('homepage', 'view') = " . ($has_perm ? "<span class='success'>✅ TRUE</span>" : "<span class='error'>❌ FALSE</span>") . "</p>";

if (!$has_perm) {
    echo "<p class='error'>⚠️ ฟังก์ชัน has_permission() คืนค่า FALSE!</p>";
    echo "<p>ปัญหาอาจอยู่ที่:</p>";
    echo "<ul>";
    echo "<li>Query ไม่เจอข้อมูล</li>";
    echo "<li>can_view เป็น 0</li>";
    echo "<li>Logic ในฟังก์ชันผิด</li>";
    echo "</ul>";
}
echo "</div>";

// 5. ตรวจสอบ admin role
echo "<div class='box'>";
echo "<h2>5️⃣ Test Admin Override</h2>";
$is_admin = in_array($_SESSION['user_role'], ['programmer', 'admin']);
echo "<p>Is Programmer/Admin: " . ($is_admin ? "<span class='success'>✅ TRUE</span>" : "❌ FALSE") . "</p>";
echo "<p>Current role: <strong>{$_SESSION['user_role']}</strong></p>";
echo "</div>";

// 6. แสดงโค้ดฟังก์ชัน
echo "<div class='box'>";
echo "<h2>6️⃣ has_permission() Function Code</h2>";
$func_code = file_get_contents('includes/functions.php');
if (preg_match('/\/\/ Check if user has permission.*?^}/ms', $func_code, $matches)) {
    echo "<pre>" . htmlspecialchars($matches[0]) . "</pre>";
}
echo "</div>";

// 7. สรุปและแนะนำ
echo "<div class='box'>";
echo "<h2>7️⃣ สรุปและการแก้ไข</h2>";

if ($logged_in && $has_perm) {
    echo "<p class='success'>✅ <strong>ทุกอย่างเรียบร้อย! Editor ควรเข้าได้</strong></p>";
    echo "<p><a href='admin/homepage/' class='button' target='_blank'>ทดสอบเข้าหน้า Homepage</a></p>";
} else {
    echo "<p class='error'>❌ <strong>ยังมีปัญหา</strong></p>";
    echo "<ul>";
    if (!$logged_in) echo "<li class='error'>is_logged_in() คืนค่า FALSE</li>";
    if (!$has_perm) echo "<li class='error'>has_permission() คืนค่า FALSE</li>";
    echo "</ul>";
    
    echo "<h3>แนวทางแก้ไข:</h3>";
    echo "<ol>";
    echo "<li><a href='force-fix-editor.php' class='button'>รันสคริปต์แก้ไขสิทธิ์ใหม่</a></li>";
    echo "<li>Refresh หน้านี้</li>";
    echo "<li>ล็อกเอาท์และล็อกอินใหม่</li>";
    echo "</ol>";
}
echo "</div>";

$conn->close();
?>

