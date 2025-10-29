<?php
/**
 * VIBEDAYBKK Admin - Gallery Management
 * จัดการรูปภาพในแกลเลอรี่
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check - allow editors to edit gallery
require_permission('homepage', 'edit');
$can_delete = has_permission('homepage', 'delete');

$page_title = 'จัดการแกลเลอรี่';
$current_page = 'homepage';

$section_id = isset($_GET['section_id']) ? (int)$_GET['section_id'] : 0;
$errors = [];
$success = false;

// Get section info
$section = db_get_row($conn, "SELECT * FROM homepage_sections WHERE id = ?", [$section_id]);

if (!$section) {
    header('Location: index.php');
    exit;
}

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $title = clean_input($_POST['title'] ?? '');
        $description = clean_input($_POST['description'] ?? '');
        $link_url = clean_input($_POST['link_url'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_result = upload_image($_FILES['image'], 'gallery');
            if ($upload_result['success']) {
                $sql = "INSERT INTO homepage_gallery (section_id, image_path, title, description, link_url, sort_order) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $params = [$section_id, $upload_result['file_path'], $title, $description, $link_url, $sort_order];
                
                if (db_execute($conn, $sql, $params)) {
                    $success = true;
                } else {
                    $errors[] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
                }
            } else {
                $errors[] = 'Image: ' . $upload_result['message'];
            }
        } else {
            $errors[] = 'กรุณาเลือกรูปภาพ';
        }
    }
}

// Get all gallery images
$images = db_get_rows($conn, "SELECT * FROM homepage_gallery WHERE section_id = ? ORDER BY sort_order ASC", [$section_id]);

include '../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-images mr-3 text-purple-600"></i>จัดการแกลเลอรี่
        </h2>
        <p class="text-gray-600 mt-1"><?php echo $section['section_name']; ?></p>
    </div>
    <a href="index.php" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
        <i class="fas fa-arrow-left mr-2"></i>กลับ
    </a>
</div>

<?php if ($success): ?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
    <div class="flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <span>เพิ่มรูปภาพเรียบร้อยแล้ว</span>
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

<!-- Add New Image Form -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
        <h5 class="text-white text-lg font-semibold flex items-center">
            <i class="fas fa-plus-circle mr-3"></i>เพิ่มรูปภาพใหม่
        </h5>
    </div>
    <div class="p-6">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="action" value="add">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">รูปภาพ *</label>
                    <input type="file" name="image" accept="image/*" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                    <p class="text-sm text-gray-500 mt-2">แนะนำขนาด: 800x600px</p>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ชื่อรูปภาพ</label>
                    <input type="text" name="title" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ลำดับ</label>
                    <input type="number" name="sort_order" value="0" min="0" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">คำอธิบาย</label>
                    <textarea name="description" rows="2" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"></textarea>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ลิงก์ (ถ้ามี)</label>
                    <input type="url" name="link_url" placeholder="https://example.com" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-plus-circle mr-2"></i>เพิ่มรูปภาพ
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Gallery Images Grid -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
        <h5 class="text-white text-lg font-semibold flex items-center">
            <i class="fas fa-th mr-3"></i>รูปภาพทั้งหมด (<?php echo count($images); ?> รูป)
        </h5>
    </div>
    <div class="p-6">
        <?php if (empty($images)): ?>
        <div class="text-center py-12">
            <i class="fas fa-images text-6xl text-gray-400 mb-4"></i>
            <h5 class="text-gray-600 text-xl font-medium">ยังไม่มีรูปภาพ</h5>
            <p class="text-gray-500 mt-2">เพิ่มรูปภาพแรกของคุณด้านบน</p>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="gallery-grid">
            <?php foreach ($images as $image): ?>
            <div class="group relative bg-white rounded-lg border-2 border-gray-200 overflow-hidden hover:border-purple-500 transition-all duration-200" data-id="<?php echo $image['id']; ?>">
                <!-- Image -->
                <div class="aspect-w-4 aspect-h-3 bg-gray-100">
                    <img src="<?php echo UPLOADS_URL . '/' . $image['image_path']; ?>" 
                         alt="<?php echo htmlspecialchars($image['title']); ?>"
                         class="w-full h-48 object-cover">
                </div>
                
                <!-- Info -->
                <div class="p-4">
                    <?php if ($image['title']): ?>
                    <h4 class="font-semibold text-gray-900 mb-1"><?php echo $image['title']; ?></h4>
                    <?php endif; ?>
                    <?php if ($image['description']): ?>
                    <p class="text-sm text-gray-600 mb-2"><?php echo truncate_text($image['description'], 60); ?></p>
                    <?php endif; ?>
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>ลำดับ: <?php echo $image['sort_order']; ?></span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?php echo $image['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                            <?php echo $image['is_active'] ? 'แสดง' : 'ซ่อน'; ?>
                        </span>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="absolute top-2 right-2 flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    <a href="edit-gallery-image.php?id=<?php echo $image['id']; ?>" 
                       class="p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-lg transition-colors duration-200" 
                       title="แก้ไข">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="delete-gallery-image.php?id=<?php echo $image['id']; ?>&section_id=<?php echo $section_id; ?>" 
                       class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-lg transition-colors duration-200 btn-delete" 
                       title="ลบ">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
                
                <!-- Drag Handle -->
                <div class="absolute top-2 left-2 p-2 bg-gray-800 bg-opacity-75 text-white rounded-lg cursor-move opacity-0 group-hover:opacity-100 transition-opacity duration-200" title="ลากเพื่อจัดเรียง">
                    <i class="fas fa-grip-vertical"></i>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Sortable gallery
const galleryGrid = document.getElementById('gallery-grid');
if (galleryGrid) {
    new Sortable(galleryGrid, {
        animation: 150,
        handle: '.cursor-move',
        ghostClass: 'opacity-50',
        onEnd: function(evt) {
            // Get new order
            const items = galleryGrid.querySelectorAll('[data-id]');
            const order = Array.from(items).map((item, index) => ({
                id: item.dataset.id,
                sort_order: index
            }));
            
            // Send to server
            fetch('update-gallery-order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order: order,
                    csrf_token: '<?php echo generate_csrf_token(); ?>'
                })
            });
        }
    });
}
</script>





