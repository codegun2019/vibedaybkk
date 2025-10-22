<?php
/**
 * เพิ่มรูปภาพแกลลอรี่
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check
require_permission('gallery', 'create');

$page_title = 'เพิ่มรูปภาพแกลลอรี่';
$current_page = 'gallery';

$errors = [];
$success = false;

// ดึงหมวดหมู่ที่มีอยู่
$existing_categories = db_get_rows($conn, "SELECT DISTINCT category FROM gallery WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");

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
    // Validate CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $title = clean_input($_POST['title']);
        $description = clean_input($_POST['description']);
        $category = clean_input($_POST['category']);
        $tags = clean_input($_POST['tags']);
        $sort_order = intval($_POST['sort_order']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Validate
        if (empty($title)) {
            $errors[] = 'กรุณากรอกชื่อรูปภาพ';
        }
        
        if (empty($category)) {
            $errors[] = 'กรุณาเลือกหมวดหมู่';
        }
        
        // อัพโหลดรูปภาพ
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_result = upload_image($_FILES['image'], 'gallery');
            if ($upload_result['success']) {
                $image_path = $upload_result['file_path'];
            } else {
                $errors[] = $upload_result['message'];
            }
        } else {
            $errors[] = 'กรุณาเลือกไฟล์รูปภาพ';
        }
        
        if (empty($errors)) {
            $data = [
                'title' => $title,
                'description' => $description,
                'image' => $image_path,
                'category' => $category,
                'tags' => $tags,
                'sort_order' => $sort_order,
                'is_active' => $is_active
            ];
            
            if (db_insert($conn, 'gallery', $data)) {
                $_SESSION['success_message'] = 'เพิ่มรูปภาพสำเร็จ';
                header('Location: index.php');
                exit;
            } else {
                $errors[] = 'เกิดข้อผิดพลาดในการเพิ่มรูปภาพ';
            }
        }
    }
}

include '../includes/header.php';
?>

<style>
    .upload-area {
        border: 3px dashed #cbd5e1;
        border-radius: 1rem;
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        min-height: 300px;
    }
    
    .upload-area:hover {
        border-color: #9333ea;
        background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(147, 51, 234, 0.1);
    }
    
    .upload-area.dragover {
        border-color: #7c3aed;
        background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
        box-shadow: 0 0 0 4px rgba(147, 51, 234, 0.1);
    }
    
    .preview-container {
        display: none;
        animation: fadeIn 0.3s ease;
    }
    
    .preview-container.active {
        display: block;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    
    .preview-image {
        max-height: 350px;
        width: 100%;
        object-fit: contain;
        border-radius: 0.75rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }
    
    .upload-icon {
        font-size: 4rem;
        background: linear-gradient(135deg, #9333ea 0%, #ec4899 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: pulse 2s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.8; transform: scale(1.05); }
    }
    
    .file-info {
        background: linear-gradient(135deg, #9333ea 0%, #ec4899 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 500;
        font-size: 0.875rem;
        box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3);
    }
</style>

<!-- Breadcrumb -->
<div class="mb-6">
    <nav class="flex items-center space-x-2 text-sm text-gray-600">
        <a href="<?php echo ADMIN_URL; ?>/index.php" class="hover:text-red-600">
            <i class="fas fa-home"></i>
        </a>
        <i class="fas fa-chevron-right text-xs"></i>
        <a href="index.php" class="hover:text-red-600">จัดการแกลลอรี่</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-900">เพิ่มรูปภาพ</span>
    </nav>
</div>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-cloud-upload-alt mr-3 text-purple-600"></i><?php echo $page_title; ?>
        </h2>
        <p class="text-gray-600 mt-1">อัพโหลดรูปภาพเข้าสู่แกลลอรี่ของคุณ</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="index.php" class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium shadow-lg">
            <i class="fas fa-arrow-left mr-2"></i>กลับ
        </a>
    </div>
</div>

<!-- Error Messages -->
<?php if (!empty($errors)): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-lg mb-6 shadow-md animate__animated animate__shakeX">
        <div class="flex items-start">
            <i class="fas fa-exclamation-circle text-2xl mr-3 mt-1"></i>
            <div class="flex-1">
                <p class="font-semibold mb-2">พบข้อผิดพลาด!</p>
                <ul class="list-disc list-inside space-y-1">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Main Form -->
<form method="POST" enctype="multipart/form-data" id="uploadForm" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Upload Section (Left) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-images mr-2 text-purple-600"></i>อัพโหลดรูปภาพ
                </h3>
                
                <!-- Upload Area -->
                <div class="upload-area p-8 text-center cursor-pointer" id="uploadArea">
                    <input type="file" name="image" id="imageInput" accept="image/*" required class="hidden">
                    
                    <div id="uploadPlaceholder">
                        <div class="upload-icon mb-4">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-2">ลากและวางไฟล์ที่นี่</h4>
                        <p class="text-gray-600 mb-4">หรือ</p>
                        <button type="button" onclick="document.getElementById('imageInput').click()" 
                                class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-full font-semibold hover:shadow-xl transition-all transform hover:scale-105">
                            <i class="fas fa-folder-open mr-2"></i>เลือกไฟล์จากเครื่อง
                        </button>
                        <p class="text-sm text-gray-500 mt-4">รองรับไฟล์: JPG, PNG, GIF, WebP (ขนาดไม่เกิน 5MB)</p>
                    </div>
                    
                    <div id="previewContainer" class="preview-container">
                        <div class="relative">
                            <img id="previewImage" src="" alt="Preview" class="preview-image mx-auto mb-4">
                            <button type="button" onclick="removeImage()" 
                                    class="absolute top-4 right-4 w-10 h-10 bg-red-500 text-white rounded-full hover:bg-red-600 transition-all shadow-lg flex items-center justify-center">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="file-info mt-4">
                            <i class="fas fa-file-image"></i>
                            <span id="fileName"></span>
                            <span id="fileSize" class="text-xs opacity-80"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Section (Right) -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg p-6 sticky top-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-purple-600"></i>รายละเอียด
                </h3>
                
                <div class="space-y-4">
                    <!-- ชื่อรูปภาพ -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-heading text-purple-600 mr-1"></i>ชื่อรูปภาพ
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all">
                    </div>
                    
                    <!-- คำอธิบาย -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-align-left text-purple-600 mr-1"></i>คำอธิบาย
                        </label>
                        <textarea name="description" rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 resize-none transition-all"></textarea>
                    </div>
                    
                    <!-- หมวดหมู่ -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-folder text-purple-600 mr-1"></i>หมวดหมู่
                        </label>
                        <select name="category" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all bg-white">
                            <option value="">-- เลือกหมวดหมู่ --</option>
                            <?php foreach ($all_categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>">
                                    <?php echo htmlspecialchars($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-sm text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            หากต้องการเพิ่มหมวดหมู่ใหม่ กรุณาไปที่ <a href="../categories/" class="text-purple-600 hover:text-purple-800 underline">จัดการหมวดหมู่</a>
                        </p>
                    </div>
                    
                    <!-- แท็ก -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-tags text-purple-600 mr-1"></i>แท็ก
                        </label>
                        <input type="text" name="tags" placeholder="แยกด้วยจุลภาค"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all">
                        <p class="text-xs text-gray-500 mt-1">ตัวอย่าง: ธรรมชาติ, ภูเขา, ทิวทัศน์</p>
                    </div>
                    
                    <!-- ลำดับและสถานะ -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-sort text-purple-600 mr-1"></i>ลำดับ
                            </label>
                            <input type="number" name="sort_order" value="0" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all">
                        </div>
                        
                        <div class="flex items-end">
                            <label class="toggle-switch">
                                <input type="checkbox" name="is_active" checked>
                                <span class="toggle-slider"></span>
                            </label>
                            <span class="ml-2 text-sm font-semibold text-gray-700">แสดงผล</span>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Buttons -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex gap-3">
                        <a href="index.php" 
                           class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all text-center font-semibold">
                            <i class="fas fa-times mr-2"></i>ยกเลิก
                        </a>
                        <button type="submit" 
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-xl transition-all font-semibold transform hover:scale-105">
                            <i class="fas fa-save mr-2"></i>บันทึก
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
const uploadArea = document.getElementById('uploadArea');
const imageInput = document.getElementById('imageInput');
const uploadPlaceholder = document.getElementById('uploadPlaceholder');
const previewContainer = document.getElementById('previewContainer');
const previewImage = document.getElementById('previewImage');
const fileName = document.getElementById('fileName');
const fileSize = document.getElementById('fileSize');

// Click to upload
uploadArea.addEventListener('click', (e) => {
    if (!e.target.closest('button[type="button"]') && !previewContainer.classList.contains('active')) {
        imageInput.click();
    }
});

// Drag and drop
uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('dragover');
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('dragover');
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        handleFile(files[0]);
    }
});

// File input change
imageInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        handleFile(e.target.files[0]);
    }
});

function handleFile(file) {
    // Validate file type
    if (!file.type.match('image.*')) {
        Swal.fire({
            icon: 'error',
            title: 'ไฟล์ไม่ถูกต้อง',
            text: 'กรุณาเลือกไฟล์รูปภาพเท่านั้น!'
        });
        return;
    }
    
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        Swal.fire({
            icon: 'error',
            title: 'ไฟล์ใหญ่เกินไป',
            text: 'ขนาดไฟล์เกิน 5MB กรุณาเลือกไฟล์ที่มีขนาดเล็กกว่า'
        });
        return;
    }
    
    // Create file list for input
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    imageInput.files = dataTransfer.files;
    
    // Show preview
    const reader = new FileReader();
    reader.onload = (e) => {
        previewImage.src = e.target.result;
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        
        uploadPlaceholder.style.display = 'none';
        previewContainer.classList.add('active');
    };
    reader.readAsDataURL(file);
}

function removeImage() {
    imageInput.value = '';
    previewImage.src = '';
    uploadPlaceholder.style.display = 'block';
    previewContainer.classList.remove('active');
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// Form validation
document.getElementById('uploadForm').addEventListener('submit', (e) => {
    if (!imageInput.files.length) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'กรุณาเลือกรูปภาพ',
            text: 'กรุณาเลือกรูปภาพที่ต้องการอัพโหลด!'
        });
        return false;
    }
});
</script>

<?php include '../includes/footer.php'; ?>
