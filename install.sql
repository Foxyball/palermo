-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 27, 2025 at 10:20 AM
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
-- Database: `palermo_live`
--

-- --------------------------------------------------------

--
-- Table structure for table `addons`
--

CREATE TABLE `addons` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` enum('0','1') DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addons`
--

INSERT INTO `addons` (`id`, `name`, `price`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Extra Cheese', 2.00, '1', '2025-10-27 07:32:50', '2025-10-27 07:32:50');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(100) DEFAULT NULL,
  `admin_email` varchar(150) DEFAULT NULL,
  `admin_password` varchar(255) DEFAULT NULL,
  `last_log_ip` varchar(45) DEFAULT NULL,
  `last_log_date` datetime DEFAULT NULL,
  `active` enum('0','1') DEFAULT '1',
  `is_super_admin` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `admin_name`, `admin_email`, `admin_password`, `last_log_ip`, `last_log_date`, `active`, `is_super_admin`, `created_at`, `updated_at`) VALUES
(1, 'HSabev', 'hsabev@sprintax.com', '$2y$10$ipdoUtTKq54RM3uahyxwmuaDZCvc0UbSq7zViLl8eZyAB1sIjFGvS', '127.0.0.1', '2025-10-09 19:39:55', '1', 1, '2025-09-23 17:40:04', '2025-10-20 07:35:31'),
(2, 'ue', 'ue@palermo.com', '5f4dcc3b5aa765d61d8327deb882cf99', '127.0.0.1', '2025-09-27 12:43:18', '1', 0, '2025-09-23 17:40:04', '2025-09-27 13:22:15'),
(3, 'Ivan Dimitrov', 'idimitrov@palermo.com', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, '1', 0, '2025-09-27 12:18:51', '2025-09-27 12:18:51'),
(4, 'Stanislav Malchev', 'smalchev@palermo.com', 'ca35d11d4a538755c0b7be89255fdb56', NULL, NULL, '1', 1, '2025-09-27 12:29:16', '2025-10-09 16:40:04'),
(5, 'KGeorgieva', 'kgeorgieva@palermo.com', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, '1', 0, '2025-09-27 12:34:02', '2025-09-27 18:50:21'),
(8, 'Test', 'test@abv.bg', '8d4646eb2d7067126eb08adb0672f7bb', NULL, NULL, '1', 0, '2025-10-09 16:44:55', '2025-10-09 16:44:55');

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `gallery_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `slug` varchar(220) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('0','1') DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `status` enum('0','1') DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_categories`
--

