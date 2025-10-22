<?php
/**
 * Seed Reviews - เพิ่มรีวิว 20 รีวิวเป็นข้อมูลตัวอย่าง
 */

require_once 'includes/config.php';

echo "<h1>🌟 เพิ่มรีวิว 20 รีวิวเป็นข้อมูลตัวอย่าง</h1>";

// ข้อมูลรีวิวตัวอย่าง
$sample_reviews = [
    [
        'customer_name' => 'คุณสมชาย ใจดี',
        'rating' => 5,
        'review_text' => 'บริการดีมาก โมเดลสวย ทำงานเป็นมืออาชีพ แนะนำเลย!',
        'is_active' => 1,
        'sort_order' => 1
    ],
    [
        'customer_name' => 'คุณสมหญิง งามงาม',
        'rating' => 5,
        'review_text' => 'พนักงานดูแลดีมาก ราคาเหมาะสม คุณภาพเกินคาด',
        'is_active' => 1,
        'sort_order' => 2
    ],
    [
        'customer_name' => 'คุณวิชัย เก่งเก่ง',
        'rating' => 4,
        'review_text' => 'โมเดลทำงานได้ดี ตรงเวลา แต่ราคาค่อนข้างสูง',
        'is_active' => 1,
        'sort_order' => 3
    ],
    [
        'customer_name' => 'คุณมาลี สวยงาม',
        'rating' => 5,
        'review_text' => 'ผลงานออกมาสวยมาก เกินความคาดหวัง จะใช้บริการอีกแน่นอน',
        'is_active' => 1,
        'sort_order' => 4
    ],
    [
        'customer_name' => 'คุณประเสริฐ ดีใจ',
        'rating' => 5,
        'review_text' => 'ทีมงานเป็นมิตร โมเดลมีประสบการณ์ งานออกมาสมบูรณ์แบบ',
        'is_active' => 1,
        'sort_order' => 5
    ],
    [
        'customer_name' => 'คุณนิดา ใจงาม',
        'rating' => 4,
        'review_text' => 'บริการครบวงจร ตั้งแต่การถ่ายจนถึงการแก้ไข ดีมาก',
        'is_active' => 1,
        'sort_order' => 6
    ],
    [
        'customer_name' => 'คุณสมศักดิ์ เก่งมาก',
        'rating' => 5,
        'review_text' => 'โมเดลหลากหลาย ราคาเป็นธรรม คุณภาพดีเยี่ยม',
        'is_active' => 1,
        'sort_order' => 7
    ],
    [
        'customer_name' => 'คุณกัลยา สวยใส',
        'rating' => 5,
        'review_text' => 'พนักงานแนะนำดีมาก ช่วยเลือกโมเดลที่เหมาะกับงาน',
        'is_active' => 1,
        'sort_order' => 8
    ],
    [
        'customer_name' => 'คุณธนพล ใจดี',
        'rating' => 4,
        'review_text' => 'งานเสร็จเร็ว ตรงเวลา โมเดลมีทักษะดี แต่ราคาแพงไปหน่อย',
        'is_active' => 1,
        'sort_order' => 9
    ],
    [
        'customer_name' => 'คุณวิไล งามงาม',
        'rating' => 5,
        'review_text' => 'ผลงานสวยมาก เกินความคาดหวัง ราคาคุ้มค่า แนะนำเลย',
        'is_active' => 1,
        'sort_order' => 10
    ],
    [
        'customer_name' => 'คุณสมพร ใจดี',
        'rating' => 5,
        'review_text' => 'บริการดีมาก โมเดลสวย ทำงานเป็นมืออาชีพ ตรงเวลา',
        'is_active' => 1,
        'sort_order' => 11
    ],
    [
        'customer_name' => 'คุณชัยวัฒน์ เก่งเก่ง',
        'rating' => 4,
        'review_text' => 'คุณภาพดี ราคาเหมาะสม แต่ต้องจองล่วงหน้า',
        'is_active' => 1,
        'sort_order' => 12
    ],
    [
        'customer_name' => 'คุณมาลินี สวยงาม',
        'rating' => 5,
        'review_text' => 'ทีมงานเป็นมิตร โมเดลมีประสบการณ์ งานออกมาสมบูรณ์แบบ',
        'is_active' => 1,
        'sort_order' => 13
    ],
    [
        'customer_name' => 'คุณประยงค์ ดีใจ',
        'rating' => 5,
        'review_text' => 'ผลงานสวยมาก เกินความคาดหวัง ราคาคุ้มค่า แนะนำเลย',
        'is_active' => 1,
        'sort_order' => 14
    ],
    [
        'customer_name' => 'คุณนิตยา ใจงาม',
        'rating' => 4,
        'review_text' => 'บริการครบวงจร ตั้งแต่การถ่ายจนถึงการแก้ไข ดีมาก',
        'is_active' => 1,
        'sort_order' => 15
    ],
    [
        'customer_name' => 'คุณสมบูรณ์ เก่งมาก',
        'rating' => 5,
        'review_text' => 'โมเดลหลากหลาย ราคาเป็นธรรม คุณภาพดีเยี่ยม',
        'is_active' => 1,
        'sort_order' => 16
    ],
    [
        'customer_name' => 'คุณกัลยาณี สวยใส',
        'rating' => 5,
        'review_text' => 'พนักงานแนะนำดีมาก ช่วยเลือกโมเดลที่เหมาะกับงาน',
        'is_active' => 1,
        'sort_order' => 17
    ],
    [
        'customer_name' => 'คุณธนากร ใจดี',
        'rating' => 4,
        'review_text' => 'งานเสร็จเร็ว ตรงเวลา โมเดลมีทักษะดี แต่ราคาแพงไปหน่อย',
        'is_active' => 1,
        'sort_order' => 18
    ],
    [
        'customer_name' => 'คุณวิไลวรรณ งามงาม',
        'rating' => 5,
        'review_text' => 'ผลงานสวยมาก เกินความคาดหวัง ราคาคุ้มค่า แนะนำเลย',
        'is_active' => 1,
        'sort_order' => 19
    ],
    [
        'customer_name' => 'คุณสมชาย ใจดี',
        'rating' => 5,
        'review_text' => 'บริการดีมาก โมเดลสวย ทำงานเป็นมืออาชีพ แนะนำเลย!',
        'is_active' => 1,
        'sort_order' => 20
    ]
];

