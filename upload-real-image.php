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

// URL ของรูปภาพที่แนบมา
$image_url = 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&h=600&fit=crop';

echo "🔄 กำลังดาวน์โหลดรูปภาพ...\n";
$download_result = downloadImageFromUrl($image_url);

if (!$download_result['success']) {
    die("❌ ไม่สามารถดาวน์โหลดรูปภาพได้: " . $download_result['message']);
}

echo "✅ ดาวน์โหลดสำเร็จ (ขนาด: " . number_format($download_result['size']) . " bytes)\n";

echo "🔄 กำลังอัปโหลดไปยังเซิร์ฟเวอร์...\n";
$upload_result = uploadImageToServer($download_result['data'], 'thai-traditional-woman.jpg');

if (!$upload_result['success']) {
    die("❌ ไม่สามารถอัปโหลดได้: " . $upload_result['message']);
}

echo "✅ อัปโหลดสำเร็จ: " . $upload_result['filename'] . "\n";

// บันทึกลงฐานข้อมูล
echo "🔄 กำลังบันทึกลงฐานข้อมูล...\n";

$title = 'ภาพถ่ายสตรีทแฟชั่นไทย';
$description = 'ภาพถ่ายหญิงสาวสวมชุดไทยแบบดั้งเดิมพร้อมเครื่องประดับทอง ฉากหลังเป็นซากปรักหักพังโบราณ สไตล์สตรีทแฟชั่นไทย';
$category = 'สตรีทแฟชั่น';
$tags = 'ไทย,แฟชั่น,ชุดไทย,เครื่องประดับ,โบราณ';
$image_path = $upload_result['file_path'];

$sql = "INSERT INTO gallery (title, description, image, category, tags, sort_order, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("❌ ไม่สามารถเตรียม SQL ได้: " . $conn->error);
}

$sort_order = 1;
$is_active = 1;

$stmt->bind_param('sssssii', $title, $description, $image_path, $category, $tags, $sort_order, $is_active);

if ($stmt->execute()) {
    $image_id = $conn->insert_id;
    echo "✅ บันทึกลงฐานข้อมูลสำเร็จ (ID: $image_id)\n";
    
    echo "\n🎉 เสร็จสิ้น!\n";
    echo "📋 ข้อมูลที่บันทึก:\n";
    echo "   ID: $image_id\n";
    echo "   ชื่อ: $title\n";
    echo "   หมวดหมู่: $category\n";
    echo "   ไฟล์: " . $upload_result['filename'] . "\n";
    echo "   Path: $image_path\n";
    echo "   ขนาด: " . number_format($upload_result['size']) . " bytes\n";
    
    echo "\n🔗 ลิงก์สำหรับดู:\n";
    echo "   Admin Gallery: http://localhost:8888/vibedaybkk/admin/gallery/\n";
    echo "   หน้า Gallery: http://localhost:8888/vibedaybkk/gallery.php\n";
    
} else {
    die("❌ ไม่สามารถบันทึกลงฐานข้อมูลได้: " . $stmt->error);
}

$stmt->close();
$conn->close();

echo "\n✨ รูปภาพถูกอัปโหลดและบันทึกลงฐานข้อมูลเรียบร้อยแล้ว!\n";
?>
