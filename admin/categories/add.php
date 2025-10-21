<?php
/**
 * VIBEDAYBKK Admin - Add Category
 * เพิ่มหมวดหมู่ใหม่
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';


// Permission check
require_permission('categories', 'create');
$page_title = 'เพิ่มหมวดหมู่ใหม่';
$current_page = 'categories';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $data = [
            'code' => clean_input($_POST['code']),
            'name' => clean_input($_POST['name']),
            'name_en' => clean_input($_POST['name_en']),
            'description' => $_POST['description'],
            'icon' => clean_input($_POST['icon']),
            'color' => clean_input($_POST['color']),
            'gender' => $_POST['gender'],
            'price_min' => !empty($_POST['price_min']) ? (float)$_POST['price_min'] : null,
            'price_max' => !empty($_POST['price_max']) ? (float)$_POST['price_max'] : null,
            'sort_order' => !empty($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0,
            'status' => $_POST['status']
        ];
        
        // Validate
        if (empty($data['code'])) $errors[] = 'กรุณากรอกรหัสหมวดหมู่';
        if (empty($data['name'])) $errors[] = 'กรุณากรอกชื่อหมวดหมู่';
        
        // Check duplicate code
        if (!empty($data['code'])) {
            $stmt = $conn->prepare("SELECT id FROM categories WHERE code = ?");
            $stmt->bind_param('s', $data['code']);
            $stmt->execute();
            if ($stmt->fetch()) {
                $errors[] = 'รหัสหมวดหมู่นี้มีอยู่แล้ว';
            }
        }
        
        if (empty($errors)) {
            if (db_insert($conn, 'categories', $data)) {
                $category_id = $conn->insert_id;
                
                // Add requirements if provided
                if (!empty($_POST['requirements'])) {
                    $requirements = explode("\n", $_POST['requirements']);
                    $sort = 1;
                    foreach ($requirements as $req) {
                        $req = trim($req);
                        if (!empty($req)) {
                            $req_data = [
                                'category_id' => $category_id,
                                'requirement' => $req,
                                'sort_order' => $sort++
                            ];
                            db_insert($conn, 'model_requirements', $req_data);
                        }
                    }
                }
                
                log_activity($conn, $_SESSION['user_id'], 'create', 'categories', $category_id, null, $data);
                
                set_message('success', 'เพิ่มหมวดหมู่สำเร็จ');
                redirect(ADMIN_URL . '/categories/');
            } else {
                $errors[] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-plus-circle mr-3 text-red-600"></i>เพิ่มหมวดหมู่ใหม่
        </h2>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="index.php" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>กลับ
        </a>
    </div>
</div>

<?php if (!empty($errors)): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
    <h5 class="font-bold mb-2 flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>พบข้อผิดพลาด:
    </h5>
    <ul class="list-disc list-inside">
        <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
    <!-- ข้อมูลหมวดหมู่ -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-info-circle mr-3"></i>ข้อมูลหมวดหมู่
            </h5>
        </div>
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        รหัสหมวดหมู่ <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="code" required placeholder="female-fashion" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    <p class="text-sm text-gray-500 mt-2">ใช้ตัวอักษรภาษาอังกฤษและ - เท่านั้น</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">เพศ</label>
                    <select name="gender" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <option value="all">ทั้งหมด</option>
                        <option value="female">หญิง</option>
                        <option value="male">ชาย</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        ชื่อหมวดหมู่ (ไทย) <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="name" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ชื่อหมวดหมู่ (อังกฤษ)</label>
                    <input type="text" name="name_en" placeholder="Fashion Models" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">คำอธิบาย</label>
                <textarea name="description" rows="3" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"></textarea>
            </div>
        </div>
    </div>
    
    <!-- การแสดงผล -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-palette mr-3"></i>การแสดงผล
            </h5>
        </div>
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ไอคอน Font Awesome</label>
                    <div class="flex items-center space-x-3">
                        <input type="text" name="icon" id="icon-input" placeholder="fa-female" value="fa-user" 
                               class="icon-picker-input flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                        <div id="icon-input_preview" class="w-16 h-16 flex items-center justify-center bg-gradient-to-br from-purple-100 to-pink-100 rounded-lg border-2 border-purple-300">
                            <i class="fas fa-user text-3xl text-purple-600"></i>
                        </div>
                        <button type="button" class="icon-picker-btn px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-200 font-medium shadow-lg">
                            <i class="fas fa-icons mr-2"></i>เลือกไอคอน
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        คลิก "เลือกไอคอน" หรือพิมพ์ชื่อไอคอนเอง เช่น: fa-female, fa-male, fa-camera
                    </p>
                    <div class="mt-3" style="display:none;">
                        <a href="https://fontawesome.com/icons" target="_blank" 
                           class="inline-flex items-center px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-800 rounded-lg transition-colors duration-200 text-sm font-medium">
                            <i class="fas fa-external-link-alt mr-2"></i>ดูไอคอน
                        </a>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">สี Gradient (Tailwind)</label>
                    <input type="text" name="color" placeholder="from-pink-500 to-red-primary" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                    <p class="text-sm text-gray-500 mt-2">เช่น: from-pink-500 to-red-primary</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ราคาและคุณสมบัติ -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-dollar-sign mr-3"></i>ราคาและคุณสมบัติ
            </h5>
        </div>
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ราคาขั้นต่ำ (บาท/วัน)</label>
                    <input type="number" name="price_min" step="100" placeholder="3000" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ราคาสูงสุด (บาท/วัน)</label>
                    <input type="number" name="price_max" step="100" placeholder="5000" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">คุณสมบัติที่ต้องการ (แยกแต่ละบรรทัด)</label>
                <textarea name="requirements" rows="5" placeholder="ส่วนสูง 165-175 cm&#10;รูปร่างสมส่วน&#10;มีประสบการณ์" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"></textarea>
                <p class="text-sm text-gray-500 mt-2">แยกแต่ละคุณสมบัติด้วยการขึ้นบรรทัดใหม่</p>
            </div>
        </div>
    </div>
    
    <!-- การตั้งค่า -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-orange-600 to-orange-700 px-6 py-4">
            <h5 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-cog mr-3"></i>การตั้งค่า
            </h5>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ลำดับการแสดง</label>
                    <input type="number" name="sort_order" value="0" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                    <p class="text-sm text-gray-500 mt-2">เลขน้อยแสดงก่อน</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">สถานะ</label>
                    <select name="status" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                        <option value="active">ใช้งาน</option>
                        <option value="inactive">ไม่ใช้งาน</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Buttons -->
    <div class="flex justify-end space-x-4">
        <a href="index.php" 
           class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
            <i class="fas fa-times mr-2"></i>ยกเลิก
        </a>
        <button type="submit" 
                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 font-medium">
            <i class="fas fa-save mr-2"></i>บันทึก
        </button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>
<script src="../includes/icon-picker.js"></script>



