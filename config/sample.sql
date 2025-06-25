-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 20 Jun 2025 pada 17.16
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ppw-mirna-uas-potionpantry`
--

DELIMITER $$
--
-- Prosedur
--
CREATE DEFINER=`u985354573_ayu_mirna`@`localhost` PROCEDURE `GetUserStats` (IN `p_user_id` INT)   BEGIN
    SELECT 
        (SELECT COUNT(*) FROM Products WHERE user_id = p_user_id) as total_products,
        (SELECT COALESCE(SUM(price), 0) FROM Products WHERE user_id = p_user_id) as total_value,
        (SELECT COUNT(DISTINCT ul.product_id) 
         FROM UsageLog ul 
         JOIN Products p ON ul.product_id = p.product_id 
         WHERE p.user_id = p_user_id AND ul.usage_date = CURDATE()) as used_today,
        (SELECT COUNT(*) 
         FROM Products 
         WHERE user_id = p_user_id 
         AND expiration_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 180 DAY)) as expiring_soon;
END$$

CREATE DEFINER=`u985354573_ayu_mirna`@`localhost` PROCEDURE `LogProductUsage` (IN `p_product_id` INT, IN `p_usage_date` DATE, IN `p_time_of_day` VARCHAR(10), IN `p_notes` TEXT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    INSERT INTO UsageLog (product_id, usage_date, time_of_day, notes) 
    VALUES (p_product_id, p_usage_date, p_time_of_day, p_notes);
    
    UPDATE Products 
    SET last_used_at = p_usage_date 
    WHERE product_id = p_product_id;
    
    COMMIT;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `daily_usage_summary`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `daily_usage_summary` (
`usage_date` date
,`user_id` int(11)
,`num_unique_products_used` bigint(21)
,`total_usage_count` bigint(21)
,`products_used` mediumtext
,`times_of_day` mediumtext
);

-- --------------------------------------------------------

--
-- Struktur dari tabel `ingredients`
--

CREATE TABLE `ingredients` (
  `ingredient_id` int(11) NOT NULL,
  `ingredient_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `ingredients`
--

