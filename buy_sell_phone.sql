-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2025 at 09:10 AM
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
-- Database: `buy_sell_phone`
--

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `logo`, `created_at`) VALUES
(1, 'Apple', 'apple-logo.png', '2025-04-05 07:22:58'),
(2, 'Samsung', 'samsung-logo.png', '2025-04-05 07:22:58'),
(3, 'Google', 'google-logo.png', '2025-04-05 07:22:58'),
(4, 'Xiaomi', 'xiaomi-logo.png', '2025-04-05 07:22:58'),
(5, 'OnePlus', 'oneplus-logo.png', '2025-04-05 07:22:58'),
(6, 'Huawei', 'huawei-logo.png', '2025-04-05 07:22:58'),
(7, 'Motorola', 'motorola-logo.png', '2025-04-05 07:22:58'),
(8, 'Sony', 'sony-logo.png', '2025-04-05 07:22:58'),
(9, 'Nokia', 'nokia-logo.png', '2025-04-05 07:22:58'),
(10, 'LG', 'lg-logo.png', '2025-04-05 07:22:58');

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `user1_id` int(11) NOT NULL,
  `user2_id` int(11) NOT NULL,
  `phone_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_message_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`id`, `user1_id`, `user2_id`, `phone_id`, `created_at`, `last_message_time`) VALUES
(1, 14, 2, 1, '2025-04-23 08:40:16', '2025-04-23 08:40:16'),
(2, 14, 1, 15, '2025-04-23 15:49:14', '2025-04-23 15:49:14'),
(6, 14, 9, 14, '2025-04-23 17:05:49', '2025-04-23 17:05:49'),
(15, 14, 2, 13, '2025-04-26 14:57:03', '2025-04-26 14:57:03');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `conversation_id`, `sender_id`, `message`, `is_read`, `created_at`) VALUES
(1, 2, 14, 'helloo', 0, '2025-04-23 16:27:39'),
(2, 2, 14, 'hello', 0, '2025-04-23 16:32:05'),
(3, 2, 14, 'hello', 0, '2025-04-23 16:32:11'),
(4, 2, 14, 'hello', 0, '2025-04-23 16:33:03'),
(5, 2, 14, 'hello', 0, '2025-04-23 16:33:12'),
(6, 2, 14, 'hello', 0, '2025-04-23 16:33:55'),
(7, 2, 14, 'hello', 0, '2025-04-23 16:35:20'),
(8, 2, 14, 'hope you will be fine', 0, '2025-04-23 16:35:33'),
(9, 6, 14, 'hello', 0, '2025-04-23 17:08:07'),
(10, 6, 14, 'hello', 0, '2025-04-23 17:10:37'),
(11, 15, 14, 'hey', 0, '2025-04-26 14:57:11'),
(12, 15, 14, 'is this phone availablie?', 0, '2025-04-26 14:57:30');

-- --------------------------------------------------------

--
-- Table structure for table `phones`
--

CREATE TABLE `phones` (
  `id` int(11) NOT NULL,
  `phone_name` varchar(255) NOT NULL,
  `phone_price` decimal(10,2) NOT NULL,
  `phone_condition` int(11) NOT NULL,
  `phone_details` text DEFAULT NULL,
  `image_paths` text DEFAULT '\'uploads/none.jpg\'',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `views` int(11) NOT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `model_id` int(11) DEFAULT NULL,
  `phone_storage` varchar(50) DEFAULT NULL,
  `phone_color` varchar(50) DEFAULT NULL,
  `sellerId` int(255) NOT NULL,
  `seller_name` varchar(100) NOT NULL,
  `seller_email` varchar(100) NOT NULL DEFAULT '1',
  `seller_phone` varchar(50) NOT NULL,
  `seller_location` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `phones`
--

INSERT INTO `phones` (`id`, `phone_name`, `phone_price`, `phone_condition`, `phone_details`, `image_paths`, `created_at`, `views`, `brand_id`, `model_id`, `phone_storage`, `phone_color`, `sellerId`, `seller_name`, `seller_email`, `seller_phone`, `seller_location`) VALUES
(1, 'iphone', 200000.00, 6, NULL, '', '2025-03-29 11:30:41', 11, NULL, NULL, NULL, NULL, 1, '', '', '', ''),
(2, 'iphone', 200000.00, 6, NULL, '', '2025-03-29 11:33:02', 17, NULL, NULL, NULL, NULL, 0, '', '', '', ''),
(3, 'iphone', 200000.00, 6, NULL, '', '2025-03-29 11:33:41', 0, NULL, NULL, NULL, NULL, 0, '', '', '', ''),
(4, 'iphone', 200000.00, 6, NULL, '', '2025-03-29 11:37:58', 0, NULL, NULL, NULL, NULL, 0, '', '', '', ''),
(5, 'iphone', 200000.00, 6, NULL, '', '2025-03-29 11:38:38', 0, NULL, NULL, NULL, NULL, 0, '', '', '', ''),
(7, 'iphone', 2000.00, 5, 'best phone ever', '', '2025-03-29 11:39:48', 1, NULL, NULL, NULL, NULL, 0, '', '', '', ''),
(8, 'iphone', 2000.00, 6, 'best phone ever', '', '2025-03-29 11:48:24', 1, NULL, NULL, NULL, NULL, 0, '', '', '', ''),
(9, 'iphone', 2000.00, 6, 'best phone ever', '', '2025-03-29 11:48:53', 0, NULL, NULL, NULL, NULL, 0, '', '', '', ''),
(10, 'iphone', 20000.00, 5, 'best phone ever', '', '2025-03-29 11:49:58', 0, NULL, NULL, NULL, NULL, 0, '', '', '', ''),
(11, 'iphone', 20000.00, 5, 'best phone ever', '', '2025-03-29 11:50:48', 0, NULL, NULL, NULL, NULL, 0, '', '', '', ''),
(12, 'iphone', 100000.00, 5, 'kikjihjnjiknk', '1743253142_Amin CV.png,uploads/1743253142_Screenshot 2023-11-26 200602.png,uploads/1743253142_Screenshot 2023-11-26 225425.png,uploads/1743253142_Screenshot 2023-12-08 103756.png,uploads/1743253142_Screenshot 2024-04-07 203312.png,uploads/1743253142_Screenshot 2024-05-23 114504.png,uploads/1743253142_Screenshot 2024-05-23 114548.png', '2025-03-29 12:59:02', 6, NULL, NULL, NULL, NULL, 0, '', '', '', ''),
(13, 'iphone 10', 30000.00, 9, 'Best phone', '../uploads/1743265963_Amin CV.png,../uploads/1743265963_Screenshot 2023-11-26 200602.png,../uploads/1743265963_Screenshot 2023-11-26 225425.png,../uploads/1743265963_Screenshot 2023-12-08 103756.png,../uploads/1743265963_Screenshot 2024-04-07 203312.png,../uploads/1743265963_Screenshot 2024-05-23 114504.png,../uploads/1743265963_Screenshot 2024-05-23 114548.png', '2025-03-29 16:32:43', 27, NULL, NULL, NULL, NULL, 2, '', '', '', ''),
(14, 'iPhone 11 Pro Max', 10000.00, 5, '', '1743870992_Amin CV.png,1743870992_phones.png,1743870992_Screenshot 2023-11-26 200602.png,1743870992_Screenshot 2023-11-26 225425.png,1743870992_Screenshot 2023-12-08 103756.png,1743870992_Screenshot 2024-04-07 203312.png', '2025-04-05 16:36:32', 31, 1, 18, '16GB', 'White', 9, 'Arbab ', 'arbab@gmail.com', '03330411255', 'peshawar'),
(15, 'iPhone 15', 20000.00, 5, '', '1743871284_Amin CV.png,1743871284_phones.png,1743871284_Screenshot 2023-11-26 200602.png,1743871284_Screenshot 2023-11-26 225425.png,1743871284_Screenshot 2024-04-07 203312.png,1743871284_Screenshot 2024-05-23 114504.png,1743871284_Screenshot 2024-06-10 163541.png', '2025-04-05 16:41:24', 25, 1, 3, '256GB', 'White', 9, 'Arbab ', 'arbab@gmail.com', '03330411255', 'peshawar'),
(16, 'Pixel 7 Pro', 100000.00, 9, '', '1745685408_cars.jpeg', '2025-04-26 16:36:48', 2, 3, 43, '64GB', 'White', 0, 'Arbab ', 'arbab@gmail.com', '03330411255', 'Peshawar'),
(17, 'iPhone 14 Pro Max', 5000.00, 7, 'Very good phone', '1745852744_cars.jpeg', '2025-04-28 15:05:44', 2, 1, 5, '64GB', 'White', 14, 'Arbab ', 'arbab@gmail.com', '03330411255', 'Peshawar'),
(18, 'Pixel 7a', 5000.00, 7, '', '1745853019_cars.jpeg', '2025-04-28 15:10:19', 4, 3, 45, '32GB', 'White', 14, 'Arbab ', 'arbab@gmail.com', '03330411255', 'Peshawar'),
(21, 'Galaxy S22 Ultra', 5000.00, 9, '', '1746110057_Screenshot 2024-06-23 224352.png,1746110057_Screenshot 2024-06-23 224444.png,1746110057_Screenshot 2024-06-23 224509.png,1746110057_Screenshot 2024-06-23 224543.png,1746110057_Screenshot 2024-06-23 224624.png,6813a840e14e8.png', '2025-05-01 14:34:17', 22, 2, 24, '32GB', 'golden', 18, 'Arbab Rahim ullah jan', 'arbabrahimullahjan@gmail.com', '03330411255', 'Peshawar');

-- --------------------------------------------------------

--
-- Table structure for table `phone_models`
--

CREATE TABLE `phone_models` (
  `id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `model_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `phone_models`
--

INSERT INTO `phone_models` (`id`, `brand_id`, `model_name`, `created_at`) VALUES
(1, 1, 'iPhone 15 Pro Max', '2025-04-05 07:22:58'),
(2, 1, 'iPhone 15 Pro', '2025-04-05 07:22:58'),
(3, 1, 'iPhone 15', '2025-04-05 07:22:58'),
(4, 1, 'iPhone 15 Plus', '2025-04-05 07:22:58'),
(5, 1, 'iPhone 14 Pro Max', '2025-04-05 07:22:58'),
(6, 1, 'iPhone 14 Pro', '2025-04-05 07:22:58'),
(7, 1, 'iPhone 14', '2025-04-05 07:22:58'),
(8, 1, 'iPhone 14 Plus', '2025-04-05 07:22:58'),
(9, 1, 'iPhone 13 Pro Max', '2025-04-05 07:22:58'),
(10, 1, 'iPhone 13 Pro', '2025-04-05 07:22:58'),
(11, 1, 'iPhone 13', '2025-04-05 07:22:58'),
(12, 1, 'iPhone 13 Mini', '2025-04-05 07:22:58'),
(13, 1, 'iPhone 12 Pro Max', '2025-04-05 07:22:58'),
(14, 1, 'iPhone 12 Pro', '2025-04-05 07:22:58'),
(15, 1, 'iPhone 12', '2025-04-05 07:22:58'),
(16, 1, 'iPhone 12 Mini', '2025-04-05 07:22:58'),
(17, 1, 'iPhone SE (2022)', '2025-04-05 07:22:58'),
(18, 1, 'iPhone 11 Pro Max', '2025-04-05 07:22:58'),
(19, 1, 'iPhone 11 Pro', '2025-04-05 07:22:58'),
(20, 1, 'iPhone 11', '2025-04-05 07:22:58'),
(21, 2, 'Galaxy S23 Ultra', '2025-04-05 07:22:58'),
(22, 2, 'Galaxy S23+', '2025-04-05 07:22:58'),
(23, 2, 'Galaxy S23', '2025-04-05 07:22:58'),
(24, 2, 'Galaxy S22 Ultra', '2025-04-05 07:22:58'),
(25, 2, 'Galaxy S22+', '2025-04-05 07:22:58'),
(26, 2, 'Galaxy S22', '2025-04-05 07:22:58'),
(27, 2, 'Galaxy S21 FE', '2025-04-05 07:22:58'),
(28, 2, 'Galaxy Z Fold 5', '2025-04-05 07:22:58'),
(29, 2, 'Galaxy Z Flip 5', '2025-04-05 07:22:58'),
(30, 2, 'Galaxy Z Fold 4', '2025-04-05 07:22:58'),
(31, 2, 'Galaxy Z Flip 4', '2025-04-05 07:22:58'),
(32, 2, 'Galaxy A54', '2025-04-05 07:22:58'),
(33, 2, 'Galaxy A53', '2025-04-05 07:22:58'),
(34, 2, 'Galaxy A34', '2025-04-05 07:22:58'),
(35, 2, 'Galaxy A33', '2025-04-05 07:22:58'),
(36, 2, 'Galaxy A23', '2025-04-05 07:22:58'),
(37, 2, 'Galaxy A14', '2025-04-05 07:22:58'),
(38, 2, 'Galaxy M53', '2025-04-05 07:22:58'),
(39, 2, 'Galaxy M33', '2025-04-05 07:22:58'),
(40, 2, 'Galaxy M23', '2025-04-05 07:22:58'),
(41, 3, 'Pixel 8 Pro', '2025-04-05 07:22:58'),
(42, 3, 'Pixel 8', '2025-04-05 07:22:58'),
(43, 3, 'Pixel 7 Pro', '2025-04-05 07:22:58'),
(44, 3, 'Pixel 7', '2025-04-05 07:22:58'),
(45, 3, 'Pixel 7a', '2025-04-05 07:22:58'),
(46, 3, 'Pixel 6 Pro', '2025-04-05 07:22:58'),
(47, 3, 'Pixel 6', '2025-04-05 07:22:58'),
(48, 3, 'Pixel 6a', '2025-04-05 07:22:58'),
(49, 3, 'Pixel 5', '2025-04-05 07:22:58'),
(50, 3, 'Pixel 5a', '2025-04-05 07:22:58'),
(51, 4, 'Xiaomi 13 Pro', '2025-04-05 07:22:58'),
(52, 4, 'Xiaomi 13', '2025-04-05 07:22:58'),
(53, 4, 'Xiaomi 12 Pro', '2025-04-05 07:22:58'),
(54, 4, 'Xiaomi 12', '2025-04-05 07:22:58'),
(55, 4, 'Xiaomi 12T Pro', '2025-04-05 07:22:58'),
(56, 4, 'Xiaomi 12T', '2025-04-05 07:22:58'),
(57, 4, 'Redmi Note 12 Pro+', '2025-04-05 07:22:58'),
(58, 4, 'Redmi Note 12 Pro', '2025-04-05 07:22:58'),
(59, 4, 'Redmi Note 12', '2025-04-05 07:22:58'),
(60, 4, 'Redmi 12', '2025-04-05 07:22:58'),
(61, 5, 'OnePlus 11', '2025-04-05 07:22:58'),
(62, 5, 'OnePlus 10 Pro', '2025-04-05 07:22:58'),
(63, 5, 'OnePlus 10T', '2025-04-05 07:22:58'),
(64, 5, 'OnePlus Nord 3', '2025-04-05 07:22:58'),
(65, 5, 'OnePlus Nord 2T', '2025-04-05 07:22:58'),
(66, 5, 'OnePlus Nord CE 3', '2025-04-05 07:22:58'),
(67, 5, 'OnePlus Nord CE 2', '2025-04-05 07:22:58'),
(68, 5, 'OnePlus 9 Pro', '2025-04-05 07:22:58'),
(69, 5, 'OnePlus 9', '2025-04-05 07:22:58'),
(70, 5, 'OnePlus 8T', '2025-04-05 07:22:58');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` text DEFAULT '/Components/NoDp.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `phone`, `created_at`, `profile_picture`) VALUES
(1, 'Arbab', 'arbabsherahmad6@gmail.com', '$2y$10$p18anPHvtxWLmIQoBU5xXeZZjP/gsZQPToitnabNZgA', '12345458', '2025-03-27 13:23:15', ''),
(2, 'Arbab', 'arbabsherahmad@gmail.com', '$2y$10$7VrpcPwtLyOP6YYJ.YMdZuzA/xUxKDwgOzUQObekgud', '12345458', '2025-03-27 16:07:29', ''),
(9, 'Arbab', 'arbabsherahmad7@gmail.com', '$2y$10$CIP1RCN9iqOFTZp3SYc42OY1yPKLk2T4x1JShJv8NvT', 'arbabsherahmad@gmail.com', '2025-03-27 17:33:01', ''),
(10, 'Arbab', 'arbabsherahmad8@gmail.com', '$2y$10$dqzyutpEpUtlCS7K5y02xe5d8j9ETLXKw0CN11VCOgm', 'arbabsherahmad8@gmail.com', '2025-04-03 16:28:33', ''),
(11, 'Arbab', 'arbabsherahmad10@gmail.com', '$2y$10$p5oLt8sKotX/jcffOyHob.D/gcdK1dqlmH5G644RV9T', 'arbabsherahmad6@gmail.com', '2025-04-03 16:30:16', ''),
(12, 'Arbab', 'arbabsherahmad9@gmail.com', '$2y$10$Cu8bKEDEVvkxiCtJu6YTLOJx7XQNS4WWnTEgpDX/hNa', 'arbabsherahmad8@gmail.com', '2025-04-03 16:37:32', ''),
(13, 'Arbab', 'a@gmail.com', '$2y$10$SYSZHsnhzbEPT3YzZKtXq.aEK6UXJZelD1/guNZZLop', '123', '2025-04-04 12:45:07', ''),
(14, 'kn', 'a1@gmail.com', '$2y$10$6QErXKnkV/8b9aIXCe3EwuEgh9bIabaTwzuE0ErLjLBJKHY9LeW4m', 'a@gmail.com', '2025-04-04 13:17:42', ''),
(15, 'Arbab', 'a2@gmail.com', '$2y$10$yyzduaH2hBWwich0Eq2no.8XwyIiSfsOvMOxbWatcfE8aj/dr0ICO', '13425364756', '2025-04-04 13:19:42', ''),
(16, 'Arbab', 'a6@gmail.com', '$2y$10$BgvVdIlRzQT7pE7vRH7hduOqDMwuX40cKm0DWiIFOu2xPGmezUiRq', 'arbabsherahmad@gmail.com', '2025-04-04 13:30:52', '/Components/noDP.png'),
(17, 'Arbab', 'a55@gmail.com', '$2y$10$acSc0KrUmIQrJV1t5cXUdeEuXvoQJme/OCOBfk0TxFt5U1BhUDk7m', '0333-0411255', '2025-04-28 16:27:29', NULL),
(18, 'Arbab Rahim', 'arbabrahimullahjan@gmail.com', '$2y$10$fYM6OPivqtUpKIYECKyvMO.2pn3mIbOBLE0G03HpyxYepYJnCJSCu', '0333-0411255', '2025-04-29 14:49:04', NULL),
(19, 'Arbab Rahim Ullah ', 'a776@gmail.com', '$2y$10$oROFan2NVcHfGtxTX1wgyuzLbURouK6iN.pA7E1uT01alL4AzN/aK', '0333-0411255', '2025-04-29 15:35:53', 'uploads/profile_images/6810f1d90f259.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user2_id` (`user2_id`),
  ADD KEY `phone_id` (`phone_id`),
  ADD KEY `idx_conversations_users` (`user1_id`,`user2_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_messages_conversation` (`conversation_id`),
  ADD KEY `idx_messages_sender` (`sender_id`);

--
-- Indexes for table `phones`
--
ALTER TABLE `phones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_brand_id` (`brand_id`),
  ADD KEY `fk_model_id` (`model_id`);

--
-- Indexes for table `phone_models`
--
ALTER TABLE `phone_models`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `phones`
--
ALTER TABLE `phones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `phone_models`
--
ALTER TABLE `phone_models`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`user1_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversations_ibfk_2` FOREIGN KEY (`user2_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversations_ibfk_3` FOREIGN KEY (`phone_id`) REFERENCES `phones` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `phones`
--
ALTER TABLE `phones`
  ADD CONSTRAINT `fk_brand_id` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  ADD CONSTRAINT `fk_model_id` FOREIGN KEY (`model_id`) REFERENCES `phone_models` (`id`);

--
-- Constraints for table `phone_models`
--
ALTER TABLE `phone_models`
  ADD CONSTRAINT `phone_models_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
