-- MySQL dump 10.13  Distrib 8.0.34, for Win64 (x86_64)
--
-- Host: localhost    Database: attendance_control
-- ------------------------------------------------------
-- Server version	8.1.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `department`
--
CREATE DATABASE IF NOT EXISTS attendance_control;
CREATE USER IF NOT EXISTS 'user'@'%' IDENTIFIED BY 'password';
GRANT SELECT, UPDATE, DELETE, INSERT ON attendance_control.* TO 'user'@'%';
FLUSH PRIVILEGES;

USE attendance_control;

DROP TABLE IF EXISTS `department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `department` (
  `id_department` int NOT NULL AUTO_INCREMENT,
  `department_name` varchar(60) NOT NULL,
  `phone_number` int NOT NULL,
  PRIMARY KEY (`id_department`),
  UNIQUE KEY `department_name_UNIQUE` (`department_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department`
--

LOCK TABLES `department` WRITE;
/*!40000 ALTER TABLE `department` DISABLE KEYS */;
/*!40000 ALTER TABLE `department` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group`
--

DROP TABLE IF EXISTS `group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `group` (
  `id_group` int NOT NULL AUTO_INCREMENT,
  `group_name` varchar(20) NOT NULL,
  `id_department` int NOT NULL,
  PRIMARY KEY (`id_group`),
  UNIQUE KEY `group_name_UNIQUE` (`group_name`),
  KEY `id_department_idx` (`id_department`),
  CONSTRAINT `id_department1` FOREIGN KEY (`id_department`) REFERENCES `department` (`id_department`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group`
--

