-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 02, 2025 at 01:36 PM
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
-- Database: `hotelco`
--

-- --------------------------------------------------------

--
-- Table structure for table `amenities`
--

CREATE TABLE `amenities` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `amenities`
--

INSERT INTO `amenities` (`id`, `name`) VALUES
(5, '24-Hour Front Desk'),
(8, 'Bar'),
(10, 'Business Center'),
(3, 'Fitness Center'),
(6, 'Free Parking'),
(1, 'Free Wi-Fi'),
(7, 'Restaurant'),
(9, 'Room Service'),
(4, 'Spa & Wellness'),
(2, 'Swimming Pool');

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) DEFAULT NULL,
  `room_type_id` int(11) DEFAULT NULL,
  `discount_percentage` decimal(5,2) NOT NULL,
  `status` enum('inactive','active') NOT NULL DEFAULT 'inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) NOT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `map_url` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `website` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `owner_id`, `name`, `description`, `address`, `city`, `state`, `country`, `zip_code`, `map_url`, `phone`, `email`, `website`, `created_at`, `status`) VALUES
(2, 1, 'Centara Grand at Central Plaza Ladprao Bangkok', 'Stay luxuriously at Centara Grand at Central Plaza Ladprao Bangkok. Enjoy stunning views, top-tier restaurants, outdoor pool, award-winning spa, and convenient access to nearby attractions in Chatuchak. Create unforgettable memories at Centara Grand. Experience exclusive amenities at Centara Grand at Central Plaza Ladprao Bangkok. Enjoy a delicious breakfast, relax by the pool, and rejuvenate in the steam room. Stay connected with free Wi-Fi and enjoy modern comforts like AC and in-room safes. Some rooms offer serene views of the garden, park, or street. Explore nearby Chatuchak Park and its botanical gardens. Let the guided tours help you discover local attractions. Centara Grand guarantees a memorable and unique experience for two travelers, whether for business or leisure.', '1695 Phaholyothin Road', 'Chatuchak', 'Bangkok', 'Thailand', '10900', 'https://maps.app.goo.gl/yL5sSLjLmDok2NM56', '025-411-234', 'cglb@chr.co.th\r\n', 'https://www.centarahotelsresorts.com/centaragrand/th/cglb', '2025-02-24 16:06:31', 'inactive'),
(3, 3, 'U Nimman Chiang Mai', 'U Nimman Chiang Mai is a 5-star boutique hotel with luxurious suites, exquisite dining, and an outdoor pool. Enjoy stunning mountain views and easy access to trendy shopping, dining, and nightlife. Discover the trendy neighborhood of Nimmanhemin in Chiang Mai, known for its hip cafes, boutiques, and vibrant nightlife. Indulge in a takeaway breakfast, relax by the mesmerizing pool, and unwind in the sauna at this exclusive property. Enjoy the comfort of air conditioning, a private balcony/terrace, and complimentary Wi-Fi. Explore nearby landmarks such as Kad Suan Kaew shopping center, Wat Phra Singh (Gold Temple), and Wat Suan Dok. Book your perfect retreat at U Nimman Chiang Mai today!', '1 Nimmanhaemin Road', 'Tambon Suthep', 'Chiang Mai', 'Thailand', '50200', 'https://maps.app.goo.gl/oNabBfoSrKm2khiG8', '052-005-111', 'reserve@unimmanchiangmai.com', 'https://www.uhotelsresorts.com/unimmanchiangmai', '2025-02-24 16:55:09', 'active'),
(4, 3, 'Eastin Tan Hotel Chiang Mai', 'Eastin Tan Hotel Chiang Mai: Free Wi-Fi, deluxe amenities, extended breakfast hours. Near trendy restaurants, cafes, and bars on Nimanhemin Road. Explore vibrant Chiang Mai attractions. Try the chic 1920s American styled T Station Bar & Restaurant. Discover the trendy neighborhood of Nimmanhemin in Chiang Mai, known for its hip cafes, boutiques, and vibrant nightlife. The hotel features an indoor pool, steam room, and a bar for you to relax and unwind. Explore local attractions with the tour services. Experience utmost comfort with air conditioning in the rooms, along with complimentary Wi-Fi and freshening up with complimentary toiletries. Conveniently located near Kad Suan Kaew shopping center, Wat Suan Dok, and Wat Lok Molee, there is plenty to explore and experience during your stay.', '171 Huay Kaew Road', 'Tambon Suthep', 'Chiang Mai', 'Thailand', '50200', 'https://maps.app.goo.gl/Xk3GDmiYMEf3REym6', '052-001-999', 'rsvn@eastintanchiangmai.com', 'https://www.eastinhotelsresidences.com/eastintanchiangmai', '2025-02-28 16:36:06', 'active'),
(5, 1, 'Grande Centre Point Pattaya', 'Experience a unique \'Space\' themed stay for two at Grande Centre Point Pattaya. Enjoy delicious dining at Waves & Wind and The Sky 32 restaurants. Stay fit at the state-of-the-art Fit Club and host your own events with cutting-edge technology. Stay in North Pattaya at Grande Centre Point Pattaya. Enjoy stunning beaches, lively markets, and thrilling nightlife. Relax by the pool, savor exquisite meals, and rejuvenate with massages. Air-conditioned rooms feature private balconies, complimentary Wi-Fi, invigorating showers, and luxury toiletries. Explore nearby attractions including Wong Amat Beach, Central Festival, and Terminal 21 Pattaya. Create unforgettable memories at Grande Centre Point Pattaya.', '456, 777, 777/1 Moo 6, Na Kluea, Bang Lamung', 'Pattaya', 'Chon Buri', 'Thailand', '20150', 'https://maps.app.goo.gl/ajipvq8dLL5UnXd76', '033-168-999', 'pattaya@gcphotels.com', 'https://www.grandecentrepointpattaya.com/', '2025-03-01 11:23:52', 'active'),
(6, 1, 'Kalima Resort & Spa', 'Indulge in luxury and relaxation at Kalima Resort & Spa in Phuket. Stay in spacious rooms with balconies and stunning views of Patong Bay. Enjoy the beach, spa, and unique dining experiences. Perfect for a memorable solo getaway. Experience luxury and convenience at Kalima Resort & Spa in vibrant Patong, Phuket. With stunning views, a stylish bar, and complimentary Wi-Fi in air-conditioned rooms, this resort is perfect for solo travelers. Indulge in a rejuvenating spa, relax at the artificial beach, and explore the vibrant nightlife, pristine beaches, and shopping and dining options. Make your stay in Phuket unforgettable at Kalima Resort & Spa.', '338/1 Prabaramee Road', 'Patong', 'Phuket', 'Thailand', '83150', 'https://maps.app.goo.gl/pfZmpJBW5ycRum8j8', '076 358 999', 'rsvn@kalimaresort.com', 'https://kalimaresort.com/homepage', '2025-03-02 02:55:29', 'inactive'),
(7, 3, 'Avani+ Riverside Bangkok Hotel', 'Avani+ Riverside Bangkok Hotel offers a regenerative holiday experience for solo travelers with panoramic river views, chic design, and outstanding amenities including restaurants, retailers, and a world-class spa. Located in Bangkok Riverside, this property offers stunning views of the Chao Phraya River. Enjoy 24/7 security, food delivery, and a refreshing pool. The air-conditioned rooms come with complimentary Wi-Fi, invigorating shower, and luxurious toiletries. Explore Asiatique - The Riverfront, steps away from the hotel. Shop at nearby shopping centers. [Some content may be Generative AI assisted. Inaccuracies may occur.]', '257 Charoennakorn Road', 'Thonburi', 'Bangkok', 'Thailand', '10600', NULL, NULL, NULL, NULL, '2025-03-02 03:57:47', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_amenities`
--

CREATE TABLE `hotel_amenities` (
  `hotel_id` int(11) NOT NULL,
  `amenity_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotel_amenities`
--

INSERT INTO `hotel_amenities` (`hotel_id`, `amenity_id`) VALUES
(2, 4),
(2, 5),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(3, 6),
(3, 8),
(3, 10),
(7, 3),
(7, 5),
(7, 7);

-- --------------------------------------------------------

--
-- Table structure for table `hotel_images`
--

CREATE TABLE `hotel_images` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotel_images`
--

INSERT INTO `hotel_images` (`id`, `hotel_id`, `image_url`, `is_primary`, `created_at`) VALUES
(1, 2, '/bucket/hotel-2/main/landscape.jpg', 1, '2025-02-24 17:46:36'),
(2, 3, '/bucket/hotel-3/main/exterior.jpg', 1, '2025-02-24 17:47:57'),
(3, 3, '/bucket/hotel-3/main/restaurant.jpg', 0, '2025-02-26 07:33:32'),
(4, 4, '/bucket/hotel-4/main/exterior.jpg', 1, '2025-02-28 16:39:04'),
(5, 5, '/bucket/hotel-5/main/exterior.jpg', 1, '2025-03-01 11:27:50'),
(6, 4, '/bucket/hotel-4/main/1740903709_room.jpg', 0, '2025-03-02 08:21:49'),
(7, 7, '/bucket/hotel-7/main/1740905229_avani_plus_riverside_bangkok_hotel_pool_1920x600.jpg', 0, '2025-03-02 08:47:09');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` enum('credit_card','cash') NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `reservation_id`, `amount`, `payment_date`, `payment_method`, `status`) VALUES
(1, 9, 6500.00, '2025-03-01 04:43:59', 'cash', 'pending'),
(2, 10, 6500.00, '2025-03-01 06:12:04', 'cash', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `hotel_id`, `check_in`, `check_out`, `total_price`, `status`, `created_at`) VALUES
(1, 3, 3, '2025-03-01', '2025-03-03', 9100.00, 'confirmed', '2025-02-28 17:17:34'),
(2, 3, 3, '2025-03-01', '2025-03-03', 9100.00, 'confirmed', '2025-02-28 17:29:09'),
(5, 1, 3, '2025-03-01', '2025-03-05', 5000.00, 'pending', '2025-02-28 17:48:53'),
(7, 3, 3, '2025-03-01', '2025-03-03', 4550.00, 'pending', '2025-02-28 18:28:21'),
(8, 3, 3, '2025-03-04', '2025-03-05', 6500.00, 'pending', '2025-03-01 03:42:53'),
(9, 3, 3, '2025-03-01', '2025-03-03', 6500.00, 'pending', '2025-03-01 04:43:59'),
(10, 3, 3, '2025-03-01', '2025-03-03', 6500.00, 'pending', '2025-03-01 06:12:04');

-- --------------------------------------------------------

--
-- Table structure for table `reservation_rooms`
--

CREATE TABLE `reservation_rooms` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation_rooms`
--

INSERT INTO `reservation_rooms` (`id`, `reservation_id`, `room_id`, `price`, `created_at`) VALUES
(1, 9, 1, 6500.00, '2025-03-01 04:43:59'),
(2, 10, 1, 6500.00, '2025-03-01 06:12:04');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` decimal(2,1) DEFAULT NULL CHECK (`rating` between 1.0 and 5.0),
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `hotel_id`, `user_id`, `rating`, `review_text`, `created_at`) VALUES
(1, 3, 1, 5.0, 'Good!', '2025-03-01 10:47:11');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `room_number` varchar(50) DEFAULT NULL,
  `status` enum('available','maintenance','occupied') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `hotel_id`, `room_type_id`, `room_number`, `status`, `created_at`) VALUES
(1, 3, 1, '101', 'available', '2025-03-01 04:18:38'),
(3, 3, 1, '102', 'available', '2025-03-02 09:27:31'),
(4, 3, 2, '103', 'available', '2025-03-02 10:57:27'),
(5, 4, 4, '101', 'available', '2025-03-02 11:56:40'),
(7, 4, 4, '102', 'available', '2025-03-02 12:31:11'),
(8, 3, 3, '104', 'available', '2025-03-02 12:31:27');

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `capacity` int(11) DEFAULT 2,
  `base_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `hotel_id`, `name`, `description`, `capacity`, `base_price`, `created_at`) VALUES
