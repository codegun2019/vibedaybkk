<?php
/**
 * การตั้งค่าการแสดงราคา
 * ต้องเป็น Admin หรือ Programmer เท่านั้น
 */

session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

// ตรวจสอบการ login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// ตรวจสอบ role - ต้องเป็น admin หรือ programmer เท่านั้น
$user_role = $_SESSION['role'] ?? 'viewer';
$user_level = 0;

// ดึงระดับสิทธิ์
$role_check = $conn->prepare("SELECT level FROM roles WHERE role_key = ?");
$role_check->bind_param('s', $user_role);
$role_check->execute();
$role_result = $role_check->get_result();
if ($role_row = $role_result->fetch_assoc()) {
    $user_level = (int)$role_row['level'];
}
$role_check->close();

// ต้องมีระดับ 80 ขึ้นไป (Admin = 80, Programmer = 100)
if ($user_level < 80) {
    set_message('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้ (ต้องเป็น Admin หรือ Programmer เท่านั้น)');
    header('Location: ../dashboard.php');
    exit;
}

$page_title = 'การตั้งค่าการแสดงราคา';
$success = false;
$errors = [];

// บันทึกการตั้งค่า
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = 'Invalid CSRF token';
    } else {
        try {
            // รับค่าจากฟอร์ม
            $settings = [
                // หน้าแรก
                'homepage_show_price' => isset($_POST['homepage_show_price']) ? '1' : '0',
                'homepage_price_format' => $_POST['homepage_price_format'] ?? 'full',
                
                // หน้ารายการโมเดล
                'models_list_show_price' => isset($_POST['models_list_show_price']) ? '1' : '0',
                'models_list_price_format' => $_POST['models_list_price_format'] ?? 'full',
                
                // หน้ารายละเอียดโมเดล
                'model_detail_show_price' => isset($_POST['model_detail_show_price']) ? '1' : '0',
                'model_detail_show_price_range' => isset($_POST['model_detail_show_price_range']) ? '1' : '0',
                'model_detail_price_format' => $_POST['model_detail_price_format'] ?? 'full',
                
                // ข้อความแทนราคา (เมื่อปิด)
                'price_hidden_text' => $_POST['price_hidden_text'] ?? 'ติดต่อสอบถาม',
            ];
            
            // บันทึกลง database
            foreach ($settings as $key => $value) {
                update_setting($conn, $key, $value, 'text', 'price_display');
            }
            
            // Log activity
            log_activity($conn, $_SESSION['user_id'], 'update', 'settings', 0, 'Updated price display settings');
            
            $success = true;
            set_message('success', 'บันทึกการตั้งค่าสำเร็จ');
            
        } catch (Exception $e) {
            $errors[] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
        }
    }
}

// ดึงการตั้งค่าปัจจุบัน
$current_settings = get_all_settings($conn);

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

