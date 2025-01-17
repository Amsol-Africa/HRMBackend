/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.4.3-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: amsol
-- ------------------------------------------------------
-- Server version	11.4.3-MariaDB-1

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business_modules`
--

LOCK TABLES `business_modules` WRITE;
/*!40000 ALTER TABLE `business_modules` DISABLE KEYS */;
INSERT INTO `business_modules` VALUES
(1,1,1,1,NULL,'2025-01-14 01:46:04','2025-01-14 01:46:04'),
(2,1,8,1,NULL,'2025-01-14 01:46:04','2025-01-14 01:46:04'),
(3,1,2,1,NULL,'2025-01-14 01:46:04','2025-01-14 01:46:04'),
(4,1,10,1,NULL,'2025-01-14 01:46:04','2025-01-14 01:46:04'),
(5,1,3,1,NULL,'2025-01-14 01:46:04','2025-01-14 01:46:04'),
(6,1,6,1,NULL,'2025-01-14 01:46:04','2025-01-14 01:46:04'),
(7,2,7,1,NULL,'2025-01-14 07:48:59','2025-01-14 07:48:59'),
(8,2,1,1,NULL,'2025-01-14 07:48:59','2025-01-14 07:48:59'),
(9,2,8,1,NULL,'2025-01-14 07:48:59','2025-01-14 07:48:59'),
(10,2,2,1,NULL,'2025-01-14 07:48:59','2025-01-14 07:48:59'),
(11,2,3,1,NULL,'2025-01-14 07:48:59','2025-01-14 07:48:59');
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `businesses_slug_unique` (`slug`),
  KEY `businesses_user_id_foreign` (`user_id`),
  CONSTRAINT `businesses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `businesses`
--

LOCK TABLES `businesses` WRITE;
/*!40000 ALTER TABLE `businesses` DISABLE KEYS */;
INSERT INTO `businesses` VALUES
(1,1,'Anzar KE','anzar-ke','technology','11-50','+254711616015','Kenya','254','2025-01-14 01:45:40','2025-01-14 01:45:40'),
(2,8,'Ongod Designs','ongod-designs','manufacturing','51-200','+254743048147','Kenya','254','2025-01-14 07:48:43','2025-01-14 07:48:43');
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES
(1,1,'IT Department','it-department','Responsible for all IT-related tasks','2025-01-16 11:57:40','2025-01-16 11:57:40'),
(2,1,'Human Resources','human-resources','Handles recruitment, employee benefits, and other HR tasks','2025-01-16 11:57:40','2025-01-16 11:57:40'),
(3,1,'Sales Department','sales-department','Responsible for sales and customer interactions','2025-01-16 11:57:40','2025-01-16 11:57:40'),
(4,1,'Marketing Department','marketing-department','Oversees marketing and branding strategies','2025-01-16 11:57:40','2025-01-16 11:57:40'),
(5,1,'Operations','operations','Manages day-to-day operations of the business','2025-01-16 11:57:40','2025-01-16 11:57:40');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_contact_details`
--

LOCK TABLES `employee_contact_details` WRITE;
/*!40000 ALTER TABLE `employee_contact_details` DISABLE KEYS */;
INSERT INTO `employee_contact_details` VALUES
(1,3,'797702066','254',NULL,'hecaboxyfy@mailinator.com','Bara Street 42','Bara','00100','kenya','SIncerely','2025-01-16 13:46:28','2025-01-16 13:46:28');
/*!40000 ALTER TABLE `employee_contact_details` ENABLE KEYS */;
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
-- Table structure for table `employee_next_of_kin`
--

DROP TABLE IF EXISTS `employee_next_of_kin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_next_of_kin` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `relationship` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_next_of_kin_employee_id_foreign` (`employee_id`),
  CONSTRAINT `employee_next_of_kin_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_next_of_kin`
--

