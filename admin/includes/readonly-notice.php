<?php
/**
 * Readonly Notice Component
 * แสดงข้อความแจ้งเตือนสำหรับผู้ใช้ที่มีสิทธิ์ดูอย่างเดียว
 */

if (!defined('VIBEDAYBKK_ADMIN')) {
    die('Direct access not permitted');
}

function show_readonly_notice($feature_name = 'ฟีเจอร์นี้') {
    ?>
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 mb-6 rounded-r-lg shadow-sm">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-yellow-800 mb-2">
                    <i class="fas fa-lock mr-2"></i>คุณมีสิทธิ์ดูอย่างเดียว
                </h3>
                <p class="text-yellow-700 mb-3">
                    คุณสามารถดูข้อมูล<?php echo $feature_name; ?>ได้ แต่ไม่สามารถแก้ไข เพิ่ม หรือลบได้
                </p>
                <div class="flex items-center space-x-2 text-sm text-yellow-600">
                    <i class="fas fa-info-circle"></i>
                    <span>หากต้องการสิทธิ์เพิ่มเติม กรุณาติดต่อผู้ดูแลระบบ หรืออัพเกรดบทบาทของคุณ</span>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>


