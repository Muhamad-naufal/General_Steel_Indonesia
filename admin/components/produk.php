<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
?>
<?php
require_once '../../backend/connection/db.php'; // sesuaikan path
?>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 p-0 bg-dark">
            <?php include 'sidebar.php'; ?>
        </div>

        <?php
        // Ambil kata kunci pencarian dan halaman dari URL
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 5;
        $offset = ($page - 1) * $limit;

        // Hitung total produk (dengan filter pencarian jika ada)
        $totalQuery = "SELECT COUNT(*) FROM produk WHERE nama_produk LIKE :search";
        $totalStmt = $pdo->prepare($totalQuery);
        $totalStmt->execute(['search' => '%' . $search . '%']);
        $totalProduk = $totalStmt->fetchColumn();
        $totalPages = ceil($totalProduk / $limit);

        // Ambil data produk untuk halaman ini
        $dataQuery = "SELECT * FROM produk WHERE nama_produk LIKE :search ORDER BY id_produk DESC LIMIT :offset, :limit";
        $stmt = $pdo->prepare($dataQuery);
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $produkList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $no = $offset + 1;
        ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5 mt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Data Produk</h2>
                <a href="tambah.php" class="btn btn-primary">+ Tambah Produk</a>
            </div>

            <form method="get" class="mb-3 d-flex" role="search">
                <input type="text" name="search" value="<?= htmlspecialchars($search); ?>" class="form-control me-2" placeholder="Cari nama produk...">
                <button class="btn btn-outline-secondary" type="submit">Cari</button>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Gambar</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($produkList) > 0): ?>
                            <?php foreach ($produkList as $row): ?>
                                <?php $gambar = !empty($row['gambar']) ? '../../backend/uploads/' . $row['gambar'] : 'https://via.placeholder.com/150x150?text=No+Image'; ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['nama_produk']); ?></td>
                                    <td><img src="<?= $gambar; ?>" alt="Gambar Produk" class="img-fluid" style="max-height: 100px;"></td>
                                    <td><?= htmlspecialchars($row['stok']); ?></td>
                                    <td>
                                        <a href="edit_produk.php?id=<?= $row['id_produk']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <button class="btn btn-sm btn-danger" onclick="konfirmasiHapus(<?= $row['id_produk']; ?>)">Hapus</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada produk ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginasi -->
            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($i === $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?search=<?= urlencode($search); ?>&page=<?= $i; ?>"><?= $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </main>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal-konfirmasi" id="modalKonfirmasi">
    <div class="modal-box shadow">
        <h5 class="mb-3 text-danger">Yakin ingin menghapus produk ini?</h5>
        <p class="text-muted">Aksi ini tidak bisa dibatalkan.</p>
        <div class="text-end mt-4">
            <button class="btn btn-secondary me-2" onclick="tutupModal()">Batal</button>
            <a id="btnHapus" href="#" class="btn btn-danger">Ya, Hapus</a>
        </div>
    </div>
</div>

<style>
    .modal-konfirmasi {
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: fadeIn 0.3s ease-in-out;
    }

    .modal-konfirmasi.active {
        display: flex;
    }

    .modal-box {
        background: #fff;
        border-radius: 12px;
        padding: 30px;
        width: 100%;
        max-width: 400px;
        animation: scaleIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes scaleIn {
        0% {
            transform: scale(0.8);
            opacity: 0;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>

<script>
    function konfirmasiHapus(id) {
        const modal = document.getElementById('modalKonfirmasi');
        const btnHapus = document.getElementById('btnHapus');
        btnHapus.href = "../../backend/admin/proses_hapus_produk.php?id_produk=" + id;
        modal.classList.add('active');
    }

    function tutupModal() {
        document.getElementById('modalKonfirmasi').classList.remove('active');
    }
</script>

<?php include 'footer.php'; ?>