<?php
/**
 * การตั้งค่าโมเดลในหน้าแรก
 */

session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

// ตรวจสอบการ login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$page_title = 'การตั้งค่าโมเดลหน้าแรก';
$success = false;
$errors = [];

// บันทึกการตั้งค่า
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = 'Invalid CSRF token';
    } else {
        try {
            // รับค่าจากฟอร์ม
            $enabled = isset($_POST['homepage_models_enabled']) ? '1' : '0';
            $title = $_POST['homepage_models_title'] ?? 'โมเดลของเรา';
            $subtitle = $_POST['homepage_models_subtitle'] ?? 'โมเดลและนางแบบมืออาชีพ คัดสรรคุณภาพ';
            $limit = max(1, min(20, (int)($_POST['homepage_models_limit'] ?? 8)));
            $category = (int)($_POST['homepage_models_category'] ?? 0);
            $sort = $_POST['homepage_models_sort'] ?? 'latest';
            
            // อัพเดทหรือเพิ่มการตั้งค่า
            $settings = [
                'homepage_models_enabled' => $enabled,
                'homepage_models_title' => $title,
                'homepage_models_subtitle' => $subtitle,
                'homepage_models_limit' => $limit,
                'homepage_models_category' => $category,
                'homepage_models_sort' => $sort
            ];
            
            foreach ($settings as $key => $value) {
                $stmt = $conn->prepare("
                    INSERT INTO settings (setting_key, setting_value, setting_type, category) 
                    VALUES (?, ?, 'text', 'homepage') 
                    ON DUPLICATE KEY UPDATE setting_value = ?
                ");
                $stmt->bind_param('sss', $key, $value, $value);
                $stmt->execute();
                $stmt->close();
            }
            
            // Log activity
            log_activity($conn, $_SESSION['user_id'], 'update', 'settings', 0, 'Updated homepage models settings');
            
            $success = true;
            set_message('success', 'บันทึกการตั้งค่าสำเร็จ');
            
        } catch (Exception $e) {
            $errors[] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
        }
    }
}

// ดึงการตั้งค่าปัจจุบัน
$current_settings = get_all_settings($conn);

