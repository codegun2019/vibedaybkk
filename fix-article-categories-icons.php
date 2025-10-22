<?php
/**
 * แก้ไขไอคอนหมวดหมู่บทความ
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขไอคอนหมวดหมู่บทความ</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Thai', sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 15px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        h1 { color: #667eea; margin-bottom: 20px; }
        .category-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin: 20px 0; }
        .category-item { border: 2px solid #e0e0e0; border-radius: 10px; padding: 20px; text-align: center; }
        .category-item:hover { border-color: #667eea; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2); }
        .icon-preview { font-size: 48px; color: #667eea; margin-bottom: 15px; }
        .btn { background: #667eea; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; margin: 5px; }
        .btn:hover { background: #5558d9; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 แก้ไขไอคอนหมวดหมู่บทความ</h1>
        
        <p>เลือกไอคอนสำหรับแต่ละหมวดหมู่:</p>

        <form method="POST">
            <div class="category-grid">
                <?php
                $categories = db_get_rows($conn, "SELECT * FROM article_categories ORDER BY id ASC");
                $suggested_icons = [
                    1 => 'fas fa-newspaper',      // ข่าวสาร
                    2 => 'fas fa-shirt',          // แฟชั่น
                    3 => 'fas fa-heart',          // ไลฟ์สไตล์
                    4 => 'fas fa-spa',            // ความสวยความงาม
                    5 => 'fas fa-camera',         // การถ่ายภาพ
                    6 => 'fas fa-calendar',       // อีเวนต์
                    7 => 'fas fa-film',           // เบื้องหลัง
                    8 => 'fas fa-lightbulb'       // เคล็ดลับ
                ];
                
                foreach ($categories as $cat) {
                    $suggested_icon = $suggested_icons[$cat['id']] ?? 'fas fa-tag';
                    $current_icon = $cat['icon'] ?: $suggested_icon;
                ?>
                <div class="category-item">
                    <div class="icon-preview">
                        <i class="<?php echo htmlspecialchars($current_icon); ?>" id="preview-<?php echo $cat['id']; ?>"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                    <p>ID: <?php echo $cat['id']; ?></p>
                    
                    <select name="icons[<?php echo $cat['id']; ?>]" class="icon-select" data-category="<?php echo $cat['id']; ?>" style="width: 100%; padding: 8px; margin: 10px 0;">
                        <option value="fas fa-newspaper" <?php echo $current_icon == 'fas fa-newspaper' ? 'selected' : ''; ?>>📰 ข่าวสาร</option>
                        <option value="fas fa-shirt" <?php echo $current_icon == 'fas fa-shirt' ? 'selected' : ''; ?>>👕 แฟชั่น</option>
                        <option value="fas fa-heart" <?php echo $current_icon == 'fas fa-heart' ? 'selected' : ''; ?>>❤️ ไลฟ์สไตล์</option>
                        <option value="fas fa-spa" <?php echo $current_icon == 'fas fa-spa' ? 'selected' : ''; ?>>🧖 ความสวยความงาม</option>
                        <option value="fas fa-camera" <?php echo $current_icon == 'fas fa-camera' ? 'selected' : ''; ?>>📷 การถ่ายภาพ</option>
                        <option value="fas fa-calendar" <?php echo $current_icon == 'fas fa-calendar' ? 'selected' : ''; ?>>📅 อีเวนต์</option>
                        <option value="fas fa-film" <?php echo $current_icon == 'fas fa-film' ? 'selected' : ''; ?>>🎬 เบื้องหลัง</option>
                        <option value="fas fa-lightbulb" <?php echo $current_icon == 'fas fa-lightbulb' ? 'selected' : ''; ?>>💡 เคล็ดลับ</option>
                        <option value="fas fa-tag" <?php echo $current_icon == 'fas fa-tag' ? 'selected' : ''; ?>>🏷️ อื่นๆ</option>
                        <option value="fas fa-star" <?php echo $current_icon == 'fas fa-star' ? 'selected' : ''; ?>>⭐ ดาว</option>
                        <option value="fas fa-fire" <?php echo $current_icon == 'fas fa-fire' ? 'selected' : ''; ?>>🔥 ฮอต</option>
                    </select>
                </div>
                <?php } ?>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <button type="submit" name="action" value="update" class="btn btn-success">
                    <i class="fas fa-save"></i> บันทึกไอคอนทั้งหมด
                </button>
            </div>
        </form>

        <?php
        if ($_POST['action'] ?? '' === 'update') {
            $icons = $_POST['icons'] ?? [];
            $success_count = 0;
            
            foreach ($icons as $category_id => $icon) {
                $stmt = $conn->prepare("UPDATE article_categories SET icon = ? WHERE id = ?");
                $stmt->bind_param("si", $icon, $category_id);
                if ($stmt->execute()) {
                    $success_count++;
                }
                $stmt->close();
            }
            
            echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
            echo "<i class='fas fa-check-circle'></i> บันทึกไอคอนสำเร็จ! อัพเดท {$success_count} หมวดหมู่";
            echo "</div>";
            
            echo "<script>setTimeout(() => { window.location.reload(); }, 1500);</script>";
        }
        ?>

        <div style="text-align: center; margin-top: 30px;">
            <a href="articles.php" class="btn">
                <i class="fas fa-arrow-left"></i> กลับไปหน้าบทความ
            </a>
            <a href="debug-article-categories-icons.php" class="btn">
                <i class="fas fa-search"></i> ตรวจสอบไอคอน
            </a>
        </div>
    </div>

    <script>
        // Update icon preview when select changes
        document.querySelectorAll('.icon-select').forEach(select => {
            select.addEventListener('change', function() {
                const categoryId = this.dataset.category;
                const preview = document.getElementById('preview-' + categoryId);
                preview.className = this.value;
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>

