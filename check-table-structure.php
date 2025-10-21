<?php
/**
 * ตรวจสอบโครงสร้างตารางทั้งหมด
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

$tables_to_check = ['categories', 'models', 'articles', 'article_categories', 'menus', 'users', 'settings'];

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔍 ตรวจสอบโครงสร้างตาราง</title>
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
        h1 { color: #667eea; text-align: center; margin-bottom: 30px; }
        .table-section {
            margin: 30px 0;
            border: 2px solid #667eea;
            border-radius: 15px;
            overflow: hidden;
        }
        .table-header {
            background: #667eea;
            color: white;
            padding: 20px;
            font-size: 1.5em;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        .field-name { color: #667eea; font-weight: bold; font-family: monospace; }
        .type { color: #059669; font-family: monospace; }
        .error { background: #fee; color: #c00; padding: 20px; border-radius: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 ตรวจสอบโครงสร้างตารางทั้งหมด</h1>
        
        <?php foreach ($tables_to_check as $table): ?>
            <div class="table-section">
                <div class="table-header">📋 <?php echo $table; ?></div>
                
                <?php
                try {
                    $result = $conn->query("SHOW COLUMNS FROM `{$table}`");
                    
                    if ($result && $result->num_rows > 0) {
                        echo '<table>';
                        echo '<thead><tr>';
                        echo '<th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th>';
                        echo '</tr></thead><tbody>';
                        
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td class="field-name">' . htmlspecialchars($row['Field']) . '</td>';
                            echo '<td class="type">' . htmlspecialchars($row['Type']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['Null']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['Key']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['Default'] ?? 'NULL') . '</td>';
                            echo '<td>' . htmlspecialchars($row['Extra']) . '</td>';
                            echo '</tr>';
                        }
                        
                        echo '</tbody></table>';
                    } else {
                        echo '<div class="error">⚠️ ไม่พบตาราง ' . $table . '</div>';
                    }
                } catch (Exception $e) {
                    echo '<div class="error">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
                ?>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>

