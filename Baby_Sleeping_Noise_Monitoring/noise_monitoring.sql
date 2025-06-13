-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2025 at 05:11 AM
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
-- Database: `noise_monitoring`
--

-- --------------------------------------------------------

--
-- Table structure for table `cooldowns`
--

CREATE TABLE `cooldowns` (
  `id` int(11) NOT NULL,
  `value` int(11) NOT NULL DEFAULT 15
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cooldowns`
--

INSERT INTO `cooldowns` (`id`, `value`) VALUES
(1, 16);

-- --------------------------------------------------------

--
-- Table structure for table `cry_logs`
--

CREATE TABLE `cry_logs` (
  `id` int(11) NOT NULL,
  `time` datetime NOT NULL DEFAULT current_timestamp(),
  `loudness` int(11) NOT NULL,
  `loudness_level` enum('moderate','loud','dangerous') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cry_logs`
--

INSERT INTO `cry_logs` (`id`, `time`, `loudness`, `loudness_level`) VALUES
(1, '2025-05-03 16:50:38', 582, 'moderate'),
(2, '2025-05-03 17:58:33', 576, 'moderate'),
(3, '2025-05-03 18:00:11', 821, 'loud'),
(4, '2025-05-03 18:07:39', 522, 'moderate'),
(5, '2025-05-04 11:22:05', 601, 'moderate'),
(6, '2025-05-04 11:25:17', 505, 'moderate'),
(7, '2025-05-06 14:12:02', 513, 'moderate'),
(8, '2025-05-06 15:27:50', 523, 'moderate'),
(9, '2025-05-06 15:28:54', 502, 'moderate'),
(10, '2025-05-06 15:31:47', 501, 'moderate'),
(11, '2025-05-06 18:40:30', 521, 'moderate'),
(12, '2025-05-06 18:46:07', 512, 'moderate'),
(13, '2025-05-06 18:47:49', 512, 'moderate'),
(14, '2025-05-06 18:48:21', 512, 'moderate'),
(15, '2025-05-06 18:48:53', 505, 'moderate'),
(16, '2025-05-06 18:49:25', 530, 'moderate'),
(17, '2025-05-07 09:44:35', 525, 'moderate'),
(18, '2025-05-07 09:45:08', 529, 'moderate'),
(19, '2025-05-07 09:48:47', 523, 'moderate'),
(20, '2025-05-07 09:49:28', 523, 'moderate'),
(21, '2025-05-07 09:53:21', 518, 'moderate'),
(22, '2025-05-07 09:58:00', 513, 'moderate'),
(23, '2025-05-07 09:58:32', 509, 'moderate'),
(24, '2025-05-07 09:59:04', 512, 'moderate'),
(25, '2025-05-07 09:59:37', 513, 'moderate'),
(26, '2025-05-07 10:02:01', 512, 'moderate'),
(27, '2025-05-07 10:03:26', 508, 'moderate'),
(28, '2025-05-07 10:03:58', 508, 'moderate'),
(29, '2025-05-07 10:07:12', 507, 'moderate'),
(30, '2025-05-07 10:07:44', 507, 'moderate'),
(31, '2025-05-07 10:13:13', 505, 'moderate'),
(32, '2025-05-07 10:13:48', 506, 'moderate'),
(33, '2025-05-07 10:14:42', 513, 'moderate'),
(34, '2025-05-07 10:15:14', 505, 'moderate'),
(35, '2025-05-07 10:15:47', 505, 'moderate'),
(36, '2025-05-07 10:16:20', 502, 'moderate'),
(37, '2025-05-07 10:17:59', 505, 'moderate'),
(38, '2025-05-07 10:18:36', 507, 'moderate'),
(39, '2025-05-07 10:21:37', 501, 'moderate'),
(40, '2025-05-07 10:22:10', 504, 'moderate'),
(41, '2025-05-07 10:30:03', 505, 'moderate'),
(42, '2025-05-07 10:30:35', 504, 'moderate'),
(43, '2025-05-07 10:32:07', 505, 'moderate'),
(44, '2025-05-07 10:32:40', 501, 'moderate'),
(45, '2025-05-07 10:35:43', 517, 'moderate'),
(46, '2025-05-07 10:36:16', 505, 'moderate'),
(47, '2025-05-07 10:40:25', 504, 'moderate'),
(48, '2025-05-07 11:55:13', 504, 'moderate'),
(49, '2025-05-08 09:22:22', 507, 'moderate'),
(50, '2025-05-08 09:25:41', 506, 'moderate'),
(51, '2025-05-08 09:45:15', 693, 'loud'),
(52, '2025-05-09 08:38:53', 508, 'moderate'),
(53, '2025-05-09 08:39:26', 517, 'moderate'),
(54, '2025-05-09 12:17:00', 774, 'loud'),
(55, '2025-05-15 19:06:20', 509, 'moderate'),
(56, '2025-05-15 19:06:53', 528, 'moderate'),
(57, '2025-05-15 19:29:09', 518, 'moderate'),
(58, '2025-05-16 13:11:12', 507, 'moderate'),
(59, '2025-05-16 13:11:44', 572, 'moderate'),
(60, '2025-05-16 13:12:21', 502, 'moderate'),
(61, '2025-05-16 13:12:53', 531, 'moderate'),
(62, '2025-05-16 13:13:56', 517, 'moderate'),
(63, '2025-05-16 13:14:58', 567, 'moderate');

-- --------------------------------------------------------

--
-- Table structure for table `log_flags`
--

CREATE TABLE `log_flags` (
  `id` int(11) NOT NULL,
  `value` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_flags`
--

INSERT INTO `log_flags` (`id`, `value`) VALUES
(1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `monitoring_status`
--

CREATE TABLE `monitoring_status` (
  `id` int(11) NOT NULL,
  `status` enum('play','pause') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `monitoring_status`
--

INSERT INTO `monitoring_status` (`id`, `status`) VALUES
(1, 'pause');

-- --------------------------------------------------------

--
-- Table structure for table `monitor_intervals`
--

CREATE TABLE `monitor_intervals` (
  `id` int(11) NOT NULL,
  `value` int(11) NOT NULL DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `monitor_intervals`
--

INSERT INTO `monitor_intervals` (`id`, `value`) VALUES
(1, 60);

-- --------------------------------------------------------

--
-- Table structure for table `notification_setting`
--

CREATE TABLE `notification_setting` (
  `id` int(11) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `api_key` varchar(100) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification_setting`
--

INSERT INTO `notification_setting` (`id`, `phone_number`, `api_key`, `updated_at`) VALUES
(1, '09936007426', '8378742', '2025-05-04 03:30:56');

-- --------------------------------------------------------

--
-- Table structure for table `realtime_loudness`
--

CREATE TABLE `realtime_loudness` (
  `id` int(11) NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `realtime_loudness`
--

INSERT INTO `realtime_loudness` (`id`, `value`) VALUES
(1, 332);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cooldowns`
--
ALTER TABLE `cooldowns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cry_logs`
--
ALTER TABLE `cry_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_flags`
--
ALTER TABLE `log_flags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `monitoring_status`
--
ALTER TABLE `monitoring_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `monitor_intervals`
--
ALTER TABLE `monitor_intervals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_setting`
--
ALTER TABLE `notification_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `realtime_loudness`
--
ALTER TABLE `realtime_loudness`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cry_logs`
--
ALTER TABLE `cry_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `notification_setting`
--
ALTER TABLE `notification_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
