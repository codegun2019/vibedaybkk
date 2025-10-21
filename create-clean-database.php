<?php
/**
 * สร้างฐานข้อมูล VIBEDAYBKK ใหม่ทั้งหมด
 * จากโครงสร้างที่สะอาดและถูกต้อง
 */

set_time_limit(600);
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'>";
echo "<link href='https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap' rel='stylesheet'>";
echo "<style>
body { font-family: 'Kanit', sans-serif; padding: 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
.container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
.header { background: linear-gradient(135deg, #DC2626 0%, #991b1b 100%); color: white; padding: 40px; text-align: center; }
.header h1 { font-size: 2.5rem; }
.content { padding: 40px; }
.success { background: #d4edda; color: #155724; padding: 20px; border-radius: 10px; margin: 15px 0; border-left: 5px solid #28a745; }
.error { background: #f8d7da; color: #721c24; padding: 20px; border-radius: 10px; margin: 15px 0; border-left: 5px solid #dc3545; }
.info { background: #d1ecf1; color: #0c5460; padding: 20px; border-radius: 10px; margin: 15px 0; border-left: 5px solid #17a2b8; }
.warning { background: #fff3cd; color: #856404; padding: 20px; border-radius: 10px; margin: 15px 0; border-left: 5px solid #ffc107; }
h2 { color: #DC2626; margin: 30px 0 20px 0; padding-bottom: 10px; border-bottom: 3px solid #DC2626; }
.step { background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 15px 0; border-left: 4px solid #DC2626; }
.step-number { display: inline-block; width: 40px; height: 40px; background: #DC2626; color: white; border-radius: 50%; text-align: center; line-height: 40px; font-weight: bold; margin-right: 15px; }
table { width: 100%; background: white; border-collapse: collapse; margin: 20px 0; }
th { background: #DC2626; color: white; padding: 15px; }
td { padding: 12px 15px; border-bottom: 1px solid #eee; }
</style></head><body>";

echo "<div class='container'>";
echo "<div class='header'><h1>🗄️ สร้างฐานข้อมูลใหม่</h1><p>VIBEDAYBKK - Clean Database</p></div>";
echo "<div class='content'>";

// เชื่อมต่อ
$socket = '/Applications/MAMP/tmp/mysql/mysql.sock';
$conn = new mysqli('localhost', 'root', 'root', '', 0, $socket);

if ($conn->connect_error) {
    die("<div class='error'>❌ เชื่อมต่อล้มเหลว: " . $conn->connect_error . "</div>");
}

echo "<div class='success'>✅ เชื่อมต่อสำเร็จ! MySQL v" . $conn->server_info . "</div>";

// สำรอง + ลบ + สร้างใหม่
echo "<div class='step'><span class='step-number'>1</span><strong>เตรียมฐานข้อมูล</strong></div>";

$result = $conn->query("SHOW DATABASES LIKE 'vibedaybkk'");
if ($result && $result->num_rows > 0) {
    $backup = "vibedaybkk_backup_" . date('Ymd_His');
    $conn->query("CREATE DATABASE `{$backup}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    $tables = [];
    $r = $conn->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'vibedaybkk'");
    while ($row = $r->fetch_array()) $tables[] = $row[0];
    
    foreach ($tables as $t) {
        $conn->query("CREATE TABLE `{$backup}`.`{$t}` LIKE `vibedaybkk`.`{$t}`");
        $conn->query("INSERT INTO `{$backup}`.`{$t}` SELECT * FROM `vibedaybkk`.`{$t}`");
    }
    echo "<div class='success'>✅ สำรอง: {$backup}</div>";
}

$conn->query("DROP DATABASE IF EXISTS vibedaybkk");
$conn->query("CREATE DATABASE vibedaybkk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db('vibedaybkk');
echo "<div class='success'>✅ สร้างฐานข้อมูลใหม่</div>";

// อ่าน SQL file แล้ว clean
echo "<div class='step'><span class='step-number'>2</span><strong>เตรียม SQL</strong></div>";

$sqlFile = __DIR__ . '/vibedaybkk_17_10.sql';
if (!file_exists($sqlFile)) {
    die("<div class='error'>❌ ไม่พบไฟล์ vibedaybkk_17_10.sql</div>");
}

$sql = file_get_contents($sqlFile);

// ลบ Stored Procedures
$start = strpos($sql, 'DELIMITER $$');
$end = strrpos($sql, 'DELIMITER ;');
if ($start !== false && $end !== false) {
    $sql = substr($sql, 0, $start) . substr($sql, $end + 11);
    echo "<div class='warning'>⚠️ ลบ Stored Procedures</div>";
}

// ลบ USE statement
$sql = preg_replace('/USE `vibedaybkk`;/i', '', $sql);

echo "<div class='info'>📄 ไฟล์: " . number_format(strlen($sql)/1024, 2) . " KB (หลัง clean)</div>";

// Import
echo "<div class='step'><span class='step-number'>3</span><strong>Import ข้อมูล</strong></div>";

$conn->query("SET SQL_MODE = ''");
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("SET UNIQUE_CHECKS = 0");

$success = 0;
$errors = 0;
$errorDetails = [];

// Execute ทีเดียว
if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
            $success++;
        }
        
        if ($conn->errno) {
            $err = $conn->error;
            // ข้าม duplicate/exists errors
            if (stripos($err, 'Duplicate') === false && stripos($err, 'exists') === false) {
                $errors++;
                if (count($errorDetails) < 5) {
                    $errorDetails[] = substr($err, 0, 200);
                }
            }
        }
        
        if ($conn->more_results()) {
            $conn->next_result();
        } else {
            break;
        }
        
    } while (true);
}

$conn->query("SET FOREIGN_KEY_CHECKS = 1");
$conn->query("SET UNIQUE_CHECKS = 1");

if ($errors > 0) {
    echo "<div class='warning'>⚠️ พบ errors: {$errors} รายการ</div>";
    if (!empty($errorDetails)) {
        echo "<table><tr><th>Error</th></tr>";
        foreach ($errorDetails as $err) {
            echo "<tr><td>" . htmlspecialchars($err) . "</td></tr>";
        }
        echo "</table>";
    }
}

// ตรวจสอบผล
echo "<div class='step'><span class='step-number'>4</span><strong>ตรวจสอบผล</strong></div>";

$tables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) $tables[] = $row[0];

echo "<div class='success'>✅ สร้าง: " . count($tables) . " ตาราง</div>";

$totalRecords = 0;
echo "<table><tr><th>ตาราง</th><th>ข้อมูล</th></tr>";
foreach ($tables as $t) {
    $c = $conn->query("SELECT COUNT(*) as cnt FROM `{$t}`")->fetch_assoc();
    $totalRecords += $c['cnt'];
    echo "<tr><td>{$t}</td><td>" . number_format($c['cnt']) . "</td></tr>";
}
echo "</table>";

echo "<div class='info'>📊 ข้อมูลทั้งหมด: " . number_format($totalRecords) . " records</div>";

// ตรวจสอบข้อมูลสำคัญ
$admin = $conn->query("SELECT username, role FROM users WHERE role IN ('admin','programmer') LIMIT 1");
if ($admin && $admin->num_rows > 0) {
    $a = $admin->fetch_assoc();
    echo "<div class='success'>✅ Admin: {$a['username']} ({$a['role']})</div>";
}

$roles = $conn->query("SELECT COUNT(*) as c FROM roles")->fetch_assoc();
$perms = $conn->query("SELECT COUNT(*) as c FROM permissions")->fetch_assoc();

echo "<div class='info'>👥 Roles: {$roles['c']} | 🔐 Permissions: {$perms['c']}</div>";

// สรุป
if (count($tables) >= 20 && $totalRecords >= 100 && $errors < 20) {
    echo "<div class='success' style='padding: 40px; text-align: center;'>";
    echo "<h2 style='color: #155724;'>🎉 สำเร็จแล้ว!</h2>";
    echo "<p style='font-size: 1.2rem; margin: 20px 0;'>ฐานข้อมูลพร้อมใช้งาน</p>";
    echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 30px;'>";
    echo "<a href='verify-database-structure.php' style='padding: 20px; background: #17a2b8; color: white; text-decoration: none; border-radius: 10px; text-align: center; font-weight: 600;'>🔍 ตรวจสอบ DB</a>";
    echo "<a href='admin/' style='padding: 20px; background: #DC2626; color: white; text-decoration: none; border-radius: 10px; text-align: center; font-weight: 600;'>👨‍💼 Admin</a>";
    echo "<a href='test-connection-all.php' style='padding: 20px; background: #28a745; color: white; text-decoration: none; border-radius: 10px; text-align: center; font-weight: 600;'>✅ Test</a>";
    echo "<a href='index.php' style='padding: 20px; background: #6f42c1; color: white; text-decoration: none; border-radius: 10px; text-align: center; font-weight: 600;'>🏠 Frontend</a>";
    echo "</div>";
    echo "<div style='margin-top: 30px; padding: 20px; background: #fff3cd; border-radius: 10px;'>";
    echo "<p style='font-size: 1.1rem;'>👤 Login: <strong>admin</strong> / <strong>admin123</strong></p>";
    echo "</div>";
    echo "</div>";
} else {
    echo "<div class='warning'>⚠️ Import เสร็จแล้ว แต่อาจไม่สมบูรณ์<br>";
    echo "ตาราง: " . count($tables) . " | ข้อมูล: {$totalRecords} | Errors: {$errors}</div>";
}

$conn->close();
echo "</div></div></body></html>";
?>

