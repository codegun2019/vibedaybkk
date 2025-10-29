<?php
/**
 * จำลองการเข้าถึงหน้า homepage ในฐานะ editor
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 จำลองการเข้าถึงหน้า Homepage ในฐานะ Editor</h1>";
echo "<style>
body { font-family: Arial; padding: 20px; }
.step { background: #f0f0f0; padding: 15px; margin: 10px 0; border-left: 4px solid #333; }
.success { color: green; }
.error { color: red; font-weight: bold; }
pre { background: #f5f5f5; padding: 10px; overflow: auto; }
table { border-collapse: collapse; width: 100%; margin: 20px 0; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
th { background: #e0e0e0; }
</style>";

// Step 1: Start session and set as editor
session_start();
$_SESSION['user_id'] = 5;
$_SESSION['username'] = '0900000020';
$_SESSION['user_role'] = 'editor';

echo "<div class='step'>";
echo "<h2>Step 1: ตั้งค่า Session</h2>";
echo "<table>";
echo "<tr><th>Key</th><th>Value</th></tr>";
echo "<tr><td>user_id</td><td>{$_SESSION['user_id']}</td></tr>";
echo "<tr><td>username</td><td>{$_SESSION['username']}</td></tr>";
echo "<tr><td>user_role</td><td class='success'>{$_SESSION['user_role']}</td></tr>";
echo "</table>";
echo "</div>";

// Step 2: Load config and functions
require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<div class='step'>";
echo "<h2>Step 2: โหลด Config & Functions</h2>";
echo "<p class='success'>✅ โหลดสำเร็จ</p>";
echo "</div>";

// Step 3: Check database permissions
echo "<div class='step'>";
echo "<h2>Step 3: ตรวจสอบ Permissions ในฐานข้อมูล</h2>";
$perm_check = $conn->query("SELECT * FROM permissions WHERE role_key = 'editor' AND feature = 'homepage'");
if ($perm_check && $perm_check->num_rows > 0) {
    $perm_row = $perm_check->fetch_assoc();
    echo "<table>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    foreach ($perm_row as $key => $value) {
        $display_value = is_numeric($value) ? ($value ? '✅ TRUE (1)' : '❌ FALSE (0)') : $value;
        echo "<tr><td>$key</td><td>$display_value</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>❌ ไม่พบ permission สำหรับ editor/homepage ในฐานข้อมูล!</p>";
}
echo "</div>";

// Step 4: Test is_logged_in()
echo "<div class='step'>";
echo "<h2>Step 4: ทดสอบ is_logged_in()</h2>";
$is_logged = is_logged_in();
echo "<p>ผลลัพธ์: " . ($is_logged ? "<span class='success'>✅ TRUE</span>" : "<span class='error'>❌ FALSE</span>") . "</p>";
echo "</div>";

// Step 5: Test has_permission()
echo "<div class='step'>";
echo "<h2>Step 5: ทดสอบ has_permission('homepage', 'view')</h2>";

// Show the actual SQL being executed
$role_key = $_SESSION['user_role'];
$feature = 'homepage';
echo "<p><strong>SQL Query:</strong></p>";
echo "<pre>SELECT can_view, can_create, can_edit, can_delete, can_export 
FROM permissions 
WHERE role_key = '$role_key' AND feature = '$feature'</pre>";

// Execute manually to see result
$manual_result = $conn->query("SELECT can_view, can_create, can_edit, can_delete, can_export FROM permissions WHERE role_key = '$role_key' AND feature = '$feature'");
if ($manual_result && $manual_result->num_rows > 0) {
    $manual_row = $manual_result->fetch_assoc();
    echo "<p class='success'>✅ Query คืนผลลัพธ์:</p>";
    echo "<table>";
    echo "<tr><th>can_view</th><th>can_create</th><th>can_edit</th><th>can_delete</th><th>can_export</th></tr>";
    echo "<tr>";
    echo "<td>" . ($manual_row['can_view'] ? '✅ 1' : '❌ 0') . "</td>";
    echo "<td>" . ($manual_row['can_create'] ? '✅ 1' : '❌ 0') . "</td>";
    echo "<td>" . ($manual_row['can_edit'] ? '✅ 1' : '❌ 0') . "</td>";
    echo "<td>" . ($manual_row['can_delete'] ? '✅ 1' : '❌ 0') . "</td>";
    echo "<td>" . ($manual_row['can_export'] !== null ? ($manual_row['can_export'] ? '✅ 1' : '❌ 0') : 'NULL') . "</td>";
    echo "</tr>";
    echo "</table>";
} else {
    echo "<p class='error'>❌ Query ไม่คืนผลลัพธ์!</p>";
}

// Now test the actual function
$has_perm = has_permission('homepage', 'view');
echo "<p><strong>has_permission('homepage', 'view'):</strong> " . ($has_perm ? "<span class='success'>✅ TRUE</span>" : "<span class='error'>❌ FALSE</span>") . "</p>";
echo "</div>";

// Step 6: Test require_permission() - but catch the redirect
echo "<div class='step'>";
echo "<h2>Step 6: จำลอง require_permission('homepage', 'view')</h2>";
echo "<p>กำลังทดสอบ...</p>";

// Save output buffer
ob_start();
$redirect_caught = false;
$error_caught = null;

try {
    // This will redirect if no permission
    if (!has_permission('homepage', 'view')) {
        $error_caught = "❌ has_permission คืนค่า FALSE - จะถูก redirect";
        $redirect_caught = true;
    } else {
        echo "<p class='success'>✅ has_permission คืนค่า TRUE - สามารถเข้าถึงได้!</p>";
    }
} catch (Exception $e) {
    $error_caught = "Exception: " . $e->getMessage();
}

$output = ob_get_clean();
echo $output;

if ($error_caught) {
    echo "<p class='error'>$error_caught</p>";
}
echo "</div>";

// Step 7: Summary
echo "<div class='step'>";
echo "<h2>Step 7: สรุปผลการทดสอบ</h2>";

$can_access = $is_logged && $has_perm;

if ($can_access) {
    echo "<p class='success'>✅ <strong>ผลลัพธ์: Editor ควรเข้าถึงหน้า Homepage ได้</strong></p>";
    echo "<p>ลองเข้าจริง: <a href='admin/homepage/' target='_blank'>คลิกที่นี่</a></p>";
} else {
    echo "<p class='error'>❌ <strong>ผลลัพธ์: Editor ไม่สามารถเข้าถึงได้</strong></p>";
    echo "<p><strong>สาเหตุ:</strong></p>";
    echo "<ul>";
    if (!$is_logged) echo "<li class='error'>ไม่ได้ล็อกอิน</li>";
    if (!$has_perm) echo "<li class='error'>ไม่มีสิทธิ์ (has_permission คืนค่า FALSE)</li>";
    echo "</ul>";
}
echo "</div>";

// Step 8: Show current functions.php code
echo "<div class='step'>";
echo "<h2>Step 8: ตรวจสอบโค้ดฟังก์ชัน</h2>";
$functions_file = file_get_contents('includes/functions.php');
if (preg_match('/function has_permission\([^}]+\}/s', $functions_file, $matches)) {
    echo "<p><strong>ฟังก์ชัน has_permission():</strong></p>";
    echo "<pre>" . htmlspecialchars($matches[0]) . "</pre>";
}
echo "</div>";

$conn->close();
?>

