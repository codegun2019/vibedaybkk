<?php
/**
 * แก้ไขสิทธิ์ Edit สำหรับ Homepage
 */
require_once 'includes/config.php';

echo "<h1>🔧 แก้ไขสิทธิ์ Edit สำหรับ Homepage</h1>";
echo "<style>
body { font-family: Arial; padding: 20px; background: #f5f5f5; }
.box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
table { border-collapse: collapse; width: 100%; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
th { background: #f3f4f6; font-weight: bold; }
.success { color: #22c55e; font-weight: bold; }
.error { color: #ef4444; font-weight: bold; }
pre { background: #f9fafb; padding: 15px; border-radius: 4px; overflow: auto; }
.btn { display: inline-block; padding: 12px 24px; margin: 5px; text-decoration: none; border-radius: 6px; font-weight: bold; color: white; }
.btn-primary { background: #3b82f6; }
.btn-success { background: #22c55e; }
</style>";

// 1. ตรวจสอบสิทธิ์ปัจจุบัน
echo "<div class='box'>";
echo "<h2>1️⃣ สิทธิ์ปัจจุบันสำหรับ homepage</h2>";
$current = $conn->query("SELECT * FROM permissions WHERE role_key = 'editor' AND feature = 'homepage'");
if ($current && $current->num_rows > 0) {
    $perm = $current->fetch_assoc();
    echo "<table>";
    echo "<tr><th>Permission</th><th>Current Value</th><th>Status</th></tr>";
    echo "<tr><td>can_view</td><td>{$perm['can_view']}</td><td>" . ($perm['can_view'] ? '<span class="success">✅ OK</span>' : '<span class="error">❌ Need Fix</span>') . "</td></tr>";
    echo "<tr><td>can_create</td><td>{$perm['can_create']}</td><td>" . ($perm['can_create'] ? '<span class="success">✅ OK</span>' : '⚪ Optional') . "</td></tr>";
    echo "<tr><td><strong>can_edit</strong></td><td><strong>{$perm['can_edit']}</strong></td><td>" . ($perm['can_edit'] ? '<span class="success">✅ OK</span>' : '<span class="error">❌ Need Fix</span>') . "</td></tr>";
    echo "<tr><td>can_delete</td><td>{$perm['can_delete']}</td><td>" . ($perm['can_delete'] ? '<span class="success">✅ OK</span>' : '⚪ Optional') . "</td></tr>";
    echo "</table>";
    
    if ($perm['can_edit'] == 0) {
        echo "<p class='error'>❌ ปัญหา: can_edit = 0 (Editor ไม่สามารถแก้ไขได้)</p>";
    } else {
        echo "<p class='success'>✅ can_edit = 1 (ควรแก้ไขได้)</p>";
    }
} else {
    echo "<p class='error'>❌ ไม่พบ permission record!</p>";
}
echo "</div>";

// 2. อัปเดตสิทธิ์ให้ครบ
echo "<div class='box'>";
echo "<h2>2️⃣ อัปเดตสิทธิ์</h2>";

$update_sql = "UPDATE permissions 
               SET can_view = 1, can_create = 1, can_edit = 1, can_delete = 0 
               WHERE role_key = 'editor' AND feature = 'homepage'";

echo "<pre>$update_sql</pre>";
$result = $conn->query($update_sql);

if ($result) {
    if ($conn->affected_rows > 0) {
        echo "<p class='success'>✅ อัปเดตสำเร็จ! (Affected rows: {$conn->affected_rows})</p>";
    } else {
        echo "<p class='success'>✅ ข้อมูลเป็นค่าเดียวกันอยู่แล้ว</p>";
    }
} else {
    echo "<p class='error'>❌ อัปเดตล้มเหลว: " . $conn->error . "</p>";
}

// ตรวจสอบผลลัพธ์
$verify = $conn->query("SELECT * FROM permissions WHERE role_key = 'editor' AND feature = 'homepage'");
if ($verify && $verify->num_rows > 0) {
    $v = $verify->fetch_assoc();
    echo "<p><strong>ผลลัพธ์หลังอัปเดต:</strong></p>";
    echo "<table>";
    echo "<tr><th>Permission</th><th>Value</th></tr>";
    echo "<tr><td>can_view</td><td>" . ($v['can_view'] ? '<span class="success">✅ 1</span>' : '<span class="error">❌ 0</span>') . "</td></tr>";
    echo "<tr><td>can_create</td><td>" . ($v['can_create'] ? '<span class="success">✅ 1</span>' : '❌ 0') . "</td></tr>";
    echo "<tr><td><strong>can_edit</strong></td><td>" . ($v['can_edit'] ? '<span class="success">✅ 1</span>' : '<span class="error">❌ 0</span>') . "</td></tr>";
    echo "<tr><td>can_delete</td><td>" . ($v['can_delete'] ? '✅ 1' : '❌ 0') . "</td></tr>";
    echo "</table>";
}
echo "</div>";

// 3. ตรวจสอบไฟล์ที่เกี่ยวข้อง
echo "<div class='box'>";
echo "<h2>3️⃣ ตรวจสอบไฟล์ที่เกี่ยวข้อง</h2>";

$files_to_check = [
    'admin/homepage/edit.php' => 'หน้าแก้ไข section',
    'admin/homepage/toggle-status.php' => 'Toggle เปิด/ปิด',
    'admin/homepage/features.php' => 'จัดการรายการ',
];

echo "<table>";
echo "<tr><th>File</th><th>Description</th><th>Permission Required</th></tr>";
foreach ($files_to_check as $file => $desc) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (preg_match('/require_permission\([\'"]([^\'"]+)[\'"],\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
            $feature = $matches[1];
            $action = $matches[2];
            echo "<tr>";
            echo "<td><code>$file</code></td>";
            echo "<td>$desc</td>";
            echo "<td><strong>$feature</strong> → <strong>$action</strong></td>";
            echo "</tr>";
        } else {
            echo "<tr><td><code>$file</code></td><td>$desc</td><td>ไม่พบ require_permission</td></tr>";
        }
    }
}
echo "</table>";
echo "</div>";

// 4. อัปเดตสิทธิ์ทุก feature ที่ editor ควรมี
echo "<div class='box'>";
echo "<h2>4️⃣ อัปเดตสิทธิ์ทุก feature ที่เกี่ยวข้อง</h2>";

$features_to_update = [
    'homepage' => ['view' => 1, 'create' => 1, 'edit' => 1, 'delete' => 0],
    'models' => ['view' => 1, 'create' => 1, 'edit' => 1, 'delete' => 0],
    'gallery' => ['view' => 1, 'create' => 1, 'edit' => 1, 'delete' => 0],
    'articles' => ['view' => 1, 'create' => 1, 'edit' => 1, 'delete' => 0],
    'reviews' => ['view' => 1, 'create' => 1, 'edit' => 1, 'delete' => 0],
];

echo "<table>";
echo "<tr><th>Feature</th><th>View</th><th>Create</th><th>Edit</th><th>Delete</th><th>Status</th></tr>";

foreach ($features_to_update as $feature => $perms) {
    $check = $conn->query("SELECT id FROM permissions WHERE role_key = 'editor' AND feature = '$feature'");
    
    if ($check && $check->num_rows > 0) {
        // Update
        $sql = "UPDATE permissions SET 
                can_view = {$perms['view']}, 
                can_create = {$perms['create']}, 
                can_edit = {$perms['edit']}, 
                can_delete = {$perms['delete']}
                WHERE role_key = 'editor' AND feature = '$feature'";
        $result = $conn->query($sql);
        $status = $result ? '<span class="success">✅ Updated</span>' : '<span class="error">❌ Failed</span>';
    } else {
        // Insert
        $sql = "INSERT INTO permissions (role_key, feature, can_view, can_create, can_edit, can_delete) 
                VALUES ('editor', '$feature', {$perms['view']}, {$perms['create']}, {$perms['edit']}, {$perms['delete']})";
        $result = $conn->query($sql);
        $status = $result ? '<span class="success">✅ Created</span>' : '<span class="error">❌ Failed</span>';
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
echo "</div>";

// 5. สรุปและทดสอบ
echo "<div class='box'>";
echo "<h2>5️⃣ ทดสอบเข้าหน้าแก้ไข</h2>";
echo "<p class='success'>✅ อัปเดตสิทธิ์เรียบร้อยแล้ว!</p>";
echo "<p><strong>ขั้นตอนต่อไป:</strong></p>";
echo "<ol>";
echo "<li>ล็อกเอาท์และล็อกอินใหม่ (หรือคลิกปุ่มด้านล่าง)</li>";
echo "<li>เข้าหน้า Homepage Management</li>";
echo "<li>กดปุ่ม 'แก้ไข' ที่ section ใดก็ได้</li>";
echo "<li>ควรเข้าได้แล้ว! 🎉</li>";
echo "</ol>";

echo "<p>";
echo "<a href='quick-login-editor.php' class='btn btn-primary'>🔐 ล็อกอินเป็น Editor</a>";
echo "<a href='admin/homepage/' class='btn btn-success' target='_blank'>📝 เข้าหน้า Homepage</a>";
echo "</p>";
echo "</div>";

$conn->close();
?>

