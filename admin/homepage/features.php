<?php
/**
 * VIBEDAYBKK Admin - Features/Stats Management
 * จัดการรายการ Features และ Stats
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_admin();

$page_title = 'จัดการรายการ';
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

// Handle add feature
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $icon = clean_input($_POST['icon'] ?? '');
        $title = clean_input($_POST['title'] ?? '');
        $description = clean_input($_POST['description'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        
        if (empty($title)) {
            $errors[] = 'กรุณาใส่หัวข้อ';
        } else {
            $sql = "INSERT INTO homepage_features (section_id, icon, title, description, sort_order) 
                    VALUES (?, ?, ?, ?, ?)";
            $params = [$section_id, $icon, $title, $description, $sort_order];
            
            if (db_execute($conn, $sql, $params)) {
                $success = true;
            } else {
                $errors[] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
            }
        }
    }
}

// Get all features
$features = db_get_rows($conn, "SELECT * FROM homepage_features WHERE section_id = ? ORDER BY sort_order ASC", [$section_id]);

include '../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-list mr-3 text-green-600"></i>จัดการรายการ
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
        <span>เพิ่มรายการเรียบร้อยแล้ว</span>
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

<!-- Add New Feature Form -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
        <h5 class="text-white text-lg font-semibold flex items-center">
            <i class="fas fa-plus-circle mr-3"></i>เพิ่มรายการใหม่
        </h5>
    </div>
    <div class="p-6">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="action" value="add">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        ไอคอน (FontAwesome) *
                    </label>
                    <div class="flex items-center space-x-3">
                        <input type="text" name="icon" id="icon-input" placeholder="fa-check-circle" 
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                        <div id="icon-preview" class="w-12 h-12 flex items-center justify-center bg-gray-100 rounded-lg border border-gray-300">
                            <i class="fas fa-question text-2xl text-gray-400"></i>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        ดูไอคอนได้ที่ <a href="https://fontawesome.com/icons" target="_blank" class="text-blue-600 hover:underline">FontAwesome</a>
                    </p>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ลำดับ</label>
                    <input type="number" name="sort_order" value="0" min="0" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">หัวข้อ *</label>
                    <input type="text" name="title" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">คำอธิบาย</label>
                    <textarea name="description" rows="3" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"></textarea>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-plus-circle mr-2"></i>เพิ่มรายการ
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Features List -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
        <h5 class="text-white text-lg font-semibold flex items-center">
            <i class="fas fa-th-list mr-3"></i>รายการทั้งหมด (<?php echo count($features); ?> รายการ)
        </h5>
    </div>
    <div class="p-6">
        <?php if (empty($features)): ?>
        <div class="text-center py-12">
            <i class="fas fa-list text-6xl text-gray-400 mb-4"></i>
            <h5 class="text-gray-600 text-xl font-medium">ยังไม่มีรายการ</h5>
            <p class="text-gray-500 mt-2">เพิ่มรายการแรกของคุณด้านบน</p>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="features-grid">
            <?php foreach ($features as $feature): ?>
            <div class="group relative bg-white rounded-lg border-2 border-gray-200 p-6 hover:border-green-500 hover:shadow-lg transition-all duration-200" data-id="<?php echo $feature['id']; ?>">
                <!-- Icon -->
                <div class="w-16 h-16 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                    <?php if ($feature['icon']): ?>
                    <i class="fas <?php echo $feature['icon']; ?> text-3xl text-green-600"></i>
                    <?php else: ?>
                    <i class="fas fa-star text-3xl text-green-600"></i>
                    <?php endif; ?>
                </div>
                
                <!-- Content -->
                <h4 class="text-xl font-bold text-gray-900 mb-2"><?php echo $feature['title']; ?></h4>
                <?php if ($feature['description']): ?>
                <p class="text-sm text-gray-600 mb-4"><?php echo $feature['description']; ?></p>
                <?php endif; ?>
                
                <!-- Meta -->
                <div class="flex items-center justify-between text-xs text-gray-500 pt-4 border-t border-gray-200">
                    <span>ลำดับ: <?php echo $feature['sort_order']; ?></span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?php echo $feature['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                        <?php echo $feature['is_active'] ? 'แสดง' : 'ซ่อน'; ?>
                    </span>
                </div>
                
                <!-- Actions -->
                <div class="absolute top-4 right-4 flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    <a href="edit-feature.php?id=<?php echo $feature['id']; ?>" 
                       class="p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-lg transition-colors duration-200" 
                       title="แก้ไข">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="delete-feature.php?id=<?php echo $feature['id']; ?>&section_id=<?php echo $section_id; ?>" 
                       class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-lg transition-colors duration-200 btn-delete" 
                       title="ลบ">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
                
                <!-- Drag Handle -->
                <div class="absolute top-4 left-4 p-2 bg-gray-800 bg-opacity-75 text-white rounded-lg cursor-move opacity-0 group-hover:opacity-100 transition-opacity duration-200" title="ลากเพื่อจัดเรียง">
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
// Icon preview
const iconInput = document.getElementById('icon-input');
const iconPreview = document.getElementById('icon-preview');

iconInput.addEventListener('input', function() {
    const iconClass = this.value.trim();
    if (iconClass) {
        iconPreview.innerHTML = '<i class="fas ' + iconClass + ' text-2xl text-green-600"></i>';
    } else {
        iconPreview.innerHTML = '<i class="fas fa-question text-2xl text-gray-400"></i>';
    }
});

// Sortable features
const featuresGrid = document.getElementById('features-grid');
if (featuresGrid) {
    new Sortable(featuresGrid, {
        animation: 150,
        handle: '.cursor-move',
        ghostClass: 'opacity-50',
        onEnd: function(evt) {
            const items = featuresGrid.querySelectorAll('[data-id]');
            const order = Array.from(items).map((item, index) => ({
                id: item.dataset.id,
                sort_order: index
            }));
            
            fetch('update-features-order.php', {
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

