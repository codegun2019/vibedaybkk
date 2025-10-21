<?php
/**
 * Fix Duplicate Menus
 * แก้ไขเมนูซ้ำซ้อน
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['user_role'] = 'admin';

$message = '';
$action_taken = '';

// ดึงเมนูทั้งหมด
$all_menus = db_get_rows($conn, "SELECT * FROM menus ORDER BY sort_order ASC");

// เมนูคงที่ที่มีอยู่แล้วใน index.php
$fixed_menus = ['หน้าแรก', 'เกี่ยวกับเรา', 'บริการ', 'ติดต่อ'];

// หาเมนูที่ซ้ำ
$duplicate_menus = [];
foreach ($all_menus as $menu) {
    if (in_array($menu['title'], $fixed_menus)) {
        $duplicate_menus[] = $menu;
    }
}

// ถ้ากด Fix
if (isset($_POST['fix_method'])) {
    $method = $_POST['fix_method'];
    
    if ($method === 'disable_duplicates' && !empty($duplicate_menus)) {
        // ปิดใช้งานเมนูที่ซ้ำ
        foreach ($duplicate_menus as $menu) {
            $stmt = $conn->prepare("UPDATE menus SET status = 'inactive' WHERE id = ?");
            $stmt->bind_param('i', $menu['id']);
            $stmt->execute();
            $stmt->close();
        }
        $action_taken = "✅ ปิดใช้งานเมนูที่ซ้ำ " . count($duplicate_menus) . " รายการ";
    } elseif ($method === 'delete_duplicates' && !empty($duplicate_menus)) {
        // ลบเมนูที่ซ้ำ
        foreach ($duplicate_menus as $menu) {
            $stmt = $conn->prepare("DELETE FROM menus WHERE id = ?");
            $stmt->bind_param('i', $menu['id']);
            $stmt->execute();
            $stmt->close();
        }
        $action_taken = "✅ ลบเมนูที่ซ้ำ " . count($duplicate_menus) . " รายการ";
    } elseif ($method === 'delete_all') {
        // ลบเมนูทั้งหมด (ใช้เฉพาะเมนูคงที่)
        $conn->query("DELETE FROM menus WHERE parent_id IS NULL");
        $action_taken = "✅ ลบเมนูทั้งหมดจาก database (ใช้เฉพาะเมนูคงที่)";
    }
    
    // Refresh
    $all_menus = db_get_rows($conn, "SELECT * FROM menus ORDER BY sort_order ASC");
    $duplicate_menus = [];
    foreach ($all_menus as $menu) {
        if (in_array($menu['title'], $fixed_menus)) {
            $duplicate_menus[] = $menu;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>🔧 แก้เมนูซ้ำ</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
            border-left: 5px solid #f59e0b;
        }
        h2 { color: #f59e0b; margin-bottom: 15px; font-size: 1.8em; }
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
        th { background: #f59e0b; color: white; }
        .duplicate { background: #fee2e2; }
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
            padding: 18px 35px;
            background: #f59e0b;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            margin: 10px 8px;
            border: none;
            cursor: pointer;
            font-size: 1.1em;
        }
        .btn:hover { background: #d97706; }
        .btn-danger { background: #dc2626; }
        .btn-danger:hover { background: #991b1b; }
        .btn-success { background: #10b981; }
        .btn-success:hover { background: #059669; }
        .option-card {
            background: white;
            padding: 25px;
            margin: 15px 0;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .option-card h3 {
            color: #374151;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 แก้เมนูซ้ำซ้อน</h1>
            <p style="font-size: 1.3em; margin-top: 10px;">Fix Duplicate Menus</p>
        </div>
        
        <div class="content">
            <?php if ($action_taken): ?>
            <div class="message">
                <?php echo $action_taken; ?>
                <p style="font-size: 0.9em; margin-top: 15px;">
                    กด Refresh เพื่อดูผล หรือ Hard Refresh หน้าบ้าน
                </p>
            </div>
            <?php endif; ?>
            
            <div class="section">
                <h2>📊 สถานะปัจจุบัน</h2>
                
                <p><strong>เมนูคงที่:</strong> 4 รายการ (หน้าแรก, เกี่ยวกับเรา, บริการ, ติดต่อ)</p>
                <p><strong>เมนูจาก Database:</strong> <?php echo count($all_menus); ?> รายการ</p>
                <p><strong>เมนูที่ซ้ำ:</strong> 
                    <span style="color: <?php echo !empty($duplicate_menus) ? '#dc2626' : '#10b981'; ?>; font-weight: bold;">
                        <?php echo count($duplicate_menus); ?> รายการ
                    </span>
                </p>
                
                <?php if (!empty($duplicate_menus)): ?>
                <table style="margin-top: 20px;">
                    <tr>
                        <th>ID</th>
                        <th>ชื่อเมนู (ซ้ำ)</th>
                        <th>URL</th>
                        <th>สถานะ</th>
                    </tr>
                    <?php foreach ($duplicate_menus as $menu): ?>
                    <tr class="duplicate">
                        <td><?php echo $menu['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($menu['title']); ?></strong></td>
                        <td><?php echo htmlspecialchars($menu['url']); ?></td>
                        <td><?php echo $menu['status']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($duplicate_menus) || !empty($all_menus)): ?>
            <div class="section">
                <h2>🔧 เลือกวิธีแก้ไข</h2>
                
                <form method="POST">
                    <?php if (!empty($duplicate_menus)): ?>
                    <div class="option-card">
                        <h3>🔸 ตัวเลือกที่ 1: ปิดใช้งานเมนูที่ซ้ำ (แนะนำ)</h3>
                        <p style="color: #6b7280; line-height: 1.8;">
                            เมนูจะยังอยู่ใน database แต่จะไม่แสดงในหน้าเว็บ<br>
                            สามารถเปิดใช้งานใหม่ได้ในภายหลัง
                        </p>
                        <button type="submit" name="fix_method" value="disable_duplicates" class="btn" style="margin-top: 15px;">
                            ⚪ ปิดใช้งานเมนูซ้ำ (<?php echo count($duplicate_menus); ?> รายการ)
                        </button>
                    </div>
                    
                    <div class="option-card">
                        <h3>🔸 ตัวเลือกที่ 2: ลบเมนูที่ซ้ำ</h3>
                        <p style="color: #6b7280; line-height: 1.8;">
                            ลบเมนูที่ซ้ำออกจาก database ถาวร<br>
                            ไม่สามารถกู้คืนได้
                        </p>
                        <button type="submit" name="fix_method" value="delete_duplicates" class="btn btn-danger" style="margin-top: 15px;" onclick="return confirm('แน่ใจหรือไม่ที่จะลบเมนูซ้ำ?')">
                            🗑️ ลบเมนูซ้ำ (<?php echo count($duplicate_menus); ?> รายการ)
                        </button>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($all_menus)): ?>
                    <div class="option-card" style="border: 2px solid #dc2626;">
                        <h3 style="color: #dc2626;">🔸 ตัวเลือกที่ 3: ลบเมนูทั้งหมด (ใช้เฉพาะเมนูคงที่)</h3>
                        <p style="color: #6b7280; line-height: 1.8;">
                            ลบเมนูทั้งหมดจาก database<br>
                            ใช้เฉพาะเมนูคงที่ 4 เมนู: หน้าแรก, เกี่ยวกับเรา, บริการ, ติดต่อ
                        </p>
                        <button type="submit" name="fix_method" value="delete_all" class="btn btn-danger" style="margin-top: 15px;" onclick="return confirm('⚠️ แน่ใจหรือที่จะลบเมนูทั้งหมด?')">
                            🗑️ ลบเมนูทั้งหมด (<?php echo count($all_menus); ?> รายการ)
                        </button>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
            <?php else: ?>
            <div style="background: #d1fae5; padding: 30px; border-radius: 15px; text-align: center; border: 3px solid #10b981;">
                <h2 style="color: #065f46; margin-bottom: 15px;">✅ ไม่มีปัญหา!</h2>
                <p style="color: #047857; font-size: 1.2em;">
                    ไม่มีเมนูซ้ำซ้อน ทุกอย่างเรียบร้อย
                </p>
            </div>
            <?php endif; ?>
            
            <div style="background: #e0f2fe; padding: 25px; border-radius: 10px; margin-top: 30px; border-left: 5px solid #0284c7;">
                <h3 style="color: #0369a1; margin-bottom: 15px;">💡 คำแนะนำ</h3>
                <p style="color: #075985; font-size: 1.1em; line-height: 2;">
                    <strong>เมนูคงที่ที่มีอยู่แล้วใน index.php:</strong><br>
                    • หน้าแรก (BASE_URL)<br>
                    • เกี่ยวกับเรา (#about)<br>
                    • บริการ (#services)<br>
                    • ติดต่อ (#contact)<br>
                    <br>
                    <strong>เมนูจาก database:</strong> ใช้สำหรับเพิ่มเมนูพิเศษ<br>
                    (เช่น แกลเลอรี่, บทความ, โมเดล, ฯลฯ)
                </p>
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="check-menus.php" class="btn" style="background: #0284c7;">🔍 ตรวจสอบเมนู</a>
                <a href="admin/menus/" class="btn">⚙️ จัดการเมนู</a>
                <a href="/" class="btn btn-success">🏠 ดูหน้าบ้าน</a>
            </div>
        </div>
    </div>
</body>
</html>

