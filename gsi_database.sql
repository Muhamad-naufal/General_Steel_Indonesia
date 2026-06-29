-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 29, 2026 at 08:08 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gsi_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `PASSWORD`, `nama_lengkap`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Admin123', 'Admin User', '2026-06-29 17:53:06', '2026-06-29 17:53:06');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id_order` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(150) NOT NULL,
  `kontak` varchar(150) NOT NULL,
  `metode_kontak` enum('telepon','email') NOT NULL,
  `alamat` text DEFAULT NULL,
  `harga` decimal(18,2) DEFAULT 0.00,
  `STATUS` varchar(40) DEFAULT 'pending',
  `catatan` text NOT NULL,
  `sudah_dihubungi` tinyint(1) DEFAULT 0,
  `remarks_dibuat` tinyint(1) DEFAULT 0,
  `spm_dibuat` tinyint(1) DEFAULT 0,
  `invoice_dilihat` tinyint(1) DEFAULT 0,
  `resi_dicetak` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id_order`, `nama`, `kontak`, `metode_kontak`, `alamat`, `harga`, `STATUS`, `catatan`, `sudah_dihubungi`, `remarks_dibuat`, `spm_dibuat`, `invoice_dilihat`, `resi_dicetak`, `created_at`, `updated_at`) VALUES
(1, 'Sri Adi', 'codeversetechno@gmail.com', 'email', 'Suropati', 13000000.00, 'selesai', 'Bawa Bersih Aja', 1, 1, 1, 1, 1, '2026-06-29 18:01:32', '2026-06-29 18:04:34'),
(2, 'harahap', '0882008862336', 'telepon', 'hello0 bandung', 1600000.00, 'selesai', 'sadasdas', 1, 1, 1, 1, 1, '2026-06-29 18:05:32', '2026-06-29 18:08:14');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id_item` bigint(20) UNSIGNED NOT NULL,
  `id_order` bigint(20) UNSIGNED NOT NULL,
  `produk_id` int(10) UNSIGNED NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `harga_item` decimal(18,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id_item`, `id_order`, `produk_id`, `jumlah`, `harga_item`, `created_at`) VALUES
(1, 1, 1, 20, 200000.00, '2026-06-29 18:01:32'),
(2, 1, 2, 30, 300000.00, '2026-06-29 18:01:32'),
(3, 2, 1, 4, 400000.00, '2026-06-29 18:05:32');

-- --------------------------------------------------------

--
-- Table structure for table `order_remarks`
--

CREATE TABLE `order_remarks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `remark` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_remarks`
--

INSERT INTO `order_remarks` (`id`, `order_id`, `remark`, `created_at`) VALUES
(1, 1, 'Pemesanan Biasa', '2026-06-29 18:02:50'),
(2, 1, 'Pemesanan Mantap', '2026-06-29 18:02:50'),
(3, 2, 'fdgdg', '2026-06-29 18:05:57'),
(4, 2, 'dfgfgf', '2026-06-29 18:05:57');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(10) UNSIGNED NOT NULL,
  `nama_produk` varchar(200) NOT NULL,
  `berat` decimal(10,2) DEFAULT 0.00,
  `stok` int(11) DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `nama_produk`, `berat`, `stok`, `keterangan`, `gambar`, `created_at`, `updated_at`) VALUES
(1, 'Besi Baja', 99.99, 20, 'Besi baja kuat', '6a42b1e9a23ff_Jenis-Jenis-Besi-Baja-kps-steel-distributor-besi-jakarta.webp', '2026-06-29 17:56:57', '2026-06-29 17:56:57'),
(2, 'Besi Hollow', 20.00, 12, 'Besi Hollow', '6a42b24352004_OIP.webp', '2026-06-29 17:58:27', '2026-06-29 17:58:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_username` (`username`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id_order`),
  ADD KEY `idx_order_status` (`STATUS`),
  ADD KEY `idx_order_nama` (`nama`),
  ADD KEY `idx_order_created` (`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `idx_orderitem_order` (`id_order`),
  ADD KEY `idx_orderitem_produk` (`produk_id`);

--
-- Indexes for table `order_remarks`
--
ALTER TABLE `order_remarks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_remark_order` (`order_id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `idx_produk_nama` (`nama_produk`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id_order` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id_item` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_remarks`
--
ALTER TABLE `order_remarks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_produk` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id_produk`) ON UPDATE CASCADE;

--
-- Constraints for table `order_remarks`
--
ALTER TABLE `order_remarks`
  ADD CONSTRAINT `fk_order_remark_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id_order`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
