<?php
require_once '../../backend/connection/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Update status pesanan menjadi 'batal'
    $stmt = $pdo->prepare("UPDATE orders SET status = 'batal' WHERE id_order = ?");
    $stmt->execute([$id]);
}

header("Location: ../../admin/components/pesanan.php");
exit;
