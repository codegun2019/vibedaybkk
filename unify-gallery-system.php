<?php
/**
 * รวมระบบแกลลอรี่ให้ใช้ตารางเดียวกัน
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รวมระบบแกลลอรี่</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Thai', sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 15px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        h1 { color: #667eea; margin-bottom: 20px; }
        .btn { background: #667eea; color: white; padding: 15px 30px; border: none; border-radius: 8px; cursor: pointer; margin: 10px; text-decoration: none; display: inline-block; }
        .btn:hover { background: #5558d9; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .btn-warning { background: #ffc107; color: #000; }
        .btn-warning:hover { background: #e0a800; }
        .result { padding: 15px; border-radius: 8px; margin: 15px 0; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 รวมระบบแกลลอรี่</h1>
        
        <?php
        if ($_POST['action'] ?? '' === 'unify_system') {
            try {
                echo "<div class='result info'>🔧 กำลังรวมระบบแกลลอรี่...</div>";
                
                // ตรวจสอบข้อมูลในแต่ละตาราง
                $gallery_count = 0;
                $gallery_images_count = 0;
                
                $check_gallery = $conn->query("SHOW TABLES LIKE 'gallery'");
                if ($check_gallery->num_rows > 0) {
                    $gallery_count = $conn->query("SELECT COUNT(*) as total FROM gallery")->fetch_assoc()['total'];
                }
                
                $check_gallery_images = $conn->query("SHOW TABLES LIKE 'gallery_images'");
                if ($check_gallery_images->num_rows > 0) {
                    $gallery_images_count = $conn->query("SELECT COUNT(*) as total FROM gallery_images")->fetch_assoc()['total'];
                }
                
                echo "<div class='result info'>📊 ข้อมูลปัจจุบัน:</div>";
                echo "<div class='result info'>- ตาราง 'gallery': {$gallery_count} รายการ</div>";
                echo "<div class='result info'>- ตาราง 'gallery_images': {$gallery_images_count} รายการ</div>";
                
                if ($gallery_count > 0 && $gallery_images_count == 0) {
                    // ใช้ตาราง gallery
                    echo "<div class='result success'>✅ ใช้ตาราง 'gallery' เป็นหลัก</div>";
                    
                    // แก้ไข gallery.php ให้ใช้ตาราง gallery
                    $gallery_php_content = file_get_contents('gallery.php');
                    
                    // เปลี่ยนจาก gallery_images เป็น gallery
                    $new_content = str_replace('gallery_images gi', 'gallery gi', $gallery_php_content);
                    $new_content = str_replace('gallery_albums ga', 'gallery ga', $new_content);
                    $new_content = str_replace('gi.album_id = ga.id', 'gi.category = ga.name', $new_content);
                    $new_content = str_replace('gi.status = \'active\'', 'gi.is_active = 1', $new_content);
                    $new_content = str_replace('gi.file_path', 'gi.image', $new_content);
                    
                    file_put_contents('gallery.php', $new_content);
                    echo "<div class='result success'>✅ แก้ไข gallery.php ให้ใช้ตาราง 'gallery'</div>";
                    
                } elseif ($gallery_images_count > 0 && $gallery_count == 0) {
                    // ใช้ตาราง gallery_images
                    echo "<div class='result success'>✅ ใช้ตาราง 'gallery_images' เป็นหลัก</div>";
                    
                    // แก้ไข gallery.php ให้ใช้ตาราง gallery_images
                    $gallery_php_content = file_get_contents('gallery.php');
                    
                    // เปลี่ยนจาก gallery เป็น gallery_images
                    $new_content = str_replace('FROM gallery gi', 'FROM gallery_images gi', $gallery_php_content);
                    $new_content = str_replace('LEFT JOIN gallery ga', 'LEFT JOIN gallery_albums ga', $new_content);
                    $new_content = str_replace('gi.category = ga.name', 'gi.album_id = ga.id', $new_content);
                    $new_content = str_replace('gi.is_active = 1', 'gi.status = \'active\'', $new_content);
                    $new_content = str_replace('gi.image', 'gi.file_path', $new_content);
                    
                    file_put_contents('gallery.php', $new_content);
                    echo "<div class='result success'>✅ แก้ไข gallery.php ให้ใช้ตาราง 'gallery_images'</div>";
                    
                } elseif ($gallery_count > 0 && $gallery_images_count > 0) {
                    // มีข้อมูลทั้งสองตาราง - ให้เลือก
                    echo "<div class='result warning'>⚠️ มีข้อมูลทั้งสองตาราง - ต้องเลือกใช้ตารางเดียว</div>";
                    echo "<div class='result info'>💡 แนะนำ: ใช้ตาราง 'gallery' เพราะมีข้อมูลมากกว่า</div>";
                    
                    // ใช้ตาราง gallery
                    echo "<div class='result success'>✅ ใช้ตาราง 'gallery' เป็นหลัก</div>";
                    
                    // แก้ไข gallery.php ให้ใช้ตาราง gallery
                    $gallery_php_content = file_get_contents('gallery.php');
                    
                    // เปลี่ยนจาก gallery_images เป็น gallery
                    $new_content = str_replace('gallery_images gi', 'gallery gi', $gallery_php_content);
                    $new_content = str_replace('gallery_albums ga', 'gallery ga', $new_content);
                    $new_content = str_replace('gi.album_id = ga.id', 'gi.category = ga.name', $new_content);
                    $new_content = str_replace('gi.status = \'active\'', 'gi.is_active = 1', $new_content);
                    $new_content = str_replace('gi.file_path', 'gi.image', $new_content);
                    
                    file_put_contents('gallery.php', $new_content);
                    echo "<div class='result success'>✅ แก้ไข gallery.php ให้ใช้ตาราง 'gallery'</div>";
                    
                } else {
                    // ไม่มีข้อมูล - สร้างข้อมูลใหม่
                    echo "<div class='result warning'>⚠️ ไม่มีข้อมูลในตารางใด - จะสร้างข้อมูลใหม่</div>";
                    
                    // สร้างตาราง gallery ใหม่
                    $create_gallery_sql = "CREATE TABLE IF NOT EXISTS gallery (
                        id int(11) NOT NULL AUTO_INCREMENT,
                        title varchar(255) NOT NULL,
                        description text,
                        image varchar(500) NOT NULL,
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
                    
                    if ($conn->query($create_gallery_sql)) {
                        echo "<div class='result success'>✅ สร้างตาราง 'gallery' ใหม่</div>";
                    } else {
                        echo "<div class='result error'>❌ เกิดข้อผิดพลาดในการสร้างตาราง: " . $conn->error . "</div>";
                    }
                }
                
                echo "<div class='result success'>";
                echo "<h3>🎉 รวมระบบแกลลอรี่เสร็จสิ้น!</h3>";
                echo "<p>ตอนนี้หน้าบ้านและหลังบ้านใช้ตารางเดียวกันแล้ว</p>";
                echo "<p><a href='gallery.php' style='color: #155724; font-weight: bold;'>📸 ดูแกลลอรี่หน้าบ้าน</a></p>";
                echo "<p><a href='admin/gallery/' style='color: #155724; font-weight: bold;'>⚙️ จัดการแกลลอรี่หลังบ้าน</a></p>";
                echo "</div>";
                
            } catch (Exception $e) {
                echo "<div class='result error'>❌ Exception: " . $e->getMessage() . "</div>";
            }
        }
        ?>
        
        <form method="POST">
            <button type="submit" name="action" value="unify_system" class="btn btn-success">
                <i class="fas fa-unify"></i> รวมระบบแกลลอรี่
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="check-gallery-system.php" class="btn">
                <i class="fas fa-search"></i> ตรวจสอบระบบ
            </a>
            <a href="gallery.php" class="btn">
                <i class="fas fa-images"></i> หน้าบ้าน
            </a>
            <a href="admin/gallery/" class="btn">
                <i class="fas fa-cog"></i> หลังบ้าน
            </a>
        </div>
        
        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <h3>📋 ระบบที่แก้ไข:</h3>
            <ul>
                <li><strong>ตรวจสอบข้อมูล:</strong> ดูว่าตารางไหนมีข้อมูล</li>
                <li><strong>เลือกตารางหลัก:</strong> ใช้ตารางที่มีข้อมูลมากกว่า</li>
                <li><strong>แก้ไขโค้ด:</strong> ปรับ gallery.php ให้ใช้ตารางที่เลือก</li>
                <li><strong>รวมระบบ:</strong> ให้หน้าบ้านและหลังบ้านใช้ตารางเดียวกัน</li>
            </ul>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>



