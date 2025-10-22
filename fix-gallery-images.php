<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// ดึงข้อมูลจากตาราง gallery
$sql = "SELECT * FROM gallery WHERE is_active = 1 ORDER BY sort_order ASC, created_at DESC";
$gallery_images = [];
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $gallery_images[] = $row;
    }
}

// ดึงรายชื่อไฟล์ในโฟลเดอร์
$gallery_folder = '/Applications/MAMP/htdocs/vibedaybkk/uploads/gallery/';
$existing_files = [];
if (is_dir($gallery_folder)) {
    $files = scandir($gallery_folder);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && !is_dir($gallery_folder . $file)) {
            $existing_files[] = $file;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลรูปภาพ Gallery</title>
    <style>
        body { font-family: 'Noto Sans Thai', sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        h1 { color: #333; }
        .section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        .btn:hover { background: #005a87; }
        .btn-success { background: #28a745; }
        .btn-warning { background: #ffc107; color: #000; }
        .btn-danger { background: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 แก้ไขข้อมูลรูปภาพ Gallery</h1>
        
        <div class="section">
            <h2>📊 สถิติ:</h2>
            <p>ข้อมูลในฐานข้อมูล: <strong><?php echo count($gallery_images); ?></strong> รายการ</p>
            <p>ไฟล์ในโฟลเดอร์: <strong><?php echo count($existing_files); ?></strong> ไฟล์</p>
        </div>
        
        <div class="section">
            <h2>📁 ไฟล์ที่มีอยู่ในโฟลเดอร์:</h2>
            <?php if (!empty($existing_files)): ?>
                <ul>
                    <?php foreach ($existing_files as $file): ?>
                        <li><?php echo htmlspecialchars($file); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="error">❌ ไม่มีไฟล์ในโฟลเดอร์</p>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h2>📋 ข้อมูลในฐานข้อมูล:</h2>
            <?php if (!empty($gallery_images)): ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Current Path</th>
                        <th>File Exists?</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($gallery_images as $image): ?>
                        <?php
                        $current_path = $image['image'];
                        $file_exists = false;
                        $suggested_file = null;
                        
                        // ตรวจสอบว่าไฟล์มีอยู่หรือไม่
                        if (file_exists($gallery_folder . basename($current_path))) {
                            $file_exists = true;
                        } else {
                            // หาไฟล์ที่ใกล้เคียง
                            foreach ($existing_files as $file) {
                                if (strpos($file, 'gallery') !== false || strpos($file, 'beauty') !== false || strpos($file, 'workshop') !== false) {
                                    $suggested_file = $file;
                                    break;
                                }
                            }
                        }
                        ?>
                        <tr>
                            <td><?php echo $image['id']; ?></td>
                            <td><?php echo htmlspecialchars($image['title']); ?></td>
                            <td><?php echo htmlspecialchars($current_path); ?></td>
                            <td>
                                <?php if ($file_exists): ?>
                                    <span class="success">✅ มีอยู่</span>
                                <?php else: ?>
                                    <span class="error">❌ ไม่มี</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!$file_exists && $suggested_file): ?>
                                    <button class="btn btn-warning" onclick="updateImagePath(<?php echo $image['id']; ?>, '<?php echo $suggested_file; ?>')">
                                        แก้ไขเป็น <?php echo $suggested_file; ?>
                                    </button>
                                <?php elseif (!$file_exists): ?>
                                    <button class="btn btn-danger" onclick="deleteRecord(<?php echo $image['id']; ?>)">
                                        ลบข้อมูล
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p class="error">❌ ไม่มีข้อมูลในฐานข้อมูล</p>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h2>🛠️ การแก้ไข:</h2>
            <p>คลิกปุ่ม "แก้ไขเป็น" เพื่อเปลี่ยน path ให้ตรงกับไฟล์ที่มีอยู่จริง</p>
            <p>หรือคลิก "ลบข้อมูล" เพื่อลบข้อมูลที่ไม่มีไฟล์จริง</p>
        </div>
        
        <hr>
        <p><a href="gallery.php">← กลับไปหน้า Gallery</a></p>
    </div>

    <script>
        function updateImagePath(id, newPath) {
            if (confirm('คุณแน่ใจหรือไม่ที่จะเปลี่ยน path เป็น: ' + newPath)) {
                fetch('fix-gallery-images-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'update_path',
                        id: id,
                        new_path: 'gallery/' + newPath
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('แก้ไขสำเร็จ!');
                        location.reload();
                    } else {
                        alert('เกิดข้อผิดพลาด: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('เกิดข้อผิดพลาด: ' + error);
                });
            }
        }
        
        function deleteRecord(id) {
            if (confirm('คุณแน่ใจหรือไม่ที่จะลบข้อมูลนี้?')) {
                fetch('fix-gallery-images-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        id: id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('ลบสำเร็จ!');
                        location.reload();
                    } else {
                        alert('เกิดข้อผิดพลาด: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('เกิดข้อผิดพลาด: ' + error);
                });
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>