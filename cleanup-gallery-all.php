<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// ตรวจสอบว่ามีการส่ง request มาหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        if ($_POST['action'] === 'delete_all_problems') {
            // ลบข้อมูลที่มีปัญหาทั้งหมด
            $sql = "DELETE FROM gallery WHERE 
                    image LIKE '%unsplash%' OR 
                    image LIKE '%placeholder%' OR 
                    image LIKE '%nature-photo%' OR 
                    image LIKE '%beauty%' OR 
                    image LIKE '%workshop%' OR 
                    image LIKE '%skincare%' OR
                    image LIKE '%street-fashion%' OR
                    image LIKE '%portrait%' OR
                    image LIKE '%landscape%' OR
                    image LIKE '%event%'";
            
            $result = $conn->query($sql);
            $deleted_count = $conn->affected_rows;
            
            echo json_encode([
                'success' => true,
                'deleted_count' => $deleted_count,
                'message' => "ลบข้อมูลที่มีปัญหาแล้ว $deleted_count รายการ"
            ]);
            
        } elseif ($_POST['action'] === 'delete_specific' && isset($_POST['id'])) {
            $id = intval($_POST['id']);
            
            $sql = "DELETE FROM gallery WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $id);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'ลบข้อมูลสำเร็จ'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการลบ: ' . $stmt->error
                ]);
            }
            
            $stmt->close();
            
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Action ไม่ถูกต้อง'
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ]);
    }
    
    $conn->close();
    exit;
}

// ตรวจสอบข้อมูลในฐานข้อมูล
$sql = "SELECT * FROM gallery ORDER BY created_at DESC";
$result = $conn->query($sql);
$gallery_data = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $gallery_data[] = $row;
    }
}

// ตรวจสอบไฟล์ในโฟลเดอร์
$gallery_folder = __DIR__ . '/uploads/gallery/';
$existing_files = [];
if (is_dir($gallery_folder)) {
    $files = scandir($gallery_folder);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && !is_dir($gallery_folder . $file)) {
            $existing_files[] = $file;
        }
    }
}

