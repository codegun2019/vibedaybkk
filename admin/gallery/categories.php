<?php
/**
 * จัดการหมวดหมู่ Gallery
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check
require_permission('gallery', 'create');

$page_title = 'จัดการหมวดหมู่ Gallery';
$current_page = 'gallery';

$errors = [];
$success = false;

// ดึงหมวดหมู่ที่มีอยู่
$existing_categories = db_get_rows($conn, "SELECT DISTINCT category, COUNT(*) as count FROM gallery WHERE category IS NOT NULL AND category != '' GROUP BY category ORDER BY category ASC");

// หมวดหมู่เริ่มต้น
$default_categories = [
    'สตรีทแฟชั่น',
    'แฟชั่นชุดทำงาน', 
    'แฟชั่นชุดออกงาน',
    'ไลฟ์สไตล์',
    'ความสวยความงาม',
    'อีเวนต์',
    'การถ่ายภาพ',
    'แฟชั่นชุดว่ายน้ำ'
];

// รวมหมวดหมู่ที่มีอยู่กับหมวดหมู่เริ่มต้น
$all_categories = array_unique(array_merge($default_categories, array_column($existing_categories, 'category')));
sort($all_categories);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_category'])) {
        $new_category = clean_input($_POST['new_category']);
        
        if (empty($new_category)) {
            $errors[] = 'กรุณากรอกชื่อหมวดหมู่';
        } elseif (in_array($new_category, $all_categories)) {
            $errors[] = 'หมวดหมู่นี้มีอยู่แล้ว';
        } else {
            $success = true;
            // หมวดหมู่ใหม่จะถูกเพิ่มในรายการอัตโนมัติเมื่อมีการใช้งาน
        }
    }
}

include '../includes/header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-folder text-purple-600 mr-3"></i>จัดการหมวดหมู่ Gallery
        </h2>
        <p class="mt-2 text-gray-600">จัดการหมวดหมู่สำหรับรูปภาพในแกลลอรี่</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="index.php" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>กลับไปแกลลอรี่
        </a>
    </div>
</div>

<?php if ($success): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <i class="fas fa-check-circle mr-2"></i>เพิ่มหมวดหมู่ใหม่เรียบร้อยแล้ว
    </div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <ul class="list-disc list-inside">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- หมวดหมู่ที่มีอยู่ -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-list text-purple-600 mr-2"></i>หมวดหมู่ที่มีอยู่
        </h3>
        
        <?php if (!empty($existing_categories)): ?>
            <div class="space-y-3">
                <?php foreach ($existing_categories as $cat): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-folder text-purple-600 mr-3"></i>
                            <span class="font-medium text-gray-800"><?php echo htmlspecialchars($cat['category']); ?></span>
                        </div>
                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-sm">
                            <?php echo $cat['count']; ?> รูป
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-folder-open text-4xl mb-4"></i>
                <p>ยังไม่มีหมวดหมู่ที่ใช้งาน</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- หมวดหมู่เริ่มต้น -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-star text-yellow-500 mr-2"></i>หมวดหมู่เริ่มต้น
        </h3>
        
        <div class="space-y-3">
            <?php foreach ($default_categories as $cat): ?>
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-folder text-blue-600 mr-3"></i>
                        <span class="font-medium text-gray-800"><?php echo htmlspecialchars($cat); ?></span>
                    </div>
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm">
                        เริ่มต้น
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- เพิ่มหมวดหมู่ใหม่ -->
<div class="mt-8 bg-white rounded-lg shadow-md p-6">
    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-plus text-green-600 mr-2"></i>เพิ่มหมวดหมู่ใหม่
    </h3>
    
    <form method="POST" class="flex gap-4">
        <div class="flex-1">
            <input type="text" name="new_category" 
                   placeholder="กรอกชื่อหมวดหมู่ใหม่..." 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition-all">
        </div>
        <button type="submit" name="add_category" 
                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>เพิ่มหมวดหมู่
        </button>
    </form>
    
    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <h4 class="font-semibold text-yellow-800 mb-2">
            <i class="fas fa-lightbulb mr-2"></i>คำแนะนำ
        </h4>
        <ul class="text-sm text-yellow-700 space-y-1">
            <li>• หมวดหมู่จะถูกเพิ่มในรายการทันทีที่กรอกเสร็จ</li>
            <li>• สามารถใช้หมวดหมู่ใหม่ได้ทันทีเมื่ออัปโหลดรูปภาพ</li>
            <li>• ชื่อหมวดหมู่ควรสั้น กระชับ และเข้าใจง่าย</li>
            <li>• หลีกเลี่ยงการใช้สัญลักษณ์พิเศษหรือตัวเลข</li>
        </ul>
    </div>
</div>

<!-- สถิติ -->
<div class="mt-8 bg-gradient-to-r from-purple-500 to-blue-600 rounded-lg shadow-md p-6 text-white">
    <h3 class="text-xl font-semibold mb-4 flex items-center">
        <i class="fas fa-chart-bar mr-2"></i>สถิติหมวดหมู่
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white/20 rounded-lg p-4">
            <div class="text-2xl font-bold"><?php echo count($existing_categories); ?></div>
            <div class="text-sm opacity-90">หมวดหมู่ที่ใช้งาน</div>
        </div>
        <div class="bg-white/20 rounded-lg p-4">
            <div class="text-2xl font-bold"><?php echo count($default_categories); ?></div>
            <div class="text-sm opacity-90">หมวดหมู่เริ่มต้น</div>
        </div>
        <div class="bg-white/20 rounded-lg p-4">
            <div class="text-2xl font-bold"><?php echo count($all_categories); ?></div>
            <div class="text-sm opacity-90">หมวดหมู่ทั้งหมด</div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