LOCK TABLES `employee_next_of_kin` WRITE;
/*!40000 ALTER TABLE `employee_next_of_kin` DISABLE KEYS */;
INSERT INTO `employee_next_of_kin` VALUES
(3,3,'James Hosea','Brother','7116752132','254','2025-01-16 13:46:28','2025-01-16 13:46:28');
/*!40000 ALTER TABLE `employee_next_of_kin` ENABLE KEYS */;
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
(3,3,52000.00,'KES','mpesa','Meghan Octavia Blake','4574165154','Jescie Frazier Bank','BN37465','Kilimani','BR7376','2025-01-16 13:46:28','2025-01-16 13:46:28');
/*!40000 ALTER TABLE `employee_payment_details` ENABLE KEYS */;
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
  `date_of_birth` date NOT NULL,
  `marital_status` enum('single','married','divorced','widowed') NOT NULL,
  `national_id` varchar(255) NOT NULL,
  `tax_no` varchar(255) NOT NULL,
  `nhif_no` varchar(255) DEFAULT NULL,
  `nssf_no` varchar(255) DEFAULT NULL,
  `blood_group` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `passport_no` varchar(255) DEFAULT NULL,
  `passport_issue_date` date DEFAULT NULL,
  `passport_expiry_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
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
  CONSTRAINT `employees_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES
