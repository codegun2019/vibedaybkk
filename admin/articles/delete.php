<?php
/**
 * VIBEDAYBKK Admin - Delete Article
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_permission('articles', 'delete');

$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($article_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->bind_param('i', $article_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $article = $result->fetch_assoc();
    $stmt->close();
    
    if ($article) {
        // Delete featured image
        if (!empty($article['featured_image']) && file_exists(ROOT_PATH . '/' . $article['featured_image'])) {
            @unlink(ROOT_PATH . '/' . $article['featured_image']);
        }
        
        db_delete($conn, 'articles', 'id = ?', [$article_id]);
        
        log_activity($conn, $_SESSION['user_id'], 'delete', 'articles', $article_id, json_encode($article), null);
        
        set_message('success', 'ลบบทความสำเร็จ');
    } else {
        set_message('error', 'ไม่พบข้อมูลบทความ');
    }
} else {
    set_message('error', 'ข้อมูลไม่ถูกต้อง');
}

redirect(ADMIN_URL . '/articles/');
?>




