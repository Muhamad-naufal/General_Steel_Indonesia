<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Resi & Surat Jalan</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 40px;
            background: #f9f9f9;
            color: #333;
        }

        .page {
            background: white;
            border: 2px dashed #333;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 50px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 24px;
        }

        .info {
            margin-bottom: 20px;
        }

        .info div {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        .print-btn {
            display: block;
            width: 120px;
            margin: 30px auto;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        .print-btn:hover {
            background: #0056b3;
        }

        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }

        .signature div {
            width: 45%;
        }

        .ttd-space {
            height: 60px;
        }

        .title {
            font-weight: bold;
            text-transform: uppercase;
        }

        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>

<body>

    <?php
    require_once '../../backend/connection/db.php';

    $id_order = $_GET['id'] ?? null;
    if (!$id_order) die("ID pesanan tidak ditemukan.");

    // Ambil data pesanan
    $stmt = $pdo->prepare("SELECT 
    o.id_order, o.nama, o.kontak, o.harga, o.created_at,
    p.nama_produk, i.jumlah, i.harga_item, o.alamat
FROM orders o
JOIN order_items i ON o.id_order = i.id_order
JOIN produk p ON i.produk_id = p.id_produk
WHERE o.id_order = ?");
    $stmt->execute([$id_order]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) die("Data pesanan tidak ditemukan.");

    // Format data
    $pesanan = [
        'id_order' => $rows[0]['id_order'],
        'nama' => $rows[0]['nama'],
        'kontak' => $rows[0]['kontak'],
        'harga' => $rows[0]['harga'],
        'tanggal' => date('d-m-Y', strtotime($rows[0]['created_at'])),
        'items' => [],
        'alamat' => $rows[0]['alamat'] ?? 'Tidak ada alamat',
    ];

    foreach ($rows as $row) {
        $pesanan['items'][] = [
            'produk' => $row['nama_produk'],
            'jumlah' => $row['jumlah'],
            'harga_item' => $row['harga_item'],
        ];
    }
    $total = 0;

    // Update resi_dicetak
    $updateStmt = $pdo->prepare("UPDATE orders SET status = 'pengiriman', resi_dicetak = 1 WHERE id_order = ?");
    $updateStmt->execute([$id_order]);
    ?>


    <!-- Halaman 1: RESI PENGIRIMAN -->
    <div class="page">
        <h2>📦 RESI PENGIRIMAN</h2>
        <div class="info">
            <div><strong>ID Pesanan:</strong> #<?= $pesanan['id_order'] ?></div>
            <div><strong>Nama Pemesan:</strong> <?= htmlspecialchars($pesanan['nama']) ?></div>
            <div><strong>Kontak:</strong> <?= htmlspecialchars($pesanan['kontak']) ?></div>
            <div><strong>Tanggal Pemesanan:</strong> <?= $pesanan['tanggal'] ?></div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Harga / Item</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pesanan['items'] as $i => $item):
                    $subtotal = $item['jumlah'] * $item['harga_item'];
                    $total += $subtotal;
                ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($item['produk']) ?></td>
                        <td><?= $item['jumlah'] ?></td>
                        <td>Rp <?= number_format($item['harga_item'], 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <table style="width: 50%; float: right; margin-top: 10px;">
            <tbody>
                <tr>
                    <td><strong>Total Harga</strong></td>
                    <td>Rp <?= number_format($total, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td><strong>PPN 11%</strong></td>
                    <td>Rp <?= number_format($total * 0.11, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td><strong>Grand Total</strong></td>
                    <td>Rp <?= number_format($total * 1.11, 0, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>
        <div style="clear: both;"></div>

        <div class="footer">PT General Steel Indonesia — <?= date('Y') ?></div>
    </div>

    <!-- Halaman 2: SURAT JALAN -->
    <div class="page">
        <h2>🚚 SURAT JALAN</h2>
        <div class="info">
            <div><strong>Nomor:</strong> SJ-<?= date('Ymd') ?>-<?= $pesanan['id_order'] ?></div>
            <div><strong>Tanggal:</strong> <?= $pesanan['tanggal'] ?></div>
            <div><strong>Nama Penerima:</strong> <?= htmlspecialchars($pesanan['nama']) ?></div>
            <div><strong>Alamat Tujuan:</strong> <?= htmlspecialchars($pesanan['alamat']) ?></div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pesanan['items'] as $i => $item): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($item['produk']) ?></td>
                        <td><?= $item['jumlah'] ?></td>
                        <td></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="signature">
            <div>
                <div class="title">Diterima Oleh</div>
                <div class="ttd-space"></div>
                <div>(_____________________)</div>
            </div>
            <div>
                <div class="title">Dikirim Oleh</div>
                <div class="ttd-space"></div>
                <div>(PT. General Steel Indonesia)</div>
            </div>
        </div>

        <div class="footer">Dokumen ini dicetak otomatis dari sistem General Steel Indonesia</div>
    </div>

    <!-- Tombol Print -->
    <button class="print-btn" onclick="window.print()">🖨️ Print Dokumen</button>

</body>

</html>