echo "<h2>📋 ข้อมูลรีวิวที่จะเพิ่ม:</h2>";
echo "<p>จำนวนรีวิว: " . count($sample_reviews) . " รีวิว</p>";

// ตรวจสอบตาราง customer_reviews
echo "<h2>🔍 ตรวจสอบตาราง customer_reviews:</h2>";

$table_check = $conn->query("SHOW TABLES LIKE 'customer_reviews'");
if ($table_check && $table_check->num_rows > 0) {
    echo "<p>✅ ตาราง customer_reviews มีอยู่</p>";
    
    // ตรวจสอบโครงสร้างตาราง
    $structure = $conn->query("DESCRIBE customer_reviews");
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
    echo "<p style='color: red;'>❌ ตาราง customer_reviews ไม่มีอยู่</p>";
    echo "<p>สร้างตาราง customer_reviews...</p>";
    
    $create_table = "CREATE TABLE IF NOT EXISTS customer_reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(100),
        image VARCHAR(255),
        rating INT DEFAULT 5,
        content TEXT,
        is_active TINYINT(1) DEFAULT 1,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($create_table)) {
        echo "<p>✅ สร้างตาราง customer_reviews สำเร็จ</p>";
    } else {
        echo "<p style='color: red;'>❌ สร้างตาราง customer_reviews ล้มเหลว: " . $conn->error . "</p>";
        exit;
    }
}

// ลบรีวิวเดิม (ถ้ามี)
echo "<h2>🗑️ ลบรีวิวเดิม:</h2>";
$delete_result = $conn->query("DELETE FROM customer_reviews");
if ($delete_result) {
    echo "<p>✅ ลบรีวิวเดิมสำเร็จ</p>";
} else {
    echo "<p>⚠️ ไม่มีรีวิวเดิมหรือลบไม่ได้: " . $conn->error . "</p>";
}

// เพิ่มรีวิวใหม่
echo "<h2>🚀 เพิ่มรีวิวใหม่:</h2>";

$success_count = 0;
$error_count = 0;

foreach ($sample_reviews as $index => $review) {
    $sql = "INSERT INTO customer_reviews (customer_name, rating, content, is_active, sort_order, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisii", 
        $review['customer_name'],
        $review['rating'],
        $review['review_text'],
        $review['is_active'],
        $review['sort_order']
    );
    
    if ($stmt->execute()) {
        echo "<p>✅ [{$index}] เพิ่มรีวิว: " . htmlspecialchars($review['customer_name']) . " (⭐ {$review['rating']})</p>";
        $success_count++;
    } else {
        echo "<p>❌ [{$index}] เพิ่มรีวิวล้มเหลว: " . $stmt->error . "</p>";
        $error_count++;
    }
    $stmt->close();
}

// ตรวจสอบผลลัพธ์
echo "<h2>📊 สรุปผลลัพธ์:</h2>";
echo "<p>✅ สำเร็จ: {$success_count} รีวิว</p>";
echo "<p>❌ ล้มเหลว: {$error_count} รีวิว</p>";

// แสดงรีวิวทั้งหมด
echo "<h2>🔍 รีวิวทั้งหมดในระบบ:</h2>";

$all_reviews = $conn->query("SELECT * FROM customer_reviews ORDER BY sort_order ASC");
if ($all_reviews && $all_reviews->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin: 20px 0; width: 100%;'>";
    echo "<tr><th>ID</th><th>ชื่อลูกค้า</th><th>Rating</th><th>รีวิว</th><th>สถานะ</th><th>ลำดับ</th></tr>";
    
    while ($review = $all_reviews->fetch_assoc()) {
        $rating_stars = str_repeat('⭐', $review['rating']);
        $status = $review['is_active'] ? '✅ เปิด' : '❌ ปิด';
        
        echo "<tr>";
        echo "<td>" . $review['id'] . "</td>";
        echo "<td>" . htmlspecialchars($review['customer_name']) . "</td>";
        echo "<td>{$rating_stars} ({$review['rating']})</td>";
        echo "<td>" . htmlspecialchars(substr($review['content'], 0, 50)) . "...</td>";
        echo "<td>{$status}</td>";
        echo "<td>" . $review['sort_order'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ ไม่พบรีวิวในระบบ</p>";
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Seed Reviews</title>
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
        <a href="models.php" class="btn">ดูหน้าโมเดล</a>
        <a href="admin/reviews/" class="btn">จัดการรีวิว</a>
        <a href="javascript:location.reload()" class="btn">รีเฟรช</a>
    </div>
</body>
</html>

<?php $conn->close(); ?>
