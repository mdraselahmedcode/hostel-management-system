-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 30, 2025 at 08:13 PM
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
-- Table structure for table `student_payments`
--

CREATE TABLE `student_payments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `hostel_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `room_fee_id` int(11) NOT NULL,
  `year` int(4) NOT NULL,
  `month` int(2) NOT NULL,
  `amount_due` decimal(10,2) NOT NULL,
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `o_p_balance_added` decimal(10,2) DEFAULT 0.00,
  `payment_status` enum('paid','unpaid','partial','late') NOT NULL DEFAULT 'unpaid',
  `due_date` date NOT NULL,
  `late_fee` decimal(10,2) DEFAULT 0.00,
  `late_fee_applied_date` date DEFAULT NULL,
  `is_late` tinyint(1) NOT NULL DEFAULT 0,
  `is_late_fee_taken` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_payments`
--

INSERT INTO `student_payments` (`id`, `student_id`, `hostel_id`, `room_id`, `room_type_id`, `room_fee_id`, `year`, `month`, `amount_due`, `amount_paid`, `balance`, `o_p_balance_added`, `payment_status`, `due_date`, `late_fee`, `late_fee_applied_date`, `is_late`, `is_late_fee_taken`, `created_at`, `updated_at`, `updated_by`, `created_by`) VALUES
(410, 17, 1, 83, 4, 16, 2025, 1, '15050.00', '16000.00', '0.00', '0.00', 'paid', '2025-01-05', '50.00', '2025-01-05', 1, 1, '2025-07-30 14:15:13', '2025-07-30 14:16:20', 7, 7),
(411, 22, 1, 77, 2, 4, 2025, 1, '16050.00', '5030.00', '11020.00', '0.00', 'partial', '2025-01-05', '50.00', '2025-01-05', 1, 0, '2025-07-30 14:15:13', '2025-07-30 15:27:49', 7, 7),
(412, 28, 1, 83, 4, 16, 2025, 1, '15050.00', '0.00', '15050.00', '0.00', 'unpaid', '2025-01-05', '50.00', '2025-01-05', 1, 0, '2025-07-30 14:15:13', '2025-07-30 14:15:13', NULL, 7),
(413, 17, 1, 83, 4, 16, 2025, 2, '15050.00', '14200.00', '0.00', '950.00', 'paid', '2025-02-05', '50.00', '2025-02-05', 1, 1, '2025-07-30 14:16:20', '2025-07-30 17:55:46', 7, 7),
(414, 22, 1, 77, 2, 4, 2025, 2, '16050.00', '0.00', '16050.00', '0.00', 'unpaid', '2025-02-05', '50.00', '2025-02-05', 1, 0, '2025-07-30 14:16:20', '2025-07-30 14:16:20', NULL, 7),
(415, 28, 1, 83, 4, 16, 2025, 2, '15050.00', '0.00', '15050.00', '0.00', 'unpaid', '2025-02-05', '50.00', '2025-02-05', 1, 0, '2025-07-30 14:16:20', '2025-07-30 14:16:20', NULL, 7),
(416, 17, 1, 83, 4, 16, 2025, 3, '15050.00', '0.00', '14950.00', '100.00', 'partial', '2025-07-30', '50.00', '2025-07-05', 1, 0, '2025-07-30 17:55:46', '2025-07-30 17:55:46', NULL, 7),
(417, 22, 1, 77, 2, 4, 2025, 3, '16050.00', '0.00', '16050.00', '0.00', 'unpaid', '2025-07-30', '50.00', '2025-07-05', 1, 0, '2025-07-30 17:55:46', '2025-07-30 17:55:46', NULL, 7),
(418, 28, 1, 83, 4, 16, 2025, 3, '15050.00', '0.00', '15050.00', '0.00', 'unpaid', '2025-07-30', '50.00', '2025-07-05', 1, 0, '2025-07-30 17:55:46', '2025-07-30 17:55:46', NULL, 7);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `student_payments`
--
ALTER TABLE `student_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `hostel_id` (`hostel_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `room_type_id` (`room_type_id`),
  ADD KEY `room_fee_id` (`room_fee_id`),
  ADD KEY `created_by` (`created_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `student_payments`
--
ALTER TABLE `student_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=419;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `student_payments`
--
ALTER TABLE `student_payments`
  ADD CONSTRAINT `student_payments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_payments_ibfk_2` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_payments_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_payments_ibfk_4` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_payments_ibfk_5` FOREIGN KEY (`room_fee_id`) REFERENCES `room_fees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_payments_ibfk_6` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
