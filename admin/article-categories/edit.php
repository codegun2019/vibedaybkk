<?php
/**
 * VIBEDAYBKK Admin - Edit Article Category
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check
require_permission('article_categories', 'edit');

$page_title = 'แก้ไขหมวดหมู่บทความ';
$current_page = 'article_categories';

$id = (int)($_GET['id'] ?? 0);
$errors = [];
$success = false;

// Get category
$stmt = $conn->prepare("SELECT * FROM article_categories WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();
$stmt->close();

if (!$category) {
    set_message('error', 'ไม่พบข้อมูลหมวดหมู่');
    redirect(ADMIN_URL . '/article-categories/');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $data = [
            'name' => clean_input($_POST['name']),
            'slug' => clean_input($_POST['slug']),
            'description' => clean_input($_POST['description'] ?? ''),
            'icon' => clean_input($_POST['icon'] ?? 'fa-folder'),
            'color' => clean_input($_POST['color'] ?? 'from-gray-500 to-gray-700'),
            'sort_order' => (int)$_POST['sort_order'],
            'status' => $_POST['status']
        ];
        
        // Validation
        if (empty($data['name'])) $errors[] = 'กรุณากรอกชื่อหมวดหมู่';
        if (empty($data['slug'])) $errors[] = 'กรุณากรอก slug';
        
        // Check duplicate slug (excluding current record)
        if (!empty($data['slug'])) {
            $stmt = $conn->prepare("SELECT id FROM article_categories WHERE slug = ? AND id != ?");
            $stmt->bind_param('si', $data['slug'], $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->fetch_assoc()) {
                $errors[] = 'Slug นี้มีอยู่แล้ว';
            }
            $stmt->close();
        }
        
        if (empty($errors)) {
            if (db_update($conn, 'article_categories', $data, 'id = ?', [$id])) {
                log_activity($conn, $_SESSION['user_id'], 'update', 'article_categories', $id);
                $success = true;
                // Refresh data
                $stmt = $conn->prepare("SELECT * FROM article_categories WHERE id = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $category = $result->fetch_assoc();
                $stmt->close();
            } else {
                $errors[] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h2 class="text-3xl font-bold text-gray-900 flex items-center">
        <i class="fas fa-edit mr-3 text-blue-600"></i>แก้ไขหมวดหมู่บทความ
    </h2>
    <a href="index.php" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
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
    <h5 class="font-bold mb-2 flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>พบข้อผิดพลาด:
    </h5>
    <ul class="list-disc list-inside">
        <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
    <!-- ข้อมูลพื้นฐาน -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-folder mr-3"></i>ข้อมูลพื้นฐาน
            </h5>
        </div>
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        ชื่อหมวดหมู่ <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Slug <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="slug" value="<?php echo htmlspecialchars($category['slug']); ?>" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    <p class="text-sm text-gray-500 mt-1">ใช้สำหรับ URL (ภาษาอังกฤษ, ตัวเลข, ขีดกลาง)</p>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">คำอธิบาย</label>
                <textarea name="description" rows="3" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none"
                          placeholder="อธิบายเกี่ยวกับหมวดหมู่นี้..."><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
            </div>
        </div>
    </div>
    
    <!-- การตั้งค่าไอคอนและสี -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-palette mr-3"></i>การตั้งค่าไอคอนและสี
            </h5>
        </div>
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">ไอคอน</label>
                    <div class="flex items-center space-x-3">
                        <input type="text" name="icon" id="icon-input" value="<?php echo htmlspecialchars($category['icon'] ?? 'fa-folder'); ?>" 
                               class="icon-picker-input flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                        <div id="icon-input_preview" class="w-16 h-16 flex items-center justify-center bg-gradient-to-br from-purple-100 to-pink-100 rounded-xl border-2 border-purple-300 shadow-lg">
                            <i class="fas <?php echo htmlspecialchars($category['icon'] ?? 'fa-folder'); ?> text-3xl text-purple-600"></i>
                        </div>
                        <button type="button" class="icon-picker-btn px-4 py-3 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white rounded-lg font-medium shadow-lg transition-all duration-200">
                            <i class="fas fa-icons mr-2"></i>เลือกไอคอน
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">สีธีม</label>
                    <div class="space-y-3">
                        <input type="text" name="color" value="<?php echo htmlspecialchars($category['color'] ?? 'from-gray-500 to-gray-700'); ?>" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                               placeholder="from-blue-500 to-blue-700">
                        <div class="grid grid-cols-4 gap-2">
                            <button type="button" onclick="setColor('from-blue-500 to-blue-700')" 
                                    class="h-8 bg-gradient-to-r from-blue-500 to-blue-700 rounded-lg border-2 border-transparent hover:border-blue-300 transition-all duration-200"></button>
                            <button type="button" onclick="setColor('from-green-500 to-green-700')" 
                                    class="h-8 bg-gradient-to-r from-green-500 to-green-700 rounded-lg border-2 border-transparent hover:border-green-300 transition-all duration-200"></button>
                            <button type="button" onclick="setColor('from-purple-500 to-purple-700')" 
                                    class="h-8 bg-gradient-to-r from-purple-500 to-purple-700 rounded-lg border-2 border-transparent hover:border-purple-300 transition-all duration-200"></button>
                            <button type="button" onclick="setColor('from-red-500 to-red-700')" 
                                    class="h-8 bg-gradient-to-r from-red-500 to-red-700 rounded-lg border-2 border-transparent hover:border-red-300 transition-all duration-200"></button>
                            <button type="button" onclick="setColor('from-yellow-500 to-yellow-700')" 
                                    class="h-8 bg-gradient-to-r from-yellow-500 to-yellow-700 rounded-lg border-2 border-transparent hover:border-yellow-300 transition-all duration-200"></button>
                            <button type="button" onclick="setColor('from-pink-500 to-pink-700')" 
                                    class="h-8 bg-gradient-to-r from-pink-500 to-pink-700 rounded-lg border-2 border-transparent hover:border-pink-300 transition-all duration-200"></button>
                            <button type="button" onclick="setColor('from-indigo-500 to-indigo-700')" 
                                    class="h-8 bg-gradient-to-r from-indigo-500 to-indigo-700 rounded-lg border-2 border-transparent hover:border-indigo-300 transition-all duration-200"></button>
                            <button type="button" onclick="setColor('from-gray-500 to-gray-700')" 
                                    class="h-8 bg-gradient-to-r from-gray-500 to-gray-700 rounded-lg border-2 border-transparent hover:border-gray-300 transition-all duration-200"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- การตั้งค่าขั้นสูง -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-cog mr-3"></i>การตั้งค่าขั้นสูง
            </h5>
        </div>
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ลำดับการแสดง</label>
                    <input type="number" name="sort_order" value="<?php echo $category['sort_order']; ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                    <p class="text-sm text-gray-500 mt-1">ตัวเลขน้อยจะแสดงก่อน</p>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">สถานะ</label>
                    <select name="status" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                        <option value="active" <?php echo $category['status'] == 'active' ? 'selected' : ''; ?>>ใช้งาน</option>
                        <option value="inactive" <?php echo $category['status'] == 'inactive' ? 'selected' : ''; ?>>ไม่ใช้งาน</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ปุ่มบันทึก -->
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

<?php include '../includes/footer.php'; ?>

<script src="../includes/icon-picker.js"></script>

<script>
// Color picker functionality
function setColor(color) {
    document.querySelector('input[name="color"]').value = color;
    
    // Visual feedback
    const buttons = document.querySelectorAll('button[onclick^="setColor"]');
    buttons.forEach(btn => {
        btn.classList.remove('border-blue-400', 'ring-2', 'ring-blue-200');
    });
    
    event.target.classList.add('border-blue-400', 'ring-2', 'ring-blue-200');
}

// Auto-generate slug from name
document.querySelector('input[name="name"]').addEventListener('input', function() {
    const name = this.value;
    const slug = name
        .toLowerCase()
        .replace(/[^\w\s-]/g, '') // Remove special characters
        .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with hyphens
        .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
    
    document.querySelector('input[name="slug"]').value = slug;
});

// Icon preview update
document.addEventListener('DOMContentLoaded', function() {
    const iconInput = document.getElementById('icon-input');
    const iconPreview = document.getElementById('icon-input_preview');
    
    function updateIconPreview() {
        const iconClass = iconInput.value || 'fa-folder';
        const iconElement = iconPreview.querySelector('i');
        iconElement.className = `fas ${iconClass} text-3xl text-purple-600`;
    }
    
    iconInput.addEventListener('input', updateIconPreview);
    updateIconPreview();
    
    // Set initial color selection
    const currentColor = document.querySelector('input[name="color"]').value;
    const colorButtons = document.querySelectorAll('button[onclick^="setColor"]');
    colorButtons.forEach(btn => {
        if (btn.getAttribute('onclick').includes(currentColor)) {
            btn.classList.add('border-blue-400', 'ring-2', 'ring-blue-200');
        }
    });
});
</script>

<style>
/* Custom styles for better UX */
button[onclick^="setColor"] {
    transition: all 0.2s ease;
}

button[onclick^="setColor"]:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

#icon-input_preview {
    transition: all 0.3s ease;
}

#icon-input_preview:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}
</style>