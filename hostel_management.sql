-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2025 at 05:37 AM
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
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `division` varchar(50) NOT NULL,
  `district` varchar(50) NOT NULL,
  `sub_district` varchar(50) NOT NULL,
  `village` varchar(50) NOT NULL,
  `postalcode` varchar(20) NOT NULL,
  `street` varchar(255) DEFAULT NULL,
  `house_no` varchar(255) DEFAULT NULL,
  `detail` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `country_id`, `state`, `division`, `district`, `sub_district`, `village`, `postalcode`, `street`, `house_no`, `detail`, `created_at`, `updated_at`) VALUES
(2, 33, '', 'Dhaka', 'Dhaka', 'Savar', 'Road 12', '1209', '27/A Dhanmondi Street', 'House 15', 'Near Dhanmondi Lake', '2025-05-23 19:09:14', '2025-05-30 06:40:30'),
(3, 33, 'Chittagong', 'Chittagong Division', 'Chittagong', 'Pahartali', 'Lake View', '4212', 'Block B, Lane 3', 'House 22', 'Near Pahartali Lake', '2025-05-24 03:15:39', '2025-05-24 03:15:39'),
(31, 33, '', 'Dhaka', 'Gaziput', 'Kaliakair', 'Chondra', '1102', 'Pouroshova', '', '', '2025-05-30 08:28:03', '2025-05-30 08:28:03'),
(32, 33, '', 'Dhaka', 'Dhaka', 'Savar', 'Ashulia', '546', '32/E ashulia Street', 'H-323', '', '2025-05-30 08:30:42', '2025-05-30 08:30:42'),
(33, 33, '', 'Dhaka', 'Dhaka', 'Savar', 'Khagan', '5434', 'Pouroshova', 'H-56', 'here it should be additional details', '2025-05-30 11:51:37', '2025-05-30 11:51:37'),
(34, 33, '', 'Dhaka', 'Dhaka', 'Savar', 'Khagan', '5434', 'Pouroshova', 'H-56', 'here it should be additional details', '2025-05-30 11:51:50', '2025-05-30 11:51:50'),
(35, 33, '', 'Dhaka', 'Dhaka', 'Savar', 'Khagan', '5434', 'Pouroshova', 'H-56', 'here it should be additional detail', '2025-05-30 11:53:26', '2025-05-30 17:54:15');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `admin_type_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `firstname`, `lastname`, `email`, `password`, `admin_type_id`, `created_at`, `updated_at`) VALUES
(7, 'Mili', 'Akter', 'mili123@gmail.com', '$2y$10$cYIpCoOcC8k4SmI7mykmm.kaR0PG1YXvjnPWuGlkCQQaAumpP/wxS', 2, '2025-05-26 02:04:08', '2025-05-26 02:04:08'),
(8, 'Chumki', 'Rahman', 'chumki123@gmail.com', '$2y$10$cYIpCoOcC8k4SmI7mykmm.kaR0PG1YXvjnPWuGlkCQQaAumpP/wxS', 1, '2025-05-26 02:04:08', '2025-05-26 02:04:08'),
(9, 'Sheikh', 'Arham', 'arham123@gmail.com', '$2y$10$cYIpCoOcC8k4SmI7mykmm.kaR0PG1YXvjnPWuGlkCQQaAumpP/wxS', 4, '2025-05-29 12:48:52', '2025-05-29 12:48:52'),
(10, 'Aktara', 'Banu', 'banu123@gmail.com', '$2y$10$cYIpCoOcC8k4SmI7mykmm.kaR0PG1YXvjnPWuGlkCQQaAumpP/wxS', 1, '2025-05-29 12:48:52', '2025-05-29 12:48:52');

-- --------------------------------------------------------

--
-- Table structure for table `admin_types`
--

CREATE TABLE `admin_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_types`
--

INSERT INTO `admin_types` (`id`, `type_name`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', '2025-05-23 18:23:23', '2025-05-23 18:23:23'),
(2, 'Hostel Manager', '2025-05-23 18:23:23', '2025-05-23 18:23:23'),
(3, 'Warden', '2025-05-23 18:23:23', '2025-05-23 18:23:23'),
(4, 'Accountant', '2025-05-23 18:23:23', '2025-05-23 18:23:23'),
(5, 'Maintenance Supervisor', '2025-05-23 18:23:23', '2025-05-23 18:23:23');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(11) NOT NULL,
  `country_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `country_name`, `created_at`, `updated_at`) VALUES
