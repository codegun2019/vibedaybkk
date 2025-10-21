// Gallery Upload with Drag & Drop

const dropzone = document.getElementById('dropzone');
const fileInput = document.getElementById('file-input');
const previewArea = document.getElementById('preview-area');
const previewGrid = document.getElementById('preview-grid');
const uploadForm = document.getElementById('upload-form');

let selectedFiles = [];

// Dropzone click
dropzone.addEventListener('click', () => {
    fileInput.click();
});

// Prevent default drag behaviors
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropzone.addEventListener(eventName, preventDefaults, false);
    document.body.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

// Highlight dropzone when dragging
['dragenter', 'dragover'].forEach(eventName => {
    dropzone.addEventListener(eventName, () => {
        dropzone.classList.add('border-purple-600', 'bg-purple-200');
    });
});

['dragleave', 'drop'].forEach(eventName => {
    dropzone.addEventListener(eventName, () => {
        dropzone.classList.remove('border-purple-600', 'bg-purple-200');
    });
});

// Handle dropped files
dropzone.addEventListener('drop', (e) => {
    const dt = e.dataTransfer;
    const files = dt.files;
    handleFiles(files);
});

// Handle selected files
fileInput.addEventListener('change', (e) => {
    handleFiles(e.target.files);
});

// Handle files
function handleFiles(files) {
    selectedFiles = Array.from(files).filter(file => file.type.startsWith('image/'));
    
    if (selectedFiles.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'ไม่พบไฟล์รูปภาพ',
            text: 'กรุณาเลือกไฟล์รูปภาพเท่านั้น',
            confirmButtonColor: '#DC2626'
        });
        return;
    }
    
    // Show preview
    previewArea.classList.remove('hidden');
    previewGrid.innerHTML = '';
    
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        
        reader.onload = (e) => {
            const div = document.createElement('div');
            div.className = 'relative group';
            div.innerHTML = `
                <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 border-2 border-purple-300">
                    <img src="${e.target.result}" alt="${file.name}" 
                         class="w-full h-full object-cover">
                </div>
                <button type="button" onclick="removeFile(${index})" 
                        class="absolute top-1 right-1 w-6 h-6 bg-red-600 hover:bg-red-700 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-times text-xs"></i>
                </button>
                <p class="text-xs text-gray-600 mt-1 truncate">${file.name}</p>
            `;
            previewGrid.appendChild(div);
        };
        
        reader.readAsDataURL(file);
    });
}

// Remove file from selection
function removeFile(index) {
    selectedFiles.splice(index, 1);
    
    if (selectedFiles.length === 0) {
        previewArea.classList.add('hidden');
        fileInput.value = '';
    } else {
        // Re-render preview
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
        handleFiles(selectedFiles);
    }
}

// Upload form submission
uploadForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    if (selectedFiles.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณาเลือกรูปภาพ',
            text: 'คุณยังไม่ได้เลือกรูปภาพที่จะอัพโหลด',
            confirmButtonColor: '#DC2626'
        });
        return;
    }
    
    const formData = new FormData(uploadForm);
    
    // Show upload progress
    let uploadProgress = 0;
    Swal.fire({
        title: 'กำลังอัพโหลด...',
        html: `
            <div class="py-4">
                <div class="mb-4">
                    <i class="fas fa-cloud-upload-alt text-5xl text-purple-600"></i>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4 mb-3">
                    <div id="upload-progress" class="bg-gradient-to-r from-purple-600 to-pink-600 h-4 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <p id="upload-text" class="text-sm text-gray-600">กำลังอัพโหลด <span id="upload-count">0</span>/${selectedFiles.length} รูป</p>
            </div>
        `,
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false
    });
    
    try {
        const xhr = new XMLHttpRequest();
        
        // Progress tracking
        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                document.getElementById('upload-progress').style.width = percentComplete + '%';
            }
        });
        
        // Response
        xhr.addEventListener('load', () => {
            const data = JSON.parse(xhr.responseText);
            
            if (data.success) {
                // Close modal and reset form immediately
                document.getElementById('upload-modal').classList.add('hidden');
                document.getElementById('upload-form').reset();
                previewArea.classList.add('hidden');
                previewGrid.innerHTML = '';
                selectedFiles = [];
                
                // Show success toast
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-2xl shadow-lg'
                    }
                });
                
                Toast.fire({
                    icon: 'success',
                    title: `อัพโหลดสำเร็จ! ${data.uploaded_count} รูปภาพใหม่`,
                    background: '#10B981',
                    color: 'white'
                });
                
                // Refresh gallery after a short delay
                setTimeout(() => {
                    if (typeof loadGalleryImages === 'function') {
                        loadGalleryImages();
                    } else {
                        location.reload();
                    }
                }, 1000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: data.message || 'ไม่สามารถอัพโหลดได้',
                    confirmButtonColor: '#DC2626'
                });
            }
        });
        
        xhr.addEventListener('error', () => {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                confirmButtonColor: '#DC2626'
            });
        });
        
        xhr.open('POST', 'upload-process.php');
        xhr.send(formData);
        
    } catch (error) {
        console.error('Upload error:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถอัพโหลดได้',
            confirmButtonColor: '#DC2626'
        });
    }
});

// Close modal - reset form
document.getElementById('close-upload')?.addEventListener('click', () => {
    document.getElementById('upload-modal').classList.add('hidden');
    document.getElementById('upload-form').reset();
    previewArea.classList.add('hidden');
    previewGrid.innerHTML = '';
    selectedFiles = [];
});

document.getElementById('cancel-upload')?.addEventListener('click', () => {
    document.getElementById('upload-modal').classList.add('hidden');
    document.getElementById('upload-form').reset();
    previewArea.classList.add('hidden');
    previewGrid.innerHTML = '';
    selectedFiles = [];
});



