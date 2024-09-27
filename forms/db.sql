-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 27, 2024 at 11:50 AM
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
(7, '7 Habits That Have a High Rate of Return in Life', 'Saw this on Twitter the other day and had to share it around: So true, right? Especially that first one? SO WHY AREN\\\'T WE DOING THEM ALL', '../assets/img/Blogs/7_Habits_That_Have_a_High_Rate_of_Return_in_Life_20240911_111400.jpeg', '2024-09-11 09:14:00'),
(9, 'test story', 'The Lighthouse Keeper’s Promise\\r\\nOn a rugged coastline, where the waves crashed fiercely against the rocks, stood an old lighthouse. Its light, though dimmed by time, had guided many a sailor safely to shore. The keeper of this lighthouse was a grizzled old man named Elias.\\r\\n\\r\\nElias had tended to the lighthouse for as long as anyone could remember. His face was weathered by the salt and sun, and his hands bore the marks of decades of labor. He lived a solitary life, with only the sea and the beacon for company.\\r\\n\\r\\nOne stormy night, as lightning danced across the sky and thunder rumbled like a beast, Elias noticed a small boat struggling against the storm. He knew it was not just any boat; it was the boat of a young woman who had recently come to the village seeking help for her ailing father. Her name was Mira.\\r\\n\\r\\nDetermined to save her, Elias climbed the creaky stairs of the lighthouse, bracing himself against the wind. He turned the light’s beam towards the storm, guiding Mira’s boat through the tumultuous sea. Hours seemed like days, but Elias stayed true to his task, unwavering in his commitment.\\r\\n\\r\\nAs dawn broke, the storm subsided, and Mira’s boat, battered but safe, reached the shore. Exhausted, she saw Elias standing by the lighthouse, his face etched with relief. With tears in her eyes, she thanked him.\\r\\n\\r\\nElias simply nodded, saying, \\\"I promised the sea that I would always guide those in need. It’s a promise I intend to keep.\\\"', '../assets/img/Blogs/test_story_20240916_121732.jpeg', '2024-09-16 10:17:33'),
(10, 'test blog2ee', 'The Lighthouse Keeper’s Promise\\r\\nOn a rugged coastline, where the waves crashed fiercely against the rocks, stood an old lighthouse. Its light, though dimmed by time, had guided many a sailor safely to shore. The keeper of this lighthouse was a grizzled old man named Elias.\\r\\n\\r\\nElias had tended to the lighthouse for as long as anyone could remember. His face was weathered by the salt and sun, and his hands bore the marks of decades of labor. He lived a solitary life, with only the sea and the beacon for company.\\r\\n\\r\\nOne stormy night, as lightning danced across the sky and thunder rumbled like a beast, Elias noticed a small boat struggling against the storm. He knew it was not just any boat; it was the boat of a young woman who had recently come to the village seeking help for her ailing father. Her name was Mira.\\r\\n\\r\\nDetermined to save her, Elias climbed the creaky stairs of the lighthouse, bracing himself against the wind. He turned the light’s beam towards the storm, guiding Mira’s boat through the tumultuous sea. Hours seemed like days, but Elias stayed true to his task, unwavering in his commitment.\\r\\n\\r\\nAs dawn broke, the storm subsided, and Mira’s boat, battered but safe, reached the shore. Exhausted, she saw Elias standing by the lighthouse, his face etched with relief. With tears in her eyes, she thanked him.\\r\\n\\r\\nElias simply nodded, saying, \\\"I promised the sea that I would always guide those in need. It’s a promise I intend to keep.\\\"\\r\\n\\r\\nYears passed, and Elias\\\'s hair turned completely white. His legend grew, becoming a beacon of hope for many. Mira, now a grown woman, returned often to visit him, bringing her children and sharing stories of the lighthouse keeper who saved her life.\\r\\n\\r\\nOne day, as Elias’s strength waned, he knew his time was near. Mira sat with him, holding his hand. \\\"Thank you for everything,\\\" she said softly.\\r\\n\\r\\nElias smiled faintly. \\\"I’ve always been just a guide. It’s the light that truly matters.\\\"\\r\\n\\r\\nWith that, Elias closed his eyes for the final time. The villagers honored him by dedicating the lighthouse to his memory, ensuring that the light would continue to shine brightly, guiding sailors safely home.\\r\\n\\r\\nAnd so, the old lighthouse stood as a testament to Elias’s promise—a promise that, no matter how fierce the storm, there would always be a light to guide the lost.\\r\\n\\r\\n', '../assets/img/Blogs/test_blog2ee_20240916_123737.jpeg', '2024-09-16 10:37:37');

