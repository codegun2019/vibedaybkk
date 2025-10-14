<?php
/**
 * Delete Gallery Image
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$section_id = isset($_GET['section_id']) ? (int)$_GET['section_id'] : 0;

// Get image info
$image = db_get_row($conn, "SELECT * FROM homepage_gallery WHERE id = ?", [$id]);

if ($image) {
    // Delete image file
    delete_image($image['image_path']);
    
    // Delete from database
    db_execute($conn, "DELETE FROM homepage_gallery WHERE id = ?", [$id]);
}

header('Location: gallery.php?section_id=' . $section_id);
exit;

