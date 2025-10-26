<?php
/**
 * ตรวจสอบโครงสร้างตาราง articles
 */

require_once 'includes/config.php';

echo "<h1>🔍 ตรวจสอบตาราง articles</h1>";

// ตรวจสอบว่าตารางมีอยู่หรือไม่
$table_check = $conn->query("SHOW TABLES LIKE 'articles'");

if ($table_check->num_rows > 0) {
    echo "<p>✅ ตาราง articles มีอยู่</p>";
    
    // ดูโครงสร้างตาราง
    echo "<h2>โครงสร้างตาราง:</h2>";
    $columns = $conn->query("DESCRIBE articles");
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    $existing_columns = [];
    while ($col = $columns->fetch_assoc()) {
        $existing_columns[] = $col['Field'];
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . ($col['Extra'] ?? '') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // ตรวจสอบคอลัมน์ที่ต้องการ
    echo "<h2>ตรวจสอบคอลัมน์ที่จำเป็น:</h2>";
    $required_columns = [
        'id' => 'Primary Key',
        'title' => 'หัวข้อบทความ',
        'slug' => 'URL Slug',
        'excerpt' => 'เนื้อหาย่อ',
        'content' => 'เนื้อหาเต็ม',
        'featured_image' => 'รูปภาพหลัก',
        'category_id' => 'หมวดหมู่',
        'author_id' => 'ผู้เขียน',
        'tags' => 'แท็ก',
        'read_time' => 'เวลาอ่าน',
        'view_count' => 'จำนวนการดู',
        'status' => 'สถานะ',
        'published_at' => 'วันที่เผยแพร่',
        'created_at' => 'วันที่สร้าง',
        'updated_at' => 'วันที่อัพเดต'
    ];
    
    foreach ($required_columns as $col => $desc) {
        if (in_array($col, $existing_columns)) {
            echo "<p>✅ <strong>$col</strong> ($desc) มีอยู่</p>";
        } else {
            echo "<p>❌ <strong>$col</strong> ($desc) ไม่มี</p>";
        }
    }
    
    // นับจำนวนบทความ
    echo "<h2>ข้อมูลบทความ:</h2>";
    $count_result = $conn->query("SELECT COUNT(*) as total FROM articles");
    $count = $count_result->fetch_assoc()['total'];
    echo "<p>📊 จำนวนบทความทั้งหมด: <strong>$count</strong> รายการ</p>";
    
    if ($count > 0) {
        echo "<h3>บทความล่าสุด 5 รายการ:</h3>";
        $articles = $conn->query("SELECT id, title, status, created_at FROM articles ORDER BY id DESC LIMIT 5");
        
        echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Status</th><th>Created At</th></tr>";
        
        while ($article = $articles->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$article['id']}</td>";
            echo "<td>" . htmlspecialchars($article['title']) . "</td>";
            echo "<td>{$article['status']}</td>";
            echo "<td>{$article['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} else {
    echo "<p>❌ ตาราง articles ไม่มีอยู่</p>";
    echo "<p>⚠️ ต้องสร้างตารางก่อนใช้งาน</p>";
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ตรวจสอบตาราง articles</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        h1 { color: #667eea; }
        h2 { color: #DC2626; border-bottom: 2px solid #DC2626; padding-bottom: 10px; margin-top: 30px; }
        h3 { color: #3B82F6; }
        table { background: white; margin: 20px 0; width: 100%; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background: #667eea; color: white; }
        .btn { display: inline-block; background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
        .btn:hover { background: #5558d9; }
    </style>
</head>
<body>
    <div style="text-align: center; margin: 30px 0;">
        <a href="admin/articles/add.php" class="btn">เพิ่มบทความ</a>
        <a href="admin/articles/" class="btn">จัดการบทความ</a>
        <a href="/" class="btn">กลับหน้าแรก</a>
    </div>
</body>
</html>




