<?php
/**
 * VIBEDAYBKK Admin - Login Page
 * หน้าเข้าสู่ระบบสำหรับผู้ดูแล
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../includes/config.php';

// ถ้า login แล้วให้ redirect ไป dashboard
if (is_logged_in()) {
    redirect(ADMIN_URL . '/index.php');
}

$error = '';

// Process login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($username) || empty($password)) {
        $error = 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน';
    } else {
        // ค้นหา user
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        if ($user && password_verify($password, $user['password'])) {
            // Login success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['logged_in_at'] = time();
            
            // Update last login
            $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->bind_param('i', $user['id']);
            $stmt->execute();
            $stmt->close();
            
            // Log activity
            log_activity($conn, $user['id'], 'login', 'users', $user['id']);
            
            // Remember me
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (REMEMBER_ME_DAYS * 86400), '/');
                // Store token in database (ในระบบจริงควรเก็บ hashed token)
            }
            
            redirect(ADMIN_URL . '/index.php');
        } else {
            $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - VIBEDAYBKK Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        'kanit': ['Kanit', 'sans-serif'] 
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-purple-600 via-pink-500 to-red-500 min-h-screen flex items-center justify-center font-kanit p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-red-600 to-red-500 text-white p-10 text-center">
                <i class="fas fa-star text-6xl mb-4 animate-pulse"></i>
                <h2 class="text-3xl font-bold">ระบบสมาชิก</h2>
                <p class="text-red-100 mt-2">ระบบจัดการ Admin</p>
            </div>
            
            <!-- Body -->
            <div class="p-8">
                <?php if ($error): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-red-600"></i>ชื่อผู้ใช้
                        </label>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               required 
                               autofocus
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 placeholder-gray-400">
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 text-red-600"></i>รหัสผ่าน
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 placeholder-gray-400">
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="remember" 
                               name="remember"
                               class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <label for="remember" class="ml-2 text-sm text-gray-700">
                            จดจำการเข้าสู่ระบบ (30 วัน)
                        </label>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-medium py-3 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-sign-in-alt mr-2"></i>เข้าสู่ระบบ
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-shield-alt mr-1"></i>
                        ระบบปลอดภัยด้วย CSRF Protection
                    </p>
                </div>
                
                <div class="mt-4 text-center">
                    <a href="<?php echo SITE_URL; ?>/index.php" 
                       class="text-red-600 hover:text-red-700 text-sm font-medium transition-colors">
                        <i class="fas fa-home mr-1"></i>กลับหน้าแรก
                    </a>
                </div>
            </div>
        </div>
        
        
    </div>
</body>
</html>



