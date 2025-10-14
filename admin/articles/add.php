<?php
/**
 * VIBEDAYBKK Admin - Add Article
 * เพิ่มบทความใหม่
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$page_title = 'เพิ่มบทความใหม่';
$current_page = 'articles';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $title = clean_input($_POST['title']);
        $slug = !empty($_POST['slug']) ? generate_slug($_POST['slug']) : generate_slug($title);
        
        // Handle image upload
        $featured_image = null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $upload_result = upload_image($_FILES['featured_image'], 'articles');
            if ($upload_result['success']) {
                $featured_image = $upload_result['path'];
            } else {
                $errors[] = $upload_result['message'];
            }
        }
        
        $data = [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => clean_input($_POST['excerpt']),
            'content' => $_POST['content'],
            'featured_image' => $featured_image,
            'category' => clean_input($_POST['category']),
            'author_id' => $_SESSION['user_id'],
            'read_time' => !empty($_POST['read_time']) ? (int)$_POST['read_time'] : 5,
            'status' => $_POST['status'],
            'published_at' => $_POST['status'] == 'published' ? date('Y-m-d H:i:s') : null
        ];
        
        if (empty($title)) {
            $errors[] = 'กรุณากรอกหัวข้อบทความ';
        }
        
        // Check duplicate slug
        $stmt = $pdo->prepare("SELECT id FROM articles WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetch()) {
            $errors[] = 'URL นี้มีอยู่แล้ว กรุณาเปลี่ยน';
        }
        
        if (empty($errors)) {
            if (db_insert($pdo, 'articles', $data)) {
                $article_id = $pdo->lastInsertId();
                log_activity($pdo, $_SESSION['user_id'], 'create', 'articles', $article_id, null, $data);
                set_message('success', 'เพิ่มบทความสำเร็จ');
                redirect(ADMIN_URL . '/articles/edit.php?id=' . $article_id);
            } else {
                $errors[] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-plus-circle me-2"></i>เพิ่มบทความใหม่</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>กลับ
        </a>
    </div>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <h5><i class="fas fa-exclamation-circle me-2"></i>พบข้อผิดพลาด:</h5>
    <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Content -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>เนื้อหาบทความ</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">หัวข้อบทความ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">URL (Slug)</label>
                        <input type="text" class="form-control" name="slug" placeholder="auto-generate จากหัวข้อ">
                        <small class="text-muted">ถ้าไม่กรอก จะสร้างอัตโนมัติจากหัวข้อ</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">คำอธิบายสั้น (Excerpt)</label>
                        <textarea class="form-control" name="excerpt" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">เนื้อหาบทความ <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="content" rows="15" required></textarea>
                        <small class="text-muted">รองรับ HTML tags</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Publish -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-paper-plane me-2"></i>เผยแพร่</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">สถานะ</label>
                        <select class="form-select" name="status">
                            <option value="draft">แบบร่าง</option>
                            <option value="published">เผยแพร่ทันที</option>
                            <option value="archived">เก็บถาวร</option>
                        </select>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>บันทึก
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Featured Image -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-image me-2"></i>รูปภาพหลัก</h5>
                </div>
                <div class="card-body">
                    <input type="file" class="form-control" name="featured_image" accept="image/*">
                    <small class="text-muted">แนะนำขนาด 1200x630px</small>
                </div>
            </div>
            
            <!-- Meta -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tags me-2"></i>ข้อมูลเพิ่มเติม</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">หมวดหมู่</label>
                        <input type="text" class="form-control" name="category" placeholder="Photography">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">เวลาอ่าน (นาที)</label>
                        <input type="number" class="form-control" name="read_time" value="5">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php include '../includes/footer.php'; ?>

