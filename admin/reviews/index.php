<?php
/**
 * VIBEDAYBKK Admin - Customer Reviews Management
 * จัดการรีวิวลูกค้า
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check
require_permission('homepage', 'view');
$can_create = has_permission('homepage', 'create');
$can_edit = has_permission('homepage', 'edit');
$can_delete = has_permission('homepage', 'delete');

$page_title = 'จัดการรีวิวลูกค้า';
$current_page = 'reviews';

// Get all reviews
$reviews = db_get_rows($conn, "SELECT * FROM customer_reviews ORDER BY is_active DESC, created_at DESC");

include '../includes/header.php';
?>

<!-- Read-only Notice -->
<?php if (!$can_edit && !$can_create && !$can_delete): ?>
<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-eye text-yellow-400"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-yellow-700">
                <strong>โหมดดูอย่างเดียว:</strong> คุณสามารถดูข้อมูลได้เท่านั้น ไม่สามารถแก้ไขหรือลบได้
                <a href="../roles/upgrade.php?feature=homepage" class="underline hover:text-yellow-800">อัพเกรดเพื่อปลดล็อค</a>
            </p>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-comments mr-3 text-purple-600"></i>จัดการรีวิวลูกค้า
        </h2>
        <p class="text-gray-600 mt-1">จำนวนรีวิวทั้งหมด: <?php echo count($reviews); ?> รีวิว</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <?php if ($can_create): ?>
        <a href="add.php" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 font-medium">
            <i class="fas fa-plus-circle mr-2"></i>เพิ่มรีวิวใหม่
        </a>
        <?php else: ?>
        <button class="inline-flex items-center px-6 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed" disabled>
            <i class="fas fa-lock mr-2"></i>ปลดล็อค
        </button>
        <?php endif; ?>
    </div>
</div>
        
<!-- Empty State -->
<?php if (empty($reviews)): ?>
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <div class="w-24 h-24 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <i class="fas fa-comments text-4xl text-purple-600"></i>
    </div>
    <h3 class="text-2xl font-bold text-gray-900 mb-2">ยังไม่มีรีวิวลูกค้า</h3>
    <p class="text-gray-600 mb-6">เริ่มต้นด้วยการเพิ่มรีวิวลูกค้าแรกของคุณ</p>
    <?php if ($can_create): ?>
    <a href="add.php" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 font-medium">
        <i class="fas fa-plus-circle mr-2"></i>เพิ่มรีวิวใหม่
    </a>
    <?php endif; ?>
</div>
<?php else: ?>

<!-- Reviews Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php foreach ($reviews as $review): ?>
    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 group">
        <!-- Review Image -->
        <?php if (!empty($review['image'])): ?>
        <div class="aspect-w-16 aspect-h-9 bg-gradient-to-br from-purple-100 to-pink-100 overflow-hidden">
            <img src="<?php echo UPLOADS_URL . '/' . $review['image']; ?>" 
                 alt="<?php echo htmlspecialchars($review['customer_name']); ?>"
                 class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-300">
        </div>
        <?php else: ?>
        <div class="h-48 bg-gradient-to-br from-purple-100 to-pink-100 flex items-center justify-center">
            <i class="fas fa-user text-5xl text-purple-300"></i>
        </div>
        <?php endif; ?>
        
        <!-- Review Content -->
        <div class="p-6">
            <!-- Customer Info -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($review['customer_name']); ?></h3>
                    <p class="text-sm text-gray-500"><?php echo format_date_thai($review['created_at']); ?></p>
                </div>
                <div class="flex items-center space-x-1">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?> text-sm"></i>
                    <?php endfor; ?>
                </div>
            </div>
            
            <!-- Review Text -->
            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                "<?php echo htmlspecialchars($review['content'] ?? ''); ?>"
            </p>
            
            <!-- Status Badges -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex flex-wrap gap-2">
                    
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold <?php echo $review['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                        <i class="fas fa-<?php echo $review['is_active'] ? 'check-circle' : 'times-circle'; ?> mr-1"></i>
                        <?php echo $review['is_active'] ? 'เปิดใช้งาน' : 'ปิดใช้งาน'; ?>
                    </span>
                </div>
            </div>
            
            <div class="flex items-center justify-between text-xs text-gray-400 mb-4">
                <span><i class="fas fa-calendar-alt mr-1"></i><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></span>
                <span><i class="fas fa-sort mr-1"></i>ลำดับที่ <?php echo $review['sort_order']; ?></span>
            </div>
            
            <!-- Actions -->
            <div class="flex items-center gap-2 pt-4 border-t border-gray-100">
                <?php if ($can_edit): ?>
                <a href="edit.php?id=<?php echo $review['id']; ?>" 
                   class="flex-1 inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-edit mr-2"></i>แก้ไข
                </a>
                <?php endif; ?>
                
                <?php if ($can_delete): ?>
                <a href="delete.php?id=<?php echo $review['id']; ?>" 
                   class="flex-1 inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md"
                   onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบรีวิวนี้?')">
                    <i class="fas fa-trash-alt mr-2"></i>ลบ
                </a>
                <?php endif; ?>
                
                <?php if (!$can_edit && !$can_delete): ?>
                <div class="flex-1 text-center py-2.5 text-sm text-gray-400 bg-gray-50 rounded-lg">
                    <i class="fas fa-eye mr-1"></i>ดูอย่างเดียว
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php endif; ?>

<?php include '../includes/footer.php'; ?>



