-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.11.7-MariaDB-log - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for spd
CREATE DATABASE IF NOT EXISTS `spd_production` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `spd`;

-- Dumping structure for table spd_production.addons
CREATE TABLE IF NOT EXISTS `addons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table spd_production.addons: ~2 rows (approximately)
INSERT INTO `addons` (`id`, `name`, `description`, `price`, `image`, `status`, `created_at`) VALUES
	(1, 'White Frame 16x24', 'Premium white frame 16x24 inches', 120.00, 'frame16x24.jpg', 'active', '2026-02-03 08:35:51'),
	(2, 'White Frame 24x36', 'Premium white frame 24x36 inches', 200.00, 'frame24x36.jpg', 'active', '2026-02-03 08:35:51');

-- Dumping structure for table spd_production.booked_slots
CREATE TABLE IF NOT EXISTS `booked_slots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `status` enum('booked','available') DEFAULT 'booked',
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  CONSTRAINT `booked_slots_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table spd_production.booked_slots: ~0 rows (approximately)
INSERT INTO `booked_slots` (`id`, `booking_date`, `start_time`, `end_time`, `booking_id`, `status`) VALUES
	(1, '2026-02-11', '13:00:00', '13:20:00', 1, 'booked');

-- Dumping structure for table spd_production.bookings
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_reference` varchar(20) NOT NULL,
  `theme_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `duration` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `pax_count` int(11) NOT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) DEFAULT 0.00,
  `addons_total` decimal(10,2) DEFAULT 0.00,
  `total_price` decimal(10,2) NOT NULL,
  `deposit` decimal(10,2) NOT NULL,
  `balance` decimal(10,2) NOT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','paid','cancelled') DEFAULT 'pending',
  `booking_status` enum('confirmed','completed','cancelled') DEFAULT 'confirmed',
  `slot_expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_reference` (`booking_reference`),
  KEY `theme_id` (`theme_id`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`theme_id`) REFERENCES `themes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table spd_production.bookings: ~1 rows (approximately)
INSERT INTO `bookings` (`id`, `booking_reference`, `theme_id`, `booking_date`, `booking_time`, `duration`, `customer_name`, `customer_phone`, `customer_email`, `pax_count`, `base_price`, `discount`, `addons_total`, `total_price`, `deposit`, `balance`, `coupon_code`, `payment_status`, `booking_status`, `slot_expires_at`, `created_at`) VALUES
	(1, 'SPD202602036674', 1, '2026-02-11', '13:00:00', 20, 'Ahmad Albab', '0101234567', 'admin@haqis.com', 5, 150.00, 51.00, 120.00, 219.00, 50.00, 169.00, NULL, 'pending', 'confirmed', '2026-02-03 16:46:33', '2026-02-03 08:37:06');

-- Dumping structure for table spd_production.booking_addons
CREATE TABLE IF NOT EXISTS `booking_addons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `addon_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `addon_id` (`addon_id`),
  CONSTRAINT `booking_addons_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `booking_addons_ibfk_2` FOREIGN KEY (`addon_id`) REFERENCES `addons` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table spd_production.booking_addons: ~0 rows (approximately)
INSERT INTO `booking_addons` (`id`, `booking_id`, `addon_id`, `quantity`, `price`) VALUES
	(1, 1, 1, 1, 120.00);

-- Dumping structure for table spd_production.coupons
CREATE TABLE IF NOT EXISTS `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('fixed','percentage') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_purchase` decimal(10,2) DEFAULT 0.00,
  `max_usage` int(11) DEFAULT 0,
  `used_count` int(11) DEFAULT 0,
  `valid_from` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table spd_production.coupons: ~2 rows (approximately)
INSERT INTO `coupons` (`id`, `code`, `discount_type`, `discount_value`, `min_purchase`, `max_usage`, `used_count`, `valid_from`, `valid_until`, `status`, `created_at`) VALUES
	(1, 'DISCOUNT10', 'fixed', 10.00, 0.00, 0, 0, '2026-01-01', '2026-12-31', 'active', '2026-02-03 08:35:51'),
	(2, 'SAVE20', 'percentage', 20.00, 0.00, 0, 0, '2026-01-01', '2026-12-31', 'active', '2026-02-03 08:35:51');

-- Dumping structure for table spd_production.themes
CREATE TABLE IF NOT EXISTS `themes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `max_pax` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table spd_production.themes: ~5 rows (approximately)
INSERT INTO `themes` (`id`, `name`, `description`, `duration`, `max_pax`, `price`, `image`, `status`, `created_at`) VALUES
	(1, 'Classic', 'Pilih Tarikh & Masa', 20, 6, 150.00, 'classic.jpg', 'active', '2026-02-03 08:35:51'),
	(2, 'Garden', 'Outdoor garden themed shoot', 20, 6, 150.00, 'garden.jpg', 'active', '2026-02-03 08:35:51'),
	(3, 'Modern', 'Contemporary minimalist style', 30, 8, 180.00, 'modern.jpg', 'active', '2026-02-03 08:35:51'),
	(4, 'Vintage', 'Retro classic vibes', 25, 6, 170.00, 'vintage.jpg', 'active', '2026-02-03 08:35:51'),
	(5, 'Minimalist', 'Clean and simple aesthetics', 20, 4, 160.00, 'minimalist.jpg', 'active', '2026-02-03 08:35:51');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
