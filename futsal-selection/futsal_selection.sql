-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 16, 2025 at 03:42 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `futsal_selection`
--

-- --------------------------------------------------------

--
-- Table structure for table `criteria`
--

CREATE TABLE `criteria` (
  `id` int NOT NULL,
  `kode_kriteria` varchar(10) NOT NULL,
  `nama_kriteria` varchar(50) NOT NULL,
  `tipe` enum('Taktikal','Individu') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `criteria`
--

INSERT INTO `criteria` (`id`, `kode_kriteria`, `nama_kriteria`, `tipe`) VALUES
(1, 'KT', 'Keterampilan Taktikal', 'Taktikal'),
(2, 'KI', 'Keterampilan Individu', 'Individu');

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `id` int NOT NULL,
  `kode_pemain` varchar(10) NOT NULL,
  `nama_pemain` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`id`, `kode_pemain`, `nama_pemain`) VALUES
(1, 'A1', 'Rizky Maulana'),
(2, 'A2', 'Fajar Nugroho'),
(3, 'A3', 'Arif Hidayat'),
(4, 'A4', 'Surya Pratama'),
(5, 'A5', 'Gilang Ramadhan'),
(6, 'A6', 'Hanif Pria');

-- --------------------------------------------------------

--
-- Table structure for table `scores`
--

