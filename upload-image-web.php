<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// ฟังก์ชันอัปโหลดรูปภาพ
function uploadImageToServer($image_data, $filename) {
    $upload_dir = __DIR__ . '/uploads/gallery/';
    
    // สร้างโฟลเดอร์ถ้ายังไม่มี
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // สร้างชื่อไฟล์ใหม่
    $extension = 'jpg';
    $new_filename = uniqid() . '_' . time() . '.' . $extension;
    $file_path = $upload_dir . $new_filename;
    
    // เก็บข้อมูลรูปภาพเป็นไฟล์
    if (file_put_contents($file_path, $image_data)) {
        return [
            'success' => true,
            'filename' => $new_filename,
            'path' => 'gallery/' . $new_filename,
            'size' => strlen($image_data)
        ];
    } else {
        return [
            'success' => false,
            'message' => 'ไม่สามารถบันทึกไฟล์ได้'
        ];
    }
}

// ฟังก์ชันดึงรูปภาพจาก URL
function downloadImageFromUrl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    
    $image_data = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200 && $image_data !== false) {
        return [
            'success' => true,
            'data' => $image_data,
            'size' => strlen($image_data)
        ];
    } else {
        return [
            'success' => false,
            'message' => 'ไม่สามารถดาวน์โหลดรูปภาพได้'
        ];
    }
}

$success = false;
$error = '';
$image_info = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    // URL ของรูปภาพที่แนบมา
    $image_url = 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&h=600&fit=crop';
    
    try {
        // ดาวน์โหลดรูปภาพ
        $download_result = downloadImageFromUrl($image_url);
        
        if (!$download_result['success']) {
            throw new Exception($download_result['message']);
        }
        
        // อัปโหลดไปยังเซิร์ฟเวอร์
        $upload_result = uploadImageToServer($download_result['data'], 'thai-traditional-woman.jpg');
        
        if (!$upload_result['success']) {
            throw new Exception($upload_result['message']);
        }
        
        // บันทึกลงฐานข้อมูล
        $title = 'ภาพถ่ายสตรีทแฟชั่นไทย';
        $description = 'ภาพถ่ายหญิงสาวสวมชุดไทยแบบดั้งเดิมพร้อมเครื่องประดับทอง ฉากหลังเป็นซากปรักหักพังโบราณ สไตล์สตรีทแฟชั่นไทย';
        $category = 'สตรีทแฟชั่น';
        $tags = 'ไทย,แฟชั่น,ชุดไทย,เครื่องประดับ,โบราณ';
        $image_path = $upload_result['file_path'];
        
        $sql = "INSERT INTO gallery (title, description, image, category, tags, sort_order, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("ไม่สามารถเตรียม SQL ได้: " . $conn->error);
        }
        
        $sort_order = 1;
        $is_active = 1;
        
        $stmt->bind_param('sssssii', $title, $description, $image_path, $category, $tags, $sort_order, $is_active);
        
        if ($stmt->execute()) {
            $image_id = $conn->insert_id;
            $success = true;
            $image_info = [
                'id' => $image_id,
                'title' => $title,
                'category' => $category,
                'filename' => $upload_result['filename'],
                'path' => $image_path,
                'size' => $upload_result['size']
            ];
        } else {
            throw new Exception("ไม่สามารถบันทึกลงฐานข้อมูลได้: " . $stmt->error);
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>อัปโหลดรูปภาพจริง</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Noto Sans Thai', sans-serif; 
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
        }
        h1 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }
        .subtitle { text-align: center; color: #666; margin-bottom: 30px; }
        .icon {
            text-align: center;
            font-size: 5rem;
            margin-bottom: 20px;
            color: #10b981;
        }
        .success { color: #10b981; }
        .error { color: #ef4444; }
        .btn {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            margin: 10px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            font-family: 'Noto Sans Thai', sans-serif;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.4);
        }
        .btn-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        }
        .btn-group { text-align: center; margin: 30px 0; }
        .info-box {
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .info-box h3 { color: #10b981; margin-bottom: 10px; }
        .error-box {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .error-box h3 { color: #ef4444; margin-bottom: 10px; }
        .details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .details h4 { color: #333; margin-bottom: 10px; }
        .details p { color: #666; margin: 5px 0; }
        .image-preview {
            max-width: 300px;
            max-height: 300px;
            border-radius: 10px;
            margin: 20px auto;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <i class="fas fa-upload"></i>
        </div>
        <h1>อัปโหลดรูปภาพจริง</h1>
        <p class="subtitle">อัปโหลดรูปภาพที่แนบมาและบันทึกลงฐานข้อมูล</p>
        
        <?php if ($success): ?>
            <div class="info-box">
                <h3><i class="fas fa-check-circle"></i> อัปโหลดสำเร็จ!</h3>
                <p>รูปภาพถูกอัปโหลดและบันทึกลงฐานข้อมูลเรียบร้อยแล้ว</p>
                
                <?php if (!empty($image_info)): ?>
                    <div class="details">
                        <h4>ข้อมูลที่บันทึก:</h4>
                        <p><strong>ID:</strong> <?php echo $image_info['id']; ?></p>
                        <p><strong>ชื่อ:</strong> <?php echo htmlspecialchars($image_info['title']); ?></p>
                        <p><strong>หมวดหมู่:</strong> <?php echo htmlspecialchars($image_info['category']); ?></p>
                        <p><strong>ไฟล์:</strong> <?php echo htmlspecialchars($image_info['filename']); ?></p>
                        <p><strong>Path:</strong> <?php echo htmlspecialchars($image_info['path']); ?></p>
                        <p><strong>ขนาด:</strong> <?php echo number_format($image_info['size']); ?> bytes</p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($image_info['path'])): ?>
                    <img src="<?php echo UPLOADS_URL . '/' . $image_info['path']; ?>" 
                         alt="Uploaded Image" 
                         class="image-preview">
                <?php endif; ?>
            </div>
            
            <div class="btn-group">
                <a href="admin/gallery/" class="btn">
                    <i class="fas fa-images"></i> ไปที่ Admin Gallery
                </a>
                <a href="gallery.php" class="btn btn-secondary">
                    <i class="fas fa-eye"></i> ดูหน้า Gallery
                </a>
            </div>
            
        <?php elseif (!empty($error)): ?>
            <div class="error-box">
                <h3><i class="fas fa-exclamation-circle"></i> เกิดข้อผิดพลาด</h3>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
            
            <div class="btn-group">
                <button onclick="location.reload()" class="btn">
                    <i class="fas fa-redo"></i> ลองใหม่
                </button>
            </div>
            
        <?php else: ?>
            <div class="info-box">
                <h3><i class="fas fa-info-circle"></i> ข้อมูลรูปภาพที่จะอัปโหลด</h3>
                <p><strong>ชื่อ:</strong> ภาพถ่ายสตรีทแฟชั่นไทย</p>
                <p><strong>หมวดหมู่:</strong> สตรีทแฟชั่น</p>
                <p><strong>คำอธิบาย:</strong> ภาพถ่ายหญิงสาวสวมชุดไทยแบบดั้งเดิมพร้อมเครื่องประดับทอง ฉากหลังเป็นซากปรักหักพังโบราณ สไตล์สตรีทแฟชั่นไทย</p>
                <p><strong>แท็ก:</strong> ไทย, แฟชั่น, ชุดไทย, เครื่องประดับ, โบราณ</p>
            </div>
            
            <div class="btn-group">
                <form method="POST">
                    <button type="submit" name="upload" class="btn">
                        <i class="fas fa-upload"></i> อัปโหลดรูปภาพ
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php $conn->close(); ?>
