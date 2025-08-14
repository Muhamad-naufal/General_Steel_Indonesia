<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once '../../backend/connection/db.php';

// Jumlah produk
$stmtProduk = $pdo->query("SELECT COUNT(*) FROM produk");
$totalProduk = $stmtProduk->fetchColumn();

// Jumlah pesanan yang resi sudah dicetak (penjualan sukses)
$stmtPenjualan = $pdo->query("SELECT COUNT(*) FROM orders WHERE resi_dicetak = 1");
$totalPenjualan = $stmtPenjualan->fetchColumn();

// Data chart penjualan per bulan (hanya yang resi sudah dicetak)
$stmtChart = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as bulan, COUNT(*) as total
    FROM orders
    WHERE resi_dicetak = 1
    GROUP BY bulan
    ORDER BY bulan ASC
");

$dataChart = [];
while ($row = $stmtChart->fetch(PDO::FETCH_ASSOC)) {
    $dataChart[] = $row;
}
// Ambil total pesanan berdasarkan status
$statusCounts = [];
$statusList = ['menunggu', 'negosiasi', 'pengiriman', 'selesai', 'batal'];
foreach ($statusList as $status) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE status = ?");
    $stmt->execute([$status]);
    $statusCounts[$status] = $stmt->fetchColumn();
}

?>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0 bg-dark">
            <?php include 'sidebar.php'; ?>
        </div>

        <?php

        ?>

        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 ps-md-5 mt-4"> <!-- Tambahkan ps-md-5 untuk padding kiri -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
            </div>

            <div class="row g-4" data-aos="fade-up">
                <div class="col-md-4">
                    <div class="card text-white bg-primary shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Total Produk</h5>
                            <p class="card-text fs-4"><?= $totalProduk ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Total Penjualan</h5>
                            <p class="card-text fs-4"><?= $totalPenjualan ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-1" data-aos="fade-up">
                <div class="col-md-3">
                    <div class="card bg-secondary text-white shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Menunggu</h6>
                            <p class="card-text fs-5"><?= $statusCounts['menunggu'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-dark shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Negosiasi</h6>
                            <p class="card-text fs-5"><?= $statusCounts['negosiasi'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-primary text-white shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Pengiriman</h6>
                            <p class="card-text fs-5"><?= $statusCounts['pengiriman'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Selesai</h6>
                            <p class="card-text fs-5"><?= $statusCounts['selesai'] ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-1" data-aos="fade-up">
                <div class="col-md-3">
                    <div class="card bg-danger text-white shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Dibatalkan</h6>
                            <p class="card-text fs-5"><?= $statusCounts['batal'] ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Penjualan -->
            <div class="card shadow-sm mt-5" data-aos="fade-up">
                <div class="card-body">
                    <h5 class="card-title">Grafik Penjualan per Bulan</h5>
                    <canvas id="penjualanChart" width="300" height="300"></canvas>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'footer.php'; ?>

<!-- CDN Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('penjualanChart').getContext('2d');
    const penjualanChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($dataChart, 'bulan')) ?>,
            datasets: [{
                label: 'Jumlah Penjualan',
                data: <?= json_encode(array_column($dataChart, 'total')) ?>,
                backgroundColor: [
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(0, 123, 255, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(220, 53, 69, 0.7)',
                    'rgba(108, 117, 125, 0.7)',
                    'rgba(23, 162, 184, 0.7)'
                ],
                borderColor: '#fff',
                borderWidth: 1
            }]
        },
        options: {
            responsive: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12
                    }
                }
            }
        }
    });
</script>