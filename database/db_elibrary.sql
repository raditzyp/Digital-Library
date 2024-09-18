-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 10, 2024 at 06:49 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_elibrary`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `email`, `password`) VALUES
(1, 'admin1', 'admin1@gmail.com', '$2y$10$07Y8QtgXRdFpnrjVx9OXzu7Xm9UWo/IPr33gBfDbwLQZC3VIgpz6.'),
(2, 'admin2', 'admin2@gmail.com', '$2y$10$fPeBeUaR8lkZQiFpwvbg.OO6.2FkwrUV/73P/ulV0faybFUioYfuC'),
(3, 'abcdef', 'abcdef@gmail.com', '$2y$10$3gVVGuKTQfDVlr4kgUllB.D3KYJyLWCuoi3ecfHHaDwWhGCxVHPCi'),
(4, 'gojo', 'gojo@gmail.com', '$2y$10$yhBayhJJA2iqjzYxAVWBqeSWRfGqfk6CHyXsyVYxAh3iqemGe72OC');

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id_buku` int(11) NOT NULL,
  `judul_buku` varchar(100) NOT NULL,
  `pengarang` varchar(100) NOT NULL,
  `penerbit` varchar(100) NOT NULL,
  `tahun_terbit` varchar(5) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `id_kategori` int(11) NOT NULL,
  `file_pdf` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id_buku`, `judul_buku`, `pengarang`, `penerbit`, `tahun_terbit`, `status`, `id_kategori`, `file_pdf`) VALUES
(26, 'Dilan 1990', 'Pidi Baiq', 'Pastel Books', '2014', 0, 1, 'Dilan 1990.pdf'),
(27, 'Dunia Sophie', 'Jostein Gaarder', 'Mirzan Pustaka', '1996', 0, 2, 'dunia sophie.pdf'),
(29, 'Laut Bercerita', 'Leila S. Chudori', 'Gramedia', '2017', 0, 6, 'Laut_bercerita.pdf'),
(30, 'Milea', 'Pidi Baiq', 'Pastel Books', '2018', 0, 1, 'Milea_ suara dari dilan.pdf'),
(32, 'Fantasteen: Lucid dream', 'Ziggy Z', 'Mizan ', '2013', 1, 3, 'Fantasteen Lucid Dream (Ziggy Zezsyazeoviennazabrizkie).pdf'),
(33, 'Perahu Kertas', 'Dee Lestari', 'Truedee Pustaka', '2009', 1, 1, 'Perahu_Kertas.pdf'),
(34, 'Winter in Tokyo', 'Ilana Tan', 'Gramedia', '2008', 0, 1, 'Winter in Tokyo.pdf'),
(36, 'Retorika Seni Bicara', 'Aristoteles', 'Basa Basi', '2018', 0, 2, 'Retorika Seni Berbicara (Aristoteles).pdf'),
(37, 'Supernova', 'Dee Lestari', 'Bentang Pustaka', '2012', 0, 3, '93543ad8a098a36dd2ce18419cb1f02c.pdf'),
(38, 'Kala Langit Abu-Abu', 'Pradnya Paramitha', 'Gramedia', '2020', 0, 1, 'Kala Langit Abu-Abu (Pradnya Paramitha).pdf'),
(39, 'Garis Waktu', 'Fiersa Besari', 'Mediakita', '2016', 0, 1, 'Garis Waktu (Fiersa Besari).pdf'),
(40, 'Psychology of Selling', 'Brian Tracy', 'Bhuana  ', '2021', 0, 7, 'Psychology of Selling (Brian Tracy).pdf'),
(41, 'Imperfect: A Journey', 'Meira Anastasia', 'Gramedia', '2018', 0, 7, 'Imperfect A Journey to Self-Acceptance (Meira Anastasia).pdf'),
(42, ' The Psychology of Money', 'Morgan Housel', 'Baca', '2021', 0, 7, 'The Psychology of Money (Morgan Housel).pdf'),
(43, 'Senjangkala', 'Risa Saraswati', 'Gramedia', '2022', 0, 8, 'Senjakala - Risa Saraswati.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `favorite_buku`
--

CREATE TABLE `favorite_buku` (
  `id_favorite` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_buku` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Romance'),
