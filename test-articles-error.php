<?php
/**
 * Test Articles Error
 * ทดสอบว่าหน้า articles.php มี error อะไร
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Testing articles.php...</h1>";

try {
    define('VIBEDAYBKK_ADMIN', true);
    require_once 'includes/config.php';
    echo "<p style='color:green;'>✅ Config loaded</p>";
    
    // ดึง settings
    $settings = [];
    $result = db_get_rows($conn, "SELECT * FROM settings");
    foreach ($result as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    echo "<p style='color:green;'>✅ Settings loaded (" . count($settings) . " items)</p>";
    
    // ดึงบทความ
    $articles = db_get_rows($conn, "
        SELECT a.*, u.full_name as author_name
        FROM articles a
        LEFT JOIN users u ON a.author_id = u.id
        WHERE a.status = 'published'
        ORDER BY a.published_at DESC, a.created_at DESC
        LIMIT 12
    ");
    echo "<p style='color:green;'>✅ Articles loaded (" . count($articles) . " items)</p>";
    
    // ดึงเมนู
    $main_menus = db_get_rows($conn, "SELECT * FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC");
    echo "<p style='color:green;'>✅ Menus loaded (" . count($main_menus) . " items)</p>";
    
    // ทดสอบ include navigation
    echo "<p style='color:blue;'>Testing navigation include...</p>";
    
    ob_start();
    require_once 'includes/navigation.php';
    $nav_output = ob_get_clean();
    
    echo "<p style='color:green;'>✅ Navigation loaded</p>";
    echo "<p>Navigation output length: " . strlen($nav_output) . " chars</p>";
    
    echo "<hr>";
    echo "<h2>✅ ทุกอย่างโหลดสำเร็จ!</h2>";
    echo "<p><a href='articles.php'>ลองเปิด articles.php อีกครั้ง</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color:red; background:#fee; padding:20px; border-radius:8px;'>";
    echo "<strong>❌ Error:</strong><br>";
    echo htmlspecialchars($e->getMessage());
    echo "<br><br><strong>File:</strong> " . $e->getFile();
    echo "<br><strong>Line:</strong> " . $e->getLine();
    echo "</p>";
    
    echo "<pre style='background:#1e293b; color:#e2e8f0; padding:20px; border-radius:8px;'>";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
}
?>

