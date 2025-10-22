<?php
/**
 * สร้างตาราง gallery_albums และ gallery_images
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สร้างตาราง Gallery</title>
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
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 สร้างตาราง Gallery</h1>
        
        <?php
        if ($_POST['action'] ?? '' === 'create_tables') {
            try {
                // สร้างตาราง gallery_albums
                $albums_sql = "CREATE TABLE IF NOT EXISTS gallery_albums (
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
                
                // สร้างตาราง gallery_images
                $images_sql = "CREATE TABLE IF NOT EXISTS gallery_images (
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
                foreach ($tables as $table_name) {
                    $check_table = $conn->query("SHOW TABLES LIKE '{$table_name}'");
                    if ($check_table->num_rows > 0) {
                        echo "<div class='result success'>✅ ตาราง {$table_name} มีอยู่ในระบบแล้ว</div>";
                        
                        // แสดงโครงสร้างตาราง
                        $structure = $conn->query("DESCRIBE {$table_name}");
                        echo "<h3>📋 โครงสร้างตาราง {$table_name}:</h3>";
                        echo "<table>";
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
                    } else {
                        echo "<div class='result error'>❌ ตาราง {$table_name} ไม่มีอยู่ในระบบ</div>";
                    }
                }
                
            } catch (Exception $e) {
                echo "<div class='result error'>❌ Exception: " . $e->getMessage() . "</div>";
            }
        }
        ?>
        
        <form method="POST">
            <button type="submit" name="action" value="create_tables" class="btn btn-success">
                <i class="fas fa-database"></i> สร้างตาราง Gallery
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="seed-gallery-admin.php" class="btn">
                <i class="fas fa-plus"></i> เพิ่มข้อมูลตัวอย่าง
            </a>
            <a href="admin/gallery/" class="btn">
                <i class="fas fa-cog"></i> หลังบ้าน Gallery
            </a>
            <a href="gallery.php" class="btn">
                <i class="fas fa-images"></i> หน้าบ้าน Gallery
            </a>
        </div>
        
        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <h3>📋 ตารางที่จะสร้าง:</h3>
            <ul>
                <li><strong>gallery_albums:</strong> เก็บข้อมูลอัลบั้ม (ชื่อ, คำอธิบาย, รูปปก)</li>
                <li><strong>gallery_images:</strong> เก็บข้อมูลรูปภาพ (ชื่อ, คำอธิบาย, path, album_id)</li>
            </ul>
            <p><strong>ความสัมพันธ์:</strong> gallery_images.album_id → gallery_albums.id</p>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>

