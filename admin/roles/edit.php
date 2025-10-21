<?php
/**
 * VIBEDAYBKK Admin - Edit Role Permissions
 * แก้ไขสิทธิ์ของบทบาท
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Only programmer can access
if (!is_programmer()) {
    set_message('error', 'เฉพาะโปรแกรมเมอร์เท่านั้นที่เข้าถึงได้');
    redirect(ADMIN_URL . '/index.php');
}

$page_title = 'แก้ไขสิทธิ์';
$current_page = 'roles';

$role_key = $_GET['role'] ?? '';

if (empty($role_key)) {
    set_message('error', 'ไม่พบข้อมูลบทบาท');
    redirect(ADMIN_URL . '/roles/');
}

// Get role info
$stmt = $conn->prepare("SELECT * FROM roles WHERE role_key = ?");
$stmt->bind_param('s', $role_key);
$stmt->execute();
$result = $stmt->get_result();
$role = $result->fetch_assoc();
$stmt->close();

if (!$role) {
    set_message('error', 'ไม่พบข้อมูลบทบาท');
    redirect(ADMIN_URL . '/roles/');
}

// Get current permissions
$permissions = [];
$result = $conn->query("SELECT * FROM permissions WHERE role_key = '{$role_key}'");
while ($row = $result->fetch_assoc()) {
    $permissions[$row['feature']] = $row;
}

$success = false;
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $features = $_POST['features'] ?? [];
        
        foreach ($features as $feature => $perms) {
            $can_view = isset($perms['view']) ? 1 : 0;
            $can_create = isset($perms['create']) ? 1 : 0;
            $can_edit = isset($perms['edit']) ? 1 : 0;
            $can_delete = isset($perms['delete']) ? 1 : 0;
            $can_export = isset($perms['export']) ? 1 : 0;
            
            $stmt = $conn->prepare("
                INSERT INTO permissions (role_key, feature, can_view, can_create, can_edit, can_delete, can_export)
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                can_view = ?, can_create = ?, can_edit = ?, can_delete = ?, can_export = ?
            ");
            $stmt->bind_param('ssiiiiiiiiii', 
                $role_key, $feature, 
                $can_view, $can_create, $can_edit, $can_delete, $can_export,
                $can_view, $can_create, $can_edit, $can_delete, $can_export
            );
            $stmt->execute();
            $stmt->close();
        }
        
        log_activity($conn, $_SESSION['user_id'], 'update', 'permissions', 0, "Updated permissions for {$role_key}");
        
        $success = true;
    }
}

include '../includes/header.php';
?>

<?php if ($success): ?>
<script>
showSuccess('บันทึกสิทธิ์เรียบร้อยแล้ว');
</script>
<?php endif; ?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <div class="w-12 h-12 <?php echo $role['color']; ?> rounded-lg flex items-center justify-center mr-3">
                <i class="fas <?php echo $role['icon']; ?> text-white text-xl"></i>
            </div>
            แก้ไขสิทธิ์: <?php echo $role['display_name']; ?>
        </h2>
    </div>
    <a href="index.php" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
        <i class="fas fa-arrow-left mr-2"></i>กลับ
    </a>
</div>

<!-- Role Info Card -->
<div class="bg-gradient-to-r <?php echo $role['color']; ?> rounded-xl shadow-lg p-6 mb-6 text-white">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <div class="text-white/70 text-sm mb-1">บทบาท</div>
            <div class="font-bold text-lg"><?php echo $role['display_name']; ?></div>
        </div>
        <div>
            <div class="text-white/70 text-sm mb-1">Level</div>
            <div class="font-bold text-lg"><?php echo $role['level']; ?></div>
        </div>
        <div>
            <div class="text-white/70 text-sm mb-1">จำนวนผู้ใช้</div>
            <div class="font-bold text-lg"><?php echo $role_stats[$role_key] ?? 0; ?> คน</div>
        </div>
        <div>
            <div class="text-white/70 text-sm mb-1">ราคา</div>
            <div class="font-bold text-lg"><?php echo $role['price'] > 0 ? '฿' . number_format($role['price'], 2) : 'ฟรี'; ?></div>
        </div>
    </div>
</div>

<!-- Permissions Form -->
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-gray-800 to-gray-900 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-shield-alt mr-3"></i>กำหนดสิทธิ์การเข้าถึง
            </h5>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">ฟีเจอร์</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">
                                <i class="fas fa-eye mr-1"></i>ดู
                            </th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">
                                <i class="fas fa-plus mr-1"></i>สร้าง
                            </th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">
                                <i class="fas fa-edit mr-1"></i>แก้ไข
                            </th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">
                                <i class="fas fa-trash mr-1"></i>ลบ
                            </th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">
                                <i class="fas fa-download mr-1"></i>Export
                            </th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">
                                <i class="fas fa-bolt mr-1"></i>ตั้งค่าเร็ว
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $all_features = [
                            'models' => 'จัดการโมเดล',
                            'categories' => 'จัดการหมวดหมู่',
                            'articles' => 'จัดการบทความ',
                            'article_categories' => 'หมวดหมู่บทความ',
                            'bookings' => 'การจอง',
                            'contacts' => 'ข้อความติดต่อ',
                            'menus' => 'จัดการเมนู',
                            'users' => 'จัดการผู้ใช้',
                            'gallery' => 'แกลเลอรี่',
                            'settings' => 'ตั้งค่าระบบ',
                            'homepage' => 'จัดการหน้าแรก'
                        ];
                        
                        foreach ($all_features as $feature => $name):
                            $perm = $permissions[$feature] ?? null;
                        ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-4 px-4 font-medium text-gray-900"><?php echo $name; ?></td>
                            
                            <!-- View Switch -->
                            <td class="py-4 px-4 text-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="features[<?php echo $feature; ?>][view]" value="1" 
                                           <?php echo ($perm && $perm['can_view']) ? 'checked' : ''; ?>
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                                </label>
                            </td>
                            
                            <!-- Create Switch -->
                            <td class="py-4 px-4 text-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="features[<?php echo $feature; ?>][create]" value="1" 
                                           <?php echo ($perm && $perm['can_create']) ? 'checked' : ''; ?>
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                                </label>
                            </td>
                            
                            <!-- Edit Switch -->
                            <td class="py-4 px-4 text-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="features[<?php echo $feature; ?>][edit]" value="1" 
                                           <?php echo ($perm && $perm['can_edit']) ? 'checked' : ''; ?>
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-500"></div>
                                </label>
                            </td>
                            
                            <!-- Delete Switch -->
                            <td class="py-4 px-4 text-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="features[<?php echo $feature; ?>][delete]" value="1" 
                                           <?php echo ($perm && $perm['can_delete']) ? 'checked' : ''; ?>
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500"></div>
                                </label>
                            </td>
                            
                            <!-- Export Switch -->
                            <td class="py-4 px-4 text-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="features[<?php echo $feature; ?>][export]" value="1" 
                                           <?php echo ($perm && $perm['can_export']) ? 'checked' : ''; ?>
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-500"></div>
                                </label>
                            </td>
                            
                            <!-- Quick Set -->
                            <td class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center space-x-1" data-feature="<?php echo $feature; ?>">
                                    <button type="button" 
                                            onclick="quickSet('<?php echo $feature; ?>', 'enable_all')"
                                            class="inline-flex items-center px-2 py-1 bg-green-100 hover:bg-green-200 text-green-700 rounded text-xs transition-colors duration-200"
                                            title="เปิดทั้งหมด">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                    <button type="button" 
                                            onclick="quickSet('<?php echo $feature; ?>', 'view_only')"
                                            class="inline-flex items-center px-2 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded text-xs transition-colors duration-200"
                                            title="ดูอย่างเดียว">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" 
                                            onclick="quickSet('<?php echo $feature; ?>', 'disable_all')"
                                            class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded text-xs transition-colors duration-200"
                                            title="ปิดทั้งหมด">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="flex justify-end space-x-4 mt-6">
        <a href="index.php" class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-times mr-2"></i>ยกเลิก
        </a>
        <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 font-medium">
            <i class="fas fa-save mr-2"></i>บันทึกการเปลี่ยนแปลง
        </button>
    </div>
</form>

<script>
const roleKey = '<?php echo $role_key; ?>';

// Auto-save และ visual feedback
document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', async function() {
        const row = this.closest('tr');
        const viewCheckbox = row.querySelector('input[name*="[view]"]');
        
        // Parse checkbox name to get feature and permission type
        const name = this.getAttribute('name');
        const matches = name.match(/features\[(\w+)\]\[(\w+)\]/);
        if (!matches) return;
        
        const feature = matches[1];
        const permissionType = matches[2];
        const isEnabled = this.checked;
        const originalState = !isEnabled;
        
        // Visual feedback
        const label = this.parentElement;
        label.classList.add('scale-110');
        setTimeout(() => label.classList.remove('scale-110'), 200);
        
        try {
            // AJAX save
            const response = await fetch('update-permission.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    role_key: roleKey,
                    feature: feature,
                    permission_type: permissionType,
                    enabled: isEnabled,
                    csrf_token: '<?php echo generate_csrf_token(); ?>'
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Show success toast
                showSuccess(result.message);
                
                // If view was auto-enabled
                if (result.auto_enabled_view && this !== viewCheckbox) {
                    viewCheckbox.checked = true;
                    viewCheckbox.parentElement.classList.add('animate-pulse');
                    setTimeout(() => {
                        viewCheckbox.parentElement.classList.remove('animate-pulse');
                    }, 1000);
                }
            } else {
                // Revert on error
                this.checked = originalState;
                showError(result.message || 'ไม่สามารถบันทึกได้');
            }
        } catch (error) {
            // Revert on error
            this.checked = originalState;
            console.error('Save error:', error);
            showError('เกิดข้อผิดพลาดในการบันทึก');
        }
    });
    
    // Hover effect
    checkbox.parentElement.addEventListener('mouseenter', function() {
        this.classList.add('scale-105');
    });
    
    checkbox.parentElement.addEventListener('mouseleave', function() {
        this.classList.remove('scale-105');
    });
});

// Add transition classes
document.querySelectorAll('label').forEach(label => {
    label.classList.add('transition-transform', 'duration-200');
});

// Quick Set Function
async function quickSet(feature, action) {
    const csrfToken = '<?php echo generate_csrf_token(); ?>';
    
    try {
        const response = await fetch('quick-set.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                role_key: roleKey,
                feature: feature,
                action: action,
                csrf_token: csrfToken
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Update all checkboxes for this feature
            const row = document.querySelector(`[data-feature="${feature}"]`).closest('tr');
            const viewCheckbox = row.querySelector('input[name*="[view]"]');
            const createCheckbox = row.querySelector('input[name*="[create]"]');
            const editCheckbox = row.querySelector('input[name*="[edit]"]');
            const deleteCheckbox = row.querySelector('input[name*="[delete]"]');
            const exportCheckbox = row.querySelector('input[name*="[export]"]');
            
            viewCheckbox.checked = result.permissions.can_view;
            createCheckbox.checked = result.permissions.can_create;
            editCheckbox.checked = result.permissions.can_edit;
            deleteCheckbox.checked = result.permissions.can_delete;
            exportCheckbox.checked = result.permissions.can_export;
            
            // Animate row
            row.classList.add('bg-green-50');
            setTimeout(() => {
                row.classList.remove('bg-green-50');
            }, 500);
            
            showSuccess(result.message);
        } else {
            showError(result.message || 'ไม่สามารถตั้งค่าได้');
        }
    } catch (error) {
        console.error('Quick set error:', error);
        showError('เกิดข้อผิดพลาดในการตั้งค่า');
    }
}
</script>

<?php include '../includes/footer.php'; ?>



