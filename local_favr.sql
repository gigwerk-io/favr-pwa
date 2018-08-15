-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 15, 2018 at 08:00 AM
-- Server version: 10.1.34-MariaDB
-- PHP Version: 7.0.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `local_favr`
--

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `blocked` tinyint(1) NOT NULL DEFAULT '0',
  `scope` enum('Private','Friends','Public','Friends of Friends') NOT NULL DEFAULT 'Friends',
  `given` int(11) NOT NULL DEFAULT '0',
  `received` int(11) NOT NULL DEFAULT '0',
  `friends_since` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `friends_favr_requests`
--

CREATE TABLE `friends_favr_requests` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `friend_id` int(11) DEFAULT NULL,
  `task_freelancer_accepted` int(11) NOT NULL DEFAULT '0',
  `task_freelancer_count` int(11) NOT NULL DEFAULT '1',
  `task_category` enum('General Request','Home Improvement','Yard Work') NOT NULL DEFAULT 'General Request',
  `task_description` varchar(255) NOT NULL,
  `task_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `task_location` varchar(255) NOT NULL,
  `task_contact` varchar(255) DEFAULT NULL,
  `task_time_to_accomplish` time DEFAULT NULL,
  `arrival_time` timestamp NULL DEFAULT NULL,
  `completion_time` timestamp NULL DEFAULT NULL,
  `favr_price` int(11) NOT NULL DEFAULT '0',
  `task_price` decimal(9,2) NOT NULL DEFAULT '0.00',
  `task_intensity` enum('Hard','Medium','Easy') DEFAULT NULL,
  `task_status` enum('Requested','Pending Approval','Paid','In Progress','Completed') NOT NULL DEFAULT 'Requested',
  `task_rating` int(11) DEFAULT NULL,
  `task_optional_service_review` varchar(255) DEFAULT NULL,
  `task_picture_path_1` varchar(255) DEFAULT NULL,
  `task_picture_path_2` varchar(255) DEFAULT NULL,
  `task_picture_path_3` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `marketplace_favr_freelancers`
--

CREATE TABLE `marketplace_favr_freelancers` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `arrival_time` timestamp NULL DEFAULT NULL,
  `completion_time` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `marketplace_favr_freelancers`
--

INSERT INTO `marketplace_favr_freelancers` (`id`, `request_id`, `user_id`, `approved`, `arrival_time`, `completion_time`) VALUES
(14, 46, 6, 1, '2018-08-12 08:19:03', '2018-08-12 08:19:41'),
(15, 47, 6, 1, '2018-08-13 10:51:57', '2018-08-13 10:52:17'),
(16, 48, 6, 1, '2018-08-13 11:55:47', '2018-08-13 11:56:12'),
(17, 49, 6, 1, '2018-08-13 12:00:54', '2018-08-13 12:01:13'),
(18, 50, 6, 1, '2018-08-13 12:30:16', '2018-08-13 12:31:27'),
(21, 52, 6, 1, '2018-08-13 16:10:26', '2018-08-13 16:10:46');

-- --------------------------------------------------------

--
-- Table structure for table `marketplace_favr_requests`
--

CREATE TABLE `marketplace_favr_requests` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `freelancer_id` int(11) DEFAULT NULL,
  `task_freelancer_accepted` int(11) NOT NULL DEFAULT '0',
  `task_freelancer_count` int(11) NOT NULL DEFAULT '1',
  `task_category` enum('General Request','Home Improvement','Yard Work') NOT NULL DEFAULT 'General Request',
  `task_description` varchar(255) NOT NULL,
  `task_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `task_location` varchar(255) NOT NULL,
  `task_contact` varchar(255) DEFAULT NULL,
  `task_time_to_accomplish` time DEFAULT NULL,
  `task_price` decimal(9,2) NOT NULL DEFAULT '0.00',
  `task_intensity` enum('Hard','Medium','Easy') DEFAULT NULL,
  `task_status` enum('Requested','Pending Approval','Paid','In Progress','Completed') NOT NULL DEFAULT 'Requested',
  `task_rating` int(11) DEFAULT NULL,
  `task_optional_service_review` varchar(255) DEFAULT NULL,
  `task_picture_path_1` varchar(255) DEFAULT NULL,
  `task_picture_path_2` varchar(255) DEFAULT NULL,
  `task_picture_path_3` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `marketplace_favr_requests`