INSERT INTO `ingredients` (`ingredient_id`, `ingredient_name`, `description`, `created_at`) VALUES
(1, 'Hyaluronic Acid', 'Powerful humectant for hydration', '2025-06-20 08:15:13'),
(2, 'Ceramides', 'Lipids for skin barrier repair', '2025-06-20 08:15:13'),
(3, 'Salicylic Acid', 'BHA for exfoliation and pore cleansing', '2025-06-20 08:15:13'),
(4, 'Centella Asiatica', 'Soothing anti-inflammatory extract', '2025-06-20 08:15:13'),
(5, 'Rose Water', 'Gentle astringent and hydrating toner', '2025-06-20 08:15:13'),
(6, 'Niacinamide', 'Vitamin B3 for pore minimizing', '2025-06-20 08:15:13'),
(7, 'Glycerin', 'Humectant for moisture retention', '2025-06-20 08:15:13'),
(8, 'Vitamin C', 'Antioxidant for brightening', '2025-06-20 08:15:13'),
(9, 'Retinol', 'Vitamin A for anti-aging', '2025-06-20 08:15:13'),
(10, 'Tea Tree Oil', 'Antimicrobial for acne treatment', '2025-06-20 08:15:13'),
(11, 'Squalane', 'Lightweight moisturizing oil', '2025-06-20 08:15:13'),
(12, 'Panthenol', 'Pro-Vitamin B5 for soothing', '2025-06-20 08:15:13'),
(13, 'Peptides', 'Amino acids for collagen support', '2025-06-20 08:15:13'),
(14, 'AHA', 'Alpha Hydroxy Acids for surface exfoliation', '2025-06-20 08:15:13'),
(15, 'Zinc Oxide', 'Mineral sunscreen ingredient', '2025-06-20 08:15:13'),
(16, 'Snail Secretion', 'Healing and hydrating ingredient', '2025-06-20 08:15:13'),
(17, 'Collagen', 'Protein for skin firmness', '2025-06-20 08:15:13'),
(18, 'Azelaic Acid', 'Multi-functional acid for acne and brightening', '2025-06-20 08:15:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `productingredients`
--

CREATE TABLE `productingredients` (
  `product_ingredient_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `productingredients`
--

INSERT INTO `productingredients` (`product_ingredient_id`, `product_id`, `ingredient_id`, `created_at`) VALUES
(73, 46, 1, '2025-06-20 10:36:28'),
(76, 47, 9, '2025-06-20 13:18:18'),
(77, 47, 5, '2025-06-20 13:18:18'),
(78, 38, 13, '2025-06-20 13:40:48'),
(79, 38, 5, '2025-06-20 13:40:48'),
(80, 38, 10, '2025-06-20 13:40:48'),
(81, 39, 2, '2025-06-20 13:41:06'),
(82, 39, 7, '2025-06-20 13:41:06'),
(83, 39, 5, '2025-06-20 13:41:06'),
(84, 40, 8, '2025-06-20 13:41:25'),
(85, 40, 15, '2025-06-20 13:41:25'),
(86, 41, 17, '2025-06-20 13:41:50'),
(87, 41, 13, '2025-06-20 13:41:50'),
(88, 41, 11, '2025-06-20 13:41:50'),
(89, 42, 6, '2025-06-20 13:42:24'),
(90, 43, 18, '2025-06-20 13:42:42'),
(91, 43, 4, '2025-06-20 13:42:42'),
(92, 43, 6, '2025-06-20 13:42:42'),
(93, 43, 13, '2025-06-20 13:42:42'),
(94, 44, 1, '2025-06-20 13:42:55'),
(95, 45, 11, '2025-06-20 13:43:05'),
(96, 45, 15, '2025-06-20 13:43:05'),
(97, 48, 17, '2025-06-20 14:05:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `brand` varchar(255) NOT NULL,
  `product_type` varchar(100) NOT NULL,
  `purchase_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `price` decimal(12,2) DEFAULT 0.00,
  `volume_ml` int(11) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `last_used_at` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`product_id`, `user_id`, `product_name`, `brand`, `product_type`, `purchase_date`, `expiration_date`, `price`, `volume_ml`, `notes`, `image_path`, `last_used_at`, `created_at`, `updated_at`) VALUES
(28, 1, 'Calming Cream', 'Elsheskin', 'Moisturizer', '2025-06-20', '2027-12-20', 120000.00, 30, 'Pelembab untuk menenangkan kulit kemerahan dan sensitif.', '', NULL, '2025-06-20 08:49:31', '2025-06-20 08:49:31'),
(29, 1, 'Level 1% Retinol', 'Somethinc', 'Serum', '2025-05-15', '2026-11-15', 159000.00, 20, 'Serum retinol untuk pemula, membantu mengurangi garis halus dan memperbaiki tekstur kulit.', '', NULL, '2025-06-20 08:49:31', '2025-06-20 08:49:31'),
(30, 1, 'Miraculous Refining Toner', 'Avoskin', 'Toner', '2025-04-10', '2027-04-10', 189000.00, 100, 'Toner eksfoliasi untuk mengangkat sel kulit mati dan mencerahkan.', '', NULL, '2025-06-20 08:49:31', '2025-06-20 08:49:31'),
(31, 1, 'Everyday Sunscreen SPF 50 PA++++', 'Ourdaylee', 'Sunscreen', '2025-06-01', '2027-01-01', 85000.00, 50, 'Sunscreen ringan tidak lengket, cocok untuk penggunaan sehari-hari.', '', NULL, '2025-06-20 08:49:31', '2025-06-20 08:49:31'),
(32, 1, 'Brightening Facial Wash', 'Azarine', 'Cleanser', '2025-05-20', '2027-05-20', 49000.00, 100, 'Pembersih wajah untuk mencerahkan dan membersihkan secara menyeluruh.', '', NULL, '2025-06-20 08:49:31', '2025-06-20 08:49:31'),
(33, 1, 'Pure Glow Serum', 'Scarlett Whitening', 'Serum', '2025-03-25', '2026-09-25', 75000.00, 30, 'Serum untuk kulit kusam, membantu memberikan efek glowing.', '', NULL, '2025-06-20 08:49:31', '2025-06-20 08:49:31'),
(34, 1, 'Hydrating Gel Moisturizer', 'Hada Labo', 'Moisturizer', '2025-02-18', '2027-08-18', 65000.00, 50, 'Pelembab gel dengan Hyaluronic Acid untuk hidrasi intensif.', '', NULL, '2025-06-20 08:49:31', '2025-06-20 08:49:31'),
(35, 1, 'Acne Treatment Spot Gel', 'MS GLOW', 'Treatment', '2025-01-30', '2026-07-30', 90000.00, 15, 'Gel totol jerawat untuk meredakan peradangan.', '', NULL, '2025-06-20 08:49:31', '2025-06-20 08:49:31'),
(36, 1, 'Daily Acne Facial Foam', 'ERHA', 'Cleanser', '2025-06-10', '2027-06-10', 80000.00, 100, 'Pembersih wajah khusus untuk kulit berjerawat.', '', NULL, '2025-06-20 08:49:31', '2025-06-20 08:49:31'),
(37, 1, 'Intensive Brightening Essence', 'Wardah', 'Essence', '2025-04-05', '2026-10-05', 70000.00, 50, 'Esensi pencerah untuk tampilan kulit yang lebih cerah dan sehat.', '', NULL, '2025-06-20 08:49:31', '2025-06-20 08:49:31'),
(38, 2, 'Triple Glow Essence Toner', 'Facetology', 'Toner', '2025-06-18', '2027-06-18', 79000.00, 100, 'Toner pencerah dengan 3in1 benefits, sangat viral.', '', NULL, '2025-06-20 09:19:59', '2025-06-20 13:40:48'),
(39, 2, 'Triple Purity Cleanser', 'Facetology', 'Cleanser', '2025-05-10', '2025-11-10', 69000.00, 100, 'Pembersih wajah gentle, perhatikan tanggal kadaluarsa.', '', '2025-06-20', '2025-06-20 09:19:59', '2025-06-20 13:43:42'),
(40, 2, 'All-Day Glow Sunscreen SPF 50 PA++++', 'Facetology', 'Sunscreen', '2025-04-20', '2025-10-20', 89000.00, 50, 'Sunscreen hybrid viral, mendekati kadaluarsa.', '', NULL, '2025-06-20 09:19:59', '2025-06-20 13:41:25'),
(41, 2, 'UV Sunscreen Serum SPF 50 PA++++', 'Amaterasun', 'Sunscreen', '2025-06-01', '2027-06-01', 119000.00, 30, 'Sunscreen serum ringan, no whitecast.', '', NULL, '2025-06-20 09:19:59', '2025-06-20 13:41:50'),
(42, 2, 'Acne Care Sunscreen SPF 35 PA+++', 'Amaterasun', 'Sunscreen', '2025-03-01', '2025-09-01', 99000.00, 30, 'Sunscreen untuk kulit berjerawat, segera habiskan.', '', NULL, '2025-06-20 09:19:59', '2025-06-20 13:42:24'),
(43, 2, 'Brightening Cica Serum', 'Facetology', 'Serum', '2025-06-05', '2027-06-05', 110000.00, 20, 'Serum dengan Cica dan Niacinamide untuk menenangkan dan mencerahkan.', '', NULL, '2025-06-20 09:19:59', '2025-06-20 13:42:42'),
(44, 2, 'Hydra Lock Moisturizer', 'Facetology', 'Moisturizer', '2025-05-25', '2027-05-25', 95000.00, 30, 'Moisturizer untuk mengunci kelembapan.', '', NULL, '2025-06-20 09:19:59', '2025-06-20 13:42:55'),
(45, 2, 'Physical Sunscreen SPF 50 PA++++', 'Amaterasun', 'Sunscreen', '2025-06-12', '2027-06-12', 135000.00, 50, 'Sunscreen physical untuk kulit sensitif.', '', '2025-06-20', '2025-06-20 09:19:59', '2025-06-20 13:43:33'),
(46, 1, 'Hyaluronic Acid Serum', 'SOMETHINC', 'Serum', '2025-06-20', '2027-06-20', 50000.00, 30, 'belum dicoba', '', NULL, '2025-06-20 10:36:28', '2025-06-20 10:36:28'),
(47, 7, 'Khaf Wash Face', 'Khaf', 'Cleanser', '2025-06-20', '2025-06-21', 90000.00, 200, 'mundak', 'product_68555f325ca4e.jpg', '2025-06-20', '2025-06-20 13:16:34', '2025-06-20 13:18:18'),
(48, 1, 'Hyaluronic Acid Serum', 'Avoskin', 'Serum', '2025-06-20', '2025-07-20', 76000.00, 30, '', '', NULL, '2025-06-20 14:05:16', '2025-06-20 14:05:16'),
(49, 1, 'Khaf Wash Face', 'Khaf', 'Cleanser', '2025-06-20', '2025-12-20', 90000.00, 100, '', '', '2025-06-20', '2025-06-20 14:10:00', '2025-06-20 14:11:17');

--
-- Trigger `products`
--
DELIMITER $$
CREATE TRIGGER `before_products_update` BEFORE UPDATE ON `products` FOR EACH ROW BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `products_about_to_expire`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `products_about_to_expire` (
`product_id` int(11)
,`user_id` int(11)
,`product_name` varchar(255)
,`brand` varchar(255)
,`product_type` varchar(100)
,`expiration_date` date
,`days_until_expiration` int(7)
,`price` decimal(12,2)
,`volume_ml` int(11)
,`last_used_at` date
);

-- --------------------------------------------------------

--
-- Struktur dari tabel `usagelog`
--

CREATE TABLE `usagelog` (
  `usage_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `usage_date` date NOT NULL,
  `time_of_day` enum('Morning','Evening','Both') NOT NULL DEFAULT 'Morning',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `usagelog`
--

INSERT INTO `usagelog` (`usage_id`, `product_id`, `usage_date`, `time_of_day`, `notes`, `created_at`) VALUES
(29, 47, '2025-06-20', 'Both', '', '2025-06-20 13:17:09'),
(30, 45, '2025-06-20', 'Morning', '', '2025-06-20 13:43:33'),
(31, 39, '2025-06-20', 'Evening', '', '2025-06-20 13:43:42'),
(32, 49, '2025-06-20', 'Evening', '', '2025-06-20 14:10:47');

--
-- Trigger `usagelog`
--
DELIMITER $$
CREATE TRIGGER `after_usage_update_last_used_at` AFTER INSERT ON `usagelog` FOR EACH ROW BEGIN
    UPDATE Products 
    SET last_used_at = NEW.usage_date 
    WHERE product_id = NEW.product_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `email`, `created_at`, `updated_at`) VALUES
(1, 'demo', '$2y$10$shtRrd/IA2miFsH8TjOvievEZc3A7aGscDnboJ50BH3Gs0VLcbvAm', 'demo@potionpantry.com', '2025-06-20 08:42:31', '2025-06-20 08:46:57'),
(2, 'miwaa', '$2y$10$rNvUlVbjg1op1XO7tTD.zOCSMobhhxM9qIcQBdt/Au84Ma3g15Q4W', 'mirna@potionpantry.com', '2025-06-20 08:23:47', '2025-06-20 09:19:47'),
(7, 'bangjan', '$2y$10$ClaRDqtFV/xI7DyHkP3eD.cf1s.wl/zSZ51R7MxW4IoIXQSc.effi', 'bangjan@mail.com', '2025-06-20 13:14:35', '2025-06-20 13:14:35');

-- --------------------------------------------------------

--
-- Struktur untuk view `daily_usage_summary`
--
DROP TABLE IF EXISTS `daily_usage_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u985354573_ayu_mirna`@`localhost` SQL SECURITY DEFINER VIEW `daily_usage_summary`  AS SELECT `ul`.`usage_date` AS `usage_date`, `p`.`user_id` AS `user_id`, count(distinct `ul`.`product_id`) AS `num_unique_products_used`, count(`ul`.`usage_id`) AS `total_usage_count`, group_concat(distinct `p`.`product_name` order by `p`.`product_name` ASC separator ', ') AS `products_used`, group_concat(distinct `ul`.`time_of_day` order by `ul`.`time_of_day` ASC separator ', ') AS `times_of_day` FROM (`usagelog` `ul` join `products` `p` on(`ul`.`product_id` = `p`.`product_id`)) GROUP BY `ul`.`usage_date`, `p`.`user_id` ORDER BY `ul`.`usage_date` DESC ;

-- --------------------------------------------------------

--
-- Struktur untuk view `products_about_to_expire`
--
DROP TABLE IF EXISTS `products_about_to_expire`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u985354573_ayu_mirna`@`localhost` SQL SECURITY DEFINER VIEW `products_about_to_expire`  AS SELECT `p`.`product_id` AS `product_id`, `p`.`user_id` AS `user_id`, `p`.`product_name` AS `product_name`, `p`.`brand` AS `brand`, `p`.`product_type` AS `product_type`, `p`.`expiration_date` AS `expiration_date`, to_days(`p`.`expiration_date`) - to_days(curdate()) AS `days_until_expiration`, `p`.`price` AS `price`, `p`.`volume_ml` AS `volume_ml`, `p`.`last_used_at` AS `last_used_at` FROM `products` AS `p` WHERE `p`.`expiration_date` between curdate() and curdate() + interval 6 month ORDER BY `p`.`expiration_date` ASC ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`ingredient_id`),
  ADD UNIQUE KEY `ingredient_name` (`ingredient_name`),
  ADD KEY `idx_ingredient_name` (`ingredient_name`);

--
-- Indeks untuk tabel `productingredients`
--
ALTER TABLE `productingredients`
  ADD PRIMARY KEY (`product_ingredient_id`),
  ADD UNIQUE KEY `unique_product_ingredient` (`product_id`,`ingredient_id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_ingredient_id` (`ingredient_id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_product_type` (`product_type`),
  ADD KEY `idx_expiration_date` (`expiration_date`),
  ADD KEY `idx_last_used_at` (`last_used_at`),
  ADD KEY `idx_products_user_expiry` (`user_id`,`expiration_date`),
  ADD KEY `idx_products_user_type` (`user_id`,`product_type`);

--
-- Indeks untuk tabel `usagelog`
--
ALTER TABLE `usagelog`
  ADD PRIMARY KEY (`usage_id`),
  ADD UNIQUE KEY `unique_product_date_time` (`product_id`,`usage_date`,`time_of_day`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_usage_date` (`usage_date`),
  ADD KEY `idx_time_of_day` (`time_of_day`),
  ADD KEY `idx_usage_product_date` (`product_id`,`usage_date`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `ingredient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `productingredients`
--
ALTER TABLE `productingredients`
  MODIFY `product_ingredient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT untuk tabel `usagelog`
--
ALTER TABLE `usagelog`
  MODIFY `usage_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `productingredients`
--
ALTER TABLE `productingredients`
  ADD CONSTRAINT `productingredients_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `productingredients_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`ingredient_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `usagelog`
--
ALTER TABLE `usagelog`
  ADD CONSTRAINT `usagelog_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
