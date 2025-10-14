<?php
/**
 * Update Features Order
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_admin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!verify_csrf_token($data['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }
    
    if (isset($data['order']) && is_array($data['order'])) {
        foreach ($data['order'] as $item) {
            $id = (int)$item['id'];
            $sort_order = (int)$item['sort_order'];
            
            db_execute($conn, "UPDATE homepage_features SET sort_order = ? WHERE id = ?", [$sort_order, $id]);
        }
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

