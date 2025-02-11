/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.4.4-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: amsol
-- ------------------------------------------------------
-- Server version	11.4.4-MariaDB-3

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
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `access_requests`
--

LOCK TABLES `access_requests` WRITE;
/*!40000 ALTER TABLE `access_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `access_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `advances`
--

DROP TABLE IF EXISTS `advances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
-- Table structure for table `business_modules`
--

DROP TABLE IF EXISTS `business_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
(10,1,6,1,NULL,'2025-02-04 03:35:38','2025-02-04 03:35:38');
/*!40000 ALTER TABLE `business_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `businesses`
--

DROP TABLE IF EXISTS `businesses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `businesses`
--

LOCK TABLES `businesses` WRITE;
/*!40000 ALTER TABLE `businesses` DISABLE KEYS */;
INSERT INTO `businesses` VALUES
(1,1,'Anzar KE','anzar-ke','information-technology','1-10','+254797702066','Kenya','254',NULL,NULL,NULL,NULL,NULL,'2025-02-04 03:35:20','2025-02-04 03:35:20');
/*!40000 ALTER TABLE `businesses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deductions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `deductions_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deductions`
--

LOCK TABLES `deductions` WRITE;
/*!40000 ALTER TABLE `deductions` DISABLE KEYS */;
/*!40000 ALTER TABLE `deductions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_deductions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `deduction_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_deductions`
--

LOCK TABLES `employee_deductions` WRITE;
/*!40000 ALTER TABLE `employee_deductions` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_deductions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_documents`
--

DROP TABLE IF EXISTS `employee_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!40101 SET character_set_client = utf8 */;
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_payrolls`
--

LOCK TABLES `employee_payrolls` WRITE;
/*!40000 ALTER TABLE `employee_payrolls` DISABLE KEYS */;
INSERT INTO `employee_payrolls` VALUES
(1,3,1,68000,NULL,68000,15183,4400,840,NULL,1020,68000,NULL,52817,0,52817,'2025-02-06 00:42:01','2025-02-06 00:42:01'),
(2,3,2,60000,NULL,60000,12783,4400,840,NULL,900,60000,NULL,47217,0,47217,'2025-02-06 00:42:01','2025-02-06 00:42:01'),
(3,3,3,55000,NULL,55000,11283,4400,840,NULL,825,55000,NULL,43717,0,43717,'2025-02-06 00:42:01','2025-02-06 00:42:01'),
(4,4,1,68000,NULL,68000,15183,4400,840,NULL,1020,68000,NULL,52817,0,52817,'2025-02-07 03:49:28','2025-02-07 03:49:28'),
(5,4,3,55000,NULL,55000,11283,4400,840,NULL,825,55000,NULL,43717,0,43717,'2025-02-07 03:49:28','2025-02-07 03:49:28');
/*!40000 ALTER TABLE `employee_payrolls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_reliefs`
--

DROP TABLE IF EXISTS `employee_reliefs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interviews`
--

LOCK TABLES `interviews` WRITE;
/*!40000 ALTER TABLE `interviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `interviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_delegations`
--