(33, 'Bangladesh', '2025-05-23 18:07:35', '2025-05-23 18:07:35'),
(34, 'Pakistan', '2025-05-23 18:07:35', '2025-05-23 18:07:35'),
(35, 'India', '2025-05-23 18:07:35', '2025-05-23 18:07:35'),
(36, 'Nepal', '2025-05-23 18:07:35', '2025-05-23 18:07:35'),
(37, 'Srilanka', '2025-05-23 18:07:35', '2025-05-23 18:07:35'),
(38, 'Vhutan', '2025-05-23 18:07:35', '2025-05-23 18:07:35'),
(39, 'Afghanistan', '2025-05-23 18:07:35', '2025-05-23 18:07:35'),
(40, 'China', '2025-05-23 18:07:35', '2025-05-23 18:07:35'),
(41, 'Miyanmar', '2025-05-23 18:07:35', '2025-05-23 18:07:35'),
(42, 'United States', '2025-05-23 18:07:35', '2025-05-23 18:07:35'),
(43, 'Canada', '2025-05-23 18:07:35', '2025-05-23 18:07:35'),
(44, 'Germany', '2025-05-23 18:07:35', '2025-05-23 18:07:35'),
(45, 'Australia', '2025-05-23 18:07:35', '2025-05-23 18:07:35');

-- --------------------------------------------------------

--
-- Table structure for table `floors`
--

CREATE TABLE `floors` (
  `id` int(11) NOT NULL,
  `hostel_id` int(11) DEFAULT NULL,
  `floor_number` int(11) NOT NULL,
  `floor_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `floors`
--

INSERT INTO `floors` (`id`, `hostel_id`, `floor_number`, `floor_name`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Ground Floor', '2025-05-23 19:14:57', '2025-05-30 16:04:24'),
(2, 1, 2, '1st', '2025-05-30 17:03:48', '2025-05-30 18:07:37'),
(3, 1, 3, '3rd', '2025-05-30 18:11:40', '2025-05-30 18:11:40'),
(4, 3, 1, 'Ground Floor', '2025-06-02 07:02:10', '2025-06-02 07:02:10');

-- --------------------------------------------------------

--
-- Table structure for table `hostels`
--

CREATE TABLE `hostels` (
  `id` int(11) NOT NULL,
  `hostel_incharge_id` int(11) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `hostel_name` varchar(100) NOT NULL,
  `hostel_type` enum('male','female') NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `capacity` int(11) DEFAULT 0,
  `amenities` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hostels`
--

INSERT INTO `hostels` (`id`, `hostel_incharge_id`, `address_id`, `hostel_name`, `hostel_type`, `contact_number`, `capacity`, `amenities`, `created_at`, `updated_at`) VALUES
(1, 8, 2, 'Dhanmondi Hostel', 'female', '+8801712345678', 200, 'Wi-Fi, Laundry, Security, Mess', '2025-05-23 19:13:00', '2025-05-30 05:49:41'),
(3, 8, 31, 'shantir Nir', 'female', '01929951023', 80, '', '2025-05-30 08:28:03', '2025-05-30 08:28:03'),
(4, 8, 32, 'Najrul', 'male', '01929951023', 65, 'healthy living space, oppurtunities for play in the ground, free wifi etc', '2025-05-30 08:30:42', '2025-05-30 11:47:12'),
(5, 8, 35, 'Wajuddin Hall', 'male', '01299301256', 202, '', '2025-05-30 11:53:26', '2025-05-30 12:08:31');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_type_id` int(11) DEFAULT NULL,
  `floor_id` int(11) DEFAULT NULL,
  `room_number` varchar(20) NOT NULL,
  `max_capacity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `hostel_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_type_id`, `floor_id`, `room_number`, `max_capacity`, `created_at`, `updated_at`, `hostel_id`) VALUES
(76, 6, 1, 'G107', 2, '2025-05-24 02:59:32', '2025-06-02 14:34:29', 1),
(77, 2, 1, 'G108', 2, '2025-05-24 02:59:32', '2025-06-02 14:34:29', 1),
(78, 4, 1, 'G109', 4, '2025-05-24 02:59:32', '2025-06-02 14:34:29', 1),
(81, 4, 3, 'G111', 5, '2025-05-30 19:06:39', '2025-06-02 14:34:29', 1),
(82, 4, 2, 'g112', 5, '2025-05-31 11:25:05', '2025-06-02 14:34:29', 1),
(83, 4, 3, 'G222', 3, '2025-05-31 13:07:38', '2025-06-02 14:34:29', 1),
(102, 2, 4, 'SN0002', 2, '2025-06-02 16:46:48', '2025-06-02 16:46:48', 3);

-- --------------------------------------------------------

--
-- Table structure for table `room_fees`
--

