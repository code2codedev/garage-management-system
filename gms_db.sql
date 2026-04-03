-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2026 at 01:58 PM
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
-- Database: `gms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `services_selected` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`services_selected`)),
  `status` enum('pending','approved','in_progress','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `vehicle_id`, `appointment_date`, `appointment_time`, `services_selected`, `status`, `created_at`) VALUES
(1, 2, '2026-03-04', '14:45:00', '[\"{\\\"name\\\":\\\"general diagnosis\\\",\\\"price\\\":2300.00}\",\"{\\\"name\\\":\\\"wheel alignment\\\",\\\"price\\\":3700.00}\",\"{\\\"name\\\":\\\"oil change\\\",\\\"price\\\":4000.00}\",\"{\\\"name\\\":\\\"paint job\\\",\\\"price\\\":50000.00}\"]', '', '2026-03-03 11:44:21'),
(2, 3, '2026-03-17', '06:07:00', '[\"{\\\"name\\\":\\\"general diagnosis\\\",\\\"price\\\":2300.00}\",\"{\\\"name\\\":\\\"wheel alignment\\\",\\\"price\\\":3700.00}\",\"{\\\"name\\\":\\\"oil change\\\",\\\"price\\\":4000.00}\",\"{\\\"name\\\":\\\"paint job\\\",\\\"price\\\":50000.00}\"]', 'pending', '2026-03-04 13:06:05'),
(3, 4, '2026-03-12', '10:08:00', '[\"{\\\"name\\\":\\\"general diagnosis\\\",\\\"price\\\":2300.00}\",\"{\\\"name\\\":\\\"oil change\\\",\\\"price\\\":4000.00}\"]', 'completed', '2026-03-04 13:06:24'),
(4, 5, '2026-03-08', '05:06:00', '[\"{\\\"name\\\":\\\"wheel alignment\\\",\\\"price\\\":3700.00}\",\"{\\\"name\\\":\\\"paint job\\\",\\\"price\\\":50000.00}\"]', '', '2026-03-04 13:06:43'),
(5, 4, '2026-03-05', '05:32:00', '[\"{\\\"name\\\":\\\"general diagnosis\\\",\\\"price\\\":2300.00}\",\"{\\\"name\\\":\\\"oil change\\\",\\\"price\\\":4000.00}\"]', 'pending', '2026-03-04 13:31:26'),
(6, 8, '2026-03-17', '16:55:00', '[\"{\\\"name\\\":\\\"general diagnosis\\\",\\\"price\\\":2300.00}\",\"{\\\"name\\\":\\\"wheel alignment\\\",\\\"price\\\":3700.00}\",\"{\\\"name\\\":\\\"oil change\\\",\\\"price\\\":4000.00}\"]', 'in_progress', '2026-03-11 11:55:35'),
(7, 10, '2026-03-17', '03:23:00', '[\"{\\\"name\\\":\\\"general diagnosis\\\",\\\"price\\\":2300.00}\",\"{\\\"name\\\":\\\"wheel alignment\\\",\\\"price\\\":3700.00}\",\"{\\\"name\\\":\\\"paint job\\\",\\\"price\\\":50000.00}\"]', '', '2026-03-11 12:22:17'),
(8, 11, '2026-03-18', '06:39:00', '[\"{\\\"name\\\":\\\"general diagnosis\\\",\\\"price\\\":2300.00}\",\"{\\\"name\\\":\\\"wheel alignment\\\",\\\"price\\\":3700.00}\"]', '', '2026-03-17 13:37:40'),
(9, 11, '2026-03-26', '04:00:00', '[\"{\\\"name\\\":\\\"wheel alignment\\\",\\\"price\\\":3700.00}\",\"{\\\"name\\\":\\\"oil change\\\",\\\"price\\\":4000.00}\"]', 'pending', '2026-03-17 13:59:00');

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `performed_by_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`id`, `action`, `vehicle_id`, `appointment_id`, `job_id`, `performed_by_id`, `created_at`) VALUES
(1, 'Used 1 of item #1 for vehicle #2', 2, NULL, NULL, 4, '2026-03-03 12:19:03'),
(2, 'Updated job #1 to status \'in_progress\'', 2, NULL, 1, 4, '2026-03-03 12:20:25'),
(3, 'Updated job #1 to status \'completed\'', 2, NULL, 1, 4, '2026-03-03 12:20:31'),
(4, 'Used 2 of item #1 for vehicle #5', 5, NULL, NULL, 4, '2026-03-04 13:28:19'),
(5, 'Used 2 of item #1 for vehicle #5', 5, NULL, NULL, 4, '2026-03-04 13:29:22'),
(6, 'Updated job #3 to status \'completed\'', 4, NULL, 3, 4, '2026-03-04 13:30:08'),
(7, 'Updated job #5 to status \'in_progress\'', 10, NULL, 5, 4, '2026-03-11 12:51:51'),
(8, 'Used 1 of item #3 for vehicle #11', 11, NULL, NULL, 4, '2026-03-17 13:41:46'),
(9, 'Used 2 of item #5 for vehicle #11', 11, NULL, NULL, 4, '2026-03-17 13:41:46'),
(10, 'Used 1 of item #4 for vehicle #11', 11, NULL, NULL, 4, '2026-03-17 13:41:46'),
(11, 'Updated job #6 to status \'completed\'', 11, NULL, 6, 4, '2026-03-17 13:42:21');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `instock` int(11) NOT NULL,
  `used` int(11) DEFAULT 0,
  `remaining` int(11) GENERATED ALWAYS AS (`instock` - `used`) STORED,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `item_name`, `price`, `instock`, `used`, `updated_at`) VALUES
