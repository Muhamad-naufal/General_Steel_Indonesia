<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark shadow" style="width: 250px; height: 200vh;">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-2">
            <a href="dashboard.php" class="nav-link text-white <?php echo $currentPage == 'dashboard.php' ? 'active bg-primary' : ''; ?>">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="produk.php" class="nav-link text-white <?php echo $currentPage == 'produk.php' ? 'active bg-primary' : ''; ?>">
                <i class="bi bi-box-seam me-2"></i> Produk
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="pesanan.php" class="nav-link text-white <?php echo $currentPage == 'pesanan.php' ? 'active bg-primary' : ''; ?>">
                <i class="bi bi-bag-check me-2"></i> Pesanan
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="riwayat_pesanan.php" class="nav-link text-white <?php echo $currentPage == 'riwayat_pesanan.php' ? 'active bg-primary' : ''; ?>">
                <i class="bi bi-bag-check me-2"></i> Riwayat Pesanan
            </a>
        </li>
        <!-- Tambah menu lain jika ada -->
    </ul>
    <hr>
    <div class="text-white small">
        &copy; 2025 PT GSI
    </div>
</div>