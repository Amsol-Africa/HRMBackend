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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_qualifications`
--

LOCK TABLES `academic_qualifications` WRITE;
/*!40000 ALTER TABLE `academic_qualifications` DISABLE KEYS */;
INSERT INTO `academic_qualifications` VALUES
(11,9,'2010-01-01','2014-01-01','University A','MBA','2025-01-27 23:44:26','2025-01-27 23:44:26'),
(12,9,'2010-01-01','2014-01-01','Institute B','HR Certification','2025-01-27 23:44:26','2025-01-27 23:44:26');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `access_requests`
--

LOCK TABLES `access_requests` WRITE;
/*!40000 ALTER TABLE `access_requests` DISABLE KEYS */;
INSERT INTO `access_requests` VALUES
(1,1,1,'sammyorondo2@gmail.com','a3df02d1dd362af89cb543cc384b301eea61a3c7e96a3620b704a1786d920441','2025-01-27 12:27:38','2025-01-27 12:27:38'),
(2,1,1,'sammyorondo2@gmail.com','89ea8ba8b238b00e61ebe0f516c0455124c6e8cce1224290949859b08cc96256','2025-01-27 12:39:36','2025-01-27 12:39:36');
/*!40000 ALTER TABLE `access_requests` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business_modules`
--

LOCK TABLES `business_modules` WRITE;
/*!40000 ALTER TABLE `business_modules` DISABLE KEYS */;
INSERT INTO `business_modules` VALUES
(1,1,1,1,NULL,'2025-01-23 08:28:59','2025-01-23 08:28:59'),
(2,1,5,1,NULL,'2025-01-23 08:28:59','2025-01-23 08:28:59'),
(3,1,2,1,NULL,'2025-01-23 08:28:59','2025-01-23 08:28:59'),
(4,1,4,1,NULL,'2025-01-23 08:28:59','2025-01-23 08:28:59'),
(5,1,10,1,NULL,'2025-01-23 08:28:59','2025-01-23 08:28:59'),
(6,1,3,1,NULL,'2025-01-23 08:28:59','2025-01-23 08:28:59'),
(7,1,6,1,NULL,'2025-01-23 08:28:59','2025-01-23 08:28:59'),
(14,5,7,1,NULL,'2025-01-27 12:59:41','2025-01-27 12:59:41'),
(15,5,1,1,NULL,'2025-01-27 12:59:41','2025-01-27 12:59:41'),
(16,5,9,1,NULL,'2025-01-27 12:59:41','2025-01-27 12:59:41'),
(17,5,8,1,NULL,'2025-01-27 12:59:41','2025-01-27 12:59:41'),
(18,5,5,1,NULL,'2025-01-27 12:59:41','2025-01-27 12:59:41'),
(19,5,2,1,NULL,'2025-01-27 12:59:41','2025-01-27 12:59:41'),
(20,5,3,1,NULL,'2025-01-27 12:59:41','2025-01-27 12:59:41'),
(21,5,6,1,NULL,'2025-01-27 12:59:41','2025-01-27 12:59:41');
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `businesses_slug_unique` (`slug`),
  KEY `businesses_user_id_foreign` (`user_id`),
  CONSTRAINT `businesses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `businesses`
--

LOCK TABLES `businesses` WRITE;
/*!40000 ALTER TABLE `businesses` DISABLE KEYS */;
INSERT INTO `businesses` VALUES
(1,1,'Anzar Technologies','anzar-technologies','technology','11-50','+254711616015','Kenya','254',NULL,NULL,NULL,NULL,'2025-01-23 08:28:43','2025-01-23 08:28:43'),
(5,1,'Aushi Tenancy','aushi-tenancy','real-estate','1-10','+254707702054','Kenya','254','REG2784764','TX93768443','LC73563','Business Streeet Uzima','2025-01-27 12:55:11','2025-01-28 02:30:29'),
(6,1,'TX93768443','tx93768443','real-estate','1-10','+254707702054','Kenya','254',NULL,NULL,NULL,NULL,'2025-01-28 02:21:24','2025-01-28 02:21:24');
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
INSERT INTO `cache` VALUES
('da4b9237bacccdf19c0760cab7aec4a8359010b0','i:2;',1737990339),
('da4b9237bacccdf19c0760cab7aec4a8359010b0:timer','i:1737990339;',1737990339);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES
(1,1,1,1,'2025-01-27 06:55:11','2025-01-27 06:55:11'),
(2,1,5,1,'2025-01-27 12:55:11','2025-01-27 12:55:11');
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
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
(1,1,'IT Department','it-department','Responsible for all IT-related tasks','2025-01-23 08:18:01','2025-01-23 08:18:01'),
(2,1,'Human Resources','human-resources','Handles recruitment, employee benefits, and other HR tasks','2025-01-23 08:18:01','2025-01-23 08:18:01'),
(3,1,'Sales Department','sales-department','Responsible for sales and customer interactions','2025-01-23 08:18:01','2025-01-23 08:18:01'),
(4,1,'Marketing Department','marketing-department','Oversees marketing and branding strategies','2025-01-23 08:18:01','2025-01-23 08:18:01'),
(5,1,'Operations','operations','Manages day-to-day operations of the business','2025-01-23 08:18:01','2025-01-23 08:18:01');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emergency_contacts`
--

