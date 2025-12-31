<?php
// backend/update_order_status.php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    
    if ($order_id > 0 && in_array($status, ['en attente', 'confirmee', 'livree', 'annulee'])) {
        $sql = "UPDATE commandes SET statut = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $order_id);
        
        if ($stmt->execute()) {
            header('Location: admin_orders.php?success=1');
        } else {
            header('Location: admin_orders.php?error=' . urlencode($stmt->error));
        }
        $stmt->close();
    } else {
        header('Location: admin_orders.php?error=Données invalides');
    }
} else {
    header('Location: admin_orders.php');
}

$conn->close();
?>