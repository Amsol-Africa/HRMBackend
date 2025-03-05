/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.4.5-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: amsol
-- ------------------------------------------------------
-- Server version	11.4.5-MariaDB-1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `academic_qualifications`
--

DROP TABLE IF EXISTS `academic_qualifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_qualifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `institution_name` varchar(255) NOT NULL,
  `certification_obtained` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `academic_qualifications_employee_id_foreign` (`employee_id`),
  CONSTRAINT `academic_qualifications_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_qualifications`
--

LOCK TABLES `academic_qualifications` WRITE;
/*!40000 ALTER TABLE `academic_qualifications` DISABLE KEYS */;
INSERT INTO `academic_qualifications` VALUES
(1,1,'2014-02-19','2022-02-15','Institution Of Tech','Bsc Computer Science','2025-02-04 23:11:27','2025-02-04 23:11:27'),
(2,1,'2010-02-19','2014-02-19','Highest of Schools','KCSE','2025-02-04 23:11:27','2025-02-04 23:11:27'),
(3,2,'2010-01-01','2014-12-31','XYZ University','MBA','2025-02-04 23:22:26','2025-02-04 23:22:26'),
(4,2,'2015-05-01','2020-12-31','ABC Institute','HR Certification','2025-02-04 23:22:26','2025-02-04 23:22:26'),
(5,3,'2010-01-01','2014-12-31','ABC University','Accounting Degree','2025-02-04 23:30:46','2025-02-04 23:30:46');
/*!40000 ALTER TABLE `academic_qualifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `access_requests`
--

DROP TABLE IF EXISTS `access_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `access_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `requester_id` bigint(20) unsigned NOT NULL,
  `business_id` bigint(20) unsigned NOT NULL,
  `email` varchar(255) NOT NULL,
  `registration_token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_req_bus_token` (`requester_id`,`business_id`,`registration_token`),
  KEY `access_requests_business_id_foreign` (`business_id`),
  CONSTRAINT `access_requests_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `access_requests_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `access_requests`
--

