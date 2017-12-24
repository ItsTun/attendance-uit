-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 24, 2017 at 07:16 AM
-- Server version: 10.1.10-MariaDB
-- PHP Version: 7.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uit_attendance`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `student_roll_no` varchar(10) NOT NULL DEFAULT '',
  `percent` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `attendances`:
--   `student_roll_no`
--       `students` -> `roll_no`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` varchar(10) NOT NULL,
  `name` varchar(40) NOT NULL,
  `year_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `classes`:
--   `year_id`
--       `years` -> `year_id`
--

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `name`, `year_id`) VALUES
('1', 'First Year - Section A', 1),
('2', 'Second Year - Section A', 1);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONS FOR TABLE `migrations`:
--

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(4, '2017_12_13_111333_add_google_user_id_to_users', 2);

-- --------------------------------------------------------

--
-- Table structure for table `open_periods`
--

CREATE TABLE `open_periods` (
  `open_period_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `period_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `open_periods`:
--   `period_id`
--       `periods` -> `period_id`
--

--
-- Dumping data for table `open_periods`
--

INSERT INTO `open_periods` (`open_period_id`, `date`, `period_id`) VALUES
(15, '2017-12-25', 5),
(16, '2017-12-25', 6);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONS FOR TABLE `password_resets`:
--

-- --------------------------------------------------------

--
-- Table structure for table `periods`
--

CREATE TABLE `periods` (
  `period_id` int(11) NOT NULL,
  `subject_class_id` int(11) NOT NULL,
  `period_num` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `room` varchar(10) DEFAULT '',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `periods`:
--   `subject_class_id`
--       `subject_class` -> `subject_class_id`
--   `subject_class_id`
--       `subject_class` -> `subject_class_id`
--

--
-- Dumping data for table `periods`
--

INSERT INTO `periods` (`period_id`, `subject_class_id`, `period_num`, `day`, `room`, `start_time`, `end_time`) VALUES
(5, 1, 1, 1, '201', '08:30:00', '09:20:00'),
(6, 1, 2, 1, '201', '09:30:00', '10:20:00'),
(7, 2, 3, 1, '302', '10:30:00', '11:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `period_attendance`
--

CREATE TABLE `period_attendance` (
  `period_attendance_id` int(11) NOT NULL,
  `roll_no` varchar(10) NOT NULL,
  `open_period_id` int(11) NOT NULL,
  `present` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `period_attendance`:
--   `open_period_id`
--       `open_periods` -> `open_period_id`
--

--
-- Dumping data for table `period_attendance`
--

INSERT INTO `period_attendance` (`period_attendance_id`, `roll_no`, `open_period_id`, `present`) VALUES
(19, '19', 15, 0),
(20, '20', 15, 1),
(21, '19', 16, 1),
(22, '20', 16, 1);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `roll_no` varchar(10) NOT NULL,
  `name` varchar(35) NOT NULL,
  `email` varchar(50) NOT NULL DEFAULT '',
  `class_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `students`:
--   `class_id`
--       `classes` -> `class_id`
--

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`roll_no`, `name`, `email`, `class_id`) VALUES
('19', 'Yar Zar Myo Min', 'yarzarmyomin@gmail.com', '1'),
('20', 'Ye Min Htut', 'yeminhtut@uit.edu.mm', '1');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_code` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `class_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `subjects`:
--   `class_id`
--       `classes` -> `class_id`
--

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_code`, `name`, `class_id`) VALUES
(1, '101', 'Computer Fundamental', '1'),
(2, '201', 'Algorithms', '2'),
(3, '202', 'Maths', '1');

-- --------------------------------------------------------

--
-- Table structure for table `subject_class`
--

CREATE TABLE `subject_class` (
  `subject_class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `subject_class`:
--   `class_id`
--       `classes` -> `class_id`
--   `subject_id`
--       `subjects` -> `subject_id`
--

--
-- Dumping data for table `subject_class`
--

