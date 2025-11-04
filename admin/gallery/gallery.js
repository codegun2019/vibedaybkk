// Gallery View & Management

// Load gallery images function
function loadGalleryImages() {
    const galleryContainer = document.getElementById('gallery-container');
    if (!galleryContainer) return;
    
    fetch('index.php')
        .then(response => response.text())
        .then(html => {
            // Extract gallery images HTML from response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newGalleryContainer = doc.getElementById('gallery-container');
            
            if (newGalleryContainer) {
                galleryContainer.innerHTML = newGalleryContainer.innerHTML;
                
                // Update image count
                const imageCount = galleryContainer.querySelectorAll('.gallery-item').length;
                const countElement = document.querySelector('.image-count');
                if (countElement) {
                    countElement.textContent = imageCount;
                }
                
                // Reinitialize event listeners for new images
                initializeGalleryEvents();
            }
        })
        .catch(error => {
            console.error('Error loading gallery:', error);
            location.reload(); // Fallback to page reload
        });
}

// Initialize gallery event listeners
function initializeGalleryEvents() {
    // Lightbox functionality
    document.querySelectorAll('.gallery-item').forEach(item => {
        item.addEventListener('click', function() {
            const imageId = this.dataset.id;
            viewImage(imageId);
        });
    });
    
    // Delete functionality
    document.querySelectorAll('.delete-image').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const imageId = this.dataset.id;
            deleteImage(imageId);
        });
    });
    
    // Edit functionality
    document.querySelectorAll('.edit-image').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const imageId = this.dataset.id;
            editImage(imageId);
        });
    });
}

// View Image in Lightbox
function viewImage(imageId) {
    // Fetch image details
    fetch(`get-image.php?id=${imageId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const img = data.image;
                
                Swal.fire({
                    html: `
                        <div class="text-left">
                            <img src="${img.file_path}" alt="${img.title || 'Image'}" 
                                 class="w-full rounded-lg mb-4 max-h-[60vh] object-contain bg-gray-100">
                            
                            ${img.title ? `<h3 class="text-xl font-bold text-gray-900 mb-2">${img.title}</h3>` : ''}
                            ${img.description ? `<p class="text-gray-600 mb-4">${img.description}</p>` : ''}
                            
                            <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-gray-500 mb-1">อัลบั้ม</p>
                                    <p class="font-semibold text-gray-900">${img.album_name || 'ไม่ระบุ'}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-gray-500 mb-1">ขนาดไฟล์</p>
                                    <p class="font-semibold text-gray-900">${formatFileSize(img.file_size)}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-gray-500 mb-1">ขนาดภาพ</p>
                                    <p class="font-semibold text-gray-900">${img.width} × ${img.height} px</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-gray-500 mb-1">จำนวนดู</p>
                                    <p class="font-semibold text-gray-900">${img.view_count.toLocaleString()} ครั้ง</p>
                                </div>
                            </div>
                            
                            ${img.tags ? `
                                <div class="mb-4">
                                    <p class="text-sm text-gray-500 mb-2">แท็ก:</p>
                                    <div class="flex flex-wrap gap-2">
                                        ${img.tags.split(',').map(tag => 
                                            `<span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">${tag.trim()}</span>`
                                        ).join('')}
                                    </div>
                                </div>
                            ` : ''}
                            
                            <div class="grid grid-cols-2 gap-2">
                                <button onclick="Swal.close(); showCopyOptions('${img.image_path}', '${img.title}')" 
                                        class="px-4 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg font-semibold shadow-lg">
                                    <i class="fas fa-copy mr-2"></i>คัดลอกลิงก์
                                </button>
                                <button onclick="downloadImage('${img.image_path}', '${img.title || 'image'}')" 
                                        class="px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold shadow-lg">
                                    <i class="fas fa-download mr-2"></i>ดาวน์โหลด
                                </button>
                                ${typeof canEdit !== 'undefined' && canEdit ? `
                                <button onclick="Swal.close(); editImage(${img.id})" 
                                        class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold shadow-lg">
                                    <i class="fas fa-edit mr-2"></i>แก้ไข
                                </button>
                                ` : ''}
                                ${typeof canDelete !== 'undefined' && canDelete ? `
                                <button onclick="Swal.close(); deleteImage(${img.id})" 
                                        class="px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold shadow-lg">
                                    <i class="fas fa-trash mr-2"></i>ลบ
                                </button>
                                ` : ''}
                            </div>
                        </div>
                    `,
                    showConfirmButton: false,
                    showCloseButton: true,
                    width: '800px',
                    padding: '2rem',
                    customClass: {
                        popup: 'rounded-2xl',
                        closeButton: 'text-gray-400 hover:text-gray-600'
                    }
                });
                
                // Update view count
                fetch(`update-view.php?id=${imageId}`, { method: 'POST' });
            }
        });
}

// Edit Image
function editImage(imageId) {
    Swal.close();
    window.location.href = `edit.php?id=${imageId}`;
}

