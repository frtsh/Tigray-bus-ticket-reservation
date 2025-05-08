-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 21, 2024 at 02:17 PM
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
-- Database: `final`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES
(1, 'Mani', 'abc@gmail.com', '$2y$10$pU4UVIeGfo0WfEmqoEccZexP3GtcDJkdDp/e1CPlV7uIY9lEi2yZe'),
(2, 'siddiq', 'efg@gmail.com', '$2y$10$7umE/1HFn4oUOcarj1Lf7.MjGKkrrnxEl9g.upVR2IV17XknTB8i6'),
(3, 'sri ganesh', 'xyz@gmail.com', '$2y$10$I5bU.PK2aejUy5SzrqRElOY4QlSsP5bZxWRY70bxMo6dUSsyegcv6'),
(4, 'ram', 'ram@gmail.com', '$2y$10$y2LM7IB/owN9YgGKqwiPCeiA7jqvkbPoZm8beyfJu3Yw1JJM2O5l2'),
(5, 'KTRK', 'JGFHGg@gmail.com', '$2y$10$SQuOss5DA1lcCU2vAEE7ou8WkS7j9lSdrCDJdM0iWhz9FMHBjs5rC');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
