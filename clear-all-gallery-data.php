<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// เช็คว่ามี request POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'delete_all') {
        // ลบข้อมูลทั้งหมดในตาราง gallery
        $sql = "DELETE FROM gallery";
        $result = $conn->query($sql);
        
        // ลบไฟล์รูปภาพทั้งหมดในโฟลเดอร์
        $gallery_folder = __DIR__ . '/uploads/gallery/';
        $deleted_files = 0;
        
        if (is_dir($gallery_folder)) {
            $files = scandir($gallery_folder);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && !is_dir($gallery_folder . $file)) {
                    if (unlink($gallery_folder . $file)) {
                        $deleted_files++;
                    }
                }
            }
        }
        
        // ลบไฟล์ thumbnail ด้วย
        $thumb_folder = $gallery_folder . 'thumbs/';
        if (is_dir($thumb_folder)) {
            $thumb_files = scandir($thumb_folder);
            foreach ($thumb_files as $file) {
                if ($file != '.' && $file != '..' && !is_dir($thumb_folder . $file)) {
                    unlink($thumb_folder . $file);
                }
            }
        }
        
        echo json_encode([
            'success' => true,
            'deleted_records' => $conn->affected_rows,
            'deleted_files' => $deleted_files,
            'message' => "ลบข้อมูลทั้งหมดแล้ว {$conn->affected_rows} รายการ และไฟล์ {$deleted_files} ไฟล์"
        ]);
    }
    
    $conn->close();
    exit;
}

// ตรวจสอบข้อมูลปัจจุบัน
$sql_count = "SELECT COUNT(*) as total FROM gallery";
$result_count = $conn->query($sql_count);
$total_records = $result_count->fetch_assoc()['total'];

// ตรวจสอบไฟล์ในโฟลเดอร์
$gallery_folder = __DIR__ . '/uploads/gallery/';
$total_files = 0;
if (is_dir($gallery_folder)) {
    $files = scandir($gallery_folder);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && !is_dir($gallery_folder . $file)) {
            $total_files++;
        }
    }
}

