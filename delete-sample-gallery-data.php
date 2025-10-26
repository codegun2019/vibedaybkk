<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

try {
    // ลบข้อมูลตัวอย่างออกจากตาราง gallery
    $sql = "DELETE FROM gallery WHERE image LIKE '%unsplash%' OR image LIKE '%placeholder%' OR image LIKE '%nature-photo%' OR image LIKE '%beauty%' OR image LIKE '%workshop%' OR image LIKE '%skincare%'";
    $result = $conn->query($sql);
    
    $deleted_count = $conn->affected_rows;
    
    echo json_encode([
        'success' => true,
        'deleted_count' => $deleted_count,
        'message' => "ลบข้อมูลตัวอย่างแล้ว $deleted_count รายการ"
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}

$conn->close();
?>


