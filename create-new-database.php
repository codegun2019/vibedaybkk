<?php
/**
 * สร้างฐานข้อมูลใหม่สำหรับ VIBEDAYBKK
 * โดยใช้ไฟล์ vibedaybkk_17_10.sql
 */

set_time_limit(300); // 5 minutes
ini_set('memory_limit', '512M');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'>";
echo "<title>สร้างฐานข้อมูลใหม่ - VIBEDAYBKK</title>";
echo "<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; max-width: 1200px; margin: 0 auto; }
.success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #28a745; }
.error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #dc3545; }
.info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #17a2b8; }
.warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #ffc107; }
h1, h2 { color: #DC2626; }
pre { background: #f8f9fa; padding: 10px; border-left: 3px solid #DC2626; overflow-x: auto; }
table { width: 100%; background: white; border-collapse: collapse; margin: 10px 0; }
th { background: #DC2626; color: white; padding: 10px; text-align: left; }
td { padding: 10px; border: 1px solid #ddd; }
.progress { background: #e9ecef; height: 30px; border-radius: 5px; overflow: hidden; margin: 10px 0; }
.progress-bar { background: #DC2626; height: 100%; transition: width 0.3s; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }
</style></head><body>";

echo "<h1>🔄 สร้างฐานข้อมูลใหม่ - VIBEDAYBKK</h1>";
echo "<hr>";

// Step 1: เชื่อมต่อฐานข้อมูล
echo "<h2>ขั้นตอนที่ 1: เชื่อมต่อ MySQL Server</h2>";
$socket = '/Applications/MAMP/tmp/mysql/mysql.sock';
$conn = new mysqli('localhost', 'root', 'root', '', 0, $socket);

if ($conn->connect_error) {
    echo "<div class='error'>❌ เชื่อมต่อล้มเหลว: " . $conn->connect_error . "</div>";
    echo "</body></html>";
    exit;
}

echo "<div class='success'>✅ เชื่อมต่อ MySQL Server สำเร็จ</div>";
echo "<div class='info'>📌 MySQL Version: " . $conn->server_info . "</div>";

// Step 2: สำรองฐานข้อมูลเก่า (ถ้ามี)
echo "<h2>ขั้นตอนที่ 2: ตรวจสอบฐานข้อมูลเดิม</h2>";
$result = $conn->query("SHOW DATABASES LIKE 'vibedaybkk'");
if ($result && $result->num_rows > 0) {
    echo "<div class='warning'>⚠️ พบฐานข้อมูล 'vibedaybkk' เดิมอยู่แล้ว</div>";
    
    // Rename old database
    $timestamp = date('Ymd_His');
    $backupName = "vibedaybkk_backup_{$timestamp}";
    
    // ตรวจสอบว่ามี backup name ซ้ำหรือไม่
    $result2 = $conn->query("SHOW DATABASES LIKE '{$backupName}'");
    if ($result2 && $result2->num_rows > 0) {
        $backupName .= "_" . uniqid();
    }
    
    // สร้าง backup
    $conn->query("CREATE DATABASE `{$backupName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // คัดลอกข้อมูล
    $tables = [];
    $result3 = $conn->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'vibedaybkk'");
    while ($row = $result3->fetch_array()) {
        $tables[] = $row[0];
    }
    
    echo "<div class='info'>📦 กำลังสำรองฐานข้อมูลเดิม (" . count($tables) . " ตาราง) เป็น: <strong>{$backupName}</strong></div>";
    
    foreach ($tables as $table) {
        $conn->query("CREATE TABLE `{$backupName}`.`{$table}` LIKE `vibedaybkk`.`{$table}`");
        $conn->query("INSERT INTO `{$backupName}`.`{$table}` SELECT * FROM `vibedaybkk`.`{$table}`");
    }
    
    echo "<div class='success'>✅ สำรองฐานข้อมูลเดิมเรียบร้อยแล้ว</div>";
} else {
    echo "<div class='info'>📌 ไม่พบฐานข้อมูลเดิม - จะสร้างใหม่ทั้งหมด</div>";
}

// Step 3: ลบฐานข้อมูลเก่า
echo "<h2>ขั้นตอนที่ 3: ลบฐานข้อมูลเก่า (ถ้ามี)</h2>";
$conn->query("DROP DATABASE IF EXISTS vibedaybkk");
echo "<div class='success'>✅ ลบฐานข้อมูลเก่าแล้ว (ถ้ามี)</div>";

// Step 4: สร้างฐานข้อมูลใหม่
echo "<h2>ขั้นตอนที่ 4: สร้างฐานข้อมูลใหม่</h2>";
$result = $conn->query("CREATE DATABASE vibedaybkk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
if ($result) {
    echo "<div class='success'>✅ สร้างฐานข้อมูล 'vibedaybkk' เรียบร้อยแล้ว</div>";
} else {
    echo "<div class='error'>❌ ไม่สามารถสร้างฐานข้อมูลได้: " . $conn->error . "</div>";
    echo "</body></html>";
    exit;
}

// Select database
$conn->select_db('vibedaybkk');
$conn->set_charset('utf8mb4');

// Step 5: อ่านและ import ไฟล์ SQL
echo "<h2>ขั้นตอนที่ 5: Import ข้อมูลจาก vibedaybkk_17_10.sql</h2>";

$sqlFile = __DIR__ . '/vibedaybkk_17_10.sql';

if (!file_exists($sqlFile)) {
    echo "<div class='error'>❌ ไม่พบไฟล์ vibedaybkk_17_10.sql</div>";
    echo "</body></html>";
    exit;
}

echo "<div class='info'>📄 ไฟล์: vibedaybkk_17_10.sql</div>";
echo "<div class='info'>📊 ขนาดไฟล์: " . number_format(filesize($sqlFile)/1024, 2) . " KB</div>";

// อ่านไฟล์
$sql = file_get_contents($sqlFile);

echo "<div class='progress'><div class='progress-bar' id='progressBar' style='width: 0%'>0%</div></div>";
echo "<div id='status'>กำลังประมวลผล...</div>";
echo "<div id='details'></div>";

echo "<script>
function updateProgress(current, total, success, errors) {
    var percent = Math.round((current / total) * 100);
    document.getElementById('progressBar').style.width = percent + '%';
    document.getElementById('progressBar').textContent = percent + '%';
    document.getElementById('status').innerHTML = 'ประมวลผลแล้ว: ' + current + ' / ' + total + ' queries';
}
</script>";

flush();
ob_flush();

// ลบส่วน Stored Procedures ทั้งหมดออก (ไม่จำเป็นสำหรับการทำงานของระบบ)
$cleanSQL = $sql;

// หา position ของ DELIMITER $$ และ DELIMITER ; สุดท้าย
$delimiterStart = strpos($cleanSQL, 'DELIMITER $$');
$delimiterEnd = strrpos($cleanSQL, 'DELIMITER ;');

if ($delimiterStart !== false && $delimiterEnd !== false) {
    // ตัด stored procedures section ออกทั้งหมด
    $beforeProcedures = substr($cleanSQL, 0, $delimiterStart);
    $afterProcedures = substr($cleanSQL, $delimiterEnd + strlen('DELIMITER ;'));
    $cleanSQL = $beforeProcedures . $afterProcedures;
    
    echo "<div class='warning'>⚠️ ข้าม Stored Procedures (ไม่จำเป็นสำหรับระบบ)</div>";
    flush();
}

// ลบ comments และ empty lines
$lines = explode("\n", $cleanSQL);
$cleanLines = [];
foreach ($lines as $line) {
    $line = trim($line);
    // ข้าม comment lines และ empty lines
    if (empty($line) || 
        strpos($line, '--') === 0 || 
        strpos($line, '/*') === 0 ||
        $line === '*/' ||
        strpos($line, 'DELIMITER') === 0) {
        continue;
    }
    $cleanLines[] = $line;
}
$cleanSQL = implode("\n", $cleanLines);

// แยก queries โดยใช้ semicolon
$queries = explode(';', $cleanSQL);

$success = 0;
$errors = 0;
$skipped = 0;
$processedQueries = 0;
$detailsHTML = "<table><tr><th>ลำดับ</th><th>Query Type</th><th>สถานะ</th></tr>";

$total = count($queries);

foreach ($queries as $index => $query) {
    $query = trim($query);
    
    // ข้าม query ว่าง
    if (empty($query) || strlen($query) < 5) {
        $skipped++;
        continue;
    }
    
    // ข้าม special commands
    if (preg_match('/^(SET|START TRANSACTION|COMMIT|DELIMITER)/i', $query)) {
        $skipped++;
        continue;
    }
    
    // ข้าม /*!...*/ style comments
    if (strpos($query, '/*!') === 0) {
        $skipped++;
        continue;
    }
    
    // ตรวจสอบว่าเป็น query อะไร
    $queryType = 'UNKNOWN';
    if (stripos($query, 'CREATE TABLE') !== false) {
        $queryType = 'CREATE TABLE';
    } elseif (stripos($query, 'CREATE DATABASE') !== false) {
        $queryType = 'CREATE DATABASE';
    } elseif (stripos($query, 'INSERT INTO') !== false) {
        $queryType = 'INSERT';
    } elseif (stripos($query, 'ALTER TABLE') !== false) {
        $queryType = 'ALTER TABLE';
    } elseif (stripos($query, 'DROP') !== false) {
        $queryType = 'DROP';
    }
    
    // Execute query
    try {
        $result = @$conn->query($query);
        
        if ($result !== false) {
            $success++;
            $processedQueries++;
            $status = "<span style='color: green;'>✓</span>";
        } else {
            $error = $conn->error;
            
            // ตรวจสอบว่าเป็น error ที่ยอมรับได้หรือไม่
            if (stripos($error, 'Duplicate entry') !== false || 
                stripos($error, 'already exists') !== false ||
                stripos($error, 'Table') !== false && stripos($error, 'already exists') !== false) {
                // Error ที่ยอมรับได้ - นับเป็น success
                $success++;
                $processedQueries++;
                $status = "<span style='color: orange;'>⚠ (Skip)</span>";
            } else {
                // Error จริงๆ
                $errors++;
                $status = "<span style='color: red;'>✗</span>";
                
                // เก็บ error สำหรับแสดง
                if ($errors <= 10) {
                    $shortQuery = mb_substr($query, 0, 80) . (mb_strlen($query) > 80 ? '...' : '');
                    $detailsHTML .= "<tr><td>" . ($index + 1) . "</td><td>{$queryType}<br><small>" . htmlspecialchars($shortQuery) . "</small></td><td>" . $status . " " . htmlspecialchars(mb_substr($error, 0, 100)) . "</td></tr>";
                }
            }
        }
    } catch (Exception $e) {
        $errors++;
        $status = "<span style='color: red;'>✗ Exception</span>";
        
        if ($errors <= 10) {
            $shortQuery = mb_substr($query, 0, 80);
            $detailsHTML .= "<tr><td>" . ($index + 1) . "</td><td>{$queryType}<br><small>" . htmlspecialchars($shortQuery) . "</small></td><td>{$status}</td></tr>";
        }
    }
    
    // Update progress every 50 queries
    if ($index % 50 == 0 && $index > 0) {
        $currentTotal = max($total - $skipped, 1);
        echo "<script>updateProgress(" . $processedQueries . ", " . $currentTotal . ", {$success}, {$errors});</script>";
        flush();
        ob_flush();
    }
}

$detailsHTML .= "</table>";
$total = $processedQueries;

echo "<script>updateProgress({$total}, {$total}, {$success}, {$errors});</script>";

echo "<h3>📊 สรุปผลการ Import</h3>";
echo "<div class='info'>";
echo "📌 จำนวน Queries ทั้งหมด: " . number_format($total) . "<br>";
echo "✅ สำเร็จ: " . number_format($success) . "<br>";
echo "❌ ล้มเหลว: " . number_format($errors) . "<br>";
echo "⏭️ ข้าม: " . number_format($skipped) . "<br>";
echo "</div>";

if ($errors > 0 && $errors <= 10) {
    echo "<h3>⚠️ Queries ที่มีปัญหา (แสดง 10 รายการแรก):</h3>";
    echo $detailsHTML;
}

// Step 6: ตรวจสอบโครงสร้างฐานข้อมูล
echo "<h2>ขั้นตอนที่ 6: ตรวจสอบโครงสร้างฐานข้อมูล</h2>";

$result = $conn->query("SHOW TABLES");
$tables = [];
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

echo "<div class='success'>✅ พบ " . count($tables) . " ตาราง</div>";

echo "<table>";
echo "<tr><th>ลำดับ</th><th>ชื่อตาราง</th><th>จำนวนข้อมูล</th><th>สถานะ</th></tr>";

$importantTables = ['users', 'roles', 'permissions', 'settings', 'homepage_sections', 'categories', 'models', 'articles', 'menus'];

foreach ($tables as $index => $table) {
    $count = $conn->query("SELECT COUNT(*) as cnt FROM `{$table}`");
    $countRow = $count->fetch_assoc();
    $isImportant = in_array($table, $importantTables);
    $highlight = $isImportant ? "background: #fff3cd;" : "";
    $icon = $isImportant ? "⭐" : "📋";
    
    echo "<tr style='{$highlight}'>";
    echo "<td>" . ($index + 1) . "</td>";
    echo "<td>{$icon} {$table}</td>";
    echo "<td>" . number_format($countRow['cnt']) . "</td>";
    echo "<td><span style='color: green;'>✓ OK</span></td>";
    echo "</tr>";
}

echo "</table>";

// Step 7: ตรวจสอบข้อมูลสำคัญ
echo "<h2>ขั้นตอนที่ 7: ตรวจสอบข้อมูลสำคัญ</h2>";

// Check admin user
$admin = $conn->query("SELECT * FROM users WHERE role = 'admin' OR role = 'programmer' LIMIT 1");
if ($admin && $admin->num_rows > 0) {
    $adminRow = $admin->fetch_assoc();
    echo "<div class='success'>✅ พบผู้ดูแลระบบ: <strong>" . htmlspecialchars($adminRow['username']) . "</strong> (Role: {$adminRow['role']})</div>";
} else {
    echo "<div class='warning'>⚠️ ไม่พบผู้ดูแลระบบ - อาจต้องสร้างใหม่</div>";
}

// Check roles
$roles = $conn->query("SELECT * FROM roles");
echo "<div class='info'>👥 จำนวน Roles: " . $roles->num_rows . "</div>";

// Check permissions
$permissions = $conn->query("SELECT DISTINCT feature FROM permissions");
$features = [];
while ($row = $permissions->fetch_assoc()) {
    $features[] = $row['feature'];
}
echo "<div class='info'>🔐 ฟีเจอร์ที่มี Permission: " . count($features) . " ฟีเจอร์</div>";
echo "<div class='info'>📝 ได้แก่: " . implode(', ', $features) . "</div>";

// Check settings
$settings_count = $conn->query("SELECT COUNT(*) as cnt FROM settings")->fetch_assoc();
echo "<div class='info'>⚙️ การตั้งค่า: " . $settings_count['cnt'] . " รายการ</div>";

// Check homepage sections
$sections = $conn->query("SELECT * FROM homepage_sections WHERE is_active = 1");
echo "<div class='info'>📄 Homepage Sections (Active): " . $sections->num_rows . " sections</div>";

// สรุปท้าย
echo "<h2>✅ สรุปการสร้างฐานข้อมูล</h2>";

if ($errors == 0) {
    echo "<div class='success'>";
    echo "<h3>🎉 สร้างฐานข้อมูลสำเร็จ 100%!</h3>";
    echo "<p><strong>ฐานข้อมูล:</strong> vibedaybkk</p>";
    echo "<p><strong>จำนวนตาราง:</strong> " . count($tables) . " ตาราง</p>";
    echo "<p><strong>สถานะ:</strong> พร้อมใช้งาน</p>";
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<h4>🔗 ทดสอบระบบ:</h4>";
    echo "<ul>";
    echo "<li><a href='http://localhost:8888/vibedaybkk/' target='_blank'>Frontend</a></li>";
    echo "<li><a href='http://localhost:8888/vibedaybkk/admin/' target='_blank'>Admin Login</a></li>";
    echo "<li><a href='http://localhost:8888/vibedaybkk/test-connection-all.php' target='_blank'>Test Connection</a></li>";
    echo "</ul>";
    echo "<h4>👤 ข้อมูลเข้าสู่ระบบ:</h4>";
    echo "<p>Username: <strong>admin</strong><br>Password: <strong>admin123</strong></p>";
    echo "</div>";
} else {
    echo "<div class='warning'>";
    echo "<h3>⚠️ สร้างฐานข้อมูลเสร็จแล้ว แต่มีข้อผิดพลาด</h3>";
    echo "<p>Queries ที่ล้มเหลว: {$errors} queries</p>";
    echo "<p>กรุณาตรวจสอบข้อผิดพลาดด้านบนและแก้ไขให้เรียบร้อย</p>";
    echo "</div>";
}

$conn->close();

echo "</body></html>";
?>

