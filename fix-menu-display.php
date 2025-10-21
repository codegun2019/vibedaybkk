<?php
/**
 * Fix Menu Display - แก้ไขปัญหาการแสดงเมนู
 */

require_once 'includes/config.php';

echo "<h1>🔧 แก้ไขปัญหาการแสดงเมนู</h1>";

// 1. ตรวจสอบตาราง menus
echo "<h2>📋 ตรวจสอบตาราง menus:</h2>";

$table_check = $conn->query("SHOW TABLES LIKE 'menus'");
if ($table_check && $table_check->num_rows > 0) {
    echo "<p>✅ ตาราง menus มีอยู่</p>";
    
    // ตรวจสอบโครงสร้างตาราง
    $structure = $conn->query("DESCRIBE menus");
    if ($structure) {
        echo "<p><strong>โครงสร้างตาราง:</strong></p>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $structure->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p style='color: red;'>❌ ตาราง menus ไม่มีอยู่</p>";
    echo "<p>สร้างตาราง menus...</p>";
    
    $create_table = "CREATE TABLE IF NOT EXISTS menus (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        url VARCHAR(255) NOT NULL,
        status ENUM('active', 'inactive') DEFAULT 'active',
        parent_id INT NULL,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($create_table)) {
        echo "<p>✅ สร้างตาราง menus สำเร็จ</p>";
    } else {
        echo "<p style='color: red;'>❌ สร้างตาราง menus ล้มเหลว: " . $conn->error . "</p>";
        exit;
    }
}

// 2. เพิ่มเมนูเริ่มต้น
echo "<h2>🚀 เพิ่มเมนูเริ่มต้น:</h2>";

$default_menus = [
    ['title' => 'หน้าแรก', 'url' => '/', 'status' => 'active', 'sort_order' => 1],
    ['title' => 'โมเดล', 'url' => '/models.php', 'status' => 'active', 'sort_order' => 2],
    ['title' => 'บทความ', 'url' => '/articles.php', 'status' => 'active', 'sort_order' => 3],
    ['title' => 'เกี่ยวกับเรา', 'url' => '/about.php', 'status' => 'active', 'sort_order' => 4],
    ['title' => 'บริการ', 'url' => '/services.php', 'status' => 'active', 'sort_order' => 5],
    ['title' => 'ติดต่อ', 'url' => '/contact.php', 'status' => 'active', 'sort_order' => 6]
];

$success_count = 0;
$error_count = 0;

foreach ($default_menus as $menu) {
    // ตรวจสอบว่าเมนูมีอยู่แล้วหรือไม่
    $check_sql = "SELECT id FROM menus WHERE title = ? AND url = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $menu['title'], $menu['url']);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<p>⚠️ เมนู <strong>" . $menu['title'] . "</strong> มีอยู่แล้ว</p>";
        $check_stmt->close();
        continue;
    }
    $check_stmt->close();
    
    // เพิ่มเมนูใหม่
    $insert_sql = "INSERT INTO menus (title, url, status, sort_order, created_at) VALUES (?, ?, ?, ?, NOW())";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sssi", $menu['title'], $menu['url'], $menu['status'], $menu['sort_order']);
    
    if ($insert_stmt->execute()) {
        echo "<p>✅ เพิ่มเมนู <strong>" . $menu['title'] . "</strong> สำเร็จ</p>";
        $success_count++;
    } else {
        echo "<p>❌ เพิ่มเมนู <strong>" . $menu['title'] . "</strong> ล้มเหลว: " . $insert_stmt->error . "</p>";
        $error_count++;
    }
    $insert_stmt->close();
}

// 3. ทดสอบการดึงเมนู
echo "<h2>🧪 ทดสอบการดึงเมนู:</h2>";

// ทดสอบแบบปกติ
$test_query = "SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC";
$test_result = $conn->query($test_query);

if ($test_result && $test_result->num_rows > 0) {
    echo "<p>✅ การดึงเมนูแบบปกติสำเร็จ - พบ " . $test_result->num_rows . " รายการ</p>";
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Name</th><th>URL</th><th>Status</th><th>Sort Order</th></tr>";
    
    while ($menu = $test_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . ($menu['id'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($menu['title'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($menu['url'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($menu['status'] ?? 'N/A') . "</td>";
        echo "<td>" . ($menu['sort_order'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ การดึงเมนูแบบปกติล้มเหลว</p>";
}

// ทดสอบ db_get_rows function
if (function_exists('db_get_rows')) {
    echo "<p>✅ Function db_get_rows มีอยู่</p>";
    
    require_once 'includes/functions.php';
    $test_menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC");
    
    echo "<p><strong>ผลลัพธ์ db_get_rows:</strong> " . count($test_menus) . " รายการ</p>";
    
    if (!empty($test_menus)) {
        echo "<ul>";
        foreach ($test_menus as $menu) {
            echo "<li>" . htmlspecialchars($menu['title'] ?? 'N/A') . " (" . htmlspecialchars($menu['url'] ?? 'N/A') . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>⚠️ db_get_rows ส่งคืน array ว่าง</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Function db_get_rows ไม่มีอยู่</p>";
}

// 4. ทดสอบการแสดงเมนู
echo "<h2>🎨 ทดสอบการแสดงเมนู:</h2>";

$display_menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC");

if (!empty($display_menus)) {
    echo "<div style='background: #1F2937; color: white; padding: 20px; border-radius: 10px;'>";
    echo "<h3 style='color: #DC2626; margin-bottom: 15px;'>Desktop Menu Preview:</h3>";
    echo "<nav style='display: flex; gap: 20px;'>";
    
    foreach ($display_menus as $menu) {
        echo "<a href='" . htmlspecialchars($menu['url'] ?? '#') . "' style='color: #D1D5DB; text-decoration: none; font-weight: 500;'>";
        echo htmlspecialchars($menu['title'] ?? 'Unnamed');
        echo "</a>";
    }
    
    echo "</nav>";
    echo "</div>";
    
    echo "<div style='background: #374151; color: white; padding: 20px; border-radius: 10px; margin-top: 20px;'>";
    echo "<h3 style='color: #DC2626; margin-bottom: 15px;'>Mobile Menu Preview:</h3>";
    
    foreach ($display_menus as $menu) {
        echo "<a href='" . htmlspecialchars($menu['url'] ?? '#') . "' style='display: block; padding: 12px 0; color: #D1D5DB; text-decoration: none; border-bottom: 1px solid #4B5563;'>";
        echo htmlspecialchars($menu['title'] ?? 'Unnamed');
        echo "</a>";
    }
    
    echo "</div>";
} else {
    echo "<p style='color: red;'>❌ ไม่มีเมนูสำหรับแสดง</p>";
}

echo "<h2>✅ สรุป:</h2>";
echo "<p>✅ สำเร็จ: " . $success_count . " รายการ</p>";
echo "<p>❌ ล้มเหลว: " . $error_count . " รายการ</p>";

if ($success_count > 0 || !empty($display_menus)) {
    echo "<p style='color: green; font-weight: bold;'>🎉 เมนูพร้อมใช้งานแล้ว! ลองรีเฟรชหน้า Models และหน้าแรก</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>⚠️ ยังมีปัญหาในการสร้างเมนู</p>";
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Fix Menu Display</title>
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
