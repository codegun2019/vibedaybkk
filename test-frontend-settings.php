<?php
/**
 * Test Frontend Settings
 * ทดสอบว่าหน้าบ้านดึงข้อมูล settings ได้หรือไม่
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ทดสอบการตั้งค่าหน้าบ้าน</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
        }
        h1 {
            color: #DC2626;
            margin-bottom: 20px;
            text-align: center;
            font-size: 2.5em;
        }
        .section {
            margin: 30px 0;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 15px;
            border-left: 5px solid #DC2626;
        }
        h2 {
            color: #DC2626;
            margin-bottom: 15px;
            font-size: 1.5em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #DC2626;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background: #f9fafb;
        }
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9em;
            font-weight: bold;
        }
        .status.ok {
            background: #d1fae5;
            color: #065f46;
        }
        .status.error {
            background: #fee2e2;
            color: #991b1b;
        }
        .status.warning {
            background: #fef3c7;
            color: #92400e;
        }
        .code {
            background: #1e293b;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            overflow-x: auto;
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #DC2626;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 10px 5px;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #991b1b;
            transform: translateY(-2px);
        }
        .info-box {
            background: #e0f2fe;
            border-left: 5px solid #0284c7;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .preview {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin: 15px 0;
            border: 2px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 ทดสอบการตั้งค่าหน้าบ้าน</h1>
        
        <?php
        // ดึงข้อมูล settings ทั้งหมด
        $global_settings = [];
        $result = db_get_rows($conn, "SELECT * FROM settings ORDER BY setting_key ASC");
        foreach ($result as $row) {
            $global_settings[$row['setting_key']] = $row['setting_value'];
        }
        
        $total_settings = count($global_settings);
        ?>
        
        <div class="section">
            <h2>📊 สถานะการโหลด Settings</h2>
            <div class="info-box">
                <p><strong>✅ โหลด Settings สำเร็จ:</strong> <?php echo $total_settings; ?> รายการ</p>
                <p><strong>📅 เวลาตรวจสอบ:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
            </div>
        </div>
        
        <div class="section">
            <h2>🔧 การตั้งค่าสำคัญ</h2>
            <table>
                <thead>
                    <tr>
                        <th>Setting Key</th>
                        <th>Setting Value</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $important_keys = [
                        'site_name' => 'ชื่อเว็บไซต์',
                        'site_email' => 'อีเมล',
                        'site_phone' => 'เบอร์โทร',
                        'site_line' => 'LINE ID',
                        'logo_type' => 'ประเภทโลโก้',
                        'logo_text' => 'ข้อความโลโก้',
                        'show_model_price' => 'แสดงราคา',
                        'show_model_details' => 'แสดงรายละเอียด',
                        'facebook_url' => 'Facebook URL',
                        'instagram_url' => 'Instagram URL',
                        'social_facebook_enabled' => 'Facebook เปิดใช้งาน',
                        'social_instagram_enabled' => 'Instagram เปิดใช้งาน',
                        'gototop_enabled' => 'Go to Top เปิดใช้งาน',
                    ];
                    
                    foreach ($important_keys as $key => $label) {
                        $value = $global_settings[$key] ?? '';
                        $has_value = !empty($value);
                        
                        echo '<tr>';
                        echo '<td><strong>' . htmlspecialchars($label) . '</strong><br><small style="color:#6b7280;">' . $key . '</small></td>';
                        echo '<td>' . htmlspecialchars($value ?: '(ว่าง)') . '</td>';
                        
                        if ($has_value) {
                            echo '<td><span class="status ok">✅ มีค่า</span></td>';
                        } else {
                            echo '<td><span class="status warning">⚠️ ว่าง</span></td>';
                        }
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h2>🎨 ตัวอย่างการแสดงผล</h2>
            
            <div class="preview">
                <h3 style="color: #DC2626; margin-bottom: 15px;">Logo & Site Name</h3>
                <?php
                $logo_type = $global_settings['logo_type'] ?? 'text';
                $logo_text = $global_settings['logo_text'] ?? 'VIBEDAYBKK';
                $logo_image = $global_settings['logo_image'] ?? '';
                ?>
                
                <div style="background: #0a0a0a; padding: 20px; border-radius: 10px; margin: 10px 0;">
                    <?php if ($logo_type === 'image' && !empty($logo_image)): ?>
                        <img src="<?php echo UPLOADS_URL . '/' . $logo_image; ?>" alt="Logo" style="height: 60px;">
                    <?php else: ?>
                        <div style="color: #DC2626; font-size: 2em; font-weight: bold;">
                            <i class="fas fa-star"></i> <?php echo htmlspecialchars($logo_text); ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <p><strong>ประเภท:</strong> <?php echo $logo_type === 'image' ? '🖼️ รูปภาพ' : '📝 ข้อความ'; ?></p>
                <p><strong>ค่า:</strong> <?php echo $logo_type === 'image' ? $logo_image : $logo_text; ?></p>
            </div>
            
            <div class="preview">
                <h3 style="color: #DC2626; margin-bottom: 15px;">Contact Info</h3>
                <p>📧 <strong>Email:</strong> <?php echo htmlspecialchars($global_settings['site_email'] ?? 'ไม่ได้ตั้งค่า'); ?></p>
                <p>📱 <strong>Phone:</strong> <?php echo htmlspecialchars($global_settings['site_phone'] ?? 'ไม่ได้ตั้งค่า'); ?></p>
                <p>💬 <strong>LINE:</strong> <?php echo htmlspecialchars($global_settings['site_line'] ?? 'ไม่ได้ตั้งค่า'); ?></p>
            </div>
            
            <div class="preview">
                <h3 style="color: #DC2626; margin-bottom: 15px;">Display Settings</h3>
                <?php
                $show_price = ($global_settings['show_model_price'] ?? '1') == '1';
                $show_details = ($global_settings['show_model_details'] ?? '1') == '1';
                ?>
                <p>💰 <strong>แสดงราคา:</strong> 
                    <?php echo $show_price ? '<span class="status ok">✅ เปิด</span>' : '<span class="status error">❌ ปิด</span>'; ?>
                </p>
                <p>📋 <strong>แสดงรายละเอียด:</strong> 
                    <?php echo $show_details ? '<span class="status ok">✅ เปิด</span>' : '<span class="status error">❌ ปิด</span>'; ?>
                </p>
            </div>
        </div>
        
        <div class="section">
            <h2>🔗 Social Media Settings</h2>
            <table>
                <thead>
                    <tr>
                        <th>Platform</th>
                        <th>URL</th>
                        <th>Icon</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $social_platforms = ['facebook', 'instagram', 'twitter', 'line', 'youtube', 'tiktok'];
                    foreach ($social_platforms as $platform) {
                        $url = $global_settings["social_{$platform}_url"] ?? '';
                        $icon = $global_settings["social_{$platform}_icon"] ?? 'fa-' . $platform;
                        $enabled = ($global_settings["social_{$platform}_enabled"] ?? '0') == '1';
                        
                        echo '<tr>';
                        echo '<td><strong>' . ucfirst($platform) . '</strong></td>';
                        echo '<td>' . htmlspecialchars($url ?: '(ไม่มี URL)') . '</td>';
                        echo '<td><i class="fab ' . $icon . '"></i> ' . $icon . '</td>';
                        
                        if ($enabled && !empty($url)) {
                            echo '<td><span class="status ok">✅ เปิดใช้งาน</span></td>';
                        } elseif ($enabled) {
                            echo '<td><span class="status warning">⚠️ เปิด แต่ไม่มี URL</span></td>';
                        } else {
                            echo '<td><span class="status error">❌ ปิดใช้งาน</span></td>';
                        }
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h2>📝 โค้ดที่ใช้ดึงข้อมูล (ใน index.php)</h2>
            <div class="code">
// ดึงข้อมูลจาก settings
$global_settings = [];
$result = db_get_rows($conn, "SELECT * FROM settings");
foreach ($result as $row) {
    $global_settings[$row['setting_key']] = $row['setting_value'];
}

// ตัวอย่างการใช้งาน
$logo_type = $global_settings['logo_type'] ?? 'text';
$logo_text = $global_settings['logo_text'] ?? 'VIBEDAYBKK';
            </div>
        </div>
        
        <div class="section">
            <h2>🔍 ปัญหาที่อาจพบและวิธีแก้</h2>
            <table>
                <thead>
                    <tr>
                        <th>ปัญหา</th>
                        <th>สาเหตุ</th>
                        <th>วิธีแก้</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>บันทึกแล้วแต่ไม่เปลี่ยน</td>
                        <td>Browser cache</td>
                        <td>กด Ctrl+Shift+R (Clear cache)</td>
                    </tr>
                    <tr>
                        <td>ข้อมูลไม่แสดง</td>
                        <td>ไม่มีใน database</td>
                        <td>ตรวจสอบว่าบันทึกจริงหรือไม่</td>
                    </tr>
                    <tr>
                        <td>แสดงค่าเก่า</td>
                        <td>Query ผิด</td>
                        <td>ตรวจสอบ SQL query</td>
                    </tr>
                    <tr>
                        <td>Logo ไม่แสดง</td>
                        <td>Path ไม่ถูกต้อง</td>
                        <td>ตรวจสอบ UPLOADS_URL</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h2>✅ ขั้นตอนการแก้ปัญหา</h2>
            <ol style="line-height: 2; font-size: 1.1em;">
                <li><strong>Clear Browser Cache:</strong> กด Ctrl+Shift+R หรือ Cmd+Shift+R</li>
                <li><strong>ตรวจสอบการบันทึก:</strong> ไปที่ phpMyAdmin ดูตาราง settings</li>
                <li><strong>Refresh หน้าบ้าน:</strong> เปิดหน้าบ้านใหม่ (Hard Refresh)</li>
                <li><strong>ตรวจสอบ Console:</strong> เปิด Developer Tools ดู error</li>
                <li><strong>ลอง Incognito Mode:</strong> เปิดหน้าใน Private/Incognito</li>
            </ol>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="/" class="btn">🏠 ดูหน้าบ้าน</a>
            <a href="admin/settings/" class="btn">⚙️ ตั้งค่า</a>
            <a href="<?php echo str_replace('test-frontend-settings.php', '', $_SERVER['PHP_SELF']); ?>phpMyAdmin" class="btn" target="_blank">🗄️ phpMyAdmin</a>
        </div>
        
        <div style="background: #f1f5f9; padding: 20px; border-radius: 10px; margin-top: 40px; text-align: center;">
            <p style="color: #64748b;">
                💡 <strong>Tip:</strong> ถ้าข้อมูลใน Settings มีแล้ว แต่หน้าบ้านยังไม่แสดง
                <br>ให้ลองกด <strong>Ctrl+Shift+R</strong> (Windows) หรือ <strong>Cmd+Shift+R</strong> (Mac) เพื่อ Hard Refresh
            </p>
        </div>
    </div>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</body>
</html>




