<?php
/**
 * VIBEDAYBKK Admin - Contacts List
 * รายการข้อความติดต่อ
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$page_title = 'ข้อความติดต่อ';
$current_page = 'contacts';

// Filter
$status_filter = $_GET['status'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = ADMIN_ITEMS_PER_PAGE;
$offset = ($page - 1) * $per_page;

$where = $status_filter ? "WHERE status = '{$status_filter}'" : '';

// Count total
$count_sql = "SELECT COUNT(*) as total FROM contacts {$where}";
$result = $conn->query($count_sql);
$total_contacts = $result->fetch_assoc()['total'];
$total_pages = ceil($total_contacts / $per_page);

// Get contacts
$sql = "SELECT * FROM contacts {$where} ORDER BY created_at DESC LIMIT {$per_page} OFFSET {$offset}";
$contacts = db_get_rows($conn, $sql);

include '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-envelope me-2"></i>ข้อความติดต่อ</h2>
        <p class="text-muted">จำนวนข้อความทั้งหมด: <?php echo $total_contacts; ?> ข้อความ</p>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <div class="btn-group" role="group">
            <a href="?" class="btn <?php echo empty($status_filter) ? 'btn-primary' : 'btn-outline-primary'; ?>">
                ทั้งหมด
            </a>
            <a href="?status=new" class="btn <?php echo $status_filter == 'new' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                <i class="fas fa-circle text-danger me-1"></i>ใหม่
            </a>
            <a href="?status=read" class="btn <?php echo $status_filter == 'read' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                อ่านแล้ว
            </a>
            <a href="?status=replied" class="btn <?php echo $status_filter == 'replied' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                ตอบกลับแล้ว
            </a>
            <a href="?status=closed" class="btn <?php echo $status_filter == 'closed' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                ปิด
            </a>
        </div>
    </div>
</div>

<!-- Contacts Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($contacts)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">ไม่มีข้อความ</h5>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>วันที่</th>
                            <th>ชื่อ</th>
                            <th>อีเมล</th>
                            <th>โทรศัพท์</th>
                            <th>ประเภทงาน</th>
                            <th>ข้อความ</th>
                            <th>สถานะ</th>
                            <th width="100" class="text-center">การกระทำ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $contact): ?>
                        <tr class="<?php echo $contact['status'] == 'new' ? 'table-warning' : ''; ?>">
                            <td>
                                <?php echo time_ago($contact['created_at']); ?>
                                <br>
                                <small class="text-muted"><?php echo format_date_thai($contact['created_at'], 'd/m/Y H:i'); ?></small>
                            </td>
                            <td>
                                <strong><?php echo $contact['name']; ?></strong>
                                <?php if ($contact['status'] == 'new'): ?>
                                    <span class="badge bg-danger ms-1">ใหม่</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $contact['email']; ?></td>
                            <td><?php echo $contact['phone']; ?></td>
                            <td><?php echo $contact['service_type'] ?: '-'; ?></td>
                            <td><?php echo truncate_text($contact['message'], 60); ?></td>
                            <td><?php echo get_status_badge($contact['status']); ?></td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="view.php?id=<?php echo $contact['id']; ?>" 
                                       class="btn btn-sm btn-primary" 
                                       title="ดู">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $contact['id']; ?>" 
                                       class="btn btn-sm btn-danger btn-delete" 
                                       title="ลบ">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

