<?php
require_once '../backend/connection/db.php'; // Sesuaikan path koneksi DB

// Pencarian
$search = $_GET['search'] ?? '';

// Pagination
$limit = 9;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total produk
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM produk WHERE nama_produk LIKE :search");
$countStmt->execute([':search' => '%' . $search . '%']);
$totalProduk = $countStmt->fetchColumn();
$totalPage = ceil($totalProduk / $limit);

// Ambil produk sesuai halaman
$stmt = $pdo->prepare("
    SELECT * FROM produk 
    WHERE nama_produk LIKE :search 
    ORDER BY id_produk DESC 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$produkList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Produk Lengkap - PT General Steel Indonesia</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/styles.css">
    <!-- AOS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="../assets/logo.png" type="image/png">
</head>

<body>

    <?php include '../components/navbar-product.php'; ?>

    <section class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Daftar Produk Lengkap</h2>

            <!-- Form Pencarian -->
            <div class="mb-5">
                <form class="row justify-content-center" method="GET">
                    <div class="col-md-8 col-lg-6">
                        <div class="input-group shadow-sm">
                            <input type="text" name="search" class="form-control form-control-lg rounded-start-pill"
                                placeholder="Cari produk..." value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-primary btn-lg rounded-end-pill px-4" type="submit">
                                <i class="bi bi-search me-2"></i> Cari
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div id="produk-list" class="row g-4">
                <?php if (count($produkList) > 0): ?>
                    <?php foreach ($produkList as $produk): ?>
                        <div class="col-md-4 produk-card" data-aos="fade-up">
                            <div class="card shadow-sm h-100" data-bs-toggle="modal"
                                data-bs-target="#modalProduk<?= $produk['id_produk']; ?>" style="cursor: pointer;">
                                <img src="../backend/uploads/<?= htmlspecialchars($produk['gambar']); ?>"
                                    class="card-img-top" alt="<?= htmlspecialchars($produk['nama_produk']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($produk['nama_produk']); ?></h5>
                                    <p class="card-text">
                                        <?= htmlspecialchars(mb_strimwidth($produk['keterangan'], 0, 100, '...')); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="modalProduk<?= $produk['id_produk']; ?>" tabindex="-1"
                            aria-labelledby="modalProdukLabel<?= $produk['id_produk']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg rounded-4">
                                    <div class="modal-header border-0">
                                        <h5 class="modal-title" id="modalProdukLabel<?= $produk['id_produk']; ?>">
                                            Detail Produk - <?= htmlspecialchars($produk['nama_produk']); ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <img src="../backend/uploads/<?= htmlspecialchars($produk['gambar']); ?>"
                                                    class="img-fluid rounded"
                                                    alt="<?= htmlspecialchars($produk['nama_produk']); ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <h5 class="mb-3 text-primary"><?= htmlspecialchars($produk['nama_produk']); ?></h5>
                                                <p><?= nl2br(htmlspecialchars($produk['keterangan'])); ?></p>
                                                <p class="fw-bold">Stok: <?= htmlspecialchars($produk['stok']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p class="text-center text-muted">Produk tidak ditemukan.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalProduk > $limit): ?>
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php
    include 'components/pesan.php'; // Form pemesanan 
    ?>

    <?php include '../components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true,
            duration: 800
        });
    </script>
    <script src="../assets/js/script.js"></script>
</body>

</html>