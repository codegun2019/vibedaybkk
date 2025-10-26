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
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        'kanit': ['Kanit', 'sans-serif'] 
                    },
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
        body, input, textarea, select, button {
            font-family: 'Noto Sans Thai', sans-serif;
            font-size: 12.25px;
        }
        
        /* Keep Font Awesome icons */
        i, .fa, .fas, .far, .fal, .fab {
            font-family: 'Font Awesome 6 Free', 'Font Awesome 6 Brands' !important;
        }
        
        h1 { font-size: 28px; }
        h2 { font-size: 24px; }
        h3 { font-size: 21px; }
        h4 { font-size: 18px; }
        .sidebar-link.active { 
            background: linear-gradient(135deg, #DC2626 0%, #EF4444 100%); 
            color: white; 
        }
        .sidebar-link:hover {
            background-color: rgba(220, 38, 38, 0.1);
        }
        
        /* Toggle Switch Styles */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 56px;
            height: 28px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: .3s;
            border-radius: 28px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .toggle-switch input:checked + .toggle-slider {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .toggle-switch input:checked + .toggle-slider:before {
            transform: translateX(28px);
        }
        
        .toggle-switch input:disabled + .toggle-slider {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Toggle Switch Hover Effect */
        .toggle-slider:hover {
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 0 8px rgba(16, 185, 129, 0.3);
        }
        
        .toggle-switch input:checked + .toggle-slider:hover {
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 0 12px rgba(16, 185, 129, 0.5);
        }
    </style>
    <?php if (isset($extra_css)): ?><?php echo $extra_css; ?><?php endif; ?>
</head>
<body class="bg-gray-50 font-kanit">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-72 bg-gradient-to-b from-slate-800 to-slate-900 text-white flex-shrink-0 hidden lg:block overflow-y-auto">
            <div class="p-6 border-b border-slate-700 bg-red-600/20">
                <div class="text-center">
                    <i class="fas fa-star text-4xl text-red-500 mb-2"></i>
                    <h2 class="text-xl font-bold">VIBEDAYBKK</h2>
                    <p class="text-sm text-slate-400">Admin Panel</p>
                </div>
            </div>
            
            <nav class="p-4 space-y-1">
                <a href="<?php echo ADMIN_URL; ?>/index.php" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'dashboard' ? 'active' : ''; ?> flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-tachometer-alt w-5 mr-3"></i>
                    <span>Dashboard</span>
                </a>
                
                <?php if (has_permission('models', 'view')): ?>
                <a href="<?php echo ADMIN_URL; ?>/models/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'models' ? 'active' : ''; ?> flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-users w-5 mr-3"></i>
                    <span>จัดการโมเดล</span>
                    <?php if (!has_permission('models', 'edit')): ?>
                    <span class="ml-auto text-xs bg-yellow-500/20 text-yellow-300 px-2 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-lock text-xs"></i>ปลดล็อค
                    </span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                
                <?php if (has_permission('categories', 'view')): ?>
                <a href="<?php echo ADMIN_URL; ?>/categories/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'categories' ? 'active' : ''; ?> flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-th-large w-5 mr-3"></i>
                    <span>จัดการหมวดหมู่</span>
                    <?php if (!has_permission('categories', 'edit')): ?>
                    <span class="ml-auto text-xs bg-yellow-500/20 text-yellow-300 px-2 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-lock text-xs"></i>ปลดล็อค
                    </span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                
                <?php if (has_permission('homepage', 'view')): ?>
                <a href="<?php echo ADMIN_URL; ?>/homepage/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'homepage' ? 'active' : ''; ?> flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-home w-5 mr-3"></i>
                    <span>จัดการหน้าแรก</span>
                    <?php if (!has_permission('homepage', 'edit')): ?>
                    <span class="ml-auto text-xs bg-yellow-500/20 text-yellow-300 px-2 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-lock text-xs"></i>ปลดล็อค
                    </span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                
                <?php if (has_permission('articles', 'view')): ?>
                <a href="<?php echo ADMIN_URL; ?>/articles/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'articles' ? 'active' : ''; ?> flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-newspaper w-5 mr-3"></i>
                    <span>จัดการบทความ</span>
                    <?php if (!has_permission('articles', 'edit')): ?>
                    <span class="ml-auto text-xs bg-yellow-500/20 text-yellow-300 px-2 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-lock text-xs"></i>ปลดล็อค
                    </span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                
                <?php if (has_permission('article_categories', 'view')): ?>
                <a href="<?php echo ADMIN_URL; ?>/article-categories/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'article-categories' ? 'active' : ''; ?> flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-folder-open w-5 mr-3"></i>
                    <span>หมวดหมู่บทความ</span>
                    <?php if (!has_permission('article_categories', 'edit')): ?>
                    <span class="ml-auto text-xs bg-yellow-500/20 text-yellow-300 px-2 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-lock text-xs"></i>ปลดล็อค
                    </span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                
                <?php if (has_permission('gallery', 'view')): ?>
                <a href="<?php echo ADMIN_URL; ?>/gallery/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'gallery' ? 'active' : ''; ?> flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-images w-5 mr-3"></i>
                    <span>แกลเลอรี่</span>
                    <?php if (!has_permission('gallery', 'edit')): ?>
                    <span class="ml-auto text-xs bg-yellow-500/20 text-yellow-300 px-2 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-lock text-xs"></i>ปลดล็อค
                    </span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                
                <?php if (has_permission('homepage', 'view')): ?>
                <a href="<?php echo ADMIN_URL; ?>/reviews/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'reviews' ? 'active' : ''; ?> flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-comments w-5 mr-3"></i>
                    <span>รีวิวลูกค้า</span>
                    <?php if (!has_permission('homepage', 'edit')): ?>
                    <span class="ml-auto text-xs bg-yellow-500/20 text-yellow-300 px-2 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-lock text-xs"></i>ปลดล็อค
                    </span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                
                <?php if (has_permission('menus', 'view')): ?>
                <a href="<?php echo ADMIN_URL; ?>/menus/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'menus' ? 'active' : ''; ?> flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-bars w-5 mr-3"></i>
                    <span>จัดการเมนู</span>
                    <?php if (!has_permission('menus', 'edit')): ?>
                    <span class="ml-auto text-xs bg-yellow-500/20 text-yellow-300 px-2 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-lock text-xs"></i>ปลดล็อค
                    </span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                
                <?php if (has_permission('contacts', 'view')): ?>
                <a href="<?php echo ADMIN_URL; ?>/contacts/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'contacts' ? 'active' : ''; ?> flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-envelope w-5 mr-3"></i>
                    <span>ข้อความติดต่อ</span>
                    <?php if (!has_permission('contacts', 'edit')): ?>
                    <span class="ml-auto text-xs bg-yellow-500/20 text-yellow-300 px-2 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-lock text-xs"></i>ปลดล็อค
                    </span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                
                <?php if (has_permission('bookings', 'view')): ?>
                <a href="<?php echo ADMIN_URL; ?>/bookings/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'bookings' ? 'active' : ''; ?> flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-calendar-check w-5 mr-3"></i>
                    <span>การจอง</span>
                    <?php if (!has_permission('bookings', 'edit')): ?>
                    <span class="ml-auto text-xs bg-yellow-500/20 text-yellow-300 px-2 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-lock text-xs"></i>ปลดล็อค
                    </span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                
                <?php if (has_permission('settings', 'view')): ?>
                <a href="<?php echo ADMIN_URL; ?>/settings/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'settings' ? 'active' : ''; ?> flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-cog w-5 mr-3"></i>
                    <span>ตั้งค่าระบบ</span>
                    <?php if (!has_permission('settings', 'edit')): ?>
                    <span class="ml-auto text-xs bg-yellow-500/20 text-yellow-300 px-2 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-lock text-xs"></i>ปลดล็อค
                    </span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                
                <?php if (has_permission('users', 'view')): ?>
                <a href="<?php echo ADMIN_URL; ?>/users/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'users' ? 'active' : ''; ?> flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-user-shield w-5 mr-3"></i>
                    <span>จัดการผู้ใช้</span>
                    <?php if (!has_permission('users', 'edit')): ?>
                    <span class="ml-auto text-xs bg-yellow-500/20 text-yellow-300 px-2 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-lock text-xs"></i>ปลดล็อค
                    </span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                
                <?php if (is_programmer()): ?>
                <a href="<?php echo ADMIN_URL; ?>/roles/" 
                   class="sidebar-link <?php echo ($current_page ?? '') == 'roles' ? 'active' : ''; ?> flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-purple-600/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-user-tag w-5 mr-3"></i>
                    <span>จัดการบทบาท</span>
                </a>
                <?php endif; ?>
                
                <hr class="border-slate-700 my-4">
                
                <a href="<?php echo SITE_URL; ?>" target="_blank" 
                   class="flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-all duration-200">
                    <i class="fas fa-external-link-alt w-5 mr-3"></i>
                    <span>ดูหน้าเว็บ</span>
                </a>
                
                <a href="<?php echo ADMIN_URL; ?>/logout.php" onclick="return confirm('ต้องการออกจากระบบ?')"
                   class="flex items-center px-4 py-3 rounded-lg text-red-400 hover:bg-red-600/20 hover:text-red-300 transition-all duration-200">
                    <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                    <span>ออกจากระบบ</span>
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navbar -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <button id="sidebar-toggle" class="lg:hidden mr-4 text-gray-600 hover:text-gray-900 p-2 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <div class="flex flex-col">
                            <span class="text-gray-600 text-sm">ยินดีต้อนรับ</span>
                            <span class="text-gray-900 font-semibold"><?php echo $full_name; ?></span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">
                            <?php echo $user_role == 'admin' ? 'ผู้ดูแลระบบ' : 'ผู้แก้ไข'; ?>
                        </span>
                        <a href="<?php echo ADMIN_URL; ?>/logout.php" 
                           onclick="return confirm('ต้องการออกจากระบบ?')"
                           class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 text-sm font-medium">
                            <i class="fas fa-sign-out-alt mr-2"></i> ออกจากระบบ
                        </a>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">


