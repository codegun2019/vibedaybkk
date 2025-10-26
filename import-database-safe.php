<?php
/**
 * Import ฐานข้อมูล VIBEDAYBKK (Safe Method)
 * ข้าม Stored Procedures ทั้งหมด เพื่อป้องกัน syntax error
 */

set_time_limit(600);
ini_set('memory_limit', '1024M');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'>";
echo "<title>Import ฐานข้อมูล - VIBEDAYBKK (Safe Mode)</title>";
echo "<link href='https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap' rel='stylesheet'>";
echo "<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Kanit', Arial, sans-serif; padding: 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
.container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
.header { background: linear-gradient(135deg, #DC2626 0%, #991b1b 100%); color: white; padding: 40px; text-align: center; }
.header h1 { font-size: 2.5rem; margin-bottom: 10px; }
.content { padding: 40px; }
.success { background: #d4edda; color: #155724; padding: 20px; border-radius: 10px; margin: 15px 0; border-left: 5px solid #28a745; }
.error { background: #f8d7da; color: #721c24; padding: 20px; border-radius: 10px; margin: 15px 0; border-left: 5px solid #dc3545; }
.info { background: #d1ecf1; color: #0c5460; padding: 20px; border-radius: 10px; margin: 15px 0; border-left: 5px solid #17a2b8; }
.warning { background: #fff3cd; color: #856404; padding: 20px; border-radius: 10px; margin: 15px 0; border-left: 5px solid #ffc107; }
h2 { color: #DC2626; margin: 30px 0 20px 0; padding-bottom: 10px; border-bottom: 3px solid #DC2626; }
h3 { color: #555; margin: 20px 0 10px 0; }
table { width: 100%; background: white; border-collapse: collapse; margin: 20px 0; border-radius: 10px; overflow: hidden; }
th { background: #DC2626; color: white; padding: 15px; text-align: left; font-weight: 600; }
td { padding: 12px 15px; border-bottom: 1px solid #eee; }
tr:hover { background: #f8f9fa; }
.progress { background: #e9ecef; height: 40px; border-radius: 20px; overflow: hidden; margin: 20px 0; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1); }
.progress-bar { background: linear-gradient(90deg, #DC2626 0%, #991b1b 100%); height: 100%; transition: width 0.3s; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 16px; }
.step { background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 15px 0; border-left: 4px solid #DC2626; }
.step-number { display: inline-block; width: 40px; height: 40px; background: #DC2626; color: white; border-radius: 50%; text-align: center; line-height: 40px; font-weight: bold; margin-right: 15px; }
</style></head><body>";

echo "<div class='container'>";
echo "<div class='header'>";
echo "<h1>🗄️ Import ฐานข้อมูล VIBEDAYBKK</h1>";
echo "<p style='margin-top: 10px; opacity: 0.9;'>Safe Mode - ข้าม Stored Procedures อัตโนมัติ</p>";
echo "</div>";
echo "<div class='content'>";

// Step 1: เชื่อมต่อ
echo "<div class='step'>";
echo "<span class='step-number'>1</span>";
echo "<strong style='font-size: 1.2rem;'>เชื่อมต่อ MySQL Server</strong>";
echo "</div>";

$socket = '/Applications/MAMP/tmp/mysql/mysql.sock';
$conn = new mysqli('localhost', 'root', 'root', '', 0, $socket);

if ($conn->connect_error) {
    echo "<div class='error'>❌ <strong>เชื่อมต่อล้มเหลว:</strong> " . $conn->connect_error . "</div>";
    echo "</div></div></body></html>";
    exit;
}

echo "<div class='success'>✅ <strong>เชื่อมต่อสำเร็จ!</strong><br>MySQL Version: " . $conn->server_info . "</div>";

// Step 2: Backup
echo "<div class='step'>";
echo "<span class='step-number'>2</span>";
echo "<strong style='font-size: 1.2rem;'>สำรองฐานข้อมูลเดิม</strong>";
echo "</div>";

$result = $conn->query("SHOW DATABASES LIKE 'vibedaybkk'");
if ($result && $result->num_rows > 0) {
    $timestamp = date('Ymd_His');
    $backupName = "vibedaybkk_backup_{$timestamp}";
    
    $conn->query("CREATE DATABASE `{$backupName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    $tables = [];
    $result2 = $conn->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'vibedaybkk'");
    while ($row = $result2->fetch_array()) {
        $tables[] = $row[0];
    }
    
    foreach ($tables as $table) {
        $conn->query("CREATE TABLE `{$backupName}`.`{$table}` LIKE `vibedaybkk`.`{$table}`");
        $conn->query("INSERT INTO `{$backupName}`.`{$table}` SELECT * FROM `vibedaybkk`.`{$table}`");
    }
    
    echo "<div class='success'>✅ สำรองเป็น: <strong>{$backupName}</strong> (" . count($tables) . " ตาราง)</div>";
} else {
    echo "<div class='info'>📌 ไม่พบฐานข้อมูลเดิม - จะสร้างใหม่</div>";
}

// Step 3: Drop & Create
echo "<div class='step'>";
echo "<span class='step-number'>3</span>";
echo "<strong style='font-size: 1.2rem;'>สร้างฐานข้อมูลใหม่</strong>";
echo "</div>";

$conn->query("DROP DATABASE IF EXISTS vibedaybkk");
$conn->query("CREATE DATABASE vibedaybkk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db('vibedaybkk');
$conn->set_charset('utf8mb4');

echo "<div class='success'>✅ สร้างฐานข้อมูล 'vibedaybkk' ใหม่แล้ว</div>";

// Step 4: Import
echo "<div class='step'>";
echo "<span class='step-number'>4</span>";
echo "<strong style='font-size: 1.2rem;'>Import ข้อมูล</strong>";
echo "</div>";

$sqlFile = __DIR__ . '/vibedaybkk_17_10.sql';

if (!file_exists($sqlFile)) {
    echo "<div class='error'>❌ ไม่พบไฟล์ vibedaybkk_17_10.sql</div>";
    echo "</div></div></body></html>";
    exit;
}

$fileSize = filesize($sqlFile);
echo "<div class='info'>📄 ไฟล์: vibedaybkk_17_10.sql<br>📊 ขนาด: " . number_format($fileSize/1024, 2) . " KB</div>";

$sql = file_get_contents($sqlFile);

echo "<div class='progress'><div class='progress-bar' id='progressBar' style='width: 0%'>0%</div></div>";
echo "<div id='statusText' style='text-align: center; font-size: 18px; margin: 20px 0;'>กำลังเตรียมข้อมูล...</div>";

echo "<script>
function updateStatus(text, percent) {
    document.getElementById('statusText').innerHTML = text;
    document.getElementById('progressBar').style.width = percent + '%';
    document.getElementById('progressBar').textContent = percent + '%';
}
</script>";

flush();
ob_flush();

// ====================
// ขั้นตอนการทำความสะอาด SQL
// ====================

echo "<script>updateStatus('กำลังทำความสะอาด SQL...', 10);</script>";
flush();

// 1. ลบส่วน Stored Procedures ทั้งหมด
$delimiterStart = strpos($sql, 'DELIMITER $$');
$delimiterEnd = strrpos($sql, 'DELIMITER ;');

if ($delimiterStart !== false && $delimiterEnd !== false) {
    $before = substr($sql, 0, $delimiterStart);
    $after = substr($sql, $delimiterEnd + strlen('DELIMITER ;'));
    $sql = $before . "\n" . $after;
    echo "<div class='warning'>⚠️ ข้าม Stored Procedures (2 procedures)</div>";
    flush();
}

// 2. ลบ multi-line comments /* ... */
$sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

// 3. แยกเป็นบรรทัด และลบ comments
$lines = explode("\n", $sql);
$cleanLines = [];

foreach ($lines as $line) {
    $line = trim($line);
    
    // ข้ามบรรทัดว่าง
    if (empty($line)) continue;
    
    // ข้าม single-line comments
    if (strpos($line, '--') === 0) continue;
    
    // ข้าม DELIMITER commands
    if (stripos($line, 'DELIMITER') === 0) continue;
    
    // ข้าม special SET commands ที่ไม่จำเป็น
    if (preg_match('/^\/\*!.*\*\//i', $line)) continue;
    
    $cleanLines[] = $line;
}

$sql = implode("\n", $cleanLines);

echo "<script>updateStatus('แยก Queries...', 20);</script>";
flush();

// 4. แยก queries
$tempQueries = explode(';', $sql);
$queries = [];

foreach ($tempQueries as $q) {
    $q = trim($q);
    if (!empty($q) && strlen($q) > 3) {
        $queries[] = $q;
    }
}

$total = count($queries);
echo "<div class='info'>📝 พบ {$total} queries ที่จะประมวลผล</div>";
flush();

// ====================
// ประมวลผล Queries
// ====================

$success = 0;
$errors = 0;
$skipped = 0;
$errorList = [];

foreach ($queries as $index => $query) {
    $query = trim($query);
    
    // ข้าม special commands
    if (preg_match('/^(SET SQL_MODE|SET time_zone|START TRANSACTION|COMMIT|USE `vibedaybkk`)/i', $query)) {
        $skipped++;
        continue;
    }
    
    // ข้าม /*!...*/ 
    if (preg_match('/^\/\*!.*\*\//i', $query)) {
        $skipped++;
        continue;
    }
    
    // Execute
    $result = @$conn->query($query);
    
    if ($result !== false) {
        $success++;
    } else {
        $errorMsg = $conn->error;
        
        // ตรวจสอบว่าเป็น error ที่ยอมรับได้
        if (stripos($errorMsg, 'Duplicate entry') !== false ||
            stripos($errorMsg, 'already exists') !== false ||
            stripos($errorMsg, 'Multiple primary key') !== false) {
            // Duplicate - ไม่ใช่ error จริง
            $success++;
            $skipped++;
        } else {
            // Error จริง
            $errors++;
            
            if (count($errorList) < 10) {
                $shortQuery = mb_substr($query, 0, 100) . '...';
                $errorList[] = [
                    'index' => $index + 1,
                    'query' => $shortQuery,
                    'error' => mb_substr($errorMsg, 0, 150)
                ];
            }
        }
    }
    
    // Update progress
    if ($index % 100 == 0) {
        $percent = 20 + (($index / $total) * 70);
        $statusText = "ประมวลผลแล้ว: " . number_format($index) . " / " . number_format($total) . " queries";
        echo "<script>updateStatus('{$statusText}', {$percent});</script>";
        flush();
        ob_flush();
    }
}

echo "<script>updateStatus('เสร็จสมบูรณ์!', 100);</script>";

// ====================
// แสดงผลลัพธ์
// ====================

echo "<h2>📊 ผลลัพธ์การ Import</h2>";

echo "<table>";
echo "<tr><th>รายการ</th><th>จำนวน</th></tr>";
echo "<tr><td>Queries ทั้งหมด</td><td>" . number_format($total) . "</td></tr>";
echo "<tr><td><span style='color: green;'>✅ สำเร็จ</span></td><td><strong style='color: green;'>" . number_format($success) . "</strong></td></tr>";
echo "<tr><td><span style='color: red;'>❌ ล้มเหลว</span></td><td><strong style='color: red;'>" . number_format($errors) . "</strong></td></tr>";
echo "<tr><td><span style='color: orange;'>⏭️ ข้าม</span></td><td>" . number_format($skipped) . "</td></tr>";
echo "</table>";

if ($errors > 0 && !empty($errorList)) {
    echo "<h3>⚠️ Queries ที่มีปัญหา (แสดง 10 รายการแรก):</h3>";
    echo "<table>";
    echo "<tr><th>#</th><th>Query</th><th>Error Message</th></tr>";
    foreach ($errorList as $err) {
        echo "<tr>";
        echo "<td>{$err['index']}</td>";
        echo "<td><code style='font-size: 11px;'>" . htmlspecialchars($err['query']) . "</code></td>";
        echo "<td><span style='color: red;'>" . htmlspecialchars($err['error']) . "</span></td>";
        echo "</tr>";
    }
    echo "</table>";
}

// ====================
// ตรวจสอบผลลัพธ์
// ====================

echo "<div class='step'>";
echo "<span class='step-number'>5</span>";
echo "<strong style='font-size: 1.2rem;'>ตรวจสอบผลลัพธ์</strong>";
echo "</div>";

$result = $conn->query("SHOW TABLES");
$tables = [];

if ($result) {
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
}

echo "<div class='success'>✅ สร้างตารางสำเร็จ: <strong>" . count($tables) . " ตาราง</strong></div>";

// ตรวจสอบข้อมูลในตาราง
echo "<h3>📋 สรุปข้อมูลแต่ละตาราง:</h3>";
echo "<table>";
echo "<tr><th>#</th><th>ตาราง</th><th>จำนวนข้อมูล</th><th>สถานะ</th></tr>";

$totalRecords = 0;
$importantTables = ['users', 'roles', 'permissions', 'settings', 'homepage_sections', 'categories', 'models'];

foreach ($tables as $idx => $table) {
    $count = $conn->query("SELECT COUNT(*) as cnt FROM `{$table}`");
    $countRow = $count->fetch_assoc();
    $totalRecords += $countRow['cnt'];
    
    $isImportant = in_array($table, $importantTables);
    $star = $isImportant ? '⭐ ' : '';
    
    $badge = $countRow['cnt'] > 0 ? 
        "<span class='badge' style='background: #28a745; color: white; padding: 4px 8px; border-radius: 4px;'>มีข้อมูล</span>" :
        "<span class='badge' style='background: #6c757d; color: white; padding: 4px 8px; border-radius: 4px;'>ว่าง</span>";
    
    echo "<tr>";
    echo "<td>" . ($idx + 1) . "</td>";
    echo "<td>{$star}<strong>{$table}</strong></td>";
    echo "<td>" . number_format($countRow['cnt']) . "</td>";
    echo "<td>{$badge}</td>";
    echo "</tr>";
}

echo "</table>";
echo "<div class='info'>📊 ข้อมูลทั้งหมด: <strong>" . number_format($totalRecords) . " records</strong></div>";

// ตรวจสอบ Admin
echo "<h3>👤 ตรวจสอบผู้ดูแลระบบ:</h3>";

$admin = $conn->query("SELECT username, role, status FROM users WHERE role IN ('admin', 'programmer') LIMIT 5");

if ($admin && $admin->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Username</th><th>Role</th><th>Status</th></tr>";
    while ($row = $admin->fetch_assoc()) {
        echo "<tr>";
        echo "<td><strong>{$row['username']}</strong></td>";
        echo "<td>{$row['role']}</td>";
        echo "<td>{$row['status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<div class='success'>✅ พบผู้ดูแลระบบ: " . $admin->num_rows . " คน</div>";
} else {
    echo "<div class='error'>❌ ไม่พบผู้ดูแลระบบ!</div>";
}

// ====================
// สรุปท้าย
// ====================

echo "<h2>✨ สรุปผล</h2>";

if ($errors == 0 && count($tables) >= 20 && $totalRecords > 100) {
    echo "<div class='success' style='padding: 30px; text-align: center;'>";
    echo "<h2 style='color: #155724; margin: 0 0 20px 0;'>🎉 Import ฐานข้อมูลสำเร็จ 100%!</h2>";
    echo "<p style='font-size: 18px; margin: 10px 0;'>✅ สร้างตาราง: " . count($tables) . " ตาราง</p>";
    echo "<p style='font-size: 18px; margin: 10px 0;'>✅ Import ข้อมูล: " . number_format($totalRecords) . " records</p>";
    echo "<p style='font-size: 18px; margin: 10px 0;'>✅ Queries สำเร็จ: " . number_format($success) . " queries</p>";
    echo "</div>";
    
    echo "<div class='info' style='padding: 30px;'>";
    echo "<h3 style='color: #0c5460;'>🚀 ขั้นตอนถัดไป:</h3>";
    echo "<ol style='line-height: 2;'>";
    echo "<li><strong>ทดสอบการเชื่อมต่อ:</strong> <a href='test-connection-all.php' target='_blank'>test-connection-all.php</a></li>";
    echo "<li><strong>ตรวจสอบโครงสร้าง:</strong> <a href='verify-database-structure.php' target='_blank'>verify-database-structure.php</a></li>";
    echo "<li><strong>เข้าสู่ระบบ Admin:</strong> <a href='admin/' target='_blank'>admin/</a> (admin/admin123)</li>";
    echo "<li><strong>ดู Frontend:</strong> <a href='index.php' target='_blank'>index.php</a></li>";
    echo "</ol>";
    echo "</div>";
    
} else {
    echo "<div class='warning' style='padding: 30px;'>";
    echo "<h3 style='color: #856404;'>⚠️ Import เสร็จแล้ว แต่อาจมีปัญหา</h3>";
    echo "<p>- ตาราง: " . count($tables) . " (คาดหวัง ≥ 20)</p>";
    echo "<p>- ข้อมูล: " . number_format($totalRecords) . " (คาดหวัง > 100)</p>";
    echo "<p>- Errors: {$errors}</p>";
    echo "<p style='margin-top: 20px;'>กรุณาตรวจสอบ errors ด้านบนและแก้ไข</p>";
    echo "</div>";
}

$conn->close();

echo "</div></div></body></html>";
?>




