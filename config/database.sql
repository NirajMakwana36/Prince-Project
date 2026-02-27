-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Feb 27, 2026 at 05:50 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `grocart`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(33, 5, 4, 1, '2026-02-27 16:30:35', '2026-02-27 16:30:35');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `image`, `description`, `created_at`, `updated_at`) VALUES
(2, 'Fresh Vegetables', 'https://images.unsplash.com/photo-1597362925123-77861d3fbac7?w=600&amp;auto=format&amp;fit=crop&amp;q=60&amp;ixlib=rb-4.1.0&amp;ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8dmVnZXRhYmxlc3xlbnwwfHwwfHx8MA%3D%3D', 'Organic and fresh from farms', '2026-02-27 10:54:27', '2026-02-27 16:41:40'),
(3, 'Fresh Fruits', 'https://plus.unsplash.com/premium_photo-1671379041175-782d15092945?w=600&amp;auto=format&amp;fit=crop&amp;q=60&amp;ixlib=rb-4.1.0&amp;ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZnJ1aXRzfGVufDB8fDB8fHww', 'Sweet and juicy seasonal fruits', '2026-02-27 10:54:27', '2026-02-27 16:42:27'),
(4, 'Dairy &amp; Bakery', 'https://images.unsplash.com/photo-1634141510639-d691d86f47be?w=600&amp;auto=format&amp;fit=crop&amp;q=60&amp;ixlib=rb-4.1.0&amp;ixid=M3wxMjA3fDB8MHxzZWFyY2h8MjB8fGRhaXJ5fGVufDB8fDB8fHww', 'Fresh milk, bread and butter', '2026-02-27 10:54:27', '2026-02-27 16:43:06'),
(5, 'Snacks &amp; Drinks', 'https://images.unsplash.com/photo-1613462847848-f65a8b231bb5?w=600&amp;auto=format&amp;fit=crop&amp;q=60&amp;ixlib=rb-4.1.0&amp;ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8c25ha3N8ZW58MHx8MHx8fDA%3D', 'Crunchy snacks and refreshing drinks', '2026-02-27 10:54:27', '2026-02-27 16:43:34'),
(6, 'Household Items', 'https://images.unsplash.com/photo-1688573485271-392c26ffe70a?w=600&amp;auto=format&amp;fit=crop&amp;q=60&amp;ixlib=rb-4.1.0&amp;ixid=M3wxMjA3fDB8MHxzZWFyY2h8Nnx8aG91c2UlMjBob2xkfGVufDB8fDB8fHww', 'Daily essentials for your home', '2026-02-27 10:54:27', '2026-02-27 16:44:02'),
(7, 'Beauty &amp; Hygiene', 'https://images.unsplash.com/photo-1583209814683-c023dd293cc6?w=600&amp;auto=format&amp;fit=crop&amp;q=60&amp;ixlib=rb-4.1.0&amp;ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTB8fGNvc21ldGljfGVufDB8fDB8fHww', 'Personal care and beauty products', '2026-02-27 10:54:27', '2026-02-27 16:44:27');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_percentage` int(11) NOT NULL,
  `max_uses` int(11) DEFAULT NULL,
  `current_uses` int(11) DEFAULT 0,
  `expiry_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_partners`
--

CREATE TABLE `delivery_partners` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vehicle_number` varchar(20) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 5.00,
  `total_deliveries` int(11) DEFAULT 0,
  `status` enum('available','busy','offline') DEFAULT 'offline',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `delivery_charge` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','accepted','preparing','out_for_delivery','delivered','cancelled') DEFAULT 'pending',
  `address` text NOT NULL,
  `city` varchar(50) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `delivery_partner_id` int(11) DEFAULT NULL,
  `payment_method` enum('cash_on_delivery') DEFAULT 'cash_on_delivery',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `delivery_charge`, `status`, `address`, `city`, `postal_code`, `phone`, `delivery_partner_id`, `payment_method`, `created_at`, `updated_at`) VALUES