(3,13,1,3,'EMP006','male','1990-01-01','single','1335874','A009826376Y','N35345','NS78276','AB+','P298738IUG','2023-07-26','2026-08-20','2025-01-16 13:46:28','2025-01-16 13:46:28');
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
  `employment_status` enum('contract','fulltime','permanent') NOT NULL,
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
(3,3,3,4,1,'2023-08-23','2023-11-23','2029-12-27','2038-12-30','permanent','What is Lorem Ipsum?\r\n\r\nLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.\r\nWhy do we use it?\r\n\r\nIt is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).','2025-01-16 13:46:28','2025-01-16 13:46:28');
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_categories`
--

LOCK TABLES `job_categories` WRITE;
/*!40000 ALTER TABLE `job_categories` DISABLE KEYS */;
INSERT INTO `job_categories` VALUES
(1,1,'Software Developer','software-developer','Develops and maintains software applications','2025-01-16 11:57:16','2025-01-16 12:52:38'),
(2,1,'Human Resources','human-resources','Manages employee relations and recruitment','2025-01-16 11:57:16','2025-01-16 11:57:16'),
(3,1,'Marketing Manager','marketing-manager','Oversees marketing strategies and campaigns','2025-01-16 11:57:16','2025-01-16 11:57:16'),
(4,1,'Project Manager','project-manager','Manages and coordinates project teams','2025-01-16 11:57:16','2025-01-16 11:57:16'),
(5,1,'Sales Executive','sales-executive','Responsible for sales and client acquisition','2025-01-16 11:57:16','2025-01-16 11:57:16');
/*!40000 ALTER TABLE `job_categories` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
INSERT INTO `media` VALUES
(1,'App\\Models\\User',1,'2116cdfe-6d17-4c8f-a38b-d15ff5652152','avatars','media-libraryEsMFTj','media-libraryEsMFTj','image/png','public','public',392,'[]','[]','[]','[]',1,'2025-01-14 01:45:09','2025-01-14 01:45:09'),
(2,'App\\Models\\Business',1,'021a3d6d-add4-4c9f-9d01-db339618dfa8','businesses','logo 2','logo-2.png','image/png','public','public',11294,'[]','[]','[]','[]',1,'2025-01-14 01:45:40','2025-01-14 01:45:40'),
(5,'App\\Models\\User',6,'52e9c672-1135-4b45-a6cf-fec68fc1d3f0','avatars','GfyBQ80XQAAgDFZ','GfyBQ80XQAAgDFZ.jpeg','image/jpeg','public','public',33809,'[]','[]','[]','[]',1,'2025-01-14 07:06:47','2025-01-14 07:06:47'),
(6,'App\\Models\\User',7,'fb9b860a-c33e-4ae3-821a-57af09c572c2','avatars','profie-woman','profie-woman.jpeg','image/jpeg','public','public',8910,'[]','[]','[]','[]',1,'2025-01-14 07:09:04','2025-01-14 07:09:04'),
(7,'App\\Models\\User',8,'fe149cad-6cda-4ad5-8b26-9d8a561e929d','avatars','media-libraryn4C3J3','media-libraryn4C3J3','image/png','public','public',388,'[]','[]','[]','[]',1,'2025-01-14 07:48:10','2025-01-14 07:48:10'),
(8,'App\\Models\\Business',2,'4cb73f0d-ae4f-4183-b318-aa227f054567','businesses','avatar','avatar.png','image/png','public','public',53546,'[]','[]','[]','[]',1,'2025-01-14 07:48:43','2025-01-14 07:48:43'),
(9,'App\\Models\\User',13,'3f70ded1-ea0f-4101-b1f3-35216655e82a','avatars','person-rtgf43563','person-rtgf43563.jpeg','image/jpeg','public','public',7013,'[]','[]','[]','[]',1,'2025-01-16 13:46:28','2025-01-16 13:46:28'),
(10,'App\\Models\\Employee',3,'c199db04-d3cb-4277-918d-e1b7b03cfdf5','academic_files','Sammy James Orondo - Report Update','Sammy-James-Orondo---Report-Update.pdf','application/pdf','public','public',63876,'[]','[]','[]','[]',1,'2025-01-16 13:46:28','2025-01-16 13:46:28');
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
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
(8,'2024_12_28_025027_create_modules_table',1),
(29,'2025_01_14_025030_create_job_categories_table',2),
(30,'2025_01_14_025032_create_shifts_table',2),
(31,'2025_01_14_052658_create_departments_table',2),
(32,'2025_01_14_083726_create_employees_table',2),
(33,'2025_01_16_113024_create_employee_next_of_kin_table',2),
(34,'2025_01_16_113239_create_employment_details_table',2),
(35,'2025_01_16_113352_create_employee_payment_details_table',2),
(36,'2025_01_16_113502_create_employee_contact_details_table',2),
(37,'2025_01_16_113640_create_employee_documents_table',2),
(40,'2025_01_17_014648_create_payroll_formulas_table',3),
(41,'2025_01_17_020359_create_payroll_formula_brackets_table',3);
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
(2,'App\\Models\\User',1),
(2,'App\\Models\\User',6),
(3,'App\\Models\\User',7),
(2,'App\\Models\\User',8),
(3,'App\\Models\\User',13);
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
(1,'Core HR Management','core-hr-management','Essential HR features including employee management, attendance, and basic reporting',0.00,0.00,1,'[\"Employee Database\",\"Attendance Management\",\"Leave Management\",\"Basic Reports\",\"Document Management\"]','people-fill','2025-01-14 01:44:37','2025-01-14 01:44:37'),
(2,'Payroll Management','payroll-management','Complete payroll processing system with tax calculations and compliance',49.99,499.99,0,'[\"Salary Processing\",\"Tax Calculations\",\"Payslip Generation\",\"Statutory Compliance\",\"Multiple Payment Methods\",\"Payroll Reports\"]','wallet2','2025-01-14 01:44:37','2025-01-14 01:44:37'),
(3,'Recruitment & Onboarding','recruitment-onboarding','End-to-end recruitment solution from job posting to onboarding',39.99,399.99,0,'[\"Job Posting Management\",\"Applicant Tracking\",\"Interview Scheduling\",\"Candidate Assessment\",\"Onboarding Workflow\",\"Document Collection\"]','person-plus-fill','2025-01-14 01:44:37','2025-01-14 01:44:37'),
(4,'Performance Management','performance-management','Complete performance evaluation and goal tracking system',29.99,299.99,0,'[\"Goal Setting & Tracking\",\"Performance Reviews\",\"360\\u00b0 Feedback\",\"Skills Assessment\",\"Development Plans\",\"Performance Analytics\"]','graph-up-arrow','2025-01-14 01:44:37','2025-01-14 01:44:37'),
(5,'Learning Management','learning-management','Employee training and development platform',34.99,349.99,0,'[\"Course Management\",\"Training Schedules\",\"Learning Paths\",\"Assessment Tools\",\"Certification Tracking\",\"Training Reports\"]','journal-bookmark','2025-01-14 01:44:37','2025-01-14 01:44:37'),
(6,'Time & Attendance','time-attendance','Advanced time tracking and attendance management',24.99,249.99,0,'[\"Time Tracking\",\"Shift Management\",\"Overtime Calculation\",\"Leave Planning\",\"Attendance Reports\",\"Mobile Check-in\"]','clock-fill','2025-01-14 01:44:37','2025-01-14 01:44:37'),
(7,'Asset Management','asset-management','Track and manage company assets and resources',19.99,199.99,0,'[\"Asset Tracking\",\"Maintenance Scheduling\",\"Asset Assignment\",\"Inventory Management\",\"Asset Reports\",\"Depreciation Tracking\"]','box-seam','2025-01-14 01:44:37','2025-01-14 01:44:37'),
(8,'Employee Self-Service','employee-self-service','Portal for employees to manage their information and requests',14.99,149.99,0,'[\"Profile Management\",\"Leave Requests\",\"Expense Claims\",\"Document Access\",\"Payslip Download\",\"Benefits Enrollment\"]','person-workspace','2025-01-14 01:44:37','2025-01-14 01:44:37'),
(9,'CRM Integration','crm-integration','Customer relationship management integration with HR',44.99,449.99,0,'[\"Contact Management\",\"Lead Tracking\",\"Sales Pipeline\",\"Customer Support\",\"Email Integration\",\"Analytics & Reports\"]','people','2025-01-14 01:44:37','2025-01-14 01:44:37'),
(10,'Project Management','project-management','Project planning and resource management tools',39.99,399.99,0,'[\"Project Planning\",\"Task Management\",\"Resource Allocation\",\"Time Tracking\",\"Project Reports\",\"Team Collaboration\"]','clipboard-data','2025-01-14 01:44:37','2025-01-14 01:44:37');
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payroll_formula_brackets_payroll_formula_id_foreign` (`payroll_formula_id`),
  CONSTRAINT `payroll_formula_brackets_payroll_formula_id_foreign` FOREIGN KEY (`payroll_formula_id`) REFERENCES `payroll_formulas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_formula_brackets`
