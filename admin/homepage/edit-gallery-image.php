<?php
/**
 * VIBEDAYBKK Admin - Edit Gallery Image
 * แก้ไขรูปภาพในแกลเลอรี่
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check - allow editors to edit gallery
require_permission('homepage', 'edit');
$can_delete = has_permission('homepage', 'delete');

$page_title = 'แก้ไขรูปภาพ';
$current_page = 'homepage';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];
$success = false;

// Get image data
$image = db_get_row($conn, "SELECT g.*, s.section_name, s.id as section_id 
                             FROM homepage_gallery g 
                             JOIN homepage_sections s ON g.section_id = s.id 
                             WHERE g.id = ?", [$id]);

if (!$image) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $title = clean_input($_POST['title'] ?? '');
        $description = clean_input($_POST['description'] ?? '');
        $link_url = clean_input($_POST['link_url'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Handle image upload
        $image_path = $image['image_path'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Delete old image
            if (!empty($image_path)) {
                delete_image($image_path);
            }
            
            $upload_result = upload_image($_FILES['image'], 'gallery');
            if ($upload_result['success']) {
                $image_path = $upload_result['file_path'];
            } else {
                $errors[] = 'Image: ' . $upload_result['message'];
            }
        }
        
        if (empty($errors)) {
            $sql = "UPDATE homepage_gallery SET 
                    image_path = ?, title = ?, description = ?, link_url = ?, sort_order = ?, is_active = ?
                    WHERE id = ?";
            $params = [$image_path, $title, $description, $link_url, $sort_order, $is_active, $id];
            
            if (db_execute($conn, $sql, $params)) {
                $success = true;
                // Refresh data
                $image = db_get_row($conn, "SELECT g.*, s.section_name, s.id as section_id 
                                             FROM homepage_gallery g 
                                             JOIN homepage_sections s ON g.section_id = s.id 
                                             WHERE g.id = ?", [$id]);
            } else {
                $errors[] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-image mr-3 text-purple-600"></i>แก้ไขรูปภาพ
        </h2>
        <p class="text-gray-600 mt-1"><?php echo $image['section_name']; ?></p>
    </div>
    <a href="gallery.php?section_id=<?php echo $image['section_id']; ?>" 
       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
        <i class="fas fa-arrow-left mr-2"></i>กลับ
    </a>
</div>

<?php if ($success): ?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
    <div class="flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <span>บันทึกข้อมูลเรียบร้อยแล้ว</span>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
    <?php foreach ($errors as $error): ?>
    <div class="flex items-center mb-1">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <span><?php echo $error; ?></span>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-info-circle mr-3"></i>ข้อมูลรูปภาพ
            </h5>
        </div>
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">รูปภาพปัจจุบัน</label>
                <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <img src="<?php echo UPLOADS_URL . '/' . $image['image_path']; ?>" 
                         alt="<?php echo htmlspecialchars($image['title']); ?>"
                         class="max-h-64 object-contain rounded">
                </div>
                
                <label class="block text-sm font-semibold text-gray-700 mb-2">เปลี่ยนรูปภาพ (ถ้าต้องการ)</label>
                <input type="file" name="image" accept="image/*" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                <p class="text-sm text-gray-500 mt-2">แนะนำขนาด: 800x600px</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ชื่อรูปภาพ</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($image['title']); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ลำดับ</label>
                    <input type="number" name="sort_order" value="<?php echo $image['sort_order']; ?>" min="0" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">คำอธิบาย</label>
                <textarea name="description" rows="3" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"><?php echo htmlspecialchars($image['description']); ?></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">ลิงก์ (ถ้ามี)</label>
                <input type="url" name="link_url" value="<?php echo htmlspecialchars($image['link_url']); ?>" 
                       placeholder="https://example.com" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
            </div>
            
            <div>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" <?php echo $image['is_active'] ? 'checked' : ''; ?> 
                           class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <span class="ml-3 text-gray-700 font-medium">แสดงรูปภาพนี้</span>
                </label>
            </div>
        </div>
    </div>
    
    <div class="flex justify-end space-x-4">
        <a href="gallery.php?section_id=<?php echo $image['section_id']; ?>" 
           class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-times mr-2"></i>ยกเลิก
        </a>
        <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 font-medium">
            <i class="fas fa-save mr-2"></i>บันทึกการเปลี่ยนแปลง
        </button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>





