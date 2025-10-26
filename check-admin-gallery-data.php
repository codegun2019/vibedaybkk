<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// ตรวจสอบข้อมูลในตาราง gallery
$sql = "SELECT * FROM gallery ORDER BY created_at DESC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบข้อมูล Admin Gallery</title>
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
        .image-preview { max-width: 100px; max-height: 100px; object-fit: cover; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 ตรวจสอบข้อมูล Admin Gallery</h1>
        
        <div class="section">
            <h2>📊 สถิติ:</h2>
            <p>จำนวนรูปภาพทั้งหมด: <strong><?php echo $result->num_rows; ?></strong> รายการ</p>
        </div>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="section">
                <h2>📋 ข้อมูลรูปภาพ:</h2>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Image Path</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Preview</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td><?php echo htmlspecialchars($row['image']); ?></td>
                            <td>
                                <?php if ($row['is_active'] == 1): ?>
                                    <span class="success">✅ Active</span>
                                <?php else: ?>
                                    <span class="error">❌ Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('Y-m-d H:i:s', strtotime($row['created_at'])); ?></td>
                            <td>
                                <?php if (!empty($row['image'])): ?>
                                    <?php
                                    $image_src = $row['image'];
                                    if (filter_var($image_src, FILTER_VALIDATE_URL)) {
                                        $final_src = $image_src;
                                    } else {
                                        if (strpos($image_src, 'uploads/') === 0) {
                                            $final_src = BASE_URL . '/' . $image_src;
                                        } else {
                                            $final_src = UPLOADS_URL . '/' . $image_src;
                                        }
                                    }
                                    ?>
                                    <img src="<?php echo $final_src; ?>" alt="Preview" class="image-preview" onerror="this.style.display='none';">
                                <?php else: ?>
                                    <span class="error">ไม่มีรูปภาพ</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        <?php else: ?>
            <div class="section">
                <h2 class="warning">⚠️ ไม่มีข้อมูลรูปภาพ</h2>
                <p>ตาราง gallery ว่างเปล่า</p>
                <p>กรุณาไปที่ <a href="admin/gallery/">Admin Gallery</a> เพื่ออัปโหลดรูปภาพ</p>
            </div>
        <?php endif; ?>
        
        <div class="section">
            <h2>🛠️ การแก้ไข:</h2>
            <p><a href="admin/gallery/" class="btn btn-success">ไปที่ Admin Gallery</a></p>
            <p><a href="gallery.php" class="btn">ดูหน้า Gallery</a></p>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>


