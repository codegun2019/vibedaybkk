<?php
/**
 * VIBEDAYBKK Admin - Add Menu
 * เพิ่มเมนูใหม่
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';


// Permission check
require_permission('menus', 'create');
$page_title = 'เพิ่มเมนูใหม่';
$current_page = 'menus';

$errors = [];

// Get parent menus
$parent_menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL ORDER BY sort_order ASC");

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
            if (db_insert($conn, 'menus', $data)) {
                $menu_id = $conn->insert_id;
                log_activity($conn, $_SESSION['user_id'], 'create', 'menus', $menu_id, null, $data);
                set_message('success', 'เพิ่มเมนูสำเร็จ');
                redirect(ADMIN_URL . '/menus/');
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
        <i class="fas fa-plus-circle mr-3 text-green-600"></i>เพิ่มเมนูใหม่
    </h2>
    <a href="index.php" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
        <i class="fas fa-arrow-left mr-2"></i>กลับ
    </a>
</div>

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
                    <input type="text" name="title" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        URL <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="url" required placeholder="index.php หรือ #section" 
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
                        <option value="<?php echo $parent['id']; ?>"><?php echo $parent['title']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ไอคอน</label>
                    <div class="flex items-center space-x-3">
                        <input type="text" name="icon" id="icon-input" placeholder="fa-home" 
                               class="icon-picker-input flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <div id="icon-input_preview" class="w-16 h-16 flex items-center justify-center bg-gradient-to-br from-blue-100 to-purple-100 rounded-lg border-2 border-blue-300">
                            <i class="fas fa-question text-3xl text-gray-400"></i>
                        </div>
                        <button type="button" class="icon-picker-btn px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-200 font-medium shadow-lg">
                            <i class="fas fa-icons mr-2"></i>เลือกไอคอน
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        หรือดูไอคอนได้ที่ <a href="https://fontawesome.com/icons" target="_blank" class="text-blue-600 hover:underline">FontAwesome</a>
                    </p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Target</label>
                    <select name="target" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <option value="_self">เปิดหน้าเดิม (_self)</option>
                        <option value="_blank">เปิดหน้าใหม่ (_blank)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ลำดับ</label>
                    <input type="number" name="sort_order" value="0" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">สถานะ</label>
                    <select name="status" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <option value="active">ใช้งาน</option>
                        <option value="inactive">ไม่ใช้งาน</option>
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
                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 font-medium">
            <i class="fas fa-save mr-2"></i>บันทึก
        </button>
    </div>
</form>

<!-- Icon Picker Modal -->
<div id="icon-picker-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4 flex items-center justify-between">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-icons mr-3"></i>เลือกไอคอน
            </h5>
            <button type="button" id="close-icon-picker" class="text-white hover:text-gray-200">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <div class="p-6">
            <input type="text" id="icon-search" placeholder="ค้นหาไอคอน... (เช่น home, user, star)" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 mb-4">
            
            <div id="icon-grid" class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-3 overflow-y-auto max-h-96">
                <!-- Icons will be loaded here -->
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="../includes/icon-picker.js"></script>

<script>
// Popular FontAwesome icons (120+ icons)
const popularIcons = [
    'fa-home', 'fa-user', 'fa-users', 'fa-star', 'fa-heart', 'fa-envelope', 'fa-phone', 'fa-map-marker-alt',
    'fa-calendar', 'fa-clock', 'fa-search', 'fa-shopping-cart', 'fa-camera', 'fa-image', 'fa-video',
    'fa-music', 'fa-file', 'fa-folder', 'fa-download', 'fa-upload', 'fa-cloud', 'fa-database',
    'fa-cog', 'fa-wrench', 'fa-tools', 'fa-edit', 'fa-trash', 'fa-save', 'fa-print', 'fa-share',
    'fa-info-circle', 'fa-question-circle', 'fa-exclamation-circle', 'fa-check-circle', 'fa-times-circle',
    'fa-plus-circle', 'fa-minus-circle', 'fa-arrow-left', 'fa-arrow-right', 'fa-arrow-up', 'fa-arrow-down',
    'fa-chevron-left', 'fa-chevron-right', 'fa-chevron-up', 'fa-chevron-down', 'fa-bars', 'fa-th',
    'fa-list', 'fa-th-list', 'fa-table', 'fa-chart-bar', 'fa-chart-line', 'fa-chart-pie',
    'fa-briefcase', 'fa-building', 'fa-hospital', 'fa-university', 'fa-graduation-cap', 'fa-book',
    'fa-bookmark', 'fa-tag', 'fa-tags', 'fa-flag', 'fa-trophy', 'fa-gift', 'fa-bell', 'fa-comment',
    'fa-comments', 'fa-inbox', 'fa-paper-plane', 'fa-thumbs-up', 'fa-thumbs-down', 'fa-lock', 'fa-unlock',
    'fa-key', 'fa-shield-alt', 'fa-eye', 'fa-eye-slash', 'fa-lightbulb', 'fa-fire', 'fa-bolt',
    'fa-globe', 'fa-wifi', 'fa-bluetooth', 'fa-rss', 'fa-link', 'fa-unlink', 'fa-code', 'fa-terminal',
    'fa-laptop', 'fa-mobile-alt', 'fa-tablet-alt', 'fa-desktop', 'fa-tv', 'fa-keyboard', 'fa-mouse',
    'fa-gamepad', 'fa-headphones', 'fa-microphone', 'fa-volume-up', 'fa-volume-down', 'fa-volume-mute',
    'fa-play', 'fa-pause', 'fa-stop', 'fa-forward', 'fa-backward', 'fa-step-forward', 'fa-step-backward',
    'fa-male', 'fa-female', 'fa-child', 'fa-baby', 'fa-running', 'fa-walking', 'fa-biking', 'fa-car',
    'fa-bus', 'fa-train', 'fa-plane', 'fa-ship', 'fa-rocket', 'fa-bicycle', 'fa-motorcycle',
    'fa-coffee', 'fa-pizza-slice', 'fa-hamburger', 'fa-ice-cream', 'fa-birthday-cake', 'fa-wine-glass',
    'fa-beer', 'fa-utensils', 'fa-apple-alt', 'fa-lemon', 'fa-pepper-hot', 'fa-carrot',
    'fa-newspaper', 'fa-blog', 'fa-rss-square', 'fa-pen', 'fa-pencil-alt', 'fa-highlighter'
];

// Icon preview
const iconInput = document.getElementById('icon-input');
const iconPreview = document.getElementById('icon-preview');

iconInput.addEventListener('input', function() {
    const iconClass = this.value.trim();
    if (iconClass) {
        iconPreview.innerHTML = '<i class="fas ' + iconClass + ' text-3xl text-blue-600"></i>';
    } else {
        iconPreview.innerHTML = '<i class="fas fa-question text-3xl text-gray-400"></i>';
    }
});

// Icon Picker Modal
const modal = document.getElementById('icon-picker-modal');
const openBtn = document.getElementById('open-icon-picker');
const closeBtn = document.getElementById('close-icon-picker');
const iconGrid = document.getElementById('icon-grid');
const iconSearch = document.getElementById('icon-search');

// Load icons
function loadIcons(filter = '') {
    iconGrid.innerHTML = '';
    const filteredIcons = filter 
        ? popularIcons.filter(icon => icon.toLowerCase().includes(filter.toLowerCase()))
        : popularIcons;
    
    filteredIcons.forEach(icon => {
        const div = document.createElement('div');
        div.className = 'icon-item flex flex-col items-center justify-center p-3 bg-gray-50 hover:bg-purple-100 rounded-lg cursor-pointer transition-all duration-200 border-2 border-transparent hover:border-purple-500';
        div.innerHTML = '<i class="fas ' + icon + ' text-2xl text-gray-700 mb-1"></i><span class="text-xs text-gray-600">' + icon.replace('fa-', '') + '</span>';
        div.addEventListener('click', function() {
            iconInput.value = icon;
            iconPreview.innerHTML = '<i class="fas ' + icon + ' text-3xl text-blue-600"></i>';
            modal.classList.add('hidden');
            
            // Show toast
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'success',
                title: 'เลือกไอคอน: ' + icon
            });
        });
        iconGrid.appendChild(div);
    });
    
    if (filteredIcons.length === 0) {
        iconGrid.innerHTML = '<div class="col-span-full text-center py-8 text-gray-500">ไม่พบไอคอนที่ค้นหา</div>';
    }
}

// Open modal
openBtn.addEventListener('click', function() {
    modal.classList.remove('hidden');
    loadIcons();
    iconSearch.focus();
});

// Close modal
closeBtn.addEventListener('click', function() {
    modal.classList.add('hidden');
});

// Close on outside click
modal.addEventListener('click', function(e) {
    if (e.target === modal) {
        modal.classList.add('hidden');
    }
});

// Search icons
iconSearch.addEventListener('input', function() {
    loadIcons(this.value);
});

// Close on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
        modal.classList.add('hidden');
    }
});
</script>


