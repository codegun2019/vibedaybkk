<?php
/**
 * Seed Models API - API สำหรับสร้างข้อมูลโมเดล
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

header('Content-Type: application/json');

// รับข้อมูล JSON
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

// ฟังก์ชันสร้าง slug (แปลงภาษาไทยเป็นภาษาอังกฤษ)
function createSlug($conn, $text) {
    // แปลงภาษาไทยเป็นภาษาอังกฤษ
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
    
    // แปลงเป็นตัวพิมพ์เล็ก
    $text = mb_strtolower($text, 'UTF-8');
    
    // แทนที่อักษรไทยด้วยภาษาอังกฤษ
    $slug = strtr($text, $thai_to_roman);
    
    // ลบอักขระที่เหลือที่ไม่ใช่ a-z, 0-9
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    
    // ลบขีดซ้ำซ้อน
    $slug = preg_replace('/-+/', '-', $slug);
    
    // ลบขีดหน้าหลัง
    $slug = trim($slug, '-');
    
    // ถ้า slug ว่างเปล่า ให้ใช้ model-timestamp
    if (empty($slug)) {
        $slug = 'model-' . time();
    }
    
    // ตรวจสอบว่า slug ซ้ำไหม
    $original_slug = $slug;
    $counter = 1;
    
    while (true) {
        $check = $conn->prepare("SELECT id FROM models WHERE slug = ?");
        $check->bind_param('s', $slug);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows === 0) {
            break;
        }
        
        $slug = $original_slug . '-' . $counter;
        $counter++;
        $check->close();
    }
    
    return $slug;
}

try {
    switch ($action) {
        case 'clear':
            // ลบโมเดลทั้งหมด
            $conn->query("DELETE FROM model_images");
            $conn->query("DELETE FROM model_requirements");
            $result = $conn->query("DELETE FROM models");
            
            echo json_encode([
                'success' => true,
                'deleted' => $conn->affected_rows
            ]);
            break;
            
        case 'count':
            // นับจำนวนโมเดล
            $result = $conn->query("SELECT COUNT(*) as total FROM models");
            $row = $result->fetch_assoc();
            
            echo json_encode([
                'success' => true,
                'total' => (int)$row['total']
            ]);
            break;
            
        case 'create':
            // สร้างโมเดล
            $category_id = (int)($input['category_id'] ?? 0);
            $name = $input['name'] ?? '';
            $price = (float)($input['price'] ?? 0);
            $height = (int)($input['height'] ?? 0);
            $weight = (int)($input['weight'] ?? 0);
            $birth_date = $input['birth_date'] ?? null;
            $experience = $input['experience'] ?? '';
            $portfolio = $input['portfolio'] ?? '';
            $description = $input['description'] ?? '';
            $featured_image = $input['featured_image'] ?? '';
            $status = $input['status'] ?? 'available';
            
            // ตรวจสอบคอลัมน์ที่มีในตาราง models
            $columns_result = $conn->query("SHOW COLUMNS FROM models");
            $existing_columns = [];
            while ($col = $columns_result->fetch_assoc()) {
                $existing_columns[] = $col['Field'];
            }
            
            // สร้าง slug และ code
            $has_slug = in_array('slug', $existing_columns);
            $has_code = in_array('code', $existing_columns);
            $slug = $has_slug ? createSlug($conn, $name) : '';
            $code = $has_code ? 'MDL' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT) : '';
            
            // Insert ข้อมูล (ปรับตามคอลัมน์ที่มีจริง)
            if ($has_code && $has_slug && in_array('featured_image', $existing_columns) && in_array('birth_date', $existing_columns)) {
                // กรณีมีครบทุกคอลัมน์ (รวม code)
                $stmt = $conn->prepare("
                    INSERT INTO models (
                        category_id, code, name, slug, featured_image, price, 
                        height, weight, birth_date, experience, portfolio, 
                        description, status, view_count, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW(), NOW())
                ");
                $stmt->bind_param(
                    'issssdissssss',
                    $category_id,
                    $code,
                    $name,
                    $slug,
                    $featured_image,
                    $price,
                    $height,
                    $weight,
                    $birth_date,
                    $experience,
                    $portfolio,
                    $description,
                    $status
                );
            } elseif ($has_slug && in_array('featured_image', $existing_columns) && in_array('birth_date', $existing_columns)) {
                // กรณีมีครบแต่ไม่มี code
                $stmt = $conn->prepare("
                    INSERT INTO models (
                        category_id, name, slug, featured_image, price, 
                        height, weight, birth_date, experience, portfolio, 
                        description, status, view_count, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW(), NOW())
                ");
                $stmt->bind_param(
                    'isssdissssss',
                    $category_id,
                    $name,
                    $slug,
                    $featured_image,
                    $price,
                    $height,
                    $weight,
                    $birth_date,
                    $experience,
                    $portfolio,
                    $description,
                    $status
                );
            } else {
                // กรณีเป็นโครงสร้างเดิม (ไม่มี slug, featured_image, birth_date)
                // ใช้คอลัมน์พื้นฐาน
                $stmt = $conn->prepare("
                    INSERT INTO models (
                        category_id, name, description, 
                        height, weight, status, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->bind_param(
                    'issiis',
                    $category_id,
                    $name,
                    $description,
                    $height,
                    $weight,
                    $status
                );
            }
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'id' => $conn->insert_id,
                    'code' => $code,
                    'slug' => $slug
                ]);
            } else {
                throw new Exception($stmt->error);
            }
            
            $stmt->close();
            break;
            
        default:
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

