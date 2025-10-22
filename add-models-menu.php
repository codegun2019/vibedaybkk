<?php
/**
 * เพิ่มเมนู "โมเดล" ในฐานข้อมูล
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

// ตรวจสอบว่ามีเมนูโมเดลแล้วหรือยัง
$check = $conn->query("SELECT id FROM menus WHERE url = 'models.php' OR title = 'โมเดล'");

if ($check->num_rows > 0) {
    echo "✅ เมนู 'โมเดล' มีอยู่แล้ว!";
} else {
    // หา sort_order สูงสุด
    $max = $conn->query("SELECT MAX(sort_order) as max_order FROM menus WHERE parent_id IS NULL");
    $max_order = $max->fetch_assoc()['max_order'] ?? 0;
    $new_order = $max_order + 1;
    
    // เพิ่มเมนูโมเดล
    $stmt = $conn->prepare("
        INSERT INTO menus (
            parent_id, title, url, icon, sort_order, status, created_at, updated_at
        ) VALUES (
            NULL, 'โมเดล', 'models.php', 'fa-users', ?, 'active', NOW(), NOW()
        )
    ");
    $stmt->bind_param('i', $new_order);
    
    if ($stmt->execute()) {
        $menu_id = $conn->insert_id;
        echo "✅ เพิ่มเมนู 'โมเดล' สำเร็จ! (ID: {$menu_id}, ลำดับ: {$new_order})\n\n";
        
        // แสดงเมนูทั้งหมด
        echo "📋 เมนูทั้งหมดตอนนี้:\n";
        $menus = $conn->query("SELECT id, title, url, icon, sort_order FROM menus WHERE parent_id IS NULL ORDER BY sort_order");
        while ($menu = $menus->fetch_assoc()) {
            echo "  - [{$menu['sort_order']}] {$menu['title']} → {$menu['url']} ({$menu['icon']})\n";
        }
    } else {
        echo "❌ เกิดข้อผิดพลาด: " . $stmt->error;
    }
    
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มเมนูโมเดล</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 { color: #667eea; margin-bottom: 20px; }
        pre {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
            overflow-x: auto;
            line-height: 1.8;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            margin: 10px 5px;
            font-weight: bold;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✅ เพิ่มเมนูโมเดล</h1>
        <pre><?php
        // Output is already echoed above
        ?></pre>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="/" class="btn">หน้าแรก</a>
            <a href="models.php" class="btn">ดูหน้าโมเดล</a>
            <a href="setup-complete-guide.php" class="btn">คู่มือ</a>
        </div>
    </div>
</body>
</html>


