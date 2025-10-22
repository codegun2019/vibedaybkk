<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// ตรวจสอบก่อนลบ
$sql_check = "SELECT COUNT(*) as total FROM gallery";
$result_check = $conn->query($sql_check);
$total_before = $result_check->fetch_assoc()['total'];

// ตรวจสอบรูปภาพที่เป็นตัวอย่าง
$sql_sample = "SELECT COUNT(*) as total FROM gallery WHERE 
    image LIKE '%unsplash%' OR 
    image LIKE '%placeholder%' OR 
    image LIKE '%nature-photo%' OR 
    image LIKE '%beauty-%' OR 
    image LIKE '%workshop%' OR 
    image LIKE '%skincare%' OR
    image LIKE '%street-fashion%' OR
    image LIKE '%portrait-%' OR
    image LIKE '%landscape-%' OR
    image LIKE '%event-%' OR
    image LIKE '%hair-%'";
$result_sample = $conn->query($sql_sample);
$total_sample = $result_sample->fetch_assoc()['total'];

// ตรวจสอบรูปภาพที่อัปโหลดจริง
$sql_real = "SELECT COUNT(*) as total FROM gallery WHERE 
    image NOT LIKE '%unsplash%' AND 
    image NOT LIKE '%placeholder%' AND 
    image NOT LIKE '%nature-photo%' AND 
    image NOT LIKE '%beauty-%' AND 
    image NOT LIKE '%workshop%' AND 
    image NOT LIKE '%skincare%' AND
    image NOT LIKE '%street-fashion%' AND
    image NOT LIKE '%portrait-%' AND
    image NOT LIKE '%landscape-%' AND
    image NOT LIKE '%event-%' AND
    image NOT LIKE '%hair-%'";
$result_real = $conn->query($sql_real);
$total_real = $result_real->fetch_assoc()['total'];

// ลบข้อมูลตัวอย่าง
$sql_delete = "DELETE FROM gallery WHERE 
    image LIKE '%unsplash%' OR 
    image LIKE '%placeholder%' OR 
    image LIKE '%nature-photo%' OR 
    image LIKE '%beauty-%' OR 
    image LIKE '%workshop%' OR 
    image LIKE '%skincare%' OR
    image LIKE '%street-fashion%' OR
    image LIKE '%portrait-%' OR
    image LIKE '%landscape-%' OR
    image LIKE '%event-%' OR
    image LIKE '%hair-%'";
$conn->query($sql_delete);
$deleted_count = $conn->affected_rows;

// ตรวจสอบหลังลบ
$sql_check_after = "SELECT COUNT(*) as total FROM gallery";
$result_check_after = $conn->query($sql_check_after);
$total_after = $result_check_after->fetch_assoc()['total'];

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ทำความสะอาดฐานข้อมูล Gallery</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Noto Sans Thai', sans-serif; 
            background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
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
        .icon {
            text-align: center;
            font-size: 5rem;
            margin-bottom: 20px;
        }
        .success { color: #22c55e; }
        .warning { color: #f59e0b; }
        .info { color: #3b82f6; }
        .stats {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
        }
        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .stat-row:last-child {
            border-bottom: none;
        }
        .stat-label {
            font-size: 1.1rem;
            color: #666;
        }
        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
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
            font-size: 1.1rem;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .btn-success {
            background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
        }
        .btn-group {
            text-align: center;
            margin-top: 30px;
        }
        .message {
            background: #e0f2fe;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .message h3 {
            color: #3b82f6;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon success">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>ทำความสะอาดสำเร็จ!</h1>
        
        <div class="stats">
            <div class="stat-row">
                <span class="stat-label">รูปภาพทั้งหมดก่อนลบ:</span>
                <span class="stat-value info"><?php echo $total_before; ?> รายการ</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">รูปภาพตัวอย่างที่ลบ:</span>
                <span class="stat-value warning"><?php echo $deleted_count; ?> รายการ</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">รูปภาพที่อัปโหลดจริง:</span>
                <span class="stat-value success"><?php echo $total_real; ?> รายการ</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">รูปภาพคงเหลือ:</span>
                <span class="stat-value success"><?php echo $total_after; ?> รายการ</span>
            </div>
        </div>
        
        <?php if ($total_after == 0): ?>
            <div class="message">
                <h3><i class="fas fa-info-circle"></i> ข้อมูลว่างเปล่า</h3>
                <p>ไม่มีรูปภาพในฐานข้อมูล กรุณาไปอัปโหลดรูปภาพใหม่ที่ Admin Gallery</p>
            </div>
        <?php else: ?>
            <div class="message">
                <h3><i class="fas fa-check-circle"></i> พร้อมใช้งาน</h3>
                <p>ตอนนี้ Gallery จะแสดงเฉพาะรูปภาพที่อัปโหลดจริงแล้ว (<?php echo $total_after; ?> รายการ)</p>
            </div>
        <?php endif; ?>
        
        <div class="btn-group">
            <a href="admin/gallery/" class="btn btn-success">
                <i class="fas fa-upload"></i> อัปโหลดรูปภาพใหม่
            </a>
            <a href="gallery.php" class="btn">
                <i class="fas fa-eye"></i> ดูหน้า Gallery
            </a>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
