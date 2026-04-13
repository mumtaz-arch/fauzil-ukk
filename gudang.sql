-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 12, 2026 at 09:06 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gudang`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` int NOT NULL,
  `kode_barang` varchar(20) NOT NULL,
  `nama_barang` varchar(50) NOT NULL,
  `varian_barang` varchar(50) DEFAULT NULL,
  `stok_barang` int NOT NULL,
  `keterangan` varchar(200) NOT NULL,
  `harga_satuan` int NOT NULL,
  `harga_jual` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `kode_barang`, `nama_barang`, `varian_barang`, `stok_barang`, `keterangan`, `harga_satuan`, `harga_jual`) VALUES
(12, 'ROTI001', 'roti tawar', 'Coklat', 70, '', 3000, 4500),
(13, 'ROTI002', 'roti tawar', 'mangga', 14, '', 5000, 7500),
(14, 'ROTI003', 'roti tawar', 'kacang', 0, '', 2000, 3000),
(15, 'ROTI004', 'roti tawar', 'durian', 12, '', 5000, 7500),
(16, 'ROTI0052', 'roti sddsdbulat', 'mangga', 44, '', 2000, 31000),
(18, 'ROTI053', 'sabagsdas', '312dfs', 112, '', 123123, 184685);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int NOT NULL,
  `id_barang` int NOT NULL,
  `jenis` enum('masuk','keluar') NOT NULL,
  `jumlah` int NOT NULL,
  `tanggal_transaksi` int NOT NULL,
  `keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_barang`, `jenis`, `jumlah`, `tanggal_transaksi`, `keterangan`) VALUES
(1, 13, 'masuk', 1, 20260124, ''),
(2, 13, 'masuk', 1, 20260124, ''),
(3, 12, 'keluar', 2, 20260124, ''),
(4, 13, 'masuk', 2, 20260124, ''),
(5, 12, 'keluar', 1, 20260124, ''),
(6, 13, 'masuk', 3, 20260202, ''),
(7, 14, 'masuk', 1, 20260202, ''),
(8, 16, 'keluar', 2, 20260204, ''),
(9, 14, 'masuk', 1, 20260204, ''),
(10, 14, 'masuk', 1, 20260205, ''),
(11, 16, 'keluar', 1, 20260205, ''),
(12, 12, 'masuk', 10, 20260207, ''),
(13, 16, 'masuk', 20, 20260224, ''),
(14, 18, 'masuk', 1000, 20260412, ''),
(15, 18, 'keluar', 13000, 20260412, 'penjual'),
(16, 16, 'masuk', 7, 20260412, ''),
(17, 12, 'keluar', 10, 20260412, '');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int NOT NULL,
  `nama_user` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama_user`, `password`) VALUES
(1, 'admin', '$2y$10$THWYzrQanukcHYqz4SMhDeNq5o950IBkOKC2z0p4S.o5st2eQ03GK');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