LOCK TABLES `access_requests` WRITE;
/*!40000 ALTER TABLE `access_requests` DISABLE KEYS */;
INSERT INTO `access_requests` VALUES
(1,1,1,'info@anzar.co.ke','64830630e0ceae15bfdcc95bbad039b9df21cb3af5c7d2f4d9c889d96ec28b0d','2025-02-27 10:16:03','2025-02-27 10:16:03');
/*!40000 ALTER TABLE `access_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `advances`
--

DROP TABLE IF EXISTS `advances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `advances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `advances_employee_id_foreign` (`employee_id`),
  CONSTRAINT `advances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `advances`
--

LOCK TABLES `advances` WRITE;
/*!40000 ALTER TABLE `advances` DISABLE KEYS */;
INSERT INTO `advances` VALUES
(1,1,10000.00,'2025-03-07','To be cut from salary','2025-02-07 02:21:15','2025-02-07 02:28:24'),
(2,3,11040.00,'2025-02-07','To be cut from salary','2025-02-07 03:53:10','2025-02-07 03:53:24');
/*!40000 ALTER TABLE `advances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `allowances`
--

DROP TABLE IF EXISTS `allowances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `allowances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `is_taxable` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `allowances_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `allowances`
--

LOCK TABLES `allowances` WRITE;
/*!40000 ALTER TABLE `allowances` DISABLE KEYS */;
/*!40000 ALTER TABLE `allowances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `applicant_skills`
--

DROP TABLE IF EXISTS `applicant_skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `applicant_skills` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `skill_id` bigint(20) unsigned NOT NULL,
  `skill_level` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `applicant_skills_applicant_id_foreign` (`applicant_id`),
  KEY `applicant_skills_skill_id_foreign` (`skill_id`),
  CONSTRAINT `applicant_skills_applicant_id_foreign` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `applicant_skills_skill_id_foreign` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applicant_skills`
--

LOCK TABLES `applicant_skills` WRITE;
/*!40000 ALTER TABLE `applicant_skills` DISABLE KEYS */;
/*!40000 ALTER TABLE `applicant_skills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `applicants`
--

DROP TABLE IF EXISTS `applicants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `applicants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip_code` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `linkedin_profile` varchar(255) DEFAULT NULL,
  `portfolio_url` varchar(255) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `current_job_title` varchar(255) DEFAULT NULL,
  `current_company` varchar(255) DEFAULT NULL,
  `experience_level` varchar(255) DEFAULT NULL,
  `education_level` varchar(255) DEFAULT NULL,
  `desired_salary` varchar(255) DEFAULT NULL,
  `job_preferences` varchar(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `applicants_user_id_foreign` (`user_id`),
  KEY `applicants_created_by_foreign` (`created_by`),
  CONSTRAINT `applicants_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `applicants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applicants`
--

LOCK TABLES `applicants` WRITE;
/*!40000 ALTER TABLE `applicants` DISABLE KEYS */;
INSERT INTO `applicants` VALUES
(1,6,'Anzar 23rd Street, Nairobi','Nairobi','Nairobi','27689','Kenya','https://www.kubeparavyf.org','https://www.wyseme.ws',NULL,'Facilis in tempore','Duke Vance Co','Entry-level','Bachelor\'s','70000','Web Developer','Referral',1,'2025-02-11 00:18:24','2025-02-11 00:18:24'),
(2,7,'123 Elm Street','Nairobi','Nairobi','00100','Kenya','https://www.linkedin.com/in/johndoe','https://johndoeportfolio.com',NULL,'Software Engineer','TechCorp','Mid-level','Bachelor\'s','150000','Remote, Full-time','LinkedIn',1,'2025-02-11 00:32:30','2025-02-11 00:32:30'),
(3,8,'456 Maple Ave',NULL,NULL,NULL,'Kenya',NULL,NULL,NULL,NULL,NULL,'Entry-level','Bachelor\'s',NULL,NULL,NULL,1,'2025-02-11 00:33:39','2025-02-11 00:33:39');
/*!40000 ALTER TABLE `applicants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `applications`
--

DROP TABLE IF EXISTS `applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `applications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `job_post_id` bigint(20) unsigned NOT NULL,
  `cover_letter` longtext DEFAULT NULL,
  `stage` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `match_score` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `applications_business_id_foreign` (`business_id`),
  KEY `applications_location_id_foreign` (`location_id`),
  KEY `applications_applicant_id_foreign` (`applicant_id`),
  KEY `applications_job_post_id_foreign` (`job_post_id`),
  KEY `applications_created_by_foreign` (`created_by`),
  CONSTRAINT `applications_applicant_id_foreign` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`id`),
  CONSTRAINT `applications_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `applications_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `applications_job_post_id_foreign` FOREIGN KEY (`job_post_id`) REFERENCES `job_posts` (`id`),
  CONSTRAINT `applications_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applications`
--

LOCK TABLES `applications` WRITE;
/*!40000 ALTER TABLE `applications` DISABLE KEYS */;
INSERT INTO `applications` VALUES
(1,1,NULL,1,1,'<p>Dear Hiring Manager,\\n\\nI am writing to express my strong interest in the Software Engineer position at your esteemed company. With a solid background in web development and a passion for building scalable and efficient applications, I am excited about the opportunity to contribute to your team.\\n\\nOver the past five years, I have worked extensively with Laravel, Vue.js, and various backend technologies to develop enterprise-level applications. My experience includes designing RESTful APIs, optimizing database queries for performance, and implementing robust authentication and role-based access control mechanisms. I have successfully led projects that improved system efficiency, enhanced security, and delivered a seamless user experience.\\n\\nBeyond my technical expertise, I am a firm believer in collaboration and continuous learning. I thrive in fast-paced environments where problem-solving and innovation are key. I am particularly drawn to your company\'s mission of leveraging technology to drive business success, and I am eager to bring my skills in full-stack development to support your objectives.\\n\\nI have attached my resume and portfolio for your review. I would welcome the opportunity to discuss how my experience and skills align with your needs. Thank you for your time and consideration. I look forward to hearing from you.\\n\\nBest regards,\\nJohn Doe</p>','applied',NULL,1,NULL,'2025-02-11 01:04:24','2025-02-11 01:04:24'),
(2,1,NULL,2,3,'<p>Dear Hiring Manager,\\n\\nI am writing to express my strong interest in the Software Engineer position at your esteemed company. With a solid background in web development and a passion for building scalable and efficient applications, I am excited about the opportunity to contribute to your team.\\n\\nOver the past five years, I have worked extensively with Laravel, Vue.js, and various backend technologies to develop enterprise-level applications. My experience includes designing RESTful APIs, optimizing database queries for performance, and implementing robust authentication and role-based access control mechanisms. I have successfully led projects that improved system efficiency, enhanced security, and delivered a seamless user experience.\\n\\nBeyond my technical expertise, I am a firm believer in collaboration and continuous learning. I thrive in fast-paced environments where problem-solving and innovation are key. I am particularly drawn to your company\'s mission of leveraging technology to drive business success, and I am eager to bring my skills in full-stack development to support your objectives.\\n\\nI have attached my resume and portfolio for your review. I would welcome the opportunity to discuss how my experience and skills align with your needs. Thank you for your time and consideration. I look forward to hearing from you.\\n\\nBest regards,\\nJohn Doe</p>','applied',NULL,1,NULL,'2025-02-11 01:04:52','2025-02-11 01:04:52');
/*!40000 ALTER TABLE `applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendances`
--

DROP TABLE IF EXISTS `attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `business_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `clock_in` time DEFAULT NULL,
  `clock_out` time DEFAULT NULL,
  `overtime_hours` decimal(5,2) NOT NULL DEFAULT 0.00,
  `is_absent` tinyint(1) NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `logged_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendances_employee_id_foreign` (`employee_id`),
  KEY `attendances_business_id_foreign` (`business_id`),
  KEY `attendances_logged_by_foreign` (`logged_by`),
  CONSTRAINT `attendances_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_logged_by_foreign` FOREIGN KEY (`logged_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendances`
--

LOCK TABLES `attendances` WRITE;
/*!40000 ALTER TABLE `attendances` DISABLE KEYS */;
INSERT INTO `attendances` VALUES
(1,1,1,'2025-02-18','00:00:00',NULL,0.00,0,NULL,1,'2025-02-17 21:00:23','2025-02-17 21:00:23'),
(2,2,1,'2025-02-18','00:01:00','03:25:00',0.00,0,NULL,1,'2025-02-17 21:01:14','2025-02-18 00:25:18'),
(3,3,1,'2025-02-18','00:08:00','03:20:00',0.00,0,NULL,1,'2025-02-17 21:08:25','2025-02-18 00:20:13'),
(4,1,1,'2025-02-19','14:19:00','14:19:00',0.00,0,NULL,1,'2025-02-19 11:19:40','2025-02-19 11:19:45'),
(5,3,1,'2025-02-21','06:55:00',NULL,0.00,0,NULL,1,'2025-02-21 03:44:11','2025-02-21 03:55:16'),
(7,1,1,'2025-02-21','07:00:00',NULL,0.00,0,NULL,1,'2025-02-21 04:00:57','2025-02-21 04:00:57'),
(8,1,1,'2025-02-27','14:01:00','14:11:00',0.00,0,NULL,1,'2025-02-27 11:01:44','2025-02-27 11:11:07');
/*!40000 ALTER TABLE `attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `business_modules`
--

DROP TABLE IF EXISTS `business_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `business_modules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned NOT NULL,
  `module_id` bigint(20) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `subscription_ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `business_modules_business_id_foreign` (`business_id`),
  KEY `business_modules_module_id_foreign` (`module_id`),
  CONSTRAINT `business_modules_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `business_modules_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business_modules`
--

LOCK TABLES `business_modules` WRITE;
/*!40000 ALTER TABLE `business_modules` DISABLE KEYS */;
INSERT INTO `business_modules` VALUES
(1,1,7,1,NULL,'2025-02-04 03:35:38','2025-02-04 03:35:38'),
(2,1,1,1,NULL,'2025-02-04 03:35:38','2025-02-04 03:35:38'),
(3,1,9,1,NULL,'2025-02-04 03:35:38','2025-02-04 03:35:38'),
(4,1,8,1,NULL,'2025-02-04 03:35:38','2025-02-04 03:35:38'),
(5,1,5,1,NULL,'2025-02-04 03:35:38','2025-02-04 03:35:38'),
(6,1,2,1,NULL,'2025-02-04 03:35:38','2025-02-04 03:35:38'),
(7,1,4,1,NULL,'2025-02-04 03:35:38','2025-02-04 03:35:38'),
(8,1,10,1,NULL,'2025-02-04 03:35:38','2025-02-04 03:35:38'),
(9,1,3,1,NULL,'2025-02-04 03:35:38','2025-02-04 03:35:38'),
(10,1,6,1,NULL,'2025-02-04 03:35:38','2025-02-04 03:35:38'),
(11,2,1,1,NULL,'2025-02-18 03:23:48','2025-02-18 03:23:48'),
(12,2,9,1,NULL,'2025-02-18 03:23:48','2025-02-18 03:23:48'),
(13,2,2,1,NULL,'2025-02-18 03:23:48','2025-02-18 03:23:48'),
(14,2,3,1,NULL,'2025-02-18 03:23:48','2025-02-18 03:23:48'),
(15,3,1,1,NULL,'2025-02-18 05:29:28','2025-02-18 05:29:28'),
(16,3,2,1,NULL,'2025-02-18 05:29:28','2025-02-18 05:29:28'),
(17,4,7,1,NULL,'2025-02-27 11:15:31','2025-02-27 11:15:31'),
(18,4,1,1,NULL,'2025-02-27 11:15:31','2025-02-27 11:15:31'),
(19,4,9,1,NULL,'2025-02-27 11:15:31','2025-02-27 11:15:31'),
(20,4,8,1,NULL,'2025-02-27 11:15:31','2025-02-27 11:15:31');
/*!40000 ALTER TABLE `business_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `businesses`
--

DROP TABLE IF EXISTS `businesses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `businesses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `industry` varchar(255) NOT NULL,
  `company_size` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `registration_no` varchar(255) DEFAULT NULL,
  `tax_pin_no` varchar(255) DEFAULT NULL,
  `business_license_no` varchar(255) DEFAULT NULL,
  `physical_address` varchar(255) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `businesses_slug_unique` (`slug`),
  KEY `businesses_user_id_foreign` (`user_id`),
  CONSTRAINT `businesses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `businesses`
--

LOCK TABLES `businesses` WRITE;
/*!40000 ALTER TABLE `businesses` DISABLE KEYS */;
INSERT INTO `businesses` VALUES
(1,1,'Anzar KE','anzar-ke','information-technology','1-10','+254797702066','Kenya','254',NULL,NULL,NULL,NULL,NULL,'2025-02-04 03:35:20','2025-02-04 03:35:20'),
(2,9,'Unlimited Informatics','unlimited-informatics','telecommunications','11-50','+254711616012','Kenya','254',NULL,NULL,NULL,NULL,NULL,'2025-02-18 03:23:39','2025-02-18 03:23:39'),
(3,11,'Hays and Phillips Co','hays-and-phillips-co','retail','11-50','+254711616918','Kenya','254',NULL,NULL,NULL,NULL,NULL,'2025-02-18 05:29:22','2025-02-18 05:29:22'),
(4,12,'Chambers and Fowler LLC','chambers-and-fowler-llc','event-management','11-50','+254711616098','Kenya','254',NULL,NULL,NULL,NULL,NULL,'2025-02-27 11:13:09','2025-02-27 11:13:09');
/*!40000 ALTER TABLE `businesses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned NOT NULL,
  `client_business` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `clients_business_id_foreign` (`business_id`),
  KEY `clients_client_business_foreign` (`client_business`),
  CONSTRAINT `clients_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`),
  CONSTRAINT `clients_client_business_foreign` FOREIGN KEY (`client_business`) REFERENCES `businesses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deductions`
--

DROP TABLE IF EXISTS `deductions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `deductions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `calculation_basis` enum('basic_pay','gross_pay','cash_pay','taxable_pay') NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `deductions_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deductions`
--

LOCK TABLES `deductions` WRITE;
/*!40000 ALTER TABLE `deductions` DISABLE KEYS */;
INSERT INTO `deductions` VALUES
(1,1,NULL,'HELB Loan','helb-loan','Optional...','basic_pay',1,'2025-03-03 05:04:56','2025-03-03 05:04:56'),
(2,1,NULL,'Insurance Deduction','insurance-deduction','Optional...','basic_pay',1,'2025-03-03 05:05:42','2025-03-03 05:05:42'),
(3,1,NULL,'Uzima Sacco','uzima-sacco','Optional...','basic_pay',1,'2025-03-03 05:06:10','2025-03-03 05:06:10');
/*!40000 ALTER TABLE `deductions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES
(1,1,'IT Department','it-department','Responsible for all IT-related tasks','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(2,1,'Human Resources','human-resources','Handles recruitment, employee benefits, and other HR tasks','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(3,1,'Sales Department','sales-department','Responsible for sales and customer interactions','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(4,1,'Marketing Department','marketing-department','Oversees marketing and branding strategies','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(5,1,'Operations','operations','Manages day-to-day operations of the business','2025-02-04 03:34:17','2025-02-04 03:34:17');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emergency_contacts`
--

DROP TABLE IF EXISTS `emergency_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `emergency_contacts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `relationship` varchar(255) NOT NULL,
  `contact_address` varchar(255) NOT NULL,
  `telephone` varchar(255) NOT NULL,
  `additional_instructions` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `emergency_contacts_employee_id_foreign` (`employee_id`),
  CONSTRAINT `emergency_contacts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emergency_contacts`
--

LOCK TABLES `emergency_contacts` WRITE;
/*!40000 ALTER TABLE `emergency_contacts` DISABLE KEYS */;
INSERT INTO `emergency_contacts` VALUES
(1,1,'Zilper Zipora','Sister','Home Address 21763','78954126',NULL,'2025-02-04 23:11:27','2025-02-04 23:11:27'),
(2,1,'Keleb Misoli','Friend','Home Address 131','711235893',NULL,'2025-02-04 23:11:27','2025-02-04 23:11:27'),
(3,2,'Mary Johnson','Mother','123 Elm St, NY','1234567893',NULL,'2025-02-04 23:22:26','2025-02-04 23:22:26'),
(4,2,'Anna Doe','Sister','456 Maple St, NY','1234567894',NULL,'2025-02-04 23:22:26','2025-02-04 23:22:26'),
(5,3,'John Smith','Father','456 Oak St, CA','1245678904',NULL,'2025-02-04 23:30:46','2025-02-04 23:30:46'),
(6,3,'Anna Doe','Sister','456 Maple St, NY','1234567894',NULL,'2025-02-04 23:30:46','2025-02-04 23:30:46');
/*!40000 ALTER TABLE `emergency_contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_allowances`
--

DROP TABLE IF EXISTS `employee_allowances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_allowances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `allowance_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_allowances`
--

LOCK TABLES `employee_allowances` WRITE;
/*!40000 ALTER TABLE `employee_allowances` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_allowances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_contact_details`
--

DROP TABLE IF EXISTS `employee_contact_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_contact_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `work_phone` varchar(255) DEFAULT NULL,
  `work_phone_code` varchar(255) DEFAULT NULL,
  `work_phone_country` varchar(255) DEFAULT NULL,
  `work_email` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `country` varchar(255) NOT NULL,
  `email_signature` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_contact_details_work_email_unique` (`work_email`),
  KEY `employee_contact_details_employee_id_foreign` (`employee_id`),
  CONSTRAINT `employee_contact_details_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_contact_details`
--

LOCK TABLES `employee_contact_details` WRITE;
/*!40000 ALTER TABLE `employee_contact_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_contact_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_deductions`
--

DROP TABLE IF EXISTS `employee_deductions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_deductions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `deduction_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_deductions`
--

LOCK TABLES `employee_deductions` WRITE;
/*!40000 ALTER TABLE `employee_deductions` DISABLE KEYS */;
INSERT INTO `employee_deductions` VALUES
(2,1,1,7500.00,NULL,NULL,'2025-03-03 05:45:56','2025-03-03 05:45:56'),
(3,2,3,3790.00,NULL,NULL,'2025-03-03 05:46:26','2025-03-03 05:46:26'),
(4,1,3,3790.00,NULL,NULL,'2025-03-03 05:49:58','2025-03-03 05:49:58');
/*!40000 ALTER TABLE `employee_deductions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_documents`
--

DROP TABLE IF EXISTS `employee_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `document_type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_documents_employee_id_foreign` (`employee_id`),
  CONSTRAINT `employee_documents_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_documents`
--

LOCK TABLES `employee_documents` WRITE;
/*!40000 ALTER TABLE `employee_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_family_members`
--

DROP TABLE IF EXISTS `employee_family_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_family_members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `relationship` varchar(255) NOT NULL,
  `date_of_birth` date NOT NULL,
  `contact_address` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_family_members_employee_id_foreign` (`employee_id`),
  CONSTRAINT `employee_family_members_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_family_members`
--

LOCK TABLES `employee_family_members` WRITE;
/*!40000 ALTER TABLE `employee_family_members` DISABLE KEYS */;
INSERT INTO `employee_family_members` VALUES
(1,1,'Melinda Knowles Korean','Wife','1995-02-23','Contact address 36','744158621','254','2025-02-04 23:11:27','2025-02-04 23:11:27'),
(2,2,'Anna Doe','Sister','1992-07-25','456 Maple St, NY','1234567894','254','2025-02-04 23:22:26','2025-02-04 23:22:26'),
(3,3,'James Smith','Brother','1995-12-15','789 Pine St, CA','1245678905','254','2025-02-04 23:30:46','2025-02-04 23:30:46');
/*!40000 ALTER TABLE `employee_family_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_payment_details`
--

DROP TABLE IF EXISTS `employee_payment_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_payment_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `basic_salary` decimal(12,2) NOT NULL,
  `currency` enum('KES','USD','TZS','EUR') NOT NULL,
  `payment_mode` enum('bank','cash','cheque','mpesa') NOT NULL,
  `account_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_code` varchar(255) DEFAULT NULL,
  `bank_branch` varchar(255) DEFAULT NULL,
  `bank_branch_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_payment_details_employee_id_foreign` (`employee_id`),
  CONSTRAINT `employee_payment_details_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_payment_details`
--

LOCK TABLES `employee_payment_details` WRITE;
/*!40000 ALTER TABLE `employee_payment_details` DISABLE KEYS */;
INSERT INTO `employee_payment_details` VALUES
(1,1,68000.00,'KES','bank','Arnold W Zahara','398456382984','Diamond Trust','B9487','Delta Ware','DT625','2025-02-04 23:11:27','2025-02-04 23:11:27'),
(2,2,60000.00,'KES','bank','John Doe','123456789012','ABC Bank','ABC123','Main Branch','MB123','2025-02-04 23:22:26','2025-02-04 23:22:26'),
(3,3,55000.00,'KES','bank','Emily Smith','987654321098','XYZ Bank','XYZ123','Downtown Branch','DB123','2025-02-04 23:30:46','2025-02-04 23:30:46');
/*!40000 ALTER TABLE `employee_payment_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_payrolls`
--

DROP TABLE IF EXISTS `employee_payrolls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_payrolls` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payroll_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `basic_salary` int(11) DEFAULT NULL,
  `housing_allowance` int(11) DEFAULT NULL,
  `gross_pay` int(11) DEFAULT NULL,
  `paye` int(11) DEFAULT NULL,
  `nhif` int(11) DEFAULT NULL,
  `nssf` int(11) DEFAULT NULL,
  `pension` int(11) DEFAULT NULL,
  `housing_levy` int(11) DEFAULT NULL,
  `taxable_income` int(11) DEFAULT NULL,
  `personal_relief` int(11) DEFAULT NULL,
  `pay_after_tax` int(11) DEFAULT NULL,
  `deductions_after_tax` int(11) DEFAULT NULL,
  `net_pay` int(11) DEFAULT NULL,
  `deductions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`deductions`)),
  `overtime` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`overtime`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_payrolls`
--

LOCK TABLES `employee_payrolls` WRITE;
/*!40000 ALTER TABLE `employee_payrolls` DISABLE KEYS */;
INSERT INTO `employee_payrolls` VALUES
(1,3,1,68000,NULL,68000,15183,4400,840,NULL,1020,68000,NULL,52817,0,52817,NULL,NULL,'2025-02-06 00:42:01','2025-02-06 00:42:01'),
(2,3,2,60000,NULL,60000,12783,4400,840,NULL,900,60000,NULL,47217,0,47217,NULL,NULL,'2025-02-06 00:42:01','2025-02-06 00:42:01'),
(3,3,3,55000,NULL,55000,11283,4400,840,NULL,825,55000,NULL,43717,0,43717,NULL,NULL,'2025-02-06 00:42:01','2025-02-06 00:42:01'),
(4,4,1,68000,NULL,68000,15183,4400,840,NULL,1020,68000,NULL,52817,0,52817,NULL,NULL,'2025-02-07 03:49:28','2025-02-07 03:49:28'),
(5,4,3,55000,NULL,55000,11283,4400,840,NULL,825,55000,NULL,43717,0,43717,NULL,NULL,'2025-02-07 03:49:28','2025-02-07 03:49:28'),
(6,5,1,68000,NULL,68000,15183,4400,840,NULL,1020,68000,NULL,52817,0,52817,NULL,NULL,'2025-02-11 04:05:11','2025-02-11 04:05:11'),
(7,5,3,55000,NULL,55000,11283,4400,840,NULL,825,55000,NULL,43717,0,43717,NULL,NULL,'2025-02-11 04:05:11','2025-02-11 04:05:11'),
(8,6,4,60000,NULL,60000,12783,4400,840,NULL,900,60000,NULL,47217,0,47217,NULL,NULL,'2025-02-18 03:42:32','2025-02-18 03:42:32'),
(9,7,4,60000,NULL,60000,12783,4400,840,NULL,900,60000,NULL,47217,0,47217,NULL,NULL,'2025-02-18 03:45:17','2025-02-18 03:45:17'),
(10,8,4,60000,NULL,60000,12783,4400,840,NULL,900,60000,NULL,47217,0,47217,NULL,NULL,'2025-02-18 03:50:35','2025-02-18 03:50:35'),
(39,27,1,68000,NULL,68006,15185,4400,840,NULL,1020,68006,2400,43321,11290,32031,'[{\"type\":\"Employee Deduction\",\"name\":\"HELB Loan\",\"amount\":\"7500.00\",\"notes\":null},{\"type\":\"Employee Deduction\",\"name\":\"Uzima Sacco\",\"amount\":\"3790.00\",\"notes\":null}]','6.00','2025-03-04 00:54:34','2025-03-04 00:54:34'),
(40,27,3,55000,NULL,55000,11283,4400,840,NULL,825,55000,2400,34412,0,34412,'[]','0','2025-03-04 00:54:34','2025-03-04 00:54:34');
/*!40000 ALTER TABLE `employee_payrolls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_reliefs`
--

DROP TABLE IF EXISTS `employee_reliefs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_reliefs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `relief_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_reliefs`
--

LOCK TABLES `employee_reliefs` WRITE;
/*!40000 ALTER TABLE `employee_reliefs` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_reliefs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_task`
--

DROP TABLE IF EXISTS `employee_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_task` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_task_task_id_foreign` (`task_id`),
  KEY `employee_task_employee_id_foreign` (`employee_id`),
  CONSTRAINT `employee_task_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_task_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_task`
--

LOCK TABLES `employee_task` WRITE;
/*!40000 ALTER TABLE `employee_task` DISABLE KEYS */;
INSERT INTO `employee_task` VALUES
(3,2,2,'2025-02-19 21:47:52','2025-02-19 21:47:52'),
(4,2,3,'2025-02-19 21:47:52','2025-02-19 21:47:52'),
(5,3,2,'2025-02-21 08:29:15','2025-02-21 08:29:15'),
(6,3,3,'2025-02-21 08:29:15','2025-02-21 08:29:15');
/*!40000 ALTER TABLE `employee_task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `business_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `employee_code` varchar(255) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `alternate_phone` varchar(255) DEFAULT NULL,
  `date_of_birth` date NOT NULL,
  `place_of_birth` varchar(255) DEFAULT NULL,
  `marital_status` enum('single','married','divorced','widowed') NOT NULL,
  `national_id` varchar(255) NOT NULL,
  `place_of_issue` varchar(255) DEFAULT NULL,
  `tax_no` varchar(255) NOT NULL,
  `nhif_no` varchar(255) DEFAULT NULL,
  `nssf_no` varchar(255) DEFAULT NULL,
  `passport_no` varchar(255) DEFAULT NULL,
  `passport_issue_date` date DEFAULT NULL,
  `passport_expiry_date` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `permanent_address` varchar(255) DEFAULT NULL,
  `blood_group` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_employee_code_unique` (`employee_code`),
  UNIQUE KEY `employees_national_id_unique` (`national_id`),
  UNIQUE KEY `employees_tax_no_unique` (`tax_no`),
  UNIQUE KEY `employees_nhif_no_unique` (`nhif_no`),
  UNIQUE KEY `employees_nssf_no_unique` (`nssf_no`),
  UNIQUE KEY `employees_passport_no_unique` (`passport_no`),
  KEY `employees_user_id_foreign` (`user_id`),
  KEY `employees_business_id_foreign` (`business_id`),
  KEY `employees_department_id_foreign` (`department_id`),
  KEY `employees_location_id_foreign` (`location_id`),
  CONSTRAINT `employees_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES
(1,2,1,1,'EMP7836','male','+25474589631','1992-09-16',NULL,'married','3655874','Another place','TX68734653L','N4T67365','NST73256','PS9867','2023-08-16','2028-02-16','The river side ST, 353','Home River Street, 872','AB+','2025-02-04 23:11:27','2025-02-04 23:11:27',NULL),
(2,3,1,2,'EMP1023','male','+2541234567891','1985-04-15',NULL,'single','1234567890','New York','TAX12345','NHIF12345','NSSF12345','P123456','2010-05-01','2025-05-01','123 Main St, New York, NY','456 Park Ave, New York, NY','O+','2025-02-04 23:22:26','2025-02-04 23:22:26',3),
(3,4,1,3,'EMP1002','female','+2541245678902','1990-08-10',NULL,'married','2233445566','California','TAX22345','NHIF22345','NSSF22345','P223456','2012-03-15','2027-03-15','456 Oak St, Los Angeles, CA','789 Pine St, Los Angeles, CA','A+','2025-02-04 23:30:46','2025-02-04 23:30:46',NULL);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employment_details`
--

DROP TABLE IF EXISTS `employment_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employment_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `job_category_id` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned DEFAULT NULL,
  `employment_date` date NOT NULL,
  `probation_end_date` date DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `retirement_date` date DEFAULT NULL,
  `employment_term` enum('contract','fulltime','permanent') NOT NULL,
  `job_description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employment_details_employee_id_foreign` (`employee_id`),
  KEY `employment_details_department_id_foreign` (`department_id`),
  KEY `employment_details_job_category_id_foreign` (`job_category_id`),
  KEY `employment_details_shift_id_foreign` (`shift_id`),
  CONSTRAINT `employment_details_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  CONSTRAINT `employment_details_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employment_details_job_category_id_foreign` FOREIGN KEY (`job_category_id`) REFERENCES `job_categories` (`id`),
  CONSTRAINT `employment_details_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employment_details`
--

LOCK TABLES `employment_details` WRITE;
/*!40000 ALTER TABLE `employment_details` DISABLE KEYS */;
INSERT INTO `employment_details` VALUES
(1,1,1,1,4,'2025-02-05','2025-04-16','2028-02-24','2055-02-25','fulltime','Plenty of animals are semi-aquatic, just like hippos! Here are some cool ones:\r\n\r\n    Capybaras – The world’s largest rodents love both land and water, chilling in South American wetlands.\r\n    Beavers – Master engineers that build dams and lodges in rivers while still roaming on land.\r\n    Crocodiles & Alligators – Apex predators that dominate both environments, lurking in the water but hunting on land too.\r\n    Otters – Playful swimmers that also move comfortably on land.\r\n    Penguins – Flightless birds that waddle on land but are like torpedoes in the water.\r\n    Turtles – Some, like sea turtles, only come on land to lay eggs, while others (like snapping turtles) split their time.\r\n    Frogs – Amphibians that need water to reproduce but are often hopping around on land.\r\n\r\nNature really loves mixing things up! Any particular creature you\'re curious about?','2025-02-04 23:11:27','2025-02-04 23:11:27'),
(2,2,2,2,1,'2020-01-01','2020-06-01','2025-01-01','2045-01-01','permanent','You can replicate the structure for more data sets with slight variations for testing. Just ensure the values match the expected types and formats for the fields.','2025-02-04 23:22:26','2025-02-04 23:22:26'),
(3,3,3,5,4,'2019-02-20','2019-08-20','2024-02-20','2050-02-20','permanent','Accounting and bookkeeping','2025-02-04 23:30:46','2025-02-04 23:30:46');
/*!40000 ALTER TABLE `employment_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `industries`
--

DROP TABLE IF EXISTS `industries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `industries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `industries_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `industries`
--

LOCK TABLES `industries` WRITE;
/*!40000 ALTER TABLE `industries` DISABLE KEYS */;
INSERT INTO `industries` VALUES
(1,'Information Technology','information-technology','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(2,'Healthcare','healthcare','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(3,'Finance','finance','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(4,'Education','education','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(5,'Retail','retail','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(6,'Manufacturing','manufacturing','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(7,'Construction','construction','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(8,'Real Estate','real-estate','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(9,'Hospitality','hospitality','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(10,'Entertainment','entertainment','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(11,'Automotive','automotive','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(12,'Telecommunications','telecommunications','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(13,'Energy','energy','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(14,'Agriculture','agriculture','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(15,'Aerospace','aerospace','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(16,'Logistics and Supply Chain','logistics-and-supply-chain','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(17,'Food and Beverage','food-and-beverage','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(18,'Fashion','fashion','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(19,'Media and Publishing','media-and-publishing','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(20,'Pharmaceuticals','pharmaceuticals','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(21,'Sports and Recreation','sports-and-recreation','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(22,'Legal Services','legal-services','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(23,'Consulting','consulting','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(24,'Environmental Services','environmental-services','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(25,'Transportation','transportation','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(26,'Government and Public Administration','government-and-public-administration','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(27,'Non-Profit and Social Services','non-profit-and-social-services','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(28,'Marketing and Advertising','marketing-and-advertising','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(29,'E-Commerce','e-commerce','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(30,'Beauty and Personal Care','beauty-and-personal-care','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(31,'Insurance','insurance','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(32,'Cybersecurity','cybersecurity','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(33,'Event Management','event-management','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(34,'Research and Development','research-and-development','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(35,'Art and Design','art-and-design','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(36,'Pet Care','pet-care','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(37,'Fitness and Wellness','fitness-and-wellness','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(38,'Waste Management','waste-management','2025-02-04 03:34:17','2025-02-04 03:34:17');
/*!40000 ALTER TABLE `industries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `interview_feedback`
--

DROP TABLE IF EXISTS `interview_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `interview_feedback` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `interview_id` bigint(20) unsigned NOT NULL,
  `interviewer_id` bigint(20) unsigned NOT NULL,
  `comments` text NOT NULL,
  `score` int(11) NOT NULL DEFAULT 0,
  `recommendation` enum('hire','reject','second_interview') NOT NULL DEFAULT 'second_interview',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interview_feedback_interview_id_foreign` (`interview_id`),
  KEY `interview_feedback_interviewer_id_foreign` (`interviewer_id`),
  CONSTRAINT `interview_feedback_interview_id_foreign` FOREIGN KEY (`interview_id`) REFERENCES `interviews` (`id`) ON DELETE CASCADE,
  CONSTRAINT `interview_feedback_interviewer_id_foreign` FOREIGN KEY (`interviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interview_feedback`
--

LOCK TABLES `interview_feedback` WRITE;
/*!40000 ALTER TABLE `interview_feedback` DISABLE KEYS */;
/*!40000 ALTER TABLE `interview_feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `interviews`
--

DROP TABLE IF EXISTS `interviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `interviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `application_id` bigint(20) unsigned NOT NULL,
  `interviewer_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `type` enum('phone','video','in-person') NOT NULL DEFAULT 'in-person',
  `location` varchar(255) DEFAULT NULL,
  `meeting_link` varchar(255) DEFAULT NULL,
  `scheduled_at` timestamp NOT NULL,
  `notes` text DEFAULT NULL,
  `outcome` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interviews_application_id_foreign` (`application_id`),
  KEY `interviews_interviewer_id_foreign` (`interviewer_id`),
  KEY `interviews_created_by_foreign` (`created_by`),
  CONSTRAINT `interviews_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `interviews_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `interviews_interviewer_id_foreign` FOREIGN KEY (`interviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interviews`
--

LOCK TABLES `interviews` WRITE;
/*!40000 ALTER TABLE `interviews` DISABLE KEYS */;
INSERT INTO `interviews` VALUES
(6,1,NULL,NULL,'in-person','Othaya 51, Kielleshwa',NULL,'2025-02-11 09:00:00',NULL,NULL,'2025-02-11 02:38:32','2025-02-11 02:38:32'),
(7,1,NULL,NULL,'in-person','Othaya 51, Kielleshwa',NULL,'2025-02-11 09:00:00',NULL,NULL,'2025-02-11 02:44:41','2025-02-11 02:44:41');
/*!40000 ALTER TABLE `interviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_categories`
--

DROP TABLE IF EXISTS `job_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `job_categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_categories`
--

LOCK TABLES `job_categories` WRITE;
/*!40000 ALTER TABLE `job_categories` DISABLE KEYS */;
INSERT INTO `job_categories` VALUES
(1,1,'Software Developer','software-developer','Develops and maintains software applications','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(2,1,'Human Resources','human-resources','Manages employee relations and recruitment','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(3,1,'Marketing Manager','marketing-manager','Oversees marketing strategies and campaigns','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(4,1,'Project Manager','project-manager','Manages and coordinates project teams','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(5,1,'Sales Executive','sales-executive','Responsible for sales and client acquisition','2025-02-04 03:34:17','2025-02-04 03:34:17');
/*!40000 ALTER TABLE `job_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_posts`
--

DROP TABLE IF EXISTS `job_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `requirements` longtext DEFAULT NULL,
  `salary_range` varchar(255) DEFAULT NULL,
  `number_of_positions` int(11) NOT NULL DEFAULT 1,
  `employment_type` enum('full-time','part-time','contract','internship') NOT NULL,
  `place` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `job_posts_slug_unique` (`slug`),
  KEY `job_posts_created_by_foreign` (`created_by`),
  CONSTRAINT `job_posts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_posts`
--

LOCK TABLES `job_posts` WRITE;
/*!40000 ALTER TABLE `job_posts` DISABLE KEYS */;
INSERT INTO `job_posts` VALUES
(1,1,NULL,NULL,'Software Engineer (Full-Stack) – Laravel & Vue.js','software-engineer-full-stack-laravel-vuejs','<p data-start=\"298\" data-end=\"627\">We are seeking a skilled <strong data-start=\"323\" data-end=\"355\">Full-Stack Software Engineer</strong> with experience in <strong data-start=\"375\" data-end=\"386\">Laravel</strong> and <strong data-start=\"391\" data-end=\"401\">Vue.js</strong> to join our dynamic development team. The ideal candidate will be responsible for designing, developing, and maintaining scalable web applications while ensuring seamless integration between frontend and backend components.</p>\r\n<h4 data-start=\"629\" data-end=\"661\"><strong data-start=\"634\" data-end=\"659\">Key Responsibilities:</strong></h4>\r\n<ul data-start=\"662\" data-end=\"1220\">\r\n<li data-start=\"662\" data-end=\"737\">Develop and maintain web applications using <strong data-start=\"708\" data-end=\"719\">Laravel</strong> and <strong data-start=\"724\" data-end=\"734\">Vue.js</strong>.</li>\r\n<li data-start=\"738\" data-end=\"836\">Collaborate with UX/UI designers to implement visually appealing and user-friendly interfaces.</li>\r\n<li data-start=\"837\" data-end=\"907\">Write efficient, reusable, and secure <strong data-start=\"877\" data-end=\"899\">PHP and JavaScript</strong> code.</li>\r\n<li data-start=\"908\" data-end=\"964\">Integrate <strong data-start=\"920\" data-end=\"936\">RESTful APIs</strong> and third-party services.</li>\r\n<li data-start=\"965\" data-end=\"1018\">Optimize application performance and scalability.</li>\r\n<li data-start=\"1019\" data-end=\"1090\">Maintain and update database structures using <strong data-start=\"1067\" data-end=\"1087\">MySQL/PostgreSQL</strong>.</li>\r\n<li data-start=\"1091\" data-end=\"1134\">Troubleshoot and debug software issues.</li>\r\n<li data-start=\"1135\" data-end=\"1220\">Work closely with cross-functional teams to define and meet project requirements.</li>\r\n</ul>\r\n<h4 data-start=\"1222\" data-end=\"1246\"><strong data-start=\"1227\" data-end=\"1244\">Requirements:</strong></h4>\r\n<p data-start=\"1247\" data-end=\"1670\">✅ Bachelor\'s degree in Computer Science, Software Engineering, or a related field.<br data-start=\"1329\" data-end=\"1332\">✅ 3+ years of experience in <strong data-start=\"1360\" data-end=\"1371\">Laravel</strong> and <strong data-start=\"1376\" data-end=\"1386\">Vue.js</strong> development.<br data-start=\"1399\" data-end=\"1402\">✅ Strong knowledge of <strong data-start=\"1424\" data-end=\"1440\">RESTful APIs</strong>, database management, and cloud services.<br data-start=\"1482\" data-end=\"1485\">✅ Experience with <strong data-start=\"1503\" data-end=\"1510\">Git</strong>, <strong data-start=\"1512\" data-end=\"1522\">Docker</strong>, and CI/CD pipelines.<br data-start=\"1544\" data-end=\"1547\">✅ Ability to write clean, maintainable, and well-documented code.<br data-start=\"1612\" data-end=\"1615\">✅ Excellent problem-solving and communication skills.</p>\r\n<h4 data-start=\"1672\" data-end=\"1700\"><strong data-start=\"1677\" data-end=\"1698\">Preferred Skills:</strong></h4>\r\n<ul data-start=\"1701\" data-end=\"1897\">\r\n<li data-start=\"1701\" data-end=\"1752\">Experience with <strong data-start=\"1719\" data-end=\"1749\">microservices architecture</strong>.</li>\r\n<li data-start=\"1753\" data-end=\"1824\">Knowledge of <strong data-start=\"1768\" data-end=\"1779\">Node.js</strong> and WebSockets for real-time applications.</li>\r\n<li data-start=\"1825\" data-end=\"1897\">Familiarity with <strong data-start=\"1844\" data-end=\"1860\">unit testing</strong> and test-driven development (TDD).</li>\r\n</ul>',NULL,'150000 - 200000',1,'full-time','Nairobi, Kenya',1,NULL,'2025-02-10 22:55:57','2025-02-10 22:55:57'),
(2,1,NULL,NULL,'Digital Marketing Specialist','digital-marketing-specialist','<p data-start=\"3607\" data-end=\"3913\">We are looking for a creative and data-driven <strong data-start=\"3653\" data-end=\"3685\">Digital Marketing Specialist</strong> to drive our <strong data-start=\"3699\" data-end=\"3739\">SEO, SEM, and social media campaigns</strong>. The ideal candidate will have a deep understanding of digital marketing strategies and be able to analyze key performance indicators (KPIs) to optimize marketing efforts.</p>\r\n<h4 data-start=\"3915\" data-end=\"3947\"><strong data-start=\"3920\" data-end=\"3945\">Key Responsibilities:</strong></h4>\r\n<ul data-start=\"3948\" data-end=\"4408\">\r\n<li data-start=\"3948\" data-end=\"4022\">Develop and execute <strong data-start=\"3970\" data-end=\"3977\">SEO</strong> and <strong data-start=\"3982\" data-end=\"3989\">PPC</strong> campaigns for lead generation.</li>\r\n<li data-start=\"4023\" data-end=\"4110\">Manage and grow company presence across <strong data-start=\"4065\" data-end=\"4107\">Facebook, Twitter, LinkedIn, Instagram</strong>.</li>\r\n<li data-start=\"4111\" data-end=\"4177\">Write compelling <strong data-start=\"4130\" data-end=\"4174\">blog posts, email campaigns, and ad copy</strong>.</li>\r\n<li data-start=\"4178\" data-end=\"4256\">Optimize website content and landing pages for <strong data-start=\"4227\" data-end=\"4253\">search engine rankings</strong>.</li>\r\n<li data-start=\"4257\" data-end=\"4321\">Track, analyze, and report on marketing performance metrics.</li>\r\n<li data-start=\"4322\" data-end=\"4408\">Collaborate with the sales team to align marketing strategies with business goals.</li>\r\n</ul>\r\n<h4 data-start=\"4410\" data-end=\"4434\"><strong data-start=\"4415\" data-end=\"4432\">Requirements:</strong></h4>\r\n<p data-start=\"4435\" data-end=\"4769\">✅ Bachelor&rsquo;s degree in <strong data-start=\"4458\" data-end=\"4501\">Marketing, Business, or a related field</strong>.<br data-start=\"4502\" data-end=\"4505\">✅ 3+ years of experience in <strong data-start=\"4533\" data-end=\"4573\">digital marketing, SEO, and paid ads</strong>.<br data-start=\"4574\" data-end=\"4577\">✅ Proficiency in <strong data-start=\"4594\" data-end=\"4648\">Google Analytics, Google Ads, and Meta Ads Manager</strong>.<br data-start=\"4649\" data-end=\"4652\">✅ Strong writing and content creation skills.<br data-start=\"4697\" data-end=\"4700\">✅ Ability to analyze data and make data-driven marketing decisions.</p>\r\n<h4 data-start=\"4771\" data-end=\"4799\"><strong data-start=\"4776\" data-end=\"4797\">Preferred Skills:</strong></h4>\r\n<ul data-start=\"4800\" data-end=\"4960\">\r\n<li data-start=\"4800\" data-end=\"4878\">Experience with <strong data-start=\"4818\" data-end=\"4848\">marketing automation tools</strong> (e.g., HubSpot, Mailchimp).</li>\r\n<li data-start=\"4879\" data-end=\"4960\">Familiarity with <strong data-start=\"4898\" data-end=\"4922\">graphic design tools</strong> like Canva or Adobe Creative Suite.</li>\r\n</ul>',NULL,'100000 - 150000',1,'contract','Remote / Hybrid',1,NULL,'2025-02-10 22:57:03','2025-02-10 22:57:03'),
(3,1,NULL,NULL,'Human Resource Manager','human-resource-manager','<p data-start=\"2085\" data-end=\"2360\">We are looking for a <strong data-start=\"2106\" data-end=\"2132\">Human Resource Manager</strong> to oversee our HR department and drive <strong data-start=\"2172\" data-end=\"2245\">talent management, employee relations, and organizational development</strong>. The HR Manager will work closely with department heads to implement HR policies that align with business goals.</p>\r\n<h4 data-start=\"2362\" data-end=\"2394\"><strong data-start=\"2367\" data-end=\"2392\">Key Responsibilities:</strong></h4>\r\n<ul data-start=\"2395\" data-end=\"2833\">\r\n<li data-start=\"2395\" data-end=\"2476\">Develop and implement <strong data-start=\"2419\" data-end=\"2473\">HR policies, procedures, and compliance strategies</strong>.</li>\r\n<li data-start=\"2477\" data-end=\"2556\">Manage the <strong data-start=\"2490\" data-end=\"2531\">recruitment, onboarding, and training</strong> process for new hires.</li>\r\n<li data-start=\"2557\" data-end=\"2638\">Handle <strong data-start=\"2566\" data-end=\"2635\">employee relations, conflict resolution, and disciplinary actions</strong>.</li>\r\n<li data-start=\"2639\" data-end=\"2712\">Oversee payroll, benefits administration, and performance appraisals.</li>\r\n<li data-start=\"2713\" data-end=\"2784\">Ensure compliance with <strong data-start=\"2738\" data-end=\"2781\">Kenyan labor laws and HR best practices</strong>.</li>\r\n<li data-start=\"2785\" data-end=\"2833\">Foster a positive and engaging work culture.</li>\r\n</ul>\r\n<h4 data-start=\"2835\" data-end=\"2859\"><strong data-start=\"2840\" data-end=\"2857\">Requirements:</strong></h4>\r\n<p data-start=\"2860\" data-end=\"3224\">✅ Bachelor&rsquo;s degree in <strong data-start=\"2883\" data-end=\"2957\">Human Resource Management, Business Administration, or a related field</strong>.<br data-start=\"2958\" data-end=\"2961\">✅ Minimum of <strong data-start=\"2974\" data-end=\"3013\">5 years of HR management experience</strong>.<br data-start=\"3014\" data-end=\"3017\">✅ Strong knowledge of <strong data-start=\"3039\" data-end=\"3079\">Kenyan labor laws and HR regulations</strong>.<br data-start=\"3080\" data-end=\"3083\">✅ Excellent leadership, communication, and interpersonal skills.<br data-start=\"3147\" data-end=\"3150\">✅ Experience using <strong data-start=\"3169\" data-end=\"3185\">HRM software</strong> for payroll and employee management.</p>\r\n<h4 data-start=\"3226\" data-end=\"3254\"><strong data-start=\"3231\" data-end=\"3252\">Preferred Skills:</strong></h4>\r\n<ul data-start=\"3255\" data-end=\"3399\">\r\n<li data-start=\"3255\" data-end=\"3327\">Certification in <strong data-start=\"3274\" data-end=\"3284\">CHRP-K</strong> or other professional HR qualifications.</li>\r\n<li data-start=\"3328\" data-end=\"3399\">Experience in <strong data-start=\"3344\" data-end=\"3396\">organizational development and change management</strong>.</li>\r\n</ul>',NULL,'180000 - 250000',1,'full-time','Mombasa, Kenya',1,NULL,'2025-02-10 22:58:53','2025-02-10 22:58:53');
/*!40000 ALTER TABLE `job_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES
(1,'default','{\"uuid\":\"138f12a0-6cad-4632-a644-0e45cf7eb4eb\",\"displayName\":\"App\\\\Notifications\\\\InterviewScheduledNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:48:\\\"App\\\\Notifications\\\\InterviewScheduledNotification\\\":2:{s:12:\\\"\\u0000*\\u0000interview\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:20:\\\"App\\\\Models\\\\Interview\\\";s:2:\\\"id\\\";i:6;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"270ae3c4-79ad-4f0c-9298-5f7606162172\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1739252312,1739252312),
(2,'default','{\"uuid\":\"9b73b924-1f80-43ec-aff9-350a780a2e1f\",\"displayName\":\"App\\\\Notifications\\\\InterviewScheduledNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:48:\\\"App\\\\Notifications\\\\InterviewScheduledNotification\\\":2:{s:12:\\\"\\u0000*\\u0000interview\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:20:\\\"App\\\\Models\\\\Interview\\\";s:2:\\\"id\\\";i:6;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"270ae3c4-79ad-4f0c-9298-5f7606162172\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"}}',0,NULL,1739252312,1739252312),
(3,'default','{\"uuid\":\"afa305ca-d8fa-438d-9bab-45c310dd15d4\",\"displayName\":\"App\\\\Notifications\\\\InterviewScheduledNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:48:\\\"App\\\\Notifications\\\\InterviewScheduledNotification\\\":2:{s:12:\\\"\\u0000*\\u0000interview\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:20:\\\"App\\\\Models\\\\Interview\\\";s:2:\\\"id\\\";i:7;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"695bcb0e-5576-4b81-84b7-59d27b4d07a1\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1739252681,1739252681),
(4,'default','{\"uuid\":\"e272fed5-e207-43eb-9c19-e2a6c1c4ccf8\",\"displayName\":\"App\\\\Notifications\\\\InterviewScheduledNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:48:\\\"App\\\\Notifications\\\\InterviewScheduledNotification\\\":2:{s:12:\\\"\\u0000*\\u0000interview\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:20:\\\"App\\\\Models\\\\Interview\\\";s:2:\\\"id\\\";i:7;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"695bcb0e-5576-4b81-84b7-59d27b4d07a1\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"}}',0,NULL,1739252681,1739252681),
(5,'default','{\"uuid\":\"ea4a65af-41e0-4dd1-80aa-f7ee359d345a\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"1f4ace0a-0dd8-452c-8544-b14ee9e45e96\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1740365328,1740365328),
(6,'default','{\"uuid\":\"7a6648bc-9e15-460f-bcc7-ccd225013ed9\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"350d24c7-18e6-4ddc-97d2-0c08b1922ceb\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1740366298,1740366298),
(7,'default','{\"uuid\":\"d6750ed9-3363-49af-9b84-2732aa996342\",\"displayName\":\"App\\\\Events\\\\NotificationSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:27:\\\"App\\\\Events\\\\NotificationSent\\\":2:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":2:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"}}',0,NULL,1740366298,1740366298),
(8,'default','{\"uuid\":\"d3400661-0771-43f2-81bd-5dfff573fd28\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"fdfbaaaa-00a9-4247-83a8-c2c3e930c025\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1740366453,1740366453),
(9,'default','{\"uuid\":\"391c6431-6712-47a4-92d7-d0e7d99795e0\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"fdfbaaaa-00a9-4247-83a8-c2c3e930c025\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:5:\\\"email\\\";}}\"}}',0,NULL,1740366453,1740366453),
(10,'default','{\"uuid\":\"48bcf53a-7bb6-43f5-9a1f-e1f3f5f4fc69\",\"displayName\":\"App\\\\Events\\\\NotificationSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:27:\\\"App\\\\Events\\\\NotificationSent\\\":2:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":2:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"}}',0,NULL,1740366453,1740366453),
(11,'default','{\"uuid\":\"05a7322d-0cd3-4ad9-8608-dfdf731220d1\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"6f52131f-e7e0-4b29-a8cf-f465ab563965\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1740366772,1740366772),
(12,'default','{\"uuid\":\"d7c70215-2dff-4403-ad99-81c58c76f463\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"6f52131f-e7e0-4b29-a8cf-f465ab563965\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:5:\\\"email\\\";}}\"}}',0,NULL,1740366772,1740366772),
(13,'default','{\"uuid\":\"bcaca988-476c-4280-8901-dfdc6432949f\",\"displayName\":\"App\\\\Events\\\\NotificationSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:27:\\\"App\\\\Events\\\\NotificationSent\\\":2:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":2:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"}}',0,NULL,1740366772,1740366772),
(14,'default','{\"uuid\":\"7159fb13-7c6d-4e93-9f27-5fbc9e93b967\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"fb59d61e-7749-47a9-a195-d665b7d595cf\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1740366797,1740366797),
(15,'default','{\"uuid\":\"3da9d61c-c370-40a3-837e-eaa3ae346c1c\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"fb59d61e-7749-47a9-a195-d665b7d595cf\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:5:\\\"email\\\";}}\"}}',0,NULL,1740366797,1740366797),
(16,'default','{\"uuid\":\"030b2676-b376-43b0-9535-9c2d0b3f38aa\",\"displayName\":\"App\\\\Events\\\\NotificationSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:27:\\\"App\\\\Events\\\\NotificationSent\\\":2:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":2:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"}}',0,NULL,1740366797,1740366797),
(17,'default','{\"uuid\":\"6c06cdb1-4c56-43d7-955e-1212b4a5c506\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"3bf53586-0e9c-4f2b-adda-87ca7f38fe9d\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1740378524,1740378524),
(18,'default','{\"uuid\":\"018345a8-f319-4191-a529-a2e8e378d49e\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"3bf53586-0e9c-4f2b-adda-87ca7f38fe9d\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:5:\\\"email\\\";}}\"}}',0,NULL,1740378524,1740378524),
(19,'default','{\"uuid\":\"7aa1bf4a-ed11-4934-83d9-ff949a68d202\",\"displayName\":\"App\\\\Events\\\\NotificationSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:27:\\\"App\\\\Events\\\\NotificationSent\\\":2:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":2:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"}}',0,NULL,1740378524,1740378524),
(20,'default','{\"uuid\":\"a48fdcbc-1b12-4a3c-b3bd-de72a5bae8e8\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"ba841758-8db3-4487-a2d1-de1e6071fe86\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1740560760,1740560760),
(21,'default','{\"uuid\":\"ddd5813c-e9ab-473b-b0b1-6f7b2a4025ab\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"ba841758-8db3-4487-a2d1-de1e6071fe86\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:5:\\\"email\\\";}}\"}}',0,NULL,1740560760,1740560760),
(22,'default','{\"uuid\":\"c2c10852-eddd-4bca-98b5-e977e1e4ca32\",\"displayName\":\"App\\\\Events\\\\NotificationSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:27:\\\"App\\\\Events\\\\NotificationSent\\\":2:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":2:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"}}',0,NULL,1740560760,1740560760),
(23,'default','{\"uuid\":\"c5a8abb0-a649-4410-b7f8-8d7ece04ad09\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"6938b5d8-c151-42d9-be56-adae230b8ef8\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1740628020,1740628020),
(24,'default','{\"uuid\":\"488b12ef-b16f-458a-acbc-c9ed2f1e82e3\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"6938b5d8-c151-42d9-be56-adae230b8ef8\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:5:\\\"email\\\";}}\"}}',0,NULL,1740628020,1740628020),
(25,'default','{\"uuid\":\"b1513781-09aa-4faf-8d9a-f6dff4176105\",\"displayName\":\"App\\\\Events\\\\NotificationSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:27:\\\"App\\\\Events\\\\NotificationSent\\\":2:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":2:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"}}',0,NULL,1740628020,1740628020),
(26,'default','{\"uuid\":\"4b61e345-3a72-4c69-bd27-add2570938f6\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"1e53c6d5-02a5-4d46-8bad-faf957ba0853\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1740644988,1740644988),
(27,'default','{\"uuid\":\"f0d0e5c5-eafe-471b-8e86-21a4c3011618\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"1e53c6d5-02a5-4d46-8bad-faf957ba0853\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:5:\\\"email\\\";}}\"}}',0,NULL,1740644988,1740644988),
(28,'default','{\"uuid\":\"b0353219-c82d-4975-a1ca-261c527fb2be\",\"displayName\":\"App\\\\Events\\\\NotificationSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:27:\\\"App\\\\Events\\\\NotificationSent\\\":2:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":2:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"}}',0,NULL,1740644988,1740644988),
(29,'default','{\"uuid\":\"7a3a51f8-ab79-4f46-a7a2-6fa6f5e3e59f\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"16ef96d2-ae78-4b85-803c-e3ee9a159194\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1740647224,1740647224),
(30,'default','{\"uuid\":\"cdfc8d13-0efa-4753-b441-eac43dd6fe7c\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"16ef96d2-ae78-4b85-803c-e3ee9a159194\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:5:\\\"email\\\";}}\"}}',0,NULL,1740647224,1740647224),
(31,'default','{\"uuid\":\"59200fe0-1dc8-4512-9e09-fda3f68b0635\",\"displayName\":\"App\\\\Events\\\\NotificationSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:27:\\\"App\\\\Events\\\\NotificationSent\\\":2:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":2:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"}}',0,NULL,1740647224,1740647224),
(32,'default','{\"uuid\":\"ddb3ce35-9a25-41fa-babc-8099958c9df4\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:9;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"40f4dd91-c5da-4647-99e6-edb4ac04a8ff\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1740651136,1740651136),
(33,'default','{\"uuid\":\"debb0d39-f9fc-4974-88f6-25683d9c12e4\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:9;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"40f4dd91-c5da-4647-99e6-edb4ac04a8ff\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:5:\\\"email\\\";}}\"}}',0,NULL,1740651136,1740651136),
(34,'default','{\"uuid\":\"c22043bb-e207-44d1-a6be-a8203431ef84\",\"displayName\":\"App\\\\Events\\\\NotificationSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:27:\\\"App\\\\Events\\\\NotificationSent\\\":2:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:9;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":2:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"}}',0,NULL,1740651136,1740651136),
(35,'default','{\"uuid\":\"2d78f783-55fd-4038-9a5d-350651da7841\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"6e63492d-c180-471f-8233-da64db62e0d0\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1740651308,1740651308),
(36,'default','{\"uuid\":\"28e373fb-147a-4ab2-a86e-da4cdd04496a\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"6e63492d-c180-471f-8233-da64db62e0d0\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:5:\\\"email\\\";}}\"}}',0,NULL,1740651308,1740651308),
(37,'default','{\"uuid\":\"9601f78a-84fd-49a5-b908-39ac8bd98aec\",\"displayName\":\"App\\\\Events\\\\NotificationSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:27:\\\"App\\\\Events\\\\NotificationSent\\\":2:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":2:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"}}',0,NULL,1740651308,1740651308),
(38,'default','{\"uuid\":\"0d3a20c8-85f0-4b13-b3cb-72862a2dcf76\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"8262d613-2b0d-4b21-b9d2-50ae7b0efdd8\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1740655126,1740655126),
(39,'default','{\"uuid\":\"e35182a7-6eb0-4a00-ae52-be68cd396780\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"8262d613-2b0d-4b21-b9d2-50ae7b0efdd8\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:5:\\\"email\\\";}}\"}}',0,NULL,1740655126,1740655126),
(40,'default','{\"uuid\":\"79ee4eef-b713-4b2c-8586-070f9528c257\",\"displayName\":\"App\\\\Events\\\\NotificationSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:27:\\\"App\\\\Events\\\\NotificationSent\\\":2:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":2:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"}}',0,NULL,1740655126,1740655126),
(41,'default','{\"uuid\":\"7913712d-d89e-4bbc-a333-953632d89c3c\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"642e8023-37ae-46fa-bd81-b8c0f4749ed3\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1740754989,1740754989),
(42,'default','{\"uuid\":\"d973c768-2ca4-465c-ad7d-657069bbd8be\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"642e8023-37ae-46fa-bd81-b8c0f4749ed3\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:5:\\\"email\\\";}}\"}}',0,NULL,1740754989,1740754989),
(43,'default','{\"uuid\":\"84cc59f9-54a7-4ba7-8bba-ef2699bc220c\",\"displayName\":\"App\\\\Events\\\\NotificationSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:27:\\\"App\\\\Events\\\\NotificationSent\\\":2:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":2:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"}}',0,NULL,1740754989,1740754989),
(44,'default','{\"uuid\":\"d304b727-8029-4a21-83fe-ec6e8352661c\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"b1079fdc-f3b7-46fa-87ec-05af2415088e\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1740913888,1740913888),
(45,'default','{\"uuid\":\"faf6a37e-4401-4fcd-aca1-6e0def2fb50d\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"b1079fdc-f3b7-46fa-87ec-05af2415088e\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:5:\\\"email\\\";}}\"}}',0,NULL,1740913888,1740913888),
(46,'default','{\"uuid\":\"8ece8e85-9b07-4b87-8920-13438cc114f9\",\"displayName\":\"App\\\\Events\\\\NotificationSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:27:\\\"App\\\\Events\\\\NotificationSent\\\":2:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":2:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"}}',0,NULL,1740913888,1740913888),
(47,'default','{\"uuid\":\"52cb9ccd-70f8-4066-946a-18fd5740e5c7\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:9;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"469ad220-207d-4a64-a7ae-935c6bc3b52d\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1740926160,1740926160),
(48,'default','{\"uuid\":\"1076085d-3fe9-4a45-85d6-91cd0fe40770\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:9;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"469ad220-207d-4a64-a7ae-935c6bc3b52d\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:5:\\\"email\\\";}}\"}}',0,NULL,1740926160,1740926160),
(49,'default','{\"uuid\":\"682cbeaf-0191-4579-aff3-c4777be97557\",\"displayName\":\"App\\\\Events\\\\NotificationSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:27:\\\"App\\\\Events\\\\NotificationSent\\\":2:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:9;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":2:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"}}',0,NULL,1740926160,1740926160),
(50,'default','{\"uuid\":\"d6c3f8f0-fb4c-4045-a1ec-08de8545ecda\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"bf9baf93-4804-4e68-a797-63a3a9758bea\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1740971347,1740971347),
(51,'default','{\"uuid\":\"499e5d5e-963a-440f-850c-145c258ec6a7\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"bf9baf93-4804-4e68-a797-63a3a9758bea\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:5:\\\"email\\\";}}\"}}',0,NULL,1740971347,1740971347),
(52,'default','{\"uuid\":\"3ee381cd-2fbe-48dd-a661-6f586edaeaf3\",\"displayName\":\"App\\\\Events\\\\NotificationSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:27:\\\"App\\\\Events\\\\NotificationSent\\\":2:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":2:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"}}',0,NULL,1740971347,1740971347),
(53,'default','{\"uuid\":\"8d7c66f3-8e31-4104-974b-0584472c5aa0\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"0a1af690-d793-4fda-aea5-450491f59a4d\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1741035027,1741035027),
(54,'default','{\"uuid\":\"16e8295d-cedd-48fb-a4e6-77f24c5efd4d\",\"displayName\":\"App\\\\Notifications\\\\SystemAlertNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":3:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}s:2:\\\"id\\\";s:36:\\\"0a1af690-d793-4fda-aea5-450491f59a4d\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:5:\\\"email\\\";}}\"}}',0,NULL,1741035027,1741035027),
(55,'default','{\"uuid\":\"c6ba6331-a042-48b8-a75e-5c9523ba1e49\",\"displayName\":\"App\\\\Events\\\\NotificationSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:27:\\\"App\\\\Events\\\\NotificationSent\\\":2:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"roles\\\";i:1;s:8:\\\"business\\\";}s:10:\\\"connection\\\";s:7:\\\"mariadb\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"App\\\\Notifications\\\\SystemAlertNotification\\\":2:{s:10:\\\"\\u0000*\\u0000message\\\";s:29:\\\"System maintenance scheduled.\\\";s:7:\\\"\\u0000*\\u0000data\\\";a:1:{s:7:\\\"details\\\";s:32:\\\"Server will be down for 2 hours.\\\";}}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"}}',0,NULL,1741035027,1741035027);
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_delegations`
--

DROP TABLE IF EXISTS `leave_delegations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_delegations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `delegate_id` bigint(20) unsigned NOT NULL,
  `leave_request_id` bigint(20) unsigned NOT NULL,
  `duties_delegated` text NOT NULL,
  `delegate_accepted` tinyint(1) NOT NULL DEFAULT 0,
  `accepted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_delegations_employee_id_foreign` (`employee_id`),
  KEY `leave_delegations_delegate_id_foreign` (`delegate_id`),
  KEY `leave_delegations_leave_request_id_foreign` (`leave_request_id`),
  CONSTRAINT `leave_delegations_delegate_id_foreign` FOREIGN KEY (`delegate_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `leave_delegations_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `leave_delegations_leave_request_id_foreign` FOREIGN KEY (`leave_request_id`) REFERENCES `leave_requests` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_delegations`
--

LOCK TABLES `leave_delegations` WRITE;
/*!40000 ALTER TABLE `leave_delegations` DISABLE KEYS */;
/*!40000 ALTER TABLE `leave_delegations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_entitlements`
--

DROP TABLE IF EXISTS `leave_entitlements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_entitlements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `leave_type_id` bigint(20) unsigned NOT NULL,
  `leave_period_id` bigint(20) unsigned NOT NULL,
  `entitled_days` decimal(5,2) NOT NULL DEFAULT 0.00,
  `accrued_days` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_days` decimal(5,2) NOT NULL DEFAULT 0.00,
  `days_taken` decimal(5,2) NOT NULL DEFAULT 0.00,
  `days_remaining` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_entitlements_employee_id_foreign` (`employee_id`),
  KEY `leave_entitlements_leave_type_id_foreign` (`leave_type_id`),
  KEY `leave_entitlements_leave_period_id_foreign` (`leave_period_id`),
  CONSTRAINT `leave_entitlements_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `leave_entitlements_leave_period_id_foreign` FOREIGN KEY (`leave_period_id`) REFERENCES `leave_periods` (`id`),
  CONSTRAINT `leave_entitlements_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_entitlements`
--

LOCK TABLES `leave_entitlements` WRITE;
/*!40000 ALTER TABLE `leave_entitlements` DISABLE KEYS */;
INSERT INTO `leave_entitlements` VALUES
(1,1,1,6,1,21.00,0.00,21.00,0.00,21.00,'2025-02-10 01:11:31','2025-02-10 01:11:31'),
(2,1,3,6,1,21.00,0.00,21.00,0.00,21.00,'2025-02-10 01:11:31','2025-02-10 01:11:31');
/*!40000 ALTER TABLE `leave_entitlements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_periods`
--

DROP TABLE IF EXISTS `leave_periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_periods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `accept_applications` tinyint(1) NOT NULL DEFAULT 1,
  `can_accrue` tinyint(1) NOT NULL DEFAULT 1,
  `restrict_applications_within_dates` tinyint(1) NOT NULL DEFAULT 0,
  `archive` tinyint(1) NOT NULL DEFAULT 0,
  `autocreate` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leave_periods_name_unique` (`name`),
  UNIQUE KEY `leave_periods_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_periods`
--

LOCK TABLES `leave_periods` WRITE;
/*!40000 ALTER TABLE `leave_periods` DISABLE KEYS */;
INSERT INTO `leave_periods` VALUES
(1,1,'Leave Period 2025','leave-period-2025','2025-01-01','2025-12-31',1,1,1,0,0,'2025-02-04 23:35:40','2025-02-04 23:35:40');
/*!40000 ALTER TABLE `leave_periods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_policies`
--

DROP TABLE IF EXISTS `leave_policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_policies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `leave_type_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `job_category_id` bigint(20) unsigned DEFAULT NULL,
  `gender_applicable` enum('all','male','female') NOT NULL DEFAULT 'all',
  `default_days` int(11) NOT NULL,
  `accrual_frequency` enum('monthly','quarterly','yearly') NOT NULL,
  `accrual_amount` decimal(5,2) NOT NULL,
  `max_carryover_days` int(11) NOT NULL DEFAULT 0,
  `prorated_for_new_employees` tinyint(1) NOT NULL DEFAULT 1,
  `minimum_service_days_required` int(11) NOT NULL DEFAULT 0,
  `effective_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_policies_leave_type_id_foreign` (`leave_type_id`),
  KEY `leave_policies_department_id_foreign` (`department_id`),
  KEY `leave_policies_job_category_id_foreign` (`job_category_id`),
  CONSTRAINT `leave_policies_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  CONSTRAINT `leave_policies_job_category_id_foreign` FOREIGN KEY (`job_category_id`) REFERENCES `job_categories` (`id`),
  CONSTRAINT `leave_policies_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_policies`
--

LOCK TABLES `leave_policies` WRITE;
/*!40000 ALTER TABLE `leave_policies` DISABLE KEYS */;
INSERT INTO `leave_policies` VALUES
(1,6,1,1,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(2,6,1,2,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(3,6,1,3,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(4,6,1,4,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(5,6,1,5,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(6,6,2,1,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(7,6,2,2,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(8,6,2,3,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(9,6,2,4,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(10,6,2,5,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(11,6,3,1,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(12,6,3,2,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(13,6,3,3,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(14,6,3,4,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(15,6,3,5,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(16,6,4,1,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(17,6,4,2,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(18,6,4,3,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(19,6,4,4,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(20,6,4,5,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(21,6,5,1,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(22,6,5,2,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(23,6,5,3,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(24,6,5,4,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47'),
(25,6,5,5,'all',20,'monthly',1.75,0,1,60,'2025-01-01','2025-12-31','2025-02-07 03:39:47','2025-02-07 03:39:47');
/*!40000 ALTER TABLE `leave_policies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_requests`
--

DROP TABLE IF EXISTS `leave_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reference_number` varchar(255) NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `business_id` bigint(20) unsigned NOT NULL,
  `leave_type_id` bigint(20) unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` decimal(5,2) NOT NULL,
  `half_day` tinyint(1) NOT NULL DEFAULT 0,
  `half_day_type` enum('first_half','second_half') DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leave_requests_reference_number_unique` (`reference_number`),
  KEY `leave_requests_employee_id_foreign` (`employee_id`),
  KEY `leave_requests_business_id_foreign` (`business_id`),
  KEY `leave_requests_leave_type_id_foreign` (`leave_type_id`),
  KEY `leave_requests_approved_by_foreign` (`approved_by`),
  CONSTRAINT `leave_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `leave_requests_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`),
  CONSTRAINT `leave_requests_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `leave_requests_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_requests`
--

LOCK TABLES `leave_requests` WRITE;
/*!40000 ALTER TABLE `leave_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `leave_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_type_lists`
--

DROP TABLE IF EXISTS `leave_type_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_type_lists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leave_type_lists_name_unique` (`name`),
  UNIQUE KEY `leave_type_lists_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_type_lists`
--

LOCK TABLES `leave_type_lists` WRITE;
/*!40000 ALTER TABLE `leave_type_lists` DISABLE KEYS */;
INSERT INTO `leave_type_lists` VALUES
(1,'Annual Leave','annual-leave',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(2,'Sick Leave','sick-leave',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(3,'Maternity Leave','maternity-leave',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(4,'Paternity Leave','paternity-leave',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(5,'Compassionate Leave','compassionate-leave',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(6,'Study Leave','study-leave',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(7,'Unpaid Leave','unpaid-leave',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(8,'Public Holidays','public-holidays',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(9,'Sabbatical Leave','sabbatical-leave',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(10,'Marriage Leave','marriage-leave',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(11,'Bereavement Leave','bereavement-leave',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(12,'Adoption Leave','adoption-leave',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(13,'Relocation Leave','relocation-leave',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(14,'Childcare Leave','childcare-leave',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(15,'Voting Leave','voting-leave',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(16,'Jury Duty Leave','jury-duty-leave',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(17,'Military Leave','military-leave',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(18,'Emergency Leave','emergency-leave',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(19,'Volunteer Leave','volunteer-leave',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(20,'Wellness Leave','wellness-leave',1,'2025-02-04 03:34:17','2025-02-04 03:34:17');
/*!40000 ALTER TABLE `leave_type_lists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_types`
--

DROP TABLE IF EXISTS `leave_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `requires_approval` tinyint(1) NOT NULL DEFAULT 1,
  `is_paid` tinyint(1) NOT NULL DEFAULT 1,
  `allowance_accruable` tinyint(1) NOT NULL DEFAULT 1,
  `allows_half_day` tinyint(1) NOT NULL DEFAULT 1,
  `requires_attachment` tinyint(1) NOT NULL DEFAULT 0,
  `max_continuous_days` int(11) DEFAULT NULL,
  `min_notice_days` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leave_types_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_types`
--

LOCK TABLES `leave_types` WRITE;
/*!40000 ALTER TABLE `leave_types` DISABLE KEYS */;
INSERT INTO `leave_types` VALUES
(6,1,'Annual Leave','annual-leave','Annual Leave',1,1,0,0,0,20,5,1,'2025-02-07 03:39:47','2025-02-07 03:39:47');
/*!40000 ALTER TABLE `leave_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loan_repayments`
--

DROP TABLE IF EXISTS `loan_repayments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `loan_repayments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `loan_id` bigint(20) unsigned NOT NULL,
  `repayment_date` date NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loan_repayments_loan_id_foreign` (`loan_id`),
  CONSTRAINT `loan_repayments_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loan_repayments`
--

LOCK TABLES `loan_repayments` WRITE;
/*!40000 ALTER TABLE `loan_repayments` DISABLE KEYS */;
/*!40000 ALTER TABLE `loan_repayments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loans`
--

DROP TABLE IF EXISTS `loans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `loans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `term_months` int(11) NOT NULL DEFAULT 12,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loans_employee_id_foreign` (`employee_id`),
  CONSTRAINT `loans_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loans`
--

LOCK TABLES `loans` WRITE;
/*!40000 ALTER TABLE `loans` DISABLE KEYS */;
/*!40000 ALTER TABLE `loans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `locations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `company_size` varchar(255) DEFAULT NULL,
  `registration_no` varchar(255) DEFAULT NULL,
  `tax_pin_no` varchar(255) DEFAULT NULL,
  `business_license_no` varchar(255) DEFAULT NULL,
  `physical_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `locations_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locations`
--

LOCK TABLES `locations` WRITE;
/*!40000 ALTER TABLE `locations` DISABLE KEYS */;
INSERT INTO `locations` VALUES
(3,1,'Location 1','location-1','1-10',NULL,NULL,NULL,'Some location in Location1','2025-02-05 00:13:08','2025-02-05 00:24:48'),
(4,1,'Location 2','location-2','1-10',NULL,NULL,NULL,'Some location in Location 2','2025-02-05 00:15:44','2025-02-05 00:15:44'),
(5,1,'Location 3','location-3','1-10',NULL,NULL,NULL,'Location 3 address','2025-02-07 03:47:37','2025-02-07 03:47:48');
/*!40000 ALTER TABLE `locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `uuid` uuid DEFAULT NULL,
  `collection_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `disk` varchar(255) NOT NULL,
  `conversions_disk` varchar(255) DEFAULT NULL,
  `size` bigint(20) unsigned NOT NULL,
  `manipulations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`manipulations`)),
  `custom_properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`custom_properties`)),
  `generated_conversions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`generated_conversions`)),
  `responsive_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`responsive_images`)),
  `order_column` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_uuid_unique` (`uuid`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `media_order_column_index` (`order_column`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
INSERT INTO `media` VALUES
(1,'App\\Models\\User',1,'95fbc06a-1852-430e-a9f9-a9cbd32b09d7','avatars','media-libraryCpl2Je','media-libraryCpl2Je','image/png','public','public',393,'[]','[]','[]','[]',1,'2025-02-04 03:34:48','2025-02-04 03:34:48'),
(2,'App\\Models\\Business',1,'3d0425ab-c979-4dc8-ac36-07bc6b7cb9e9','businesses','logo 2','logo-2.png','image/png','public','public',11294,'[]','[]','[]','[]',1,'2025-02-04 03:35:20','2025-02-04 03:35:20'),
(3,'App\\Models\\User',2,'3edd2649-6199-405e-aad6-fc3e53c388bc','avatars','person1','person1.jpeg','image/jpeg','public','public',5220,'[]','[]','[]','[]',1,'2025-02-04 23:11:27','2025-02-04 23:11:27'),
(4,'App\\Models\\Employee',1,'41b0ad3e-6df7-428d-8f36-18ff4833a56a','academic_files','New - Employee Bio Data Form','New---Employee-Bio-Data-Form.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document','public','public',164215,'[]','[]','[]','[]',1,'2025-02-04 23:11:27','2025-02-04 23:11:27'),
(5,'App\\Models\\User',3,'63503b37-5589-4af4-9047-996e568c2304','avatars','person1','person1.jpeg','image/jpeg','public','public',5220,'[]','[]','[]','[]',1,'2025-02-04 23:22:26','2025-02-04 23:22:26'),
(6,'App\\Models\\Employee',2,'05ffaad3-0edc-4005-88a6-860e83d6c23f','academic_files','New - Employee Bio Data Form','New---Employee-Bio-Data-Form.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document','public','public',164215,'[]','[]','[]','[]',1,'2025-02-04 23:22:26','2025-02-04 23:22:26'),
(7,'App\\Models\\User',4,'10276f1d-2d61-4083-8870-e67f8c6f717e','avatars','person4','person4.jpeg','image/jpeg','public','public',3522,'[]','[]','[]','[]',1,'2025-02-04 23:30:46','2025-02-04 23:30:46'),
(8,'App\\Models\\Employee',3,'2356b19e-cdbd-4935-b554-0ca508cbf309','academic_files','New - Employee Bio Data Form','New---Employee-Bio-Data-Form.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document','public','public',164215,'[]','[]','[]','[]',1,'2025-02-04 23:30:46','2025-02-04 23:30:46'),
(10,'App\\Models\\User',6,'4e6672a8-2abc-4886-8bb1-d4d796e4eb5e','avatars','media-libraryfUR2gY','media-libraryfUR2gY','image/png','public','public',338,'[]','[]','[]','[]',1,'2025-02-11 00:18:24','2025-02-11 00:18:24'),
(11,'App\\Models\\User',7,'bd37c5a4-8c8a-43c2-908d-6200ca2782d6','avatars','media-library4xDc9T','media-library4xDc9T','image/png','public','public',339,'[]','[]','[]','[]',1,'2025-02-11 00:32:30','2025-02-11 00:32:30'),
(12,'App\\Models\\User',8,'f5547c10-162f-4d31-9361-0fb751a3e28f','avatars','media-library7ZKF9t','media-library7ZKF9t','image/png','public','public',338,'[]','[]','[]','[]',1,'2025-02-11 00:33:39','2025-02-11 00:33:39'),
(13,'App\\Models\\Application',1,'52cc4cd2-ab83-49fd-aeec-b1dc1c177d52','applications','New - Employee Bio Data Form','New---Employee-Bio-Data-Form.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document','public','public',164215,'[]','[]','[]','[]',1,'2025-02-11 01:04:24','2025-02-11 01:04:24'),
(14,'App\\Models\\Application',2,'386aa516-51a8-4e33-9f43-f6dedfe1b559','applications','Head Of Department-Payslip for the month of January (01), 2025','Head-Of-Department-Payslip-for-the-month-of-January-(01),-2025.pdf','application/pdf','public','public',14126,'[]','[]','[]','[]',1,'2025-02-11 01:04:52','2025-02-11 01:04:52'),
(15,'App\\Models\\User',9,'40cd77e2-3d6b-4c0b-8412-a380c5288e7f','avatars','media-library2sBRIC','media-library2sBRIC','image/png','public','public',389,'[]','[]','[]','[]',1,'2025-02-18 03:22:59','2025-02-18 03:22:59'),
(16,'App\\Models\\Business',2,'2db3be86-a22e-4c33-ae5f-7031a0146b7b','businesses','avatar','avatar.png','image/png','public','public',53546,'[]','[]','[]','[]',1,'2025-02-18 03:23:39','2025-02-18 03:23:39'),
(17,'App\\Models\\User',10,'aaf85ffd-f1ef-4211-acf8-c6cafd9eaa59','avatars','person2','person2.jpeg','image/jpeg','public','public',5716,'[]','[]','[]','[]',1,'2025-02-18 03:32:39','2025-02-18 03:32:39'),
(18,'App\\Models\\Employee',4,'9c0f1532-4d80-4320-bc67-adf37ed3d019','academic_files','Letterhead','Letterhead.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document','public','public',37920,'[]','[]','[]','[]',1,'2025-02-18 03:32:39','2025-02-18 03:32:39'),
(19,'App\\Models\\User',11,'7ed667fa-b610-4c47-a3ed-bccd27c27f6a','avatars','media-libraryshJFk2','media-libraryshJFk2','image/png','public','public',399,'[]','[]','[]','[]',1,'2025-02-18 05:28:51','2025-02-18 05:28:51'),
(20,'App\\Models\\Business',3,'9a3702a5-9ae0-4d11-8377-bde92cb85db5','businesses','avatar','avatar.png','image/png','public','public',53546,'[]','[]','[]','[]',1,'2025-02-18 05:29:22','2025-02-18 05:29:22'),
(21,'App\\Models\\User',12,'3af8a8dc-c723-477b-bb3b-18e4aad90977','avatars','media-libraryxl1lHJ','media-libraryxl1lHJ','image/png','public','public',388,'[]','[]','[]','[]',1,'2025-02-27 11:12:18','2025-02-27 11:12:18'),
(22,'App\\Models\\Business',4,'b7deda45-3c13-4b95-969c-8d1a93758f5c','businesses','qrCode','qrCode.jpeg','image/jpeg','public','public',33217,'[]','[]','[]','[]',1,'2025-02-27 11:13:09','2025-02-27 11:13:09');
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2024_12_28_020422_create_permission_tables',1),
(5,'2024_12_28_020618_create_media_table',1),
(6,'2024_12_28_020943_create_statuses_table',1),
(7,'2024_12_28_024918_create_businesses_table',1),
(8,'2024_12_28_024919_create_industries_table',1),
(9,'2024_12_28_025027_create_modules_table',1),
(10,'2025_01_14_025030_create_job_categories_table',1),
(11,'2025_01_14_025032_create_shifts_table',1),
(12,'2025_01_14_052658_create_departments_table',1),
(13,'2025_01_14_083726_create_employees_table',1),
(14,'2025_01_14_083727_create_spouses_table',1),
(15,'2025_01_14_083728_create_emergency_contacts_table',1),
(16,'2025_01_14_083729_create_academic_qualifications_table',1),
(17,'2025_01_14_083730_create_previous_employments_table',1),
(18,'2025_01_16_113024_create_employee_family_members_table',1),
(19,'2025_01_16_113239_create_employment_details_table',1),
(20,'2025_01_16_113352_create_employee_payment_details_table',1),
(21,'2025_01_16_113502_create_employee_contact_details_table',1),
(22,'2025_01_16_113640_create_employee_documents_table',1),
(23,'2025_01_17_014648_create_payroll_formulas_table',1),
(24,'2025_01_17_020359_create_payroll_formula_brackets_table',1),
(25,'2025_01_17_050009_create_reliefs_table',1),
(26,'2025_01_17_050011_create_allowances_table',1),
(27,'2025_01_17_050012_create_employee_allowances_table',1),
(30,'2025_01_17_050015_create_leave_type_lists_table',1),
(31,'2025_01_23_100649_create_leave_types_table',1),
(32,'2025_01_23_100656_create_leave_policies_table',1),
(33,'2025_01_23_123044_create_leave_requests_table',1),
(34,'2025_01_23_130905_create_leave_delegations_table',1),
(35,'2025_01_23_131303_create_leave_periods_table',1),
(36,'2025_01_23_143454_create_leave_entitlements_table',1),
(37,'2025_01_24_025902_create_clients_table',1),
(38,'2025_01_27_063018_create_access_requests_table',1),
(39,'2025_01_30_082503_create_locations_table',1),
(40,'2025_01_30_165752_create_payrolls_table',1),
(41,'2025_02_02_085754_create_employee_payrolls_table',1),
(42,'2025_02_05_025648_add_location_id_to_employees_table',2),
(43,'2025_01_17_050010_create_employee_reliefs_table',3),
(44,'2025_02_07_042439_create_advances_table',4),
(47,'2025_02_07_052854_create_loans_table',5),
(48,'2025_02_07_053852_create_loan_repayments_table',5),
(49,'2025_02_08_184451_create_peoples_table',5),
(72,'2025_02_10_210013_create_skills_table',6),
(73,'2025_02_10_210016_create_job_posts_table',6),
(74,'2025_02_10_211642_create_applicants_table',6),
(75,'2025_02_10_211643_create_applicant_skills_table',6),
(76,'2025_02_10_213257_create_applications_table',6),
(77,'2025_02_10_221803_create_interviews_table',6),
(78,'2025_02_10_224847_create_interview_feedback_table',6),
(79,'2025_02_11_031751_add_timestamps_to_applicants_table',7),
(80,'2025_02_11_065712_update_payrolls_table',8),
(81,'2025_02_14_044514_add_unique_constraint_to_payrolls',9),
(85,'2025_02_16_084350_create_attendances_table',10),
(86,'2025_02_16_104550_create_overtimes_table',10),
(88,'2025_02_12_070910_create_tasks_table',11),
(89,'2025_02_24_040831_create_notifications_table',12),
(90,'2025_02_24_041321_create_notification_preferences_table',12),
(91,'2025_02_24_041640_create_notification_logs_table',12),
(94,'2025_01_17_050013_create_deductions_table',13),
(95,'2025_01_17_050014_create_employee_deductions_table',13),
(97,'2025_03_03_090714_add_deductions_to_employee_payrolls',14);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES
(7,'App\\Models\\User',1),
(11,'App\\Models\\User',1),
(11,'App\\Models\\User',2),
(11,'App\\Models\\User',3),
(11,'App\\Models\\User',4),
(12,'App\\Models\\User',6),
(12,'App\\Models\\User',7),
(12,'App\\Models\\User',8),
(7,'App\\Models\\User',9),
(6,'App\\Models\\User',10),
(11,'App\\Models\\User',10),
(7,'App\\Models\\User',11),
(7,'App\\Models\\User',12);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price_monthly` decimal(10,2) NOT NULL,
  `price_yearly` decimal(10,2) NOT NULL,
  `is_core` tinyint(1) NOT NULL DEFAULT 0,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`features`)),
  `icon` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modules_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modules`
--

LOCK TABLES `modules` WRITE;
/*!40000 ALTER TABLE `modules` DISABLE KEYS */;
INSERT INTO `modules` VALUES
(1,'Core HR Management','core-hr-management','Essential HR features including employee management, attendance, and basic reporting',0.00,0.00,1,'[\"Employee Database\",\"Attendance Management\",\"Leave Management\",\"Basic Reports\",\"Document Management\"]','people-fill','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(2,'Payroll Management','payroll-management','Complete payroll processing system with tax calculations and compliance',49.99,499.99,0,'[\"Salary Processing\",\"Tax Calculations\",\"Payslip Generation\",\"Statutory Compliance\",\"Multiple Payment Methods\",\"Payroll Reports\"]','wallet2','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(3,'Recruitment & Onboarding','recruitment-onboarding','End-to-end recruitment solution from job posting to onboarding',39.99,399.99,0,'[\"Job Posting Management\",\"Applicant Tracking\",\"Interview Scheduling\",\"Candidate Assessment\",\"Onboarding Workflow\",\"Document Collection\"]','person-plus-fill','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(4,'Performance Management','performance-management','Complete performance evaluation and goal tracking system',29.99,299.99,0,'[\"Goal Setting & Tracking\",\"Performance Reviews\",\"360\\u00b0 Feedback\",\"Skills Assessment\",\"Development Plans\",\"Performance Analytics\"]','graph-up-arrow','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(5,'Learning Management','learning-management','Employee training and development platform',34.99,349.99,0,'[\"Course Management\",\"Training Schedules\",\"Learning Paths\",\"Assessment Tools\",\"Certification Tracking\",\"Training Reports\"]','journal-bookmark','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(6,'Time & Attendance','time-attendance','Advanced time tracking and attendance management',24.99,249.99,0,'[\"Time Tracking\",\"Shift Management\",\"Overtime Calculation\",\"Leave Planning\",\"Attendance Reports\",\"Mobile Check-in\"]','clock-fill','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(7,'Asset Management','asset-management','Track and manage company assets and resources',19.99,199.99,0,'[\"Asset Tracking\",\"Maintenance Scheduling\",\"Asset Assignment\",\"Inventory Management\",\"Asset Reports\",\"Depreciation Tracking\"]','box-seam','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(8,'Employee Self-Service','employee-self-service','Portal for employees to manage their information and requests',14.99,149.99,0,'[\"Profile Management\",\"Leave Requests\",\"Expense Claims\",\"Document Access\",\"Payslip Download\",\"Benefits Enrollment\"]','person-workspace','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(9,'CRM Integration','crm-integration','Customer relationship management integration with HR',44.99,449.99,0,'[\"Contact Management\",\"Lead Tracking\",\"Sales Pipeline\",\"Customer Support\",\"Email Integration\",\"Analytics & Reports\"]','people','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(10,'Project Management','project-management','Project planning and resource management tools',39.99,399.99,0,'[\"Project Planning\",\"Task Management\",\"Resource Allocation\",\"Time Tracking\",\"Project Reports\",\"Team Collaboration\"]','clipboard-data','2025-02-04 03:34:17','2025-02-04 03:34:17');
/*!40000 ALTER TABLE `modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_logs`
--

DROP TABLE IF EXISTS `notification_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `notification_type` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `sent_at` timestamp NULL DEFAULT NULL,
  `channel` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `notification_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_logs`
--

LOCK TABLES `notification_logs` WRITE;
/*!40000 ALTER TABLE `notification_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_preferences`
--

DROP TABLE IF EXISTS `notification_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_preferences` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `email` tinyint(1) NOT NULL DEFAULT 1,
  `database` tinyint(1) NOT NULL DEFAULT 1,
  `sms` tinyint(1) NOT NULL DEFAULT 0,
  `slack` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_preferences_user_id_foreign` (`user_id`),
  CONSTRAINT `notification_preferences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_preferences`
--

LOCK TABLES `notification_preferences` WRITE;
/*!40000 ALTER TABLE `notification_preferences` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` uuid NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `overtimes`
--

DROP TABLE IF EXISTS `overtimes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `overtimes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `business_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `overtime_hours` decimal(5,2) NOT NULL DEFAULT 0.00,
  `rate` decimal(8,2) NOT NULL,
  `total_pay` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `approved_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `overtimes_employee_id_foreign` (`employee_id`),
  KEY `overtimes_business_id_foreign` (`business_id`),
  KEY `overtimes_approved_by_foreign` (`approved_by`),
  CONSTRAINT `overtimes_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `overtimes_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `overtimes_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `overtimes`
--

LOCK TABLES `overtimes` WRITE;
/*!40000 ALTER TABLE `overtimes` DISABLE KEYS */;
INSERT INTO `overtimes` VALUES
(1,1,1,'2025-02-18',4.00,1.50,6.00,'Sorting out emergency company paper work for tomorrows meeting.',1,'2025-02-18 01:01:54','2025-02-18 01:01:54');
/*!40000 ALTER TABLE `overtimes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_formula_brackets`
--

DROP TABLE IF EXISTS `payroll_formula_brackets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll_formula_brackets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payroll_formula_id` bigint(20) unsigned NOT NULL,
  `min` decimal(15,2) NOT NULL,
  `max` decimal(15,2) DEFAULT NULL,
  `rate` decimal(5,2) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payroll_formula_brackets_payroll_formula_id_foreign` (`payroll_formula_id`),
  CONSTRAINT `payroll_formula_brackets_payroll_formula_id_foreign` FOREIGN KEY (`payroll_formula_id`) REFERENCES `payroll_formulas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_formula_brackets`
--

LOCK TABLES `payroll_formula_brackets` WRITE;
/*!40000 ALTER TABLE `payroll_formula_brackets` DISABLE KEYS */;
INSERT INTO `payroll_formula_brackets` VALUES
(1,1,0.00,24000.00,10.00,NULL,NULL,NULL),
(2,1,24001.00,32333.00,25.00,NULL,NULL,NULL),
(3,1,32334.00,500000.00,30.00,NULL,NULL,NULL),
(4,1,500001.00,NULL,35.00,NULL,NULL,NULL),
(5,2,0.00,5999.00,NULL,150.00,NULL,NULL),
(6,2,6000.00,7999.00,NULL,300.00,NULL,NULL),
(7,2,8000.00,11999.00,NULL,400.00,NULL,NULL),
(8,2,12000.00,14999.00,NULL,500.00,NULL,NULL),
(9,2,15000.00,19999.00,NULL,600.00,NULL,NULL),
(10,2,20000.00,24999.00,NULL,750.00,NULL,NULL),
(11,2,25000.00,NULL,NULL,1700.00,NULL,NULL),
(12,5,0.00,7000.00,6.00,0.00,'2025-02-05 04:38:29','2025-02-05 04:38:29'),
(13,5,7000.00,14000.00,6.00,0.00,'2025-02-05 04:38:29','2025-02-05 04:38:29'),
(14,6,0.00,7000.00,6.00,0.00,'2025-02-27 06:42:50','2025-02-27 06:42:50'),
(15,6,7000.00,14000.00,6.00,0.00,'2025-02-27 06:42:50','2025-02-27 06:42:50');
/*!40000 ALTER TABLE `payroll_formula_brackets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_formulas`
--

DROP TABLE IF EXISTS `payroll_formulas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll_formulas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `formula_type` varchar(255) DEFAULT NULL,
  `calculation_basis` enum('basic_pay','gross_pay','cash_pay','taxable_pay') NOT NULL,
  `formula_expression` varchar(255) DEFAULT NULL,
  `is_progressive` tinyint(1) NOT NULL DEFAULT 0,
  `minimum_amount` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_formulas_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_formulas`
--

LOCK TABLES `payroll_formulas` WRITE;
/*!40000 ALTER TABLE `payroll_formulas` DISABLE KEYS */;
INSERT INTO `payroll_formulas` VALUES
(1,NULL,'PAYE','paye','rate','taxable_pay',NULL,1,NULL,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(2,NULL,'NHIF','nhif','amount','gross_pay',NULL,1,NULL,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(3,NULL,'Housing Levy','housing-levy','rate','gross_pay',NULL,0,1.50,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(4,NULL,'Personal Relief','personal-relief','fixed','taxable_pay',NULL,0,2400.00,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(5,NULL,'NSSF','nssf','rate','gross_pay',NULL,1,NULL,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(6,1,'NSSF','nssf-1',NULL,'gross_pay',NULL,1,NULL,'2025-02-27 06:42:50','2025-02-27 06:42:50');
/*!40000 ALTER TABLE `payroll_formulas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payrolls`
--

DROP TABLE IF EXISTS `payrolls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payrolls` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned NOT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `payroll_type` varchar(255) NOT NULL,
  `currency` varchar(255) NOT NULL,
  `staff` int(11) NOT NULL,
  `payrun_year` year(4) NOT NULL,
  `payrun_month` tinyint(3) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_unique` (`payrun_year`,`payrun_month`,`business_id`,`location_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payrolls`
--

LOCK TABLES `payrolls` WRITE;
/*!40000 ALTER TABLE `payrolls` DISABLE KEYS */;
INSERT INTO `payrolls` VALUES
(2,1,NULL,'monthly','KSH',3,2025,1,'2025-02-05 01:53:40','2025-02-05 01:53:40'),
(3,1,NULL,'monthly','KSH',3,2024,12,'2025-02-06 00:42:01','2025-02-06 00:42:01'),
(4,1,NULL,'monthly','KSH',2,2024,11,'2025-02-07 03:49:28','2025-02-07 03:49:28'),
(5,1,NULL,'monthly','KSH',2,2025,3,'2025-02-11 04:05:11','2025-02-11 04:05:11'),
(6,1,NULL,'monthly','KSH',1,2025,2,'2025-02-18 03:42:32','2025-02-18 03:42:32'),
(7,1,NULL,'monthly','KSH',1,2025,2,'2025-02-18 03:45:17','2025-02-18 03:45:17'),
(8,1,5,'monthly','KSH',1,2025,2,'2025-02-18 03:50:35','2025-02-18 03:50:35'),
(27,1,NULL,'monthly','KSH',2,2025,3,'2025-03-04 00:54:34','2025-03-04 00:54:34');
/*!40000 ALTER TABLE `payrolls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `previous_employments`
--

DROP TABLE IF EXISTS `previous_employments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `previous_employments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `employer_name` varchar(255) NOT NULL,
  `business_or_profession` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `capacity_employed` varchar(255) NOT NULL,
  `reason_for_leaving` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `previous_employments_employee_id_foreign` (`employee_id`),
  CONSTRAINT `previous_employments_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `previous_employments`
--

LOCK TABLES `previous_employments` WRITE;
/*!40000 ALTER TABLE `previous_employments` DISABLE KEYS */;
INSERT INTO `previous_employments` VALUES
(1,1,'Employer de\' IT','InfoWars Tech Abys','Work Adress 9826','Tech Lead','Just because...','2022-02-23','2024-02-19','2025-02-04 23:11:27','2025-02-04 23:11:27'),
(2,2,'Employer de\' IT','InfoWars Tech Abys','Work Adress 9826','Tech Lead','Just because...','2022-02-23','2024-02-19','2025-02-04 23:22:26','2025-02-04 23:22:26'),
(3,3,'ABC Corp','Accounting','123 Business St, LA','Accountant','N/A','2015-01-01','2024-02-20','2025-02-04 23:30:46','2025-02-04 23:30:46');
/*!40000 ALTER TABLE `previous_employments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reliefs`
--

DROP TABLE IF EXISTS `reliefs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reliefs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `tax_application` enum('before_tax','after_tax') NOT NULL,
  `relief_type` enum('rate','fixed') NOT NULL,
  `comparison_method` enum('least','greatest') DEFAULT NULL,
  `rate_percentage` decimal(8,2) DEFAULT NULL,
  `fixed_amount` decimal(15,2) DEFAULT NULL,
  `maximum_relief` decimal(15,2) DEFAULT NULL,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reliefs_slug_unique` (`slug`),
  KEY `reliefs_business_id_foreign` (`business_id`),
  CONSTRAINT `reliefs_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reliefs`
--

LOCK TABLES `reliefs` WRITE;
/*!40000 ALTER TABLE `reliefs` DISABLE KEYS */;
/*!40000 ALTER TABLE `reliefs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'super-admin','web','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(2,'admin','web','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(3,'hr','web','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(4,'finance','web','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(5,'it','web','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(6,'employee','web','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(7,'business-admin','web','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(8,'business-hr','web','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(9,'business-finance','web','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(10,'business-it','web','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(11,'business-employee','web','2025-02-04 03:34:17','2025-02-04 03:34:17'),
(12,'applicant','web','2025-02-04 03:34:17','2025-02-04 03:34:17');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES
('5C9pWrNnVDGZlAESQ4SEi7gqlM32XbOQphCkT77K',NULL,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64; rv:136.0) Gecko/20100101 Firefox/136.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMUpHY1NZQUJad0Y3YjgwUzlIQUtmWGNtemRwRmdLYjBsT2JFcUZ5QyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjQ6Imh0dHA6Ly9hbXNvbC5sb2NhbC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1741151130),
('XAgODwXbqiy0JP91qfbViVnalg4qY1ESo6RyFjhA',1,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64; rv:136.0) Gecko/20100101 Firefox/136.0','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiZ0pWelNqNWVJdDFWbEVnaW9WRjdZbHlNR2YzZEZDcGFTZjFDTElyRSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTU6Imh0dHA6Ly9hbXNvbC5sb2NhbC9idXNpbmVzcy9hbnphci1rZS9wYXlyb2xsL2RlZHVjdGlvbnMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MjA6ImFjdGl2ZV9idXNpbmVzc19zbHVnIjtzOjg6ImFuemFyLWtlIjtzOjExOiJhY3RpdmVfcm9sZSI7czoxNDoiYnVzaW5lc3MtYWRtaW4iO30=',1741059177);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shifts`
--

DROP TABLE IF EXISTS `shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shifts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shifts_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shifts`
--

LOCK TABLES `shifts` WRITE;
/*!40000 ALTER TABLE `shifts` DISABLE KEYS */;
INSERT INTO `shifts` VALUES
(1,1,'Morning Shift','morning-shift','08:00:00','16:00:00','Standard morning shift',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(2,1,'Afternoon Shift','afternoon-shift','16:00:00','00:00:00','Standard afternoon shift',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(3,1,'Night Shift','night-shift','00:00:00','08:00:00','Standard night shift',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(4,1,'Flexible Shift','flexible-shift','10:00:00','18:00:00','Flexible shift for employees',1,'2025-02-04 03:34:17','2025-02-04 03:34:17'),
(5,1,'Weekend Shift','weekend-shift','09:00:00','17:00:00','Shift during weekends',1,'2025-02-04 03:34:17','2025-02-04 03:34:17');
/*!40000 ALTER TABLE `shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `skills`
--

DROP TABLE IF EXISTS `skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `skills` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `skills_name_unique` (`name`),
  UNIQUE KEY `skills_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `skills`
--

LOCK TABLES `skills` WRITE;
/*!40000 ALTER TABLE `skills` DISABLE KEYS */;
/*!40000 ALTER TABLE `skills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spouses`
--

DROP TABLE IF EXISTS `spouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `spouses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `surname` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `date_of_birth` date NOT NULL,
  `national_id` varchar(255) NOT NULL,
  `current_employer` varchar(255) DEFAULT NULL,
  `spouse_contact` varchar(255) DEFAULT NULL,
  `spouse_postal_address` varchar(255) DEFAULT NULL,
  `spouse_physical_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `spouses_employee_id_foreign` (`employee_id`),
  CONSTRAINT `spouses_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spouses`
--

LOCK TABLES `spouses` WRITE;
/*!40000 ALTER TABLE `spouses` DISABLE KEYS */;
INSERT INTO `spouses` VALUES
(1,1,'Melinda','Knowles','Korean','1995-02-23','325874','Self Employed','+254755123984','00100','Home Address 21763','2025-02-04 23:11:27','2025-02-04 23:11:27'),
(2,2,'Doe','Jane','Ann','1988-09-20','0987654321','XYZ Corp','+2541234567892','1234 Elm St, NY','1234 Elm St, NY','2025-02-04 23:22:26','2025-02-04 23:22:26'),
(3,3,'Smith','David','John','1985-06-05','6677889900','ABC Corp','+2541245678903','789 Oak St, CA','789 Oak St, CA','2025-02-04 23:30:46','2025-02-04 23:30:46');
/*!40000 ALTER TABLE `spouses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statuses`
--

DROP TABLE IF EXISTS `statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `statuses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `reason` text DEFAULT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `statuses_model_type_model_id_index` (`model_type`,`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statuses`
--

LOCK TABLES `statuses` WRITE;
/*!40000 ALTER TABLE `statuses` DISABLE KEYS */;
INSERT INTO `statuses` VALUES
(1,'setup',NULL,'App\\Models\\User',1,'2025-02-04 03:34:48','2025-02-04 03:34:48'),
(2,'module',NULL,'App\\Models\\Business',1,'2025-02-04 03:35:20','2025-02-04 03:35:20'),
(3,'module',NULL,'App\\Models\\User',1,'2025-02-04 03:35:21','2025-02-04 03:35:21'),
(4,'active',NULL,'App\\Models\\User',1,'2025-02-04 03:35:38','2025-02-04 03:35:38'),
(5,'active',NULL,'App\\Models\\Business',1,'2025-02-04 03:35:38','2025-02-04 03:35:38'),
(6,'active',NULL,'App\\Models\\User',2,'2025-02-04 23:11:27','2025-02-04 23:11:27'),
(7,'active',NULL,'App\\Models\\User',3,'2025-02-04 23:22:26','2025-02-04 23:22:26'),
(8,'active',NULL,'App\\Models\\User',4,'2025-02-04 23:30:46','2025-02-04 23:30:46'),
(9,'active',NULL,'App\\Models\\LeavePeriod',1,'2025-02-04 23:35:40','2025-02-04 23:35:40'),
(10,'active',NULL,'App\\Models\\Location',3,'2025-02-05 00:13:08','2025-02-05 00:13:08'),
(11,'active',NULL,'App\\Models\\Location',4,'2025-02-05 00:15:44','2025-02-05 00:15:44'),
(12,'active',NULL,'App\\Models\\Location',3,'2025-02-05 00:24:48','2025-02-05 00:24:48'),
(13,'active',NULL,'App\\Models\\Advance',1,'2025-02-07 02:21:15','2025-02-07 02:21:15'),
(14,'active',NULL,'App\\Models\\Loan',1,'2025-02-07 02:58:45','2025-02-07 02:58:45'),
(15,'active',NULL,'App\\Models\\Location',5,'2025-02-07 03:47:37','2025-02-07 03:47:37'),
(16,'active',NULL,'App\\Models\\Location',5,'2025-02-07 03:47:48','2025-02-07 03:47:48'),
(17,'active',NULL,'App\\Models\\Advance',2,'2025-02-07 03:53:10','2025-02-07 03:53:10'),
(18,'active',NULL,'App\\Models\\Loan',2,'2025-02-07 03:54:05','2025-02-07 03:54:05'),
(19,'open',NULL,'App\\Models\\JobPost',1,'2025-02-10 22:55:57','2025-02-10 22:55:57'),
(20,'open',NULL,'App\\Models\\JobPost',2,'2025-02-10 22:57:03','2025-02-10 22:57:03'),
(21,'open',NULL,'App\\Models\\JobPost',3,'2025-02-10 22:58:53','2025-02-10 22:58:53'),
(23,'active',NULL,'App\\Models\\User',6,'2025-02-11 00:18:24','2025-02-11 00:18:24'),
(24,'active',NULL,'App\\Models\\Applicant',1,'2025-02-11 00:18:24','2025-02-11 00:18:24'),
(25,'active',NULL,'App\\Models\\User',7,'2025-02-11 00:32:30','2025-02-11 00:32:30'),
(26,'active',NULL,'App\\Models\\Applicant',2,'2025-02-11 00:32:30','2025-02-11 00:32:30'),
(27,'active',NULL,'App\\Models\\User',8,'2025-02-11 00:33:38','2025-02-11 00:33:38'),
(28,'active',NULL,'App\\Models\\Applicant',3,'2025-02-11 00:33:39','2025-02-11 00:33:39'),
(29,'applied',NULL,'App\\Models\\Application',1,'2025-02-11 00:33:39','2025-02-11 00:33:39'),
(30,'applied',NULL,'App\\Models\\Application',2,'2025-02-11 00:33:39','2025-02-11 00:33:39'),
(31,'scheduled',NULL,'App\\Models\\Interview',6,'2025-02-11 00:33:39','2025-02-11 00:33:39'),
(32,'scheduled',NULL,'App\\Models\\Interview',7,'2025-02-11 00:33:39','2025-02-11 00:33:39'),
(33,'approved',NULL,'App\\Models\\Overtime',1,'2025-02-18 01:01:54','2025-02-18 01:01:54'),
(34,'setup',NULL,'App\\Models\\User',9,'2025-02-18 03:22:59','2025-02-18 03:22:59'),
(35,'module',NULL,'App\\Models\\Business',2,'2025-02-18 03:23:39','2025-02-18 03:23:39'),
(36,'module',NULL,'App\\Models\\User',9,'2025-02-18 03:23:39','2025-02-18 03:23:39'),
(37,'active',NULL,'App\\Models\\User',9,'2025-02-18 03:23:48','2025-02-18 03:23:48'),
(38,'active',NULL,'App\\Models\\Business',2,'2025-02-18 03:23:48','2025-02-18 03:23:48'),
(39,'active',NULL,'App\\Models\\User',10,'2025-02-18 03:32:39','2025-02-18 03:32:39'),
(40,'setup',NULL,'App\\Models\\User',11,'2025-02-18 05:28:51','2025-02-18 05:28:51'),
(41,'module',NULL,'App\\Models\\Business',3,'2025-02-18 05:29:22','2025-02-18 05:29:22'),
(42,'module',NULL,'App\\Models\\User',11,'2025-02-18 05:29:22','2025-02-18 05:29:22'),
(43,'active',NULL,'App\\Models\\User',11,'2025-02-18 05:29:28','2025-02-18 05:29:28'),
(44,'active',NULL,'App\\Models\\Business',3,'2025-02-18 05:29:28','2025-02-18 05:29:28'),
(45,'pending',NULL,'App\\Models\\Task',1,'2025-02-19 21:41:39','2025-02-19 21:41:39'),
(46,'pending',NULL,'App\\Models\\Task',1,'2025-02-19 21:44:35','2025-02-19 21:44:35'),
(47,'pending',NULL,'App\\Models\\Task',2,'2025-02-19 21:47:52','2025-02-19 21:47:52'),
(48,'in_progress','It\'s also more consistent with Laravel\'s collection-handling style. We chain it directly after the pluck.\r\n\r\nThis version is cleaner, more idiomatic Laravel, and achieves the same result.  It\'s a small change, but it improves readability and maintainability.','App\\Models\\Task',2,'2025-02-19 23:49:55','2025-02-19 23:49:55'),
(49,'open',NULL,'App\\Models\\JobPost',4,'2025-02-21 04:09:58','2025-02-21 04:09:58'),
(50,'pending',NULL,'App\\Models\\Task',3,'2025-02-21 08:29:15','2025-02-21 08:29:15'),
(51,'pending',NULL,'App\\Models\\Task',4,'2025-02-21 08:33:02','2025-02-21 08:33:02'),
(52,'pending',NULL,'App\\Models\\AccessRequest',1,'2025-02-27 10:16:03','2025-02-27 10:16:03'),
(53,'setup',NULL,'App\\Models\\User',12,'2025-02-27 11:12:18','2025-02-27 11:12:18'),
(54,'module',NULL,'App\\Models\\Business',4,'2025-02-27 11:13:09','2025-02-27 11:13:09'),
(55,'module',NULL,'App\\Models\\User',12,'2025-02-27 11:13:09','2025-02-27 11:13:09'),
(56,'active',NULL,'App\\Models\\User',12,'2025-02-27 11:15:31','2025-02-27 11:15:31'),
(57,'active',NULL,'App\\Models\\Business',4,'2025-02-27 11:15:31','2025-02-27 11:15:31'),
(58,'active',NULL,'App\\Models\\Deduction',1,'2025-03-03 05:04:56','2025-03-03 05:04:56'),
(59,'active',NULL,'App\\Models\\Deduction',2,'2025-03-03 05:05:42','2025-03-03 05:05:42'),
(60,'active',NULL,'App\\Models\\Deduction',3,'2025-03-03 05:06:10','2025-03-03 05:06:10'),
(61,'active',NULL,'App\\Models\\EmployeeDeduction',2,'2025-03-03 05:45:56','2025-03-03 05:45:56'),
(62,'active',NULL,'App\\Models\\EmployeeDeduction',3,'2025-03-03 05:46:26','2025-03-03 05:46:26'),
(63,'active',NULL,'App\\Models\\EmployeeDeduction',4,'2025-03-03 05:49:58','2025-03-03 05:49:58');
/*!40000 ALTER TABLE `statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tasks_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
INSERT INTO `tasks` VALUES
(2,1,'Bradley Walker','bradley-walker','What is Lorem Ipsum?\r\n\r\nLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.\r\nWhy do we use it?\r\n\r\nIt is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).','2025-02-22','2025-02-19 21:47:52','2025-02-19 21:47:52'),
(3,1,'Amanda Sawyer','amanda-sawyer','Laravel 11 provides a world best framework for building APIs, and Passport is a powerful package that adds OAuth2 authentication to Laravel applications. In this blog post, we will teach how to set up and configure Passport for API authentication step by step for apply seurity in laravel 11','2025-02-25','2025-02-21 08:29:15','2025-02-21 08:29:15');
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `provider_token` text DEFAULT NULL,
  `social_id` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_phone_unique` (`phone`),
  UNIQUE KEY `users_social_id_unique` (`social_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Sammy James','sammy@anzar.co.ke','+254797702066','Kenya','254',NULL,NULL,NULL,NULL,'$2y$12$vU9XVsvB.biaV0SFk4PHHODAsvZLrHjO9gboZShaVhYOhq1aPK9cG',NULL,'2025-02-04 03:34:48','2025-02-27 08:42:36'),
(2,'Wayne Zahara Arnold','arnold@gmail.com','+711458963','Kenya','254',NULL,NULL,NULL,NULL,'$2y$12$0G.GRi3w/NjlzNOUvNfzv.jaMFpby2QCVoduJoS4Caa1DP9C86Spu',NULL,'2025-02-04 23:11:27','2025-02-04 23:11:27'),
(3,'John Michael Doe','john.doe@example.com','+1234567890','Kenya','254',NULL,NULL,NULL,NULL,'$2y$12$I4nAR/VoIgCQejXKjY80U.7Zw3D371i..XCsY9PMlYOkgErc7Ex0G',NULL,'2025-02-04 23:22:26','2025-02-04 23:22:26'),
(4,'Emily Rose Smith','emily.smith@example.com','+1245678901','Kenya','254',NULL,NULL,NULL,NULL,'$2y$12$VW3waobGdCszZ2HX00EMEeY3/RcS.5K15LbMTIGZP0LMoPy7rfo9G',NULL,'2025-02-04 23:30:46','2025-02-04 23:30:46'),
(6,'Martin James Barton Morgan','bujovytywe@mailinator.com','+2547977020836','Kenya',NULL,NULL,NULL,NULL,NULL,'$2y$12$Fi6r3yw36RkBK8czNpROK.Njd5tXSzL2UbRk88s57jgvxvaELGekC',NULL,'2025-02-11 00:18:24','2025-02-11 00:18:24'),
(7,'John Michael Doe','johndoe@example.com','+254712345678','Kenya',NULL,NULL,NULL,NULL,NULL,'$2y$12$3dCTqMsvobRzriVumuDq1.neoOWf5TVSfr3R9YzlZ6SVyPnP0Pkru',NULL,'2025-02-11 00:32:30','2025-02-11 00:32:30'),
(8,'Alice  ','alice@example.com','+254723456789','Kenya',NULL,NULL,NULL,NULL,NULL,'$2y$12$jDkc9j9eUSMv9um/RyJ9a.yM77pjpLOpH4tOFkbmzn4/GzPi.wcsW',NULL,'2025-02-11 00:33:38','2025-02-11 00:33:38'),
(9,'Ben Shapiro','info@anzar.co.ke','+254711616012','Kenya','254',NULL,NULL,NULL,NULL,'$2y$12$04qhLAQBZHFazL7rcw2t9.lpDdx49d6WnFl3FT1oDzEm7IU8.bIFy',NULL,'2025-02-18 03:22:59','2025-02-18 03:22:59'),
(11,'Curran Logan','erick@anzar.co.ke','+254711616018','Kenya','254',NULL,NULL,NULL,NULL,'$2y$12$XTcyS30hLa2a5TS2zk66B.cfq5c7kil499V1Xzy3kaJ6yYkvD3hhi',NULL,'2025-02-18 05:28:51','2025-02-18 05:28:51'),
(12,'Ann Kabaka','sammyorondo2@gmail.com','+254711616065','Kenya','254',NULL,NULL,NULL,NULL,'$2y$12$pHOgnav2tUX7z1aCrCFmU.4coUjYCgzoIHbwYTDgZgFgezRDMO7Oe',NULL,'2025-02-27 11:12:18','2025-02-27 11:12:18');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-03-05 17:07:47
