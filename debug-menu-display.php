<?php
/**
 * Debug Menu Display - ตรวจสอบการแสดงเมนู
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<h1>🔍 ตรวจสอบการแสดงเมนู</h1>";

// ดึงข้อมูลเมนูแบบเดียวกับหน้าแรก
$main_menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC");

echo "<h2>📋 ข้อมูลเมนูในฐานข้อมูล:</h2>";

$menu_query = "SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC";
$menu_result = $conn->query($menu_query);

if ($menu_result && $menu_result->num_rows > 0) {
    echo "<p>✅ พบเมนู " . $menu_result->num_rows . " รายการ</p>";
    
    echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
    echo "<tr><th>ID</th><th>Name</th><th>URL</th><th>Status</th><th>Sort Order</th></tr>";
    
    while ($menu = $menu_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . ($menu['id'] ?? 'N/A') . "</td>";
        echo "<td>" . h($menu['title'] ?? 'N/A') . "</td>";
        echo "<td>" . h($menu['url'] ?? 'N/A') . "</td>";
        echo "<td>" . h($menu['status'] ?? 'N/A') . "</td>";
        echo "<td>" . ($menu['sort_order'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // ทดสอบการแสดงเมนู
    echo "<h2>🎨 ทดสอบการแสดงเมนู:</h2>";
    
    echo "<div style='background: #1F2937; color: white; padding: 20px; border-radius: 10px;'>";
    echo "<h3 style='color: #DC2626; margin-bottom: 15px;'>Desktop Menu:</h3>";
    echo "<nav style='display: flex; gap: 20px;'>";
    
    foreach ($main_menus as $menu) {
        echo "<a href='" . h($menu['url'] ?? '#') . "' style='color: #D1D5DB; text-decoration: none; font-weight: 500;'>";
        echo h($menu['title'] ?? 'Unnamed');
        echo "</a>";
    }
    
    echo "</nav>";
    echo "</div>";
    
    // ทดสอบ Mobile Menu
    echo "<div style='background: #374151; color: white; padding: 20px; border-radius: 10px; margin-top: 20px;'>";
    echo "<h3 style='color: #DC2626; margin-bottom: 15px;'>Mobile Menu:</h3>";
    
    foreach ($main_menus as $menu) {
        echo "<a href='" . h($menu['url'] ?? '#') . "' style='display: block; padding: 12px 0; color: #D1D5DB; text-decoration: none; border-bottom: 1px solid #4B5563;'>";
        echo h($menu['title'] ?? 'Unnamed');
        echo "</a>";
    }
    
    echo "</div>";
    
} else {
    echo "<p style='color: red;'>❌ ไม่พบเมนูในฐานข้อมูล</p>";
    
    // ตรวจสอบตารางเมนูทั้งหมด
    echo "<h3>ตรวจสอบตาราง menus ทั้งหมด:</h3>";
    $all_menus = $conn->query("SELECT * FROM menus ORDER BY id ASC");
    
    if ($all_menus && $all_menus->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
        echo "<tr><th>ID</th><th>Name</th><th>URL</th><th>Status</th><th>Parent ID</th><th>Sort Order</th></tr>";
        
        while ($menu = $all_menus->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . ($menu['id'] ?? 'N/A') . "</td>";
            echo "<td>" . h($menu['title'] ?? 'N/A') . "</td>";
            echo "<td>" . h($menu['url'] ?? 'N/A') . "</td>";
            echo "<td>" . h($menu['status'] ?? 'N/A') . "</td>";
            echo "<td>" . ($menu['parent_id'] ?? 'NULL') . "</td>";
            echo "<td>" . ($menu['sort_order'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ ไม่พบข้อมูลในตาราง menus เลย</p>";
    }
}

// ทดสอบ db_get_rows function
echo "<h2>🧪 ทดสอบ db_get_rows Function:</h2>";

if (function_exists('db_get_rows')) {
    echo "<p>✅ Function db_get_rows มีอยู่</p>";
    
    $test_menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC");
    echo "<p><strong>ผลลัพธ์ db_get_rows:</strong> " . count($test_menus) . " รายการ</p>";
    
    if (!empty($test_menus)) {
        echo "<ul>";
        foreach ($test_menus as $menu) {
            echo "<li>" . h($menu['title'] ?? 'N/A') . " (" . h($menu['url'] ?? 'N/A') . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>⚠️ db_get_rows ส่งคืน array ว่าง</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Function db_get_rows ไม่มีอยู่</p>";
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Debug Menu Display</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        h1 { color: #667eea; }
        h2 { color: #DC2626; border-bottom: 2px solid #DC2626; padding-bottom: 10px; }
        table { margin: 20px 0; background: white; border-radius: 10px; overflow: hidden; }
        th, td { padding: 12px; text-align: left; }
        th { background: #667eea; color: white; }
        .btn { display: inline-block; background: #667eea; color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; margin: 10px 5px; }
        .btn:hover { background: #5558d9; }
    </style>
</head>
<body>
    <div style="text-align: center; margin: 30px 0;">
        <a href="models.php" class="btn">ดูหน้า Models</a>
        <a href="/" class="btn">ดูหน้าแรก</a>
        <a href="setup-default-menus.php" class="btn">ตั้งค่าเมนูเริ่มต้น</a>
        <a href="javascript:location.reload()" class="btn">รีเฟรช</a>
    </div>
</body>
</html>

<?php $conn->close(); ?>
