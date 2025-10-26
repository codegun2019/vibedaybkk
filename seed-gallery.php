<?php
/**
 * เพิ่มข้อมูลตัวอย่างแกลลอรี่ 20 รูป
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มข้อมูลตัวอย่างแกลลอรี่</title>
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
        .gallery-preview { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; margin: 20px 0; }
        .gallery-item { border: 2px solid #e0e0e0; border-radius: 8px; padding: 10px; text-align: center; }
        .gallery-item img { width: 100%; height: 100px; object-fit: cover; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📸 เพิ่มข้อมูลตัวอย่างแกลลอรี่</h1>
        
        <?php
        if ($_POST['action'] ?? '' === 'seed_gallery') {
            // ข้อมูลตัวอย่างแกลลอรี่
            $gallery_data = [
                // หมวดหมู่: การถ่ายภาพ
                [
                    'title' => 'การถ่ายภาพแฟชั่น',
                    'description' => 'การถ่ายภาพแฟชั่นชุดสวยงามในสตูดิโอ',
                    'category' => 'การถ่ายภาพ',
                    'tags' => 'แฟชั่น,สตูดิโอ,การถ่ายภาพ',
                    'image' => 'gallery/fashion-studio-1.jpg',
                    'sort_order' => 1
                ],
                [
                    'title' => 'การถ่ายภาพธรรมชาติ',
                    'description' => 'การถ่ายภาพในบรรยากาศธรรมชาติที่สวยงาม',
                    'category' => 'การถ่ายภาพ',
                    'tags' => 'ธรรมชาติ,การถ่ายภาพ,แสงธรรมชาติ',
                    'image' => 'gallery/nature-photo-1.jpg',
                    'sort_order' => 2
                ],
                [
                    'title' => 'การถ่ายภาพสตรีท',
                    'description' => 'การถ่ายภาพสไตล์สตรีทแฟชั่นในเมือง',
                    'category' => 'การถ่ายภาพ',
                    'tags' => 'สตรีท,แฟชั่น,เมือง',
                    'image' => 'gallery/street-fashion-1.jpg',
                    'sort_order' => 3
                ],
                [
                    'title' => 'การถ่ายภาพคู่รัก',
                    'description' => 'การถ่ายภาพคู่รักในบรรยากาศโรแมนติก',
                    'category' => 'การถ่ายภาพ',
                    'tags' => 'คู่รัก,โรแมนติก,การถ่ายภาพ',
                    'image' => 'gallery/couple-photo-1.jpg',
                    'sort_order' => 4
                ],
                
                // หมวดหมู่: แฟชั่น
                [
                    'title' => 'แฟชั่นชุดยูนิฟอร์ม',
                    'description' => 'การแสดงแฟชั่นชุดยูนิฟอร์มสไตล์เกาหลี',
                    'category' => 'แฟชั่น',
                    'tags' => 'ยูนิฟอร์ม,เกาหลี,แฟชั่น',
                    'image' => 'gallery/uniform-fashion-1.jpg',
                    'sort_order' => 5
                ],
                [
                    'title' => 'แฟชั่นชุดว่ายน้ำ',
                    'description' => 'การแสดงแฟชั่นชุดว่ายน้ำสไตล์บิกินี่',
                    'category' => 'แฟชั่น',
                    'tags' => 'ว่ายน้ำ,บิกินี่,แฟชั่น',
                    'image' => 'gallery/swimwear-fashion-1.jpg',
                    'sort_order' => 6
                ],
                [
                    'title' => 'แฟชั่นชุดทำงาน',
                    'description' => 'การแสดงแฟชั่นชุดทำงานสไตล์ออฟฟิศ',
                    'category' => 'แฟชั่น',
                    'tags' => 'ชุดทำงาน,ออฟฟิศ,แฟชั่น',
                    'image' => 'gallery/office-fashion-1.jpg',
                    'sort_order' => 7
                ],
                [
                    'title' => 'แฟชั่นชุดออกงาน',
                    'description' => 'การแสดงแฟชั่นชุดออกงานสไตล์อีฟนิง',
                    'category' => 'แฟชั่น',
                    'tags' => 'ชุดออกงาน,อีฟนิง,แฟชั่น',
                    'image' => 'gallery/evening-fashion-1.jpg',
                    'sort_order' => 8
                ],
                
                // หมวดหมู่: ไลฟ์สไตล์
                [
                    'title' => 'ไลฟ์สไตล์คาเฟ่',
                    'description' => 'การถ่ายภาพไลฟ์สไตล์ในบรรยากาศคาเฟ่',
                    'category' => 'ไลฟ์สไตล์',
                    'tags' => 'คาเฟ่,ไลฟ์สไตล์,บรรยากาศ',
                    'image' => 'gallery/cafe-lifestyle-1.jpg',
                    'sort_order' => 9
                ],
                [
                    'title' => 'ไลฟ์สไตล์ฟิตเนส',
                    'description' => 'การถ่ายภาพไลฟ์สไตล์การออกกำลังกาย',
                    'category' => 'ไลฟ์สไตล์',
                    'tags' => 'ฟิตเนส,ออกกำลังกาย,ไลฟ์สไตล์',
                    'image' => 'gallery/fitness-lifestyle-1.jpg',
                    'sort_order' => 10
                ],
                [
                    'title' => 'ไลฟ์สไตล์บ้าน',
                    'description' => 'การถ่ายภาพไลฟ์สไตล์ในบ้านพักอาศัย',
                    'category' => 'ไลฟ์สไตล์',
                    'tags' => 'บ้าน,ไลฟ์สไตล์,พักอาศัย',
                    'image' => 'gallery/home-lifestyle-1.jpg',
                    'sort_order' => 11
                ],
                [
                    'title' => 'ไลฟ์สไตล์ท่องเที่ยว',
                    'description' => 'การถ่ายภาพไลฟ์สไตล์การท่องเที่ยว',
                    'category' => 'ไลฟ์สไตล์',
                    'tags' => 'ท่องเที่ยว,ไลฟ์สไตล์,การเดินทาง',
                    'image' => 'gallery/travel-lifestyle-1.jpg',
                    'sort_order' => 12
                ],
                
                // หมวดหมู่: ความสวยความงาม
                [
                    'title' => 'ความสวยความงามสกินแคร์',
                    'description' => 'การถ่ายภาพความสวยความงามด้านสกินแคร์',
                    'category' => 'ความสวยความงาม',
                    'tags' => 'สกินแคร์,ความสวย,ความงาม',
                    'image' => 'gallery/skincare-beauty-1.jpg',
                    'sort_order' => 13
                ],
                [
                    'title' => 'ความสวยความงามเมคอัพ',
                    'description' => 'การถ่ายภาพความสวยความงามด้านเมคอัพ',
                    'category' => 'ความสวยความงาม',
                    'tags' => 'เมคอัพ,ความสวย,ความงาม',
                    'image' => 'gallery/makeup-beauty-1.jpg',
                    'sort_order' => 14
                ],
                [
                    'title' => 'ความสวยความงามผม',
                    'description' => 'การถ่ายภาพความสวยความงามด้านผม',
                    'category' => 'ความสวยความงาม',
                    'tags' => 'ผม,ความสวย,ความงาม',
                    'image' => 'gallery/hair-beauty-1.jpg',
                    'sort_order' => 15
                ],
                
                // หมวดหมู่: อีเวนต์
                [
                    'title' => 'อีเวนต์แฟชั่นโชว์',
                    'description' => 'การถ่ายภาพในงานแฟชั่นโชว์',
                    'category' => 'อีเวนต์',
                    'tags' => 'แฟชั่นโชว์,อีเวนต์,แฟชั่น',
                    'image' => 'gallery/fashion-show-event-1.jpg',
                    'sort_order' => 16
                ],
                [
                    'title' => 'อีเวนต์เปิดตัวผลิตภัณฑ์',
                    'description' => 'การถ่ายภาพในงานเปิดตัวผลิตภัณฑ์',
                    'category' => 'อีเวนต์',
                    'tags' => 'เปิดตัว,ผลิตภัณฑ์,อีเวนต์',
                    'image' => 'gallery/product-launch-event-1.jpg',
                    'sort_order' => 17
                ],
                [
                    'title' => 'อีเวนต์ปาร์ตี้',
                    'description' => 'การถ่ายภาพในงานปาร์ตี้',
                    'category' => 'อีเวนต์',
                    'tags' => 'ปาร์ตี้,อีเวนต์,งานเลี้ยง',
                    'image' => 'gallery/party-event-1.jpg',
                    'sort_order' => 18
                ],
                [
                    'title' => 'อีเวนต์คอนเสิร์ต',
                    'description' => 'การถ่ายภาพในงานคอนเสิร์ต',
                    'category' => 'อีเวนต์',
                    'tags' => 'คอนเสิร์ต,อีเวนต์,ดนตรี',
                    'image' => 'gallery/concert-event-1.jpg',
                    'sort_order' => 19
                ],
                [
                    'title' => 'อีเวนต์เวิร์กช็อป',
                    'description' => 'การถ่ายภาพในงานเวิร์กช็อป',
                    'category' => 'อีเวนต์',
                    'tags' => 'เวิร์กช็อป,อีเวนต์,การเรียนรู้',
                    'image' => 'gallery/workshop-event-1.jpg',
                    'sort_order' => 20
                ]
            ];
            
            echo "<div class='result progress'>🚀 เริ่มเพิ่มข้อมูลตัวอย่างแกลลอรี่ 20 รูป...</div>";
            
            // ลบข้อมูลเดิม
            $conn->query("DELETE FROM gallery");
            echo "<div class='result progress'>🗑️ ลบข้อมูลเดิมแล้ว</div>";
            
            $success_count = 0;
            $error_count = 0;
            
            // สร้างโฟลเดอร์ uploads/gallery ถ้ายังไม่มี
            $gallery_dir = 'uploads/gallery';
            if (!file_exists($gallery_dir)) {
                mkdir($gallery_dir, 0755, true);
                echo "<div class='result progress'>📁 สร้างโฟลเดอร์ {$gallery_dir} แล้ว</div>";
            }
            
            foreach ($gallery_data as $index => $data) {
                try {
                    // เพิ่มข้อมูลลงฐานข้อมูล
                    $insert_data = [
                        'title' => $data['title'],
                        'description' => $data['description'],
                        'category' => $data['category'],
                        'tags' => $data['tags'],
                        'image' => $data['image'],
                        'sort_order' => $data['sort_order'],
                        'is_active' => 1,
                        'view_count' => rand(10, 100)
                    ];
                    
                    if (db_insert($conn, 'gallery', $insert_data)) {
                        echo "<div class='result progress'>✅ [{$index}] เพิ่ม: {$data['title']} (หมวดหมู่: {$data['category']})</div>";
                        $success_count++;
                    } else {
                        echo "<div class='result error'>❌ [{$index}] เกิดข้อผิดพลาด: {$data['title']}</div>";
                        $error_count++;
                    }
                } catch (Exception $e) {
                    echo "<div class='result error'>❌ [{$index}] Exception: {$data['title']} - " . $e->getMessage() . "</div>";
                    $error_count++;
                }
            }
            
            echo "<hr>";
            echo "<div class='result " . ($error_count == 0 ? 'success' : 'error') . "'>";
            echo "<h3>📊 ผลลัพธ์:</h3>";
            echo "<p>✅ สำเร็จ: {$success_count} รายการ</p>";
            echo "<p>❌ ล้มเหลว: {$error_count} รายการ</p>";
            echo "</div>";
            
            if ($success_count > 0) {
                echo "<div class='result success'>";
                echo "<h3>🎉 เพิ่มข้อมูลแกลลอรี่สำเร็จ!</h3>";
                echo "<p>ตอนนี้คุณสามารถดูแกลลอรี่ได้ที่:</p>";
                echo "<p><a href='gallery.php' style='color: #155724; font-weight: bold;'>📸 หน้าแกลลอรี่</a></p>";
                echo "</div>";
            }
        }
        ?>
        
        <form method="POST">
            <button type="submit" name="action" value="seed_gallery" class="btn btn-success">
                <i class="fas fa-plus"></i> เพิ่มข้อมูลตัวอย่างแกลลอรี่ 20 รูป
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
            <h3>📋 ข้อมูลที่จะเพิ่ม:</h3>
            <ul>
                <li><strong>การถ่ายภาพ:</strong> 4 รูป (แฟชั่น, ธรรมชาติ, สตรีท, คู่รัก)</li>
                <li><strong>แฟชั่น:</strong> 4 รูป (ยูนิฟอร์ม, ว่ายน้ำ, ทำงาน, ออกงาน)</li>
                <li><strong>ไลฟ์สไตล์:</strong> 4 รูป (คาเฟ่, ฟิตเนส, บ้าน, ท่องเที่ยว)</li>
                <li><strong>ความสวยความงาม:</strong> 3 รูป (สกินแคร์, เมคอัพ, ผม)</li>
                <li><strong>อีเวนต์:</strong> 5 รูป (แฟชั่นโชว์, เปิดตัวผลิตภัณฑ์, ปาร์ตี้, คอนเสิร์ต, เวิร์กช็อป)</li>
            </ul>
            <p><strong>รวม:</strong> 20 รูป พร้อมหมวดหมู่และแท็ก</p>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>



