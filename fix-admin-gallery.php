<?php
/**
 * แก้ไขหลังบ้านให้ใช้ตาราง gallery
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขหลังบ้านแกลลอรี่</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Thai', sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 15px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        h1 { color: #667eea; margin-bottom: 20px; }
        .btn { background: #667eea; color: white; padding: 15px 30px; border: none; border-radius: 8px; cursor: pointer; margin: 10px; text-decoration: none; display: inline-block; }
        .btn:hover { background: #5558d9; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .result { padding: 15px; border-radius: 8px; margin: 15px 0; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 แก้ไขหลังบ้านแกลลอรี่</h1>
        
        <?php
        if ($_POST['action'] ?? '' === 'fix_admin_gallery') {
            try {
                echo "<div class='result info'>🔧 กำลังแก้ไขหลังบ้านแกลลอรี่...</div>";
                
                // ตรวจสอบว่ามีตาราง gallery หรือไม่
                $check_gallery = $conn->query("SHOW TABLES LIKE 'gallery'");
                if ($check_gallery->num_rows == 0) {
                    echo "<div class='result error'>❌ ไม่พบตาราง 'gallery' ในระบบ</div>";
                    exit;
                }
                
                // ตรวจสอบข้อมูลในตาราง gallery
                $gallery_count = $conn->query("SELECT COUNT(*) as total FROM gallery")->fetch_assoc()['total'];
                echo "<div class='result info'>📊 ข้อมูลในตาราง 'gallery': {$gallery_count} รายการ</div>";
                
                if ($gallery_count == 0) {
                    echo "<div class='result warning'>⚠️ ไม่มีข้อมูลในตาราง 'gallery' - จะเพิ่มข้อมูลตัวอย่าง</div>";
                    
                    // เพิ่มข้อมูลตัวอย่าง
                    $sample_images = [
                        [
                            'title' => 'รูปภาพตัวอย่าง 1',
                            'description' => 'คำอธิบายรูปภาพตัวอย่าง 1',
                            'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&h=600&fit=crop',
                            'category' => 'ธรรมชาติ',
                            'tags' => 'ธรรมชาติ, ภูเขา, ทิวทัศน์',
                            'sort_order' => 1,
                            'is_active' => 1
                        ],
                        [
                            'title' => 'รูปภาพตัวอย่าง 2',
                            'description' => 'คำอธิบายรูปภาพตัวอย่าง 2',
                            'image' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                            'category' => 'เมือง',
                            'tags' => 'เมือง, อาคาร, 夜景',
                            'sort_order' => 2,
                            'is_active' => 1
                        ],
                        [
                            'title' => 'รูปภาพตัวอย่าง 3',
                            'description' => 'คำอธิบายรูปภาพตัวอย่าง 3',
                            'image' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                            'category' => 'บุคคล',
                            'tags' => 'บุคคล, ภาพถ่าย, portrait',
                            'sort_order' => 3,
                            'is_active' => 1
                        ]
                    ];
                    
                    $insert_sql = "INSERT INTO gallery (title, description, image, category, tags, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($insert_sql);
                    
                    foreach ($sample_images as $image) {
                        $stmt->bind_param('sssssii', 
                            $image['title'], 
                            $image['description'], 
                            $image['image'], 
                            $image['category'], 
                            $image['tags'], 
                            $image['sort_order'], 
                            $image['is_active']
                        );
                        $stmt->execute();
                    }
                    
                    $stmt->close();
                    echo "<div class='result success'>✅ เพิ่มข้อมูลตัวอย่าง 3 รายการ</div>";
                }
                
                // สร้างไฟล์ admin/gallery/index.php ใหม่
                $admin_gallery_content = '<?php
/**
 * จัดการแกลลอรี่ - หลังบ้าน
 */

require_once "../../includes/config.php";
require_once "../../includes/functions.php";

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION["user_id"])) {
    header("Location: ../../login.php");
    exit;
}

// ตรวจสอบสิทธิ์
if ($_SESSION["role"] !== "admin") {
    header("Location: ../../index.php");
    exit;
}

$page_title = "จัดการแกลลอรี่";

// ดึงข้อมูลแกลลอรี่
$gallery_images = db_get_rows($conn, "SELECT * FROM gallery ORDER BY sort_order ASC, created_at DESC");

