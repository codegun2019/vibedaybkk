<?php
/**
 * Gallery Albums Management
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check
require_permission('gallery', 'view');
$can_create = has_permission('gallery', 'create');
$can_edit = has_permission('gallery', 'edit');
$can_delete = has_permission('gallery', 'delete');

$page_title = 'จัดการอัลบั้ม';
$current_page = 'gallery';

// Get albums with image count
$sql = "SELECT ga.*, 
        (SELECT COUNT(*) FROM gallery_images WHERE album_id = ga.id) as image_count,
        (SELECT image_path FROM gallery_images WHERE album_id = ga.id ORDER BY created_at DESC LIMIT 1) as latest_image
        FROM gallery_albums ga
        ORDER BY ga.sort_order ASC";
$albums = db_get_rows($conn, $sql);

include '../includes/header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-folder-open mr-3 text-blue-600"></i>จัดการอัลบั้ม
        </h2>
        <p class="text-gray-600 mt-1">จำนวนอัลบั้มทั้งหมด: <?php echo count($albums); ?> อัลบั้ม</p>
    </div>
    <div class="mt-4 sm:mt-0 flex space-x-3">
        <a href="index.php" class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-images mr-2"></i>กลับไปแกลเลอรี่
        </a>
        <?php if ($can_create): ?>
        <button id="add-album-btn" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-plus-circle mr-2"></i>เพิ่มอัลบั้มใหม่
        </button>
        <?php else: ?>
        <button class="inline-flex items-center px-6 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed" disabled>
            <i class="fas fa-lock mr-2"></i>ปลดล็อค
        </button>
        <?php endif; ?>
    </div>
</div>

<!-- Albums Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($albums as $album): ?>
    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 group">
        <!-- Cover Image -->
        <div class="aspect-video overflow-hidden bg-gradient-to-br from-purple-100 to-pink-100">
            <?php if ($album['cover_image'] || $album['latest_image']): ?>
            <img src="<?php echo BASE_URL . '/' . ($album['cover_image'] ?: $album['latest_image']); ?>" 
                 alt="<?php echo htmlspecialchars($album['title']); ?>"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
            <?php else: ?>
            <div class="w-full h-full flex items-center justify-center">
                <i class="fas fa-folder text-6xl text-purple-300"></i>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Album Info -->
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($album['title']); ?></h3>
            <?php if ($album['description']): ?>
            <p class="text-gray-600 text-sm mb-4"><?php echo truncate_text($album['description'], 80); ?></p>
            <?php endif; ?>
            
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm text-gray-500">
                    <i class="fas fa-images mr-1"></i><?php echo number_format($album['image_count']); ?> รูป
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $album['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                    <?php echo $album['is_active'] ? 'ใช้งาน' : 'ไม่ใช้งาน'; ?>
                </span>
            </div>
            
            <div class="flex space-x-2">
                <a href="index.php?album=<?php echo $album['id']; ?>" 
                   class="flex-1 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-center font-medium transition-colors duration-200">
                    <i class="fas fa-eye mr-2"></i>ดูรูปภาพ
                </a>
                <?php if ($can_edit): ?>
                <button onclick="editAlbum(<?php echo $album['id']; ?>)" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-edit"></i>
                </button>
                <?php endif; ?>
                <?php if ($can_delete): ?>
                <button onclick="deleteAlbum(<?php echo $album['id']; ?>, <?php echo $album['image_count']; ?>)" 
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-trash"></i>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php include '../includes/footer.php'; ?>

<script src="albums.js"></script>