LOCK TABLES `group` WRITE;
/*!40000 ALTER TABLE `group` DISABLE KEYS */;
/*!40000 ALTER TABLE `group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lesson`
--

DROP TABLE IF EXISTS `lesson`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lesson` (
  `id_lesson` int NOT NULL AUTO_INCREMENT,
  `attendance_status` tinyint(1) DEFAULT NULL,
  `mark` int DEFAULT NULL,
  `id_student` int NOT NULL,
  `id_group` int NOT NULL,
  `id_teacher` int NOT NULL,
  `id_subject` int NOT NULL,
  PRIMARY KEY (`id_lesson`),
  KEY `id_teacher_idx` (`id_teacher`),
  KEY `id_subject_idx` (`id_subject`),
  CONSTRAINT `id_group1` FOREIGN KEY (`id_group`) REFERENCES `group` (`id_group`),
  CONSTRAINT `id_subject` FOREIGN KEY (`id_subject`) REFERENCES `subject` (`id_subject`),
  CONSTRAINT `id_teacher` FOREIGN KEY (`id_teacher`) REFERENCES `teacher` (`id_teacher`),
  CONSTRAINT `id_student` FOREIGN KEY (`id_student`) REFERENCES `student` (`id_student`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lesson`
--

LOCK TABLES `lesson` WRITE;
/*!40000 ALTER TABLE `lesson` DISABLE KEYS */;
/*!40000 ALTER TABLE `lesson` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student` (
  `id_student` int NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `surname` varchar(30) NOT NULL,
  `course` int NOT NULL,
  `id_group` int NOT NULL,
  `email` varchar(45) NOT NULL,
  PRIMARY KEY (`id_student`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  KEY `id_group_idx` (`id_group`),
  CONSTRAINT `id_group` FOREIGN KEY (`id_group`) REFERENCES `group` (`id_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student`
--

LOCK TABLES `student` WRITE;
/*!40000 ALTER TABLE `student` DISABLE KEYS */;
/*!40000 ALTER TABLE `student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subject`
--

DROP TABLE IF EXISTS `subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subject` (
  `id_subject` int NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `hours` int NOT NULL,
  `id_department` int NOT NULL,
  PRIMARY KEY (`id_subject`),
  UNIQUE KEY `title_UNIQUE` (`title`),
  KEY `id_department_idx` (`id_department`),
  CONSTRAINT `id_department` FOREIGN KEY (`id_department`) REFERENCES `department` (`id_department`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subject`
--

LOCK TABLES `subject` WRITE;
/*!40000 ALTER TABLE `subject` DISABLE KEYS */;
/*!40000 ALTER TABLE `subject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teacher`
--

DROP TABLE IF EXISTS `teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teacher` (
  `id_teacher` int NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `surname` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `id_department` int NOT NULL,
  PRIMARY KEY (`id_teacher`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  KEY `id_department2_idx` (`id_department`),
  CONSTRAINT `id_department2` FOREIGN KEY (`id_department`) REFERENCES `department` (`id_department`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teacher`
--

LOCK TABLES `teacher` WRITE;
/*!40000 ALTER TABLE `teacher` DISABLE KEYS */;
/*!40000 ALTER TABLE `teacher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `token`
--

DROP TABLE IF EXISTS `token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `token` (
  `token_body` varchar(500) NOT NULL,
  `user_id` int NOT NULL,
  `time_expires` datetime NOT NULL,
  PRIMARY KEY (`token_body`),
  KEY `id_user_idx` (`user_id`),
  CONSTRAINT `id_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `token`
--

LOCK TABLES `token` WRITE;
/*!40000 ALTER TABLE `token` DISABLE KEYS */;
/*!40000 ALTER TABLE `token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `login` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `role` varchar(30) NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-12-03  0:46:00

INSERT INTO `department` (`id_department`, `department_name`, `phone_number`) VALUES (1, 'it_department', '121231234');

INSERT INTO `group` (`id_group`, `group_name`, `id_department`) VALUES (NULL, 'Kalabashki', '1');

INSERT INTO `teacher` (`id_teacher`, `name`, `surname`, `email`, `id_department`) VALUES (NULL, 'Антон', 'Антон', 'anton@gmail.com', '1');

INSERT INTO `subject` (`id_subject`, `title`, `hours`, `id_department`) VALUES (NULL, 'programming', '180','1');

INSERT INTO `student` (`id_student`, `name`, `surname`, `course`, `id_group`, `email`) VALUES (NULL, 'Вася', 'Вася', '3', '1', 'vasya@gmail.com');

INSERT INTO `student` (`id_student`, `name`, `surname`, `course`, `id_group`, `email`) VALUES (NULL, 'Иван', 'Иван', '3', '1', 'ivan@gmail.com');

INSERT INTO `lesson` (`id_lesson`, `attendance_status`, `id_student`, `id_group`, `id_teacher`, `id_subject`) VALUES (NULL, '1', '1', '1', '1', '1');

INSERT INTO `lesson` (`id_lesson`, `attendance_status`, `id_student`, `id_group`, `id_teacher`, `id_subject`) VALUES (NULL, '1', '2', '1', '1', '1');

INSERT INTO `teacher` (`id_teacher`, `name`, `surname`, `email`, `id_department`) VALUES (NULL, 'Максим', 'Максим', 'maxim@gmail.com', '1');

INSERT INTO `lesson` (`id_lesson`, `attendance_status`, `id_student`, `id_group`, `id_teacher`, `id_subject`) VALUES (NULL, '1', '1', '1', '2', '1');

INSERT INTO `lesson` (`id_lesson`, `attendance_status`, `id_student`, `id_group`, `id_teacher`, `id_subject`) VALUES (NULL, '1', '2', '1', '2', '1');

INSERT INTO `lesson` (`id_lesson`, `attendance_status`, `id_student`, `id_group`, `id_teacher`, `id_subject`) VALUES (NULL, '0', '1', '1', '1', '1');

INSERT INTO `lesson` (`id_lesson`, `attendance_status`, `id_student`, `id_group`, `id_teacher`, `id_subject`) VALUES (NULL, '0', '2', '1', '1', '1');

INSERT INTO `lesson` (`id_lesson`, `attendance_status`, `mark`, `id_student`, `id_group`, `id_teacher`, `id_subject`) VALUES (NULL, '1', '3', '1', '1', '1', '1');

INSERT INTO `lesson` (`id_lesson`, `attendance_status`, `mark`, `id_student`, `id_group`, `id_teacher`, `id_subject`) VALUES (NULL, '1', '3', '2', '1', '1', '1');

INSERT INTO `lesson` (`id_lesson`, `attendance_status`, `mark`, `id_student`, `id_group`, `id_teacher`, `id_subject`) VALUES (NULL, '1', '5', '1', '1', '2', '1');

INSERT INTO `lesson` (`id_lesson`, `attendance_status`, `mark`, `id_student`, `id_group`, `id_teacher`, `id_subject`) VALUES (NULL, '1', '5', '2', '1', '2', '1');