LOCK TABLES `emergency_contacts` WRITE;
/*!40000 ALTER TABLE `emergency_contacts` DISABLE KEYS */;
INSERT INTO `emergency_contacts` VALUES
(7,9,'Mary Jane','Mother','789 Parent Street','711616015',NULL,'2025-01-27 23:44:26','2025-01-27 23:44:26'),
(8,9,'Jane Doe','Spouse','789 Boulevard','711616016',NULL,'2025-01-27 23:44:26','2025-01-27 23:44:26');
/*!40000 ALTER TABLE `emergency_contacts` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_family_members`
--

LOCK TABLES `employee_family_members` WRITE;
/*!40000 ALTER TABLE `employee_family_members` DISABLE KEYS */;
INSERT INTO `employee_family_members` VALUES
(2,9,'Jane Doe','Spouse','1995-01-01','1010 Place','711616013','254','2025-01-27 23:44:26','2025-01-27 23:44:26');
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_payment_details`
--

LOCK TABLES `employee_payment_details` WRITE;
/*!40000 ALTER TABLE `employee_payment_details` DISABLE KEYS */;
INSERT INTO `employee_payment_details` VALUES
(9,9,54000.00,'KES','bank','John Doe Smith','1763566347283','Reliable Bank','78367','6376','367','2025-01-27 23:44:26','2025-01-27 23:44:26');
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES
(9,13,1,2,'EMP0001','male','+254711616019','2003-01-16',NULL,'married','123456789','Cityville','TAX00123','NHIF00123','NSSF00123','P12345678','2015-01-01','2025-01-01','123 Street, City','456 Avenue, City','O+','2025-01-27 23:44:26','2025-01-27 23:44:26');
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employment_details`
--

