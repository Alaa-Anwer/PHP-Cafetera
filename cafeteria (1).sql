-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 15, 2026 at 10:04 AM
-- Server version: 8.0.30
-- PHP Version: 8.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cafeteria`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Drink'),
(2, 'Drink2');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `room_id` int DEFAULT NULL,
  `notes` text,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` enum('processing','out_for_delivery','done') DEFAULT 'processing',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `room_id`, `notes`, `total_price`, `status`, `created_at`) VALUES
(2, 4, 1, 'no sugar please\r\n', 100.00, 'processing', '2026-03-14 10:01:44'),
(3, 4, 1, 'no nswan', 150.00, 'processing', '2026-03-14 12:17:47'),
(4, 4, 1, 'gkghrkrghgkrykrtyi5yti5ytuii5', 100.00, 'processing', '2026-03-14 12:21:02');

-- --------------------------------------------------------

--
-- Stand-in structure for view `orders_with_rooms`
-- (See below for the actual view)
--
CREATE TABLE `orders_with_rooms` (
`id` int
,`user_id` int
,`room_id` int
,`notes` text
,`total_price` decimal(10,2)
,`status` enum('processing','out_for_delivery','done')
,`created_at` timestamp
,`room_name` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `orders_with_room_and_user`
-- (See below for the actual view)
--
CREATE TABLE `orders_with_room_and_user` (
`id` int
,`user_id` int
,`room_id` int
,`notes` text
,`total_price` decimal(10,2)
,`status` enum('processing','out_for_delivery','done')
,`created_at` timestamp
,`room_name` varchar(50)
,`user_name` varchar(100)
);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int DEFAULT '1',
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 2, 1, 4, 25.00),
(2, 3, 1, 6, 25.00),
(3, 4, 1, 4, 25.00);

-- --------------------------------------------------------

--
-- Stand-in structure for view `order_items_with_products`
-- (See below for the actual view)
--
CREATE TABLE `order_items_with_products` (
`id` int
,`order_id` int
,`product_id` int
,`quantity` int
,`price` decimal(10,2)
,`product_name` varchar(100)
,`product_image` varchar(255)
);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`, `category_id`, `created_at`) VALUES
(1, 'Coffee', 25.00, 'coffee.jpg', 1, '2026-03-12 22:34:56');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`) VALUES
(1, '100');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `room_id` int DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `room_id`, `image`, `role`, `created_at`) VALUES
(3, 'Admin User', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'admin', '2026-03-12 02:07:13'),
(4, 'Regular User', 'user@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'user', '2026-03-12 02:07:13'),
(5, 'usef mohamed', 'usef@gmail.com', '$2y$10$30ij.EHFw5uogxgB8zJ6aerQWAluWjwUvLswVkm3GE4txGpsgliDy', 1, '1773281343_69b2203f29391.png', 'user', '2026-03-12 00:09:03'),
(6, 'robaa', 'robaa@gmail.com', '$2y$10$BW0diZkJgG9ANrUpMrkYVuGd6yQjlg2ya7glKcCXotEjUK6wmw.3W', 1, '1773281573_69b221254cda7.png', 'user', '2026-03-12 00:12:53'),
(7, 'alaa', 'alaa@gmail.com', '$2y$12$7iHlVSsdIH1htH39HpckDOAKBp41Z39UiGVkNYZQH5nplAH8vZyW2', 1, '1773356199_69b344a735a5c.png', 'user', '2026-03-12 20:56:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

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
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `room_id` (`room_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

-- --------------------------------------------------------

--
-- Structure for view `orders_with_rooms`
--
DROP TABLE IF EXISTS `orders_with_rooms`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `orders_with_rooms`  AS SELECT `o`.`id` AS `id`, `o`.`user_id` AS `user_id`, `o`.`room_id` AS `room_id`, `o`.`notes` AS `notes`, `o`.`total_price` AS `total_price`, `o`.`status` AS `status`, `o`.`created_at` AS `created_at`, `r`.`name` AS `room_name` FROM (`orders` `o` left join `rooms` `r` on((`o`.`room_id` = `r`.`id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `orders_with_room_and_user`
--
DROP TABLE IF EXISTS `orders_with_room_and_user`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `orders_with_room_and_user`  AS SELECT `o`.`id` AS `id`, `o`.`user_id` AS `user_id`, `o`.`room_id` AS `room_id`, `o`.`notes` AS `notes`, `o`.`total_price` AS `total_price`, `o`.`status` AS `status`, `o`.`created_at` AS `created_at`, `r`.`name` AS `room_name`, `u`.`name` AS `user_name` FROM ((`orders` `o` left join `rooms` `r` on((`o`.`room_id` = `r`.`id`))) left join `users` `u` on((`o`.`user_id` = `u`.`id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `order_items_with_products`
--
DROP TABLE IF EXISTS `order_items_with_products`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `order_items_with_products`  AS SELECT `oi`.`id` AS `id`, `oi`.`order_id` AS `order_id`, `oi`.`product_id` AS `product_id`, `oi`.`quantity` AS `quantity`, `oi`.`price` AS `price`, `p`.`name` AS `product_name`, `p`.`image` AS `product_image` FROM (`order_items` `oi` left join `products` `p` on((`oi`.`product_id` = `p`.`id`))) ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
