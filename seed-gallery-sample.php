<?php
/**
 * เพิ่มข้อมูลตัวอย่างแกลลอรี่ 20-30 รูป
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
        .gallery-item { border: 2px solid #e0e0e0; border-radius: 8px; padding: 10px; text-align: center; background: #f8f9fa; }
        .gallery-item img { width: 100%; height: 100px; object-fit: cover; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📸 เพิ่มข้อมูลตัวอย่างแกลลอรี่ 25 รูป</h1>
        
        <?php
        if ($_POST['action'] ?? '' === 'seed_gallery') {
            echo "<div class='result progress'>🚀 เริ่มเพิ่มข้อมูลตัวอย่างแกลลอรี่ 25 รูป...</div>";
            
            // ข้อมูลอัลบั้ม
            $albums_data = [
                [
                    'name' => 'การถ่ายภาพแฟชั่น',
                    'description' => 'การถ่ายภาพแฟชั่นและสไตล์การแต่งตัว',
                    'cover_image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=300&fit=crop',
                    'sort_order' => 1,
                    'is_active' => 1
                ],
                [
                    'name' => 'ไลฟ์สไตล์',
                    'description' => 'ไลฟ์สไตล์และการใช้ชีวิตประจำวัน',
                    'cover_image' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=400&h=300&fit=crop',
                    'sort_order' => 2,
                    'is_active' => 1
                ],
                [
                    'name' => 'ความสวยความงาม',
                    'description' => 'ความสวยความงามและสกินแคร์',
                    'cover_image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=300&fit=crop',
                    'sort_order' => 3,
                    'is_active' => 1
                ],
                [
                    'name' => 'อีเวนต์และงาน',
                    'description' => 'งานอีเวนต์และกิจกรรมต่างๆ',
                    'cover_image' => 'https://images.unsplash.com/photo-1516589091380-5d8e87dfe2c0?w=400&h=300&fit=crop',
                    'sort_order' => 4,
                    'is_active' => 1
                ],
                [
                    'name' => 'การถ่ายภาพธรรมชาติ',
                    'description' => 'การถ่ายภาพในบรรยากาศธรรมชาติ',
                    'cover_image' => 'https://images.unsplash.com/photo-1506905925346-04b8e0d7c0c0?w=400&h=300&fit=crop',
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
            
            // ข้อมูลรูปภาพ 25 รูป
            $images_data = [
                // การถ่ายภาพแฟชั่น (5 รูป)
                [
                    'title' => 'แฟชั่นชุดยูนิฟอร์ม',
                    'description' => 'การถ่ายภาพแฟชั่นชุดยูนิฟอร์มสไตล์เกาหลีในสตูดิโอ',
                    'file_path' => 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['การถ่ายภาพแฟชั่น'] ?? 1,
                    'tags' => 'แฟชั่น,ยูนิฟอร์ม,เกาหลี,สตูดิโอ',
                    'sort_order' => 1,
                    'status' => 'active',
                    'view_count' => rand(50, 200)
                ],
                [
                    'title' => 'แฟชั่นชุดว่ายน้ำ',
                    'description' => 'การถ่ายภาพแฟชั่นชุดว่ายน้ำสไตล์บิกินี่ที่ชายหาด',
                    'file_path' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['การถ่ายภาพแฟชั่น'] ?? 1,
                    'tags' => 'แฟชั่น,ว่ายน้ำ,บิกินี่,ชายหาด',
                    'sort_order' => 2,
                    'status' => 'active',
                    'view_count' => rand(50, 200)
                ],
                [
                    'title' => 'แฟชั่นชุดทำงาน',
                    'description' => 'การถ่ายภาพแฟชั่นชุดทำงานสไตล์ออฟฟิศในเมือง',
                    'file_path' => 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['การถ่ายภาพแฟชั่น'] ?? 1,
                    'tags' => 'แฟชั่น,ชุดทำงาน,ออฟฟิศ,เมือง',
                    'sort_order' => 3,
                    'status' => 'active',
                    'view_count' => rand(50, 200)
                ],
                [
                    'title' => 'แฟชั่นชุดออกงาน',
                    'description' => 'การถ่ายภาพแฟชั่นชุดออกงานสไตล์อีฟนิงในโรงแรม',
                    'file_path' => 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['การถ่ายภาพแฟชั่น'] ?? 1,
                    'tags' => 'แฟชั่น,ชุดออกงาน,อีฟนิง,โรงแรม',
                    'sort_order' => 4,
                    'status' => 'active',
                    'view_count' => rand(50, 200)
                ],
                [
                    'title' => 'แฟชั่นสตรีท',
                    'description' => 'การถ่ายภาพแฟชั่นสไตล์สตรีทในย่านเมืองเก่า',
                    'file_path' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['การถ่ายภาพแฟชั่น'] ?? 1,
                    'tags' => 'แฟชั่น,สตรีท,เมืองเก่า,การถ่ายภาพ',
                    'sort_order' => 5,
                    'status' => 'active',
                    'view_count' => rand(50, 200)
                ],
                
                // ไลฟ์สไตล์ (5 รูป)
                [
                    'title' => 'ไลฟ์สไตล์คาเฟ่',
                    'description' => 'การถ่ายภาพไลฟ์สไตล์ในบรรยากาศคาเฟ่สไตล์ญี่ปุ่น',
                    'file_path' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['ไลฟ์สไตล์'] ?? 2,
                    'tags' => 'ไลฟ์สไตล์,คาเฟ่,ญี่ปุ่น,บรรยากาศ',
                    'sort_order' => 6,
                    'status' => 'active',
                    'view_count' => rand(30, 150)
                ],
                [
                    'title' => 'ไลฟ์สไตล์ฟิตเนส',
                    'description' => 'การถ่ายภาพไลฟ์สไตล์การออกกำลังกายในยิม',
                    'file_path' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['ไลฟ์สไตล์'] ?? 2,
                    'tags' => 'ไลฟ์สไตล์,ฟิตเนส,ออกกำลังกาย,ยิม',
                    'sort_order' => 7,
                    'status' => 'active',
                    'view_count' => rand(30, 150)
                ],
                [
                    'title' => 'ไลฟ์สไตล์บ้าน',
                    'description' => 'การถ่ายภาพไลฟ์สไตล์ในบ้านพักอาศัยสไตล์มินิมอล',
                    'file_path' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['ไลฟ์สไตล์'] ?? 2,
                    'tags' => 'ไลฟ์สไตล์,บ้าน,มินิมอล,พักอาศัย',
                    'sort_order' => 8,
                    'status' => 'active',
                    'view_count' => rand(30, 150)
                ],
                [
                    'title' => 'ไลฟ์สไตล์ท่องเที่ยว',
                    'description' => 'การถ่ายภาพไลฟ์สไตล์การท่องเที่ยวในต่างประเทศ',
                    'file_path' => 'https://images.unsplash.com/photo-1506905925346-04b8e0d7c0c0?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['ไลฟ์สไตล์'] ?? 2,
                    'tags' => 'ไลฟ์สไตล์,ท่องเที่ยว,ต่างประเทศ,การเดินทาง',
                    'sort_order' => 9,
                    'status' => 'active',
                    'view_count' => rand(30, 150)
                ],
                [
                    'title' => 'ไลฟ์สไตล์การอ่าน',
                    'description' => 'การถ่ายภาพไลฟ์สไตล์การอ่านหนังสือในร้านหนังสือ',
                    'file_path' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['ไลฟ์สไตล์'] ?? 2,
                    'tags' => 'ไลฟ์สไตล์,การอ่าน,หนังสือ,ร้านหนังสือ',
                    'sort_order' => 10,
                    'status' => 'active',
                    'view_count' => rand(30, 150)
                ],
                
                // ความสวยความงาม (5 รูป)
                [
                    'title' => 'ความสวยความงามสกินแคร์',
                    'description' => 'การถ่ายภาพความสวยความงามด้านสกินแคร์และผลิตภัณฑ์',
                    'file_path' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['ความสวยความงาม'] ?? 3,
                    'tags' => 'ความสวย,ความงาม,สกินแคร์,ผลิตภัณฑ์',
                    'sort_order' => 11,
                    'status' => 'active',
                    'view_count' => rand(40, 180)
                ],
                [
                    'title' => 'ความสวยความงามเมคอัพ',
                    'description' => 'การถ่ายภาพความสวยความงามด้านเมคอัพและเครื่องสำอาง',
                    'file_path' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['ความสวยความงาม'] ?? 3,
                    'tags' => 'ความสวย,ความงาม,เมคอัพ,เครื่องสำอาง',
                    'sort_order' => 12,
                    'status' => 'active',
                    'view_count' => rand(40, 180)
                ],
                [
                    'title' => 'ความสวยความงามผม',
                    'description' => 'การถ่ายภาพความสวยความงามด้านผมและการทำผม',
                    'file_path' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['ความสวยความงาม'] ?? 3,
                    'tags' => 'ความสวย,ความงาม,ผม,การทำผม',
                    'sort_order' => 13,
                    'status' => 'active',
                    'view_count' => rand(40, 180)
                ],
                [
                    'title' => 'ความสวยความงามเล็บ',
                    'description' => 'การถ่ายภาพความสวยความงามด้านเล็บและการทำเล็บ',
                    'file_path' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['ความสวยความงาม'] ?? 3,
                    'tags' => 'ความสวย,ความงาม,เล็บ,การทำเล็บ',
                    'sort_order' => 14,
                    'status' => 'active',
                    'view_count' => rand(40, 180)
                ],
                [
                    'title' => 'ความสวยความงามสปา',
                    'description' => 'การถ่ายภาพความสวยความงามในสปาและการนวด',
                    'file_path' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['ความสวยความงาม'] ?? 3,
                    'tags' => 'ความสวย,ความงาม,สปา,การนวด',
                    'sort_order' => 15,
                    'status' => 'active',
                    'view_count' => rand(40, 180)
                ],
                
                // อีเวนต์และงาน (5 รูป)
                [
                    'title' => 'อีเวนต์แฟชั่นโชว์',
                    'description' => 'การถ่ายภาพในงานแฟชั่นโชว์และรันเวย์',
                    'file_path' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['อีเวนต์และงาน'] ?? 4,
                    'tags' => 'อีเวนต์,แฟชั่นโชว์,รันเวย์,แฟชั่น',
                    'sort_order' => 16,
                    'status' => 'active',
                    'view_count' => rand(60, 250)
                ],
                [
                    'title' => 'อีเวนต์เปิดตัวผลิตภัณฑ์',
                    'description' => 'การถ่ายภาพในงานเปิดตัวผลิตภัณฑ์และแบรนด์',
                    'file_path' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['อีเวนต์และงาน'] ?? 4,
                    'tags' => 'อีเวนต์,เปิดตัว,ผลิตภัณฑ์,แบรนด์',
                    'sort_order' => 17,
                    'status' => 'active',
                    'view_count' => rand(60, 250)
                ],
                [
                    'title' => 'อีเวนต์ปาร์ตี้',
                    'description' => 'การถ่ายภาพในงานปาร์ตี้และงานเลี้ยง',
                    'file_path' => 'https://images.unsplash.com/photo-1516589091380-5d8e87dfe2c0?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['อีเวนต์และงาน'] ?? 4,
                    'tags' => 'อีเวนต์,ปาร์ตี้,งานเลี้ยง,สังสรรค์',
                    'sort_order' => 18,
                    'status' => 'active',
                    'view_count' => rand(60, 250)
                ],
                [
                    'title' => 'อีเวนต์คอนเสิร์ต',
                    'description' => 'การถ่ายภาพในงานคอนเสิร์ตและดนตรี',
                    'file_path' => 'https://images.unsplash.com/photo-1516589091380-5d8e87dfe2c0?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['อีเวนต์และงาน'] ?? 4,
                    'tags' => 'อีเวนต์,คอนเสิร์ต,ดนตรี,การแสดง',
                    'sort_order' => 19,
                    'status' => 'active',
                    'view_count' => rand(60, 250)
                ],
                [
                    'title' => 'อีเวนต์เวิร์กช็อป',
                    'description' => 'การถ่ายภาพในงานเวิร์กช็อปและการเรียนรู้',
                    'file_path' => 'https://images.unsplash.com/photo-1506905925346-04b8e0d7c0c0?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['อีเวนต์และงาน'] ?? 4,
                    'tags' => 'อีเวนต์,เวิร์กช็อป,การเรียนรู้,การศึกษา',
                    'sort_order' => 20,
                    'status' => 'active',
                    'view_count' => rand(60, 250)
                ],
                
                // การถ่ายภาพธรรมชาติ (5 รูป)
                [
                    'title' => 'การถ่ายภาพธรรมชาติป่า',
                    'description' => 'การถ่ายภาพในบรรยากาศป่าและธรรมชาติ',
                    'file_path' => 'https://images.unsplash.com/photo-1506905925346-04b8e0d7c0c0?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['การถ่ายภาพธรรมชาติ'] ?? 5,
                    'tags' => 'ธรรมชาติ,ป่า,การถ่ายภาพ,บรรยากาศ',
                    'sort_order' => 21,
                    'status' => 'active',
                    'view_count' => rand(70, 300)
                ],
                [
                    'title' => 'การถ่ายภาพธรรมชาติทะเล',
                    'description' => 'การถ่ายภาพในบรรยากาศทะเลและชายหาด',
                    'file_path' => 'https://images.unsplash.com/photo-1506905925346-04b8e0d7c0c0?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['การถ่ายภาพธรรมชาติ'] ?? 5,
                    'tags' => 'ธรรมชาติ,ทะเล,ชายหาด,การถ่ายภาพ',
                    'sort_order' => 22,
                    'status' => 'active',
                    'view_count' => rand(70, 300)
                ],
                [
                    'title' => 'การถ่ายภาพธรรมชาติภูเขา',
                    'description' => 'การถ่ายภาพในบรรยากาศภูเขาและทิวทัศน์',
                    'file_path' => 'https://images.unsplash.com/photo-1506905925346-04b8e0d7c0c0?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['การถ่ายภาพธรรมชาติ'] ?? 5,
                    'tags' => 'ธรรมชาติ,ภูเขา,ทิวทัศน์,การถ่ายภาพ',
                    'sort_order' => 23,
                    'status' => 'active',
                    'view_count' => rand(70, 300)
                ],
                [
                    'title' => 'การถ่ายภาพธรรมชาติดอกไม้',
                    'description' => 'การถ่ายภาพดอกไม้และพืชพรรณในธรรมชาติ',
                    'file_path' => 'https://images.unsplash.com/photo-1506905925346-04b8e0d7c0c0?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['การถ่ายภาพธรรมชาติ'] ?? 5,
                    'tags' => 'ธรรมชาติ,ดอกไม้,พืชพรรณ,การถ่ายภาพ',
                    'sort_order' => 24,
                    'status' => 'active',
                    'view_count' => rand(70, 300)
                ],
                [
                    'title' => 'การถ่ายภาพธรรมชาติสัตว์',
                    'description' => 'การถ่ายภาพสัตว์ป่าและสัตว์เลี้ยงในธรรมชาติ',
                    'file_path' => 'https://images.unsplash.com/photo-1506905925346-04b8e0d7c0c0?w=600&h=400&fit=crop',
                    'album_id' => $album_ids['การถ่ายภาพธรรมชาติ'] ?? 5,
                    'tags' => 'ธรรมชาติ,สัตว์,สัตว์ป่า,การถ่ายภาพ',
                    'sort_order' => 25,
                    'status' => 'active',
                    'view_count' => rand(70, 300)
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
                echo "<p>ตอนนี้มีรูปภาพ {$image_success} รูป ใน {$album_success} อัลบั้ม</p>";
                echo "<p><a href='gallery.php' style='color: #155724; font-weight: bold;'>📸 ดูแกลลอรี่หน้าบ้าน</a></p>";
                echo "<p><a href='admin/gallery/' style='color: #155724; font-weight: bold;'>⚙️ จัดการแกลลอรี่หลังบ้าน</a></p>";
                echo "</div>";
            }
        }
        ?>
        
        <form method="POST">
            <button type="submit" name="action" value="seed_gallery" class="btn btn-success">
                <i class="fas fa-plus"></i> เพิ่มข้อมูลตัวอย่างแกลลอรี่ 25 รูป
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="gallery.php" class="btn">
                <i class="fas fa-images"></i> ดูแกลลอรี่
            </a>
            <a href="admin/gallery/" class="btn">
                <i class="fas fa-cog"></i> จัดการแกลลอรี่
            </a>
        </div>
        
        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <h3>📋 ข้อมูลที่จะเพิ่ม:</h3>
            <h4>อัลบั้ม 5 อัลบั้ม:</h4>
            <ul>
                <li><strong>การถ่ายภาพแฟชั่น:</strong> 5 รูป (ยูนิฟอร์ม, ว่ายน้ำ, ทำงาน, ออกงาน, สตรีท)</li>
                <li><strong>ไลฟ์สไตล์:</strong> 5 รูป (คาเฟ่, ฟิตเนส, บ้าน, ท่องเที่ยว, การอ่าน)</li>
                <li><strong>ความสวยความงาม:</strong> 5 รูป (สกินแคร์, เมคอัพ, ผม, เล็บ, สปา)</li>
                <li><strong>อีเวนต์และงาน:</strong> 5 รูป (แฟชั่นโชว์, เปิดตัว, ปาร์ตี้, คอนเสิร์ต, เวิร์กช็อป)</li>
                <li><strong>การถ่ายภาพธรรมชาติ:</strong> 5 รูป (ป่า, ทะเล, ภูเขา, ดอกไม้, สัตว์)</li>
            </ul>
            <p><strong>รวม:</strong> 25 รูป พร้อมหมวดหมู่และแท็ก</p>
            <p><strong>รูปภาพ:</strong> ใช้ URL จาก Unsplash คุณภาพสูง</p>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>

