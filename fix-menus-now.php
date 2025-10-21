<?php
/**
 * Fix Duplicate Menus - Auto Fix
 * ลบเมนูซ้ำอัตโนมัติ
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['user_role'] = 'admin';

$fixed = [];
$message = '';

// เมนูที่ซ้ำ (มีอยู่แล้วใน index.php แบบคงที่)
$duplicate_titles = ['หน้าแรก', 'เกี่ยวกับเรา', 'บริการ', 'ติดต่อ'];

// ตรวจสอบเมนูที่ซ้ำ
$check_result = $conn->query("SELECT id, title, url, status FROM menus WHERE title IN ('หน้าแรก', 'เกี่ยวกับเรา', 'บริการ', 'ติดต่อ')");
$found_duplicates = [];
if ($check_result) {
    while ($row = $check_result->fetch_assoc()) {
        $found_duplicates[] = $row;
    }
}

// ถ้ากดปุ่ม Fix
if (isset($_POST['auto_fix'])) {
    if (!empty($found_duplicates)) {
        // ลบเมนูซ้ำ
        foreach ($found_duplicates as $menu) {
            $stmt = $conn->prepare("DELETE FROM menus WHERE id = ?");
            $stmt->bind_param('i', $menu['id']);
            if ($stmt->execute()) {
                $fixed[] = $menu['title'];
            }
            $stmt->close();
        }
        
        if (!empty($fixed)) {
            $message = "✅ ลบเมนูซ้ำสำเร็จ: " . implode(', ', $fixed);
            log_activity($conn, $_SESSION['user_id'], 'delete', 'menus', null, 'Removed duplicate menus');
        }
        
        // Refresh
        $check_result = $conn->query("SELECT id, title, url, status FROM menus WHERE title IN ('หน้าแรก', 'เกี่ยวกับเรา', 'บริการ', 'ติดต่อ')");
        $found_duplicates = [];
        if ($check_result) {
            while ($row = $check_result->fetch_assoc()) {
                $found_duplicates[] = $row;
            }
        }
    }
}

// นับเมนูทั้งหมด
$total_menus_result = $conn->query("SELECT COUNT(*) as total FROM menus");
$total_menus = ($total_menus_result && $row = $total_menus_result->fetch_assoc()) ? $row['total'] : 0;

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚡ แก้เมนูซ้ำ - Auto Fix</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            max-width: 900px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: white;
            padding: 60px;
            text-align: center;
        }
        .header h1 { font-size: 3.5em; margin-bottom: 15px; }
        .content { padding: 50px; }
        .problem {
            background: #fee2e2;
            border-left: 5px solid #dc2626;
            padding: 30px;
            margin: 25px 0;
            border-radius: 12px;
        }
        .problem h3 { color: #991b1b; margin-bottom: 15px; font-size: 1.5em; }
        .solution {
            background: #d1fae5;
            border-left: 5px solid #10b981;
            padding: 30px;
            margin: 25px 0;
            border-radius: 12px;
        }
        .solution h3 { color: #065f46; margin-bottom: 15px; font-size: 1.5em; }
        .message {
            padding: 30px;
            border-radius: 15px;
            margin: 25px 0;
            font-size: 1.3em;
            font-weight: bold;
            text-align: center;
            background: #d1fae5;
            color: #065f46;
            border: 3px solid #10b981;
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        .btn-huge {
            display: block;
            width: 100%;
            padding: 35px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: white;
            text-decoration: none;
            border-radius: 15px;
            font-weight: bold;
            margin: 30px 0;
            border: none;
            cursor: pointer;
            font-size: 2em;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            text-align: center;
            transition: all 0.3s;
        }
        .btn-huge:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
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
        }
        .btn:hover { background: #991b1b; }
        .btn-success { background: #10b981; }
        .btn-success:hover { background: #059669; }
        ul {
            list-style: none;
            padding: 0;
        }
        ul li {
            padding: 15px;
            margin: 10px 0;
            background: white;
            border-left: 4px solid #dc2626;
            border-radius: 8px;
        }
        .info-box {
            background: #e0f2fe;
            border-left: 5px solid #0284c7;
            padding: 25px;
            margin: 25px 0;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚡ แก้เมนูซ้ำ</h1>
            <p style="font-size: 1.4em; margin-top: 10px;">Auto Fix Duplicate Menus</p>
        </div>
        
        <div class="content">
            <?php if ($message): ?>
            <div class="message">
                <?php echo $message; ?>
                <p style="font-size: 0.8em; margin-top: 15px; opacity: 0.9;">
                    Hard Refresh หน้าบ้านเพื่อดูผล (Ctrl+Shift+R)
                </p>
            </div>
            <?php endif; ?>
            
            <div class="problem">
                <h3>🐛 ปัญหา: เมนูซ้ำซ้อน</h3>
                <p style="font-size: 1.2em; line-height: 2; color: #7f1d1d;">
                    <strong>เมนูที่พบว่าซ้ำ:</strong> เกี่ยวกับเรา, บริการ, ติดต่อ<br>
                    <br>
                    <strong>สาเหตุ:</strong> มีเมนูเหล่านี้ใน <strong>database</strong> ด้วย<br>
                    แต่ใน <strong>index.php</strong> ก็มีเมนูคงที่อยู่แล้ว<br>
                    เลยทำให้แสดงซ้ำ 2 ครั้ง
                </p>
            </div>
            
            <?php if (!empty($found_duplicates)): ?>
            <div class="section" style="background: #fef3c7; padding: 25px; border-radius: 12px; margin: 25px 0;">
                <h3 style="color: #92400e; margin-bottom: 15px;">📋 เมนูซ้ำที่พบใน Database:</h3>
                <ul>
                    <?php foreach ($found_duplicates as $menu): ?>
                    <li style="border-left-color: #f59e0b; background: white;">
                        <strong><?php echo htmlspecialchars($menu['title']); ?></strong>
                        (ID: <?php echo $menu['id']; ?>, URL: <?php echo htmlspecialchars($menu['url']); ?>, 
                        Status: <?php echo $menu['status']; ?>)
                    </li>
                    <?php endforeach; ?>
                </ul>
                <p style="color: #92400e; margin-top: 15px; font-weight: bold;">
                    รวม: <?php echo count($found_duplicates); ?> รายการ
                </p>
            </div>
            
            <div class="solution">
                <h3>💡 วิธีแก้ไข</h3>
                <p style="font-size: 1.2em; line-height: 2; color: #047857;">
                    <strong>ลบเมนูซ้ำออกจาก database</strong><br>
                    เพราะเมนูเหล่านี้มีอยู่แล้วแบบคงที่ใน index.php<br>
                    <br>
                    กดปุ่มด้านล่างเพื่อลบทันที!
                </p>
            </div>
            
            <form method="POST">
                <button type="submit" name="auto_fix" class="btn-huge" onclick="return confirm('แน่ใจหรือว่าต้องการลบเมนูซ้ำ <?php echo count($found_duplicates); ?> รายการ?')">
                    ⚡ ลบเมนูซ้ำทันที!
                </button>
            </form>
            
            <?php else: ?>
            
            <div class="solution" style="text-align: center; padding: 40px;">
                <h2 style="color: #065f46; font-size: 2.5em; margin-bottom: 15px;">✅ ไม่มีเมนูซ้ำ!</h2>
                <p style="color: #047857; font-size: 1.3em; line-height: 2;">
                    ไม่พบเมนูซ้ำใน database<br>
                    ทุกอย่างเรียบร้อยแล้ว
                </p>
            </div>
            
            <?php endif; ?>
            
            <div class="info-box">
                <h3 style="color: #0369a1; margin-bottom: 15px; font-size: 1.3em;">📝 ข้อมูลเพิ่มเติม</h3>
                <p style="color: #075985; font-size: 1.1em; line-height: 2;">
                    <strong>เมนูคงที่ใน index.php (4 เมนู):</strong><br>
                    1. หน้าแรก → BASE_URL<br>
                    2. เกี่ยวกับเรา → #about<br>
                    3. บริการ → #services<br>
                    4. ติดต่อ → #contact<br>
                    <br>
                    <strong>เมนูใน database ปัจจุบัน:</strong> <?php echo $total_menus; ?> รายการ<br>
                    <strong>เมนูซ้ำที่พบ:</strong> <?php echo count($found_duplicates); ?> รายการ
                </p>
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="?" class="btn" style="background: #6b7280;">🔄 Refresh</a>
                <a href="check-menus.php" class="btn" style="background: #0284c7;">🔍 ตรวจสอบเมนู</a>
                <a href="/" class="btn btn-success">🏠 ดูหน้าบ้าน</a>
            </div>
            
            <div style="background: #fef3c7; padding: 25px; border-radius: 10px; margin-top: 30px; text-align: center; border-left: 5px solid #f59e0b;">
                <p style="color: #92400e; font-size: 1.1em; line-height: 2;">
                    💡 <strong>หลังกดปุ่มแก้ไขแล้ว:</strong><br>
                    Hard Refresh หน้าบ้าน (Ctrl+Shift+R)<br>
                    เมนูจะไม่ซ้ำอีกต่อไป! ✅
                </p>
            </div>
        </div>
    </div>
</body>
</html>

