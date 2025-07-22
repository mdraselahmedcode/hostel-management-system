-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 18, 2025 at 05:25 AM
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
-- Database: `hostel_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `room_fees`
--

CREATE TABLE `room_fees` (
  `id` int(11) NOT NULL,
  `hostel_id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `billing_cycle` enum('monthly','quarterly','yearly') NOT NULL,
  `effective_from` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_fees`
--

INSERT INTO `room_fees` (`id`, `hostel_id`, `room_type_id`, `price`, `billing_cycle`, `effective_from`, `created_at`, `updated_at`) VALUES
(4, 1, 2, '16000.00', 'monthly', '2025-06-01', '2025-05-24 03:10:31', '2025-06-05 14:30:27'),
(5, 1, 5, '20000.00', 'monthly', '2025-06-01', '2025-05-24 03:10:31', '2025-07-16 15:38:36'),
(12, 3, 13, '3000.00', 'monthly', '2025-06-04', '2025-06-04 08:48:24', '2025-06-05 10:55:44'),
(16, 1, 4, '15000.00', 'monthly', '2025-06-01', '2025-06-05 14:35:34', '2025-06-05 14:35:34'),
(17, 1, 1, '3000.00', 'monthly', '2025-06-04', '2025-06-05 14:36:06', '2025-06-05 14:36:06'),
(19, 3, 14, '3000.00', 'monthly', '2025-06-04', '2025-06-05 16:47:58', '2025-06-05 16:47:58'),
(26, 3, 17, '2000.00', 'monthly', '2025-06-05', '2025-06-05 18:02:43', '2025-06-05 18:02:43'),
(28, 1, 20, '3000.00', 'monthly', '2025-07-16', '2025-07-16 15:37:12', '2025-07-16 15:37:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `room_fees`
--
ALTER TABLE `room_fees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_fee` (`hostel_id`,`room_type_id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `room_fees`
--
ALTER TABLE `room_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `room_fees`
--
ALTER TABLE `room_fees`
  ADD CONSTRAINT `fk_room_fees_hostel` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_fees_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
