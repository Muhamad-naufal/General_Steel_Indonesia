<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<?php require_once '../../backend/connection/db.php'; ?>

<!-- Tambahkan SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$searchQuery = !empty($search) ? "WHERE o.nama LIKE :search" : '';

$countStmt = $pdo->prepare("SELECT COUNT(DISTINCT o.id_order) FROM orders o $searchQuery");
if (!empty($search)) {
    $countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$countStmt->execute();
$totalOrders = $countStmt->fetchColumn();
$totalPages = ceil($totalOrders / $limit);

$idStmt = $pdo->prepare("SELECT DISTINCT o.id_order
                         FROM orders o
                         $searchQuery
                         ORDER BY o.id_order DESC
                         LIMIT :start, :limit");
if (!empty($search)) {
    $idStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$idStmt->bindValue(':start', $start, PDO::PARAM_INT);
$idStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$idStmt->execute();
$order_ids = $idStmt->fetchAll(PDO::FETCH_COLUMN);

$orders = [];
if (!empty($order_ids)) {
    $placeholders = implode(',', array_fill(0, count($order_ids), '?'));
    $stmt = $pdo->prepare("SELECT o.id_order, o.nama, o.metode_kontak, o.catatan, o.kontak, o.sudah_dihubungi, o.harga, o.status, o.resi_dicetak, o.invoice_dilihat, p.nama_produk, i.jumlah 
                            FROM orders o 
                            JOIN order_items i ON o.id_order = i.id_order 
                            JOIN produk p ON i.produk_id = p.id_produk 
                            WHERE o.id_order IN ($placeholders)
                            ORDER BY o.id_order DESC");
    $stmt->execute($order_ids);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id_order'];
        if (!isset($orders[$id])) {
            $orders[$id] = [
                'nama' => $row['nama'],
                'metode_kontak' => $row['metode_kontak'],
                'kontak' => $row['kontak'],
                'harga' => $row['harga'],
                'status' => $row['status'],
                'catatan' => $row['catatan'] ?? '',
                'sudah_dihubungi' => $row['sudah_dihubungi'],
                'resi_dicetak' => $row['resi_dicetak'],
                'invoice_dilihat' => $row['invoice_dilihat'],
                'produk' => [],
            ];
        }
        $orders[$id]['produk'][] = [
            'nama' => $row['nama_produk'],
            'jumlah' => $row['jumlah']
        ];
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 p-0 bg-dark">
            <?php include 'sidebar.php'; ?>
        </div>

        <main class="col-md-9 col-lg-10 ps-md-5 mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h2">Permintaan Pesanan</h1>
                <form class="d-flex" method="GET">
                    <input class="form-control me-2" type="search" name="search" placeholder="Cari nama pemesan" value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-outline-primary" type="submit">Cari</button>
                </form>
            </div>

            <div class="card shadow-sm p-4" data-aos="fade-up">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Pemesan</th>
                                <th>Produk</th>
                                <th>Jumlah</th>
                                <th>Catatan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (empty($orders)) {
                                echo '<tr><td colspan="7" class="text-center text-muted">Belum ada pesanan.</td></tr>';
                            } else {
                                $no = $start + 1;
                                foreach ($orders as $id_order => $data) {
                                    $statusText = 'Menunggu';
                                    $badgeClass = 'bg-secondary';
                                    $actionButton = '';

                                    switch ($data['status']) {
                                        case 'menunggu':
                                            $statusText = 'Menunggu Harga';
                                            $badgeClass = 'bg-warning text-dark';
                                            $actionButton = "<a href='tentukan_harga.php?id=$id_order' class='btn btn-sm btn-primary mb-1'>Tentukan Harga</a>";
                                            break;

                                        case 'harga_ditentukan':
                                            $statusText = 'Menunggu Remarks';
                                            $badgeClass = 'bg-secondary';
                                            $actionButton = "<a href='buat_remarks.php?id_order=$id_order' class='btn btn-sm btn-secondary mb-1'>Buat Remarks</a>";
                                            break;

                                        case 'remarks_dibuat':
                                            $statusText = 'Menunggu SPM';
                                            $badgeClass = 'bg-dark text-white';
                                            $actionButton = "<a href='../../backend/admin/proses_lihat_spm.php?id=$id_order' class='btn btn-sm btn-dark mb-1'>Buat SPM</a>";
                                            break;

                                        case 'spm_dibuat':
                                            $statusText = 'Menunggu Dihubungi';
                                            $badgeClass = 'bg-success text-white';
                                            $metodeKontak = strtolower($data['metode_kontak']);
                                            $labelKontak = '';
                                            if ($metodeKontak === 'email') {
                                                $labelKontak = ' (Email)';
                                            } elseif ($metodeKontak === 'telepon' || $metodeKontak === 'telp' || $metodeKontak === 'phone') {
                                                $labelKontak = ' (Telepon)';
                                            }
                                            $actionButton = "<a href='../../backend/admin/hubungi_customer.php?id=$id_order' class='btn btn-sm btn-success mb-1'>Hubungi Customer$labelKontak</a>";
                                            break;

                                        case 'customer_dihubungi':
                                            $statusText = 'Menunggu Respons Customer';
                                            $badgeClass = 'bg-info';
                                            // opsional: tombol follow-up atau next ke negosiasi
                                            $actionButton = "
                                                <a href='perbaiki_harga.php?id=$id_order' class='btn btn-sm btn-warning mb-1'>Perbaiki Harga</a>
                                                <a href='../../backend/admin/proses_buat_invoice.php?id=$id_order' class='btn btn-sm btn-primary mb-1'>Lanjut Buat Invoice</a>
                                            ";
                                            break;

                                        case 'cetak_resi':
                                            $statusText = 'Resi Dicetak';
                                            $badgeClass = 'bg-info';
                                            $actionButton = "<a href='../../backend/admin/cetak_resi.php?id=$id_order' class='btn btn-sm btn-info mb-1'>Cetak Resi</a>";
                                            break;

                                        case 'pengiriman':
                                            $statusText = 'Dalam Pengiriman';
                                            $badgeClass = 'bg-primary';
                                            $actionButton = "
            <a href='../../backend/admin/selesaikan_pesanan.php?id=$id_order' class='btn btn-sm btn-success mb-1'>Selesaikan</a>
                                            ";
                                            break;

                                        case 'selesai':
                                            $statusText = 'Selesai';
                                            $badgeClass = 'bg-success';
                                            break;

                                        case 'batal':
                                            $statusText = 'Dibatalkan';
                                            $badgeClass = 'bg-danger';
                                            break;
                                    }

                                    $produkList = '';
                                    $jumlahList = '';
                                    foreach ($data['produk'] as $item) {
                                        $produkList .= htmlspecialchars($item['nama']) . '<br>';
                                        $jumlahList .= htmlspecialchars($item['jumlah']) . '<br>';
                                    }
                                    $catatan = $data['catatan'] ?? 'Tidak ada catatan';
                                    echo "<tr>
                                        <td>$no</td>
                                        <td>" . htmlspecialchars($data['nama']) . "</td>
                                        <td>$produkList</td>
                                        <td>$jumlahList</td>
                                        <td>$catatan</td>
                                        <td><span class='badge $badgeClass'>$statusText</span></td>
                                        <td>
                                            $actionButton
                                            " . (!in_array($data['status'], ['batal', 'selesai']) ? "<a href='../../backend/admin/batalkan_pesanan.php?id=$id_order' class='btn btn-sm btn-danger mb-1' onclick=\"return confirm('Yakin ingin membatalkan pesanan ini?')\">Batalkan</a>" : '') . "
                                        </td>
                                    </tr>";
                                    $no++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center mt-3">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            </div>
        </main>
    </div>
</div>

<script>
    function confirmCetakResi(id, harga) {
        Swal.fire({
            title: 'Konfirmasi Harga',
            text: 'Apakah Anda yakin harga ini sudah deal dengan customer?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Cetak Resi',
            cancelButtonText: 'Belum Deal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../../backend/admin/cetak_resi.php?id=' + id;
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                window.location.href = 'tentukan_harga.php?id=' + id;
            }
        });
    }
</script>

<?php include 'footer.php'; ?>