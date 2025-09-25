-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 25, 2025 at 09:39 PM
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
-- Database: `crm_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `assignment_id` int(11) NOT NULL,
  `complaint_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `assigned_at` datetime DEFAULT current_timestamp(),
  `deadline` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT 'In Progress'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`assignment_id`, `complaint_id`, `employee_id`, `assigned_at`, `deadline`, `status`) VALUES
(10, 15, 6, '2025-09-09 21:14:20', '2025-09-10 21:14:20', 'In Progress'),
(11, 13, 4, '2025-09-09 21:14:27', '2025-09-10 21:14:27', 'In Progress'),
(12, 14, 8, '2025-09-09 21:14:42', '2025-09-12 21:14:42', 'In Progress'),
(13, 12, 1, '2025-09-09 21:14:49', '2025-09-14 21:14:49', 'In Progress'),
(14, 16, 8, '2025-09-09 21:19:20', '2025-09-14 21:19:20', 'In Progress'),
(15, 18, 12, '2025-09-09 21:19:35', '2025-09-10 21:19:35', 'In Progress'),
(16, 17, 10, '2025-09-09 21:19:44', '2025-09-14 21:19:44', 'In Progress');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `client_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`client_id`, `name`, `email`, `password`, `status`, `product_id`) VALUES
(1, 'chris', 'chris@gmail.com', '$2y$10$6PgkBDvdTWn0rj1F6AqRpenlKrMueL8FtwcwTkc1z90/YLFfq.kxq', 'inactive', 1),
(6, 'Manuel Jacob', 'manueljacob@client.com', '$2y$10$rgYc2/lLiqxILI5Y9CdvVev9UByNnrXtHKKdaJh36QAdg3oCYVZFG', 'active', 4),
(7, 'Christin Benny', 'christin@client.com', '$2y$10$QDEB7.UbObwqlK.picQYveoj3PMqTczMdvPUDQHuvNZf0TKPlX1Tu', 'active', 6),
(8, 'Olivia Thompson', 'olivia@client.com', '$2y$10$6KateNAoEmBhlEsVw/tLjOUu3XYmWRfedlK5x3x5ZNt0PNDeg209a', 'active', 3),
(9, 'Ethan Roberts', 'ethan@client.com', '$2y$10$bJuHsPHjhRvOajsIuw9uMuKlLhRU4T6Apvr4UktIjO1X2jK2D0uT6', 'active', 6),
(10, 'Sophia Martinez', 'sophia@client.com', '$2y$10$.SoweI6rManUQvoyeufrFO/HS0QcXNarCSDIVcknvgTxGO9oqj1uu', 'active', 7),
(11, 'Aarav Sharma', 'aarav@client.com', '$2y$10$cYG2UmHzYitiQ0BwJ4njVeZnOacFlVK2l0YBt3WX6atBxWJEd8e/2', 'active', 5),
(12, 'Ishita Nair', 'ishita@client.com', '$2y$10$Oo3SOJTwf.Wj3NVwiNIDP.p5NKEBZOxGiyzcKfRw3zP/Q1w26mCiO', 'active', 5);

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `complaint_id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `priority` enum('High','Medium','Low') DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `product_id` int(11) DEFAULT NULL,
  `is_inactive` tinyint(1) DEFAULT 0,
  `inactive_reason` varchar(255) DEFAULT NULL,
  `inactivated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`complaint_id`, `client_id`, `description`, `priority`, `status`, `created_at`, `product_id`, `is_inactive`, `inactive_reason`, `inactivated_at`) VALUES
(12, 6, 'Website Crashes on load', 'Low', 'Resolved', '2025-09-10 00:39:00', 4, 0, NULL, NULL),
(13, 6, 'IT Support wasnt satisfactory', 'High', 'Assigned', '2025-09-10 00:39:43', 1, 1, 'Product deactivated', '2025-09-10 00:55:20'),
(14, 7, 'Environment takes longer time to render', 'Medium', 'Assigned', '2025-09-10 00:40:45', 6, 0, NULL, NULL),
(15, 8, 'Firewall keeps failing to detect malicious files', 'High', 'Assigned', '2025-09-10 00:41:50', 3, 0, NULL, NULL),
(16, 9, 'NOT SATISFACTORY', 'Low', 'Resolved', '2025-09-10 00:47:10', 6, 0, NULL, NULL),
(17, 10, 'Change the profile icon styles', 'Low', 'Assigned', '2025-09-10 00:47:57', 7, 0, NULL, NULL),
(18, 11, 'The app is not autoscaling when i opened in my tablet', 'High', 'Assigned', '2025-09-10 00:48:57', 5, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `name`, `email`, `password`, `product_id`) VALUES
(1, 'John Doe', 'john@example.com', 'john123', 4),
(2, 'Aloshy', 'aloshy@example.com', 'aloshy123', 3),
(3, 'Jess', 'jess@example.com', 'jess123', 2),
(4, 'Anuj ', 'anuj@gmail.com', 'anuj', 1),
(5, 'emp', 'emp@gmail.com', 'emp', 1),
(6, 'Liya', 'liya@gmail.com', 'liya', 3),
(7, 'Ele', 'ele@gmail.com', 'ele', 2),
(8, 'Ben ', 'ben@gmail.com', 'ben123', 6),
(9, 'Alen', 'alen@gmail.com', 'alen123', 2),
(10, 'Alina', 'alina@gmail.com', 'alina123', 7),
(11, 'Christina', 'christina@gmail.com', 'christina123', 5),
(12, 'Fousteena', 'fousteena@gmail.com', 'fousteena123', 5);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `complaint_id` int(11) DEFAULT NULL,
  `sender_type` enum('client','employee','admin') DEFAULT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `sent_at` datetime DEFAULT current_timestamp(),
  `sender` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `complaint_id`, `sender_type`, `sender_id`, `message`, `sent_at`, `sender`) VALUES
(41, 16, NULL, NULL, 'Hey', '2025-09-10 01:01:22', 'client'),
(42, 16, NULL, NULL, 'Hello whtas the issue', '2025-09-10 01:02:07', 'employee'),
(43, 16, NULL, NULL, 'product was not satisfactory', '2025-09-10 01:02:54', 'client'),
(44, 16, NULL, NULL, 'we will rectify it', '2025-09-10 01:03:10', 'employee');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `created_at`, `is_active`) VALUES
(1, 'IT CONSULTING', 'offer consulting services to help businesses plan and implement technology solutions', 2000.00, '2025-05-05 10:06:20', 0),
(2, 'CLOUD SERVICES', 'cloud storage, virtual desktops, and cloud-based software applications', 10000.00, '2025-05-05 10:06:20', 0),
(3, 'CYBER SECURITY', 'firewalls, intrusion detection and prevention systems, and cybersecurity consulting', 10000.00, '2025-05-05 10:06:20', 1),
(4, ' WEB APP DEVELOPMENT', 'creating responsive websites, portals, and web apps.', 4000.00, '2025-09-10 00:23:26', 1),
(5, 'MOBILE APP DEVELOPMENT', 'iOS, Android, and cross-platform apps for businesses or consumers', 5099.00, '2025-09-10 00:23:58', 1),
(6, 'AR/VR Development', 'Creating immersive applications for training, gaming, or marketing.', 12000.00, '2025-09-10 00:24:35', 1),
(7, 'UI/UX DESIGN SERVICES', 'Creating user-friendly interfaces and enhancing customer experiences', 2099.00, '2025-09-10 00:25:32', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `complaint_id` (`complaint_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_client_product` (`product_id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`complaint_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `fk_complaint_product` (`product_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_employee_product` (`product_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `complaint_id` (`complaint_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `complaint_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
