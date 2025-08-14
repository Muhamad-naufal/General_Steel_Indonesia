<?php
session_start();
require_once '../connection/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id_produk'] ?? null;

if (!$id) {
    echo "<script>alert('ID tidak ditemukan'); window.location.href='../../admin/components/produk.php';</script>";
    exit;
}

// Ambil data produk untuk cek gambar
$stmt = $pdo->prepare("SELECT * FROM produk WHERE id_produk = ?");
$stmt->execute([$id]);
$produk = $stmt->fetch();

if (!$produk) {
    echo "<script>alert('Produk tidak ditemukan'); window.location.href='../../admin/components/produk.php';</script>";
    exit;
}

// Hapus gambar dari server jika ada
if (!empty($produk['gambar'])) {
    $gambarPath = '../uploads/' . $produk['gambar'];
    if (file_exists($gambarPath)) {
        unlink($gambarPath);
    }
}

// Hapus produk dari database
$stmt = $pdo->prepare("DELETE FROM produk WHERE id_produk = ?");
$stmt->execute([$id]);

echo "
<html>
<head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head>
<body>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Produk berhasil dihapus.',
        showConfirmButton: false,
        timer: 2000
    }).then(() => {
        window.location.href = '../../admin/components/produk.php';
    });
</script>
</body>
</html>";