--

INSERT INTO `marketplace_favr_requests` (`id`, `customer_id`, `freelancer_id`, `task_freelancer_accepted`, `task_freelancer_count`, `task_category`, `task_description`, `task_date`, `task_location`, `task_contact`, `task_time_to_accomplish`, `task_price`, `task_intensity`, `task_status`, `task_rating`, `task_optional_service_review`, `task_picture_path_1`, `task_picture_path_2`, `task_picture_path_3`) VALUES
(46, 7, 46, 1, 1, 'General Request', 'test', '2018-08-12 01:19:41', ', Rochester, Minnesota, 55901', NULL, NULL, '5.00', 'Medium', 'Completed', 4, 'Haron was great!', NULL, NULL, NULL),
(47, 7, 47, 1, 1, 'General Request', 'test', '2018-08-13 03:52:17', ', Rochester, Minnesota, 55901', NULL, NULL, '3.00', 'Easy', 'Completed', 5, 'Was great!', NULL, NULL, NULL),
(48, 7, 48, 1, 1, 'General Request', 'Test', '2018-08-13 04:57:07', ', Rochester, Minnesota, 55901', NULL, NULL, '34.00', 'Hard', 'Completed', 1, 'Wasn\'t so great..', NULL, NULL, NULL),
(49, 7, 49, 1, 1, 'General Request', 'Test', '2018-08-13 05:01:13', ', Rochester, Minnesota, 55901', NULL, NULL, '6.00', 'Medium', 'Completed', 4, 'Decent service!', NULL, NULL, NULL),
(50, 7, 50, 1, 1, 'General Request', 'Test', '2018-08-13 17:31:27', ', Rochester, Minnesota, 55901', NULL, NULL, '5.00', 'Medium', 'Completed', 3, 'Good job!', NULL, NULL, NULL),
(52, 7, 52, 1, 1, 'General Request', 'test', '2018-08-13 21:10:46', ', Rochester, Minnesota, 55901', NULL, NULL, '6.00', '', 'Completed', 5, 'Wonderful!!', NULL, NULL, NULL),
(54, 7, NULL, 0, 1, 'General Request', 'Test', '2018-08-15 02:10:02', ', Rochester, Minnesota, 55901', NULL, NULL, '5.00', 'Hard', 'Requested', NULL, NULL, 'a:4:{s:4:\"name\";s:37:\"2efaf4414162a943e71267acae19b313.jpeg\";s:4:\"type\";s:10:\"image/jpeg\";s:4:\"size\";i:1896240;s:7:\"task_id\";s:2:\"54\";}', 'a:4:{s:4:\"name\";s:37:\"e5a56ad003543fc2a3b84f23790d44ac.jpeg\";s:4:\"type\";s:10:\"image/jpeg\";s:4:\"size\";i:2604768;s:7:\"task_id\";s:2:\"54\";}', 'a:4:{s:4:\"name\";s:37:\"2420910c938f2536795569d44282b2cf.jpeg\";s:4:\"type\";s:10:\"image/jpeg\";s:4:\"size\";i:1268382;s:7:\"task_id\";s:2:\"54\";}');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `access_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(80) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(4000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_authorization_codes`
--