// ดึงหมวดหมู่
$categories = db_get_rows($conn, "SELECT DISTINCT category FROM gallery WHERE category IS NOT NULL AND category != \'\' ORDER BY category ASC");

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body { font-family: \'Noto Sans Thai\', sans-serif; }
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .gallery-item { position: relative; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .gallery-item img { width: 100%; height: 200px; object-fit: cover; }
        .gallery-overlay { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.8)); color: white; padding: 15px; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800"><?php echo $page_title; ?></h1>
            <a href="add.php" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                <i class="fas fa-plus mr-2"></i>เพิ่มรูปภาพ
            </a>
        </div>
        
        <!-- สถิติ -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-images text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">รูปภาพทั้งหมด</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($gallery_images); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-tags text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">หมวดหมู่</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($categories); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-eye text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">การดูทั้งหมด</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo array_sum(array_column($gallery_images, \'view_count\')); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- รายการรูปภาพ -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">รายการรูปภาพ</h2>
            </div>
            
            <?php if (!empty($gallery_images)): ?>
                <div class="gallery-grid p-6">
                    <?php foreach ($gallery_images as $image): ?>
                        <div class="gallery-item">
                            <img src="<?php echo $image[\'image\']; ?>" alt="<?php echo htmlspecialchars($image[\'title\']); ?>" loading="lazy">
                            <div class="gallery-overlay">
                                <h3 class="font-semibold mb-1"><?php echo htmlspecialchars($image[\'title\']); ?></h3>
                                <?php if (!empty($image[\'category\'])): ?>
                                    <span class="inline-block px-2 py-1 bg-blue-500 text-white text-xs rounded-full mb-2">
                                        <?php echo htmlspecialchars($image[\'category\']); ?>
                                    </span>
                                <?php endif; ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm">
                                        <i class="fas fa-eye mr-1"></i>
                                        <?php echo number_format($image[\'view_count\']); ?>
                                    </span>
                                    <div class="flex space-x-2">
                                        <a href="edit.php?id=<?php echo $image[\'id\']; ?>" class="text-yellow-400 hover:text-yellow-300">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete.php?id=<?php echo $image[\'id\']; ?>" class="text-red-400 hover:text-red-300" onclick="return confirm(\'คุณแน่ใจหรือไม่ที่จะลบรูปภาพนี้?\')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">ยังไม่มีรูปภาพ</h3>
                    <p class="text-gray-500 mb-6">เริ่มต้นด้วยการเพิ่มรูปภาพแรกของคุณ</p>
                    <a href="add.php" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition-colors">
                        <i class="fas fa-plus mr-2"></i>เพิ่มรูปภาพ
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>';

                // สร้างโฟลเดอร์ admin/gallery ถ้ายังไม่มี
                if (!file_exists('admin/gallery')) {
                    mkdir('admin/gallery', 0755, true);
                }
                
                // บันทึกไฟล์ admin/gallery/index.php
                file_put_contents('admin/gallery/index.php', $admin_gallery_content);
                echo "<div class='result success'>✅ สร้างไฟล์ admin/gallery/index.php</div>";
                
                // สร้างไฟล์ admin/gallery/add.php
                $admin_add_content = '<?php
/**
 * เพิ่มรูปภาพแกลลอรี่
 */

require_once "../../includes/config.php";
require_once "../../includes/functions.php";

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION["user_id"])) {
    header("Location: ../../login.php");
    exit;
}

// ตรวจสอบสิทธิ์
if ($_SESSION["role"] !== "admin") {
    header("Location: ../../index.php");
    exit;
}

$page_title = "เพิ่มรูปภาพแกลลอรี่";

// ดึงหมวดหมู่
$categories = db_get_rows($conn, "SELECT DISTINCT category FROM gallery WHERE category IS NOT NULL AND category != \'\' ORDER BY category ASC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = clean_input($_POST["title"]);
    $description = clean_input($_POST["description"]);
    $category = clean_input($_POST["category"]);
    $tags = clean_input($_POST["tags"]);
    $sort_order = intval($_POST["sort_order"]);
    $is_active = isset($_POST["is_active"]) ? 1 : 0;
    
    // อัพโหลดรูปภาพ
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $upload_result = upload_image($_FILES["image"], "gallery");
        if ($upload_result["success"]) {
            $image_path = $upload_result["file_path"];
        } else {
            $error_message = $upload_result["message"];
        }
    } else {
        $error_message = "กรุณาเลือกไฟล์รูปภาพ";
    }
    
    if (!isset($error_message)) {
        $data = [
            "title" => $title,
            "description" => $description,
            "image" => $image_path,
            "category" => $category,
            "tags" => $tags,
            "sort_order" => $sort_order,
            "is_active" => $is_active
        ];
        
        if (db_insert($conn, "gallery", $data)) {
            $_SESSION["success_message"] = "เพิ่มรูปภาพสำเร็จ";
            header("Location: index.php");
            exit;
        } else {
            $error_message = "เกิดข้อผิดพลาดในการเพิ่มรูปภาพ";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body { font-family: \'Noto Sans Thai\', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center mb-8">
                <a href="index.php" class="text-blue-500 hover:text-blue-600 mr-4">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-3xl font-bold text-gray-800"><?php echo $page_title; ?></h1>
            </div>
            
            <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">ชื่อรูปภาพ *</label>
                        <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">คำอธิบาย</label>
                        <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">หมวดหมู่</label>
                        <input type="text" name="category" list="categories" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <datalist id="categories">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat[\'category\']); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">แท็ก (คั่นด้วยจุลภาค)</label>
                        <input type="text" name="tags" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ลำดับ</label>
                        <input type="number" name="sort_order" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-700">แสดงผล</label>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">รูปภาพ *</label>
                        <input type="file" name="image" accept="image/*" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-sm text-gray-500 mt-1">รองรับไฟล์: JPG, PNG, GIF (ขนาดไม่เกิน 5MB)</p>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4 mt-6">
                    <a href="index.php" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                        ยกเลิก
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                        <i class="fas fa-save mr-2"></i>บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>';

                file_put_contents('admin/gallery/add.php', $admin_add_content);
                echo "<div class='result success'>✅ สร้างไฟล์ admin/gallery/add.php</div>";
                
                echo "<div class='result success'>";
                echo "<h3>🎉 แก้ไขหลังบ้านแกลลอรี่เสร็จสิ้น!</h3>";
                echo "<p>ตอนนี้หลังบ้านใช้ตาราง 'gallery' เหมือนกับหน้าบ้านแล้ว</p>";
                echo "<p><a href='admin/gallery/' style='color: #155724; font-weight: bold;'>⚙️ ไปที่หลังบ้านแกลลอรี่</a></p>";
                echo "<p><a href='gallery.php' style='color: #155724; font-weight: bold;'>📸 ดูแกลลอรี่หน้าบ้าน</a></p>";
                echo "</div>";
                
            } catch (Exception $e) {
                echo "<div class='result error'>❌ Exception: " . $e->getMessage() . "</div>";
            }
        }
        ?>
        
        <form method="POST">
            <button type="submit" name="action" value="fix_admin_gallery" class="btn btn-success">
                <i class="fas fa-tools"></i> แก้ไขหลังบ้านแกลลอรี่
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="check-gallery-system.php" class="btn">
                <i class="fas fa-search"></i> ตรวจสอบระบบ
            </a>
            <a href="gallery.php" class="btn">
                <i class="fas fa-images"></i> หน้าบ้าน
            </a>
            <a href="admin/gallery/" class="btn">
                <i class="fas fa-cog"></i> หลังบ้าน
            </a>
        </div>
        
        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <h3>📋 ระบบที่แก้ไข:</h3>
            <ul>
                <li><strong>หน้าบ้าน:</strong> ใช้ตาราง 'gallery' (แก้ไขแล้ว)</li>
                <li><strong>หลังบ้าน:</strong> สร้างใหม่ให้ใช้ตาราง 'gallery'</li>
                <li><strong>ข้อมูล:</strong> เพิ่มข้อมูลตัวอย่างถ้าไม่มี</li>
                <li><strong>ระบบ:</strong> ให้หน้าบ้านและหลังบ้านใช้ตารางเดียวกัน</li>
            </ul>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>



