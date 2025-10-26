<?php
/**
 * Fix Missing Columns - ตรวจสอบและเพิ่มคอลัมน์ที่ขาดหายไป
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

$fixes = [];
$errors = [];

// ตรวจสอบตาราง categories
$cat_columns = [];
$result = $conn->query("SHOW COLUMNS FROM categories");
while ($row = $result->fetch_assoc()) {
    $cat_columns[] = $row['Field'];
}

// ตรวจสอบและเพิ่มคอลัมน์ที่จำเป็นสำหรับ categories
$required_cat_columns = [
    'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
    'name' => 'VARCHAR(100) NOT NULL',
    'slug' => 'VARCHAR(150) UNIQUE',
    'description' => 'TEXT',
    'icon' => 'VARCHAR(50)',
    'image' => 'VARCHAR(255)',
    'price_range' => 'VARCHAR(100)',
    'sort_order' => 'INT DEFAULT 0',
    'status' => "ENUM('active', 'inactive') DEFAULT 'active'",
    'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
];

foreach ($required_cat_columns as $column => $definition) {
    if (!in_array($column, $cat_columns)) {
        try {
            if ($column === 'id') {
                // ข้าม id เพราะต้องมีอยู่แล้ว
                continue;
            }
            
            $sql = "ALTER TABLE categories ADD COLUMN `{$column}` {$definition}";
            if ($conn->query($sql)) {
                $fixes[] = "✅ เพิ่มคอลัมน์ '{$column}' ในตาราง 'categories'";
            } else {
                $errors[] = "❌ ไม่สามารถเพิ่มคอลัมน์ '{$column}' ในตาราง 'categories': " . $conn->error;
            }
        } catch (Exception $e) {
            $errors[] = "❌ Error adding '{$column}': " . $e->getMessage();
        }
    } else {
        $fixes[] = "ℹ️ คอลัมน์ '{$column}' มีอยู่แล้วในตาราง 'categories'";
    }
}

// ตรวจสอบตาราง models
$model_columns = [];
$result = $conn->query("SHOW COLUMNS FROM models");
while ($row = $result->fetch_assoc()) {
    $model_columns[] = $row['Field'];
}

// ตรวจสอบและเพิ่มคอลัมน์ที่จำเป็นสำหรับ models
$required_model_columns = [
    'slug' => 'VARCHAR(150) UNIQUE',
    'featured_image' => 'VARCHAR(255)',
    'price' => 'DECIMAL(10,2) DEFAULT 0',
    'height' => 'INT',
    'weight' => 'INT',
    'birth_date' => 'DATE',
    'experience' => 'TEXT',
    'portfolio' => 'TEXT',
    'description' => 'TEXT',
    'status' => "ENUM('active', 'inactive') DEFAULT 'active'",
    'view_count' => 'INT DEFAULT 0'
];

foreach ($required_model_columns as $column => $definition) {
    if (!in_array($column, $model_columns)) {
        try {
            $sql = "ALTER TABLE models ADD COLUMN `{$column}` {$definition}";
            if ($conn->query($sql)) {
                $fixes[] = "✅ เพิ่มคอลัมน์ '{$column}' ในตาราง 'models'";
            } else {
                $errors[] = "❌ ไม่สามารถเพิ่มคอลัมน์ '{$column}' ในตาราง 'models': " . $conn->error;
            }
        } catch (Exception $e) {
            $errors[] = "❌ Error adding '{$column}': " . $e->getMessage();
        }
    } else {
        $fixes[] = "ℹ️ คอลัมน์ '{$column}' มีอยู่แล้วในตาราง 'models'";
    }
}

// ดึงโครงสร้างตารางปัจจุบัน
$cat_structure = [];
$result = $conn->query("SHOW COLUMNS FROM categories");
while ($row = $result->fetch_assoc()) {
    $cat_structure[] = $row;
}

$model_structure = [];
$result = $conn->query("SHOW COLUMNS FROM models");
while ($row = $result->fetch_assoc()) {
    $model_structure[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔧 แก้ไขคอลัมน์ที่ขาดหายไป</title>
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
            margin: 10px 0;
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 1em;
            line-height: 1.5;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
        .section {
            margin: 30px 0;
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
        }
        .section h2 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 1.8em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #667eea;
            color: white;
            font-weight: bold;
        }
        .field-name {
            color: #667eea;
            font-weight: bold;
            font-family: 'Courier New', monospace;
        }
        .type {
            color: #28a745;
            font-family: 'Courier New', monospace;
        }
        .btn-group {
            text-align: center;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #eee;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            margin: 5px;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        .summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        .summary h2 {
            color: white;
            font-size: 2em;
            margin-bottom: 15px;
        }
        .summary p {
            font-size: 1.2em;
            line-height: 2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-tools"></i> แก้ไขคอลัมน์ที่ขาดหายไป</h1>
        <p class="subtitle">ตรวจสอบและเพิ่มคอลัมน์ที่จำเป็นในตาราง categories และ models</p>
        
        <div class="summary">
            <h2><i class="fas fa-check-circle"></i> สรุปผลการแก้ไข</h2>
            <p>
                <strong>การแก้ไข:</strong> <?php echo count($fixes); ?> รายการ<br>
                <?php if (!empty($errors)): ?>
                    <strong>ข้อผิดพลาด:</strong> <?php echo count($errors); ?> รายการ
                <?php else: ?>
                    <strong>สถานะ:</strong> ✅ แก้ไขเสร็จสมบูรณ์
                <?php endif; ?>
            </p>
        </div>
        
        <div class="section">
            <h2><i class="fas fa-clipboard-check"></i> ผลการตรวจสอบและแก้ไข</h2>
            
            <?php foreach ($fixes as $fix): ?>
                <div class="result-box <?php echo strpos($fix, 'ℹ️') === 0 ? 'info' : 'success'; ?>">
                    <?php echo $fix; ?>
                </div>
            <?php endforeach; ?>
            
            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <div class="result-box error">
                        <?php echo $error; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h2><i class="fas fa-database"></i> โครงสร้างตาราง categories</h2>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Null</th>
                        <th>Key</th>
                        <th>Default</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cat_structure as $col): ?>
                        <tr>
                            <td class="field-name"><?php echo $col['Field']; ?></td>
                            <td class="type"><?php echo $col['Type']; ?></td>
                            <td><?php echo $col['Null']; ?></td>
                            <td><?php echo $col['Key']; ?></td>
                            <td><?php echo $col['Default'] ?? 'NULL'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h2><i class="fas fa-database"></i> โครงสร้างตาราง models</h2>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Null</th>
                        <th>Key</th>
                        <th>Default</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($model_structure as $col): ?>
                        <tr>
                            <td class="field-name"><?php echo $col['Field']; ?></td>
                            <td class="type"><?php echo $col['Type']; ?></td>
                            <td><?php echo $col['Null']; ?></td>
                            <td><?php echo $col['Key']; ?></td>
                            <td><?php echo $col['Default'] ?? 'NULL'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="btn-group">
            <a href="check-table-structure.php" class="btn">
                <i class="fas fa-search"></i> ดูโครงสร้างตารางทั้งหมด
            </a>
            <a href="seed-categories.php" class="btn">
                <i class="fas fa-folder-plus"></i> สร้างหมวดหมู่
            </a>
            <a href="seed-models.php" class="btn">
                <i class="fas fa-user-plus"></i> สร้างโมเดล 100 รายการ
            </a>
        </div>
    </div>
</body>
</html>




