-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 28, 2025 at 04:56 AM
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
-- Database: `ftwdatabase`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

CREATE TABLE `tbladmin` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `mobile` varchar(45) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`id`, `name`, `email`, `mobile`, `password`, `create_date`) VALUES
(1, 'admin1', 'admin@gmail.com', '1234567890', '12bce374e7be15142e8172f668da00d8', '2025-08-05 04:11:57');

-- --------------------------------------------------------

--
-- Table structure for table `tblftwworker`
--

CREATE TABLE `tblftwworker` (
  `id` int(11) NOT NULL,
  `StaffICPassport` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `contactno` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `BOD` date NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblftwworker`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblftw_assessment`
--

CREATE TABLE `tblftw_assessment` (
  `id` int(11) NOT NULL,
  `workerid` int(11) NOT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblftw_assessment`
--


-- --------------------------------------------------------

--
-- Table structure for table `tblftw_assessment_job`
--

CREATE TABLE `tblftw_assessment_job` (
  `id` int(11) NOT NULL,
  `assessment_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `place_id` int(11) NOT NULL,
  `exam_date` date NOT NULL,
  `status_ftw` enum('Fit','Unfit','Fit with Restriction') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblftw_assessment_job`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbljobspecific`
--

CREATE TABLE `tbljobspecific` (
  `id` int(11) NOT NULL,
  `jobspecific` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbljobspecific`
--

INSERT INTO `tbljobspecific` (`id`, `jobspecific`) VALUES
(1, 'Breathing Apparatus'),
(2, 'Fire Fighter and Emergency Response Personnel'),
(3, 'Working at Height'),
(4, 'Crane and/or Forklift Operator'),
(5, 'Confined Space Worker'),
(6, 'Contractor Plant & Field'),
(7, 'Contractor Non-Plant & Non-Field');

-- --------------------------------------------------------

--
-- Table structure for table `tblpreplacement`
--

CREATE TABLE `tblpreplacement` (
  `id` int(11) NOT NULL,
  `preplacement` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblpreplacement`
--

INSERT INTO `tblpreplacement` (`id`, `preplacement`) VALUES
(1, 'Twin Towers Medical Clinic KLCC'),
(2, 'Klinik Peringgit Point'),
(3, 'Poliklinik Al Syifa');

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

CREATE TABLE `tbluser` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `mobile` varchar(45) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `password` varchar(450) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT current_timestamp(),
  `placeid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`id`, `name`, `email`, `mobile`, `address`, `password`, `create_date`, `placeid`) VALUES
(2, 'Sofia Sazali', 'sofia@gmail.com', '0111909981', 'Peringgit', '12bce374e7be15142e8172f668da00d8', '2025-08-11 01:05:18', 1),
(5, 'Badrul  Muhymin', 'badrul@gmail.com', '0197865544', 'Ayer Keroh, Melaka', '81dc9bdb52d04dc20036dbd8313ed055', '2025-08-28 01:12:45', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblftwworker`
--
ALTER TABLE `tblftwworker`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblftw_assessment`
--
ALTER TABLE `tblftw_assessment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblftw_assessment_job`
--
ALTER TABLE `tblftw_assessment_job`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbljobspecific`
--
ALTER TABLE `tbljobspecific`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblpreplacement`
--
ALTER TABLE `tblpreplacement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblftwworker`
--
ALTER TABLE `tblftwworker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tblftw_assessment`
--
ALTER TABLE `tblftw_assessment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `tblftw_assessment_job`
--
ALTER TABLE `tblftw_assessment_job`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `tbljobspecific`
--
ALTER TABLE `tbljobspecific`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tblpreplacement`
--
ALTER TABLE `tblpreplacement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
