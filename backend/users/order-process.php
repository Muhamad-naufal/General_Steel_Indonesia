<?php
require_once '../connection/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'] ?? '';
    $metode = $_POST['metode_kontak'] ?? '';
    $telepon = $_POST['telepon'] ?? '';
    $email = $_POST['email'] ?? '';
    $catatan = $_POST['catatan'] ?? '';
    $produkIds = $_POST['produk'] ?? [];
    $jumlahs = $_POST['jumlah'] ?? [];
    $alamat = $_POST['alamat'] ?? '';

    // Validasi wajib
    if (empty($nama) || empty($metode) || empty($produkIds) || empty($jumlahs) || empty($alamat)) {
        echo "<script>alert('Lengkapi semua data!'); window.history.back();</script>";
        exit;
    }

    // Validasi metode kontak
    $kontak = '';
    if ($metode === 'telepon') {
        if (empty($telepon)) {
            echo "<script>alert('Nomor telepon wajib diisi!'); window.history.back();</script>";
            exit;
        }
        $kontak = $telepon;
    } elseif ($metode === 'email') {
        if (empty($email)) {
            echo "<script>alert('Alamat email wajib diisi!'); window.history.back();</script>";
            exit;
        }
        $kontak = $email;
    }

    try {
        $pdo->beginTransaction();

        // Simpan ke tabel orders
        $stmt = $pdo->prepare("INSERT INTO orders (nama, metode_kontak, kontak, catatan, status, alamat) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nama, $metode, $kontak, $catatan, 'menunggu', $alamat]);
        $orderId = $pdo->lastInsertId();

        // Simpan ke order_items
        $stmtItem = $pdo->prepare("INSERT INTO order_items (id_order, produk_id, jumlah) VALUES (?, ?, ?)");
        foreach ($produkIds as $i => $id_produk) {
            $jml = (int) $jumlahs[$i];
            if ($id_produk && $jml > 0) {
                $stmtItem->execute([$orderId, $id_produk, $jml]);
            }
        }

        $pdo->commit();

        echo "
        <html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Pesanan Anda telah dikirim.',
                timer: 2500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '../../page/product.php';
            });
        </script></body></html>";
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
