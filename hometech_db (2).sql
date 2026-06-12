-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2026 at 02:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hometech_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `service_id` int(11) NOT NULL,
  `appointment_time` datetime NOT NULL,
  `note` text DEFAULT NULL,
  `total_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('đang chờ','đã xác nhận','đang xử lý','đã hoàn tất','đã hủy') NOT NULL DEFAULT 'đang chờ',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `technician_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `district` varchar(100) DEFAULT NULL,
  `service_id` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `appointment_time` date NOT NULL,
  `final_price` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `customer_signature` varchar(255) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT 'unpaid',
  `transaction_code` varchar(100) DEFAULT NULL,
  `photo_before` varchar(255) DEFAULT NULL,
  `photo_after` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `customer_id`, `technician_id`, `customer_name`, `phone`, `address`, `district`, `service_id`, `note`, `appointment_time`, `final_price`, `status`, `customer_signature`, `payment_status`, `transaction_code`, `photo_before`, `photo_after`, `created_at`, `lat`, `lng`, `updated_at`) VALUES
(1, 1, 3, 'Kỳ Ân', '0388', '123 Đường ABC, Phường XYZ, Quận 1', NULL, 2, NULL, '2025-10-31', NULL, 'completed', 'assets/upload/signatures/sig_1_1766911848.png', 'unpaid', NULL, NULL, NULL, '2025-10-31 09:21:23', NULL, NULL, '2025-12-28 08:50:48'),
(2, 123, 3, 'Tên Khách Hàng Mẫu', '0388074313', '123 Đường ABC, Phường XYZ, Quận 1', NULL, 4, NULL, '2025-10-31', NULL, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-10-31 09:35:49', NULL, NULL, NULL),
(3, 123, 3, 'Tên Khách Hàng Mẫu', '0388074313', '123 Đường ABC, Phường XYZ, Quận 1', NULL, 4, NULL, '2025-10-31', NULL, 'completed', 'assets/upload/signatures/sig_3_1766911451.png', 'unpaid', NULL, '1763543222_logo_goc.png', '1763543222_Ảnh chụp màn hình 2025-10-03 195054.png', '2025-10-31 09:36:42', NULL, NULL, '2025-12-28 08:44:11'),
(4, 123, 3, 'Tên Khách Hàng Mẫu', '0388074313', '123 Đường ABC, Phường XYZ, Quận 1', NULL, 4, NULL, '2025-10-31', NULL, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-10-31 09:39:52', NULL, NULL, NULL),
(5, 2, NULL, 'Nguyễn Văn A', '0900000002', '123 Đường ABC, Phường XYZ, Quận 1', NULL, 1, NULL, '2025-10-31', NULL, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-10-31 09:46:50', NULL, NULL, NULL),
(6, 4, 3, 'Kỳ Ân', '0388074313', '80/16e', NULL, 3, NULL, '2025-10-31', NULL, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-10-31 10:13:59', NULL, NULL, NULL),
(7, 5, 3, 'Nguyễn Văn A', '0900000002', '80/16e', NULL, 2, NULL, '2025-10-31', NULL, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-10-31 10:14:29', NULL, NULL, NULL),
(8, 2, 3, 'Kỳ Ân', '0388074313', '80/16e', NULL, 4, NULL, '2025-11-01', NULL, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-01 06:54:48', NULL, NULL, NULL),
(9, 3, 3, 'Kỳ Ân', '0388074313', '123 Đường ABC, Phường XYZ, Quận 1', NULL, 4, NULL, '2025-11-01', NULL, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-01 06:57:41', NULL, NULL, NULL),
(10, 2, NULL, 'Nguyễn Văn A', '0388074313', '123 Đường ABC, Phường XYZ, Quận 1', NULL, 2, NULL, '2025-11-01', NULL, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-01 07:02:47', NULL, NULL, NULL),
(11, 2, 3, 'Nguyễn Văn A', '0900000002', 'sjadgkj', 'Quận 7', 3, NULL, '2025-11-01', 300000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-01 09:34:26', NULL, NULL, NULL),
(12, 2, NULL, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Quận 7', 2, NULL, '2025-11-01', 360000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-01 09:37:32', NULL, NULL, NULL),
(13, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Quận 7', 2, NULL, '2025-11-01', 360000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-01 11:07:17', NULL, NULL, NULL),
(14, 6, NULL, 'Naykei', '0901234567', 'Vĩnh Long', 'Quận 7', 2, NULL, '2025-11-02', 360000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-02 08:20:45', NULL, NULL, NULL),
(15, 6, 3, 'Naykei', '0901234567', 'Vĩnh Long', 'Quận 1', 2, NULL, '2025-11-02', 414000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-02 08:22:04', NULL, NULL, NULL),
(16, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Quận 1', 2, NULL, '2025-11-02', 379500.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-04 05:14:40', NULL, NULL, NULL),
(18, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Quận 1', 2, NULL, '2025-11-14', 379500.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-14 01:08:29', NULL, NULL, NULL),
(19, 2, NULL, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Quận 1', 1, NULL, '2025-11-14', 172500.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-14 01:14:26', NULL, NULL, NULL),
(20, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Quận 7', 20, NULL, '2025-11-14', 200000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-14 03:07:22', NULL, NULL, NULL),
(21, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Quận Ba Đình', 14, NULL, '2025-11-14', 200000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-14 03:32:29', NULL, NULL, NULL),
(22, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Quận Ba Đình', 9, NULL, '2025-11-14', 200000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-14 03:41:16', NULL, NULL, NULL),
(23, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Quận Ba Đình', 23, NULL, '2025-11-14', 200000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-14 03:42:50', NULL, NULL, NULL),
(24, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Quận Ba Đình', 19, NULL, '2025-11-14', 200000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-14 04:18:46', NULL, NULL, NULL),
(25, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Hóc Môn', 9, NULL, '2025-11-14', 250000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-14 04:23:20', NULL, NULL, NULL),
(26, 2, NULL, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Quận Ba Đình', 9, NULL, '2025-11-14', 150000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-14 04:31:09', NULL, NULL, NULL),
(27, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Hóc Môn', 19, 'Khung giờ 16:00:00 - Phụ phí khu vực xa +50.000đ', '2025-11-19', 150000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-19 15:23:35', NULL, NULL, NULL),
(28, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Quận Ba Đình', 16, 'Khung giờ 09:00:00', '2025-11-19', 500000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-19 15:31:24', NULL, NULL, NULL),
(29, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Quận Ba Đình', 23, 'Khung giờ 09:00:00', '2025-11-19', 100000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-19 15:53:11', NULL, NULL, NULL),
(30, 2, NULL, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Quận Hoàn Kiếm', 4, 'Khung giờ 09:00:00', '2025-11-19', 120000.00, 'cancelled', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-19 15:53:49', NULL, NULL, NULL),
(31, 4, 3, 'kyan123', '0986327883', 'Hậu Giang', 'Hóc Môn', 19, 'Khung giờ 09:00:00 - Phụ phí khu vực xa +50.000đ', '2025-11-19', 150000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-19 15:56:14', NULL, NULL, NULL),
(32, 6, 3, 'Naykei', '0901234567', 'Vĩnh Long', 'Quận Ba Đình', 23, 'Khung giờ 09:00:00', '2025-11-19', 100000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-19 15:57:25', NULL, NULL, NULL),
(33, 6, 3, 'Naykei', '0901234567', 'Vĩnh Long', 'Quận Ba Đình', 23, 'Khung giờ 09:00:00', '2025-11-22', 100000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-19 15:57:31', NULL, NULL, NULL),
(34, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Quận Ba Đình', 9, 'Khung giờ 09:00:00', '2025-11-20', 150000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-20 00:59:02', NULL, NULL, NULL),
(35, 2, 3, 'Nguyễn Văn A', '0900000002', 'Vĩnh Long', 'Hóc Môn', 1, 'Khung giờ 09:00:00 - Phụ phí khu vực xa +50.000đ', '2025-11-20', 200000.00, 'completed', 'assets/upload/signatures/sig_35_1763807045.png', 'unpaid', NULL, NULL, NULL, '2025-11-20 01:13:31', NULL, NULL, '2025-11-22 10:24:05'),
(36, 2, 3, 'Nguyễn Văn A', '0900000002', 'Hồ Chí Minh', 'Hóc Môn', 1, 'Khung giờ 09:00:00 - Phụ phí khu vực xa +50.000đ', '2025-11-20', 200000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-20 01:14:27', 10.86546550, 106.60101240, NULL),
(37, 2, NULL, 'Nguyễn Văn A', '0900000002', 'Vĩnh Long', 'Xã An Bình', 14, 'Khung giờ 09:00:00', '2025-11-20', 250000.00, 'cancelled', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-20 02:11:16', 10.28084310, 105.99771810, NULL),
(38, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Xã Đá Bạc', 9, 'Khung giờ 09:00:00', '2025-11-19', 150000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-20 02:19:12', 15.94402890, 108.17340810, NULL),
(39, 5, 3, 'kyan123', '0986327833', 'Hậu Giang', 'Phường Thục Phán', 17, 'Khung giờ 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-22', 180000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-22 10:59:55', NULL, NULL, NULL),
(40, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Xã Bạch Hà', 17, 'Khung giờ 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-22', 180000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-22 11:03:01', NULL, NULL, NULL),
(41, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Xã Công Sơn', 18, 'Khung giờ 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-22', 300000.00, 'completed', 'assets/upload/signatures/sig_41_1763886205.png', 'unpaid', NULL, NULL, NULL, '2025-11-22 11:05:56', NULL, NULL, '2025-11-23 08:23:25'),
(42, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Phường Buôn Ma Thuột', 8, 'Khung giờ 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-22', 480000.00, 'completed', 'assets/upload/signatures/sig_42_1763886552.png', 'unpaid', NULL, NULL, NULL, '2025-11-22 11:22:50', NULL, NULL, '2025-11-23 08:29:12'),
(44, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Phường Nhân Hòa', 7, 'Khung giờ 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-23', 300000.00, 'completed', 'assets/upload/signatures/sig_44_1763866580.png', 'unpaid', NULL, NULL, NULL, '2025-11-23 01:39:29', 20.25144760, 105.98780960, '2025-11-23 02:56:20'),
(46, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Xã Đoàn Kết', 29, 'Khung giờ 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-23', 600000.00, 'completed', 'assets/upload/signatures/sig_46_1763886345.png', 'unpaid', NULL, NULL, NULL, '2025-11-23 08:24:45', NULL, NULL, '2025-11-23 08:25:45'),
(47, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Xã Mường Nhé', 7, 'Khung giờ 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-23', 300000.00, 'completed', 'assets/upload/signatures/sig_47_1763889354.png', 'unpaid', NULL, NULL, NULL, '2025-11-23 08:37:51', NULL, NULL, '2025-11-23 09:15:54'),
(48, 6, 3, 'Naykei', '0901234567', 'Vĩnh Long', 'Xã Đá Bạc', 32, 'Khung giờ 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-23', 1200.00, 'completed', 'assets/upload/signatures/sig_48_1763890399.png', 'unpaid', NULL, NULL, NULL, '2025-11-23 09:21:57', NULL, NULL, '2025-11-23 09:33:19'),
(49, 6, 3, 'Naykei', '0901234567', 'Vĩnh Long', 'Xã Công Sơn', 32, 'Khung giờ 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-23', 1200.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-23 09:22:58', 20.93646930, 105.64759690, NULL),
(50, 6, 3, 'Naykei', '0901234567', 'Vĩnh Long', 'Phường Ea Kao', 32, 'Khung giờ 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-23', 1200.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-23 10:20:37', NULL, NULL, NULL),
(51, 6, 3, 'Naykei', '0901234567', 'Vĩnh Long', 'Xã Mù Cả', 29, 'Khung giờ 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-23', 600000.00, 'completed', 'assets/upload/signatures/sig_51_1764562025.png', 'unpaid', NULL, NULL, NULL, '2025-11-23 11:02:23', NULL, NULL, '2025-12-01 04:07:05'),
(52, 6, 3, 'Naykei', '0901234567', 'Vĩnh Long', 'Xã Mù Cả', 29, 'Khung giờ 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-23', 600000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-23 11:02:30', NULL, NULL, NULL),
(53, 6, 3, 'Naykei', '0901234567', 'Vĩnh Long', 'Xã Mù Cả', 29, 'Khung giờ 11:00:00 - Phụ phí cuối tuần +20%', '2025-11-23', 600000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-23 11:02:44', NULL, NULL, NULL),
(54, 6, 3, 'Naykei', '0901234567', 'Vĩnh Long', 'Xã Bản Liền', 3, 'Khung giờ 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-23', 300000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-23 11:03:07', NULL, NULL, NULL),
(55, 6, 3, 'Naykei', '0901234567', 'Vĩnh Long', 'Xã Bản Liền', 3, 'Khung giờ 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-23', 300000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-23 11:12:31', NULL, NULL, NULL),
(56, 6, 3, 'Naykei', '0901234567', 'Vĩnh Long', 'Xã Cao Lộc', 28, 'Khung giờ 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-23', 360000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-23 11:13:22', NULL, NULL, NULL),
(57, 6, NULL, 'Naykei', '0901234567', 'Vĩnh Long', 'Xã Cái Đôi Vàm', 17, 'Khung giờ 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-23', 180000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-23 11:14:42', NULL, NULL, NULL),
(58, 6, NULL, 'Naykei', '0901234567', 'Vĩnh Long', 'Phường Lang Biang - Đà Lạt', 18, 'Khung giờ: 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-23', 300000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-23 11:16:58', NULL, NULL, NULL),
(59, 6, NULL, 'Naykei', '0901234567', 'Vĩnh Long', 'Phường Lang Biang - Đà Lạt', 18, 'Khung giờ: 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-23', 300000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-23 11:17:07', NULL, NULL, NULL),
(60, 6, NULL, 'Naykei', '0901234567', 'Vĩnh Long', 'Phường Phan Thiết', 8, 'Khung giờ 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-23', 480000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-23 11:19:11', NULL, NULL, NULL),
(61, 6, 3, 'Naykei', '0901234567', 'Vĩnh Long', 'Phường Phan Thiết', 8, 'Khung giờ 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-23', 480000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-23 11:19:29', NULL, NULL, NULL),
(62, 6, NULL, 'Naykei', '0901234567', 'Vĩnh Long', 'Phường Lý Thường Kiệt', 18, 'Khung giờ 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-23', 300000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-23 11:27:54', NULL, NULL, NULL),
(63, 6, 3, 'Naykei', '0901234567', 'Vĩnh Long', 'Phường Lý Thường Kiệt', 18, 'Khung giờ 09:00:00 - Phụ phí cuối tuần +20%', '2025-11-23', 300000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-11-23 11:30:21', NULL, NULL, NULL),
(64, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Xã Điềm He', 23, 'Khung giờ 09:00:00', '2025-12-01', 800000.00, '', NULL, 'unpaid', NULL, NULL, NULL, '2025-12-01 06:32:56', NULL, NULL, NULL),
(65, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Phường Nam Nha Trang', 23, 'Khung giờ 09:00:00', '2025-12-03', 800000.00, '', NULL, 'unpaid', NULL, NULL, NULL, '2025-12-03 09:59:24', NULL, NULL, NULL),
(66, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Phường Bến Thành', 20, 'Khung giờ 09:00:00', '2025-12-05', 500000.00, '', 'assets/upload/signatures/sig_66_1764897938.png', 'unpaid', NULL, NULL, NULL, '2025-12-05 01:22:29', NULL, NULL, '2025-12-05 01:25:38'),
(67, 5, 3, 'kyan123', '0986327833', 'Hậu Giang', 'Xã Bến Hải', 17, 'Khung giờ 09:00:00', '2025-12-06', 150000.00, 'completed', 'assets/upload/signatures/sig_67_1764899087.png', 'unpaid', NULL, NULL, NULL, '2025-12-05 01:39:18', NULL, NULL, '2025-12-05 01:44:47'),
(68, 2, 3, 'Nguyễn Văn A', '0900000002', 'Phường 1', 'Thành phố Vĩnh Long', 32, 'Khung giờ 09:00:00 - Khu vực xa (+50,000đ)', '2025-12-26', 51000.00, '', 'assets/upload/signatures/sig_68_1766720711.png', 'unpaid', NULL, NULL, NULL, '2025-12-26 03:41:49', 21.00020470, 105.82209440, '2025-12-26 03:45:11'),
(69, 2, 3, 'Nguyễn Văn A', '0900000002', 'Vĩnh Long', 'Thành phố Vĩnh Long', 32, 'Khung giờ 09:00:00 - Khu vực xa (+50,000đ)', '2025-12-26', 51000.00, '', 'assets/upload/signatures/sig_69_1766722373.png', 'unpaid', NULL, NULL, NULL, '2025-12-26 04:10:23', 10.25269390, 105.94100190, '2025-12-26 04:12:53'),
(70, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Thành phố Vĩnh Long', 32, 'Khung giờ 09:00:00 - Khu vực xa (+50,000đ)', '2025-12-26', 51000.00, 'completed', 'assets/upload/signatures/sig_70_1766723155.png', 'paid', NULL, NULL, NULL, '2025-12-26 04:23:59', 10.24785400, 105.96117700, '2025-12-26 04:25:55'),
(71, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Huyện Vũng Liêm', 28, 'Khung giờ 09:00:00 - Khu vực xa (+50,000đ)', '2025-12-26', 350000.00, '', 'assets/upload/signatures/sig_71_1766724110.png', 'paid', NULL, NULL, NULL, '2025-12-26 04:40:09', NULL, NULL, '2025-12-26 04:41:50'),
(72, 2, 3, 'Nguyễn Văn A', '0900000002', 'vĩnh long', 'Huyện Mang Thít', 28, 'Khung giờ 09:00:00 - Khu vực xa (+50,000đ)', '2025-12-26', 350000.00, '', 'assets/upload/signatures/sig_72_1766725190.png', 'paid', NULL, NULL, NULL, '2025-12-26 04:58:43', NULL, NULL, '2025-12-26 04:59:50'),
(73, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Thành phố Vĩnh Long', 18, 'Khung giờ 09:00:00 - Khu vực xa (+50,000đ)', '2025-12-26', 300000.00, '', NULL, 'paid', NULL, NULL, NULL, '2025-12-26 05:08:01', 10.24785400, 105.96117700, NULL),
(74, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Thành phố Vĩnh Long', 8, 'Khung giờ 09:00:00 - Khu vực xa (+50,000đ)', '2025-12-27', 450000.00, '', 'assets/upload/signatures/sig_74_1766727274.png', 'paid', NULL, NULL, NULL, '2025-12-26 05:32:46', 10.24785400, 105.96117700, '2025-12-26 05:34:34'),
(75, 2, 3, 'Nguyễn Văn A', '0900000002', 'Vĩnh Long', 'Thành phố Vĩnh Long', 24, 'Khung giờ 09:00:00 - Khu vực xa (+50,000đ)', '2025-12-26', 450000.00, 'completed', 'assets/upload/signatures/sig_75_1766727499.png', 'unpaid', NULL, NULL, NULL, '2025-12-26 05:37:12', 10.25269390, 105.94100190, '2025-12-26 05:38:19'),
(76, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Thành phố Vĩnh Long', 23, 'Khung giờ 09:00:00 - Khu vực xa (+50,000đ) | Cuối tuần (+20,000đ)', '2025-12-27', 870000.00, 'completed', 'assets/upload/signatures/sig_76_1766727999.png', 'unpaid', NULL, NULL, NULL, '2025-12-26 05:44:56', 10.24785400, 105.96117700, '2025-12-26 05:46:39'),
(77, 2, 3, 'Đinh Lương Kỳ Ân', '0900000002', 'Đà Nẵng', 'Thành phố Vĩnh Long', 17, 'Khung giờ 09:00:00 - Khu vực xa (+50,000đ)', '2025-12-26', 200000.00, 'completed', 'assets/upload/signatures/sig_77_1766736815.png', 'unpaid', NULL, NULL, NULL, '2025-12-26 08:09:39', 10.24785400, 105.96117700, '2025-12-26 08:13:35'),
(78, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Huyện Vũng Liêm', 18, 'Khung giờ 09:00:00 - Khu vực xa (+50,000đ) | Cuối tuần (+20,000đ)', '2025-12-28', 320000.00, 'cancelled', NULL, 'unpaid', NULL, NULL, NULL, '2025-12-28 07:45:53', NULL, NULL, NULL),
(79, 2, 3, 'Nguyễn Văn A', '0900000002', 'Phường 2', 'Thành phố Vĩnh Long', 28, 'Khung giờ 09:00:00 - Khu vực xa (+50,000đ) | Cuối tuần (+20,000đ)', '2025-12-28', 370000.00, 'completed', NULL, 'unpaid', NULL, NULL, NULL, '2025-12-28 08:33:33', 21.07981470, 105.81578940, NULL),
(80, 2, NULL, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Thành phố Vĩnh Long', 5, 'Khung giờ 09:00:00 - Khu vực xa (+50,000đ)', '2026-06-01', 550000.00, '', NULL, 'paid', NULL, NULL, NULL, '2026-06-01 08:08:38', 10.24785400, 105.96117700, NULL),
(81, 2, NULL, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Thành phố Vĩnh Long', 5, 'Khung giờ 11:00:00 - Khu vực xa (+50,000đ)', '2026-06-01', 550000.00, 'pending', NULL, 'unpaid', NULL, NULL, NULL, '2026-06-01 08:08:45', 10.24785400, 105.96117700, NULL),
(82, 2, 3, 'Nguyễn Văn A', '0900000002', 'Đà Nẵng', 'Thành phố Vĩnh Long', 34, 'Khung giờ 09:00:00 - Khu vực xa (+50,000đ)', '2026-06-01', 51000.00, 'confirmed', NULL, 'unpaid', NULL, NULL, NULL, '2026-06-01 08:25:26', 10.24785400, 105.96117700, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ward_code` varchar(6) NOT NULL,
  `name` varchar(255) NOT NULL,
  `province_code` varchar(2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_center` tinyint(1) DEFAULT 0 COMMENT '1: Nội thành/Trung tâm, 0: Ngoại thành'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`id`, `ward_code`, `name`, `province_code`, `created_at`, `updated_at`, `is_center`) VALUES
(1, '855', 'Thành phố Vĩnh Long', '86', '2025-12-06 04:00:28', '2025-12-06 04:00:28', 1),
(2, '857', 'Huyện Long Hồ', '86', '2025-12-06 04:00:28', '2025-12-06 04:00:28', 0),
(3, '858', 'Huyện Mang Thít', '86', '2025-12-06 04:00:28', '2025-12-06 04:00:28', 0),
(4, '859', 'Huyện Vũng Liêm', '86', '2025-12-06 04:00:28', '2025-12-06 04:00:28', 0),
(5, '860', 'Huyện Tam Bình', '86', '2025-12-06 04:00:28', '2025-12-06 04:00:28', 0),
(6, '861', 'Thị xã Bình Minh', '86', '2025-12-06 04:00:28', '2025-12-06 04:00:28', 0),
(7, '862', 'Huyện Trà Ôn', '86', '2025-12-06 04:00:28', '2025-12-06 04:00:28', 0),
(8, '863', 'Huyện Bình Tân', '86', '2025-12-06 04:00:28', '2025-12-06 04:00:28', 0);

-- --------------------------------------------------------

--
-- Table structure for table `equipments`
--

CREATE TABLE `equipments` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `img` varchar(200) DEFAULT NULL,
  `unit` varchar(100) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipments`
--

INSERT INTO `equipments` (`id`, `name`, `img`, `unit`, `price`, `quantity`, `description`) VALUES
(1, 'Laptop Dell', '1.png', 'cái', 15000000, 10, 'Máy tính xách tay Dell Inspiron'),
(2, 'Màn hình Samsung', '2.png', 'cái', 3500000, 20, 'Màn hình LCD 24 inch'),
(3, 'Bàn phím Logitech', '3.png', 'cái', 450000, 50, 'Bàn phím không dây Logitech K380');

-- --------------------------------------------------------

--
-- Table structure for table `forum_answers`
--

CREATE TABLE `forum_answers` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_answers`
--

INSERT INTO `forum_answers` (`id`, `question_id`, `user_id`, `user_name`, `content`, `created_at`) VALUES
(1, 2, 2, 'Nguyễn Văn A', 'mình thấy thợ làm việc ok có tâm á bạn', '2025-12-26 09:37:53');

-- --------------------------------------------------------

--
-- Table structure for table `forum_questions`
--

CREATE TABLE `forum_questions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_questions`
--

INSERT INTO `forum_questions` (`id`, `user_id`, `user_name`, `title`, `content`, `views`, `created_at`) VALUES
(1, 1, 'Nguyễn Văn A', 'Máy lạnh chảy nước ở cục lạnh là sao ạ?', 'Mọi người cho em hỏi máy nhà em mới mua 1 năm mà giờ chảy nước ròng ròng, có tự sửa được không?', 128, '2025-12-26 09:27:13'),
(2, 2, 'Trần Thị B', 'Xin review dịch vụ vệ sinh máy giặt?', 'Có ai thuê thợ TechFix vệ sinh chưa ạ? Cho em xin ít review với.', 94, '2025-12-26 09:27:13');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `customer_id`, `message`, `status`, `created_at`) VALUES
(4, 1, 'Đơn hàng #1 của bạn đã được xác nhận!', 'unread', '2025-11-01 17:39:40'),
(5, 2, 'Đơn hàng #13 của bạn đã được xác nhận!', 'read', '2025-11-01 18:07:58'),
(8, 6, 'Đơn hàng #15 của bạn đã được xác nhận!', 'read', '2025-11-02 15:22:25'),
(9, 6, 'Đơn hàng #15 của bạn đã được xác nhận!', 'read', '2025-11-02 15:22:53'),
(10, 6, 'Đơn hàng #15 của bạn đã được xác nhận!', 'read', '2025-11-02 15:26:08'),
(11, 6, 'Đơn hàng #15 đã hoàn thành. Cảm ơn bạn!', 'read', '2025-11-02 15:37:51'),
(12, 6, 'Đơn hàng #17 của bạn đã được xác nhận!', 'read', '2025-11-04 13:27:55'),
(13, 6, 'Đơn hàng #17 đã hoàn thành. Cảm ơn bạn!', 'read', '2025-11-04 13:28:51'),
(14, 6, 'Đơn hàng #32 của bạn đã được xác nhận!', 'read', '2025-11-19 22:58:18'),
(15, 6, 'Đơn hàng #32 đã hoàn thành. Cảm ơn bạn!', 'read', '2025-11-19 22:59:17'),
(16, 2, 'Đơn hàng #38 của bạn đã được xác nhận!', 'read', '2025-11-20 11:20:07'),
(17, 2, 'Đơn hàng #29 của bạn đã được xác nhận!', 'read', '2025-11-20 11:20:15'),
(18, 2, 'Đơn hàng #34 của bạn đã được xác nhận!', 'read', '2025-11-20 11:20:24'),
(19, 2, 'Đơn hàng #28 đã hoàn thành. Cảm ơn bạn!', 'read', '2025-11-20 11:21:33'),
(20, 2, 'Đơn hàng #29 đã hoàn thành. Cảm ơn bạn!', 'read', '2025-11-20 11:21:40'),
(21, 2, 'Đơn hàng #34 đã hoàn thành. Cảm ơn bạn!', 'read', '2025-11-20 11:21:48'),
(22, 2, 'Đơn hàng #38 đã hoàn thành. Cảm ơn bạn!', 'read', '2025-11-22 16:05:35'),
(23, 2, 'Đơn hàng #35 của bạn đã được xác nhận!', 'read', '2025-11-22 17:01:54'),
(24, 2, 'Đơn hàng #40 của bạn đã được xác nhận!', 'read', '2025-11-22 18:04:07'),
(25, 2, 'Đơn hàng #41 của bạn đã được xác nhận!', 'read', '2025-11-22 18:06:40'),
(26, 2, 'Đơn hàng #44 của bạn đã được xác nhận!', 'read', '2025-11-23 09:11:41'),
(27, 2, 'Đơn hàng #44 của bạn đã được xác nhận!', 'read', '2025-11-23 09:11:46'),
(28, 2, 'Đơn hàng #44 của bạn đã được xác nhận!', 'read', '2025-11-23 09:11:51'),
(29, 2, 'Đơn hàng #44 của bạn đã được xác nhận!', 'read', '2025-11-23 09:55:07'),
(30, 2, 'Đơn hàng #47 của bạn đã được xác nhận!', 'read', '2025-11-23 15:38:11'),
(31, 2, 'Đơn hàng #47 của bạn đã được xác nhận!', 'read', '2025-11-23 16:14:53'),
(32, 6, 'Đơn hàng #48 của bạn đã được xác nhận!', 'read', '2025-11-23 16:23:27'),
(33, 6, 'Đơn hàng #49 của bạn đã được xác nhận!', 'read', '2025-11-23 16:55:34'),
(34, 6, 'Đơn hàng #50 của bạn đã được xác nhận!', 'read', '2025-11-23 17:21:11'),
(35, 6, 'Đơn hàng #50 của bạn đã được xác nhận!', 'read', '2025-11-23 17:34:28'),
(36, 6, 'Đơn hàng #50 của bạn đã được xác nhận!', 'read', '2025-11-23 17:53:23'),
(37, 6, 'Đơn hàng #50 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-11-23 17:58:11'),
(38, 6, 'Đơn hàng #50 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-11-23 18:01:35'),
(39, 6, 'Đơn hàng #51 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-11-23 18:12:49'),
(40, 6, 'Đơn hàng #52 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-11-23 18:17:55'),
(41, 6, 'Đơn hàng #61 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-11-23 18:20:14'),
(42, 6, 'Đơn hàng #53 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-11-23 18:27:15'),
(43, 6, 'Đơn hàng #54 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-11-23 18:30:55'),
(44, 6, 'Đơn hàng #55 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-12-01 11:00:00'),
(45, 6, 'Đơn hàng #56 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-12-01 11:07:47'),
(46, 2, 'Đơn hàng #64 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-12-01 13:33:22'),
(47, 2, 'Đơn hàng #65 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-12-05 08:23:18'),
(48, 2, 'Đơn hàng #66 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-12-05 08:24:08'),
(49, 2, 'Đơn hàng #65 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-12-05 08:39:52'),
(50, 2, 'Đơn hàng #68 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-12-26 10:42:50'),
(51, 2, 'Đơn hàng #69 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-12-26 11:11:33'),
(52, 2, 'Đơn hàng #70 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-12-26 11:24:39'),
(53, 2, 'Đơn hàng #71 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-12-26 11:40:47'),
(54, 2, 'Đơn hàng #72 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-12-26 11:59:18'),
(55, 2, 'Đơn hàng #73 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-12-26 12:08:36'),
(56, 2, 'Đơn hàng #74 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-12-26 12:33:54'),
(57, 2, 'Đơn hàng #75 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-12-26 12:37:32'),
(58, 2, 'Đơn hàng #76 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-12-26 12:45:34'),
(59, 2, 'Đơn hàng #78 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-12-28 15:38:14'),
(60, 2, 'Đơn hàng #79 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2025-12-28 15:38:57'),
(61, 2, 'Đơn hàng #82 đã được kỹ thuật viên Trần Văn Bê tiếp nhận!', 'read', '2026-06-05 11:48:22');

-- --------------------------------------------------------

--
-- Table structure for table `orderequipments`
--

CREATE TABLE `orderequipments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `equipment_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderequipments`
--

INSERT INTO `orderequipments` (`id`, `order_id`, `equipment_id`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2025-10-28 15:13:33', '2025-10-28 15:13:33'),
(2, 1, 2, 1, '2025-10-28 15:13:33', '2025-10-28 15:13:33'),
(3, 18, 1, 1, '2025-10-29 05:24:02', '2025-10-29 05:24:02'),
(4, 19, 1, 1, '2025-10-29 05:24:12', '2025-10-29 05:24:12'),
(5, 20, 1, 1, '2025-10-29 05:24:13', '2025-10-29 05:24:13'),
(6, 21, 1, 1, '2025-10-29 05:24:13', '2025-10-29 05:24:13'),
(7, 22, 1, 1, '2025-10-29 05:24:13', '2025-10-29 05:24:13'),
(8, 23, 1, 1, '2025-10-29 05:24:14', '2025-10-29 05:24:14'),
(9, 24, 1, 1, '2025-10-29 05:24:16', '2025-10-29 05:24:16'),
(10, 25, 1, 1, '2025-10-29 05:24:17', '2025-10-29 05:24:17'),
(11, 26, 1, 1, '2025-10-29 05:24:17', '2025-10-29 05:24:17'),
(12, 27, 1, 1, '2025-10-29 05:24:17', '2025-10-29 05:24:17'),
(13, 28, 1, 1, '2025-10-29 05:24:24', '2025-10-29 05:24:24'),
(14, 29, 1, 1, '2025-10-29 05:24:24', '2025-10-29 05:24:24'),
(15, 30, 1, 1, '2025-10-29 05:24:25', '2025-10-29 05:24:25'),
(16, 31, 1, 1, '2025-10-29 05:24:25', '2025-10-29 05:24:25'),
(17, 32, 1, 1, '2025-10-29 05:24:25', '2025-10-29 05:24:25'),
(18, 33, 1, 1, '2025-10-29 05:24:25', '2025-10-29 05:24:25'),
(19, 34, 1, 1, '2025-10-29 05:24:25', '2025-10-29 05:24:25');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `technician_id` int(11) DEFAULT NULL,
  `schedule_time` datetime NOT NULL,
  `status` enum('đang chờ','đã hoàn thành','đã hủy') DEFAULT 'đang chờ',
  `total_price` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `service_id`, `technician_id`, `schedule_time`, `status`, `total_price`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 3, '2025-10-28 09:00:00', '', 15300000, '2025-10-28 15:13:33', '2025-10-28 15:19:57'),
(18, 2, 1, 3, '0000-00-00 00:00:00', '', 15150000, '2025-10-29 05:24:02', '2025-10-29 05:24:02'),
(19, 2, 1, 3, '2004-12-21 00:00:00', '', 15150000, '2025-10-29 05:24:12', '2025-10-29 05:24:12'),
(20, 2, 1, 3, '2004-12-21 00:00:00', '', 15150000, '2025-10-29 05:24:13', '2025-10-29 05:24:13'),
(21, 2, 1, 3, '2004-12-21 00:00:00', '', 15150000, '2025-10-29 05:24:13', '2025-10-29 05:24:13'),
(22, 2, 1, 3, '2004-12-21 00:00:00', '', 15150000, '2025-10-29 05:24:13', '2025-10-29 05:24:13'),
(23, 2, 1, 3, '2004-12-21 00:00:00', '', 15150000, '2025-10-29 05:24:14', '2025-10-29 09:18:05'),
(24, 2, 1, 3, '2004-12-21 00:00:00', '', 15150000, '2025-10-29 05:24:16', '2025-10-29 05:24:16'),
(25, 2, 1, 3, '2004-12-21 00:00:00', '', 15150000, '2025-10-29 05:24:17', '2025-10-29 05:24:17'),
(26, 2, 1, 3, '2004-12-21 00:00:00', '', 15150000, '2025-10-29 05:24:17', '2025-10-29 05:24:17'),
(27, 2, 1, 3, '2004-12-21 00:00:00', '', 15150000, '2025-10-29 05:24:17', '2025-10-29 05:24:17'),
(28, 2, 1, 3, '2004-12-21 00:00:00', '', 15150000, '2025-10-29 05:24:24', '2025-10-29 05:24:24'),
(29, 2, 1, 3, '2004-12-21 00:00:00', '', 15150000, '2025-10-29 05:24:24', '2025-10-29 05:24:24'),
(30, 2, 1, 3, '2004-12-21 00:00:00', '', 15150000, '2025-10-29 05:24:25', '2025-10-29 05:24:25'),
(31, 2, 1, 3, '2004-12-21 00:00:00', '', 15150000, '2025-10-29 05:24:25', '2025-10-29 05:24:25'),
(32, 2, 1, 3, '2004-12-21 00:00:00', '', 15150000, '2025-10-29 05:24:25', '2025-10-29 05:24:25'),
(33, 2, 1, 3, '2004-12-21 00:00:00', '', 15150000, '2025-10-29 05:24:25', '2025-10-29 05:24:25'),
(34, 2, 1, 3, '2004-12-21 00:00:00', '', 15150000, '2025-10-29 05:24:25', '2025-10-29 05:24:25');

-- --------------------------------------------------------

--
-- Table structure for table `provinces`
--

CREATE TABLE `provinces` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `province_code` varchar(2) NOT NULL,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `code` varchar(5) NOT NULL,
  `place_type` varchar(255) NOT NULL,
  `country` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `provinces`
--

INSERT INTO `provinces` (`id`, `province_code`, `name`, `short_name`, `code`, `place_type`, `country`, `created_at`, `updated_at`) VALUES
(1, '86', 'Tỉnh Vĩnh Long', 'Vĩnh Long', '86', 'Tỉnh', 'VN', '2025-12-06 03:59:51', '2025-12-06 03:59:51');

-- --------------------------------------------------------

--
-- Table structure for table `repairs`
--

CREATE TABLE `repairs` (
  `id` int(11) NOT NULL,
  `device` varchar(255) NOT NULL,
  `problem` text DEFAULT NULL,
  `status` enum('đang chờ xử lý','đang xử lý','đã hoàn thành') DEFAULT 'đang xử lý',
  `customer_id` int(11) DEFAULT NULL,
  `technician_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `booking_id`, `customer_id`, `rating`, `image`, `comment`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 5, NULL, 'Kỹ thuật viên làm việc rất tốt, sạch sẽ, đúng giờ.', '2025-10-28 15:13:33', '2025-10-28 15:13:33'),
(3, 26, 2, 5, NULL, 'tuyệt vời', '2025-11-19 14:47:57', '2025-11-19 14:47:57'),
(4, 66, 2, 1, NULL, '7uyy', '2025-12-05 01:35:47', '2025-12-05 01:35:47'),
(5, 75, 2, 1, NULL, 'tuyệt', '2025-12-26 05:44:25', '2025-12-26 05:44:25'),
(6, 76, 2, 5, 'review_76_1766730651.jpeg', 'tuyệt vời', '2025-12-26 06:30:51', '2025-12-26 06:30:51'),
(7, 77, 2, 5, NULL, 'ssgf', '2025-12-28 08:36:11', '2025-12-28 08:36:11');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `image` varchar(255) DEFAULT NULL,
  `group_name` varchar(100) DEFAULT 'Khác'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `price`, `unit`, `created_at`, `updated_at`, `image`, `group_name`) VALUES
(1, 'Sửa chữa & bảo trì hệ thống điện', 'Khắc phục sự cố chập điện, mất điện, thay công tắc, đi lại đường dây.', 200000, 'bộ', '2025-11-20 03:45:43', '2025-12-28 07:32:04', '/TechFixPHP/assets/image/fixelec.jpg', 'Điện – Điện tử'),
(2, 'Lắp đặt & thay thế đèn, ổ cắm', 'Thay thế đèn LED, đèn trang trí, ổ cắm, công tắc an toàn & thẩm mỹ.', 150000, 'cái', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/lapden.jpg', 'Điện – Điện tử'),
(3, 'Lắp đặt & bảo trì điều hòa', 'Vệ sinh, bơm ga, lắp đặt và bảo dưỡng điều hòa định kỳ.', 250000, 'máy', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/air.jpg', 'Điện – Điện tử'),
(4, 'Kiểm tra & đánh giá an toàn điện', 'Đo kiểm tải điện, phát hiện nguy cơ chập cháy, lập báo cáo an toàn.', 300000, 'lần', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/ktraelec.jpg', 'Điện – Điện tử'),
(5, 'Thi công chiếu sáng thông minh', 'Lắp đặt hệ thống chiếu sáng IoT điều khiển qua điện thoại.', 500000, 'gói', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/denTM.jpg', 'Điện – Điện tử'),
(6, 'Sửa chữa hệ thống nước', 'Xử lý rò rỉ đường ống, thay vòi nước, van khóa, thông tắc.', 200000, 'lần', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/plumbing.jpg', 'Nước – Môi trường'),
(7, 'Lắp đặt & sửa máy bơm nước', 'Lắp đặt mới hoặc sửa chữa máy bơm không lên nước, kêu to.', 250000, 'máy', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/waterpump.jpg', 'Nước – Môi trường'),
(8, 'Chống thấm tường, sàn, mái', 'Xử lý thấm dột trần nhà, tường, nhà vệ sinh chuyên nghiệp.', 400000, 'm2', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/constructionn.jpg', 'Nước – Môi trường'),
(9, 'Vệ sinh bể chứa, bồn nước', 'Thau rửa bể nước ngầm, bồn inox, đảm bảo nguồn nước sạch.', 350000, 'bồn', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/watertank.jpg', 'Nước – Môi trường'),
(10, 'Lắp đặt hệ thống lọc nước', 'Tư vấn và lắp đặt hệ thống lọc nước sinh hoạt, thay lõi lọc.', 300000, 'lần', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/waterfilter.jpg', 'Nước – Môi trường'),
(11, 'Sửa chữa tủ lạnh', 'Khắc phục lỗi không lạnh, đóng tuyết, hỏng block, thay ron cửa.', 250000, 'máy', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/fridge.jpg', 'Thiết bị gia dụng'),
(12, 'Sửa chữa máy giặt', 'Sửa lỗi không vắt, không cấp nước, kêu to, rung lắc mạnh.', 250000, 'máy', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/washingmachine.jpg', 'Thiết bị gia dụng'),
(13, 'Sửa chữa bếp từ, lò vi sóng', 'Sửa lỗi không nóng, mất nguồn, liệt phím cảm ứng.', 200000, 'cái', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/kitchen.jpg', 'Thiết bị gia dụng'),
(14, 'Vệ sinh thiết bị gia dụng', 'Vệ sinh tổng hợp quạt hơi nước, máy hút mùi, lò nướng.', 150000, 'máy', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/appliances.jpg', 'Thiết bị gia dụng'),
(15, 'Hỗ trợ kỹ thuật IT & Phần mềm', 'Cài đặt Office, diệt virus, cứu dữ liệu, sửa lỗi phần mềm từ xa.', 150000, 'lần', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/computer.jpg', 'CNTT – Viễn thông'),
(16, 'Sửa chữa Laptop & PC', 'Thay màn hình, bàn phím, nâng cấp SSD/RAM, sửa nguồn.', 200000, 'máy', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/laptop.jpg', 'CNTT – Viễn thông'),
(17, 'Cài đặt hệ điều hành (Win/Mac)', 'Cài mới Windows, MacOS, Linux và driver đầy đủ.', 150000, 'máy', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/os.jpg', 'CNTT – Viễn thông'),
(18, 'Lắp đặt & cấu hình WiFi', 'Thi công mạng LAN, lắp Router, kích sóng WiFi cho nhà tầng.', 250000, 'điểm', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/network.jpg', 'CNTT – Viễn thông'),
(19, 'Lắp đặt Camera giám sát', 'Lắp đặt trọn gói camera an ninh, cài đặt xem qua điện thoại.', 300000, 'cam', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/camera.jpg', 'CNTT – Viễn thông'),
(20, 'Dịch vụ An ninh mạng', 'Bảo mật hệ thống mạng gia đình/văn phòng, backup dữ liệu.', 500000, 'gói', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/security.jpg', 'CNTT – Viễn thông'),
(21, 'Kiểm định hệ thống PCCC', 'Kiểm tra bình chữa cháy, hệ thống báo khói, báo nhiệt.', 1000000, 'lần', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/fire.jpg', 'An toàn – Kiểm định'),
(22, 'Đánh giá chất lượng nước', 'Lấy mẫu và phân tích các chỉ số an toàn của nguồn nước.', 500000, 'mẫu', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/waterquality.jpg', 'An toàn – Kiểm định'),
(23, 'Kiểm định thiết bị công suất lớn', 'Kiểm tra máy phát điện, thang máy, máy công nghiệp.', 800000, 'máy', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/factory.jpg', 'An toàn – Kiểm định'),
(24, 'Bảo dưỡng hệ thống định kỳ', 'Gói bảo trì định kỳ điện - nước - lạnh cho gia đình/văn phòng.', 400000, 'tháng', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/maintenance.jpg', 'Bảo trì – Quản lý thiết bị'),
(25, 'Cung cấp vật tư tiêu hao', 'Thay bóng đèn, lõi lọc nước, pin khóa cửa định kỳ.', 200000, 'lần', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/tools.jpg', 'Bảo trì – Quản lý thiết bị'),
(26, 'Nâng cấp hệ thống kỹ thuật', 'Tư vấn và nâng cấp hệ thống điện nước cũ lên chuẩn mới.', 500000, 'gói', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/upgrade.jpg', 'Bảo trì – Quản lý thiết bị'),
(27, 'Vệ sinh công nghiệp', 'Vệ sinh nhà sau xây dựng, giặt thảm, ghế sofa, rèm cửa.', 200000, 'm2', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/cleaning.jpg', 'Dịch vụ đặc biệt'),
(28, 'Khử trùng & Diệt côn trùng', 'Phun thuốc muỗi, diệt mối, khử khuẩn không gian sống.', 300000, 'lần', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/sanitation.jpg', 'Dịch vụ đặc biệt'),
(29, 'Dịch vụ Cứu hộ 24/7', 'Sửa điện nước, mở khóa cửa khẩn cấp ban đêm.', 500000, 'lần', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/emergency.jpg', 'Dịch vụ đặc biệt'),
(30, 'Dịch vụ VIP cho Doanh nghiệp', 'Bảo trì trọn gói ưu tiên, SLA phản hồi nhanh trong 1h.', 1000000, 'tháng', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/vip.jpg', 'Dịch vụ đặc biệt'),
(31, 'Hỗ trợ kỹ thuật ngoài giờ', 'Dịch vụ hỗ trợ ban đêm, cuối tuần, ngày lễ.', 400000, 'lần', '2025-11-20 03:45:43', '2025-11-20 03:45:43', '/TechFixPHP/assets/image/support.jpg', 'Dịch vụ đặc biệt'),
(32, 'Kéo cắt', 'test', 1000, 'bộ', '2025-11-23 09:21:15', '2025-11-23 09:21:15', NULL, 'Khác'),
(34, 'Mua đồ hộ', '', 1000, 'lần', '2026-06-01 08:24:03', '2026-06-01 08:24:03', NULL, 'Khác');

-- --------------------------------------------------------

--
-- Table structure for table `service_keywords`
--

CREATE TABLE `service_keywords` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `keyword` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_keywords`
--

INSERT INTO `service_keywords` (`id`, `service_id`, `keyword`) VALUES
(1, 1, 'mất điện'),
(2, 1, 'chập điện'),
(3, 1, 'sửa điện'),
(4, 1, 'nhảy aptomat'),
(5, 1, 'đứt dây điện'),
(6, 2, 'thay bóng đèn'),
(7, 2, 'lắp đèn led'),
(8, 2, 'hỏng công tắc'),
(9, 2, 'cháy bóng'),
(10, 2, 'ổ cắm lỏng'),
(11, 3, 'vệ sinh máy lạnh'),
(12, 3, 'sửa điều hòa'),
(13, 3, 'máy lạnh không lạnh'),
(14, 3, 'bơm ga máy lạnh'),
(15, 3, 'chảy nước máy lạnh'),
(16, 4, 'kiểm tra điện'),
(17, 4, 'an toàn điện'),
(18, 4, 'đo điện'),
(19, 4, 'sự cố điện'),
(20, 5, 'nhà thông minh'),
(21, 5, 'điều khiển từ xa'),
(22, 5, 'đèn tự động'),
(23, 5, 'smarthome'),
(24, 6, 'vỡ ống nước'),
(25, 6, 'rò rỉ nước'),
(26, 6, 'sửa ống nước'),
(27, 6, 'tắc cống'),
(28, 6, 'thay vòi nước'),
(29, 7, 'máy bơm hỏng'),
(30, 7, 'máy bơm không lên nước'),
(31, 7, 'sửa máy bơm'),
(32, 7, 'lắp máy bơm'),
(33, 8, 'chống thấm'),
(34, 8, 'dột mái tôn'),
(35, 8, 'thấm tường'),
(36, 8, 'nứt trần'),
(37, 9, 'rửa bể nước'),
(38, 9, 'vệ sinh bồn inox'),
(39, 9, 'bể nước bẩn'),
(40, 9, 'thau bể'),
(41, 10, 'lọc nước'),
(42, 10, 'máy lọc nước'),
(43, 10, 'thay lõi lọc'),
(44, 10, 'nước sạch'),
(45, 11, 'tủ lạnh không lạnh'),
(46, 11, 'sửa tủ lạnh'),
(47, 11, 'đóng tuyết'),
(48, 11, 'thay ron tủ lạnh'),
(49, 12, 'máy giặt không vắt'),
(50, 12, 'sửa máy giặt'),
(51, 12, 'máy giặt kêu to'),
(52, 12, 'vệ sinh máy giặt'),
(53, 13, 'bếp từ hỏng'),
(54, 13, 'lò vi sóng không nóng'),
(55, 13, 'sửa bếp từ'),
(56, 13, 'mất nguồn'),
(57, 14, 'vệ sinh quạt'),
(58, 14, 'bảo dưỡng thiết bị'),
(59, 14, 'lò nướng'),
(60, 14, 'máy hút mùi'),
(61, 15, 'cài win'),
(62, 15, 'diệt virus'),
(63, 15, 'sửa lỗi phần mềm'),
(64, 15, 'cứu dữ liệu'),
(65, 15, 'it helpdesk'),
(66, 16, 'sửa laptop'),
(67, 16, 'sửa máy tính'),
(68, 16, 'thay màn hình'),
(69, 16, 'nâng cấp ram'),
(70, 16, 'thay pin laptop'),
(71, 17, 'cài macos'),
(72, 17, 'cài linux'),
(73, 17, 'cài driver'),
(74, 17, 'hệ điều hành'),
(75, 18, 'lắp wifi'),
(76, 18, 'mạng chậm'),
(77, 18, 'kích sóng wifi'),
(78, 18, 'cấu hình router'),
(79, 18, 'mạng lan'),
(80, 19, 'lắp camera'),
(81, 19, 'camera giám sát'),
(82, 19, 'camera an ninh'),
(83, 19, 'xem camera qua điện thoại'),
(84, 20, 'bảo mật mạng'),
(85, 20, 'an ninh mạng'),
(86, 20, 'chống hack'),
(87, 20, 'tường lửa'),
(88, 21, 'kiểm tra pccc'),
(89, 21, 'bình chữa cháy'),
(90, 21, 'báo cháy'),
(91, 21, 'phòng cháy'),
(92, 22, 'xét nghiệm nước'),
(93, 22, 'nước nhiễm phèn'),
(94, 22, 'chất lượng nước'),
(95, 23, 'máy phát điện'),
(96, 23, 'bảo trì máy công nghiệp'),
(97, 23, 'thang máy'),
(98, 24, 'bảo trì định kỳ'),
(99, 24, 'bảo dưỡng trọn gói'),
(100, 24, 'kiểm tra định kỳ'),
(101, 25, 'thay vật tư'),
(102, 25, 'mua bóng đèn'),
(103, 25, 'vật tư tiêu hao'),
(104, 26, 'cải tạo điện nước'),
(105, 26, 'nâng cấp hệ thống'),
(106, 26, 'sửa nhà'),
(107, 27, 'dọn nhà'),
(108, 27, 'vệ sinh công nghiệp'),
(109, 27, 'giặt sofa'),
(110, 27, 'giặt rèm'),
(111, 28, 'diệt mối'),
(112, 28, 'phun thuốc muỗi'),
(113, 28, 'khử trùng'),
(114, 28, 'diệt côn trùng'),
(115, 29, 'sửa điện đêm'),
(116, 29, 'cứu hộ điện nước'),
(117, 29, 'khẩn cấp'),
(118, 29, 'mở khóa cửa'),
(119, 30, 'dịch vụ vip'),
(120, 30, 'bảo trì doanh nghiệp'),
(121, 30, 'khách hàng doanh nghiệp'),
(122, 31, 'làm ngoài giờ'),
(123, 31, 'sửa chữa cuối tuần'),
(124, 31, 'hỗ trợ ban đêm');

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL,
  `action` varchar(255) NOT NULL,
  `target_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_logs`
--

INSERT INTO `system_logs` (`id`, `user_id`, `user_name`, `role`, `action`, `target_id`, `ip_address`, `created_at`) VALUES
(1, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-11-23 08:20:24'),
(2, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-11-23 08:20:39'),
(3, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-11-23 08:20:44'),
(4, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-11-23 08:20:44'),
(5, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-11-23 08:21:02'),
(6, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-11-23 08:21:08'),
(7, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-11-23 08:21:12'),
(8, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-11-23 08:21:12'),
(9, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 08:24:00'),
(10, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 08:24:04'),
(11, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 08:24:55'),
(12, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 08:24:58'),
(13, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 08:38:01'),
(14, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 08:38:05'),
(15, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 09:18:05'),
(16, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 09:18:08'),
(17, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 09:20:47'),
(18, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 09:20:51'),
(19, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 09:23:17'),
(20, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 09:23:21'),
(21, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 09:25:31'),
(22, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 09:25:37'),
(23, 5, 'kyan123', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 09:26:06'),
(24, 5, 'kyan123', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 09:26:16'),
(25, 5, 'kyan123', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 09:26:53'),
(26, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 09:33:05'),
(27, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 09:33:09'),
(28, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 09:55:20'),
(29, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 09:55:24'),
(30, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 10:21:00'),
(31, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 10:21:04'),
(32, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 11:12:37'),
(33, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 11:12:40'),
(34, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 11:17:41'),
(35, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 11:17:45'),
(36, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 11:20:00'),
(37, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 11:20:04'),
(38, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 11:30:39'),
(39, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-11-23 11:30:43'),
(40, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-01 04:06:07'),
(41, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-01 04:06:10'),
(42, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-01 04:07:19'),
(43, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-01 04:07:25'),
(44, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-12-01 04:08:04'),
(45, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-12-01 04:08:27'),
(46, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-12-01 04:08:29'),
(47, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-12-01 04:08:30'),
(48, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-12-01 04:08:32'),
(49, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-01 06:33:05'),
(50, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-01 06:33:08'),
(51, 1, 'Admin213', 'admin', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-03 09:56:04'),
(52, 1, 'Admin213', 'admin', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-03 09:56:06'),
(53, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:22:56'),
(54, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:23:01'),
(55, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:26:17'),
(56, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:26:18'),
(57, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:26:19'),
(58, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:26:25'),
(59, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:26:27'),
(60, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-12-05 01:28:18'),
(61, 1, 'Admin213', 'admin', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:29:38'),
(62, 1, 'Admin213', 'admin', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:30:09'),
(63, 1, 'Admin213', 'admin', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:30:12'),
(64, 1, 'Admin213', 'admin', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:30:13'),
(65, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:30:21'),
(66, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:30:55'),
(67, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:32:15'),
(68, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:32:16'),
(69, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:32:17'),
(70, 5, 'kyan123', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:33:08'),
(71, 5, 'kyan123', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:33:12'),
(72, 5, 'kyan123', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:33:17'),
(73, 1, 'Admin213', 'admin', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:34:42'),
(74, 1, 'Admin213', 'admin', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:34:45'),
(75, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:35:01'),
(76, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:35:06'),
(77, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:37:02'),
(78, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:37:08'),
(79, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:37:38'),
(80, 5, 'kyan123', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:39:27'),
(81, 5, 'kyan123', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:39:29'),
(82, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:39:31'),
(83, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:39:35'),
(84, 1, 'Admin213', 'admin', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:44:04'),
(85, 1, 'Admin213', 'admin', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:44:10'),
(86, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:45:32'),
(87, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-05 01:45:38'),
(88, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-06 04:00:51'),
(89, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-06 04:00:54'),
(90, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:23:37'),
(91, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:23:41'),
(92, 1, 'Admin213', 'admin', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:24:24'),
(93, 1, 'Admin213', 'admin', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:24:26'),
(94, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:28:30'),
(95, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:28:31'),
(96, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:42:05'),
(97, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:42:07'),
(98, 1, 'Admin213', 'admin', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:42:17'),
(99, 1, 'Admin213', 'admin', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:42:20'),
(100, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:42:38'),
(101, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:42:40'),
(102, 1, 'Admin213', 'admin', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:42:53'),
(103, 1, 'Admin213', 'admin', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:42:56'),
(104, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:43:11'),
(105, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:43:13'),
(106, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:44:24'),
(107, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:44:29'),
(108, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:47:13'),
(109, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:47:15'),
(110, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:48:38'),
(111, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:48:40'),
(112, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:49:27'),
(113, 3, 'Trần Văn Bê', 'technical', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 03:49:31'),
(114, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:11:20'),
(115, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:11:22'),
(116, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:12:21'),
(117, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:12:25'),
(118, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:24:27'),
(119, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:24:30'),
(120, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:25:23'),
(121, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:25:28'),
(122, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:39:10'),
(123, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:39:14'),
(124, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:40:27'),
(125, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:40:30'),
(126, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:41:18'),
(127, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:41:23'),
(128, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:42:53'),
(129, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:42:55'),
(130, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:57:28'),
(131, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:58:10'),
(132, 6, 'Naykei', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:58:14'),
(133, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:58:52'),
(134, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:58:56'),
(135, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:59:04'),
(136, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 04:59:07'),
(137, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:00:42'),
(138, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:00:46'),
(139, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:07:06'),
(140, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:07:10'),
(141, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:08:23'),
(142, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:08:25'),
(143, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:10:38'),
(144, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:10:40'),
(145, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:33:05'),
(146, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:33:41'),
(147, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:33:43'),
(148, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:33:45'),
(149, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:35:18'),
(150, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:35:20'),
(151, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:37:20'),
(152, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:37:23'),
(153, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:38:10'),
(154, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:38:12'),
(155, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:38:44'),
(156, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:38:46'),
(157, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:39:24'),
(158, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:39:26'),
(159, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-12-26 05:39:47'),
(160, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-12-26 05:40:00'),
(161, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-12-26 05:40:16'),
(162, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:45:07'),
(163, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:45:11'),
(164, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:45:58'),
(165, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:46:01'),
(166, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:49:25'),
(167, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 05:49:27'),
(168, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-12-26 05:49:45'),
(169, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 06:45:44'),
(170, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 06:48:45'),
(171, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 06:48:48'),
(172, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 06:48:49'),
(173, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 06:48:50'),
(174, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 06:48:51'),
(175, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 06:48:51'),
(176, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 06:48:52'),
(177, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 06:50:27'),
(178, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 06:50:28'),
(179, 2, 'Nguyễn Văn A', 'customer', 'Đăng nhập vào hệ thống', 0, '::1', '2025-12-26 06:50:35'),
(180, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-12-26 08:12:53'),
(181, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-12-26 08:13:13'),
(182, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-12-26 08:13:20'),
(183, 1, 'Admin213', 'admin', 'Xóa vĩnh viễn đơn hàng', NULL, '::1', '2025-12-28 08:39:55');

-- --------------------------------------------------------

--
-- Table structure for table `technician_schedule`
--

CREATE TABLE `technician_schedule` (
  `id` int(11) NOT NULL,
  `technician_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` enum('available','busy','off') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `technician_schedule`
--

INSERT INTO `technician_schedule` (`id`, `technician_id`, `date`, `start_time`, `end_time`, `status`, `created_at`) VALUES
(4, 3, '2025-11-02', '08:00:00', '12:00:00', '', '2025-11-01 02:43:46'),
(5, 3, '2025-11-02', '13:00:00', '17:00:00', '', '2025-11-01 02:43:46'),
(6, 3, '2025-11-03', '08:00:00', '12:00:00', '', '2025-11-01 02:43:46'),
(7, 3, '2025-11-02', '08:00:00', '12:00:00', '', '2025-11-01 02:45:58'),
(8, 3, '2025-11-02', '13:00:00', '17:00:00', '', '2025-11-01 02:45:58');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `face_descriptor` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `role` enum('admin','customer','technical') DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_token_hash` varchar(64) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `phone`, `email`, `password`, `google_id`, `face_descriptor`, `avatar`, `address`, `role`, `created_at`, `updated_at`, `reset_token_hash`, `reset_token_expires_at`) VALUES
(1, 'Admin213', '0900000001', NULL, 'admin123', NULL, NULL, 'assets/upload/1761709078_2fb95908-edd0-49bb-a2ea-aab74c26e3bf.png', 'Hà Nội', 'admin', '2025-10-28 15:13:33', '2025-10-29 03:44:12', NULL, NULL),
(2, 'Nguyễn Văn A', '0900000002', '22004073@st.vlute.edu.vn', '$2y$10$iiEevdl5uWBoDnswFNiEMOdItVdqvz4p1CEhDr6T3NqA6PN/wUTuq', '109223338281646643565', NULL, 'assets/upload/avatar_2_1766734673.jpg', 'Đà Nẵng', 'customer', '2025-10-28 15:13:33', '2025-12-26 09:28:35', NULL, NULL),
(3, 'Trần Văn Bê', '0900000003', '', 'tech123', NULL, NULL, NULL, 'Hồ Chí Minh', 'technical', '2025-10-28 15:13:33', '2025-11-22 09:52:49', NULL, NULL),
(4, 'kyan123', '0986327883', NULL, '$2y$10$tnsnk35Yosj0Flz6lVvVqOyjYFTeMQfC2wB7D8pXvjFPKYCoxw4gS', NULL, '[-0.08513624966144562,0.04996180534362793,0.023579740896821022,-0.10597727447748184,-0.11101862043142319,-0.001214080024510622,-0.05925804376602173,-0.09166013449430466,0.12983781099319458,-0.070644810795784,0.19473040103912354,0.0038242237642407417,-0.27074378728866577,-0.02193320356309414,0.01388473529368639,0.10322128236293793,-0.1495049148797989,-0.1304326355457306,-0.18964409828186035,-0.12014880776405334,-0.04043307527899742,-0.01309508178383112,-0.022392921149730682,0.0014879400841891766,-0.19781394302845,-0.17379088699817657,-0.07612632215023041,-0.15684284269809723,0.047475218772888184,-0.1133868619799614,0.02065994031727314,0.03627859055995941,-0.15335534512996674,-0.0234785508364439,0.019823772832751274,0.05383017659187317,-0.047378744930028915,-0.10163304954767227,0.15445972979068756,-0.06300171464681625,-0.13081969320774078,-0.017023589462041855,0.01127245556563139,0.18705059587955475,0.2095591276884079,0.008899319916963577,0.06008921191096306,-0.06716417521238327,0.06451897323131561,-0.2768872082233429,0.044564247131347656,0.13353247940540314,0.01591680385172367,0.11922795325517654,0.05201331898570061,-0.17637960612773895,0.05476925149559975,0.14977848529815674,-0.15846490859985352,0.06889446079730988,0.07302172482013702,-0.12633471190929413,-0.0851280465722084,-0.06580254435539246,0.22272276878356934,0.10133422911167145,-0.1190078854560852,-0.16132573783397675,0.20116496086120605,-0.1395217329263687,-0.06088506430387497,0.07669375836849213,-0.1329667866230011,-0.1445455551147461,-0.22278329730033875,0.07407121360301971,0.42080187797546387,0.14634770154953003,-0.13542646169662476,0.049985550343990326,-0.11448139697313309,-0.060128066688776016,-0.015736842527985573,0.0031543546356260777,-0.04606200382113457,-0.10160057991743088,-0.04393003135919571,0.06040051579475403,0.26230505108833313,-0.07273987680673599,0.06362710893154144,0.1762406826019287,0.0028688583988696337,-0.07214901596307755,-0.00868624821305275,0.01358184777200222,-0.07167181372642517,-0.0026797433383762836,-0.01456017792224884,0.032905396074056625,0.07383231818675995,-0.1301647424697876,0.024186313152313232,0.09474789351224899,-0.13530942797660828,0.1575576812028885,-0.008373137563467026,-0.04448789358139038,-0.0016963978996500373,-0.03012937121093273,-0.05414966866374016,-0.04093864560127258,0.23683489859104156,-0.2506933808326721,0.23676517605781555,0.21501527726650238,0.09991268813610077,0.10007849335670471,0.02947498857975006,0.11370264738798141,0.013911571353673935,0.040363144129514694,-0.18245083093643188,-0.08442404121160507,0.02723812311887741,-0.03492109104990959,-0.023656262084841728,0.06641318649053574]', NULL, 'Hậu Giang', 'customer', '2025-11-02 06:31:38', '2025-11-02 06:31:38', NULL, NULL),
(5, 'kyan123', '0986327833', NULL, '$2y$10$KgqJFN3ybRdHsFQqalydnuKo0LBr2bcxuDSA7Mtjw8WcpJA5oVwjy', NULL, '[-0.11322492361068726,0.1266099065542221,0.0844174474477768,0.03997572883963585,-0.05270844325423241,-0.07504859566688538,-0.001282377983443439,-0.13967902958393097,0.15018773078918457,-0.0795489177107811,0.2864815592765808,-0.06235950067639351,-0.21017025411128998,-0.11593882739543915,0.020433025434613228,0.17288492619991302,-0.15881529450416565,-0.13319918513298035,-0.08836709707975388,0.015231281518936157,0.05344301462173462,-0.03412563353776932,0.020184287801384926,0.07581468671560287,-0.0035508046858012676,-0.36366087198257446,-0.0843118205666542,-0.09076549112796783,0.06078960373997688,-0.08111894130706787,-0.04766641557216644,0.025230003520846367,-0.17135947942733765,-0.08936421573162079,-0.016681114211678505,0.026259729638695717,-0.03346126899123192,-0.016684647649526596,0.24378684163093567,-0.05245640128850937,-0.24636003375053406,-0.010527134872972965,-0.009341620840132236,0.23658131062984467,0.2105056494474411,0.05103554576635361,-0.0033202257473021746,-0.12339700758457184,0.028997326269745827,-0.20460118353366852,0.04233211278915405,0.18097904324531555,0.05586759001016617,0.0618978887796402,-0.06889218837022781,-0.1243354082107544,-0.03783503547310829,0.013674736022949219,-0.175332173705101,0.018763437867164612,0.08926376700401306,-0.15809211134910583,-0.07591234147548676,-0.04582510143518448,0.22857967019081116,0.09378021210432053,-0.10781527310609818,-0.1701282113790512,0.11608437448740005,-0.21782168745994568,-0.07583242654800415,0.05416787415742874,-0.1306730955839157,-0.1775088608264923,-0.3400779068470001,0.034269146621227264,0.44286689162254333,0.04282842576503754,-0.21001239120960236,0.031884901225566864,-0.0969352275133133,-0.0634274110198021,0.09172188490629196,0.16114474833011627,-0.020190052688121796,0.008015524595975876,-0.08606631308794022,0.008503674529492855,0.1920192688703537,-0.06584538519382477,-0.025483539327979088,0.2354772686958313,-0.009417615830898285,0.05988244339823723,0.04978499189019203,0.053215142339468,0.0423111617565155,-0.00898082833737135,-0.14325858652591705,-0.009509372524917126,0.034896787256002426,-0.042449165135622025,-0.003493756288662553,0.06890275329351425,-0.16980378329753876,0.13253876566886902,0.03651033341884613,0.029790954664349556,0.06418392062187195,-0.014380591921508312,-0.12504222989082336,-0.12777309119701385,0.1921636313199997,-0.17485909163951874,0.21297985315322876,0.19236993789672852,0.08431852608919144,0.11379572004079819,0.08976535499095917,0.1322367936372757,-0.011101188138127327,-0.024398867040872574,-0.17971262335777283,0.020884215831756592,0.09336363524198532,-3.783704596571624e-5,0.06788083910942078,0.02093096449971199]', NULL, 'Hậu Giang', 'customer', '2025-11-02 06:37:51', '2025-11-02 06:37:51', NULL, NULL),
(6, 'Kỳ Ân Học Code', '0388074313', 'funnyofficials@gmail.com', '$2y$10$yQnaSwAoe1HHCoOARqXQl.4wrA/tXzUnZlJQa9yDUwB6GT9OTkhui', '103461709658130785001', '[-0.08024952560663223,0.1386886090040207,0.11138959974050522,0.041747208684682846,-0.018329937011003494,-0.08456601947546005,0.007732834201306105,-0.14728252589702606,0.12038874626159668,-0.05854262411594391,0.3008788526058197,-0.06579337269067764,-0.21581026911735535,-0.09918869286775589,-0.009630728513002396,0.20576496422290802,-0.182212695479393,-0.12259063869714737,-0.08318675309419632,0.027777807787060738,0.07568206638097763,-0.019961047917604446,0.04454030096530914,0.03922668844461441,-0.0033529256470501423,-0.32781559228897095,-0.07452642172574997,-0.0692196786403656,0.08285100013017654,-0.09234905987977982,-0.07958842068910599,0.04496365785598755,-0.16484969854354858,-0.046453122049570084,0.02586825005710125,0.03133751451969147,-0.04245849326252937,-0.04022890329360962,0.22725139558315277,-0.07452318072319031,-0.2522875666618347,0.0177406407892704,0.027786042541265488,0.21978777647018433,0.2473645657300949,0.04534689709544182,-0.016104167327284813,-0.16385121643543243,0.035717494785785675,-0.1911141276359558,0.020487723872065544,0.1543813794851303,0.06261248141527176,0.08409999310970306,-0.06845321506261826,-0.15737105906009674,-0.01761234737932682,-0.007038543466478586,-0.15965896844863892,-0.019346732646226883,0.11245585232973099,-0.15582488477230072,-0.053237393498420715,-0.03373118117451668,0.21016375720500946,0.06045585498213768,-0.1295851320028305,-0.18228228390216827,0.09833298623561859,-0.19769015908241272,-0.04021664708852768,0.07638029009103775,-0.1356709748506546,-0.16763412952423096,-0.30060550570487976,0.018195485696196556,0.43565189838409424,0.026413820683956146,-0.18698129057884216,0.05598718672990799,-0.07054124027490616,-0.036038849502801895,0.10266026109457016,0.15924328565597534,-0.04671493545174599,0.010137073695659637,-0.11019105464220047,0.019056107848882675,0.187773197889328,-0.10262756049633026,-0.019287124276161194,0.21255162358283997,-0.036081451922655106,0.07753340154886246,0.03038659878075123,0.05292456969618797,0.0259633120149374,0.04063740372657776,-0.14223213493824005,-0.008837109431624413,0.0130552863702178,-0.0427946001291275,0.005334715358912945,0.09558556973934174,-0.13553836941719055,0.10642892122268677,0.0246994961053133,0.056954316794872284,0.07313073426485062,-0.01986696757376194,-0.10751038789749146,-0.11657825112342834,0.13608182966709137,-0.20266349613666534,0.22241638600826263,0.18515734374523163,0.07708694785833359,0.07596002519130707,0.09647580981254578,0.14511844515800476,0.00144214800093323,-0.011088008992373943,-0.19167295098304749,0.0478956438601017,0.11115057021379471,-0.00017087443848140538,0.07506650686264038,-0.0038325577042996883]', 'assets/upload/1766732535_sua-laptop-gan-day-3.jpeg', 'Hậu Giang', 'customer', '2025-11-02 08:19:53', '2025-12-26 07:40:08', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `warranty_requests`
--

CREATE TABLE `warranty_requests` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warranty_requests`
--

INSERT INTO `warranty_requests` (`id`, `booking_id`, `reason`, `status`, `created_at`) VALUES
(1, 8, 'máy lại hư', 'accepted', '2025-11-22 08:18:39'),
(2, 8, '1234', 'accepted', '2025-12-05 01:29:08'),
(3, 8, 'Máy hỏng', 'accepted', '2025-12-05 01:43:25'),
(4, 8, 'máy chập chờn', 'accepted', '2025-12-26 05:49:21'),
(5, 76, 'Máy lag', 'accepted', '2025-12-28 07:42:26'),
(6, 8, 'máy hư', 'accepted', '2025-12-28 08:36:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `technician_id` (`technician_id`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `wards_ward_code_unique` (`ward_code`) USING BTREE,
  ADD KEY `wards_province_code_index` (`province_code`) USING BTREE;

--
-- Indexes for table `equipments`
--
ALTER TABLE `equipments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forum_answers`
--
ALTER TABLE `forum_answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forum_questions`
--
ALTER TABLE `forum_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `orderequipments`
--
ALTER TABLE `orderequipments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orderequipments_equipments` (`equipment_id`),
  ADD KEY `orderequipments_ibfk_1` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `technician_id` (`technician_id`);

--
-- Indexes for table `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `provinces_province_code_unique` (`province_code`),
  ADD UNIQUE KEY `provinces_code_unique` (`code`);

--
-- Indexes for table `repairs`
--
ALTER TABLE `repairs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `technician_id` (`technician_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`booking_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_keywords`
--
ALTER TABLE `service_keywords`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `keyword` (`keyword`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `technician_schedule`
--
ALTER TABLE `technician_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_technician_user` (`technician_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `warranty_requests`
--
ALTER TABLE `warranty_requests`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `book`
--
ALTER TABLE `book`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `equipments`
--
ALTER TABLE `equipments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `forum_answers`
--
ALTER TABLE `forum_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `forum_questions`
--
ALTER TABLE `forum_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `orderequipments`
--
ALTER TABLE `orderequipments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `provinces`
--
ALTER TABLE `provinces`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `repairs`
--
ALTER TABLE `repairs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `service_keywords`
--
ALTER TABLE `service_keywords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=184;

--
-- AUTO_INCREMENT for table `technician_schedule`
--
ALTER TABLE `technician_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `warranty_requests`
--
ALTER TABLE `warranty_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `districts`
--
ALTER TABLE `districts`
  ADD CONSTRAINT `wards_province_code_foreign` FOREIGN KEY (`province_code`) REFERENCES `provinces` (`province_code`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `orderequipments`
--
ALTER TABLE `orderequipments`
  ADD CONSTRAINT `fk_orderequipments_equipments` FOREIGN KEY (`equipment_id`) REFERENCES `equipments` (`id`),
  ADD CONSTRAINT `orderequipments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `repairs`
--
ALTER TABLE `repairs`
  ADD CONSTRAINT `repairs_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `repairs_technician_fk` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `technician_schedule`
--
ALTER TABLE `technician_schedule`
  ADD CONSTRAINT `fk_technician_user` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
