<?php
/**
 * VIBEDAYBKK - Functions Library
 * ฟังก์ชันที่ใช้ร่วมกันทั้งระบบ
 */

// Prevent direct access
if (!defined('VIBEDAYBKK_ADMIN')) {
    die('Direct access not permitted');
}

/**
 * Security Functions
 */

// Clean input data
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Sanitize for database
function sanitize_db($conn, $data) {
    return $conn->real_escape_string(trim($data));
}

// Generate CSRF Token
function generate_csrf_token() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

// Verify CSRF Token
function verify_csrf_token($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// Generate random string
function generate_random_string($length = 10) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Authentication Functions
 */

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

// Check if user is admin
function is_admin() {
    return is_logged_in() && $_SESSION['user_role'] === 'admin';
}

// Require login
function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . ADMIN_URL . '/login.php');
        exit;
    }
}

// Require admin
function require_admin() {
    require_login();
    if (!is_admin()) {
        $_SESSION['error'] = 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้';
        header('Location: ' . ADMIN_URL . '/dashboard.php');
        exit;
    }
}

/**
 * Database Functions
 */

// Execute query with error handling
function db_query($conn, $sql) {
    $result = $conn->query($sql);
    if (!$result) {
        error_log("Database Error: " . $conn->error . " | Query: " . $sql);
        return false;
    }
    return $result;
}

// Get single row
function db_get_row($conn, $sql) {
    $result = db_query($conn, $sql);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Get all rows
function db_get_rows($conn, $sql) {
    $result = db_query($conn, $sql);
    $rows = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    return $rows;
}

// Insert with prepared statement (PDO)
function db_insert($pdo, $table, $data) {
    $keys = array_keys($data);
    $fields = implode(', ', $keys);
    $placeholders = ':' . implode(', :', $keys);
    
    $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";
    
    try {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($data);
    } catch (PDOException $e) {
        error_log("Insert Error: " . $e->getMessage());
        return false;
    }
}

// Update with prepared statement (PDO)
function db_update($pdo, $table, $data, $where, $whereParams = []) {
    $setParts = [];
    foreach (array_keys($data) as $key) {
        $setParts[] = "{$key} = :{$key}";
    }
    $setString = implode(', ', $setParts);
    
    $sql = "UPDATE {$table} SET {$setString} WHERE {$where}";
    
    try {
        $stmt = $pdo->prepare($sql);
        $params = array_merge($data, $whereParams);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Update Error: " . $e->getMessage());
        return false;
    }
}

// Delete with prepared statement (PDO)
function db_delete($pdo, $table, $where, $whereParams = []) {
    $sql = "DELETE FROM {$table} WHERE {$where}";
    
    try {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($whereParams);
    } catch (PDOException $e) {
        error_log("Delete Error: " . $e->getMessage());
        return false;
    }
}

/**
 * File Upload Functions
 */

// Upload image
function upload_image($file, $folder = 'general') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'ไม่มีไฟล์หรือเกิดข้อผิดพลาดในการอัพโหลด'];
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'ไฟล์มีขนาดใหญ่เกินไป (สูงสุด ' . (MAX_FILE_SIZE / 1024 / 1024) . ' MB)'];
    }
    
    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'message' => 'ประเภทไฟล์ไม่ถูกต้อง'];
    }
    
    // Get file extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_IMAGE_EXT)) {
        return ['success' => false, 'message' => 'นามสกุลไฟล์ไม่ถูกต้อง'];
    }
    
    // Create upload directory if not exists
    $uploadDir = UPLOADS_PATH . '/' . $folder;
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $newFilename = uniqid() . '_' . time() . '.' . $ext;
    $uploadPath = $uploadDir . '/' . $newFilename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $relativePath = $folder . '/' . $newFilename;
        return [
            'success' => true,
            'filename' => $newFilename,
            'path' => $relativePath,
            'url' => UPLOADS_URL . '/' . $relativePath
        ];
    }
    
    return ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการย้ายไฟล์'];
}

