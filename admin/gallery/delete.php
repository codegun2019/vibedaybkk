<?php
/**
 * Delete Gallery Image
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

// Permission check
require_permission('gallery', 'delete');
header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$response = ['success' => false, 'message' => ''];

if ($id > 0) {
    try {
        // Get image info from gallery table
        $stmt = $conn->prepare("SELECT * FROM gallery WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $image = $result->fetch_assoc();
        $stmt->close();
        
        if ($image) {
            $deleted_files = [];
            $file_errors = [];
            
            // ลบไฟล์รูปภาพ
            if (!empty($image['image'])) {
                $image_path = $image['image'];
                
                // ตรวจสอบว่าเป็น URL ภายนอกหรือไม่
                if (!filter_var($image_path, FILTER_VALIDATE_URL)) {
                    // ถ้าไม่ใช่ URL ภายนอก ให้ลบไฟล์
                    $possible_paths = [
                        ROOT_PATH . '/' . $image_path,
                        ROOT_PATH . '/uploads/' . $image_path,
                        ROOT_PATH . '/uploads/' . basename($image_path)
                    ];
                    
                    foreach ($possible_paths as $path) {
                        if (file_exists($path) && is_file($path)) {
                            if (unlink($path)) {
                                $deleted_files[] = basename($path);
                            } else {
                                $file_errors[] = "ไม่สามารถลบ: " . basename($path);
                            }
                            break;
                        }
                    }
                }
            }
            
            // ลบ thumbnail ถ้ามี
            if (!empty($image['image'])) {
                $thumb_paths = [
                    ROOT_PATH . '/uploads/gallery/thumbs/thumb_' . basename($image['image']),
                    ROOT_PATH . '/uploads/thumbs/thumb_' . basename($image['image'])
                ];
                
                foreach ($thumb_paths as $thumb_path) {
                    if (file_exists($thumb_path) && is_file($thumb_path)) {
                        if (unlink($thumb_path)) {
                            $deleted_files[] = 'thumbnail: ' . basename($thumb_path);
                        }
                        break;
                    }
                }
            }
            
            // ลบข้อมูลจากฐานข้อมูล
            $stmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");
            $stmt->bind_param('i', $id);
            
            if ($stmt->execute()) {
                $stmt->close();
                
                // Log activity
                if (function_exists('log_activity')) {
                    log_activity($conn, $_SESSION['user_id'], 'delete', 'gallery', $id);
                }
                
                $response['success'] = true;
                $response['message'] = 'ลบรูปภาพสำเร็จ';
                
                if (!empty($deleted_files)) {
                    $response['deleted_files'] = $deleted_files;
                }
                
                if (!empty($file_errors)) {
                    $response['file_errors'] = $file_errors;
                }
            } else {
                $response['message'] = 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $stmt->error;
                $stmt->close();
            }
        } else {
            $response['message'] = 'ไม่พบรูปภาพ (ID: ' . $id . ')';
        }
    } catch (Exception $e) {
        $response['message'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'ID ไม่ถูกต้อง';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
$conn->close();
?>