<?php
/**
 * VIBEDAYBKK Admin - Dashboard
 * หน้าแรกของระบบ Admin
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../includes/config.php';

$page_title = 'Dashboard';
$current_page = 'dashboard';

// ดึงสถิติต่างๆ
$stats = [];

// จำนวนโมเดลทั้งหมด
$result = $conn->query("SELECT COUNT(*) as total FROM models");
$stats['total_models'] = $result->fetch_assoc()['total'];

// จำนวนโมเดลที่ว่าง
$result = $conn->query("SELECT COUNT(*) as total FROM models WHERE status = 'available'");
$stats['available_models'] = $result->fetch_assoc()['total'];

// จำนวนหมวดหมู่
$result = $conn->query("SELECT COUNT(*) as total FROM categories WHERE status = 'active'");
$stats['total_categories'] = $result->fetch_assoc()['total'];

// จำนวนบทความ
$result = $conn->query("SELECT COUNT(*) as total FROM articles WHERE status = 'published'");
$stats['published_articles'] = $result->fetch_assoc()['total'];

// ข้อความติดต่อใหม่
$result = $conn->query("SELECT COUNT(*) as total FROM contacts WHERE status = 'new'");
$stats['new_contacts'] = $result->fetch_assoc()['total'];

// การจองใหม่
$result = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE status = 'pending'");
$stats['pending_bookings'] = $result->fetch_assoc()['total'];

// รายได้รวม
$result = $conn->query("SELECT COALESCE(SUM(total_price), 0) as total FROM bookings WHERE status = 'completed'");
$stats['total_revenue'] = $result->fetch_assoc()['total'];

// โมเดลยอดนิยม (Top 5)
$top_models = db_get_rows($conn, "
    SELECT m.*, c.name as category_name,
           COUNT(b.id) as booking_count
    FROM models m
    LEFT JOIN categories c ON m.category_id = c.id
    LEFT JOIN bookings b ON m.id = b.model_id AND b.status = 'completed'
    GROUP BY m.id
    ORDER BY booking_count DESC, m.view_count DESC
    LIMIT 5
");

// ข้อความติดต่อล่าสุด
$recent_contacts = db_get_rows($conn, "
    SELECT * FROM contacts
    ORDER BY created_at DESC
    LIMIT 5
");

// การจองล่าสุด
$recent_bookings = db_get_rows($conn, "
    SELECT b.*, m.name as model_name
    FROM bookings b
    LEFT JOIN models m ON b.model_id = m.id
    ORDER BY b.created_at DESC
    LIMIT 5
");

// Activity logs ล่าสุด
$recent_activities = db_get_rows($conn, "
    SELECT a.*, u.full_name
    FROM activity_logs a
    LEFT JOIN users u ON a.user_id = u.id
    ORDER BY a.created_at DESC
    LIMIT 10
");

include 'includes/header.php';
?>

<div class="row">
    <div class="col-12 mb-4">
        <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
        <p class="text-muted">ภาพรวมของระบบ VIBEDAYBKK</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <!-- Total Models -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2" style="border-left: 4px solid #DC2626;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #DC2626;">
                            โมเดลทั้งหมด
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_models']; ?> คน</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Models -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2" style="border-left: 4px solid #10B981;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            โมเดลว่าง
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['available_models']; ?> คน</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Bookings -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2" style="border-left: 4px solid #F59E0B;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            การจองรอดำเนินการ
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['pending_bookings']; ?> รายการ</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2" style="border-left: 4px solid #3B82F6;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            รายได้รวม
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo format_price($stats['total_revenue']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- More Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-th-large fa-3x text-primary mb-3"></i>
                <h3><?php echo $stats['total_categories']; ?></h3>
                <p class="text-muted mb-0">หมวดหมู่บริการ</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-newspaper fa-3x text-success mb-3"></i>
                <h3><?php echo $stats['published_articles']; ?></h3>
                <p class="text-muted mb-0">บทความเผยแพร่</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-envelope fa-3x text-warning mb-3"></i>
                <h3><?php echo $stats['new_contacts']; ?></h3>
                <p class="text-muted mb-0">ข้อความใหม่</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Models -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-star me-2"></i>โมเดลยอดนิยม (Top 5)
            </div>
            <div class="card-body">
                <?php if (empty($top_models)): ?>
                    <p class="text-muted text-center py-3">ยังไม่มีข้อมูล</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>รหัส</th>
                                    <th>ชื่อ</th>
                                    <th>หมวดหมู่</th>
                                    <th class="text-center">จองสำเร็จ</th>
                                    <th class="text-center">Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_models as $model): ?>
                                <tr>
                                    <td><?php echo $model['code']; ?></td>
                                    <td><?php echo $model['name']; ?></td>
                                    <td><span class="badge bg-secondary"><?php echo $model['category_name']; ?></span></td>
                                    <td class="text-center"><?php echo $model['booking_count']; ?></td>
                                    <td class="text-center"><?php echo $model['view_count']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Contacts -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-envelope me-2"></i>ข้อความติดต่อล่าสุด
            </div>
            <div class="card-body">
                <?php if (empty($recent_contacts)): ?>
                    <p class="text-muted text-center py-3">ยังไม่มีข้อความ</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($recent_contacts as $contact): ?>
                        <a href="<?php echo ADMIN_URL; ?>/contacts/view.php?id=<?php echo $contact['id']; ?>" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?php echo $contact['name']; ?></h6>
                                <small><?php echo time_ago($contact['created_at']); ?></small>
                            </div>
                            <p class="mb-1 small"><?php echo truncate_text($contact['message'], 60); ?></p>
                            <small><?php echo get_status_badge($contact['status']); ?></small>
                        </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Bookings -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-calendar-check me-2"></i>การจองล่าสุด
            </div>
            <div class="card-body">
                <?php if (empty($recent_bookings)): ?>
                    <p class="text-muted text-center py-3">ยังไม่มีการจอง</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>รหัส</th>
                                    <th>โมเดล</th>
                                    <th>ลูกค้า</th>
                                    <th>วันที่จอง</th>
                                    <th>ราคา</th>
                                    <th>สถานะ</th>
                                    <th>วันที่</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_bookings as $booking): ?>
                                <tr>
                                    <td>#<?php echo $booking['id']; ?></td>
                                    <td><?php echo $booking['model_name']; ?></td>
                                    <td><?php echo $booking['customer_name']; ?></td>
                                    <td><?php echo format_date_thai($booking['booking_date']); ?></td>
                                    <td><?php echo format_price($booking['total_price']); ?></td>
                                    <td><?php echo get_status_badge($booking['status']); ?></td>
                                    <td><?php echo time_ago($booking['created_at']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bolt me-2"></i>Quick Actions
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo ADMIN_URL; ?>/models/add.php" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-plus-circle mb-2 d-block fa-2x"></i>
                            เพิ่มโมเดลใหม่
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo ADMIN_URL; ?>/categories/add.php" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-plus-circle mb-2 d-block fa-2x"></i>
                            เพิ่มหมวดหมู่
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo ADMIN_URL; ?>/articles/add.php" class="btn btn-info btn-lg w-100">
                            <i class="fas fa-plus-circle mb-2 d-block fa-2x"></i>
                            เพิ่มบทความ
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo ADMIN_URL; ?>/models/" class="btn btn-warning btn-lg w-100">
                            <i class="fas fa-users mb-2 d-block fa-2x"></i>
                            ดูโมเดลทั้งหมด
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

