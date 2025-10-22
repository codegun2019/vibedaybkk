<?php
/**
 * สร้าง How to Book Section ในฐานข้อมูล (เวอร์ชันง่าย)
 */

// การตั้งค่าฐานข้อมูล
$host = 'localhost';
$dbname = 'vibedaybkk';
$username = 'root';
$password = 'root';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🚀 กำลังสร้าง How to Book Section...</h2>";
    
    // ตรวจสอบว่า section มีอยู่แล้วหรือไม่
    $stmt = $conn->prepare("SELECT * FROM homepage_sections WHERE section_key = 'how-to-book'");
    $stmt->execute();
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        echo "<h3>✅ How to Book Section มีอยู่แล้ว!</h3>";
        echo "<p><strong>Section ID:</strong> " . $existing['id'] . "</p>";
        echo "<p><strong>Title:</strong> " . htmlspecialchars($existing['title']) . "</p>";
        echo "<p><strong>Sort Order:</strong> " . $existing['sort_order'] . "</p>";
        echo "<p><strong>Active:</strong> " . ($existing['is_active'] ? '✅ เปิดใช้งาน' : '❌ ปิดใช้งาน') . "</p>";
        echo "<p><a href='admin/homepage/edit.php?id={$existing['id']}' target='_blank' style='background: #DC2626; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>แก้ไข How to Book Section</a></p>";
        echo "<p><a href='index.php#how-to-book' target='_blank' style='background: #059669; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>ดูผลลัพธ์ในหน้าแรก</a></p>";
    } else {
        echo "<h3>🔧 กำลังสร้าง How to Book Section ใหม่...</h3>";
        
        // เพิ่ม columns ที่อาจไม่มี (ใช้วิธีตรวจสอบก่อน)
        $columns_to_add = [
            'right_image' => 'VARCHAR(255) AFTER left_image',
            'steps' => 'TEXT AFTER right_image',
            'settings' => 'TEXT AFTER steps'
        ];
        
        foreach ($columns_to_add as $column_name => $column_definition) {
            try {
                // ตรวจสอบว่าคอลัมน์มีอยู่หรือไม่
                $check_sql = "SHOW COLUMNS FROM homepage_sections LIKE '$column_name'";
                $check_stmt = $conn->query($check_sql);
                
                if ($check_stmt->rowCount() == 0) {
                    // คอลัมน์ไม่มี ให้เพิ่ม
                    $add_sql = "ALTER TABLE homepage_sections ADD COLUMN $column_name $column_definition";
                    $conn->exec($add_sql);
                    echo "<p>✅ เพิ่มคอลัมน์ '$column_name' สำเร็จ</p>";
                } else {
                    echo "<p>ℹ️ คอลัมน์ '$column_name' มีอยู่แล้ว</p>";
                }
            } catch (PDOException $e) {
                echo "<p>⚠️ ข้อผิดพลาดในการจัดการคอลัมน์ '$column_name': " . $e->getMessage() . "</p>";
            }
        }
        
        // สร้าง How to Book section ใหม่
        $default_steps = [
            [
                'title' => 'เลือกบริการ',
                'description' => 'เลือกประเภทโมเดลและบริการที่ต้องการ'
            ],
            [
                'title' => 'ติดต่อเรา',
                'description' => 'ติดต่อผ่าน Line หรือโทรศัพท์เพื่อปรึกษารายละเอียด'
            ],
            [
                'title' => 'ยืนยันการจอง',
                'description' => 'ยืนยันรายละเอียดและชำระเงินมัดจำ'
            ],
            [
                'title' => 'เริ่มงาน',
                'description' => 'โมเดลจะมาถึงสถานที่ตามเวลาที่กำหนด'
            ]
        ];
        
        // ตรวจสอบว่าคอลัมน์มีอยู่หรือไม่ก่อนสร้าง SQL
        $available_columns = [];
        $column_checks = ['left_image', 'right_image', 'steps', 'settings'];
        
        foreach ($column_checks as $column) {
            try {
                $check_sql = "SHOW COLUMNS FROM homepage_sections LIKE '$column'";
                $check_stmt = $conn->query($check_sql);
                if ($check_stmt->rowCount() > 0) {
                    $available_columns[] = $column;
                }
            } catch (PDOException $e) {
                echo "<p>⚠️ ไม่สามารถตรวจสอบคอลัมน์ '$column': " . $e->getMessage() . "</p>";
            }
        }
        
        // สร้าง SQL แบบ dynamic ตามคอลัมน์ที่มีอยู่
        $base_columns = [
            'section_key', 'title', 'subtitle', 'content', 
            'button1_text', 'button1_link', 'button2_text', 'button2_link',
            'background_type', 'background_color', 'background_image',
            'background_position', 'background_size', 'background_repeat', 'background_attachment',
            'sort_order', 'is_active',
            'created_at', 'updated_at'
        ];
        
        $all_columns = array_merge($base_columns, $available_columns);
        $columns_str = implode(', ', $all_columns);
        $placeholders = ':' . implode(', :', $all_columns);
        
        $sql = "INSERT INTO homepage_sections ($columns_str) VALUES ($placeholders)";
        
        // เตรียมข้อมูลสำหรับ INSERT
        $data = [
            ':section_key' => 'how-to-book',
            ':title' => 'วิธีการจองบริการ',
            ':subtitle' => '',
            ':content' => 'ขั้นตอนการจองบริการที่ง่ายและรวดเร็ว เพียง 4 ขั้นตอนคุณก็สามารถจองโมเดลมืออาชีพได้แล้ว',
            ':button1_text' => 'เริ่มจองเลย',
            ':button1_link' => '#contact',
            ':button2_text' => '',
            ':button2_link' => '',
            ':background_type' => 'color',
            ':background_color' => '',
            ':background_image' => '',
            ':background_position' => 'center',
            ':background_size' => 'cover',
            ':background_repeat' => 'no-repeat',
            ':background_attachment' => 'scroll',
            ':sort_order' => 40,
            ':is_active' => 1,
            ':created_at' => date('Y-m-d H:i:s'),
            ':updated_at' => date('Y-m-d H:i:s')
        ];
        
        // เพิ่มข้อมูลสำหรับคอลัมน์ที่เลือกได้
        if (in_array('left_image', $available_columns)) {
            $data[':left_image'] = '';
        }
        if (in_array('right_image', $available_columns)) {
            $data[':right_image'] = '';
        }
        if (in_array('steps', $available_columns)) {
            $data[':steps'] = json_encode($default_steps, JSON_UNESCAPED_UNICODE);
        }
        if (in_array('settings', $available_columns)) {
            $data[':settings'] = '';
        }
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute($data);
        
        if ($result) {
            $new_id = $conn->lastInsertId();
            echo "<h3>✅ สร้าง How to Book Section สำเร็จ!</h3>";
            echo "<p><strong>Section ID:</strong> " . $new_id . "</p>";
            echo "<p><strong>Title:</strong> วิธีการจองบริการ</p>";
            echo "<p><strong>Steps:</strong> " . count($default_steps) . " ขั้นตอน</p>";
            echo "<p><a href='admin/homepage/edit.php?id={$new_id}' target='_blank' style='background: #DC2626; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>แก้ไข How to Book Section</a></p>";
            echo "<p><a href='index.php#how-to-book' target='_blank' style='background: #059669; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>ดูผลลัพธ์ในหน้าแรก</a></p>";
        } else {
            echo "<h3>❌ เกิดข้อผิดพลาด!</h3>";
            echo "<p>ไม่สามารถสร้าง section ได้</p>";
        }
    }
    
    // แสดงข้อมูล Homepage Sections ทั้งหมด
    echo "<hr>";
    echo "<h3>📋 ข้อมูล Homepage Sections ทั้งหมด:</h3>";
    
    $stmt = $conn->prepare("SELECT * FROM homepage_sections ORDER BY sort_order");
    $stmt->execute();
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($sections)) {
        echo "<p>❌ ไม่พบข้อมูล sections</p>";
    } else {
        echo "<table style='width: 100%; border-collapse: collapse; margin-top: 20px;'>";
        echo "<tr style='background: #DC2626; color: white;'>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>ID</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Section Key</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Title</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Sort Order</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Active</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Actions</th>";
        echo "</tr>";
        
        foreach ($sections as $section) {
            $active_text = $section['is_active'] ? '✅' : '❌';
            echo "<tr style='border-bottom: 1px solid #ddd;'>";
            echo "<td style='padding: 12px; border: 1px solid #ddd;'>{$section['id']}</td>";
            echo "<td style='padding: 12px; border: 1px solid #ddd;'>{$section['section_key']}</td>";
            echo "<td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($section['title']) . "</td>";
            echo "<td style='padding: 12px; border: 1px solid #ddd;'>{$section['sort_order']}</td>";
            echo "<td style='padding: 12px; border: 1px solid #ddd;'>{$active_text}</td>";
            echo "<td style='padding: 12px; border: 1px solid #ddd;'>";
            echo "<a href='admin/homepage/edit.php?id={$section['id']}' target='_blank' style='background: #DC2626; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 12px;'>แก้ไข</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (PDOException $e) {
    echo "<h3>❌ เกิดข้อผิดพลาด!</h3>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>แก้ไข:</strong> ตรวจสอบการตั้งค่าฐานข้อมูลใน MAMP</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    padding: 20px;
    background: #f5f5f5;
    line-height: 1.6;
    max-width: 1200px;
    margin: 0 auto;
}
h2, h3 {
    color: #333;
    margin: 20px 0 10px;
}
table {
    background: #fff;
    border-radius: 5px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-top: 20px;
}
th, td {
    padding: 12px;
    text-align: left;
    border: 1px solid #ddd;
}
th {
    background: #DC2626;
    color: white;
    font-weight: bold;
}
tr:hover {
    background: #f9f9f9;
}
a {
    color: white;
    text-decoration: none;
    font-weight: bold;
}
a:hover {
    opacity: 0.8;
}
hr {
    border: none;
    border-top: 2px solid #DC2626;
    margin: 30px 0;
}
</style>
