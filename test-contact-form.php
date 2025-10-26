<?php
/**
 * Test Contact Form
 * ทดสอบการบันทึกฟอร์มติดต่อ
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

$message = '';
$test_data = null;

// ถ้ากดทดสอบ
if (isset($_POST['test_submit'])) {
    $_POST['csrf_token'] = generate_csrf_token();
    $_POST['name'] = 'ทดสอบ ระบบ';
    $_POST['email'] = 'test@vibedaybkk.com';
    $_POST['phone'] = '081-234-5678';
    $_POST['service_type'] = 'photography';
    $_POST['message'] = 'ทดสอบการส่งข้อความติดต่อ - ' . date('Y-m-d H:i:s');
    
    // เรียก contact-submit.php
    ob_start();
    include 'contact-submit.php';
    $response = ob_get_clean();
    
    $result = json_decode($response, true);
    
    if ($result && $result['success']) {
        $message = "✅ ทดสอบสำเร็จ! บันทึกข้อมูลแล้ว";
        $test_data = $_POST;
    } else {
        $message = "❌ ทดสอบล้มเหลว: " . ($result['message'] ?? 'Unknown error');
    }
}

// ดึงข้อมูลจาก contacts
$contacts = db_get_rows($conn, "SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5");

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>🧪 ทดสอบฟอร์มติดต่อ</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
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
        h2 { color: #8b5cf6; margin-bottom: 15px; font-size: 1.8em; }
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
        .message {
            padding: 25px;
            border-radius: 15px;
            margin: 20px 0;
            font-size: 1.2em;
            font-weight: bold;
            text-align: center;
        }
        .message.success { background: #d1fae5; color: #065f46; border: 3px solid #10b981; }
        .message.error { background: #fee2e2; color: #991b1b; border: 3px solid #dc2626; }
        .btn {
            display: inline-block;
            padding: 18px 36px;
            background: #8b5cf6;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            margin: 10px 5px;
            border: none;
            cursor: pointer;
            font-size: 1.2em;
        }
        .btn:hover { background: #7c3aed; }
        .btn-success { background: #10b981; }
        .btn-success:hover { background: #059669; }
        pre {
            background: #1e293b;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 ทดสอบฟอร์มติดต่อ</h1>
        
        <?php if ($message): ?>
        <div class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
        
        <div class="section">
            <h2>📊 ข้อมูลใน Database (5 รายการล่าสุด)</h2>
            
            <?php if (!empty($contacts)): ?>
            <p><strong>จำนวนทั้งหมด:</strong> <?php echo count($contacts); ?> รายการ (แสดง 5 ล่าสุด)</p>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ชื่อ</th>
                        <th>อีเมล</th>
                        <th>โทร</th>
                        <th>ประเภทงาน</th>
                        <th>เวลา</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $contact): ?>
                    <tr>
                        <td><?php echo $contact['id']; ?></td>
                        <td><?php echo htmlspecialchars($contact['name']); ?></td>
                        <td><?php echo htmlspecialchars($contact['email']); ?></td>
                        <td><?php echo htmlspecialchars($contact['phone']); ?></td>
                        <td><?php echo htmlspecialchars($contact['service_type']); ?></td>
                        <td><?php echo htmlspecialchars($contact['created_at']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="color: #dc2626; font-weight: bold; padding: 20px; text-align: center;">
                ❌ ยังไม่มีข้อมูล
            </p>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h2>🧪 ทดสอบส่งข้อความ</h2>
            <p style="margin-bottom: 15px;">กดปุ่มด้านล่างเพื่อทดสอบส่งข้อความติดต่อ</p>
            
            <form method="POST">
                <button type="submit" name="test_submit" class="btn btn-success" style="font-size: 1.4em; padding: 25px 50px;">
                    🧪 ทดสอบส่งข้อความ
                </button>
            </form>
            
            <?php if ($test_data): ?>
            <div style="margin-top: 20px;">
                <h3 style="color: #065f46;">ข้อมูลที่ส่ง:</h3>
                <pre><?php print_r($test_data); ?></pre>
            </div>
            <?php endif; ?>
        </div>
        
        <div style="background: #e0f2fe; padding: 25px; border-radius: 10px; margin-top: 30px; border-left: 5px solid #0284c7;">
            <h3 style="color: #0369a1; margin-bottom: 15px;">💡 ข้อมูลเพิ่มเติม</h3>
            <p style="color: #075985; line-height: 2;">
                <strong>ตารางที่ใช้:</strong> <code>contacts</code><br>
                <strong>ไฟล์ที่ประมวลผล:</strong> <code>contact-submit.php</code><br>
                <strong>การแสดงผล:</strong> SweetAlert2 Notification<br>
            </p>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="?" class="btn" style="background: #6b7280;">🔄 Refresh</a>
            <a href="/" class="btn">🏠 หน้าแรก</a>
            <a href="admin/contacts/" class="btn" style="background: #dc2626;">📧 ดูข้อความใน Admin</a>
        </div>
    </div>
</body>
</html>




