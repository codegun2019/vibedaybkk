<?php
/**
 * Notification System สำหรับ VIBEDAYBKK Admin
 * ใช้ SweetAlert2 แสดง notification แบบสวยงาม
 */

// ตรวจสอบว่ามี notification ใน session หรือไม่
function check_and_show_notifications() {
    $notifications = [];
    
    // Success notification
    if (isset($_SESSION['success'])) {
        $notifications[] = [
            'type' => 'success',
            'message' => $_SESSION['success']
        ];
        unset($_SESSION['success']);
    }
    
    // Error notification
    if (isset($_SESSION['error'])) {
        $notifications[] = [
            'type' => 'error',
            'message' => $_SESSION['error']
        ];
        unset($_SESSION['error']);
    }
    
    // Warning notification
    if (isset($_SESSION['warning'])) {
        $notifications[] = [
            'type' => 'warning',
            'message' => $_SESSION['warning']
        ];
        unset($_SESSION['warning']);
    }
    
    // Info notification
    if (isset($_SESSION['info'])) {
        $notifications[] = [
            'type' => 'info',
            'message' => $_SESSION['info']
        ];
        unset($_SESSION['info']);
    }
    
    return $notifications;
}

// สร้าง JavaScript สำหรับแสดง notifications
function render_notifications_script() {
    $notifications = check_and_show_notifications();
    
    if (empty($notifications)) {
        return '';
    }
    
    $script = "<script>\n";
    $script .= "document.addEventListener('DOMContentLoaded', function() {\n";
    
    foreach ($notifications as $index => $notification) {
        $delay = $index * 500; // แสดงทีละตัว ห่างกัน 500ms
        
        $icon = $notification['type'];
        $message = addslashes($notification['message']);
        
        $title = [
            'success' => '✅ สำเร็จ!',
            'error' => '❌ ผิดพลาด!',
            'warning' => '⚠️ คำเตือน!',
            'info' => 'ℹ️ ข้อมูล'
        ][$notification['type']] ?? 'แจ้งเตือน';
        
        $script .= "    setTimeout(function() {\n";
        $script .= "        Swal.fire({\n";
        $script .= "            icon: '{$icon}',\n";
        $script .= "            title: '{$title}',\n";
        $script .= "            text: '{$message}',\n";
        $script .= "            toast: true,\n";
        $script .= "            position: 'top-end',\n";
        $script .= "            showConfirmButton: false,\n";
        $script .= "            timer: 4000,\n";
        $script .= "            timerProgressBar: true,\n";
        $script .= "            showClass: {\n";
        $script .= "                popup: 'animate__animated animate__fadeInRight animate__faster'\n";
        $script .= "            },\n";
        $script .= "            hideClass: {\n";
        $script .= "                popup: 'animate__animated animate__fadeOutRight animate__faster'\n";
        $script .= "            },\n";
        $script .= "            didOpen: (toast) => {\n";
        $script .= "                toast.addEventListener('mouseenter', Swal.stopTimer);\n";
        $script .= "                toast.addEventListener('mouseleave', Swal.resumeTimer);\n";
        $script .= "            }\n";
        $script .= "        });\n";
        $script .= "    }, {$delay});\n";
    }
    
    $script .= "});\n";
    $script .= "</script>\n";
    
    return $script;
}

// Helper functions สำหรับตั้งค่า notifications
function set_success_message($message) {
    $_SESSION['success'] = $message;
}

function set_error_message($message) {
    $_SESSION['error'] = $message;
}

function set_warning_message($message) {
    $_SESSION['warning'] = $message;
}

function set_info_message($message) {
    $_SESSION['info'] = $message;
}
?>



