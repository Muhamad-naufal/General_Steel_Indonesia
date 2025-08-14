<?php
require_once '../../backend/connection/db.php';
require('../../lib/fpdf/fpdf.php');

function bulanIndonesia($bln)
{
    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];
    return $bulan[intval($bln)];
}

function bulanRomawi($bln)
{
    $romawi = [
        1 => 'I',
        'II',
        'III',
        'IV',
        'V',
        'VI',
        'VII',
        'VIII',
        'IX',
        'X',
        'XI',
        'XII'
    ];
    return $romawi[intval($bln)];
}

function terbilang($angka)
{
    $angka = abs($angka);
    $baca = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
    $temp = "";

    if ($angka < 12) {
        $temp = " " . $baca[$angka];
    } else if ($angka < 20) {
        $temp = terbilang($angka - 10) . " Belas";
    } else if ($angka < 100) {
        $temp = terbilang(intval($angka / 10)) . " Puluh" . terbilang($angka % 10);
    } else if ($angka < 200) {
        $temp = " Seratus" . terbilang($angka - 100);
    } else if ($angka < 1000) {
        $temp = terbilang(intval($angka / 100)) . " Ratus" . terbilang($angka % 100);
    } else if ($angka < 2000) {
        $temp = " Seribu" . terbilang($angka - 1000);
    } else if ($angka < 1000000) {
        $temp = terbilang(intval($angka / 1000)) . " Ribu" . terbilang($angka % 1000);
    } else if ($angka < 1000000000) {
        $temp = terbilang(intval($angka / 1000000)) . " Juta" . terbilang($angka % 1000000);
    }
    return trim($temp);
}

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID tidak valid");
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id_order = ?");
$stmt->execute([$id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) {
    die("Order tidak ditemukan");
}

$stmt_items = $pdo->prepare("
    SELECT oi.*, p.nama_produk
    FROM order_items oi
    JOIN produk p ON oi.produk_id = p.id_produk
    WHERE oi.id_order = ?
");
$stmt_items->execute([$id]);
$items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

$tanggal = date('d');
$bulan = date('n');
$tahun = date('Y');
$nomor_invoice = sprintf("%03d/INV/GSI/%s/%d", $id, bulanRomawi($bulan), $tahun);

$pdf = new FPDF();
$pdf->AddPage();

// --- Kop Surat ---
$pdf->Image('../../admin/assets/images/logo-gsi.jpg', 10, 8, 30);
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(255, 0, 0); // Merah
$pdf->Cell(0, 6, 'PT.GENERAL STEEL INDONESIA', 0, 1, 'C');

$pdf->SetTextColor(0, 0, 0); // Hitam
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'STEEL TRADINGS', 0, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, 'Head Office : Gedung Utaka 21 lt 27B Jalan Let Jend. S. Parman Kav. 21 Slipi Jakarta Barat 11480', 0, 1, 'C');
$pdf->Cell(0, 5, 'Tlp / Fax : 021 8379 2553 email : generalsteelindonesia@gmail.com', 0, 1, 'C');
$pdf->Ln(3);
$pdf->Line(10, 32, 200, 32);
$pdf->Ln(5);

// Judul Invoice
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 6, 'INVOICE', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, $nomor_invoice, 0, 1, 'C');
$pdf->Ln(5);

// Kepada Yth
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'Kepada Yth,', 0, 1);
$pdf->Cell(0, 6, $order['nama'], 0, 1);
$pdf->MultiCell(0, 6, $order['alamat']);
$pdf->Ln(2);

// Tabel Header
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(10, 8, 'No', 1, 0, 'C');
$pdf->Cell(80, 8, 'Nama Barang', 1, 0, 'C');
$pdf->Cell(30, 8, 'Kuantiti', 1, 0, 'C');
$pdf->Cell(35, 8, 'Harga', 1, 0, 'C');
$pdf->Cell(35, 8, 'Total Harga', 1, 1, 'C');

// Isi tabel
$pdf->SetFont('Arial', '', 9);
$total = 0;
$no = 1;
foreach ($items as $item) {
    $subtotal = $item['jumlah'] * $item['harga_item'];
    $pdf->Cell(10, 8, $no++, 1, 0, 'C');
    $pdf->Cell(80, 8, $item['nama_produk'], 1);
    $pdf->Cell(30, 8, $item['jumlah'], 1, 0, 'C');
    $pdf->Cell(35, 8, number_format($item['harga_item'], 0, ',', '.'), 1, 0, 'R');
    $pdf->Cell(35, 8, number_format($subtotal, 0, ',', '.'), 1, 1, 'R');
    $total += $subtotal;
}

// Baris tambahan
$pdf->Cell(120, 8, 'Nomor Purchase Order :', 1);
$pdf->Cell(35, 8, 'Jumlah Harga', 1, 0, 'R');
$pdf->Cell(35, 8, number_format($total, 0, ',', '.'), 1, 1, 'R');

// Terbilang (bold & rapi)
$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 6, 'Terbilang : ' . terbilang($total) . ' Rupiah', 0, 1);

// Info pembayaran (dalam kotak)
$pdf->Ln(3);
$pdf->SetFont('Arial', '', 9);
$rekening = "Pembayaran Harap diatur Atas Nama : \nPT.GENERAL STEEL INDONESIA\nRek : 579-0404-132\nBank Central Asia Cab Rawasari";
$pdf->MultiCell(0, 5, $rekening, 1, 'L');

// Tanggal & tanda tangan
$pdf->Ln(2);
$pdf->Cell(0, 6, 'Jakarta, ' . $tanggal . ' ' . bulanIndonesia($bulan) . ' ' . $tahun, 0, 1, 'R');
$pdf->Ln(15);
$pdf->Cell(0, 6, 'Shania Aiko Simpedely', 0, 1, 'R');
$pdf->Cell(0, 6, 'Admin', 0, 1, 'R');

// Ketentuan
$pdf->Ln(5);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 5, 'Note : Pembayaran diproses setelah dana masuk rekening perusahaan kami', 0, 1);

$filename = 'invoice_' . str_replace(' ', '_', $order['nama']) . '.pdf';
$pdf->Output('I', $filename);