--

LOCK TABLES `payroll_formula_brackets` WRITE;
/*!40000 ALTER TABLE `payroll_formula_brackets` DISABLE KEYS */;
INSERT INTO `payroll_formula_brackets` VALUES
(4,5,0.00,5999.00,NULL,'2025-01-16 23:57:07','2025-01-16 23:57:07',150.00),
(5,5,6000.00,7999.00,NULL,'2025-01-16 23:57:07','2025-01-16 23:57:07',300.00),
(6,5,8000.00,11999.00,NULL,'2025-01-16 23:57:07','2025-01-16 23:57:07',400.00),
(7,5,12000.00,14999.00,NULL,'2025-01-16 23:57:07','2025-01-16 23:57:07',500.00),
(8,6,24001.00,32333.00,10.00,'2025-01-17 01:45:21','2025-01-17 01:45:21',NULL),
(9,6,32334.00,40667.00,25.00,'2025-01-17 01:45:21','2025-01-17 01:45:21',NULL),
(10,6,40668.00,508333.00,30.00,'2025-01-17 01:45:21','2025-01-17 01:45:21',NULL),
(11,6,508334.00,808333.00,32.50,'2025-01-17 01:45:21','2025-01-17 01:45:21',NULL);
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
  `business_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `calculation_basis` enum('basic pay','gross pay','cash pay','taxable pay') NOT NULL,
  `is_progressive` tinyint(1) NOT NULL DEFAULT 0,
  `minimum_amount` decimal(15,2) DEFAULT NULL,
  `brackets` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`brackets`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `formula_type` varchar(255) NOT NULL,
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
(5,1,'NHIF','nhif','gross pay',1,0.00,NULL,'2025-01-16 23:57:07','2025-01-16 23:57:07','amount'),
(6,1,'PAYE','paye','taxable pay',1,24001.00,NULL,'2025-01-17 01:45:21','2025-01-17 01:45:21','rate');
/*!40000 ALTER TABLE `payroll_formulas` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'superadmin','web','2025-01-14 01:44:37','2025-01-14 01:44:37'),
(2,'business_owner','web','2025-01-14 01:44:37','2025-01-14 01:44:37'),
(3,'employee','web','2025-01-14 01:44:37','2025-01-14 01:44:37');
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
('0rdLknIyutTn8DQB4JLUmGl8nVi5VkVoUBHW60sZ',1,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64; rv:135.0) Gecko/20100101 Firefox/135.0','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiUU1qNGwya0VORUx1Yk9WRVlXeXdWcUw2aEhLOTljM0plMWFVN1pvRiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTQ6Imh0dHA6Ly9hbXNvbC5sb2NhbC9idXNpbmVzcy9hbnphci1rZS9kZWR1Y3Rpb25zL2NyZWF0ZSI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoyMDoiYWN0aXZlX2J1c2luZXNzX3NsdWciO3M6ODoiYW56YXIta2UiO30=',1737097889),
('aNKeBueGUoKZaIbLERANWrkA0hYE3BOuErdoCuuU',NULL,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64; rv:135.0) Gecko/20100101 Firefox/135.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiY0JwSUlUMTZPdmk1OTNKaElyV1BBVFlobjlIQ09zWVRrU1FwdjFHVSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1MDoiaHR0cDovL2Ftc29sLmxvY2FsL2J1c2luZXNzL2FuemFyLWtlL3JlbGllZi9jcmVhdGUiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo1MDoiaHR0cDovL2Ftc29sLmxvY2FsL2J1c2luZXNzL2FuemFyLWtlL3JlbGllZi9jcmVhdGUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1737097286),
('OHIYaW6kbdbZlEzPNRiGpNIzXf8HKQwYIrez7bdl',NULL,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64; rv:135.0) Gecko/20100101 Firefox/135.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiM1daMU5IS3k5TmtjTnlWeXJRMW1GWVZ5MVh5Uk9rcVlwSlJQWk8zMSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjQ6Imh0dHA6Ly9hbXNvbC5sb2NhbC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1737097287);
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
(1,1,'Morning Shift','morning-shift','08:00:00','16:00:00','Standard morning shift',1,'2025-01-16 11:56:11','2025-01-16 12:57:30'),
(2,1,'Afternoon Shift','afternoon-shift','16:00:00','00:00:00','Standard afternoon shift',1,'2025-01-16 11:56:11','2025-01-16 11:56:11'),
(3,1,'Night Shift','night-shift','00:00:00','08:00:00','Standard night shift',1,'2025-01-16 11:56:11','2025-01-16 11:56:11'),
(4,1,'Flexible Shift','flexible-shift','10:00:00','18:00:00','Flexible shift for employees',1,'2025-01-16 11:56:11','2025-01-16 11:56:11'),
(5,1,'Weekend Shift','weekend-shift','09:00:00','17:00:00','Shift during weekends',1,'2025-01-16 11:56:11','2025-01-16 11:56:11');
/*!40000 ALTER TABLE `shifts` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statuses`
--

