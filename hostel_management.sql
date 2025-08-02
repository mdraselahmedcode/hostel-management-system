-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 02, 2025 at 11:20 AM
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
(33, 33, '', 'Dhaka', 'Dhaka', 'Savar', 'Khagan', '5434', 'Pouroshova', 'H-56', 'here it should be additional details', '2025-05-30 11:51:37', '2025-05-30 11:51:37'),
(34, 33, '', 'Dhaka', 'Dhaka', 'Savar', 'Khagan', '5434', 'Pouroshova', 'H-56', 'here it should be additional details', '2025-05-30 11:51:50', '2025-05-30 11:51:50'),
(36, 33, NULL, '', '', '', '', '', '', '', '', '2025-06-08 17:05:47', '2025-06-08 17:05:47'),
(37, 43, NULL, '', '', '', '', '', '', '', '', '2025-06-08 17:05:47', '2025-06-08 17:05:47'),
(54, 33, '', 'Dhaka', 'dhaka', 'savar', 'khagan', 'I JUST FORGOT IT', 'khagan street', 'h-34', 'additional detail will be here', '2025-06-11 04:49:15', '2025-06-11 04:49:15'),
(55, 33, '', 'Dhaka', 'dhaka', 'savar', 'khagan', 'I JUST FORGOT IT', 'khagan street', 'h-34', 'additional detail will be here', '2025-06-11 04:49:15', '2025-06-11 04:49:15'),
(58, 33, '', 'Dhaka', 'dhaka', 'savar', 'khagan', 'I JUST FORGOT IT', 'khagan street', 'h-34', 'here would be some additional details', '2025-06-12 06:14:42', '2025-06-12 06:14:42'),
(59, 33, '', 'Dhaka', 'dhaka', 'savar', 'khagan', 'I JUST FORGOT IT', 'khagan street', 'h-34', 'here would be some additional details', '2025-06-12 06:14:42', '2025-06-12 06:14:42'),
(64, 33, '', 'Dhaka', 'dahka', 'savar', 'khagan', '6442', 'khagan street', 'h-34', '', '2025-06-22 19:27:09', '2025-07-24 21:29:52'),
(65, 33, '', 'Dhaka', 'dahka', 'savar', 'khagan', 'I JUST FORGOT IT', 'khagan street', 'h-34', '', '2025-06-22 19:27:09', '2025-06-22 19:27:09'),
(66, 33, '', 'Dhaka', 'dahka', 'savar', 'khagan', '3422', 'khagan street', 'h-34', '', '2025-07-15 03:20:00', '2025-07-18 22:13:37'),
(67, 33, '', 'Dhaka', 'dahka', 'savar', 'khagan', '3423', 'khagan street', 'h-34', '', '2025-07-15 03:20:00', '2025-07-15 03:20:00'),
(70, 33, '', 'Dhaka', 'dahka', 'savar', 'khagan', '3434', 'khagan street', 'h-34', '', '2025-07-16 08:30:35', '2025-07-16 08:30:35'),
(71, 33, '', 'Dhaka', 'dahka', 'savar', 'khagan', '3434', 'khagan street', 'h-34', '', '2025-07-16 08:30:35', '2025-07-16 08:30:35'),
(74, 33, '', 'Dhaka', 'dahka', 'savar', 'khagan', '4323', 'khagan street', 'h-34', '', '2025-07-16 10:19:02', '2025-07-16 10:19:02'),
(75, 33, '', 'Dhaka', 'dahka', 'savar', 'khagan', '4323', 'khagan street', 'h-34', '', '2025-07-16 10:19:02', '2025-07-16 10:19:02'),
(76, 33, '', 'Dhaka', 'dahka', 'savar', 'khagan', '6454', 'khagan street', 'h-34', '', '2025-07-16 10:24:17', '2025-07-16 10:24:17'),
(77, 33, '', 'Dhaka', 'dahka', 'savar', 'khagan', '6454', 'khagan street', 'h-34', '', '2025-07-16 10:24:17', '2025-07-16 10:24:17'),
(78, 33, '', 'Dhaka', 'dahka', 'savar', 'khagan', '5432', 'khagan street', 'h-34', '', '2025-07-16 22:33:04', '2025-07-16 22:33:04'),
(79, 33, '', 'Dhaka', 'dahka', 'savar', 'khagan', '5432', 'khagan street', 'h-34', '', '2025-07-16 22:33:04', '2025-07-16 22:33:04'),
(80, 33, '', 'Dhaka', 'dahka', 'savar', 'khagan', '6342', 'khagan street', 'h-34', 'this is additional detail', '2025-07-17 19:06:51', '2025-07-17 19:06:51'),
(81, 33, '', 'Dhaka', 'dahka', 'savar', 'khagan', '6342', 'khagan street', 'h-34', 'this is additional detail', '2025-07-17 19:06:51', '2025-07-17 19:06:51');

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
(16, 'Lazina', 'Khatun', 'lazina123@gmail.com', '$2y$10$ZPhZHEO5SWQdZJL22VCh5egDSXuYYv.U3SfG5H3uGIxzeMCCLi2F6', 2, '2025-07-16 18:57:53', '2025-07-16 19:00:30');

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
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `hostel_id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','in_progress','resolved','rejected') NOT NULL DEFAULT 'pending',
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `resolved_at` datetime DEFAULT NULL,
  `resolved_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `student_id`, `hostel_id`, `room_id`, `category_id`, `title`, `description`, `status`, `priority`, `created_at`, `updated_at`, `resolved_at`, `resolved_by`) VALUES
