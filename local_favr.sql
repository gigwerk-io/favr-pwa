-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 17, 2018 at 10:10 AM
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
  `task_date` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `task_location` varchar(255) NOT NULL,
  `task_contact` varchar(255) DEFAULT NULL,
  `task_time_to_accomplish` time DEFAULT NULL,
  `arrival_time` timestamp NULL DEFAULT NULL,
  `completion_time` timestamp NULL DEFAULT NULL,
  `favr_price` int(11) NOT NULL DEFAULT '0',
  `approved` tinyint(1) NOT NULL DEFAULT '0',
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
  `task_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
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
  `payment_id` int(11) DEFAULT NULL,
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
  `date_joined` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `display_ratings` tinyint(1) NOT NULL DEFAULT '1',
  `display_receipts` tinyint(1) NOT NULL DEFAULT '1',
  `display_description` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `friends_favr_requests`
--
ALTER TABLE `friends_favr_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marketplace_favr_freelancers`
--
ALTER TABLE `marketplace_favr_freelancers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marketplace_favr_requests`
--
ALTER TABLE `marketplace_favr_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
