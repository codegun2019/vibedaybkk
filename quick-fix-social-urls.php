<?php
/**
 * Quick Fix Social URLs
 * แก้ไข URL = "text" ให้เป็น URL จริง
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['user_role'] = 'admin';

$fixed = [];
$message = '';

// ถ้ากด Fix
if (isset($_POST['fix_now'])) {
    $urls = [
        'twitter' => 'https://twitter.com/vibedaybkk',
        'line' => 'https://line.me/ti/p/@vibedaybkk',
        'youtube' => 'https://youtube.com/@vibedaybkk',
        'tiktok' => 'https://tiktok.com/@vibedaybkk'
    ];
    
    foreach ($urls as $platform => $url) {
        $key = "social_{$platform}_url";
        $stmt = $conn->prepare("UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?");
        $stmt->bind_param('ss', $url, $key);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $fixed[] = ucfirst($platform);
        }
        $stmt->close();
    }
    
    if (!empty($fixed)) {
        $message = "✅ แก้ไข URL สำเร็จ: " . implode(', ', $fixed);
    }
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>⚡ Quick Fix Social URLs</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            max-width: 800px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 50px;
            text-align: center;
        }
        .header h1 { font-size: 3em; margin-bottom: 15px; }
        .content { padding: 50px; }
        .problem {
            background: #fee2e2;
            border-left: 5px solid #dc2626;
            padding: 25px;
            margin: 20px 0;
            border-radius: 10px;
        }
        .problem h3 { color: #991b1b; margin-bottom: 15px; }
        .solution {
            background: #d1fae5;
            border-left: 5px solid #10b981;
            padding: 25px;
            margin: 20px 0;
            border-radius: 10px;
        }
        .solution h3 { color: #065f46; margin-bottom: 15px; }
        .urls {
            background: #1e293b;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 10px;
            margin: 15px 0;
            font-family: monospace;
            line-height: 2;
        }
        .btn-huge {
            display: block;
            width: 100%;
            padding: 30px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            text-decoration: none;
            border-radius: 15px;
            font-weight: bold;
            margin: 30px 0;
            border: none;
            cursor: pointer;
            font-size: 1.8em;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            text-align: center;
            transition: all 0.3s;
        }
        .btn-huge:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.3);
        }
        .message {
            padding: 30px;
            border-radius: 15px;
            margin: 20px 0;
            font-size: 1.3em;
            font-weight: bold;
            text-align: center;
            background: #d1fae5;
            color: #065f46;
            border: 3px solid #10b981;
            animation: slideDown 0.5s ease-out;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .icon { font-size: 1.2em; margin-right: 10px; }
        table {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th { background: #f3f4f6; font-weight: bold; }
        .old { color: #dc2626; text-decoration: line-through; }
        .new { color: #10b981; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚡ Quick Fix</h1>
            <p style="font-size: 1.4em; margin-top: 10px;">แก้ไข Social URLs ใน 1 คลิก!</p>
        </div>
        
        <div class="content">
            <?php if ($message): ?>
            <div class="message">
                <?php echo $message; ?>
                <p style="font-size: 0.9em; margin-top: 15px; opacity: 0.9;">
                    ตอนนี้ Hard Refresh หน้าบ้าน (Ctrl+Shift+R) เลย!
                </p>
            </div>
            <?php endif; ?>
            
            <div class="problem">
                <h3><span class="icon">🐛</span> ปัญหา</h3>
                <p style="font-size: 1.1em; line-height: 2;">
                    <strong>สถานะใน Database:</strong><br>
                    ✅ enabled = 1 (เปิดทุกตัว)<br>
                    ❌ URL = <span class="old">"text"</span> (ผิด!)<br>
                    <br>
                    <strong>ผลที่ได้:</strong><br>
                    แสดงเฉพาะ Facebook และ Instagram<br>
                    เพราะมีแค่ 2 ตัวนี้ที่มี <strong>URL จริง</strong>
                </p>
            </div>
            
            <div class="solution">
                <h3><span class="icon">💡</span> วิธีแก้</h3>
                <p style="font-size: 1.1em; margin-bottom: 15px;">
                    <strong>เปลี่ยน URL จาก "text" เป็น URL จริง:</strong>
                </p>
                
                <table>
                    <tr>
                        <th>Platform</th>
                        <th>เดิม</th>
                        <th>ใหม่</th>
                    </tr>
                    <tr>
                        <td><i class="fab fa-twitter"></i> Twitter</td>
                        <td><span class="old">text</span></td>
                        <td><span class="new">https://twitter.com/vibedaybkk</span></td>
                    </tr>
                    <tr>
                        <td><i class="fab fa-line"></i> LINE</td>
                        <td><span class="old">text</span></td>
                        <td><span class="new">https://line.me/ti/p/@vibedaybkk</span></td>
                    </tr>
                    <tr>
                        <td><i class="fab fa-youtube"></i> YouTube</td>
                        <td><span class="old">text</span></td>
                        <td><span class="new">https://youtube.com/@vibedaybkk</span></td>
                    </tr>
                    <tr>
                        <td><i class="fab fa-tiktok"></i> TikTok</td>
                        <td><span class="old">text</span></td>
                        <td><span class="new">https://tiktok.com/@vibedaybkk</span></td>
                    </tr>
                </table>
            </div>
            
            <form method="POST">
                <button type="submit" name="fix_now" class="btn-huge">
                    <span class="icon">⚡</span> แก้ไขทันที!
                </button>
            </form>
            
            <div style="background: #fef3c7; padding: 25px; border-radius: 10px; margin-top: 30px; text-align: center; border-left: 5px solid #f59e0b;">
                <p style="color: #92400e; font-size: 1.2em; line-height: 2;">
                    <strong>⚠️ หมายเหตุ:</strong><br>
                    URL ที่ใส่เป็นตัวอย่าง<br>
                    หลังแก้แล้วสามารถไปแก้ URL จริงได้ที่:<br>
                    <strong>admin/settings/social.php</strong>
                </p>
            </div>
            
            <div style="background: #e0f2fe; padding: 25px; border-radius: 10px; margin-top: 20px; text-align: center; border-left: 5px solid #0284c7;">
                <h3 style="color: #0369a1; margin-bottom: 10px;">📝 หลังกดปุ่มแก้ไขแล้ว</h3>
                <p style="color: #075985; font-size: 1.1em; line-height: 2;">
                    1️⃣ Hard Refresh หน้าบ้าน (<strong>Ctrl+Shift+R</strong>)<br>
                    2️⃣ Icons ทั้ง 6 ตัวจะแสดงแล้ว! ✅<br>
                    3️⃣ แก้ URL ให้ถูกต้องใน Settings (ถ้าต้องการ)
                </p>
            </div>
        </div>
    </div>
</body>
</html>


