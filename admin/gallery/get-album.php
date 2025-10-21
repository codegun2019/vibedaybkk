<?php
define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$response = ['success' => false];

if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM gallery_albums WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $album = $result->fetch_assoc();
    $stmt->close();
    
    if ($album) {
        $response['success'] = true;
        $response['album'] = $album;
    }
}

echo json_encode($response);




