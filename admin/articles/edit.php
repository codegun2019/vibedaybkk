<?php
/**
 * VIBEDAYBKK Admin - Edit Article
 * แก้ไขบทความ
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$page_title = 'แก้ไขบทความ';
$current_page = 'articles';

$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$article_id) {
    set_message('error', 'ไม่พบข้อมูลบทความ');
    redirect(ADMIN_URL . '/articles/');
}

$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch();

if (!$article) {
    set_message('error', 'ไม่พบข้อมูลบทความ');
    redirect(ADMIN_URL . '/articles/');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $title = clean_input($_POST['title']);
        $slug = !empty($_POST['slug']) ? generate_slug($_POST['slug']) : generate_slug($title);
        
        // Handle image upload
        $featured_image = $article['featured_image'];
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            // Delete old image
            if ($article['featured_image']) {
                delete_image($article['featured_image']);
            }
            
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
            'read_time' => !empty($_POST['read_time']) ? (int)$_POST['read_time'] : 5,
            'status' => $_POST['status'],
            'published_at' => $_POST['status'] == 'published' && !$article['published_at'] ? date('Y-m-d H:i:s') : $article['published_at']
        ];
        
        if (empty($title)) {
            $errors[] = 'กรุณากรอกหัวข้อบทความ';
        }
        
        // Check duplicate slug
        $stmt = $pdo->prepare("SELECT id FROM articles WHERE slug = ? AND id != ?");
        $stmt->execute([$slug, $article_id]);
        if ($stmt->fetch()) {
            $errors[] = 'URL นี้มีอยู่แล้ว';
        }
        
        if (empty($errors)) {
            if (db_update($pdo, 'articles', $data, 'id = :id', ['id' => $article_id])) {
                log_activity($pdo, $_SESSION['user_id'], 'update', 'articles', $article_id, $article, $data);
                set_message('success', 'อัพเดทบทความสำเร็จ');
                redirect(ADMIN_URL . '/articles/edit.php?id=' . $article_id);
            } else {
                $errors[] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
            }
        }
    }
    
    // Refresh data
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$article_id]);
    $article = $stmt->fetch();
}

include '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-edit me-2"></i>แก้ไขบทความ</h2>
        <small class="text-muted">ID: #<?php echo $article_id; ?></small>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>กลับ
        </a>
        <a href="delete.php?id=<?php echo $article_id; ?>" class="btn btn-danger btn-delete">
            <i class="fas fa-trash me-2"></i>ลบ
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
                        <input type="text" class="form-control form-control-lg" name="title" value="<?php echo $article['title']; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">URL (Slug)</label>
                        <input type="text" class="form-control" name="slug" value="<?php echo $article['slug']; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">คำอธิบายสั้น</label>
                        <textarea class="form-control" name="excerpt" rows="3"><?php echo $article['excerpt']; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">เนื้อหาบทความ</label>
                        <textarea class="form-control" name="content" rows="15" required><?php echo $article['content']; ?></textarea>
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
                            <option value="draft" <?php echo $article['status'] == 'draft' ? 'selected' : ''; ?>>แบบร่าง</option>
                            <option value="published" <?php echo $article['status'] == 'published' ? 'selected' : ''; ?>>เผยแพร่</option>
                            <option value="archived" <?php echo $article['status'] == 'archived' ? 'selected' : ''; ?>>เก็บถาวร</option>
                        </select>
                    </div>
                    
                    <?php if ($article['published_at']): ?>
                    <div class="mb-3">
                        <small class="text-muted">
                            เผยแพร่เมื่อ: <?php echo format_date_thai($article['published_at'], 'd/m/Y H:i'); ?>
                        </small>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>บันทึกการแก้ไข
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
                    <?php if ($article['featured_image']): ?>
                    <img src="<?php echo UPLOADS_URL . '/' . $article['featured_image']; ?>" 
                         class="img-fluid mb-3 rounded">
                    <?php endif; ?>
                    
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
                        <input type="text" class="form-control" name="category" value="<?php echo $article['category']; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">เวลาอ่าน (นาที)</label>
                        <input type="number" class="form-control" name="read_time" value="<?php echo $article['read_time']; ?>">
                    </div>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>สถิติ</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h3><?php echo $article['view_count']; ?></h3>
                        <p class="text-muted mb-0">จำนวนผู้เข้าชม</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php include '../includes/footer.php'; ?>

