<?php
/**
 * VIBEDAYBKK Admin - Gallery Upload Process
 * ประมวลผลการอัพโหลดรูปภาพ
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';
require_once '../includes/auth.php';

// Error handling
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

$response = [
    'success' => false,
    'error' => '',
    'uploaded' => [],
    'failed' => []
];

try {
    // Validate request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Verify CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        throw new Exception('Invalid CSRF token');
    }

    // Check if files were uploaded
    if (!isset($_FILES['images']) || empty($_FILES['images']['name'])) {
        throw new Exception('กรุณาเลือกรูปภาพ');
    }

    $album_id = !empty($_POST['album_id']) ? (int)$_POST['album_id'] : null;
    $user_id = $_SESSION['user_id'];

    // Validate album if specified
    if ($album_id) {
        $stmt = $conn->prepare("SELECT id FROM gallery_albums WHERE id = ? AND is_active = 1");
        $stmt->bind_param('i', $album_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception('ไม่พบอัลบั้มที่เลือก');
        }
        $stmt->close();
    }

    // Process uploaded files
    $files = $_FILES['images'];
    $fileCount = is_array($files['name']) ? count($files['name']) : 1;

    for ($i = 0; $i < $fileCount; $i++) {
        $fileName = is_array($files['name']) ? $files['name'][$i] : $files['name'];
        $fileTmpName = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
        $fileError = is_array($files['error']) ? $files['error'][$i] : $files['error'];
        $fileSize = is_array($files['size']) ? $files['size'][$i] : $files['size'];

        // Skip if no file
        if (empty($fileName) || $fileError === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        // Check for upload errors
        if ($fileError !== UPLOAD_ERR_OK) {
            $response['failed'][] = [
                'name' => $fileName,
                'error' => 'Upload error: ' . $fileError
            ];
            continue;
        }

        // Prepare file array for upload_image function
        $fileArray = [
            'name' => $fileName,
            'tmp_name' => $fileTmpName,
            'error' => $fileError,
            'size' => $fileSize
        ];

        try {
            // Upload image
            $uploadResult = upload_image($fileArray, 'gallery');

            if ($uploadResult['success']) {
                // Get max sort_order
                $maxOrder = db_get_row($conn, "SELECT MAX(sort_order) as max_order FROM gallery_images");
                $sortOrder = ($maxOrder['max_order'] ?? 0) + 1;
                
                // Insert into database
                $data = [
                    'title' => pathinfo($fileName, PATHINFO_FILENAME),
                    'image_path' => $uploadResult['file_path'],
                    'thumbnail_path' => $uploadResult['thumbnail_path'] ?? null,
                    'album_id' => $album_id,
                    'uploaded_by' => $user_id,
                    'sort_order' => $sortOrder,
                    'is_active' => 1
                ];

                if (db_insert($conn, 'gallery_images', $data)) {
                    $imageId = $conn->insert_id;
                    
                    // Log activity
                    log_activity($conn, $user_id, 'create', 'gallery_images', $imageId);

                    $response['uploaded'][] = [
                        'id' => $imageId,
                        'name' => $fileName,
                        'path' => $uploadResult['file_path']
                    ];
                } else {
                    // Delete uploaded file if database insert fails
                    if (file_exists(ROOT_PATH . '/' . $uploadResult['file_path'])) {
                        unlink(ROOT_PATH . '/' . $uploadResult['file_path']);
                    }
                    if (!empty($uploadResult['thumbnail_path']) && file_exists(ROOT_PATH . '/' . $uploadResult['thumbnail_path'])) {
                        unlink(ROOT_PATH . '/' . $uploadResult['thumbnail_path']);
                    }

                    $response['failed'][] = [
                        'name' => $fileName,
                        'error' => 'Database insert failed'
                    ];
                }
            } else {
                $response['failed'][] = [
                    'name' => $fileName,
                    'error' => $uploadResult['error'] ?? 'Upload failed'
                ];
            }
        } catch (Exception $e) {
            $response['failed'][] = [
                'name' => $fileName,
                'error' => $e->getMessage()
            ];
        }
    }

    // Set success if at least one file was uploaded
    if (count($response['uploaded']) > 0) {
        $response['success'] = true;
        $response['message'] = 'อัพโหลดสำเร็จ ' . count($response['uploaded']) . ' ไฟล์';
    } else {
        $response['error'] = 'ไม่สามารถอัพโหลดไฟล์ได้';
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['error'] = $e->getMessage();
    error_log('Gallery upload error: ' . $e->getMessage());
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>

