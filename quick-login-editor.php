<?php
/**
 * ล็อกอินเป็น editor ด่วน
 */
session_start();
session_unset();

// ตั้งค่า session เป็น editor
$_SESSION['user_id'] = 5;
$_SESSION['username'] = '0900000020';
$_SESSION['user_role'] = 'editor';

// Redirect ไปหน้า homepage ทันที
header('Location: admin/homepage/');
exit;
?>