(1, 'egine oil', 2000.00, 20, 6, '2026-03-04 13:29:22'),
(2, 'probox left headlights', 15000.00, 30, 0, '2026-03-11 13:05:10'),
(3, '22 inch rims', 40000.00, 15, 1, '2026-03-17 13:41:46'),
(4, 'Brake pads', 30000.00, 40, 20, '2026-03-18 13:12:42'),
(5, 'Brake fluid', 12000.00, 50, 2, '2026-03-17 13:41:46'),
(6, '2 inch nut', 50.00, 120, 29, '2026-03-17 14:03:46');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `mechanic_id` int(11) NOT NULL,
  `status` enum('in_progress','completed') DEFAULT 'in_progress',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `appointment_id`, `vehicle_id`, `mechanic_id`, `status`, `notes`, `created_at`) VALUES
(1, 1, 2, 4, 'completed', 'Also check on the front left headlight.', '2026-03-03 11:45:09'),
(2, 4, 5, 4, 'in_progress', 'fix bumber', '2026-03-04 13:26:54'),
(3, 3, 4, 4, 'completed', 'tires', '2026-03-04 13:28:48'),
(4, 6, 8, 4, 'in_progress', 'keep track of bumper', '2026-03-11 11:56:12'),
(5, 7, 10, 4, 'in_progress', 'keep track of bumper', '2026-03-11 12:22:32'),
(6, 8, 11, 4, 'completed', 'check on kdt', '2026-03-17 13:40:27');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `status`, `created_at`) VALUES
(1, 2, 6, 'wee mzee vipi', 'unread', '2026-03-01 14:08:17'),
(2, 4, 2, 'client to keep cheking on engine', 'unread', '2026-03-11 12:51:48'),
(3, 6, 2, 'ayee', 'unread', '2026-03-17 13:24:39'),
(4, 17, 2, 'hey check on kdt', 'unread', '2026-03-17 13:38:43'),
(5, 4, 2, 'maestro to keep track service time.', 'unread', '2026-03-17 13:42:16');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('cash','mpesa','card') NOT NULL,
  `status` enum('unpaid','paid') DEFAULT 'paid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `appointment_id`, `amount`, `method`, `status`, `created_at`) VALUES
(1, 1, 6030.00, 'cash', 'paid', '2026-03-04 13:24:05'),
(2, 7, 56300.00, 'mpesa', 'paid', '2026-03-11 13:22:54'),
(3, 4, 54000.00, 'card', 'paid', '2026-03-11 13:23:03'),
(4, 8, 6300.00, 'mpesa', 'paid', '2026-03-17 13:56:55');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `price`, `created_at`) VALUES
(1, 'paint job', 50000.00, '2026-03-03 11:43:18'),
(2, 'oil change', 4000.00, '2026-03-03 11:43:28'),
(3, 'wheel alignment', 3700.00, '2026-03-03 11:43:39'),
(4, 'general diagnosis', 2300.00, '2026-03-03 11:43:58'),
(5, 'buffing', 7800.00, '2026-03-17 14:00:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','receptionist','mechanic','customer') NOT NULL,
  `status` enum('active','frozen') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `status`, `created_at`) VALUES
