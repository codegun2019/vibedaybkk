<?php
/**
 * VIBEDAYBKK Admin - Menus List
 * รายการเมนูทั้งหมด
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check
require_permission('menus', 'view');
$can_create = has_permission('menus', 'create');
$can_edit = has_permission('menus', 'edit');
$can_delete = has_permission('menus', 'delete');

$page_title = 'จัดการเมนู';
$current_page = 'menus';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Count total
$total_menus = $conn->query("SELECT COUNT(*) as total FROM menus")->fetch_assoc()['total'];
$total_pages = ceil($total_menus / $per_page);

// Get menus with pagination
$sql = "SELECT m.*,
        (SELECT title FROM menus WHERE id = m.parent_id) as parent_title
        FROM menus m
        ORDER BY m.parent_id ASC, m.sort_order ASC
        LIMIT {$per_page} OFFSET {$offset}";
$menus = db_get_rows($conn, $sql);

include '../includes/header.php';
require_once '../includes/readonly-notice.php';
?>

<?php if (!$can_create && !$can_edit && !$can_delete): ?>
    <?php show_readonly_notice('เมนู'); ?>
<?php endif; ?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-bars mr-3 text-red-600"></i>จัดการเมนู
        </h2>
        <p class="text-gray-600 mt-1">จำนวนเมนูทั้งหมด: <?php echo count($menus); ?> รายการ</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="add.php" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-plus-circle mr-2"></i>เพิ่มเมนูใหม่
        </a>
    </div>
</div>

<!-- Menus Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="p-6">
        <?php if (empty($menus)): ?>
            <div class="text-center py-12">
                <i class="fas fa-bars text-6xl text-gray-400 mb-4"></i>
                <h5 class="text-gray-600 text-xl font-medium mb-4">ยังไม่มีเมนู</h5>
                <a href="add.php" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-plus-circle mr-2"></i>เพิ่มเมนูใหม่
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                <i class="fas fa-grip-vertical mr-2"></i>เรียง
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ไอคอน</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ชื่อเมนู</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">URL</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">เมนูหลัก</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Target</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ลำดับ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">สถานะ</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">การกระทำ</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-menu" class="divide-y divide-gray-200">
                        <?php foreach ($menus as $menu): ?>
                        <tr class="hover:bg-gray-50 cursor-move transition-all duration-200" data-id="<?php echo $menu['id']; ?>" data-parent="<?php echo $menu['parent_id'] ?: '0'; ?>">
                            <td class="px-4 py-3">
                                <i class="fas fa-grip-vertical text-gray-400 text-xl cursor-grab active:cursor-grabbing"></i>
                            </td>
                            <td class="px-4 py-3">
                                <?php if ($menu['icon']): ?>
                                <i class="fas <?php echo $menu['icon']; ?> text-lg text-gray-600"></i>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <?php if ($menu['parent_id']): ?>
                                        <span class="text-gray-400 mr-3">└─</span>
                                    <?php endif; ?>
                                    <span class="font-semibold text-gray-900"><?php echo $menu['title']; ?></span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <code class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm"><?php echo $menu['url']; ?></code>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo $menu['parent_title'] ?: '-'; ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <?php echo $menu['target']; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo $menu['sort_order']; ?></td>
                            <td class="px-4 py-3">
                                <?php 
                                $status_classes = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'inactive' => 'bg-gray-100 text-gray-800'
                                ];
                                $status_texts = [
                                    'active' => 'ใช้งาน',
                                    'inactive' => 'ไม่ใช้งาน'
                                ];
                                $status_class = $status_classes[$menu['status']] ?? 'bg-gray-100 text-gray-800';
                                $status_text = $status_texts[$menu['status']] ?? $menu['status'];
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="edit.php?id=<?php echo $menu['id']; ?>" 
                                       class="inline-flex items-center px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-lg transition-colors duration-200" 
                                       title="แก้ไข">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $menu['id']; ?>" 
                                       class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-800 rounded-lg transition-colors duration-200 btn-delete" 
                                       title="ลบ">
                                        <i class="fas fa-trash text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php 
            // Pagination
            if ($total_pages > 1):
                include '../includes/pagination.php';
                render_pagination($page, $total_pages);
            endif;
            ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<!-- SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortableElement = document.getElementById('sortable-menu');
    
    if (sortableElement) {
        const sortable = new Sortable(sortableElement, {
            animation: 350,
            easing: 'cubic-bezier(0.25, 0.8, 0.25, 1)',
            handle: '.fa-grip-vertical',
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            chosenClass: 'sortable-chosen',
            forceFallback: false,
            fallbackTolerance: 3,
            scrollSensitivity: 60,
            scrollSpeed: 15,
            bubbleScroll: true,
            
            onStart: function(evt) {
                document.body.style.overflow = 'hidden';
                evt.item.classList.add('dragging-item');
            },
            
            onEnd: function(evt) {
                document.body.style.overflow = '';
                evt.item.classList.remove('dragging-item');
                
                // Get new order - send only IDs in order
                const items = Array.from(sortableElement.querySelectorAll('tr[data-id]'));
                const newOrder = items.map(item => parseInt(item.dataset.id));
                
                console.log('Sending order:', newOrder); // Debug
                
                // Show loading
                Swal.fire({
                    title: 'กำลังบันทึก...',
                    html: '<div class="flex justify-center"><i class="fas fa-spinner fa-spin text-4xl text-blue-600"></i></div>',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
                
                // Send AJAX request
                fetch('update-order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        order: newOrder
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Success notification
                        Swal.fire({
                            icon: 'success',
                            title: '✅ บันทึกลำดับสำเร็จ!',
                            html: `
                                <div class="text-center py-3">
                                    <div class="mb-3">
                                        <i class="fas fa-check-circle text-green-600 text-5xl"></i>
                                    </div>
                                    <p class="text-gray-700 mb-3">ลำดับเมนูได้รับการอัพเดทแล้ว</p>
                                    <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-3 text-left">
                                        <div class="flex items-center text-sm text-green-800">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            <span>เมนูจะแสดงตามลำดับใหม่ในหน้าเว็บ</span>
                                        </div>
                                    </div>
                                </div>
                            `,
                            showConfirmButton: true,
                            confirmButtonText: '<i class="fas fa-check mr-2"></i>เข้าใจแล้ว',
                            confirmButtonColor: '#DC2626',
                            timer: 3000,
                            timerProgressBar: true,
                            showClass: {
                                popup: 'animate__animated animate__bounceIn animate__faster'
                            },
                            customClass: {
                                popup: 'rounded-2xl shadow-2xl',
                                title: 'text-2xl font-bold',
                                confirmButton: 'px-6 py-3 rounded-xl font-semibold'
                            }
                        }).then(() => {
                            // Reload page to show new order
                            location.reload();
                        });
                    } else {
                        // Error notification
                        Swal.fire({
                            icon: 'error',
                            title: '❌ เกิดข้อผิดพลาด',
                            html: `
                                <div class="text-center py-3">
                                    <div class="mb-3">
                                        <i class="fas fa-exclamation-circle text-red-600 text-5xl"></i>
                                    </div>
                                    <p class="text-gray-700 mb-3">${data.message || 'ไม่สามารถบันทึกลำดับได้'}</p>
                                    <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-3 text-left">
                                        <div class="flex items-center text-sm text-red-800">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            <span>กรุณาลองใหม่อีกครั้ง</span>
                                        </div>
                                    </div>
                                </div>
                            `,
                            showConfirmButton: true,
                            confirmButtonText: '<i class="fas fa-redo mr-2"></i>ลองใหม่',
                            confirmButtonColor: '#DC2626',
                            customClass: {
                                popup: 'rounded-2xl shadow-2xl',
                                title: 'text-2xl font-bold',
                                confirmButton: 'px-6 py-3 rounded-xl font-semibold'
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: '🔌 เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#DC2626'
                    });
                });
            }
        });
    }
});
</script>

<style>
/* Cursor styles */
.cursor-grab {
    cursor: grab;
}

.cursor-grab:active {
    cursor: grabbing;
}

/* Sortable states */
.sortable-ghost {
    opacity: 0.4;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    transform: scale(1.02);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
}

.sortable-drag {
    opacity: 0;
}

.sortable-chosen {
    background: #EBF5FF !important;
    box-shadow: 0 0 0 2px #3B82F6;
}

.dragging-item {
    opacity: 1 !important;
    background: white !important;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2) !important;
    transform: rotate(2deg) scale(1.05);
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    z-index: 9999 !important;
}

/* Row hover effect */
#sortable-menu tr[data-id] {
    transition: all 0.2s ease;
}

#sortable-menu tr[data-id]:hover {
    background: #F0F9FF;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
    transform: translateY(-1px);
}

/* Grip icon hover */
.fa-grip-vertical {
    transition: all 0.2s ease;
}

.fa-grip-vertical:hover {
    color: #3B82F6 !important;
    transform: scale(1.2);
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Hide scrollbar during drag */
body.dragging {
    overflow: hidden !important;
}

/* Table wrapper */
.overflow-x-auto {
    overflow-x: visible !important;
}

/* Animation for row movement */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

#sortable-menu tr[data-id] {
    animation: slideIn 0.3s ease;
}
</style>



