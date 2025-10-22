<?php
/**
 * API สำหรับเพิ่มหมวดหมู่บทความ
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

// อ่านข้อมูล JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['action'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

// ตรวจสอบว่าตาราง article_categories มีอยู่หรือไม่
$table_check = $conn->query("SHOW TABLES LIKE 'article_categories'");
if ($table_check->num_rows === 0) {
    // สร้างตาราง article_categories
    $create_table = "CREATE TABLE IF NOT EXISTS article_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        name_en VARCHAR(255),
        slug VARCHAR(255) UNIQUE NOT NULL,
        description TEXT,
        icon VARCHAR(100),
        color VARCHAR(50),
        image VARCHAR(255),
        parent_id INT DEFAULT NULL,
        sort_order INT DEFAULT 0,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (parent_id) REFERENCES article_categories(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if (!$conn->query($create_table)) {
        echo json_encode(['success' => false, 'error' => 'Failed to create table: ' . $conn->error]);
        exit;
    }
}

// ฟังก์ชันสร้าง slug
function createSlug($text) {
    // แปลงตัวอักษรไทยเป็นภาษาอังกฤษ (Romanization)
    $thai_to_eng = array(
        'ก' => 'k', 'ข' => 'kh', 'ฃ' => 'kh', 'ค' => 'kh', 'ฅ' => 'kh', 'ฆ' => 'kh',
        'ง' => 'ng', 'จ' => 'ch', 'ฉ' => 'ch', 'ช' => 'ch', 'ซ' => 's', 'ฌ' => 'ch',
        'ญ' => 'y', 'ฎ' => 'd', 'ฏ' => 't', 'ฐ' => 'th', 'ฑ' => 'th', 'ฒ' => 'th',
        'ณ' => 'n', 'ด' => 'd', 'ต' => 't', 'ถ' => 'th', 'ท' => 'th', 'ธ' => 'th',
        'น' => 'n', 'บ' => 'b', 'ป' => 'p', 'ผ' => 'ph', 'ฝ' => 'f', 'พ' => 'ph',
        'ฟ' => 'f', 'ภ' => 'ph', 'ม' => 'm', 'ย' => 'y', 'ร' => 'r', 'ฤ' => 'rue',
        'ล' => 'l', 'ฦ' => 'lue', 'ว' => 'w', 'ศ' => 's', 'ษ' => 's', 'ส' => 's',
        'ห' => 'h', 'ฬ' => 'l', 'อ' => 'o', 'ฮ' => 'h',
        'ะ' => 'a', 'ั' => 'a', 'า' => 'a', 'ำ' => 'am', 'ิ' => 'i', 'ี' => 'i',
        'ึ' => 'ue', 'ื' => 'ue', 'ุ' => 'u', 'ู' => 'u',
        'เ' => 'e', 'แ' => 'ae', 'โ' => 'o', 'ใ' => 'ai', 'ไ' => 'ai',
        '็' => '', '่' => '', '้' => '', '์' => '', 'ํ' => '', 'ๆ' => '',
        '๐' => '0', '๑' => '1', '๒' => '2', '๓' => '3', '๔' => '4',
        '๕' => '5', '๖' => '6', '๗' => '7', '๘' => '8', '๙' => '9',
        ' ' => '-', '_' => '-'
    );
    
    $text = strtr($text, $thai_to_eng);
    $text = preg_replace('/[^a-zA-Z0-9\-]/', '', $text);
    $text = preg_replace('/-+/', '-', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    
    return $text;
}

if ($data['action'] === 'delete_all') {
    // ลบหมวดหมู่ทั้งหมด
    $result = $conn->query("SELECT COUNT(*) as count FROM article_categories");
    $row = $result->fetch_assoc();
    $deleted_count = $row['count'];
    
    $conn->query("DELETE FROM article_categories");
    $conn->query("ALTER TABLE article_categories AUTO_INCREMENT = 1");
    
    echo json_encode([
        'success' => true,
        'deleted_count' => $deleted_count
    ]);
    exit;
}

if ($data['action'] === 'add') {
    $category = $data['category'];
    
    // ตรวจสอบว่าตารางมีคอลัมน์อะไรบ้าง
    $columns_result = $conn->query("DESCRIBE article_categories");
    $existing_columns = [];
    while ($col = $columns_result->fetch_assoc()) {
        $existing_columns[] = $col['Field'];
    }
    
    $name = $category['name'];
    $name_en = $category['name_en'] ?? '';
    $slug = $category['slug'];
    $description = $category['description'] ?? '';
    $icon = $category['icon'] ?? '';
    $color = $category['color'] ?? '';
    $sort_order = $category['sort_order'] ?? 0;
    $status = 'active';
    
    // ถ้า slug ว่างหรือเป็นภาษาไทย ให้สร้างใหม่
    if (empty($slug) || preg_match('/[\x{0E00}-\x{0E7F}]/u', $slug)) {
        $slug = createSlug($name);
    }
    
    // ตรวจสอบว่า slug ซ้ำหรือไม่
    $check_stmt = $conn->prepare("SELECT id FROM article_categories WHERE slug = ?");
    $check_stmt->bind_param("s", $slug);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // ถ้า slug ซ้ำ ให้เพิ่มตัวเลขต่อท้าย
        $counter = 1;
        $original_slug = $slug;
        do {
            $slug = $original_slug . '-' . $counter;
            $check_stmt = $conn->prepare("SELECT id FROM article_categories WHERE slug = ?");
            $check_stmt->bind_param("s", $slug);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            $counter++;
        } while ($check_result->num_rows > 0);
    }
    
    try {
        // สร้าง SQL query ตามคอลัมน์ที่มีจริง
        $fields = ['name', 'slug', 'status'];
        $values = [$name, $slug, $status];
        $types = 'sss';
        
        if (in_array('name_en', $existing_columns)) {
            $fields[] = 'name_en';
            $values[] = $name_en;
            $types .= 's';
        }
        
        if (in_array('description', $existing_columns)) {
            $fields[] = 'description';
            $values[] = $description;
            $types .= 's';
        }
        
        if (in_array('icon', $existing_columns)) {
            $fields[] = 'icon';
            $values[] = $icon;
            $types .= 's';
        }
        
        if (in_array('color', $existing_columns)) {
            $fields[] = 'color';
            $values[] = $color;
            $types .= 's';
        }
        
        if (in_array('sort_order', $existing_columns)) {
            $fields[] = 'sort_order';
            $values[] = $sort_order;
            $types .= 'i';
        }
        
        $fields_str = implode(', ', $fields);
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        
        $sql = "INSERT INTO article_categories ($fields_str) VALUES ($placeholders)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'id' => $conn->insert_id,
                'name' => $name,
                'slug' => $slug
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => $stmt->error
            ]);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid action']);
$conn->close();
?>

