<?php
/**
 * Debug Price Settings - ตรวจสอบการตั้งค่าการแสดงราคา
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

// ดึงการตั้งค่าทั้งหมด
$global_settings = get_all_settings($conn);

// ดึงจากฐานข้อมูลโดยตรง
$db_settings = [];
$result = $conn->query("SELECT * FROM settings WHERE setting_key LIKE '%price%' OR setting_key LIKE '%homepage%' OR setting_key LIKE '%model_detail%' ORDER BY setting_key");
while ($row = $result->fetch_assoc()) {
    $db_settings[] = $row;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔍 ตรวจสอบการตั้งค่าราคา</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
        }
        h1 { color: #667eea; margin-bottom: 30px; }
        h2 { color: #DC2626; margin: 30px 0 15px 0; border-bottom: 2px solid #DC2626; padding-bottom: 10px; }
        .status-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 15px 0;
            border-left: 5px solid #667eea;
        }
        .status-box.enabled {
            border-color: #28a745;
            background: #d4edda;
        }
        .status-box.disabled {
            border-color: #dc3545;
            background: #f8d7da;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #667eea;
            color: white;
        }
        .key { font-family: monospace; font-weight: bold; color: #667eea; }
        .value { font-family: monospace; color: #28a745; }
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            margin: 5px;
        }
        .btn:hover { background: #5558d9; }
        .code-block {
            background: #1e1e1e;
            color: #0f0;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-bug me-2"></i>🔍 ตรวจสอบการตั้งค่าการแสดงราคา</h1>
        
        <!-- สถานะปัจจุบัน -->
        <h2>📊 สถานะปัจจุบัน</h2>
        
        <div class="row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
            <!-- หน้าแรก -->
            <div class="status-box <?php echo ($global_settings['homepage_show_price'] ?? '1') == '1' ? 'enabled' : 'disabled'; ?>">
                <h4 style="margin-bottom: 10px;">
                    <i class="fas fa-home me-2"></i>หน้าแรก (Homepage)
                </h4>
                <p><strong>แสดงราคา:</strong> 
                    <?php if (($global_settings['homepage_show_price'] ?? '1') == '1'): ?>
                        <span style="color: #28a745; font-weight: bold;">✅ เปิด</span>
                    <?php else: ?>
                        <span style="color: #dc3545; font-weight: bold;">❌ ปิด</span>
                    <?php endif; ?>
                </p>
                <p><strong>รูปแบบ:</strong> <?php echo $global_settings['homepage_price_format'] ?? 'full'; ?></p>
            </div>
            
            <!-- หน้ารายการ -->
            <div class="status-box <?php echo ($global_settings['models_list_show_price'] ?? '1') == '1' ? 'enabled' : 'disabled'; ?>">
                <h4 style="margin-bottom: 10px;">
                    <i class="fas fa-list me-2"></i>หน้ารายการโมเดล
                </h4>
                <p><strong>แสดงราคา:</strong> 
                    <?php if (($global_settings['models_list_show_price'] ?? '1') == '1'): ?>
                        <span style="color: #28a745; font-weight: bold;">✅ เปิด</span>
                    <?php else: ?>
                        <span style="color: #dc3545; font-weight: bold;">❌ ปิด</span>
                    <?php endif; ?>
                </p>
                <p><strong>รูปแบบ:</strong> <?php echo $global_settings['models_list_price_format'] ?? 'full'; ?></p>
            </div>
            
            <!-- หน้ารายละเอียด -->
            <div class="status-box <?php echo ($global_settings['model_detail_show_price'] ?? '1') == '1' ? 'enabled' : 'disabled'; ?>">
                <h4 style="margin-bottom: 10px;">
                    <i class="fas fa-eye me-2"></i>หน้ารายละเอียด
                </h4>
                <p><strong>แสดงราคา:</strong> 
                    <?php if (($global_settings['model_detail_show_price'] ?? '1') == '1'): ?>
                        <span style="color: #28a745; font-weight: bold;">✅ เปิด</span>
                    <?php else: ?>
                        <span style="color: #dc3545; font-weight: bold;">❌ ปิด</span>
                    <?php endif; ?>
                </p>
                <p><strong>แสดงช่วงราคา:</strong> 
                    <?php echo ($global_settings['model_detail_show_price_range'] ?? '1') == '1' ? '✅ เปิด' : '❌ ปิด'; ?>
                </p>
            </div>
        </div>
        
        <!-- ข้อมูลส่วนตัว -->
        <h2>👤 การแสดงข้อมูลส่วนตัว</h2>
        
        <div class="status-box <?php echo ($global_settings['model_detail_show_personal_info'] ?? '1') == '1' ? 'enabled' : 'disabled'; ?>">
            <p><strong>แสดงข้อมูลส่วนตัว:</strong> 
                <?php if (($global_settings['model_detail_show_personal_info'] ?? '1') == '1'): ?>
                    <span style="color: #28a745; font-weight: bold;">✅ เปิด</span>
                <?php else: ?>
                    <span style="color: #dc3545; font-weight: bold;">❌ ปิด</span>
                <?php endif; ?>
            </p>
            <p><small>น้ำหนัก, ส่วนสูง, วันเกิด, อายุ, รอบอก/เอว/สะโพก</small></p>
        </div>
        
        <!-- ข้อความแทนราคา -->
        <h2>💬 ข้อความเมื่อปิดการแสดงราคา</h2>
        
        <div class="status-box">
            <p><strong>ข้อความ:</strong> "<?php echo htmlspecialchars($global_settings['price_hidden_text'] ?? 'ติดต่อสอบถาม'); ?>"</p>
        </div>
        
        <!-- ข้อมูลใน Database -->
        <h2>🗄️ ข้อมูลใน Database (ตาราง settings)</h2>
        
        <table>
            <thead>
                <tr>
                    <th>Setting Key</th>
                    <th>Value</th>
                    <th>Type</th>
                    <th>Category</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($db_settings)): ?>
                    <?php foreach ($db_settings as $setting): ?>
                        <tr>
                            <td class="key"><?php echo $setting['setting_key']; ?></td>
                            <td class="value">
                                <?php 
                                $val = $setting['setting_value'];
                                if ($val == '1') echo '✅ เปิด (1)';
                                elseif ($val == '0') echo '❌ ปิด (0)';
                                else echo htmlspecialchars($val);
                                ?>
                            </td>
                            <td><?php echo $setting['setting_type'] ?? '-'; ?></td>
                            <td><?php echo $setting['category'] ?? '-'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #dc3545;">
                            ⚠️ ไม่พบข้อมูล settings ที่เกี่ยวข้อง
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Code ที่ใช้ใน index.php -->
        <h2>💻 Code ที่ใช้ในหน้าแรก (index.php)</h2>
        
        <div class="code-block">
// ตรวจสอบการตั้งค่าการแสดงราคา
$homepage_show_price = ($global_settings['homepage_show_price'] ?? '1') == '1';

if ($homepage_show_price && !empty($model['price']) && $model['price'] > 0): 
    // แสดงราคา
    echo number_format($model['price']) . ' ฿';
elseif (!$homepage_show_price): 
    // แสดงข้อความแทน
    echo $global_settings['price_hidden_text'] ?? 'ติดต่อสอบถาม';
endif;
        </div>
        
        <!-- ค่าที่ได้ -->
        <h2>🔍 ค่าที่ได้จาก $global_settings</h2>
        
        <table>
            <thead>
                <tr>
                    <th>Key</th>
                    <th>Value</th>
                    <th>เงื่อนไข (== '1')</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="key">homepage_show_price</td>
                    <td class="value"><?php echo var_export($global_settings['homepage_show_price'] ?? '1', true); ?></td>
                    <td>
                        <?php if (($global_settings['homepage_show_price'] ?? '1') == '1'): ?>
                            <span style="color: #28a745; font-weight: bold;">✅ TRUE (แสดงราคา)</span>
                        <?php else: ?>
                            <span style="color: #dc3545; font-weight: bold;">❌ FALSE (ไม่แสดงราคา)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="key">models_list_show_price</td>
                    <td class="value"><?php echo var_export($global_settings['models_list_show_price'] ?? '1', true); ?></td>
                    <td>
                        <?php if (($global_settings['models_list_show_price'] ?? '1') == '1'): ?>
                            <span style="color: #28a745; font-weight: bold;">✅ TRUE (แสดงราคา)</span>
                        <?php else: ?>
                            <span style="color: #dc3545; font-weight: bold;">❌ FALSE (ไม่แสดงราคา)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="key">model_detail_show_price</td>
                    <td class="value"><?php echo var_export($global_settings['model_detail_show_price'] ?? '1', true); ?></td>
                    <td>
                        <?php if (($global_settings['model_detail_show_price'] ?? '1') == '1'): ?>
                            <span style="color: #28a745; font-weight: bold;">✅ TRUE (แสดงราคา)</span>
                        <?php else: ?>
                            <span style="color: #dc3545; font-weight: bold;">❌ FALSE (ไม่แสดงราคา)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="key">model_detail_show_personal_info</td>
                    <td class="value"><?php echo var_export($global_settings['model_detail_show_personal_info'] ?? '1', true); ?></td>
                    <td>
                        <?php if (($global_settings['model_detail_show_personal_info'] ?? '1') == '1'): ?>
                            <span style="color: #28a745; font-weight: bold;">✅ TRUE (แสดง)</span>
                        <?php else: ?>
                            <span style="color: #dc3545; font-weight: bold;">❌ FALSE (ไม่แสดง)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="key">price_hidden_text</td>
                    <td class="value"><?php echo htmlspecialchars($global_settings['price_hidden_text'] ?? 'ติดต่อสอบถาม'); ?></td>
                    <td>-</td>
                </tr>
            </tbody>
        </table>
        
        <!-- ทดสอบ SQL Query -->
        <h2>📝 ทดสอบ SQL Query</h2>
        
        <div class="code-block">
<?php
$test_query = "SELECT setting_key, setting_value FROM settings WHERE setting_key = 'homepage_show_price'";
echo "Query: {$test_query}\n\n";

$test_result = $conn->query($test_query);
if ($test_result && $row = $test_result->fetch_assoc()) {
    echo "Result:\n";
    echo "  setting_key: " . $row['setting_key'] . "\n";
    echo "  setting_value: " . $row['setting_value'] . "\n";
    echo "  Type: " . gettype($row['setting_value']) . "\n";
    echo "  == '1': " . (($row['setting_value'] == '1') ? 'TRUE' : 'FALSE') . "\n";
    echo "  === '1': " . (($row['setting_value'] === '1') ? 'TRUE' : 'FALSE') . "\n";
} else {
    echo "❌ ไม่พบการตั้งค่า 'homepage_show_price' ในฐานข้อมูล!\n";
    echo "ค่าเริ่มต้น (default): '1'\n";
}
?>
        </div>
        
        <!-- สรุป -->
        <h2>📊 สรุป</h2>
        
        <div style="background: #e3f2fd; padding: 20px; border-radius: 10px; border-left: 5px solid #2196f3;">
            <h4 style="color: #1976d2; margin-bottom: 15px;">สิ่งที่ควรเห็น:</h4>
            <ul style="line-height: 2;">
                <li>
                    <strong>ถ้าตั้งค่าปิดการแสดงราคา (homepage_show_price = 0):</strong><br>
                    → หน้าแรกควรแสดง "<?php echo htmlspecialchars($global_settings['price_hidden_text'] ?? 'ติดต่อสอบถาม'); ?>" แทนราคา
                </li>
                <li>
                    <strong>ถ้าตั้งค่าเปิดการแสดงราคา (homepage_show_price = 1):</strong><br>
                    → หน้าแรกควรแสดง "8,500 ฿" (ราคาจริง)
                </li>
                <li>
                    <strong>ถ้าปิดข้อมูลส่วนตัว (model_detail_show_personal_info = 0):</strong><br>
                    → หน้ารายละเอียดไม่แสดง น้ำหนัก, ส่วนสูง, วันเกิด
                </li>
            </ul>
        </div>
        
        <!-- Actions -->
        <div style="text-align: center; margin: 40px 0;">
            <a href="admin/settings/price-display.php" class="btn">
                <i class="fas fa-cog me-2"></i>ไปตั้งค่าการแสดงราคา
            </a>
            <a href="admin/settings/model-detail.php" class="btn">
                <i class="fas fa-eye me-2"></i>ไปตั้งค่าหน้ารายละเอียด
            </a>
            <a href="/" class="btn">
                <i class="fas fa-home me-2"></i>ดูหน้าแรก
            </a>
            <a href="javascript:location.reload()" class="btn">
                <i class="fas fa-sync me-2"></i>รีเฟรช
            </a>
        </div>
        
        <!-- คำแนะนำ -->
        <div style="background: #fff3cd; padding: 20px; border-radius: 10px; border-left: 5px solid #ffc107; margin-top: 30px;">
            <h4 style="color: #856404; margin-bottom: 15px;">
                <i class="fas fa-lightbulb me-2"></i>วิธีแก้ปัญหา:
            </h4>
            <ol style="line-height: 2; color: #856404;">
                <li><strong>Hard Refresh:</strong> กด <code>Ctrl+Shift+R</code> (Windows) หรือ <code>Cmd+Shift+R</code> (Mac) เพื่อล้าง cache</li>
                <li><strong>ตรวจสอบ Database:</strong> เช็คว่าค่าใน settings ถูกบันทึกจริงหรือไม่</li>
                <li><strong>ตรวจสอบ Code:</strong> ดูว่า index.php ดึงค่า settings มาถูกต้องหรือไม่</li>
                <li><strong>Clear Browser Cache:</strong> ลบ cache ของเบราว์เซอร์</li>
            </ol>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>




