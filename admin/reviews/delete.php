<?php
/**
 * VIBEDAYBKK Admin - Delete Review
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$id = (int)$_GET['id'] ?? 0;

if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM customer_reviews WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $review = $result->fetch_assoc();
    $stmt->close();
    
    if ($review) {
        // Delete image
        if (!empty($review['image']) && file_exists(ROOT_PATH . '/' . $review['image'])) {
            @unlink(ROOT_PATH . '/' . $review['image']);
        }
        
        db_delete($conn, 'customer_reviews', 'id = ?', [$id]);
        log_activity($conn, $_SESSION['user_id'], 'delete', 'customer_reviews', $id);
        set_message('success', 'ลบรีวิวสำเร็จ');
    }
}

redirect(ADMIN_URL . '/reviews/');
?>