(1, 5, 278.10, 40.00, 'delivered', 'RBM general store, Main Road, Thonda, Gujarat', 'Thonda', '364230', '9191919191', NULL, 'cash_on_delivery', '2026-02-27 15:10:57', '2026-02-27 15:36:14'),
(2, 5, 15000.00, 40.00, 'delivered', 'Thonda', 'Thonda', '364', '9191919191', 4, 'cash_on_delivery', '2026-02-27 15:25:16', '2026-02-27 15:27:19'),
(3, 6, 1110.35, 40.00, 'delivered', 'Rojkot', 'Rojkot', '364002', '8787878787', 4, 'cash_on_delivery', '2026-02-27 15:39:29', '2026-02-27 16:02:55'),
(4, 2, 58.20, 40.00, 'delivered', 'Rojkot', 'Rojkot', '364002', '1234567890', NULL, 'cash_on_delivery', '2026-02-27 16:05:02', '2026-02-27 16:06:43'),
(5, 6, 1218.00, 40.00, 'delivered', 'Rajkot', 'Rajkot', '364002', '8787878787', 4, 'cash_on_delivery', '2026-02-27 16:10:13', '2026-02-27 16:26:22'),
(6, 5, 568.55, 40.00, 'delivered', 'Main Road, Thonda', 'Thonda', '364230', '9191919191', 4, 'cash_on_delivery', '2026-02-27 16:18:58', '2026-02-27 16:26:18'),
(7, 5, 112.50, 40.00, 'delivered', 'Main Road, Thonda', 'Thonda', '364230', '9191919191', 4, 'cash_on_delivery', '2026-02-27 16:21:42', '2026-02-27 16:26:12'),
(8, 5, 94.20, 40.00, 'delivered', 'Main Road, Thonda', 'Thonda', '364230', '9191919191', 4, 'cash_on_delivery', '2026-02-27 16:23:22', '2026-02-27 16:28:03'),
(9, 2, 1047.60, 40.00, 'delivered', 'Bhavnagar', 'Bhavnagar', '364002', '1234567890', 4, 'cash_on_delivery', '2026-02-27 16:34:45', '2026-02-27 16:35:31'),
(10, 2, 87.30, 40.00, 'delivered', 'Bhavnagar', 'Bhavnagar', '364002', '1234567890', 4, 'cash_on_delivery', '2026-02-27 16:36:33', '2026-02-27 16:37:27');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `created_at`) VALUES
(1, 1, 10, 1, 29.10, '2026-02-27 15:10:57'),
(2, 1, 1, 1, 36.00, '2026-02-27 15:10:57'),
(3, 1, 4, 1, 60.00, '2026-02-27 15:10:57'),
(4, 1, 3, 1, 153.00, '2026-02-27 15:10:57'),
(6, 3, 10, 5, 29.10, '2026-02-27 15:39:29'),
(7, 3, 9, 2, 47.50, '2026-02-27 15:39:29'),
(8, 3, 3, 1, 153.00, '2026-02-27 15:39:29'),
(9, 3, 2, 1, 76.00, '2026-02-27 15:39:29'),
(10, 3, 4, 3, 60.00, '2026-02-27 15:39:29'),
(12, 3, 7, 1, 20.00, '2026-02-27 15:39:29'),
(13, 3, 6, 3, 42.75, '2026-02-27 15:39:29'),
(14, 3, 8, 3, 39.20, '2026-02-27 15:39:29'),
(15, 4, 10, 2, 29.10, '2026-02-27 16:05:02'),
(16, 5, 3, 6, 153.00, '2026-02-27 16:10:13'),
(17, 5, 4, 5, 60.00, '2026-02-27 16:10:13'),
(18, 6, 1, 1, 36.00, '2026-02-27 16:18:58'),
(19, 6, 2, 1, 76.00, '2026-02-27 16:18:58'),
(20, 6, 3, 1, 153.00, '2026-02-27 16:18:58'),
(21, 6, 4, 1, 60.00, '2026-02-27 16:18:58'),
(23, 6, 6, 1, 42.75, '2026-02-27 16:18:58'),
(24, 6, 7, 1, 20.00, '2026-02-27 16:18:58'),
(25, 6, 8, 1, 39.20, '2026-02-27 16:18:58'),
(26, 6, 9, 1, 47.50, '2026-02-27 16:18:58'),
(27, 6, 10, 1, 29.10, '2026-02-27 16:18:58'),
(28, 7, 9, 1, 47.50, '2026-02-27 16:21:42'),
(30, 8, 10, 2, 29.10, '2026-02-27 16:23:22'),
(31, 8, 1, 1, 36.00, '2026-02-27 16:23:22'),
(32, 9, 10, 36, 29.10, '2026-02-27 16:34:45'),
(33, 10, 10, 3, 29.10, '2026-02-27 16:36:33');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `category_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount` int(11) DEFAULT 0,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category_id`, `price`, `discount`, `stock`, `image`, `description`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 'Fresh Tomato', 2, 40.00, 10, 97, 'https://images.unsplash.com/photo-1561136594-7f68413baa99?w=600&amp;auto=format&amp;fit=crop&amp;q=60&amp;ixlib=rb-4.1.0&amp;ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTB8fHRvbWF0b3xlbnwwfHwwfHx8MA%3D%3D', 'Local farm fresh tomatoes', 1, '2026-02-27 10:54:51', '2026-02-27 16:45:35'),
(2, 'Broccoli', 2, 80.00, 5, 48, 'https://plus.unsplash.com/premium_photo-1724250160975-6c789dbfdc9f?w=600&amp;auto=format&amp;fit=crop&amp;q=60&amp;ixlib=rb-4.1.0&amp;ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8YnJvY29saXxlbnwwfHwwfHx8MA%3D%3D', 'Highly nutritious fresh broccoli', 1, '2026-02-27 10:54:51', '2026-02-27 16:46:10'),
(3, 'Royal Gala Apple', 3, 180.00, 15, 191, 'https://media.istockphoto.com/id/184927564/photo/close-up-of-red-royal-gala-apples.webp?a=1&amp;b=1&amp;s=612x612&amp;w=0&amp;k=20&amp;c=VRLgQX6mEIUAXF168Aoy5LgdNf4-V1WuD0I8ma16JUg=', 'Sweet and crunchy apples', 1, '2026-02-27 10:54:51', '2026-02-27 16:46:36'),
(4, 'Fresh Banana', 3, 60.00, 0, 140, 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=600&amp;auto=format&amp;fit=crop&amp;q=60&amp;ixlib=rb-4.1.0&amp;ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8QmFuYW5hfGVufDB8fDB8fHww', 'Energizing fresh bananas', 1, '2026-02-27 10:54:51', '2026-02-27 16:47:05'),
(6, 'Whole Wheat Bread', 4, 45.00, 5, 36, 'https://images.unsplash.com/photo-1598373182133-52452f7691ef?w=600&amp;auto=format&amp;fit=crop&amp;q=60&amp;ixlib=rb-4.1.0&amp;ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8YnJlYWR8ZW58MHx8MHx8fDA%3D', 'Freshly baked healthy bread', 1, '2026-02-27 10:54:51', '2026-02-27 16:47:43'),
(7, 'Potato Chips', 5, 20.00, 0, 298, 'https://images.unsplash.com/photo-1741520150134-0d60d82dfac9?w=600&amp;auto=format&amp;fit=crop&amp;q=60&amp;ixlib=rb-4.1.0&amp;ixid=M3wxMjA3fDB8MHxzZWFyY2h8Nnx8bGF5cyUyMGNoaXBzfGVufDB8fDB8fHww', 'Classic salted chips', 1, '2026-02-27 10:54:51', '2026-02-27 16:48:23'),
(8, 'Coca Cola 500ml', 5, 40.00, 2, 96, 'https://images.unsplash.com/photo-1567103472667-6898f3a79cf2?w=600&amp;auto=format&amp;fit=crop&amp;q=60&amp;ixlib=rb-4.1.0&amp;ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8Y29jbyUyMGNvbGF8ZW58MHx8MHx8fDA%3D', 'Refreshing cold drink', 1, '2026-02-27 10:54:51', '2026-02-27 16:48:51'),
(9, 'Amul Milk', 4, 50.00, 5, 46, 'https://images.unsplash.com/photo-1757857843388-b8e0127e1cef?w=600&amp;auto=format&amp;fit=crop&amp;q=60&amp;ixlib=rb-4.1.0&amp;ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8YW11bCUyMG1pbGt8ZW58MHx8MHx8fDA%3D', 'Fresh milk', 1, '2026-02-27 10:57:55', '2026-02-27 16:45:06'),
(10, 'Fresh Tomato', 2, 30.00, 3, 0, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTGrlSoNcyDnMIqd1dfFkeeHLYESBofcq4I7xCHoFd1z2FyTjN3VtpB0ECD20gtLzYF3yYSyosJ8hfUC6wQ9kg6iRMGlJE1YFGwdC9jGDcztw&amp;s=10', 'Fresh Tasty Tomato', 1, '2026-02-27 15:08:51', '2026-02-27 16:36:33');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'delivery_charge', '40', '2026-02-27 10:07:49', '2026-02-27 10:07:49'),
(2, 'store_status', 'open', '2026-02-27 10:07:49', '2026-02-27 16:07:52'),
(3, 'store_opening_time', '08:00', '2026-02-27 10:07:49', '2026-02-27 10:07:49'),
(4, 'store_closing_time', '22:00', '2026-02-27 10:07:49', '2026-02-27 10:07:49'),
(5, 'contact_email', 'support@grocart.com', '2026-02-27 10:07:49', '2026-02-27 10:07:49'),
(6, 'contact_phone', '+91-1234567890', '2026-02-27 10:07:49', '2026-02-27 10:07:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin','delivery') NOT NULL DEFAULT 'customer',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `status`, `address`, `city`, `postal_code`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@grocart.com', '9876543210', '$2y$10$YIjlrPnoJ8TSi5lofLc3uO5qpm8.pW0dHaWjW2k8xK5H8q8F8y5He', 'admin', 'active', NULL, NULL, NULL, '2026-02-27 10:07:49', '2026-02-27 10:07:49'),
(2, 'Niraj Makwana', 'niraj@gmail.com', '1234567890', '$2y$10$YHIzeUDsjyTOCH7sE1tR/.vdlLScxzzQ4S.qtBb4GkBhSZ294BXu2', 'admin', 'active', NULL, NULL, NULL, '2026-02-27 10:29:25', '2026-02-27 10:31:59'),
(4, 'Rohit Sharma', 'rohit@gmail.com', '9090909090', '$2y$10$mL0nvpggcCDfUkrqW10X..QG3DOrkgYe5JuWa9MXWUNUq2jbkoM9K', 'delivery', 'active', NULL, NULL, NULL, '2026-02-27 10:44:36', '2026-02-27 15:13:24'),
(5, 'Prince Majethiya', 'prince@gmail.com', '9191919191', '$2y$10$U0VHegvRzvdoMWMjqIfYDOlezDJsXHpZqdpC76NsRueXTvbTQIA2W', 'customer', 'active', NULL, NULL, NULL, '2026-02-27 10:59:49', '2026-02-27 10:59:49'),
(6, 'Priyanshu Makani', 'priyanshu@gmail.com', '8787878787', '$2y$10$arcuaz1ipqwGd8cV1XPRpuD2Z8ElOoTpqffiu3y6BrlGt3Wb0BKtm', 'customer', 'active', NULL, NULL, NULL, '2026-02-27 15:37:41', '2026-02-27 15:37:41');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_cart_user` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `delivery_partners`
--
ALTER TABLE `delivery_partners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_order_status` (`status`),
  ADD KEY `idx_delivery_partner` (`delivery_partner_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_category` (`category_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_wishlist_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_partners`
--
ALTER TABLE `delivery_partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL DEFAULT 5,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `delivery_partners`
--
ALTER TABLE `delivery_partners`
  ADD CONSTRAINT `delivery_partners_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`delivery_partner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
-- Demo Reviews
INSERT INTO `reviews` (`user_id`, `product_id`, `rating`, `comment`) VALUES 
(1, 1, 5, 'Absolutely fresh and delicious! The delivery was also super fast.'),
(1, 2, 4, 'Good quality milk, very creamy. Though I wish the packaging was a bit more sustainable.'),
(1, 3, 5, 'The best eggs I have bought online. None were broken and they were very fresh.'),
(1, 10, 5, 'Sweet and juicy. Highly recommended!');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
