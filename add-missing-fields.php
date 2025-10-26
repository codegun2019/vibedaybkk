<?php
/**
 * เพิ่มฟิลด์ที่ขาดหายไปในตาราง gallery
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มฟิลด์ที่ขาดหายไป</title>
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
        <h1>🔧 เพิ่มฟิลด์ที่ขาดหายไป</h1>
        
        <?php
        if ($_POST['action'] ?? '' === 'add_fields') {
            try {
                echo "<div class='result progress'>🔧 กำลังเพิ่มฟิลด์ที่ขาดหายไป...</div>";
                
                // เพิ่มฟิลด์ในตาราง gallery_albums
                $albums_alterations = [
                    "ALTER TABLE gallery_albums ADD COLUMN name VARCHAR(255) NOT NULL AFTER id",
                    "ALTER TABLE gallery_albums ADD COLUMN description TEXT AFTER name",
                    "ALTER TABLE gallery_albums ADD COLUMN cover_image VARCHAR(255) DEFAULT NULL AFTER description",
                    "ALTER TABLE gallery_albums ADD COLUMN sort_order INT(11) DEFAULT 0 AFTER cover_image",
                    "ALTER TABLE gallery_albums ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER sort_order"
                ];
                
                echo "<h3>📋 แก้ไขตาราง gallery_albums:</h3>";
                foreach ($albums_alterations as $sql) {
                    if ($conn->query($sql)) {
                        $field_name = explode(' ', $sql)[5];
                        echo "<div class='result progress'>✅ เพิ่มฟิลด์: {$field_name}</div>";
                    } else {
                        $field_name = explode(' ', $sql)[5];
                        if (strpos($conn->error, 'Duplicate column name') !== false) {
                            echo "<div class='result warning'>⚠️ ฟิลด์ {$field_name} มีอยู่แล้ว</div>";
                        } else {
                            echo "<div class='result error'>❌ เกิดข้อผิดพลาด {$field_name}: " . $conn->error . "</div>";
                        }
                    }
                }
                
                // เพิ่มฟิลด์ในตาราง gallery_images
                $images_alterations = [
                    "ALTER TABLE gallery_images ADD COLUMN title VARCHAR(255) NOT NULL AFTER id",
                    "ALTER TABLE gallery_images ADD COLUMN description TEXT AFTER title",
                    "ALTER TABLE gallery_images ADD COLUMN file_path VARCHAR(500) NOT NULL AFTER description",
                    "ALTER TABLE gallery_images ADD COLUMN album_id INT(11) DEFAULT NULL AFTER file_path",
                    "ALTER TABLE gallery_images ADD COLUMN tags TEXT DEFAULT NULL AFTER album_id",
                    "ALTER TABLE gallery_images ADD COLUMN sort_order INT(11) DEFAULT 0 AFTER tags",
                    "ALTER TABLE gallery_images ADD COLUMN status ENUM('active','inactive') DEFAULT 'active' AFTER sort_order",
                    "ALTER TABLE gallery_images ADD COLUMN view_count INT(11) DEFAULT 0 AFTER status"
                ];
                
                echo "<h3>📋 แก้ไขตาราง gallery_images:</h3>";
                foreach ($images_alterations as $sql) {
                    if ($conn->query($sql)) {
                        $field_name = explode(' ', $sql)[5];
                        echo "<div class='result progress'>✅ เพิ่มฟิลด์: {$field_name}</div>";
                    } else {
                        $field_name = explode(' ', $sql)[5];
                        if (strpos($conn->error, 'Duplicate column name') !== false) {
                            echo "<div class='result warning'>⚠️ ฟิลด์ {$field_name} มีอยู่แล้ว</div>";
                        } else {
                            echo "<div class='result error'>❌ เกิดข้อผิดพลาด {$field_name}: " . $conn->error . "</div>";
                        }
                    }
                }
                
                // ตรวจสอบฟิลด์ที่จำเป็น
                echo "<h3>🔍 ตรวจสอบฟิลด์ที่จำเป็น:</h3>";
                
                // ตรวจสอบ gallery_albums
                $albums_structure = $conn->query("DESCRIBE gallery_albums");
                $albums_fields = [];
                while ($row = $albums_structure->fetch_assoc()) {
                    $albums_fields[] = $row['Field'];
                }
                
                $albums_required = ['name', 'description', 'cover_image', 'sort_order', 'is_active'];
                foreach ($albums_required as $field) {
                    if (in_array($field, $albums_fields)) {
                        echo "<div class='result success'>✅ gallery_albums มีฟิลด์: {$field}</div>";
                    } else {
                        echo "<div class='result error'>❌ gallery_albums ไม่มีฟิลด์: {$field}</div>";
                    }
                }
                
                // ตรวจสอบ gallery_images
                $images_structure = $conn->query("DESCRIBE gallery_images");
                $images_fields = [];
                while ($row = $images_structure->fetch_assoc()) {
                    $images_fields[] = $row['Field'];
                }
                
                $images_required = ['title', 'description', 'file_path', 'album_id', 'tags', 'sort_order', 'status', 'view_count'];
                foreach ($images_required as $field) {
                    if (in_array($field, $images_fields)) {
                        echo "<div class='result success'>✅ gallery_images มีฟิลด์: {$field}</div>";
                    } else {
                        echo "<div class='result error'>❌ gallery_images ไม่มีฟิลด์: {$field}</div>";
                    }
                }
                
                echo "<div class='result success'>";
                echo "<h3>🎉 เพิ่มฟิลด์เสร็จสิ้น!</h3>";
                echo "<p>ตอนนี้ตาราง gallery พร้อมใช้งานแล้ว</p>";
                echo "<p><a href='seed-gallery-admin.php' style='color: #155724; font-weight: bold;'>📸 เพิ่มข้อมูลตัวอย่าง</a></p>";
                echo "</div>";
                
            } catch (Exception $e) {
                echo "<div class='result error'>❌ Exception: " . $e->getMessage() . "</div>";
            }
        }
        ?>
        
        <form method="POST">
            <button type="submit" name="action" value="add_fields" class="btn btn-success">
                <i class="fas fa-plus"></i> เพิ่มฟิลด์ที่ขาดหายไป
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
            <h3>📋 ฟิลด์ที่จะเพิ่ม:</h3>
            <h4>gallery_albums:</h4>
            <ul>
                <li>name (VARCHAR) - ชื่ออัลบั้ม</li>
                <li>description (TEXT) - คำอธิบาย</li>
                <li>cover_image (VARCHAR) - รูปปก</li>
                <li>sort_order (INT) - ลำดับ</li>
                <li>is_active (TINYINT) - สถานะ</li>
            </ul>
            <h4>gallery_images:</h4>
            <ul>
                <li>title (VARCHAR) - ชื่อรูปภาพ</li>
                <li>description (TEXT) - คำอธิบาย</li>
                <li>file_path (VARCHAR) - path ของรูปภาพ</li>
                <li>album_id (INT) - ID อัลบั้ม</li>
                <li>tags (TEXT) - แท็ก</li>
                <li>sort_order (INT) - ลำดับ</li>
                <li>status (ENUM) - สถานะ</li>
                <li>view_count (INT) - จำนวนการดู</li>
            </ul>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>



