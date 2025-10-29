<?php
/**
 * ทดสอบการเข้าถึงของ editor
 */
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// จำลองการ login เป็น editor
$_SESSION['user_id'] = 5; // ID ของ editor จริง
$_SESSION['username'] = '0900000020';
$_SESSION['user_role'] = 'editor';

echo "<h1>🧪 ทดสอบสิทธิ์การเข้าถึง Editor</h1>";
echo "<p><strong>User:</strong> " . $_SESSION['username'] . "</p>";
echo "<p><strong>Role:</strong> " . $_SESSION['user_role'] . "</p>";
echo "<hr>";

$modules = [
    'homepage' => 'จัดการหน้าแรก',
    'models' => 'จัดการโมเดล',
    'gallery' => 'จัดการแกลเลอรี่',
    'articles' => 'จัดการบทความ',
    'reviews' => 'จัดการรีวิว',
    'categories' => 'จัดการหมวดหมู่',
    'menus' => 'จัดการเมนู',
    'settings' => 'ตั้งค่า',
    'users' => 'จัดการผู้ใช้',
    'roles' => 'จัดการบทบาท'
];

echo "<h2>📊 สิทธิ์การเข้าถึง:</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Module</th><th>View</th><th>Create</th><th>Edit</th><th>Delete</th><th>Export</th></tr>";

foreach ($modules as $module => $name) {
    $view = has_permission($module, 'view') ? '✅' : '❌';
    $create = has_permission($module, 'create') ? '✅' : '❌';
    $edit = has_permission($module, 'edit') ? '✅' : '❌';
    $delete = has_permission($module, 'delete') ? '✅' : '❌';
    $export = has_permission($module, 'export') ? '✅' : '❌';
    
    echo "<tr>";
    echo "<td><strong>$name</strong> ($module)</td>";
    echo "<td>$view</td>";
    echo "<td>$create</td>";
    echo "<td>$edit</td>";
    echo "<td>$delete</td>";
    echo "<td>$export</td>";
    echo "</tr>";
}

echo "</table>";

echo "<hr>";
echo "<h2>🔗 ลิงก์ทดสอบ:</h2>";
echo "<ul>";
echo "<li><a href='admin/homepage/' target='_blank'>จัดการหน้าแรก</a></li>";
echo "<li><a href='admin/models/' target='_blank'>จัดการโมเดล</a></li>";
echo "<li><a href='admin/gallery/' target='_blank'>จัดการแกลเลอรี่</a></li>";
echo "<li><a href='admin/articles/' target='_blank'>จัดการบทความ</a></li>";
echo "<li><a href='admin/reviews/' target='_blank'>จัดการรีวิว</a></li>";
echo "</ul>";

echo "<p><strong>⚠️ หมายเหตุ:</strong> หลังจากทดสอบเสร็จแล้ว ให้ล็อกเอาท์และล็อกอินใหม่เพื่อใช้งานจริง</p>";
?>

