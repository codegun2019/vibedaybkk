<?php
/**
 * Fix - เพิ่มคอลัมน์ slug ให้กับตาราง categories และ models
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

$errors = [];
$success = [];

// ตรวจสอบและเพิ่มคอลัมน์ slug ในตาราง categories
$check_categories = $conn->query("SHOW COLUMNS FROM categories LIKE 'slug'");
if ($check_categories->num_rows === 0) {
    // ไม่มีคอลัมน์ slug ในตาราง categories
    $sql = "ALTER TABLE categories ADD COLUMN slug VARCHAR(150) NULL AFTER name";
    if ($conn->query($sql)) {
        $success[] = "✅ เพิ่มคอลัมน์ 'slug' ในตาราง 'categories' สำเร็จ";
        
        // เพิ่ม UNIQUE index
        $conn->query("ALTER TABLE categories ADD UNIQUE INDEX idx_slug (slug)");
        $success[] = "✅ เพิ่ม UNIQUE INDEX สำหรับ slug ในตาราง 'categories'";
    } else {
        $errors[] = "❌ ไม่สามารถเพิ่มคอลัมน์ slug ในตาราง categories: " . $conn->error;
    }
} else {
    $success[] = "ℹ️ ตาราง 'categories' มีคอลัมน์ 'slug' อยู่แล้ว";
}

// ตรวจสอบและเพิ่มคอลัมน์ slug ในตาราง models
$check_models = $conn->query("SHOW COLUMNS FROM models LIKE 'slug'");
if ($check_models->num_rows === 0) {
    // ไม่มีคอลัมน์ slug ในตาราง models
    $sql = "ALTER TABLE models ADD COLUMN slug VARCHAR(150) NULL AFTER name";
    if ($conn->query($sql)) {
        $success[] = "✅ เพิ่มคอลัมน์ 'slug' ในตาราง 'models' สำเร็จ";
        
        // เพิ่ม UNIQUE index
        $conn->query("ALTER TABLE models ADD UNIQUE INDEX idx_slug (slug)");
        $success[] = "✅ เพิ่ม UNIQUE INDEX สำหรับ slug ในตาราง 'models'";
    } else {
        $errors[] = "❌ ไม่สามารถเพิ่มคอลัมน์ slug ในตาราง models: " . $conn->error;
    }
} else {
    $success[] = "ℹ️ ตาราง 'models' มีคอลัมน์ 'slug' อยู่แล้ว";
}

// สร้าง slug สำหรับข้อมูลที่มีอยู่แล้ว (ถ้ามี)
function createSlug($text, $id) {
    // แปลงภาษาไทยเป็นภาษาอังกฤษ (Romanization)
    $thai_to_roman = [
        'ก' => 'k', 'ข' => 'kh', 'ค' => 'kh', 'ฆ' => 'kh',
        'ง' => 'ng', 'จ' => 'ch', 'ฉ' => 'ch', 'ช' => 'ch',
        'ซ' => 's', 'ฌ' => 'ch', 'ญ' => 'y', 'ฎ' => 'd',
        'ฏ' => 't', 'ฐ' => 'th', 'ฑ' => 'th', 'ฒ' => 'th',
        'ณ' => 'n', 'ด' => 'd', 'ต' => 't', 'ถ' => 'th',
        'ท' => 'th', 'ธ' => 'th', 'น' => 'n', 'บ' => 'b',
        'ป' => 'p', 'ผ' => 'ph', 'ฝ' => 'f', 'พ' => 'ph',
        'ฟ' => 'f', 'ภ' => 'ph', 'ม' => 'm', 'ย' => 'y',
        'ร' => 'r', 'ล' => 'l', 'ว' => 'w', 'ศ' => 's',
        'ษ' => 's', 'ส' => 's', 'ห' => 'h', 'ฬ' => 'l',
        'อ' => 'o', 'ฮ' => 'h',
        'ะ' => 'a', 'ั' => 'a', 'า' => 'a', 'ำ' => 'am',
        'ิ' => 'i', 'ี' => 'i', 'ึ' => 'ue', 'ื' => 'ue',
        'ุ' => 'u', 'ู' => 'u', 'เ' => 'e', 'แ' => 'ae',
        'โ' => 'o', 'ใ' => 'ai', 'ไ' => 'ai', 'ๅ' => '',
        '่' => '', '้' => '', '๊' => '', '๋' => '', '์' => '',
        'ํ' => '', 'ๆ' => '', '฿' => 'baht',
        // พยัญชนะควบ
        'กว' => 'kw', 'ขว' => 'khw', 'คว' => 'khw'
    ];
    
    // แปลงเป็นตัวพิมพ์เล็ก
    $text = mb_strtolower($text, 'UTF-8');
    
    // แทนที่อักษรไทยด้วยภาษาอังกฤษ
    $slug = strtr($text, $thai_to_roman);
    
    // ลบอักขระที่เหลือที่ไม่ใช่ a-z, 0-9
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    
    // ลบขีดซ้ำซ้อน
    $slug = preg_replace('/-+/', '-', $slug);
    
    // ลบขีดหน้าหลัง
    $slug = trim($slug, '-');
    
    // ถ้า slug ว่าง (กรณีแปลงไม่ได้) ให้ใช้ item-id
    if (empty($slug)) {
        $slug = 'item-' . $id;
    } else {
        $slug = $slug . '-' . $id;
    }
    
    return $slug;
}

// อัพเดท slug สำหรับ categories ที่มี slug เป็น NULL (ถ้ามีคอลัมน์ slug)
$check_slug = $conn->query("SHOW COLUMNS FROM categories LIKE 'slug'");
if ($check_slug && $check_slug->num_rows > 0) {
    $categories_result = $conn->query("SELECT id, name FROM categories WHERE slug IS NULL OR slug = ''");
    if ($categories_result && $categories_result->num_rows > 0) {
        $updated_cats = 0;
        while ($row = $categories_result->fetch_assoc()) {
            try {
                $slug = createSlug($row['name'], $row['id']);
                $stmt = $conn->prepare("UPDATE categories SET slug = ? WHERE id = ?");
                $stmt->bind_param('si', $slug, $row['id']);
                if ($stmt->execute()) {
                    $updated_cats++;
                }
                $stmt->close();
            } catch (Exception $e) {
                $errors[] = "⚠️ ไม่สามารถอัพเดท slug สำหรับ category id {$row['id']}: " . $e->getMessage();
            }
        }
        if ($updated_cats > 0) {
            $success[] = "✅ อัพเดท slug สำหรับ categories: {$updated_cats} รายการ";
        }
    }
}

// อัพเดท slug สำหรับ models ที่มี slug เป็น NULL (ถ้ามีคอลัมน์ slug)
$check_slug_models = $conn->query("SHOW COLUMNS FROM models LIKE 'slug'");
if ($check_slug_models && $check_slug_models->num_rows > 0) {
    $models_result = $conn->query("SELECT id, name FROM models WHERE slug IS NULL OR slug = ''");
    if ($models_result && $models_result->num_rows > 0) {
        $updated_models = 0;
        while ($row = $models_result->fetch_assoc()) {
            try {
                $slug = createSlug($row['name'], $row['id']);
                $stmt = $conn->prepare("UPDATE models SET slug = ? WHERE id = ?");
                $stmt->bind_param('si', $slug, $row['id']);
                if ($stmt->execute()) {
                    $updated_models++;
                }
                $stmt->close();
            } catch (Exception $e) {
                $errors[] = "⚠️ ไม่สามารถอัพเดท slug สำหรับ model id {$row['id']}: " . $e->getMessage();
            }
        }
        if ($updated_models > 0) {
            $success[] = "✅ อัพเดท slug สำหรับ models: {$updated_models} รายการ";
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔧 แก้ไข - เพิ่มคอลัมน์ Slug</title>
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
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #667eea;
            font-size: 2.5em;
            margin-bottom: 10px;
            text-align: center;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1em;
        }
        .result-box {
            margin: 15px 0;
            padding: 15px 20px;
            border-radius: 10px;
            font-size: 1.1em;
            line-height: 1.6;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 2px solid #bee5eb;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            margin: 10px 5px;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        .btn-group {
            text-align: center;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-tools"></i> แก้ไขเสร็จสิ้น</h1>
        <p class="subtitle">เพิ่มคอลัมน์ slug สำหรับตาราง categories และ models</p>
        
        <?php if (!empty($success)): ?>
            <?php foreach ($success as $msg): ?>
                <div class="result-box <?php echo strpos($msg, 'ℹ️') === 0 ? 'info' : 'success'; ?>">
                    <?php echo $msg; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $msg): ?>
                <div class="result-box error">
                    <?php echo $msg; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if (empty($errors)): ?>
            <div class="result-box success" style="margin-top: 30px; font-size: 1.2em; text-align: center;">
                <i class="fas fa-check-circle" style="font-size: 3em; display: block; margin-bottom: 15px;"></i>
                <strong>🎉 แก้ไขเสร็จสมบูรณ์!</strong><br>
                ตอนนี้คุณสามารถสร้างหมวดหมู่และโมเดลได้แล้ว
            </div>
        <?php endif; ?>
        
        <div class="btn-group">
            <a href="seed-categories.php" class="btn">
                <i class="fas fa-folder-plus"></i> สร้างหมวดหมู่
            </a>
            <a href="seed-models.php" class="btn">
                <i class="fas fa-user-plus"></i> สร้างโมเดล 100 รายการ
            </a>
            <a href="show-database-structure.php" class="btn">
                <i class="fas fa-database"></i> ดูโครงสร้าง DB
            </a>
        </div>
    </div>
</body>
</html>

