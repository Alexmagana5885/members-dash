-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2024 at 12:46 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `agldatabase`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `title`, `content`, `image_path`, `created_at`) VALUES
(1, 'the book of change', 'In a small, dusty town where stories had a way of fading into the past, there lay an old bookshop tucked between crumbling buildings. Inside, hidden on the highest shelf, was a worn, leather-bound book with no title. It had been forgotten for years, its pages yellowed and its cover cracked. One rainy afternoon, a curious young girl named Lily stumbled upon the shop. Drawn to the mysterious aura of the forgotten book, she reached up, her fingers brushing against the ancient spine. As she opened it, the room was suddenly filled with a golden light, and the pages began to glow. To her amazement, the book was no ordinary one—it was alive with stories, each page revealing a different world, a new adventure. Every word she read seemed to leap off the page, wrapping around her like a warm embrace. And so, in that quiet little shop, Lily discovered that the power of imagination and the magic of books could transcend time, bringing long-lost tales back to life.', '../assets/img/Blogs/the_book_of_change_20240908_105508.png', '2024-09-08 08:55:09');

-- --------------------------------------------------------

--
-- Table structure for table `event_registrations`
--

CREATE TABLE `event_registrations` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_location` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `member_email` varchar(255) NOT NULL,
  `member_name` varchar(255) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_registrations`
--

INSERT INTO `event_registrations` (`id`, `event_id`, `event_name`, `event_location`, `event_date`, `member_email`, `member_name`, `contact`, `registration_date`) VALUES
(1, 1, 'introduction to library tech', 'mombasa', '2024-09-01', 'Maganaalex634@gmail.com', 'alex magana', '0748027123', '2024-09-02 16:22:46'),
(2, 3, 'introduction to library tech', 'kisumu', '2024-08-28', 'Maganaalex634@gmail.com', 'alex magana', '0748027123', '2024-09-02 16:26:07'),
(3, 3, 'introduction to library tech', 'kisumu', '2024-08-28', 'Maganaalex634@gmail.com', 'alex magana', '0748027123', '2024-09-02 17:00:51'),
(4, 4, 'introduction to library tech', 'kisumu', '2024-08-28', 'Maganaalex634@gmail.com', 'alex magana', '0748027123', '2024-09-02 17:13:25'),
(5, 2, 'introduction to library tech', 'nairobi', '2024-09-04', 'Maganaalex634@outlook.com', 'alex magana', '0748027123', '2024-09-02 17:13:51'),
(6, 1, 'introduction to library tech', 'mombasa', '2024-09-01', 'Maganaalex634@outlook.com', 'alex magana', '0748027123', '2024-09-02 17:21:58'),
(7, 2, 'introduction to library tech', 'nairobi', '2024-09-04', 'Maganaalex634@outlook.com', 'alex magana', '0748027123', '2024-09-02 17:23:29'),
(8, 4, 'introduction to library tech', 'kisumu', '2024-08-28', 'Maganaalex634@outlook.com', 'alex magana', '0748027123', '2024-09-02 17:24:31'),
(9, 1, 'introduction to library tech', 'mombasa', '2024-09-01', 'Maganaalex634@outlook.com', 'alex magana', '0748027123', '2024-09-02 17:26:24'),
(10, 1, 'introduction to library tech', 'mombasa', '2024-09-01', 'Maganaalex634@outlook.com', 'alex magana', '0748027123', '2024-09-02 17:28:24'),
(11, 2, 'introduction to library tech', 'nairobi', '2024-09-04', 'Maganaalex634@gmail.com', 'alex magana', '0748027123', '2024-09-03 10:11:59'),
(12, 1, 'introduction to library tech', 'mombasa', '2024-09-01', 'jacklinendinda40@gmail.com', 'jackie', '+254711443724', '2024-09-07 23:24:23');

-- --------------------------------------------------------

--
-- Table structure for table `membermessages`
--