CREATE TABLE `room_fees` (
  `id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `floor_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `billing_cycle` enum('monthly','quarterly','yearly') NOT NULL,
  `effective_from` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_fees`
--

INSERT INTO `room_fees` (`id`, `room_type_id`, `floor_id`, `price`, `billing_cycle`, `effective_from`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '5000.00', 'monthly', '2025-06-01', '2025-05-24 03:10:31', '2025-05-24 03:10:31'),
(2, 2, 1, '8500.00', 'monthly', '2025-06-01', '2025-05-24 03:10:31', '2025-05-24 03:10:31'),
(3, 3, 1, '12000.00', 'monthly', '2025-06-01', '2025-05-24 03:10:31', '2025-05-24 03:10:31'),
(4, 4, 1, '16000.00', 'monthly', '2025-06-01', '2025-05-24 03:10:31', '2025-05-24 03:10:31'),
(5, 5, 1, '20000.00', 'monthly', '2025-06-01', '2025-05-24 03:10:31', '2025-05-24 03:10:31'),
(6, 6, 1, '15000.00', 'monthly', '2025-06-01', '2025-05-24 03:10:31', '2025-05-24 03:10:31');

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `default_capacity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `buffer_limit` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `type_name`, `description`, `default_capacity`, `created_at`, `updated_at`, `buffer_limit`) VALUES
(1, 'Single', 'A room with a single bed for one occupant.', 1, '2025-05-23 18:53:41', '2025-05-23 18:53:41', 0),
(2, 'Double', 'A room with two beds for two occupants.', 2, '2025-05-23 18:53:41', '2025-05-23 18:53:41', 0),
(3, 'Triple', 'A room with three beds for three occupants.', 3, '2025-05-23 18:53:41', '2025-05-23 18:53:41', 0),
(4, 'Quad', 'A room with four beds for four occupants.', 4, '2025-05-23 18:53:41', '2025-06-02 11:23:55', 2),
(5, 'Dormitory', 'A large room shared by multiple occupants, usually more than four.', 6, '2025-05-23 18:53:41', '2025-05-23 18:53:41', 0),
(6, 'Suite', 'A premium room with attached bathroom and possibly a small lounge.', 2, '2025-05-23 18:53:41', '2025-05-23 18:53:41', 0);

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
  `gender` enum('male','female') NOT NULL,
  `contact_number` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `is_approved` tinyint(1) DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `varsity_id` varchar(255) NOT NULL,
  `detail` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `profile_image_url`, `hostel_id`, `room_id`, `permanent_address_id`, `temporary_address_id`, `first_name`, `last_name`, `email`, `password`, `gender`, `contact_number`, `is_verified`, `is_approved`, `verification_token`, `varsity_id`, `detail`, `created_at`, `updated_at`) VALUES
(7, 'https://example.com/images/student1.jpg', 1, NULL, 2, 3, 'Alice', 'Johnson', 'alice.johnson@example.com', 'hashed_password_123', 'female', '1234567890', 1, 1, 'verify123token', 'VARSITY001', 'Computer Science major', '2025-05-24 03:25:21', '2025-05-24 03:25:21'),
(8, 'https://example.com/images/student2.jpg', 1, NULL, 2, 3, 'Jerin', 'Smith', 'jerin.smith@example.com', 'hashed_password_456', 'female', '0987654321', 0, 0, NULL, 'VARSITY002', 'Electrical Engineering major', '2025-05-24 03:25:21', '2025-05-24 03:25:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `country_id` (`country_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `admin_type_id` (`admin_type_id`);

--
-- Indexes for table `admin_types`
--
ALTER TABLE `admin_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `country_name` (`country_name`);

--
-- Indexes for table `floors`
--
ALTER TABLE `floors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hostel_id` (`hostel_id`);

--
-- Indexes for table `hostels`
--
ALTER TABLE `hostels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hostel_incharge_id` (`hostel_incharge_id`),
  ADD KEY `address_id` (`address_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_room_number_per_hostel` (`hostel_id`,`room_number`),
  ADD KEY `room_type_id` (`room_type_id`),
  ADD KEY `fk_floor_id` (`floor_id`);

--
-- Indexes for table `room_fees`
--
ALTER TABLE `room_fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_type_id` (`room_type_id`),
  ADD KEY `floor_id` (`floor_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `varsity_id` (`varsity_id`),
  ADD KEY `hostel_id` (`hostel_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `permanent_address_id` (`permanent_address_id`),
  ADD KEY `temporary_address_id` (`temporary_address_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `admin_types`
--
ALTER TABLE `admin_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `floors`
--
ALTER TABLE `floors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `hostels`
--
ALTER TABLE `hostels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `room_fees`
--
ALTER TABLE `room_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`admin_type_id`) REFERENCES `admin_types` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `floors`
--
ALTER TABLE `floors`
  ADD CONSTRAINT `floors_ibfk_1` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hostels`
--
ALTER TABLE `hostels`
  ADD CONSTRAINT `hostels_ibfk_1` FOREIGN KEY (`hostel_incharge_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hostels_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `fk_floor_id` FOREIGN KEY (`floor_id`) REFERENCES `floors` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_rooms_hostel` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `room_fees`
--
ALTER TABLE `room_fees`
  ADD CONSTRAINT `room_fees_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_fees_ibfk_2` FOREIGN KEY (`floor_id`) REFERENCES `floors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_ibfk_3` FOREIGN KEY (`permanent_address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_ibfk_4` FOREIGN KEY (`temporary_address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
