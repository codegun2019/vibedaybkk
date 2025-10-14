<?php
/**
 * VIBEDAYBKK Admin - Delete Article
 * ลบบทความ
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$article_id) {
    set_message('error', 'ไม่พบข้อมูลบทความ');
    redirect(ADMIN_URL . '/articles/');
}

$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch();

if (!$article) {
    set_message('error', 'ไม่พบข้อมูลบทความ');
    redirect(ADMIN_URL . '/articles/');
}

// Delete featured image
if ($article['featured_image']) {
    delete_image($article['featured_image']);
}

// Delete article
if (db_delete($pdo, 'articles', 'id = :id', ['id' => $article_id])) {
    log_activity($pdo, $_SESSION['user_id'], 'delete', 'articles', $article_id, $article);
    set_message('success', 'ลบบทความ "' . $article['title'] . '" สำเร็จ');
} else {
    set_message('error', 'เกิดข้อผิดพลาด');
}

redirect(ADMIN_URL . '/articles/');
?>

