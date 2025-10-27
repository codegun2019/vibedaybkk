<?php
/**
 * VIBEDAYBKK Admin - Edit Review
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check
require_permission('reviews', 'edit');

$page_title = 'แก้ไขรีวิว';
$current_page = 'reviews';

$id = (int)($_GET['id'] ?? 0);
$errors = [];
$success = false;

$stmt = $conn->prepare("SELECT * FROM customer_reviews WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$review = $result->fetch_assoc();
$stmt->close();

if (!$review) {
    set_message('error', 'ไม่พบข้อมูลรีวิว');
    redirect(ADMIN_URL . '/reviews/');
}

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
        
        // Upload new image if provided
        if (!empty($_FILES['image']['tmp_name'])) {
            $upload = upload_image($_FILES['image'], 'reviews');
            if ($upload['success']) {
                $data['image'] = $upload['file_path'];
                // Delete old image
                if (!empty($review['image'])) {
                    // ลอง path ต่างๆ
                    $old_image_paths = [
                        UPLOADS_PATH . '/' . str_replace('uploads/', '', $review['image']),
                        ROOT_PATH . '/' . $review['image'],
                        $review['image']
                    ];
                    
                    foreach ($old_image_paths as $old_path) {
                        if (file_exists($old_path)) {
                            @unlink($old_path);
                            break;
                        }
                    }
                }
            } else {
                $errors[] = 'ไม่สามารถอัพโหลดรูปภาพได้: ' . ($upload['error'] ?? 'Unknown error');
            }
        }
        
        if (empty($errors)) {
            if (db_update($conn, 'customer_reviews', $data, 'id = ?', [$id])) {
                log_activity($conn, $_SESSION['user_id'], 'update', 'customer_reviews', $id);
                $success = true;
                // Refresh data
                $stmt = $conn->prepare("SELECT * FROM customer_reviews WHERE id = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $review = $result->fetch_assoc();
                $stmt->close();
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
        <i class="fas fa-edit mr-3 text-blue-600"></i>แก้ไขรีวิว
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
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
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
                    <input type="text" name="customer_name" value="<?php echo htmlspecialchars($review['customer_name']); ?>" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        คะแนน <span class="text-red-600">*</span>
                    </label>
                    <select name="rating" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                        <option value="<?php echo $i; ?>" <?php echo $review['rating'] == $i ? 'selected' : ''; ?>>
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
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none"
                          placeholder="เขียนรีวิว..."><?php echo htmlspecialchars($review['content']); ?></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ลำดับการแสดง</label>
                    <input type="number" name="sort_order" value="<?php echo $review['sort_order'] ?? 0; ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    <p class="text-sm text-gray-500 mt-1">ตัวเลขน้อยจะแสดงก่อน</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">สถานะ</label>
                    <div class="flex items-center space-x-3">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_active" value="1" <?php echo $review['is_active'] ? 'checked' : ''; ?>>
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
            <?php if (!empty($review['image'])): ?>
            <?php 
            // ตรวจสอบ path ของรูปภาพ
            $image_path = $review['image'];
            
            // ถ้าเป็น relative path (เริ่มด้วย uploads/) ให้ใช้ UPLOADS_URL
            if (strpos($image_path, 'uploads/') === 0) {
                $image_url = UPLOADS_URL . '/' . str_replace('uploads/', '', $image_path);
            } 
            // ถ้าไม่มี 'http' ให้ต่อกับ BASE_URL
            elseif (strpos($image_path, 'http') === false) {
                $image_url = BASE_URL . '/' . $image_path;
            } 
            // ถ้าเป็น full URL อยู่แล้ว
            else {
                $image_url = $image_path;
            }
            
            // ตรวจสอบว่าไฟล์มีอยู่จริงหรือไม่
            $image_exists = false;
            $test_paths = [
                $image_path,
                ROOT_PATH . '/' . $image_path,
                str_replace('uploads/', UPLOADS_PATH . '/', $image_path)
            ];
            
            foreach ($test_paths as $test_path) {
                if (file_exists($test_path)) {
                    $image_exists = true;
                    break;
                }
            }
            
            // Debug info (remove in production)
            $debug_info = [
                'database_path' => $image_path,
                'resolved_url' => $image_url,
                'file_exists' => $image_exists
            ];
            ?>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">รูปภาพปัจจุบัน</label>
                
                <?php if ($image_exists): ?>
                <div class="flex items-start space-x-4">
                    <div class="relative group">
                        <img src="<?php echo htmlspecialchars($image_url); ?>" 
                             alt="รูปภาพรีวิว" 
                             class="w-32 h-32 object-cover rounded-xl shadow-lg border-2 border-gray-200 group-hover:shadow-xl transition-all duration-300"
                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2232%22 height=%2232%22%3E%3Crect width=%2232%22 height=%2232%22 fill=%22%23e5e7eb%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2212%22 fill=%22%23999%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3Eไม่พบรูป%3C/text%3E%3C/svg%3E';">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 rounded-xl transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-eye text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-600 mb-2">คลิกที่รูปเพื่อดูขนาดเต็ม</p>
                        <button type="button" onclick="viewImage('<?php echo htmlspecialchars($image_url); ?>')" 
                                class="inline-flex items-center px-3 py-2 bg-blue-100 hover:bg-blue-200 text-blue-800 rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-expand mr-2"></i>ดูรูปภาพ
                        </button>
                    </div>
                </div>
                <?php else: ?>
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                        <div>
                            <p class="text-sm font-medium text-yellow-800 mb-1">ไม่พบไฟล์รูปภาพ</p>
                            <p class="text-xs text-yellow-600">Path ในฐานข้อมูล: <?php echo htmlspecialchars($image_path); ?></p>
                            <p class="text-xs text-yellow-600">Resolved URL: <?php echo htmlspecialchars($image_url); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">อัพโหลดรูปภาพใหม่</label>
                <div class="relative">
                    <input type="file" name="image" id="image-upload" accept="image/*" 
                           class="hidden" onchange="previewImage(this)">
                    <label for="image-upload" 
                           class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-blue-400 transition-all duration-300 group">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 group-hover:text-blue-500 transition-colors duration-300 mb-3"></i>
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
                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 font-medium">
            <i class="fas fa-save mr-2"></i>บันทึกการเปลี่ยนแปลง
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

// View full image
function viewImage(imageSrc) {
    Swal.fire({
        imageUrl: imageSrc,
        imageAlt: 'รูปภาพรีวิว',
        showConfirmButton: false,
        showCloseButton: true,
        customClass: {
            popup: 'rounded-2xl',
            closeButton: 'text-gray-400 hover:text-gray-600 text-2xl'
        }
    });
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
    dropZone.classList.add('border-blue-400', 'bg-blue-50');
}

function unhighlight(e) {
    dropZone.classList.remove('border-blue-400', 'bg-blue-50');
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