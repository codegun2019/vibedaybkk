/**
 * Toast Notification Functions
 * Using SweetAlert2 for VIBEDAYBKK Admin
 */

// Toast Configuration
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    },
    customClass: {
        popup: 'rounded-xl shadow-2xl',
        title: 'text-base font-semibold'
    }
});

/**
 * Show Success Toast
 * @param {string} message - ข้อความที่จะแสดง
 * @param {string} title - หัวข้อ (optional)
 */
function showSuccess(message, title = 'สำเร็จ!') {
    Toast.fire({
        icon: 'success',
        title: title,
        text: message
    });
}

/**
 * Show Error Toast
 * @param {string} message - ข้อความที่จะแสดง
 * @param {string} title - หัวข้อ (optional)
 */
function showError(message, title = 'เกิดข้อผิดพลาด!') {
    Toast.fire({
        icon: 'error',
        title: title,
        text: message
    });
}

/**
 * Show Warning Toast
 * @param {string} message - ข้อความที่จะแสดง
 * @param {string} title - หัวข้อ (optional)
 */
function showWarning(message, title = 'คำเตือน!') {
    Toast.fire({
        icon: 'warning',
        title: title,
        text: message
    });
}

/**
 * Show Info Toast
 * @param {string} message - ข้อความที่จะแสดง
 * @param {string} title - หัวข้อ (optional)
 */
function showInfo(message, title = 'แจ้งเตือน') {
    Toast.fire({
        icon: 'info',
        title: title,
        text: message
    });
}

/**
 * Confirmation Dialog for Delete Actions
 * @param {string} message - ข้อความยืนยัน
 * @returns {Promise}
 */
function confirmDelete(message = 'คุณแน่ใจหรือไม่ว่าต้องการลบรายการนี้?') {
    return Swal.fire({
        title: 'ยืนยันการลบ',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: '<i class="fas fa-trash mr-2"></i>ยืนยันลบ',
        cancelButtonText: '<i class="fas fa-times mr-2"></i>ยกเลิก',
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'px-6 py-3 font-semibold rounded-lg',
            cancelButton: 'px-6 py-3 font-semibold rounded-lg'
        },
        buttonsStyling: false
    });
}

/**
 * General Confirmation Dialog
 * @param {string} title - หัวข้อ
 * @param {string} message - ข้อความ
 * @param {string} confirmText - ข้อความปุ่มยืนยัน
 * @returns {Promise}
 */
function confirmAction(title, message, confirmText = 'ยืนยัน') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: confirmText,
        cancelButtonText: 'ยกเลิก',
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'px-6 py-3 font-semibold rounded-lg',
            cancelButton: 'px-6 py-3 font-semibold rounded-lg'
        },
        buttonsStyling: false
    });
}

// Auto-attach delete confirmation to .btn-delete elements
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.href || this.getAttribute('data-url');
            const message = this.getAttribute('data-message') || 'คุณแน่ใจหรือไม่? การกระทำนี้ไม่สามารถย้อนกลับได้';
            
            confirmDelete(message).then((result) => {
                if (result.isConfirmed) {
                    if (url) {
                        window.location.href = url;
                    } else if (this.tagName === 'FORM') {
                        this.submit();
                    }
                }
            });
        });
    });
});
