<?php
require_once 'includes/config.php';

$result = $conn->query("SHOW COLUMNS FROM models LIKE 'status'");
$row = $result->fetch_assoc();

echo "Column: status\n";
echo "Type: " . $row['Type'] . "\n";
echo "Null: " . $row['Null'] . "\n";
echo "Default: " . $row['Default'] . "\n";

