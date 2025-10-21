-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 16, 2025 at 08:12 PM
-- Server version: 5.7.24
-- PHP Version: 8.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vibedaybkk`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_create_model` (IN `p_category_id` INT, IN `p_code` VARCHAR(50), IN `p_name` VARCHAR(100), IN `p_description` TEXT, IN `p_price_min` DECIMAL(10,2), IN `p_price_max` DECIMAL(10,2), IN `p_height` INT, IN `p_experience_years` INT)   BEGIN
    INSERT INTO models (category_id, code, name, description, price_min, price_max, height, experience_years)
    VALUES (p_category_id, p_code, p_name, p_description, p_price_min, p_price_max, p_height, p_experience_years);
    
    SELECT LAST_INSERT_ID() as model_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_model_stats` (IN `p_model_id` INT)   BEGIN
    UPDATE models m
    SET 
        m.booking_count = (SELECT COUNT(*) FROM bookings WHERE model_id = p_model_id AND status = 'completed'),
        m.rating = (SELECT COALESCE(AVG(rating), 0) FROM bookings WHERE model_id = p_model_id AND rating IS NOT NULL)
    WHERE m.id = p_model_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` text COLLATE utf8mb4_unicode_ci,
  `new_values` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'login', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 16:56:14'),
(2, 1, 'update_settings', 'settings', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 17:11:39'),
(3, 1, 'update', 'menus', 1, '{\"id\":1,\"parent_id\":null,\"title\":\"\\u0e2b\\u0e19\\u0e49\\u0e32\\u0e41\\u0e23\\u0e01\",\"url\":\"index.html\",\"icon\":\"fa-home\",\"target\":\"_self\",\"sort_order\":1,\"status\":\"active\",\"created_at\":\"2025-10-14 23:48:03\",\"updated_at\":\"2025-10-14 23:48:03\"}', '{\"parent_id\":null,\"title\":\"\\u0e2b\\u0e19\\u0e49\\u0e32\\u0e41\\u0e23\\u0e01x\",\"url\":\"index.html\",\"icon\":\"fa-home\",\"target\":\"_self\",\"sort_order\":1,\"status\":\"active\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 17:18:38'),
(4, 1, 'update', 'menus', 1, '{\"id\":1,\"parent_id\":null,\"title\":\"\\u0e2b\\u0e19\\u0e49\\u0e32\\u0e41\\u0e23\\u0e01x\",\"url\":\"index.html\",\"icon\":\"fa-home\",\"target\":\"_self\",\"sort_order\":1,\"status\":\"active\",\"created_at\":\"2025-10-14 23:48:03\",\"updated_at\":\"2025-10-15 00:18:38\"}', '{\"parent_id\":null,\"title\":\"\\u0e2b\\u0e19\\u0e49\\u0e32\\u0e41\\u0e23\\u0e01\",\"url\":\"index.html\",\"icon\":\"fa-home\",\"target\":\"_self\",\"sort_order\":1,\"status\":\"active\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 17:18:47'),
(5, 1, 'update_settings', 'settings', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 17:31:35'),
(6, 1, 'update_settings', 'settings', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 17:31:57'),
(7, 1, 'update_settings', 'settings', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 17:39:05'),
(8, 1, 'update_settings', 'settings', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 17:39:19'),
(9, 1, 'update_settings', 'settings', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 17:39:24'),
(10, 1, 'update_settings', 'settings', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 17:42:45'),
(11, 1, 'update_settings', 'settings', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 17:42:53'),
(12, 1, 'delete', 'menus', 1, '{\"id\":1,\"parent_id\":null,\"title\":\"\\u0e2b\\u0e19\\u0e49\\u0e32\\u0e41\\u0e23\\u0e01\",\"url\":\"index.html\",\"icon\":\"fa-home\",\"target\":\"_self\",\"sort_order\":1,\"status\":\"active\",\"created_at\":\"2025-10-14 23:48:03\",\"updated_at\":\"2025-10-15 00:18:47\"}', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 20:04:29'),
(13, 1, 'update', 'menus', 2, '{\"id\":2,\"parent_id\":null,\"title\":\"\\u0e40\\u0e01\\u0e35\\u0e48\\u0e22\\u0e27\\u0e01\\u0e31\\u0e1a\\u0e40\\u0e23\\u0e32\",\"url\":\"index.html#about\",\"icon\":\"fa-info-circle\",\"target\":\"_self\",\"sort_order\":2,\"status\":\"active\",\"created_at\":\"2025-10-14 23:48:03\",\"updated_at\":\"2025-10-14 23:48:03\"}', '{\"parent_id\":null,\"title\":\"\\u0e40\\u0e01\\u0e35\\u0e48\\u0e22\\u0e27\\u0e01\\u0e31\\u0e1a\\u0e40\\u0e23\\u0e32\",\"url\":\"index.php#about\",\"icon\":\"fa-info-circle\",\"target\":\"_self\",\"sort_order\":2,\"status\":\"active\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 20:54:29'),
(14, 1, 'delete', 'menus', 7, '{\"id\":7,\"parent_id\":null,\"title\":\"\\u0e40\\u0e01\\u0e35\\u0e48\\u0e22\\u0e27\\u0e01\\u0e31\\u0e1a\\u0e40\\u0e23\\u0e32\",\"url\":\"about.php\",\"icon\":\"fa-info-circle\",\"target\":\"_self\",\"sort_order\":2,\"status\":\"active\",\"created_at\":\"2025-10-15 02:50:44\",\"updated_at\":\"2025-10-15 02:50:44\"}', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 20:54:40'),
(15, 1, 'delete', 'menus', 3, '{\"id\":3,\"parent_id\":null,\"title\":\"\\u0e1a\\u0e23\\u0e34\\u0e01\\u0e32\\u0e23\",\"url\":\"services.html\",\"icon\":\"fa-briefcase\",\"target\":\"_self\",\"sort_order\":3,\"status\":\"active\",\"created_at\":\"2025-10-14 23:48:03\",\"updated_at\":\"2025-10-14 23:48:03\"}', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 20:54:43'),
(16, 1, 'delete', 'menus', 4, '{\"id\":4,\"parent_id\":null,\"title\":\"\\u0e1a\\u0e17\\u0e04\\u0e27\\u0e32\\u0e21\",\"url\":\"articles.html\",\"icon\":\"fa-newspaper\",\"target\":\"_self\",\"sort_order\":4,\"status\":\"active\",\"created_at\":\"2025-10-14 23:48:03\",\"updated_at\":\"2025-10-14 23:48:03\"}', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 20:54:52'),
(17, 1, 'update', 'menus', 9, '{\"id\":9,\"parent_id\":null,\"title\":\"\\u0e42\\u0e21\\u0e40\\u0e14\\u0e25\",\"url\":\"models.php\",\"icon\":\"fa-users\",\"target\":\"_self\",\"sort_order\":4,\"status\":\"active\",\"created_at\":\"2025-10-15 02:50:44\",\"updated_at\":\"2025-10-15 02:50:44\"}', '{\"parent_id\":null,\"title\":\"\\u0e42\\u0e21\\u0e40\\u0e14\\u0e25\",\"url\":\"models.php\",\"icon\":\"fa-users\",\"target\":\"_self\",\"sort_order\":4,\"status\":\"inactive\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 21:14:07'),
(18, 1, 'update', 'menus', 12, '{\"id\":12,\"parent_id\":null,\"title\":\"\\u0e15\\u0e34\\u0e14\\u0e15\\u0e48\\u0e2d\\u0e40\\u0e23\\u0e32\",\"url\":\"contact.php\",\"icon\":\"fa-envelope\",\"target\":\"_self\",\"sort_order\":7,\"status\":\"active\",\"created_at\":\"2025-10-15 02:50:44\",\"updated_at\":\"2025-10-15 02:50:44\"}', '{\"parent_id\":null,\"title\":\"\\u0e15\\u0e34\\u0e14\\u0e15\\u0e48\\u0e2d\\u0e40\\u0e23\\u0e32\",\"url\":\"contact.php\",\"icon\":\"fa-envelope\",\"target\":\"_self\",\"sort_order\":7,\"status\":\"inactive\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 21:14:28'),
(19, 1, 'update', 'menus', 5, '{\"id\":5,\"parent_id\":null,\"title\":\"\\u0e15\\u0e34\\u0e14\\u0e15\\u0e48\\u0e2d\",\"url\":\"index.html#contact\",\"icon\":\"fa-envelope\",\"target\":\"_self\",\"sort_order\":5,\"status\":\"active\",\"created_at\":\"2025-10-14 23:48:03\",\"updated_at\":\"2025-10-14 23:48:03\"}', '{\"parent_id\":null,\"title\":\"\\u0e15\\u0e34\\u0e14\\u0e15\\u0e48\\u0e2d\",\"url\":\"index.phpl#contact\",\"icon\":\"fa-envelope\",\"target\":\"_self\",\"sort_order\":5,\"status\":\"active\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 21:14:44'),
(20, 1, 'update', 'menus', 5, '{\"id\":5,\"parent_id\":null,\"title\":\"\\u0e15\\u0e34\\u0e14\\u0e15\\u0e48\\u0e2d\",\"url\":\"index.phpl#contact\",\"icon\":\"fa-envelope\",\"target\":\"_self\",\"sort_order\":5,\"status\":\"active\",\"created_at\":\"2025-10-14 23:48:03\",\"updated_at\":\"2025-10-15 04:14:44\"}', '{\"parent_id\":null,\"title\":\"\\u0e15\\u0e34\\u0e14\\u0e15\\u0e48\\u0e2d\",\"url\":\"index.php#contact\",\"icon\":\"fa-envelope\",\"target\":\"_self\",\"sort_order\":5,\"status\":\"active\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 21:31:30'),
(21, 1, 'update', 'menus', 0, '\"Updated menu order\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 21:42:50'),
(22, 1, 'update', 'menus', 0, '\"Updated menu order\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 21:43:02'),
(23, 1, 'update', 'menus', 0, '\"Updated menu order\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 21:46:37'),
(24, 1, 'update', 'menus', 0, '\"Updated menu order\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 21:46:42'),
(25, 1, 'login', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 16:11:11'),
(26, 1, 'create', 'gallery_images', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 16:47:24'),
(27, 1, 'update', 'gallery_images', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 16:47:38'),
(28, 1, 'create', 'gallery_images', 4, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 17:03:03'),
(29, 1, 'create', 'gallery_images', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 17:04:02'),
(30, 1, 'update', 'settings', 0, '\"Toggle youtube: enabled\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 17:35:46'),
(31, 1, 'update', 'settings', 0, '\"Toggle tiktok: enabled\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 17:35:51'),
(32, 1, 'update', 'settings', 0, '\"Toggle tiktok: disabled\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 17:36:01'),
(33, 1, 'update', 'settings', 0, '\"Toggle youtube: disabled\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 17:36:02'),
(34, 1, 'update', 'settings', 0, '\"Toggle line: disabled\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 17:36:04'),
(35, 1, 'update', 'settings', 0, '\"Toggle Go to Top: disabled\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 17:37:25'),
(36, 1, 'update', 'settings', 0, '\"Toggle Go to Top: enabled\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 17:37:32'),
(37, 1, 'logout', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:25:10'),
(38, 1, 'login', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:25:24'),
(39, 12, 'login', 'users', 12, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:32:58'),
(40, 1, 'update', 'permissions', 0, '\"Updated editor - models - create\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:41:47'),
(41, 1, 'update', 'permissions', 0, '\"Updated editor - models - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:41:49'),
(42, 1, 'update', 'permissions', 0, '\"Updated editor - models - delete\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:41:50'),
(43, 1, 'update', 'permissions', 0, '\"Updated editor - models - export\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:41:51'),
(44, 1, 'update', 'permissions', 0, '\"Updated editor - categories - export\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:41:52'),
(45, 1, 'update', 'permissions', 0, '\"Updated editor - categories - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:41:53'),
(46, 1, 'update', 'permissions', 0, '\"Updated editor - categories - create\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:41:53'),
(47, 1, 'update', 'permissions', 0, '\"Updated editor - articles - create\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:41:54'),
(48, 1, 'update', 'permissions', 0, '\"Updated editor - articles - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:41:54'),
(49, 1, 'update', 'permissions', 0, '\"Updated editor - articles - delete\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:41:55'),
(50, 1, 'update', 'permissions', 0, '\"Updated editor - articles - export\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:41:56'),
(51, 1, 'update', 'permissions', 0, '\"Updated editor - models - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:05'),
(52, 1, 'update', 'permissions', 0, '\"Updated editor - article_categories - create\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:20'),
(53, 1, 'update', 'permissions', 0, '\"Updated editor - article_categories - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:20'),
(54, 1, 'update', 'permissions', 0, '\"Updated editor - article_categories - export\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:21'),
(55, 1, 'update', 'permissions', 0, '\"Updated editor - bookings - export\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:21'),
(56, 1, 'update', 'permissions', 0, '\"Updated editor - contacts - export\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:23'),
(57, 1, 'update', 'permissions', 0, '\"Updated editor - gallery - export\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:24'),
(58, 1, 'update', 'permissions', 0, '\"Updated editor - gallery - delete\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:26'),
(59, 1, 'update', 'permissions', 0, '\"Updated editor - bookings - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:28'),
(60, 1, 'update', 'permissions', 0, '\"Updated editor - contacts - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:29'),
(61, 1, 'update', 'permissions', 0, '\"Updated editor - menus - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:30'),
(62, 1, 'update', 'permissions', 0, '\"Updated editor - gallery - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:30'),
(63, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:31'),
(64, 1, 'update', 'permissions', 0, '\"Updated editor - gallery - create\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:32'),
(65, 1, 'update', 'permissions', 0, '\"Updated editor - bookings - create\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:32'),
(66, 1, 'update', 'permissions', 0, '\"Updated editor - gallery - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:34'),
(67, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:34'),
(68, 1, 'update', 'permissions', 0, '\"Updated editor - menus - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:35'),
(69, 1, 'update', 'permissions', 0, '\"Updated editor - contacts - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:35'),
(70, 1, 'update', 'permissions', 0, '\"Updated editor - bookings - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:36'),
(71, 1, 'update', 'permissions', 0, '\"Updated editor - article_categories - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:37'),
(72, 1, 'update', 'permissions', 0, '\"Updated editor - articles - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:37'),
(73, 1, 'update', 'permissions', 0, '\"Updated editor - categories - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:42:38'),
(74, 1, 'update', 'permissions', 0, '\"Updated editor - models - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:45:59'),
(75, 1, 'update', 'permissions', 0, '\"Updated editor - models - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:46:11'),
(76, 12, 'logout', 'users', 12, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:46:57'),
(77, 1, 'create', 'users', 13, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:47:22'),
(78, 13, 'login', 'users', 13, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:47:30'),
(79, 1, 'update', 'permissions', 0, '\"Updated editor - models - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:47:57'),
(80, 1, 'update', 'permissions', 0, '\"Updated editor - models - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:48:01'),
(81, 1, 'update', 'permissions', 0, '\"Updated editor - models - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:48:02'),
(82, 1, 'update', 'permissions', 0, '\"Updated editor - models - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:48:05'),
(83, 1, 'update', 'permissions', 0, '\"Updated editor - models - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:48:06'),
(84, 1, 'update', 'permissions', 0, '\"Updated editor - models - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:48:07'),
(85, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:48:37'),
(86, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - create\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:48:46'),
(87, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:48:48'),
(88, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - delete\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:48:48'),
(89, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - export\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:48:49'),
(90, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - create\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:48:57'),
(91, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:48:57'),
(92, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - delete\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:48:58'),
(93, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - export\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:48:59'),
(94, 1, 'update', 'permissions', 0, '\"Updated editor - settings - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:49:37'),
(95, 1, 'update', 'permissions', 0, '\"Updated editor - settings - create\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:49:42'),
(96, 1, 'update', 'permissions', 0, '\"Updated editor - settings - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:49:43'),
(97, 1, 'update', 'permissions', 0, '\"Updated editor - settings - delete\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:49:43'),
(98, 1, 'update', 'permissions', 0, '\"Updated editor - settings - export\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 18:49:44'),
(99, 1, 'update', 'permissions', 0, '\"Updated editor - gallery - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:01:21'),
(100, 1, 'update', 'permissions', 0, '\"Updated editor - users - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:01:22'),
(101, 1, 'update', 'permissions', 0, '\"Updated editor - menus - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:01:22'),
(102, 1, 'update', 'permissions', 0, '\"Updated editor - contacts - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:01:23'),
(103, 1, 'update', 'permissions', 0, '\"Updated editor - bookings - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:01:23'),
(104, 1, 'update', 'permissions', 0, '\"Updated editor - article_categories - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:01:24'),
(105, 1, 'update', 'permissions', 0, '\"Updated editor - articles - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:01:24'),
(106, 1, 'update', 'permissions', 0, '\"Updated editor - categories - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:01:24'),
(107, 1, 'update', 'permissions', 0, '\"Updated editor - models - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:01:28'),
(108, 1, 'update', 'permissions', 0, '\"Updated editor - menus - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:08:12'),
(109, 1, 'update', 'permissions', 0, '\"Updated editor - menus - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:08:22'),
(110, 1, 'update', 'permissions', 0, '\"Updated editor - settings - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:09:51'),
(111, 1, 'update', 'permissions', 0, '\"Updated editor - settings - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:10:01'),
(112, 1, 'update', 'permissions', 0, '\"Updated editor - settings - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:10:31'),
(113, 1, 'update', 'permissions', 0, '\"Updated editor - models - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:13:37'),
(114, 1, 'update', 'permissions', 0, '\"Updated editor - models - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:13:43'),
(115, 1, 'update', 'permissions', 0, '\"Updated editor - settings - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:13:46'),
(116, 1, 'update', 'permissions', 0, '\"Updated editor - settings - create\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:13:55'),
(117, 1, 'update', 'permissions', 0, '\"Updated editor - settings - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:13:55'),
(118, 1, 'update', 'permissions', 0, '\"Updated editor - settings - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:14:01'),
(119, 1, 'update', 'permissions', 0, '\"Updated editor - settings - create\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:14:01'),
(120, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - create\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:16:56'),
(121, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:16:57'),
(122, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - delete\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:16:58'),
(123, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - export\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:16:59'),
(124, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - create\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:17:08'),
(125, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:17:08'),
(126, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - delete\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:17:09'),
(127, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - export\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:17:09'),
(128, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - create\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:25:26'),
(129, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - create\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:25:27'),
(130, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - create\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:25:30'),
(131, 1, 'update', 'permissions', 0, '\"Updated editor - homepage - create\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:25:31'),
(132, 1, 'update', 'permissions', 0, '\"Updated editor - bookings - create\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:25:33'),
(133, 1, 'update', 'permissions', 0, '\"Updated editor - bookings - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:25:36'),
(134, 1, 'update', 'permissions', 0, '\"Updated editor - bookings - delete\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:25:37'),
(135, 1, 'update', 'permissions', 0, '\"Updated editor - bookings - export\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:25:38'),
(136, 1, 'update', 'permissions', 0, '\"Updated editor - bookings - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:25:51'),
(137, 1, 'update', 'permissions', 0, '\"Updated editor - bookings - view\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:26:08'),
(138, 1, 'update', 'permissions', 0, '\"Updated editor - menus - create\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:36:09'),
(139, 1, 'update', 'permissions', 0, '\"Updated editor - menus - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:36:10'),
(140, 1, 'update', 'permissions', 0, '\"Updated editor - menus - delete\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:36:11'),
(141, 1, 'update', 'permissions', 0, '\"Updated editor - menus - export\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:36:11'),
(142, 1, 'update', 'permissions', 0, '\"Updated editor - gallery - create\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:38:34'),
(143, 1, 'update', 'permissions', 0, '\"Updated editor - gallery - edit\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:38:35'),
(144, 1, 'update', 'permissions', 0, '\"Updated editor - gallery - delete\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:38:35'),
(145, 1, 'update', 'permissions', 0, '\"Updated editor - gallery - export\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-15 19:38:36'),
(146, 1, 'test_action', 'test_table', 1, '{\"old\":\"test\"}', '{\"new\":\"test_new\"}', '127.0.0.1', NULL, '2025-10-15 19:52:27'),
(147, 1, 'logout', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 09:39:19'),
(148, 1, 'login', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 09:39:26'),
(149, 1, 'update_settings', 'settings', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 10:25:46'),
(150, 1, 'update_settings', 'settings', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 10:25:57'),
(151, 1, 'update_settings', 'settings', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 10:58:51'),
(152, 1, 'update_settings', 'settings', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 11:18:09'),
(153, 1, 'update_settings', 'settings', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 12:01:11'),
(154, 1, 'update_settings', 'settings', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 12:01:18'),
(155, 1, 'update', 'settings', 0, NULL, 'Toggle tiktok: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:31:42'),
(156, 1, 'update', 'settings', 0, NULL, 'Toggle youtube: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:31:42'),
(157, 1, 'update', 'settings', 0, NULL, 'Toggle line: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:31:43'),
(158, 1, 'update', 'settings', 0, NULL, 'Toggle youtube: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:31:56'),
(159, 1, 'update', 'settings', 0, NULL, 'Toggle line: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:31:57'),
(160, 1, 'update', 'settings', 0, NULL, 'Toggle tiktok: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:31:58'),
(161, 1, 'update', 'settings', 0, NULL, 'Toggle twitter: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:32:00'),
(162, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:32:01'),
(163, 1, 'update', 'settings', 0, NULL, 'Updated social and go to top settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:34:57'),
(164, 1, 'update', 'settings', 0, NULL, 'Updated social and go to top settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:35:28'),
(165, 1, 'update', 'settings', 0, NULL, 'Toggle youtube: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:35:32'),
(166, 1, 'update', 'settings', 0, NULL, 'Toggle youtube: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:35:34'),
(167, 1, 'update', 'settings', 0, NULL, 'Toggle youtube: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:35:34'),
(168, 1, 'update', 'settings', 0, NULL, 'Toggle youtube: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:35:35'),
(169, 1, 'update', 'settings', 0, NULL, 'Toggle youtube: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:35:35'),
(170, 1, 'update', 'settings', 0, NULL, 'Toggle youtube: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:35:37'),
(171, 1, 'update', 'settings', 0, NULL, 'Toggle line: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:35:39'),
(172, 1, 'update', 'settings', 0, NULL, 'Toggle line: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:35:39'),
(173, 1, 'update', 'settings', 0, NULL, 'Updated social and go to top settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:35:42'),
(174, 1, 'update', 'settings', 0, NULL, 'Toggle youtube: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:35:46'),
(175, 1, 'update', 'settings', 0, NULL, 'Toggle youtube: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:35:47'),
(176, 1, 'update', 'settings', 0, NULL, 'Toggle youtube: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:35:49'),
(177, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:35:50'),
(178, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:35:51'),
(179, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:35:51'),
(180, 1, 'update', 'settings', 0, NULL, 'Toggle youtube: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:35:55'),
(181, 1, 'update', 'settings', 0, NULL, 'Updated social and go to top settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:35:57'),
(182, 1, 'update', 'settings', 0, NULL, 'Toggle youtube: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:36:00'),
(183, 1, 'update', 'settings', 0, NULL, 'Toggle twitter: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:36:01'),
(184, 1, 'update', 'settings', 0, NULL, 'Toggle twitter: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:36:02'),
(185, 1, 'update', 'settings', 0, NULL, 'Toggle twitter: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:36:02'),
(186, 1, 'update', 'settings', 0, NULL, 'Toggle twitter: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:36:02'),
(187, 1, 'update', 'settings', 0, NULL, 'Updated social and go to top settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:36:16'),
(188, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:36:17'),
(189, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:36:18'),
(190, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:36:18'),
(191, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:36:19'),
(192, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:36:19'),
(193, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:36:19'),
(194, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:36:20'),
(195, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:36:20'),
(196, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:36:20'),
(197, 1, 'update', 'settings', 0, NULL, 'Updated social and go to top settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:45:56'),
(198, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:46:03'),
(199, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:46:04'),
(200, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:46:04'),
(201, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:46:04'),
(202, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:46:04'),
(203, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:46:04'),
(204, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:46:05'),
(205, 1, 'update', 'settings', 0, NULL, 'Toggle facebook: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:46:06'),
(206, 1, 'update', 'settings', 0, NULL, 'Toggle facebook: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:46:07'),
(207, 1, 'update', 'settings', 0, NULL, 'Toggle facebook: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:46:27'),
(208, 1, 'update', 'settings', 0, NULL, 'Toggle facebook: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:46:28'),
(209, 1, 'update', 'settings', 0, NULL, 'Toggle facebook: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:46:28'),
(210, 1, 'update', 'settings', 0, NULL, 'Toggle facebook: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:46:28');
INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(211, 1, 'update', 'settings', 0, NULL, 'Toggle facebook: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:46:29'),
(212, 1, 'update', 'settings', 0, NULL, 'Toggle facebook: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:46:29'),
(213, 1, 'update', 'settings', 0, NULL, 'Updated social and go to top settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:52:34'),
(214, 1, 'update', 'settings', 0, NULL, 'Toggle facebook: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:52:36'),
(215, 1, 'update', 'settings', 0, NULL, 'Toggle facebook: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:52:37'),
(216, 1, 'update', 'settings', 0, NULL, 'Toggle instagram: enabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:52:38'),
(217, 1, 'update', 'settings', 0, NULL, 'Toggle youtube: disabled', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:52:48'),
(218, 1, 'update_settings', 'settings', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:58:24'),
(219, 1, 'update_settings', 'settings', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 13:58:33'),
(220, 1, 'update_settings', 'settings', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 15:03:20');

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `read_time` int(11) DEFAULT '5',
  `view_count` int(11) DEFAULT '0',
  `status` enum('draft','published','archived') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `slug`, `excerpt`, `content`, `featured_image`, `category`, `category_id`, `author_id`, `read_time`, `view_count`, `status`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 'เทรนด์แฟชั่น 2024 ที่ต้องจับตามอง', 'fashion-trends-2024', 'รวมเทรนด์แฟชั่นที่กำลังมาแรงในปี 2024', '<p>เทรนด์แฟชั่นในปี 2024 มีความหลากหลายและน่าสนใจมาก ตั้งแต่สีสันสดใส ไปจนถึงการตัดเย็บที่เน้นความเรียบง่าย</p><p>ไฮไลท์สำคัญ ได้แก่ การใช้วัสดุที่เป็นมิตรกับสิ่งแวดล้อม และการผสมผสานสไตล์วินเทจเข้ากับความทันสมัย</p>', NULL, 'Fashion', NULL, 1, 5, 2, 'published', '2025-10-15 02:50:44', '2025-10-14 19:50:44', '2025-10-16 00:31:08'),
(2, '10 ทริคการถ่ายภาพแฟชั่นให้สวยปัง', '10-fashion-photography-tips', 'เทคนิคการถ่ายภาพแฟชั่นสำหรับมือใหม่', '<p>การถ่ายภาพแฟชั่นไม่ใช่เรื่องยาก หากคุณรู้เทคนิคพื้นฐาน</p><p>1. เลือกแสงที่เหมาะสม<br>2. ใช้มุมกล้องที่น่าสนใจ<br>3. ดูแลรายละเอียดของชุด<br>4. สื่อสารกับโมเดลให้ชัดเจน<br>5. ใช้โปรแกรมแต่งภาพอย่างเหมาะสม</p>', NULL, 'Photography', NULL, 1, 7, 0, 'published', '2025-10-15 02:50:44', '2025-10-14 19:50:44', '2025-10-14 19:50:44'),
(3, 'วิธีเลือกโมเดลที่เหมาะกับงาน', 'how-to-choose-right-model', 'คำแนะนำในการเลือกโมเดลสำหรับงานต่างๆ', '<p>การเลือกโมเดลที่เหมาะสมเป็นสิ่งสำคัญต่อความสำเร็จของงาน</p><p>พิจารณาจาก: ประเภทงาน, งบประมาณ, ประสบการณ์, บุคลิกภาพ, และความเหมาะสมกับแบรนด์</p>', NULL, 'Tips', NULL, 1, 6, 0, 'published', '2025-10-15 02:50:44', '2025-10-14 19:50:44', '2025-10-14 19:50:44'),
(4, 'Behind the Scenes: Bangkok Fashion Week', 'behind-scenes-bangkok-fashion-week', 'เบื้องหลังงาน Bangkok Fashion Week 2024', '<p>พาไปดูเบื้องหลังงานแฟชั่นที่ยิ่งใหญ่ที่สุดของปี</p><p>ตั้งแต่การเตรียมตัว การซ้อม ไปจนถึงการแสดงบนรันเวย์ ทุกขั้นตอนต้องใช้ความพยายามและความมืออาชีพสูง</p>', NULL, 'Events', NULL, 1, 8, 0, 'published', '2025-10-15 02:50:44', '2025-10-14 19:50:44', '2025-10-14 19:50:44'),
(5, 'การดูแลผิวพรรณสำหรับโมเดล', 'skincare-for-models', 'เคล็ดลับการดูแลผิวให้สวยใสเหมือนโมเดล', '<p>ผิวพรรณที่ดีเป็นทุนสำคัญของโมเดล</p><p>แนะนำ: ดื่มน้ำเยอะๆ, นอนหลับพักผ่อนเพียงพอ, ใช้ครีมกันแดด, ทำความสะอาดผิวอย่างสม่ำเสมอ</p>', NULL, 'Beauty', NULL, 1, 5, 0, 'published', '2025-10-15 02:50:44', '2025-10-14 19:50:44', '2025-10-14 19:50:44'),
(6, 'เทคนิคการเดินแบบสำหรับมือใหม่', 'runway-walking-techniques', 'เรียนรู้การเดินรันเวย์แบบมืออาชีพ', '<p>การเดินรันเวย์เป็นศิลปะที่ต้องฝึกฝน</p><p>เทคนิคพื้นฐาน: ยืนตัวตรง, เดินมั่นใจ, สบตากล้อง, หมุนตัวอย่างสง่างาม, รักษาจังหวะการเดิน</p>', NULL, 'Tutorial', NULL, 1, 6, 0, 'published', '2025-10-15 02:50:44', '2025-10-14 19:50:44', '2025-10-14 19:50:44'),
(7, 'แฟชั่นโชว์ที่น่าจับตามองในปีนี้', 'fashion-shows-2024', 'รวมแฟชั่นโชว์สำคัญในปี 2024', '<p>ปีนี้มีแฟชั่นโชว์ใหญ่ๆ มากมาย</p><p>ไม่ว่าจะเป็น Bangkok Fashion Week, Thailand Fashion Week, และอีกหลายงานที่น่าสนใจ</p>', NULL, 'Events', NULL, 1, 4, 0, 'published', '2025-10-15 02:50:44', '2025-10-14 19:50:44', '2025-10-14 19:50:44'),
(8, 'การเตรียมตัวก่อนถ่ายแบบ', 'prepare-before-photoshoot', 'สิ่งที่ต้องเตรียมก่อนไปถ่ายแบบ', '<p>การเตรียมตัวที่ดีจะทำให้งานถ่ายแบบราบรื่น</p><p>เช็คลิสต์: ดูแลผิวพรรณ, นอนหลับพักผ่อน, เตรียมชุดสำรอง, ทำความสะอาดเล็บ, เตรียมอุปกรณ์ส่วนตัว</p>', NULL, 'Tips', NULL, 1, 5, 0, 'published', '2025-10-15 02:50:44', '2025-10-14 19:50:44', '2025-10-14 19:50:44'),
(9, 'ประวัติความเป็นมาของ VIBEDAYBKK', 'about-vibedaybkk', 'เรื่องราวของเราตั้งแต่วันแรก', '<p>VIBEDAYBKK ก่อตั้งขึ้นในปี 2014 ด้วยความมุ่งมั่นที่จะเป็นเอเจนซี่โมเดลชั้นนำ</p><p>เราเติบโตมาพร้อมกับโมเดลมากกว่า 500 คน และทำงานกับแบรนด์ชั้นนำมากมาย</p>', NULL, 'About', NULL, 1, 4, 0, 'published', '2025-10-15 02:50:44', '2025-10-14 19:50:44', '2025-10-14 19:50:44'),
(10, 'แนวโน้มการตลาดด้วยโมเดล', 'model-marketing-trends', 'การใช้โมเดลในการตลาดยุคใหม่', '<p>การตลาดด้วยโมเดลในยุคดิจิทัลมีความสำคัญมากขึ้น</p><p>ไม่ว่าจะเป็น Social Media Marketing, Influencer Marketing, หรือ Content Marketing ล้วนต้องใช้โมเดลที่เหมาะสม</p>', NULL, 'Marketing', NULL, 1, 7, 0, 'published', '2025-10-15 02:50:44', '2025-10-14 19:50:44', '2025-10-14 19:50:44');

-- --------------------------------------------------------

--
-- Table structure for table `article_categories`
--

CREATE TABLE `article_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `article_categories`
--

INSERT INTO `article_categories` (`id`, `name`, `slug`, `description`, `icon`, `color`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'แฟชั่น', 'fashion', 'เทรนด์แฟชั่นและสไตล์ล่าสุด', 'fa-tshirt', 'from-pink-500 to-red-500', 1, 'active', '2025-10-14 21:15:04', '2025-10-14 21:15:04'),
(2, 'การถ่ายภาพ', 'photography', 'เทคนิคและคำแนะนำการถ่ายภาพ', 'fa-camera', 'from-blue-500 to-purple-500', 2, 'active', '2025-10-14 21:15:04', '2025-10-14 21:15:04'),
(3, 'เคล็ดลับ', 'tips', 'เคล็ดลับและคำแนะนำต่างๆ', 'fa-lightbulb', 'from-yellow-500 to-orange-500', 3, 'active', '2025-10-14 21:15:04', '2025-10-14 21:15:04'),
(4, 'งานอีเวนท์', 'events', 'ข่าวสารและรีวิวงานอีเวนท์', 'fa-calendar-alt', 'from-green-500 to-teal-500', 4, 'active', '2025-10-14 21:15:04', '2025-10-14 21:15:04'),
(5, 'ความงาม', 'beauty', 'การดูแลผิวพรรณและความงาม', 'fa-spa', 'from-purple-500 to-pink-500', 5, 'active', '2025-10-14 21:15:04', '2025-10-14 21:15:04'),
(6, 'บทเรียน', 'tutorial', 'บทเรียนและคู่มือต่างๆ', 'fa-graduation-cap', 'from-indigo-500 to-blue-500', 6, 'active', '2025-10-14 21:15:04', '2025-10-14 21:15:04'),
(7, 'การตลาด', 'marketing', 'กลยุทธ์การตลาดและโปรโมชั่น', 'fa-chart-line', 'from-red-500 to-orange-500', 7, 'active', '2025-10-14 21:15:04', '2025-10-14 21:15:04'),
(8, 'เกี่ยวกับเรา', 'about', 'ข้อมูลเกี่ยวกับบริษัท', 'fa-info-circle', 'from-gray-500 to-gray-700', 8, 'active', '2025-10-14 21:15:04', '2025-10-14 21:15:04');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `model_id` int(11) NOT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `booking_date` date NOT NULL,
  `booking_days` int(11) DEFAULT '1',
  `service_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` text COLLATE utf8mb4_unicode_ci,
  `message` text COLLATE utf8mb4_unicode_ci,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `payment_status` enum('unpaid','deposit','paid','refunded') COLLATE utf8mb4_unicode_ci DEFAULT 'unpaid',
  `confirmed_at` datetime DEFAULT NULL,
  `confirmed_by` int(11) DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `cancel_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `model_id`, `customer_name`, `customer_email`, `customer_phone`, `booking_date`, `booking_days`, `service_type`, `location`, `message`, `total_price`, `status`, `payment_status`, `confirmed_at`, `confirmed_by`, `cancelled_at`, `cancel_reason`, `created_at`, `updated_at`) VALUES
(1, 1, 'บริษัท แฟชั่น จำกัด', 'fashion@company.com', '02-123-4567', '2024-11-15', 1, 'Fashion Show', 'ศูนย์การค้าสยามพารากอน', 'ต้องการโมเดลมาซ้อมล่วงหน้า 1 วัน', '5000.00', 'completed', 'unpaid', NULL, NULL, NULL, NULL, '2025-10-14 19:54:30', '2025-10-15 17:40:18'),
(2, 2, 'บริษัท โฆษณา จำกัด', 'ads@company.com', '02-234-5678', '2024-11-20', 2, 'Commercial Shoot', 'สตูดิโอ ถ่ายภาพ กรุงเทพ', 'ถ่ายโฆษณาเครื่องสำอาง', '8000.00', 'confirmed', 'unpaid', NULL, NULL, NULL, NULL, '2025-10-14 19:54:30', '2025-10-14 19:54:30'),
(3, 3, 'นิตยสาร แฟชั่น', 'magazine@fashion.com', '02-345-6789', '2024-11-25', 3, 'Editorial Shoot', 'หาดชะอำ', 'ถ่ายแบบริมทะเล', '18000.00', 'pending', 'unpaid', NULL, NULL, NULL, NULL, '2025-10-14 19:54:30', '2025-10-14 19:54:30'),
(4, 4, 'บริษัท อีเวนท์ จำกัด', 'event@company.com', '02-456-7890', '2024-12-01', 1, 'Product Launch', 'โรงแรม 5 ดาว', 'เป็น MC และโมเดลประจำบูธ', '5000.00', 'pending', 'unpaid', NULL, NULL, NULL, NULL, '2025-10-14 19:54:30', '2025-10-14 19:54:30'),
(5, 5, 'แบรนด์ กีฬา', 'sports@brand.com', '02-567-8901', '2024-12-05', 1, 'Commercial Shoot', 'สนามกีฬา', 'ถ่ายโฆษณาชุดกีฬา', '4500.00', 'confirmed', 'unpaid', NULL, NULL, NULL, NULL, '2025-10-14 19:54:30', '2025-10-14 19:54:30'),
(6, 6, 'บริษัท ท่องเที่ยว', 'travel@company.com', '02-678-9012', '2024-12-10', 4, 'Promotional', 'ภูเก็ต', 'ถ่ายโปรโมทรีสอร์ท', '24000.00', 'completed', 'unpaid', NULL, NULL, NULL, NULL, '2025-10-14 19:54:30', '2025-10-14 19:54:30'),
(7, 7, 'แบรนด์ เครื่องสำอาง', 'beauty@brand.com', '02-789-0123', '2024-12-15', 1, 'Commercial Shoot', 'สตูดิโอ', 'ถ่ายโฆษณาลิปสติก', '3500.00', 'completed', 'unpaid', NULL, NULL, NULL, NULL, '2025-10-14 19:54:30', '2025-10-14 19:54:30'),
(8, 8, 'บริษัท รถยนต์', 'car@company.com', '02-890-1234', '2024-12-20', 2, 'Commercial Shoot', 'โชว์รูม', 'ถ่ายโฆษณารถยนต์รุ่นใหม่', '11000.00', 'pending', 'unpaid', NULL, NULL, NULL, NULL, '2025-10-14 19:54:30', '2025-10-14 19:54:30'),
(9, 9, 'นิตยสาร ไลฟ์สไตล์', 'lifestyle@magazine.com', '02-901-2345', '2024-12-25', 1, 'Editorial Shoot', 'กรุงเทพ', 'ถ่ายแบบไลฟ์สไตล์', '4000.00', 'confirmed', 'unpaid', NULL, NULL, NULL, NULL, '2025-10-14 19:54:30', '2025-10-14 19:54:30'),
(10, 10, 'แบรนด์ แฟชั่น', 'fashion@brand.com', '02-012-3456', '2024-12-30', 1, 'Fashion Show', 'ศูนย์ประชุม', 'งานเปิดตัวคอลเลคชั่นใหม่', '5500.00', 'pending', 'unpaid', NULL, NULL, NULL, NULL, '2025-10-14 19:54:30', '2025-10-14 19:54:30');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('female','male','all') COLLATE utf8mb4_unicode_ci DEFAULT 'all',
  `price_min` decimal(10,2) DEFAULT NULL,
  `price_max` decimal(10,2) DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `code`, `name`, `name_en`, `description`, `icon`, `color`, `gender`, `price_min`, `price_max`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'female-fashion', 'โมเดลแฟชั่นหญิง', 'Fashion Models', 'สำหรับงานถ่ายแฟชั่น แคตตาล็อก และงานรันเวย์', 'fa-female', 'from-pink-500 to-red-primary', 'female', '3000.00', '5000.00', 1, 'active', '2025-10-14 16:48:03', '2025-10-14 16:48:03'),
(2, 'female-photography', 'โมเดลถ่ายภาพหญิง', 'Photography Models', 'สำหรับงานถ่ายภาพโฆษณา แคตตาล็อกสินค้า', 'fa-camera', 'from-purple-500 to-pink-500', 'female', '2500.00', '4000.00', 2, 'active', '2025-10-14 16:48:03', '2025-10-14 16:48:03'),
(3, 'female-event', 'นางแบบอีเวนต์', 'Event Models', 'สำหรับงานแสดงสินค้า งานมอเตอร์โชว์', 'fa-star', 'from-red-primary to-red-light', 'female', '2000.00', '3500.00', 3, 'active', '2025-10-14 16:48:03', '2025-10-14 16:48:03'),
(4, 'male-fashion', 'โมเดลแฟชั่นชาย', 'Male Fashion Models', 'สำหรับงานถ่ายแฟชั่นผู้ชาย แคตตาล็อก', 'fa-male', 'from-blue-500 to-indigo-600', 'male', '3500.00', '6000.00', 4, 'active', '2025-10-14 16:48:03', '2025-10-14 16:48:03'),
(5, 'male-fitness', 'โมเดลฟิตเนส', 'Fitness Models', 'สำหรับงานถ่ายโฆษณาฟิตเนส อาหารเสริม', 'fa-dumbbell', 'from-green-500 to-teal-600', 'male', '3000.00', '5000.00', 5, 'active', '2025-10-14 16:48:03', '2025-10-14 16:48:03'),
(6, 'male-business', 'โมเดลธุรกิจ', 'Business Models', 'สำหรับงานถ่ายโฆษณาธุรกิจ คอร์ปอเรต', 'fa-briefcase', 'from-orange-500 to-red-500', 'male', '2500.00', '4500.00', 6, 'active', '2025-10-14 16:48:03', '2025-10-14 16:48:03');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('new','read','replied','closed') COLLATE utf8mb4_unicode_ci DEFAULT 'new',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `replied_at` datetime DEFAULT NULL,
  `replied_by` int(11) DEFAULT NULL,
  `reply_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `phone`, `service_type`, `message`, `status`, `ip_address`, `user_agent`, `replied_at`, `replied_by`, `reply_message`, `created_at`) VALUES
(1, 'Malee Jande', 'mackbook2024.com@gmail.com', '812345689', 'fashion', 'ปปปปปปปปปป', 'read', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, NULL, NULL, '2025-10-14 18:44:49'),
(2, 'สมชาย ใจดี', 'somchai@email.com', '081-234-5678', 'Fashion Show', 'สนใจจองโมเดลสำหรับงานแฟชั่นโชว์ วันที่ 15 มกราคม 2024', 'new', NULL, NULL, NULL, NULL, NULL, '2025-10-14 19:50:44'),
(3, 'วิภา สวยงาม', 'wipa@email.com', '082-345-6789', 'Photo Shoot', 'ต้องการโมเดลหญิง 2 คน สำหรับถ่ายแบบโฆษณา', 'new', NULL, NULL, NULL, NULL, NULL, '2025-10-14 19:50:44'),
(4, 'ธนพล รุ่งเรือง', 'thanapol@email.com', '083-456-7890', 'Event', 'สอบถามราคาโมเดลสำหรับงานเปิดตัวสินค้า', 'read', NULL, NULL, NULL, NULL, NULL, '2025-10-14 19:50:44'),
(5, 'ปิยะนุช มั่นคง', 'piyanut@email.com', '084-567-8901', 'Commercial', 'ต้องการโมเดลชายสำหรับถ่ายโฆษณารถยนต์', 'read', NULL, NULL, NULL, NULL, NULL, '2025-10-14 19:50:44'),
(6, 'นภัสวรรณ เจริญ', 'napatsawan@email.com', '085-678-9012', 'Fashion Show', 'สนใจจองโมเดล 5 คน สำหรับงาน Fashion Week', 'replied', NULL, NULL, NULL, NULL, NULL, '2025-10-14 19:50:44'),
(7, 'อรุณ สว่างจิต', 'arun@email.com', '086-789-0123', 'Photo Shoot', 'ต้องการโมเดลสำหรับถ่ายแบบนิตยสาร', 'replied', NULL, NULL, NULL, NULL, NULL, '2025-10-14 19:50:44'),
(8, 'ชญานิศ ใจงาม', 'chayanit@email.com', '087-890-1234', 'Event', 'สอบถามข้อมูลการจองโมเดลล่วงหน้า', 'closed', NULL, NULL, NULL, NULL, NULL, '2025-10-14 19:50:44'),
(9, 'ภูมิพัฒน์ สุขใจ', 'phumipat@email.com', '088-901-2345', 'Commercial', 'ต้องการโมเดลสำหรับถ่ายโฆษณาอาหาร', 'new', NULL, NULL, NULL, NULL, NULL, '2025-10-14 19:50:44'),
(10, 'กมลวรรณ ดีมาก', 'kamonwan@email.com', '089-012-3456', 'Fashion Show', 'สนใจจองโมเดลสำหรับงานเปิดตัวแบรนด์', 'new', NULL, NULL, NULL, NULL, NULL, '2025-10-14 19:50:44'),
(11, 'วีรภัทร สมบูรณ์', 'weerapat@email.com', '090-123-4567', 'Photo Shoot', 'ต้องการโมเดลชายและหญิงอย่างละ 2 คน', 'read', NULL, NULL, NULL, NULL, NULL, '2025-10-14 19:50:44'),
(12, 'สมชาย ใจดี', 'somchai@email.com', '081-234-5678', 'Fashion Show', 'สนใจจองโมเดลสำหรับงานแฟชั่นโชว์ วันที่ 15 มกราคม 2024', 'read', NULL, NULL, NULL, NULL, NULL, '2025-10-14 19:54:30'),
(13, 'วิภา สวยงาม', 'wipa@email.com', '082-345-6789', 'Photo Shoot', 'ต้องการโมเดลหญิง 2 คน สำหรับถ่ายแบบโฆษณา', 'new', NULL, NULL, NULL, NULL, NULL, '2025-10-14 19:54:30'),
(14, 'ธนพล รุ่งเรือง', 'thanapol@email.com', '083-456-7890', 'Event', 'สอบถามราคาโมเดลสำหรับงานเปิดตัวสินค้า', 'read', NULL, NULL, NULL, NULL, NULL, '2025-10-14 19:54:30'),
(15, 'ปิยะนุช มั่นคง', 'piyanut@email.com', '084-567-8901', 'Commercial', 'ต้องการโมเดลชายสำหรับถ่ายโฆษณารถยนต์', 'read', NULL, NULL, NULL, NULL, NULL, '2025-10-14 19:54:30'),
(16, 'นภัสวรรณ เจริญ', 'napatsawan@email.com', '085-678-9012', 'Fashion Show', 'สนใจจองโมเดล 5 คน สำหรับงาน Fashion Week', 'replied', NULL, NULL, NULL, NULL, NULL, '2025-10-14 19:54:30'),
(17, 'อรุณ สว่างจิต', 'arun@email.com', '086-789-0123', 'Photo Shoot', 'ต้องการโมเดลสำหรับถ่ายแบบนิตยสาร', 'replied', NULL, NULL, NULL, NULL, NULL, '2025-10-14 19:54:30'),
(18, 'ชญานิศ ใจงาม', 'chayanit@email.com', '087-890-1234', 'Event', 'สอบถามข้อมูลการจองโมเดลล่วงหน้า', 'closed', NULL, NULL, NULL, NULL, NULL, '2025-10-14 19:54:30'),
(19, 'ภูมิพัฒน์ สุขใจ', 'phumipat@email.com', '088-901-2345', 'Commercial', 'ต้องการโมเดลสำหรับถ่ายโฆษณาอาหาร', 'new', NULL, NULL, NULL, NULL, NULL, '2025-10-14 19:54:30'),
(20, 'กมลวรรณ ดีมาก', 'kamonwan@email.com', '089-012-3456', 'Fashion Show', 'สนใจจองโมเดลสำหรับงานเปิดตัวแบรนด์', 'new', NULL, NULL, NULL, NULL, NULL, '2025-10-14 19:54:30'),
(21, 'วีรภัทร สมบูรณ์', 'weerapat@email.com', '090-123-4567', 'Photo Shoot', 'ต้องการโมเดลชายและหญิงอย่างละ 2 คน', 'read', NULL, NULL, NULL, NULL, NULL, '2025-10-14 19:54:30'),
(22, 'Malee Jande', 'mackbook2024.com@gmail.com', '812345689', '', 'xxxxxxxxx', 'read', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, NULL, NULL, '2025-10-14 21:29:53');

-- --------------------------------------------------------

--
-- Table structure for table `customer_reviews`
--

CREATE TABLE `customer_reviews` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_image` varchar(255) DEFAULT NULL,
  `rating` int(1) NOT NULL DEFAULT '5',
  `review_text` text NOT NULL,
  `review_image` varchar(255) DEFAULT NULL,
  `service_type` varchar(100) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT '0',
  `sort_order` int(11) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customer_reviews`
--

INSERT INTO `customer_reviews` (`id`, `customer_name`, `customer_image`, `rating`, `review_text`, `review_image`, `service_type`, `is_featured`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'คุณสมชาย', NULL, 5, 'บริการดีมาก โมเดลน่ารักและเป็นมิตร งานถ่ายออกมาสวยมาก แนะนำเลย!', NULL, 'งานถ่ายภาพสินค้า', 1, 1, 1, '2025-10-16 00:54:14', '2025-10-16 00:54:14'),
(2, 'คุณนิดา', NULL, 5, 'จองง่าย รวดเร็ว โมเดลมาถึงตรงเวลา ราคาเหมาะสมมาก ไว้จะใช้บริการอีกแน่นอน', NULL, 'งานแฟชั่น', 1, 2, 1, '2025-10-16 00:54:14', '2025-10-16 00:54:14'),
(3, 'คุณวิทยา', NULL, 4, 'โมเดลมีประสบการณ์สูง ทำให้งานเสร็จเร็วและมีคุณภาพ ดีใจที่เลือกใช้บริการ', NULL, 'งานโฆษณา', 0, 3, 1, '2025-10-16 00:54:14', '2025-10-16 00:54:14'),
(4, 'คุณส้ม', NULL, 5, 'ทีมงานเป็นมิตรมาก ให้คำแนะนำดี โมเดลมีความเป็นมืออาชีพสูง งานออกมาสมบูรณ์แบบ', NULL, 'งานอีเวนต์', 1, 4, 1, '2025-10-16 00:54:14', '2025-10-16 00:54:14'),
(5, 'คุณแม็ก', NULL, 5, 'ราคาคุ้มค่ามาก ได้โมเดลคุณภาพสูง งานเสร็จตามกำหนด ไว้จะติดต่อใหม่ครับ', NULL, 'งานถ่ายภาพแฟชั่น', 0, 5, 1, '2025-10-16 00:54:14', '2025-10-16 00:54:14');

-- --------------------------------------------------------

--
-- Table structure for table `feature_locks`
--

CREATE TABLE `feature_locks` (
  `id` int(11) NOT NULL,
  `feature` varchar(100) NOT NULL,
  `min_role_level` int(11) NOT NULL DEFAULT '0' COMMENT 'Level ขั้นต่ำที่เข้าถึงได้',
  `is_premium` tinyint(1) DEFAULT '0' COMMENT 'ต้องจ่ายเงินหรือไม่',
  `price` decimal(10,2) DEFAULT '0.00',
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `feature_locks`
--

INSERT INTO `feature_locks` (`id`, `feature`, `min_role_level`, `is_premium`, `price`, `description`, `created_at`) VALUES
(1, 'models', 10, 0, '0.00', 'จัดการโมเดล - ฟรีสำหรับทุก role', '2025-10-15 18:12:10'),
(2, 'articles', 10, 0, '0.00', 'จัดการบทความ - ฟรีสำหรับทุก role', '2025-10-15 18:12:10'),
(3, 'gallery', 10, 0, '0.00', 'แกลเลอรี่ - ฟรีสำหรับทุก role', '2025-10-15 18:12:10'),
(4, 'bookings', 10, 0, '0.00', 'การจอง - ฟรีสำหรับทุก role', '2025-10-15 18:12:10'),
(5, 'contacts', 10, 0, '0.00', 'ข้อความติดต่อ - ฟรีสำหรับทุก role', '2025-10-15 18:12:10'),
(6, 'users', 80, 0, '0.00', 'จัดการผู้ใช้ - เฉพาะ Admin ขึ้นไป', '2025-10-15 18:12:10'),
(7, 'settings', 80, 0, '0.00', 'ตั้งค่าระบบ - เฉพาะ Admin ขึ้นไป', '2025-10-15 18:12:10'),
(8, 'advanced_analytics', 50, 1, '499.00', 'รายงานขั้นสูง - Premium Feature', '2025-10-15 18:12:10'),
(9, 'api_access', 50, 1, '999.00', 'API Access - Premium Feature', '2025-10-15 18:12:10'),
(10, 'white_label', 100, 1, '2999.00', 'White Label - Programmer Only', '2025-10-15 18:12:10');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_albums`
--

CREATE TABLE `gallery_albums` (
  `id` int(11) NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery_albums`
--

INSERT INTO `gallery_albums` (`id`, `name`, `slug`, `description`, `cover_image`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'งานแฟชั่น', 'fashion-work', 'ผลงานถ่ายภาพแฟชั่นและนิตยสาร', NULL, 1, 'active', '2025-10-14 21:52:02', '2025-10-14 21:52:02'),
(2, 'งานอีเวนต์', 'event-work', 'ภาพบรรยากาศงานอีเวนต์ต่างๆ', NULL, 2, 'active', '2025-10-14 21:52:02', '2025-10-14 21:52:02'),
(3, 'งานโฆษณา', 'commercial-work', 'ผลงานถ่ายภาพโฆษณาและแคมเปญ', NULL, 3, 'active', '2025-10-14 21:52:02', '2025-10-14 21:52:02'),
(4, 'Behind The Scenes', 'behind-the-scenes', 'ภาพเบื้องหลังการถ่ายทำ', NULL, 4, 'active', '2025-10-14 21:52:02', '2025-10-14 21:52:02'),
(5, 'Portfolio โมเดล', 'model-portfolio', 'พอร์ตโฟลิโอของโมเดลและนางแบบ', NULL, 5, 'active', '2025-10-14 21:52:02', '2025-10-14 21:52:02');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_images`
--

CREATE TABLE `gallery_images` (
  `id` int(11) NOT NULL,
  `album_id` int(11) DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` int(11) DEFAULT NULL COMMENT 'Size in bytes',
  `file_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `thumbnail_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags` text COLLATE utf8mb4_unicode_ci COMMENT 'Comma-separated tags',
  `sort_order` int(11) DEFAULT '0',
  `view_count` int(11) DEFAULT '0',
  `download_count` int(11) DEFAULT '0',
  `uploaded_by` int(11) DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery_images`
--

INSERT INTO `gallery_images` (`id`, `album_id`, `title`, `description`, `file_name`, `file_path`, `file_size`, `file_type`, `width`, `height`, `thumbnail_path`, `tags`, `sort_order`, `view_count`, `download_count`, `uploaded_by`, `status`, `created_at`, `updated_at`) VALUES
(2, 1, '23', '&lt;br /&gt;\r\n&lt;b&gt;Deprecated&lt;/b&gt;:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated in &lt;b&gt;C:MAMPhtdocsvibedaybkkadmingalleryedit.php&lt;/b&gt; on line &lt;b&gt;155&lt;/b&gt;&lt;br /&gt;', '68efd01bf1aef_1760546843.jpg', 'uploads/gallery/68efd01bf1aef_1760546843.jpg', 1440933, 'image/jpeg', 2000, 2000, 'uploads/gallery/thumbs/thumb_68efd01bf1aef_1760546843.jpg', '&lt;br /&gt;&lt;b&gt;Deprecated&lt;/b&gt;:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated in &lt;b&gt;C:MAMPhtdocsvibedaybkkadmingalleryedit.php&lt;/b&gt; on line &lt;b&gt;172&lt;/b&gt;&lt;br /&gt;', 0, 2, 0, 1, 'active', '2025-10-15 16:47:24', '2025-10-15 17:02:50'),
(4, 2, '56', NULL, '68efd3c7227f6_1760547783.jpg', 'uploads/gallery/68efd3c7227f6_1760547783.jpg', 1113362, 'image/jpeg', 1920, 1080, 'uploads/gallery/thumbs/thumb_68efd3c7227f6_1760547783.jpg', NULL, 0, 2, 0, 1, 'active', '2025-10-15 17:03:03', '2025-10-15 17:17:15'),
(5, 2, '68', NULL, '68efd402b1aff_1760547842.jpg', 'uploads/gallery/68efd402b1aff_1760547842.jpg', 709624, 'image/jpeg', 1200, 1200, 'uploads/gallery/thumbs/thumb_68efd402b1aff_1760547842.jpg', NULL, 0, 5, 0, 1, 'active', '2025-10-15 17:04:02', '2025-10-15 17:41:53');

-- --------------------------------------------------------

--
-- Table structure for table `homepage_columns`
--

CREATE TABLE `homepage_columns` (
  `id` int(11) NOT NULL,
  `section_key` varchar(50) NOT NULL,
  `column_index` int(11) NOT NULL DEFAULT '1',
  `column_title` varchar(255) DEFAULT NULL,
  `column_content` longtext,
  `column_image` varchar(255) DEFAULT NULL,
  `column_link` varchar(255) DEFAULT NULL,
  `column_class` varchar(100) DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `homepage_features`
--

CREATE TABLE `homepage_features` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'FontAwesome icon class',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int(11) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `homepage_features`
--

INSERT INTO `homepage_features` (`id`, `section_id`, `icon`, `title`, `description`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 6, 'fa-users', '500+', 'โมเดลมืออาชีพ', 1, 1, '2025-10-14 18:44:36'),
(2, 6, 'fa-briefcase', '1,000+', 'โปรเจกต์สำเร็จ', 2, 1, '2025-10-14 18:44:36'),
(3, 6, 'fa-award', '10+', 'ปีประสบการณ์', 3, 1, '2025-10-14 18:44:36'),
(4, 6, 'fa-smile', '100%', 'ความพึงพอใจ', 4, 1, '2025-10-14 18:44:36'),
(8, 2, 'fa-check-circle', 'โมเดลมืออาชีพ', 'คัดสรรโมเดลคุณภาพสูง ผ่านการฝึกฝนมาเป็นอย่างดี', 1, 1, '2025-10-14 18:44:36'),
(9, 2, 'fa-clock', 'บริการรวดเร็ว', 'ตอบสนองความต้องการได้อย่างรวดเร็ว ทันเวลา', 2, 1, '2025-10-14 18:44:36'),
(10, 2, 'fa-dollar-sign', 'ราคายุติธรรม', 'ราคาที่เหมาะสม คุ้มค่ากับคุณภาพที่ได้รับ', 3, 1, '2025-10-14 18:44:36'),
(11, 2, 'fa-headset', 'ซัพพอร์ตตลอด 24/7', 'ทีมงานพร้อมให้คำปรึกษาและช่วยเหลือตลอดเวลา', 4, 1, '2025-10-14 18:44:36');

-- --------------------------------------------------------

--
-- Table structure for table `homepage_gallery`
--

CREATE TABLE `homepage_gallery` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `link_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `homepage_gallery`
--

INSERT INTO `homepage_gallery` (`id`, `section_id`, `image_path`, `title`, `description`, `link_url`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 4, 'placeholder-gallery-1.jpg', 'Fashion Show 2024', 'Bangkok Fashion Week', NULL, 1, 1, '2025-10-14 18:44:36'),
(2, 4, 'placeholder-gallery-2.jpg', 'Commercial Shoot', 'Product Photography', NULL, 2, 1, '2025-10-14 18:44:36'),
(3, 4, 'placeholder-gallery-3.jpg', 'Event Coverage', 'Corporate Event', NULL, 3, 1, '2025-10-14 18:44:36'),
(4, 4, 'placeholder-gallery-4.jpg', 'Magazine Editorial', 'Fashion Magazine', NULL, 4, 1, '2025-10-14 18:44:36');

-- --------------------------------------------------------

--
-- Table structure for table `homepage_sections`
--

CREATE TABLE `homepage_sections` (
  `id` int(11) NOT NULL,
  `section_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique key for section',
  `section_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Display name',
  `section_type` enum('hero','about','services','gallery','testimonials','cta','stats','features') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `sort_order` int(11) DEFAULT '0',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `button_text` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `background_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `background_position` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'center',
  `background_size` enum('cover','contain','auto','fixed') COLLATE utf8mb4_unicode_ci DEFAULT 'cover',
  `background_repeat` enum('no-repeat','repeat','repeat-x','repeat-y') COLLATE utf8mb4_unicode_ci DEFAULT 'no-repeat',
  `background_attachment` enum('scroll','fixed') COLLATE utf8mb4_unicode_ci DEFAULT 'scroll',
  `overlay_color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `overlay_opacity` decimal(3,2) DEFAULT '0.50',
  `background_color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text_color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `settings` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON settings for section',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `background_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'color',
  `left_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `left_image_size_desktop` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'w-80 h-80',
  `left_image_size_mobile` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'w-64 h-64',
  `left_image_position` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'left'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `homepage_sections`
--

INSERT INTO `homepage_sections` (`id`, `section_key`, `section_name`, `section_type`, `is_active`, `sort_order`, `title`, `subtitle`, `description`, `button_text`, `button_link`, `background_image`, `background_position`, `background_size`, `background_repeat`, `background_attachment`, `overlay_color`, `overlay_opacity`, `background_color`, `text_color`, `settings`, `created_at`, `updated_at`, `background_type`, `left_image`, `left_image_size_desktop`, `left_image_size_mobile`, `left_image_position`) VALUES
(1, 'hero', 'Hero Section', 'hero', 1, 1, 'VIBEDAYBKK', 'บริการโมเดลและนางแบบมืออาชีพ', 'เราคือผู้เชี่ยวชาญด้านบริการโมเดลและนางแบบคุณภาพสูง พร้อมให้บริการสำหรับงานถ่ายภาพ งานแฟชั่น และงานอีเวนต์ต่างๆ ด้วยทีมงานมืออาชีพและโมเดลที่ผ่านการคัดสรรอย่างดี', 'ดูบริการของเรา', 'services.php', 'uploads/homepage/68f11a63cb37b_1760631395.jpg', 'top', 'auto', 'no-repeat', 'scroll', NULL, '0.50', '#171717', '#ffffff', '{\"custom_css\":\"\",\"animation_class\":\"\",\"section_id\":\"hero\",\"section_class\":\"\",\"container_class\":\"hero-gradient min-h-screen flex items-center pt-16 animate-fade-in\",\"padding_top\":\"py-20\",\"padding_bottom\":\"py-16\",\"show_title\":\"1\",\"show_subtitle\":\"1\",\"show_description\":\"1\",\"show_button\":\"1\",\"overlay_opacity\":\"0.5\",\"text_align\":\"center\",\"animation\":\"fade-in\",\"title_colors\":{\"vibe\":\"#dc2626\",\"vibe_text\":\"#dc2626\",\"day\":\"#ffffff\",\"day_text\":\"#ffffff\",\"bkk\":\"#dc2626\",\"bkk_text\":\"#dc2626\"}}', '2025-10-14 18:44:36', '2025-10-16 17:45:41', 'color', NULL, 'w-80 h-80', 'w-64 h-64', 'left'),
(2, 'about', 'About Section', 'about', 1, 2, 'เกี่ยวกับเรา', 'มืออาชีพ ประสบการณ์สูง', 'VIBEDAYBKK เป็นเอเจนซี่โมเดลชั้นนำในกรุงเทพฯ ที่มีประสบการณ์กว่า 10 ปี ในการจัดหาโมเดลมืออาชีพสำหรับงานแฟชั่น อีเวนท์ และโฆษณา เรามีโมเดลที่ผ่านการคัดสรรและฝึกฝนมาอย่างดี พร้อมให้บริการในทุกประเภทงาน', 'อ่านเพิ่มเติม', 'about.php', NULL, 'center', 'cover', 'no-repeat', 'scroll', NULL, '0.50', '#1a1a1a', '#ffffff', '{\"custom_css\":\"\",\"animation_class\":\"\",\"section_id\":\"about\",\"section_class\":\"\",\"container_class\":\"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8\",\"padding_top\":\"py-20\",\"padding_bottom\":\"\",\"show_title\":\"1\",\"show_subtitle\":\"1\",\"show_description\":\"1\",\"show_button\":\"1\",\"layout\":\"image-text\",\"image_position\":\"left\",\"left_image_size_desktop\":\"w-96 h-96\",\"left_image_size_mobile\":\"w-48 h-48\",\"left_image_position\":\"left\"}', '2025-10-14 18:44:36', '2025-10-16 18:43:24', 'color', 'uploads/homepage/68f139b43f4e3_1760639412.jpg', 'w-96 h-96', 'w-48 h-48', 'left'),
(3, 'services', 'Services Section', 'services', 1, 3, 'บริการของเรา', 'หมวดหมู่โมเดลที่หลากหลาย', 'เรามีโมเดลมืออาชีพให้เลือกหลากหลายประเภท ตามความต้องการของคุณ', 'ดูทั้งหมด', 'services.php', NULL, 'center', 'cover', 'no-repeat', 'scroll', NULL, '0.50', '#181818', '#ffffff', '{\"columns\":\"2\",\"show_price\":\"1\"}', '2025-10-14 18:44:36', '2025-10-15 23:44:08', 'color', NULL, 'w-80 h-80', 'w-64 h-64', 'left'),
(4, 'gallery', 'Gallery Section', 'gallery', 0, 4, 'ผลงานของเรา', 'ภาพจากงานที่ผ่านมา', 'ชมผลงานและประสบการณ์ของโมเดลจากงานต่างๆ ที่เราได้ร่วมงานมา', 'ดูทั้งหมด', 'gallery.php', NULL, 'center', 'cover', 'no-repeat', 'scroll', NULL, '0.50', '#1a1a1a', '#ffffff', '{\"columns\": \"4\", \"lightbox\": true, \"autoplay\": false}', '2025-10-14 18:44:36', '2025-10-16 00:28:29', 'color', NULL, 'w-80 h-80', 'w-64 h-64', 'left'),
(5, 'testimonials', 'Testimonials Section', 'testimonials', 1, 5, 'ความคิดเห็นจากลูกค้า', 'สิ่งที่ลูกค้าพูดถึงเรา', 'เราภูมิใจที่ได้รับความไว้วางใจจากลูกค้ามากมาย', '', '', NULL, 'center', 'cover', 'no-repeat', 'scroll', NULL, '0.50', '#1c1c1c', '#ffffff', '{\"custom_css\":\"\",\"animation_class\":\"\",\"section_id\":\"testimonials\",\"section_class\":\"py-20\",\"container_class\":\"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8\",\"padding_top\":\"py-20\",\"padding_bottom\":\"\",\"show_title\":\"1\",\"show_subtitle\":\"1\",\"show_description\":\"1\",\"show_button\":\"1\",\"carousel\":\"3\",\"autoplay\":\"1\",\"interval\":\"5000\"}', '2025-10-14 18:44:36', '2025-10-16 00:37:51', 'color', NULL, 'w-80 h-80', 'w-64 h-64', 'left'),
(6, 'stats', 'Statistics Section', 'stats', 0, 6, 'ตัวเลขที่น่าประทับใจ', NULL, NULL, NULL, NULL, NULL, 'center', 'cover', 'no-repeat', 'scroll', NULL, '0.50', '#dc2626', '#ffffff', '{\"columns\": \"4\"}', '2025-10-14 18:44:36', '2025-10-16 00:28:12', 'color', NULL, 'w-80 h-80', 'w-64 h-64', 'left'),
(7, 'cta', 'Call to Action', 'cta', 0, 7, 'พร้อมเริ่มโปรเจกต์กับเราแล้วหรือยัง?', 'ติดต่อเราวันนี้เพื่อรับคำปรึกษาฟรี', 'ทีมงานของเราพร้อมให้คำแนะนำและหาโมเดลที่เหมาะสมกับงานของคุณ', 'ติดต่อเรา', 'contact.php', NULL, 'center', 'cover', 'no-repeat', 'scroll', NULL, '0.50', '#dc2626', '#ffffff', '{\"button_style\": \"large\", \"show_phone\": true}', '2025-10-14 18:44:36', '2025-10-16 00:27:42', 'color', NULL, 'w-80 h-80', 'w-64 h-64', 'left'),
(8, 'contact', 'Contact Section', 'features', 1, 9, 'ติดต่อเรา', 'พร้อมให้คำปรึกษาและรับจองบริการ', 'ติดต่อเราเพื่อรับคำปรึกษาและจองบริการ', NULL, NULL, NULL, 'center', 'cover', 'no-repeat', 'scroll', NULL, '0.50', '#1a1a1a', '#ffffff', '{\"section_id\":\"contact\",\"section_class\":\"py-20 bg-dark-light\",\"show_title\":true,\"show_subtitle\":true,\"show_description\":true,\"show_button\":true}', '2025-10-16 00:32:53', '2025-10-16 00:33:29', 'color', NULL, 'w-80 h-80', 'w-64 h-64', 'left');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target` enum('_self','_blank') COLLATE utf8mb4_unicode_ci DEFAULT '_self',
  `sort_order` int(11) DEFAULT '0',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `parent_id`, `title`, `url`, `icon`, `target`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(2, NULL, 'เกี่ยวกับเรา', 'index.php#about', 'fa-info-circle', '_self', 2, 'active', '2025-10-14 16:48:03', '2025-10-14 21:46:42'),
(5, NULL, 'ติดต่อ', 'index.php#contact', 'fa-envelope', '_self', 5, 'active', '2025-10-14 16:48:03', '2025-10-14 21:31:30'),
(6, NULL, 'หน้าแรก', 'index.php', 'fa-home', '_self', 1, 'active', '2025-10-14 19:50:44', '2025-10-14 21:46:42'),
(8, NULL, 'บริการ', 'services.php', 'fa-briefcase', '_self', 3, 'active', '2025-10-14 19:50:44', '2025-10-14 19:50:44'),
(9, NULL, 'โมเดล', 'models.php', 'fa-users', '_self', 4, 'inactive', '2025-10-14 19:50:44', '2025-10-14 21:14:07'),
(10, NULL, 'บทความ', 'articles.php', 'fa-newspaper', '_self', 6, 'active', '2025-10-14 19:50:44', '2025-10-14 21:42:50'),
(11, NULL, 'แกลเลอรี่', 'gallery.php', 'fa-images', '_self', 7, 'active', '2025-10-14 19:50:44', '2025-10-14 21:42:50'),
(12, NULL, 'ติดต่อเรา', 'contact.php', 'fa-envelope', '_self', 8, 'inactive', '2025-10-14 19:50:44', '2025-10-14 21:42:50');

-- --------------------------------------------------------

--
-- Table structure for table `models`
--

CREATE TABLE `models` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price_min` decimal(10,2) DEFAULT NULL,
  `price_max` decimal(10,2) DEFAULT NULL,
  `height` int(11) DEFAULT NULL COMMENT 'ส่วนสูงในหน่วย cm',
  `weight` int(11) DEFAULT NULL COMMENT 'น้ำหนักในหน่วย kg',
  `bust` int(11) DEFAULT NULL COMMENT 'รอบอก (สำหรับหญิง)',
  `waist` int(11) DEFAULT NULL COMMENT 'รอบเอว',
  `hips` int(11) DEFAULT NULL COMMENT 'รอบสะโพก',
  `experience_years` int(11) DEFAULT '0',
  `age` int(11) DEFAULT NULL,
  `skin_tone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hair_color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eye_color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `languages` text COLLATE utf8mb4_unicode_ci COMMENT 'ภาษาที่พูดได้',
  `skills` text COLLATE utf8mb4_unicode_ci COMMENT 'ความสามารถพิเศษ',
  `featured` tinyint(1) DEFAULT '0',
  `status` enum('available','busy','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'available',
  `view_count` int(11) DEFAULT '0',
  `booking_count` int(11) DEFAULT '0',
  `rating` decimal(3,2) DEFAULT '0.00',
  `sort_order` int(11) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `models`
--

INSERT INTO `models` (`id`, `category_id`, `code`, `name`, `name_en`, `description`, `price_min`, `price_max`, `height`, `weight`, `bust`, `waist`, `hips`, `experience_years`, `age`, `skin_tone`, `hair_color`, `eye_color`, `languages`, `skills`, `featured`, `status`, `view_count`, `booking_count`, `rating`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'FM001', 'สุภาพร สวยงาม', 'Supaporn Suayngam', 'โมเดลมืออาชีพ มีประสบการณ์ในงานแฟชั่นโชว์และถ่ายแบบ', '3000.00', '5000.00', 170, 48, 34, 24, 36, 5, 25, 'ขาว', 'ดำ', 'น้ำตาล', 'ไทย, อังกฤษ', 'เดินรันเวย์, ถ่ายแบบ, MC', 1, 'available', 5, 0, '0.00', 1, '2025-10-14 19:50:44', '2025-10-16 14:15:29'),
(2, 1, 'FM002', 'วรรณา ใจดี', 'Wanna Jaidee', 'โมเดลรุ่นใหม่ มีความสดใส เหมาะกับงานอีเวนท์', '2500.00', '4000.00', 168, 47, 33, 23, 35, 3, 23, 'ขาวเหลือง', 'น้ำตาล', 'น้ำตาล', 'ไทย, อังกฤษ', 'เดินรันเวย์, ถ่ายแบบ', 1, 'available', 0, 0, '0.00', 2, '2025-10-14 19:50:44', '2025-10-14 19:50:44'),
(3, 1, 'FM003', 'ปิยะนุช มั่นคง', 'Piyanut Munkong', 'โมเดลที่มีความเป็นมืออาชีพสูง ทำงานได้หลากหลาย', '3500.00', '6000.00', 172, 50, 35, 25, 37, 7, 27, 'ขาว', 'ดำ', 'ดำ', 'ไทย, อังกฤษ, จีน', 'เดินรันเวย์, ถ่ายแบบ, MC, พรีเซ็นเตอร์', 1, 'available', 0, 0, '0.00', 3, '2025-10-14 19:50:44', '2025-10-14 19:50:44'),
(4, 2, 'MM001', 'ธนพล หล่อเหลา', 'Thanapol Lolao', 'โมเดลชายมืออาชีพ รูปร่างสมส่วน', '3000.00', '5000.00', 180, 70, NULL, NULL, NULL, 6, 28, 'ขาวเหลือง', 'ดำ', 'น้ำตาล', 'ไทย, อังกฤษ', 'เดินรันเวย์, ถ่ายแบบ, แอคติ้ง', 1, 'available', 1, 0, '0.00', 4, '2025-10-14 19:50:44', '2025-10-16 11:18:18'),
(5, 2, 'MM002', 'สมชาย ใจกล้า', 'Somchai Jaikla', 'โมเดลชายที่มีบุคลิกเด่น เหมาะกับงานโฆษณา', '2800.00', '4500.00', 178, 68, NULL, NULL, NULL, 4, 26, 'ขาว', 'น้ำตาล', 'น้ำตาล', 'ไทย, อังกฤษ', 'ถ่ายแบบ, พรีเซ็นเตอร์', 0, 'available', 0, 0, '0.00', 5, '2025-10-14 19:50:44', '2025-10-14 19:50:44'),
(6, 1, 'FM004', 'ชญานิศ สง่างาม', 'Chayanit Sangngam', 'โมเดลที่มีความสง่างาม เหมาะกับงานแฟชั่นระดับสูง', '4000.00', '7000.00', 175, 52, 36, 26, 38, 8, 29, 'ขาว', 'ดำ', 'ดำ', 'ไทย, อังกฤษ, ฝรั่งเศส', 'เดินรันเวย์, ถ่ายแบบ, MC', 1, 'available', 0, 0, '0.00', 6, '2025-10-14 19:50:44', '2025-10-14 19:50:44'),
(7, 1, 'FM005', 'นภัสวรรณ รุ่งเรือง', 'Napatsawan Rungruang', 'โมเดลหน้าใหม่ มีความสดใสและมีพลัง', '2000.00', '3500.00', 165, 45, 32, 23, 34, 2, 22, 'ขาวเหลือง', 'น้ำตาล', 'น้ำตาล', 'ไทย, อังกฤษ', 'เดินรันเวย์, ถ่ายแบบ', 0, 'available', 0, 0, '0.00', 7, '2025-10-14 19:50:44', '2025-10-14 19:50:44'),
(8, 2, 'MM003', 'ภูมิพัฒน์ เจริญสุข', 'Phumipat Charoensuk', 'โมเดลชายที่มีความมั่นใจ เหมาะกับงานแฟชั่น', '3200.00', '5500.00', 182, 72, NULL, NULL, NULL, 7, 30, 'ขาว', 'ดำ', 'ดำ', 'ไทย, อังกฤษ', 'เดินรันเวย์, ถ่ายแบบ, แอคติ้ง', 1, 'available', 0, 0, '0.00', 8, '2025-10-14 19:50:44', '2025-10-14 19:50:44'),
(9, 1, 'FM006', 'กมลวรรณ ศรีสุข', 'Kamonwan Srisuk', 'โมเดลที่มีความเป็นธรรมชาติ เหมาะกับงานถ่ายแบบ', '2500.00', '4000.00', 167, 46, 33, 24, 35, 4, 24, 'ขาว', 'น้ำตาลอ่อน', 'น้ำตาล', 'ไทย, อังกฤษ', 'ถ่ายแบบ, พรีเซ็นเตอร์', 0, 'available', 0, 0, '0.00', 9, '2025-10-14 19:50:44', '2025-10-14 19:50:44'),
(10, 2, 'MM004', 'วีรภัทร แข็งแรง', 'Weerapat Kaengraeng', 'โมเดลชายที่มีรูปร่างแข็งแรง เหมาะกับงานสปอร์ต', '2800.00', '4500.00', 179, 75, NULL, NULL, NULL, 5, 27, 'ขาวเหลือง', 'ดำ', 'น้ำตาล', 'ไทย, อังกฤษ', 'เดินรันเวย์, ถ่ายแบบ, ฟิตเนส', 0, 'available', 0, 0, '0.00', 10, '2025-10-14 19:50:44', '2025-10-14 19:50:44');

-- --------------------------------------------------------

--
-- Table structure for table `model_images`
--

CREATE TABLE `model_images` (
  `id` int(11) NOT NULL,
  `model_id` int(11) NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_type` enum('profile','portfolio','cover') COLLATE utf8mb4_unicode_ci DEFAULT 'portfolio',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT '0',
  `sort_order` int(11) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_requirements`
--

CREATE TABLE `model_requirements` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `requirement` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int(11) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_requirements`
--

INSERT INTO `model_requirements` (`id`, `category_id`, `requirement`, `sort_order`, `created_at`) VALUES
(1, 1, 'ส่วนสูง 165-175 cm', 1, '2025-10-14 16:48:03'),
(2, 1, 'รูปร่างสมส่วนและสวยงาม', 2, '2025-10-14 16:48:03'),
(3, 1, 'มีประสบการณ์งานแฟชั่น', 3, '2025-10-14 16:48:03'),
(4, 1, 'เดินรันเวย์ได้อย่างมืออาชีพ', 4, '2025-10-14 16:48:03'),
(5, 1, 'มีพอร์ตโฟลิโอครบถ้วน', 5, '2025-10-14 16:48:03');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `role_key` varchar(50) NOT NULL,
  `feature` varchar(100) NOT NULL COMMENT 'models, articles, bookings, etc.',
  `can_view` tinyint(1) DEFAULT '1',
  `can_create` tinyint(1) DEFAULT '0',
  `can_edit` tinyint(1) DEFAULT '0',
  `can_delete` tinyint(1) DEFAULT '0',
  `can_export` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `role_key`, `feature`, `can_view`, `can_create`, `can_edit`, `can_delete`, `can_export`, `created_at`) VALUES
(1, 'programmer', 'models', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(2, 'programmer', 'categories', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(3, 'programmer', 'articles', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(4, 'programmer', 'article_categories', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(5, 'programmer', 'bookings', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(6, 'programmer', 'contacts', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(7, 'programmer', 'menus', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(8, 'programmer', 'users', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(9, 'programmer', 'gallery', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(10, 'programmer', 'settings', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(11, 'programmer', 'homepage', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(12, 'admin', 'models', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(13, 'admin', 'categories', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(14, 'admin', 'articles', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(15, 'admin', 'article_categories', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(16, 'admin', 'bookings', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(17, 'admin', 'contacts', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(18, 'admin', 'menus', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(19, 'admin', 'users', 1, 1, 1, 0, 1, '2025-10-15 18:12:10'),
(20, 'admin', 'gallery', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(21, 'admin', 'settings', 1, 0, 1, 0, 0, '2025-10-15 18:12:10'),
(22, 'admin', 'homepage', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(23, 'editor', 'models', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(24, 'editor', 'categories', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(25, 'editor', 'articles', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(26, 'editor', 'article_categories', 1, 0, 0, 0, 0, '2025-10-15 18:12:10'),
(27, 'editor', 'bookings', 1, 0, 0, 0, 0, '2025-10-15 18:12:10'),
(28, 'editor', 'contacts', 1, 1, 0, 0, 0, '2025-10-15 18:12:10'),
(29, 'editor', 'menus', 1, 0, 0, 0, 0, '2025-10-15 18:12:10'),
(30, 'editor', 'users', 1, 0, 0, 0, 0, '2025-10-15 18:12:10'),
(31, 'editor', 'gallery', 1, 0, 0, 0, 0, '2025-10-15 18:12:10'),
(32, 'editor', 'settings', 1, 0, 0, 0, 0, '2025-10-15 18:12:10'),
(33, 'editor', 'homepage', 1, 1, 1, 1, 1, '2025-10-15 18:12:10'),
(34, 'viewer', 'models', 1, 0, 0, 0, 1, '2025-10-15 18:12:10'),
(35, 'viewer', 'categories', 1, 0, 0, 0, 1, '2025-10-15 18:12:10'),
(36, 'viewer', 'articles', 1, 0, 0, 0, 1, '2025-10-15 18:12:10'),
(37, 'viewer', 'article_categories', 1, 0, 0, 0, 1, '2025-10-15 18:12:10'),
(38, 'viewer', 'bookings', 1, 0, 0, 0, 1, '2025-10-15 18:12:10'),
(39, 'viewer', 'contacts', 1, 0, 0, 0, 1, '2025-10-15 18:12:10'),
(40, 'viewer', 'menus', 1, 0, 0, 0, 0, '2025-10-15 18:12:10'),
(41, 'viewer', 'users', 0, 0, 0, 0, 0, '2025-10-15 18:12:10'),
(42, 'viewer', 'gallery', 1, 0, 0, 0, 1, '2025-10-15 18:12:10'),
(43, 'viewer', 'settings', 0, 0, 0, 0, 0, '2025-10-15 18:12:10'),
(44, 'viewer', 'homepage', 1, 0, 0, 0, 0, '2025-10-15 18:12:10');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `role_key` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `description` text,
  `level` int(11) NOT NULL DEFAULT '0' COMMENT 'Level สูงสุด = สิทธิ์มากสุด',
  `color` varchar(50) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT '0.00' COMMENT 'ราคาสำหรับอัพเกรด',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `role_key`, `display_name`, `description`, `level`, `color`, `icon`, `price`, `is_active`, `created_at`) VALUES
(1, 'Programmer', 'programmer', 'โปรแกรมเมอร์', 'สิทธิ์สูงสุด - เข้าถึงทุกอย่างรวมถึงการตั้งค่าระบบ', 100, 'bg-purple-600', 'fa-code', '0.00', 1, '2025-10-15 18:12:10'),
(2, 'Admin', 'admin', 'ผู้ดูแลระบบ', 'จัดการทุกอย่างยกเว้นการตั้งค่าระบบ', 80, 'bg-red-600', 'fa-user-shield', '0.00', 1, '2025-10-15 18:12:10'),
(3, 'Editor', 'editor', 'บรรณาธิการ', 'สามารถเพิ่ม แก้ไข ลบข้อมูลได้', 50, 'bg-blue-600', 'fa-user-edit', '999.00', 1, '2025-10-15 18:12:10'),
(4, 'Viewer', 'viewer', 'ผู้ดู', 'ดูข้อมูลได้อย่างเดียว ไม่สามารถแก้ไขได้', 10, 'bg-gray-600', 'fa-eye', '0.00', 1, '2025-10-15 18:12:10');

-- --------------------------------------------------------

--
-- Table structure for table `role_upgrades`
--

CREATE TABLE `role_upgrades` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `from_role` varchar(50) NOT NULL,
  `to_role` varchar(50) NOT NULL,
  `price_paid` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_ref` varchar(100) DEFAULT NULL,
  `payment_status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `expires_at` datetime DEFAULT NULL COMMENT 'วันหมดอายุ (ถ้ามี)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `role_upgrades`
--

INSERT INTO `role_upgrades` (`id`, `user_id`, `from_role`, `to_role`, `price_paid`, `payment_method`, `payment_ref`, `payment_status`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 13, 'editor', 'admin', '0.00', 'manual', NULL, 'pending', NULL, '2025-10-15 19:03:39', '2025-10-15 19:03:39');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `setting_type` enum('text','number','boolean','json') COLLATE utf8mb4_unicode_ci DEFAULT 'text',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'VIBEDAY', 'text', 'ชื่อเว็บไซต์', '2025-10-14 16:48:03', '2025-10-14 17:31:57'),
(2, 'site_email', 'info@vibedaybkk.com', 'text', 'อีเมลติดต่อ', '2025-10-14 16:48:03', '2025-10-14 16:48:03'),
(3, 'site_phone', '02-123-4567', 'text', 'เบอร์โทรศัพท์', '2025-10-14 16:48:03', '2025-10-14 16:48:03'),
(4, 'site_line', '@vibedaybkk', 'text', 'LINE ID', '2025-10-14 16:48:03', '2025-10-14 16:48:03'),
(5, 'site_address', '123 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110', 'text', 'ที่อยู่', '2025-10-14 16:48:03', '2025-10-14 16:48:03'),
(6, 'booking_advance_days', '3', 'number', 'จองล่วงหน้าอย่างน้อย (วัน)', '2025-10-14 16:48:03', '2025-10-14 16:48:03'),
(7, 'items_per_page', '12', 'number', 'จำนวนรายการต่อหน้า', '2025-10-14 16:48:03', '2025-10-14 16:48:03'),
(8, 'enable_registration', 'false', 'boolean', 'เปิดให้สมัครสมาชิก', '2025-10-14 16:48:03', '2025-10-14 16:48:03'),
(9, 'logo_type', 'text', 'text', 'ประเภทโลโก้ (text, image)', '2025-10-14 17:42:37', '2025-10-14 17:42:37'),
(10, 'logo_text', 'VIBEDAY2', 'text', 'ข้อความโลโก้', '2025-10-14 17:42:37', '2025-10-14 17:42:53'),
(11, 'logo_image', NULL, 'text', 'รูปภาพโลโก้', '2025-10-14 17:42:37', '2025-10-16 15:03:20'),
(12, 'favicon', '', 'text', 'Favicon', '2025-10-14 17:42:37', '2025-10-14 17:42:37'),
(44, 'social_facebook_url', 'https://www.facebook.com/Lollipopdekn', 'text', 'Facebook URL', '2025-10-14 21:25:49', '2025-10-16 13:34:57'),
(45, 'social_facebook_icon', 'fa-facebook-f', 'text', 'Facebook Icon', '2025-10-14 21:25:49', '2025-10-14 21:25:49'),
(46, 'social_facebook_enabled', '1', 'boolean', 'Enable Facebook', '2025-10-14 21:25:49', '2025-10-16 13:52:37'),
(47, 'social_instagram_url', 'https://www.instagram.com/lollipop_modeling/', 'text', 'Instagram URL', '2025-10-14 21:25:49', '2025-10-16 13:34:57'),
(48, 'social_instagram_icon', 'fa-instagram', 'text', 'Instagram Icon', '2025-10-14 21:25:49', '2025-10-14 21:25:49'),
(49, 'social_instagram_enabled', '1', 'boolean', 'Enable Instagram', '2025-10-14 21:25:49', '2025-10-16 13:52:38'),
(50, 'social_twitter_url', 'https://twitter.com/vibedaybkk', 'text', 'Twitter URL', '2025-10-14 21:25:49', '2025-10-14 21:25:49'),
(51, 'social_twitter_icon', 'fa-twitter', 'text', 'Twitter Icon', '2025-10-14 21:25:49', '2025-10-14 21:25:49'),
(52, 'social_twitter_enabled', '0', 'boolean', 'Enable Twitter', '2025-10-14 21:25:49', '2025-10-16 13:36:16'),
(53, 'social_line_url', 'http://line.me/ti/p/@dekn', 'text', 'LINE URL', '2025-10-14 21:25:49', '2025-10-16 13:34:57'),
(54, 'social_line_icon', 'fa-line', 'text', 'LINE Icon', '2025-10-14 21:25:49', '2025-10-14 21:25:49'),
(55, 'social_line_enabled', '1', 'boolean', 'Enable LINE', '2025-10-14 21:25:49', '2025-10-16 13:35:42'),
(56, 'social_youtube_url', 'https://youtube.com/@lollipop_modeling', 'text', 'YouTube URL', '2025-10-14 21:25:49', '2025-10-16 13:35:28'),
(57, 'social_youtube_icon', 'fa-youtube', 'text', 'YouTube Icon', '2025-10-14 21:25:49', '2025-10-14 21:25:49'),
(58, 'social_youtube_enabled', '0', 'boolean', 'Enable YouTube', '2025-10-14 21:25:49', '2025-10-16 13:52:48'),
(59, 'social_tiktok_url', 'https://www.tiktok.com/@lollipop_modeling', 'text', 'TikTok URL', '2025-10-14 21:25:49', '2025-10-16 13:34:57'),
(60, 'social_tiktok_icon', 'fa-tiktok', 'text', 'TikTok Icon', '2025-10-14 21:25:49', '2025-10-14 21:25:49'),
(61, 'social_tiktok_enabled', '1', 'boolean', 'Enable TikTok', '2025-10-14 21:25:49', '2025-10-16 13:31:58'),
(62, 'gototop_enabled', '1', 'boolean', 'Enable Go to Top Button', '2025-10-14 21:25:49', '2025-10-15 17:37:32'),
(63, 'gototop_icon', 'fa-arrow-up', 'text', 'Go to Top Icon', '2025-10-14 21:25:49', '2025-10-14 21:25:49'),
(64, 'gototop_bg_color', 'bg-red-primary', 'text', 'Go to Top Background Color', '2025-10-14 21:25:49', '2025-10-14 21:25:49'),
(65, 'gototop_text_color', 'text-white', 'text', 'Go to Top Text Color', '2025-10-14 21:25:49', '2025-10-14 21:25:49'),
(66, 'gototop_position', 'right', 'text', 'Go to Top Position (left/right)', '2025-10-14 21:25:49', '2025-10-14 21:25:49'),
(67, 'theme_primary_color', '#dc2626', 'text', 'Primary Theme Color (Red)', '2025-10-14 21:25:49', '2025-10-16 13:34:57'),
(68, 'theme_secondary_color', '#1f2937', 'text', 'Secondary Theme Color (Dark Gray)', '2025-10-14 21:25:49', '2025-10-16 13:34:57'),
(69, 'theme_accent_color', '#f59e0b', 'text', 'Accent Theme Color (Orange)', '2025-10-14 21:25:49', '2025-10-16 13:34:57'),
(70, 'theme_background_color', '#0f172a', 'text', 'Background Color (Dark)', '2025-10-14 21:25:49', '2025-10-16 13:34:57'),
(71, 'theme_text_color', '#f3f4f6', 'text', 'Text Color (Light Gray)', '2025-10-14 21:25:49', '2025-10-16 13:34:57'),
(72, 'seo_title', 'VIBEDAYBKK - บริการโมเดลและนางแบบมืออาชีพ', 'text', 'SEO Title', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(73, 'seo_description', 'บริการโมเดลและนางแบบมืออาชีพ ครบวงจร คุณภาพสูง พร้อมทีมงานมากประสบการณ์ รองรับงานแฟชั่น ถ่ายภาพ อีเวนต์ และโฆษณา', 'text', 'SEO Description', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(74, 'seo_keywords', 'โมเดล, นางแบบ, model, fashion model, event model, commercial model, บริการโมเดล, จองโมเดล', 'text', 'SEO Keywords', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(75, 'seo_author', 'VIBEDAYBKK Team', 'text', 'SEO Author', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(76, 'seo_canonical_url', 'https://vibedaybkk.com', 'text', 'Canonical URL', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(77, 'og_title', 'VIBEDAYBKK - บริการโมเดลและนางแบบมืออาชีพ', 'text', 'OG Title', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(78, 'og_description', 'บริการโมเดลและนางแบบมืออาชีพ ครบวงจร คุณภาพสูง', 'text', 'OG Description', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(79, 'og_image', '/assets/images/og-image.jpg', 'text', 'OG Image (1200x630px)', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(80, 'og_type', 'website', 'text', 'OG Type', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(81, 'og_locale', 'th_TH', 'text', 'OG Locale', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(82, 'og_site_name', 'VIBEDAYBKK', 'text', 'OG Site Name', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(83, 'twitter_card', 'summary_large_image', 'text', 'Twitter Card Type', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(84, 'twitter_site', '@vibedaybkk', 'text', 'Twitter Site Handle', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(85, 'twitter_creator', '@vibedaybkk', 'text', 'Twitter Creator Handle', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(86, 'twitter_title', 'VIBEDAYBKK - บริการโมเดลและนางแบบมืออาชีพ', 'text', 'Twitter Title', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(87, 'twitter_description', 'บริการโมเดลและนางแบบมืออาชีพ ครบวงจร คุณภาพสูง', 'text', 'Twitter Description', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(88, 'twitter_image', '/assets/images/twitter-image.jpg', 'text', 'Twitter Image (1200x600px)', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(89, 'robots_index', '1', 'boolean', 'Allow Search Engine Indexing', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(90, 'robots_follow', '1', 'boolean', 'Allow Search Engine Following Links', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(91, 'robots_txt_custom', '', 'text', 'Custom Robots.txt Content', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(92, 'sitemap_enabled', '1', 'boolean', 'Enable XML Sitemap', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(93, 'sitemap_frequency', 'weekly', 'text', 'Sitemap Update Frequency', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(94, 'google_analytics_enabled', '0', 'boolean', 'Enable Google Analytics', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(95, 'google_analytics_id', '', 'text', 'Google Analytics Measurement ID (G-XXXXXXXXXX)', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(96, 'google_tag_manager_id', '', 'text', 'Google Tag Manager ID (GTM-XXXXXXX)', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(97, 'google_site_verification', '', 'text', 'Google Search Console Verification Code', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(98, 'google_search_console_enabled', '0', 'boolean', 'Enable Google Search Console', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(99, 'facebook_pixel_enabled', '0', 'boolean', 'Enable Facebook Pixel', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(100, 'facebook_pixel_id', '', 'text', 'Facebook Pixel ID', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(101, 'meta_theme_color', '#DC2626', 'text', 'Theme Color (Mobile Browser)', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(102, 'meta_apple_mobile_capable', '1', 'boolean', 'Apple Mobile Web App Capable', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(103, 'meta_apple_status_bar_style', 'black-translucent', 'text', 'Apple Status Bar Style', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(104, 'schema_org_type', 'Organization', 'text', 'Schema.org Type', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(105, 'schema_org_enabled', '1', 'boolean', 'Enable Schema.org Markup', '2025-10-14 21:36:07', '2025-10-14 21:36:07'),
(106, 'gallery_enabled', '1', 'boolean', 'Enable Gallery', '2025-10-14 21:52:02', '2025-10-14 21:52:02'),
(107, 'gallery_per_page', '24', 'number', 'Images per page', '2025-10-14 21:52:02', '2025-10-14 21:52:02'),
(108, 'gallery_upload_max_size', '10', 'number', 'Max upload size in MB', '2025-10-14 21:52:02', '2025-10-14 21:52:02'),
(109, 'gallery_allowed_types', 'jpg,jpeg,png,gif,webp', 'text', 'Allowed file types', '2025-10-14 21:52:02', '2025-10-14 21:52:02'),
(110, 'gallery_thumbnail_width', '400', 'number', 'Thumbnail width in pixels', '2025-10-14 21:52:02', '2025-10-14 21:52:02'),
(111, 'gallery_thumbnail_height', '400', 'number', 'Thumbnail height in pixels', '2025-10-14 21:52:02', '2025-10-14 21:52:02'),
(112, 'gallery_watermark_enabled', '0', 'boolean', 'Enable watermark', '2025-10-14 21:52:02', '2025-10-14 21:52:02'),
(113, 'gallery_public_download', '1', 'boolean', 'Allow public download', '2025-10-14 21:52:02', '2025-10-14 21:52:02'),
(114, 'show_model_price', '0', 'text', NULL, '2025-10-16 10:18:42', '2025-10-16 13:58:33'),
(115, 'show_model_details', '0', 'boolean', 'แสดงรายละเอียดส่วนตัวของโมเดล (น้ำหนัก, ส่วนสูง, วันเกิด)', '2025-10-16 13:26:13', '2025-10-16 13:58:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('programmer','admin','editor','viewer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'viewer',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$Fbetghb9CInguhgrkxwHfeRdqOpbG19qUE3D.GVOj0p8T0IpVjF4a', 'ผู้ดูแลระบบ', 'admin@vibedaybkk.com', 'programmer', 'active', '2025-10-16 16:39:26', '2025-10-14 16:48:03', '2025-10-16 09:39:26'),
(2, 'editor1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'สมชาย บรรณาธิการ', 'editor1@vibedaybkk.com', 'editor', 'active', NULL, '2025-10-14 19:54:30', '2025-10-14 19:54:30'),
(3, 'editor2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'วิภา จัดการ', 'editor2@vibedaybkk.com', 'editor', 'active', NULL, '2025-10-14 19:54:30', '2025-10-14 19:54:30'),
(4, 'editor3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ธนพล ดูแล', 'editor3@vibedaybkk.com', 'editor', 'active', NULL, '2025-10-14 19:54:30', '2025-10-14 19:54:30'),
(5, 'manager1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ปิยะนุช ผู้จัดการ', 'manager@vibedaybkk.com', 'admin', 'active', NULL, '2025-10-14 19:54:30', '2025-10-14 19:54:30'),
(6, 'staff1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'นภัสวรรณ พนักงาน', 'staff1@vibedaybkk.com', 'editor', 'active', NULL, '2025-10-14 19:54:30', '2025-10-14 19:54:30'),
(7, 'staff2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'อรุณ ช่วยงาน', 'staff2@vibedaybkk.com', 'editor', 'active', NULL, '2025-10-14 19:54:30', '2025-10-14 19:54:30'),
(8, 'staff3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ชญานิศ ประสานงาน', 'staff3@vibedaybkk.com', 'editor', 'inactive', NULL, '2025-10-14 19:54:30', '2025-10-14 19:54:30'),
(9, 'designer1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ภูมิพัฒน์ ออกแบบ', 'designer@vibedaybkk.com', 'editor', 'active', NULL, '2025-10-14 19:54:30', '2025-10-14 19:54:30'),
(10, 'marketing1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'กมลวรรณ การตลาด', 'marketing@vibedaybkk.com', 'editor', 'active', NULL, '2025-10-14 19:54:30', '2025-10-14 19:54:30'),
(11, 'support1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'วีรภัทร ซัพพอร์ต', 'support@vibedaybkk.com', 'editor', 'active', NULL, '2025-10-14 19:54:30', '2025-10-14 19:54:30'),
(12, 'admin_test', '$2y$10$CDi2v/73fbBu25sfI391guP/mGp1LTJoIssQdAZnQ3zVjxI0CdNAa', 'admin_test', 'xxxproninja@gmail.com', 'admin', 'active', '2025-10-16 01:32:58', '2025-10-15 18:27:43', '2025-10-15 18:32:58'),
(13, 'viewer_test', '$2y$10$JToTdXX3hBHN2FkIoiLzMer4YTJ6QDTbzNsKOMAoWpigwdF6VvPS.', 'viewer_test', 'xxxproninja@gmail.com', 'editor', 'active', '2025-10-16 01:47:30', '2025-10-15 18:47:22', '2025-10-15 18:47:30');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_model_statistics`
-- (See below for the actual view)
--
CREATE TABLE `v_model_statistics` (
`id` int(11)
,`code` varchar(50)
,`name` varchar(100)
,`category_name` varchar(100)
,`status` enum('available','busy','inactive')
,`view_count` int(11)
,`booking_count` int(11)
,`rating` decimal(3,2)
,`image_count` bigint(21)
,`total_bookings` bigint(21)
,`completed_bookings` decimal(23,0)
,`total_revenue` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Structure for view `v_model_statistics`
--
DROP TABLE IF EXISTS `v_model_statistics`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_model_statistics`  AS SELECT `m`.`id` AS `id`, `m`.`code` AS `code`, `m`.`name` AS `name`, `c`.`name` AS `category_name`, `m`.`status` AS `status`, `m`.`view_count` AS `view_count`, `m`.`booking_count` AS `booking_count`, `m`.`rating` AS `rating`, count(distinct `mi`.`id`) AS `image_count`, count(distinct `b`.`id`) AS `total_bookings`, sum((case when (`b`.`status` = 'completed') then 1 else 0 end)) AS `completed_bookings`, coalesce(sum((case when (`b`.`status` = 'completed') then `b`.`total_price` else 0 end)),0) AS `total_revenue` FROM (((`models` `m` left join `categories` `c` on((`m`.`category_id` = `c`.`id`))) left join `model_images` `mi` on((`m`.`id` = `mi`.`model_id`))) left join `bookings` `b` on((`m`.`id` = `b`.`model_id`))) GROUP BY `m`.`id``id`  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `idx_articles_status` (`status`),
  ADD KEY `idx_articles_slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `article_categories`
--
ALTER TABLE `article_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `confirmed_by` (`confirmed_by`),
  ADD KEY `idx_bookings_status` (`status`),
  ADD KEY `idx_bookings_model` (`model_id`),
  ADD KEY `idx_bookings_date` (`booking_date`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `replied_by` (`replied_by`),
  ADD KEY `idx_contacts_status` (`status`);

--
-- Indexes for table `customer_reviews`
--
ALTER TABLE `customer_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_featured` (`is_featured`),
  ADD KEY `is_active` (`is_active`),
  ADD KEY `sort_order` (`sort_order`);

--
-- Indexes for table `feature_locks`
--
ALTER TABLE `feature_locks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `feature` (`feature`);

--
-- Indexes for table `gallery_albums`
--
ALTER TABLE `gallery_albums`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `status` (`status`),
  ADD KEY `sort_order` (`sort_order`);

--
-- Indexes for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `album_id` (`album_id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `status` (`status`),
  ADD KEY `sort_order` (`sort_order`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `homepage_columns`
--
ALTER TABLE `homepage_columns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_key` (`section_key`),
  ADD KEY `column_index` (`column_index`),
  ADD KEY `sort_order` (`sort_order`);

--
-- Indexes for table `homepage_features`
--
ALTER TABLE `homepage_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `homepage_gallery`
--
ALTER TABLE `homepage_gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `homepage_sections`
--
ALTER TABLE `homepage_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_key` (`section_key`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `models`
--
ALTER TABLE `models`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_models_category` (`category_id`),
  ADD KEY `idx_models_status` (`status`),
  ADD KEY `idx_models_featured` (`featured`);

--
-- Indexes for table `model_images`
--
ALTER TABLE `model_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `model_id` (`model_id`);

--
-- Indexes for table `model_requirements`
--
ALTER TABLE `model_requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_feature` (`role_key`,`feature`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_key` (`role_key`);

--
-- Indexes for table `role_upgrades`
--
ALTER TABLE `role_upgrades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=221;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `article_categories`
--
ALTER TABLE `article_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `customer_reviews`
--
ALTER TABLE `customer_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `feature_locks`
--
ALTER TABLE `feature_locks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `gallery_albums`
--
ALTER TABLE `gallery_albums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `gallery_images`
--
ALTER TABLE `gallery_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `homepage_columns`
--
ALTER TABLE `homepage_columns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `homepage_features`
--
ALTER TABLE `homepage_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `homepage_gallery`
--
ALTER TABLE `homepage_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `homepage_sections`
--
ALTER TABLE `homepage_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `models`
--
ALTER TABLE `models`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `model_images`
--
ALTER TABLE `model_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `model_requirements`
--
ALTER TABLE `model_requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `role_upgrades`
--
ALTER TABLE `role_upgrades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=368;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `articles_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `article_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `models` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`confirmed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `contacts_ibfk_1` FOREIGN KEY (`replied_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD CONSTRAINT `gallery_images_ibfk_1` FOREIGN KEY (`album_id`) REFERENCES `gallery_albums` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `gallery_images_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `homepage_features`
--
ALTER TABLE `homepage_features`
  ADD CONSTRAINT `homepage_features_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `homepage_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `homepage_gallery`
--
ALTER TABLE `homepage_gallery`
  ADD CONSTRAINT `homepage_gallery_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `homepage_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `models`
--
ALTER TABLE `models`
  ADD CONSTRAINT `models_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_images`
--
ALTER TABLE `model_images`
  ADD CONSTRAINT `model_images_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `models` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_requirements`
--
ALTER TABLE `model_requirements`
  ADD CONSTRAINT `model_requirements_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_upgrades`
--
ALTER TABLE `role_upgrades`
  ADD CONSTRAINT `role_upgrades_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
