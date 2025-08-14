<?php
session_start();
require_once '../connection/db.php'; // sesuaikan path

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$nama_produk = $_POST['nama_produk'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';
$stok = $_POST['stok'] ?? 0;
$berat = $_POST['berat'] ?? 0.0;
$gambar = null;

// Upload gambar
if (!empty($_FILES['gambar']['name'])) {
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $fileName = uniqid() . '_' . $_FILES['gambar']['name'];
    $targetPath = $uploadDir . basename($fileName);

    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetPath)) {
        $gambar = $fileName;
    }
}

try {
    $stmt = $pdo->prepare("INSERT INTO produk (nama_produk, berat, keterangan, stok, gambar) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nama_produk, $berat, $keterangan, $stok, $gambar]);

    echo "
<html>
<head>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head>
<body>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Produk berhasil ditambahkan!',
            showConfirmButton: false,
            timer: 2000
        }).then(() => {
            window.location.href = '../../admin/components/produk.php';
        });
    </script>
</body>
</html>";
} catch (PDOException $e) {
    echo "Gagal menyimpan produk: " . $e->getMessage();
}