// Delete image
function delete_image($imagePath) {
    $fullPath = UPLOADS_PATH . '/' . $imagePath;
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    return false;
}

/**
 * Utility Functions
 */

// Redirect
function redirect($url) {
    header("Location: {$url}");
    exit;
}

// Set flash message
function set_message($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

// Get and clear flash message
function get_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// Generate slug from string
function generate_slug($string) {
    $string = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    $string = trim($string, '-');
    return strtolower($string);
}

// Format date Thai
function format_date_thai($date, $format = 'd/m/Y') {
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return '-';
    }
    return date($format, strtotime($date));
}

// Format price
function format_price($price) {
    return '฿' . number_format($price, 2);
}

// Format number
function format_number($number) {
    return number_format($number);
}

// Time ago
function time_ago($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'เมื่อสักครู่';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' นาทีที่แล้ว';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' ชั่วโมงที่แล้ว';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' วันที่แล้ว';
    } else {
        return format_date_thai($datetime);
    }
}

// Pagination
function get_pagination($total, $perPage, $currentPage, $url) {
    $totalPages = ceil($total / $perPage);
    $html = '<nav aria-label="Page navigation"><ul class="pagination">';
    
    // Previous button
    if ($currentPage > 1) {
        $prevPage = $currentPage - 1;
        $html .= '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . $prevPage . '">ก่อนหน้า</a></li>';
    }
    
    // Page numbers
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = ($i == $currentPage) ? 'active' : '';
        $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . $url . '?page=' . $i . '">' . $i . '</a></li>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $nextPage = $currentPage + 1;
        $html .= '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . $nextPage . '">ถัดไป</a></li>';
    }
    
    $html .= '</ul></nav>';
    return $html;
}

// Truncate text
function truncate_text($text, $length = 100, $suffix = '...') {
    if (mb_strlen($text) > $length) {
        return mb_substr($text, 0, $length) . $suffix;
    }
    return $text;
}