-- --------------------------------------------------------

--
-- Table structure for table `eventregcheckout`
--

CREATE TABLE `eventregcheckout` (
  `id` int(11) NOT NULL,
  `CheckoutRequestID` varchar(255) NOT NULL,
  `event_id` varchar(255) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_location` varchar(255) NOT NULL,
  `event_date` datetime NOT NULL,
  `email` varchar(255) NOT NULL,
  `member_name` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Failed','Completed') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eventregcheckout`
--

INSERT INTO `eventregcheckout` (`id`, `CheckoutRequestID`, `event_id`, `event_name`, `event_location`, `event_date`, `email`, `member_name`, `phone`, `amount`, `status`, `created_at`) VALUES
(0, 'ws_CO_27092024094742224748027123', '10', 'introduction to library tech', 'Nairobi ', '2024-09-30 00:00:00', 'maganaadmin@agl.or.ke', 'alex magana', '254748027123', 1.00, 'Pending', '2024-09-27 06:47:47');

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
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(10, 'alex magana', 'info@agl.or.ke', 'all_members', 'Website Building meeting', 'Starting from 10pm', '2024-09-12 22:58:31');

-- --------------------------------------------------------

--
-- Table structure for table `member_payments`
--

CREATE TABLE `member_payments` (
  `id` int(11) NOT NULL,
  `member_email` varchar(255) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `payment_code` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mpesa_transactions`
--

CREATE TABLE `mpesa_transactions` (
  `CheckoutRequestID` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `mpesa_transactions`
--

INSERT INTO `mpesa_transactions` (`CheckoutRequestID`, `email`, `phone`, `amount`, `status`, `transaction_date`) VALUES
('ws_CO_13092024145806687724263598', 'test@gmail.com', '254724263598', 1.00, 'Pending', '2024-09-13 11:58:08'),
('ws_CO_13092024151554256721257524', 'test@gmail.com', '254721257524', 1.00, 'Pending', '2024-09-13 12:15:56'),
('ws_CO_13092024151618898721257824', 'test@gmail.com', '254721257824', 1.00, 'Pending', '2024-09-13 12:16:21'),
('ws_CO_13092024151729563748027123', 'maganaadmin@agl.or.ke', '254748027123', 1.00, 'Pending', '2024-09-13 12:17:16');

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

-- --------------------------------------------------------

--
-- Table structure for table `organizationmembership`
--

CREATE TABLE `organizationmembership` (
  `id` int(11) NOT NULL,
  `organization_name` varchar(255) NOT NULL,
  `organization_email` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `logo_image` varchar(255) DEFAULT NULL,
  `contact_phone_number` varchar(20) DEFAULT NULL,
  `date_of_registration` date DEFAULT NULL,
  `organization_address` text DEFAULT NULL,
  `location_country` varchar(100) DEFAULT NULL,
  `location_county` varchar(100) DEFAULT NULL,
  `location_town` varchar(100) DEFAULT NULL,
  `registration_certificate` varchar(255) DEFAULT NULL,
  `organization_type` varchar(100) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `what_you_do` text DEFAULT NULL,
  `payment_Number` varchar(50) DEFAULT NULL,
  `payment_code` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organizationmembership`
--

INSERT INTO `organizationmembership` (`id`, `organization_name`, `organization_email`, `contact_person`, `logo_image`, `contact_phone_number`, `date_of_registration`, `organization_address`, `location_country`, `location_county`, `location_town`, `registration_certificate`, `organization_type`, `start_date`, `what_you_do`, `payment_Number`, `payment_code`, `password`, `created_at`) VALUES
(6, 'ASSOCIATION OF GOVERNMENT LIBRARIANS', 'maganaadmin@agl.or.ke', 'UYTR', '../assets/img/MembersProfile/orgMembers/maganaadmin@agl.or.ke_1727422854.jpeg', '0748027123', '2024-09-19', '7654TR', 'UYTR', 'YTRE', 'YTREW', '../assets/Documents/orgMembersDocuments/maganaadmin@agl.or.ke_1727422854.pdf', 'UYTREW', '2024-09-06', 'UYTRE', NULL, NULL, '$2y$10$AgOYxh90JCCnXtsYdUtyVecyel1mny3Jl3r3ybgOF6K/oieSENXnS', '2024-09-27 07:40:54');

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
(6, 'History of Agle', 'A library is an organized collection of information resources, such as books, periodicals, manuscripts, audiovisual materials, and digital content, designed to facilitate access to knowledge, learning, and research. Libraries have been integral to societies for centuries, serving as repositories of cultural heritage, academic materials, and general knowledge. They provide not only physical collections but also various services, including lending systems, reference assistance, study areas, and programs for different age groups. Libraries come in many forms, each tailored to meet the needs of specific communities. Public libraries, for instance, are open to all members of the community, offering a broad range of materials, including fiction, nonfiction, reference books, and multimedia resources. They often serve as community centers, hosting educational workshops, literacy programs, and cultural events.\r\n\r\nAcademic libraries, found in universities and colleges, are crucial to the academic success of students and faculty. They house specialized collections that support research and education, offering access to scholarly articles, textbooks, and academic journals. These libraries often provide online databases and research tools that aid in academic work. Special libraries, on the other hand, serve a particular industry or organization, such as corporate, legal, or medical institutions. Their collections and services are focused on the specific needs of professionals, providing highly specialized materials and expert assistance.\r\n\r\nWith advancements in technology, digital libraries have emerged, offering access to e-books, online journals, databases, and multimedia content from remote locations. These libraries have become increasingly popular, allowing users to access vast amounts of information with ease. Many traditional libraries now provide hybrid services, offering both physical and digital resources to cater to the evolving needs of their patrons.\r\n\r\nOverall, libraries play a critical role in society by promoting education, literacy, and lifelong learning. They provide equitable access to information, regardless of socioeconomic status, and serve as guardians of knowledge, preserving cultural and historical records for future generations. Whether physical or digital, libraries continue to be essential pillars of intellectual growth and community development.', 'Mombasa', '2024-09-02', '[\"..\\/assets\\/img\\/PastEvents\\/1726224982_History_of_Agle_img_0.jpg\"]', '[]', '2024-09-13 10:56:22');

-- --------------------------------------------------------

--
-- Table structure for table `personalmembership`
--

CREATE TABLE `personalmembership` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `home_address` text DEFAULT NULL,
  `passport_image` varchar(255) NOT NULL,
  `highest_degree` varchar(100) NOT NULL,
  `institution` varchar(255) NOT NULL,
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
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `gender` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `personalmembership`
--

INSERT INTO `personalmembership` (`id`, `name`, `email`, `phone`, `home_address`, `passport_image`, `highest_degree`, `institution`, `graduation_year`, `completion_letter`, `profession`, `experience`, `current_company`, `position`, `work_address`, `payment_Number`, `payment_code`, `password`, `registration_date`, `gender`) VALUES
(27, 'Alex Magana', 'maganaadmin@agl.or.ke', '0748027123', '1072-MERU', '../assets/img/MembersProfile/maganaadmin@agl.or.ke_1727421327.jpeg', 'Degree', 'St Paul&#039;s University', 2024, '../assets/Documents/MembersDocuments/maganaadmin@agl.or.ke_1727421327.pdf', 'Computer Science', 3, 'Office Of The Deputy President', 'officer', 'Harambee avenue NAIROBI', '', NULL, '$2y$10$59YV3YduqXBXXZg/iV59quplzatsmCbSy1JE2P5drquz43FfWMuky', '2024-09-27 07:15:27', 'Male');

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
(10, 'introduction to library tech', '../assets/img/PlannedEvent/1726225139_introduction_to_library_tech.jpg', 'We will talk about the future of technology and libraries ', 'Nairobi ', '2024-09-30', '2024-09-13 10:58:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `membermessages`
--
ALTER TABLE `membermessages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_payments`
--
ALTER TABLE `member_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mpesa_transactions`
--
ALTER TABLE `mpesa_transactions`
  ADD PRIMARY KEY (`CheckoutRequestID`);

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
-- Indexes for table `organizationmembership`
--
ALTER TABLE `organizationmembership`
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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `membermessages`
--
ALTER TABLE `membermessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `member_payments`
--
ALTER TABLE `member_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `officialmessages`
--
ALTER TABLE `officialmessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `officialsmembers`
--
ALTER TABLE `officialsmembers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `organizationmembership`
--
ALTER TABLE `organizationmembership`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pastevents`
--
ALTER TABLE `pastevents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `personalmembership`
--
ALTER TABLE `personalmembership`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `plannedevent`
--
ALTER TABLE `plannedevent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `officialsmembers`
--
ALTER TABLE `officialsmembers`
  ADD CONSTRAINT `officialsmembers_ibfk_1` FOREIGN KEY (`personalmembership_email`) REFERENCES `personalmembership` (`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
