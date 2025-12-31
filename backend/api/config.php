<?php
// config.php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "pc_tech_boutique";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("❌ فشل الاتصال: " . $conn->connect_error);
}

// تعيين الترميز
$conn->set_charset("utf8mb4");
?>