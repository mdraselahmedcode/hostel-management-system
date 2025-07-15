-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 15, 2025 at 07:21 AM
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
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `profile_image_url` varchar(255) DEFAULT NULL,
  `hostel_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `permanent_address_id` int(11) DEFAULT NULL,
  `temporary_address_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `contact_number` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `is_approved` tinyint(1) DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `varsity_id` varchar(255) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `batch_year` varchar(20) DEFAULT NULL,
  `detail` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_checked_in` tinyint(1) DEFAULT 0,
  `check_in_at` datetime DEFAULT NULL,
  `check_out_at` datetime DEFAULT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `mother_name` varchar(100) DEFAULT NULL,
  `father_contact` varchar(20) DEFAULT NULL,
  `mother_contact` varchar(20) DEFAULT NULL,
  `emergency_contact` varchar(20) DEFAULT NULL,
  `blood_group` enum('A+','A-','B+','B-','AB+','AB-','O+','O-','Unknown') DEFAULT 'Unknown',
  `floor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `profile_image_url`, `hostel_id`, `room_id`, `permanent_address_id`, `temporary_address_id`, `first_name`, `last_name`, `email`, `password`, `gender`, `contact_number`, `is_verified`, `is_approved`, `verification_token`, `varsity_id`, `department`, `batch_year`, `detail`, `created_at`, `updated_at`, `is_checked_in`, `check_in_at`, `check_out_at`, `father_name`, `mother_name`, `father_contact`, `mother_contact`, `emergency_contact`, `blood_group`, `floor_id`) VALUES
(14, NULL, 1, 77, 58, 59, 'Sheikh', 'Russell', 'rasel123@gmail.com', '$2y$10$8Ei7jK4S9iKl6jsU.2R.nOWJ8jJDi74GRS2hmDMvQmY0ntXs2rmTy', 'male', '01929951023', NULL, 1, NULL, 'VARSITY004', 'CSE', '2nd', NULL, '2025-06-12 06:14:42', '2025-07-15 04:42:45', 0, NULL, NULL, 'borkot uddin', 'Jahara Bagum', '00821903124', '91723123143', '01283012232', 'A+', 1),
(15, NULL, 1, 78, 60, 61, 'bilkis', 'akter', 'bilkis123@gmail.com', '$2y$10$0gXYDYFDX5xUU8I2NPZ90uQFNFUrVXfDHs0ZGGoiXC5bWKk99jEvm', 'female', '01929951026', 0, 0, NULL, 'VARSITY006', 'SE', '1st', NULL, '2025-06-12 07:22:22', '2025-06-23 07:28:45', 0, '2025-06-22 13:19:00', '2028-12-31 13:20:00', 'borkot uddin', 'Jahara Bagum', '00821903124', '91723123143', '01283012232', 'Unknown', 1),
(17, NULL, 1, 83, 64, 65, 'billua', 'mia', 'billu123@gmail.com', '$2y$10$xMtQYjQZvodQ.Usi5Get3OnEMkR2GqtAKFonziU.SigOQQagYMkei', 'male', '01929951028', 1, 1, NULL, 'VARSITY001', 'LAW', '3rd', NULL, '2025-06-22 19:27:09', '2025-06-23 11:54:28', 1, '2025-06-23 01:26:00', NULL, 'borkot uddin', 'Jahara Bagum', '00821903124', '91723123143', '01283012232', 'O+', 3),
(22, NULL, 1, 77, 66, 67, 'Mehtab', 'Islam', 'mehtab123@gmail.com', '$2y$10$HHqgb.cl.EWlchje/NuH..bRpMfViaY3LAFqwXaY55SwUBkFZHbfC', 'male', '01929951543', 0, 1, NULL, '01283012312', NULL, NULL, '', '2025-07-15 03:20:00', '2025-07-15 05:14:36', 0, NULL, NULL, 'borkot uddin', 'Jahara Bagum', '00821903124', '91723123143', '012830122323', 'Unknown', 1),
(23, NULL, NULL, NULL, 68, 69, 'shaila', 'Islam', 'shaila123@gmail.com', '$2y$10$SLuVs2xjcg51VLF/x8HpSOTHllfWvBus1Dfdsm1dbFYL5YpKVjKzW', 'female', '01929951231', 0, 0, NULL, '1283701234', 'CSE', '3rd', '', '2025-07-15 04:05:10', '2025-07-15 04:05:10', 0, NULL, NULL, 'borkot uddin', 'Jahara Bagum', '00821903124', '91723123143', '01283012232', 'A+', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `varsity_id` (`varsity_id`),
  ADD UNIQUE KEY `contact_number` (`contact_number`),
  ADD UNIQUE KEY `contact_number_2` (`contact_number`),
  ADD KEY `hostel_id` (`hostel_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `fk_students_floors` (`floor_id`),
  ADD KEY `fk_students_permanent_address` (`permanent_address_id`),
  ADD KEY `fk_students_temporary_address` (`temporary_address_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_students_floors` FOREIGN KEY (`floor_id`) REFERENCES `floors` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_students_permanent_address` FOREIGN KEY (`permanent_address_id`) REFERENCES `addresses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_students_temporary_address` FOREIGN KEY (`temporary_address_id`) REFERENCES `addresses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
