<?php
/**
 * ตรวจสอบโครงสร้างตาราง gallery
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบโครงสร้างตาราง</title>
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
        <h1>🔍 ตรวจสอบโครงสร้างตาราง Gallery</h1>
        
        <?php
        try {
            $tables = ['gallery_albums', 'gallery_images'];
            
            foreach ($tables as $table_name) {
                echo "<h2>📋 ตาราง: {$table_name}</h2>";
                
                $check_table = $conn->query("SHOW TABLES LIKE '{$table_name}'");
                
                if ($check_table->num_rows > 0) {
                    echo "<div class='result success'>✅ ตาราง {$table_name} มีอยู่ในระบบ</div>";
                    
                    // แสดงโครงสร้างตาราง
                    $structure = $conn->query("DESCRIBE {$table_name}");
                    echo "<h3>โครงสร้างตาราง:</h3>";
                    echo "<table>";
                    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
                    
                    $fields = [];
                    while ($row = $structure->fetch_assoc()) {
                        $fields[] = $row['Field'];
                        echo "<tr>";
                        echo "<td>" . $row['Field'] . "</td>";
                        echo "<td>" . $row['Type'] . "</td>";
                        echo "<td>" . $row['Null'] . "</td>";
                        echo "<td>" . $row['Key'] . "</td>";
                        echo "<td>" . $row['Default'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    
                    // ตรวจสอบฟิลด์ที่จำเป็น
                    if ($table_name === 'gallery_albums') {
                        $required_fields = ['name', 'description', 'cover_image', 'sort_order', 'is_active'];
                        echo "<h3>🔍 ตรวจสอบฟิลด์ที่จำเป็น:</h3>";
                        foreach ($required_fields as $field) {
                            if (in_array($field, $fields)) {
                                echo "<div class='result success'>✅ มีฟิลด์: {$field}</div>";
                            } else {
                                echo "<div class='result error'>❌ ไม่มีฟิลด์: {$field}</div>";
                            }
                        }
                    }
                    
                    if ($table_name === 'gallery_images') {
                        $required_fields = ['title', 'description', 'file_path', 'album_id', 'tags', 'sort_order', 'status', 'view_count'];
                        echo "<h3>🔍 ตรวจสอบฟิลด์ที่จำเป็น:</h3>";
                        foreach ($required_fields as $field) {
                            if (in_array($field, $fields)) {
                                echo "<div class='result success'>✅ มีฟิลด์: {$field}</div>";
                            } else {
                                echo "<div class='result error'>❌ ไม่มีฟิลด์: {$field}</div>";
                            }
                        }
                    }
                    
                } else {
                    echo "<div class='result error'>❌ ตาราง {$table_name} ไม่มีอยู่ในระบบ</div>";
                }
                
                echo "<hr>";
            }
            
        } catch (Exception $e) {
            echo "<div class='result error'>❌ Exception: " . $e->getMessage() . "</div>";
        }
        ?>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="fix-gallery-tables.php" class="btn btn-success">
                <i class="fas fa-wrench"></i> แก้ไขตาราง Gallery
            </a>
            <a href="create-gallery-tables.php" class="btn">
                <i class="fas fa-database"></i> สร้างตารางใหม่
            </a>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>