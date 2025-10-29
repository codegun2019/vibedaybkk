<?php
/**
 * Debug Permissions System
 */
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<h1>🔍 Debug ระบบสิทธิ์ (Permissions)</h1>";
echo "<style>
table { border-collapse: collapse; width: 100%; margin: 20px 0; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
th { background: #f0f0f0; }
.success { color: green; font-weight: bold; }
.error { color: red; font-weight: bold; }
pre { background: #f5f5f5; padding: 10px; overflow: auto; }
</style>";

// 1. Session Info
echo "<h2>1️⃣ Session Information:</h2>";
if (isset($_SESSION['user_id'])) {
    echo "<table>";
    echo "<tr><th>Key</th><th>Value</th></tr>";
    echo "<tr><td>user_id</td><td class='success'>{$_SESSION['user_id']}</td></tr>";
    echo "<tr><td>username</td><td class='success'>{$_SESSION['username']}</td></tr>";
    echo "<tr><td>user_role</td><td class='success'>{$_SESSION['user_role']}</td></tr>";
    echo "</table>";
} else {
    echo "<p class='error'>❌ ไม่ได้ล็อกอิน</p>";
}

// 2. Database Permissions
echo "<h2>2️⃣ Permissions ในฐานข้อมูล (role_key = 'editor'):</h2>";
$result = $conn->query("SELECT * FROM permissions WHERE role_key = 'editor' ORDER BY feature");
echo "<table>";
echo "<tr><th>ID</th><th>Role Key</th><th>Feature</th><th>View</th><th>Create</th><th>Edit</th><th>Delete</th></tr>";
$has_homepage = false;
while ($row = $result->fetch_assoc()) {
    if ($row['feature'] === 'homepage') $has_homepage = true;
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['role_key']}</td>";
    echo "<td><strong>{$row['feature']}</strong></td>";
    echo "<td>" . ($row['can_view'] ? '✅' : '❌') . "</td>";
    echo "<td>" . ($row['can_create'] ? '✅' : '❌') . "</td>";
    echo "<td>" . ($row['can_edit'] ? '✅' : '❌') . "</td>";
    echo "<td>" . ($row['can_delete'] ? '✅' : '❌') . "</td>";
    echo "</tr>";
}
echo "</table>";

if (!$has_homepage) {
    echo "<p class='error'>❌ ไม่พบ permission สำหรับ feature 'homepage'</p>";
}

// 3. Test has_permission() function
echo "<h2>3️⃣ ทดสอบฟังก์ชัน has_permission():</h2>";

if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'editor') {
    echo "<table>";
    echo "<tr><th>Feature</th><th>Action</th><th>Result</th><th>Expected</th></tr>";
    
    $tests = [
        ['feature' => 'homepage', 'action' => 'view', 'expected' => true],
        ['feature' => 'homepage', 'action' => 'edit', 'expected' => true],
        ['feature' => 'homepage', 'action' => 'create', 'expected' => false],
        ['feature' => 'homepage', 'action' => 'delete', 'expected' => false],
    ];
    
    foreach ($tests as $test) {
        $result = has_permission($test['feature'], $test['action']);
        $match = ($result == $test['expected']);
        echo "<tr>";
        echo "<td>{$test['feature']}</td>";
        echo "<td>{$test['action']}</td>";
        echo "<td>" . ($result ? '✅ TRUE' : '❌ FALSE') . "</td>";
        echo "<td>" . ($test['expected'] ? '✅ TRUE' : '❌ FALSE') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>❌ ไม่ได้ล็อกอินด้วย editor role</p>";
}

// 4. Check what admin/homepage/index.php uses
echo "<h2>4️⃣ ตรวจสอบไฟล์ admin/homepage/index.php:</h2>";
$homepage_file = file_get_contents('admin/homepage/index.php');
if (preg_match('/require_permission\([\'"]([^\'"]+)[\'"]/', $homepage_file, $matches)) {
    echo "<p>ไฟล์ใช้: <code>require_permission('{$matches[1]}', 'view')</code></p>";
    
    // Test this exact permission
    if (isset($_SESSION['user_role'])) {
        $test_result = has_permission($matches[1], 'view');
        echo "<p>ผลการทดสอบ: " . ($test_result ? "<span class='success'>✅ TRUE (ควรเข้าได้)</span>" : "<span class='error'>❌ FALSE (เข้าไม่ได้)</span>") . "</p>";
    }
}

// 5. Raw SQL Test
echo "<h2>5️⃣ ทดสอบ SQL Query โดยตรง:</h2>";
if (isset($_SESSION['user_role'])) {
    $role_key = $_SESSION['user_role'];
    $feature = 'homepage';
    
    $sql = "SELECT can_view, can_create, can_edit, can_delete 
            FROM permissions 
            WHERE role_key = '$role_key' AND feature = '$feature'";
    
    echo "<pre>SQL: $sql</pre>";
    
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<table>";
        echo "<tr><th>can_view</th><th>can_create</th><th>can_edit</th><th>can_delete</th></tr>";
        echo "<tr>";
        echo "<td>" . ($row['can_view'] ? '✅ 1' : '❌ 0') . "</td>";
        echo "<td>" . ($row['can_create'] ? '✅ 1' : '❌ 0') . "</td>";
        echo "<td>" . ($row['can_edit'] ? '✅ 1' : '❌ 0') . "</td>";
        echo "<td>" . ($row['can_delete'] ? '✅ 1' : '❌ 0') . "</td>";
        echo "</tr>";
        echo "</table>";
    } else {
        echo "<p class='error'>❌ ไม่พบข้อมูล! Query ไม่คืนผลลัพธ์</p>";
    }
}

// 6. Show all permissions structure
echo "<h2>6️⃣ โครงสร้างตาราง permissions:</h2>";
$result = $conn->query("DESCRIBE permissions");
echo "<table>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
}
echo "</table>";

echo "<hr>";
echo "<h2>🔧 แนวทางแก้ไข:</h2>";
echo "<ol>";
echo "<li><a href='fix-editor-now.php'>รันสคริปต์แก้ไขสิทธิ์</a></li>";
echo "<li><a href='test-login-editor.php'>จำลองล็อกอิน editor</a></li>";
echo "<li><a href='admin/homepage/'>ทดสอบเข้าหน้า Homepage</a></li>";
echo "</ol>";

$conn->close();
?>

