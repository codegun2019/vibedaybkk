<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// ดึงข้อมูลจากตาราง gallery
$sql = "SELECT * FROM gallery WHERE is_active = 1 ORDER BY sort_order ASC, created_at DESC LIMIT 10";
$gallery_images = [];
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $gallery_images[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ทดสอบข้อมูล Gallery</title>
    <style>
        body { font-family: 'Noto Sans Thai', sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        h1 { color: #333; }
        .image-item { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .image-preview { max-width: 200px; max-height: 200px; object-fit: cover; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
        pre { background: #f8f8f8; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 ทดสอบข้อมูล Gallery</h1>
        
        <h2>📊 สถิติ:</h2>
        <p>จำนวนรูปภาพทั้งหมด: <strong><?php echo count($gallery_images); ?></strong> รูป</p>
        
        <?php if (empty($gallery_images)): ?>
            <p class="error">❌ ไม่มีข้อมูลรูปภาพในตาราง gallery</p>
            <p>กรุณาเพิ่มรูปภาพที่: <a href="admin/gallery/add.php">admin/gallery/add.php</a></p>
        <?php else: ?>
            <p class="success">✅ พบข้อมูลรูปภาพ <?php echo count($gallery_images); ?> รูป</p>
            
            <h2>📸 รายการรูปภาพ:</h2>
            <?php foreach ($gallery_images as $index => $image): ?>
                <div class="image-item">
                    <h3>รูปที่ <?php echo $index + 1; ?>: <?php echo htmlspecialchars($image['title']); ?></h3>
                    
                    <p><strong>ID:</strong> <?php echo $image['id']; ?></p>
                    <p><strong>Path:</strong> <?php echo htmlspecialchars($image['image']); ?></p>
                    
                    <?php
                    $image_src = $image['image'] ?? '';
                    if (filter_var($image_src, FILTER_VALIDATE_URL)) {
                        $final_src = $image_src;
                        echo "<p><strong>Type:</strong> External URL</p>";
                    } else {
                        // Local path - ตรวจสอบว่า path มี 'uploads/' หน้าหรือไม่
                        if (strpos($image_src, 'uploads/') === 0) {
                            // ถ้ามี uploads/ อยู่แล้ว ใช้ BASE_URL
                            $final_src = BASE_URL . '/' . $image_src;
                            echo "<p><strong>Type:</strong> Local Path (with uploads/ prefix)</p>";
                        } else {
                            // ถ้าไม่มี ใช้ UPLOADS_URL
                            $final_src = UPLOADS_URL . '/' . $image_src;
                            echo "<p><strong>Type:</strong> Local Path (without uploads/ prefix)</p>";
                        }
                    }
                    echo "<p><strong>Final URL:</strong> <a href='{$final_src}' target='_blank'>{$final_src}</a></p>";
                    ?>
                    
                    <p><strong>หมวดหมู่:</strong> <?php echo htmlspecialchars($image['category'] ?? '-'); ?></p>
                    <p><strong>คำอธิบาย:</strong> <?php echo htmlspecialchars($image['description'] ?? '-'); ?></p>
                    
                    <div style="margin-top: 10px;">
                        <img src="<?php echo $final_src; ?>" alt="<?php echo htmlspecialchars($image['title']); ?>" class="image-preview" onerror="this.style.border='3px solid red'; this.alt='❌ ไม่สามารถโหลดรูปได้';">
                    </div>
                </div>
            <?php endforeach; ?>
            
            <h2>🔧 JavaScript Data (สำหรับ Modal):</h2>
            <pre><?php 
            $images_data = array_map(function($img) {
                $image_src = $img['image'] ?? '';
                if (filter_var($image_src, FILTER_VALIDATE_URL)) {
                    // External URL
                    $final_src = $image_src;
                } else {
                    // Local path - ตรวจสอบว่า path มี 'uploads/' หน้าหรือไม่
                    if (strpos($image_src, 'uploads/') === 0) {
                        // ถ้ามี uploads/ อยู่แล้ว ใช้ BASE_URL
                        $final_src = BASE_URL . '/' . $image_src;
                    } else {
                        // ถ้าไม่มี ใช้ UPLOADS_URL
                        $final_src = UPLOADS_URL . '/' . $image_src;
                    }
                }
                return [
                    'src' => $final_src,
                    'title' => $img['title'] ?? '',
                    'description' => $img['description'] ?? '',
                    'category' => $img['category'] ?? ''
                ];
            }, $gallery_images);
            echo json_encode($images_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            ?></pre>
        <?php endif; ?>
        
        <hr>
        <p><a href="gallery.php">← กลับไปหน้า Gallery</a></p>
    </div>
</body>
</html>

<?php $conn->close(); ?>

