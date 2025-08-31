-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 31, 2025 at 03:02 AM
-- Server version: 10.11.11-MariaDB-0+deb12u1
-- PHP Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `qrprof`
--

-- --------------------------------------------------------

--
-- Table structure for table `runs`
--

CREATE TABLE `runs` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `distance` float NOT NULL,
  `duration` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` varchar(255) DEFAULT NULL,
  `gear` enum('без лопаток','в лопатках') DEFAULT 'без лопаток',
  `location` enum('бассейн','море') DEFAULT 'бассейн'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `runs`
--

INSERT INTO `runs` (`id`, `date`, `distance`, `duration`, `created_at`, `description`, `gear`, `location`) VALUES
(1, '2025-05-30', 3500, '00:55:05', '2025-05-29 23:10:46', NULL, 'без лопаток', 'бассейн'),
(2, '2025-05-17', 10000, '02:45:00', '2025-05-29 23:11:37', NULL, 'без лопаток', 'бассейн'),
(4, '2025-05-29', 3050, '00:47:25', '2025-05-29 23:28:26', NULL, 'без лопаток', 'бассейн'),
(5, '2025-05-31', 3500, '00:55:16', '2025-06-01 00:30:50', NULL, 'без лопаток', 'бассейн'),
(6, '2025-06-05', 4000, '01:01:45', '2025-06-04 22:48:34', NULL, 'в лопатках', 'бассейн'),
(8, '2025-06-06', 3200, '00:49:50', '2025-06-06 00:18:20', NULL, 'без лопаток', 'бассейн'),
(9, '2025-06-16', 3500, '00:54:55', '2025-06-16 10:52:52', '', 'без лопаток', 'бассейн'),
(12, '2025-06-28', 10500, '03:30:42', '2025-07-02 03:18:44', 'AmurBay 2025', 'без лопаток', 'море'),
(13, '2024-09-15', 4800, '01:22:00', '2025-07-02 03:19:55', NULL, 'без лопаток', 'бассейн'),
(14, '2025-08-22', 2000, '00:31:45', '2025-08-27 10:30:18', NULL, 'без лопаток', 'бассейн'),
(15, '2025-08-30', 2000, '00:31:05', '2025-08-30 06:41:31', NULL, 'в лопатках', 'бассейн');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `runs`
--
ALTER TABLE `runs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `runs`
--
ALTER TABLE `runs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