// Get client IP
function get_client_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Log activity
function log_activity($pdo, $userId, $action, $tableName = null, $recordId = null, $oldValues = null, $newValues = null) {
    $data = [
        'user_id' => $userId,
        'action' => $action,
        'table_name' => $tableName,
        'record_id' => $recordId,
        'old_values' => $oldValues ? json_encode($oldValues) : null,
        'new_values' => $newValues ? json_encode($newValues) : null,
        'ip_address' => get_client_ip(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
    ];
    
    return db_insert($pdo, 'activity_logs', $data);
}

/**
 * Validation Functions
 */

// Validate email
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Validate phone
function validate_phone($phone) {
    return preg_match('/^[0-9]{9,10}$/', $phone);
}

// Validate required field
function validate_required($value) {
    return !empty(trim($value));
}

// Validate min length
function validate_min_length($value, $min) {
    return mb_strlen(trim($value)) >= $min;
}

// Validate max length
function validate_max_length($value, $max) {
    return mb_strlen(trim($value)) <= $max;
}

/**
 * Model/Category Functions
 */

// Get all categories
function get_categories($conn, $status = 'active') {
    $where = $status ? "WHERE status = '{$status}'" : '';
    $sql = "SELECT * FROM categories {$where} ORDER BY sort_order ASC, name ASC";
    return db_get_rows($conn, $sql);
}

// Get category by ID
function get_category($conn, $id) {
    $id = (int)$id;
    $sql = "SELECT * FROM categories WHERE id = {$id}";
    return db_get_row($conn, $sql);
}

// Get models by category
function get_models_by_category($conn, $categoryId, $status = 'available') {
    $categoryId = (int)$categoryId;
    $where = $status ? "AND m.status = '{$status}'" : '';
    $sql = "SELECT m.*, c.name as category_name 
            FROM models m 
            LEFT JOIN categories c ON m.category_id = c.id 
            WHERE m.category_id = {$categoryId} {$where}
            ORDER BY m.featured DESC, m.sort_order ASC, m.name ASC";
    return db_get_rows($conn, $sql);
}

// Get model by ID
function get_model($conn, $id) {
    $id = (int)$id;
    $sql = "SELECT m.*, c.name as category_name, c.code as category_code
            FROM models m 
            LEFT JOIN categories c ON m.category_id = c.id 
            WHERE m.id = {$id}";
    return db_get_row($conn, $sql);
}

// Get model images
function get_model_images($conn, $modelId) {
    $modelId = (int)$modelId;
    $sql = "SELECT * FROM model_images WHERE model_id = {$modelId} ORDER BY is_primary DESC, sort_order ASC";
    return db_get_rows($conn, $sql);
}

// Get primary image
function get_primary_image($conn, $modelId) {
    $modelId = (int)$modelId;
    $sql = "SELECT * FROM model_images WHERE model_id = {$modelId} AND is_primary = 1 LIMIT 1";
    $image = db_get_row($conn, $sql);
    if (!$image) {
        $sql = "SELECT * FROM model_images WHERE model_id = {$modelId} ORDER BY sort_order ASC LIMIT 1";
        $image = db_get_row($conn, $sql);
    }
    return $image;
}

/**
 * Display category badge color
 */
function get_category_badge_color($gender) {
    $colors = [
        'female' => 'badge-pink',
        'male' => 'badge-blue',
        'all' => 'badge-secondary'
    ];
    return $colors[$gender] ?? 'badge-secondary';
}

/**
 * Get logo HTML
 */
function get_logo($settings) {
    $logo_type = $settings['logo_type'] ?? 'text';
    
    if ($logo_type == 'image' && !empty($settings['logo_image'])) {
        return '<img src="' . UPLOADS_URL . '/' . $settings['logo_image'] . '" alt="' . ($settings['site_name'] ?? 'VIBEDAYBKK') . '" style="height: 40px;">';
    } else {
        $logo_text = $settings['logo_text'] ?? $settings['site_name'] ?? 'VIBEDAYBKK';
        return '<i class="fas fa-star mr-2"></i>' . $logo_text;
    }
}

/**
 * Get favicon HTML
 */
function get_favicon($settings) {
    if (!empty($settings['favicon'])) {
        return '<link rel="icon" type="image/x-icon" href="' . UPLOADS_URL . '/' . $settings['favicon'] . '">';
    }
    return '';
}

/**
 * Display status badge
 */
function get_status_badge($status) {
    $badges = [
        'active' => '<span class="badge badge-success">ใช้งาน</span>',
        'inactive' => '<span class="badge badge-secondary">ไม่ใช้งาน</span>',
        'available' => '<span class="badge badge-success">ว่าง</span>',
        'busy' => '<span class="badge badge-warning">ไม่ว่าง</span>',
        'draft' => '<span class="badge badge-secondary">แบบร่าง</span>',
        'published' => '<span class="badge badge-success">เผยแพร่แล้ว</span>',
        'archived' => '<span class="badge badge-dark">เก็บถาวร</span>',
        'new' => '<span class="badge badge-primary">ใหม่</span>',
        'read' => '<span class="badge badge-info">อ่านแล้ว</span>',
        'replied' => '<span class="badge badge-success">ตอบกลับแล้ว</span>',
        'closed' => '<span class="badge badge-secondary">ปิด</span>',
        'pending' => '<span class="badge badge-warning">รอดำเนินการ</span>',
        'confirmed' => '<span class="badge badge-success">ยืนยันแล้ว</span>',
        'cancelled' => '<span class="badge badge-danger">ยกเลิก</span>',
        'completed' => '<span class="badge badge-success">เสร็จสิ้น</span>',
    ];
    return $badges[$status] ?? '<span class="badge badge-secondary">' . $status . '</span>';
}
?>

