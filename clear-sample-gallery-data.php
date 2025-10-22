<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// ลบข้อมูลตัวอย่างออกจากตาราง gallery
$sql = "DELETE FROM gallery WHERE image LIKE '%unsplash%' OR image LIKE '%placeholder%'";
$result = $conn->query($sql);

$deleted_count = $conn->affected_rows;

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลบข้อมูลตัวอย่าง Gallery</title>
    <style>
        body { font-family: 'Noto Sans Thai', sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        h1 { color: #333; }
        .section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .btn { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block; }
        .btn:hover { background: #005a87; }
        .btn-success { background: #28a745; }
        .btn-danger { background: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗑️ ลบข้อมูลตัวอย่าง Gallery</h1>
        
        <div class="section">
            <?php if ($deleted_count > 0): ?>
                <h2 class="success">✅ สำเร็จ!</h2>
                <p>ลบข้อมูลตัวอย่างออกแล้ว: <strong><?php echo $deleted_count; ?></strong> รายการ</p>
                <p>ข้อมูลที่ลบคือรูปภาพตัวอย่างจาก Unsplash และ placeholder</p>
            <?php else: ?>
                <h2 class="warning">⚠️ ไม่มีข้อมูลตัวอย่าง</h2>
                <p>ไม่พบข้อมูลตัวอย่างที่ต้องลบ</p>
                <p>ตาราง gallery อาจมีแต่ข้อมูลที่อัปโหลดจริงแล้ว</p>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h2>📋 ขั้นตอนต่อไป:</h2>
            <ol>
                <li>ไปที่ <a href="admin/gallery/" class="btn btn-success">Admin Gallery</a> เพื่ออัปโหลดรูปภาพจริง</li>
                <li>หรือ <a href="check-admin-gallery-data.php" class="btn">ตรวจสอบข้อมูลปัจจุบัน</a></li>
                <li>จากนั้น <a href="gallery.php" class="btn">ดูหน้า Gallery</a></li>
            </ol>
        </div>
        
        <div class="section">
            <h2>💡 คำแนะนำ:</h2>
            <ul>
                <li>อัปโหลดรูปภาพผ่าน Admin Gallery เท่านั้น</li>
                <li>ใช้ไฟล์รูปภาพที่มีอยู่จริงในเครื่อง</li>
                <li>ตั้งชื่อไฟล์ให้เหมาะสม</li>
                <li>เลือกหมวดหมู่ที่ถูกต้อง</li>
            </ul>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