LOCK TABLES `employment_details` WRITE;
/*!40000 ALTER TABLE `employment_details` DISABLE KEYS */;
INSERT INTO `employment_details` VALUES
(9,9,2,1,4,'2025-01-28','2025-01-31','2028-01-28','2028-01-28','fulltime','Some job description','2025-01-27 23:44:26','2025-01-27 23:44:26');
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
(1,'Information Technology','information-technology','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(2,'Healthcare','healthcare','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(3,'Finance','finance','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(4,'Education','education','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(5,'Retail','retail','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(6,'Manufacturing','manufacturing','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(7,'Construction','construction','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(8,'Real Estate','real-estate','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(9,'Hospitality','hospitality','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(10,'Entertainment','entertainment','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(11,'Automotive','automotive','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(12,'Telecommunications','telecommunications','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(13,'Energy','energy','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(14,'Agriculture','agriculture','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(15,'Aerospace','aerospace','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(16,'Logistics and Supply Chain','logistics-and-supply-chain','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(17,'Food and Beverage','food-and-beverage','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(18,'Fashion','fashion','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(19,'Media and Publishing','media-and-publishing','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(20,'Pharmaceuticals','pharmaceuticals','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(21,'Sports and Recreation','sports-and-recreation','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(22,'Legal Services','legal-services','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(23,'Consulting','consulting','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(24,'Environmental Services','environmental-services','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(25,'Transportation','transportation','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(26,'Government and Public Administration','government-and-public-administration','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(27,'Non-Profit and Social Services','non-profit-and-social-services','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(28,'Marketing and Advertising','marketing-and-advertising','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(29,'E-Commerce','e-commerce','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(30,'Beauty and Personal Care','beauty-and-personal-care','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(31,'Insurance','insurance','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(32,'Cybersecurity','cybersecurity','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(33,'Event Management','event-management','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(34,'Research and Development','research-and-development','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(35,'Art and Design','art-and-design','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(36,'Pet Care','pet-care','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(37,'Fitness and Wellness','fitness-and-wellness','2025-01-26 06:39:39','2025-01-26 06:39:39'),
(38,'Waste Management','waste-management','2025-01-26 06:39:39','2025-01-26 06:39:39');
/*!40000 ALTER TABLE `industries` ENABLE KEYS */;
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
(1,1,'Software Developer','software-developer','Develops and maintains software applications','2025-01-23 08:18:01','2025-01-23 08:18:01'),
(2,1,'Human Resources','human-resources','Manages employee relations and recruitment','2025-01-23 08:18:01','2025-01-23 08:18:01'),
(3,1,'Marketing Manager','marketing-manager','Oversees marketing strategies and campaigns','2025-01-23 08:18:01','2025-01-23 08:18:01'),
(4,1,'Project Manager','project-manager','Manages and coordinates project teams','2025-01-23 08:18:01','2025-01-23 08:18:01'),
(5,1,'Sales Executive','sales-executive','Responsible for sales and client acquisition','2025-01-23 08:18:01','2025-01-23 08:18:01');
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
-- Table structure for table `leave_delegations`
--

DROP TABLE IF EXISTS `leave_delegations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leave_delegations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `carry_forward` decimal(5,2) NOT NULL,
  `entitled_days` decimal(5,2) NOT NULL,
  `accrued_days` decimal(5,2) NOT NULL,
  `total_days` decimal(5,2) NOT NULL,
  `days_taken` decimal(5,2) NOT NULL DEFAULT 0.00,
  `days_remaining` decimal(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_entitlements_employee_id_foreign` (`employee_id`),
  KEY `leave_entitlements_leave_type_id_foreign` (`leave_type_id`),
  KEY `leave_entitlements_leave_period_id_foreign` (`leave_period_id`),
  CONSTRAINT `leave_entitlements_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `leave_entitlements_leave_period_id_foreign` FOREIGN KEY (`leave_period_id`) REFERENCES `leave_periods` (`id`),
  CONSTRAINT `leave_entitlements_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_entitlements`
--

LOCK TABLES `leave_entitlements` WRITE;
/*!40000 ALTER TABLE `leave_entitlements` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_periods`
--

LOCK TABLES `leave_periods` WRITE;
/*!40000 ALTER TABLE `leave_periods` DISABLE KEYS */;
INSERT INTO `leave_periods` VALUES
(1,1,'2024 Calendar Year','2024-calendar-year','2024-01-01','2024-12-31',1,1,0,0,0,'2025-01-23 11:12:39','2025-01-23 11:12:39'),
(2,1,'FY 2024-2025','fy-2024-2025','2024-07-01','2025-06-30',1,1,0,0,0,'2025-01-23 11:21:39','2025-01-23 11:21:39');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_policies`
--

LOCK TABLES `leave_policies` WRITE;
/*!40000 ALTER TABLE `leave_policies` DISABLE KEYS */;
INSERT INTO `leave_policies` VALUES
(1,1,1,1,'all',15,'monthly',200.00,5,1,30,'2025-01-23','2031-01-30','2025-01-23 08:35:03','2025-01-23 08:35:03');
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
(1,'Annual Leave','annual-leave',1,'2025-01-24 00:36:48','2025-01-24 00:36:48'),
(2,'Sick Leave','sick-leave',1,'2025-01-24 00:36:48','2025-01-24 00:36:48'),
(3,'Maternity Leave','maternity-leave',1,'2025-01-24 00:36:48','2025-01-24 00:36:48'),
(4,'Paternity Leave','paternity-leave',1,'2025-01-24 00:36:48','2025-01-24 00:36:48'),
(5,'Compassionate Leave','compassionate-leave',1,'2025-01-24 00:36:48','2025-01-24 00:36:48'),
(6,'Study Leave','study-leave',1,'2025-01-24 00:36:48','2025-01-24 00:36:48'),
(7,'Unpaid Leave','unpaid-leave',1,'2025-01-24 00:36:48','2025-01-24 00:36:48'),
(8,'Public Holidays','public-holidays',1,'2025-01-24 00:36:48','2025-01-24 00:36:48'),
(9,'Sabbatical Leave','sabbatical-leave',1,'2025-01-24 00:36:48','2025-01-24 00:36:48'),
(10,'Marriage Leave','marriage-leave',1,'2025-01-24 00:36:48','2025-01-24 00:36:48'),
(11,'Bereavement Leave','bereavement-leave',1,'2025-01-24 00:36:48','2025-01-24 00:36:48'),
(12,'Adoption Leave','adoption-leave',1,'2025-01-24 00:36:48','2025-01-24 00:36:48'),
(13,'Relocation Leave','relocation-leave',1,'2025-01-24 00:36:48','2025-01-24 00:36:48'),
(14,'Childcare Leave','childcare-leave',1,'2025-01-24 00:36:48','2025-01-24 00:36:48'),
(15,'Voting Leave','voting-leave',1,'2025-01-24 00:36:48','2025-01-24 00:36:48'),
(16,'Jury Duty Leave','jury-duty-leave',1,'2025-01-24 00:36:48','2025-01-24 00:36:48'),
(17,'Military Leave','military-leave',1,'2025-01-24 00:36:48','2025-01-24 00:36:48'),
(18,'Emergency Leave','emergency-leave',1,'2025-01-24 00:36:48','2025-01-24 00:36:48'),
(19,'Volunteer Leave','volunteer-leave',1,'2025-01-24 00:36:48','2025-01-24 00:36:48'),
(20,'Wellness Leave','wellness-leave',1,'2025-01-24 00:36:48','2025-01-24 00:36:48');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_types`
--

LOCK TABLES `leave_types` WRITE;
/*!40000 ALTER TABLE `leave_types` DISABLE KEYS */;
INSERT INTO `leave_types` VALUES
(1,1,'Annual Leave','annual-leave','Leave for personal or vacation purposes',1,1,1,1,1,10,5,1,'2025-01-23 08:35:03','2025-01-23 08:35:03');
/*!40000 ALTER TABLE `leave_types` ENABLE KEYS */;
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
(1,'App\\Models\\User',1,'862a99cb-5e67-4880-a0dc-8f8b49e24754','avatars','media-libraryRwskW0','media-libraryRwskW0','image/png','public','public',392,'[]','[]','[]','[]',1,'2025-01-23 08:28:07','2025-01-23 08:28:07'),
(2,'App\\Models\\Business',1,'102dc2f2-8247-406b-858f-95a488ab48e3','businesses','logo 2','logo-2.png','image/png','public','public',11294,'[]','[]','[]','[]',1,'2025-01-23 08:28:43','2025-01-23 08:28:43'),
(3,'App\\Models\\User',2,'8dd71b69-e995-4c0b-9204-e2f482e20068','avatars','media-librarypiXkhS','media-librarypiXkhS','image/png','public','public',388,'[]','[]','[]','[]',1,'2025-01-27 11:44:33','2025-01-27 11:44:33'),
(4,'App\\Models\\Business',2,'ad095073-a9dd-4a50-8c11-f7a544d72c10','businesses','avatar','avatar.png','image/png','public','public',53546,'[]','[]','[]','[]',1,'2025-01-27 12:08:44','2025-01-27 12:08:44'),
(5,'App\\Models\\User',3,'6dab6076-f9d9-49c9-867c-39217c4623c1','avatars','media-library3NyeEB','media-library3NyeEB','image/png','public','public',397,'[]','[]','[]','[]',1,'2025-01-27 12:29:08','2025-01-27 12:29:08'),
(6,'App\\Models\\Business',3,'0b3b198f-676a-4082-a08a-7b79b833b53f','businesses','avatar','avatar.png','image/png','public','public',53546,'[]','[]','[]','[]',1,'2025-01-27 12:30:46','2025-01-27 12:30:46'),
(7,'App\\Models\\User',4,'662c77e2-eb9b-4690-b12c-7eb39b793534','avatars','media-librarywUkXVw','media-librarywUkXVw','image/png','public','public',397,'[]','[]','[]','[]',1,'2025-01-27 12:51:13','2025-01-27 12:51:13'),
(9,'App\\Models\\Business',5,'2a540438-ca0d-4ab1-b42b-de42e266b862','businesses','avatar','avatar.png','image/png','public','public',53546,'[]','[]','[]','[]',1,'2025-01-27 12:55:11','2025-01-27 12:55:11'),
(10,'App\\Models\\User',13,'44331e4a-97cf-4c9d-8b4a-9dc0e2d79111','avatars','someone-using-pos-3','someone-using-pos-3.jpg','image/jpeg','public','public',122413,'[]','[]','[]','[]',1,'2025-01-27 23:44:26','2025-01-27 23:44:26'),
(11,'App\\Models\\Employee',9,'3d303622-fa45-4073-b109-0164b83d74c7','academic_files','The-Ultimate-Website-QA-Checklist','The-Ultimate-Website-QA-Checklist.xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','public','public',36109,'[]','[]','[]','[]',1,'2025-01-27 23:44:26','2025-01-27 23:44:26'),
(12,'App\\Models\\Business',6,'d574cce1-5db7-47b0-8915-66bfa52c5699','businesses','media-libraryOr0n12','media-libraryOr0n12','image/png','public','public',378,'[]','[]','[]','[]',1,'2025-01-28 02:21:24','2025-01-28 02:21:24'),
(13,'App\\Models\\Business',5,'30e3aa6a-ef62-4467-b812-fc016c4eeafb','tax_pin_certificates','The-Ultimate-Website-QA-Checklist','The-Ultimate-Website-QA-Checklist.xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','public','public',36109,'[]','[]','[]','[]',2,'2025-01-28 02:30:29','2025-01-28 02:30:29'),
(14,'App\\Models\\Business',5,'c8cdeb58-d98a-4573-a8d4-89195b77644f','business_license_certificates','Saparma ERP','Saparma-ERP.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document','public','public',1073655,'[]','[]','[]','[]',3,'2025-01-28 02:30:29','2025-01-28 02:30:29');
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
(9,'2025_01_14_025030_create_job_categories_table',1),
(10,'2025_01_14_025032_create_shifts_table',1),
(11,'2025_01_14_052658_create_departments_table',1),
(12,'2025_01_14_083726_create_employees_table',1),
(13,'2025_01_14_083727_create_spouses_table',1),
(14,'2025_01_14_083728_create_emergency_contacts_table',1),
(15,'2025_01_14_083729_create_academic_qualifications_table',1),
(16,'2025_01_14_083730_create_previous_employments_table',1),
(17,'2025_01_16_113024_create_employee_family_members_table',1),
(18,'2025_01_16_113239_create_employment_details_table',1),
(19,'2025_01_16_113352_create_employee_payment_details_table',1),
(20,'2025_01_16_113502_create_employee_contact_details_table',1),
(21,'2025_01_16_113640_create_employee_documents_table',1),
(22,'2025_01_17_014648_create_payroll_formulas_table',1),
(23,'2025_01_17_020359_create_payroll_formula_brackets_table',1),
(24,'2025_01_17_050013_create_reliefs_table',1),
(25,'2025_01_23_100649_create_leave_types_table',1),
(26,'2025_01_23_100656_create_leave_policies_table',1),
(27,'2025_01_23_123044_create_leave_requests_table',2),
(28,'2025_01_23_130905_create_leave_delegations_table',3),
(29,'2025_01_23_131303_create_leave_periods_table',4),
(36,'2025_01_17_050014_create_leave_type_lists_table',5),
(37,'2025_01_23_143454_create_leave_entitlements_table',5),
(38,'2025_01_24_025902_create_clients_table',5),
(39,'2024_12_28_024919_create_industries_table',6),
(40,'2025_01_27_063018_create_access_requests_table',7),
(41,'2025_01_28_050255_add_fields_to_businesses_table',8);
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
(2,'App\\Models\\User',2),
(2,'App\\Models\\User',3),
(2,'App\\Models\\User',4),
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
(1,'Core HR Management','core-hr-management','Essential HR features including employee management, attendance, and basic reporting',0.00,0.00,1,'[\"Employee Database\",\"Attendance Management\",\"Leave Management\",\"Basic Reports\",\"Document Management\"]','people-fill','2025-01-23 08:18:01','2025-01-23 08:18:01'),
(2,'Payroll Management','payroll-management','Complete payroll processing system with tax calculations and compliance',49.99,499.99,0,'[\"Salary Processing\",\"Tax Calculations\",\"Payslip Generation\",\"Statutory Compliance\",\"Multiple Payment Methods\",\"Payroll Reports\"]','wallet2','2025-01-23 08:18:01','2025-01-23 08:18:01'),
(3,'Recruitment & Onboarding','recruitment-onboarding','End-to-end recruitment solution from job posting to onboarding',39.99,399.99,0,'[\"Job Posting Management\",\"Applicant Tracking\",\"Interview Scheduling\",\"Candidate Assessment\",\"Onboarding Workflow\",\"Document Collection\"]','person-plus-fill','2025-01-23 08:18:01','2025-01-23 08:18:01'),
(4,'Performance Management','performance-management','Complete performance evaluation and goal tracking system',29.99,299.99,0,'[\"Goal Setting & Tracking\",\"Performance Reviews\",\"360\\u00b0 Feedback\",\"Skills Assessment\",\"Development Plans\",\"Performance Analytics\"]','graph-up-arrow','2025-01-23 08:18:01','2025-01-23 08:18:01'),
(5,'Learning Management','learning-management','Employee training and development platform',34.99,349.99,0,'[\"Course Management\",\"Training Schedules\",\"Learning Paths\",\"Assessment Tools\",\"Certification Tracking\",\"Training Reports\"]','journal-bookmark','2025-01-23 08:18:01','2025-01-23 08:18:01'),
(6,'Time & Attendance','time-attendance','Advanced time tracking and attendance management',24.99,249.99,0,'[\"Time Tracking\",\"Shift Management\",\"Overtime Calculation\",\"Leave Planning\",\"Attendance Reports\",\"Mobile Check-in\"]','clock-fill','2025-01-23 08:18:01','2025-01-23 08:18:01'),
(7,'Asset Management','asset-management','Track and manage company assets and resources',19.99,199.99,0,'[\"Asset Tracking\",\"Maintenance Scheduling\",\"Asset Assignment\",\"Inventory Management\",\"Asset Reports\",\"Depreciation Tracking\"]','box-seam','2025-01-23 08:18:01','2025-01-23 08:18:01'),
(8,'Employee Self-Service','employee-self-service','Portal for employees to manage their information and requests',14.99,149.99,0,'[\"Profile Management\",\"Leave Requests\",\"Expense Claims\",\"Document Access\",\"Payslip Download\",\"Benefits Enrollment\"]','person-workspace','2025-01-23 08:18:01','2025-01-23 08:18:01'),
(9,'CRM Integration','crm-integration','Customer relationship management integration with HR',44.99,449.99,0,'[\"Contact Management\",\"Lead Tracking\",\"Sales Pipeline\",\"Customer Support\",\"Email Integration\",\"Analytics & Reports\"]','people','2025-01-23 08:18:01','2025-01-23 08:18:01'),
(10,'Project Management','project-management','Project planning and resource management tools',39.99,399.99,0,'[\"Project Planning\",\"Task Management\",\"Resource Allocation\",\"Time Tracking\",\"Project Reports\",\"Team Collaboration\"]','clipboard-data','2025-01-23 08:18:01','2025-01-23 08:18:01');
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
  `rate` decimal(5,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payroll_formula_brackets_payroll_formula_id_foreign` (`payroll_formula_id`),
  CONSTRAINT `payroll_formula_brackets_payroll_formula_id_foreign` FOREIGN KEY (`payroll_formula_id`) REFERENCES `payroll_formulas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_formula_brackets`
--

LOCK TABLES `payroll_formula_brackets` WRITE;
/*!40000 ALTER TABLE `payroll_formula_brackets` DISABLE KEYS */;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_formulas_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_formulas`
--

LOCK TABLES `payroll_formulas` WRITE;
/*!40000 ALTER TABLE `payroll_formulas` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `previous_employments`
--

LOCK TABLES `previous_employments` WRITE;
/*!40000 ALTER TABLE `previous_employments` DISABLE KEYS */;
INSERT INTO `previous_employments` VALUES
(1,9,'Company XYZ','IT','123 Business Lane','Manager','Better opportunities','2016-01-01','2020-01-01','2025-01-27 23:44:26','2025-01-27 23:44:26');
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reliefs_slug_unique` (`slug`)
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'superadmin','web','2025-01-23 08:18:01','2025-01-23 08:18:01'),
(2,'business_owner','web','2025-01-23 08:18:01','2025-01-23 08:18:01'),
(3,'employee','web','2025-01-23 08:18:01','2025-01-23 08:18:01');
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
('QHRlv5aedqSPpPJQhB1UjDjlB6VxDSp9rzLeqtQo',1,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64; rv:135.0) Gecko/20100101 Firefox/135.0','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiM2VKQzBQWE1nQndLV214TFQ0SExTNUlhdHlMaENSb1NKNWNXbDkwayI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo2NToiaHR0cDovL2Ftc29sLmxvY2FsL2J1c2luZXNzL2FuemFyLXRlY2hub2xvZ2llcy9lbXBsb3llZXMvcmVnaXN0ZXIiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo2MDoiaHR0cDovL2Ftc29sLmxvY2FsL2J1c2luZXNzL2F1c2hpLXRlbmFuY3kvb3JnYW5pemF0aW9uLXNldHVwIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjIwOiJhY3RpdmVfYnVzaW5lc3Nfc2x1ZyI7czoxMzoiYXVzaGktdGVuYW5jeSI7fQ==',1738042380);
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
(1,1,'Morning Shift','morning-shift','08:00:00','16:00:00','Standard morning shift',1,'2025-01-23 08:18:01','2025-01-23 08:18:01'),
(2,1,'Afternoon Shift','afternoon-shift','16:00:00','00:00:00','Standard afternoon shift',1,'2025-01-23 08:18:01','2025-01-23 08:18:01'),
(3,1,'Night Shift','night-shift','00:00:00','08:00:00','Standard night shift',1,'2025-01-23 08:18:01','2025-01-23 08:18:01'),
(4,1,'Flexible Shift','flexible-shift','10:00:00','18:00:00','Flexible shift for employees',1,'2025-01-23 08:18:01','2025-01-23 08:18:01'),
(5,1,'Weekend Shift','weekend-shift','09:00:00','17:00:00','Shift during weekends',1,'2025-01-23 08:18:01','2025-01-23 08:18:01');
/*!40000 ALTER TABLE `shifts` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spouses`
--

LOCK TABLES `spouses` WRITE;
/*!40000 ALTER TABLE `spouses` DISABLE KEYS */;
INSERT INTO `spouses` VALUES
(9,9,'Jane','Doe','Ann','1995-01-01','987654321','Company ABC','+254711616016','789 Boulevard','1010 Place','2025-01-27 23:44:26','2025-01-27 23:44:26');
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
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statuses`
--

LOCK TABLES `statuses` WRITE;
/*!40000 ALTER TABLE `statuses` DISABLE KEYS */;
INSERT INTO `statuses` VALUES
(1,'setup',NULL,'App\\Models\\User',1,'2025-01-23 08:28:07','2025-01-23 08:28:07'),
(2,'module',NULL,'App\\Models\\Business',1,'2025-01-23 08:28:43','2025-01-23 08:28:43'),
(3,'module',NULL,'App\\Models\\User',1,'2025-01-23 08:28:43','2025-01-23 08:28:43'),
(4,'active',NULL,'App\\Models\\User',1,'2025-01-23 08:28:59','2025-01-23 08:28:59'),
(5,'active',NULL,'App\\Models\\Business',1,'2025-01-23 08:28:59','2025-01-23 08:28:59'),
(6,'active',NULL,'App\\Models\\LeavePeriod',1,'2025-01-23 11:12:39','2025-01-23 11:12:39'),
(7,'active',NULL,'App\\Models\\LeavePeriod',2,'2025-01-23 11:21:39','2025-01-23 11:21:39'),
(8,'inactive',NULL,'App\\Models\\LeavePeriod',1,'2025-01-23 11:12:39','2025-01-23 11:12:39'),
(11,'pending',NULL,'App\\Models\\AccessRequest',3,'2025-01-27 11:07:43','2025-01-27 11:07:43'),
(12,'pending',NULL,'App\\Models\\AccessRequest',1,'2025-01-27 11:09:31','2025-01-27 11:09:31'),
(13,'pending',NULL,'App\\Models\\AccessRequest',2,'2025-01-27 11:13:19','2025-01-27 11:13:19'),
(14,'pending',NULL,'App\\Models\\AccessRequest',3,'2025-01-27 11:18:06','2025-01-27 11:18:06'),
(15,'setup',NULL,'App\\Models\\User',2,'2025-01-27 11:44:33','2025-01-27 11:44:33'),
(16,'module',NULL,'App\\Models\\Business',2,'2025-01-27 12:08:44','2025-01-27 12:08:44'),
(17,'module',NULL,'App\\Models\\User',2,'2025-01-27 12:08:44','2025-01-27 12:08:44'),
(18,'active',NULL,'App\\Models\\User',2,'2025-01-27 12:18:22','2025-01-27 12:18:22'),
(19,'active',NULL,'App\\Models\\Business',2,'2025-01-27 12:18:22','2025-01-27 12:18:22'),
(20,'pending',NULL,'App\\Models\\AccessRequest',1,'2025-01-27 12:27:38','2025-01-27 12:27:38'),
(21,'setup',NULL,'App\\Models\\User',3,'2025-01-27 12:29:08','2025-01-27 12:29:08'),
(22,'module',NULL,'App\\Models\\Business',3,'2025-01-27 12:30:46','2025-01-27 12:30:46'),
(23,'module',NULL,'App\\Models\\User',3,'2025-01-27 12:30:46','2025-01-27 12:30:46'),
(24,'pending',NULL,'App\\Models\\AccessRequest',2,'2025-01-27 12:39:36','2025-01-27 12:39:36'),
(25,'setup',NULL,'App\\Models\\User',4,'2025-01-27 12:51:13','2025-01-27 12:51:13'),
(26,'module',NULL,'App\\Models\\Business',5,'2025-01-27 12:55:11','2025-01-27 12:55:11'),
(27,'active',NULL,'App\\Models\\Client',2,'2025-01-27 12:55:11','2025-01-27 12:55:11'),
(28,'module',NULL,'App\\Models\\User',4,'2025-01-27 12:55:11','2025-01-27 12:55:11'),
(29,'active',NULL,'App\\Models\\User',4,'2025-01-27 12:59:41','2025-01-27 12:59:41'),
(30,'active',NULL,'App\\Models\\Business',5,'2025-01-27 12:59:41','2025-01-27 12:59:41'),
(39,'active',NULL,'App\\Models\\User',13,'2025-01-27 23:44:26','2025-01-27 23:44:26'),
(40,'module',NULL,'App\\Models\\Business',6,'2025-01-28 02:21:24','2025-01-28 02:21:24'),
(41,'module',NULL,'App\\Models\\User',1,'2025-01-28 02:21:24','2025-01-28 02:21:24');
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
(1,'Sammy James','sammy@anzar.co.ke','+254797702066','Kenya','254',NULL,NULL,NULL,NULL,'$2y$12$Mhc.0jbv5XRQQ44pTBf5ielx.EWT/8.lzf72L.GyE.uE5Zk3BhAKK',NULL,'2025-01-23 08:28:07','2025-01-23 08:28:07'),
(4,'Remington Carter','sammyorondo2@gmail.com','+254711616012','Kenya','254',NULL,NULL,NULL,NULL,'$2y$12$VW/GaJjvTTfosZ1sL19PhesfBRrgWiHU8EKZYitdIkNfWanlzLm.a',NULL,'2025-01-27 12:51:13','2025-01-27 12:51:13'),
(13,'John Smith Doe','john.doe@example.com','+711616018','Kenya','254',NULL,NULL,NULL,NULL,'$2y$12$ek8qPJNwEd8EfEzPiSBHSe5OBzSfWtMOL5hHtFxLLSh2BaI6h5O86',NULL,'2025-01-27 23:44:26','2025-01-27 23:44:26');
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

-- Dump completed on 2025-01-28  8:56:38
