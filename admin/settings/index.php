<?php
/**
 * VIBEDAYBKK Admin - Settings
 * ตั้งค่าระบบ
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_admin(); // Only admin can access

$page_title = 'ตั้งค่าระบบ';
$current_page = 'settings';

$errors = [];

// Get all settings
$settings = [];
$result = db_get_rows($conn, "SELECT * FROM settings ORDER BY setting_key ASC");
foreach ($result as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        // Handle logo image upload
        if (isset($_FILES['logo_image']) && $_FILES['logo_image']['error'] === UPLOAD_ERR_OK) {
            // Delete old logo
            if (!empty($settings['logo_image'])) {
                delete_image($settings['logo_image']);
            }
            
            $upload_result = upload_image($_FILES['logo_image'], 'general');
            if ($upload_result['success']) {
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'logo_image'");
                $stmt->execute([$upload_result['path']]);
            } else {
                $errors[] = 'Logo: ' . $upload_result['message'];
            }
        }
        
        // Handle favicon upload
        if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
            // Delete old favicon
            if (!empty($settings['favicon'])) {
                delete_image($settings['favicon']);
            }
            
            $upload_result = upload_image($_FILES['favicon'], 'general');
            if ($upload_result['success']) {
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'favicon'");
                $stmt->execute([$upload_result['path']]);
            } else {
                $errors[] = 'Favicon: ' . $upload_result['message'];
            }
        }
        
        // Update text settings
        foreach ($_POST as $key => $value) {
            if ($key != 'csrf_token' && strpos($key, 'setting_') === 0) {
                $setting_key = str_replace('setting_', '', $key);
                $setting_value = clean_input($value);
                
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->execute([$setting_value, $setting_key]);
            }
        }
        
        if (empty($errors)) {
            log_activity($pdo, $_SESSION['user_id'], 'update_settings', 'settings', null);
            set_message('success', 'บันทึกการตั้งค่าสำเร็จ');
            redirect(ADMIN_URL . '/settings/');
        }
    }
    
    // Refresh settings
    $settings = [];
    $result = db_get_rows($conn, "SELECT * FROM settings");
    foreach ($result as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

include '../includes/header.php';
?>

<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-900 flex items-center">
        <i class="fas fa-cog mr-3 text-red-600"></i>ตั้งค่าระบบ
    </h2>
    <p class="text-gray-600 mt-1">จัดการการตั้งค่าต่างๆ ของเว็บไซต์</p>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <?php foreach ($errors as $error): ?>
    <div><?php echo $error; ?></div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
    <!-- Logo & Branding -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-image me-2"></i>โลโก้และไอคอน</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-12">
                    <label class="form-label">ประเภทโลโก้</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="setting_logo_type" id="logo_text" value="text" <?php echo ($settings['logo_type'] ?? 'text') == 'text' ? 'checked' : ''; ?>>
                        <label class="btn btn-outline-primary" for="logo_text">
                            <i class="fas fa-font me-2"></i>ข้อความ
                        </label>
                        
                        <input type="radio" class="btn-check" name="setting_logo_type" id="logo_image" value="image" <?php echo ($settings['logo_type'] ?? 'text') == 'image' ? 'checked' : ''; ?>>
                        <label class="btn btn-outline-primary" for="logo_image">
                            <i class="fas fa-image me-2"></i>รูปภาพ
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">ข้อความโลโก้</label>
                    <input type="text" class="form-control form-control-lg" name="setting_logo_text" value="<?php echo $settings['logo_text'] ?? 'VIBEDAYBKK'; ?>">
                    <small class="text-muted">ใช้เมื่อเลือกประเภทโลโก้เป็น "ข้อความ"</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">รูปภาพโลโก้</label>
                    <?php if (!empty($settings['logo_image'])): ?>
                    <div class="mb-2">
                        <img src="<?php echo UPLOADS_URL . '/' . $settings['logo_image']; ?>" class="img-thumbnail" style="max-height: 80px;">
                        <div class="mt-2">
                            <small class="text-muted">รูปปัจจุบัน</small>
                        </div>
                    </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" name="logo_image" accept="image/*">
                    <small class="text-muted">แนะนำขนาด: 200x60px, PNG มีพื้นหลังโปร่งใส</small>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Favicon</label>
                    <?php if (!empty($settings['favicon'])): ?>
                    <div class="mb-2">
                        <img src="<?php echo UPLOADS_URL . '/' . $settings['favicon']; ?>" class="img-thumbnail" style="max-height: 32px;">
                        <small class="text-muted ms-2">Favicon ปัจจุบัน</small>
                    </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" name="favicon" accept="image/x-icon,image/png,image/svg+xml">
                    <small class="text-muted">แนะนำขนาด: 32x32px หรือ 64x64px (.ico, .png)</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Site Info -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-globe me-2"></i>ข้อมูลเว็บไซต์</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">ชื่อเว็บไซต์</label>
                <input type="text" class="form-control" name="setting_site_name" value="<?php echo $settings['site_name'] ?? ''; ?>">
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">อีเมล</label>
                    <input type="email" class="form-control" name="setting_site_email" value="<?php echo $settings['site_email'] ?? ''; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">เบอร์โทรศัพท์</label>
                    <input type="text" class="form-control" name="setting_site_phone" value="<?php echo $settings['site_phone'] ?? ''; ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">LINE ID</label>
                    <input type="text" class="form-control" name="setting_site_line" value="<?php echo $settings['site_line'] ?? ''; ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">ที่อยู่</label>
                <textarea class="form-control" name="setting_site_address" rows="2"><?php echo $settings['site_address'] ?? ''; ?></textarea>
            </div>
        </div>
    </div>
    
    <!-- System Settings -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-sliders-h me-2"></i>การตั้งค่าระบบ</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">จำนวนรายการต่อหน้า</label>
                    <input type="number" class="form-control" name="setting_items_per_page" value="<?php echo $settings['items_per_page'] ?? 12; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">จองล่วงหน้าอย่างน้อย (วัน)</label>
                    <input type="number" class="form-control" name="setting_booking_advance_days" value="<?php echo $settings['booking_advance_days'] ?? 3; ?>">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Social Media -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-share-alt me-2"></i>โซเชียลมีเดีย</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label"><i class="fab fa-facebook me-2"></i>Facebook</label>
                <input type="url" class="form-control" name="setting_facebook_url" value="<?php echo $settings['facebook_url'] ?? ''; ?>" placeholder="https://facebook.com/vibedaybkk">
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fab fa-instagram me-2"></i>Instagram</label>
                <input type="url" class="form-control" name="setting_instagram_url" value="<?php echo $settings['instagram_url'] ?? ''; ?>" placeholder="https://instagram.com/vibedaybkk">
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fab fa-twitter me-2"></i>Twitter / X</label>
                <input type="url" class="form-control" name="setting_twitter_url" value="<?php echo $settings['twitter_url'] ?? ''; ?>" placeholder="https://x.com/vibedaybkk">
            </div>
        </div>
    </div>
    
    <div class="text-end">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save me-2"></i>บันทึกการตั้งค่า
        </button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>

