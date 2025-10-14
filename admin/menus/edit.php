<?php
/**
 * VIBEDAYBKK Admin - Edit Menu
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$page_title = 'แก้ไขเมนู';
$current_page = 'menus';

$menu_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$menu_id) {
    set_message('error', 'ไม่พบข้อมูล');
    redirect(ADMIN_URL . '/menus/');
}

$stmt = $pdo->prepare("SELECT * FROM menus WHERE id = ?");
$stmt->execute([$menu_id]);
$menu = $stmt->fetch();

if (!$menu) {
    set_message('error', 'ไม่พบข้อมูล');
    redirect(ADMIN_URL . '/menus/');
}

$parent_menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL AND id != {$menu_id} ORDER BY sort_order ASC");

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $data = [
            'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
            'title' => clean_input($_POST['title']),
            'url' => clean_input($_POST['url']),
            'icon' => clean_input($_POST['icon']),
            'target' => $_POST['target'],
            'sort_order' => (int)$_POST['sort_order'],
            'status' => $_POST['status']
        ];
        
        if (empty($data['title'])) $errors[] = 'กรุณากรอกชื่อเมนู';
        if (empty($data['url'])) $errors[] = 'กรุณากรอก URL';
        
        if (empty($errors)) {
            if (db_update($pdo, 'menus', $data, 'id = :id', ['id' => $menu_id])) {
                log_activity($pdo, $_SESSION['user_id'], 'update', 'menus', $menu_id, $menu, $data);
                $success = true;
                
                $stmt = $pdo->prepare("SELECT * FROM menus WHERE id = ?");
                $stmt->execute([$menu_id]);
                $menu = $stmt->fetch();
            } else {
                $errors[] = 'เกิดข้อผิดพลาด';
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h2 class="text-3xl font-bold text-gray-900 flex items-center">
        <i class="fas fa-edit mr-3 text-blue-600"></i>แก้ไขเมนู
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
    <ul class="list-disc list-inside">
        <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-info-circle mr-3"></i>ข้อมูลเมนู
            </h5>
        </div>
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        ชื่อเมนู <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($menu['title']); ?>" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        URL <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="url" value="<?php echo htmlspecialchars($menu['url']); ?>" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">เมนูหลัก (Parent)</label>
                    <select name="parent_id" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <option value="">ไม่มี (เมนูหลัก)</option>
                        <?php foreach ($parent_menus as $parent): ?>
                        <option value="<?php echo $parent['id']; ?>" <?php echo $menu['parent_id'] == $parent['id'] ? 'selected' : ''; ?>>
                            <?php echo $parent['title']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ไอคอน</label>
                    <input type="text" name="icon" value="<?php echo htmlspecialchars($menu['icon']); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Target</label>
                    <select name="target" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <option value="_self" <?php echo $menu['target'] == '_self' ? 'selected' : ''; ?>>เปิดหน้าเดิม (_self)</option>
                        <option value="_blank" <?php echo $menu['target'] == '_blank' ? 'selected' : ''; ?>>เปิดหน้าใหม่ (_blank)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ลำดับ</label>
                    <input type="number" name="sort_order" value="<?php echo $menu['sort_order']; ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">สถานะ</label>
                    <select name="status" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <option value="active" <?php echo $menu['status'] == 'active' ? 'selected' : ''; ?>>ใช้งาน</option>
                        <option value="inactive" <?php echo $menu['status'] == 'inactive' ? 'selected' : ''; ?>>ไม่ใช้งาน</option>
                    </select>
                </div>
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

<?php include '../includes/footer.php'; ?>
