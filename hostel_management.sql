-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2025 at 02:25 PM
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
(31, 33, '', 'Dhaka', 'Gaziput', 'Kaliakair', 'Chondra', '1102', 'Pouroshova', '', '', '2025-05-30 08:28:03', '2025-05-30 08:28:03'),
(32, 33, '', 'Dhaka', 'Dhaka', 'Savar', 'Ashulia', '546', '32/E ashulia Street', 'H-323', '', '2025-05-30 08:30:42', '2025-05-30 08:30:42'),
(33, 33, '', 'Dhaka', 'Dhaka', 'Savar', 'Khagan', '5434', 'Pouroshova', 'H-56', 'here it should be additional details', '2025-05-30 11:51:37', '2025-05-30 11:51:37'),
(34, 33, '', 'Dhaka', 'Dhaka', 'Savar', 'Khagan', '5434', 'Pouroshova', 'H-56', 'here it should be additional details', '2025-05-30 11:51:50', '2025-05-30 11:51:50'),
(35, 33, '', 'Dhaka', 'Dhaka', 'Savar', 'Khagan', '5434', 'Pouroshova', 'H-56', 'here it should be additional detail', '2025-05-30 11:53:26', '2025-05-30 17:54:15'),
(36, 33, NULL, '', '', '', '', '', '', '', '', '2025-06-08 17:05:47', '2025-06-08 17:05:47'),
(37, 43, NULL, '', '', '', '', '', '', '', '', '2025-06-08 17:05:47', '2025-06-08 17:05:47'),
(54, 33, '', 'Dhaka', 'dhaka', 'savar', 'khagan', 'I JUST FORGOT IT', 'khagan street', 'h-34', 'additional detail will be here', '2025-06-11 04:49:15', '2025-06-11 04:49:15'),
(55, 33, '', 'Dhaka', 'dhaka', 'savar', 'khagan', 'I JUST FORGOT IT', 'khagan street', 'h-34', 'additional detail will be here', '2025-06-11 04:49:15', '2025-06-11 04:49:15'),
(58, 33, '', 'Dhaka', 'dhaka', 'savar', 'khagan', 'I JUST FORGOT IT', 'khagan street', 'h-34', 'here would be some additional details', '2025-06-12 06:14:42', '2025-06-12 06:14:42'),
(59, 33, '', 'Dhaka', 'dhaka', 'savar', 'khagan', 'I JUST FORGOT IT', 'khagan street', 'h-34', 'here would be some additional details', '2025-06-12 06:14:42', '2025-06-12 06:14:42'),
(60, 33, '', 'Dhaka', 'dhaka', 'savar', 'khagan', 'I JUST FORGOT IT', 'khagan street', 'h-34', '', '2025-06-12 07:22:22', '2025-06-12 07:22:22'),
(61, 33, '', 'Dhaka', 'dhaka', 'savar', 'khagan', 'I JUST FORGOT IT', 'khagan street', 'h-34', '', '2025-06-12 07:22:22', '2025-06-12 07:22:22');

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
(10, 'Aktara', 'Banu', 'banu123@gmail.com', '$2y$10$cYIpCoOcC8k4SmI7mykmm.kaR0PG1YXvjnPWuGlkCQQaAumpP/wxS', 1, '2025-05-29 12:48:52', '2025-05-29 12:48:52'),
(12, 'jabba', 'islam', 'jabbar123@gmail.com', '$2y$10$1IK32epo4ij8HBT5oBEax.aDjL/sPD52JewiSmIz8E39TMS.p3BKC', 2, '2025-06-06 18:40:42', '2025-06-06 18:40:42'),
(14, 'akramul', 'Islam', 'akramul123@gmail.com', '$2y$10$8.S8FKp9UdfeUl5ANSMtJeQfRPSdQdICSxejbZ3BRnbtsKXs1mjKC', 2, '2025-06-06 18:44:27', '2025-06-06 18:44:27'),
(15, 'sayra', 'islam', 'sayra123@gmail.com', '$2y$10$xybDSu49CircxnmYy8d12uQQAVWNfI7gi6YG7HmRT2rnxx0Aq5rkm', 6, '2025-06-06 18:46:40', '2025-06-06 18:55:26');

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
(5, 'Maintenance Supervisor', '2025-05-23 18:23:23', '2025-05-23 18:23:23'),
(6, 'Editor', '2025-06-06 11:52:07', '2025-06-06 11:52:07');

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
(1, 8, NULL, 'Dhanmondi Hostel', 'female', '+8801712345678', 200, 'Wi-Fi, Laundry, Security, Mess', '2025-05-23 19:13:00', '2025-05-30 05:49:41'),
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
(102, 2, 4, 'SN0002', 2, '2025-06-02 16:46:48', '2025-06-02 16:46:48', 3),
(105, 4, 2, 'sn0001', 5, '2025-06-03 09:52:31', '2025-06-03 10:28:54', 1),
(106, 4, 1, 'sn0002', 4, '2025-06-03 10:30:22', '2025-06-03 17:00:50', 1);

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
(5, 1, 5, '20000.00', 'monthly', '2025-06-01', '2025-05-24 03:10:31', '2025-06-04 03:45:23'),
(6, 1, 6, '15000.00', 'monthly', '2025-06-01', '2025-05-24 03:10:31', '2025-06-04 03:45:23'),
(12, 3, 13, '3000.00', 'monthly', '2025-06-04', '2025-06-04 08:48:24', '2025-06-05 10:55:44'),
(16, 1, 4, '15000.00', 'monthly', '2025-06-01', '2025-06-05 14:35:34', '2025-06-05 14:35:34'),
(17, 1, 1, '3000.00', 'monthly', '2025-06-04', '2025-06-05 14:36:06', '2025-06-05 14:36:06'),
(19, 3, 14, '3000.00', 'monthly', '2025-06-04', '2025-06-05 16:47:58', '2025-06-05 16:47:58'),
(20, 5, 18, '3000.00', 'monthly', '2025-06-05', '2025-06-05 16:49:01', '2025-06-05 18:04:36'),
(21, 5, 15, '2000.00', 'monthly', '2025-06-05', '2025-06-05 16:50:12', '2025-06-05 16:50:12'),
(26, 3, 17, '2000.00', 'monthly', '2025-06-05', '2025-06-05 18:02:43', '2025-06-05 18:02:43');

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
  `buffer_limit` int(11) NOT NULL DEFAULT 0,
  `hostel_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `type_name`, `description`, `default_capacity`, `created_at`, `updated_at`, `buffer_limit`, `hostel_id`) VALUES
(1, 'single', 'A room with a single bed for one occupant.', 1, '2025-05-23 18:53:41', '2025-06-04 07:19:44', 0, 1),
(2, 'Double', 'A room with two beds for two occupants.', 2, '2025-05-23 18:53:41', '2025-06-03 08:46:26', 0, 1),
(4, 'Quad', 'A room with four beds for four occupants.', 4, '2025-05-23 18:53:41', '2025-06-03 08:46:26', 2, 1),
(5, 'Dormitory', 'A large room shared by multiple occupants, usually more than four.', 6, '2025-05-23 18:53:41', '2025-06-03 08:46:26', 0, 1),
(6, 'Suite', 'A premium room with attached bathroom and possibly a small lounge.', 2, '2025-05-23 18:53:41', '2025-06-03 08:46:26', 0, 1),
(13, 'single', '', 1, '2025-06-04 07:11:49', '2025-06-04 07:11:49', 1, 3),
(14, 'double', '', 2, '2025-06-04 07:47:27', '2025-06-04 07:47:27', 1, 3),
(15, 'single', '', 1, '2025-06-04 09:37:31', '2025-06-04 09:37:31', 1, 5),
(16, 'triple', '', 3, '2025-06-05 13:57:37', '2025-06-05 13:57:37', 0, 1),
(17, 'triple', '', 3, '2025-06-05 18:01:23', '2025-06-05 18:01:23', 0, 3),
(18, 'triple', '', 3, '2025-06-05 18:03:48', '2025-06-05 18:03:48', 0, 5);

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
  `floor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `profile_image_url`, `hostel_id`, `room_id`, `permanent_address_id`, `temporary_address_id`, `first_name`, `last_name`, `email`, `password`, `gender`, `contact_number`, `is_verified`, `is_approved`, `verification_token`, `varsity_id`, `detail`, `created_at`, `updated_at`, `is_checked_in`, `check_in_at`, `check_out_at`, `father_name`, `mother_name`, `father_contact`, `mother_contact`, `emergency_contact`, `floor_id`) VALUES
