<?php
/**
 * VIBEDAYBKK Admin - View Contact
 * ดูและตอบกลับข้อความติดต่อ
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$page_title = 'ดูข้อความติดต่อ';
$current_page = 'contacts';

$contact_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$contact_id) {
    set_message('error', 'ไม่พบข้อมูล');
    redirect(ADMIN_URL . '/contacts/');
}

$stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
$stmt->execute([$contact_id]);
$contact = $stmt->fetch();

if (!$contact) {
    set_message('error', 'ไม่พบข้อมูล');
    redirect(ADMIN_URL . '/contacts/');
}

// Mark as read if status is new
if ($contact['status'] == 'new') {
    $conn->query("UPDATE contacts SET status = 'read' WHERE id = {$contact_id}");
}

$errors = [];

// Handle reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    $reply_message = trim($_POST['reply_message']);
    
    if (empty($reply_message)) {
        $errors[] = 'กรุณากรอกข้อความตอบกลับ';
    } else {
        $data = [
            'status' => 'replied',
            'reply_message' => $reply_message,
            'replied_at' => date('Y-m-d H:i:s'),
            'replied_by' => $_SESSION['user_id']
        ];
        
        if (db_update($pdo, 'contacts', $data, 'id = :id', ['id' => $contact_id])) {
            // ในระบบจริงควรส่งอีเมลตอบกลับ
            log_activity($pdo, $_SESSION['user_id'], 'reply_contact', 'contacts', $contact_id);
            set_message('success', 'ส่งข้อความตอบกลับสำเร็จ');
            redirect(ADMIN_URL . '/contacts/view.php?id=' . $contact_id);
        }
    }
}

// Handle status change
if (isset($_GET['action'])) {
    $new_status = $_GET['action'];
    if (in_array($new_status, ['read', 'replied', 'closed'])) {
        $conn->query("UPDATE contacts SET status = '{$new_status}' WHERE id = {$contact_id}");
        set_message('success', 'เปลี่ยนสถานะสำเร็จ');
        redirect(ADMIN_URL . '/contacts/view.php?id=' . $contact_id);
    }
}

// Refresh data
$stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
$stmt->execute([$contact_id]);
$contact = $stmt->fetch();

include '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-envelope-open me-2"></i>ข้อความติดต่อ #<?php echo $contact_id; ?></h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>กลับ
        </a>
        <a href="delete.php?id=<?php echo $contact_id; ?>" class="btn btn-danger btn-delete">
            <i class="fas fa-trash me-2"></i>ลบ
        </a>
    </div>
</div>

<!-- Contact Info -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>ข้อมูลผู้ติดต่อ</h5>
        <div>
            <?php echo get_status_badge($contact['status']); ?>
            <?php if ($contact['status'] != 'closed'): ?>
            <a href="?id=<?php echo $contact_id; ?>&action=closed" class="btn btn-sm btn-secondary ms-2">
                <i class="fas fa-times-circle me-1"></i>ปิดเคส
            </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="150">ชื่อ:</th>
                        <td><?php echo $contact['name']; ?></td>
                    </tr>
                    <tr>
                        <th>อีเมล:</th>
                        <td><a href="mailto:<?php echo $contact['email']; ?>"><?php echo $contact['email']; ?></a></td>
                    </tr>
                    <tr>
                        <th>โทรศัพท์:</th>
                        <td><a href="tel:<?php echo $contact['phone']; ?>"><?php echo $contact['phone']; ?></a></td>
                    </tr>
                    <tr>
                        <th>ประเภทงาน:</th>
                        <td><?php echo $contact['service_type'] ?: '-'; ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="150">วันที่ส่ง:</th>
                        <td><?php echo format_date_thai($contact['created_at'], 'd/m/Y H:i:s'); ?></td>
                    </tr>
                    <tr>
                        <th>IP Address:</th>
                        <td><?php echo $contact['ip_address']; ?></td>
                    </tr>
                    <tr>
                        <th>สถานะ:</th>
                        <td><?php echo get_status_badge($contact['status']); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Message -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-comment me-2"></i>ข้อความ</h5>
    </div>
    <div class="card-body">
        <div class="p-3 bg-light rounded">
            <?php echo nl2br(htmlspecialchars($contact['message'])); ?>
        </div>
    </div>
</div>

<!-- Reply -->
<?php if ($contact['reply_message']): ?>
<div class="card mb-4 border-success">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-reply me-2"></i>ข้อความตอบกลับ</h5>
        <small>ตอบกลับเมื่อ: <?php echo format_date_thai($contact['replied_at'], 'd/m/Y H:i:s'); ?></small>
    </div>
    <div class="card-body">
        <div class="p-3 bg-light rounded">
            <?php echo nl2br(htmlspecialchars($contact['reply_message'])); ?>
        </div>
    </div>
</div>
<?php else: ?>
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-reply me-2"></i>ตอบกลับ</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
            <div><?php echo $error; ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <textarea class="form-control" name="reply_message" rows="5" placeholder="พิมพ์ข้อความตอบกลับ..."></textarea>
            </div>
            <div class="text-end">
                <button type="submit" name="reply" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>ส่งข้อความตอบกลับ
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>

