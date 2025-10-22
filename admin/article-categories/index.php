<?php
/**
 * VIBEDAYBKK Admin - Article Categories
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';

require_permission('article_categories', 'view');

$page_title = 'หมวดหมู่บทความ';
$current_page = 'article_categories';

$categories = db_get_rows($conn, "SELECT *, (SELECT COUNT(*) FROM articles WHERE category_id = article_categories.id) as article_count FROM article_categories ORDER BY sort_order");

include '../includes/header.php';
?>

<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-900">
        <i class="fas fa-folder mr-3 text-red-600"></i>หมวดหมู่บทความ
    </h2>
</div>

<div class="bg-white rounded-xl shadow-lg p-6">
    <?php if (has_permission('article_categories', 'create')): ?>
    <a href="add.php" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg mb-6">
        <i class="fas fa-plus mr-2"></i>เพิ่มหมวดหมู่
    </a>
    <?php endif; ?>
    
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">ชื่อ</th>
                <th class="px-4 py-3 text-left">Slug</th>
                <th class="px-4 py-3 text-center">บทความ</th>
                <th class="px-4 py-3 text-center">การกระทำ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
            <tr>
                <td class="px-4 py-3"><?php echo $cat['name']; ?></td>
                <td class="px-4 py-3"><?php echo $cat['slug']; ?></td>
                <td class="px-4 py-3 text-center"><?php echo $cat['article_count']; ?></td>
                <td class="px-4 py-3 text-center">
                    <?php if (has_permission('article_categories', 'edit')): ?>
                    <a href="edit.php?id=<?php echo $cat['id']; ?>" class="text-yellow-600 hover:text-yellow-700 mx-2">
                        <i class="fas fa-edit"></i>
                    </a>
                    <?php endif; ?>
                    <?php if (has_permission('article_categories', 'delete') && $cat['article_count'] == 0): ?>
                    <a href="delete.php?id=<?php echo $cat['id']; ?>" class="text-red-600 hover:text-red-700 btn-delete mx-2">
                        <i class="fas fa-trash"></i>
                    </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>


