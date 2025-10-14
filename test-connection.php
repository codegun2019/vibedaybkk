<?php
/**
 * ไฟล์ทดสอบการเชื่อมต่อ Database
 * ใช้เพื่อตรวจสอบว่า Database พร้อมใช้งานหรือไม่
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database config
$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'root';
$db_name = 'vibedaybkk';

echo "<h2>🔍 ทดสอบการเชื่อมต่อ Database</h2>";
echo "<hr>";

// Test 1: Connect to MySQL Server
echo "<h3>1. ทดสอบเชื่อมต่อ MySQL Server</h3>";
$conn = @new mysqli($db_host, $db_user, $db_pass);

if ($conn->connect_error) {
    echo "❌ <span style='color:red'>เชื่อมต่อ MySQL ไม่สำเร็จ!</span><br>";
    echo "Error: " . $conn->connect_error . "<br>";
    echo "<br><strong>แก้ไข:</strong> ตรวจสอบว่า MAMP/MySQL เปิดอยู่และ username/password ถูกต้อง<br>";
    exit;
} else {
    echo "✅ <span style='color:green'>เชื่อมต่อ MySQL สำเร็จ!</span><br>";
}

// Test 2: Check if database exists
echo "<br><h3>2. ตรวจสอบฐานข้อมูล 'vibedaybkk'</h3>";
$result = $conn->query("SHOW DATABASES LIKE '$db_name'");

if ($result->num_rows == 0) {
    echo "❌ <span style='color:red'>ไม่พบฐานข้อมูล 'vibedaybkk'</span><br>";
    echo "<br><strong>แก้ไข:</strong> ต้อง Import ไฟล์ database.sql ก่อน<br>";
    echo "<br><strong>วิธีแก้:</strong><br>";
    echo "1. เปิด phpMyAdmin<br>";
    echo "2. สร้างฐานข้อมูลใหม่ชื่อ: <code>vibedaybkk</code><br>";
    echo "3. เลือกฐานข้อมูล vibedaybkk<br>";
    echo "4. คลิก Import > เลือกไฟล์ database.sql > Go<br>";
    echo "<br>หรือใช้ command:<br>";
    echo "<code>mysql -u root -proot vibedaybkk < C:/MAMP/htdocs/vibedaybkk/database.sql</code><br>";
    $conn->close();
    exit;
} else {
    echo "✅ <span style='color:green'>พบฐานข้อมูล 'vibedaybkk' แล้ว</span><br>";
}

// Test 3: Connect to database
echo "<br><h3>3. เชื่อมต่อฐานข้อมูล 'vibedaybkk'</h3>";
$conn->select_db($db_name);
if ($conn->error) {
    echo "❌ <span style='color:red'>เชื่อมต่อฐานข้อมูลไม่สำเร็จ</span><br>";
    echo "Error: " . $conn->error . "<br>";
    exit;
} else {
    echo "✅ <span style='color:green'>เชื่อมต่อฐานข้อมูลสำเร็จ</span><br>";
}

// Test 4: Check tables
echo "<br><h3>4. ตรวจสอบตาราง</h3>";
$tables = ['users', 'models', 'categories', 'articles', 'contacts', 'bookings', 'menus', 'settings'];
$missing_tables = [];

foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) {
        $missing_tables[] = $table;
        echo "❌ ไม่พบตาราง: <code>$table</code><br>";
    } else {
        echo "✅ พบตาราง: <code>$table</code><br>";
    }
}

if (!empty($missing_tables)) {
    echo "<br><span style='color:red'><strong>❌ ขาดตาราง!</strong> ต้อง Import database.sql</span><br>";
    $conn->close();
    exit;
}

// Test 5: Check admin user
echo "<br><h3>5. ตรวจสอบ Admin User</h3>";
$result = $conn->query("SELECT * FROM users WHERE username = 'admin'");

if ($result->num_rows == 0) {
    echo "❌ <span style='color:red'>ไม่พบ user 'admin' ในฐานข้อมูล</span><br>";
    echo "<br><strong>🛠️ กำลังสร้าง admin user ใหม่...</strong><br>";
    
    // Create admin user
    $password_hash = password_hash('admin123', PASSWORD_BCRYPT);
    $sql = "INSERT INTO users (username, password, full_name, email, role, status) 
            VALUES ('admin', '$password_hash', 'ผู้ดูแลระบบ', 'admin@vibedaybkk.com', 'admin', 'active')";
    
    if ($conn->query($sql)) {
        echo "✅ <span style='color:green'><strong>สร้าง admin user สำเร็จ!</strong></span><br>";
        echo "<br>ข้อมูล Login:<br>";
        echo "Username: <code>admin</code><br>";
        echo "Password: <code>admin123</code><br>";
    } else {
        echo "❌ Error: " . $conn->error . "<br>";
    }
} else {
    $user = $result->fetch_assoc();
    echo "✅ <span style='color:green'>พบ user 'admin' แล้ว</span><br>";
    echo "<br><strong>ข้อมูลที่มีอยู่:</strong><br>";
    echo "- Username: <code>" . $user['username'] . "</code><br>";
    echo "- Full Name: " . $user['full_name'] . "<br>";
    echo "- Email: " . $user['email'] . "<br>";
    echo "- Role: " . $user['role'] . "<br>";
    echo "- Status: " . $user['status'] . "<br>";
    
    if ($user['status'] != 'active') {
        echo "<br>⚠️ <span style='color:orange'>User ไม่ active! กำลังแก้ไข...</span><br>";
        $conn->query("UPDATE users SET status = 'active' WHERE username = 'admin'");
        echo "✅ แก้ไขแล้ว<br>";
    }
}

echo "<br><h3>6. ทดสอบ Password</h3>";
$password_test = 'admin123';
$result = $conn->query("SELECT password FROM users WHERE username = 'admin'");
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password_test, $user['password'])) {
        echo "✅ <span style='color:green'><strong>Password ถูกต้อง!</strong></span><br>";
        echo "<br>คุณสามารถ Login ได้แล้วด้วย:<br>";
        echo "Username: <code>admin</code><br>";
        echo "Password: <code>admin123</code><br>";
    } else {
        echo "❌ <span style='color:red'>Password ไม่ถูกต้อง! กำลังแก้ไข...</span><br>";
        $new_hash = password_hash('admin123', PASSWORD_BCRYPT);
        $conn->query("UPDATE users SET password = '$new_hash' WHERE username = 'admin'");
        echo "✅ อัพเดท password เรียบร้อย<br>";
    }
}

$conn->close();

echo "<br><hr>";
echo "<h2>✅ สรุป</h2>";
echo "<p style='color:green; font-size:18px;'><strong>ระบบพร้อมใช้งาน!</strong></p>";
echo "<p>กลับไปที่: <a href='admin/login.php' style='font-size:18px;'><strong>หน้า Login</strong></a></p>";
echo "<p>ใช้: <code>admin</code> / <code>admin123</code></p>";
?>

<style>
body { font-family: 'Arial', sans-serif; padding: 20px; background: #f5f5f5; }
code { background: #e0e0e0; padding: 2px 6px; border-radius: 3px; }
h2 { color: #DC2626; }
h3 { color: #333; margin-top: 20px; }
</style>