CREATE TABLE `oauth_authorization_codes` (
  `authorization_code` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(80) DEFAULT NULL,
  `redirect_uri` varchar(2000) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(4000) DEFAULT NULL,
  `id_token` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `client_id` varchar(80) NOT NULL,
  `client_secret` varchar(80) DEFAULT NULL,
  `redirect_uri` varchar(2000) DEFAULT NULL,
  `grant_types` varchar(80) DEFAULT NULL,
  `scope` varchar(4000) DEFAULT NULL,
  `user_id` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_jwt`
--

CREATE TABLE `oauth_jwt` (
  `client_id` varchar(80) NOT NULL,
  `subject` varchar(80) DEFAULT NULL,
  `public_key` varchar(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `refresh_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(80) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(4000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_scopes`
--

CREATE TABLE `oauth_scopes` (
  `scope` varchar(80) NOT NULL,
  `is_default` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_users`
--

CREATE TABLE `oauth_users` (
  `username` varchar(80) NOT NULL,
  `password` varchar(80) DEFAULT NULL,
  `first_name` varchar(80) DEFAULT NULL,
  `last_name` varchar(80) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT NULL,
  `scope` varchar(4000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `birthday` timestamp NULL DEFAULT NULL,
  `rating` varchar(255) NOT NULL DEFAULT 'a:0:{}',
  `phone` varchar(255) NOT NULL DEFAULT '000-000-0000',
  `class` enum('Verified Freelancer','User','Moderator') NOT NULL DEFAULT 'User',
  `street` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL DEFAULT 'Rochester',
  `state_province` varchar(255) NOT NULL DEFAULT 'Minnesota',
  `zip` int(11) NOT NULL DEFAULT '55901',
  `country` varchar(255) NOT NULL DEFAULT 'United States of America',
  `profile_picture_path` varchar(255) DEFAULT NULL,
  `profile_description` varchar(255) DEFAULT NULL,
  `default_scope` enum('Private','Friends','Friends of Friends','Public') NOT NULL DEFAULT 'Public',
  `date_joined` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `password`, `first_name`, `last_name`, `birthday`, `rating`, `phone`, `class`, `street`, `city`, `state_province`, `zip`, `country`, `profile_picture_path`, `profile_description`, `default_scope`, `date_joined`) VALUES
(6, 'arama006@umn.edu', 'haron68', 'aab77686697975c48636e339dceb7a06', 'Haron', 'Arama', NULL, 'a:5:{i:0;s:1:\"5\";i:1;s:1:\"1\";i:2;s:1:\"4\";i:3;s:1:\"3\";i:4;s:1:\"5\";}', '000-000-0000', 'Verified Freelancer', '', 'Rochester', 'Minnesota', 55901, 'United States of America', 'a:4:{s:4:\"name\";s:44:\"1679091c5a880faf6fb5e6087eb1b2dc-profile.jpg\";s:4:\"type\";s:10:\"image/jpeg\";s:4:\"size\";i:15483;s:7:\"task_id\";s:1:\"6\";}', 'I am a developer and a verified freelancer.', 'Public', '0000-00-00 00:00:00'),
(7, 'test@test.com', 'test', '098f6bcd4621d373cade4e832627b4f6', 'Test', 'Test', NULL, 'a:0:{}', '000-000-0000', 'User', '', 'Rochester', 'Minnesota', 55901, 'United States of America', NULL, NULL, 'Friends of Friends', '0000-00-00 00:00:00'),
(8, 'test3@test.com', 'test3', '098f6bcd4621d373cade4e832627b4f6', 'Jane', 'Doe', NULL, 'a:0:{}', '000-000-0000', 'User', '', 'Rochester', 'Minnesota', 55901, 'United States of America', NULL, NULL, 'Public', '0000-00-00 00:00:00'),
(9, 'test2@test.com', 'test2', '098f6bcd4621d373cade4e832627b4f6', 'John', 'Doe', NULL, 'a:0:{}', '000-000-0000', 'User', '', 'Rochester', 'Minnesota', 55901, 'United States of America', NULL, 'Hi, I am John Doe and I like apples.', 'Public', '0000-00-00 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `friends_favr_requests`
--
ALTER TABLE `friends_favr_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `marketplace_favr_freelancers`
--
ALTER TABLE `marketplace_favr_freelancers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `marketplace_favr_requests`
--
ALTER TABLE `marketplace_favr_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`access_token`);

--
-- Indexes for table `oauth_authorization_codes`
--
ALTER TABLE `oauth_authorization_codes`
  ADD PRIMARY KEY (`authorization_code`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`refresh_token`);

--
-- Indexes for table `oauth_scopes`
--
ALTER TABLE `oauth_scopes`
  ADD PRIMARY KEY (`scope`);

--
-- Indexes for table `oauth_users`
--
ALTER TABLE `oauth_users`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `friends_favr_requests`
--
ALTER TABLE `friends_favr_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marketplace_favr_freelancers`
--
ALTER TABLE `marketplace_favr_freelancers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `marketplace_favr_requests`
--
ALTER TABLE `marketplace_favr_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
