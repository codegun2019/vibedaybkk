<?php
/**
 * Test Social Toggle
 * ทดสอบการเปิด/ปิด Social Media
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

// Simulate admin
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['user_role'] = 'admin';

$message = '';
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $platform = $_POST['platform'] ?? '';
    $enabled = $_POST['enabled'] ?? '0';
    
    if (!empty($platform)) {
        $key = "social_{$platform}_enabled";
        $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value, setting_type) VALUES (?, ?, 'toggle') ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
        $stmt->bind_param('sss', $key, $enabled, $enabled);
        
        if ($stmt->execute()) {
            $message = "✅ บันทึก {$platform} = {$enabled} สำเร็จ!";
            $success = true;
        } else {
            $message = "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Get current settings
$social_settings = [];
$platforms = ['facebook', 'instagram', 'twitter', 'line', 'youtube', 'tiktok'];
foreach ($platforms as $platform) {
    $result = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'social_{$platform}_enabled'");
    if ($result && $row = $result->fetch_assoc()) {
        $social_settings[$platform] = $row['setting_value'];
    } else {
        $social_settings[$platform] = null;
    }
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>🧪 ทดสอบ Social Toggle</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
        }
        h1 { color: #8b5cf6; margin-bottom: 20px; text-align: center; font-size: 2.5em; }
        .section {
            margin: 30px 0;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 15px;
            border-left: 5px solid #8b5cf6;
        }
        h2 { color: #8b5cf6; margin-bottom: 15px; font-size: 1.5em; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th { background: #8b5cf6; color: white; }
        .status-null { background: #fee2e2; color: #991b1b; padding: 5px 10px; border-radius: 5px; }
        .status-0 { background: #fef3c7; color: #92400e; padding: 5px 10px; border-radius: 5px; }
        .status-1 { background: #d1fae5; color: #065f46; padding: 5px 10px; border-radius: 5px; }
        .form-group {
            margin: 20px 0;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        select, button {
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 1em;
            margin: 5px;
        }
        select {
            border: 2px solid #d1d5db;
        }
        button {
            background: #8b5cf6;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover { background: #7c3aed; }
        .message {
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            font-size: 1.1em;
            font-weight: bold;
            text-align: center;
        }
        .message.success { background: #d1fae5; color: #065f46; border: 2px solid #10b981; }
        .message.error { background: #fee2e2; color: #991b1b; border: 2px solid #dc2626; }
        .btn { 
            display: inline-block;
            padding: 12px 24px;
            background: #8b5cf6;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 5px;
        }
        .btn:hover { background: #7c3aed; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 ทดสอบ Social Toggle</h1>
        
        <?php if ($message): ?>
        <div class="message <?php echo $success ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
        
        <div class="section">
            <h2>📊 สถานะปัจจุบันใน Database</h2>
            <table>
                <tr>
                    <th>Platform</th>
                    <th>Setting Key</th>
                    <th>Value</th>
                    <th>สถานะ</th>
                </tr>
                <?php foreach ($platforms as $platform): ?>
                <tr>
                    <td><strong><?php echo ucfirst($platform); ?></strong></td>
                    <td><code>social_<?php echo $platform; ?>_enabled</code></td>
                    <td><?php echo $social_settings[$platform] ?? '(NULL)'; ?></td>
                    <td>
                        <?php if ($social_settings[$platform] === null): ?>
                            <span class="status-null">❌ NULL (ไม่มีใน DB)</span>
                        <?php elseif ($social_settings[$platform] == '1'): ?>
                            <span class="status-1">✅ เปิด (1)</span>
                        <?php else: ?>
                            <span class="status-0">⚠️ ปิด (0)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        
        <div class="section">
            <h2>🔧 ทดสอบเปิด/ปิด Social Media</h2>
            <form method="POST">
                <div class="form-group">
                    <label><strong>เลือก Platform:</strong></label>
                    <select name="platform" required>
                        <option value="">-- เลือก --</option>
                        <?php foreach ($platforms as $platform): ?>
                        <option value="<?php echo $platform; ?>"><?php echo ucfirst($platform); ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <label><strong>เลือกสถานะ:</strong></label>
                    <select name="enabled" required>
                        <option value="1">✅ เปิด (1)</option>
                        <option value="0">❌ ปิด (0)</option>
                    </select>
                    
                    <button type="submit">💾 บันทึก</button>
                </div>
            </form>
        </div>
        
        <div class="section">
            <h2>💡 สำคัญ! URL ต้องมีด้วย</h2>
            <p style="line-height: 2;">
                แม้จะเปิดใช้งาน (enabled = 1) แล้ว<br>
                แต่ถ้า <strong>ไม่มี URL</strong> ก็จะ<strong>ไม่แสดง</strong>ในหน้าบ้าน<br>
                <br>
                <strong>ต้องตั้งค่า 2 อย่าง:</strong><br>
                1️⃣ social_*_enabled = 1 (เปิดใช้งาน)<br>
                2️⃣ social_*_url = https://... (มี URL)
            </p>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="admin/settings/social.php" class="btn">⚙️ ไปหน้า Settings</a>
            <a href="/" class="btn" style="background:#10b981;">🏠 ดูหน้าบ้าน</a>
            <a href="?" class="btn" style="background:#6b7280;">🔄 Refresh</a>
        </div>
    </div>
</body>
</html>




