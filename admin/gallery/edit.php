<?php
/**
 * Edit Gallery Image
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';


// Permission check
require_permission('gallery', 'edit');
$page_title = 'แก้ไขรูปภาพ';
$current_page = 'gallery';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    set_message('error', 'ไม่พบข้อมูล');
    redirect(ADMIN_URL . '/gallery/');
}

$stmt = $conn->prepare("SELECT * FROM gallery_images WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$image = $result->fetch_assoc();
$stmt->close();

if (!$image) {
    set_message('error', 'ไม่พบข้อมูล');
    redirect(ADMIN_URL . '/gallery/');
}

// Get albums
$albums = db_get_rows($conn, "SELECT * FROM gallery_albums WHERE status = 'active' ORDER BY sort_order ASC");

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $data = [
            'title' => clean_input($_POST['title']),
            'description' => clean_input($_POST['description']),
            'album_id' => !empty($_POST['album_id']) ? (int)$_POST['album_id'] : null,
            'tags' => clean_input($_POST['tags']),
            'status' => $_POST['status']
        ];
        
        if (db_update($conn, 'gallery_images', $data, 'id = ?', [$id])) {
            log_activity($conn, $_SESSION['user_id'], 'update', 'gallery_images', $id);
            $success = true;
            
            // Refresh data
            $stmt = $conn->prepare("SELECT * FROM gallery_images WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $image = $result->fetch_assoc();
            $stmt->close();
        } else {
            $errors[] = 'เกิดข้อผิดพลาด';
        }
    }
}

include '../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h2 class="text-3xl font-bold text-gray-900 flex items-center">
        <i class="fas fa-edit mr-3 text-blue-600"></i>แก้ไขรูปภาพ
    </h2>
    <a href="index.php" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
        <i class="fas fa-arrow-left mr-2"></i>กลับ
    </a>
</div>

<?php if ($success): ?>
<script>
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});
Toast.fire({
    icon: 'success',
    title: 'บันทึกข้อมูลเรียบร้อยแล้ว'
});
</script>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Image Preview -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden sticky top-6">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                <h5 class="text-white text-lg font-semibold flex items-center">
                    <i class="fas fa-image mr-3"></i>ตัวอย่างรูปภาพ
                </h5>
            </div>
            <div class="p-6">
                <img src="<?php echo BASE_URL . '/' . $image['file_path']; ?>" 
                     alt="<?php echo htmlspecialchars($image['title']); ?>"
                     class="w-full rounded-lg shadow-md mb-4">
                
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">ชื่อไฟล์:</span>
                        <span class="font-medium text-gray-900"><?php echo $image['file_name']; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">ขนาด:</span>
                        <span class="font-medium text-gray-900"><?php echo $image['width']; ?> × <?php echo $image['height']; ?> px</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">ขนาดไฟล์:</span>
                        <span class="font-medium text-gray-900"><?php echo number_format($image['file_size'] / 1024, 2); ?> KB</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">จำนวนดู:</span>
                        <span class="font-medium text-gray-900"><?php echo number_format($image['view_count']); ?> ครั้ง</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">ดาวน์โหลด:</span>
                        <span class="font-medium text-gray-900"><?php echo number_format($image['download_count']); ?> ครั้ง</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Form -->
    <div class="lg:col-span-2">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <h5 class="text-white text-lg font-semibold flex items-center">
                        <i class="fas fa-info-circle mr-3"></i>ข้อมูลรูปภาพ
                    </h5>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">ชื่อรูปภาพ</label>
                        <input type="text" name="title" value="<?php echo escape_html($image['title'] ?? ''); ?>" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">คำอธิบาย</label>
                        <textarea name="description" rows="4" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?php echo escape_html($image['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">อัลบั้ม</label>
                        <select name="album_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">ไม่ระบุอัลบั้ม</option>
                            <?php foreach ($albums as $album): ?>
                            <option value="<?php echo $album['id']; ?>" <?php echo $image['album_id'] == $album['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($album['title']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">แท็ก</label>
                        <input type="text" name="tags" value="<?php echo escape_html($image['tags'] ?? ''); ?>" 
                               placeholder="แฟชั่น, โมเดล, สตูดิโอ"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-sm text-gray-500 mt-1">คั่นด้วยเครื่องหมายจุลภาค (,)</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">สถานะ</label>
                        <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="active" <?php echo $image['status'] == 'active' ? 'selected' : ''; ?>>ใช้งาน</option>
                            <option value="inactive" <?php echo $image['status'] == 'inactive' ? 'selected' : ''; ?>>ไม่ใช้งาน</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-4">
                <a href="index.php" 
                   class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-times mr-2"></i>ยกเลิก
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 font-medium">
                    <i class="fas fa-save mr-2"></i>บันทึกการเปลี่ยนแปลง
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>




