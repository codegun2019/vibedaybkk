<?php
/**
 * Show Database Structure
 * แสดงโครงสร้างฐานข้อมูลทั้งหมด
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

// ดึงรายชื่อตารางทั้งหมด
$tables_result = $conn->query("SHOW TABLES");
$tables = [];
while ($row = $tables_result->fetch_array()) {
    $tables[] = $row[0];
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📊 โครงสร้างฐานข้อมูล VIBEDAYBKK</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #e2e8f0;
            padding: 20px;
        }
        .container {
            max-width: 1600px;
            margin: 0 auto;
        }
        .header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            padding: 40px;
            border-radius: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        .header h1 { font-size: 3em; margin-bottom: 10px; }
        .summary {
            background: #334155;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .summary-item {
            text-align: center;
            padding: 20px;
            background: #475569;
            border-radius: 10px;
        }
        .summary-item .number {
            font-size: 3em;
            font-weight: bold;
            color: #8b5cf6;
        }
        .summary-item .label {
            font-size: 1.1em;
            color: #cbd5e1;
            margin-top: 10px;
        }
        .table-card {
            background: #334155;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            border-left: 5px solid #8b5cf6;
        }
        .table-card h2 {
            color: #a78bfa;
            margin-bottom: 15px;
            font-size: 1.8em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .table-card .description {
            color: #cbd5e1;
            margin-bottom: 20px;
            font-size: 1.1em;
            line-height: 1.6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background: #1e293b;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #475569;
        }
        th {
            background: #0f172a;
            color: #a78bfa;
            font-weight: bold;
        }
        td {
            color: #e2e8f0;
        }
        .field-name {
            color: #60a5fa;
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        .type {
            color: #34d399;
            font-family: 'Courier New', monospace;
        }
        .key {
            color: #fbbf24;
            font-size: 0.9em;
        }
        .record-count {
            background: #059669;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
        }
        .icon { font-size: 1.3em; }
        .search-box {
            background: #334155;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        #search-input {
            width: 100%;
            max-width: 500px;
            padding: 12px 20px;
            border: 2px solid #475569;
            border-radius: 10px;
            background: #1e293b;
            color: white;
            font-size: 1.1em;
        }
        #search-input:focus {
            outline: none;
            border-color: #8b5cf6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-database"></i> โครงสร้างฐานข้อมูล</h1>
            <p style="font-size: 1.3em; margin-top: 10px;">VIBEDAYBKK Database Structure</p>
            <p style="font-size: 0.9em; margin-top: 10px; opacity: 0.8;">Database: vibedaybkk</p>
        </div>
        
        <div class="summary">
            <div class="summary-item">
                <div class="number"><?php echo count($tables); ?></div>
                <div class="label">ตารางทั้งหมด</div>
            </div>
            <div class="summary-item">
                <div class="number">
                    <?php
                    $total_fields = 0;
                    foreach ($tables as $table) {
                        $fields_result = $conn->query("SHOW COLUMNS FROM `{$table}`");
                        $total_fields += $fields_result->num_rows;
                    }
                    echo $total_fields;
                    ?>
                </div>
                <div class="label">ฟิลด์ทั้งหมด</div>
            </div>
            <div class="summary-item">
                <div class="number">
                    <?php
                    $total_records = 0;
                    foreach ($tables as $table) {
                        $count_result = $conn->query("SELECT COUNT(*) as total FROM `{$table}`");
                        if ($count_result && $row = $count_result->fetch_assoc()) {
                            $total_records += $row['total'];
                        }
                    }
                    echo number_format($total_records);
                    ?>
                </div>
                <div class="label">Records ทั้งหมด</div>
            </div>
        </div>
        
        <div class="search-box">
            <input type="text" id="search-input" placeholder="🔍 ค้นหาตาราง..." onkeyup="searchTables()">
        </div>
        
        <?php 
        // คำอธิบายแต่ละตาราง
        $table_descriptions = [
            'users' => ['icon' => 'fa-users', 'desc' => 'ผู้ใช้งานระบบ - เก็บข้อมูล username, password, role และสิทธิ์การเข้าถึง', 'category' => 'User Management'],
            'roles' => ['icon' => 'fa-user-shield', 'desc' => 'บทบาทผู้ใช้ - กำหนดระดับสิทธิ์ (Programmer, Admin, Editor, Viewer)', 'category' => 'User Management'],
            'permissions' => ['icon' => 'fa-key', 'desc' => 'สิทธิ์การเข้าถึง - ควบคุมว่า role ไหนทำอะไรได้บ้าง (view, create, edit, delete, export)', 'category' => 'User Management'],
            'settings' => ['icon' => 'fa-cog', 'desc' => 'การตั้งค่าระบบ - เก็บค่า config ต่างๆ (Logo, SEO, Social Media, Fonts, Colors)', 'category' => 'Configuration'],
            'menus' => ['icon' => 'fa-bars', 'desc' => 'เมนูนำทาง - จัดการเมนูที่แสดงบนเว็บ (หน้าแรก, เกี่ยวกับเรา, บริการ, ติดต่อ)', 'category' => 'Content'],
            'categories' => ['icon' => 'fa-folder', 'desc' => 'หมวดหมู่โมเดล - แบ่งประเภทบริการโมเดล (นางแบบ, พรีเซ็นเตอร์, ผู้ชาย, ฯลฯ)', 'category' => 'Content'],
            'models' => ['icon' => 'fa-user-tie', 'desc' => 'ข้อมูลโมเดล - เก็บข้อมูลโมเดลทั้งหมด (ชื่อ, รูป, ราคา, รายละเอียด, portfolio)', 'category' => 'Content'],
            'articles' => ['icon' => 'fa-newspaper', 'desc' => 'บทความ - เก็บบทความและข่าวสาร (title, content, featured_image, author)', 'category' => 'Content'],
            'article_categories' => ['icon' => 'fa-tags', 'desc' => 'หมวดหมู่บทความ - แบ่งประเภทบทความ (เคล็ดลับ, ข่าวสาร, แนะนำ)', 'category' => 'Content'],
            'bookings' => ['icon' => 'fa-calendar-check', 'desc' => 'การจองบริการ - เก็บข้อมูลการจองโมเดล (วันที่, เวลา, สถานที่, ราคา, สถานะ)', 'category' => 'Business'],
            'contacts' => ['icon' => 'fa-envelope', 'desc' => 'ข้อความติดต่อ - เก็บข้อความจากฟอร์มติดต่อ (ชื่อ, อีเมล, โทร, รายละเอียดงาน)', 'category' => 'Business'],
            'customer_reviews' => ['icon' => 'fa-star', 'desc' => 'รีวิวลูกค้า - เก็บรีวิวและความคิดเห็นจากลูกค้า (rating, comment, รูปภาพ)', 'category' => 'Content'],
            'gallery' => ['icon' => 'fa-images', 'desc' => 'แกลเลอรี่ - เก็บรูปภาพแกลเลอรี่ (ผลงาน, portfolio, รูปกิจกรรม)', 'category' => 'Media'],
            'homepage_sections' => ['icon' => 'fa-home', 'desc' => 'เนื้อหาหน้าแรก - เก็บ section ต่างๆ ในหน้าแรก (Hero, About, Services, Contact)', 'category' => 'Content'],
            'homepage_features' => ['icon' => 'fa-star', 'desc' => 'จุดเด่น - เก็บจุดเด่นของบริการ (icon, title, description)', 'category' => 'Content'],
            'homepage_gallery' => ['icon' => 'fa-image', 'desc' => 'แกลเลอรี่หน้าแรก - รูปที่แสดงในหน้าแรก', 'category' => 'Media'],
            'activity_logs' => ['icon' => 'fa-history', 'desc' => 'บันทึกกิจกรรม - เก็บ log การทำงานของ user (create, update, delete, login)', 'category' => 'System'],
            'model_images' => ['icon' => 'fa-camera', 'desc' => 'รูปภาพโมเดล - เก็บรูปหลายรูปของแต่ละโมเดล', 'category' => 'Media'],
            'model_requirements' => ['icon' => 'fa-list-check', 'desc' => 'ความต้องการของงาน - เก็บความต้องการสำหรับแต่ละโมเดล', 'category' => 'Content']
        ];
        
        foreach ($tables as $table):
            // ดึงโครงสร้างตาราง
            $columns_result = $conn->query("SHOW FULL COLUMNS FROM `{$table}`");
            $columns = [];
            while ($col = $columns_result->fetch_assoc()) {
                $columns[] = $col;
            }
            
            // นับจำนวน records
            $count_result = $conn->query("SELECT COUNT(*) as total FROM `{$table}`");
            $record_count = ($count_result && $row = $count_result->fetch_assoc()) ? $row['total'] : 0;
            
            $table_info = $table_descriptions[$table] ?? ['icon' => 'fa-table', 'desc' => 'ไม่มีคำอธิบาย', 'category' => 'Other'];
        ?>
        
        <div class="table-card" data-table="<?php echo $table; ?>">
            <h2>
                <i class="fas <?php echo $table_info['icon']; ?> icon"></i>
                <?php echo $table; ?>
                <span class="record-count" style="margin-left: auto; font-size: 0.6em;">
                    <?php echo number_format($record_count); ?> records
                </span>
            </h2>
            
            <div class="description">
                <strong style="color: #fbbf24;">📂 หมวดหมู่:</strong> <?php echo $table_info['category']; ?><br>
                <strong style="color: #60a5fa;">📝 คำอธิบาย:</strong> <?php echo $table_info['desc']; ?>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th style="width: 25%;">ชื่อฟิลด์</th>
                        <th style="width: 20%;">ประเภทข้อมูล</th>
                        <th style="width: 10%;">Null</th>
                        <th style="width: 10%;">Key</th>
                        <th style="width: 15%;">Default</th>
                        <th style="width: 20%;">หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($columns as $col): ?>
                    <tr>
                        <td class="field-name">
                            <?php echo $col['Field']; ?>
                            <?php if ($col['Key'] === 'PRI'): ?>
                                <i class="fas fa-key" style="color: #fbbf24; margin-left: 5px;" title="Primary Key"></i>
                            <?php endif; ?>
                        </td>
                        <td class="type"><?php echo $col['Type']; ?></td>
                        <td><?php echo $col['Null'] === 'YES' ? '✓' : '✗'; ?></td>
                        <td class="key"><?php echo $col['Key'] ?: '-'; ?></td>
                        <td><?php echo $col['Default'] !== null ? htmlspecialchars($col['Default']) : 'NULL'; ?></td>
                        <td><?php echo htmlspecialchars($col['Comment'] ?: '-'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="margin-top: 15px; padding: 15px; background: #475569; border-radius: 8px; font-size: 0.95em;">
                <strong style="color: #a78bfa;">📊 สถิติ:</strong> 
                <span style="color: #cbd5e1;">
                    <?php echo count($columns); ?> ฟิลด์ | 
                    <?php echo number_format($record_count); ?> records
                </span>
            </div>
        </div>
        
        <?php endforeach; ?>
        
        <div style="background: #334155; padding: 30px; border-radius: 15px; text-align: center; margin-top: 40px;">
            <h3 style="color: #a78bfa; margin-bottom: 15px; font-size: 1.8em;">🎯 สรุปฐานข้อมูล</h3>
            <p style="color: #cbd5e1; font-size: 1.2em; line-height: 2;">
                ฐานข้อมูล <strong>vibedaybkk</strong> มีทั้งหมด <strong><?php echo count($tables); ?> ตาราง</strong><br>
                รวม <strong><?php echo number_format($total_fields); ?> ฟิลด์</strong> และ <strong><?php echo number_format($total_records); ?> records</strong><br>
                พร้อมใช้งาน Production! ✅
            </p>
        </div>
    </div>
    
    <script>
        function searchTables() {
            const input = document.getElementById('search-input');
            const filter = input.value.toLowerCase();
            const cards = document.querySelectorAll('.table-card');
            
            cards.forEach(card => {
                const tableName = card.getAttribute('data-table').toLowerCase();
                const text = card.textContent.toLowerCase();
                
                if (tableName.includes(filter) || text.includes(filter)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>

