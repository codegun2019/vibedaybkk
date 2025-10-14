<?php
/**
 * VIBEDAYBKK Admin - Edit Feature
 * แก้ไขรายการ Feature
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_admin();

$page_title = 'แก้ไขรายการ';
$current_page = 'homepage';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];
$success = false;

// Get feature data
$feature = db_get_row($conn, "SELECT f.*, s.section_name, s.id as section_id 
                               FROM homepage_features f 
                               JOIN homepage_sections s ON f.section_id = s.id 
                               WHERE f.id = ?", [$id]);

if (!$feature) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $icon = clean_input($_POST['icon'] ?? '');
        $title = clean_input($_POST['title'] ?? '');
        $description = clean_input($_POST['description'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if (empty($title)) {
            $errors[] = 'กรุณาใส่หัวข้อ';
        } else {
            $sql = "UPDATE homepage_features SET 
                    icon = ?, title = ?, description = ?, sort_order = ?, is_active = ?
                    WHERE id = ?";
            $params = [$icon, $title, $description, $sort_order, $is_active, $id];
            
            if (db_execute($conn, $sql, $params)) {
                $success = true;
                // Refresh data
                $feature = db_get_row($conn, "SELECT f.*, s.section_name, s.id as section_id 
                                               FROM homepage_features f 
                                               JOIN homepage_sections s ON f.section_id = s.id 
                                               WHERE f.id = ?", [$id]);
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
            <i class="fas fa-edit mr-3 text-blue-600"></i>แก้ไขรายการ
        </h2>
        <p class="text-gray-600 mt-1"><?php echo $feature['section_name']; ?></p>
    </div>
    <a href="features.php?section_id=<?php echo $feature['section_id']; ?>" 
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

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-info-circle mr-3"></i>ข้อมูลรายการ
            </h5>
        </div>
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        ไอคอน (FontAwesome)
                    </label>
                    <div class="flex items-center space-x-3">
                        <input type="text" name="icon" id="icon-input" value="<?php echo htmlspecialchars($feature['icon']); ?>" 
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <div id="icon-preview" class="w-12 h-12 flex items-center justify-center bg-gray-100 rounded-lg border border-gray-300">
                            <?php if ($feature['icon']): ?>
                            <i class="fas <?php echo $feature['icon']; ?> text-2xl text-blue-600"></i>
                            <?php else: ?>
                            <i class="fas fa-question text-2xl text-gray-400"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        ดูไอคอนได้ที่ <a href="https://fontawesome.com/icons" target="_blank" class="text-blue-600 hover:underline">FontAwesome</a>
                    </p>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ลำดับ</label>
                    <input type="number" name="sort_order" value="<?php echo $feature['sort_order']; ?>" min="0" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">หัวข้อ *</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($feature['title']); ?>" required 
                       class="w-full px-4 py-3 text-lg border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">คำอธิบาย</label>
                <textarea name="description" rows="4" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"><?php echo htmlspecialchars($feature['description']); ?></textarea>
            </div>
            
            <div>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" <?php echo $feature['is_active'] ? 'checked' : ''; ?> 
                           class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-3 text-gray-700 font-medium">แสดงรายการนี้</span>
                </label>
            </div>
        </div>
    </div>
    
    <div class="flex justify-end space-x-4">
        <a href="features.php?section_id=<?php echo $feature['section_id']; ?>" 
           class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-times mr-2"></i>ยกเลิก
        </a>
        <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 font-medium">
            <i class="fas fa-save mr-2"></i>บันทึกการเปลี่ยนแปลง
        </button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>

<script>
// Icon preview
const iconInput = document.getElementById('icon-input');
const iconPreview = document.getElementById('icon-preview');

iconInput.addEventListener('input', function() {
    const iconClass = this.value.trim();
    if (iconClass) {
        iconPreview.innerHTML = '<i class="fas ' + iconClass + ' text-2xl text-blue-600"></i>';
    } else {
        iconPreview.innerHTML = '<i class="fas fa-question text-2xl text-gray-400"></i>';
    }
});
</script>

