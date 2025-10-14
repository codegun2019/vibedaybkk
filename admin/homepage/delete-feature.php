<?php
/**
 * Delete Feature
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$section_id = isset($_GET['section_id']) ? (int)$_GET['section_id'] : 0;

// Delete from database
db_execute($conn, "DELETE FROM homepage_features WHERE id = ?", [$id]);

header('Location: features.php?section_id=' . $section_id);
exit;

