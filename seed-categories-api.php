<?php
/**
 * Seed Categories API - API สำหรับสร้างหมวดหมู่
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

header('Content-Type: application/json');

// รับข้อมูล JSON
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

// ฟังก์ชันแปลงภาษาไทยเป็น slug
function createSlugFromThai($text, $id = '') {
    $thai_to_roman = [
        'ก' => 'k', 'ข' => 'kh', 'ค' => 'kh', 'ฆ' => 'kh',
        'ง' => 'ng', 'จ' => 'ch', 'ฉ' => 'ch', 'ช' => 'ch',
        'ซ' => 's', 'ฌ' => 'ch', 'ญ' => 'y', 'ฎ' => 'd',
        'ฏ' => 't', 'ฐ' => 'th', 'ฑ' => 'th', 'ฒ' => 'th',
        'ณ' => 'n', 'ด' => 'd', 'ต' => 't', 'ถ' => 'th',
        'ท' => 'th', 'ธ' => 'th', 'น' => 'n', 'บ' => 'b',
        'ป' => 'p', 'ผ' => 'ph', 'ฝ' => 'f', 'พ' => 'ph',
        'ฟ' => 'f', 'ภ' => 'ph', 'ม' => 'm', 'ย' => 'y',
        'ร' => 'r', 'ล' => 'l', 'ว' => 'w', 'ศ' => 's',
        'ษ' => 's', 'ส' => 's', 'ห' => 'h', 'ฬ' => 'l',
        'อ' => 'o', 'ฮ' => 'h',
        'ะ' => 'a', 'ั' => 'a', 'า' => 'a', 'ำ' => 'am',
        'ิ' => 'i', 'ี' => 'i', 'ึ' => 'ue', 'ื' => 'ue',
        'ุ' => 'u', 'ู' => 'u', 'เ' => 'e', 'แ' => 'ae',
        'โ' => 'o', 'ใ' => 'ai', 'ไ' => 'ai',
        '่' => '', '้' => '', '๊' => '', '๋' => '', '์' => '',
    ];
    
    $text = mb_strtolower($text, 'UTF-8');
    $slug = strtr($text, $thai_to_roman);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    
    if (empty($slug)) {
        $slug = 'item';
    }
    
    return $id ? $slug . '-' . $id : $slug;
}

// ข้อมูลหมวดหมู่
$categories = [
    [
        'name' => 'นางแบบ',
        'slug' => 'nang-baeb',
        'description' => 'นางแบบมืออาชีพสำหรับงานแฟชั่น โฆษณา และถ่ายภาพ',
        'icon' => 'fa-user-tie',
        'image' => 'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?w=800',
        'price_range' => '3,000-15,000 บาท',
        'sort_order' => 1,
        'status' => 'active'
    ],
    [
        'name' => 'พรีเซ็นเตอร์',
        'slug' => 'presenter',
        'description' => 'พรีเซ็นเตอร์สินค้า Brand Ambassador และงานอีเวนต์',
        'icon' => 'fa-star',
        'image' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=800',
        'price_range' => '5,000-20,000 บาท',
        'sort_order' => 2,
        'status' => 'active'
    ],
    [
        'name' => 'โมเดลผู้ชาย',
        'slug' => 'model-phu-chai',
        'description' => 'โมเดลผู้ชายสำหรับงานแฟชั่นและโฆษณา',
        'icon' => 'fa-male',
        'image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=800',
        'price_range' => '4,000-18,000 บาท',
        'sort_order' => 3,
        'status' => 'active'
    ],
    [
        'name' => 'Kids Model',
        'slug' => 'kids-model',
        'description' => 'โมเดลเด็กสำหรับงานโฆษณาและถ่ายภาพ',
        'icon' => 'fa-child',
        'image' => 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?w=800',
        'price_range' => '3,000-12,000 บาท',
        'sort_order' => 4,
        'status' => 'active'
    ],
    [
        'name' => 'Fitness Model',
        'slug' => 'fitness-model',
        'description' => 'โมเดลฟิตเนสสำหรับแบรนด์กีฬาและสุขภาพ',
        'icon' => 'fa-dumbbell',
        'image' => 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=800',
        'price_range' => '5,000-15,000 บาท',
        'sort_order' => 5,
        'status' => 'active'
    ],
    [
        'name' => 'Plus Size Model',
        'slug' => 'plus-size-model',
        'description' => 'โมเดล Plus Size สำหรับแบรนด์แฟชั่นทุกไซส์',
        'icon' => 'fa-heart',
        'image' => 'https://images.unsplash.com/photo-1583391733956-6c78276477e2?w=800',
        'price_range' => '4,000-15,000 บาท',
        'sort_order' => 6,
        'status' => 'active'
    ],
    [
        'name' => 'MC & พิธีกร',
        'slug' => 'mc-phithikorn',
        'description' => 'MC และพิธีกรมืออาชีพสำหรับงานอีเวนต์',
        'icon' => 'fa-microphone',
        'image' => 'https://images.unsplash.com/photo-1475483768296-6163e08872a1?w=800',
        'price_range' => '8,000-25,000 บาท',
        'sort_order' => 7,
        'status' => 'active'
    ],
    [
        'name' => 'Commercial Model',
        'slug' => 'commercial-model',
        'description' => 'โมเดลสำหรับงานโฆษณา TVC และสื่อดิจิทัล',
        'icon' => 'fa-video',
        'image' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=800',
        'price_range' => '6,000-30,000 บาท',
        'sort_order' => 8,
        'status' => 'active'
    ]
];

try {
    if ($action === 'create_all') {
        $created = 0;
        $skipped = 0;
        
        // ตรวจสอบคอลัมน์ที่มีในตาราง
        $columns_check = $conn->query("SHOW COLUMNS FROM categories");
        $has_slug = false;
        while ($col = $columns_check->fetch_assoc()) {
            if ($col['Field'] === 'slug') {
                $has_slug = true;
                break;
            }
        }
        
        foreach ($categories as $cat) {
            // ตรวจสอบว่ามีอยู่แล้วไหม
            if ($has_slug) {
                $check = $conn->prepare("SELECT id FROM categories WHERE slug = ?");
                $check->bind_param('s', $cat['slug']);
            } else {
                $check = $conn->prepare("SELECT id FROM categories WHERE name = ?");
                $check->bind_param('s', $cat['name']);
            }
            $check->execute();
            $result = $check->get_result();
            
            if ($result->num_rows > 0) {
                $skipped++;
                $check->close();
                continue;
            }
            $check->close();
            
            // ตรวจสอบว่าตารางมีคอลัมน์อะไรบ้าง
            $columns_result = $conn->query("SHOW COLUMNS FROM categories");
            $existing_columns = [];
            while ($col = $columns_result->fetch_assoc()) {
                $existing_columns[] = $col['Field'];
            }
            
            // สร้างหมวดหมู่ใหม่ (ปรับตามคอลัมน์ที่มีจริง)
            if (in_array('slug', $existing_columns) && in_array('image', $existing_columns)) {
                // กรณีมีทั้ง slug และ image
                $stmt = $conn->prepare("
                    INSERT INTO categories (
                        name, slug, description, icon, image, 
                        price_range, sort_order, status, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->bind_param(
                    'ssssssss',
                    $cat['name'],
                    $cat['slug'],
                    $cat['description'],
                    $cat['icon'],
                    $cat['image'],
                    $cat['price_range'],
                    $cat['sort_order'],
                    $cat['status']
                );
            } elseif (in_array('slug', $existing_columns)) {
                // กรณีมีเฉพาะ slug
                $stmt = $conn->prepare("
                    INSERT INTO categories (
                        name, slug, description, icon, 
                        price_range, sort_order, status, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->bind_param(
                    'sssssss',
                    $cat['name'],
                    $cat['slug'],
                    $cat['description'],
                    $cat['icon'],
                    $cat['price_range'],
                    $cat['sort_order'],
                    $cat['status']
                );
            } else {
                // กรณีไม่มี slug (ใช้โครงสร้างเดิม)
                $stmt = $conn->prepare("
                    INSERT INTO categories (
                        name, description, icon, 
                        price_range, sort_order, status, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->bind_param(
                    'ssssss',
                    $cat['name'],
                    $cat['description'],
                    $cat['icon'],
                    $cat['price_range'],
                    $cat['sort_order'],
                    $cat['status']
                );
            }
            
            if ($stmt->execute()) {
                $created++;
            }
            
            $stmt->close();
        }
        
        // นับจำนวนหมวดหมู่ทั้งหมด
        $count_result = $conn->query("SELECT COUNT(*) as total FROM categories");
        $total = $count_result->fetch_assoc()['total'];
        
        echo json_encode([
            'success' => true,
            'created' => $created,
            'skipped' => $skipped,
            'total' => (int)$total
        ]);
    } else {
        throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conn->close();

