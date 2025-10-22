<?php
/**
 * ตรวจสอบตาราง gallery
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบตาราง Gallery</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Thai', sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 15px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        h1 { color: #667eea; margin-bottom: 20px; }
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
        <h1>🔍 ตรวจสอบตาราง Gallery</h1>
        
        <?php
        try {
            // ตรวจสอบว่าตาราง gallery มีอยู่หรือไม่
            $tables = $conn->query("SHOW TABLES LIKE 'gallery'");
            
            if ($tables->num_rows > 0) {
                echo "<div class='result success'>✅ ตาราง gallery มีอยู่ในระบบ</div>";
                
                // แสดงโครงสร้างตาราง
                $structure = $conn->query("DESCRIBE gallery");
                echo "<h3>📋 โครงสร้างตาราง gallery:</h3>";
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
                
                // ตรวจสอบข้อมูลในตาราง
                $count = $conn->query("SELECT COUNT(*) as total FROM gallery")->fetch_assoc()['total'];
                echo "<div class='result " . ($count > 0 ? 'success' : 'warning') . "'>";
                echo "📊 จำนวนข้อมูลในตาราง gallery: {$count} รายการ";
                echo "</div>";
                
                if ($count > 0) {
                    // แสดงข้อมูลตัวอย่าง
                    $samples = $conn->query("SELECT id, title, category, image FROM gallery LIMIT 5")->fetch_all(MYSQLI_ASSOC);
                    echo "<h3>📸 ข้อมูลตัวอย่าง:</h3>";
                    echo "<table>";
                    echo "<tr><th>ID</th><th>Title</th><th>Category</th><th>Image Path</th></tr>";
                    
                    foreach ($samples as $sample) {
                        echo "<tr>";
                        echo "<td>" . $sample['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($sample['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($sample['category']) . "</td>";
                        echo "<td>" . htmlspecialchars($sample['image']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
                
            } else {
                echo "<div class='result error'>❌ ตาราง gallery ไม่มีอยู่ในระบบ</div>";
                echo "<div class='result warning'>⚠️ ต้องสร้างตาราง gallery ก่อน</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='result error'>❌ Exception: " . $e->getMessage() . "</div>";
        }
        ?>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="setup-gallery-table.php" class="btn" style="background: #667eea; color: white; padding: 15px 30px; border: none; border-radius: 8px; cursor: pointer; margin: 10px; text-decoration: none; display: inline-block;">
                <i class="fas fa-database"></i> สร้างตาราง Gallery
            </a>
            <a href="gallery.php" class="btn" style="background: #28a745; color: white; padding: 15px 30px; border: none; border-radius: 8px; cursor: pointer; margin: 10px; text-decoration: none; display: inline-block;">
                <i class="fas fa-images"></i> ดูแกลลอรี่
            </a>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>

