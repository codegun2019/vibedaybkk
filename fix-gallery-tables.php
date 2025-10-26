<?php
/**
 * แก้ไขตาราง gallery ให้มีโครงสร้างที่ถูกต้อง
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขตาราง Gallery</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Thai', sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 15px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        h1 { color: #667eea; margin-bottom: 20px; }
        .btn { background: #667eea; color: white; padding: 15px 30px; border: none; border-radius: 8px; cursor: pointer; margin: 10px; text-decoration: none; display: inline-block; }
        .btn:hover { background: #5558d9; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .result { padding: 15px; border-radius: 8px; margin: 15px 0; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 แก้ไขตาราง Gallery</h1>
        
        <?php
        if ($_POST['action'] ?? '' === 'fix_tables') {
            try {
                echo "<div class='result progress'>🔧 กำลังแก้ไขตาราง Gallery...</div>";
                
                // ลบตารางเดิมถ้ามี
                $conn->query("DROP TABLE IF EXISTS gallery_images");
                $conn->query("DROP TABLE IF EXISTS gallery_albums");
                echo "<div class='result progress'>🗑️ ลบตารางเดิมแล้ว</div>";
                
                // สร้างตาราง gallery_albums ใหม่
                $albums_sql = "CREATE TABLE gallery_albums (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    name varchar(255) NOT NULL,
                    description text,
                    cover_image varchar(255) DEFAULT NULL,
                    sort_order int(11) DEFAULT 0,
                    is_active tinyint(1) DEFAULT 1,
                    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    KEY idx_is_active (is_active),
                    KEY idx_sort_order (sort_order)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                
                if ($conn->query($albums_sql)) {
                    echo "<div class='result success'>✅ สร้างตาราง gallery_albums สำเร็จ!</div>";
                } else {
                    echo "<div class='result error'>❌ เกิดข้อผิดพลาด gallery_albums: " . $conn->error . "</div>";
                }
                
                // สร้างตาราง gallery_images ใหม่
                $images_sql = "CREATE TABLE gallery_images (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    title varchar(255) NOT NULL,
                    description text,
                    file_path varchar(500) NOT NULL,
                    album_id int(11) DEFAULT NULL,
                    tags text DEFAULT NULL,
                    sort_order int(11) DEFAULT 0,
                    status enum('active','inactive') DEFAULT 'active',
                    view_count int(11) DEFAULT 0,
                    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    KEY idx_album_id (album_id),
                    KEY idx_status (status),
                    KEY idx_sort_order (sort_order),
                    FOREIGN KEY (album_id) REFERENCES gallery_albums(id) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                
                if ($conn->query($images_sql)) {
                    echo "<div class='result success'>✅ สร้างตาราง gallery_images สำเร็จ!</div>";
                } else {
                    echo "<div class='result error'>❌ เกิดข้อผิดพลาด gallery_images: " . $conn->error . "</div>";
                }
                
                // ตรวจสอบว่าตารางมีอยู่หรือไม่
                $tables = ['gallery_albums', 'gallery_images'];
                $all_success = true;
                
                foreach ($tables as $table_name) {
                    $check_table = $conn->query("SHOW TABLES LIKE '{$table_name}'");
                    if ($check_table->num_rows > 0) {
                        echo "<div class='result success'>✅ ตาราง {$table_name} พร้อมใช้งาน</div>";
                        
                        // ตรวจสอบฟิลด์ที่จำเป็น
                        $structure = $conn->query("DESCRIBE {$table_name}");
                        $fields = [];
                        while ($row = $structure->fetch_assoc()) {
                            $fields[] = $row['Field'];
                        }
                        
                        if ($table_name === 'gallery_albums') {
                            $required_fields = ['name', 'description', 'cover_image', 'sort_order', 'is_active'];
                            foreach ($required_fields as $field) {
                                if (!in_array($field, $fields)) {
                                    echo "<div class='result error'>❌ ไม่มีฟิลด์: {$field}</div>";
                                    $all_success = false;
                                }
                            }
                        }
                        
                        if ($table_name === 'gallery_images') {
                            $required_fields = ['title', 'description', 'file_path', 'album_id', 'tags', 'sort_order', 'status', 'view_count'];
                            foreach ($required_fields as $field) {
                                if (!in_array($field, $fields)) {
                                    echo "<div class='result error'>❌ ไม่มีฟิลด์: {$field}</div>";
                                    $all_success = false;
                                }
                            }
                        }
                    } else {
                        echo "<div class='result error'>❌ ตาราง {$table_name} ไม่มีอยู่ในระบบ</div>";
                        $all_success = false;
                    }
                }
                
                if ($all_success) {
                    echo "<div class='result success'>";
                    echo "<h3>🎉 แก้ไขตารางสำเร็จ!</h3>";
                    echo "<p>ตอนนี้ตาราง gallery_albums และ gallery_images พร้อมใช้งานแล้ว</p>";
                    echo "<p><a href='seed-gallery-admin.php' style='color: #155724; font-weight: bold;'>📸 เพิ่มข้อมูลตัวอย่าง</a></p>";
                    echo "</div>";
                } else {
                    echo "<div class='result error'>❌ ยังมีปัญหากับโครงสร้างตาราง</div>";
                }
                
            } catch (Exception $e) {
                echo "<div class='result error'>❌ Exception: " . $e->getMessage() . "</div>";
            }
        }
        ?>
        
        <form method="POST">
            <button type="submit" name="action" value="fix_tables" class="btn btn-success">
                <i class="fas fa-wrench"></i> แก้ไขตาราง Gallery
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="check-table-structure.php" class="btn">
                <i class="fas fa-search"></i> ตรวจสอบโครงสร้าง
            </a>
            <a href="seed-gallery-admin.php" class="btn">
                <i class="fas fa-plus"></i> เพิ่มข้อมูลตัวอย่าง
            </a>
        </div>
        
        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <h3>📋 ฟิลด์ที่จำเป็น:</h3>
            <h4>gallery_albums:</h4>
            <ul>
                <li>name (varchar) - ชื่ออัลบั้ม</li>
                <li>description (text) - คำอธิบาย</li>
                <li>cover_image (varchar) - รูปปก</li>
                <li>sort_order (int) - ลำดับ</li>
                <li>is_active (tinyint) - สถานะ</li>
            </ul>
            <h4>gallery_images:</h4>
            <ul>
                <li>title (varchar) - ชื่อรูปภาพ</li>
                <li>description (text) - คำอธิบาย</li>
                <li>file_path (varchar) - path ของรูปภาพ</li>
                <li>album_id (int) - ID อัลบั้ม</li>
                <li>tags (text) - แท็ก</li>
                <li>sort_order (int) - ลำดับ</li>
                <li>status (enum) - สถานะ</li>
                <li>view_count (int) - จำนวนการดู</li>
            </ul>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>



