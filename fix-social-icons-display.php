<?php
/**
 * Fix Social Icons Display Issue
 * แก้ปัญหาไอคอนไม่แสดงครบ
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['user_role'] = 'admin';

$message = '';
$updates = [];

// ถ้ากดปุ่ม Fix Icons
if (isset($_POST['fix_icons'])) {
    // ไอคอนที่ถูกต้องสำหรับ Font Awesome 6.0
    $correct_icons = [
        'facebook' => 'fa-facebook-f',
        'instagram' => 'fa-instagram',
        'twitter' => 'fa-x-twitter',  // Font Awesome 6.0+ ใช้ fa-x-twitter
        'line' => 'fa-line',
        'youtube' => 'fa-youtube',
        'tiktok' => 'fa-tiktok'
    ];
    
    foreach ($correct_icons as $platform => $icon) {
        $key = "social_{$platform}_icon";
        $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value, setting_type) VALUES (?, ?, 'text') ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
        $stmt->bind_param('sss', $key, $icon, $icon);
        if ($stmt->execute()) {
            $updates[] = "{$platform} icon = {$icon}";
        }
        $stmt->close();
    }
    
    $message = "✅ อัปเดต Icons สำเร็จ: " . implode(', ', $updates);
}

// ดึงข้อมูลปัจจุบัน
$platforms = ['facebook', 'instagram', 'twitter', 'line', 'youtube', 'tiktok'];
$social_data = [];

foreach ($platforms as $platform) {
    $result = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'social_{$platform}_enabled'");
    $enabled = ($result && $row = $result->fetch_assoc()) ? $row['setting_value'] : null;
    
    $result = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'social_{$platform}_url'");
    $url = ($result && $row = $result->fetch_assoc()) ? $row['setting_value'] : null;
    
    $result = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'social_{$platform}_icon'");
    $icon = ($result && $row = $result->fetch_assoc()) ? $row['setting_value'] : null;
    
    $social_data[$platform] = [
        'enabled' => $enabled,
        'url' => $url,
        'icon' => $icon,
        'will_show' => ($enabled == '1' && !empty($url) && $url !== 'text')
    ];
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>🔧 แก้ปัญหา Social Icons</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            padding: 50px;
            text-align: center;
        }
        .header h1 { font-size: 3em; }
        .content { padding: 40px; }
        .section {
            margin: 30px 0;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 15px;
            border-left: 5px solid #6366f1;
        }
        h2 { color: #6366f1; margin-bottom: 20px; font-size: 1.8em; }
        .platform-card {
            background: white;
            padding: 25px;
            margin: 15px 0;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            display: grid;
            grid-template-columns: 80px 1fr 150px;
            gap: 20px;
            align-items: center;
        }
        .platform-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2em;
        }
        .status-show { background: #d1fae5; color: #065f46; padding: 10px 20px; border-radius: 20px; font-weight: bold; text-align: center; }
        .status-hide { background: #fee2e2; color: #991b1b; padding: 10px 20px; border-radius: 20px; font-weight: bold; text-align: center; }
        .message {
            padding: 25px;
            border-radius: 15px;
            margin: 20px 0;
            font-size: 1.2em;
            font-weight: bold;
            text-align: center;
            background: #d1fae5;
            color: #065f46;
            border: 3px solid #10b981;
        }
        .btn {
            display: inline-block;
            padding: 20px 40px;
            background: #6366f1;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            margin: 10px 5px;
            border: none;
            cursor: pointer;
            font-size: 1.2em;
        }
        .btn:hover { background: #4f46e5; }
        .btn-success { background: #10b981; }
        .btn-success:hover { background: #059669; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 แก้ปัญหา Social Icons</h1>
            <p style="font-size: 1.3em; margin-top: 10px;">ทำให้ Icon แสดงครบทั้ง 6 ตัว</p>
        </div>
        
        <div class="content">
            <?php if ($message): ?>
            <div class="message">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>
            
            <div class="section">
                <h2>📱 สถานะ Social Media (6 แพลตฟอร์ม)</h2>
                
                <?php 
                $colors = [
                    'facebook' => 'bg-blue-600',
                    'instagram' => 'bg-pink-500',
                    'twitter' => 'bg-black',
                    'line' => 'bg-green-500',
                    'youtube' => 'bg-red-600',
                    'tiktok' => 'bg-gray-800'
                ];
                
                foreach ($platforms as $platform):
                    $data = $social_data[$platform];
                ?>
                <div class="platform-card">
                    <div class="platform-icon <?php echo $colors[$platform]; ?>">
                        <?php if ($data['icon']): ?>
                            <i class="fab <?php echo $data['icon']; ?>"></i>
                        <?php else: ?>
                            <i class="fas fa-question"></i>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <h3 style="color: #374151; margin-bottom: 10px; font-size: 1.3em;">
                            <?php echo ucfirst($platform); ?>
                        </h3>
                        <p style="color: #6b7280; font-size: 0.9em;">
                            <strong>Enabled:</strong> <?php echo $data['enabled'] ?? 'NULL'; ?> |
                            <strong>URL:</strong> <?php echo !empty($data['url']) ? htmlspecialchars(substr($data['url'], 0, 30)) . '...' : '(ว่าง)'; ?><br>
                            <strong>Icon:</strong> <code>fab <?php echo $data['icon'] ?? '(ไม่มี)'; ?></code>
                        </p>
                    </div>
                    
                    <div>
                        <?php if ($data['will_show']): ?>
                            <div class="status-show">✅ จะแสดง</div>
                        <?php else: ?>
                            <div class="status-hide">❌ ไม่แสดง</div>
                            <?php if ($data['enabled'] == '1' && (empty($data['url']) || $data['url'] === 'text')): ?>
                                <p style="color: #dc2626; font-size: 0.85em; margin-top: 5px; text-align: center;">
                                    ⚠️ ขาด URL
                                </p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div style="background: #fef3c7; padding: 30px; border-radius: 15px; margin: 30px 0; border-left: 5px solid #f59e0b;">
                <h3 style="color: #92400e; margin-bottom: 15px; font-size: 1.5em;">💡 วิธีแก้ไข</h3>
                <p style="color: #78350f; font-size: 1.1em; line-height: 2;">
                    <strong>ขั้นตอนที่ 1:</strong> กดปุ่มด้านล่างเพื่ออัปเดต Icon ให้ถูกต้อง<br>
                    <strong>ขั้นตอนที่ 2:</strong> ไปที่ <strong>admin/settings/social.php</strong> กรอก URL ให้ครบ<br>
                    <strong>ขั้นตอนที่ 3:</strong> Hard Refresh หน้าบ้าน (Ctrl+Shift+R)<br>
                </p>
            </div>
            
            <form method="POST" style="text-align: center; margin: 40px 0;">
                <button type="submit" name="fix_icons" class="btn btn-success" style="font-size: 1.5em; padding: 30px 60px;">
                    🔧 แก้ไข Icons ทั้งหมด!
                </button>
                <p style="color: #6b7280; margin-top: 15px;">
                    จะอัปเดต Icons ให้เป็น Font Awesome 6.0 ที่ถูกต้อง
                </p>
            </form>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="test-social-icons.php" class="btn">🧪 ทดสอบ Icons</a>
                <a href="admin/settings/social.php" class="btn">⚙️ Settings</a>
                <a href="/" class="btn btn-success">🏠 หน้าบ้าน</a>
            </div>
        </div>
    </div>
</body>
</html>

