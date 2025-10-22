<?php
/**
 * เพิ่มข้อมูลตัวอย่างในตาราง gallery_images และ gallery_albums
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มข้อมูลตัวอย่างแกลลอรี่ Admin</title>
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
        .progress { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📸 เพิ่มข้อมูลตัวอย่างแกลลอรี่ Admin</h1>
        
        <?php
        if ($_POST['action'] ?? '' === 'seed_albums') {
            echo "<div class='result progress'>🚀 เริ่มเพิ่มข้อมูลตัวอย่างแกลลอรี่...</div>";
            
            // ข้อมูลอัลบั้ม
            $albums_data = [
                [
                    'name' => 'การถ่ายภาพ',
                    'description' => 'การถ่ายภาพแฟชั่นและไลฟ์สไตล์',
                    'cover_image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=300&fit=crop',
                    'sort_order' => 1,
                    'is_active' => 1
                ],
                [
                    'name' => 'แฟชั่น',
                    'description' => 'แฟชั่นและสไตล์การแต่งตัว',
                    'cover_image' => 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=400&h=300&fit=crop',
                    'sort_order' => 2,
                    'is_active' => 1
                ],
                [
                    'name' => 'ไลฟ์สไตล์',
                    'description' => 'ไลฟ์สไตล์และการใช้ชีวิต',
                    'cover_image' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=400&h=300&fit=crop',
                    'sort_order' => 3,
                    'is_active' => 1
                ],
                [
                    'name' => 'ความสวยความงาม',
                    'description' => 'ความสวยความงามและสกินแคร์',
                    'cover_image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=300&fit=crop',
                    'sort_order' => 4,
                    'is_active' => 1
                ],
                [
                    'name' => 'อีเวนต์',
                    'description' => 'งานอีเวนต์และกิจกรรมต่างๆ',
                    'cover_image' => 'https://images.unsplash.com/photo-1516589091380-5d8e87dfe2c0?w=400&h=300&fit=crop',
                    'sort_order' => 5,
                    'is_active' => 1
                ]
            ];
            
            $album_success = 0;
            $album_errors = 0;
            $album_ids = [];
            
            // ลบข้อมูลเดิม
            $conn->query("DELETE FROM gallery_images");
            $conn->query("DELETE FROM gallery_albums");
            echo "<div class='result progress'>🗑️ ลบข้อมูลเดิมแล้ว</div>";
            
            // เพิ่มอัลบั้ม
            foreach ($albums_data as $album) {
                try {
                    if (db_insert($conn, 'gallery_albums', $album)) {
                        $album_id = $conn->insert_id;
                        $album_ids[$album['name']] = $album_id;
                        echo "<div class='result progress'>✅ เพิ่มอัลบั้ม: {$album['name']} (ID: {$album_id})</div>";
                        $album_success++;
                    } else {
                        echo "<div class='result error'>❌ เกิดข้อผิดพลาดอัลบั้ม: {$album['name']} - " . $conn->error . "</div>";
                        $album_errors++;
                    }
                } catch (Exception $e) {
                    echo "<div class='result error'>❌ Exception อัลบั้ม: {$album['name']} - " . $e->getMessage() . "</div>";
                    $album_errors++;
                }
            }
            
            // ข้อมูลรูปภาพ
            $images_data = [
                // การถ่ายภาพ
                [
                    'title' => 'การถ่ายภาพแฟชั่น',
                    'description' => 'การถ่ายภาพแฟชั่นชุดสวยงามในสตูดิโอ',
                    'file_path' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=300&fit=crop',
                    'album_id' => $album_ids['การถ่ายภาพ'] ?? 1,
                    'tags' => 'แฟชั่น,สตูดิโอ,การถ่ายภาพ',
                    'sort_order' => 1,
                    'status' => 'active',
                    'view_count' => rand(10, 100)
                ],
                [
                    'title' => 'การถ่ายภาพธรรมชาติ',
                    'description' => 'การถ่ายภาพในบรรยากาศธรรมชาติที่สวยงาม',
                    'file_path' => 'https://images.unsplash.com/photo-1506905925346-04b8e0d7c0c0?w=400&h=300&fit=crop',
                    'album_id' => $album_ids['การถ่ายภาพ'] ?? 1,
                    'tags' => 'ธรรมชาติ,การถ่ายภาพ,แสงธรรมชาติ',
                    'sort_order' => 2,
                    'status' => 'active',
                    'view_count' => rand(10, 100)
                ],
                [
                    'title' => 'การถ่ายภาพสตรีท',
                    'description' => 'การถ่ายภาพสไตล์สตรีทแฟชั่นในเมือง',
                    'file_path' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=300&fit=crop',
                    'album_id' => $album_ids['การถ่ายภาพ'] ?? 1,
                    'tags' => 'สตรีท,แฟชั่น,เมือง',
                    'sort_order' => 3,
                    'status' => 'active',
                    'view_count' => rand(10, 100)
                ],
                [
                    'title' => 'การถ่ายภาพคู่รัก',
                    'description' => 'การถ่ายภาพคู่รักในบรรยากาศโรแมนติก',
                    'file_path' => 'https://images.unsplash.com/photo-1516589091380-5d8e87dfe2c0?w=400&h=300&fit=crop',
                    'album_id' => $album_ids['การถ่ายภาพ'] ?? 1,
                    'tags' => 'คู่รัก,โรแมนติก,การถ่ายภาพ',
                    'sort_order' => 4,
                    'status' => 'active',
                    'view_count' => rand(10, 100)
                ],
                
                // แฟชั่น
                [
                    'title' => 'แฟชั่นชุดยูนิฟอร์ม',
                    'description' => 'การแสดงแฟชั่นชุดยูนิฟอร์มสไตล์เกาหลี',
                    'file_path' => 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=400&h=300&fit=crop',
                    'album_id' => $album_ids['แฟชั่น'] ?? 2,
                    'tags' => 'ยูนิฟอร์ม,เกาหลี,แฟชั่น',
                    'sort_order' => 5,
                    'status' => 'active',
                    'view_count' => rand(10, 100)
                ],
                [
                    'title' => 'แฟชั่นชุดว่ายน้ำ',
                    'description' => 'การแสดงแฟชั่นชุดว่ายน้ำสไตล์บิกินี่',
                    'file_path' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=300&fit=crop',
                    'album_id' => $album_ids['แฟชั่น'] ?? 2,
                    'tags' => 'ว่ายน้ำ,บิกินี่,แฟชั่น',
                    'sort_order' => 6,
                    'status' => 'active',
                    'view_count' => rand(10, 100)
                ],
                [
                    'title' => 'แฟชั่นชุดทำงาน',
                    'description' => 'การแสดงแฟชั่นชุดทำงานสไตล์ออฟฟิศ',
                    'file_path' => 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=400&h=300&fit=crop',
                    'album_id' => $album_ids['แฟชั่น'] ?? 2,
                    'tags' => 'ชุดทำงาน,ออฟฟิศ,แฟชั่น',
                    'sort_order' => 7,
                    'status' => 'active',
                    'view_count' => rand(10, 100)
                ],
                [
                    'title' => 'แฟชั่นชุดออกงาน',
                    'description' => 'การแสดงแฟชั่นชุดออกงานสไตล์อีฟนิง',
                    'file_path' => 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=400&h=300&fit=crop',
                    'album_id' => $album_ids['แฟชั่น'] ?? 2,
                    'tags' => 'ชุดออกงาน,อีฟนิง,แฟชั่น',
                    'sort_order' => 8,
                    'status' => 'active',
                    'view_count' => rand(10, 100)
                ],
                
                // ไลฟ์สไตล์
                [
                    'title' => 'ไลฟ์สไตล์คาเฟ่',
                    'description' => 'การถ่ายภาพไลฟ์สไตล์ในบรรยากาศคาเฟ่',
                    'file_path' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=400&h=300&fit=crop',
                    'album_id' => $album_ids['ไลฟ์สไตล์'] ?? 3,
                    'tags' => 'คาเฟ่,ไลฟ์สไตล์,บรรยากาศ',
                    'sort_order' => 9,
                    'status' => 'active',
                    'view_count' => rand(10, 100)
                ],
                [
                    'title' => 'ไลฟ์สไตล์ฟิตเนส',
                    'description' => 'การถ่ายภาพไลฟ์สไตล์การออกกำลังกาย',
                    'file_path' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=300&fit=crop',
                    'album_id' => $album_ids['ไลฟ์สไตล์'] ?? 3,
                    'tags' => 'ฟิตเนส,ออกกำลังกาย,ไลฟ์สไตล์',
                    'sort_order' => 10,
                    'status' => 'active',
                    'view_count' => rand(10, 100)
                ],
                [
                    'title' => 'ไลฟ์สไตล์บ้าน',
                    'description' => 'การถ่ายภาพไลฟ์สไตล์ในบ้านพักอาศัย',
                    'file_path' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=300&fit=crop',
                    'album_id' => $album_ids['ไลฟ์สไตล์'] ?? 3,
                    'tags' => 'บ้าน,ไลฟ์สไตล์,พักอาศัย',
                    'sort_order' => 11,
                    'status' => 'active',
                    'view_count' => rand(10, 100)
                ],
                [
                    'title' => 'ไลฟ์สไตล์ท่องเที่ยว',
                    'description' => 'การถ่ายภาพไลฟ์สไตล์การท่องเที่ยว',
                    'file_path' => 'https://images.unsplash.com/photo-1506905925346-04b8e0d7c0c0?w=400&h=300&fit=crop',
                    'album_id' => $album_ids['ไลฟ์สไตล์'] ?? 3,
                    'tags' => 'ท่องเที่ยว,ไลฟ์สไตล์,การเดินทาง',
                    'sort_order' => 12,
                    'status' => 'active',
                    'view_count' => rand(10, 100)
                ],
                
                // ความสวยความงาม
                [
                    'title' => 'ความสวยความงามสกินแคร์',
                    'description' => 'การถ่ายภาพความสวยความงามด้านสกินแคร์',
                    'file_path' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=300&fit=crop',
                    'album_id' => $album_ids['ความสวยความงาม'] ?? 4,
                    'tags' => 'สกินแคร์,ความสวย,ความงาม',
                    'sort_order' => 13,
                    'status' => 'active',
                    'view_count' => rand(10, 100)
                ],
                [
                    'title' => 'ความสวยความงามเมคอัพ',
                    'description' => 'การถ่ายภาพความสวยความงามด้านเมคอัพ',
                    'file_path' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=300&fit=crop',
                    'album_id' => $album_ids['ความสวยความงาม'] ?? 4,
                    'tags' => 'เมคอัพ,ความสวย,ความงาม',
                    'sort_order' => 14,
                    'status' => 'active',
                    'view_count' => rand(10, 100)
                ],
                [
                    'title' => 'ความสวยความงามผม',
                    'description' => 'การถ่ายภาพความสวยความงามด้านผม',
                    'file_path' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=300&fit=crop',
                    'album_id' => $album_ids['ความสวยความงาม'] ?? 4,
                    'tags' => 'ผม,ความสวย,ความงาม',
                    'sort_order' => 15,
                    'status' => 'active',
                    'view_count' => rand(10, 100)
                ],
                
                // อีเวนต์
                [
                    'title' => 'อีเวนต์แฟชั่นโชว์',
                    'description' => 'การถ่ายภาพในงานแฟชั่นโชว์',
                    'file_path' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=300&fit=crop',
                    'album_id' => $album_ids['อีเวนต์'] ?? 5,
                    'tags' => 'แฟชั่นโชว์,อีเวนต์,แฟชั่น',
                    'sort_order' => 16,
                    'status' => 'active',
                    'view_count' => rand(10, 100)
                ],
                [
                    'title' => 'อีเวนต์เปิดตัวผลิตภัณฑ์',
                    'description' => 'การถ่ายภาพในงานเปิดตัวผลิตภัณฑ์',
                    'file_path' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=300&fit=crop',
                    'album_id' => $album_ids['อีเวนต์'] ?? 5,
                    'tags' => 'เปิดตัว,ผลิตภัณฑ์,อีเวนต์',
                    'sort_order' => 17,
                    'status' => 'active',
                    'view_count' => rand(10, 100)
                ],
                [
                    'title' => 'อีเวนต์ปาร์ตี้',
                    'description' => 'การถ่ายภาพในงานปาร์ตี้',
                    'file_path' => 'https://images.unsplash.com/photo-1516589091380-5d8e87dfe2c0?w=400&h=300&fit=crop',
                    'album_id' => $album_ids['อีเวนต์'] ?? 5,
                    'tags' => 'ปาร์ตี้,อีเวนต์,งานเลี้ยง',
                    'sort_order' => 18,
                    'status' => 'active',
                    'view_count' => rand(10, 100)
                ],
                [
                    'title' => 'อีเวนต์คอนเสิร์ต',
                    'description' => 'การถ่ายภาพในงานคอนเสิร์ต',
                    'file_path' => 'https://images.unsplash.com/photo-1516589091380-5d8e87dfe2c0?w=400&h=300&fit=crop',
                    'album_id' => $album_ids['อีเวนต์'] ?? 5,
                    'tags' => 'คอนเสิร์ต,อีเวนต์,ดนตรี',
                    'sort_order' => 19,
                    'status' => 'active',
                    'view_count' => rand(10, 100)
                ],
                [
                    'title' => 'อีเวนต์เวิร์กช็อป',
                    'description' => 'การถ่ายภาพในงานเวิร์กช็อป',
                    'file_path' => 'https://images.unsplash.com/photo-1506905925346-04b8e0d7c0c0?w=400&h=300&fit=crop',
                    'album_id' => $album_ids['อีเวนต์'] ?? 5,
                    'tags' => 'เวิร์กช็อป,อีเวนต์,การเรียนรู้',
                    'sort_order' => 20,
                    'status' => 'active',
                    'view_count' => rand(10, 100)
                ]
            ];
            
            $image_success = 0;
            $image_errors = 0;
            
            // เพิ่มรูปภาพ
            foreach ($images_data as $image) {
                try {
                    if (db_insert($conn, 'gallery_images', $image)) {
                        echo "<div class='result progress'>✅ เพิ่มรูปภาพ: {$image['title']}</div>";
                        $image_success++;
                    } else {
                        echo "<div class='result error'>❌ เกิดข้อผิดพลาดรูปภาพ: {$image['title']} - " . $conn->error . "</div>";
                        $image_errors++;
                    }
                } catch (Exception $e) {
                    echo "<div class='result error'>❌ Exception รูปภาพ: {$image['title']} - " . $e->getMessage() . "</div>";
                    $image_errors++;
                }
            }
            
            echo "<hr>";
            echo "<div class='result " . (($album_errors + $image_errors) == 0 ? 'success' : 'error') . "'>";
            echo "<h3>📊 ผลลัพธ์:</h3>";
            echo "<p>✅ อัลบั้มสำเร็จ: {$album_success} รายการ</p>";
            echo "<p>❌ อัลบั้มล้มเหลว: {$album_errors} รายการ</p>";
            echo "<p>✅ รูปภาพสำเร็จ: {$image_success} รายการ</p>";
            echo "<p>❌ รูปภาพล้มเหลว: {$image_errors} รายการ</p>";
            echo "</div>";
            
            if ($image_success > 0) {
                echo "<div class='result success'>";
                echo "<h3>🎉 เพิ่มข้อมูลแกลลอรี่สำเร็จ!</h3>";
                echo "<p>ตอนนี้หน้าบ้านและหลังบ้านจะใช้ข้อมูลเดียวกันแล้ว</p>";
                echo "<p><a href='gallery.php' style='color: #155724; font-weight: bold;'>📸 ดูแกลลอรี่หน้าบ้าน</a></p>";
                echo "<p><a href='admin/gallery/' style='color: #155724; font-weight: bold;'>⚙️ จัดการแกลลอรี่หลังบ้าน</a></p>";
                echo "</div>";
            }
        }
        ?>
        
        <form method="POST">
            <button type="submit" name="action" value="seed_albums" class="btn btn-success">
                <i class="fas fa-plus"></i> เพิ่มข้อมูลตัวอย่างแกลลอรี่ Admin
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="gallery.php" class="btn">
                <i class="fas fa-images"></i> ดูแกลลอรี่หน้าบ้าน
            </a>
            <a href="admin/gallery/" class="btn">
                <i class="fas fa-cog"></i> จัดการแกลลอรี่หลังบ้าน
            </a>
        </div>
        
        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <h3>📋 ข้อมูลที่จะเพิ่ม:</h3>
            <ul>
                <li><strong>อัลบั้ม:</strong> 5 อัลบั้ม (การถ่ายภาพ, แฟชั่น, ไลฟ์สไตล์, ความสวยความงาม, อีเวนต์)</li>
                <li><strong>รูปภาพ:</strong> 20 รูปภาพ แบ่งตามอัลบั้ม</li>
                <li><strong>ระบบ:</strong> ใช้ตาราง gallery_images + gallery_albums เหมือนหลังบ้าน</li>
            </ul>
            <p><strong>ผลลัพธ์:</strong> หน้าบ้านและหลังบ้านจะใช้ข้อมูลเดียวกัน</p>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
