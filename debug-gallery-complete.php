<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// ตรวจสอบข้อมูลในตาราง gallery
$sql = "SELECT * FROM gallery ORDER BY created_at DESC";
$result = $conn->query($sql);
$gallery_data = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $gallery_data[] = $row;
    }
}

// ตรวจสอบไฟล์ในโฟลเดอร์
$gallery_folder = '/Applications/MAMP/htdocs/vibedaybkk/uploads/gallery/';
$existing_files = [];
if (is_dir($gallery_folder)) {
    $files = scandir($gallery_folder);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && !is_dir($gallery_folder . $file)) {
            $existing_files[] = $file;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบ Gallery อย่างละเอียด</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Noto Sans Thai', sans-serif; 
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
            padding: 20px;
            color: #fff;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .section {
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .stat-card {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.2) 0%, rgba(118, 75, 162, 0.2) 100%);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: #667eea;
        }
        .stat-label {
            font-size: 1rem;
            color: #aaa;
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        th {
            background: rgba(255,255,255,0.1);
            font-weight: 600;
        }
        .success { color: #4ade80; }
        .error { color: #f87171; }
        .warning { color: #fbbf24; }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            margin: 5px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-family: 'Noto Sans Thai', sans-serif;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        .image-preview {
            max-width: 80px;
            max-height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid rgba(255,255,255,0.1);
        }
        .image-error {
            border-color: #f87171;
        }
        .path-info {
            font-family: monospace;
            background: rgba(0,0,0,0.3);
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85rem;
        }
        .file-list {
            max-height: 300px;
            overflow-y: auto;
            background: rgba(0,0,0,0.3);
            padding: 15px;
            border-radius: 10px;
        }
        .file-item {
            padding: 8px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .file-item:last-child {
            border-bottom: none;
        }
        .problem {
            background: rgba(248, 113, 113, 0.1);
            border-left: 4px solid #f87171;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .solution {
            background: rgba(74, 222, 128, 0.1);
            border-left: 4px solid #4ade80;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-search"></i> ตรวจสอบ Gallery อย่างละเอียด</h1>
        <p style="color: #aaa; margin-bottom: 20px;">วิเคราะห์ปัญหาและหาสาเหตุที่รูปภาพโหลดไม่ได้</p>
        
        <!-- สถิติ -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($gallery_data); ?></div>
                <div class="stat-label"><i class="fas fa-database"></i> ข้อมูลในฐานข้อมูล</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($existing_files); ?></div>
                <div class="stat-label"><i class="fas fa-folder-open"></i> ไฟล์ในโฟลเดอร์</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php 
                    $match_count = 0;
                    foreach ($gallery_data as $item) {
                        $filename = basename($item['image']);
                        if (in_array($filename, $existing_files)) {
                            $match_count++;
                        }
                    }
                    echo $match_count;
                    ?>
                </div>
                <div class="stat-label"><i class="fas fa-check-circle"></i> ไฟล์ที่ตรงกัน</div>
            </div>
        </div>

        <!-- ปัญหาที่พบ -->
        <div class="section">
            <h2><i class="fas fa-exclamation-triangle"></i> ปัญหาที่พบ</h2>
            <?php
            $problems = [];
            $missing_files = [];
            
            foreach ($gallery_data as $item) {
                $image_path = $item['image'];
                $filename = basename($image_path);
                
                // ตรวจสอบว่าเป็น URL ภายนอก
                if (filter_var($image_path, FILTER_VALIDATE_URL)) {
                    if (strpos($image_path, 'unsplash.com') !== false || strpos($image_path, 'placeholder') !== false) {
                        $problems[] = [
                            'id' => $item['id'],
                            'title' => $item['title'],
                            'path' => $image_path,
                            'type' => 'external_url',
                            'message' => 'ใช้ URL ภายนอก (Unsplash หรือ Placeholder)'
                        ];
                    }
                } else {
                    // ตรวจสอบว่าไฟล์มีอยู่จริงหรือไม่
                    $full_path = $gallery_folder . $filename;
                    if (!file_exists($full_path)) {
                        $problems[] = [
                            'id' => $item['id'],
                            'title' => $item['title'],
                            'path' => $image_path,
                            'type' => 'missing_file',
                            'message' => 'ไฟล์ไม่มีอยู่ในโฟลเดอร์'
                        ];
                        $missing_files[] = $filename;
                    }
                }
            }
            ?>
            
            <?php if (count($problems) > 0): ?>
                <?php foreach ($problems as $problem): ?>
                    <div class="problem">
                        <strong><i class="fas fa-times-circle"></i> ปัญหา #<?php echo $problem['id']; ?>: <?php echo htmlspecialchars($problem['title']); ?></strong>
                        <p style="margin: 10px 0;">
                            <span class="error"><i class="fas fa-arrow-right"></i> <?php echo $problem['message']; ?></span>
                        </p>
                        <p class="path-info"><?php echo htmlspecialchars($problem['path']); ?></p>
                        
                        <?php if ($problem['type'] === 'external_url'): ?>
                            <div class="solution">
                                <strong><i class="fas fa-lightbulb"></i> วิธีแก้:</strong> ลบข้อมูลนี้ออก แล้วอัปโหลดรูปภาพจริงใหม่
                            </div>
                        <?php elseif ($problem['type'] === 'missing_file'): ?>
                            <div class="solution">
                                <strong><i class="fas fa-lightbulb"></i> วิธีแก้:</strong> 
                                <ul style="margin: 10px 0 0 20px;">
                                    <li>ลบข้อมูลนี้ออกจากฐานข้อมูล</li>
                                    <li>อัปโหลดรูปภาพใหม่ผ่าน Admin Gallery</li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="solution">
                    <strong><i class="fas fa-check-circle"></i> ไม่พบปัญหา</strong>
                    <p>ทุกรูปภาพในฐานข้อมูลมีไฟล์อยู่จริง</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- ข้อมูลในฐานข้อมูล -->
        <div class="section">
            <h2><i class="fas fa-database"></i> ข้อมูลในฐานข้อมูล</h2>
            <?php if (count($gallery_data) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Image Path</th>
                            <th>สถานะไฟล์</th>
                            <th>Preview</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gallery_data as $item): ?>
                            <?php
                            $image_path = $item['image'];
                            $filename = basename($image_path);
                            $file_exists = false;
                            $is_external = filter_var($image_path, FILTER_VALIDATE_URL);
                            
                            if (!$is_external) {
                                $full_path = $gallery_folder . $filename;
                                $file_exists = file_exists($full_path);
                            }
                            
                            // สร้าง URL สำหรับแสดงรูป
                            if ($is_external) {
                                $image_url = $image_path;
                            } else {
                                if (strpos($image_path, 'uploads/') === 0) {
                                    $image_url = 'http://localhost:8888/vibedaybkk/' . $image_path;
                                } else {
                                    $image_url = 'http://localhost:8888/vibedaybkk/uploads/' . $image_path;
                                }
                            }
                            ?>
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td><?php echo htmlspecialchars($item['title']); ?></td>
                                <td><span class="path-info"><?php echo htmlspecialchars($image_path); ?></span></td>
                                <td>
                                    <?php if ($is_external): ?>
                                        <span class="warning"><i class="fas fa-external-link-alt"></i> URL ภายนอก</span>
                                    <?php elseif ($file_exists): ?>
                                        <span class="success"><i class="fas fa-check-circle"></i> มีไฟล์</span>
                                    <?php else: ?>
                                        <span class="error"><i class="fas fa-times-circle"></i> ไม่มีไฟล์</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <img src="<?php echo $image_url; ?>" 
                                         alt="Preview" 
                                         class="image-preview <?php echo (!$is_external && !$file_exists) ? 'image-error' : ''; ?>"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <span style="display:none; color: #f87171;"><i class="fas fa-image"></i> โหลดไม่ได้</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="warning"><i class="fas fa-info-circle"></i> ไม่มีข้อมูลในฐานข้อมูล</p>
            <?php endif; ?>
        </div>

        <!-- ไฟล์ในโฟลเดอร์ -->
        <div class="section">
            <h2><i class="fas fa-folder-open"></i> ไฟล์ในโฟลเดอร์ (uploads/gallery/)</h2>
            <?php if (count($existing_files) > 0): ?>
                <div class="file-list">
                    <?php foreach ($existing_files as $file): ?>
                        <div class="file-item">
                            <i class="fas fa-file-image"></i> <?php echo htmlspecialchars($file); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="warning"><i class="fas fa-info-circle"></i> ไม่มีไฟล์ในโฟลเดอร์</p>
            <?php endif; ?>
        </div>

        <!-- ปุ่มดำเนินการ -->
        <div class="section" style="text-align: center;">
            <h2><i class="fas fa-tools"></i> ดำเนินการ</h2>
            <p style="color: #aaa; margin-bottom: 20px;">เลือกดำเนินการเพื่อแก้ไขปัญหา</p>
            
            <?php if (count($problems) > 0): ?>
                <a href="clean-gallery-ui.php" class="btn">
                    <i class="fas fa-broom"></i> ลบข้อมูลที่มีปัญหา
                </a>
            <?php endif; ?>
            
            <a href="admin/gallery/" class="btn">
                <i class="fas fa-plus-circle"></i> อัปโหลดรูปภาพใหม่
            </a>
            
            <a href="gallery.php" class="btn">
                <i class="fas fa-eye"></i> ดูหน้า Gallery
            </a>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
