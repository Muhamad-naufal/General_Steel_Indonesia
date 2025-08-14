<?php
require_once '../../backend/connection/db.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // Update status dan tandai SPM sudah dibuat
    $stmt = $pdo->prepare("UPDATE orders SET invoice_dilihat = 1, status = 'cetak_resi' WHERE id_order = ?");
    $stmt->execute([$id]);

    // Redirect ke file generate Invoice
    header("Location: generate_invoice.php?id=$id");
    exit;
} else {
    echo "ID tidak valid";
}
