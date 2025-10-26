<?php
/**
 * Fix Social Media Settings
 * แก้ไขปัญหา Twitter, LINE, YouTube
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['user_role'] = 'admin';

$message = '';
$fixed = [];

// ถ้ากดปุ่ม Fix
if (isset($_POST['fix_all'])) {
    $platforms = [
        'twitter' => ['url' => 'https://twitter.com/vibedaybkk', 'icon' => 'fa-twitter'],
        'line' => ['url' => 'https://line.me/ti/p/@vibedaybkk', 'icon' => 'fa-line'],
        'youtube' => ['url' => 'https://youtube.com/@vibedaybkk', 'icon' => 'fa-youtube'],
        'tiktok' => ['url' => 'https://tiktok.com/@vibedaybkk', 'icon' => 'fa-tiktok']
    ];
    
    foreach ($platforms as $platform => $data) {
        // 1. สร้าง/อัปเดต enabled
        $key = "social_{$platform}_enabled";
        $value = '1'; // เปิดใช้งาน
        $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value, setting_type) VALUES (?, ?, 'toggle') ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
        $stmt->bind_param('sss', $key, $value, $value);
        $stmt->execute();
        $stmt->close();
        
        // 2. สร้าง/อัปเดต URL
        $key = "social_{$platform}_url";
        $value = $data['url'];
        $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value, setting_type) VALUES (?, ?, 'text') ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
        $stmt->bind_param('sss', $key, $value, $value);
        $stmt->execute();
        $stmt->close();
        
        // 3. สร้าง/อัปเดต Icon
        $key = "social_{$platform}_icon";
        $value = $data['icon'];
        $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value, setting_type) VALUES (?, ?, 'text') ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
        $stmt->bind_param('sss', $key, $value, $value);
        $stmt->execute();
        $stmt->close();
        
        $fixed[] = ucfirst($platform);
    }
    
    $message = "✅ แก้ไขเรียบร้อย: " . implode(', ', $fixed);
}

// ดึงข้อมูลปัจจุบัน
$all_platforms = ['facebook', 'instagram', 'twitter', 'line', 'youtube', 'tiktok'];
$social_data = [];

foreach ($all_platforms as $platform) {
    $enabled = null;
    $url = null;
    $icon = null;
    
    $result = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'social_{$platform}_enabled'");
    if ($result && $row = $result->fetch_assoc()) {
        $enabled = $row['setting_value'];
    }
    
    $result = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'social_{$platform}_url'");
    if ($result && $row = $result->fetch_assoc()) {
        $url = $row['setting_value'];
    }
    
    $result = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'social_{$platform}_icon'");
    if ($result && $row = $result->fetch_assoc()) {
        $icon = $row['setting_value'];
    }
    
    $social_data[$platform] = [
        'enabled' => $enabled,
        'url' => $url,
        'icon' => $icon
    ];
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>🔧 แก้ไข Social Media</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
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
            background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
            color: white;
            padding: 50px;
            text-align: center;
        }
        .header h1 { font-size: 3em; margin-bottom: 15px; }
        .content { padding: 40px; }
        .section {
            margin: 30px 0;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 15px;
            border-left: 5px solid #ec4899;
        }
        h2 { color: #ec4899; margin-bottom: 20px; font-size: 1.8em; }
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
        th { background: #ec4899; color: white; }
        .status-null { background: #fee2e2; color: #991b1b; padding: 8px 15px; border-radius: 20px; font-weight: bold; }
        .status-missing { background: #fef3c7; color: #92400e; padding: 8px 15px; border-radius: 20px; font-weight: bold; }
        .status-ok { background: #d1fae5; color: #065f46; padding: 8px 15px; border-radius: 20px; font-weight: bold; }
        .status-off { background: #f3f4f6; color: #6b7280; padding: 8px 15px; border-radius: 20px; font-weight: bold; }
        .btn {
            display: inline-block;
            padding: 20px 40px;
            background: #ec4899;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            margin: 10px 5px;
            border: none;
            cursor: pointer;
            font-size: 1.2em;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .btn:hover { background: #db2777; transform: translateY(-2px); }
        .btn-success { background: #10b981; }
        .btn-success:hover { background: #059669; }
        .message {
            padding: 25px;
            border-radius: 15px;
            margin: 20px 0;
            font-size: 1.2em;
            font-weight: bold;
            text-align: center;
        }
        .message.success { background: #d1fae5; color: #065f46; border: 3px solid #10b981; }
        .problem {
            background: #fee2e2;
            border-left: 5px solid #dc2626;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
        }
        .solution {
            background: #d1fae5;
            border-left: 5px solid #10b981;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 แก้ไข Social Media</h1>
            <p style="font-size: 1.3em; margin-top: 10px;">Twitter, LINE, YouTube - Fix Tool</p>
        </div>
        
        <div class="content">
            <?php if ($message): ?>
            <div class="message success">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>
            
            <div class="section">
                <h2>📊 สถานะปัจจุบัน</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Platform</th>
                            <th>Enabled</th>
                            <th>URL</th>
                            <th>Icon</th>
                            <th>จะแสดงหรือไม่?</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_platforms as $platform): 
                            $data = $social_data[$platform];
                            $enabled = $data['enabled'];
                            $url = $data['url'];
                            $icon = $data['icon'];
                            
                            $will_show = ($enabled == '1' && !empty($url));
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo ucfirst($platform); ?></strong>
                            </td>
                            <td>
                                <?php if ($enabled === null): ?>
                                    <span class="status-null">❌ NULL</span>
                                <?php elseif ($enabled == '1'): ?>
                                    <span class="status-ok">✅ เปิด</span>
                                <?php else: ?>
                                    <span class="status-off">⚪ ปิด</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (empty($url)): ?>
                                    <span class="status-missing">⚠️ ไม่มี URL</span>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($url); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($icon)): ?>
                                    <i class="fab <?php echo $icon; ?>"></i> <?php echo $icon; ?>
                                <?php else: ?>
                                    <span class="status-missing">⚠️ ไม่มี</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($will_show): ?>
                                    <span class="status-ok">✅ แสดง</span>
                                <?php elseif ($enabled == '1' && empty($url)): ?>
                                    <span class="status-missing">⚠️ เปิดแต่ไม่มี URL</span>
                                <?php else: ?>
                                    <span class="status-off">❌ ไม่แสดง</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="section">
                <h2>🐛 ปัญหาที่พบ</h2>
                
                <?php
                $problems = [];
                foreach (['twitter', 'line', 'youtube', 'tiktok'] as $platform) {
                    $data = $social_data[$platform];
                    if ($data['enabled'] === null) {
                        $problems[] = ucfirst($platform) . " - ไม่มี enabled ใน database";
                    }
                    if (empty($data['url'])) {
                        $problems[] = ucfirst($platform) . " - ไม่มี URL";
                    }
                    if (empty($data['icon'])) {
                        $problems[] = ucfirst($platform) . " - ไม่มี icon";
                    }
                }
                
                if (!empty($problems)):
                ?>
                <div class="problem">
                    <h3 style="color: #991b1b; margin-bottom: 15px;">❌ พบปัญหา:</h3>
                    <ul style="margin-left: 20px; line-height: 2;">
                        <?php foreach ($problems as $prob): ?>
                        <li><?php echo $prob; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php else: ?>
                <div class="solution">
                    <h3 style="color: #065f46; margin-bottom: 15px;">✅ ไม่มีปัญหา!</h3>
                    <p>ทุกอย่างพร้อมใช้งาน</p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="section">
                <h2>🔧 แก้ไขอัตโนมัติ</h2>
                <div class="solution">
                    <h3 style="color: #065f46; margin-bottom: 15px;">💡 วิธีแก้ไข</h3>
                    <p style="line-height: 2; font-size: 1.1em;">
                        กดปุ่มด้านล่างเพื่อ:<br>
                        ✅ เปิดใช้งาน Twitter, LINE, YouTube, TikTok<br>
                        ✅ ตั้ง URL ตัวอย่าง<br>
                        ✅ ตั้ง Icon ที่ถูกต้อง<br>
                    </p>
                    <p style="margin-top: 15px; color: #64748b;">
                        <strong>หมายเหตุ:</strong> URL เป็นตัวอย่าง สามารถแก้ไขใน admin/settings/social.php ได้
                    </p>
                </div>
                
                <form method="POST" style="text-align: center; margin: 30px 0;">
                    <button type="submit" name="fix_all" class="btn btn-success" style="font-size: 1.3em; padding: 25px 50px;">
                        🔧 แก้ไขทั้งหมดเลย!
                    </button>
                </form>
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="?" class="btn" style="background:#6b7280;">🔄 Refresh</a>
                <a href="admin/settings/social.php" class="btn">⚙️ ไปหน้า Settings</a>
                <a href="/" class="btn btn-success">🏠 ดูหน้าบ้าน</a>
            </div>
            
            <div style="background: #f1f5f9; padding: 25px; border-radius: 10px; margin-top: 40px; text-align: center;">
                <h3 style="color: #64748b; margin-bottom: 15px;">📝 ขั้นตอนหลังแก้ไข</h3>
                <p style="color: #475569; font-size: 1.1em; line-height: 2;">
                    1️⃣ กดปุ่ม "แก้ไขทั้งหมดเลย!"<br>
                    2️⃣ ไปที่ <strong>admin/settings/social.php</strong> แก้ URL ให้ถูกต้อง<br>
                    3️⃣ กดบันทึก<br>
                    4️⃣ Hard Refresh หน้าบ้าน (<strong>Ctrl+Shift+R</strong>)<br>
                    5️⃣ Social icons จะแสดงครบ! ✅
                </p>
            </div>
        </div>
    </div>
</body>
</html>




