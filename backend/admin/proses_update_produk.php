<?php
session_start();
require_once '../connection/db.php';

$id = $_POST['id_produk'];
$nama_produk = $_POST['nama_produk'];
$keterangan = $_POST['keterangan'];
$stok = $_POST['stok'];
$berat = $_POST['berat'];
$gambar_lama = $_POST['gambar_lama'];
$gambar = $gambar_lama;

// Upload gambar baru jika ada
if (!empty($_FILES['gambar']['name'])) {
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $fileName = uniqid() . '_' . $_FILES['gambar']['name'];
    $targetPath = $uploadDir . basename($fileName);

    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetPath)) {
        // Hapus gambar lama jika ada
        if (!empty($gambar_lama) && file_exists($uploadDir . $gambar_lama)) {
            unlink($uploadDir . $gambar_lama);
        }
        $gambar = $fileName;
    }
}

try {
    $stmt = $pdo->prepare("UPDATE produk SET nama_produk=?, berat=?, keterangan=?, stok=?, gambar=?, updated_at=NOW() WHERE id_produk=?");
    $stmt->execute([$nama_produk, $berat, $keterangan, $stok, $gambar, $id]);

    echo "
    <html>
    <head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head>
    <body>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Produk berhasil diperbarui.',
            showConfirmButton: false,
            timer: 2000
        }).then(() => {
            window.location.href = '../../admin/components/produk.php';
        });
    </script>
    </body>
    </html>";
} catch (PDOException $e) {
    echo "Gagal update: " . $e->getMessage();
}
