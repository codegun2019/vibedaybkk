<?php
/**
 * Check Logo Path
 * ตรวจสอบ Path ของ Logo
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>🔍 ตรวจสอบ Logo Path</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: monospace;
            background: #1e293b;
            color: #e2e8f0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #334155;
            padding: 30px;
            border-radius: 10px;
        }
        h1 { color: #fbbf24; margin-bottom: 20px; }
        .box {
            background: #475569;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            border-left: 5px solid #fbbf24;
        }
        .error { border-color: #dc2626; }
        .success { border-color: #10b981; }
        .code {
            background: #1e293b;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            overflow-x: auto;
        }
        .highlight { color: #fbbf24; font-weight: bold; }
        .wrong { color: #fca5a5; }
        .correct { color: #6ee7b7; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #64748b;
        }
        th { background: #1e293b; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 ตรวจสอบ Logo Path</h1>
        
        <?php
        // ดึงข้อมูลจาก database
        $result = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'logo_image'");
        $logo_image = '';
        if ($result && $row = $result->fetch_assoc()) {
            $logo_image = $row['setting_value'];
        }
        
        echo '<div class="box">';
        echo '<h2>📊 ข้อมูลใน Database</h2>';
        echo '<table>';
        echo '<tr><th>Key</th><th>Value</th></tr>';
        echo '<tr><td>logo_image</td><td>' . htmlspecialchars($logo_image ?: '(NULL)') . '</td></tr>';
        echo '</table>';
        echo '</div>';
        
        echo '<div class="box">';
        echo '<h2>🔧 ค่าคงที่ในระบบ</h2>';
        echo '<table>';
        echo '<tr><th>Constant</th><th>Value</th></tr>';
        echo '<tr><td>BASE_URL</td><td>' . BASE_URL . '</td></tr>';
        echo '<tr><td>UPLOADS_URL</td><td>' . UPLOADS_URL . '</td></tr>';
        echo '<tr><td>UPLOADS_PATH</td><td>' . UPLOADS_PATH . '</td></tr>';
        echo '</table>';
        echo '</div>';
        
        if (!empty($logo_image)) {
            echo '<div class="box">';
            echo '<h2>🧮 การคำนวณ Path</h2>';
            
            // ตรวจสอบว่า path มี 'uploads/' ข้างหน้าไหม
            $has_uploads_prefix = strpos($logo_image, 'uploads/') === 0;
            
            echo '<div class="code">';
            echo '<strong>Path ใน Database:</strong><br>';
            echo '<span class="highlight">' . htmlspecialchars($logo_image) . '</span><br><br>';
            
            echo '<strong>มี "uploads/" ข้างหน้า?</strong> ';
            if ($has_uploads_prefix) {
                echo '<span class="wrong">❌ มี (ผิด!)</span><br><br>';
            } else {
                echo '<span class="correct">✅ ไม่มี (ถูก!)</span><br><br>';
            }
            echo '</div>';
            
            // แสดงวิธีการสร้าง URL
            echo '<h3 style="margin-top:20px;">📝 วิธีการสร้าง URL:</h3>';
            
            // วิธีที่ 1: ใช้ UPLOADS_URL
            $url1 = UPLOADS_URL . '/' . $logo_image;
            echo '<div class="code">';
            echo '<strong>วิธีที่ 1:</strong> UPLOADS_URL . \'/\' . logo_image<br>';
            echo 'UPLOADS_URL = <span class="highlight">' . UPLOADS_URL . '</span><br>';
            echo 'logo_image = <span class="highlight">' . htmlspecialchars($logo_image) . '</span><br>';
            echo 'ผลลัพธ์ = <span class="' . ($has_uploads_prefix ? 'wrong' : 'correct') . '">' . htmlspecialchars($url1) . '</span><br>';
            if ($has_uploads_prefix) {
                echo '<span class="wrong">❌ ผิด! (uploads ซ้ำ)</span>';
            } else {
                echo '<span class="correct">✅ ถูกต้อง!</span>';
            }
            echo '</div>';
            
            // วิธีที่ 2: ใช้ BASE_URL
            $url2 = BASE_URL . '/' . $logo_image;
            echo '<div class="code">';
            echo '<strong>วิธีที่ 2:</strong> BASE_URL . \'/\' . logo_image<br>';
            echo 'BASE_URL = <span class="highlight">' . BASE_URL . '</span><br>';
            echo 'logo_image = <span class="highlight">' . htmlspecialchars($logo_image) . '</span><br>';
            echo 'ผลลัพธ์ = <span class="' . (!$has_uploads_prefix ? 'wrong' : 'correct') . '">' . htmlspecialchars($url2) . '</span><br>';
            if ($has_uploads_prefix) {
                echo '<span class="correct">✅ ถูกต้อง!</span>';
            } else {
                echo '<span class="wrong">❌ ผิด! (ขาด uploads/)</span>';
            }
            echo '</div>';
            
            echo '</div>';
            
            // แสดงรูปตัวอย่าง
            echo '<div class="box ' . (!$has_uploads_prefix ? 'success' : 'error') . '">';
            echo '<h2>🖼️ ทดสอบแสดงรูป</h2>';
            
            $test_url = $has_uploads_prefix ? $url2 : $url1;
            
            echo '<p><strong>URL ที่ควรใช้:</strong> ' . htmlspecialchars($test_url) . '</p>';
            echo '<div style="background:white; padding:20px; margin:10px 0; border-radius:5px;">';
            echo '<img src="' . htmlspecialchars($test_url) . '" style="max-height:100px;" onerror="this.parentElement.innerHTML=\'<p style=color:red>❌ ไม่สามารถโหลดรูปได้</p>\'">';
            echo '</div>';
            echo '</div>';
        }
        
        // คำแนะนำ
        echo '<div class="box success">';
        echo '<h2>💡 คำแนะนำ</h2>';
        echo '<p><strong>Path ที่ถูกต้องควรเป็น:</strong></p>';
        echo '<div class="code">';
        echo '<span class="correct">general/filename.png</span> ← ไม่มี uploads/ ข้างหน้า<br>';
        echo 'แล้วใช้: <span class="correct">UPLOADS_URL . \'/\' . logo_image</span><br>';
        echo 'จะได้: http://localhost:8888/vibedaybkk/uploads/general/filename.png';
        echo '</div>';
        echo '</div>';
        ?>
        
        <div style="text-align:center; margin-top:30px;">
            <a href="admin/settings/" style="display:inline-block; padding:15px 30px; background:#fbbf24; color:#1e293b; text-decoration:none; border-radius:8px; font-weight:bold;">⚙️ ไปหน้า Settings</a>
        </div>
    </div>
</body>
</html>




