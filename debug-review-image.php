<?php
require_once 'includes/config.php';

$id = 24;
$stmt = $conn->prepare("SELECT * FROM customer_reviews WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$review = $result->fetch_assoc();
$stmt->close();

echo "Review ID: " . $review['id'] . "\n";
echo "Customer Name: " . $review['customer_name'] . "\n";
echo "Image field: " . var_export($review['image'], true) . "\n";

// Test different paths
$image_path = $review['image'];

echo "\n=== Testing image paths ===\n";

// Test 1: Direct
echo "1. Direct: " . $image_path . "\n";
echo "   File exists: " . (file_exists($image_path) ? 'YES' : 'NO') . "\n";

// Test 2: With ROOT_PATH
$path2 = ROOT_PATH . '/' . $image_path;
echo "2. ROOT_PATH: " . $path2 . "\n";
echo "   File exists: " . (file_exists($path2) ? 'YES' : 'NO') . "\n";

// Test 3: With UPLOADS_PATH
if (strpos($image_path, 'uploads/') === 0) {
    $path3 = str_replace('uploads/', '', $image_path);
    $path3 = UPLOADS_PATH . '/' . $path3;
    echo "3. UPLOADS_PATH: " . $path3 . "\n";
    echo "   File exists: " . (file_exists($path3) ? 'YES' : 'NO') . "\n";
}

// Test URL
if (strpos($image_path, 'uploads/') === 0) {
    $url = UPLOADS_URL . '/' . str_replace('uploads/', '', $image_path);
    echo "4. URL: " . $url . "\n";
}
