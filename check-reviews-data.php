<?php
/**
 * ตรวจสอบข้อมูลรีวิวในฐานข้อมูล
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<h1>🔍 ตรวจสอบข้อมูลรีวิว</h1>";

// ตรวจสอบรีวิวทั้งหมด
$all_reviews = db_get_rows($conn, "SELECT * FROM customer_reviews ORDER BY id ASC");
$active_reviews = db_get_rows($conn, "SELECT * FROM customer_reviews WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 6");

echo "<h2>📊 สถิติรีวิว:</h2>";
echo "<p>✅ รีวิวทั้งหมด: " . count($all_reviews) . " รายการ</p>";
echo "<p>✅ รีวิวที่เปิดใช้งาน: " . count($active_reviews) . " รายการ</p>";

echo "<h2>📋 รายการรีวิวทั้งหมด:</h2>";

if (!empty($all_reviews)) {
    echo "<table border='1' style='border-collapse: collapse; margin: 20px 0; width: 100%;'>";
    echo "<tr><th>ID</th><th>ชื่อลูกค้า</th><th>รูปภาพ</th><th>Rating</th><th>เนื้อหา</th><th>สถานะ</th><th>Sort Order</th><th>วันที่สร้าง</th></tr>";
    
    foreach ($all_reviews as $review) {
        $rating_stars = str_repeat('⭐', $review['rating']);
        $status = $review['is_active'] ? '✅ เปิด' : '❌ ปิด';
        $image_info = !empty($review['image']) ? 
            '<strong>Path:</strong> ' . htmlspecialchars($review['image']) . '<br>' .
            '<strong>Full URL:</strong> ' . htmlspecialchars(UPLOADS_URL . '/' . $review['image']) . '<br>' .
            '<strong>File Exists:</strong> ' . (file_exists($_SERVER['DOCUMENT_ROOT'] . '/vibedaybkk/uploads/' . $review['image']) ? '✅ ใช่' : '❌ ไม่') :
            'ไม่มีรูปภาพ';
        
        echo "<tr>";
        echo "<td>" . $review['id'] . "</td>";
        echo "<td>" . htmlspecialchars($review['customer_name']) . "</td>";
        echo "<td>" . $image_info . "</td>";
        echo "<td>{$rating_stars} ({$review['rating']})</td>";
        echo "<td>" . htmlspecialchars(substr($review['content'], 0, 100)) . "...</td>";
        echo "<td>{$status}</td>";
        echo "<td>" . $review['sort_order'] . "</td>";
        echo "<td>" . $review['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ ไม่พบรีวิวในฐานข้อมูล</p>";
}

echo "<h2>🎯 รีวิวที่จะแสดงในหน้าแรก (6 รายการแรก):</h2>";

if (!empty($active_reviews)) {
    echo "<table border='1' style='border-collapse: collapse; margin: 20px 0; width: 100%;'>";
    echo "<tr><th>ID</th><th>ชื่อลูกค้า</th><th>รูปภาพ</th><th>Rating</th><th>เนื้อหา</th><th>Sort Order</th></tr>";
    
    foreach ($active_reviews as $review) {
        $rating_stars = str_repeat('⭐', $review['rating']);
        $image_display = !empty($review['image']) ? 
            '<img src="' . htmlspecialchars(UPLOADS_URL . '/' . $review['image']) . '" alt="Review Image" style="width: 100px; height: 60px; object-fit: cover; border-radius: 5px;">' :
            'ไม่มีรูปภาพ';
        
        echo "<tr>";
        echo "<td>" . $review['id'] . "</td>";
        echo "<td>" . htmlspecialchars($review['customer_name']) . "</td>";
        echo "<td>" . $image_display . "</td>";
        echo "<td>{$rating_stars} ({$review['rating']})</td>";
        echo "<td>" . htmlspecialchars(substr($review['content'], 0, 50)) . "...</td>";
        echo "<td>" . $review['sort_order'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ ไม่พบรีวิวที่เปิดใช้งานสำหรับแสดงในหน้าแรก</p>";
}

// ตรวจสอบโฟลเดอร์ uploads
echo "<h2>📁 ตรวจสอบโฟลเดอร์ uploads:</h2>";
$uploads_dir = $_SERVER['DOCUMENT_ROOT'] . '/vibedaybkk/uploads/';
echo "<p><strong>Uploads Directory:</strong> " . htmlspecialchars($uploads_dir) . "</p>";
echo "<p><strong>Directory exists:</strong> " . (is_dir($uploads_dir) ? '✅ ใช่' : '❌ ไม่') . "</p>";

if (is_dir($uploads_dir)) {
    $files = scandir($uploads_dir);
    $image_files = array_filter($files, function($file) {
        return !in_array($file, ['.', '..']) && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    });
    
    echo "<p><strong>ไฟล์รูปภาพใน uploads:</strong> " . count($image_files) . " ไฟล์</p>";
    if (!empty($image_files)) {
        echo "<ul>";
        foreach ($image_files as $file) {
            $file_path = $uploads_dir . $file;
            $file_size = filesize($file_path);
            echo "<li>" . htmlspecialchars($file) . " (" . number_format($file_size) . " bytes)</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ ไม่พบไฟล์รูปภาพ</p>";
    }
} else {
    echo "<p>❌ โฟลเดอร์ uploads ไม่มีอยู่</p>";
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ตรวจสอบข้อมูลรีวิว</title>
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
        <a href="/" class="btn">ดูหน้าแรก</a>
        <a href="seed-reviews.php" class="btn">เพิ่มรีวิวตัวอย่าง</a>
        <a href="test-reviews-display.php" class="btn">ทดสอบการแสดงรีวิว</a>
        <a href="javascript:location.reload()" class="btn">รีเฟรช</a>
    </div>
</body>
</html>

<?php $conn->close(); ?>

