# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.7.19)
# Database: uit_attendance
# Generation Time: 2017-12-08 06:03:50 +0000
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
  `student_roll_no` varchar(10) NOT NULL DEFAULT '',
  `percent` text NOT NULL,
  PRIMARY KEY (`student_roll_no`),
  CONSTRAINT `attendances_ibfk_1` FOREIGN KEY (`student_roll_no`) REFERENCES `students` (`roll_no`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table classes
# ------------------------------------------------------------

CREATE TABLE `classes` (
  `class_id` varchar(10) NOT NULL,
  `name` varchar(40) NOT NULL,
  `year_id` int(11) NOT NULL,
  PRIMARY KEY (`class_id`),
  KEY `year_id` (`year_id`),
  CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`year_id`) REFERENCES `years` (`year_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



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



# Dump of table period_attendance
# ------------------------------------------------------------

CREATE TABLE `period_attendance` (
  `period_attendance_id` int(11) NOT NULL AUTO_INCREMENT,
  `roll_no` varchar(10) NOT NULL,
  `open_period_id` int(11) NOT NULL,
  `present` tinyint(1) NOT NULL,
  PRIMARY KEY (`period_attendance_id`),
  KEY `open_period_id` (`open_period_id`),
  CONSTRAINT `period_attendance_ibfk_1` FOREIGN KEY (`open_period_id`) REFERENCES `open_periods` (`open_period_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table periods
# ------------------------------------------------------------

CREATE TABLE `periods` (
  `period_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `period_num` int(11) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `day` int(11) NOT NULL,
  `room` varchar(10) DEFAULT '',
  PRIMARY KEY (`period_id`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `periods_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table students
# ------------------------------------------------------------

CREATE TABLE `students` (
  `roll_no` varchar(10) NOT NULL,
  `name` varchar(35) NOT NULL,
  `email` varchar(50) NOT NULL DEFAULT '',
  `class_id` varchar(10) NOT NULL,
  PRIMARY KEY (`roll_no`),
  KEY `class_id` (`class_id`),
  CONSTRAINT `students_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table subject_teacher
# ------------------------------------------------------------

CREATE TABLE `subject_teacher` (
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  PRIMARY KEY (`subject_id`,`teacher_id`),
  KEY `teacher_id` (`teacher_id`),
  CONSTRAINT `subject_teacher_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `subject_teacher_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table subjects
# ------------------------------------------------------------

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_code` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `class_id` varchar(10) NOT NULL,
  PRIMARY KEY (`subject_id`),
  KEY `class_id` (`class_id`),
  CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table teachers
# ------------------------------------------------------------

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(35) NOT NULL,
  `email` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`teacher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table years
# ------------------------------------------------------------

CREATE TABLE `years` (
  `year_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(11) NOT NULL DEFAULT '',
  PRIMARY KEY (`year_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
