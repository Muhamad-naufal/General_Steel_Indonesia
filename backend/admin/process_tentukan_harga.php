<?php
require_once '../../backend/connection/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_order = $_POST['id_order'] ?? null;
    $harga_items = $_POST['harga_item'] ?? [];
    $id_items = $_POST['id_item'] ?? [];
    $total_harga = $_POST['total_harga'] ?? 0;

    if (!$id_order || empty($harga_items) || empty($id_items)) {
        die('Data tidak valid.');
    }

    try {
        $pdo->beginTransaction();

        // Update harga per item
        $stmtUpdateItem = $pdo->prepare("UPDATE order_items SET harga_item = ? WHERE id_item = ?");
        foreach ($harga_items as $i => $harga) {
            $id_item = $id_items[$i];
            $stmtUpdateItem->execute([$harga, $id_item]);
        }

        // Simpan total harga ke tabel orders
        $stmtOrder = $pdo->prepare("UPDATE orders SET harga = ?, status = 'harga_ditentukan' WHERE id_order = ?");
        $stmtOrder->execute([$total_harga, $id_order]);

        $pdo->commit();

        echo "
        <html>
        <head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head>
        <body>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Horeeee',
                    text: 'Harga berhasil ditentukan!',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '../../admin/components/pesanan.php';
                });
            </script>
        </body>
        </html>";
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Gagal menyimpan: " . $e->getMessage());
    }
} else {
    header("Location: pesanan.php");
    exit;
}
