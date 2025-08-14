<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<?php require_once '../../backend/connection/db.php'; ?>

<?php
$id_order = $_GET['id'] ?? null;

if (!$id_order) {
    echo "<div class='alert alert-danger text-center'>ID pesanan tidak ditemukan.</div>";
    exit;
}

$stmt = $pdo->prepare("SELECT o.nama, o.kontak, i.jumlah, i.id_item, p.nama_produk FROM orders o JOIN order_items i ON o.id_order = i.id_order JOIN produk p ON i.produk_id = p.id_produk WHERE o.id_order = :id");
$stmt->execute([':id' => $id_order]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$data) {
    echo "<div class='alert alert-warning text-center'>Pesanan tidak ditemukan.</div>";
    exit;
}

$nama = $data[0]['nama'];
$kontak = $data[0]['kontak'];
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 p-0 bg-dark">
            <?php include 'sidebar.php'; ?>
        </div>

        <main class="col-md-9 col-lg-10 ps-md-5 mt-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                <h2>Tentukan Harga Pesanan</h2>
            </div>

            <div class="card shadow-sm p-4" data-aos="fade-up">
                <form action="../../backend/admin/process_perbaikan_harga.php" method="POST" oninput="
    let total = 0;
    document.querySelectorAll('.harga-item').forEach((input, index) => {
        const jumlah = parseInt(document.querySelectorAll('.jumlah-item')[index].value) || 0;
        const harga = parseInt(input.value) || 0;
        total += harga * jumlah;
    });
    document.getElementById('total-harga').value = total;
">
                    <h5 class="mb-4">Detail Pesanan</h5>
                    <input type="hidden" name="id_order" value="<?= htmlspecialchars($id_order); ?>">

                    <div class="mb-3">
                        <label class="form-label">Nama Pemesan</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($nama); ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kontak</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($kontak); ?>" disabled>
                    </div>

                    <?php foreach ($data as $item): ?>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Produk</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($item['nama_produk']); ?>" disabled>
                                <input type="hidden" name="id_item[]" value="<?= $item['id_item']; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jumlah</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($item['jumlah']); ?>" disabled>
                                <input type="hidden" class="jumlah-item" value="<?= $item['jumlah']; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Harga (per item)</label>
                                <input type="number" class="form-control harga-item" name="harga_item[]" required>
                            </div>
                        </div>
                    <?php endforeach; ?>


                    <div class="mb-3">
                        <label class="form-label fw-bold">Total Harga</label>
                        <input type="text" class="form-control" id="total-harga" name="total_harga" readonly>
                    </div>

                    <button type="submit" class="btn btn-success">Simpan Harga</button>
                    <a href="pesanan.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </main>
    </div>
</div>

<?php include 'footer.php'; ?>