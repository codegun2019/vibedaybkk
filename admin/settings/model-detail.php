<?php
/**
 * การตั้งค่าหน้ารายละเอียดโมเดล
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
if (!in_array($user_role, ['admin', 'programmer'])) {
    set_message('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้ (ต้องเป็น Admin หรือ Programmer)');
    header('Location: ../dashboard.php');
    exit;
}

$page_title = 'การตั้งค่าหน้ารายละเอียดโมเดล';
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
                'model_detail_enabled' => isset($_POST['model_detail_enabled']) ? '1' : '0',
                'model_detail_show_price' => isset($_POST['model_detail_show_price']) ? '1' : '0',
                'model_detail_show_price_range' => isset($_POST['model_detail_show_price_range']) ? '1' : '0',
                'model_detail_show_personal_info' => isset($_POST['model_detail_show_personal_info']) ? '1' : '0',
                'model_detail_show_measurements' => isset($_POST['model_detail_show_measurements']) ? '1' : '0',
                'model_detail_show_experience' => isset($_POST['model_detail_show_experience']) ? '1' : '0',
                'model_detail_show_portfolio' => isset($_POST['model_detail_show_portfolio']) ? '1' : '0',
                'model_detail_show_contact' => isset($_POST['model_detail_show_contact']) ? '1' : '0',
            ];
            
            // บันทึกลง database
            foreach ($settings as $key => $value) {
                update_setting($conn, $key, $value, 'toggle', 'model_detail');
            }
            
            // Log activity
            log_activity($conn, $_SESSION['user_id'], 'update', 'settings', 0, 'Updated model detail settings');
            
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
        <i class="fas fa-eye me-2"></i><?php echo $page_title; ?>
        <span class="badge bg-danger ms-3">Admin/Programmer Only</span>
    </h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="../settings/">การตั้งค่า</a></li>
        <li class="breadcrumb-item active">หน้ารายละเอียดโมเดล</li>
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
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-shield-alt me-2"></i>
                    การตั้งค่าหน้ารายละเอียดโมเดล
                    <span class="badge bg-white text-danger ms-2">สิทธิ์สูง</span>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>คำเตือน:</strong> การตั้งค่านี้สามารถทำได้โดย <strong>Admin</strong> และ <strong>Programmer</strong> เท่านั้น
                    </div>
                    
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        
                        <!-- Enable/Disable Feature -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <i class="fas fa-power-off me-2"></i>เปิด/ปิดฟีเจอร์
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch form-check-lg">
                                    <input class="form-check-input" type="checkbox" id="model_detail_enabled" 
                                           name="model_detail_enabled" 
                                           <?php echo ($current_settings['model_detail_enabled'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="model_detail_enabled">
                                        <strong>เปิดใช้งานหน้ารายละเอียดโมเดล</strong>
                                    </label>
                                </div>
                                <small class="text-muted">
                                    ถ้าปิด: ผู้ใช้จะไม่สามารถเข้าดูรายละเอียดโมเดลได้ (จะถูก redirect กลับไปหน้ารายการโมเดล)
                                </small>
                            </div>
                        </div>
                        
                        <!-- What to Show -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <i class="fas fa-list-check me-2"></i>ข้อมูลที่แสดง
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- ราคา -->
                                    <div class="col-md-12 mb-4">
                                        <h6 class="text-success mb-3">
                                            <i class="fas fa-dollar-sign me-2"></i>ข้อมูลราคา
                                        </h6>
                                        
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="model_detail_show_price" 
                                                       name="model_detail_show_price"
                                                       <?php echo ($current_settings['model_detail_show_price'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="model_detail_show_price">
                                                    <strong>แสดงราคา</strong>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="model_detail_show_price_range" 
                                                       name="model_detail_show_price_range"
                                                       <?php echo ($current_settings['model_detail_show_price_range'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="model_detail_show_price_range">
                                                    แสดงช่วงราคา (price_min - price_max)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- ข้อมูลส่วนตัว -->
                                    <div class="col-md-12 mb-4">
                                        <h6 class="text-info mb-3">
                                            <i class="fas fa-user me-2"></i>ข้อมูลส่วนตัว
                                        </h6>
                                        
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="model_detail_show_personal_info" 
                                                       name="model_detail_show_personal_info"
                                                       <?php echo ($current_settings['model_detail_show_personal_info'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="model_detail_show_personal_info">
                                                    <strong>แสดงข้อมูลส่วนตัว</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">น้ำหนัก, ส่วนสูง, วันเกิด, อายุ, รอบอก/เอว/สะโพก</small>
                                        </div>
                                        
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="model_detail_show_measurements" 
                                                       name="model_detail_show_measurements"
                                                       <?php echo ($current_settings['model_detail_show_measurements'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="model_detail_show_measurements">
                                                    แสดงข้อมูลร่างกายเพิ่มเติม
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- ข้อมูลอื่นๆ -->
                                    <div class="col-md-12">
                                        <h6 class="text-warning mb-3">
                                            <i class="fas fa-briefcase me-2"></i>ข้อมูลเพิ่มเติม
                                        </h6>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="model_detail_show_experience" 
                                                           name="model_detail_show_experience"
                                                           <?php echo ($current_settings['model_detail_show_experience'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="model_detail_show_experience">
                                                        แสดงประสบการณ์
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="model_detail_show_portfolio" 
                                                           name="model_detail_show_portfolio"
                                                           <?php echo ($current_settings['model_detail_show_portfolio'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="model_detail_show_portfolio">
                                                        แสดงผลงาน (Portfolio)
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="model_detail_show_contact" 
                                                           name="model_detail_show_contact"
                                                           <?php echo ($current_settings['model_detail_show_contact'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="model_detail_show_contact">
                                                        แสดงปุ่มติดต่อ/จอง
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Save Button -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>บันทึกการตั้งค่า
                            </button>
                            <a href="../settings/" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>ยกเลิก
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview & Info -->
        <div class="col-xl-4">
            <!-- Current Status -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-info-circle me-2"></i>สถานะปัจจุบัน
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>หน้ารายละเอียด:</strong>
                        <?php if (($current_settings['model_detail_enabled'] ?? '1') == '1'): ?>
                            <span class="badge bg-success">เปิด</span>
                        <?php else: ?>
                            <span class="badge bg-danger">ปิด</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <strong>ข้อมูลที่แสดง:</strong>
                        <ul class="list-unstyled mt-2">
                            <li>
                                <?php echo ($current_settings['model_detail_show_price'] ?? '1') == '1' ? '✅' : '❌'; ?>
                                ราคา
                            </li>
                            <li>
                                <?php echo ($current_settings['model_detail_show_price_range'] ?? '1') == '1' ? '✅' : '❌'; ?>
                                ช่วงราคา
                            </li>
                            <li>
                                <?php echo ($current_settings['model_detail_show_personal_info'] ?? '1') == '1' ? '✅' : '❌'; ?>
                                ข้อมูลส่วนตัว (น้ำหนัก, ส่วนสูง, วันเกิด)
                            </li>
                            <li>
                                <?php echo ($current_settings['model_detail_show_measurements'] ?? '1') == '1' ? '✅' : '❌'; ?>
                                ข้อมูลร่างกายเพิ่มเติม
                            </li>
                            <li>
                                <?php echo ($current_settings['model_detail_show_experience'] ?? '1') == '1' ? '✅' : '❌'; ?>
                                ประสบการณ์
                            </li>
                            <li>
                                <?php echo ($current_settings['model_detail_show_portfolio'] ?? '1') == '1' ? '✅' : '❌'; ?>
                                ผลงาน
                            </li>
                            <li>
                                <?php echo ($current_settings['model_detail_show_contact'] ?? '1') == '1' ? '✅' : '❌'; ?>
                                ปุ่มติดต่อ/จอง
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Permission Info -->
            <div class="card mb-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-user-shield me-2"></i>ข้อมูลสิทธิ์
                </div>
                <div class="card-body">
                    <p><strong>Role ของคุณ:</strong> 
                        <span class="badge bg-<?php echo $user_role == 'programmer' ? 'purple' : 'danger'; ?>">
                            <?php echo strtoupper($user_role); ?>
                        </span>
                    </p>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        หน้านี้สามารถเข้าถึงได้โดย Admin และ Programmer เท่านั้น
                    </p>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-link me-2"></i>ลิงก์ที่เกี่ยวข้อง
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="../../models.php" class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="fas fa-users me-2"></i>ดูหน้ารายการโมเดล
                        </a>
                        <?php
                        // ดึงโมเดลตัวอย่าง
                        $sample = $conn->query("SELECT id, slug FROM models LIMIT 1")->fetch_assoc();
                        if ($sample):
                        ?>
                            <a href="../../model-detail.php?id=<?php echo $sample['id']; ?>" 
                               class="btn btn-outline-success btn-sm" target="_blank">
                                <i class="fas fa-eye me-2"></i>ดูหน้ารายละเอียดตัวอย่าง
                            </a>
                        <?php endif; ?>
                        <a href="../models/" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-edit me-2"></i>จัดการโมเดล
                        </a>
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
                    <h5 class="mb-3">การควบคุมการแสดงรายละเอียด:</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-success"><i class="fas fa-check-circle me-2"></i>เมื่อเปิด (ON)</h6>
                            <ul>
                                <li>ผู้ใช้สามารถเข้าดูรายละเอียดโมเดลได้</li>
                                <li>แสดงข้อมูลตามที่เลือก (ราคา, ประสบการณ์, ฯลฯ)</li>
                                <li>แสดงปุ่มติดต่อ/จอง (ถ้าเลือก)</li>
                                <li>แสดงโมเดลที่เกี่ยวข้อง</li>
                            </ul>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-danger"><i class="fas fa-times-circle me-2"></i>เมื่อปิด (OFF)</h6>
                            <ul>
                                <li>ผู้ใช้จะถูก redirect กลับไปหน้ารายการโมเดล</li>
                                <li>ไม่สามารถดูรายละเอียดได้</li>
                                <li>เหมาะกับช่วงปรับปรุงระบบ</li>
                            </ul>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="mb-3">ฟิลด์ที่สามารถควบคุมได้:</h5>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>ฟิลด์</th>
                                <th>คำอธิบาย</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><i class="fas fa-dollar-sign text-success me-2"></i>ราคา</td>
                                <td>แสดงค่าบริการและช่วงราคา</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-ruler text-info me-2"></i>ข้อมูลร่างกาย</td>
                                <td>ส่วนสูง น้ำหนัก รอบอก เอว สะโพก</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-briefcase text-warning me-2"></i>ประสบการณ์</td>
                                <td>ประวัติการทำงานและประสบการณ์</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-star text-primary me-2"></i>ผลงาน</td>
                                <td>Portfolio, ผลงานที่ผ่านมา</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-phone text-danger me-2"></i>ปุ่มติดต่อ</td>
                                <td>ปุ่มจองบริการและติดต่อ LINE</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

