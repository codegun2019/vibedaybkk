<?php
/**
 * VIBEDAYBKK Admin - Gallery Upload
 * หน้าอัพโหลดรูปภาพแกลเลอรี่
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check
require_permission('gallery', 'create');
require_once '../includes/auth.php';

$page_title = 'อัพโหลดรูปภาพ';
$current_page = 'gallery';

// Get active albums for dropdown
$albums = db_get_rows($conn, "SELECT * FROM gallery_albums WHERE is_active = 1 ORDER BY sort_order ASC");

include '../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-cloud-upload-alt mr-3"></i>อัพโหลดรูปภาพ
            </h1>
            <p class="text-gray-600">อัพโหลดรูปภาพเข้าสู่แกลเลอรี่</p>
        </div>
        <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl">
            <i class="fas fa-arrow-left mr-2"></i>กลับ
        </a>
    </div>

    <!-- Upload Form -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Album Selection -->
        <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-6">
            <div class="max-w-md">
                <label class="block text-white text-sm font-semibold mb-2">
                    <i class="fas fa-folder mr-2"></i>เลือกอัลบั้ม
                </label>
                <select id="album_id" class="w-full px-4 py-3 rounded-lg border-2 border-white/30 bg-white/90 backdrop-blur-sm focus:border-white focus:ring-2 focus:ring-white/50 transition-all">
                    <option value="">-- ไม่ระบุอัลบั้ม --</option>
                    <?php foreach ($albums as $album): ?>
                        <option value="<?php echo $album['id']; ?>"><?php echo htmlspecialchars($album['title']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Upload Area -->
        <div class="p-8">
            <!-- Drop Zone -->
            <div id="dropzone" class="border-4 border-dashed border-gray-300 rounded-2xl p-12 text-center hover:border-purple-500 hover:bg-purple-50 transition-all duration-300 cursor-pointer group">
                <div class="space-y-4">
                    <div class="text-6xl text-gray-400 group-hover:text-purple-500 transition-colors">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-gray-700 mb-2">ลากไฟล์มาวางที่นี่</p>
                        <p class="text-gray-500">หรือ</p>
                    </div>
                    <button type="button" onclick="document.getElementById('file-input').click()" class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white px-8 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-folder-open mr-2"></i>เลือกไฟล์
                    </button>
                    <input type="file" id="file-input" multiple accept="image/*" class="hidden">
                    <p class="text-sm text-gray-500 mt-4">รองรับ: JPG, PNG, GIF, WEBP (สูงสุด 10MB/ไฟล์)</p>
                </div>
            </div>

            <!-- Preview Area -->
            <div id="preview-area" class="mt-8 hidden">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-images mr-2"></i>รูปภาพที่เลือก (<span id="file-count">0</span>)
                    </h3>
                    <button type="button" onclick="clearFiles()" class="text-red-500 hover:text-red-700 font-semibold">
                        <i class="fas fa-times-circle mr-1"></i>ล้างทั้งหมด
                    </button>
                </div>
                <div id="preview-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4"></div>
            </div>

            <!-- Upload Progress -->
            <div id="upload-progress" class="mt-8 hidden">
                <div class="bg-gray-100 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-semibold text-gray-700">กำลังอัพโหลด...</span>
                        <span id="progress-text" class="text-sm font-semibold text-purple-600">0%</span>
                    </div>
                    <div class="w-full bg-gray-300 rounded-full h-3 overflow-hidden">
                        <div id="progress-bar" class="bg-gradient-to-r from-purple-500 to-pink-500 h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <p id="upload-status" class="text-sm text-gray-600 mt-2">เตรียมการอัพโหลด...</p>
                </div>
            </div>

            <!-- Upload Button -->
            <div class="mt-8 flex justify-end space-x-4">
                <button type="button" onclick="window.location.href='index.php'" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition-all duration-300">
                    <i class="fas fa-times mr-2"></i>ยกเลิก
                </button>
                <button type="button" id="upload-btn" onclick="uploadFiles()" disabled class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white px-8 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                    <i class="fas fa-cloud-upload-alt mr-2"></i>อัพโหลด <span id="upload-count"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedFiles = [];
const maxFileSize = 10 * 1024 * 1024; // 10MB
const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

// Drag & Drop Events
const dropzone = document.getElementById('dropzone');
const fileInput = document.getElementById('file-input');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropzone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropzone.addEventListener(eventName, () => {
        dropzone.classList.add('border-purple-500', 'bg-purple-50');
    });
});

['dragleave', 'drop'].forEach(eventName => {
    dropzone.addEventListener(eventName, () => {
        dropzone.classList.remove('border-purple-500', 'bg-purple-50');
    });
});

dropzone.addEventListener('drop', (e) => {
    const files = e.dataTransfer.files;
    handleFiles(files);
});

fileInput.addEventListener('change', (e) => {
    handleFiles(e.target.files);
});

function handleFiles(files) {
    const validFiles = [];
    const errors = [];

    Array.from(files).forEach(file => {
        // Check file type
        if (!allowedTypes.includes(file.type)) {
            errors.push(`${file.name}: ประเภทไฟล์ไม่รองรับ`);
            return;
        }

        // Check file size
        if (file.size > maxFileSize) {
            errors.push(`${file.name}: ขนาดไฟล์เกิน 10MB`);
            return;
        }

        validFiles.push(file);
    });

    if (errors.length > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'ไฟล์บางไฟล์ไม่ถูกต้อง',
            html: errors.join('<br>'),
            confirmButtonColor: '#9333ea'
        });
    }

    if (validFiles.length > 0) {
        selectedFiles = [...selectedFiles, ...validFiles];
        updatePreview();
    }
}

function updatePreview() {
    const previewArea = document.getElementById('preview-area');
    const previewGrid = document.getElementById('preview-grid');
    const fileCount = document.getElementById('file-count');
    const uploadBtn = document.getElementById('upload-btn');
    const uploadCount = document.getElementById('upload-count');

    if (selectedFiles.length === 0) {
        previewArea.classList.add('hidden');
        uploadBtn.disabled = true;
        return;
    }

    previewArea.classList.remove('hidden');
    uploadBtn.disabled = false;
    fileCount.textContent = selectedFiles.length;
    uploadCount.textContent = `(${selectedFiles.length} ไฟล์)`;

    previewGrid.innerHTML = '';
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const div = document.createElement('div');
            div.className = 'relative group';
            div.innerHTML = `
                <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 shadow-md">
                    <img src="${e.target.result}" class="w-full h-full object-cover">
                </div>
                <button type="button" onclick="removeFile(${index})" class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 shadow-lg">
                    <i class="fas fa-times"></i>
                </button>
                <div class="absolute bottom-2 left-2 right-2 bg-black/70 backdrop-blur-sm text-white text-xs px-2 py-1 rounded truncate">
                    ${file.name}
                </div>
            `;
            previewGrid.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    updatePreview();
}

function clearFiles() {
    selectedFiles = [];
    updatePreview();
    fileInput.value = '';
}

async function uploadFiles() {
    if (selectedFiles.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณาเลือกไฟล์',
            text: 'กรุณาเลือกรูปภาพที่ต้องการอัพโหลด',
            confirmButtonColor: '#9333ea'
        });
        return;
    }

    const albumId = document.getElementById('album_id').value;
    const uploadBtn = document.getElementById('upload-btn');
    const progressSection = document.getElementById('upload-progress');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const uploadStatus = document.getElementById('upload-status');

    uploadBtn.disabled = true;
    progressSection.classList.remove('hidden');

    let uploaded = 0;
    const total = selectedFiles.length;
    const results = { success: [], failed: [] };

    for (let i = 0; i < selectedFiles.length; i++) {
        const file = selectedFiles[i];
        uploadStatus.textContent = `กำลังอัพโหลด ${file.name}...`;

        const formData = new FormData();
        formData.append('images[]', file);
        formData.append('album_id', albumId);
        formData.append('csrf_token', '<?php echo generate_csrf_token(); ?>');

        try {
            const response = await fetch('upload-process.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                results.success.push(file.name);
            } else {
                results.failed.push({ name: file.name, error: result.error || 'Unknown error' });
            }
        } catch (error) {
            results.failed.push({ name: file.name, error: error.message });
        }

        uploaded++;
        const progress = Math.round((uploaded / total) * 100);
        progressBar.style.width = progress + '%';
        progressText.textContent = progress + '%';
    }

    // Show results
    uploadStatus.textContent = 'เสร็จสิ้น!';

    let message = '';
    if (results.success.length > 0) {
        message += `<div class="text-green-600 mb-2"><i class="fas fa-check-circle mr-2"></i>อัพโหลดสำเร็จ ${results.success.length} ไฟล์</div>`;
    }
    if (results.failed.length > 0) {
        message += `<div class="text-red-600"><i class="fas fa-exclamation-circle mr-2"></i>อัพโหลดล้มเหลว ${results.failed.length} ไฟล์</div>`;
        message += '<ul class="text-sm text-left mt-2">';
        results.failed.forEach(f => {
            message += `<li>• ${f.name}: ${f.error}</li>`;
        });
        message += '</ul>';
    }

    Swal.fire({
        icon: results.failed.length === 0 ? 'success' : 'warning',
        title: 'เสร็จสิ้น',
        html: message,
        confirmButtonColor: '#9333ea',
        confirmButtonText: 'ดูแกลเลอรี่'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'index.php';
        }
    });

    // Reset
    clearFiles();
    progressSection.classList.add('hidden');
    progressBar.style.width = '0%';
    progressText.textContent = '0%';
    uploadBtn.disabled = false;
}
</script>

<?php include '../includes/footer.php'; ?>