(14, NULL, 1, 77, 58, 59, 'Sheikh', 'Russell', 'rasel123@gmail.com', '$2y$10$8Ei7jK4S9iKl6jsU.2R.nOWJ8jJDi74GRS2hmDMvQmY0ntXs2rmTy', 'male', '01929951023', NULL, 1, NULL, 'VARSITY004', NULL, '2025-06-12 06:14:42', '2025-06-12 06:14:42', 1, '2025-06-12 12:14:00', '2028-12-31 12:14:00', 'borkot uddin', 'Jahara Bagum', '00821903124', '91723123143', '01283012232', 1),
(15, NULL, 1, 78, 60, 61, 'bilkis', 'akter', 'bilkis123@gmail.com', '$2y$10$0gXYDYFDX5xUU8I2NPZ90uQFNFUrVXfDHs0ZGGoiXC5bWKk99jEvm', 'female', '01929951026', 0, 0, NULL, 'VARSITY006', NULL, '2025-06-12 07:22:22', '2025-06-22 07:10:56', 0, '2025-06-22 13:19:00', '2028-12-31 13:20:00', 'borkot uddin', 'Jahara Bagum', '00821903124', '91723123143', '01283012232', 1);

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
  ADD UNIQUE KEY `unique_fee` (`hostel_id`,`room_type_id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_room_types_hostel_id` (`hostel_id`);

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
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `admin_types`
--
ALTER TABLE `admin_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `room_fees`
--
ALTER TABLE `room_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
  ADD CONSTRAINT `fk_room_fees_hostel` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_fees_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_types`
--
ALTER TABLE `room_types`
  ADD CONSTRAINT `fk_room_types_hostel_id` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`id`) ON DELETE CASCADE;

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
