<?php
define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

// ตรวจสอบสถานะตารางและคอลัมน์
$status = [];

// ตรวจสอบ categories
$cat_columns = [];
$result = $conn->query("SHOW COLUMNS FROM categories");
while ($row = $result->fetch_assoc()) {
    $cat_columns[] = $row['Field'];
}
$status['categories'] = [
    'has_slug' => in_array('slug', $cat_columns),
    'has_image' => in_array('image', $cat_columns),
    'total_columns' => count($cat_columns),
    'count' => $conn->query("SELECT COUNT(*) as c FROM categories")->fetch_assoc()['c']
];

// ตรวจสอบ models
$model_columns = [];
$result = $conn->query("SHOW COLUMNS FROM models");
while ($row = $result->fetch_assoc()) {
    $model_columns[] = $row['Field'];
}
$status['models'] = [
    'has_slug' => in_array('slug', $model_columns),
    'has_featured_image' => in_array('featured_image', $model_columns),
    'has_birth_date' => in_array('birth_date', $model_columns),
    'total_columns' => count($model_columns),
    'count' => $conn->query("SELECT COUNT(*) as c FROM models")->fetch_assoc()['c']
];

$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📚 คู่มือการติดตั้ง - VIBEDAYBKK</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #667eea;
            font-size: 3em;
            margin-bottom: 20px;
            text-align: center;
        }
        .step {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            margin: 25px 0;
            border-left: 6px solid #667eea;
        }
        .step h2 {
            color: #667eea;
            font-size: 2em;
            margin-bottom: 15px;
        }
        .step p {
            font-size: 1.1em;
            line-height: 1.8;
            color: #555;
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 18px 35px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1em;
            margin: 10px 5px;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        .status {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin: 15px 0;
            border: 2px solid #ddd;
        }
        .status.ready {
            border-color: #28a745;
            background: #d4edda;
        }
        .status.pending {
            border-color: #ffc107;
            background: #fff3cd;
        }
        .check {
            display: flex;
            align-items: center;
            padding: 10px 0;
            font-size: 1.1em;
        }
        .check i {
            margin-right: 10px;
            font-size: 1.3em;
        }
        .yes { color: #28a745; }
        .no { color: #dc3545; }
        .summary {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            margin: 30px 0;
        }
        .summary h2 {
            font-size: 2.5em;
            margin-bottom: 20px;
        }
        code {
            background: #f8f9fa;
            padding: 3px 8px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            color: #667eea;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-rocket"></i> คู่มือการติดตั้งสมบูรณ์</h1>
        
        <div class="summary">
            <h2><i class="fas fa-check-circle"></i> สถานะระบบ</h2>
            <p style="font-size: 1.3em; line-height: 2;">
                <strong>หมวดหมู่:</strong> <?php echo $status['categories']['count']; ?> รายการ 
                (<?php echo $status['categories']['total_columns']; ?> คอลัมน์)<br>
                <strong>โมเดล:</strong> <?php echo $status['models']['count']; ?> รายการ 
                (<?php echo $status['models']['total_columns']; ?> คอลัมน์)
            </p>
        </div>
        
        <!-- สถานะคอลัมน์ -->
        <div class="step">
            <h2><i class="fas fa-database"></i> สถานะโครงสร้างตาราง</h2>
            
            <div class="status <?php echo ($status['categories']['has_slug'] && $status['categories']['has_image']) ? 'ready' : 'pending'; ?>">
                <h3 style="color: #667eea; margin-bottom: 15px;">📋 ตาราง categories</h3>
                <div class="check">
                    <i class="fas fa-<?php echo $status['categories']['has_slug'] ? 'check-circle yes' : 'times-circle no'; ?>"></i>
                    <span>slug - <?php echo $status['categories']['has_slug'] ? 'มีแล้ว ✅' : 'ยังไม่มี ⚠️'; ?></span>
                </div>
                <div class="check">
                    <i class="fas fa-<?php echo $status['categories']['has_image'] ? 'check-circle yes' : 'times-circle no'; ?>"></i>
                    <span>image - <?php echo $status['categories']['has_image'] ? 'มีแล้ว ✅' : 'ยังไม่มี ⚠️'; ?></span>
                </div>
            </div>
            
            <div class="status <?php echo ($status['models']['has_slug'] && $status['models']['has_featured_image']) ? 'ready' : 'pending'; ?>">
                <h3 style="color: #667eea; margin-bottom: 15px;">👤 ตาราง models</h3>
                <div class="check">
                    <i class="fas fa-<?php echo $status['models']['has_slug'] ? 'check-circle yes' : 'times-circle no'; ?>"></i>
                    <span>slug - <?php echo $status['models']['has_slug'] ? 'มีแล้ว ✅' : 'ยังไม่มี ⚠️'; ?></span>
                </div>
                <div class="check">
                    <i class="fas fa-<?php echo $status['models']['has_featured_image'] ? 'check-circle yes' : 'times-circle no'; ?>"></i>
                    <span>featured_image - <?php echo $status['models']['has_featured_image'] ? 'มีแล้ว ✅' : 'ยังไม่มี ⚠️'; ?></span>
                </div>
                <div class="check">
                    <i class="fas fa-<?php echo $status['models']['has_birth_date'] ? 'check-circle yes' : 'times-circle no'; ?>"></i>
                    <span>birth_date - <?php echo $status['models']['has_birth_date'] ? 'มีแล้ว ✅' : 'ยังไม่มี ⚠️'; ?></span>
                </div>
            </div>
        </div>
        
        <!-- ขั้นตอนที่ 1 -->
        <div class="step">
            <h2><i class="fas fa-1"></i> ขั้นตอนที่ 1: แก้ไขโครงสร้างตาราง</h2>
            <p>
                เพิ่มคอลัมน์ที่จำเป็น (<code>slug</code>, <code>image</code>, <code>featured_image</code>, <code>birth_date</code>) 
                ลงในตาราง categories และ models
            </p>
            <div style="margin-top: 20px;">
                <a href="fix-missing-columns.php" class="btn">
                    <i class="fas fa-wrench"></i> เพิ่มคอลัมน์ที่ขาดหายไป
                </a>
                <a href="check-table-structure.php" class="btn">
                    <i class="fas fa-search"></i> ตรวจสอบโครงสร้าง
                </a>
            </div>
        </div>
        
        <!-- ขั้นตอนที่ 2 -->
        <div class="step">
            <h2><i class="fas fa-2"></i> ขั้นตอนที่ 2: สร้างหมวดหมู่โมเดล</h2>
            <p>
                สร้างหมวดหมู่ 8 ประเภท: นางแบบ, พรีเซ็นเตอร์, โมเดลผู้ชาย, Kids Model, Fitness Model, 
                Plus Size Model, MC & พิธีกร, Commercial Model
            </p>
            <div style="margin-top: 20px;">
                <a href="seed-categories.php" class="btn">
                    <i class="fas fa-folder-plus"></i> สร้างหมวดหมู่ (8 รายการ)
                </a>
            </div>
            <p style="margin-top: 15px; color: #666;">
                ✨ <strong>หมายเหตุ:</strong> ระบบจะตรวจสอบคอลัมน์อัตโนมัติและปรับการทำงานให้เหมาะสม
            </p>
        </div>
        
        <!-- ขั้นตอนที่ 3 -->
        <div class="step">
            <h2><i class="fas fa-3"></i> ขั้นตอนที่ 3: สร้างข้อมูลโมเดลตัวอย่าง</h2>
            <p>
                สร้างข้อมูลโมเดล 100 รายการ พร้อมชื่อภาษาไทย, ประสบการณ์, portfolio, รูปภาพ
            </p>
            <div style="margin-top: 20px;">
                <a href="seed-models.php" class="btn">
                    <i class="fas fa-users"></i> สร้างโมเดล 100 รายการ
                </a>
            </div>
            <p style="margin-top: 15px; color: #666;">
                ⚙️ <strong>ตัวเลือก:</strong> ปรับจำนวน, ช่วงราคา, เลือกใช้รูป Placeholder
            </p>
        </div>
        
        <!-- เครื่องมือเพิ่มเติม -->
        <div class="step">
            <h2><i class="fas fa-toolbox"></i> เครื่องมือเพิ่มเติม</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px;">
                <a href="show-database-structure.php" class="btn" style="text-align: center;">
                    <i class="fas fa-database"></i><br>ดูโครงสร้าง DB
                </a>
                <a href="DATABASE_EXPLAINED.html" class="btn" style="text-align: center;">
                    <i class="fas fa-book"></i><br>คู่มือฐานข้อมูล
                </a>
                <a href="/" class="btn" style="text-align: center;">
                    <i class="fas fa-home"></i><br>หน้าแรก
                </a>
                <a href="/admin/" class="btn" style="text-align: center;">
                    <i class="fas fa-cog"></i><br>Admin Panel
                </a>
            </div>
        </div>
        
        <!-- สรุป -->
        <div style="background: #f8f9fa; padding: 30px; border-radius: 15px; margin-top: 30px;">
            <h3 style="color: #667eea; margin-bottom: 20px; font-size: 1.8em;">
                <i class="fas fa-lightbulb"></i> สรุปการแก้ไข
            </h3>
            <ul style="line-height: 2; font-size: 1.1em; color: #555;">
                <li>✅ แก้ไข error <code>Unknown column 'image' in 'field list'</code></li>
                <li>✅ แก้ไข error <code>Unknown column 'slug' in 'where clause'</code></li>
                <li>✅ แก้ไข error <code>Incorrect string value</code> (slug encoding)</li>
                <li>✅ ระบบตรวจสอบคอลัมน์อัตโนมัติก่อนเพิ่มข้อมูล</li>
                <li>✅ รองรับทั้งโครงสร้างเดิมและโครงสร้างใหม่</li>
                <li>✅ แปลงภาษาไทยเป็นภาษาอังกฤษใน slug อัตโนมัติ</li>
                <li>✅ ไฟล์ที่แก้ไข: <code>seed-categories-api.php</code>, <code>seed-models-api.php</code>, <code>fix-add-slug-columns.php</code></li>
            </ul>
        </div>
    </div>
</body>
</html>