LOCK TABLES `statuses` WRITE;
/*!40000 ALTER TABLE `statuses` DISABLE KEYS */;
INSERT INTO `statuses` VALUES
(1,'setup',NULL,'App\\Models\\User',1,'2025-01-14 01:45:09','2025-01-14 01:45:09'),
(2,'module',NULL,'App\\Models\\Business',1,'2025-01-14 01:45:40','2025-01-14 01:45:40'),
(3,'module',NULL,'App\\Models\\User',1,'2025-01-14 01:45:40','2025-01-14 01:45:40'),
(4,'active',NULL,'App\\Models\\User',1,'2025-01-14 01:46:04','2025-01-14 01:46:04'),
(5,'active',NULL,'App\\Models\\Business',1,'2025-01-14 01:46:04','2025-01-14 01:46:04'),
(6,'active',NULL,'App\\Models\\Department',3,'2025-01-14 04:47:49','2025-01-14 04:47:49'),
(7,'active',NULL,'App\\Models\\Department',4,'2025-01-14 04:48:10','2025-01-14 04:48:10'),
(8,'active',NULL,'App\\Models\\Department',5,'2025-01-14 04:48:28','2025-01-14 04:48:28'),
(9,'active',NULL,'App\\Models\\Department',6,'2025-01-14 04:48:51','2025-01-14 04:48:51'),
(10,'active',NULL,'App\\Models\\Department',4,'2025-01-14 05:29:14','2025-01-14 05:29:14'),
(11,'active',NULL,'App\\Models\\Department',4,'2025-01-14 05:29:22','2025-01-14 05:29:22'),
(12,'active',NULL,'App\\Models\\Department',1,'2025-01-14 06:13:22','2025-01-14 06:13:22'),
(13,'active',NULL,'App\\Models\\Department',2,'2025-01-14 06:13:39','2025-01-14 06:13:39'),
(14,'active',NULL,'App\\Models\\Department',3,'2025-01-14 06:14:00','2025-01-14 06:14:00'),
(15,'active',NULL,'App\\Models\\Department',4,'2025-01-14 06:14:16','2025-01-14 06:14:16'),
(16,'active',NULL,'App\\Models\\Department',5,'2025-01-14 06:14:41','2025-01-14 06:14:41'),
(21,'active',NULL,'App\\Models\\User',6,'2025-01-14 07:06:47','2025-01-14 07:06:47'),
(22,'active',NULL,'App\\Models\\User',7,'2025-01-14 07:09:04','2025-01-14 07:09:04'),
(23,'setup',NULL,'App\\Models\\User',8,'2025-01-14 07:48:10','2025-01-14 07:48:10'),
(24,'module',NULL,'App\\Models\\Business',2,'2025-01-14 07:48:43','2025-01-14 07:48:43'),
(25,'module',NULL,'App\\Models\\User',8,'2025-01-14 07:48:43','2025-01-14 07:48:43'),
(26,'active',NULL,'App\\Models\\User',8,'2025-01-14 07:48:59','2025-01-14 07:48:59'),
(27,'active',NULL,'App\\Models\\Business',2,'2025-01-14 07:48:59','2025-01-14 07:48:59'),
(28,'active',NULL,'App\\Models\\Department',6,'2025-01-16 12:29:33','2025-01-16 12:29:33'),
(29,'active',NULL,'App\\Models\\JobCategory',11,'2025-01-16 12:40:15','2025-01-16 12:40:15'),
(30,'active',NULL,'App\\Models\\JobCategory',12,'2025-01-16 12:45:53','2025-01-16 12:45:53'),
(31,'active',NULL,'App\\Models\\JobCategory',1,'2025-01-16 12:52:16','2025-01-16 12:52:16'),
(32,'active',NULL,'App\\Models\\JobCategory',1,'2025-01-16 12:52:38','2025-01-16 12:52:38'),
(33,'active',NULL,'App\\Models\\Shift',1,'2025-01-16 12:57:22','2025-01-16 12:57:22'),
(34,'active',NULL,'App\\Models\\Shift',1,'2025-01-16 12:57:30','2025-01-16 12:57:30'),
(39,'active',NULL,'App\\Models\\User',13,'2025-01-16 13:46:28','2025-01-16 13:46:28');
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Sammy James','sammy@anzar.co.ke','+254711616015','Kenya','254',NULL,NULL,NULL,NULL,'$2y$12$8LWVXJDwcNzrKO4Vg1npNOK4fbuqCl14Uyaqt1Zlk/NriOlsAAOw2',NULL,'2025-01-14 01:45:09','2025-01-14 01:45:09'),
(6,'Fredrik Ndala','fredrick@gmail.com','+254722897615','Kenya','254',NULL,NULL,NULL,NULL,'$2y$12$cZcM3C2sutmqUE91QawrzuXvYFKPRF7gP40LyY1vpfT8zLSO7paoC',NULL,'2025-01-14 07:06:47','2025-01-14 07:06:47'),
(7,'Ann Njery','annjery@gmail.com','+254743047165','Kenya','254',NULL,NULL,NULL,NULL,'$2y$12$/HYgQAo6jRYzfJW22oN2CO8cal6BvBWUl/PiB.mlVd3uFNRo48h5y',NULL,'2025-01-14 07:09:04','2025-01-14 07:09:04'),
(8,'Erick Odhiambo','erick@anzar.co.ke','+254711616012','Kenya','254',NULL,NULL,NULL,NULL,'$2y$12$knZ9yk44HKVccz7Q5qawge0ik/JyVtreB.3l4lFdIshgR7Ktu0UDK',NULL,'2025-01-14 07:48:10','2025-01-14 07:48:10'),
(13,'Meghan Octavia Acevedo Blake','nyvupo@mailinator.com','+7346387957','kenya','254',NULL,NULL,NULL,NULL,'$2y$12$v.3QI6t3newWDoriSjbxbulaumxe30pCj0xc8rcXwuhCp73FFl8AS',NULL,'2025-01-16 13:46:28','2025-01-16 13:46:28');
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

-- Dump completed on 2025-01-17 10:15:40
