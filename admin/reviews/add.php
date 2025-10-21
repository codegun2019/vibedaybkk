<?php
/**
 * VIBEDAYBKK Admin - Add Review
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check
require_permission('reviews', 'create');

$page_title = 'เพิ่มรีวิว';
$current_page = 'reviews';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $data = [
            'customer_name' => clean_input($_POST['customer_name']),
            'rating' => (int)$_POST['rating'],
            'content' => clean_input($_POST['content']),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'sort_order' => (int)($_POST['sort_order'] ?? 0)
        ];
        
        // Upload image
        if (!empty($_FILES['image']['tmp_name'])) {
            $upload = upload_image($_FILES['image'], 'reviews');
            if ($upload['success']) {
                $data['image'] = $upload['file_path'];
            } else {
                $errors[] = 'ไม่สามารถอัพโหลดรูปภาพได้: ' . ($upload['error'] ?? 'Unknown error');
            }
        }
        
        if (empty($errors)) {
            if (db_insert($conn, 'customer_reviews', $data)) {
                $review_id = $conn->insert_id;
                log_activity($conn, $_SESSION['user_id'], 'create', 'customer_reviews', $review_id);
                set_message('success', 'เพิ่มรีวิวสำเร็จ');
                redirect(ADMIN_URL . '/reviews/');
            } else {
                $errors[] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h2 class="text-3xl font-bold text-gray-900 flex items-center">
        <i class="fas fa-plus-circle mr-3 text-green-600"></i>เพิ่มรีวิว
    </h2>
    <a href="index.php" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
        <i class="fas fa-arrow-left mr-2"></i>กลับ
    </a>
</div>

<?php if (!empty($errors)): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
    <h5 class="font-bold mb-2 flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>พบข้อผิดพลาด:
    </h5>
    <ul class="list-disc list-inside">
        <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
    <!-- ข้อมูลรีวิว -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-star mr-3"></i>ข้อมูลรีวิว
            </h5>
        </div>
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        ชื่อลูกค้า <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="customer_name" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                           placeholder="ชื่อ-นามสกุล">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        คะแนน <span class="text-red-600">*</span>
                    </label>
                    <select name="rating" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                        <option value="">เลือกคะแนน</option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                        <option value="<?php echo $i; ?>">
                            <?php echo str_repeat('⭐', $i); ?> <?php echo $i; ?> ดาว
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    เนื้อหารีวิว <span class="text-red-600">*</span>
                </label>
                <textarea name="content" required rows="4" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 resize-none"
                          placeholder="เขียนรีวิว..."></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ลำดับการแสดง</label>
                    <input type="number" name="sort_order" value="0" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                    <p class="text-sm text-gray-500 mt-1">ตัวเลขน้อยจะแสดงก่อน</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">สถานะ</label>
                    <div class="flex items-center space-x-3">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_active" value="1" checked>
                            <span class="toggle-slider"></span>
                        </label>
                        <span class="text-sm text-gray-700">เปิดใช้งาน</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- รูปภาพ -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-image mr-3"></i>รูปภาพ
            </h5>
        </div>
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">อัพโหลดรูปภาพ</label>
                <div class="relative">
                    <input type="file" name="image" id="image-upload" accept="image/*" 
                           class="hidden" onchange="previewImage(this)">
                    <label for="image-upload" 
                           class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-purple-400 transition-all duration-300 group">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 group-hover:text-purple-500 transition-colors duration-300 mb-3"></i>
                            <p class="mb-2 text-sm text-gray-500">
                                <span class="font-semibold">คลิกเพื่อเลือกไฟล์</span> หรือลากไฟล์มาวางที่นี่
                            </p>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF (ขนาดไม่เกิน 5MB)</p>
                        </div>
                    </label>
                </div>
                
                <!-- Image Preview -->
                <div id="image-preview" class="hidden mt-4">
                    <div class="relative inline-block">
                        <img id="preview-img" src="" alt="Preview" 
                             class="w-32 h-32 object-cover rounded-xl shadow-lg border-2 border-gray-200">
                        <button type="button" onclick="removePreview()" 
                                class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs transition-colors duration-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ปุ่มบันทึก -->
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

<?php include '../includes/footer.php'; ?>

<script>
// Image preview functionality
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removePreview() {
    document.getElementById('image-upload').value = '';
    document.getElementById('image-preview').classList.add('hidden');
}

// Drag and drop functionality
const dropZone = document.querySelector('label[for="image-upload"]');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, unhighlight, false);
});

function highlight(e) {
    dropZone.classList.add('border-purple-400', 'bg-purple-50');
}

function unhighlight(e) {
    dropZone.classList.remove('border-purple-400', 'bg-purple-50');
}

dropZone.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    
    if (files.length > 0) {
        document.getElementById('image-upload').files = files;
        previewImage(document.getElementById('image-upload'));
    }
}
</script>

<style>
/* Custom styles for better UX */
label[for="image-upload"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

#image-preview img {
    transition: transform 0.3s ease;
}

#image-preview img:hover {
    transform: scale(1.05);
}
</style>