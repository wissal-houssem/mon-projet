<?php
// get_products.php
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT * FROM produits";
$result = $conn->query($sql);

$products = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode($products, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
$conn->close();
?>