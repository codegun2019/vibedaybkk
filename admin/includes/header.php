<?php
/**
 * VIBEDAYBKK Admin - Header
 * ส่วน Header สำหรับหน้า Admin ทุกหน้า
 */

if (!defined('VIBEDAYBKK_ADMIN')) {
    die('Direct access not permitted');
}

// ต้อง login ก่อน
require_login();

// ดึงข้อมูล user
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
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS (optional) -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: #f8f9fa;
        }
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: 260px;
            background: linear-gradient(180deg, #1e293b 0%, #334155 100%);
            color: white;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1000;
        }
        .sidebar-header {
            padding: 20px;
            background: rgba(220, 38, 38, 0.2);
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-header h4 {
            margin: 0;
            font-size: 24px;
        }
        .sidebar-menu {
            padding: 20px 0;
        }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #cbd5e1;
            text-decoration: none;
            transition: all 0.3s;
        }
        .sidebar-menu a:hover {
            background: rgba(220, 38, 38, 0.2);
            color: white;
            padding-left: 30px;
        }
        .sidebar-menu a.active {
            background: #DC2626;
            color: white;
        }
        .sidebar-menu a i {
            width: 25px;
            margin-right: 10px;
        }
        .main-content {
            margin-left: 260px;
            padding: 0;
            min-height: 100vh;
        }
        .top-navbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .content-wrapper {
            padding: 30px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .card-header {
            background: white;
            border-bottom: 2px solid #f1f5f9;
            padding: 20px;
            font-weight: 600;
        }
        .btn-primary {
            background: #DC2626;
            border-color: #DC2626;
        }
        .btn-primary:hover {
            background: #B91C1C;
            border-color: #B91C1C;
        }
        .badge {
            padding: 5px 10px;
        }
        .table {
            font-size: 14px;
        }
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -260px;
            }
            .sidebar.show {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    
    <?php if (isset($extra_css)): ?>
        <?php echo $extra_css; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-star mb-2" style="font-size: 30px; color: #DC2626;"></i>
            <h4>VIBEDAYBKK</h4>
            <small>Admin Panel</small>
        </div>
        
        <div class="sidebar-menu">
            <a href="<?php echo ADMIN_URL; ?>/index.php" class="<?php echo ($current_page ?? '') == 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="<?php echo ADMIN_URL; ?>/models/" class="<?php echo ($current_page ?? '') == 'models' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>จัดการโมเดล</span>
            </a>
            
            <a href="<?php echo ADMIN_URL; ?>/categories/" class="<?php echo ($current_page ?? '') == 'categories' ? 'active' : ''; ?>">
                <i class="fas fa-th-large"></i>
                <span>จัดการหมวดหมู่</span>
            </a>
            
            <a href="<?php echo ADMIN_URL; ?>/articles/" class="<?php echo ($current_page ?? '') == 'articles' ? 'active' : ''; ?>">
                <i class="fas fa-newspaper"></i>
                <span>จัดการบทความ</span>
            </a>
            
            <a href="<?php echo ADMIN_URL; ?>/menus/" class="<?php echo ($current_page ?? '') == 'menus' ? 'active' : ''; ?>">
                <i class="fas fa-bars"></i>
                <span>จัดการเมนู</span>
            </a>
            
            <a href="<?php echo ADMIN_URL; ?>/contacts/" class="<?php echo ($current_page ?? '') == 'contacts' ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i>
                <span>ข้อความติดต่อ</span>
            </a>
            
            <a href="<?php echo ADMIN_URL; ?>/bookings/" class="<?php echo ($current_page ?? '') == 'bookings' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check"></i>
                <span>การจอง</span>
            </a>
            
            <a href="<?php echo ADMIN_URL; ?>/settings/" class="<?php echo ($current_page ?? '') == 'settings' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span>ตั้งค่าระบบ</span>
            </a>
            
            <?php if (is_admin()): ?>
            <a href="<?php echo ADMIN_URL; ?>/users/" class="<?php echo ($current_page ?? '') == 'users' ? 'active' : ''; ?>">
                <i class="fas fa-user-shield"></i>
                <span>จัดการผู้ใช้</span>
            </a>
            <?php endif; ?>
            
            <hr style="border-color: rgba(255,255,255,0.1); margin: 20px;">
            
            <a href="<?php echo SITE_URL; ?>" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                <span>ดูหน้าเว็บ</span>
            </a>
            
            <a href="<?php echo ADMIN_URL; ?>/logout.php" onclick="return confirm('ต้องการออกจากระบบ?')">
                <i class="fas fa-sign-out-alt"></i>
                <span>ออกจากระบบ</span>
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div>
                <button class="btn btn-link d-md-none" id="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="text-muted">ยินดีต้อนรับ, <strong><?php echo $full_name; ?></strong></span>
            </div>
            <div>
                <span class="badge bg-primary"><?php echo $user_role == 'admin' ? 'ผู้ดูแลระบบ' : 'ผู้แก้ไข'; ?></span>
                <a href="<?php echo ADMIN_URL; ?>/logout.php" class="btn btn-sm btn-outline-danger ms-2" onclick="return confirm('ต้องการออกจากระบบ?')">
                    <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                </a>
            </div>
        </div>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <?php
            // Display flash message
            $message = get_message();
            if ($message):
            ?>
            <div class="alert alert-<?php echo $message['type']; ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?php 
                    echo $message['type'] == 'success' ? 'check-circle' : 
                        ($message['type'] == 'error' ? 'exclamation-circle' : 'info-circle'); 
                ?> me-2"></i>
                <?php echo $message['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

