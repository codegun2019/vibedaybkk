<?php
/**
 * VIBEDAYBKK Admin - Homepage Management
 * จัดการหน้าแรกของเว็บไซต์
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_admin();

$page_title = 'จัดการหน้าแรก';
$current_page = 'homepage';

// Get all homepage sections
$sql = "SELECT * FROM homepage_sections ORDER BY sort_order ASC";
$sections = db_get_rows($conn, $sql);

include '../includes/header.php';
?>

<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-900 flex items-center">
        <i class="fas fa-home mr-3 text-red-600"></i>จัดการหน้าแรก
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
                    $icon = $icons[$section['section_type']] ?? 'fa-square';
                    ?>
                    <i class="fas <?php echo $icon; ?> text-2xl text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900"><?php echo $section['section_name']; ?></h3>
                    <p class="text-sm text-gray-500">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            <?php echo ucfirst($section['section_type']); ?>
                        </span>
                        <span class="ml-2">ลำดับที่: <?php echo $section['sort_order']; ?></span>
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Active Toggle -->
                <form method="POST" action="toggle-status.php" class="inline">
                    <input type="hidden" name="id" value="<?php echo $section['id']; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <button type="submit" class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 <?php echo $section['is_active'] ? 'bg-green-600' : 'bg-gray-300'; ?>">
                        <span class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform <?php echo $section['is_active'] ? 'translate-x-6' : 'translate-x-1'; ?>"></span>
                    </button>
                </form>
                
                <!-- Edit Button -->
                <a href="edit.php?id=<?php echo $section['id']; ?>" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-edit mr-2"></i>แก้ไข
                </a>
                
                <!-- Gallery Button (for gallery section) -->
                <?php if ($section['section_type'] == 'gallery'): ?>
                <a href="gallery.php?section_id=<?php echo $section['id']; ?>" 
                   class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-images mr-2"></i>จัดการรูปภาพ
                </a>
                <?php endif; ?>
                
                <!-- Features Button (for sections with features) -->
                <?php if (in_array($section['section_type'], ['about', 'stats', 'features'])): ?>
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
                    
                    <?php if ($section['description']): ?>
                    <div class="mb-3">
                        <span class="text-xs text-gray-500">คำอธิบาย:</span>
                        <p class="text-gray-600 text-sm"><?php echo truncate_text($section['description'], 150); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($section['button_text']): ?>
                    <div class="mt-4">
                        <a href="#" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm">
                            <?php echo $section['button_text']; ?>
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
                        
                        <?php if ($section['text_color']): ?>
                        <div class="flex items-center">
                            <span class="text-gray-500 w-32">สีตัวอักษร:</span>
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded border border-gray-300 mr-2" style="background-color: <?php echo $section['text_color']; ?>"></div>
                                <code class="text-xs"><?php echo $section['text_color']; ?></code>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($section['background_image']): ?>
                        <div class="flex items-center">
                            <span class="text-gray-500 w-32">รูปพื้นหลัง:</span>
                            <img src="<?php echo UPLOADS_URL . '/' . $section['background_image']; ?>" 
                                 class="h-16 w-24 object-cover rounded border border-gray-300">
                        </div>
                        <?php endif; ?>
                        
                        <?php
                        $settings = json_decode($section['settings'], true);
                        if ($settings && is_array($settings)):
                            foreach ($settings as $key => $value):
                        ?>
                        <div class="flex items-center">
                            <span class="text-gray-500 w-32"><?php echo ucfirst(str_replace('_', ' ', $key)); ?>:</span>
                            <span class="text-gray-700"><?php echo is_bool($value) ? ($value ? 'Yes' : 'No') : $value; ?></span>
                        </div>
                        <?php 
                            endforeach;
                        endif; 
                        ?>
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

<?php include '../includes/footer.php'; ?>

