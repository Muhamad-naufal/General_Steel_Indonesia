<?php
require_once '../connection/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("UPDATE orders SET status = 'pengiriman' WHERE id_order = ?");
    $stmt->execute([$id]);
}

header("Location: ../../admin/components/pesanan.php");
exit;
