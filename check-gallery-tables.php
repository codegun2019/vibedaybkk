<?php
/**
 * ตรวจสอบตาราง gallery ทั้งหมด
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
        .container { max-width: 1000px; margin: 0 auto; background: white; border-radius: 15px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        h1 { color: #667eea; margin-bottom: 20px; }
        .result { padding: 15px; border-radius: 8px; margin: 15px 0; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { background: #667eea; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block; }
        .btn:hover { background: #5558d9; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 ตรวจสอบตาราง Gallery ทั้งหมด</h1>
        
        <?php
        try {
            // ตรวจสอบตารางทั้งหมดที่เกี่ยวข้องกับ gallery
            $gallery_tables = ['gallery', 'gallery_images', 'gallery_albums'];
            
            foreach ($gallery_tables as $table_name) {
                echo "<h2>📋 ตาราง: {$table_name}</h2>";
                
                $tables = $conn->query("SHOW TABLES LIKE '{$table_name}'");
                
                if ($tables->num_rows > 0) {
                    echo "<div class='result success'>✅ ตาราง {$table_name} มีอยู่ในระบบ</div>";
                    
                    // แสดงโครงสร้างตาราง
                    $structure = $conn->query("DESCRIBE {$table_name}");
                    echo "<h3>โครงสร้างตาราง:</h3>";
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
                    $count = $conn->query("SELECT COUNT(*) as total FROM {$table_name}")->fetch_assoc()['total'];
                    echo "<div class='result " . ($count > 0 ? 'success' : 'warning') . "'>";
                    echo "📊 จำนวนข้อมูลในตาราง {$table_name}: {$count} รายการ";
                    echo "</div>";
                    
                    if ($count > 0) {
                        // แสดงข้อมูลตัวอย่าง
                        $samples = $conn->query("SELECT * FROM {$table_name} LIMIT 3")->fetch_all(MYSQLI_ASSOC);
                        echo "<h3>ข้อมูลตัวอย่าง:</h3>";
                        echo "<table>";
                        
                        if (!empty($samples)) {
                            // Header
                            echo "<tr>";
                            foreach (array_keys($samples[0]) as $key) {
                                echo "<th>" . $key . "</th>";
                            }
                            echo "</tr>";
                            
                            // Data
                            foreach ($samples as $sample) {
                                echo "<tr>";
                                foreach ($sample as $value) {
                                    echo "<td>" . htmlspecialchars(substr($value, 0, 50)) . (strlen($value) > 50 ? '...' : '') . "</td>";
                                }
                                echo "</tr>";
                            }
                        }
                        echo "</table>";
                    }
                    
                } else {
                    echo "<div class='result error'>❌ ตาราง {$table_name} ไม่มีอยู่ในระบบ</div>";
                }
                
                echo "<hr>";
            }
            
            // ตรวจสอบความสัมพันธ์ระหว่างตาราง
            echo "<h2>🔗 ความสัมพันธ์ระหว่างตาราง</h2>";
            
            $gallery_exists = $conn->query("SHOW TABLES LIKE 'gallery'")->num_rows > 0;
            $gallery_images_exists = $conn->query("SHOW TABLES LIKE 'gallery_images'")->num_rows > 0;
            $gallery_albums_exists = $conn->query("SHOW TABLES LIKE 'gallery_albums'")->num_rows > 0;
            
            if ($gallery_exists && $gallery_images_exists) {
                echo "<div class='result warning'>⚠️ มีตาราง gallery และ gallery_images ทั้งคู่ - อาจเกิดความสับสน</div>";
            }
            
            if ($gallery_images_exists && $gallery_albums_exists) {
                echo "<div class='result success'>✅ ระบบหลังบ้านใช้ gallery_images + gallery_albums</div>";
            }
            
            if ($gallery_exists && !$gallery_images_exists) {
                echo "<div class='result success'>✅ ระบบหน้าบ้านใช้ gallery</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='result error'>❌ Exception: " . $e->getMessage() . "</div>";
        }
        ?>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="admin/gallery/" class="btn">
                <i class="fas fa-cog"></i> หลังบ้าน Gallery
            </a>
            <a href="gallery.php" class="btn">
                <i class="fas fa-images"></i> หน้าบ้าน Gallery
            </a>
            <a href="gallery-simple.php" class="btn btn-success">
                <i class="fas fa-images"></i> Gallery แบบง่าย
            </a>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>

