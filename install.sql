-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2025 at 07:27 PM
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
(1, 'HSabev', 'hsabev@sprintax.com', 'e10adc3949ba59abbe56e057f20f883e', '127.0.0.1', '2025-10-09 19:39:55', '1', 1, '2025-09-23 17:40:04', '2025-10-09 16:39:55'),
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
  `message` text DEFAULT NULL,
  `status` enum('pending','processing','completed','canceled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `order_item_addons`
--

CREATE TABLE `order_item_addons` (
  `order_item_id` int(11) NOT NULL,
  `addon_id` int(11) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `status` enum('0','1') DEFAULT '1',
  `short_description` varchar(255) DEFAULT NULL,
  `long_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `image`, `price`, `status`, `short_description`, `long_description`, `created_at`, `updated_at`) VALUES
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
(1, 'John', 'Doe', 'john.doe@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123 Main Street', 'New York', '+1-555-0101', '10001', '1', '2025-09-27 14:55:05', '2025-09-27 14:55:05'),
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
(36, 'Test', 'Tst', '124767@students.ue-varna.bg', '5f4dcc3b5aa765d61d8327deb882cf99', 'ADADA', '', '1231', '123', '1', '2025-10-09 16:44:28', '2025-10-09 16:44:28');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

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
