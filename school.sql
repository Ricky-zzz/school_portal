-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for school
CREATE DATABASE IF NOT EXISTS `school` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `school`;

-- Dumping structure for table school.audit_trail
CREATE TABLE IF NOT EXISTS `audit_trail` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `module` varchar(255) DEFAULT 'collections',
  `refno` varchar(10) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `action_datetime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table school.audit_trail: ~8 rows (approximately)
DELETE FROM `audit_trail`;
INSERT INTO `audit_trail` (`id`, `user_id`, `module`, `refno`, `action`, `action_datetime`) VALUES
	(2, 2, 'collections', '000001', 'A', '2025-11-08 16:38:22'),
	(3, 2, 'collections', '000002', 'A', '2025-11-08 17:21:43'),
	(4, 2, 'collections', '000001', 'E', '2025-11-09 01:52:37'),
	(5, 2, 'collections', '000001', 'E', '2025-11-10 22:57:01'),
	(6, 2, 'collections', '000001', 'D', '2025-11-10 22:58:02'),
	(7, 2, 'collections', '000003', 'A', '2025-11-12 22:09:09'),
	(8, 2, 'collections', '000002', 'E', '2025-11-12 22:10:29'),
	(9, 2, 'collections', '000003', 'D', '2025-11-12 22:10:53');

-- Dumping structure for table school.collections
CREATE TABLE IF NOT EXISTS `collections` (
  `collection_id` mediumint unsigned NOT NULL AUTO_INCREMENT,
  `or_number` varchar(10) DEFAULT NULL,
  `or_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `student_id` smallint unsigned DEFAULT NULL,
  `semester_id` tinyint unsigned DEFAULT NULL,
  `cash` decimal(8,2) unsigned DEFAULT '0.00',
  `gcash` decimal(8,2) unsigned DEFAULT '0.00',
  `gcash_refno` varchar(20) DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`collection_id`),
  UNIQUE KEY `or_number` (`or_number`),
  KEY `or_date` (`or_date`),
  KEY `student_id` (`student_id`),
  KEY `semester_id` (`semester_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table school.collections: ~1 rows (approximately)
DELETE FROM `collections`;
INSERT INTO `collections` (`collection_id`, `or_number`, `or_date`, `student_id`, `semester_id`, `cash`, `gcash`, `gcash_refno`, `user_id`) VALUES
	(4, '000002', '2025-11-08 17:21:43', 1, 1, 4000.00, 0.00, NULL, 2),
	(7, '000003', '2025-11-08 17:21:43', 1, 1, 4000.00, 0.00, NULL, 2);

-- Dumping structure for table school.courses
CREATE TABLE IF NOT EXISTS `courses` (
  `course_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`course_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table school.courses: ~3 rows (approximately)
DELETE FROM `courses`;
INSERT INTO `courses` (`course_id`, `name`) VALUES
	(1, 'BSCS'),
	(2, 'BSCE'),
	(3, 'BSA');

-- Dumping structure for table school.room
CREATE TABLE IF NOT EXISTS `room` (
  `id` int NOT NULL AUTO_INCREMENT,
  `room_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table school.room: ~8 rows (approximately)
DELETE FROM `room`;
INSERT INTO `room` (`id`, `room_name`) VALUES
	(1, '201'),
	(2, '202'),
	(3, '204'),
	(4, 'gym'),
	(5, 'pool'),
	(6, '407'),
	(7, '603'),
	(8, '101');

-- Dumping structure for table school.semesters
CREATE TABLE IF NOT EXISTS `semesters` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `summer` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'No',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table school.semesters: ~3 rows (approximately)
DELETE FROM `semesters`;
INSERT INTO `semesters` (`id`, `code`, `start_date`, `end_date`, `summer`) VALUES
	(1, '1st-Semester:2025-2026', '2025-10-17', '2026-01-16', 'No'),
	(2, '2nd-Semester:2025-2026', '2025-10-17', '2026-01-16', 'No'),
	(4, 'Summer:2025-2026', '2025-10-17', '2026-01-16', 'Yes');

-- Dumping structure for table school.students
CREATE TABLE IF NOT EXISTS `students` (
  `id` smallint unsigned NOT NULL DEFAULT '0',
  `stud_no` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL DEFAULT 'pass123',
  `gender` enum('Male','Female','Other') NOT NULL,
  `course_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table school.students: ~6 rows (approximately)
DELETE FROM `students`;
INSERT INTO `students` (`id`, `stud_no`, `name`, `pass`, `gender`, `course_id`) VALUES
	(1, '23-00900', 'lina', 'lcc1947', 'Male', 1),
	(2, '24-00900', 'dino', 'pass123', 'Male', 1),
	(3, '27-00900', 'steph', 'pass123', 'Female', 2),
	(4, '22-82828', 'faye', 'pass123', 'Female', 2),
	(5, '67-09876', 'yana', 'pass123', 'Female', 3),
	(6, '43-98788', 'jades', 'pass123', 'Male', 3);

-- Dumping structure for table school.students_subjects
CREATE TABLE IF NOT EXISTS `students_subjects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int DEFAULT NULL,
  `subject_id` int DEFAULT NULL,
  `semester_id` int DEFAULT NULL,
  `mid` decimal(5,2) DEFAULT NULL,
  `fcg` decimal(5,2) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_enrollment` (`student_id`,`subject_id`,`semester_id`),
  KEY `student_id_idx` (`student_id`),
  KEY `subject_id_idx` (`subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table school.students_subjects: ~25 rows (approximately)
DELETE FROM `students_subjects`;
INSERT INTO `students_subjects` (`id`, `student_id`, `subject_id`, `semester_id`, `mid`, `fcg`, `created_at`) VALUES
	(3, 1, 4, 1, 1.25, 1.25, '2025-10-24 15:39:51'),
	(4, 1, 6, 1, 1.25, 1.25, '2025-10-24 15:39:51'),
	(11, 1, 2, 2, 1.25, 1.25, '2025-10-24 15:39:51'),
	(12, 1, 4, 2, 1.50, 1.75, '2025-10-24 15:39:51'),
	(13, 1, 8, 2, 1.00, 1.75, '2025-10-24 15:39:51'),
	(18, 1, 5, 1, 1.75, 1.75, '2025-10-24 15:39:51'),
	(19, 1, 7, 1, 3.00, 1.25, '2025-10-24 15:39:51'),
	(21, 2, 1, 1, NULL, NULL, '2025-11-19 22:05:46'),
	(22, 3, 1, 1, NULL, NULL, '2025-11-19 22:06:25'),
	(23, 4, 1, 1, NULL, NULL, '2025-11-19 22:07:02'),
	(25, 1, 1, 1, NULL, NULL, '2025-11-19 22:07:02'),
	(26, 5, 1, 1, NULL, NULL, '2025-11-19 22:07:02'),
	(27, 6, 1, 1, NULL, NULL, '2025-11-19 22:07:02'),
	(28, 6, 10, 1, NULL, NULL, '2025-11-19 22:07:02'),
	(29, 6, 10, 2, NULL, NULL, '2025-11-19 22:07:02'),
	(30, 5, 10, 2, NULL, NULL, '2025-11-19 22:07:02'),
	(31, 5, 10, 1, NULL, NULL, '2025-11-19 22:07:02'),
	(32, 4, 10, 1, NULL, NULL, '2025-11-19 22:07:02'),
	(33, 4, 10, 2, NULL, NULL, '2025-11-19 22:07:02'),
	(34, 3, 10, 2, NULL, NULL, '2025-11-19 22:07:02'),
	(35, 3, 10, 1, NULL, NULL, '2025-11-19 22:07:02'),
	(36, 2, 10, 1, NULL, NULL, '2025-11-19 22:07:02'),
	(37, 2, 10, 2, NULL, NULL, '2025-11-19 22:07:02'),
	(38, 1, 10, 2, NULL, NULL, '2025-11-19 22:07:02'),
	(39, 1, 10, 1, NULL, NULL, '2025-11-19 22:07:02');

-- Dumping structure for table school.subjects
CREATE TABLE IF NOT EXISTS `subjects` (
  `subject_id` int NOT NULL AUTO_INCREMENT,
  `subject_code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `days` varchar(50) NOT NULL,
  `start_time` time NOT NULL,
  `teacher_id` int NOT NULL,
  `end_time` time NOT NULL,
  `room_id` int NOT NULL,
  `price_unit` float DEFAULT NULL,
  `unit` int DEFAULT NULL,
  PRIMARY KEY (`subject_id`),
  UNIQUE KEY `subject_code` (`subject_code`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table school.subjects: ~10 rows (approximately)
DELETE FROM `subjects`;
INSERT INTO `subjects` (`subject_id`, `subject_code`, `name`, `days`, `start_time`, `teacher_id`, `end_time`, `room_id`, `price_unit`, `unit`) VALUES
	(1, 'CS3AMATH', 'Mathematics', 'MWF', '08:00:00', 1, '09:00:00', 1, 5000, 2),
	(2, 'CS3BMATH', 'Mathematics', 'TTh', '09:00:00', 2, '10:00:00', 2, 6000, 3),
	(3, 'CS3AENG', 'English', 'MWF', '10:00:00', 3, '11:00:00', 3, 7000, 2),
	(4, 'CS3APHYS', 'Physics', 'MWF', '13:00:00', 4, '14:00:00', 4, 7000, 3),
	(5, 'CS3BCOMP', 'Computer Science', 'TTh', '08:00:00', 5, '09:30:00', 5, 6000, 2),
	(6, 'CS3AHIST', 'History', 'MWF', '11:00:00', 6, '12:00:00', 6, 6000, 3),
	(7, 'CS3AGEO', 'Geography', 'TTh', '14:00:00', 6, '15:30:00', 7, 6000, 2),
	(8, 'CS3ALIT', 'Literature', 'MWF', '15:00:00', 5, '16:00:00', 7, 6000, 3),
	(9, 'CS3APHIL', 'Philosophy', 'MWF', '16:00:00', 2, '17:00:00', 8, 6000, 3),
	(10, 'CS3AART', 'Art', 'Sat', '09:00:00', 1, '12:00:00', 8, 6000, 2);

-- Dumping structure for table school.teacher
CREATE TABLE IF NOT EXISTS `teacher` (
  `id` int NOT NULL AUTO_INCREMENT,
  `teacher_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `teacher_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(50) DEFAULT 'lcc1947',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table school.teacher: ~7 rows (approximately)
DELETE FROM `teacher`;
INSERT INTO `teacher` (`id`, `teacher_name`, `teacher_code`, `password`) VALUES
	(1, 'John Smith', 'JS-001', 'lcc1947'),
	(2, 'Jade Smith', 'JS-002', 'lcc1947'),
	(3, 'Allain Sarmiento', 'AS-003', 'lcc1947'),
	(4, 'Arthur Doyle', 'AD-004', 'lcc1947'),
	(5, 'Snape Severus', 'SS-005', 'lcc1947'),
	(6, ' Bridgett Mychelle', 'BM-006', 'lcc1947'),
	(8, 'Joseph Levi', 'JL-007', 'lcc1947');

-- Dumping structure for table school.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'admin123',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table school.users: ~2 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `password`) VALUES
	(1, 'admin', 'admin123'),
	(2, 'cashier', 'admin123');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
