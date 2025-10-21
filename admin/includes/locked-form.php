<?php
/**
 * Locked Form Component
 * แสดงการล็อกฟอร์มสำหรับผู้ที่ไม่มีสิทธิ์แก้ไข
 */

if (!defined('VIBEDAYBKK_ADMIN')) {
    die('Direct access not permitted');
}

function show_locked_form_overlay($feature_name = 'ฟีเจอร์นี้') {
    ?>
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" id="lockedOverlay">
        <div class="bg-white rounded-2xl p-10 shadow-2xl max-w-md text-center transform animate-fade-in">
            <div class="mb-6">
                <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-lock text-4xl text-yellow-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">ฟอร์มถูกล็อก</h3>
            <p class="text-gray-600 mb-6">
                คุณไม่มีสิทธิ์แก้ไข<?php echo $feature_name; ?><br>
                สามารถดูข้อมูลได้อย่างเดียว
            </p>
            <button onclick="document.getElementById('lockedOverlay').remove()" 
                    class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors duration-200">
                <i class="fas fa-check mr-2"></i>เข้าใจแล้ว
            </button>
        </div>
    </div>
    
    <script>
    // Disable all form inputs
    document.querySelectorAll('input, textarea, select, button[type="submit"]').forEach(elem => {
        elem.disabled = true;
        elem.classList.add('opacity-60', 'cursor-not-allowed');
    });
    </script>
    <?php
}

function make_form_readonly() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Disable all form inputs except cancel/back buttons
        document.querySelectorAll('input, textarea, select').forEach(elem => {
            elem.disabled = true;
            elem.classList.add('bg-gray-100', 'cursor-not-allowed');
        });
        
        // Disable submit buttons
        document.querySelectorAll('button[type="submit"]').forEach(btn => {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            btn.innerHTML = '<i class="fas fa-lock mr-2"></i>ไม่มีสิทธิ์แก้ไข';
        });
    });
    </script>
    <?php
}

/**
 * Start locked form section
 * แสดงการแจ้งเตือนถ้าผู้ใช้ไม่มีสิทธิ์
 */
function start_locked_form($feature, $action = 'edit') {
    $has_permission = has_permission($feature, $action);
    
    if (!$has_permission) {
        ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-6 mb-6 shadow-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-lock text-3xl text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-2">
                        <i class="fas fa-exclamation-triangle mr-2"></i>ฟอร์มถูกล็อก
                    </h3>
                    <p class="text-yellow-800">
                        คุณไม่มีสิทธิ์แก้ไขข้อมูลในส่วนนี้ สามารถดูข้อมูลได้อย่างเดียว
                    </p>
                    <p class="text-sm text-yellow-700 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        หากต้องการแก้ไข กรุณาติดต่อผู้ดูแลระบบเพื่อขอสิทธิ์การแก้ไข
                    </p>
                </div>
            </div>
        </div>
        <?php
        make_form_readonly();
    }
}

/**
 * End locked form section
 * ไม่จำเป็นต้องทำอะไร แต่เก็บไว้เพื่อความสมบูรณ์
 */
function end_locked_form() {
    // Nothing to do, just for completeness
}
?>
