<?php
require_once '../../backend/connection/db.php';
include 'header.php';
include 'navbar.php';

// Ambil parameter search & pagination
$search = $_GET['search'] ?? '';
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Filter search
$searchQuery = '';
$params = [];
if (!empty($search)) {
    $searchQuery = 'WHERE o.nama LIKE :search';
    $params[':search'] = "%$search%";
}

// Hitung total data untuk pagination
$countQuery = "SELECT COUNT(DISTINCT o.id_order) FROM orders o $searchQuery";
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($params);
$totalOrders = $countStmt->fetchColumn();
$totalPages = ceil($totalOrders / $limit);

// Ambil ID sesuai halaman
$idQuery = "SELECT DISTINCT o.id_order FROM orders o $searchQuery ORDER BY o.id_order DESC LIMIT :start, :limit";
$idStmt = $pdo->prepare($idQuery);
if (!empty($search)) {
    $idStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$idStmt->bindValue(':start', $start, PDO::PARAM_INT);
$idStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$idStmt->execute();
$orderIds = $idStmt->fetchAll(PDO::FETCH_COLUMN);

// Ambil detail pesanan
$orders = [];
if (!empty($orderIds)) {
    $inQuery = implode(',', array_fill(0, count($orderIds), '?'));
    $stmt = $pdo->prepare("SELECT o.id_order, o.nama, o.kontak, o.status, o.harga, o.created_at, p.nama_produk, i.jumlah
        FROM orders o 
        JOIN order_items i ON o.id_order = i.id_order 
        JOIN produk p ON i.produk_id = p.id_produk 
        WHERE o.id_order IN ($inQuery)
        ORDER BY o.id_order DESC");
    $stmt->execute($orderIds);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id_order'];
        if (!isset($orders[$id])) {
            $orders[$id] = [
                'tanggal' => date('d-m-Y', strtotime($row['created_at'])),
                'nama' => $row['nama'],
                'harga' => $row['harga'],
                'status' => $row['status'],
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
                <h1 class="h2">Riwayat Pemesanan</h1>
                <form class="d-flex" method="GET">
                    <input class="form-control me-2" type="search" name="search" placeholder="Cari nama pemesan..." value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-outline-primary" type="submit">Cari</button>
                </form>
            </div>

            <div class="card shadow-sm p-4" data-aos="fade-up">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Pemesan</th>
                                <th>Produk</th>
                                <th>Total Harga</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (empty($orders)) {
                                echo '<tr><td colspan="7" class="text-center text-muted">Data tidak ditemukan.</td></tr>';
                            } else {
                                $no = $start + 1;
                                foreach ($orders as $id_order => $data) {
                                    $produkList = '';
                                    foreach ($data['produk'] as $item) {
                                        $produkList .= htmlspecialchars($item['nama']) . ' x' . $item['jumlah'] . '<br>';
                                    }

                                    $badgeClass = match ($data['status']) {
                                        'menunggu' => 'bg-warning text-dark',
                                        'negosiasi' => 'bg-info text-dark',
                                        'pengiriman' => 'bg-primary',
                                        'selesai' => 'bg-success',
                                        'batal' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };

                                    $aksi = "";
                                    if ($data['status'] === 'selesai') {
                                        $aksi = "<a href='../../backend/admin/generate_invoice.php?id=$id_order' class='btn btn-sm btn-outline-primary mb-1'>Invoice</a>
                                                 <a href='../../backend/admin/cetak_resi.php?id=$id_order' class='btn btn-sm btn-outline-warning mb-1'>Resi</a>";
                                    } elseif ($data['status'] === 'batal') {
                                        $aksi = "<span class='text-muted'>Dibatalkan</span>";
                                    }

                                    echo "<tr>
                                        <td>$no</td>
                                        <td>{$data['tanggal']}</td>
                                        <td>" . htmlspecialchars($data['nama']) . "</td>
                                        <td>$produkList</td>
                                        <td>Rp " . number_format($data['harga'], 0, ',', '.') . "</td>
                                        <td><span class='badge $badgeClass'>" . ucfirst($data['status']) . "</span></td>
                                        <td>$aksi</td>
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

<?php include 'footer.php'; ?>