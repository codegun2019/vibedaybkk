<?php
define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE gallery_images SET view_count = view_count + 1 WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

echo json_encode(['success' => true]);




