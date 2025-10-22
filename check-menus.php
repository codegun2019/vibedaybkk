<?php
/**
 * Check Menus
 * ตรวจสอบเมนูที่ซ้ำซ้อน
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

// ดึงข้อมูลเมนู
$main_menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL ORDER BY sort_order ASC");

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>🔍 ตรวจสอบเมนู</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
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
        h1 { color: #0284c7; margin-bottom: 20px; text-align: center; font-size: 2.5em; }
        .section {
            margin: 30px 0;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 15px;
            border-left: 5px solid #0284c7;
        }
        h2 { color: #0284c7; margin-bottom: 15px; font-size: 1.5em; }
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
        th { background: #0284c7; color: white; }
        .warning {
            background: #fef3c7;
            border-left: 5px solid #f59e0b;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
        }
        .menu-list {
            list-style: none;
            padding: 0;
        }
        .menu-list li {
            padding: 12px;
            margin: 8px 0;
            background: white;
            border-left: 4px solid #0284c7;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .menu-list li.duplicate {
            background: #fee2e2;
            border-left-color: #dc2626;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            background: #0284c7;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            margin: 10px 5px;
        }
        .btn:hover { background: #0369a1; }
        .status-active { background: #d1fae5; color: #065f46; padding: 5px 10px; border-radius: 5px; font-weight: bold; }
        .status-inactive { background: #fee2e2; color: #991b1b; padding: 5px 10px; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 ตรวจสอบเมนู</h1>
        
        <div class="section">
            <h2>📋 เมนูคงที่ (Hard-coded ใน index.php)</h2>
            <ul class="menu-list">
                <li><strong>หน้าแรก</strong> → <?php echo BASE_URL; ?></li>
                <li><strong>เกี่ยวกับเรา</strong> → #about</li>
                <li><strong>บริการ</strong> → #services</li>
                <li><strong>ติดต่อ</strong> → #contact</li>
            </ul>
        </div>
        
        <div class="section">
            <h2>📋 เมนูจาก Database (ตาราง menus)</h2>
            
            <?php if (!empty($main_menus)): ?>
                <p style="margin-bottom: 15px;">
                    <strong>จำนวนเมนู:</strong> <?php echo count($main_menus); ?> รายการ
                </p>
                
                <table>
                    <tr>
                        <th>ลำดับ</th>
                        <th>ชื่อเมนู</th>
                        <th>URL</th>
                        <th>สถานะ</th>
                        <th>Sort Order</th>
                    </tr>
                    <?php 
                    $fixed_menu_titles = ['หน้าแรก', 'เกี่ยวกับเรา', 'บริการ', 'ติดต่อ'];
                    foreach ($main_menus as $index => $menu): 
                        $is_duplicate = in_array($menu['title'], $fixed_menu_titles);
                    ?>
                    <tr style="<?php echo $is_duplicate ? 'background: #fee2e2;' : ''; ?>">
                        <td><?php echo $index + 1; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($menu['title']); ?></strong>
                            <?php if ($is_duplicate): ?>
                                <span style="color: #dc2626; font-weight: bold;"> ⚠️ ซ้ำ!</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($menu['url']); ?></td>
                        <td>
                            <?php if ($menu['status'] === 'active'): ?>
                                <span class="status-active">✅ Active</span>
                            <?php else: ?>
                                <span class="status-inactive">❌ Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $menu['sort_order']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p style="color: #6b7280; font-style: italic;">ไม่มีเมนูใน database</p>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h2>🎯 การแสดงผลในหน้าบ้าน</h2>
            <p style="margin-bottom: 15px;">เมนูที่แสดงจะเป็น:</p>
            <ul class="menu-list">
                <li>1. หน้าแรก (คงที่)</li>
                <li>2. เกี่ยวกับเรา (คงที่)</li>
                <li>3. บริการ (คงที่)</li>
                <?php foreach ($main_menus as $index => $menu): ?>
                <li class="<?php echo in_array($menu['title'], ['หน้าแรก', 'เกี่ยวกับเรา', 'บริการ', 'ติดต่อ']) ? 'duplicate' : ''; ?>">
                    <?php echo 4 + $index; ?>. <?php echo htmlspecialchars($menu['title']); ?> (จาก database)
                    <?php if (in_array($menu['title'], ['หน้าแรก', 'เกี่ยวกับเรา', 'บริการ', 'ติดต่อ'])): ?>
                        <strong style="color: #dc2626;"> ⚠️ ซ้ำกับเมนูคงที่!</strong>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
                <li><?php echo 4 + count($main_menus); ?>. ติดต่อ (คงที่)</li>
            </ul>
        </div>
        
        <?php if (!empty($main_menus)): ?>
        <div class="warning">
            <h3 style="color: #92400e; margin-bottom: 15px; font-size: 1.3em;">⚠️ ปัญหาที่พบ</h3>
            <p style="color: #78350f; font-size: 1.1em; line-height: 2;">
                มีเมนูจาก <strong>database</strong> ถูกแทรกระหว่างเมนูคงที่<br>
                ทำให้เมนูอาจซ้ำซ้อนหรือเรียงไม่ตามต้องการ<br>
                <br>
                <strong>วิธีแก้:</strong><br>
                • ลบเมนูที่ซ้ำออกจาก database<br>
                • หรือ ปิดใช้งานเมนูที่ไม่ต้องการ (status = inactive)
            </p>
        </div>
        <?php endif; ?>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="admin/menus/" class="btn">⚙️ จัดการเมนู</a>
            <a href="/" class="btn" style="background: #10b981;">🏠 ดูหน้าบ้าน</a>
        </div>
    </div>
</body>
</html>


