<?php
/**
 * ทดสอบการแสดงรีวิว
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<h1>🔍 ทดสอบการแสดงรีวิว</h1>";

// ดึงข้อมูลรีวิว
$reviews = db_get_rows($conn, "SELECT * FROM customer_reviews WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 6");

echo "<h2>📋 ข้อมูลรีวิวในฐานข้อมูล:</h2>";

if (!empty($reviews)) {
    echo "<p>✅ พบรีวิว " . count($reviews) . " รายการ</p>";
    
    echo "<table border='1' style='border-collapse: collapse; margin: 20px 0; width: 100%;'>";
    echo "<tr><th>ID</th><th>ชื่อลูกค้า</th><th>รูปภาพ</th><th>Rating</th><th>เนื้อหา</th><th>สถานะ</th></tr>";
    
    foreach ($reviews as $review) {
        $rating_stars = str_repeat('⭐', $review['rating']);
        $status = $review['is_active'] ? '✅ เปิด' : '❌ ปิด';
        $image_path = !empty($review['image']) ? UPLOADS_URL . '/' . $review['image'] : 'ไม่มีรูป';
        $image_exists = !empty($review['image']) ? (file_exists($_SERVER['DOCUMENT_ROOT'] . '/vibedaybkk/uploads/' . $review['image']) ? '✅ มีไฟล์' : '❌ ไม่มีไฟล์') : 'ไม่มีรูป';
        
        echo "<tr>";
        echo "<td>" . $review['id'] . "</td>";
        echo "<td>" . htmlspecialchars($review['customer_name']) . "</td>";
        echo "<td>";
        echo "<strong>Path:</strong> " . htmlspecialchars($image_path) . "<br>";
        echo "<strong>Status:</strong> " . $image_exists . "<br>";
        if (!empty($review['image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/vibedaybkk/uploads/' . $review['image'])) {
            echo "<img src='" . htmlspecialchars($image_path) . "' alt='Review Image' style='width: 100px; height: 60px; object-fit: cover; border-radius: 5px;'>";
        }
        echo "</td>";
        echo "<td>{$rating_stars} ({$review['rating']})</td>";
        echo "<td>" . htmlspecialchars(substr($review['content'], 0, 50)) . "...</td>";
        echo "<td>{$status}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ ไม่พบรีวิวในฐานข้อมูล</p>";
}

echo "<h2>🖼️ ทดสอบการแสดงรูปภาพ:</h2>";

if (!empty($reviews)) {
    foreach ($reviews as $index => $review) {
        if (!empty($review['image'])) {
            $image_path = UPLOADS_URL . '/' . $review['image'];
            $file_exists = file_exists($_SERVER['DOCUMENT_ROOT'] . '/vibedaybkk/uploads/' . $review['image']);
            
            echo "<div style='border: 2px solid #ccc; padding: 20px; margin: 20px 0; border-radius: 10px;'>";
            echo "<h3>รีวิว #{$review['id']} - " . htmlspecialchars($review['customer_name']) . "</h3>";
            echo "<p><strong>Image Path:</strong> " . htmlspecialchars($image_path) . "</p>";
            echo "<p><strong>File Exists:</strong> " . ($file_exists ? '✅ ใช่' : '❌ ไม่') . "</p>";
            
            if ($file_exists) {
                echo "<div style='border: 2px solid #28a745; padding: 10px; border-radius: 5px;'>";
                echo "<img src='" . htmlspecialchars($image_path) . "' alt='Review Image' style='max-width: 300px; max-height: 200px; object-fit: cover; border-radius: 5px;'>";
                echo "</div>";
            } else {
                echo "<div style='border: 2px solid #dc3545; padding: 10px; border-radius: 5px; background: #f8d7da; color: #721c24;'>";
                echo "❌ ไม่สามารถโหลดรูปภาพได้ - ไฟล์ไม่พบ";
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "<div style='border: 2px solid #ffc107; padding: 20px; margin: 20px 0; border-radius: 10px; background: #fff3cd;'>";
            echo "<h3>รีวิว #{$review['id']} - " . htmlspecialchars($review['customer_name']) . "</h3>";
            echo "<p>⚠️ ไม่มีรูปภาพ</p>";
            echo "</div>";
        }
    }
} else {
    echo "<p>❌ ไม่มีรีวิวสำหรับทดสอบ</p>";
}

echo "<h2>🔧 ข้อมูลเพิ่มเติม:</h2>";
echo "<p><strong>UPLOADS_URL:</strong> " . (defined('UPLOADS_URL') ? UPLOADS_URL : 'NOT DEFINED') . "</p>";
echo "<p><strong>BASE_URL:</strong> " . (defined('BASE_URL') ? BASE_URL : 'NOT DEFINED') . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Uploads Directory:</strong> " . $_SERVER['DOCUMENT_ROOT'] . '/vibedaybkk/uploads/</p>";

// ตรวจสอบโฟลเดอร์ uploads
$uploads_dir = $_SERVER['DOCUMENT_ROOT'] . '/vibedaybkk/uploads/';
echo "<p><strong>Uploads Directory exists:</strong> " . (is_dir($uploads_dir) ? '✅ ใช่' : '❌ ไม่') . "</p>";

if (is_dir($uploads_dir)) {
    $files = scandir($uploads_dir);
    $image_files = array_filter($files, function($file) {
        return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    });
    
    echo "<p><strong>ไฟล์รูปภาพใน uploads:</strong></p>";
    if (!empty($image_files)) {
        echo "<ul>";
        foreach ($image_files as $file) {
            echo "<li>" . htmlspecialchars($file) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ ไม่พบไฟล์รูปภาพ</p>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ทดสอบการแสดงรีวิว</title>
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
        <a href="admin/reviews/" class="btn">จัดการรีวิว</a>
        <a href="seed-reviews.php" class="btn">เพิ่มรีวิวตัวอย่าง</a>
        <a href="javascript:location.reload()" class="btn">รีเฟรช</a>
    </div>
</body>
</html>

<?php $conn->close(); ?>

