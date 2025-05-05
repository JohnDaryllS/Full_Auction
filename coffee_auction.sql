-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2025 at 01:58 PM
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
-- Database: `coffee_auction`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_contacts`
--

CREATE TABLE `admin_contacts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL,
  `status` enum('unread','read') NOT NULL DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_contacts`
--

INSERT INTO `admin_contacts` (`id`, `user_id`, `name`, `email`, `subject`, `message`, `created_at`, `status`) VALUES
(1, NULL, 'John Daryll Ramos Sampilingan', 'johndaryllramos8@gmail.com', 'testing', 'testing lang to sana pumasok na walang error', '2025-05-01 14:12:58', 'read');

-- --------------------------------------------------------

--
-- Table structure for table `auction_types`
--

CREATE TABLE `auction_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) NOT NULL DEFAULT 'default-type.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auction_types`
--

INSERT INTO `auction_types` (`id`, `name`, `description`, `created_at`, `image`) VALUES
(2, 'Coffee Auction', 'Several identical bags or lots of coffee are sold, and bidders can choose how many units they want.', '2025-04-29 13:28:39', '6810f67bbdfab.jpeg'),
(3, 'Dog Auction', 'A champion show dog or a rare breed pup is auctioned off to the highest bidder.', '2025-04-29 13:39:02', '6810f66105d43.jpg'),
(4, 'Figures Auction', 'A rare 1/7 scale Saber figure from Fate/stay night is auctioned to the highest bidder.', '2025-04-29 13:40:06', '6810f5ee823ab.jpg'),
(6, 'Personal Computer Auction', 'Several identical or similar PCs are auctioned at once, and bidders can win more than one.', '2025-04-29 15:45:19', '6810f40f8d09e.jpg'),
(7, 'Cat Auction', 'Whether you\'re a seasoned breeder or looking for a lovable companion, CAT brings charm, beauty, and a dignified presence to any home.', '2025-05-02 06:25:40', '6814656464e46.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `bids`
--

CREATE TABLE `bids` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `bid_amount` decimal(10,2) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_anonymous` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bids`
--

INSERT INTO `bids` (`id`, `user_id`, `item_id`, `bid_amount`, `timestamp`, `is_anonymous`) VALUES
(16, 3, 9, 22.50, '2025-04-18 10:00:11', 0),
(18, 4, 9, 23.00, '2025-04-18 10:01:09', 0),
(20, 3, 9, 23.50, '2025-04-19 11:10:59', 1),
(21, 3, 14, 22.50, '2025-04-19 12:01:52', 1),
(22, 4, 14, 23.00, '2025-04-19 12:02:36', 1),
(23, 3, 9, 24.00, '2025-04-20 04:24:02', 1),
(24, 3, 11, 25.50, '2025-04-20 05:49:33', 1),
(25, 5, 9, 24.50, '2025-04-20 06:00:54', 1),
(26, 5, 10, 13.50, '2025-04-20 06:19:32', 1),
(27, 5, 10, 14.00, '2025-04-20 06:24:09', 1),
(28, 3, 16, 16.50, '2025-04-29 13:42:55', 0),
(29, 3, 12, 17.50, '2025-04-30 09:41:14', 1),
(30, 3, 12, 18.00, '2025-04-30 09:41:27', 0),
(31, 3, 16, 17.00, '2025-04-30 11:07:04', 0),
(32, 3, 16, 17.50, '2025-04-30 11:07:11', 0),
(33, 3, 24, 20000.00, '2025-05-01 12:17:31', 1),
(34, 5, 24, 25000.00, '2025-05-01 12:18:07', 0),
(35, 3, 24, 30000.00, '2025-05-01 12:18:54', 0),
(36, 3, 9, 25.00, '2025-05-02 04:18:23', 0);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `starting_price` decimal(10,2) NOT NULL,
  `bid_end_date` datetime DEFAULT NULL,
  `bid_start_date` datetime DEFAULT NULL,
  `is_limited` tinyint(1) DEFAULT 0,
  `notified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `type_id`, `name`, `image`, `description`, `starting_price`, `bid_end_date`, `bid_start_date`, `is_limited`, `notified`) VALUES
(9, 2, 'Sumatra Mandheling Organic', '680215d81167d.jpg', 'Earthy and low-acidity coffee sourced from the lush mountains of Sumatra. This organic roast delivers deep, syrupy notes of dark chocolate, cedar, and spice—perfect for a bold cup.', 23.00, '2025-05-02 21:01:00', '2025-04-30 14:34:00', 0, 1),
(10, 2, 'Brazil Santos Light Roast', '68022de9c34d3.jpg', 'A light and nutty roast with smooth body and mild acidity. Grown in the Santos region of Brazil, this coffee offers delicate flavors of caramel, almond, and a touch of citrus—ideal for mellow mornings.', 13.00, '2025-06-18 10:50:00', '2025-05-05 16:00:00', 0, 1),
(11, 2, 'Kenya AA Plus Peaberry', '680371b3eea5a.jpg', 'A rare peaberry coffee from Kenya, known for its vibrant acidity and wine-like undertones. This medium roast delivers juicy berry notes with a smooth, bright finish—perfect for pour-overs or cold brew.', 25.00, '2025-10-22 12:48:00', '2025-06-01 06:00:00', 1, 1),
(12, 2, 'Honduras Marcala Honey Process', '6803720360a9c.jpg', 'A specialty coffee with a sweet and creamy profile, thanks to the honey processing method. Grown in the highlands of Marcala, it features smooth notes of honey, red apple, and vanilla with a silky body.', 17.00, '2025-05-24 08:50:00', '2025-04-30 16:20:00', 1, 0),
(13, 2, 'Ethiopia Yirgacheffe Floral Roast', '680372505bfe5.jpg', 'A bright, aromatic coffee from the Yirgacheffe region of Ethiopia. This light roast boasts floral notes of jasmine and lavender, with a tea-like body and hints of lemon zest—an elegant and refreshing cup.', 20.00, '2025-05-02 06:51:00', '2025-04-30 18:00:00', 0, 0),
(14, 2, 'Sumatra Mandheling Earthy Roast', '680372a7cd5ae.jpg', 'An intense, low-acidity coffee from the Indonesian highlands. This medium-dark roast features bold earthy tones, dark chocolate, and a touch of spice—perfect for espresso or a strong morning brew.', 22.00, '2025-05-23 01:53:00', '2025-05-05 16:00:00', 0, 1),
(15, 2, 'Panama Geisha Elite Bloom', '68049579547f9.png', 'World-renowned for its floral aroma and complex flavors, this Geisha variety offers a silky body with jasmine, bergamot, and apricot tasting notes. A luxurious cup for true connoisseurs.', 45.00, '2025-05-30 12:00:00', '2025-05-17 06:00:00', 0, 0),
(16, 2, 'Brazil Cerrado Nutty Roast', '680495ebc0565.jpg', 'A smooth and balanced roast with notes of roasted hazelnut, caramel, and milk chocolate. Perfect for drip or French press, this Brazilian coffee delivers a consistent, comforting brew.', 16.00, '2025-05-16 22:35:00', '2025-04-30 15:18:00', 1, 1),
(17, 2, 'Costa Rica Tarrazú Citrus Wave', '68049683879cb.png', 'Clean, crisp, and zesty. This light-medium roast bursts with bright citrus notes, red currant, and a hint of honey. Grown in high altitudes for a vibrant cup.Clean, crisp, and zesty. This light-medium roast bursts with bright citrus notes, red currant, and a hint of honey. Grown in high altitudes for a vibrant cup.', 19.00, '2025-11-20 00:37:00', '2025-06-21 06:00:00', 0, 0),
(18, 2, 'Rwanda Bourbon Red', '680496db2c0e6.jpg', 'Grown on the rich volcanic soils of Rwanda, this bourbon varietal shines with cranberry, pomegranate, and floral hints. Great for adventurous palates.', 20.97, '2025-05-10 12:40:00', '2025-05-01 09:19:00', 0, 0),
(19, 2, 'Java Estate Midnight Roast', '68049741d7500.jpg', 'A bold Indonesian dark roast with a velvety body, smoky aroma, and flavors of molasses, tobacco, and cedar. Strong and smooth, ideal for night owls.', 20.00, '2025-05-15 14:41:00', '2025-04-30 13:23:00', 0, 0),
(20, 2, 'Guatemala Huehuetenango Sweet Harmony', '680497c13be12.jpg', 'Delicate and sweet with notes of brown sugar, red apple, and floral undertones. This medium roast from Huehuetenango offers balance and clarity in every sip.', 18.00, '2025-05-09 11:46:00', '2025-05-01 00:00:00', 0, 0),
(22, 6, 'PC ni Daryll', '68119ee7a9aad.jpg', '4 years pc karaan na kaayo pero good as new', 20000.00, '2025-05-08 03:53:00', '2025-05-01 03:53:00', 1, 0),
(23, 4, 'Iron Man Figures MK 2', '68121b4dd6617.png', 'Experience the origins of Tony Stark’s armored legacy with the Iron Man Mark II figure — a sleek, silver prototype suit that paved the way for all future upgrades. With its polished metallic finish, detailed mechanical design, and iconic arc reactor chest plate, this figure captures the raw innovation and brilliance of Stark’s early creation. Perfect for collectors, fans, and tech-inspired displays.', 2000.00, '2025-05-10 20:44:00', '2025-04-30 21:44:00', 1, 0),
(24, 3, 'Cardigan Welsh Corgi', '6813636c16be2.jpg', 'Cardigan Welsh Corgi – Slightly larger with a long, bushy tail.', 15000.00, '2025-05-01 20:21:00', '2025-05-01 20:10:00', 1, 1),
(25, 7, 'Maine Coon', '681465d0b2158.jpg', 'The Maine Coon is one of the largest domesticated cat breeds, known for its long, bushy tail, tufted ears, and rugged, thick fur coat that helps it withstand cold climates.', 10000.00, '2025-05-23 14:27:00', '2025-05-02 15:00:00', 1, 0),
(26, 7, 'Siamese', '681466234cd85.jpg', 'The Siamese cat is a sleek, elegant breed known for its striking blue almond-shaped eyes, short coat, and pointed color pattern (darker color on the ears, face, paws, and tail)', 12000.00, '2025-05-31 14:28:00', '2025-05-02 15:30:00', 1, 0),
(27, 7, 'Ragdoll', '6814669738f11.jpg', 'The Ragdoll is a large, affectionate, and strikingly beautiful cat breed known for its soft, semi-long coat and blue eyes. It gets its name from its tendency to go limp when picked up, like a ragdoll. Ragdolls are calm, gentle, and sociable, often following their owners from room to room.', 20000.00, '2025-05-30 14:30:00', '2025-05-03 14:30:00', 0, 0),
(28, 3, 'Golden Retriever', '681466ef38362.jpg', 'The Golden Retriever is a friendly, intelligent, and loyal breed known for its golden-colored coat and gentle demeanor. Originally bred for retrieving game during hunting, Goldens are now one of the most popular family dogs worldwide.', 5000.00, '2025-05-22 14:32:00', '2025-05-05 14:32:00', 0, 0),
(29, 6, 'SYSTEM UNIT', '6814675a87e7a.jpg', '✅Ryzen 5 3500 6c/6t\r\n✅Asus prime a320m-k\r\n✅Gloway 8gbx2=16gb ddr4 2666 mhz\r\n✅Playmaster thunder gpt1 450w 80+ bronze\r\n✅MSI GTX 1050 TI\r\n✅Sanc M2456H 24\" ips monitor 75 hz white\r\n✅Secure avr\r\n✅Inplay Meteor 06 white\r\n✅Ice Tower 6 in 1 rgb fans with remote', 20000.00, '2025-05-28 14:33:00', '2025-05-02 16:33:00', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 3, 'Welcome to Coffee Auction! Your account has been approved.', 1, '2025-04-20 00:00:00'),
(2, 3, 'You placed a bid of ₱22.50 on Sumatra Mandheling Organic', 1, '2025-04-20 00:05:00'),
(3, 4, 'Welcome to Coffee Auction! Your account has been approved.', 1, '2025-04-20 00:00:00'),
(4, 4, 'Someone outbid you on Sumatra Mandheling Organic with ₱23.00', 1, '2025-04-20 00:10:00'),
(5, 3, 'You placed a bid of ₱25.50 on Kenya AA Plus Peaberry', 1, '2025-04-20 05:49:33'),
(6, 5, 'Welcome to Coffee Auction! Your account is pending admin approval.', 1, '2025-04-20 05:53:28'),
(7, 5, 'Your account has been approved! You can now place bids.', 1, '2025-04-20 05:53:44'),
(8, 5, 'You placed a bid of ₱24.50 on Sumatra Mandheling Organic', 1, '2025-04-20 06:00:54'),
(9, 3, 'Someone outbid you on Sumatra Mandheling Organic with ₱24.50', 1, '2025-04-20 06:00:54'),
(10, 5, 'You placed a bid of ₱13.50 on Brazil Santos Light Roast', 1, '2025-04-20 06:19:32'),
(11, 5, 'You placed a bid of ₱14.00 on Brazil Santos Light Roast', 1, '2025-04-20 06:24:09'),
(12, 5, 'Your account has been suspended. Please contact support.', 1, '2025-04-20 09:29:04'),
(13, 5, 'Your account has been reactivated. Welcome back!', 1, '2025-04-20 09:29:09'),
(14, 4, 'Congratulations! You won the auction for Sumatra Mandheling Earthy Roast with a bid of ₱23.00', 1, '2025-04-26 13:39:08'),
(15, 3, 'Congratulations! You won the auction for Kenya AA Plus Peaberry with a bid of ₱25.50', 1, '2025-04-26 13:39:08'),
(16, 5, 'Congratulations! You won the auction for Sumatra Mandheling Organic with a bid of ₱24.50', 1, '2025-04-26 13:39:08'),
(17, 5, 'Congratulations! You won the auction for Brazil Santos Light Roast with a bid of ₱14.00', 1, '2025-04-26 13:39:08'),
(18, 5, 'Your account has been suspended. Please contact support.', 1, '2025-04-28 08:29:04'),
(19, 5, 'Your account has been reactivated. Welcome back!', 1, '2025-04-28 08:29:12'),
(20, 5, 'Your account has been suspended. Please contact support.', 1, '2025-04-28 10:50:58'),
(21, 5, 'Your account has been reactivated. Welcome back!', 1, '2025-04-28 10:51:01'),
(22, 5, 'Your account has been suspended. Please contact support.', 1, '2025-04-29 13:36:07'),
(23, 5, 'Your account has been reactivated. Welcome back!', 1, '2025-04-29 13:36:10'),
(24, 3, 'You placed a bid of ₱16.50 on Brazil Cerrado Nutty Roast', 1, '2025-04-29 13:42:55'),
(25, 3, 'Congratulations! You won the auction for Brazil Cerrado Nutty Roast with a bid of ₱16.50', 1, '2025-04-30 03:01:14'),
(26, 5, 'Your account has been suspended. Please contact support.', 1, '2025-04-30 07:54:00'),
(27, 5, 'Your account has been reactivated. Welcome back!', 1, '2025-04-30 07:54:03'),
(28, 3, 'You placed a bid of ₱17.50 on Honduras Marcala Honey Process', 1, '2025-04-30 09:41:14'),
(29, 3, 'You placed a bid of ₱18.00 on Honduras Marcala Honey Process', 1, '2025-04-30 09:41:27'),
(30, 5, 'Your password has been reset by an administrator.', 1, '2025-04-30 10:08:17'),
(31, 3, 'You placed a bid of ₱17.00 on Brazil Cerrado Nutty Roast', 1, '2025-04-30 11:07:04'),
(32, 3, 'You placed a bid of ₱17.50 on Brazil Cerrado Nutty Roast', 1, '2025-04-30 11:07:11'),
(33, 3, 'You placed a bid of ₱20,000.00 on Cardigan Welsh Corgi', 1, '2025-05-01 12:17:31'),
(34, 5, 'You placed a bid of ₱25,000.00 on Cardigan Welsh Corgi', 1, '2025-05-01 12:18:07'),
(35, 3, 'Someone outbid you on Cardigan Welsh Corgi with ₱25,000.00', 1, '2025-05-01 12:18:07'),
(36, 3, 'You placed a bid of ₱30,000.00 on Cardigan Welsh Corgi', 1, '2025-05-01 12:18:54'),
(37, 5, 'Someone outbid you on Cardigan Welsh Corgi with ₱30,000.00', 0, '2025-05-01 12:18:54'),
(38, 3, 'Congratulations! You won the auction for Cardigan Welsh Corgi with a bid of ₱30,000.00', 1, '2025-05-01 12:21:31'),
(39, 3, 'You placed a bid of ₱25.00 on Sumatra Mandheling Organic', 1, '2025-05-02 04:18:23'),
(40, 5, 'Someone outbid you on Sumatra Mandheling Organic with ₱25.00', 0, '2025-05-02 04:18:23');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `category_id`, `rating`, `description`, `created_at`, `is_anonymous`) VALUES
(1, 3, 2, 5, 'Absolutely loved this Arabica blend! Smooth, rich, and not too acidic. The aroma alone is enough to make my day. Perfect for a calm morning.', '2025-04-30 10:33:26', 0),
(2, 5, 2, 5, 'A delightful mix of vanilla and hazelnut without overpowering the coffee. It’s my go-to for weekend mornings. Highly recommended!', '2025-04-30 12:06:49', 1),
(3, 4, 7, 5, 'The seller was communicative, and the whole auction process was smooth. Delivery was well-handled with care. Highly recommend!\"', '2025-05-02 06:48:46', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `status` enum('pending','approved','suspended') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `phone`, `password`, `role`, `status`, `created_at`) VALUES
(1, 'Admin One', 'admincoffeeauction1@coffeeauction.com', '1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'approved', '2025-04-17 10:31:47'),
(2, 'Admin Two', 'admincoffeeauction2@coffeeauction.com', '0987654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'approved', '2025-04-17 10:31:47'),
(3, 'John Daryll Sampilingan', 'johndaryllramos8@gmail.com', '09999422017', '$2y$10$dd4eg9NzvOcgVvxihgkf5.MZ4RnELHMPcN.FjH50IsF8/NzxFq2/S', 'user', 'approved', '2025-04-17 10:33:09'),
(4, 'Felsone Caragao', 'felsonecaragao@gmail.com', '09262891370', '$2y$10$6w61p./ZJaRrZhUsBsi5jeUeSnoz2tvUGXqQhypY0foAy5qow3VCW', 'user', 'approved', '2025-04-17 14:20:10'),
(5, 'Johnny Gayo', 'johnnygayo@gmail.com', '09066043962', '$2y$10$l0Vdly/Jr4Nbc3KCiSF4V.o0Q1sMGJuxq8e0LQOu9XZ4xsJN/ftce', 'user', 'approved', '2025-04-20 05:53:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_contacts`
--
ALTER TABLE `admin_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `auction_types`
--
ALTER TABLE `auction_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bids`
--
ALTER TABLE `bids`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_item_type` (`type_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_contacts`
--
ALTER TABLE `admin_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `auction_types`
--
ALTER TABLE `auction_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `bids`
--
ALTER TABLE `bids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_contacts`
--
ALTER TABLE `admin_contacts`
  ADD CONSTRAINT `admin_contacts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bids`
--
ALTER TABLE `bids`
  ADD CONSTRAINT `bids_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bids_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `fk_item_type` FOREIGN KEY (`type_id`) REFERENCES `auction_types` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `auction_types` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
