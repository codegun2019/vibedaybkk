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
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Login success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['logged_in_at'] = time();
            
            // Update last login
            $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);
            
            // Log activity
            log_activity($pdo, $user['id'], 'login', 'users', $user['id']);
            
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #DC2626 0%, #EF4444 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .login-header i {
            font-size: 60px;
            margin-bottom: 15px;
        }
        .login-body {
            padding: 40px 30px;
        }
        .form-control:focus {
            border-color: #DC2626;
            box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #DC2626 0%, #EF4444 100%);
            border: none;
            padding: 12px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #B91C1C 0%, #DC2626 100%);
        }
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-star"></i>
            <h2 class="mb-0">VIBEDAYBKK</h2>
            <p class="mb-0 mt-2">ระบบจัดการ Admin</p>
        </div>
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="fas fa-user me-2"></i>ชื่อผู้ใช้
                    </label>
                    <input type="text" class="form-control form-control-lg" id="username" name="username" required autofocus>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-2"></i>รหัสผ่าน
                    </label>
                    <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        จดจำการเข้าสู่ระบบ
                    </label>
                </div>
                
                <button type="submit" class="btn btn-danger btn-login w-100 btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ
                </button>
            </form>
            
            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>
                    ระบบปลอดภัยด้วย CSRF Protection
                </small>
            </div>
            
            <div class="text-center mt-3">
                <a href="<?php echo SITE_URL; ?>" class="text-decoration-none">
                    <i class="fas fa-home me-1"></i>กลับหน้าแรก
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

