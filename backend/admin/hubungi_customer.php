<?php
require_once '../../backend/connection/db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID tidak valid");
}

// Ambil data customer
$stmt = $pdo->prepare("SELECT nama, kontak, metode_kontak FROM orders WHERE id_order = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Pesanan tidak ditemukan");
}

// Update status sudah dihubungi
$pdo->prepare("UPDATE orders SET sudah_dihubungi = 1, status = 'customer_dihubungi' WHERE id_order = ?")->execute([$id]);

// Bersihkan nama pelanggan agar aman untuk nama file
$nama_file = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($data['nama']));

// Buat link ke file spm dengan nama file dari nama pelanggan
$spm_link = "https://ekaw.rf.gd/project/gsi/admin/assets/spm/spm_{$nama_file}.pdf";

// Redirect ke WhatsApp atau Email
if ($data['metode_kontak'] === 'telepon') {
    // Bersihkan nomor: hanya angka
    $no_wa = preg_replace('/[^0-9]/', '', $data['kontak']);

    // Konversi 08xxxx menjadi 62xxxx (kode negara Indonesia)
    if (substr($no_wa, 0, 1) === '0') {
        $no_wa = '62' . substr($no_wa, 1);
    }

    // Buat pesan WhatsApp dengan format profesional
    $pesan = "Halo *{$data['nama']}*,\n\n"
        . "Terima kasih telah mempercayakan kebutuhan material Anda kepada *PT. General Steel Indonesia*.\n\n"
        . "Berikut adalah tautan SPM pesanan Anda:\n$spm_link\n\n"
        . "Jika ada pertanyaan atau klarifikasi, silakan hubungi kami kapan saja.\n\n"
        . "Salam hormat,\n"
        . "*PT. General Steel Indonesia*";

    // Redirect ke WhatsApp
    header("Location: https://wa.me/{$no_wa}?text=" . urlencode($pesan));
    exit;
} elseif ($data['metode_kontak'] === 'email') {
    // Subjek email
    $subject = "Penawaran Harga Pesanan Anda - PT. General Steel Indonesia";

    $body = "Yth. {$data['nama']},\n\n"
        . "Terima kasih atas kepercayaan Anda kepada PT. General Steel Indonesia.\n\n"
        . "Silakan unduh Penawaran Harga pesanan Anda melalui tautan berikut:\n"
        . "$spm_link\n\n"
        . "Apabila ada hal yang ingin dikonfirmasi, jangan ragu untuk menghubungi kami.\n\n"
        . "Salam hangat,\n"
        . "Tim Penjualan\n"
        . "PT. General Steel Indonesia\n"
        . "Email: general.steelindonesia@gmail.com\n"
        . "Telepon: 0812-9444-3660";

    // Gunakan rawurlencode untuk email
    $mailto = "mailto:{$data['kontak']}?subject=" . rawurlencode($subject) . "&body=" . rawurlencode($body);
    header("Location: $mailto");
    exit;
} else {
    echo "⚠️ Metode kontak tidak dikenali. Silakan cek data customer.";
}