CREATE TABLE `scores` (
  `id` int NOT NULL,
  `player_id` int NOT NULL,
  `subkriteria_id` int NOT NULL,
  `nilai` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `scores`
--

INSERT INTO `scores` (`id`, `player_id`, `subkriteria_id`, `nilai`) VALUES
(1, 1, 1, 4),
(2, 1, 2, 5),
(3, 1, 3, 3),
(4, 1, 4, 4),
(5, 1, 5, 3),
(6, 1, 6, 5),
(7, 1, 7, 4),
(8, 1, 8, 5),
(9, 1, 9, 3),
(10, 1, 10, 4),
(11, 2, 1, 3),
(12, 2, 2, 4),
(13, 2, 3, 4),
(14, 2, 4, 3),
(15, 2, 5, 4),
(16, 2, 6, 4),
(17, 2, 7, 3),
(18, 2, 8, 4),
(19, 2, 9, 4),
(20, 2, 10, 3),
(21, 3, 1, 5),
(22, 3, 2, 3),
(23, 3, 3, 5),
(24, 3, 4, 5),
(25, 3, 5, 2),
(26, 3, 6, 3),
(27, 3, 7, 5),
(28, 3, 8, 3),
(29, 3, 9, 5),
(30, 3, 10, 2),
(31, 4, 1, 2),
(32, 4, 2, 4),
(33, 4, 3, 2),
(34, 4, 4, 3),
(35, 4, 5, 4),
(36, 4, 6, 4),
(37, 4, 7, 2),
(38, 4, 8, 4),
(39, 4, 9, 2),
(40, 4, 10, 3),
(41, 5, 1, 4),
(42, 5, 2, 5),
(43, 5, 3, 3),
(44, 5, 4, 2),
(45, 5, 5, 5),
(46, 5, 6, 5),
(47, 5, 7, 4),
(48, 5, 8, 5),
(49, 5, 9, 4),
(50, 5, 10, 5),
(51, 1, 1, 2),
(52, 1, 2, 2),
(53, 1, 3, 2),
(54, 1, 4, 2),
(55, 1, 5, 2),
(56, 1, 6, 2),
(57, 1, 7, 2),
(58, 1, 8, 2),
(59, 1, 9, 2),
(60, 1, 10, 2),
(61, 1, 1, 5),
(62, 1, 2, 5),
(63, 1, 3, 5),
(64, 1, 4, 5),
(65, 1, 5, 5),
(66, 1, 6, 5),
(67, 1, 7, 5),
(68, 1, 8, 5),
(69, 1, 9, 5),
(70, 1, 10, 5),
(71, 1, 1, 5),
(72, 1, 2, 5),
(73, 1, 3, 5),
(74, 1, 4, 5),
(75, 1, 5, 5),
(76, 1, 6, 5),
(77, 1, 7, 5),
(78, 1, 8, 5),
(79, 1, 9, 5),
(80, 1, 10, 5),
(81, 1, 1, 5),
(82, 1, 2, 5),
(83, 1, 3, 5),
(84, 1, 4, 5),
(85, 1, 5, 5),
(86, 1, 6, 5),
(87, 1, 7, 5),
(88, 1, 8, 5),
(89, 1, 9, 5),
(90, 1, 10, 5),
(91, 1, 1, 5),
(92, 1, 2, 5),
(93, 1, 3, 5),
(94, 1, 4, 5),
(95, 1, 5, 5),
(96, 1, 6, 5),
(97, 1, 7, 5),
(98, 1, 8, 5),
(99, 1, 9, 5),
(100, 1, 10, 5),
(101, 1, 1, 5),
(102, 1, 2, 5),
(103, 1, 3, 5),
(104, 1, 4, 5),
(105, 1, 5, 5),
(106, 1, 6, 5),
(107, 1, 7, 5),
(108, 1, 8, 5),
(109, 1, 9, 5),
(110, 1, 10, 5),
(111, 1, 1, 5),
(112, 1, 2, 5),
(113, 1, 3, 5),
(114, 1, 4, 5),
(115, 1, 5, 5),
(116, 1, 6, 5),
(117, 1, 7, 5),
(118, 1, 8, 5),
(119, 1, 9, 5),
(120, 1, 10, 5),
(121, 1, 11, 5),
(122, 1, 1, 3),
(123, 1, 2, 4),
(124, 1, 3, 5),
(125, 1, 4, 3),
(126, 1, 5, 5),
(127, 1, 6, 5),
(128, 1, 7, 4),
(129, 1, 8, 4),
(130, 1, 9, 5),
(131, 1, 10, 5),
(132, 1, 11, 2),
(133, 6, 6, 5),
(134, 6, 7, 5),
(135, 6, 8, 5),
(136, 6, 9, 5),
(137, 6, 10, 5),
(138, 6, 11, 5),
(139, 6, 1, 5),
(140, 6, 2, 5),
(141, 6, 3, 5),
(142, 6, 4, 5),
(143, 6, 5, 5),
(144, 5, 6, 5),
(145, 5, 7, 5),
(146, 5, 8, 5),
(147, 5, 9, 5),
(148, 5, 10, 5),
(149, 5, 11, 5),
(150, 5, 1, 5),
(151, 5, 2, 5),
(152, 5, 3, 5),
(153, 5, 4, 5),
(154, 5, 5, 5),
(155, 6, 6, 2),
(156, 6, 7, 2),
(157, 6, 8, 2),
(158, 6, 9, 2),
(159, 6, 10, 2),
(160, 6, 11, 2),
(161, 6, 1, 2),
(162, 6, 2, 2),
(163, 6, 3, 2),
(164, 6, 4, 2),
(165, 6, 5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `sub_criteria`
--

CREATE TABLE `sub_criteria` (
  `id` int NOT NULL,
  `kode_subkriteria` varchar(10) NOT NULL,
  `nama_subkriteria` varchar(50) NOT NULL,
  `kriteria_id` int NOT NULL,
  `factor_type` enum('Core','Secondary') NOT NULL,
  `nilai_ideal` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sub_criteria`
--

INSERT INTO `sub_criteria` (`id`, `kode_subkriteria`, `nama_subkriteria`, `kriteria_id`, `factor_type`, `nilai_ideal`) VALUES
(1, 'T1', 'Taktikal Individu', 1, 'Secondary', 3),
(2, 'T2', 'Taktikal Beregu', 1, 'Secondary', 4),
(3, 'T3', 'Taktikal Tim', 1, 'Core', 5),
(4, 'T4', 'Taktikal Menyerang', 1, 'Core', 3),
(5, 'T5', 'Taktikal Bertahan', 1, 'Core', 5),
(6, 'I1', 'Passing', 2, 'Core', 5),
(7, 'I2', 'Shooting', 2, 'Core', 4),
(8, 'I3', 'Kecepatan', 2, 'Secondary', 4),
(9, 'I4', 'Control', 2, 'Core', 5),
(10, 'I5', 'Chipping', 2, 'Secondary', 5),
(11, 'I6', 'Stamina', 2, 'Secondary', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `criteria`
--
ALTER TABLE `criteria`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_pemain` (`kode_pemain`);

--
-- Indexes for table `scores`
--
ALTER TABLE `scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `player_id` (`player_id`),
  ADD KEY `subkriteria_id` (`subkriteria_id`);

--
-- Indexes for table `sub_criteria`
--
ALTER TABLE `sub_criteria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kriteria_id` (`kriteria_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `criteria`
--
ALTER TABLE `criteria`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `scores`
--
ALTER TABLE `scores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT for table `sub_criteria`
--
ALTER TABLE `sub_criteria`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `scores`
--
ALTER TABLE `scores`
  ADD CONSTRAINT `scores_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`),
  ADD CONSTRAINT `scores_ibfk_2` FOREIGN KEY (`subkriteria_id`) REFERENCES `sub_criteria` (`id`);

--
-- Constraints for table `sub_criteria`
--
ALTER TABLE `sub_criteria`
  ADD CONSTRAINT `sub_criteria_ibfk_1` FOREIGN KEY (`kriteria_id`) REFERENCES `criteria` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
