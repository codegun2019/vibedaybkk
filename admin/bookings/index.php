<?php
/**
 * VIBEDAYBKK Admin - Bookings List
 * รายการการจองทั้งหมด
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$page_title = 'จัดการการจอง';
$current_page = 'bookings';

// Filter
$status_filter = $_GET['status'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = ADMIN_ITEMS_PER_PAGE;
$offset = ($page - 1) * $per_page;

$where = $status_filter ? "WHERE b.status = '{$status_filter}'" : '';

// Count total
$count_sql = "SELECT COUNT(*) as total FROM bookings b {$where}";
$result = $conn->query($count_sql);
$total_bookings = $result->fetch_assoc()['total'];
$total_pages = ceil($total_bookings / $per_page);

// Get bookings
$sql = "SELECT b.*, m.name as model_name, m.code as model_code
        FROM bookings b
        LEFT JOIN models m ON b.model_id = m.id
        {$where}
        ORDER BY b.created_at DESC
        LIMIT {$per_page} OFFSET {$offset}";
$bookings = db_get_rows($conn, $sql);

// Calculate stats
$stats = [
    'total' => $conn->query("SELECT COUNT(*) as c FROM bookings")->fetch_assoc()['c'],
    'pending' => $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status = 'pending'")->fetch_assoc()['c'],
    'confirmed' => $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status = 'confirmed'")->fetch_assoc()['c'],
    'completed' => $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status = 'completed'")->fetch_assoc()['c'],
    'revenue' => $conn->query("SELECT COALESCE(SUM(total_price), 0) as c FROM bookings WHERE status = 'completed'")->fetch_assoc()['c']
];

include '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-calendar-check me-2"></i>จัดการการจอง</h2>
        <p class="text-muted">จำนวนการจองทั้งหมด: <?php echo $stats['total']; ?> รายการ</p>
    </div>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-warning"><?php echo $stats['pending']; ?></h3>
                <p class="mb-0 text-muted">รอดำเนินการ</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-success"><?php echo $stats['confirmed']; ?></h3>
                <p class="mb-0 text-muted">ยืนยันแล้ว</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-primary"><?php echo $stats['completed']; ?></h3>
                <p class="mb-0 text-muted">เสร็จสิ้น</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-info"><?php echo format_price($stats['revenue']); ?></h3>
                <p class="mb-0 text-muted">รายได้รวม</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <div class="btn-group" role="group">
            <a href="?" class="btn <?php echo empty($status_filter) ? 'btn-primary' : 'btn-outline-primary'; ?>">ทั้งหมด</a>
            <a href="?status=pending" class="btn <?php echo $status_filter == 'pending' ? 'btn-primary' : 'btn-outline-primary'; ?>">รอดำเนินการ</a>
            <a href="?status=confirmed" class="btn <?php echo $status_filter == 'confirmed' ? 'btn-primary' : 'btn-outline-primary'; ?>">ยืนยันแล้ว</a>
            <a href="?status=completed" class="btn <?php echo $status_filter == 'completed' ? 'btn-primary' : 'btn-outline-primary'; ?>">เสร็จสิ้น</a>
            <a href="?status=cancelled" class="btn <?php echo $status_filter == 'cancelled' ? 'btn-primary' : 'btn-outline-primary'; ?>">ยกเลิก</a>
        </div>
    </div>
</div>

<!-- Bookings Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($bookings)): ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">ไม่มีการจอง</h5>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>รหัส</th>
                            <th>โมเดล</th>
                            <th>ลูกค้า</th>
                            <th>โทรศัพท์</th>
                            <th>วันที่จอง</th>
                            <th>จำนวนวัน</th>
                            <th>ราคา</th>
                            <th>สถานะ</th>
                            <th width="100" class="text-center">การกระทำ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                        <tr class="<?php echo $booking['status'] == 'pending' ? 'table-warning' : ''; ?>">
                            <td><strong>#<?php echo $booking['id']; ?></strong></td>
                            <td>
                                <?php echo $booking['model_name']; ?>
                                <br><small class="text-muted"><?php echo $booking['model_code']; ?></small>
                            </td>
                            <td><?php echo $booking['customer_name']; ?></td>
                            <td><?php echo $booking['customer_phone']; ?></td>
                            <td><?php echo format_date_thai($booking['booking_date']); ?></td>
                            <td><?php echo $booking['booking_days']; ?> วัน</td>
                            <td><?php echo format_price($booking['total_price']); ?></td>
                            <td><?php echo get_status_badge($booking['status']); ?></td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="view.php?id=<?php echo $booking['id']; ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $booking['id']; ?>" 
                                       class="btn btn-sm btn-danger btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

