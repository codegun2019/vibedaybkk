<?php
/**
 * ตรวจสอบระบบแกลลอรี่ทั้งหมด
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบระบบแกลลอรี่</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Thai', sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; border-radius: 15px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        h1 { color: #667eea; margin-bottom: 20px; }
        .result { padding: 15px; border-radius: 8px; margin: 15px 0; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
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
        <h1>🔍 ตรวจสอบระบบแกลลอรี่ทั้งหมด</h1>
        
        <?php
        try {
            // ตรวจสอบตารางทั้งหมด
            $tables = ['gallery', 'gallery_images', 'gallery_albums'];
            $table_data = [];
            
            foreach ($tables as $table_name) {
                echo "<h2>📋 ตาราง: {$table_name}</h2>";
                
                $check_table = $conn->query("SHOW TABLES LIKE '{$table_name}'");
                
                if ($check_table->num_rows > 0) {
                    echo "<div class='result success'>✅ ตาราง {$table_name} มีอยู่ในระบบ</div>";
                    
                    // นับจำนวนข้อมูล
                    $count = $conn->query("SELECT COUNT(*) as total FROM {$table_name}")->fetch_assoc()['total'];
                    echo "<div class='result " . ($count > 0 ? 'success' : 'warning') . "'>";
                    echo "📊 จำนวนข้อมูลในตาราง {$table_name}: {$count} รายการ";
                    echo "</div>";
                    
                    $table_data[$table_name] = $count;
                    
                    // แสดงข้อมูลตัวอย่าง
                    if ($count > 0) {
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
                    $table_data[$table_name] = 0;
                }
                
                echo "<hr>";
            }
            
            // วิเคราะห์ระบบ
            echo "<h2>🔍 วิเคราะห์ระบบแกลลอรี่</h2>";
            
            if ($table_data['gallery'] > 0 && $table_data['gallery_images'] == 0) {
                echo "<div class='result warning'>⚠️ ปัญหา: ตาราง 'gallery' มีข้อมูล แต่ 'gallery_images' ไม่มีข้อมูล</div>";
                echo "<div class='result info'>💡 แนะนำ: ใช้ตาราง 'gallery' สำหรับทั้งหน้าบ้านและหลังบ้าน</div>";
            } elseif ($table_data['gallery_images'] > 0 && $table_data['gallery'] == 0) {
                echo "<div class='result warning'>⚠️ ปัญหา: ตาราง 'gallery_images' มีข้อมูล แต่ 'gallery' ไม่มีข้อมูล</div>";
                echo "<div class='result info'>💡 แนะนำ: ใช้ตาราง 'gallery_images' สำหรับทั้งหน้าบ้านและหลังบ้าน</div>";
            } elseif ($table_data['gallery'] > 0 && $table_data['gallery_images'] > 0) {
                echo "<div class='result warning'>⚠️ ปัญหา: มีข้อมูลในทั้งสองตาราง - อาจเกิดความสับสน</div>";
                echo "<div class='result info'>💡 แนะนำ: เลือกใช้ตารางเดียวและลบตารางที่ไม่ใช้</div>";
            } else {
                echo "<div class='result error'>❌ ปัญหา: ไม่มีข้อมูลในตารางใดเลย</div>";
                echo "<div class='result info'>💡 แนะนำ: เพิ่มข้อมูลตัวอย่างในตารางที่ต้องการใช้</div>";
            }
            
            // ตรวจสอบการใช้งาน
            echo "<h2>🔍 ตรวจสอบการใช้งาน</h2>";
            
            // ตรวจสอบหน้าบ้าน
            if (file_exists('gallery.php')) {
                $gallery_content = file_get_contents('gallery.php');
                if (strpos($gallery_content, 'gallery_images') !== false) {
                    echo "<div class='result info'>📄 หน้าบ้าน (gallery.php) ใช้ตาราง: gallery_images</div>";
                } elseif (strpos($gallery_content, 'FROM gallery') !== false) {
                    echo "<div class='result info'>📄 หน้าบ้าน (gallery.php) ใช้ตาราง: gallery</div>";
                } else {
                    echo "<div class='result warning'>⚠️ หน้าบ้าน (gallery.php) ไม่พบการใช้งานตาราง</div>";
                }
            }
            
            // ตรวจสอบหลังบ้าน
            if (file_exists('admin/gallery/index.php')) {
                $admin_content = file_get_contents('admin/gallery/index.php');
                if (strpos($admin_content, 'gallery_images') !== false) {
                    echo "<div class='result info'>⚙️ หลังบ้าน (admin/gallery/) ใช้ตาราง: gallery_images</div>";
                } elseif (strpos($admin_content, 'FROM gallery') !== false) {
                    echo "<div class='result info'>⚙️ หลังบ้าน (admin/gallery/) ใช้ตาราง: gallery</div>";
                } else {
                    echo "<div class='result warning'>⚠️ หลังบ้าน (admin/gallery/) ไม่พบการใช้งานตาราง</div>";
                }
            }
            
        } catch (Exception $e) {
            echo "<div class='result error'>❌ Exception: " . $e->getMessage() . "</div>";
        }
        ?>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="unify-gallery-system.php" class="btn btn-success">
                <i class="fas fa-unify"></i> รวมระบบแกลลอรี่
            </a>
            <a href="gallery.php" class="btn">
                <i class="fas fa-images"></i> หน้าบ้าน
            </a>
            <a href="admin/gallery/" class="btn">
                <i class="fas fa-cog"></i> หลังบ้าน
            </a>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>

