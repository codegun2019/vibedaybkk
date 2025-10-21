<?php
/**
 * Get Image Details
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$response = ['success' => false];

if ($id > 0) {
    $stmt = $conn->prepare("
        SELECT gi.*, 
               ga.name as album_name,
               u.username as uploaded_by_name
        FROM gallery_images gi
        LEFT JOIN gallery_albums ga ON gi.album_id = ga.id
        LEFT JOIN users u ON gi.uploaded_by = u.id
        WHERE gi.id = ?
    ");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $image = $result->fetch_assoc();
    $stmt->close();
    
    if ($image) {
        $image['file_path'] = BASE_URL . '/' . $image['file_path'];
        $response['success'] = true;
        $response['image'] = $image;
    }
}

echo json_encode($response);




