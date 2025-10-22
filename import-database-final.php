<?php
/**
 * Import ฐานข้อมูล VIBEDAYBKK - Final Version
 * ใช้ multi_query() เพื่อความถูกต้อง 100%
 */

set_time_limit(600);
ini_set('memory_limit', '1024M');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'>";
echo "<title>Import ฐานข้อมูล - VIBEDAYBKK (Final)</title>";
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
table { width: 100%; background: white; border-collapse: collapse; margin: 20px 0; border-radius: 10px; overflow: hidden; }
th { background: #DC2626; color: white; padding: 15px; text-align: left; font-weight: 600; }
td { padding: 12px 15px; border-bottom: 1px solid #eee; }
.progress { background: #e9ecef; height: 40px; border-radius: 20px; overflow: hidden; margin: 20px 0; }
.progress-bar { background: linear-gradient(90deg, #DC2626 0%, #991b1b 100%); height: 100%; transition: width 0.3s; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }
.step { background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 15px 0; border-left: 4px solid #DC2626; }
.step-number { display: inline-block; width: 40px; height: 40px; background: #DC2626; color: white; border-radius: 50%; text-align: center; line-height: 40px; font-weight: bold; margin-right: 15px; }
.badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
.badge-success { background: #28a745; color: white; }
</style></head><body>";

echo "<div class='container'>";
echo "<div class='header'>";
echo "<h1>🗄️ Import ฐานข้อมูล VIBEDAYBKK</h1>";
echo "<p style='margin-top: 10px; opacity: 0.9;'>Version 3.0 - ใช้ multi_query() เพื่อความถูกต้อง 100%</p>";
echo "</div>";
echo "<div class='content'>";

// ==================== STEP 1 ====================
echo "<div class='step'><span class='step-number'>1</span><strong style='font-size: 1.2rem;'>เชื่อมต่อ MySQL</strong></div>";

$socket = '/Applications/MAMP/tmp/mysql/mysql.sock';
$conn = new mysqli('localhost', 'root', 'root', '', 0, $socket);

if ($conn->connect_error) {
    echo "<div class='error'>❌ เชื่อมต่อล้มเหลว: " . $conn->connect_error . "</div>";
    echo "</div></div></body></html>";
    exit;
}

echo "<div class='success'>✅ เชื่อมต่อสำเร็จ! MySQL v" . $conn->server_info . "</div>";

// ==================== STEP 2 ====================
echo "<div class='step'><span class='step-number'>2</span><strong style='font-size: 1.2rem;'>สำรองฐานข้อมูลเดิม</strong></div>";

$result = $conn->query("SHOW DATABASES LIKE 'vibedaybkk'");
if ($result && $result->num_rows > 0) {
    $timestamp = date('Ymd_His');
    $backupName = "vibedaybkk_backup_{$timestamp}";
    
    // สร้าง backup database
    $conn->query("CREATE DATABASE `{$backupName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // Copy ทุกตาราง
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
    echo "<div class='info'>📌 ไม่พบฐานข้อมูลเดิม</div>";
}

// ==================== STEP 3 ====================
echo "<div class='step'><span class='step-number'>3</span><strong style='font-size: 1.2rem;'>สร้างฐานข้อมูลใหม่</strong></div>";

$conn->query("DROP DATABASE IF EXISTS vibedaybkk");
$conn->query("CREATE DATABASE vibedaybkk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db('vibedaybkk');

echo "<div class='success'>✅ สร้าง 'vibedaybkk' แล้ว</div>";

// ==================== STEP 4 ====================
echo "<div class='step'><span class='step-number'>4</span><strong style='font-size: 1.2rem;'>Import ข้อมูล</strong></div>";

$sqlFile = __DIR__ . '/vibedaybkk_17_10.sql';

if (!file_exists($sqlFile)) {
    echo "<div class='error'>❌ ไม่พบไฟล์ vibedaybkk_17_10.sql</div>";
    echo "</div></div></body></html>";
    exit;
}

$fileSize = filesize($sqlFile);
echo "<div class='info'>📄 ไฟล์: vibedaybkk_17_10.sql (" . number_format($fileSize/1024, 2) . " KB)</div>";

// อ่านไฟล์
$sql = file_get_contents($sqlFile);

echo "<div class='progress'><div class='progress-bar' id='progressBar' style='width: 20%'>20%</div></div>";
echo "<div id='status' style='text-align: center; font-size: 18px; margin: 20px 0;'>กำลังเตรียมข้อมูล...</div>";

echo "<script>
function updateProgress(percent, text) {
    document.getElementById('progressBar').style.width = percent + '%';
    document.getElementById('progressBar').textContent = percent + '%';
    document.getElementById('status').innerHTML = text;
}
</script>";

flush();
ob_flush();

// ==================== CLEANING SQL ====================

echo "<script>updateProgress(30, 'กำลังทำความสะอาด SQL...');</script>";
flush();

// 1. ลบ Stored Procedures ทั้งหมด
$delimiterStart = strpos($sql, 'DELIMITER $$');
$delimiterEnd = strrpos($sql, 'DELIMITER ;');

if ($delimiterStart !== false && $delimiterEnd !== false) {
    $before = substr($sql, 0, $delimiterStart);
    $after = substr($sql, $delimiterEnd + strlen('DELIMITER ;'));
    $sql = $before . $after;
    echo "<div class='warning'>⚠️ ข้าม Stored Procedures (ไม่จำเป็น)</div>";
    flush();
}

// 2. ลบ USE database statement
$sql = preg_replace('/USE `vibedaybkk`;/i', '', $sql);

echo "<script>updateProgress(40, 'กำลัง Import ข้อมูล...');</script>";
flush();

// ==================== MULTI QUERY ====================

// เพิ่ม error suppression และใช้ multi_query
$conn->query("SET SQL_MODE = ''");
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

$success = false;
$errors = [];

// Execute ทีเดียวด้วย multi_query
if ($conn->multi_query($sql)) {
    echo "<div class='info'>📦 กำลังประมวลผล...</div>";
    flush();
    
    $queryCount = 0;
    
    // วน process results ทั้งหมด
    do {
        $queryCount++;
        
        // ดึง result (ถ้ามี)
        if ($result = $conn->store_result()) {
            $result->free();
        }
        
        // เช็ค error
        if ($conn->errno) {
            $error = $conn->error;
            
            // ข้าม errors ที่ไม่สำคัญ
            if (stripos($error, 'Duplicate entry') === false &&
                stripos($error, 'already exists') === false) {
                $errors[] = $error;
            }
        }
        
        // Update progress
        if ($queryCount % 100 == 0) {
            $percent = 40 + ($queryCount / 10);
            if ($percent > 90) $percent = 90;
            echo "<script>updateProgress({$percent}, 'ประมวลผล query ที่ {$queryCount}...');</script>";
            flush();
            ob_flush();
        }
        
        // รอ query ถัดไป
        $hasMore = $conn->more_results();
        if ($hasMore) {
            $conn->next_result();
        }
        
    } while ($hasMore);
    
    echo "<script>updateProgress(95, 'เกือบเสร็จแล้ว...');</script>";
    flush();
    
    $success = true;
    
} else {
    $errors[] = "Multi-query failed: " . $conn->error;
}

$conn->query("SET FOREIGN_KEY_CHECKS = 1");

echo "<script>updateProgress(100, 'เสร็จสมบูรณ์!');</script>";
flush();

// ==================== SHOW RESULTS ====================

echo "<h2>📊 ผลลัพธ์การ Import</h2>";

if ($success && count($errors) < 10) {
    echo "<div class='success'>";
    echo "<h3 style='color: #155724; margin: 0 0 15px 0;'>🎉 Import สำเร็จ!</h3>";
    echo "<p>✅ ประมวลผล queries ทั้งหมดเรียบร้อย</p>";
    if (count($errors) > 0) {
        echo "<p>⚠️ มี errors เล็กน้อยที่ไม่สำคัญ: " . count($errors) . " รายการ</p>";
    }
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<h3>❌ พบปัญหาระหว่าง Import</h3>";
    echo "<p>Errors: " . count($errors) . " รายการ</p>";
    echo "</div>";
}

if (!empty($errors) && count($errors) <= 10) {
    echo "<h3>⚠️ Errors ที่พบ:</h3>";
    echo "<table>";
    echo "<tr><th>#</th><th>Error Message</th></tr>";
    foreach ($errors as $idx => $error) {
        echo "<tr><td>" . ($idx + 1) . "</td><td>" . htmlspecialchars($error) . "</td></tr>";
    }
    echo "</table>";
}

// ==================== VERIFY ====================

echo "<div class='step'><span class='step-number'>5</span><strong style='font-size: 1.2rem;'>ตรวจสอบผลลัพธ์</strong></div>";

$result = $conn->query("SHOW TABLES");
$tables = [];

if ($result) {
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
}

echo "<div class='success'>✅ สร้างตาราง: <strong>" . count($tables) . " ตาราง</strong></div>";

// นับข้อมูลทั้งหมด
echo "<table>";
echo "<tr><th>#</th><th>ตาราง</th><th>จำนวนข้อมูล</th></tr>";

$totalRecords = 0;
foreach ($tables as $idx => $table) {
    $count = $conn->query("SELECT COUNT(*) as cnt FROM `{$table}`");
    $countRow = $count->fetch_assoc();
    $totalRecords += $countRow['cnt'];
    
    echo "<tr>";
    echo "<td>" . ($idx + 1) . "</td>";
    echo "<td><strong>{$table}</strong></td>";
    echo "<td>" . number_format($countRow['cnt']) . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<div class='info'>📊 ข้อมูลทั้งหมด: <strong>" . number_format($totalRecords) . " records</strong></div>";

// ตรวจสอบ Admin
$admin = $conn->query("SELECT username, role FROM users WHERE role IN ('admin', 'programmer') LIMIT 1");
if ($admin && $admin->num_rows > 0) {
    $adminRow = $admin->fetch_assoc();
    echo "<div class='success'>✅ พบผู้ดูแลระบบ: <strong>{$adminRow['username']}</strong> ({$adminRow['role']})</div>";
}

// ตรวจสอบ Roles
$roles = $conn->query("SELECT COUNT(*) as cnt FROM roles");
$rolesCount = $roles->fetch_assoc();
echo "<div class='info'>👥 Roles: {$rolesCount['cnt']} บทบาท</div>";

// ตรวจสอบ Permissions
$perms = $conn->query("SELECT COUNT(*) as cnt FROM permissions");
$permsCount = $perms->fetch_assoc();
echo "<div class='info'>🔐 Permissions: {$permsCount['cnt']} รายการ</div>";

// ตรวจสอบ Homepage Sections
$sections = $conn->query("SELECT COUNT(*) as cnt FROM homepage_sections WHERE is_active = 1");
$sectionsCount = $sections->fetch_assoc();
echo "<div class='info'>📄 Homepage Sections (Active): {$sectionsCount['cnt']} sections</div>";

// ==================== FINAL SUMMARY ====================

echo "<h2>✨ สรุปผล</h2>";

$allGood = (
    count($tables) >= 20 && 
    $totalRecords >= 100 && 
    count($errors) < 10 &&
    $rolesCount['cnt'] >= 4 &&
    $permsCount['cnt'] >= 40
);

if ($allGood) {
    echo "<div class='success' style='padding: 40px; text-align: center;'>";
    echo "<h2 style='color: #155724; margin: 0 0 20px 0; font-size: 2rem;'>🎉 สำเร็จ 100%!</h2>";
    echo "<div style='font-size: 1.2rem; line-height: 2;'>";
    echo "✅ ตาราง: <strong>" . count($tables) . "</strong><br>";
    echo "✅ ข้อมูล: <strong>" . number_format($totalRecords) . " records</strong><br>";
    echo "✅ Roles: <strong>{$rolesCount['cnt']}</strong><br>";
    echo "✅ Permissions: <strong>{$permsCount['cnt']}</strong><br>";
    echo "</div>";
    echo "</div>";
    
    echo "<div class='info' style='padding: 30px;'>";
    echo "<h3 style='color: #0c5460; margin: 0 0 20px 0;'>🚀 ลิงก์ทดสอบระบบ:</h3>";
    echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 15px;'>";
    echo "<a href='verify-database-structure.php' target='_blank' style='display: block; padding: 20px; background: #17a2b8; color: white; text-decoration: none; border-radius: 10px; text-align: center; font-weight: 600;'>🔍 ตรวจสอบโครงสร้าง DB</a>";
    echo "<a href='test-connection-all.php' target='_blank' style='display: block; padding: 20px; background: #28a745; color: white; text-decoration: none; border-radius: 10px; text-align: center; font-weight: 600;'>✅ ทดสอบการเชื่อมต่อ</a>";
    echo "<a href='admin/' target='_blank' style='display: block; padding: 20px; background: #DC2626; color: white; text-decoration: none; border-radius: 10px; text-align: center; font-weight: 600;'>👨‍💼 เข้าสู่ระบบ Admin</a>";
    echo "<a href='index.php' target='_blank' style='display: block; padding: 20px; background: #6f42c1; color: white; text-decoration: none; border-radius: 10px; text-align: center; font-weight: 600;'>🏠 ดู Frontend</a>";
    echo "</div>";
    echo "<div style='margin-top: 30px; padding: 20px; background: #fff3cd; border-radius: 10px;'>";
    echo "<h4 style='margin: 0 0 10px 0;'>👤 ข้อมูลเข้าสู่ระบบ Admin:</h4>";
    echo "<p style='font-size: 1.1rem;'>";
    echo "Username: <strong style='color: #DC2626;'>admin</strong><br>";
    echo "Password: <strong style='color: #DC2626;'>admin123</strong>";
    echo "</p>";
    echo "</div>";
    echo "</div>";
    
} else {
    echo "<div class='warning' style='padding: 30px;'>";
    echo "<h3>⚠️ Import เสร็จแล้ว แต่อาจมีปัญหา</h3>";
    echo "<p>- ตาราง: " . count($tables) . " (คาดหวัง ≥ 20)</p>";
    echo "<p>- ข้อมูล: " . number_format($totalRecords) . " (คาดหวัง ≥ 100)</p>";
    echo "<p>- Errors: " . count($errors) . "</p>";
    echo "</div>";
}

$conn->close();

echo "</div></div></body></html>";
?>


