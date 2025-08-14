<section id="produk" class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold" data-aos="fade-up">Produk Unggulan</h2>
            <p class="text-muted" data-aos="fade-up" data-aos-delay="100">
                Kami menyediakan berbagai material berkualitas tinggi untuk kebutuhan konstruksi Anda.
            </p>
        </div>

        <div class="row g-4">
            <?php
            require_once 'backend/connection/db.php'; // sesuaikan path

            $stmt = $pdo->query("SELECT nama_produk, keterangan, gambar FROM produk ORDER BY created_at DESC LIMIT 4");
            while ($produk = $stmt->fetch(PDO::FETCH_ASSOC)):
                $gambar = !empty($produk['gambar']) ? 'uploads/' . $produk['gambar'] : 'assets/default.jpg';
            ?>
                <div class="col-md-6 col-lg-3" data-aos="zoom-in">
                    <div class="card h-100 shadow-sm">
                        <img src="backend/<?= htmlspecialchars($gambar) ?>" class="card-img-top" alt="<?= htmlspecialchars($produk['nama_produk']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($produk['nama_produk']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($produk['keterangan']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Tombol Lihat Semua Produk -->
        <div class="text-center mt-4">
            <a href="page/product.php" class="btn btn-outline-primary btn-lg px-4" data-aos="fade-up" data-aos-delay="100">
                Lihat Semua Produk <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>