DROP TABLE IF EXISTS `leave_delegations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
(14,'App\\Models\\Application',2,'386aa516-51a8-4e33-9f43-f6dedfe1b559','applications','Head Of Department-Payslip for the month of January (01), 2025','Head-Of-Department-Payslip-for-the-month-of-January-(01),-2025.pdf','application/pdf','public','public',14126,'[]','[]','[]','[]',1,'2025-02-11 01:04:52','2025-02-11 01:04:52');
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
(28,'2025_01_17_050013_create_deductions_table',1),
(29,'2025_01_17_050014_create_employee_deductions_table',1),
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
(79,'2025_02_11_031751_add_timestamps_to_applicants_table',7);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
(11,'App\\Models\\User',2),
(11,'App\\Models\\User',3),
(11,'App\\Models\\User',4),
(12,'App\\Models\\User',6),
(12,'App\\Models\\User',7),
(12,'App\\Models\\User',8);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
(13,5,7000.00,14000.00,6.00,0.00,'2025-02-05 04:38:29','2025-02-05 04:38:29');
/*!40000 ALTER TABLE `payroll_formula_brackets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_formulas`
--

DROP TABLE IF EXISTS `payroll_formulas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
(5,NULL,'NSSF','nssf','rate','gross_pay',NULL,1,NULL,'2025-02-04 03:34:17','2025-02-04 03:34:17');
/*!40000 ALTER TABLE `payroll_formulas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payrolls`
--

DROP TABLE IF EXISTS `payrolls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payrolls` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned NOT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `payroll_type` varchar(255) NOT NULL,
  `currency` varchar(255) NOT NULL,
  `staff` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payrolls`
--

LOCK TABLES `payrolls` WRITE;
/*!40000 ALTER TABLE `payrolls` DISABLE KEYS */;
INSERT INTO `payrolls` VALUES
(2,1,NULL,'monthly','KSH',3,'2025-01-01','2025-01-31','2025-02-05 01:53:40','2025-02-05 01:53:40'),
(3,1,NULL,'monthly','KSH',3,'2025-01-01','2025-01-31','2025-02-06 00:42:01','2025-02-06 00:42:01'),
(4,1,NULL,'monthly','KSH',2,'2024-12-01','2024-12-31','2025-02-07 03:49:28','2025-02-07 03:49:28');
/*!40000 ALTER TABLE `payrolls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
('hWWElEMWk1tCyWx117RzgjVw2Q1Rsbm3ijaVIVCq',1,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64; rv:136.0) Gecko/20100101 Firefox/136.0','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiUFlwRG1NMWxwbXQyMnVDdlFVenNmOElJSmJxYUhGcFBkcldZVHBMTCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1NToiaHR0cDovL2Ftc29sLmxvY2FsL2J1c2luZXNzL2FuemFyLWtlL2xlYXZlL2VudGl0bGVtZW50cyI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU5OiJodHRwOi8vYW1zb2wubG9jYWwvYnVzaW5lc3MvYW56YXIta2UvcmVjcnVpdG1lbnQvaW50ZXJ2aWV3cyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoyMDoiYWN0aXZlX2J1c2luZXNzX3NsdWciO3M6ODoiYW56YXIta2UiO30=',1739248591);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shifts`
--

DROP TABLE IF EXISTS `shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
(30,'applied',NULL,'App\\Models\\Application',2,'2025-02-11 00:33:39','2025-02-11 00:33:39');
/*!40000 ALTER TABLE `statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Sammy James','sammy@anzar.co.ke','+254797702066','Kenya','254',NULL,NULL,NULL,NULL,'$2y$12$l5Tgf5vzgCrN..8JMT/80.cXVA8/HcDz4T6qK2/dwBIs0REfgOQlu',NULL,'2025-02-04 03:34:48','2025-02-04 03:34:48'),
(2,'Wayne Zahara Arnold','arnold@gmail.com','+711458963','Kenya','254',NULL,NULL,NULL,NULL,'$2y$12$0G.GRi3w/NjlzNOUvNfzv.jaMFpby2QCVoduJoS4Caa1DP9C86Spu',NULL,'2025-02-04 23:11:27','2025-02-04 23:11:27'),
(3,'John Michael Doe','john.doe@example.com','+1234567890','Kenya','254',NULL,NULL,NULL,NULL,'$2y$12$I4nAR/VoIgCQejXKjY80U.7Zw3D371i..XCsY9PMlYOkgErc7Ex0G',NULL,'2025-02-04 23:22:26','2025-02-04 23:22:26'),
(4,'Emily Rose Smith','emily.smith@example.com','+1245678901','Kenya','254',NULL,NULL,NULL,NULL,'$2y$12$VW3waobGdCszZ2HX00EMEeY3/RcS.5K15LbMTIGZP0LMoPy7rfo9G',NULL,'2025-02-04 23:30:46','2025-02-04 23:30:46'),
(6,'Martin James Barton Morgan','bujovytywe@mailinator.com','+2547977020836','Kenya',NULL,NULL,NULL,NULL,NULL,'$2y$12$Fi6r3yw36RkBK8czNpROK.Njd5tXSzL2UbRk88s57jgvxvaELGekC',NULL,'2025-02-11 00:18:24','2025-02-11 00:18:24'),
(7,'John Michael Doe','johndoe@example.com','+254712345678','Kenya',NULL,NULL,NULL,NULL,NULL,'$2y$12$3dCTqMsvobRzriVumuDq1.neoOWf5TVSfr3R9YzlZ6SVyPnP0Pkru',NULL,'2025-02-11 00:32:30','2025-02-11 00:32:30'),
(8,'Alice  ','alice@example.com','+254723456789','Kenya',NULL,NULL,NULL,NULL,NULL,'$2y$12$jDkc9j9eUSMv9um/RyJ9a.yM77pjpLOpH4tOFkbmzn4/GzPi.wcsW',NULL,'2025-02-11 00:33:38','2025-02-11 00:33:38');
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

-- Dump completed on 2025-02-11  7:51:13
