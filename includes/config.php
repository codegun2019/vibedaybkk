<?php
/**
 * VIBEDAYBKK - Configuration File
 * ไฟล์ตั้งค่าระบบ
 */

// Prevent direct access
if (!defined('VIBEDAYBKK_ADMIN')) {
    define('VIBEDAYBKK_ADMIN', true);
}

// Error Reporting (เปิดในระหว่างพัฒนา, ปิดใน production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Bangkok');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'vibedaybkk');
define('DB_CHARSET', 'utf8mb4');

// Site Configuration
define('SITE_NAME', 'VIBEDAYBKK');
define('SITE_URL', 'http://localhost/vibedaybkk');
define('ADMIN_URL', SITE_URL . '/admin');

// Path Configuration
define('ROOT_PATH', dirname(dirname(__FILE__)));
define('ADMIN_PATH', ROOT_PATH . '/admin');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('UPLOADS_URL', SITE_URL . '/uploads');

// Upload Configuration
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_IMAGE_EXT', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Pagination
define('ITEMS_PER_PAGE', 12);
define('ADMIN_ITEMS_PER_PAGE', 20);

// Security
define('SESSION_LIFETIME', 3600); // 1 hour
define('CSRF_TOKEN_NAME', 'csrf_token');
define('REMEMBER_ME_DAYS', 30);

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    
    $conn->set_charset(DB_CHARSET);
    
    // Set timezone in MySQL
    $conn->query("SET time_zone = '+07:00'");
    
} catch (Exception $e) {
    die('เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $e->getMessage());
}

// PDO Connection (สำหรับใช้งาน prepared statements)
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die('เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล (PDO): ' . $e->getMessage());
}

// Auto-load functions
require_once INCLUDES_PATH . '/functions.php';

// Initialize CSRF token
if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
}
?>