// วิเคราะห์ปัญหา
$problems = [];
foreach ($gallery_data as $item) {
    $image_path = $item['image'];
    $filename = basename($image_path);
    
    $is_external = filter_var($image_path, FILTER_VALIDATE_URL);
    $is_sample = (
        strpos($image_path, 'unsplash') !== false || 
        strpos($image_path, 'placeholder') !== false ||
        strpos($image_path, 'nature-photo') !== false ||
        strpos($image_path, 'beauty') !== false ||
        strpos($image_path, 'workshop') !== false ||
        strpos($image_path, 'skincare') !== false ||
        strpos($image_path, 'street-fashion') !== false ||
        strpos($image_path, 'portrait') !== false ||
        strpos($image_path, 'landscape') !== false ||
        strpos($image_path, 'event') !== false
    );
    
    $file_exists = false;
    if (!$is_external) {
        $file_exists = in_array($filename, $existing_files);
    }
    
    if ($is_external || $is_sample || !$file_exists) {
        $problems[] = $item;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ทำความสะอาด Gallery</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Noto Sans Thai', sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
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
        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
        }
        .problem-list {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            max-height: 400px;
            overflow-y: auto;
        }
        .problem-item {
            background: white;
            border-left: 4px solid #f5576c;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .problem-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        .problem-path {
            font-family: monospace;
            font-size: 0.85rem;
            color: #666;
            background: #f0f0f0;
            padding: 5px 10px;
            border-radius: 5px;
            margin: 5px 0;
            word-break: break-all;
        }
        .problem-reason {
            color: #f5576c;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        .btn-group {
            text-align: center;
            margin: 30px 0;
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .btn-success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .no-problems {
            text-align: center;
            padding: 60px 20px;
        }
        .no-problems i {
            font-size: 5rem;
            color: #4ade80;
            margin-bottom: 20px;
        }
        .no-problems h2 {
            color: #333;
            margin-bottom: 10px;
        }
        .no-problems p {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-broom"></i> ทำความสะอาด Gallery</h1>
        <p class="subtitle">ลบข้อมูลรูปภาพที่มีปัญหาออกจากฐานข้อมูล</p>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($gallery_data); ?></div>
                <div class="stat-label"><i class="fas fa-database"></i> ทั้งหมด</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($problems); ?></div>
                <div class="stat-label"><i class="fas fa-exclamation-triangle"></i> มีปัญหา</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($gallery_data) - count($problems); ?></div>
                <div class="stat-label"><i class="fas fa-check-circle"></i> ปกติ</div>
            </div>
        </div>
        
        <?php if (count($problems) > 0): ?>
            <h2 style="color: #333; margin: 30px 0 20px 0;">
                <i class="fas fa-list"></i> รายการที่มีปัญหา (<?php echo count($problems); ?> รายการ)
            </h2>
            
            <div class="problem-list">
                <?php foreach ($problems as $problem): ?>
                    <?php
                    $image_path = $problem['image'];
                    $filename = basename($image_path);
                    $is_external = filter_var($image_path, FILTER_VALIDATE_URL);
                    $file_exists = in_array($filename, $existing_files);
                    
                    $reason = '';
                    if ($is_external || strpos($image_path, 'unsplash') !== false || strpos($image_path, 'placeholder') !== false) {
                        $reason = '<i class="fas fa-external-link-alt"></i> ใช้ URL ภายนอก หรือรูปภาพตัวอย่าง';
                    } elseif (!$file_exists) {
                        $reason = '<i class="fas fa-file-excel"></i> ไฟล์ไม่มีอยู่ในโฟลเดอร์';
                    }
                    ?>
                    <div class="problem-item">
                        <div class="problem-title">
                            #<?php echo $problem['id']; ?> - <?php echo htmlspecialchars($problem['title']); ?>
                        </div>
                        <div class="problem-path"><?php echo htmlspecialchars($image_path); ?></div>
                        <div class="problem-reason"><?php echo $reason; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="btn-group">
                <button class="btn btn-danger" onclick="deleteAllProblems()">
                    <i class="fas fa-trash-alt"></i> ลบทั้งหมด (<?php echo count($problems); ?> รายการ)
                </button>
                <a href="admin/gallery/" class="btn btn-success">
                    <i class="fas fa-upload"></i> อัปโหลดรูปภาพใหม่
                </a>
                <a href="gallery.php" class="btn">
                    <i class="fas fa-eye"></i> ดูหน้า Gallery
                </a>
            </div>
        <?php else: ?>
            <div class="no-problems">
                <i class="fas fa-check-circle"></i>
                <h2>ไม่พบปัญหา!</h2>
                <p>ทุกรูปภาพในฐานข้อมูลมีไฟล์อยู่จริงและพร้อมใช้งาน</p>
                
                <div class="btn-group">
                    <a href="admin/gallery/" class="btn btn-success">
                        <i class="fas fa-upload"></i> อัปโหลดรูปภาพเพิ่ม
                    </a>
                    <a href="gallery.php" class="btn">
                        <i class="fas fa-eye"></i> ดูหน้า Gallery
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function deleteAllProblems() {
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                html: 'จะลบรูปภาพที่มีปัญหา <strong><?php echo count($problems); ?></strong> รายการ<br><small style="color: #666;">การดำเนินการนี้ไม่สามารถย้อนกลับได้</small>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f5576c',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash-alt"></i> ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch('cleanup-gallery-all.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=delete_all_problems'
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
                        title: 'สำเร็จ!',
                        html: `<p>${result.value.message}</p><p style="color: #666; font-size: 0.9rem;">กำลังเปลี่ยนเส้นทางไปหน้า Gallery...</p>`,
                        icon: 'success',
                        confirmButtonColor: '#667eea',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = 'gallery.php';
                    });
                }
            });
        }
    </script>
</body>
</html>
