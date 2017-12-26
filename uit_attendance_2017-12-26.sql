# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.7.19)
# Database: uit_attendance
# Generation Time: 2017-12-26 08:27:32 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table attendances
# ------------------------------------------------------------

CREATE TABLE `attendances` (
  `student_roll_no` int(11) NOT NULL,
  `attendance_json` text NOT NULL,
  PRIMARY KEY (`student_roll_no`),
  CONSTRAINT `attendances_ibfk_1` FOREIGN KEY (`student_roll_no`) REFERENCES `students` (`roll_no`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table classes
# ------------------------------------------------------------

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT,
  `short_form` varchar(10) NOT NULL,
  `name` varchar(40) NOT NULL,
  `year_id` int(11) NOT NULL,
  PRIMARY KEY (`class_id`),
  KEY `year_id` (`year_id`),
  CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`year_id`) REFERENCES `years` (`year_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `classes` WRITE;
/*!40000 ALTER TABLE `classes` DISABLE KEYS */;

INSERT INTO `classes` (`class_id`, `short_form`, `name`, `year_id`)
VALUES
	(1,'4SE','Fourth Year Software Engineering',4);

/*!40000 ALTER TABLE `classes` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table faculties
# ------------------------------------------------------------

CREATE TABLE `faculties` (
  `faculty_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`faculty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table internal_logs
# ------------------------------------------------------------

CREATE TABLE `internal_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `action` varchar(10) DEFAULT NULL,
  `message` text,
  `new_value` text,
  `old_value` text,
  `created_at` datetime DEFAULT NULL,
  `by_user` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `internal_logs_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table migrations
# ------------------------------------------------------------

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;

INSERT INTO `migrations` (`id`, `migration`, `batch`)
VALUES
	(1,'2014_10_12_000000_create_users_table',1),
	(2,'2014_10_12_100000_create_password_resets_table',1),
	(3,'2017_12_13_111333_add_google_user_id_to_users',1);

/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table open_periods
# ------------------------------------------------------------

CREATE TABLE `open_periods` (
  `open_period_id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `period_id` int(11) NOT NULL,
  PRIMARY KEY (`open_period_id`),
  KEY `period_id` (`period_id`),
  CONSTRAINT `open_periods_ibfk_1` FOREIGN KEY (`period_id`) REFERENCES `periods` (`period_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `open_periods` WRITE;
/*!40000 ALTER TABLE `open_periods` DISABLE KEYS */;

INSERT INTO `open_periods` (`open_period_id`, `date`, `period_id`)
VALUES
	(3,'2017-12-25',1),
	(4,'2017-12-25',2);

/*!40000 ALTER TABLE `open_periods` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table password_resets
# ------------------------------------------------------------

CREATE TABLE `password_resets` (
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table period_attendance
# ------------------------------------------------------------

CREATE TABLE `period_attendance` (
  `period_attendance_id` int(11) NOT NULL AUTO_INCREMENT,
  `roll_no` int(11) NOT NULL,
  `open_period_id` int(11) NOT NULL,
  `present` tinyint(1) NOT NULL,
  PRIMARY KEY (`period_attendance_id`),
  KEY `open_period_id` (`open_period_id`),
  KEY `roll_no` (`roll_no`),
  CONSTRAINT `period_attendance_ibfk_1` FOREIGN KEY (`open_period_id`) REFERENCES `open_periods` (`open_period_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `period_attendance_ibfk_2` FOREIGN KEY (`roll_no`) REFERENCES `students` (`roll_no`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `period_attendance` WRITE;
/*!40000 ALTER TABLE `period_attendance` DISABLE KEYS */;

INSERT INTO `period_attendance` (`period_attendance_id`, `roll_no`, `open_period_id`, `present`)
VALUES
	(4,7,3,1),
	(5,8,3,0),
	(6,7,4,0),
	(7,8,4,1);

/*!40000 ALTER TABLE `period_attendance` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table periods
# ------------------------------------------------------------

CREATE TABLE `periods` (
  `period_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_class_id` int(11) NOT NULL,
  `period_num` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `room` varchar(10) DEFAULT '',
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  PRIMARY KEY (`period_id`),
  KEY `subject_id` (`subject_class_id`),
  CONSTRAINT `periods_ibfk_1` FOREIGN KEY (`subject_class_id`) REFERENCES `subject_class` (`subject_class_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `periods` WRITE;
/*!40000 ALTER TABLE `periods` DISABLE KEYS */;

INSERT INTO `periods` (`period_id`, `subject_class_id`, `period_num`, `day`, `room`, `start_time`, `end_time`)
VALUES
	(1,1,1,1,'205','08:30:00','09:25:00'),
	(2,1,2,1,'205','09:30:00','10:25:00');

/*!40000 ALTER TABLE `periods` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table students
# ------------------------------------------------------------

CREATE TABLE `students` (
  `roll_no` int(11) NOT NULL,
  `name` varchar(35) NOT NULL,
  `email` varchar(50) NOT NULL DEFAULT '',
  `class_id` int(11) NOT NULL,
  PRIMARY KEY (`roll_no`),
  KEY `class_id` (`class_id`),
  CONSTRAINT `students_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;

INSERT INTO `students` (`roll_no`, `name`, `email`, `class_id`)
VALUES
	(7,'Toe Pyae Sone Oo','toepyaesoneoo@uit.edu.mm',1),
	(8,'Ye Min Htut','yeminhtut@uit.edu.mm',1);

/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table subject_class
# ------------------------------------------------------------

CREATE TABLE `subject_class` (
  `subject_class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  PRIMARY KEY (`subject_class_id`),
  KEY `subject_id` (`subject_id`),
  KEY `class_id` (`class_id`),
  CONSTRAINT `subject_class_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `subject_class_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `subject_class` WRITE;
/*!40000 ALTER TABLE `subject_class` DISABLE KEYS */;

INSERT INTO `subject_class` (`subject_class_id`, `subject_id`, `class_id`)
VALUES
	(1,1,1),
	(2,2,1);

/*!40000 ALTER TABLE `subject_class` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table subject_class_teacher
# ------------------------------------------------------------

CREATE TABLE `subject_class_teacher` (
  `subject_class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  PRIMARY KEY (`subject_class_id`,`teacher_id`),
  KEY `teacher_id` (`teacher_id`),
  CONSTRAINT `subject_class_teacher_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `subject_class_teacher_ibfk_3` FOREIGN KEY (`subject_class_id`) REFERENCES `subject_class` (`subject_class_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table subjects
# ------------------------------------------------------------

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_code` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;

INSERT INTO `subjects` (`subject_id`, `subject_code`, `name`)
VALUES
	(1,'401','Computer Vision'),
	(2,'402','Game Theory');

/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table teachers
# ------------------------------------------------------------

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(35) NOT NULL,
  `email` varchar(50) NOT NULL DEFAULT '',
  `faculty_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`teacher_id`),
  KEY `faculty_id` (`faculty_id`),
  CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table users
# ------------------------------------------------------------

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `google_user_id` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table years
# ------------------------------------------------------------

CREATE TABLE `years` (
  `year_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(11) NOT NULL DEFAULT '',
  PRIMARY KEY (`year_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `years` WRITE;
/*!40000 ALTER TABLE `years` DISABLE KEYS */;

INSERT INTO `years` (`year_id`, `name`)
VALUES
	(4,'Fourth Year');

/*!40000 ALTER TABLE `years` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