INSERT INTO `blog_categories` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Restaurant News', '1', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(2, 'Chef\'s Corner', '1', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(3, 'Recipes', '1', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(4, 'Food Culture', '1', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(5, 'Behind the Scenes', '1', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(6, 'Customer Stories', '1', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(7, 'Seasonal Menus', '1', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(8, 'Cooking Tips', '1', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(9, 'Wine & Pairing', '1', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(10, 'Events & Catering', '1', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(11, 'Health & Nutrition', '1', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(12, 'Restaurant Reviews', '1', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(13, 'Local Ingredients', '1', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(14, 'Holiday Specials', '1', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(15, 'Staff Picks', '1', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(16, 'Food Photography', '0', '2025-09-27 15:00:04', '2025-09-27 15:00:04');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `slug` varchar(120) DEFAULT NULL,
  `active` enum('0','1') DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Pizza', 'pizza', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(2, 'Pasta', 'pasta', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(3, 'Appetizers', 'appetizers', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(4, 'Salads', 'salads', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(5, 'Soups', 'soups', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(6, 'Main Courses', 'main-courses', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(7, 'Seafood', 'seafood', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(8, 'Chicken', 'chicken', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(9, 'Beef', 'beef', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(10, 'Pork', 'pork', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(11, 'Vegetarian', 'vegetarian', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(12, 'Vegan', 'vegan', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(13, 'Desserts', 'desserts', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(14, 'Beverages', 'beverages', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(15, 'Coffee', 'coffee', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(16, 'Tea', 'tea', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(17, 'Soft Drinks', 'soft-drinks', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(18, 'Alcoholic Beverages', 'alcoholic-beverages', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(19, 'Wine', 'wine', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(20, 'Beer', 'beer', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(21, 'Cocktails', 'cocktails', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(22, 'Kids Menu', 'kids-menu', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(23, 'Gluten Free', 'gluten-free', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(24, 'Healthy Options', 'healthy-options', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(25, 'Burgers', 'burgers', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(26, 'Sandwiches', 'sandwiches', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(29, 'Breakfast', 'breakfast', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(30, 'Brunch', 'brunch', '1', '2025-09-27 14:57:00', '2025-09-30 11:26:47'),
(31, 'Lunch Specials', 'lunch-specials', '0', '2025-09-27 14:57:00', '2025-09-30 11:26:44'),
(32, 'Dinner Specials', 'dinner-specials', '0', '2025-09-27 14:57:00', '2025-09-30 11:26:43'),
(33, 'Chef Recommendations', 'chef-recommendations', '1', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(34, 'Seasonal Menu', 'seasonal-menu', '0', '2025-09-27 14:57:00', '2025-09-27 14:57:00'),
(35, 'Pizza2', 'izza2', '0', '2025-09-30 11:36:46', '2025-09-30 11:41:43'),
(36, 'pizza4', 'pizza4', '0', '2025-09-30 11:40:49', '2025-09-30 11:41:41'),
(37, 'Test', 'est', '1', '2025-10-09 16:44:07', '2025-10-09 16:44:07');

-- --------------------------------------------------------

--
-- Table structure for table `galleries`
--

CREATE TABLE `galleries` (
  `id` int(11) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `active` enum('0','1') DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `galleries`
--

INSERT INTO `galleries` (`id`, `title`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Summer Dishes 2022', '1', '2025-10-09 16:41:12', '2025-10-09 16:43:47'),
(4, 'adad', '0', '2025-10-09 17:00:20', '2025-10-09 17:03:17'),
(5, 'sdfdsf', '0', '2025-10-09 17:02:58', '2025-10-09 17:03:18');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_images`
--

CREATE TABLE `gallery_images` (
  `id` int(11) NOT NULL,
  `gallery_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery_images`
--

INSERT INTO `gallery_images` (`id`, `gallery_id`, `image`, `created_at`) VALUES
(1, 1, 'uploads/2025/10/68e7ecc74afcf_28Summer-100-Video-Still-hckg-verticalTwoByThree735.jpg', '2025-10-09 17:11:35'),
(2, 1, 'uploads/2025/10/68e7ecc74b87b_images.jpg', '2025-10-09 17:11:35'),
(8, 1, 'uploads/2025/10/68e7efdd00bee_16FD-SUMMER-100-Salmon-with-Corn-and-Tomato-zmbp-mediumSquareAt3X.jpg', '2025-10-09 17:24:45'),
(9, 1, 'uploads/2025/10/68e7f01935219_28Summer-100-Video-Still-hckg-verticalTwoByThree735.jpg', '2025-10-09 17:25:45'),
(10, 1, 'uploads/2025/10/68e7f01935740_images.jpg', '2025-10-09 17:25:45'),
(11, 1, 'uploads/2025/10/68e7f0193715f_16FD-SUMMER-100-Salmon-with-Corn-and-Tomato-zmbp-mediumSquareAt3X.jpg', '2025-10-09 17:25:45');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `type` enum('new_order','system','message') NOT NULL DEFAULT 'new_order',
  `message` varchar(255) NOT NULL,
  `read_status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `order_address` text DEFAULT NULL,
  `status` enum('pending','processing','completed','canceled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `amount`, `status_id`, `message`, `order_address`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 23.98, 1, 'sfsf', 'Varna, BK', 'pending', '2025-10-27 07:13:19', '2025-10-27 07:58:31'),
(2, 47, 23.98, 2, 'sfsf', 'Varna, BK', 'pending', '2025-10-27 07:13:19', '2025-10-27 08:26:57');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `unit_price`, `qty`, `subtotal`) VALUES
(1, 1, 15, 14.99, 1, 16.99),
(2, 1, 25, 6.99, 1, 6.99);

-- --------------------------------------------------------

--
-- Table structure for table `order_item_addons`
--

CREATE TABLE `order_item_addons` (
  `order_item_id` int(11) NOT NULL,
  `addon_id` int(11) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_item_addons`
--

INSERT INTO `order_item_addons` (`order_item_id`, `addon_id`, `price`) VALUES
(1, 1, 2.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_statuses`
--

CREATE TABLE `order_statuses` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_statuses`
--

INSERT INTO `order_statuses` (`id`, `name`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Pending', '1', '2025-10-27 07:23:26', '2025-10-27 07:27:59'),
(2, 'Confirmed', '1', '2025-10-27 07:23:26', '2025-10-27 07:28:09'),
(3, 'Preparing', '1', '2025-10-27 07:23:26', '2025-10-27 07:28:14'),
(4, 'Ready', '1', '2025-10-27 07:23:26', '2025-10-27 07:28:06'),
(5, 'Out for Delivery', '1', '2025-10-27 07:23:26', '2025-10-27 07:28:18'),
(6, 'Delivered', '1', '2025-10-27 07:23:26', '2025-10-27 07:28:21'),
(7, 'Cancelled', '1', '2025-10-27 07:23:26', '2025-10-27 07:28:25');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`id`, `email`, `token`, `expires_at`, `used_at`, `created_at`) VALUES
(1, 'john.doe@example.com', '2eab34d699fc23437e7a651c1e4eaabc1ccb7c239e832c55a15a4f69d28ab047', '2025-10-23 17:11:17', '2025-10-23 16:14:10', '2025-10-23 16:11:17'),
(2, 'test123@abv.bg', 'b8bf68fd32af506323e7c5dc7a36e799cf4913bd1e273558fde9c8b85f540bfc', '2025-10-24 16:17:48', NULL, '2025-10-23 16:17:48'),
(3, 'test22@abv.bg', 'bea56f69f69dde12256211533b734b519978188063715a93fa13f149f7a4b0cf', '2025-10-24 16:17:50', NULL, '2025-10-23 16:17:50'),
(4, 'testA@abv.bg', 'a3c486914181af860b088331f9768060a21680825752e9051daeefd5e3a3c728', '2025-10-24 16:19:16', NULL, '2025-10-23 16:19:16'),
(5, 'testB@abv.bg', 'd082394ef6741e16b539f7096d196878db2c2de0927b1d7635fb9443aabfdf11', '2025-10-24 16:19:18', NULL, '2025-10-23 16:19:18'),
(6, 'test4@abv.bg', '4b8c8bd1cfe1d2d0f65b03f6c5aa08f8fafbecad7c29f059996faed0603dc549', '2025-10-24 16:21:14', NULL, '2025-10-23 16:21:14'),
(7, 'test5@abv.bg', '7373fe9bfa0d3999c661cae9fdf5916208803793f947a53796d1ebeaf233f758', '2025-10-24 16:21:17', NULL, '2025-10-23 16:21:17'),
(8, 'test6@abv.bg', '53c061061fc47e22522bccb559c14559ea87f99ef5c59c3d87e10e90295c0a36', '2025-10-24 16:21:19', NULL, '2025-10-23 16:21:19'),
(9, 'ivan@abv.bg', '8a5105fe21021b8780de7de95eeb340da630cf3ce15020744499a2cacb027325', '2025-10-24 16:32:55', NULL, '2025-10-23 16:32:55'),
(10, 'dimitar@abv.bg', '2329b06fc685a7784045cfc6e3b9d75843c7d82f7ac84d9c31b259349c57200d', '2025-10-24 16:32:57', NULL, '2025-10-23 16:32:57'),
(11, 'ivan2@abv.bg', '42c8cc665bafa34f24e32032c875c9c34b410d469fd19cb61089bfd8bc434ef2', '2025-10-24 16:32:59', NULL, '2025-10-23 16:32:59'),
(12, 'asdff@abv.bg', 'a8c3a4604d0ea998f1e600b08c6c5e7f807feca8e2b0a7ef2d6329ef8175d397', '2025-10-24 16:37:47', NULL, '2025-10-23 16:37:47'),
(13, 'asd2@abv.bg', '5b249f2329ce89b71c8e0ba124a75c26ae23cba4aab95828ae7dcba92492d6fe', '2025-10-24 16:37:49', NULL, '2025-10-23 16:37:49'),
(14, 'asd4@abv.bg', 'f7ee3231bc7621a4d0787a1960746f5df29c6aad9937ea83d7b02266a84ebfb1', '2025-10-24 16:37:56', NULL, '2025-10-23 16:37:56'),
(15, 'stefan@abv.bg', '204faefee782bec64a15bbef239351985ef61085afcff08fcf8e62ce46de3e37', '2025-10-24 16:38:54', NULL, '2025-10-23 16:38:54'),
(16, 'dimcho@abv.bg', '28f6d16a13d8a2f049ad0dee5ef80532d3c638b88f2f82d82376b0b45a2c7049', '2025-10-24 16:38:56', NULL, '2025-10-23 16:38:56'),
(17, 'stoicho@abv.bg', '64c6e20ae7180941fe863ba41c94f8091177fbfc96af810898516c3fb7662e24', '2025-10-24 16:39:03', NULL, '2025-10-23 16:39:03'),
(18, 'dimi@gmail.com', '3402a2f51cf41a76fecbf987400519d58ef8d01ee9c98bf76a719af7582dacef', '2025-10-24 16:39:09', NULL, '2025-10-23 16:39:09'),
(19, 'work@gmail.com', '997ad291e3d17470777020c2398b3bc3ef7193dd89c39c6ea5abdc6a466777f0', '2025-10-24 16:42:55', NULL, '2025-10-23 16:42:55'),
(20, 'working@abv.bg', 'dbc81a422ded8dbf1d44899a0be59d33302cc371d32cf7e702c781c538c8cd93', '2025-10-24 16:42:57', NULL, '2025-10-23 16:42:57'),
(21, 'vvvvv@example.com', 'e1d0215d262e2c88f11b44ff97cc85e5f90da19e2f3f1f285594d74844a81bfc', '2025-10-24 16:42:59', NULL, '2025-10-23 16:42:59'),
(22, 'aaaaaaaaa@abv.bg', '7c7d70bce83b367aa9761b913c37ebd5f907fbc1f750680200fef9408e4d20f1', '2025-10-24 16:45:53', NULL, '2025-10-23 16:45:53'),
(23, 'bbbbbbb@gmail.com', '9b0abab683eea1fcf17d6d658dc11a1fca9692ad767e53e2f9e0dc8c14405a0d', '2025-10-24 16:45:55', NULL, '2025-10-23 16:45:55'),
(24, 'vcdsgfdg@abv.bg', 'fbb3720a9e6570cbb32deeabc32e2a504b69cd107e4a28ec678b181c39b5974f', '2025-10-24 16:45:57', NULL, '2025-10-23 16:45:57'),
(25, 'kgkgk@abv.bg', 'f3c4df7e75cd5dfe19d9b744a8c3a15202635df7257124cfa629a48f92882eed', '2025-10-24 16:50:58', NULL, '2025-10-23 16:50:58'),
(26, 'fgbnvnnvn@test.com', 'dcc804dde59924b40267197edfc765d131b3fa266d2f5e3afbebc936ea71014c', '2025-10-24 16:51:00', NULL, '2025-10-23 16:51:00'),
(27, 'ytjt@abv.bg', '4627d685cba2e396383e5b0a114da5009440144511756e697ecb7be949f32d7f', '2025-10-24 16:51:01', NULL, '2025-10-23 16:51:01');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `slug` varchar(160) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `short_description` varchar(255) DEFAULT NULL,
  `long_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `image`, `price`, `active`, `short_description`, `long_description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Margherita Pizza', 'margherita-pizza', 'margherita.jpg', 18.99, '1', 'Classic pizza with fresh mozzarella and basil', 'Traditional Italian pizza with San Marzano tomatoes, fresh mozzarella di bufala, fresh basil, and extra virgin olive oil on our wood-fired pizza base.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(2, 1, 'Pepperoni Pizza', 'pepperoni-pizza', 'pepperoni.jpg', 21.99, '1', 'America\'s favorite with spicy pepperoni', 'Generous portions of premium pepperoni, mozzarella cheese, and our signature tomato sauce on a perfectly crispy crust.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(3, 1, 'Quattro Stagioni', 'quattro-stagioni', 'quattro.jpg', 24.99, '1', 'Four seasons pizza with varied toppings', 'Divided into four sections: mushrooms, ham, artichokes, and olives, representing the four seasons of the year.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(4, 1, 'Prosciutto e Funghi', 'prosciutto-funghi', 'prosciutto.jpg', 26.99, '1', 'Prosciutto and mushroom pizza', 'Thin sliced prosciutto di Parma, fresh mushrooms, mozzarella, and truffle oil drizzle.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(5, 2, 'Spaghetti Carbonara', 'spaghetti-carbonara', 'carbonara.jpg', 19.99, '1', 'Creamy Roman classic with pancetta', 'Spaghetti with eggs, pecorino Romano, pancetta, and black pepper - the authentic Roman way.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(6, 2, 'Fettuccine Alfredo', 'fettuccine-alfredo', 'alfredo.jpg', 18.99, '1', 'Rich and creamy butter and parmesan sauce', 'Fresh fettuccine pasta tossed in our house-made alfredo sauce with parmigiano-reggiano.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(7, 2, 'Penne Arrabbiata', 'penne-arrabbiata', 'arrabbiata.jpg', 16.99, '1', 'Spicy tomato sauce with garlic and chili', 'Penne pasta in a fiery tomato sauce with garlic, red chili peppers, and fresh parsley.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(8, 2, 'Lasagna della Casa', 'lasagna-casa', 'lasagna.jpg', 22.99, '1', 'Traditional layered pasta with meat sauce', 'Layers of pasta, meat sauce, bechamel, and three cheeses baked to perfection.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(9, 3, 'Bruschetta Trio', 'bruschetta-trio', 'bruschetta.jpg', 12.99, '1', 'Three varieties of our famous bruschetta', 'Classic tomato basil, mushroom truffle, and roasted pepper varieties on toasted ciabatta.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(10, 3, 'Antipasto Platter', 'antipasto-platter', 'antipasto.jpg', 19.99, '1', 'Selection of Italian cured meats and cheeses', 'Prosciutto, salami, mortadella, fresh mozzarella, aged provolone, olives, and roasted peppers.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(11, 3, 'Calamari Fritti', 'calamari-fritti', 'calamari.jpg', 14.99, '1', 'Crispy fried squid rings with marinara', 'Fresh squid rings lightly battered and fried, served with spicy marinara sauce.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(12, 3, 'Arancini', 'arancini', 'arancini.jpg', 11.99, '1', 'Sicilian rice balls with mozzarella', 'Golden fried risotto balls stuffed with mozzarella, served with tomato sauce.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(13, 4, 'Caesar Salad', 'caesar-salad', 'caesar.jpg', 13.99, '1', 'Classic Caesar with house-made dressing', 'Romaine lettuce, parmesan, croutons, and our signature Caesar dressing.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(14, 4, 'Caprese Salad', 'caprese-salad', 'caprese.jpg', 15.99, '1', 'Fresh mozzarella, tomatoes, and basil', 'Buffalo mozzarella, vine-ripened tomatoes, fresh basil, and balsamic reduction.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(15, 4, 'Arugula Salad', 'arugula-salad', 'arugula.jpg', 14.99, '1', 'Peppery arugula with pears and gorgonzola', 'Baby arugula, sliced pears, gorgonzola cheese, walnuts, and honey vinaigrette.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(16, 5, 'Osso Buco', 'osso-buco', 'osso-buco.jpg', 32.99, '1', 'Braised veal shanks in rich tomato sauce', 'Tender veal shanks slow-braised with vegetables, white wine, and tomatoes, served with risotto milanese.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(17, 5, 'Chicken Parmigiana', 'chicken-parmigiana', 'chicken-parm.jpg', 24.99, '1', 'Breaded chicken with marinara and mozzarella', 'Breaded chicken breast topped with marinara sauce and mozzarella, served with spaghetti.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(18, 5, 'Veal Piccata', 'veal-piccata', 'veal-piccata.jpg', 28.99, '1', 'Tender veal in lemon caper sauce', 'Pan-seared veal medallions in white wine, lemon, and caper sauce with roasted vegetables.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(19, 9, 'Branzino al Sale', 'branzino-al-sale', 'branzino.jpg', 29.99, '1', 'Mediterranean sea bass baked in sea salt', 'Whole Mediterranean sea bass baked in aromatic sea salt crust, filleted tableside.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(20, 9, 'Linguine alle Vongole', 'linguine-vongole', 'vongole.jpg', 23.99, '1', 'Linguine with fresh clams in white wine', 'Fresh linguine with littleneck clams, garlic, white wine, and parsley.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(21, 9, 'Salmon Griglia', 'salmon-griglia', 'salmon.jpg', 26.99, '1', 'Grilled Atlantic salmon with herbs', 'Fresh Atlantic salmon grilled with Mediterranean herbs, served with seasonal vegetables.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(22, 6, 'Tiramisu', 'tiramisu', 'tiramisu.jpg', 8.99, '1', 'Classic Italian coffee-flavored dessert', 'Layers of coffee-soaked ladyfingers and mascarpone cream, dusted with cocoa.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(23, 6, 'Panna Cotta', 'panna-cotta', 'pannacotta.jpg', 7.99, '1', 'Silky vanilla custard with berry coulis', 'Traditional vanilla panna cotta topped with mixed berry compote.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(24, 6, 'Cannoli Siciliani', 'cannoli-siciliani', 'cannoli.jpg', 9.99, '1', 'Crispy shells filled with sweet ricotta', 'Traditional Sicilian cannoli with sweet ricotta filling and pistachios.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(25, 6, 'Gelato Trio', 'gelato-trio', 'gelato.jpg', 6.99, '1', 'Three scoops of artisanal gelato', 'Choose three flavors from our daily selection of house-made gelato.', '2025-09-27 15:00:04', '2025-09-27 15:00:04'),
(26, 12, 'Winter Truffle Special', 'winter-truffle-special', 'truffle.jpg', 45.99, '0', 'Seasonal truffle pasta - Limited time', 'Fresh pasta with shaved white truffles, available only during truffle season.', '2025-09-27 15:00:04', '2025-09-27 15:00:04');

-- --------------------------------------------------------

--
-- Table structure for table `product_addons`
--

CREATE TABLE `product_addons` (
  `product_id` int(11) NOT NULL,
  `addon_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_addons`
--

INSERT INTO `product_addons` (`product_id`, `addon_id`) VALUES
(15, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `active` enum('0','1') DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `address`, `city`, `phone`, `zip_code`, `active`, `created_at`, `updated_at`) VALUES
(1, 'John', 'Doe', 'john.doe@example.com', '$2y$10$gr1SJuUCJ1phmenHrQnOEeuraGwmbUFyWdrzEhJCzh3GD2ZW1DJ/e', '123 Main Street', 'New York', '+1-555-0101', '10001', '1', '2025-09-27 14:55:05', '2025-10-23 13:14:10'),
(2, 'Jane', 'Smith', 'jane.smith@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '456 Oak Avenue', 'Los Angeles', '+1-555-0102', '90210', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(4, 'Emily', 'Davis', 'emily.davis@hotmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '321 Elm Street', 'Houston', '+1-555-0104', '77001', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(5, 'David', 'Wilson', 'david.wilson@outlook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '654 Maple Drive', 'Phoenix', '+1-555-0105', '85001', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(6, 'Sarah', 'Brown', 'sarah.brown@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '987 Cedar Lane', 'Philadelphia', '+1-555-0106', '19101', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(7, 'Chris', 'Taylor', 'chris.taylor@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '147 Birch Court', 'San Antonio', '+1-555-0107', '78201', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(8, 'Lisa', 'Anderson', 'lisa.anderson@yahoo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '258 Spruce Way', 'San Diego', '+1-555-0108', '92101', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(9, 'Mark', 'Thomas', 'mark.thomas@hotmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '369 Willow Street', 'Dallas', '+1-555-0109', '75201', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(10, 'Jennifer', 'Jackson', 'jennifer.j@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '741 Poplar Avenue', 'San Jose', '+1-555-0110', '95101', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(11, 'Robert', 'White', 'robert.white@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '852 Hickory Road', 'Austin', '+1-555-0111', '73301', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(12, 'Michelle', 'Harris', 'michelle.harris@outlook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '963 Walnut Drive', 'Jacksonville', '+1-555-0112', '32099', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(13, 'Kevin', 'Martin', 'kevin.martin@yahoo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '159 Cherry Lane', 'Fort Worth', '+1-555-0113', '76101', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(14, 'Amanda', 'Garcia', 'amanda.garcia@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '357 Peach Street', 'Columbus', '+1-555-0114', '43085', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(15, 'Ryan', 'Rodriguez', 'ryan.rodriguez@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '468 Apple Court', 'Charlotte', '+1-555-0115', '28201', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(16, 'Nicole', 'Lewis', 'nicole.lewis@hotmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '579 Orange Avenue', 'San Francisco', '+1-555-0116', '94101', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(17, 'Brandon', 'Lee', 'brandon.lee@outlook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '681 Lemon Road', 'Indianapolis', '+1-555-0117', '46201', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(18, 'Stephanie', 'Walker', 'stephanie.walker@yahoo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '792 Lime Drive', 'Seattle', '+1-555-0118', '98101', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(19, 'Tyler', 'Hall', 'tyler.hall@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '813 Grape Street', 'Denver', '+1-555-0119', '80201', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(20, 'Rachel', 'Allen', 'rachel.allen@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '924 Berry Lane', 'Washington', '+1-555-0120', '20001', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(21, 'Jason', 'Young', 'jason.young@hotmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '135 Plum Court', 'Boston', '+1-555-0121', '02101', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(22, 'Laura', 'Hernandez', 'laura.hernandez@outlook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '246 Fig Avenue', 'El Paso', '+1-555-0122', '79901', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(23, 'Anthony', 'King', 'anthony.king@yahoo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '357 Date Road', 'Nashville', '+1-555-0123', '37201', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(24, 'Melissa', 'Wright', 'melissa.wright@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '468 Mint Drive', 'Detroit', '+1-555-0124', '48201', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(25, 'Daniel', 'Lopez', 'daniel.lopez@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '579 Basil Street', 'Portland', '+1-555-0125', '97201', '0', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(26, 'Jessica', 'Hill', 'jessica.hill@hotmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '681 Sage Lane', 'Oklahoma City', '+1-555-0126', '73101', '0', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(27, 'Matthew', 'Scott', 'matthew.scott@outlook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '792 Thyme Court', 'Las Vegas', '+1-555-0127', '89101', '0', '2025-09-27 14:55:05', '2025-09-30 05:30:52'),
(28, 'Kimberly', 'Green', 'kimberly.green@yahoo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '813 Rosemary Avenue', 'Louisville', '+1-555-0128', '40201', '0', '2025-09-27 14:55:05', '2025-09-30 05:30:54'),
(29, 'Joshua', 'Adams', 'joshua.adams@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '924 Parsley Road', 'Baltimore', '+1-555-0129', '21201', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(30, 'Ashley', 'Baker', 'ashley.baker@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '135 Oregano Drive', 'Milwaukee', '+1-555-0130', '53201', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(31, 'Test', 'Active', 'test.active@palermo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '100 Test Street', 'Test City', '+1-555-9999', '99999', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(32, 'Test', 'Activee', 'test.inactive@palermo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', 'Dobrich', '', '', '1', '2025-09-27 14:55:05', '2025-09-30 06:19:10'),
(33, 'Admin', 'Test', 'admin.test@palermo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '300 Admin Road', 'Admin City', '+1-555-9997', '99997', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
(34, 'Tony', 'Kostadinov', 'tkostadinov@sprintax.com', '5f4dcc3b5aa765d61d8327deb882cf99', 'Varna', '', '', '', '1', '2025-09-30 05:57:51', '2025-09-30 05:57:56'),
(35, 'Rostislav', 'Demirov', 'rdemirov@abv.bg', '5f4dcc3b5aa765d61d8327deb882cf99', '', '', '', '', '1', '2025-09-30 05:59:43', '2025-09-30 05:59:43'),
(36, 'Test', 'Tst', '124767@students.ue-varna.bg', '5f4dcc3b5aa765d61d8327deb882cf99', 'ADADA', '', '1231', '123', '1', '2025-10-09 16:44:28', '2025-10-09 16:44:28'),
(37, 'Test', 'User', 'testuser@example.com', '25d55ad283aa400af464c76d713c07ad', NULL, NULL, NULL, NULL, '1', '2025-10-23 12:49:02', '2025-10-23 12:49:02'),
(38, 'Test', 'User', 'test1user@example.com', '25d55ad283aa400af464c76d713c07ad', NULL, NULL, NULL, NULL, '1', '2025-10-23 12:49:02', '2025-10-23 12:49:02'),
(39, 'Test', 'User', 'test2user@example.com', '25d55ad283aa400af464c76d713c07ad', NULL, NULL, NULL, NULL, '1', '2025-10-23 12:49:02', '2025-10-23 12:49:02'),
(40, NULL, NULL, 'test123@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:17:48', '2025-10-23 13:17:48'),
(41, NULL, NULL, 'test22@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:17:50', '2025-10-23 13:17:50'),
(42, 'Test4b9efb', 'User030bf9', 'testA@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:19:16', '2025-10-23 13:19:16'),
(43, 'Test4b9efb', 'User030bf9', 'testB@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:19:18', '2025-10-23 13:19:18'),
(44, 'Test917317', 'User89d3d8', 'asd@abv.bg', '$2y$10$F8OG.svpQ/zo8T6ZMurOGOEEhu9eISswPt/vQLkIL9fCi0nXFkO.2', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:20:57', '2025-10-23 13:20:57'),
(45, 'Test917317', 'User89d3d8', 'asd1@abv.bg', '$2y$10$jePQcweco4TzuOYFFqZynefu5bU4tEv84.OsMPK9/kojXIeUSUc4i', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:20:57', '2025-10-23 13:20:57'),
(46, 'Test917317', 'User89d3d8', 'asd3@abv.bg', '$2y$10$XsKH3MFZqfif2.M/ns4XeOBVGFMCrqOE7/9l553Sx/T4CmU0Q4086', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:20:57', '2025-10-23 13:20:57'),
(47, 'Test4c6ad1', 'User49b724', 'test4@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:21:14', '2025-10-23 13:21:14'),
(48, 'Test4c6ad1', 'User49b724', 'test5@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:21:17', '2025-10-23 13:21:17'),
(49, 'Test4c6ad1', 'User49b724', 'test6@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:21:19', '2025-10-23 13:21:19'),
(50, 'Testc48ba7', 'User9082c1', 'ivan@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:32:55', '2025-10-23 13:32:55'),
(51, 'Testc48ba7', 'User9082c1', 'dimitar@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:32:57', '2025-10-23 13:32:57'),
(52, 'Testc48ba7', 'User9082c1', 'ivan2@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:32:59', '2025-10-23 13:32:59'),
(53, 'Test760816', 'Userb16faf', 'asdff@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:37:47', '2025-10-23 13:37:47'),
(54, 'Test760816', 'Userb16faf', 'asd2@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:37:49', '2025-10-23 13:37:49'),
(55, 'Test760816', 'Userb16faf', 'asd4@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:37:56', '2025-10-23 13:37:56'),
(56, 'Testaa8bb0', 'User6bc45c', 'stefan@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:38:54', '2025-10-23 13:38:54'),
(57, 'Testaa8bb0', 'User6bc45c', 'dimcho@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:38:56', '2025-10-23 13:38:56'),
(58, 'Testaa8bb0', 'User6bc45c', 'stoicho@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:39:03', '2025-10-23 13:39:03'),
(59, 'Testaa8bb0', 'User6bc45c', 'dimi@gmail.com', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:39:09', '2025-10-23 13:39:09'),
(60, 'New', 'User', 'work@gmail.com', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:42:55', '2025-10-23 13:42:55'),
(61, 'New', 'User', 'working@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:42:57', '2025-10-23 13:42:57'),
(62, 'New', 'User', 'vvvvv@example.com', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:42:59', '2025-10-23 13:42:59'),
(63, 'New', 'User', 'aaaaaaaaa@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:45:53', '2025-10-23 13:45:53'),
(64, 'New', 'User', 'bbbbbbb@gmail.com', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:45:55', '2025-10-23 13:45:55'),
(65, 'New', 'User', 'vcdsgfdg@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:45:57', '2025-10-23 13:45:57'),
(66, 'New', 'User', 'kgkgk@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:50:58', '2025-10-23 13:50:58'),
(67, 'New', 'User', 'fgbnvnnvn@test.com', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:51:00', '2025-10-23 13:51:00'),
(68, 'New', 'User', 'ytjt@abv.bg', '', NULL, NULL, NULL, NULL, '1', '2025-10-23 13:51:01', '2025-10-23 13:51:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addons`
--
ALTER TABLE `addons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `admin_email` (`admin_email`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `gallery_id` (`gallery_id`);

--
-- Indexes for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `galleries`
--
ALTER TABLE `galleries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gallery_id` (`gallery_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_notifications_unread` (`read_status`,`created_at`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_item_addons`
--
ALTER TABLE `order_item_addons`
  ADD PRIMARY KEY (`order_item_id`,`addon_id`),
  ADD KEY `addon_id` (`addon_id`);

--
-- Indexes for table `order_statuses`
--
ALTER TABLE `order_statuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_active` (`active`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_addons`
--
ALTER TABLE `product_addons`
  ADD PRIMARY KEY (`product_id`,`addon_id`),
  ADD KEY `addon_id` (`addon_id`);

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
-- AUTO_INCREMENT for table `addons`
--
ALTER TABLE `addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `galleries`
--
ALTER TABLE `galleries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `gallery_images`
--
ALTER TABLE `gallery_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_statuses`
--
ALTER TABLE `order_statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `blogs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `blogs_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`),
  ADD CONSTRAINT `blogs_ibfk_3` FOREIGN KEY (`gallery_id`) REFERENCES `galleries` (`id`);

--
-- Constraints for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD CONSTRAINT `gallery_images_ibfk_1` FOREIGN KEY (`gallery_id`) REFERENCES `galleries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `order_item_addons`
--
ALTER TABLE `order_item_addons`
  ADD CONSTRAINT `order_item_addons_ibfk_1` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_item_addons_ibfk_2` FOREIGN KEY (`addon_id`) REFERENCES `addons` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `product_addons`
--
ALTER TABLE `product_addons`
  ADD CONSTRAINT `product_addons_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_addons_ibfk_2` FOREIGN KEY (`addon_id`) REFERENCES `addons` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
