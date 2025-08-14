<?php
session_start();
require_once '../../backend/connection/db.php';
include 'header.php';
include 'navbar.php';

// Ambil ID dari URL
$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('ID tidak ditemukan!'); window.location.href='produk.php';</script>";
    exit;
}

// Ambil data produk dari database
$stmt = $pdo->prepare("SELECT * FROM produk WHERE id_produk = ?");
$stmt->execute([$id]);
$produk = $stmt->fetch();

if (!$produk) {
    echo "<script>alert('Produk tidak ditemukan!'); window.location.href='produk.php';</script>";
    exit;
}
?>


<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0 bg-dark min-vh-100">
            <?php include 'sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4 py-4" style="background-color: #f8f9fa;">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-3 mb-4 border-bottom">
                <h2 class="mb-0">Edit Produk</h2>
            </div>

            <div class="card shadow-sm p-4 bg-white">
                <form action="../../backend/admin/proses_update_produk.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_produk" value="<?= $produk['id_produk'] ?>">
                    <input type="hidden" name="gambar_lama" value="<?= $produk['gambar'] ?>">
                    <div class="mb-3">
                        <label for="nama_produk" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="nama_produk" name="nama_produk" value="<?= htmlspecialchars($produk['nama_produk']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="4" required><?= htmlspecialchars($produk['keterangan']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="stok" class="form-label">Stok Barang</label>
                        <input type="text" class="form-control" id="stok" name="stok" min="0" value="<?= $produk['stok'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="berat" class="form-label">Berat (KG)</label>
                        <input type="number" step="0.01" name="berat" id="berat" class="form-control" value="<?= $produk['berat'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="gambar" class="form-label">Gambar Produk</label>
                        <div class="border rounded p-3 text-center" style="background-color: #f0f0f0;">
                            <img id="previewGambar" src="../../backend/uploads/<?= $produk['gambar'] ? $produk['gambar'] : 'https://via.placeholder.com/150x150?text=Preview' ?>" alt="Preview Gambar" class="img-fluid mb-2 rounded" style="max-height: 200px;">
                            <input type="file" class="form-control mt-2" id="gambar" name="gambar" accept="image/*" onchange="previewImage(event)">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="produk.php" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<?php include 'footer.php'; ?>