(1, 28, 1, 83, 4, 'cleaning issue', 'A student not cleaning the room properly', 'pending', 'high', '2025-08-01 16:28:30', '2025-08-01 16:28:30', NULL, NULL),
(4, 28, 1, 83, 4, 'bad smell', 'after opening the window there\'s a lot of bad smell coming from outside', 'rejected', 'high', '2025-08-01 17:21:49', '2025-08-01 19:13:56', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `complaint_attachments`
--

CREATE TABLE `complaint_attachments` (
  `id` int(11) NOT NULL,
  `complaint_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaint_categories`
--

CREATE TABLE `complaint_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaint_categories`
--

INSERT INTO `complaint_categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Electrical', 'Issues related to electrical systems - lights, switches, power outlets, etc.', '2025-08-01 13:53:00', '2025-08-01 13:53:00'),
(2, 'Plumbing', 'Problems with water supply, drainage, toilets, showers, etc.', '2025-08-01 13:53:00', '2025-08-01 13:53:00'),
(3, 'Furniture', 'Issues with beds, chairs, tables, wardrobes, etc.', '2025-08-01 13:53:00', '2025-08-01 13:53:00'),
(4, 'Cleanliness', 'Complaints about room cleanliness, common areas, or sanitation', '2025-08-01 13:53:00', '2025-08-01 13:53:00'),
(5, 'Security', 'Concerns about security, unauthorized access, or safety issues', '2025-08-01 13:53:00', '2025-08-01 13:53:00'),
(6, 'Noise', 'Noise complaints from neighbors or other disturbances', '2025-08-01 13:53:00', '2025-08-01 13:53:00'),
(7, 'Internet', 'Issues with WiFi connectivity or internet speed', '2025-08-01 13:53:00', '2025-08-01 13:53:00'),
(8, 'Maintenance', 'General maintenance requests for the room or common areas', '2025-08-01 13:53:00', '2025-08-01 13:53:00'),
(9, 'Other', 'Any other complaints not covered by the above categories', '2025-08-01 13:53:00', '2025-08-01 13:53:00');

-- --------------------------------------------------------

--
-- Table structure for table `complaint_comments`
--

CREATE TABLE `complaint_comments` (
  `id` int(11) NOT NULL,
  `complaint_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` enum('student','admin') NOT NULL,
  `comment` text NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaint_comments`
--

INSERT INTO `complaint_comments` (`id`, `complaint_id`, `user_id`, `user_type`, `comment`, `attachment_path`, `created_at`) VALUES
(1, 1, 28, 'student', 'Please solve the issue. (comment)', NULL, '2025-08-01 16:38:25'),
(2, 4, 28, 'student', 'Please, solve the issue fast', NULL, '2025-08-01 17:22:36'),
(3, 4, 7, 'admin', 'Is this coming from outside?', NULL, '2025-08-01 17:27:36'),
(4, 4, 28, 'student', 'Yes, bad smell coming from outside', NULL, '2025-08-01 17:39:22'),
(5, 4, 7, 'admin', 'Ok, I am taking steps to solve the issue.', NULL, '2025-08-01 17:57:13'),
(16, 4, 7, 'admin', 'And until the issue is resolved, keep the window clos', NULL, '2025-08-01 18:19:42');

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
(3, 1, 3, '3rd', '2025-05-30 18:11:40', '2025-07-16 18:06:17'),
(4, 3, 1, 'Ground Floor', '2025-06-02 07:02:10', '2025-06-02 07:02:10'),
(5, NULL, 1, 'ground', '2025-07-16 18:12:40', '2025-07-16 18:12:40');

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
  `email` varchar(100) DEFAULT NULL,
  `capacity` int(11) DEFAULT 0,
  `amenities` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hostels`
--

INSERT INTO `hostels` (`id`, `hostel_incharge_id`, `address_id`, `hostel_name`, `hostel_type`, `contact_number`, `email`, `capacity`, `amenities`, `created_at`, `updated_at`) VALUES
(1, 8, 67, 'Dhanmondi Hostel', 'male', '+8801712345678', 'dhanmondihall123@gmail.com', 200, 'Wi-Fi, Laundry, Security, Mess', '2025-05-23 19:13:00', '2025-07-17 17:47:53'),
(3, 8, 31, 'shantir Nir', 'female', '01929951023', 'shantirnirhall123@gmail.com', 80, '', '2025-05-30 08:28:03', '2025-07-15 13:02:18');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `display_name`, `account_number`, `active`, `created_at`, `updated_at`) VALUES
(1, 'bkash', 'bKash Personal', '01723908423', 1, '2025-07-19 07:09:25', '2025-07-29 19:17:06'),
(2, 'nagad', 'Nagad Merchant', '01802893742', 1, '2025-07-19 07:09:25', '2025-07-24 18:11:42'),
(3, 'rocket', 'Rocket Personal', '01997274945', 1, '2025-07-19 07:09:25', '2025-07-24 18:10:34'),
(4, 'cash', 'Cash Payment', NULL, 1, '2025-07-19 07:09:25', '2025-07-26 11:05:00'),
(5, 'bank_transfer', 'Bank Transfer', 'AC-1234567890', 1, '2025-07-19 07:09:25', '2025-07-31 12:22:42');

-- --------------------------------------------------------

--
-- Table structure for table `payment_receipts`
--

CREATE TABLE `payment_receipts` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `receipt_path` varchar(255) NOT NULL,
  `generated_at` datetime NOT NULL,
  `generated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime NOT NULL,
  `payment_method_id` int(11) NOT NULL,
  `reference_code` varchar(100) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `receipt_number` varchar(50) DEFAULT NULL,
  `sender_mobile` varchar(20) DEFAULT NULL,
  `sender_name` varchar(100) DEFAULT NULL,
  `screenshot_path` varchar(255) DEFAULT NULL,
  `verification_status` enum('pending','verified','rejected') DEFAULT 'pending',
  `verified_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_transactions`
--

INSERT INTO `payment_transactions` (`id`, `payment_id`, `amount`, `payment_date`, `payment_method_id`, `reference_code`, `transaction_id`, `receipt_number`, `sender_mobile`, `sender_name`, `screenshot_path`, `verification_status`, `verified_by`, `notes`, `created_at`, `updated_at`) VALUES
(155, 410, '16000.00', '2025-07-30 16:15:53', 4, '', NULL, NULL, NULL, NULL, NULL, 'verified', 7, '', '2025-07-30 14:15:53', NULL),
(156, 413, '200.00', '2025-07-30 16:17:48', 4, '', NULL, NULL, NULL, NULL, NULL, 'verified', 7, '', '2025-07-30 14:17:48', NULL),
(157, 413, '14000.00', '2025-07-30 16:19:28', 4, '', NULL, NULL, NULL, NULL, NULL, 'verified', 7, '', '2025-07-30 14:19:28', NULL),
(158, 411, '5000.00', '2025-07-30 16:35:24', 4, '', NULL, NULL, NULL, NULL, NULL, 'verified', 7, '', '2025-07-30 14:35:24', NULL),
(159, 414, '600.00', '2025-07-30 16:36:05', 1, '', NULL, NULL, NULL, NULL, NULL, 'verified', 7, '', '2025-07-30 14:36:05', '2025-08-01 18:40:54'),
(160, 411, '30.00', '2025-07-30 21:27:49', 4, '', NULL, NULL, '01929951543', NULL, NULL, 'verified', 7, '', '2025-07-30 15:27:49', NULL),
(161, 421, '15000.00', '2025-07-31 17:58:31', 1, '5423417', '2342342', NULL, '01929951023', 'Shekh Russel', NULL, 'verified', 7, NULL, '2025-07-31 11:58:31', '2025-07-31 18:17:27'),
(162, 418, '15050.00', '2025-07-31 18:21:30', 3, '5423417', '2342342', NULL, '01929951023', 'Shekh Russel', NULL, 'verified', 7, NULL, '2025-07-31 12:21:30', '2025-07-31 18:23:36'),
(163, 415, '15050.00', '2025-08-01 16:13:49', 3, '5423417', '2342342', NULL, '01929951023', 'Shekh Russel', NULL, 'rejected', 10, NULL, '2025-08-01 10:13:49', '2025-08-01 18:18:22'),
(164, 415, '500.00', '2025-08-01 18:42:03', 2, '5423417', NULL, NULL, '01929951023', NULL, NULL, 'rejected', 7, '', '2025-08-01 12:42:03', '2025-08-01 18:45:58');

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
(77, 2, 1, 'G108', 2, '2025-05-24 02:59:32', '2025-06-02 14:34:29', 1),
(78, 4, 1, 'G109', 4, '2025-05-24 02:59:32', '2025-06-02 14:34:29', 1),
(81, 4, 3, 'G111', 5, '2025-05-30 19:06:39', '2025-06-02 14:34:29', 1),
(82, 4, 2, 'g112', 5, '2025-05-31 11:25:05', '2025-06-02 14:34:29', 1),
(83, 4, 3, 'G222', 4, '2025-05-31 13:07:38', '2025-06-23 09:14:17', 1),
(102, 14, 4, 'snr-0002', 2, '2025-06-02 16:46:48', '2025-08-02 09:15:14', 3),
(105, 4, 2, 'sn0001', 5, '2025-06-03 09:52:31', '2025-06-03 10:28:54', 1),
(106, 4, 1, 'dh0002', 4, '2025-06-03 10:30:22', '2025-08-02 08:14:21', 1),
(107, 1, 3, 'g221', 1, '2025-07-16 10:54:38', '2025-07-16 10:54:38', 1),
(108, 14, 4, 'snr-0001', 2, '2025-08-02 09:13:19', '2025-08-02 09:13:19', 3),
(109, 17, 4, 'snr-0003', 3, '2025-08-02 09:16:12', '2025-08-02 09:16:12', 3);

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
(13, 'single', '', 1, '2025-06-04 07:11:49', '2025-06-04 07:11:49', 1, 3),
(14, 'double', '', 2, '2025-06-04 07:47:27', '2025-06-04 07:47:27', 1, 3),
(16, 'triple', '', 3, '2025-06-05 13:57:37', '2025-07-16 10:45:11', 0, 1),
(17, 'triple', '', 3, '2025-06-05 18:01:23', '2025-06-05 18:01:23', 0, 3),
(20, 'suit', '', 3, '2025-07-16 10:44:32', '2025-07-16 10:44:32', 0, 1);

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
  `is_checked_out` tinyint(1) DEFAULT 0,
  `check_in_at` datetime DEFAULT NULL,
  `check_out_at` datetime DEFAULT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `mother_name` varchar(100) DEFAULT NULL,
  `father_contact` varchar(20) DEFAULT NULL,
  `mother_contact` varchar(20) DEFAULT NULL,
  `emergency_contact` varchar(20) DEFAULT NULL,
  `blood_group` enum('A+','A-','B+','B-','AB+','AB-','O+','O-','Unknown') DEFAULT 'Unknown',
  `floor_id` int(11) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `profile_image_url`, `hostel_id`, `room_id`, `permanent_address_id`, `temporary_address_id`, `first_name`, `last_name`, `email`, `password`, `gender`, `contact_number`, `is_verified`, `is_approved`, `verification_token`, `varsity_id`, `department`, `batch_year`, `detail`, `created_at`, `updated_at`, `is_checked_in`, `is_checked_out`, `check_in_at`, `check_out_at`, `father_name`, `mother_name`, `father_contact`, `mother_contact`, `emergency_contact`, `blood_group`, `floor_id`, `reset_token`, `reset_expires`) VALUES
(17, 'http://localhost/hostel-management-system/student/assets/images/profile_22_1752749533.jpg', 1, 83, 64, 65, 'billu', 'mia', 'billu123@gmail.com', '$2y$10$xMtQYjQZvodQ.Usi5Get3OnEMkR2GqtAKFonziU.SigOQQagYMkei', 'male', '01929951028', 1, 1, NULL, 'VARSITY001', 'LAW', '3rd', NULL, '2025-06-22 19:27:09', '2025-07-19 16:24:23', 1, 0, '2025-06-23 01:26:00', NULL, 'borkot uddin', 'Jahara Bagum', '00821903124', '91723123143', '01283012232', 'O+', 3, NULL, NULL),
(22, 'http://localhost/hostel-management-system/student/assets/images/profile_22_1752749533.jpg', 1, 77, 66, 67, 'Mehtab', 'Islam', 'mehtab123@gmail.com', '$2y$10$7hX.TDIjkUStLZyZfePm0uKjXlBw06V5TZtjArB7NHF6Nmsz7iRRO', 'male', '01929951543', 1, 1, NULL, '01283012312', 'CSE', '2nd', '', '2025-07-15 03:20:00', '2025-07-17 16:16:49', 1, 0, '2025-07-15 11:37:00', NULL, 'borkot uddin', 'Jahara Bagum', '00821903153', '91723123143', '012830122323', 'O+', 1, NULL, NULL),
(24, NULL, NULL, NULL, 70, 71, 'Mehedi', 'Islam', 'mehedi123@gmail.com', '$2y$10$F./5AG6WdbjyG05Rj5nTF.oSUEF4Uu8cqyX2ZxdvKUvPXKT2DPOiq', 'male', '01924351023', 0, 0, NULL, '01231231', 'LAW', '1st', '', '2025-07-16 08:30:35', '2025-07-16 21:33:00', 0, 0, NULL, NULL, 'borkot uddin', 'Jahara Bagum', '00821903124', '91723123143', '01283012232', 'A+', NULL, NULL, NULL),
(26, NULL, 1, 83, 74, 75, 'Mamun', 'Khan', 'mamun123@gmail.com', '$2y$10$59EFCYRR6idMrzMpJih.julB2RbGQoiPqIbno7FcE1wPCrYeRIBgC', 'male', '01929541023', 0, 0, NULL, '0321842034', 'SE', NULL, NULL, '2025-07-16 10:19:02', '2025-07-17 18:13:20', 0, 0, NULL, NULL, 'borkot uddin', 'Jahara Bagum', '00821903124', '91723123143', '01283012232', 'Unknown', 3, NULL, NULL),
(27, NULL, 1, 78, 76, 77, 'Shimanto', 'Bissash', 'shimanto123@gmail.com', '$2y$10$GjsjZmcxOpWO4fEUyboBJ.drj3zDx1v.n6zLx02wrFZxM3LF5aX0u', 'male', '01928451023', 1, 1, NULL, '546345345', NULL, NULL, NULL, '2025-07-16 10:24:17', '2025-08-02 09:10:54', 1, 0, '2025-08-02 14:53:00', NULL, 'borkot uddin', 'Jahara Bagum', '00821903124', '91723123143', '012830122323', 'Unknown', 1, NULL, NULL),
(28, 'http://localhost/hostel-management-system/student/assets/images/profile_28_1752775663.jpg', 1, 83, 78, 79, 'Shekh', 'Russel', 'shekhrussel140@gmail.com', '$2y$10$UUvUP8fLwc1bqpcw85NZgOXUfZTSH9MiwEIiNGGQDBtzV6A/l9dJi', 'male', '01929951023', 1, 1, NULL, '2342342', 'CSE', '3rd', '', '2025-07-16 22:33:04', '2025-07-31 18:27:45', 1, 0, '2025-07-18 00:05:00', NULL, 'borkot uddin', 'Jahara Bagum', '00821903124', '91723123143', '019299510254', 'A+', 3, NULL, NULL),
(29, NULL, 3, 102, 80, 81, 'Lazina', 'Khatun', 'lazinacse@gmail.com', '$2y$10$Ooxlgkl0xPBnGXYfq2Mx0eOw7xdh4LaBgqclWF1W1eJcCgZ3LWihm', 'female', '01929951054', 1, 1, NULL, '0272210005101075', 'CSE', '4th', NULL, '2025-07-17 19:06:51', '2025-08-02 07:47:24', 1, 0, '2025-08-02 13:22:00', NULL, 'borkot uddin', 'Jahara Bagum', '00821903124', '91723123143', '019299510254', 'Unknown', 4, NULL, NULL);

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
(414, 22, 1, 77, 2, 4, 2025, 2, '16050.00', '600.00', '15450.00', '0.00', 'partial', '2025-02-05', '50.00', '2025-02-05', 1, 0, '2025-07-30 14:16:20', '2025-08-01 12:40:54', 7, 7),
(415, 28, 1, 83, 4, 16, 2025, 2, '15050.00', '0.00', '15050.00', '0.00', 'unpaid', '2025-02-05', '50.00', '2025-02-05', 1, 0, '2025-07-30 14:16:20', '2025-07-30 14:16:20', NULL, 7),
(416, 17, 1, 83, 4, 16, 2025, 3, '15050.00', '0.00', '14950.00', '100.00', 'partial', '2025-07-30', '50.00', '2025-07-05', 1, 0, '2025-07-30 17:55:46', '2025-07-30 17:55:46', NULL, 7),
(417, 22, 1, 77, 2, 4, 2025, 3, '16050.00', '0.00', '16050.00', '0.00', 'unpaid', '2025-07-30', '50.00', '2025-07-05', 1, 0, '2025-07-30 17:55:46', '2025-07-30 17:55:46', NULL, 7),
(418, 28, 1, 83, 4, 16, 2025, 3, '15050.00', '15050.00', '0.00', '0.00', 'paid', '2025-07-30', '50.00', '2025-07-05', 1, 1, '2025-07-30 17:55:46', '2025-07-31 12:23:36', 7, 7),
(419, 17, 1, 83, 4, 16, 2025, 6, '15000.00', '0.00', '15000.00', '0.00', 'unpaid', '2025-08-31', '50.00', '2025-08-30', 0, 0, '2025-07-30 18:54:27', '2025-07-30 18:54:27', NULL, 7),
(420, 22, 1, 77, 2, 4, 2025, 6, '16000.00', '0.00', '16000.00', '0.00', 'unpaid', '2025-08-31', '50.00', '2025-08-30', 0, 0, '2025-07-30 18:54:27', '2025-07-30 18:54:27', NULL, 7),
(421, 28, 1, 83, 4, 16, 2025, 6, '15000.00', '15000.00', '0.00', '0.00', 'paid', '2025-08-31', '50.00', '2025-08-30', 0, 0, '2025-07-30 18:54:27', '2025-07-31 12:17:28', 7, 7),
(422, 28, 1, 83, 4, 16, 2025, 4, '15050.00', '0.00', '15050.00', '0.00', 'unpaid', '2025-04-05', '50.00', '2025-04-05', 1, 0, '2025-08-02 07:44:55', '2025-08-02 07:44:55', NULL, 7),
(423, 17, 1, 83, 4, 16, 2025, 4, '15050.00', '0.00', '15050.00', '0.00', 'unpaid', '2025-04-05', '50.00', '2025-04-05', 1, 0, '2025-08-02 07:44:55', '2025-08-02 07:44:55', NULL, 7),
(424, 22, 1, 77, 2, 4, 2025, 4, '16050.00', '0.00', '16050.00', '0.00', 'unpaid', '2025-04-05', '50.00', '2025-04-05', 1, 0, '2025-08-02 07:44:56', '2025-08-02 07:44:56', NULL, 7),
(425, 17, 1, 83, 4, 16, 2025, 5, '15050.00', '0.00', '15050.00', '0.00', 'unpaid', '2025-05-02', '50.00', '2025-05-02', 1, 0, '2025-08-02 08:43:19', '2025-08-02 08:43:19', NULL, 7),
(426, 22, 1, 77, 2, 4, 2025, 5, '16050.00', '0.00', '16050.00', '0.00', 'unpaid', '2025-05-02', '50.00', '2025-05-02', 1, 0, '2025-08-02 08:43:19', '2025-08-02 08:43:19', NULL, 7),
(427, 28, 1, 83, 4, 16, 2025, 5, '15050.00', '0.00', '15050.00', '0.00', 'unpaid', '2025-05-02', '50.00', '2025-05-02', 1, 0, '2025-08-02 08:43:19', '2025-08-02 08:43:19', NULL, 7),
(428, 29, 3, 102, 14, 19, 2025, 9, '3000.00', '0.00', '3000.00', '0.00', 'unpaid', '2025-09-05', '50.00', '2025-09-05', 0, 0, '2025-08-02 09:18:26', '2025-08-02 09:18:26', NULL, 7),
(429, 17, 1, 83, 4, 16, 2025, 9, '15000.00', '0.00', '15000.00', '0.00', 'unpaid', '2025-09-05', '50.00', '2025-09-05', 0, 0, '2025-08-02 09:18:26', '2025-08-02 09:18:26', NULL, 7),
(430, 22, 1, 77, 2, 4, 2025, 9, '16000.00', '0.00', '16000.00', '0.00', 'unpaid', '2025-09-05', '50.00', '2025-09-05', 0, 0, '2025-08-02 09:18:26', '2025-08-02 09:18:26', NULL, 7),
(431, 27, 1, 78, 4, 16, 2025, 9, '15000.00', '0.00', '15000.00', '0.00', 'unpaid', '2025-09-05', '50.00', '2025-09-05', 0, 0, '2025-08-02 09:18:26', '2025-08-02 09:18:26', NULL, 7),
(432, 28, 1, 83, 4, 16, 2025, 9, '15000.00', '0.00', '15000.00', '0.00', 'unpaid', '2025-09-05', '50.00', '2025-09-05', 0, 0, '2025-08-02 09:18:26', '2025-08-02 09:18:26', NULL, 7);

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
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `hostel_id` (`hostel_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `resolved_by` (`resolved_by`);

--
-- Indexes for table `complaint_attachments`
--
ALTER TABLE `complaint_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complaint_id` (`complaint_id`);

--
-- Indexes for table `complaint_categories`
--
ALTER TABLE `complaint_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `complaint_comments`
--
ALTER TABLE `complaint_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complaint_id` (`complaint_id`);

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
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `payment_receipts`
--
ALTER TABLE `payment_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `generated_by` (`generated_by`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_id` (`payment_id`),
  ADD KEY `verified_by` (`verified_by`),
  ADD KEY `payment_method_id` (`payment_method_id`);

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
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `admin_types`
--
ALTER TABLE `admin_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `complaint_attachments`
--
ALTER TABLE `complaint_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `complaint_categories`
--
ALTER TABLE `complaint_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `complaint_comments`
--
ALTER TABLE `complaint_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `floors`
--
ALTER TABLE `floors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hostels`
--
ALTER TABLE `hostels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payment_receipts`
--
ALTER TABLE `payment_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `room_fees`
--
ALTER TABLE `room_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `student_payments`
--
ALTER TABLE `student_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=433;

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
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `complaints_ibfk_2` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `complaints_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `complaints_ibfk_4` FOREIGN KEY (`category_id`) REFERENCES `complaint_categories` (`id`),
  ADD CONSTRAINT `complaints_ibfk_5` FOREIGN KEY (`resolved_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `complaint_attachments`
--
ALTER TABLE `complaint_attachments`
  ADD CONSTRAINT `complaint_attachments_ibfk_1` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `complaint_comments`
--
ALTER TABLE `complaint_comments`
  ADD CONSTRAINT `complaint_comments_ibfk_1` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `payment_receipts`
--
ALTER TABLE `payment_receipts`
  ADD CONSTRAINT `payment_receipts_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `payment_transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_receipts_ibfk_2` FOREIGN KEY (`generated_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `payment_transactions_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `student_payments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_transactions_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_transactions_ibfk_3` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`);

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
