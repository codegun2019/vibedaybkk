<?php
/**
 * จำลองการล็อกอินเป็น editor
 */
session_start();

// เคลียร์ session เก่า
session_unset();

// ตั้งค่า session ใหม่เป็น editor
$_SESSION['user_id'] = 5; // ID จริงจาก database
$_SESSION['username'] = '0900000020';
$_SESSION['user_role'] = 'editor';

echo "<h1>✅ จำลองการล็อกอินเป็น Editor สำเร็จ!</h1>";
echo "<p><strong>User ID:</strong> {$_SESSION['user_id']}</p>";
echo "<p><strong>Username:</strong> {$_SESSION['username']}</p>";
echo "<p><strong>Role:</strong> {$_SESSION['user_role']}</p>";
echo "<hr>";
echo "<h2>ทดสอบเข้าหน้าต่างๆ:</h2>";
echo "<ul>";
echo "<li><a href='admin/homepage/' target='_blank'>จัดการหน้าแรก</a></li>";
echo "<li><a href='admin/models/' target='_blank'>จัดการโมเดล</a></li>";
echo "<li><a href='admin/gallery/' target='_blank'>จัดการแกลเลอรี่</a></li>";
echo "<li><a href='admin/articles/' target='_blank'>จัดการบทความ</a></li>";
echo "<li><a href='admin/reviews/' target='_blank'>จัดการรีวิว</a></li>";
echo "</ul>";
echo "<p><strong>⚠️ หมายเหตุ:</strong> หากทดสอบเสร็จแล้ว ให้ <a href='admin/logout.php'>ล็อกเอาท์</a> และล็อกอินใหม่ตามปกติ</p>";
?>

