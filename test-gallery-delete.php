<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// ดึงรูปภาพทั้งหมด
$sql = "SELECT * FROM gallery ORDER BY created_at DESC LIMIT 10";
$images = [];
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ทดสอบการลบรูปภาพ Gallery</title>
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
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }
        .subtitle {
            text-align: center;
            color: #aaa;
            margin-bottom: 30px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .card {
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .card-title {
            font-weight: 600;
            font-size: 1.1rem;
        }
        .card-id {
            background: rgba(102, 126, 234, 0.3);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        .card-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin: 10px 0;
            border: 2px solid rgba(255,255,255,0.1);
        }
        .card-path {
            font-family: monospace;
            font-size: 0.75rem;
            color: #aaa;
            background: rgba(0,0,0,0.3);
            padding: 8px;
            border-radius: 5px;
            margin: 10px 0;
            word-break: break-all;
        }
        .btn-delete {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-family: 'Noto Sans Thai', sans-serif;
            transition: all 0.3s ease;
        }
        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(245, 87, 108, 0.4);
        }
        .status {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            margin: 10px 0;
            text-align: center;
        }
        .status-active {
            background: rgba(74, 222, 128, 0.2);
            color: #4ade80;
        }
        .status-inactive {
            background: rgba(248, 113, 113, 0.2);
            color: #f87171;
        }
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #aaa;
        }
        .no-data i {
            font-size: 5rem;
            margin-bottom: 20px;
            color: #667eea;
        }
        .test-info {
            background: rgba(102, 126, 234, 0.1);
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .test-info h3 {
            margin-bottom: 10px;
            color: #667eea;
        }
        .test-info ul {
            margin-left: 20px;
            color: #ccc;
        }
        .test-info li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-vial"></i> ทดสอบการลบรูปภาพ Gallery</h1>
        <p class="subtitle">ทดสอบฟังก์ชันการลบรูปภาพจากแกลลอรี่</p>
        
        <div class="test-info">
            <h3><i class="fas fa-info-circle"></i> วิธีการทดสอบ:</h3>
            <ul>
                <li>แสดงรูปภาพล่าสุด 10 รายการ</li>
                <li>คลิกปุ่ม "ลบรูปภาพ" เพื่อทดสอบการลบ</li>
                <li>ระบบจะแสดงผลลัพธ์ว่าลบสำเร็จหรือไม่</li>
                <li>ตรวจสอบว่าไฟล์ถูกลบออกจากโฟลเดอร์หรือไม่</li>
            </ul>
        </div>
        
        <?php if (count($images) > 0): ?>
            <div class="grid">
                <?php foreach ($images as $image): ?>
                    <?php
                    $image_path = $image['image'];
                    if (filter_var($image_path, FILTER_VALIDATE_URL)) {
                        $image_url = $image_path;
                    } else {
                        if (strpos($image_path, 'uploads/') === 0) {
                            $image_url = BASE_URL . '/' . $image_path;
                        } else {
                            $image_url = UPLOADS_URL . '/' . $image_path;
                        }
                    }
                    ?>
                    <div class="card" id="card-<?php echo $image['id']; ?>">
                        <div class="card-header">
                            <span class="card-title"><?php echo htmlspecialchars($image['title']); ?></span>
                            <span class="card-id">ID: <?php echo $image['id']; ?></span>
                        </div>
                        
                        <img src="<?php echo $image_url; ?>" 
                             alt="<?php echo htmlspecialchars($image['title']); ?>" 
                             class="card-image"
                             onerror="this.src='https://via.placeholder.com/300x200?text=Image+Not+Found'">
                        
                        <div class="status <?php echo $image['is_active'] == 1 ? 'status-active' : 'status-inactive'; ?>">
                            <?php echo $image['is_active'] == 1 ? '✓ Active' : '✗ Inactive'; ?>
                        </div>
                        
                        <div class="card-path">
                            <strong>Path:</strong> <?php echo htmlspecialchars($image_path); ?>
                        </div>
                        
                        <?php if (!empty($image['category'])): ?>
                            <div class="card-path">
                                <strong>Category:</strong> <?php echo htmlspecialchars($image['category']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <button class="btn-delete" onclick="testDelete(<?php echo $image['id']; ?>, '<?php echo htmlspecialchars($image['title'], ENT_QUOTES); ?>')">
                            <i class="fas fa-trash-alt"></i> ลบรูปภาพ
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-images"></i>
                <h2>ไม่มีรูปภาพในแกลลอรี่</h2>
                <p>กรุณาอัปโหลดรูปภาพก่อนทดสอบ</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function testDelete(id, title) {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                html: `คุณต้องการลบรูปภาพ<br><strong>"${title}"</strong><br><small style="color: #666;">ID: ${id}</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f5576c',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash-alt"></i> ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    console.log('Attempting to delete image ID:', id);
                    
                    return fetch(`admin/gallery/delete.php?id=${id}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response headers:', response.headers);
                        return response.text();
                    })
                    .then(text => {
                        console.log('Raw response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            throw new Error('Invalid JSON response: ' + text.substring(0, 100));
                        }
                    })
                    .then(data => {
                        console.log('Parsed data:', data);
                        if (!data.success) {
                            throw new Error(data.message || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ');
                        }
                        return data;
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        Swal.showValidationMessage(`เกิดข้อผิดพลาด: ${error.message}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Delete successful:', result.value);
                    
                    let detailsHtml = `<p>${result.value.message}</p>`;
                    
                    if (result.value.deleted_files && result.value.deleted_files.length > 0) {
                        detailsHtml += `<p style="color: #4ade80; font-size: 0.9rem; margin-top: 10px;">
                            <strong>ไฟล์ที่ลบ:</strong><br>
                            ${result.value.deleted_files.join('<br>')}
                        </p>`;
                    }
                    
                    if (result.value.file_errors && result.value.file_errors.length > 0) {
                        detailsHtml += `<p style="color: #fbbf24; font-size: 0.9rem; margin-top: 10px;">
                            <strong>คำเตือน:</strong><br>
                            ${result.value.file_errors.join('<br>')}
                        </p>`;
                    }
                    
                    Swal.fire({
                        title: 'ลบสำเร็จ!',
                        html: detailsHtml,
                        icon: 'success',
                        confirmButtonColor: '#667eea',
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => {
                        // Remove card with animation
                        const card = document.getElementById(`card-${id}`);
                        if (card) {
                            card.style.transition = 'all 0.5s ease';
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.8)';
                            setTimeout(() => {
                                card.remove();
                                
                                // Check if no more cards
                                const remainingCards = document.querySelectorAll('.card');
                                if (remainingCards.length === 0) {
                                    location.reload();
                                }
                            }, 500);
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
