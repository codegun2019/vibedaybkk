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

// Clean input data for database storage (NO HTML encoding)
function clean_input($data) {
    if (is_array($data)) {
        return array_map('clean_input', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

// Escape for safe HTML output (รองรับ null ใน PHP 8.1+)
function escape_html($data) {
    if (is_array($data)) {
        return array_map('escape_html', $data);
    }
    // แปลง null เป็น empty string ก่อนส่งเข้า htmlspecialchars
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

// Helper function สำหรับ htmlspecialchars ที่รองรับ null
function h($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

// Sanitize for database (for raw queries)
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
    return is_logged_in() && in_array($_SESSION['user_role'], ['admin', 'programmer']);
}

// Check if user is programmer
function is_programmer() {
    return is_logged_in() && $_SESSION['user_role'] === 'programmer';
}

// Check if user is editor
function is_editor() {
    return is_logged_in() && in_array($_SESSION['user_role'], ['editor', 'admin', 'programmer']);
}

// Check if user is viewer or higher
function is_viewer() {
    return is_logged_in();
}

// Get user role level
function get_user_role_level() {
    if (!is_logged_in()) {
        return 0;
    }
    
    $role_levels = [
        'programmer' => 100,
        'admin' => 80,
        'editor' => 50,
        'viewer' => 10
    ];
    
    return $role_levels[$_SESSION['user_role']] ?? 0;
}

// Check if user has permission for a feature
function has_permission($feature, $action = 'view') {
    global $conn;
    
    if (!is_logged_in()) {
        return false;
    }
    
    // Programmer has all permissions
    if ($_SESSION['user_role'] === 'programmer') {
        return true;
    }
    
    $role_key = $_SESSION['user_role'];
    
    // Check in permissions table
    $stmt = $conn->prepare("SELECT can_view, can_create, can_edit, can_delete, can_export FROM permissions WHERE role_key = ? AND feature = ?");
    $stmt->bind_param('ss', $role_key, $feature);
    $stmt->execute();
    $result = $stmt->get_result();
    $perm = $result->fetch_assoc();
    $stmt->close();
    
    if (!$perm) {
        return false;
    }
    
    switch ($action) {
        case 'view':
            return $perm['can_view'] == 1;
        case 'create':
        case 'add':
            return $perm['can_create'] == 1;
        case 'edit':
        case 'update':
            return $perm['can_edit'] == 1;
        case 'delete':
            return $perm['can_delete'] == 1;
        case 'export':
            return $perm['can_export'] == 1;
        default:
            return false;
    }
}

// Require specific permission (soft check - allow view but lock actions)
function require_permission($feature, $action = 'view') {
    // Always allow view permission to show locked UI
    if ($action === 'view') {
        return true;
    }
    
    // For create/edit/delete, redirect if no permission
    if (!has_permission($feature, $action)) {
        set_message('error', 'คุณไม่มีสิทธิ์ทำการนี้ - กรุณาอัพเกรดบทบาท');
        redirect(ADMIN_URL . '/index.php');
    }
}

// Get user's role info
function get_user_role_info($role_key = null) {
    global $conn;
    
    // ถ้าไม่ส่ง role_key มา ให้ใช้ของ current user
    if ($role_key === null) {
        if (!is_logged_in()) {
            return null;
        }
        $role_key = $_SESSION['user_role'];
    }
    
    $stmt = $conn->prepare("SELECT * FROM roles WHERE role_key = ?");
    $stmt->bind_param('s', $role_key);
    $stmt->execute();
    $result = $stmt->get_result();
    $role = $result->fetch_assoc();
    $stmt->close();
    
    // ถ้าไม่เจอใน database ให้คืนค่า default
    if (!$role) {
        return [
            'role_key' => $role_key,
            'display_name' => ucfirst($role_key),
            'icon' => 'fas fa-user',
            'color' => 'gray'
        ];
    }
    
    return $role;
}

// Get all available roles
function get_available_roles() {
    global $conn;
    
    $roles = [];
    $result = $conn->query("SELECT * FROM roles WHERE is_active = 1 ORDER BY level DESC");
    while ($row = $result->fetch_assoc()) {
        $roles[$row['role_key']] = $row;
    }
    
    return $roles;
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

// Get single row (supports prepared statements)
function db_get_row($conn, $sql, $params = []) {
    if (!empty($params)) {
        // Use prepared statement with mysqli
        $stmt = $conn->prepare($sql);
        if (!$stmt) return null;
        
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_assoc() : null;
    } else {
        // Regular query
        $result = db_query($conn, $sql);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
}

// Get all rows (supports prepared statements)
function db_get_rows($conn, $sql, $params = []) {
    if (!empty($params)) {
        // Use prepared statement with mysqli
        $stmt = $conn->prepare($sql);
        if (!$stmt) return [];
        
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    } else {
        // Regular query
        $result = db_query($conn, $sql);
        $rows = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }
}

// Insert with prepared statement (MySQLi)
function db_insert($conn, $table, $data) {
    $keys = array_keys($data);
    $fields = implode(', ', $keys);
    $placeholders = str_repeat('?,', count($data) - 1) . '?';
    
    $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";
    
    try {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $types = '';
        $values = [];
        foreach ($data as $value) {
            // ตรวจสอบว่าเป็น array หรือไม่
            if (is_array($value)) {
                $value = json_encode($value);
                $types .= 's';
            } elseif (is_null($value)) {
                $types .= 's';
            } elseif (is_int($value)) {
                $types .= 'i';
            } elseif (is_double($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
            $values[] = $value;
        }
        
        if (empty($types)) {
            throw new Exception("No data to insert");
        }
        
        $stmt->bind_param($types, ...$values);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    } catch (Exception $e) {
        error_log("Insert Error: " . $e->getMessage());
        return false;
    }
}

// Update with prepared statement (MySQLi)
function db_update($conn, $table, $data, $where, $whereParams = []) {
    $setParts = [];
    foreach (array_keys($data) as $key) {
        $setParts[] = "{$key} = ?";
    }
    $setString = implode(', ', $setParts);
    
    $sql = "UPDATE {$table} SET {$setString} WHERE {$where}";
    
    try {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $types = '';
        $values = [];
        
        // Add data values
        foreach ($data as $value) {
            // ตรวจสอบว่าเป็น array หรือไม่
            if (is_array($value)) {
                $value = json_encode($value);
                $types .= 's';
            } elseif (is_null($value)) {
                $types .= 's';
            } elseif (is_int($value)) {
                $types .= 'i';
            } elseif (is_double($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
            $values[] = $value;
        }
        
        // Add where params
        foreach ($whereParams as $value) {
            // ตรวจสอบว่าเป็น array หรือไม่
            if (is_array($value)) {
                $value = json_encode($value);
                $types .= 's';
            } elseif (is_null($value)) {
                $types .= 's';
            } elseif (is_int($value)) {
                $types .= 'i';
            } elseif (is_double($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
            $values[] = $value;
        }
        
        $stmt->bind_param($types, ...$values);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    } catch (Exception $e) {
        error_log("Update Error: " . $e->getMessage());
        return false;
    }
}

// Execute query with prepared statement (MySQLi)
function db_execute($conn, $sql, $params = []) {
    try {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        if (!empty($params)) {
            $types = '';
            foreach ($params as $value) {
                if (is_int($value)) {
                    $types .= 'i';
                } elseif (is_double($value)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }
            
            $stmt->bind_param($types, ...$params);
        }
        
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    } catch (Exception $e) {
        error_log("Execute Error: " . $e->getMessage());
        return false;
    }
}

// Delete with prepared statement (MySQLi)
function db_delete($conn, $table, $where, $whereParams = []) {
    $sql = "DELETE FROM {$table} WHERE {$where}";
    
    try {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        if (!empty($whereParams)) {
            $types = '';
            foreach ($whereParams as $value) {
                if (is_int($value)) {
                    $types .= 'i';
                } elseif (is_double($value)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }
            
            $stmt->bind_param($types, ...$whereParams);
        }
        
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    } catch (Exception $e) {
        error_log("Delete Error: " . $e->getMessage());
        return false;
    }
}

/**
 * File Upload Functions
 */

// Upload image
function upload_image($file, $folder = 'general', $createThumbnail = true) {
    try {
        // Validate file
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'ไม่มีไฟล์หรือเกิดข้อผิดพลาดในการอัพโหลด (Error: ' . ($file['error'] ?? 'unknown') . ')'];
        }
        
        // Check file size
        if ($file['size'] > MAX_FILE_SIZE) {
            return ['success' => false, 'error' => 'ไฟล์มีขนาดใหญ่เกินไป (สูงสุด ' . (MAX_FILE_SIZE / 1024 / 1024) . ' MB)'];
        }
        
        // Check file extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_IMAGE_EXT)) {
            return ['success' => false, 'error' => 'นามสกุลไฟล์ไม่ถูกต้อง (รองรับเฉพาะ: ' . implode(', ', ALLOWED_IMAGE_EXT) . ')'];
        }
        
        // Verify it's an image and get dimensions
        if (!function_exists('getimagesize')) {
            return ['success' => false, 'error' => 'ฟังก์ชัน getimagesize ไม่พร้อมใช้งาน'];
        }
        
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return ['success' => false, 'error' => 'ไฟล์ไม่ใช่รูปภาพที่ถูกต้อง'];
        }
        
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $mimeType = $imageInfo['mime'];
        
        // Create upload directory if not exists
        $uploadDir = ROOT_PATH . '/uploads/' . $folder;
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return ['success' => false, 'error' => 'ไม่สามารถสร้างโฟลเดอร์ได้'];
            }
        }
        
        // Generate unique filename
        $newFilename = uniqid() . '_' . time() . '.' . $ext;
        $uploadPath = $uploadDir . '/' . $newFilename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return ['success' => false, 'error' => 'ไม่สามารถย้ายไฟล์ได้'];
        }
        
        // เก็บ path แบบ 'general/filename.png' (ไม่ใส่ uploads/ เพราะ UPLOADS_URL มีอยู่แล้ว)
        $relativePath = $folder . '/' . $newFilename;
        
        // Create thumbnail if requested and GD is available
        $thumbnailPath = null;
        if ($createThumbnail && function_exists('imagecreatefromjpeg') && function_exists('imagecreatetruecolor')) {
            try {
                $thumbDir = $uploadDir . '/thumbs';
                if (!file_exists($thumbDir)) {
                    mkdir($thumbDir, 0755, true);
                }
                
                $thumbFilename = 'thumb_' . $newFilename;
                $thumbPath = $thumbDir . '/' . $thumbFilename;
                
                // Create thumbnail (300x300)
                if (create_thumbnail($uploadPath, $thumbPath, 300, 300)) {
                    $thumbnailPath = $folder . '/thumbs/' . $thumbFilename;
                }
            } catch (Exception $e) {
                // Thumbnail creation failed, but upload succeeded
                error_log('Thumbnail creation failed: ' . $e->getMessage());
            }
        }
        
        return [
            'success' => true,
            'filename' => $newFilename,
            'file_path' => $relativePath, // เช่น 'general/filename.png'
            'thumbnail_path' => $thumbnailPath,
            'url' => UPLOADS_URL . '/' . $relativePath, // ได้ http://localhost/vibedaybkk/uploads/general/filename.png
            'width' => $width,
            'height' => $height,
            'mime_type' => $mimeType
        ];
        
    } catch (Exception $e) {
        error_log('Upload error: ' . $e->getMessage());
        return ['success' => false, 'error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
    }
}

// Create thumbnail
function create_thumbnail($source, $destination, $maxWidth, $maxHeight) {
    try {
        $imageInfo = getimagesize($source);
        if (!$imageInfo) return false;
        
        $srcWidth = $imageInfo[0];
        $srcHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];
        
        // Calculate new dimensions
        $ratio = min($maxWidth / $srcWidth, $maxHeight / $srcHeight);
        $newWidth = round($srcWidth * $ratio);
        $newHeight = round($srcHeight * $ratio);
        
        // Create source image
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                $srcImage = imagecreatefromjpeg($source);
                break;
            case 'image/png':
                $srcImage = imagecreatefrompng($source);
                break;
            case 'image/gif':
                $srcImage = imagecreatefromgif($source);
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $srcImage = imagecreatefromwebp($source);
                } else {
                    return false;
                }
                break;
            default:
                return false;
        }
        
        if (!$srcImage) return false;
        
        // Create thumbnail
        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        if ($mimeType == 'image/png' || $mimeType == 'image/gif') {
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
            $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
            imagefilledrectangle($thumb, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Resize
        imagecopyresampled($thumb, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);
        
        // Save thumbnail
        $result = false;
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                $result = imagejpeg($thumb, $destination, 85);
                break;
            case 'image/png':
                $result = imagepng($thumb, $destination, 8);
                break;
            case 'image/gif':
                $result = imagegif($thumb, $destination);
                break;
            case 'image/webp':
                if (function_exists('imagewebp')) {
                    $result = imagewebp($thumb, $destination, 85);
                }
                break;
        }
        
        // Clean up
        imagedestroy($srcImage);
        imagedestroy($thumb);
        
        return $result;
        
    } catch (Exception $e) {
        error_log('Thumbnail creation error: ' . $e->getMessage());
        return false;
    }
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

// Show SweetAlert2 Toast message
function show_alert() {
    $message = get_message();
    if ($message) {
        $icons = [
            'success' => 'success',
            'error' => 'error',
            'warning' => 'warning',
            'info' => 'info'
        ];
        $icon = $icons[$message['type']] ?? 'info';
        
        $titles = [
            'success' => 'สำเร็จ!',
            'error' => 'เกิดข้อผิดพลาด!',
            'warning' => 'คำเตือน!',
            'info' => 'แจ้งเตือน'
        ];
        $title = $titles[$message['type']] ?? 'แจ้งเตือน';
        
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                },
                customClass: {
                    popup: 'rounded-xl shadow-2xl',
                    title: 'text-base font-semibold'
                }
            });
            
            Toast.fire({
                icon: '{$icon}',
                title: '{$title}',
                text: '" . addslashes($message['message']) . "'
            });
        });
        </script>";
    }
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
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        return $_SERVER['REMOTE_ADDR'];
    } else {
        return '127.0.0.1'; // Fallback for CLI
    }
}

// Log activity
function log_activity($conn, $userId, $action, $tableName = null, $recordId = null, $description = null) {
    // Convert description to new_values for compatibility
    $newValues = $description;
    
    $data = [
        'user_id' => $userId,
        'action' => $action,
        'table_name' => $tableName,
        'record_id' => $recordId,
        'old_values' => null,
        'new_values' => $newValues,
        'ip_address' => get_client_ip(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
    ];
    
    return db_insert($conn, 'activity_logs', $data);
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

/**
 * Settings Functions
 */

// Get all settings as associative array
function get_all_settings($conn) {
    $settings = [];
    $result = $conn->query("SELECT setting_key, setting_value FROM settings");
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    return $settings;
}

// Get single setting value
function get_setting($conn, $key, $default = '') {
    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->bind_param('s', $key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['setting_value'];
    }
    
    return $default;
}

// Update or create setting
function update_setting($conn, $key, $value, $type = 'text', $category = 'general') {
    $stmt = $conn->prepare("
        INSERT INTO settings (setting_key, setting_value, setting_type, category) 
        VALUES (?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE setting_value = ?, setting_type = ?, category = ?
    ");
    $stmt->bind_param('sssssss', $key, $value, $type, $category, $value, $type, $category);
    $success = $stmt->execute();
    $stmt->close();
    
    return $success;
}

// Delete setting
function delete_setting($conn, $key) {
    $stmt = $conn->prepare("DELETE FROM settings WHERE setting_key = ?");
    $stmt->bind_param('s', $key);
    $success = $stmt->execute();
    $stmt->close();
    
    return $success;
}



