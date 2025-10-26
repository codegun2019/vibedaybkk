<?php
/**
 * เพิ่มเมนู Gallery ในฐานข้อมูล
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มเมนู Gallery</title>
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
        <h1>📸 เพิ่มเมนู Gallery</h1>
        
        <?php
        if ($_POST['action'] ?? '' === 'add_menu') {
            try {
                // ตรวจสอบว่าเมนู Gallery มีอยู่แล้วหรือไม่
                $existing_menu = db_get_row($conn, "SELECT * FROM menus WHERE url = 'gallery.php'");
                
                if ($existing_menu) {
                    echo "<div class='result warning'>⚠️ เมนู Gallery มีอยู่แล้วในระบบ</div>";
                    echo "<div class='result success'>✅ เมนู Gallery พร้อมใช้งานแล้ว</div>";
                } else {
                    // เพิ่มเมนู Gallery
                    $menu_data = [
                        'title' => 'Gallery',
                        'url' => 'gallery.php',
                        'icon' => 'fas fa-images',
                        'status' => 'active',
                        'is_active' => 1,
                        'sort_order' => 5,
                        'parent_id' => null
                    ];
                    
                    if (db_insert($conn, 'menus', $menu_data)) {
                        echo "<div class='result success'>✅ เพิ่มเมนู Gallery สำเร็จ!</div>";
                    } else {
                        echo "<div class='result error'>❌ เกิดข้อผิดพลาดในการเพิ่มเมนู</div>";
                    }
                }
                
                // แสดงรายการเมนูทั้งหมด
                $menus = db_get_rows($conn, "SELECT * FROM menus ORDER BY sort_order ASC");
                echo "<h3>📋 รายการเมนูทั้งหมด:</h3>";
                echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
                echo "<tr><th>ID</th><th>ชื่อเมนู</th><th>URL</th><th>ไอคอน</th><th>สถานะ</th><th>ลำดับ</th></tr>";
                
                foreach ($menus as $menu) {
                    echo "<tr>";
                    echo "<td>" . $menu['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($menu['title']) . "</td>";
                    echo "<td>" . htmlspecialchars($menu['url']) . "</td>";
                    echo "<td>" . htmlspecialchars($menu['icon'] ?? 'ไม่มี') . "</td>";
                    echo "<td>" . htmlspecialchars($menu['status']) . "</td>";
                    echo "<td>" . $menu['sort_order'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
                
            } catch (Exception $e) {
                echo "<div class='result error'>❌ Exception: " . $e->getMessage() . "</div>";
            }
        }
        ?>
        
        <form method="POST">
            <button type="submit" name="action" value="add_menu" class="btn btn-success">
                <i class="fas fa-plus"></i> เพิ่มเมนู Gallery
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="gallery.php" class="btn">
                <i class="fas fa-images"></i> ดูแกลลอรี่
            </a>
            <a href="index.php" class="btn">
                <i class="fas fa-home"></i> กลับหน้าแรก
            </a>
        </div>
        
        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <h3>📋 ข้อมูลเมนู Gallery:</h3>
            <ul>
                <li><strong>ชื่อเมนู:</strong> Gallery</li>
                <li><strong>URL:</strong> gallery.php</li>
                <li><strong>ไอคอน:</strong> fas fa-images</li>
                <li><strong>สถานะ:</strong> active</li>
                <li><strong>ลำดับ:</strong> 5</li>
            </ul>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>