// ดึงหมวดหมู่ทั้งหมด
$categories = [];
$cat_result = $conn->query("SELECT id, name FROM categories ORDER BY sort_order ASC");
if ($cat_result) {
    while ($cat = $cat_result->fetch_assoc()) {
        $categories[] = $cat;
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

include '../includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?php echo $page_title; ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="../settings/">การตั้งค่า</a></li>
        <li class="breadcrumb-item active">โมเดลหน้าแรก</li>
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
                <div class="card-header">
                    <i class="fas fa-users me-2"></i>การตั้งค่าการแสดงโมเดล
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        
                        <!-- เปิด/ปิด Section -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="homepage_models_enabled" name="homepage_models_enabled" 
                                       <?php echo ($current_settings['homepage_models_enabled'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="homepage_models_enabled">
                                    <strong>แสดง Section โมเดลในหน้าแรก</strong>
                                </label>
                            </div>
                            <small class="text-muted">เปิด/ปิดการแสดง section โมเดลในหน้าแรก</small>
                        </div>

                        <hr class="my-4">

                        <!-- หัวข้อ Section -->
                        <div class="mb-3">
                            <label for="homepage_models_title" class="form-label">
                                <i class="fas fa-heading me-2"></i>หัวข้อ Section
                            </label>
                            <input type="text" class="form-control" id="homepage_models_title" name="homepage_models_title" 
                                   value="<?php echo htmlspecialchars($current_settings['homepage_models_title'] ?? 'โมเดลของเรา'); ?>" required>
                        </div>

                        <!-- คำอธิบาย -->
                        <div class="mb-3">
                            <label for="homepage_models_subtitle" class="form-label">
                                <i class="fas fa-align-left me-2"></i>คำอธิบาย
                            </label>
                            <input type="text" class="form-control" id="homepage_models_subtitle" name="homepage_models_subtitle" 
                                   value="<?php echo htmlspecialchars($current_settings['homepage_models_subtitle'] ?? 'โมเดลและนางแบบมืออาชีพ คัดสรรคุณภาพ'); ?>">
                        </div>

                        <div class="row">
                            <!-- จำนวนที่แสดง -->
                            <div class="col-md-6 mb-3">
                                <label for="homepage_models_limit" class="form-label">
                                    <i class="fas fa-list-ol me-2"></i>จำนวนที่แสดง
                                </label>
                                <input type="number" class="form-control" id="homepage_models_limit" name="homepage_models_limit" 
                                       value="<?php echo ($current_settings['homepage_models_limit'] ?? 8); ?>" 
                                       min="1" max="20" required>
                                <small class="text-muted">จำนวนโมเดลที่แสดงในหน้าแรก (1-20)</small>
                            </div>

                            <!-- หมวดหมู่ -->
                            <div class="col-md-6 mb-3">
                                <label for="homepage_models_category" class="form-label">
                                    <i class="fas fa-folder me-2"></i>หมวดหมู่
                                </label>
                                <select class="form-select" id="homepage_models_category" name="homepage_models_category">
                                    <option value="0" <?php echo ($current_settings['homepage_models_category'] ?? 0) == 0 ? 'selected' : ''; ?>>
                                        ทั้งหมด
                                    </option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" 
                                                <?php echo ($current_settings['homepage_models_category'] ?? 0) == $cat['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">เลือกหมวดหมู่ที่ต้องการแสดง (หรือทั้งหมด)</small>
                            </div>
                        </div>

                        <!-- การเรียงลำดับ -->
                        <div class="mb-4">
                            <label for="homepage_models_sort" class="form-label">
                                <i class="fas fa-sort me-2"></i>เรียงลำดับตาม
                            </label>
                            <select class="form-select" id="homepage_models_sort" name="homepage_models_sort">
                                <option value="latest" <?php echo ($current_settings['homepage_models_sort'] ?? 'latest') == 'latest' ? 'selected' : ''; ?>>
                                    ล่าสุด (Latest)
                                </option>
                                <option value="random" <?php echo ($current_settings['homepage_models_sort'] ?? 'latest') == 'random' ? 'selected' : ''; ?>>
                                    สุ่ม (Random)
                                </option>
                                <option value="popular" <?php echo ($current_settings['homepage_models_sort'] ?? 'latest') == 'popular' ? 'selected' : ''; ?>>
                                    ยอดนิยม (Most Viewed)
                                </option>
                                <option value="price_low" <?php echo ($current_settings['homepage_models_sort'] ?? 'latest') == 'price_low' ? 'selected' : ''; ?>>
                                    ราคาต่ำ → สูง
                                </option>
                                <option value="price_high" <?php echo ($current_settings['homepage_models_sort'] ?? 'latest') == 'price_high' ? 'selected' : ''; ?>>
                                    ราคาสูง → ต่ำ
                                </option>
                            </select>
                        </div>

                        <!-- ปุ่มบันทึก -->
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

        <!-- Preview -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-eye me-2"></i>ตัวอย่างการแสดงผล
                </div>
                <div class="card-body">
                    <?php
                    // ดึงโมเดลตัวอย่าง
                    $preview_limit = ($current_settings['homepage_models_limit'] ?? 8);
                    $preview_category = ($current_settings['homepage_models_category'] ?? 0);
                    $preview_sort = ($current_settings['homepage_models_sort'] ?? 'latest');
                    
                    $where = "WHERE m.status = 'available'";
                    if ($preview_category > 0) {
                        $where .= " AND m.category_id = " . (int)$preview_category;
                    }
                    
                    $order = "ORDER BY m.created_at DESC";
                    switch ($preview_sort) {
                        case 'random':
                            $order = "ORDER BY RAND()";
                            break;
                        case 'popular':
                            $order = "ORDER BY m.view_count DESC";
                            break;
                        case 'price_low':
                            $order = "ORDER BY m.price ASC";
                            break;
                        case 'price_high':
                            $order = "ORDER BY m.price DESC";
                            break;
                    }
                    
                    $preview_query = "
                        SELECT m.id, m.name, m.code, m.featured_image, c.name as category_name
                        FROM models m 
                        LEFT JOIN categories c ON m.category_id = c.id 
                        {$where}
                        {$order}
                        LIMIT {$preview_limit}
                    ";
                    $preview_result = $conn->query($preview_query);
                    
                    if ($preview_result && $preview_result->num_rows > 0):
                    ?>
                        <h6 class="mb-3">จะแสดง <?php echo $preview_result->num_rows; ?> โมเดล:</h6>
                        <div class="list-group">
                            <?php while ($model = $preview_result->fetch_assoc()): ?>
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($model['featured_image'])): ?>
                                            <img src="<?php echo htmlspecialchars($model['featured_image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($model['name']); ?>"
                                                 class="rounded me-3" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-secondary rounded me-3 d-flex align-items-center justify-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <strong><?php echo htmlspecialchars($model['name']); ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo $model['code'] ?? '-'; ?> | 
                                                <?php echo $model['category_name'] ?? '-'; ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            ไม่พบโมเดลที่ตรงกับเงื่อนไข
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-link me-2"></i>ลิงก์ที่เกี่ยวข้อง
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="../../" class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="fas fa-eye me-2"></i>ดูหน้าแรก
                        </a>
                        <a href="../models/" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-users me-2"></i>จัดการโมเดล
                        </a>
                        <a href="../categories/" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-folder me-2"></i>จัดการหมวดหมู่
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>




