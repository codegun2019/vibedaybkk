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

$stmt = $conn->prepare("SELECT * FROM contacts WHERE id = ?");
$stmt->bind_param('i', $contact_id);
$stmt->execute();
$result = $stmt->get_result();
$contact = $result->fetch_assoc();
$stmt->close();

if (!$contact) {
    set_message('error', 'ไม่พบข้อมูล');
    redirect(ADMIN_URL . '/contacts/');
}

// Mark as read if status is new
if ($contact['status'] == 'new') {
    $stmt = $conn->prepare("UPDATE contacts SET status = 'read' WHERE id = ?");
    $stmt->bind_param('i', $contact_id);
    $stmt->execute();
    $stmt->close();
    $contact['status'] = 'read'; // Update local variable
}

$errors = [];
$success = false;

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
        
        if (db_update($conn, 'contacts', $data, 'id = :id', ['id' => $contact_id])) {
            log_activity($conn, $_SESSION['user_id'], 'reply_contact', 'contacts', $contact_id);
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
$stmt = $conn->prepare("SELECT * FROM contacts WHERE id = ?");
$stmt->bind_param('i', $contact_id);
            $stmt->execute();
$result = $stmt->get_result();
$contact = $result->fetch_assoc();

include '../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-envelope-open mr-3 text-blue-600"></i>ข้อความติดต่อ #<?php echo $contact_id; ?>
        </h2>
        <p class="text-sm text-gray-500 mt-1">
            <?php echo time_ago($contact['created_at']); ?>
        </p>
    </div>
    <div class="flex space-x-3">
        <a href="index.php" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>กลับ
        </a>
        <a href="delete.php?id=<?php echo $contact_id; ?>" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium btn-delete">
            <i class="fas fa-trash mr-2"></i>ลบ
        </a>
    </div>
</div>

<!-- Status Actions -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <span class="text-sm font-semibold text-gray-700">สถานะ:</span>
            <?php 
            $status_classes = [
                'new' => 'bg-red-100 text-red-800',
                'read' => 'bg-blue-100 text-blue-800',
                'replied' => 'bg-green-100 text-green-800',
                'closed' => 'bg-gray-100 text-gray-800'
            ];
            $status_texts = [
                'new' => 'ใหม่',
                'read' => 'อ่านแล้ว',
                'replied' => 'ตอบกลับแล้ว',
                'closed' => 'ปิด'
            ];
            $status_class = $status_classes[$contact['status']] ?? 'bg-gray-100 text-gray-800';
            $status_text = $status_texts[$contact['status']] ?? $contact['status'];
            ?>
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium <?php echo $status_class; ?>">
                <?php echo $status_text; ?>
            </span>
        </div>
        <?php if ($contact['status'] != 'closed'): ?>
        <a href="?id=<?php echo $contact_id; ?>&action=closed" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-times-circle mr-2"></i>ปิดเคส
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <!-- Contact Info -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <h5 class="text-white text-lg font-semibold flex items-center">
                    <i class="fas fa-info-circle mr-3"></i>ข้อมูลผู้ติดต่อ
                </h5>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-semibold text-gray-500">ชื่อ</label>
                            <div class="mt-1 text-gray-900 font-medium text-lg"><?php echo $contact['name']; ?></div>
                        </div>
                        
                        <div>
                            <label class="text-sm font-semibold text-gray-500">อีเมล</label>
                            <div class="mt-1">
                                <a href="mailto:<?php echo $contact['email']; ?>" 
                                   class="text-blue-600 hover:text-blue-800 hover:underline">
                                    <?php echo $contact['email']; ?>
                                </a>
                            </div>
                        </div>
                        
                        <div>
                            <label class="text-sm font-semibold text-gray-500">โทรศัพท์</label>
                            <div class="mt-1">
                                <a href="tel:<?php echo $contact['phone']; ?>" 
                                   class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                    <?php echo $contact['phone']; ?>
                                </a>
                            </div>
                        </div>
                        
                        <div>
                            <label class="text-sm font-semibold text-gray-500">ประเภทงาน</label>
                            <div class="mt-1 text-gray-900"><?php echo $contact['service_type'] ?: '-'; ?></div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-semibold text-gray-500">วันที่ส่ง</label>
                            <div class="mt-1 text-gray-900"><?php echo format_date_thai($contact['created_at'], 'd/m/Y H:i:s'); ?></div>
                        </div>
                        
                        <div>
                            <label class="text-sm font-semibold text-gray-500">IP Address</label>
                            <div class="mt-1 text-gray-900 font-mono text-sm"><?php echo $contact['ip_address']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Message -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                <h5 class="text-white text-lg font-semibold flex items-center">
                    <i class="fas fa-comment mr-3"></i>ข้อความ
                </h5>
            </div>
            <div class="p-6">
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <p class="text-gray-900 leading-relaxed whitespace-pre-line"><?php echo htmlspecialchars($contact['message']); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Reply -->
        <?php if ($contact['reply_message']): ?>
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border-2 border-green-500">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h5 class="text-white text-lg font-semibold flex items-center">
                        <i class="fas fa-reply mr-3"></i>ข้อความตอบกลับ
                    </h5>
                    <span class="text-white text-sm">
                        ตอบกลับเมื่อ: <?php echo format_date_thai($contact['replied_at'], 'd/m/Y H:i'); ?>
                    </span>
                </div>
            </div>
            <div class="p-6">
                <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                    <p class="text-gray-900 leading-relaxed whitespace-pre-line"><?php echo htmlspecialchars($contact['reply_message']); ?></p>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                <h5 class="text-white text-lg font-semibold flex items-center">
                    <i class="fas fa-reply mr-3"></i>ตอบกลับ
                </h5>
            </div>
            <div class="p-6">
                <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                    <?php foreach ($errors as $error): ?>
                    <div><?php echo $error; ?></div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-4">
                        <textarea name="reply_message" rows="6" placeholder="พิมพ์ข้อความตอบกลับ..." 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" name="reply" 
                                class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200 font-medium shadow-lg">
                            <i class="fas fa-paper-plane mr-2"></i>ส่งข้อความตอบกลับ
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="space-y-6">
        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                <h5 class="text-white text-lg font-semibold flex items-center">
                    <i class="fas fa-bolt mr-3"></i>การกระทำ
                </h5>
            </div>
            <div class="p-6 space-y-3">
                <a href="index.php" 
                   class="w-full inline-flex items-center justify-center px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-list mr-2"></i>รายการทั้งหมด
                </a>
                
                <?php if ($contact['status'] != 'closed'): ?>
                <a href="?id=<?php echo $contact_id; ?>&action=closed" 
                   class="w-full inline-flex items-center justify-center px-4 py-3 bg-orange-100 hover:bg-orange-200 text-orange-800 rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-times-circle mr-2"></i>ปิดเคส
                </a>
                <?php endif; ?>
                
                <a href="delete.php?id=<?php echo $contact_id; ?>" 
                   class="w-full inline-flex items-center justify-center px-4 py-3 bg-red-100 hover:bg-red-200 text-red-800 rounded-lg transition-colors duration-200 font-medium btn-delete">
                    <i class="fas fa-trash mr-2"></i>ลบข้อความ
                </a>
            </div>
        </div>
        
        <!-- Contact Methods -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                <h5 class="text-white text-lg font-semibold flex items-center">
                    <i class="fas fa-phone mr-3"></i>ติดต่อกลับ
                </h5>
            </div>
            <div class="p-6 space-y-3">
                <a href="mailto:<?php echo $contact['email']; ?>" 
                   class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-100 hover:bg-blue-200 text-blue-800 rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-envelope mr-2"></i>ส่งอีเมล
                </a>
                
                <a href="tel:<?php echo $contact['phone']; ?>" 
                   class="w-full inline-flex items-center justify-center px-4 py-3 bg-green-100 hover:bg-green-200 text-green-800 rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-phone mr-2"></i>โทรกลับ
                </a>
            </div>
        </div>
        
        <!-- Timeline -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
                <h5 class="text-white text-lg font-semibold flex items-center">
                    <i class="fas fa-history mr-3"></i>Timeline
                </h5>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-paper-plane text-blue-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h6 class="font-semibold text-gray-900">ส่งข้อความ</h6>
                            <p class="text-sm text-gray-500"><?php echo format_date_thai($contact['created_at'], 'd/m/Y H:i'); ?></p>
                        </div>
                    </div>
                    
                    <?php if ($contact['status'] != 'new'): ?>
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-eye text-green-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h6 class="font-semibold text-gray-900">อ่านแล้ว</h6>
                            <p class="text-sm text-gray-500">โดยแอดมิน</p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($contact['replied_at']): ?>
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-reply text-purple-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h6 class="font-semibold text-gray-900">ตอบกลับแล้ว</h6>
                            <p class="text-sm text-gray-500"><?php echo format_date_thai($contact['replied_at'], 'd/m/Y H:i'); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($contact['status'] == 'closed'): ?>
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-times-circle text-gray-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h6 class="font-semibold text-gray-900">ปิดเคส</h6>
                            <p class="text-sm text-gray-500">เรียบร้อยแล้ว</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>


