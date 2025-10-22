<?php
/**
 * สร้างตาราง gallery ในฐานข้อมูล
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
        cess { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📸 สร้างตาราง Gallery</h1>
        
        <?php
        if ($_POST['action'] ?? '' === 'create_table') {
            try {
                // สร้างตาราง gallery
                $sql = "CREATE TABLE IF NOT EXISTS gallery (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    title varchar(255) NOT NULL,
                    description text,
                    image varchar(255) NOT NULL,
                    category varchar(100) DEFAULT NULL,
                    tags text DEFAULT NULL,
                    sort_order int(11) DEFAULT 0,
                    is_active tinyint(1) DEFAULT 1,
                    view_count int(11) DEFAULT 0,
                    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    KEY idx_category (category),
                    KEY idx_is_active (is_active),
                    KEY idx_sort_order (sort_order)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                
                if ($conn->query($sql)) {
                    echo "<div class='result success'>✅ สร้างตาราง gallery สำเร็จ!</div>";
                } else {
                    echo "<div class='result error'>❌ เกิดข้อผิดพลาด: " . $conn->error . "</div>";
                }
                
                // ตรวจสอบว่าตารางมีอยู่หรือไม่
                $check_table = $conn->query("SHOW TABLES LIKE 'gallery'");
                if ($check_table->num_rows > 0) {
                    echo "<div class='result success'>✅ ตาราง gallery มีอยู่ในระบบแล้ว</div>";
                    
                    // แสดงโครงสร้างตาราง
                    $structure = $conn->query("DESCRIBE gallery");
                    echo "<h3>📋 โครงสร้างตาราง gallery:</h3>";
                    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
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
                
            } catch (Exception $e) {
                echo "<div class='result error'>❌ Exception: " . $e->getMessage() . "</div>";
            }
        }
        
        if ($_POST['action'] ?? '' === 'add_menu') {
            try {
                // เพิ่มเมนู Gallery
                $menu_data = [
                    'title' => 'Gallery',
                    'url' => 'gallery.php',
                    'icon' => 'fas fa-images',
                    'is_active' => 1,
                    'sort_order' => 5
                ];
                
                if (db_insert($conn, 'menus', $menu_data)) {
                    echo "<div class='result success'>✅ เพิ่มเมนู Gallery สำเร็จ!</div>";
                } else {
                    echo "<div class='result error'>❌ เกิดข้อผิดพลาดในการเพิ่มเมนู</div>";
                }
                
            } catch (Exception $e) {
                echo "<div class='result error'>❌ Exception: " . $e->getMessage() . "</div>";
            }
        }
        ?>
        
        <form method="POST">
            <button type="submit" name="action" value="create_table" class="btn">
                <i class="fas fa-database"></i> สร้างตาราง Gallery
            </button>
        </form>
        
        <form method="POST">
            <button type="submit" name="action" value="add_menu" class="btn btn-success">
                <i class="fas fa-plus"></i> เพิ่มเมนู Gallery
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="gallery.php" class="btn">
                <i class="fas fa-images"></i> ไปหน้า Gallery
            </a>
            <a href="index.php" class="btn">
                <i class="fas fa-home"></i> กลับหน้าแรก
            </a>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>