// ตรวจสอบไฟล์ thumbnail
$thumb_folder = $gallery_folder . 'thumbs/';
$total_thumbs = 0;
if (is_dir($thumb_folder)) {
    $thumb_files = scandir($thumb_folder);
    foreach ($thumb_files as $file) {
        if ($file != '.' && $file != '..' && !is_dir($thumb_folder . $file)) {
            $total_thumbs++;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลบข้อมูล Gallery ทั้งหมด</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Noto Sans Thai', sans-serif; 
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
            color: #dc2626;
            margin-bottom: 10px;
            text-align: center;
        }
        .subtitle { text-align: center; color: #666; margin-bottom: 30px; }
        .icon {
            text-align: center;
            font-size: 5rem;
            margin-bottom: 20px;
            color: #dc2626;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
        }
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .stat-label { font-size: 1rem; color: #666; }
        .alert-danger {
            background: #fee2e2;
            border-left: 4px solid #dc2626;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .alert-danger h3 { color: #dc2626; margin-bottom: 10px; }
        .alert-warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .alert-warning h3 { color: #f59e0b; margin-bottom: 10px; }
        .alert-success {
            background: #d1fae5;
            border-left: 4px solid #10b981;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .alert-success h3 { color: #10b981; margin-bottom: 10px; }
        .btn {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
            box-shadow: 0 10px 20px rgba(239, 68, 68, 0.4);
        }
        .btn-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        }
        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .btn-group { text-align: center; margin: 30px 0; }
        .details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .details h4 { color: #333; margin-bottom: 10px; }
        .details ul { margin-left: 20px; color: #666; }
        .details li { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <i class="fas fa-trash-alt"></i>
        </div>
        <h1>ลบข้อมูล Gallery ทั้งหมด</h1>
        <p class="subtitle">ลบข้อมูลและไฟล์ทั้งหมดเพื่อเริ่มต้นใหม่</p>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number" style="color: #3b82f6;"><?php echo $total_records; ?></div>
                <div class="stat-label"><i class="fas fa-database"></i> ข้อมูลในฐานข้อมูล</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #f59e0b;"><?php echo $total_files; ?></div>
                <div class="stat-label"><i class="fas fa-image"></i> ไฟล์รูปภาพ</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #8b5cf6;"><?php echo $total_thumbs; ?></div>
                <div class="stat-label"><i class="fas fa-th"></i> ไฟล์ Thumbnail</div>
            </div>
        </div>
        
        <?php if ($total_records > 0 || $total_files > 0): ?>
            <div class="alert-danger">
                <h3><i class="fas fa-exclamation-triangle"></i> พบข้อมูลที่ต้องลบ</h3>
                <p>มีข้อมูลและไฟล์ในระบบที่ต้องลบออกทั้งหมด:</p>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <?php if ($total_records > 0): ?>
                        <li>ข้อมูลในฐานข้อมูล: <?php echo $total_records; ?> รายการ</li>
                    <?php endif; ?>
                    <?php if ($total_files > 0): ?>
                        <li>ไฟล์รูปภาพ: <?php echo $total_files; ?> ไฟล์</li>
                    <?php endif; ?>
                    <?php if ($total_thumbs > 0): ?>
                        <li>ไฟล์ Thumbnail: <?php echo $total_thumbs; ?> ไฟล์</li>
                    <?php endif; ?>
                </ul>
                <p style="margin-top: 15px; color: #991b1b;">
                    <strong>⚠️ การลบนี้จะไม่สามารถย้อนกลับได้</strong>
                </p>
            </div>
            
            <div class="details">
                <h4><i class="fas fa-info-circle"></i> สิ่งที่จะถูกลบ:</h4>
                <ul>
                    <li><strong>ข้อมูลในฐานข้อมูล:</strong> ทุกรายการในตาราง gallery</li>
                    <li><strong>ไฟล์รูปภาพ:</strong> ไฟล์ทั้งหมดในโฟลเดอร์ uploads/gallery/</li>
                    <li><strong>ไฟล์ Thumbnail:</strong> ไฟล์ thumbnail ทั้งหมด</li>
                    <li><strong>ข้อมูลจำลอง:</strong> ข้อมูลตัวอย่างและข้อมูลออนไลน์ทั้งหมด</li>
                </ul>
            </div>
            
            <div class="btn-group">
                <button class="btn" onclick="deleteAll()">
                    <i class="fas fa-trash-alt"></i> ลบทั้งหมด (<?php echo $total_records; ?> รายการ + <?php echo $total_files; ?> ไฟล์)
                </button>
            </div>
        <?php else: ?>
            <div class="alert-success">
                <h3><i class="fas fa-check-circle"></i> ระบบสะอาดแล้ว</h3>
                <p>ไม่มีข้อมูลหรือไฟล์ในระบบ ตอนนี้พร้อมเริ่มต้นใหม่แล้ว</p>
            </div>
            
            <div class="btn-group">
                <a href="admin/gallery/" class="btn btn-success">
                    <i class="fas fa-upload"></i> ไปอัปโหลดรูปภาพใหม่
                </a>
                <a href="gallery.php" class="btn btn-secondary">
                    <i class="fas fa-eye"></i> ดูหน้า Gallery
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function deleteAll() {
            Swal.fire({
                title: 'ยืนยันการลบทั้งหมด?',
                html: `
                    <div style="text-align: left;">
                        <p style="font-size: 1.1rem; margin-bottom: 15px;">
                            จะลบข้อมูลทั้งหมดในระบบ:
                        </p>
                        <ul style="margin-left: 20px; color: #666;">
                            <li>ข้อมูลในฐานข้อมูล: <strong><?php echo $total_records; ?> รายการ</strong></li>
                            <li>ไฟล์รูปภาพ: <strong><?php echo $total_files; ?> ไฟล์</strong></li>
                            <li>ไฟล์ Thumbnail: <strong><?php echo $total_thumbs; ?> ไฟล์</strong></li>
                        </ul>
                        <div style="background: #fee2e2; padding: 15px; border-radius: 10px; margin: 15px 0;">
                            <p style="color: #991b1b; font-weight: 600;">⚠️ คำเตือน:</p>
                            <ul style="color: #991b1b; margin: 10px 0 0 20px;">
                                <li>การลบนี้จะไม่สามารถย้อนกลับได้</li>
                                <li>ข้อมูลจำลองและข้อมูลออนไลน์ทั้งหมดจะถูกลบ</li>
                                <li>ระบบจะเริ่มต้นใหม่แบบสะอาด</li>
                            </ul>
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash-alt"></i> ใช่, ลบทั้งหมด!',
                cancelButtonText: 'ยกเลิก',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch('clear-all-gallery-data.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=delete_all'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'เกิดข้อผิดพลาด');
                        }
                        return data;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`เกิดข้อผิดพลาด: ${error}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'ลบสำเร็จ!',
                        html: `
                            <p style="font-size: 1.1rem;">${result.value.message}</p>
                            <div style="background: #d1fae5; padding: 15px; border-radius: 10px; margin: 15px 0;">
                                <p style="color: #065f46; font-weight: 600;">✅ ระบบสะอาดแล้ว:</p>
                                <ul style="text-align: left; color: #065f46; margin: 10px 0 0 20px;">
                                    <li>ลบข้อมูลในฐานข้อมูล: ${result.value.deleted_records} รายการ</li>
                                    <li>ลบไฟล์รูปภาพ: ${result.value.deleted_files} ไฟล์</li>
                                    <li>ไม่มีข้อมูลจำลองหรือข้อมูลออนไลน์แล้ว</li>
                                    <li>พร้อมเริ่มต้นใหม่แบบสะอาด</li>
                                </ul>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonColor: '#10b981',
                        confirmButtonText: 'ไปอัปโหลดรูปภาพใหม่'
                    }).then(() => {
                        window.location.href = 'admin/gallery/';
                    });
                }
            });
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>