(1, 3, 'Deluxe', '', 2, 6500.00, '2025-02-25 06:18:47'),
(2, 3, 'Premium Deluxe', NULL, 2, 7000.00, '2025-02-25 06:19:50'),
(3, 3, 'เดี่ยว', '', 3, 1233.00, '2025-03-02 11:42:52'),
(4, 4, 'Test', '', 4, 1233.00, '2025-03-02 11:54:10');

-- --------------------------------------------------------

--
-- Table structure for table `room_type_amenities`
--

CREATE TABLE `room_type_amenities` (
  `room_type_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `amenity_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_type_amenities`
--

INSERT INTO `room_type_amenities` (`room_type_id`, `hotel_id`, `amenity_id`) VALUES
(1, 3, 9),
(2, 3, 4),
(2, 3, 6),
(2, 3, 9);

-- --------------------------------------------------------

--
-- Table structure for table `room_type_images`
--

CREATE TABLE `room_type_images` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `room_types_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_type_images`
--

INSERT INTO `room_type_images` (`id`, `hotel_id`, `room_types_id`, `image_url`, `is_primary`, `created_at`) VALUES
(1, 3, 1, '/bucket/hotel-3/rooms/deluxe/interior.jpg', 1, '2025-02-26 17:19:00'),
(2, 3, 2, '/bucket/hotel-3/rooms/premium-deluxe/interior.jpg', 1, '2025-02-26 19:06:20'),
(4, 3, 3, '/bucket/hotel-3/rooms/เดี่ยว/1740916356_room.jpg', 0, '2025-03-02 11:52:36');

-- --------------------------------------------------------

--
-- Table structure for table `support_requests`
--

CREATE TABLE `support_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_requests`
--

INSERT INTO `support_requests` (`id`, `user_id`, `name`, `email`, `message`, `status`, `created_at`) VALUES
(1, 1, 'PHANU SAWET', 'switzersawet@gmail.com', '1234', 'pending', '2025-03-01 11:40:00'),
(2, 1, 'PHANU SAWET', 'switzersawet@gmail.com', '123', 'resolved', '2025-03-01 11:40:38'),
(3, 1, 'PHANU SAWET', 'switzersawet@gmail.com', '1234', 'resolved', '2025-03-01 11:41:11'),
(4, 1, 'Phanu', 'switzersawet@gmail.com', '1234', 'pending', '2025-03-01 11:42:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','hotel_owner','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `created_at`, `role`) VALUES
(1, 'Phanu', 'johncena@lnwza.com', '$2y$10$2TM53ff.r949NO.ieBLnYO0ugxjJlVflvpjji5XY4w7arCO7ATT0S', '093-195-6230', '2025-03-01 08:41:13', 'admin'),
(2, 'Jamess', 'rock@rocking.com', '$2y$10$NccBCx2hljNo2J7VYKb0fOrgnl5r5wdgmc53V1E53YlpfAAqDSj3y', '123-123-1234', '2025-03-01 08:45:14', 'hotel_owner'),
(3, 'Jamesxx', 'steve@minecraft.com', '$2y$10$XmZIdLvZaqJySJVCSM5EteFxgoy9HkHWAirlGEkn4qVo5UXKX.GJu', '123-123-1234', '2025-03-02 02:34:59', 'hotel_owner');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `amenities`
--
ALTER TABLE `amenities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `hotel_amenities`
--
ALTER TABLE `hotel_amenities`
  ADD PRIMARY KEY (`hotel_id`,`amenity_id`),
  ADD KEY `amenity_id` (`amenity_id`);

--
-- Indexes for table `hotel_images`
--
ALTER TABLE `hotel_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `reservation_rooms`
--
ALTER TABLE `reservation_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `room_type_amenities`
--
ALTER TABLE `room_type_amenities`
  ADD PRIMARY KEY (`room_type_id`,`amenity_id`),
  ADD KEY `amenity_id` (`amenity_id`),
  ADD KEY `fk_room_type_amenities_hotel` (`hotel_id`);

--
-- Indexes for table `room_type_images`
--
ALTER TABLE `room_type_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_images_ibfk_1` (`room_types_id`),
  ADD KEY `fk_room_type_images_hotel` (`hotel_id`);

--
-- Indexes for table `support_requests`
--
ALTER TABLE `support_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `amenities`
--
ALTER TABLE `amenities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `hotel_images`
--
ALTER TABLE `hotel_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `reservation_rooms`
--
ALTER TABLE `reservation_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `room_type_images`
--
ALTER TABLE `room_type_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `support_requests`
--
ALTER TABLE `support_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `discounts`
--
ALTER TABLE `discounts`
  ADD CONSTRAINT `discounts_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discounts_ibfk_2` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hotels`
--
ALTER TABLE `hotels`
  ADD CONSTRAINT `hotels_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `hotel_amenities`
--
ALTER TABLE `hotel_amenities`
  ADD CONSTRAINT `hotel_amenities_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hotel_amenities_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hotel_images`
--
ALTER TABLE `hotel_images`
  ADD CONSTRAINT `hotel_images_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`);

--
-- Constraints for table `reservation_rooms`
--
ALTER TABLE `reservation_rooms`
  ADD CONSTRAINT `reservation_rooms_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_rooms_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rooms_ibfk_2` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_types`
--
ALTER TABLE `room_types`
  ADD CONSTRAINT `room_types_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_type_amenities`
--
ALTER TABLE `room_type_amenities`
  ADD CONSTRAINT `fk_room_type_amenities_hotel` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_type_amenities_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_type_amenities_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_type_images`
--
ALTER TABLE `room_type_images`
  ADD CONSTRAINT `fk_room_type_images_hotel` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_type_images_ibfk_1` FOREIGN KEY (`room_types_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_requests`
--
ALTER TABLE `support_requests`
  ADD CONSTRAINT `support_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
