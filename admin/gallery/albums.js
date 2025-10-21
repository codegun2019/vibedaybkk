// Album Management

// Add Album
document.getElementById('add-album-btn')?.addEventListener('click', () => {
    Swal.fire({
        title: '📁 เพิ่มอัลบั้มใหม่',
        html: `
            <div class="text-left space-y-3 px-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ชื่อออัลบั้ม <span class="text-red-500">*</span></label>
                    <input type="text" id="album-name" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" 
                           placeholder="เช่น งานแฟชั่น">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">คำอธิบาย</label>
                    <textarea id="album-description" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" 
                              rows="3" 
                              placeholder="คำอธิบายเกี่ยวกับอัลบั้ม..."></textarea>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-save mr-2"></i>บันทึก',
        cancelButtonText: '<i class="fas fa-times mr-2"></i>ยกเลิก',
        confirmButtonColor: '#2563EB',
        cancelButtonColor: '#6B7280',
        width: '500px',
        padding: '1.5rem',
        customClass: {
            popup: 'rounded-2xl',
            title: 'text-xl',
            htmlContainer: 'mt-0',
            confirmButton: 'px-6 py-2.5 rounded-lg font-semibold text-sm',
            cancelButton: 'px-6 py-2.5 rounded-lg font-semibold text-sm'
        },
        preConfirm: () => {
            const name = document.getElementById('album-name').value;
            const description = document.getElementById('album-description').value;
            
            if (!name) {
                Swal.showValidationMessage('กรุณากรอกชื่ออัลบั้ม');
                return false;
            }
            
            return { name, description };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'กำลังสร้างอัลบั้ม...',
                html: '<i class="fas fa-spinner fa-spin text-4xl text-blue-600"></i>',
                showConfirmButton: false,
                allowOutsideClick: false
            });
            
            // Create album
            const formData = new FormData();
            formData.append('name', result.value.name);
            formData.append('description', result.value.description);
            
            fetch('album-create.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '✅ สร้างอัลบั้มสำเร็จ!',
                        text: 'อัลบั้มใหม่ถูกสร้างแล้ว',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: data.message
                    });
                }
            });
        }
    });
});

// Edit Album
function editAlbum(albumId) {
    // Fetch album data
    fetch(`get-album.php?id=${albumId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const album = data.album;
                
                Swal.fire({
                    title: '✏️ แก้ไขอัลบั้ม',
                    html: `
                        <div class="text-left space-y-3 px-2">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">ชื่ออัลบั้ม <span class="text-red-500">*</span></label>
                                <input type="text" id="album-name" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" 
                                       value="${album.title || ''}">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">คำอธิบาย</label>
                                <textarea id="album-description" 
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" 
                                          rows="3">${album.description || ''}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">สถานะ</label>
                                <select id="album-status" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    <option value="active" ${album.is_active == 1 ? 'selected' : ''}>ใช้งาน</option>
                                    <option value="inactive" ${album.is_active == 0 ? 'selected' : ''}>ไม่ใช้งาน</option>
                                </select>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-save mr-2"></i>บันทึก',
                    cancelButtonText: '<i class="fas fa-times mr-2"></i>ยกเลิก',
                    confirmButtonColor: '#2563EB',
                    cancelButtonColor: '#6B7280',
                    width: '500px',
                    padding: '1.5rem',
                    customClass: {
                        popup: 'rounded-2xl',
                        title: 'text-xl',
                        htmlContainer: 'mt-0',
                        confirmButton: 'px-6 py-2.5 rounded-lg font-semibold text-sm',
                        cancelButton: 'px-6 py-2.5 rounded-lg font-semibold text-sm'
                    },
                    preConfirm: () => {
                        const name = document.getElementById('album-name').value;
                        const description = document.getElementById('album-description').value;
                        const status = document.getElementById('album-status').value;
                        
                        if (!name) {
                            Swal.showValidationMessage('กรุณากรอกชื่ออัลบั้ม');
                            return false;
                        }
                        
                        return { name, description, status };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Update album
                        const formData = new FormData();
                        formData.append('id', albumId);
                        formData.append('name', result.value.name);
                        formData.append('description', result.value.description);
                        formData.append('status', result.value.status);
                        
                        fetch('album-update.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '✅ บันทึกสำเร็จ!',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        });
                    }
                });
            }
        });
}

// Delete Album
function deleteAlbum(albumId, imageCount) {
    if (imageCount > 0) {
        Swal.fire({
            icon: 'warning',
            title: '⚠️ ไม่สามารถลบได้',
            html: `
                <div class="text-center py-4">
                    <p class="text-gray-700 mb-3">อัลบั้มนี้มีรูปภาพอยู่ ${imageCount} รูป</p>
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4 text-left">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            กรุณาลบหรือย้ายรูปภาพออกจากอัลบั้มก่อน
                        </p>
                    </div>
                </div>
            `,
            confirmButtonText: 'เข้าใจแล้ว',
            confirmButtonColor: '#DC2626',
            customClass: {
                popup: 'rounded-2xl'
            }
        });
        return;
    }
    
    Swal.fire({
        title: '🗑️ ยืนยันการลบ',
        html: `
            <div class="text-center py-4">
                <div class="inline-block p-4 bg-red-100 rounded-full mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-5xl"></i>
                </div>
                <p class="text-gray-700 mb-3">คุณแน่ใจหรือไม่ที่จะลบอัลบั้มนี้?</p>
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
            fetch(`album-delete.php?id=${albumId}`, { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '✅ ลบสำเร็จ!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }
                });
        }
    });
}



