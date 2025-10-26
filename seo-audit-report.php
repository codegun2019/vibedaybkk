<?php
/**
 * SEO Audit Report สำหรับหน้าแรก
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

// ดึงข้อมูล settings
$global_settings = [];
$result = db_get_rows($conn, "SELECT * FROM settings");
foreach ($result as $row) {
    $global_settings[$row['setting_key']] = $row['setting_value'];
}

echo "<h1>🔍 SEO Audit Report - หน้าแรก</h1>";
echo "<p><strong>วันที่ตรวจสอบ:</strong> " . date('d/m/Y H:i:s') . "</p>";
echo "<p><strong>URL:</strong> " . SITE_URL . "</p>";

echo "<hr>";

// 1. Basic SEO Tags
echo "<h2>📋 1. Basic SEO Tags</h2>";
echo "<table style='width: 100%; border-collapse: collapse;'>";
echo "<tr style='background: #f5f5f5;'><th style='padding: 10px; border: 1px solid #ddd;'>Tag</th><th style='padding: 10px; border: 1px solid #ddd;'>Status</th><th style='padding: 10px; border: 1px solid #ddd;'>Value</th></tr>";

// Title
$title = $global_settings['seo_title'] ?? $global_settings['site_name'] ?? 'VIBEDAYBKK';
$title_status = !empty($title) ? '✅' : '❌';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Title</td><td style='padding: 10px; border: 1px solid #ddd;'>{$title_status}</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($title) . "</td></tr>";

// Description
$description = $global_settings['seo_description'] ?? $global_settings['site_description'] ?? '';
$description_status = !empty($description) && strlen($description) >= 120 && strlen($description) <= 160 ? '✅' : '⚠️';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Meta Description</td><td style='padding: 10px; border: 1px solid #ddd;'>{$description_status}</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($description) . " (" . strlen($description) . " chars)</td></tr>";

// Keywords
$keywords = $global_settings['seo_keywords'] ?? '';
$keywords_status = !empty($keywords) ? '✅' : '⚠️';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Keywords</td><td style='padding: 10px; border: 1px solid #ddd;'>{$keywords_status}</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($keywords) . "</td></tr>";

// Author
$author = $global_settings['seo_author'] ?? '';
$author_status = !empty($author) ? '✅' : '⚠️';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Author</td><td style='padding: 10px; border: 1px solid #ddd;'>{$author_status}</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($author) . "</td></tr>";

// Canonical URL
$canonical = $global_settings['seo_canonical_url'] ?? '';
$canonical_status = !empty($canonical) ? '✅' : '⚠️';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Canonical URL</td><td style='padding: 10px; border: 1px solid #ddd;'>{$canonical_status}</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($canonical) . "</td></tr>";

// Robots
$robots_index = ($global_settings['robots_index'] ?? '1') == '1' ? 'index' : 'noindex';
$robots_follow = ($global_settings['robots_follow'] ?? '1') == '1' ? 'follow' : 'nofollow';
$robots_status = ($robots_index == 'index' && $robots_follow == 'follow') ? '✅' : '⚠️';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Robots</td><td style='padding: 10px; border: 1px solid #ddd;'>{$robots_status}</td><td style='padding: 10px; border: 1px solid #ddd;'>{$robots_index}, {$robots_follow}</td></tr>";

echo "</table>";

// 2. Open Graph Tags
echo "<h2>📱 2. Open Graph Tags (Facebook)</h2>";
echo "<table style='width: 100%; border-collapse: collapse;'>";
echo "<tr style='background: #f5f5f5;'><th style='padding: 10px; border: 1px solid #ddd;'>Tag</th><th style='padding: 10px; border: 1px solid #ddd;'>Status</th><th style='padding: 10px; border: 1px solid #ddd;'>Value</th></tr>";

$og_type = $global_settings['og_type'] ?? 'website';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>og:type</td><td style='padding: 10px; border: 1px solid #ddd;'>✅</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($og_type) . "</td></tr>";

$og_title = $global_settings['og_title'] ?? $global_settings['seo_title'] ?? $global_settings['site_name'] ?? 'VIBEDAYBKK';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>og:title</td><td style='padding: 10px; border: 1px solid #ddd;'>✅</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($og_title) . "</td></tr>";

$og_description = $global_settings['og_description'] ?? $global_settings['seo_description'] ?? '';
$og_desc_status = !empty($og_description) ? '✅' : '⚠️';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>og:description</td><td style='padding: 10px; border: 1px solid #ddd;'>{$og_desc_status}</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($og_description) . "</td></tr>";

$og_image = $global_settings['og_image'] ?? '';
$og_image_status = !empty($og_image) ? '✅' : '⚠️';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>og:image</td><td style='padding: 10px; border: 1px solid #ddd;'>{$og_image_status}</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($og_image) . "</td></tr>";

$og_url = $global_settings['seo_canonical_url'] ?? SITE_URL;
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>og:url</td><td style='padding: 10px; border: 1px solid #ddd;'>✅</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($og_url) . "</td></tr>";

$og_locale = $global_settings['og_locale'] ?? 'th_TH';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>og:locale</td><td style='padding: 10px; border: 1px solid #ddd;'>✅</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($og_locale) . "</td></tr>";

echo "</table>";

// 3. Twitter Card Tags
echo "<h2>🐦 3. Twitter Card Tags</h2>";
echo "<table style='width: 100%; border-collapse: collapse;'>";
echo "<tr style='background: #f5f5f5;'><th style='padding: 10px; border: 1px solid #ddd;'>Tag</th><th style='padding: 10px; border: 1px solid #ddd;'>Status</th><th style='padding: 10px; border: 1px solid #ddd;'>Value</th></tr>";

$twitter_card = $global_settings['twitter_card'] ?? 'summary_large_image';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>twitter:card</td><td style='padding: 10px; border: 1px solid #ddd;'>✅</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($twitter_card) . "</td></tr>";

$twitter_site = $global_settings['twitter_site'] ?? '';
$twitter_site_status = !empty($twitter_site) ? '✅' : '⚠️';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>twitter:site</td><td style='padding: 10px; border: 1px solid #ddd;'>{$twitter_site_status}</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($twitter_site) . "</td></tr>";

$twitter_creator = $global_settings['twitter_creator'] ?? '';
$twitter_creator_status = !empty($twitter_creator) ? '✅' : '⚠️';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>twitter:creator</td><td style='padding: 10px; border: 1px solid #ddd;'>{$twitter_creator_status}</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($twitter_creator) . "</td></tr>";

$twitter_title = $global_settings['twitter_title'] ?? $global_settings['seo_title'] ?? '';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>twitter:title</td><td style='padding: 10px; border: 1px solid #ddd;'>✅</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($twitter_title) . "</td></tr>";

$twitter_description = $global_settings['twitter_description'] ?? $global_settings['seo_description'] ?? '';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>twitter:description</td><td style='padding: 10px; border: 1px solid #ddd;'>✅</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($twitter_description) . "</td></tr>";

$twitter_image = $global_settings['twitter_image'] ?? '';
$twitter_image_status = !empty($twitter_image) ? '✅' : '⚠️';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>twitter:image</td><td style='padding: 10px; border: 1px solid #ddd;'>{$twitter_image_status}</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($twitter_image) . "</td></tr>";

echo "</table>";

// 4. Analytics & Tracking
echo "<h2>📊 4. Analytics & Tracking</h2>";
echo "<table style='width: 100%; border-collapse: collapse;'>";
echo "<tr style='background: #f5f5f5;'><th style='padding: 10px; border: 1px solid #ddd;'>Service</th><th style='padding: 10px; border: 1px solid #ddd;'>Status</th><th style='padding: 10px; border: 1px solid #ddd;'>ID/Value</th></tr>";

// Google Analytics
$ga_enabled = ($global_settings['google_analytics_enabled'] ?? '0') == '1';
$ga_id = $global_settings['google_analytics_id'] ?? '';
$ga_status = $ga_enabled && !empty($ga_id) ? '✅' : '❌';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Google Analytics</td><td style='padding: 10px; border: 1px solid #ddd;'>{$ga_status}</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($ga_id) . "</td></tr>";

// Facebook Pixel
$fb_enabled = ($global_settings['facebook_pixel_enabled'] ?? '0') == '1';
$fb_id = $global_settings['facebook_pixel_id'] ?? '';
$fb_status = $fb_enabled && !empty($fb_id) ? '✅' : '❌';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Facebook Pixel</td><td style='padding: 10px; border: 1px solid #ddd;'>{$fb_status}</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($fb_id) . "</td></tr>";

// Google Search Console
$gsc_enabled = ($global_settings['google_search_console_enabled'] ?? '0') == '1';
$gsc_verification = $global_settings['google_site_verification'] ?? '';
$gsc_status = $gsc_enabled && !empty($gsc_verification) ? '✅' : '❌';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Google Search Console</td><td style='padding: 10px; border: 1px solid #ddd;'>{$gsc_status}</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($gsc_verification) . "</td></tr>";

echo "</table>";

// 5. Mobile & Performance
echo "<h2>📱 5. Mobile & Performance</h2>";
echo "<table style='width: 100%; border-collapse: collapse;'>";
echo "<tr style='background: #f5f5f5;'><th style='padding: 10px; border: 1px solid #ddd;'>Element</th><th style='padding: 10px; border: 1px solid #ddd;'>Status</th><th style='padding: 10px; border: 1px solid #ddd;'>Value</th></tr>";

// Viewport
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Viewport Meta Tag</td><td style='padding: 10px; border: 1px solid #ddd;'>✅</td><td style='padding: 10px; border: 1px solid #ddd;'>width=device-width, initial-scale=1.0</td></tr>";

// Theme Color
$theme_color = $global_settings['meta_theme_color'] ?? '#DC2626';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Theme Color</td><td style='padding: 10px; border: 1px solid #ddd;'>✅</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($theme_color) . "</td></tr>";

// Apple Mobile Web App
$apple_capable = ($global_settings['meta_apple_mobile_capable'] ?? '1') == '1';
$apple_status = $apple_capable ? '✅' : '⚠️';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Apple Mobile Web App</td><td style='padding: 10px; border: 1px solid #ddd;'>{$apple_status}</td><td style='padding: 10px; border: 1px solid #ddd;'>" . ($apple_capable ? 'Enabled' : 'Disabled') . "</td></tr>";

// Favicon
$favicon = $global_settings['favicon'] ?? '';
$favicon_status = !empty($favicon) ? '✅' : '⚠️';
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Favicon</td><td style='padding: 10px; border: 1px solid #ddd;'>{$favicon_status}</td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($favicon) . "</td></tr>";

echo "</table>";

// 6. Recommendations
echo "<h2>💡 6. SEO Recommendations</h2>";
echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 5px; border-left: 4px solid #DC2626;'>";

$recommendations = [];

// Check description length
if (empty($description)) {
    $recommendations[] = "❌ <strong>Meta Description:</strong> ยังไม่มี meta description";
} elseif (strlen($description) < 120) {
    $recommendations[] = "⚠️ <strong>Meta Description:</strong> ควรมีอย่างน้อย 120 ตัวอักษร (ปัจจุบัน: " . strlen($description) . " ตัวอักษร)";
} elseif (strlen($description) > 160) {
    $recommendations[] = "⚠️ <strong>Meta Description:</strong> ควรไม่เกิน 160 ตัวอักษร (ปัจจุบัน: " . strlen($description) . " ตัวอักษร)";
}

// Check keywords
if (empty($keywords)) {
    $recommendations[] = "⚠️ <strong>Keywords:</strong> ควรเพิ่ม keywords ที่เกี่ยวข้อง";
}

// Check canonical URL
if (empty($canonical)) {
    $recommendations[] = "⚠️ <strong>Canonical URL:</strong> ควรตั้งค่า canonical URL";
}

// Check OG image
if (empty($og_image)) {
    $recommendations[] = "⚠️ <strong>Open Graph Image:</strong> ควรเพิ่มรูปภาพสำหรับ Facebook sharing";
}

// Check Twitter image
if (empty($twitter_image)) {
    $recommendations[] = "⚠️ <strong>Twitter Image:</strong> ควรเพิ่มรูปภาพสำหรับ Twitter sharing";
}

// Check Analytics
if (!$ga_enabled || empty($ga_id)) {
    $recommendations[] = "❌ <strong>Google Analytics:</strong> ควรเปิดใช้งาน Google Analytics เพื่อติดตาม traffic";
}

if (!$fb_enabled || empty($fb_id)) {
    $recommendations[] = "❌ <strong>Facebook Pixel:</strong> ควรเปิดใช้งาน Facebook Pixel เพื่อติดตาม conversions";
}

// Check structured data
$recommendations[] = "❌ <strong>Structured Data:</strong> ควรเพิ่ม JSON-LD structured data สำหรับ LocalBusiness, Organization, หรือ Service";

// Check sitemap
$recommendations[] = "❌ <strong>Sitemap:</strong> ควรสร้าง sitemap.xml และ robots.txt";

// Check page speed
$recommendations[] = "⚠️ <strong>Page Speed:</strong> ควรตรวจสอบ Core Web Vitals และปรับปรุงความเร็วในการโหลด";

if (empty($recommendations)) {
    echo "<p>🎉 <strong>ยอดเยี่ยม!</strong> SEO ของหน้าแรกอยู่ในเกณฑ์ดีมาก!</p>";
} else {
    echo "<h3>ข้อเสนอแนะ:</h3>";
    echo "<ul>";
    foreach ($recommendations as $rec) {
        echo "<li style='margin: 10px 0;'>{$rec}</li>";
    }
    echo "</ul>";
}

echo "</div>";

// 7. Quick Actions
echo "<h2>🚀 7. Quick Actions</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;'>";

echo "<div style='background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>";
echo "<h3>📝 แก้ไข SEO Settings</h3>";
echo "<p>แก้ไขการตั้งค่า SEO หลัก</p>";
echo "<a href='admin/settings/index.php' target='_blank' style='background: #DC2626; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>ไปที่ Admin Settings</a>";
echo "</div>";

echo "<div style='background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>";
echo "<h3>🔍 ตรวจสอบ Page Source</h3>";
echo "<p>ดู HTML source ของหน้าแรก</p>";
echo "<a href='" . SITE_URL . "' target='_blank' style='background: #059669; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>ดูหน้าแรก</a>";
echo "</div>";

echo "<div style='background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>";
echo "<h3>📊 Google PageSpeed Insights</h3>";
echo "<p>ตรวจสอบความเร็วหน้าเว็บ</p>";
echo "<a href='https://pagespeed.web.dev/report?url=" . urlencode(SITE_URL) . "' target='_blank' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>ตรวจสอบ PageSpeed</a>";
echo "</div>";

echo "<div style='background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>";
echo "<h3>🐦 Facebook Debugger</h3>";
echo "<p>ทดสอบ Open Graph tags</p>";
echo "<a href='https://developers.facebook.com/tools/debug/?q=" . urlencode(SITE_URL) . "' target='_blank' style='background: #1877f2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>ทดสอบ Facebook</a>";
echo "</div>";

echo "</div>";

echo "<hr>";
echo "<p><strong>หมายเหตุ:</strong> รายงานนี้สร้างขึ้นโดยอัตโนมัติจากข้อมูลในฐานข้อมูล ควรตรวจสอบและอัปเดตการตั้งค่า SEO อย่างสม่ำเสมอ</p>";
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: #333;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background: #f8f9fa;
}
h1 {
    color: #DC2626;
    text-align: center;
    margin-bottom: 30px;
    font-size: 2.5em;
}
h2 {
    color: #333;
    margin-top: 40px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #DC2626;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
th {
    background: #DC2626;
    color: white;
    padding: 15px 10px;
    text-align: left;
    font-weight: bold;
}
td {
    padding: 15px 10px;
    border-bottom: 1px solid #eee;
}
tr:hover {
    background: #f8f9fa;
}
hr {
    border: none;
    border-top: 2px solid #DC2626;
    margin: 40px 0;
}
ul {
    padding-left: 20px;
}
li {
    margin: 8px 0;
}
a {
    color: white;
    text-decoration: none;
    font-weight: bold;
}
a:hover {
    opacity: 0.8;
}
</style>