INSERT INTO `subject_class` (`subject_class_id`, `subject_id`, `class_id`) VALUES
(1, 1, 1),
(2, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `subject_teacher`
--

CREATE TABLE `subject_teacher` (
  `subject_class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `subject_teacher`:
--   `subject_class_id`
--       `subject_class` -> `subject_class_id`
--   `teacher_id`
--       `teachers` -> `teacher_id`
--   `subject_class_id`
--       `subject_class` -> `subject_class_id`
--   `teacher_id`
--       `teachers` -> `teacher_id`
--

--
-- Dumping data for table `subject_teacher`
--

INSERT INTO `subject_teacher` (`subject_class_id`, `teacher_id`) VALUES
(1, 1),
(2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `name` varchar(35) NOT NULL,
  `email` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `teachers`:
--

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `name`, `email`) VALUES
(1, 'Ye Min Htut', 'yeminhtut@uit.edu.mm'),
(2, 'Ye Htut', 'yehtut@uit.edu.mm');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `google_user_id` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONS FOR TABLE `users`:
--

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `remember_token`, `created_at`, `updated_at`, `google_user_id`) VALUES
(1, 'Ye Min Htut', 'yeminhtut@uit.edu.mm', NULL, 'teacher', '6Sc73dCEmaKzxorRLBBp9k9RSu4F2kMCyrz9Jx6wSVV8sN3T3n7F7ZKlck9p', '2017-12-13 04:55:45', '2017-12-13 04:55:45', '118438710954281810473'),
(2, 'admin', 'admin@gmail.com', '$2y$10$Sbqojp0bllniwiH88438W.cWT3BgY/qg2yoR/HACaesY6fMUDEFaK', 'admin', 'XNyl3D5c6gaAq4wt3F0zckRj657KFgIo0llCK5Zuh1MS7RdQIyDWbeQwVELF', '2017-12-17 08:16:49', '2017-12-17 08:16:49', '');

-- --------------------------------------------------------

--
-- Table structure for table `years`
--

CREATE TABLE `years` (
  `year_id` int(11) NOT NULL,
  `name` varchar(11) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `years`:
--

--
-- Dumping data for table `years`
--

INSERT INTO `years` (`year_id`, `name`) VALUES
(1, 'First Year'),
(2, 'Second Year');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`student_roll_no`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`),
  ADD KEY `year_id` (`year_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `open_periods`
--
ALTER TABLE `open_periods`
  ADD PRIMARY KEY (`open_period_id`),
  ADD KEY `period_id` (`period_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `periods`
--
ALTER TABLE `periods`
  ADD PRIMARY KEY (`period_id`),
  ADD KEY `subject_id` (`subject_class_id`);

--
-- Indexes for table `period_attendance`
--
ALTER TABLE `period_attendance`
  ADD PRIMARY KEY (`period_attendance_id`),
  ADD KEY `open_period_id` (`open_period_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`roll_no`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `subject_class`
--
ALTER TABLE `subject_class`
  ADD PRIMARY KEY (`subject_class_id`);

--
-- Indexes for table `subject_teacher`
--
ALTER TABLE `subject_teacher`
  ADD PRIMARY KEY (`subject_class_id`,`teacher_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `years`
--
ALTER TABLE `years`
  ADD PRIMARY KEY (`year_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `open_periods`
--
ALTER TABLE `open_periods`
  MODIFY `open_period_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `periods`
--
ALTER TABLE `periods`
  MODIFY `period_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `period_attendance`
--
ALTER TABLE `period_attendance`
  MODIFY `period_attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `subject_class`
--
ALTER TABLE `subject_class`
  MODIFY `subject_class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `years`
--
ALTER TABLE `years`
  MODIFY `year_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_ibfk_1` FOREIGN KEY (`student_roll_no`) REFERENCES `students` (`roll_no`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`year_id`) REFERENCES `years` (`year_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `open_periods`
--
ALTER TABLE `open_periods`
  ADD CONSTRAINT `open_periods_ibfk_1` FOREIGN KEY (`period_id`) REFERENCES `periods` (`period_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `periods`
--
ALTER TABLE `periods`
  ADD CONSTRAINT `periods_ibfk_1` FOREIGN KEY (`subject_class_id`) REFERENCES `subject_class` (`subject_class_id`);

--
-- Constraints for table `period_attendance`
--
ALTER TABLE `period_attendance`
  ADD CONSTRAINT `period_attendance_ibfk_1` FOREIGN KEY (`open_period_id`) REFERENCES `open_periods` (`open_period_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subject_teacher`
--
ALTER TABLE `subject_teacher`
  ADD CONSTRAINT `subject_teacher_ibfk_1` FOREIGN KEY (`subject_class_id`) REFERENCES `subject_class` (`subject_class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject_teacher_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