CREATE TABLE `membermessages` (
  `id` int(11) NOT NULL,
  `sender_name` varchar(255) NOT NULL,
  `sender_email` varchar(255) NOT NULL,
  `recipient_group` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `date_sent` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `membermessages`
--

INSERT INTO `membermessages` (`id`, `sender_name`, `sender_email`, `recipient_group`, `subject`, `message`, `date_sent`) VALUES
(1, 'alex magana', 'maganaalex634@gmail.com', 'all_members', 'general meeting ', 'you are all requred to attend the meeting on this 9th of september', '2024-09-03 21:56:31'),
(2, 'alex magana', 'maganaalex634@gmail.com', 'all_members', 'general meeting ', 'helloo', '2024-09-04 19:14:18'),
(3, 'alex magana', 'info@agl.or.ke', 'all_members', 'hi', 'test message\r\n', '2024-09-07 08:05:46'),
(4, 'alex magana', 'info@agl.or.ke', 'all_members', 'hi', 'test message\r\n', '2024-09-07 08:07:55'),
(5, 'alex magana', 'info@agl.or.ke', 'all_members', 'Hi', 'this is the test message from admin', '2024-09-08 11:31:58');

-- --------------------------------------------------------

--
-- Table structure for table `memberspayments`
--

CREATE TABLE `memberspayments` (
  `payment_id` int(11) NOT NULL,
  `member_email` varchar(100) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `payment_code` varchar(50) DEFAULT NULL,
  `time_of_payment` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member_payments`
--

CREATE TABLE `member_payments` (
  `id` int(11) NOT NULL,
  `member_email` varchar(255) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `mpesa_code` varchar(50) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `paytime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `officialmessages`
--

CREATE TABLE `officialmessages` (
  `id` int(11) NOT NULL,
  `sender_name` varchar(255) NOT NULL,
  `sender_email` varchar(255) NOT NULL,
  `recipient_group` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `date_sent` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `officialmessages`
--

INSERT INTO `officialmessages` (`id`, `sender_name`, `sender_email`, `recipient_group`, `subject`, `message`, `date_sent`) VALUES
(1, 'alex magana', 'maganaalex634@gmail.com', 'officials_only', 'general meeting ', 'you are all requred to attend the meeting on this 9th of september', '2024-09-03 21:57:37');

-- --------------------------------------------------------

--
-- Table structure for table `officialsmembers`
--

CREATE TABLE `officialsmembers` (
  `id` int(11) NOT NULL,
  `personalmembership_email` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `number_of_terms` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `officialsmembers`
--

INSERT INTO `officialsmembers` (`id`, `personalmembership_email`, `position`, `start_date`, `number_of_terms`) VALUES
(1, 'magana@gmial.com', 'admin', '2024-08-29', 2),
(2, 'magana@gmial.com', 'admin', '2024-08-29', 2),
(3, 'maganaalex634@gmail.com', 'Admin', '2024-09-08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `organizational_membership`
--

CREATE TABLE `organizational_membership` (
  `id` int(11) NOT NULL,
  `organization_name` varchar(255) NOT NULL,
  `organization_email` varchar(255) NOT NULL,
  `contact_person` varchar(255) NOT NULL,
  `logo_image` varchar(255) NOT NULL,
  `contact_phone_number` varchar(20) NOT NULL,
  `organization_date_of_registration` date NOT NULL,
  `organization_address` text NOT NULL,
  `location_country` varchar(100) NOT NULL,
  `location_county` varchar(100) NOT NULL,
  `location_town` varchar(100) NOT NULL,
  `registration_certificate` varchar(255) NOT NULL,
  `organization_type` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `what_you_do` text NOT NULL,
  `number_of_employees` int(11) NOT NULL,
  `payment_Number` varchar(50) NOT NULL,
  `payment_code` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `organizationmembership`
--

CREATE TABLE `organizationmembership` (
  `id` int(11) NOT NULL,
  `organization_name` varchar(255) NOT NULL,
  `organization_email` varchar(255) NOT NULL,
  `contact_person` varchar(255) NOT NULL,
  `logo_image` varchar(255) NOT NULL,
  `contact_phone_number` varchar(15) NOT NULL,
  `date_of_registration` date NOT NULL,
  `organization_address` text DEFAULT NULL,
  `location_country` varchar(255) NOT NULL,
  `location_county` varchar(255) NOT NULL,
  `location_town` varchar(255) NOT NULL,
  `registration_certificate` varchar(255) NOT NULL,
  `organization_type` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `what_you_do` text NOT NULL,
  `number_of_employees` int(11) NOT NULL,
  `payment_Number` varchar(50) NOT NULL,
  `payment_code` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organizationmembership`
--

INSERT INTO `organizationmembership` (`id`, `organization_name`, `organization_email`, `contact_person`, `logo_image`, `contact_phone_number`, `date_of_registration`, `organization_address`, `location_country`, `location_county`, `location_town`, `registration_certificate`, `organization_type`, `start_date`, `what_you_do`, `number_of_employees`, `payment_Number`, `payment_code`, `password`, `created_at`) VALUES
(0, 'Maglex', 'maganaalex634@gmail.com', 'Alex Magana', '../assets/img/MembersProfile/orgMembers/maganaalex634@gmail.com_1725915404.png', '0748027123', '2024-09-03', '1072', 'Kenya', 'Meru', 'Meru', '../assets/Documents/orgMembersDocuments/maganaalex634@gmail.com_1725915404.pdf', 'non governmental', '2024-09-04', 'IT services', 5, '', NULL, '$2y$10$cCBuBmHpBDA5uNXTj/iUfeKCG4psbe/RSzXxN71ROhcM252qWYdS.', '2024-09-09 20:56:44'),
(0, 'Maglex', 'Maganaalex64@gmail.com', 'Alex Magana', '../assets/img/MembersProfile/orgMembers/Maganaalex64@gmail.com_1725919366.jpg', '0748027123', '2024-09-14', '1072', 'Kenya', 'Meru', 'Meru', '../assets/Documents/orgMembersDocuments/Maganaalex64@gmail.com_1725919366.pdf', 'governmental', '2024-09-06', 'IT services', 50, '', NULL, '$2y$10$Ys.XVBYK52vylqUtdxvEzuXNIsuparyGlKDyRQmejwxfj9851r20a', '2024-09-09 22:02:46');

-- --------------------------------------------------------

--
-- Table structure for table `pastevents`
--

CREATE TABLE `pastevents` (
  `id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_details` text NOT NULL,
  `event_location` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `event_image_paths` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`event_image_paths`)),
  `event_document_paths` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`event_document_paths`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pastevents`
--

INSERT INTO `pastevents` (`id`, `event_name`, `event_details`, `event_location`, `event_date`, `event_image_paths`, `event_document_paths`, `created_at`) VALUES
(1, 'introduction to library tech', 'this is a brief introduction', 'kisumu', '2024-08-01', '[\"../assets/img/PastEvents/1724877140_introduction_to_library_tech_img_0.jpg\"]', '[\"../assets/Documents/PastEventsDocs/1724877140_introduction_to_library_tech_doc_0.html\"]', '2024-08-28 20:32:20'),
(2, 'test', 'test data innth', 'kisumu', '2024-08-08', '[\"../assets/img/PastEvents/1725004777_test_img_0.jpg\"]', '[\"../assets/Documents/PastEventsDocs/1725004777_test_doc_0.pdf\"]', '2024-08-30 07:59:37');

-- --------------------------------------------------------

--
-- Table structure for table `personalmembership`
--

CREATE TABLE `personalmembership` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `dob` date NOT NULL,
  `home_address` text DEFAULT NULL,
  `passport_image` varchar(255) NOT NULL,
  `highest_degree` varchar(100) NOT NULL,
  `institution` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `graduation_year` int(11) NOT NULL,
  `completion_letter` varchar(255) NOT NULL,
  `profession` varchar(100) NOT NULL,
  `experience` int(11) NOT NULL,
  `current_company` varchar(255) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `work_address` text DEFAULT NULL,
  `payment_Number` varchar(50) NOT NULL,
  `payment_code` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `personalmembership`
--

INSERT INTO `personalmembership` (`id`, `name`, `email`, `phone`, `dob`, `home_address`, `passport_image`, `highest_degree`, `institution`, `start_date`, `graduation_year`, `completion_letter`, `profession`, `experience`, `current_company`, `position`, `work_address`, `payment_Number`, `payment_code`, `password`, `registration_date`) VALUES
(5, 'alex', 'magana@gmial.com', '0797387302', '2024-08-09', 'Kiambu Road', '../assets/img/MembersProfile/magana@gmial.com_1724992631.jpg', 'computer science', 'St Paul’s University', '2024-08-30', 345, '../assets/Documents/MembersDocuments/magana@gmial.com_1724992631.pdf', 'ICT', 2, 'ODP', 'OFFICER', '1072', '', '432ireuwi', '$2y$10$KBUWphiafieIC2qsaCgRQOGLqNSTQpn23RxEIxKHTMU1BQuC/ow.i', '2024-08-30 04:37:11'),
(6, 'Alex Magana', 'maganaalex634@gmail.com', '0748027123', '2024-08-09', '1072', '../assets/img/MembersProfile/Maganaalex634@gmail.com_1725007319.jpg', 'computer science', 'St Paul’s University', '2024-08-15', 2024, '../assets/Documents/MembersDocuments/Maganaalex634@gmail.com_1725007319.pdf', 'ICT', 2, 'ODP', 'OFFICER', 'Kiambu Road', '', 'WERTYU345678ERTYU', '$2y$10$BKiPqZPhPICdJdXFLA4wfu0pfJmrqZcc4gZsOlrJBhDbyKMUFZaTa', '2024-08-30 08:41:59'),
(8, 'Magana', 'Maganaalex634@outlook.com', '0748027123', '2024-08-28', '1072', '../assets/img/MembersProfile/Maganaalex634@outlook.com_1725297078.jpg', 'computer science', 'Cisco networking academy', '2024-08-30', 2024, '../assets/Documents/MembersDocuments/Maganaalex634@outlook.com_1725297078.pdf', 'ICT', 2, 'maglex', 'OFFICER', 'Kiambu Road', '', 'rt355t6', '$2y$10$AyCaF2KZZNuiv4xQ0P3vYu7fmooSXPjQciy93yo8KM3WckT38m/eu', '2024-09-02 17:11:18'),
(10, 'Alex Magana', 'jacklinendinda40@gmail.com', '0748027123', '2024-09-04', '1072', '../assets/img/MembersProfile/jacklinendinda40@gmail.com_1725745710.jpg', 'computer science', 'St Paul’s University', '2024-08-27', 2024, '../assets/Documents/MembersDocuments/jacklinendinda40@gmail.com_1725745710.pdf', 'ICT', 4, 'maglex', 'OFFICER', 'Kiambu Road', '', NULL, '$2y$10$VMRT/5fBNJJZm1AdU/Rho.TBTWbZnqhpN2LaBgIDcmbCZUjJal7/u', '2024-09-07 21:48:30'),
(11, 'Alex Magana', 'maganaadmin@agl.or.ke', '0748027123', '2000-09-09', '1072', '../assets/img/MembersProfile/maganaadmin@agl.or.ke_1725797427.jpg', 'computer science', 'St Paul’s University', '2020-09-16', 2024, '../assets/Documents/MembersDocuments/maganaadmin@agl.or.ke_1725797427.pdf', 'Computer Science major', 3, 'Office of the Deputy President', 'OFFICER', 'harambee avenue Nairobi', '', NULL, '$2y$10$V6T8bXVPtSdzrcDh9l632OVStT0njAzCk9mS0lQlViII6K5p8cziu', '2024-09-08 12:10:27');

-- --------------------------------------------------------

--
-- Table structure for table `plannedevent`
--

CREATE TABLE `plannedevent` (
  `id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_image_path` varchar(255) NOT NULL,
  `event_description` text NOT NULL,
  `event_location` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plannedevent`
--

INSERT INTO `plannedevent` (`id`, `event_name`, `event_image_path`, `event_description`, `event_location`, `event_date`, `created_at`) VALUES
(1, 'introduction to library tech', '../assets/img/PlannedEvent/1724875423_introduction_to_library_tech.jpg', 'this is a test ', 'mombasa', '2024-09-01', '2024-08-28 20:03:43'),
(2, 'introduction to library tech', '../assets/img/PlannedEvent/1724875634_introduction_to_library_tech.jpg', 'Accessibility and Inclusivity\r\nAssistive Technologies: Libraries provide assistive technologies, such as screen readers, Braille displays, and adaptive keyboards, to ensure services are accessible to all patrons, including those with disabilities.', 'nairobi', '2024-09-04', '2024-08-28 20:07:14'),
(3, 'introduction to library tech', '../assets/img/PlannedEvent/1724875767_introduction_to_library_tech.jpg', '10. Accessibility and Inclusivity\r\nAssistive Technologies: Libraries provide assistive technologies, such as screen readers, Braille displays, and adaptive keyboards, to ensure services are accessible to all patrons, including those with disabilities.', 'kisumu', '2024-08-28', '2024-08-28 20:09:27'),
(4, 'introduction to library tech', '../assets/img/PlannedEvent/1724875807_introduction_to_library_tech.jpg', '10. Accessibility and Inclusivity\r\nAssistive Technologies: Libraries provide assistive technologies, such as screen readers, Braille displays, and adaptive keyboards, to ensure services are accessible to all patrons, including those with disabilities.', 'kisumu', '2024-08-28', '2024-08-28 20:10:07');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `MerchantRequestID` varchar(255) NOT NULL,
  `CheckoutRequestID` varchar(255) NOT NULL,
  `ResultCode` int(11) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `MpesaReceiptNumber` varchar(255) NOT NULL,
  `PhoneNumber` varchar(20) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `membermessages`
--
ALTER TABLE `membermessages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `memberspayments`
--
ALTER TABLE `memberspayments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `member_email` (`member_email`);

--
-- Indexes for table `member_payments`
--
ALTER TABLE `member_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_email` (`member_email`);

--
-- Indexes for table `officialmessages`
--
ALTER TABLE `officialmessages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `officialsmembers`
--
ALTER TABLE `officialsmembers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `personalmembership_email` (`personalmembership_email`);

--
-- Indexes for table `organizational_membership`
--
ALTER TABLE `organizational_membership`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `organization_email` (`organization_email`);

--
-- Indexes for table `pastevents`
--
ALTER TABLE `pastevents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personalmembership`
--
ALTER TABLE `personalmembership`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `plannedevent`
--
ALTER TABLE `plannedevent`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `event_registrations`
--
ALTER TABLE `event_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `membermessages`
--
ALTER TABLE `membermessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `memberspayments`
--
ALTER TABLE `memberspayments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `member_payments`
--
ALTER TABLE `member_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `officialmessages`
--
ALTER TABLE `officialmessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `officialsmembers`
--
ALTER TABLE `officialsmembers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `organizational_membership`
--
ALTER TABLE `organizational_membership`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pastevents`
--
ALTER TABLE `pastevents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `personalmembership`
--
ALTER TABLE `personalmembership`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `plannedevent`
--
ALTER TABLE `plannedevent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `memberspayments`
--
ALTER TABLE `memberspayments`
  ADD CONSTRAINT `memberspayments_ibfk_1` FOREIGN KEY (`member_email`) REFERENCES `personalmembership` (`email`) ON DELETE CASCADE;

--
-- Constraints for table `member_payments`
--
ALTER TABLE `member_payments`
  ADD CONSTRAINT `member_payments_ibfk_1` FOREIGN KEY (`member_email`) REFERENCES `personalmembership` (`email`);

--
-- Constraints for table `officialsmembers`
--
ALTER TABLE `officialsmembers`
  ADD CONSTRAINT `officialsmembers_ibfk_1` FOREIGN KEY (`personalmembership_email`) REFERENCES `personalmembership` (`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
