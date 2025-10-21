<?php
/**
 * VIBEDAYBKK Admin - Delete Category
 * ลบหมวดหมู่
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';


// Permission check
require_permission('categories', 'delete');
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$category_id) {
    set_message('error', 'ไม่พบข้อมูลหมวดหมู่');
    redirect(ADMIN_URL . '/categories/');
}

$category = get_category($conn, $category_id);
if (!$category) {
    set_message('error', 'ไม่พบข้อมูลหมวดหมู่');
    redirect(ADMIN_URL . '/categories/');
}

// Check if category has models
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM models WHERE category_id = ?");
$stmt->bind_param('i', $category_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$model_count = $row['total'];
$stmt->close();

if ($model_count > 0) {
    set_message('error', 'ไม่สามารถลบหมวดหมู่ที่มีโมเดลอยู่ได้ (มี ' . $model_count . ' คน)');
    redirect(ADMIN_URL . '/categories/');
}

// Delete category
if (db_delete($conn, 'categories', 'id = ?', [$category_id])) {
    log_activity($conn, $_SESSION['user_id'], 'delete', 'categories', $category_id, $category);
    set_message('success', 'ลบหมวดหมู่ "' . $category['name'] . '" สำเร็จ');
} else {
    set_message('error', 'เกิดข้อผิดพลาดในการลบข้อมูล');
}

redirect(ADMIN_URL . '/categories/');
?>




