<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// เช็คว่ามี request POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'delete_all') {
        // ลบข้อมูลทั้งหมดที่เป็นข้อมูลตัวอย่าง
        $sql = "DELETE FROM gallery WHERE 
            image LIKE '%unsplash%' OR 
            image LIKE '%placeholder%' OR
            image LIKE 'gallery/%'";
        
        $result = $conn->query($sql);
        $deleted = $conn->affected_rows;
        
        echo json_encode([
            'success' => true,
            'deleted' => $deleted,
            'message' => "ลบข้อมูลตัวอย่างแล้ว {$deleted} รายการ"
        ]);
    }
    
    $conn->close();
    exit;
}

// ดึงข้อมูลทั้งหมด
$sql = "SELECT * FROM gallery ORDER BY id ASC";
$all_images = [];
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $all_images[] = $row;
    }
}

// แยกข้อมูล
$sample_images = [];
$real_images = [];

foreach ($all_images as $image) {
    $path = $image['image'];
    
    // ตรวจสอบว่าเป็นข้อมูลตัวอย่างหรือไม่
    if (strpos($path, 'unsplash') !== false || 
        strpos($path, 'placeholder') !== false ||
        preg_match('/^gallery\/[a-z-]+-[0-9]+\.jpg$/', $path)) {
        $sample_images[] = $image;
    } else {
        $real_images[] = $image;
    }
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลบข้อมูลตัวอย่างทั้งหมด</title>
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
            max-width: 1000px;
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
        .alert-success {
            background: #d1fae5;
            border-left: 4px solid #10b981;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .alert-success h3 { color: #10b981; margin-bottom: 10px; }
        .list {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            max-height: 400px;
            overflow-y: auto;
        }
        .list-item {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border-left: 4px solid #dc2626;
        }
        .list-item-title { font-weight: 600; color: #333; margin-bottom: 5px; }
        .list-item-path {
            font-family: monospace;
            font-size: 0.85rem;
            color: #666;
            background: #f0f0f0;
            padding: 5px 10px;
            border-radius: 5px;
            margin-top: 5px;
        }
        .btn {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .btn-group { text-align: center; margin: 30px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h1>ลบข้อมูลตัวอย่างทั้งหมด</h1>
        <p class="subtitle">พบข้อมูลตัวอย่างที่ไม่ควรมีในระบบ</p>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number" style="color: #3b82f6;"><?php echo count($all_images); ?></div>
                <div class="stat-label"><i class="fas fa-database"></i> ทั้งหมด</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #dc2626;"><?php echo count($sample_images); ?></div>
                <div class="stat-label"><i class="fas fa-times-circle"></i> ตัวอย่าง</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #10b981;"><?php echo count($real_images); ?></div>
                <div class="stat-label"><i class="fas fa-check-circle"></i> อัปโหลดจริง</div>
            </div>
        </div>
        
        <?php if (count($sample_images) > 0): ?>
            <div class="alert-danger">
                <h3><i class="fas fa-exclamation-circle"></i> พบข้อมูลตัวอย่าง <?php echo count($sample_images); ?> รายการ</h3>
                <p><strong>ข้อมูลเหล่านี้ประกอบด้วย:</strong></p>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li>รูปภาพจาก Unsplash (https://images.unsplash.com/...)</li>
                    <li>รูปภาพ Placeholder</li>
                    <li>รูปภาพที่มี path เป็น gallery/xxxxx-1.jpg</li>
                </ul>
                <p style="margin-top: 15px; color: #991b1b;">
                    <strong>⚠️ ข้อมูลเหล่านี้ไม่ได้มาจากการอัปโหลดจริง และไฟล์ไม่มีอยู่ในเซิร์ฟเวอร์</strong>
                </p>
            </div>
            
            <h3 style="color: #333; margin: 20px 0;">รายการที่จะถูกลบ:</h3>
            <div class="list">
                <?php foreach ($sample_images as $image): ?>
                    <div class="list-item">
                        <div class="list-item-title">
                            #<?php echo $image['id']; ?> - <?php echo htmlspecialchars($image['title']); ?>
                        </div>
                        <div class="list-item-path"><?php echo htmlspecialchars($image['image']); ?></div>
                        <?php if (strpos($image['image'], 'unsplash') !== false): ?>
                            <span style="color: #dc2626; font-size: 0.9rem; margin-top: 5px; display: block;">
                                <i class="fas fa-external-link-alt"></i> URL จาก Unsplash
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="btn-group">
                <button class="btn btn-danger" onclick="deleteAll()">
                    <i class="fas fa-trash-alt"></i> ลบข้อมูลตัวอย่างทั้งหมด (<?php echo count($sample_images); ?> รายการ)
                </button>
            </div>
        <?php else: ?>
            <div class="alert-success">
                <h3><i class="fas fa-check-circle"></i> ไม่พบข้อมูลตัวอย่าง</h3>
                <p>ระบบสะอาดแล้ว ไม่มีข้อมูลตัวอย่างที่ต้องลบ</p>
            </div>
        <?php endif; ?>
        
        <?php if (count($real_images) > 0): ?>
            <div class="alert-success">
                <h3><i class="fas fa-info-circle"></i> รูปภาพที่อัปโหลดจริง (<?php echo count($real_images); ?> รายการ)</h3>
                <p>รูปภาพเหล่านี้จะไม่ถูกลบ เพราะมาจากการอัปโหลดจริง</p>
                <div style="margin-top: 10px;">
                    <?php foreach ($real_images as $image): ?>
                        <div style="display: inline-block; background: white; padding: 8px 15px; margin: 5px; border-radius: 20px; font-size: 0.9rem;">
                            #<?php echo $image['id']; ?> - <?php echo htmlspecialchars($image['title']); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="btn-group">
            <a href="admin/gallery/" class="btn btn-success">
                <i class="fas fa-upload"></i> ไปอัปโหลดรูปภาพใหม่
            </a>
            <a href="gallery.php" class="btn">
                <i class="fas fa-eye"></i> ดูหน้า Gallery
            </a>
        </div>
    </div>

    <script>
        function deleteAll() {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                html: `
                    <p style="font-size: 1.1rem; margin-bottom: 15px;">
                        จะลบข้อมูลตัวอย่างทั้งหมด <strong><?php echo count($sample_images); ?> รายการ</strong>
                    </p>
                    <div style="background: #fee2e2; padding: 15px; border-radius: 10px; margin: 15px 0;">
                        <p style="color: #991b1b; font-weight: 600;">⚠️ รวมถึง:</p>
                        <ul style="text-align: left; color: #991b1b; margin: 10px 0 0 20px;">
                            <li>รูปภาพจาก Unsplash</li>
                            <li>รูปภาพ Placeholder</li>
                            <li>รูปภาพที่ไฟล์ไม่มีจริง</li>
                        </ul>
                    </div>
                    <p style="color: #666; font-size: 0.9rem;">
                        การลบนี้จะไม่ส่งผลกับรูปภาพที่อัปโหลดจริง
                    </p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash-alt"></i> ใช่, ลบทั้งหมด!',
                cancelButtonText: 'ยกเลิก',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch('remove-all-sample-data.php', {
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
                                <p style="color: #065f46; font-weight: 600;">✅ ตอนนี้:</p>
                                <ul style="text-align: left; color: #065f46; margin: 10px 0 0 20px;">
                                    <li>ไม่มีลิงก์ Unsplash ในระบบ</li>
                                    <li>Gallery ดึงข้อมูลจากฐานข้อมูลเท่านั้น</li>
                                    <li>แสดงเฉพาะรูปภาพที่อัปโหลดจริง</li>
                                </ul>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonColor: '#10b981',
                        confirmButtonText: 'ไปดูหน้า Gallery'
                    }).then(() => {
                        window.location.href = 'gallery.php';
                    });
                }
            });
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
