<?php
require '../connection/db.php'; // pastikan file koneksi ini ada dan benar

// Ambil data dari form
$order_id = $_POST['order_id'];
$remarks = $_POST['remarks'];

// Validasi dasar
if (!$order_id || empty($remarks)) {
    die("Order ID atau remarks tidak boleh kosong.");
}

// Siapkan statement untuk insert
$stmt = $pdo->prepare("INSERT INTO order_remarks (order_id, remark) VALUES (?, ?)");

// Simpan setiap remark yang tidak kosong
foreach ($remarks as $remark) {
    $remark = trim($remark);
    if ($remark !== '') {
        $stmt->bindValue(1, $order_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $remark, PDO::PARAM_STR);
        $stmt->execute();
    }
}

// Opsional: update status order (misal ke 'remarks_dibuat')
$updateStmt = $pdo->prepare("UPDATE orders SET status = 'remarks_dibuat', remarks_dibuat = 1 WHERE id_order = ?");
$updateStmt->execute([$order_id]);

// Redirect ke detail order atau halaman lain
header("Location: ../../admin/components/pesanan.php");
exit;