(2, 'Philosophy'),
(3, 'Fantasy'),
(6, 'Historical'),
(7, 'Non-fiction'),
(8, 'Horor');

-- --------------------------------------------------------

--
-- Table structure for table `pinjam`
--

CREATE TABLE `pinjam` (
  `id_pinjam` int(11) NOT NULL,
  `id_buku` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `tanggal_peminjaman` date NOT NULL,
  `tanggal_pengembalian` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `pinjam`
--

INSERT INTO `pinjam` (`id_pinjam`, `id_buku`, `id_user`, `tanggal_peminjaman`, `tanggal_pengembalian`) VALUES
(55, 32, 14, '2024-07-02', '2024-08-01'),
(56, 33, 14, '2024-07-02', '2024-08-01');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_pinjam` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_buku` int(11) DEFAULT NULL,
  `tanggal_peminjaman` date DEFAULT NULL,
  `tanggal_pengembalian` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_pinjam`, `id_user`, `id_buku`, `tanggal_peminjaman`, `tanggal_pengembalian`) VALUES
(41, 16, 29, '2024-06-29', '2024-07-13'),
(53, 14, 26, '2024-07-02', '2024-08-01'),
(54, 14, 43, '2024-07-02', '2024-07-16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `email`, `password`, `name`, `is_admin`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$07Y8QtgXRdFpnrjVx9OXzu7Xm9UWo/IPr33gBfDbwLQZC3VIgpz6.', 'rahmayanti', 0),
(10, 'azmirf', 'azmirizkifar.edu@gmail.com', '$2y$10$fKRx1GCbEdc4gX2emvcLUeqzE2qGWyuXVKW6yFOB0SOmPzOfLC3A2', 'azmi rizkifar', 0),
(12, 'ridhfr20', 'ridhofar20@gmail.com', '$2y$10$0fCV3g7BqMb.NqDlncPJPuKBCOuL8lwfOosCYkGexXtfpxmtoinzq', 'ridho firdaus', 0),
(14, 'renjun', 'raditya@gmail.com', '$2y$10$iyNCqKQP5LatmWSQJ75awuzhpueN3NUmqiQ7g9fQJjRovsKa01lXy', 'raditya', 0),
(15, 'iknanis', 'iknaniswa@gmail.com', '$2y$10$Yzv04mHiF9kg4LCpPzNSqefDmiXqAb0n4yKf/lmrRmhMLDIhjGu6C', 'ikna', 0),
(16, 'nanana', 'nananis@gmail.com', '$2y$10$NP8NqjV3HCYVQbNHWHGlD.We03g.6yStHnUUAvsiple9SItIIVNJa', 'nana', 0),
(17, 'hyunjin', 'hyunjin12@gmail.com', '$2y$10$cBfvdWytf7RZ0DfJZcY4KeUHsy/2bpPVFdj3DE5U.RXPvx7KFg.dC', 'Hyunjin12', 0),
(18, 'Arifin', 'Arifin@gmail.com', '$2y$10$DWQG06I5XgSOefkU1ruBJuBC2MEhbXb6Uxu.bUwLwH65u3nXJMOxK', 'Apin12', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id_buku`),
  ADD KEY `fk_id_kategori` (`id_kategori`);

--
-- Indexes for table `favorite_buku`
--
ALTER TABLE `favorite_buku`
  ADD PRIMARY KEY (`id_favorite`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_buku` (`id_buku`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `pinjam`
--
ALTER TABLE `pinjam`
  ADD PRIMARY KEY (`id_pinjam`),
  ADD KEY `fk_id_buku` (`id_buku`),
  ADD KEY `fk_id_user` (`id_user`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_pinjam`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_buku` (`id_buku`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `id_buku` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `favorite_buku`
--
ALTER TABLE `favorite_buku`
  MODIFY `id_favorite` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pinjam`
--
ALTER TABLE `pinjam`
  MODIFY `id_pinjam` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_pinjam` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `buku`
--
ALTER TABLE `buku`
  ADD CONSTRAINT `fk_id_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `favorite_buku`
--
ALTER TABLE `favorite_buku`
  ADD CONSTRAINT `favorite_buku_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `favorite_buku_ibfk_2` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`);

--
-- Constraints for table `pinjam`
--
ALTER TABLE `pinjam`
  ADD CONSTRAINT `fk_id_buku` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_id_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
