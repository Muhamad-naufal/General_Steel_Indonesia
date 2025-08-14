<?php
require_once '../../backend/connection/db.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // Update status dan tandai SPM sudah dibuat
    $stmt = $pdo->prepare("UPDATE orders SET spm_dibuat = 1, status = 'spm_dibuat' WHERE id_order = ?");
    $stmt->execute([$id]);

    // Redirect ke file generate SPM
    header("Location: generate_spm.php?id=$id");
    exit;
} else {
    echo "ID tidak valid";
}
