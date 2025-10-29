<?php
/**
 * ตรวจสอบข้อมูลจริงในฐานข้อมูล
 */
require_once 'includes/config.php';

echo "<h1>🔍 ตรวจสอบข้อมูลจริงในฐานข้อมูล</h1>";
echo "<style>
body { font-family: Arial; padding: 20px; background: #f5f5f5; }
.box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
table { border-collapse: collapse; width: 100%; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
th { background: #f3f4f6; font-weight: bold; }
.success { color: #22c55e; font-weight: bold; }
.error { color: #ef4444; font-weight: bold; }
.warning { color: #f59e0b; font-weight: bold; }
pre { background: #f9fafb; padding: 15px; border-radius: 4px; overflow: auto; border: 1px solid #e5e7eb; }
</style>";

// 1. ตรวจสอบข้อมูลจริงในฐานข้อมูล
echo "<div class='box'>";
echo "<h2>1️⃣ ข้อมูลจริงในตาราง permissions (editor)</h2>";
$result = $conn->query("SELECT * FROM permissions WHERE role_key = 'editor' ORDER BY feature");
echo "<table>";
echo "<tr><th>ID</th><th>role_key</th><th>feature</th><th>can_view</th><th>can_create</th><th>can_edit</th><th>can_delete</th><th>can_export</th></tr>";

$found_homepage = false;
$homepage_view = 0;

while ($row = $result->fetch_assoc()) {
    if ($row['feature'] === 'homepage') {
        $found_homepage = true;
        $homepage_view = $row['can_view'];
    }
    
    $highlight = ($row['feature'] === 'homepage') ? 'style="background: #fef3c7;"' : '';
    
    echo "<tr $highlight>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['role_key']}</td>";
    echo "<td><strong>{$row['feature']}</strong></td>";
    echo "<td>" . ($row['can_view'] ? '<span class="success">✅ 1</span>' : '<span class="error">❌ 0</span>') . "</td>";
    echo "<td>" . ($row['can_create'] ? '<span class="success">✅ 1</span>' : '<span class="error">❌ 0</span>') . "</td>";
    echo "<td>" . ($row['can_edit'] ? '<span class="success">✅ 1</span>' : '<span class="error">❌ 0</span>') . "</td>";
    echo "<td>" . ($row['can_delete'] ? '<span class="success">✅ 1</span>' : '<span class="error">❌ 0</span>') . "</td>";
    echo "<td>" . (isset($row['can_export']) && $row['can_export'] ? '<span class="success">✅ 1</span>' : '<span class="error">❌ 0</span>') . "</td>";
    echo "</tr>";
}
echo "</table>";

if (!$found_homepage) {
    echo "<p class='error'>❌ ไม่พบ 'homepage' ในฐานข้อมูล!</p>";
} elseif ($homepage_view == 0) {
    echo "<p class='error'>❌ can_view สำหรับ homepage เป็น 0!</p>";
} else {
    echo "<p class='success'>✅ พบ homepage และ can_view = 1</p>";
}
echo "</div>";

// 2. ตรวจสอบว่า admin/homepage/index.php เช็คชื่ออะไร
echo "<div class='box'>";
echo "<h2>2️⃣ ตรวจสอบชื่อ feature ที่ใช้ในโค้ด</h2>";
$homepage_index = file_get_contents('admin/homepage/index.php');
if (preg_match('/require_permission\([\'"]([^\'"]+)[\'"]/', $homepage_index, $matches)) {
    $feature_name = $matches[1];
    echo "<p>ไฟล์ <code>admin/homepage/index.php</code> ใช้:</p>";
    echo "<pre>require_permission('<strong style='color: #ef4444;'>{$feature_name}</strong>', 'view')</pre>";
    
    // เช็คว่ามีในฐานข้อมูลหรือไม่
    $check = $conn->query("SELECT * FROM permissions WHERE role_key = 'editor' AND feature = '$feature_name'");
    if ($check && $check->num_rows > 0) {
        $perm = $check->fetch_assoc();
        echo "<p class='success'>✅ พบ feature '<strong>$feature_name</strong>' ในฐานข้อมูล</p>";
        echo "<table>";
        echo "<tr><th>can_view</th><th>can_create</th><th>can_edit</th><th>can_delete</th></tr>";
        echo "<tr>";
        echo "<td>" . ($perm['can_view'] ? '<span class="success">✅ 1</span>' : '<span class="error">❌ 0</span>') . "</td>";
        echo "<td>" . ($perm['can_create'] ? '<span class="success">✅ 1</span>' : '<span class="error">❌ 0</span>') . "</td>";
        echo "<td>" . ($perm['can_edit'] ? '<span class="success">✅ 1</span>' : '<span class="error">❌ 0</span>') . "</td>";
        echo "<td>" . ($perm['can_delete'] ? '<span class="success">✅ 1</span>' : '<span class="error">❌ 0</span>') . "</td>";
        echo "</tr>";
        echo "</table>";
        
        if ($perm['can_view'] == 0) {
            echo "<p class='error'>❌ ปัญหา: can_view เป็น 0 ต้องแก้เป็น 1</p>";
            echo "<p>กำลังแก้ไข...</p>";
            $conn->query("UPDATE permissions SET can_view = 1 WHERE role_key = 'editor' AND feature = '$feature_name'");
            echo "<p class='success'>✅ แก้ไขเรียบร้อย</p>";
        }
    } else {
        echo "<p class='error'>❌ ไม่พบ feature '<strong>$feature_name</strong>' ในฐานข้อมูล!</p>";
        echo "<p>กำลังสร้าง...</p>";
        $conn->query("INSERT INTO permissions (role_key, feature, can_view, can_create, can_edit, can_delete) VALUES ('editor', '$feature_name', 1, 1, 1, 0)");
        echo "<p class='success'>✅ สร้างเรียบร้อย</p>";
    }
}
echo "</div>";

// 3. บังคับอัปเดตสิทธิ์
echo "<div class='box'>";
echo "<h2>3️⃣ บังคับอัปเดตสิทธิ์ homepage</h2>";
$update_sql = "UPDATE permissions SET can_view = 1, can_edit = 1 WHERE role_key = 'editor' AND feature = 'homepage'";
echo "<pre>$update_sql</pre>";
$result = $conn->query($update_sql);
if ($result) {
    echo "<p class='success'>✅ อัปเดตสำเร็จ (Affected rows: " . $conn->affected_rows . ")</p>";
} else {
    echo "<p class='error'>❌ อัปเดตล้มเหลว: " . $conn->error . "</p>";
}

// ตรวจสอบผลลัพธ์
$verify = $conn->query("SELECT * FROM permissions WHERE role_key = 'editor' AND feature = 'homepage'");
if ($verify && $verify->num_rows > 0) {
    $v = $verify->fetch_assoc();
    echo "<p>ผลลัพธ์หลังอัปเดต:</p>";
    echo "<ul>";
    echo "<li>can_view: " . ($v['can_view'] ? '<span class="success">✅ 1</span>' : '<span class="error">❌ 0</span>') . "</li>";
    echo "<li>can_edit: " . ($v['can_edit'] ? '<span class="success">✅ 1</span>' : '<span class="error">❌ 0</span>') . "</li>";
    echo "</ul>";
}
echo "</div>";

// 4. ทดสอบทันที
echo "<div class='box'>";
echo "<h2>4️⃣ ทดสอบทันที</h2>";
echo "<p><a href='quick-login-editor.php' style='display: inline-block; padding: 12px 24px; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;'>🔐 ล็อกอินเป็น Editor และเข้าหน้า Homepage</a></p>";
echo "<p><a href='final-debug-and-fix.php' style='display: inline-block; padding: 12px 24px; background: #22c55e; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;'>🔍 ดู Debug Information</a></p>";
echo "</div>";

$conn->close();
?>

