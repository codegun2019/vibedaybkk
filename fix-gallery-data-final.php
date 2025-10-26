<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// เช็คว่า request มาจาก POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'delete_all_sample') {
        // ลบข้อมูลตัวอย่างทั้งหมด
        $sql = "DELETE FROM gallery WHERE id >= 3 AND id <= 20";
        $result = $conn->query($sql);
        
        echo json_encode([
            'success' => true,
            'deleted' => $conn->affected_rows,
            'message' => "ลบข้อมูลตัวอย่างแล้ว {$conn->affected_rows} รายการ"
        ]);
    }
    
    $conn->close();
    exit;
}

// ดึงข้อมูลทั้งหมด
$sql = "SELECT * FROM gallery ORDER BY id ASC";
$images = [];
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
}

// เช็คว่าไฟล์มีอยู่จริงหรือไม่
$gallery_folder = __DIR__ . '/uploads/gallery/';
foreach ($images as &$image) {
    $image_path = $image['image'];
    $filename = basename($image_path);
    $full_path = $gallery_folder . $filename;
    $image['file_exists'] = file_exists($full_path);
}

$total = count($images);
$sample_count = 0;
$real_count = 0;

foreach ($images as $image) {
    if ($image['id'] >= 3 && $image['id'] <= 20) {
        $sample_count++;
    } else {
        $real_count++;
    }
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูล Gallery</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Noto Sans Thai', sans-serif; 
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
            padding: 20px;
            color: #fff;
        }
        .container { max-width: 1400px; margin: 0 auto; }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }
        .subtitle { text-align: center; color: #aaa; margin-bottom: 30px; }
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background: rgba(255,255,255,0.05);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .stat-label { font-size: 1rem; color: #aaa; }
        .section {
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        th { background: rgba(255,255,255,0.1); font-weight: 600; }
        .sample-row { background: rgba(248, 113, 113, 0.1); }
        .real-row { background: rgba(74, 222, 128, 0.1); }
        .success { color: #4ade80; }
        .error { color: #f87171; }
        .warning { color: #fbbf24; }
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
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        .btn-danger {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .btn-success {
            background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
        }
        .btn-group { text-align: center; margin: 30px 0; }
        .path-text {
            font-family: monospace;
            font-size: 0.85rem;
            background: rgba(0,0,0,0.3);
            padding: 4px 8px;
            border-radius: 4px;
        }
        .alert {
            background: rgba(251, 191, 36, 0.1);
            border-left: 4px solid #fbbf24;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .alert h3 { color: #fbbf24; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-tools"></i> แก้ไขข้อมูล Gallery</h1>
        <p class="subtitle">ลบข้อมูลตัวอย่างและให้ Gallery ดึงจากฐานข้อมูลจริง</p>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number" style="color: #3b82f6;"><?php echo $total; ?></div>
                <div class="stat-label"><i class="fas fa-database"></i> รูปภาพทั้งหมด</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #f87171;"><?php echo $sample_count; ?></div>
                <div class="stat-label"><i class="fas fa-exclamation-triangle"></i> ข้อมูลตัวอย่าง (ID 3-20)</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #4ade80;"><?php echo $real_count; ?></div>
                <div class="stat-label"><i class="fas fa-check-circle"></i> ข้อมูลจริง</div>
            </div>
        </div>
        
        <?php if ($sample_count > 0): ?>
            <div class="alert">
                <h3><i class="fas fa-info-circle"></i> ปัญหาที่พบ</h3>
                <p>มีข้อมูลตัวอย่าง (ID 3-20) ที่ไฟล์รูปภาพไม่มีอยู่จริง</p>
                <p>ข้อมูลเหล่านี้ทำให้หน้า Gallery ไม่แสดงรูปภาพ เพราะไฟล์ไม่มีจริงในโฟลเดอร์</p>
            </div>
        <?php endif; ?>
        
        <div class="section">
            <h2><i class="fas fa-list"></i> รายการข้อมูลทั้งหมด</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Image Path</th>
                        <th>ไฟล์มีอยู่จริง?</th>
                        <th>ประเภท</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($images as $image): ?>
                        <?php
                        $is_sample = ($image['id'] >= 3 && $image['id'] <= 20);
                        $row_class = $is_sample ? 'sample-row' : 'real-row';
                        ?>
                        <tr class="<?php echo $row_class; ?>">
                            <td><?php echo $image['id']; ?></td>
                            <td><?php echo htmlspecialchars($image['title']); ?></td>
                            <td><span class="path-text"><?php echo htmlspecialchars($image['image']); ?></span></td>
                            <td>
                                <?php if ($image['file_exists']): ?>
                                    <span class="success"><i class="fas fa-check-circle"></i> มี</span>
                                <?php else: ?>
                                    <span class="error"><i class="fas fa-times-circle"></i> ไม่มี</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($is_sample): ?>
                                    <span class="error">ตัวอย่าง</span>
                                <?php else: ?>
                                    <span class="success">อัปโหลดจริง</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="btn-group">
            <?php if ($sample_count > 0): ?>
                <button class="btn btn-danger" onclick="deleteAllSample()">
                    <i class="fas fa-trash-alt"></i> ลบข้อมูลตัวอย่างทั้งหมด (ID 3-20)
                </button>
            <?php endif; ?>
            <a href="admin/gallery/" class="btn btn-success">
                <i class="fas fa-upload"></i> อัปโหลดรูปภาพใหม่
            </a>
            <a href="gallery.php" class="btn">
                <i class="fas fa-eye"></i> ดูหน้า Gallery
            </a>
        </div>
    </div>

    <script>
        function deleteAllSample() {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                html: `
                    <p>จะลบข้อมูลตัวอย่าง ID 3-20 ทั้งหมด (<?php echo $sample_count; ?> รายการ)</p>
                    <p style="color: #f87171; margin-top: 10px;">
                        <small>ข้อมูลเหล่านี้เป็นข้อมูลตัวอย่างที่ไฟล์รูปภาพไม่มีอยู่จริง</small>
                    </p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f5576c',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash-alt"></i> ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch('fix-gallery-data-final.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=delete_all_sample'
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
                            <p>${result.value.message}</p>
                            <p style="color: #4ade80; margin-top: 10px;">
                                <small>ตอนนี้ Gallery จะแสดงเฉพาะรูปภาพที่อัปโหลดจริงแล้ว</small>
                            </p>
                        `,
                        icon: 'success',
                        confirmButtonColor: '#667eea',
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>


