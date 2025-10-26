<?php
/**
 * Test Social Icons Display
 * ทดสอบการแสดง Social Icons
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

// ดึงข้อมูล settings
$global_settings = [];
$result = db_get_rows($conn, "SELECT * FROM settings");
foreach ($result as $row) {
    $global_settings[$row['setting_key']] = $row['setting_value'];
}

// ดึงข้อมูลโซเชียลมีเดีย (ตรงตามหลังบ้าน)
$social_platforms = [
    'facebook' => ['color' => 'bg-blue-600 hover:bg-blue-700', 'default_icon' => 'fa-facebook-f', 'title' => 'Facebook'],
    'instagram' => ['color' => 'bg-pink-500 hover:bg-pink-600', 'default_icon' => 'fa-instagram', 'title' => 'Instagram'],
    'twitter' => ['color' => 'bg-black hover:bg-gray-800', 'default_icon' => 'fa-twitter', 'title' => 'X (Twitter)'],
    'line' => ['color' => 'bg-green-500 hover:bg-green-600', 'default_icon' => 'fa-line', 'title' => 'LINE'],
    'youtube' => ['color' => 'bg-red-600 hover:bg-red-700', 'default_icon' => 'fa-youtube', 'title' => 'YouTube'],
    'tiktok' => ['color' => 'bg-gray-800 hover:bg-gray-900', 'default_icon' => 'fa-tiktok', 'title' => 'TikTok']
];

$active_socials = [];
foreach ($social_platforms as $platform => $data) {
    $enabled = $global_settings["social_{$platform}_enabled"] ?? '0';
    if ($enabled == '1') {
        $url = $global_settings["social_{$platform}_url"] ?? '';
        // เฉพาะที่มี URL ถึงจะแสดง
        if (!empty($url)) {
            $active_socials[$platform] = [
                'url' => $url,
                'color' => $data['color'],
                'icon' => $global_settings["social_{$platform}_icon"] ?? $data['default_icon'],
                'title' => $data['title']
            ];
        }
    }
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧪 ทดสอบ Social Icons</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
            padding: 20px;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .section {
            background: #475569;
            padding: 30px;
            margin: 20px 0;
            border-radius: 15px;
            border-left: 5px solid #fbbf24;
        }
        h1 {
            color: #fbbf24;
            font-size: 3em;
            margin-bottom: 20px;
            text-align: center;
        }
        h2 {
            color: #fbbf24;
            font-size: 2em;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background: #1e293b;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #64748b;
        }
        th {
            background: #0f172a;
        }
        .icon-test {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 5px;
            color: white;
            font-size: 1.5em;
        }
        .status-ok {
            background: #d1fae5;
            color: #065f46;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        .status-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        .status-warning {
            background: #fef3c7;
            color: #92400e;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        .social-preview {
            display: flex;
            gap: 15px;
            padding: 30px;
            background: #1e293b;
            border-radius: 15px;
            margin: 20px 0;
            flex-wrap: wrap;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 ทดสอบ Social Icons Display</h1>
        
        <div class="section">
            <h2>📊 สถานะ Social Media ทั้งหมด</h2>
            <table>
                <thead>
                    <tr>
                        <th>Platform</th>
                        <th>Enabled</th>
                        <th>URL</th>
                        <th>Icon Class</th>
                        <th>จะแสดงหรือไม่?</th>
                        <th>ตัวอย่าง</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($social_platforms as $platform => $data): 
                        $enabled = $global_settings["social_{$platform}_enabled"] ?? '0';
                        $url = $global_settings["social_{$platform}_url"] ?? '';
                        $icon = $global_settings["social_{$platform}_icon"] ?? $data['default_icon'];
                        $will_show = ($enabled == '1' && !empty($url) && $url !== 'text');
                        $in_active = isset($active_socials[$platform]);
                    ?>
                    <tr>
                        <td><strong><?php echo ucfirst($platform); ?></strong></td>
                        <td><?php echo $enabled == '1' ? '✅ เปิด' : '❌ ปิด'; ?></td>
                        <td><?php echo htmlspecialchars($url ?: '(ว่าง)'); ?></td>
                        <td><code>fab <?php echo htmlspecialchars($icon); ?></code></td>
                        <td>
                            <?php if ($will_show && $in_active): ?>
                                <span class="status-ok">✅ แสดง</span>
                            <?php elseif ($will_show && !$in_active): ?>
                                <span class="status-warning">⚠️ ควรแสดงแต่ไม่อยู่ใน active_socials</span>
                            <?php elseif (!$will_show && $enabled == '1'): ?>
                                <span class="status-error">❌ เปิดแต่ไม่มี URL</span>
                            <?php else: ?>
                                <span class="status-error">❌ ไม่แสดง (ปิดใช้งาน)</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="icon-test <?php echo str_replace('hover:bg-', 'bg-', explode(' ', $data['color'])[0]); ?>">
                                <i class="fab <?php echo $icon; ?>"></i>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h2>🎨 ตัวอย่างการแสดงผลจริง (เหมือนหน้าบ้าน)</h2>
            
            <h3 style="margin: 20px 0;">Social Sidebar (ด้านข้าง - Desktop):</h3>
            <?php if (!empty($active_socials)): ?>
            <div class="social-preview">
                <?php foreach ($active_socials as $platform => $social): ?>
                <a href="<?php echo htmlspecialchars($social['url']); ?>" 
                   class="icon-test <?php echo str_replace('hover:bg-', 'bg-', explode(' ', $social['color'])[0]); ?>" 
                   title="<?php echo $social['title']; ?>" 
                   target="_blank">
                    <i class="fab <?php echo $social['icon']; ?>"></i>
                </a>
                <?php endforeach; ?>
            </div>
            <p style="color: #94a3b8;">จำนวนที่แสดง: <strong><?php echo count($active_socials); ?></strong> icons</p>
            <?php else: ?>
            <p style="color: #fca5a5;">❌ ไม่มี active_socials (ไม่แสดงอะไรเลย)</p>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h2>🔍 Debug Information</h2>
            <h3 style="margin: 15px 0;">$active_socials Array:</h3>
            <pre style="background: #1e293b; padding: 20px; border-radius: 10px; overflow-x: auto;">
<?php print_r($active_socials); ?>
            </pre>
            
            <h3 style="margin: 15px 0;">Raw Settings (social_*):</h3>
            <table>
                <tr>
                    <th>Setting Key</th>
                    <th>Value</th>
                </tr>
                <?php 
                foreach ($global_settings as $key => $value) {
                    if (strpos($key, 'social_') === 0) {
                        echo '<tr>';
                        echo '<td><code>' . htmlspecialchars($key) . '</code></td>';
                        echo '<td>' . htmlspecialchars($value ?: '(empty)') . '</td>';
                        echo '</tr>';
                    }
                }
                ?>
            </table>
        </div>
        
        <div style="background: #fee2e2; padding: 30px; border-radius: 15px; margin: 30px 0; border-left: 5px solid #dc2626;">
            <h3 style="color: #991b1b; margin-bottom: 15px; font-size: 1.5em;">🐛 ถ้าไม่แสดงครบ ตรวจสอบ:</h3>
            <ul style="line-height: 2; font-size: 1.1em; margin-left: 20px; color: #7f1d1d;">
                <li><strong>URL ว่างหรือเป็น "text"</strong> → แก้ไขใน Settings</li>
                <li><strong>enabled = 0</strong> → เปิดใช้งานใน Settings</li>
                <li><strong>Icon class ผิด</strong> → Font Awesome ไม่รองรับ</li>
                <li><strong>Browser Cache</strong> → Hard Refresh (Ctrl+Shift+R)</li>
            </ul>
        </div>
        
        <div style="text-align: center; margin: 40px 0;">
            <a href="fix-social-media.php" class="icon-test bg-green-500" style="width:auto; padding:20px 40px; text-decoration:none; color:white; font-weight:bold; border-radius:10px;">
                🔧 แก้ไข Social Media
            </a>
            <a href="admin/settings/social.php" class="icon-test bg-purple-600" style="width:auto; padding:20px 40px; text-decoration:none; color:white; font-weight:bold; border-radius:10px;">
                ⚙️ Settings
            </a>
            <a href="/" class="icon-test bg-red-600" style="width:auto; padding:20px 40px; text-decoration:none; color:white; font-weight:bold; border-radius:10px;">
                🏠 หน้าบ้าน
            </a>
        </div>
    </div>
</body>
</html>




