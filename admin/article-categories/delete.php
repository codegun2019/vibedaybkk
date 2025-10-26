<?php
/**
 * VIBEDAYBKK Admin - Delete Article Category
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_permission('article_categories', 'delete');

$id = (int)$_GET['id'] ?? 0;

if ($id > 0) {
    // Check if has articles
    $count = $conn->query("SELECT COUNT(*) as c FROM articles WHERE category_id = {$id}")->fetch_assoc();
    
    if ($count['c'] > 0) {
        set_message('error', 'ไม่สามารถลบได้ มีบทความอยู่ในหมวดหมู่นี้');
    } else {
        $stmt = $conn->prepare("SELECT * FROM article_categories WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cat = $result->fetch_assoc();
        $stmt->close();
        
        if ($cat) {
            db_delete($conn, 'article_categories', 'id = ?', [$id]);
            log_activity($conn, $_SESSION['user_id'], 'delete', 'article_categories', $id, json_encode($cat));
            set_message('success', 'ลบหมวดหมู่สำเร็จ');
        }
    }
}

redirect(ADMIN_URL . '/article-categories/');
?>