include '../includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">
        <i class="fas fa-dollar-sign me-2"></i><?php echo $page_title; ?>
        <span class="badge bg-danger ms-3">Admin/Programmer Only (Level 80+)</span>
    </h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="dashboard.php">การตั้งค่า</a></li>
        <li class="breadcrumb-item active">การแสดงราคา</li>
    </ol>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>บันทึกการตั้งค่าสำเร็จ
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?php foreach ($errors as $error): ?>
                <div><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-xl-8">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <!-- หน้าแรก -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-home me-2"></i>หน้าแรก (Homepage)
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch form-check-lg mb-4">
                            <input class="form-check-input" type="checkbox" id="homepage_show_price" 
                                   name="homepage_show_price"
                                   <?php echo ($current_settings['homepage_show_price'] ?? '1') == '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="homepage_show_price">
                                <strong>แสดงราคาในหน้าแรก</strong>
                            </label>
                        </div>
                        
                        <div class="mb-3">
                            <label for="homepage_price_format" class="form-label">รูปแบบการแสดงราคา</label>
                            <select class="form-select" id="homepage_price_format" name="homepage_price_format">
                                <option value="full" <?php echo ($current_settings['homepage_price_format'] ?? 'full') == 'full' ? 'selected' : ''; ?>>
                                    แสดงเต็ม (8,500 ฿)
                                </option>
                                <option value="starting" <?php echo ($current_settings['homepage_price_format'] ?? 'full') == 'starting' ? 'selected' : ''; ?>>
                                    เริ่มต้นที่ (เริ่มต้น 8,500 ฿)
                                </option>
                                <option value="range" <?php echo ($current_settings['homepage_price_format'] ?? 'full') == 'range' ? 'selected' : ''; ?>>
                                    ช่วงราคา (8,000-10,000 ฿)
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- หน้ารายการโมเดล -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-list me-2"></i>หน้ารายการโมเดล (models.php)
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch form-check-lg mb-4">
                            <input class="form-check-input" type="checkbox" id="models_list_show_price" 
                                   name="models_list_show_price"
                                   <?php echo ($current_settings['models_list_show_price'] ?? '1') == '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="models_list_show_price">
                                <strong>แสดงราคาในหน้ารายการโมเดล</strong>
                            </label>
                        </div>
                        
                        <div class="mb-3">
                            <label for="models_list_price_format" class="form-label">รูปแบบการแสดงราคา</label>
                            <select class="form-select" id="models_list_price_format" name="models_list_price_format">
                                <option value="full" <?php echo ($current_settings['models_list_price_format'] ?? 'full') == 'full' ? 'selected' : ''; ?>>
                                    แสดงเต็ม (8,500 ฿)
                                </option>
                                <option value="starting" <?php echo ($current_settings['models_list_price_format'] ?? 'full') == 'starting' ? 'selected' : ''; ?>>
                                    เริ่มต้นที่ (เริ่มต้น 8,500 ฿)
                                </option>
                                <option value="range" <?php echo ($current_settings['models_list_price_format'] ?? 'full') == 'range' ? 'selected' : ''; ?>>
                                    ช่วงราคา (8,000-10,000 ฿)
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- หน้ารายละเอียดโมเดล -->
                <div class="card mb-4">
                    <div class="card-header bg-danger text-white">
                        <i class="fas fa-eye me-2"></i>หน้ารายละเอียดโมเดล (model-detail.php)
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch form-check-lg mb-3">
                            <input class="form-check-input" type="checkbox" id="model_detail_show_price" 
                                   name="model_detail_show_price"
                                   <?php echo ($current_settings['model_detail_show_price'] ?? '1') == '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="model_detail_show_price">
                                <strong>แสดงราคาในหน้ารายละเอียด</strong>
                            </label>
                        </div>
                        
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="model_detail_show_price_range" 
                                   name="model_detail_show_price_range"
                                   <?php echo ($current_settings['model_detail_show_price_range'] ?? '1') == '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="model_detail_show_price_range">
                                แสดงช่วงราคา (ถ้ามี)
                            </label>
                        </div>
                        
                        <div class="mb-3">
                            <label for="model_detail_price_format" class="form-label">รูปแบบการแสดงราคา</label>
                            <select class="form-select" id="model_detail_price_format" name="model_detail_price_format">
                                <option value="full" <?php echo ($current_settings['model_detail_price_format'] ?? 'full') == 'full' ? 'selected' : ''; ?>>
                                    แสดงเต็ม (8,500 ฿)
                                </option>
                                <option value="starting" <?php echo ($current_settings['model_detail_price_format'] ?? 'full') == 'starting' ? 'selected' : ''; ?>>
                                    เริ่มต้นที่ (เริ่มต้น 8,500 ฿/วัน)
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- ข้อความแทนราคา -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-comment me-2"></i>ข้อความเมื่อปิดการแสดงราคา
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="price_hidden_text" class="form-label">ข้อความที่แสดงแทนราคา</label>
                            <input type="text" class="form-control" id="price_hidden_text" name="price_hidden_text" 
                                   value="<?php echo htmlspecialchars($current_settings['price_hidden_text'] ?? 'ติดต่อสอบถาม'); ?>"
                                   placeholder="ติดต่อสอบถาม">
                            <small class="text-muted">ข้อความที่จะแสดงแทนราคาเมื่อปิดการแสดง</small>
                        </div>
                    </div>
                </div>
                
                <!-- Save Button -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>บันทึกการตั้งค่า
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times me-2"></i>ยกเลิก
                    </a>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4">
            <!-- Role Info -->
            <div class="card mb-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-user-shield me-2"></i>ข้อมูลสิทธิ์
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Role ของคุณ:</strong>
                        <span class="badge bg-<?php echo $user_role == 'programmer' ? 'purple' : 'danger'; ?> ms-2">
                            <?php echo strtoupper($user_role); ?>
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Level:</strong>
                        <span class="badge bg-dark ms-2"><?php echo $user_level; ?></span>
                    </div>
                    <hr>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-shield-alt me-2"></i>
                        <strong>สิทธิ์ที่ต้องการ:</strong> Level 80+ (Admin/Programmer)
                    </p>
                </div>
            </div>
            
            <!-- Current Status -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-info-circle me-2"></i>สถานะปัจจุบัน
                </div>
                <div class="card-body">
                    <h6 class="mb-3">การแสดงราคา:</h6>
                    
                    <div class="mb-3">
                        <strong>📱 หน้าแรก:</strong>
                        <?php if (($current_settings['homepage_show_price'] ?? '1') == '1'): ?>
                            <span class="badge bg-success">แสดงราคา</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">ซ่อนราคา</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <strong>📋 หน้ารายการ:</strong>
                        <?php if (($current_settings['models_list_show_price'] ?? '1') == '1'): ?>
                            <span class="badge bg-success">แสดงราคา</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">ซ่อนราคา</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <strong>👁️ หน้ารายละเอียด:</strong>
                        <?php if (($current_settings['model_detail_show_price'] ?? '1') == '1'): ?>
                            <span class="badge bg-success">แสดงราคา</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">ซ่อนราคา</span>
                        <?php endif; ?>
                    </div>
                    
                    <hr>
                    
                    <div>
                        <strong>ข้อความแทน:</strong>
                        <p class="text-muted mb-0">
                            "<?php echo htmlspecialchars($current_settings['price_hidden_text'] ?? 'ติดต่อสอบถาม'); ?>"
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Preview -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-eye me-2"></i>ตัวอย่างการแสดง
                </div>
                <div class="card-body">
                    <h6 class="mb-3">เมื่อเปิดการแสดงราคา:</h6>
                    <div class="alert alert-light border">
                        <strong style="color: #DC2626; font-size: 1.5em;">8,500 ฿</strong>
                    </div>
                    
                    <h6 class="mb-3">เมื่อปิดการแสดงราคา:</h6>
                    <div class="alert alert-light border">
                        <strong style="color: #6c757d;">
                            <?php echo htmlspecialchars($current_settings['price_hidden_text'] ?? 'ติดต่อสอบถาม'); ?>
                        </strong>
                    </div>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-link me-2"></i>ลิงก์ทดสอบ
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="../../" class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="fas fa-home me-2"></i>ดูหน้าแรก
                        </a>
                        <a href="../../models.php" class="btn btn-outline-success btn-sm" target="_blank">
                            <i class="fas fa-users me-2"></i>ดูหน้ารายการโมเดล
                        </a>
                        <?php
                        $sample = $conn->query("SELECT id FROM models LIMIT 1")->fetch_assoc();
                        if ($sample):
                        ?>
                            <a href="../../model-detail.php?id=<?php echo $sample['id']; ?>" 
                               class="btn btn-outline-info btn-sm" target="_blank">
                                <i class="fas fa-eye me-2"></i>ดูหน้ารายละเอียด
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Usage Guide -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-book me-2"></i>คู่มือการใช้งาน
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-primary"><i class="fas fa-home me-2"></i>หน้าแรก</h6>
                            <ul class="small">
                                <li>แสดงราคาในการ์ดโมเดล</li>
                                <li>เลือกรูปแบบ: เต็ม/เริ่มต้นที่/ช่วงราคา</li>
                                <li>ถ้าปิด → แสดงข้อความแทน</li>
                            </ul>
                        </div>
                        
                        <div class="col-md-4">
                            <h6 class="text-success"><i class="fas fa-list me-2"></i>หน้ารายการ</h6>
                            <ul class="small">
                                <li>แสดงราคาในการ์ดทุกโมเดล</li>
                                <li>ควบคุมแยกจากหน้าแรก</li>
                                <li>เหมาะกับหน้า Browse ทั้งหมด</li>
                            </ul>
                        </div>
                        
                        <div class="col-md-4">
                            <h6 class="text-danger"><i class="fas fa-eye me-2"></i>หน้ารายละเอียด</h6>
                            <ul class="small">
                                <li>แสดงราคาแบบละเอียด</li>
                                <li>แสดงช่วงราคา (min-max)</li>
                                <li>การ์ดราคาพิเศษ</li>
                            </ul>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="mb-3">ตัวอย่างรูปแบบราคา:</h6>
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>รูปแบบ</th>
                                <th>ตัวอย่าง</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>full</strong></td>
                                <td><span class="text-danger">8,500 ฿</span></td>
                            </tr>
                            <tr>
                                <td><strong>starting</strong></td>
                                <td><span class="text-danger">เริ่มต้น 8,500 ฿</span></td>
                            </tr>
                            <tr>
                                <td><strong>range</strong></td>
                                <td><span class="text-danger">8,000-10,000 ฿</span></td>
                            </tr>
                            <tr>
                                <td><strong>ปิด</strong></td>
                                <td><span class="text-muted">ติดต่อสอบถาม</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

