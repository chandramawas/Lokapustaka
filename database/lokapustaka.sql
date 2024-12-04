-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 04, 2024 at 08:26 AM
-- Server version: 8.0.39
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lokapustaka_dupl`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` varchar(5) NOT NULL COMMENT 'Primary Key',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `isbn` varchar(13) NOT NULL,
  `cover` mediumblob,
  `author` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `category` enum('Fiksi','Non-Fiksi','Teknologi dan Sains','Seni dan Budaya','Kesehatan','Pendidikan Anak','Referensi','Hukum','Pengembangan Diri','Petualangan','Karya Ilmiah') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `publisher` varchar(50) NOT NULL,
  `year_published` varchar(4) NOT NULL,
  `available_stock` int NOT NULL DEFAULT '0',
  `created_by` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `books`
--
DELIMITER $$
CREATE TRIGGER `before_insert_books` BEFORE INSERT ON `books` FOR EACH ROW BEGIN
    DECLARE next_number INT;
    DECLARE formatted_id VARCHAR(5);
    -- Get the next sequence number
    SELECT next_id INTO next_number FROM books_id_sequence;
    -- Format the ID as S0001, S0002, etc.
    SET formatted_id = CONCAT('B', LPAD(next_number, 4, '0'));
    -- Assign the formatted ID to the new row
    SET NEW.id = formatted_id;
    -- Increment the sequence
    UPDATE books_id_sequence SET next_id = next_id + 1;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `books_id_sequence`
--

CREATE TABLE `books_id_sequence` (
  `next_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `books_id_sequence`
--

INSERT INTO `books_id_sequence` (`next_id`) VALUES
(1);

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(10) UNSIGNED ZEROFILL NOT NULL COMMENT 'Primary Key',
  `member_id` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `book_id` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `borrow_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expected_return_date` timestamp NOT NULL,
  `return_date` timestamp NULL DEFAULT NULL,
  `max_extend` tinyint(1) NOT NULL DEFAULT '0',
  `fines` int DEFAULT NULL,
  `created_by` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` varchar(5) NOT NULL COMMENT 'Primary Key',
  `expired_date` timestamp NOT NULL,
  `name` varchar(50) NOT NULL,
  `phone_num` varchar(15) NOT NULL,
  `password` varchar(60) NOT NULL,
  `street` varchar(30) NOT NULL,
  `home_num` varchar(5) NOT NULL,
  `province` varchar(25) NOT NULL,
  `regency` varchar(25) NOT NULL,
  `district` varchar(25) NOT NULL,
  `village` varchar(25) NOT NULL,
  `postal_code` varchar(8) NOT NULL,
  `created_by` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `members`
--
DELIMITER $$
CREATE TRIGGER `before_insert_members` BEFORE INSERT ON `members` FOR EACH ROW BEGIN
    DECLARE next_number INT;
    DECLARE formatted_id VARCHAR(5);
    -- Get the next sequence number
    SELECT next_id INTO next_number FROM members_id_sequence;
    -- Format the ID as S0001, S0002, etc.
    SET formatted_id = CONCAT('A', LPAD(next_number, 4, '0'));
    -- Assign the formatted ID to the new row
    SET NEW.id = formatted_id;
    -- Increment the sequence
    UPDATE members_id_sequence SET next_id = next_id + 1;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `members_id_sequence`
--

CREATE TABLE `members_id_sequence` (
  `next_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `members_id_sequence`
--

INSERT INTO `members_id_sequence` (`next_id`) VALUES
(1);

-- --------------------------------------------------------

--
-- Table structure for table `staffs`
--

CREATE TABLE `staffs` (
  `id` varchar(5) NOT NULL COMMENT 'Primary Key',
  `name` varchar(50) NOT NULL,
  `password` varchar(60) NOT NULL,
  `phone_num` varchar(15) NOT NULL,
  `roles` enum('Admin','Staff','Superadmin') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Staff',
  `created_by` varchar(5) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `staffs`
--

INSERT INTO `staffs` (`id`, `name`, `password`, `phone_num`, `roles`, `created_by`, `created_at`) VALUES
('root', 'root', '$2y$10$qCYtR0YgSVGmem6w/sNXwOhVcqE3qVfc5B.bsxS7z8qMJl1wo2RJu', '0', 'Superadmin', NULL, '2023-12-31 17:00:00');

--
-- Triggers `staffs`
--
DELIMITER $$
CREATE TRIGGER `before_insert_staffs` BEFORE INSERT ON `staffs` FOR EACH ROW BEGIN
    DECLARE next_number INT;
    DECLARE formatted_id VARCHAR(5);
    -- Get the next sequence number
    SELECT next_id INTO next_number FROM staffs_id_sequence;
    -- Format the ID as S0001, S0002, etc.
    SET formatted_id = CONCAT('S', LPAD(next_number, 4, '0'));
    -- Assign the formatted ID to the new row
    SET NEW.id = formatted_id;
    -- Increment the sequence
    UPDATE staffs_id_sequence SET next_id = next_id + 1;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `staffs_id_sequence`
--

CREATE TABLE `staffs_id_sequence` (
  `next_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `staffs_id_sequence`
--

INSERT INTO `staffs_id_sequence` (`next_id`) VALUES
(1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `books_ibfk_1` (`created_by`);
ALTER TABLE `books` ADD FULLTEXT KEY `id` (`id`,`title`,`isbn`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loans_ibfk_1` (`member_id`),
  ADD KEY `loans_ibfk_2` (`book_id`),
  ADD KEY `loans_ibfk_3` (`created_by`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone_num` (`phone_num`),
  ADD KEY `created_by` (`created_by`);
ALTER TABLE `members` ADD FULLTEXT KEY `id` (`id`,`name`);

--
-- Indexes for table `staffs`
--
ALTER TABLE `staffs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone_num` (`phone_num`),
  ADD KEY `users_ibfk_1` (`created_by`);
ALTER TABLE `staffs` ADD FULLTEXT KEY `id` (`id`,`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT COMMENT 'Primary Key';

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `staffs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `loans_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `loans_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `staffs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `staffs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
