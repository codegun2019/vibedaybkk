<?php
/**
 * VIBEDAYBKK Admin - Homepage Management
 * จัดการหน้าแรกของเว็บไซต์
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check - allow view for all roles with permission
require_permission('homepage', 'view');
$can_create = has_permission('homepage', 'create');
$can_edit = has_permission('homepage', 'edit');
$can_delete = has_permission('homepage', 'delete');

$page_title = 'จัดการหน้าแรก';
$current_page = 'homepage';

// Get all homepage sections
$sql = "SELECT * FROM homepage_sections ORDER BY sort_order ASC";
$sections = db_get_rows($conn, $sql);

include '../includes/header.php';
require_once '../includes/readonly-notice.php';
?>

<?php if (!$can_create && !$can_edit && !$can_delete): ?>
    <?php show_readonly_notice('หน้าแรก'); ?>
<?php endif; ?>

<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-900 flex items-center">
        <i class="fas fa-home mr-3 text-red-600"></i>จัดการหน้าแรก
        <?php if (!$can_edit && !$can_delete): ?>
        <span class="ml-3 text-lg text-yellow-600">
            <i class="fas fa-eye"></i> ดูอย่างเดียว
        </span>
        <?php endif; ?>
    </h2>
    <p class="text-gray-600 mt-1">จัดการเนื้อหาและรูปภาพในหน้าแรกของเว็บไซต์</p>
</div>

<!-- Sections List -->
<div class="grid grid-cols-1 gap-6">
    <?php foreach ($sections as $section): ?>
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="flex items-center justify-between p-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                    <?php
                    $icons = [
                        'hero' => 'fa-star',
                        'about' => 'fa-info-circle',
                        'services' => 'fa-briefcase',
                        'gallery' => 'fa-images',
                        'testimonials' => 'fa-comments',
                        'stats' => 'fa-chart-bar',
                        'cta' => 'fa-bullhorn',
                        'features' => 'fa-list-check'
                    ];
                    $icon = $icons[$section['section_key']] ?? 'fa-square';
                    ?>
                    <i class="fas <?php echo $icon; ?> text-2xl text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900"><?php echo $section['title'] ?? $section['section_key']; ?></h3>
                    <p class="text-sm text-gray-500">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            <?php echo ucfirst($section['section_key'] ?? 'section'); ?>
                        </span>
                        <span class="ml-2">ลำดับที่: <?php echo $section['sort_order']; ?></span>
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Active Toggle -->
                <?php if ($can_edit): ?>
                <form method="POST" action="toggle-status.php" class="inline">
                    <input type="hidden" name="id" value="<?php echo $section['id']; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <button type="submit" class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 <?php echo $section['is_active'] ? 'bg-green-600' : 'bg-gray-300'; ?>">
                        <span class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform <?php echo $section['is_active'] ? 'translate-x-6' : 'translate-x-1'; ?>"></span>
                    </button>
                </form>
                <?php else: ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo $section['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                    <?php echo $section['is_active'] ? '✓ เปิดใช้งาน' : '✗ ปิดใช้งาน'; ?>
                </span>
                <?php endif; ?>
                
                <!-- Edit Button -->
                <?php if ($can_edit): ?>
                <a href="edit.php?id=<?php echo $section['id']; ?>" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-edit mr-2"></i>แก้ไข
                </a>
                <?php else: ?>
                <span class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-600 rounded-lg cursor-not-allowed">
                    <i class="fas fa-lock mr-2"></i>ล็อค
                </span>
                <?php endif; ?>
                
                <!-- Gallery Button (for gallery section) -->
                <?php if ($section['section_key'] == 'gallery'): ?>
                    <?php if ($can_edit): ?>
                    <a href="gallery.php?section_id=<?php echo $section['id']; ?>" 
                       class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-200 font-medium">
                        <i class="fas fa-images mr-2"></i>จัดการรูปภาพ
                    </a>
                    <?php else: ?>
                    <span class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-600 rounded-lg cursor-not-allowed">
                        <i class="fas fa-lock mr-2"></i>ล็อค
                    </span>
                    <?php endif; ?>
                <?php endif; ?>
                
                <!-- Features Button (for sections with features) -->
                <?php if (in_array($section['section_key'], ['about', 'stats', 'features'])): ?>
                <a href="features.php?section_id=<?php echo $section['id']; ?>" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-list mr-2"></i>จัดการรายการ
                </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Content Preview -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">เนื้อหา</h4>
                    <?php if ($section['title']): ?>
                    <div class="mb-3">
                        <span class="text-xs text-gray-500">หัวข้อ:</span>
                        <p class="text-gray-900 font-semibold"><?php echo $section['title']; ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($section['subtitle']): ?>
                    <div class="mb-3">
                        <span class="text-xs text-gray-500">หัวข้อรอง:</span>
                        <p class="text-gray-700"><?php echo $section['subtitle']; ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($section['content'])): ?>
                    <div class="mb-3">
                        <span class="text-xs text-gray-500">เนื้อหา:</span>
                        <p class="text-gray-600 text-sm"><?php echo truncate_text($section['content'], 150); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($section['button1_text'])): ?>
                    <div class="mt-4">
                        <a href="#" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm">
                            <?php echo $section['button1_text']; ?>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Settings Preview -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">การตั้งค่า</h4>
                    <div class="space-y-2 text-sm">
                        <?php if ($section['background_color']): ?>
                        <div class="flex items-center">
                            <span class="text-gray-500 w-32">สีพื้นหลัง:</span>
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded border border-gray-300 mr-2" style="background-color: <?php echo $section['background_color']; ?>"></div>
                                <code class="text-xs"><?php echo $section['background_color']; ?></code>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php // text_color column ไม่มีใน database ?>
                        
                        <?php if ($section['background_image']): ?>
                        <div class="flex items-center">
                            <span class="text-gray-500 w-32">รูปพื้นหลัง:</span>
                            <?php 
                            $preview_url = (strpos($section['background_image'], 'uploads/') === 0) 
                                ? BASE_URL . '/' . $section['background_image'] 
                                : UPLOADS_URL . '/' . $section['background_image'];
                            ?>
                            <img src="<?php echo $preview_url; ?>" 
                                 class="h-16 w-24 object-cover rounded border border-gray-300">
                        </div>
                        <?php endif; ?>
                        
                        <?php // settings column ไม่มีใน database ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Preview Button -->
<div class="mt-8 text-center">
    <a href="../../index.php" target="_blank" 
       class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-lg font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
        <i class="fas fa-eye mr-3"></i>ดูหน้าเว็บ
    </a>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// AJAX Toggle for Active Status
document.querySelectorAll('form[action="toggle-status.php"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const button = this.querySelector('button[type="submit"]');
        const toggleSpan = button.querySelector('span');
        
        // Send AJAX request
        fetch('toggle-status.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            // Toggle button classes
            if (button.classList.contains('bg-green-600')) {
                button.classList.remove('bg-green-600');
                button.classList.add('bg-gray-300');
                toggleSpan.classList.remove('translate-x-6');
                toggleSpan.classList.add('translate-x-1');
                
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: 'ปิดการใช้งาน section แล้ว',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            } else {
                button.classList.remove('bg-gray-300');
                button.classList.add('bg-green-600');
                toggleSpan.classList.remove('translate-x-1');
                toggleSpan.classList.add('translate-x-6');
                
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: 'เปิดใช้งาน section แล้ว',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด!',
                text: 'เกิดข้อผิดพลาดในการอัปเดต',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        });
    });
});

// Show success/error messages
<?php if (isset($_SESSION['success'])): ?>
Swal.fire({
    icon: 'success',
    title: 'สำเร็จ!',
    text: '<?php echo $_SESSION['success']; ?>',
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});
<?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
Swal.fire({
    icon: 'error',
    title: 'ผิดพลาด!',
    text: '<?php echo $_SESSION['error']; ?>',
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});
<?php unset($_SESSION['error']); ?>
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>




