<!-- Section Pemesanan -->
<section id="pesan" class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-4">Formulir Pemesanan</h2>
        <p class="text-center mb-5 text-muted">Silakan isi form di bawah ini untuk memesan satu atau beberapa produk</p>

        <form id="form-pemesanan" action="../backend/users/order-process.php" method="POST" class="needs-validation" novalidate>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama" required>
                </div>
                <div class="col-md-6">
                    <label for="metode_kontak" class="form-label">Kirim Invoice Via</label>
                    <select name="metode_kontak" id="metode_kontak" class="form-select" required>
                        <option value="" disabled selected>-- Pilih Metode --</option>
                        <option value="telepon">WhatsApp</option>
                        <option value="email">Email</option>
                    </select>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <label for="alamat" class="form-label">Alamat Lengkap</label>
                    <textarea class="form-control" name="alamat" id="alamat" rows="2" required></textarea>
                </div>
            </div>

            <div class="row mb-4" id="input-telepon" style="display: none;">
                <div class="col-md-12">
                    <label for="telepon" class="form-label">No. WhatsApp</label>
                    <input type="text" class="form-control" name="telepon">
                </div>
            </div>

            <div class="row mb-4" id="input-email" style="display: none;">
                <div class="col-md-12">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input type="email" class="form-control" name="email">
                </div>
            </div>

            <script>
                document.getElementById('metode_kontak').addEventListener('change', function() {
                    const metode = this.value;
                    document.getElementById('input-telepon').style.display = metode === 'telepon' ? 'block' : 'none';
                    document.getElementById('input-email').style.display = metode === 'email' ? 'block' : 'none';
                });
            </script>

            <!-- Produk Dinamis -->
            <div id="produk-wrapper">
                <div class="row g-3 align-items-end produk-item mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Pilih Produk</label>
                        <select name="produk[]" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Produk --</option>
                            <?php
                            $produk = $pdo->query("SELECT id_produk, nama_produk FROM produk ORDER BY nama_produk ASC")->fetchAll();
                            foreach ($produk as $p) {
                                echo "<option value='{$p['id_produk']}'>{$p['nama_produk']}</option>";
                            }
                            ?>
                        </select>


                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="jumlah[]" class="form-control" min="1" required>
                    </div>
                    <div class="col-md-2 text-end">
                        <button type="button" class="btn btn-danger remove-produk d-none">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <button type="button" class="btn btn-outline-secondary" id="tambah-produk">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Produk
                </button>
            </div>

            <div class="mb-4">
                <label for="catatan" class="form-label">Catatan Tambahan</label>
                <textarea class="form-control" name="catatan" rows="3"></textarea>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary px-5 rounded-pill">
                    <i class="bi bi-send me-1"></i> Kirim Pesanan
                </button>
            </div>
        </form>
    </div>
</section>