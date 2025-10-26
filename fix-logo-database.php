<?php
/**
 * Fix Logo Database
 * แก้ไขปัญหา logo_image = NULL
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

// Simulate admin login
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['user_role'] = 'admin';

$message = '';
$success = false;

// หาไฟล์รูป logo ล่าสุดที่อัปโหลดไว้
$upload_dir = UPLOADS_PATH . '/general';
$latest_file = null;
$latest_path = null;

if (is_dir($upload_dir)) {
    $files = glob($upload_dir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
    if (!empty($files)) {
        // เรียงตาม modified time
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        $latest_file = $files[0];
        $latest_path = 'general/' . basename($latest_file);
    }
}

// ถ้ากด Fix
if (isset($_POST['fix_logo'])) {
    $fix_type = $_POST['fix_type'] ?? 'latest';
    
    if ($fix_type === 'latest' && $latest_path) {
        // ใช้ไฟล์ล่าสุด
        $key = 'logo_image';
        $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value, setting_type) VALUES (?, ?, 'text') ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
        $stmt->bind_param('sss', $key, $latest_path, $latest_path);
        
        if ($stmt->execute()) {
            $message = "✅ อัปเดต logo_image เป็น: {$latest_path} สำเร็จ!";
            $success = true;
            
            // อัปเดต logo_type ด้วย
            $key = 'logo_type';
            $type_value = 'image';
            $stmt2 = $conn->prepare("INSERT INTO settings (setting_key, setting_value, setting_type) VALUES (?, ?, 'text') ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
            $stmt2->bind_param('sss', $key, $type_value, $type_value);
            $stmt2->execute();
            $stmt2->close();
        } else {
            $message = "❌ เกิดข้อผิดพลาด: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($fix_type === 'custom') {
        $custom_path = $_POST['custom_path'] ?? '';
        if (!empty($custom_path)) {
            $key = 'logo_image';
            $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value, setting_type) VALUES (?, ?, 'text') ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
            $stmt->bind_param('sss', $key, $custom_path, $custom_path);
            
            if ($stmt->execute()) {
                $message = "✅ อัปเดต logo_image เป็น: {$custom_path} สำเร็จ!";
                $success = true;
            } else {
                $message = "❌ เกิดข้อผิดพลาด: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// ดึงข้อมูลปัจจุบัน
$current_settings = [];
$result = $conn->query("SELECT setting_key, setting_value, updated_at FROM settings WHERE setting_key IN ('logo_type', 'logo_text', 'logo_image') ORDER BY setting_key");
while ($row = $result->fetch_assoc()) {
    $current_settings[$row['setting_key']] = $row;
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔧 แก้ไข Logo Database</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .header h1 {
            font-size: 2.5em;
        }
        .content {
            padding: 40px;
        }
        .section {
            margin: 25px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 5px solid #dc2626;
        }
        h2 {
            color: #dc2626;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #dc2626;
            color: white;
        }
        .status-null {
            background: #fee2e2;
            color: #991b1b;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .status-ok {
            background: #d1fae5;
            color: #065f46;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            background: #dc2626;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            margin: 10px 5px;
            border: none;
            cursor: pointer;
            font-size: 1em;
        }
        .btn:hover {
            background: #991b1b;
        }
        .btn-success {
            background: #059669;
        }
        .btn-success:hover {
            background: #047857;
        }
        .message {
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            font-size: 1.1em;
            font-weight: bold;
        }
        .message.success {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #059669;
        }
        .message.error {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #dc2626;
        }
        .preview-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin: 15px 0;
            text-align: center;
            border: 2px dashed #d1d5db;
        }
        .preview-box img {
            max-height: 100px;
            object-fit: contain;
        }
        input[type="radio"] {
            margin-right: 10px;
        }
        .radio-option {
            padding: 15px;
            margin: 10px 0;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid #e5e7eb;
        }
        .radio-option:hover {
            border-color: #dc2626;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            font-size: 1em;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 แก้ไข Logo Database</h1>
            <p>แก้ปัญหา logo_image = NULL</p>
        </div>
        
        <div class="content">
            <?php if ($message): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>
            
            <div class="section">
                <h2>📊 สถานะปัจจุบันใน Database</h2>
                <table>
                    <tr>
                        <th>Setting Key</th>
                        <th>Value</th>
                        <th>Updated At</th>
                        <th>สถานะ</th>
                    </tr>
                    <?php foreach ($current_settings as $key => $data): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($key); ?></strong></td>
                        <td><?php echo htmlspecialchars($data['setting_value'] ?: '(NULL)'); ?></td>
                        <td><?php echo htmlspecialchars($data['updated_at']); ?></td>
                        <td>
                            <?php if (empty($data['setting_value'])): ?>
                                <span class="status-null">❌ NULL</span>
                            <?php else: ?>
                                <span class="status-ok">✅ OK</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            
            <?php if ($latest_file): ?>
            <div class="section">
                <h2>🖼️ ไฟล์ล่าสุดที่อัปโหลด</h2>
                <p><strong>ชื่อไฟล์:</strong> <?php echo basename($latest_file); ?></p>
                <p><strong>Path:</strong> <code><?php echo htmlspecialchars($latest_path); ?></code></p>
                <p><strong>เวลา:</strong> <?php echo date('Y-m-d H:i:s', filemtime($latest_file)); ?></p>
                <p><strong>ขนาด:</strong> <?php echo number_format(filesize($latest_file) / 1024, 2); ?> KB</p>
                
                <div class="preview-box">
                    <h3 style="margin-bottom:15px; color:#374151;">ตัวอย่างรูป:</h3>
                    <img src="<?php echo UPLOADS_URL . '/' . $latest_path; ?>" alt="Latest Logo">
                </div>
            </div>
            <?php endif; ?>
            
            <div class="section">
                <h2>🔧 แก้ไขปัญหา</h2>
                <form method="POST">
                    <?php if ($latest_file): ?>
                    <label class="radio-option">
                        <input type="radio" name="fix_type" value="latest" checked>
                        <strong>ใช้ไฟล์ล่าสุด</strong>
                        <p style="color:#6b7280; margin-top:5px; margin-left:24px;">
                            ใช้: <?php echo htmlspecialchars($latest_path); ?>
                        </p>
                    </label>
                    <?php endif; ?>
                    
                    <label class="radio-option">
                        <input type="radio" name="fix_type" value="custom">
                        <strong>กำหนด Path เอง</strong>
                        <input type="text" name="custom_path" placeholder="general/filename.png">
                    </label>
                    
                    <div style="text-align:center; margin-top:30px;">
                        <button type="submit" name="fix_logo" class="btn btn-success" style="font-size:1.2em; padding:20px 40px;">
                            🔧 แก้ไขเลย!
                        </button>
                    </div>
                </form>
            </div>
            
            <div style="text-align:center; margin-top:40px;">
                <a href="debug-logo-save.php" class="btn">🐛 Debug</a>
                <a href="admin/settings/" class="btn">⚙️ Settings</a>
                <a href="/" class="btn">🏠 หน้าบ้าน</a>
            </div>
            
            <div style="background:#f1f5f9; padding:20px; border-radius:10px; margin-top:30px; text-align:center;">
                <p style="color:#64748b;">
                    💡 <strong>หลังจากแก้ไขแล้ว:</strong><br>
                    กด Ctrl+Shift+R ที่หน้าบ้านเพื่อ Hard Refresh
                </p>
            </div>
        </div>
    </div>
</body>
</html>