(1, 'admin', 'admin@alphagarage.com', '$2y$10$OUoRbWDQMtjSWzx1ZPJ7x.rUelc9/CEFm8XkMpKfL9NuI2GX6WsSK', 'admin', 'active', '2026-03-01 13:10:23'),
(2, 'jane', 'jane@gmail.com', '$2y$10$zIBXLtz7CzH7Jdp64VoC/uTbF5xHKhL2W4R9e1Nx4/9xbRd.nGvIK', 'receptionist', 'active', '2026-03-01 13:11:59'),
(3, 'joy', 'joy@gmail.com', '$2y$10$zVc3guJCOPV5QLRUuONF2OXc8co6mQYvSZIeMUYZKtSbQ3LZT08xa', 'receptionist', 'active', '2026-03-01 13:12:25'),
(4, 'peter', 'peter@gmail.com', '$2y$10$eFgHvXVxIK8H4ilXCW/tyuZ/mfRIqjdHQi0JlqugZxwZiwzWCname', 'mechanic', 'active', '2026-03-01 13:12:40'),
(5, 'alex', 'alex@gmail.com', '$2y$10$5QcQj3wZsr3iKuG6TCBCW.tOZ.cgCJin/vDDNK5MgWzI.t7oz9lZe', 'mechanic', 'active', '2026-03-01 13:12:54'),
(6, 'mark', 'mark@gmail.com', '$2y$10$6PvCLJ8od.OVGzWh49dyOOWtjAapdtMK5vvesYINqqG0X9oVykNxm', 'customer', 'active', '2026-03-01 13:13:24'),
(7, 'james', 'james@gmail.com', '$2y$10$bShupXm3jlvs.SvyFaW0Q.K5i4HEOMMMHMcsvUrCw4j3yubPihcnK', 'customer', 'active', '2026-03-01 13:21:24'),
(8, 'john', 'john@gmail.com', '$2y$10$Ocu7.L1BVYvQ5fGF8/Z6ievcO/H3/evkPGN5xS9m39hICdUxnDSC.', 'customer', 'active', '2026-03-01 13:21:41'),
(9, 'kim', 'kim@gmail.com', '$2y$10$Mhr5ErBk5uC5927VwiNaCu813XevpWY.kO9ba3TtoWWqpS2yhpwpG', 'customer', 'frozen', '2026-03-04 13:03:07'),
(11, 'leah', 'leah@gmail.com', '$2y$10$dqKJpVu6ZNCwyvMuDWLV9eAtoxzX8ylsH/nqSymGzQFfwwX55n4d6', 'customer', 'frozen', '2026-03-04 13:03:45'),
(12, 'money', 'money@gmail.com', '$2y$10$DMofQZKGeDish0eP9FJyIOFXDpQy8hIWiNcQXGToQl44VikyaUI5S', 'customer', 'active', '2026-03-10 13:59:07'),
(13, 'mercy', 'mercy@gmail.com', '$2y$10$3yx0n9UJIJb5yN0mxYOP3O6aXqiWiElrRVKT9kw98vUKZhZ4HR5vm', 'customer', 'active', '2026-03-11 11:53:48'),
(14, 'carson', 'carson@gmail.com', '$2y$10$idhwQM8jUf07ak8f7LgeN.Qj5K35YDK0tyL2BsL1wvEDqd8UNJhbq', 'customer', 'active', '2026-03-11 12:02:33'),
(15, 'JULIUS', 'JULIUS@gmail.com', '$2y$10$mzMsubS7aXNlXs9T/ndkNuI1SJkf5gyhSD3wlXDCtjUDnL/ZSE1AK', 'customer', 'active', '2026-03-11 13:28:58'),
(16, 'mary', 'mary@gmail.com', '$2y$10$I47KFMcAH7wAiSrRz2L2fONh/fNBHiPS0m3LaLiNW2CkglG74mQrK', 'receptionist', 'active', '2026-03-17 06:12:16'),
(17, 'maestro', 'maestro@gmail.com', '$2y$10$D6eOTpoCAmx2VRq6cRLrruLMdKiJxvbOANtTDYDhazKK3szdCeLoG', 'customer', 'active', '2026-03-17 13:36:15'),
(18, 'melo', 'melo@gmail.com', '$2y$10$Nh6A8YEEQbSZuuNn7w9kTejyxAhiymRjy9qEPe7XYmCw2zoI6iaiG', 'mechanic', 'active', '2026-03-17 13:59:53');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `reg_number` varchar(20) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `payment_status` enum('unpaid','paid') DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `reg_number`, `owner_id`, `phone`, `status`, `payment_status`, `created_at`) VALUES
(1, 'KDQ 347K', 6, '0712345678', 'pending', 'unpaid', '2026-03-01 13:14:10'),
(2, 'KDQ 347J', 6, '0740323815', 'completed', 'unpaid', '2026-03-03 11:42:28'),
(3, 'KBG 347K', 8, '0712345678', 'pending', 'unpaid', '2026-03-04 13:04:46'),
(4, 'KDP 459K', 7, '0712345678', 'completed', 'unpaid', '2026-03-04 13:05:01'),
(5, 'KAE 738D', 9, '0123456789', 'in_progress', 'unpaid', '2026-03-04 13:05:14'),
(6, 'KAK 738M', 11, '0123456789', 'pending', 'unpaid', '2026-03-04 13:05:36'),
(7, 'KDQ 568L', 13, '0793487639', 'pending', 'unpaid', '2026-03-11 11:54:19'),
(8, 'KAJ 453X', 13, '0740323815', 'in_progress', 'unpaid', '2026-03-11 11:55:00'),
(9, 'KDB 785G', 6, '0113285479', 'pending', 'unpaid', '2026-03-11 12:11:55'),
(10, 'KDQ 456L', 14, '0745256745', 'in_progress', 'unpaid', '2026-03-11 12:21:20'),
(11, 'KDT 582M', 17, '0740323815', 'completed', 'unpaid', '2026-03-17 13:37:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `performed_by_id` (`performed_by_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `mechanic_id` (`mechanic_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sender` (`sender_id`),
  ADD KEY `idx_receiver` (`receiver_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reg_number` (`reg_number`),
  ADD KEY `owner_id` (`owner_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `history`
--
ALTER TABLE `history`
  ADD CONSTRAINT `history_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `history_ibfk_2` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `history_ibfk_3` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `history_ibfk_4` FOREIGN KEY (`performed_by_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_3` FOREIGN KEY (`mechanic_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