// Delete Image
function deleteImage(imageId) {
    Swal.fire({
        title: '🗑️ ยืนยันการลบ',
        html: `
            <div class="text-center py-4">
                <div class="inline-block p-4 bg-red-100 rounded-full mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-5xl"></i>
                </div>
                <p class="text-gray-700 mb-3">คุณแน่ใจหรือไม่ที่จะลบรูปภาพนี้?</p>
                <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-3 text-left">
                    <p class="text-sm text-red-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        การลบจะไม่สามารถย้อนกลับได้
                    </p>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-trash mr-2"></i>ลบเลย',
        cancelButtonText: '<i class="fas fa-times mr-2"></i>ยกเลิก',
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'px-6 py-3 rounded-xl font-semibold',
            cancelButton: 'px-6 py-3 rounded-xl font-semibold'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'กำลังลบ...',
                html: '<i class="fas fa-spinner fa-spin text-4xl text-red-600"></i>',
                showConfirmButton: false,
                allowOutsideClick: false
            });
            
            // Delete request
            fetch(`delete.php?id=${imageId}`, { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '✅ ลบสำเร็จ!',
                            text: 'รูปภาพถูกลบออกจากระบบแล้ว',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: data.message || 'ไม่สามารถลบรูปภาพได้'
                        });
                    }
                });
        }
    });
}

// Copy Image URL (Imgur-style)
function copyImageUrl(imageUrl, type = 'direct') {
    let textToCopy = imageUrl;
    
    // Ensure full URL
    if (!imageUrl.startsWith('http')) {
        textToCopy = window.location.origin + '/' + imageUrl.replace(/^\/+/, '');
    }
    
    navigator.clipboard.writeText(textToCopy).then(() => {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
        Toast.fire({
            icon: 'success',
            title: '📋 คัดลอกลิงก์แล้ว!'
        });
    }).catch(err => {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = textToCopy;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '📋 คัดลอกลิงก์แล้ว!',
            showConfirmButton: false,
            timer: 2000
        });
    });
}

// Show copy link options (Imgur-style)
function showCopyOptions(imageUrl, imageTitle) {
    const fullUrl = imageUrl.startsWith('http') ? imageUrl : window.location.origin + '/' + imageUrl.replace(/^\/+/, '');
    const fileName = imageUrl.split('/').pop();
    
    Swal.fire({
        title: '📋 คัดลอกลิงก์',
        html: `
            <div class="text-left space-y-3">
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-link mr-2"></i>Direct Link
                    </label>
                    <div class="flex space-x-2">
                        <input type="text" value="${fullUrl}" readonly 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-white text-sm font-mono"
                               onclick="this.select()">
                        <button onclick="copyToClipboard('${fullUrl.replace(/'/g, "\\'")}', 'Direct Link')" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fab fa-html5 mr-2"></i>HTML Code
                    </label>
                    <div class="flex space-x-2">
                        <input type="text" value='<img src="${fullUrl}" alt="${imageTitle || fileName}">' readonly 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-white text-sm font-mono"
                               onclick="this.select()">
                        <button onclick="copyToClipboard('<img src=\\'${fullUrl}\\' alt=\\'${imageTitle || fileName}\\'>', 'HTML')" 
                                class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-sm font-medium">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fab fa-markdown mr-2"></i>Markdown
                    </label>
                    <div class="flex space-x-2">
                        <input type="text" value="![${imageTitle || fileName}](${fullUrl})" readonly 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-white text-sm font-mono"
                               onclick="this.select()">
                        <button onclick="copyToClipboard('![${imageTitle || fileName}](${fullUrl})', 'Markdown')" 
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-brackets-curly mr-2"></i>BBCode
                    </label>
                    <div class="flex space-x-2">
                        <input type="text" value="[img]${fullUrl}[/img]" readonly 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-white text-sm font-mono"
                               onclick="this.select()">
                        <button onclick="copyToClipboard('[img]${fullUrl}[/img]', 'BBCode')" 
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
        `,
        showConfirmButton: false,
        showCloseButton: true,
        width: '700px',
        padding: '2rem',
        customClass: {
            popup: 'rounded-2xl',
            closeButton: 'text-gray-400 hover:text-gray-600'
        }
    });
}

// Copy to clipboard helper
function copyToClipboard(text, type) {
    navigator.clipboard.writeText(text).then(() => {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
        Toast.fire({
            icon: 'success',
            title: `📋 คัดลอก ${type} แล้ว!`
        });
    });
}

// Download Image
function downloadImage(filePath, fileName) {
    const link = document.createElement('a');
    link.href = filePath;
    link.download = fileName;
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

// Format file size
function formatFileSize(bytes) {
    if (!bytes) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// Open upload modal
document.getElementById('upload-btn')?.addEventListener('click', () => {
    document.getElementById('upload-modal').classList.remove('hidden');
});

document.getElementById('upload-btn-2')?.addEventListener('click', () => {
    document.getElementById('upload-modal').classList.remove('hidden');
});

// Close upload modal
document.getElementById('close-upload')?.addEventListener('click', () => {
    document.getElementById('upload-modal').classList.add('hidden');
});

document.getElementById('cancel-upload')?.addEventListener('click', () => {
    document.getElementById('upload-modal').classList.add('hidden');
});

// Initialize gallery events when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeGalleryEvents();
});



