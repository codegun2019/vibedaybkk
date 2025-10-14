<?php
/**
 * VIBEDAYBKK Admin - Header (Tailwind CSS)
 * ส่วน Header สำหรับหน้า Admin ทุกหน้า
 */

if (!defined('VIBEDAYBKK_ADMIN')) {
    die('Direct access not permitted');
}

require_login();

$current_user = $_SESSION['username'];
$user_role = $_SESSION['user_role'];
$full_name = $_SESSION['full_name'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Dashboard'; ?> - VIBEDAYBKK Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 'kanit': ['Kanit', 'sans-serif'] },
                    colors: {
                        'admin-dark': '#1e293b',
                        'admin-darker': '#0f172a',
                        'admin-light': '#334155',
                        'red-primary': '#DC2626',
                        'red-light': '#EF4444',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Kanit', sans-serif; }
        .sidebar-link.active { background: linear-gradient(135deg, #DC2626 0%, #EF4444 100%); color: white; }
    </style>
    <?php if (isset($extra_css)): ?><?php echo $extra_css; ?><?php endif; ?>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-admin-dark to-admin-darker text-white flex-shrink-0 hidden md:block overflow-y-auto">
            <div class="p-6 border-b border-gray-700 bg-red-primary/20">
                <div class="text-center">
                    <i class="fas fa-star text-4xl text-red-primary mb-2"></i>
                    <h2 class="text-xl font-bold">VIBEDAYBKK</h2>
                    <p class="text-sm text-gray-400">Admin Panel</p>
                </div>
            </div>
            
            <nav class="p-4">
                <a href="<?php echo ADMIN_URL; ?>/index.php" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'dashboard' ? 'active' : ''; ?> flex items-center px-4 py-3 mb-2 rounded-lg text-gray-300 hover:bg-red-primary/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-tachometer-alt w-6"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="<?php echo ADMIN_URL; ?>/models/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'models' ? 'active' : ''; ?> flex items-center px-4 py-3 mb-2 rounded-lg text-gray-300 hover:bg-red-primary/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-users w-6"></i>
                    <span>จัดการโมเดล</span>
                </a>
                
                <a href="<?php echo ADMIN_URL; ?>/categories/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'categories' ? 'active' : ''; ?> flex items-center px-4 py-3 mb-2 rounded-lg text-gray-300 hover:bg-red-primary/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-th-large w-6"></i>
                    <span>จัดการหมวดหมู่</span>
                </a>
                
                <a href="<?php echo ADMIN_URL; ?>/articles/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'articles' ? 'active' : ''; ?> flex items-center px-4 py-3 mb-2 rounded-lg text-gray-300 hover:bg-red-primary/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-newspaper w-6"></i>
                    <span>จัดการบทความ</span>
                </a>
                
                <a href="<?php echo ADMIN_URL; ?>/menus/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'menus' ? 'active' : ''; ?> flex items-center px-4 py-3 mb-2 rounded-lg text-gray-300 hover:bg-red-primary/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-bars w-6"></i>
                    <span>จัดการเมนู</span>
                </a>
                
                <a href="<?php echo ADMIN_URL; ?>/contacts/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'contacts' ? 'active' : ''; ?> flex items-center px-4 py-3 mb-2 rounded-lg text-gray-300 hover:bg-red-primary/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-envelope w-6"></i>
                    <span>ข้อความติดต่อ</span>
                </a>
                
                <a href="<?php echo ADMIN_URL; ?>/bookings/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'bookings' ? 'active' : ''; ?> flex items-center px-4 py-3 mb-2 rounded-lg text-gray-300 hover:bg-red-primary/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-calendar-check w-6"></i>
                    <span>การจอง</span>
                </a>
                
                <a href="<?php echo ADMIN_URL; ?>/settings/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'settings' ? 'active' : ''; ?> flex items-center px-4 py-3 mb-2 rounded-lg text-gray-300 hover:bg-red-primary/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-cog w-6"></i>
                    <span>ตั้งค่าระบบ</span>
                </a>
                
                <?php if (is_admin()): ?>
                <a href="<?php echo ADMIN_URL; ?>/users/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'users' ? 'active' : ''; ?> flex items-center px-4 py-3 mb-2 rounded-lg text-gray-300 hover:bg-red-primary/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-user-shield w-6"></i>
                    <span>จัดการผู้ใช้</span>
                </a>
                <?php endif; ?>
                
                <hr class="border-gray-700 my-4">
                
                <a href="<?php echo SITE_URL; ?>" target="_blank" 
                   class="flex items-center px-4 py-3 mb-2 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-all duration-200">
                    <i class="fas fa-external-link-alt w-6"></i>
                    <span>ดูหน้าเว็บ</span>
                </a>
                
                <a href="<?php echo ADMIN_URL; ?>/logout.php" onclick="return confirm('ต้องการออกจากระบบ?')"
                   class="flex items-center px-4 py-3 rounded-lg text-red-400 hover:bg-red-primary/20 hover:text-red-300 transition-all duration-200">
                    <i class="fas fa-sign-out-alt w-6"></i>
                    <span>ออกจากระบบ</span>
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navbar -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <button id="sidebar-toggle" class="md:hidden mr-4 text-gray-600 hover:text-gray-900">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <span class="text-gray-600">ยินดีต้อนรับ, <strong class="text-gray-900"><?php echo $full_name; ?></strong></span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">
                            <?php echo $user_role == 'admin' ? 'ผู้ดูแลระบบ' : 'ผู้แก้ไข'; ?>
                        </span>
                        <a href="<?php echo ADMIN_URL; ?>/logout.php" 
                           onclick="return confirm('ต้องการออกจากระบบ?')"
                           class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 text-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i> ออกจากระบบ
                        </a>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <?php
                $message = get_message();
                if ($message):
                    $colors = [
                        'success' => 'bg-green-100 border-green-400 text-green-700',
                        'error' => 'bg-red-100 border-red-400 text-red-700',
                        'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
                        'info' => 'bg-blue-100 border-blue-400 text-blue-700'
                    ];
                    $icons = [
                        'success' => 'fa-check-circle',
                        'error' => 'fa-exclamation-circle',
                        'warning' => 'fa-exclamation-triangle',
                        'info' => 'fa-info-circle'
                    ];
                    $color = $colors[$message['type']] ?? $colors['info'];
                    $icon = $icons[$message['type']] ?? $icons['info'];
                ?>
                <div class="<?php echo $color; ?> border-l-4 p-4 mb-6 rounded-r-lg" role="alert">
                    <div class="flex items-center">
                        <i class="fas <?php echo $icon; ?> mr-3 text-xl"></i>
                        <span><?php echo $message['message']; ?></span>
                    </div>
                </div>
                <?php endif; ?>
