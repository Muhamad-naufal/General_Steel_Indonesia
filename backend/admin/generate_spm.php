<?php
require_once '../connection/db.php';
require('../../lib/fpdf/fpdf.php');

$id_order = $_GET['id'] ?? null;
if (!$id_order) die("ID pesanan tidak ditemukan.");

$stmt = $pdo->prepare("SELECT o.nama, o.kontak, o.harga, i.jumlah, i.harga_item, p.nama_produk
                       FROM orders o
                       JOIN order_items i ON o.id_order = i.id_order
                       JOIN produk p ON i.produk_id = p.id_produk
                       WHERE o.id_order = ?");
$stmt->execute([$id_order]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($data)) die("Data pesanan tidak ditemukan.");

$nama = $data[0]['nama'];
$total = $data[0]['harga'];
function tanggalIndonesia($tanggal)
{
    $bulanIndo = [
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember',
    ];
    $tgl = date('j', strtotime($tanggal));
    $bln = $bulanIndo[date('F', strtotime($tanggal))];
    $thn = date('Y', strtotime($tanggal));
    return "$tgl $bln $thn";
}

$tanggalSekarang = date('Y-m-d');
$tanggalIndo = tanggalIndonesia($tanggalSekarang);

$pdf = new FPDF();
$pdf->AddPage();

// Logo dan Header
$pdf->Image('../../admin/assets/images/logo-gsi.jpg', 10, 8, 30);
$pdf->SetTextColor(200, 0, 0);
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(0, 10, 'PT. GENERAL STEEL INDONESIA', 0, 1, 'C');
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'I', 13);
$pdf->Cell(0, 6, 'STEEL TRADINGS', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'Head Office : Gedung Utaka 87 #206 Jalan Utan Kayu Raya No.87 Jakarta Timur', 0, 1, 'C');
$pdf->Cell(0, 5, 'Tlp / Fax : 021 8591 2532  Email : general.steelindonesia@gmail.com', 0, 1, 'C');

// Garis bawah
$pdf->SetLineWidth(0.2);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(1);
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// Kepada
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'Kepada Yth.', 0, 1);
$pdf->Cell(0, 6, $nama, 0, 1);
$pdf->Cell(0, 6, 'Di Tempat', 0, 1);
$pdf->Cell(0, 6, 'Up ' . ($data[0]['kontak'] ?? '-'), 0, 1);
$pdf->Ln(4);

// Tanggal & Nomor Surat
$pdf->SetFont('Arial', '', 10);
$pdf->Ln(1);

$tanggalSurat = date('dm'); // contoh: 0508
$tahunSurat = date('Y');    // contoh: 2025
$nomorSurat = "SPM/" . str_pad($id_order, 3, '0', STR_PAD_LEFT) . "/{$tanggalSurat}/GSI/{$tahunSurat}";
$pdf->Cell(0, 6, "No. : $nomorSurat", 0, 1);
$pdf->Ln(2);

// Perihal
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, 'Perihal : Penawaran Material', 0, 1);
$pdf->Ln(4);

// Pembuka
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 6, "Dengan Hormat,\n\nPerkenankanlah, kami PT. General Steel Indonesia yang merupakan Distributor Besi Baja \"SNI\", dan berbagai Material lainnya, dengan ini kami menawarkan produk sebagai berikut :");
$pdf->Ln(3);

// Tabel
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 8, 'No', 1);
$pdf->Cell(80, 8, 'Deskripsi', 1);
$pdf->Cell(20, 8, 'Jumlah', 1);
$pdf->Cell(35, 8, 'Harga / SET', 1);
$pdf->Cell(40, 8, 'Total Harga', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$no = 1;
foreach ($data as $row) {
    $subtotal = $row['jumlah'] * $row['harga_item'];

    $pdf->Cell(10, 8, $no++, 1);
    $pdf->Cell(80, 8, $row['nama_produk'], 1);
    $pdf->Cell(20, 8, $row['jumlah'] . ' Pcs', 1, 0, 'C');
    $pdf->Cell(35, 8, 'Rp ' . number_format($row['harga_item'], 0, ',', '.'), 1, 0, 'R');
    $pdf->Cell(40, 8, 'Rp ' . number_format($subtotal, 0, ',', '.'), 1, 0, 'R');
    $pdf->Ln();
}
$pdf->Ln(4);

// Tambahkan total harga akhir
$grandTotal = array_sum(array_column($data, 'harga_item')) * array_sum(array_column($data, 'jumlah'));

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, "Total Harga: Rp " . number_format($grandTotal, 0, ',', '.'), 0, 1);

// Remarks
$pdf->SetFont('Arial', '', 9);
// Ambil remarks dari database atau sumber lain
$remarksStmt = $pdo->prepare("SELECT remark FROM order_remarks WHERE order_id = ?");
$remarksStmt->execute([$id_order]);
$remarksData = $remarksStmt->fetchAll(PDO::FETCH_ASSOC);

if ($remarksData) {
    $pdf->MultiCell(0, 6, "Remarks:", 0, 'L');
    $no = 1;
    foreach ($remarksData as $remarkRow) {
        $remarkText = htmlspecialchars($remarkRow['remark']);
        $pdf->MultiCell(0, 6, "{$no}. {$remarkText}", 0, 'L');
        $no++;
    }
} else {
    $pdf->MultiCell(0, 6, "Remarks: -", 0, 'L');
}
$pdf->Ln(6);

// Tanggal & Penutup
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Jakarta, $tanggalIndo", 0, 1);
$pdf->Ln(4);
$pdf->Cell(0, 6, "Hormat kami,", 0, 1);
$pdf->Ln(15);
$pdf->SetFont('Arial', 'U', 10);
$pdf->Cell(0, 6, "Lukman Fahlevi Saputra", 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "PT. General Steel Indonesia", 0, 1);

// Output
$nama_file = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($nama));
$path_simpan = "../../admin/assets/invoices/penawaran_{$nama_file}.pdf";
$pdf->Output('F', $path_simpan);
$pdf->Output('I', "penawaran_{$nama_file}.pdf");
exit;
