<?php
/**
 * Setup Default Menus - เพิ่มเมนูเริ่มต้นในฐานข้อมูล
 */

require_once 'includes/config.php';

echo "<h1>🔧 ตั้งค่าเมนูเริ่มต้น</h1>";

// เมนูเริ่มต้น
$default_menus = [
    ['name' => 'หน้าแรก', 'url' => '/', 'status' => 'active', 'sort_order' => 1],
    ['name' => 'โมเดล', 'url' => '/models.php', 'status' => 'active', 'sort_order' => 2],
    ['name' => 'บทความ', 'url' => '/articles.php', 'status' => 'active', 'sort_order' => 3],
    ['name' => 'เกี่ยวกับเรา', 'url' => '/about.php', 'status' => 'active', 'sort_order' => 4],
    ['name' => 'บริการ', 'url' => '/services.php', 'status' => 'active', 'sort_order' => 5],
    ['name' => 'ติดต่อ', 'url' => '/contact.php', 'status' => 'active', 'sort_order' => 6]
];

echo "<h2>📋 เมนูที่จะเพิ่ม:</h2>";
echo "<ul>";
foreach ($default_menus as $menu) {
    echo "<li><strong>" . $menu['name'] . "</strong> - " . $menu['url'] . " (ลำดับ: " . $menu['sort_order'] . ")</li>";
}
echo "</ul>";

echo "<h2>🚀 เริ่มเพิ่มเมนู:</h2>";

$success_count = 0;
$error_count = 0;

foreach ($default_menus as $menu) {
    // ตรวจสอบว่าเมนูมีอยู่แล้วหรือไม่
    $check_sql = "SELECT id FROM menus WHERE name = ? AND url = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $menu['name'], $menu['url']);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<p>⚠️ เมนู <strong>" . $menu['name'] . "</strong> มีอยู่แล้ว</p>";
        $check_stmt->close();
        continue;
    }
    $check_stmt->close();
    
    // เพิ่มเมนูใหม่
    $insert_sql = "INSERT INTO menus (name, url, status, sort_order, created_at) VALUES (?, ?, ?, ?, NOW())";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sssi", $menu['name'], $menu['url'], $menu['status'], $menu['sort_order']);
    
    if ($insert_stmt->execute()) {
        echo "<p>✅ เพิ่มเมนู <strong>" . $menu['name'] . "</strong> สำเร็จ</p>";
        $success_count++;
    } else {
        echo "<p>❌ เพิ่มเมนู <strong>" . $menu['name'] . "</strong> ล้มเหลว: " . $insert_stmt->error . "</p>";
        $error_count++;
    }
    $insert_stmt->close();
}

echo "<h2>📊 สรุปผลลัพธ์:</h2>";
echo "<p>✅ สำเร็จ: " . $success_count . " รายการ</p>";
echo "<p>❌ ล้มเหลว: " . $error_count . " รายการ</p>";

// ตรวจสอบเมนูทั้งหมดหลังเพิ่ม
echo "<h2>🔍 เมนูทั้งหมดในระบบ:</h2>";

$all_menus_query = "SELECT * FROM menus WHERE parent_id IS NULL ORDER BY sort_order ASC";
$all_menus_result = $conn->query($all_menus_query);

if ($all_menus_result && $all_menus_result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
    echo "<tr><th>ID</th><th>Name</th><th>URL</th><th>Status</th><th>Sort Order</th><th>Created At</th></tr>";
    
    while ($menu = $all_menus_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . ($menu['id'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($menu['name'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($menu['url'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($menu['status'] ?? 'N/A') . "</td>";
        echo "<td>" . ($menu['sort_order'] ?? 'N/A') . "</td>";
        echo "<td>" . ($menu['created_at'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ ไม่พบเมนูในระบบ</p>";
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Setup Default Menus</title>
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
        <a href="debug-menu-display.php" class="btn">ตรวจสอบเมนู</a>
        <a href="javascript:location.reload()" class="btn">รีเฟรช</a>
    </div>
</body>
</html>

<?php $conn->close(); ?>