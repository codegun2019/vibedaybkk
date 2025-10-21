<?php
/**
 * VIBEDAYBKK Admin - Gallery Images
 * จัดการรูปภาพในแกลเลอรี่
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check
require_permission('gallery', 'view');
$can_create = has_permission('gallery', 'create');
$can_edit = has_permission('gallery', 'edit');
$can_delete = has_permission('gallery', 'delete');

$page_title = 'จัดการแกลเลอรี่';
$current_page = 'gallery';

// Get filter parameters
$album_id = isset($_GET['album']) ? (int)$_GET['album'] : 0;
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 24;
$offset = ($page - 1) * $per_page;

// Build query
$where = ["1=1"];
$params = [];

if ($album_id > 0) {
    $where[] = "gi.album_id = ?";
    $params[] = $album_id;
}

if ($search) {
    $where[] = "(gi.title LIKE ? OR gi.description LIKE ? OR gi.tags LIKE ?)";
    $search_term = "%{$search}%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$where_clause = implode(' AND ', $where);

// Count total
$count_sql = "SELECT COUNT(*) as total FROM gallery_images gi WHERE {$where_clause}";
if (!empty($params)) {
    $stmt = $conn->prepare($count_sql);
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_images = $row['total'];
    $stmt->close();
} else {
    $result = $conn->query($count_sql);
    $row = $result->fetch_assoc();
    $total_images = $row['total'];
}
$total_pages = ceil($total_images / $per_page);

// Get images
$sql = "SELECT gi.*, 
        ga.title as album_name
        FROM gallery_images gi
        LEFT JOIN gallery_albums ga ON gi.album_id = ga.id
        WHERE {$where_clause}
        ORDER BY gi.created_at DESC
        LIMIT {$per_page} OFFSET {$offset}";
        
$images = [];
if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
    $stmt->close();
} else {
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
}

// Get albums for filter
$albums = db_get_rows($conn, "SELECT * FROM gallery_albums WHERE is_active = 1 ORDER BY sort_order ASC");

include '../includes/header.php';
require_once '../includes/readonly-notice.php';
?>

<?php if (!$can_create && !$can_edit && !$can_delete): ?>
    <?php show_readonly_notice('แกลเลอรี่'); ?>
<?php endif; ?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-images mr-3 text-purple-600"></i>จัดการแกลเลอรี่
        </h2>
        <p class="text-gray-600 mt-1">จำนวนรูปภาพทั้งหมด: <?php echo number_format($total_images); ?> รูป</p>
    </div>
    <div class="mt-4 sm:mt-0 flex flex-wrap gap-3">
        <!-- จัดการอัลบั้ม -->
        <?php if ($can_edit): ?>
        <a href="albums.php" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
            <i class="fas fa-folder-open mr-2"></i>จัดการอัลบั้ม
        </a>
        <?php else: ?>
        <button class="inline-flex items-center px-6 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed" disabled>
            <i class="fas fa-lock mr-2"></i>จัดการอัลบั้ม
        </button>
        <?php endif; ?>
        
        <!-- อัพโหลดรูปภาพ -->
        <?php if ($can_create): ?>
        <a href="upload.php" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-lg transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
            <i class="fas fa-cloud-upload-alt mr-2"></i>อัพโหลดรูปภาพ
        </a>
        <?php else: ?>
        <button class="inline-flex items-center px-6 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed" disabled>
            <i class="fas fa-lock mr-2"></i>อัพโหลดรูปภาพ
        </button>
        <?php endif; ?>
        
        <!-- เลือกหลายรูป -->
        <?php if ($can_delete): ?>
        <button id="bulk-select-btn" onclick="toggleBulkSelect()" class="inline-flex items-center px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-check-square mr-2"></i>เลือกหลายรูป
        </button>
        <button id="bulk-actions" class="hidden inline-flex items-center px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium" onclick="bulkDelete()">
            <i class="fas fa-trash mr-2"></i>ลบที่เลือก
        </button>
        <?php endif; ?>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">ค้นหา</label>
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                   placeholder="ชื่อ, คำอธิบาย, แท็ก..."
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">อัลบั้ม</label>
            <select name="album" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                <option value="0">ทั้งหมด</option>
                <?php foreach ($albums as $album): ?>
                <option value="<?php echo $album['id']; ?>" <?php echo $album_id == $album['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($album['title']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-200 font-medium">
                <i class="fas fa-search mr-2"></i>ค้นหา
            </button>
        </div>
    </form>
</div>

<!-- Gallery Grid -->
<?php if (empty($images)): ?>
<div class="bg-white rounded-xl shadow-lg p-12 text-center">
    <i class="fas fa-images text-6xl text-gray-400 mb-4"></i>
    <h5 class="text-gray-600 text-xl font-medium mb-4">ยังไม่มีรูปภาพ</h5>
    <button id="upload-btn-2" class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-200 font-medium">
        <i class="fas fa-cloud-upload-alt mr-2"></i>อัพโหลดรูปภาพแรก
    </button>
</div>
<?php else: ?>
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-6">
    <?php foreach ($images as $image): ?>
    <div class="gallery-item group relative bg-white rounded-lg overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 cursor-pointer"
         data-id="<?php echo $image['id']; ?>"
         onclick="handleImageClick(<?php echo $image['id']; ?>)">
        <!-- Bulk Select Checkbox -->
        <div class="bulk-checkbox absolute top-2 left-2 z-10 transition-all duration-300" style="display: none;">
            <label class="flex items-center justify-center w-8 h-8 bg-white rounded-lg shadow-lg cursor-pointer hover:bg-purple-50 transition-colors">
                <input type="checkbox" class="image-checkbox w-5 h-5 text-purple-600 border-2 border-gray-300 rounded focus:ring-purple-500 focus:ring-2 cursor-pointer" 
                       data-id="<?php echo $image['id']; ?>" 
                       onclick="event.stopPropagation();"
                       onchange="updateBulkActions()">
            </label>
        </div>
        
        <!-- Image -->
        <div class="aspect-square overflow-hidden bg-gray-100">
            <img src="<?php echo BASE_URL . '/' . ($image['thumbnail_path'] ?: $image['image_path']); ?>" 
                 alt="<?php echo htmlspecialchars($image['title'] ?: 'Image'); ?>"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                 loading="lazy">
        </div>
        
        <!-- Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <div class="absolute bottom-0 left-0 right-0 p-3">
                <?php if ($image['title']): ?>
                <p class="text-white font-medium text-sm truncate mb-1"><?php echo htmlspecialchars($image['title']); ?></p>
                <?php endif; ?>
                <div class="flex items-center justify-between text-white/80 text-xs mb-2">
                    <span><i class="fas fa-eye mr-1"></i><?php echo number_format($image['view_count']); ?></span>
                    <span><?php echo date('d/m/Y', strtotime($image['created_at'])); ?></span>
                </div>
                <!-- Image Links -->
                <div class="flex space-x-1 mb-2">
                    <button onclick="event.stopPropagation(); showCopyOptions('<?php echo $image['image_path']; ?>', '<?php echo addslashes($image['title']); ?>')" 
                            class="flex-1 px-3 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg text-xs font-semibold hover:from-blue-700 hover:to-purple-700 transition-all shadow-lg"
                            title="คัดลอกลิงก์">
                        <i class="fas fa-copy mr-1"></i>Copy
                    </button>
                    <button onclick="event.stopPropagation(); downloadImage('<?php echo BASE_URL . '/' . $image['image_path']; ?>', '<?php echo $image['title']; ?>')" 
                            class="px-3 py-2 bg-green-600 text-white rounded-lg text-xs font-semibold hover:bg-green-700 transition-all shadow-lg"
                            title="ดาวน์โหลด">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex space-x-1">
            <button onclick="event.stopPropagation(); viewImage(<?php echo $image['id']; ?>)" 
                    class="w-8 h-8 bg-gray-700 hover:bg-gray-800 text-white rounded-full flex items-center justify-center shadow-lg"
                    title="ดูรายละเอียด">
                <i class="fas fa-eye text-xs"></i>
            </button>
            <button onclick="event.stopPropagation(); editImage(<?php echo $image['id']; ?>)" 
                    class="w-8 h-8 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center shadow-lg"
                    title="แก้ไข">
                <i class="fas fa-edit text-xs"></i>
            </button>
            <button onclick="event.stopPropagation(); deleteImage(<?php echo $image['id']; ?>)" 
                    class="w-8 h-8 bg-red-600 hover:bg-red-700 text-white rounded-full flex items-center justify-center shadow-lg"
                    title="ลบ">
                <i class="fas fa-trash text-xs"></i>
            </button>
        </div>
        
        <!-- Album Badge -->
        <?php if ($image['album_name']): ?>
        <div class="absolute top-2 left-2">
            <span class="inline-flex items-center px-2 py-1 bg-purple-600/90 text-white text-xs rounded-full">
                <i class="fas fa-folder mr-1"></i><?php echo $image['album_name']; ?>
            </span>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<!-- Pagination -->
<?php 
if ($total_pages > 1):
    include '../includes/pagination.php';
    render_pagination($page, $total_pages);
endif;
?>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>


<script src="gallery.js"></script>

<script>
// Bulk selection mode
let bulkSelectMode = false;

// Toggle bulk select mode
function toggleBulkSelect() {
    bulkSelectMode = !bulkSelectMode;
    const btn = document.getElementById('bulk-select-btn');
    const checkboxes = document.querySelectorAll('.bulk-checkbox');
    const bulkActions = document.getElementById('bulk-actions');
    
    if (bulkSelectMode) {
        btn.innerHTML = '<i class="fas fa-times mr-2"></i>ยกเลิกการเลือก';
        btn.className = btn.className.replace('bg-gray-600 hover:bg-gray-700', 'bg-red-600 hover:bg-red-700');
        checkboxes.forEach(cb => cb.style.display = 'block');
        checkboxes.forEach(cb => cb.style.opacity = '1');
    } else {
        btn.innerHTML = '<i class="fas fa-check-square mr-2"></i>เลือกหลายรูป';
        btn.className = btn.className.replace('bg-red-600 hover:bg-red-700', 'bg-gray-600 hover:bg-gray-700');
        checkboxes.forEach(cb => {
            cb.style.display = 'none';
            cb.style.opacity = '0';
        });
        document.querySelectorAll('.image-checkbox').forEach(cb => cb.checked = false);
        bulkActions.classList.add('hidden');
    }
}

// Handle image click
function handleImageClick(imageId) {
    if (bulkSelectMode) {
        const checkbox = document.querySelector(`input[data-id="${imageId}"]`);
        checkbox.checked = !checkbox.checked;
        updateBulkActions();
    } else {
        viewImage(imageId);
    }
}

// Update bulk actions visibility
function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.image-checkbox:checked');
    const bulkActions = document.getElementById('bulk-actions');
    
    if (checkedBoxes.length > 0) {
        bulkActions.classList.remove('hidden');
        bulkActions.innerHTML = `<i class="fas fa-trash mr-2"></i>ลบ ${checkedBoxes.length} รูป`;
    } else {
        bulkActions.classList.add('hidden');
    }
}

// Bulk delete
function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.image-checkbox:checked');
    const imageIds = Array.from(checkedBoxes).map(cb => cb.dataset.id);
    
    if (imageIds.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณาเลือกรูปภาพ',
            text: 'ต้องเลือกรูปภาพอย่างน้อย 1 รูป'
        });
        return;
    }
    
    Swal.fire({
        title: 'ยืนยันการลบ',
        html: `คุณแน่ใจหรือไม่ที่จะลบ <strong>${imageIds.length}</strong> รูปภาพ?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ลบเลย',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#DC2626',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'กำลังลบ...',
                html: '<i class="fas fa-spinner fa-spin text-4xl text-red-600"></i>',
                showConfirmButton: false,
                allowOutsideClick: false
            });
            
            // Delete each image
            Promise.all(imageIds.map(id => 
                fetch(`delete.php?id=${id}`, { method: 'POST' })
                    .then(response => response.json())
            )).then(results => {
                const successCount = results.filter(r => r.success).length;
                const failCount = results.length - successCount;
                
                if (successCount > 0) {
                    Swal.fire({
                        icon: 'success',
                        title: 'ลบสำเร็จ!',
                        text: `ลบ ${successCount} รูปภาพเรียบร้อย${failCount > 0 ? ` (ล้มเหลว ${failCount} รูป)` : ''}`,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'ลบไม่สำเร็จ',
                        text: 'ไม่สามารถลบรูปภาพได้'
                    });
                }
            }).catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'
                });
            });
        }
    });
}

// Copy image URL to clipboard
function copyImageUrl(url) {
    navigator.clipboard.writeText(url).then(() => {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
        Toast.fire({
            icon: 'success',
            title: 'คัดลอกลิงก์เรียบร้อย!'
        });
    }).catch(() => {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = url;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
        Toast.fire({
            icon: 'success',
            title: 'คัดลอกลิงก์เรียบร้อย!'
        });
    });
}

// Download image
function downloadImage(url, filename) {
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    });
    Toast.fire({
        icon: 'success',
        title: 'กำลังดาวน์โหลด...'
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Hide bulk checkboxes initially
    document.querySelectorAll('.bulk-checkbox').forEach(cb => {
        cb.style.display = 'none';
        cb.style.opacity = '0';
    });
});
</script>



