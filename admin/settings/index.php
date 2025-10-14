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
        // Update each setting
        foreach ($_POST as $key => $value) {
            if ($key != 'csrf_token' && strpos($key, 'setting_') === 0) {
                $setting_key = str_replace('setting_', '', $key);
                $setting_value = clean_input($value);
                
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->execute([$setting_value, $setting_key]);
            }
        }
        
        log_activity($pdo, $_SESSION['user_id'], 'update_settings', 'settings', null);
        set_message('success', 'บันทึกการตั้งค่าสำเร็จ');
        redirect(ADMIN_URL . '/settings/');
    }
}

include '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-cog me-2"></i>ตั้งค่าระบบ</h2>
    </div>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <?php foreach ($errors as $error): ?>
    <div><?php echo $error; ?></div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
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

