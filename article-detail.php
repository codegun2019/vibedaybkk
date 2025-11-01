<?php
/**
 * VIBEDAYBKK - Article Detail Page
 * หน้ารายละเอียดบทความ
 */
define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get article slug from URL
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (empty($slug)) {
    header('Location: articles.php');
    exit;
}

// Fetch article
$stmt = $conn->prepare("
    SELECT a.*, ac.name as category_name, ac.color as category_color, ac.icon as category_icon,
           u.full_name as author_name
    FROM articles a
    LEFT JOIN article_categories ac ON a.category_id = ac.id
    LEFT JOIN users u ON a.author_id = u.id
    WHERE a.slug = ? AND a.status = 'published'
    LIMIT 1
");
$stmt->bind_param('s', $slug);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();

if (!$article) {
    header('Location: articles.php');
    exit;
}

// Update view count
$conn->query("UPDATE articles SET view_count = view_count + 1 WHERE id = " . (int)$article['id']);

// Get related articles
$related_articles = [];
if ($article['category_id']) {
    $stmt = $conn->prepare("
        SELECT a.*, ac.name as category_name, ac.color as category_color
        FROM articles a
        LEFT JOIN article_categories ac ON a.category_id = ac.id
        WHERE a.category_id = ? AND a.id != ? AND a.status = 'published'
        ORDER BY a.published_at DESC
        LIMIT 3
    ");
    $stmt->bind_param('ii', $article['category_id'], $article['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $related_articles[] = $row;
    }
}

// Fetch settings
$global_settings = [];
$result = db_get_rows($conn, "SELECT * FROM settings");
foreach ($result as $row) {
    $global_settings[$row['setting_key']] = $row['setting_value'];
}

// Fetch menus
$main_menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($article['title']); ?> - <?php echo h($global_settings['site_name'] ?? 'VIBEDAYBKK'); ?></title>
    
    <!-- Meta Tags -->
    <meta name="description" content="<?php echo h($article['excerpt'] ?? substr(strip_tags($article['content']), 0, 160)); ?>">
    <meta property="og:title" content="<?php echo h($article['title']); ?>">
    <meta property="og:description" content="<?php echo h($article['excerpt'] ?? substr(strip_tags($article['content']), 0, 160)); ?>">
    <?php if (!empty($article['featured_image'])): ?>
    <meta property="og:image" content="<?php echo UPLOADS_URL . '/' . $article['featured_image']; ?>">
    <?php endif; ?>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 'noto': ['Noto Sans Thai', 'sans-serif'] },
                    colors: { 'dark': '#0a0a0a', 'dark-light': '#1a1a1a', 'red-primary': '#DC2626', 'red-light': '#EF4444' }
                }
            }
        }
    </script>
    
    <style>
        body { font-family: 'Noto Sans Thai', sans-serif; background: #0a0a0a; color: white; }
        .article-content { line-height: 1.8; }
        .article-content h1 { font-size: 2rem; font-weight: bold; margin: 1.5rem 0; }
        .article-content h2 { font-size: 1.75rem; font-weight: bold; margin: 1.25rem 0; }
        .article-content h3 { font-size: 1.5rem; font-weight: bold; margin: 1rem 0; }
        .article-content h4 { font-size: 1.25rem; font-weight: bold; margin: 0.875rem 0; }
        .article-content p { margin: 1rem 0; }
        .article-content img { border-radius: 0.5rem; margin: 1.5rem 0; max-width: 100%; height: auto; }
        .article-content ul, .article-content ol { margin: 1rem 0; padding-left: 2rem; }
        .article-content li { margin: 0.5rem 0; }
        .article-content a { color: #DC2626; text-decoration: underline; }
        .article-content blockquote { border-left: 4px solid #DC2626; padding-left: 1rem; margin: 1.5rem 0; font-style: italic; color: #9CA3AF; }
        .article-content code { background: #1a1a1a; padding: 0.2rem 0.4rem; border-radius: 0.25rem; font-family: monospace; }
        .article-content pre { background: #1a1a1a; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; margin: 1rem 0; }
        .article-content table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        .article-content th, .article-content td { border: 1px solid #374151; padding: 0.75rem; text-align: left; }
        .article-content th { background: #1a1a1a; font-weight: bold; }
    </style>
</head>
<body class="bg-dark text-white">

    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-dark/95 backdrop-blur-sm z-50 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="<?php echo BASE_URL; ?>" class="text-2xl font-bold text-red-primary flex items-center">
                        <?php 
                        $logo_type = $global_settings['logo_type'] ?? 'text';
                        $logo_text = $global_settings['logo_text'] ?? 'VIBEDAYBKK';
                        $logo_image = $global_settings['logo_image'] ?? '';
                        
                        if ($logo_type === 'image' && !empty($logo_image)): 
                        ?>
                            <img src="<?php echo UPLOADS_URL . '/' . $logo_image; ?>" alt="<?php echo h($logo_text); ?>" class="h-10 object-contain">
                        <?php else: ?>
                            <i class="fas fa-star mr-2"></i><?php echo h($logo_text); ?>
                        <?php endif; ?>
                    </a>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <?php foreach ($main_menus as $menu): ?>
                    <a href="<?php echo h($menu['url'] ?? '#'); ?>" class="text-gray-300 hover:text-red-primary transition-colors duration-200 font-medium">
                        <?php echo h($menu['title'] ?? ''); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Article Content -->
    <article class="pt-24 pb-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="mb-8 flex items-center text-sm text-gray-400">
                <a href="<?php echo BASE_URL; ?>" class="hover:text-red-primary transition-colors">หน้าแรก</a>
                <i class="fas fa-chevron-right mx-2 text-xs"></i>
                <a href="articles.php" class="hover:text-red-primary transition-colors">บทความ</a>
                <?php if (!empty($article['category_name'])): ?>
                <i class="fas fa-chevron-right mx-2 text-xs"></i>
                <a href="articles.php?category=<?php echo $article['category_id']; ?>" class="hover:text-red-primary transition-colors">
                    <?php echo h($article['category_name']); ?>
                </a>
                <?php endif; ?>
            </nav>

            <!-- Category Badge -->
            <?php if (!empty($article['category_name'])): ?>
            <div class="mb-4">
                <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold text-white" style="background-color: <?php echo h($article['category_color'] ?? '#DC2626'); ?>">
                    <?php if (!empty($article['category_icon'])): ?>
                        <i class="<?php echo h($article['category_icon']); ?> mr-1"></i>
                    <?php endif; ?>
                    <?php echo h($article['category_name']); ?>
                </span>
            </div>
            <?php endif; ?>

            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">
                <?php echo h($article['title']); ?>
            </h1>

            <!-- Meta Info -->
            <div class="flex flex-wrap items-center gap-4 text-gray-400 mb-8 pb-8 border-b border-gray-800">
                <div class="flex items-center">
                    <i class="far fa-calendar mr-2"></i>
                    <span><?php echo date('d M Y', strtotime($article['published_at'] ?? $article['created_at'])); ?></span>
                </div>
                <?php if (!empty($article['author_name'])): ?>
                <div class="flex items-center">
                    <i class="far fa-user mr-2"></i>
                    <span><?php echo h($article['author_name']); ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($article['read_time'])): ?>
                <div class="flex items-center">
                    <i class="far fa-clock mr-2"></i>
                    <span><?php echo h($article['read_time']); ?> นาที</span>
                </div>
                <?php endif; ?>
                <div class="flex items-center">
                    <i class="far fa-eye mr-2"></i>
                    <span><?php echo number_format($article['view_count'] ?? 0); ?> ครั้ง</span>
                </div>
            </div>

            <!-- Featured Image -->
            <?php if (!empty($article['featured_image'])): ?>
            <div class="mb-8 rounded-lg overflow-hidden">
                <img src="<?php echo UPLOADS_URL . '/' . $article['featured_image']; ?>" alt="<?php echo h($article['title']); ?>" class="w-full h-auto">
            </div>
            <?php endif; ?>

            <!-- Excerpt -->
            <?php if (!empty($article['excerpt'])): ?>
            <div class="text-xl text-gray-300 mb-8 leading-relaxed">
                <?php echo h($article['excerpt']); ?>
            </div>
            <?php endif; ?>

            <!-- Content -->
            <div class="article-content text-gray-300">
                <?php echo $article['content']; ?>
            </div>

            <!-- Share Buttons: ซ่อนไว้ -->
            <?php /* Removed share section */ ?>
        </div>
    </article>

    <!-- Related Articles -->
    <?php if (!empty($related_articles)): ?>
    <section class="py-16 bg-dark-light">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold mb-8">บทความที่เกี่ยวข้อง</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($related_articles as $rel): ?>
                <article class="bg-dark rounded-lg overflow-hidden hover:transform hover:scale-105 transition-all duration-300">
                    <?php if (!empty($rel['featured_image'])): ?>
                    <div class="h-48 overflow-hidden">
                        <img src="<?php echo UPLOADS_URL . '/' . $rel['featured_image']; ?>" alt="<?php echo h($rel['title']); ?>" class="w-full h-full object-cover">
                    </div>
                    <?php else: ?>
                    <div class="h-48 bg-gradient-to-br from-red-primary to-red-light flex items-center justify-center">
                        <i class="fas fa-newspaper text-6xl text-white/30"></i>
                    </div>
                    <?php endif; ?>
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-2 line-clamp-2"><?php echo h($rel['title']); ?></h3>
                        <a href="article-detail.php?slug=<?php echo h($rel['slug']); ?>" class="text-red-primary hover:text-red-light">
                            อ่านเพิ่มเติม <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Back to Articles -->
    <div class="py-8 text-center">
        <a href="articles.php" class="inline-flex items-center px-6 py-3 bg-red-primary hover:bg-red-light text-white rounded-lg transition-colors font-semibold">
            <i class="fas fa-arrow-left mr-2"></i>
            กลับไปหน้าบทความ
        </a>
    </div>

    <!-- Footer -->
    <footer class="bg-dark-light py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-gray-400">© <?php echo date('Y'); ?> <?php echo h($global_settings['site_name'] ?? 'VIBEDAYBKK'); ?>. All rights reserved.</p>
        </div>
    </footer>

    <style>
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</body>
</html>
<?php $conn->close(); ?>
