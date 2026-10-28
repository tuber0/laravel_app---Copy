-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: db_pedros
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appointments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `client_name` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `service_id` bigint(20) unsigned NOT NULL,
  `artist_id` bigint(20) unsigned DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `notes` varchar(255) DEFAULT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `appointments_service_id_foreign` (`service_id`),
  KEY `appointments_artist_id_foreign` (`artist_id`),
  KEY `appointments_user_id_foreign` (`user_id`),
  CONSTRAINT `appointments_artist_id_foreign` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE SET NULL,
  CONSTRAINT `appointments_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appointments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointments`
--

LOCK TABLES `appointments` WRITE;
/*!40000 ALTER TABLE `appointments` DISABLE KEYS */;
INSERT INTO `appointments` VALUES (1,3,'Juan Dela Cruz','09191234567',1,1,350.00,'2026-10-21','10:00:00','completed','Customer requested clean skin fade.','45 min','2026-10-21 23:37:59','2026-10-21 23:37:59'),(2,3,'Juan Dela Cruz','09191234567',1,1,350.00,'2026-10-22','14:00:00','confirmed','Regular touchup appointment.','45 min','2026-10-21 23:37:59','2026-10-21 23:37:59'),(3,3,'Maria Clara','09209876543',4,2,1500.00,'2026-10-22','16:00:00','in-progress','First time getting box braids.','180 min','2026-10-21 23:37:59','2026-10-21 23:37:59');
/*!40000 ALTER TABLE `appointments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `artists`
--

DROP TABLE IF EXISTS `artists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `specialty` text DEFAULT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'Barbering',
  `rating` decimal(3,2) NOT NULL DEFAULT 5.00,
  `bio` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `portfolio_images` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `artists_email_unique` (`email`),
  KEY `artists_user_id_foreign` (`user_id`),
  CONSTRAINT `artists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `artists`
--

LOCK TABLES `artists` WRITE;
/*!40000 ALTER TABLE `artists` DISABLE KEYS */;
INSERT INTO `artists` VALUES (1,4,'Pedro Martinez','pedro.m@artisan.shop',NULL,'[\"Master Barber\",\"Fade Cut\"]','Barbering',4.90,NULL,'https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?w=400&q=80','[\"https:\\/\\/images.unsplash.com\\/photo-1621605815841-28d944683b83?w=500&q=80\",\"https:\\/\\/images.unsplash.com\\/photo-1503951914875-452162b0f3f1?w=500&q=80\",\"https:\\/\\/images.unsplash.com\\/photo-1599351431247-f577f5d48c8a?w=500&q=80\"]','2026-10-21 23:33:42','2026-10-21 23:37:57'),(2,5,'Maya Johnson','maya.j@artisan.shop',NULL,'[\"Braiding Specialist\",\"Cornrows\"]','Hair Styling',4.80,NULL,'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=400&q=80','[\"https:\\/\\/images.unsplash.com\\/photo-1582095133179-bf1089201970?w=500&q=80\",\"https:\\/\\/images.unsplash.com\\/photo-1574621100236-d25b64cfdd63?w=500&q=80\"]','2026-10-21 23:33:42','2026-10-21 23:37:57'),(3,6,'Rico Santos','rico.s@artisan.shop',NULL,'[\"Tattoo Artist\",\"Realism\"]','Tattoo',5.00,NULL,'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&q=80','[\"https:\\/\\/images.unsplash.com\\/photo-1598371839696-5c5bb00bdc28?w=500&q=80\",\"https:\\/\\/images.unsplash.com\\/photo-1590246814883-57c511e76533?w=500&q=80\",\"https:\\/\\/images.unsplash.com\\/photo-1611501275019-9b5cda994e8d?w=500&q=80\"]','2026-10-21 23:33:42','2026-10-21 23:37:58'),(4,7,'Lisa Chen','lisa.c@artisan.shop',NULL,'[\"Piercing Specialist\"]','Piercing',4.90,NULL,'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&q=80','[\"https:\\/\\/images.unsplash.com\\/photo-1611930528536-482d46ed3021?w=500&q=80\",\"https:\\/\\/images.unsplash.com\\/photo-1540206351-d6465b3ac5c1?w=500&q=80\"]','2026-10-21 23:33:42','2026-10-21 23:37:59'),(5,8,'Jay Williams','jay.w@artisan.shop',NULL,'[\"Barber & Cap Specialist\"]','Others',4.70,NULL,'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&q=80','[\"https:\\/\\/images.unsplash.com\\/photo-1532710093739-9470acff0190?w=500&q=80\",\"https:\\/\\/images.unsplash.com\\/photo-1512690196162-75d3d0299ecb?w=500&q=80\"]','2026-10-21 23:33:42','2026-10-21 23:37:59');
/*!40000 ALTER TABLE `artists` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2026_03_29_110523_create_artists_table',1),(6,'2026_03_29_110524_create_services_table',1),(7,'2026_03_29_110525_create_appointments_table',1),(8,'2026_03_31_082106_add_role_to_users_table',1),(9,'2026_03_31_175018_create_payments_table',1),(10,'2026_04_04_000000_add_user_id_to_appointments_table',1),(11,'2026_04_04_000001_add_rating_to_artists_table',1),(12,'2026_04_04_000003_add_portfolio_to_artists_table',1),(13,'2026_04_04_000004_add_category_to_services_table',1),(14,'2026_04_07_073338_add_profile_fields_to_users_table',1),(15,'2026_04_15_000000_make_appointment_id_nullable_on_payments_table',1),(16,'2026_04_16_165658_change_specialty_column_to_json_in_artists_table',1),(17,'2026_04_19_174642_create_schedule_requests_table',1),(18,'2026_04_19_180615_add_security_fields_to_pos_system',1),(19,'2026_04_20_050500_add_user_id_to_artists_table',1),(20,'2026_04_28_180538_create_reviews_table',1),(21,'2026_04_28_193611_add_items_to_payments_table',1),(22,'2026_04_30_014114_change_rating_type_in_reviews_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `appointment_id` bigint(20) unsigned DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(255) NOT NULL DEFAULT 'cash',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `transaction_id` varchar(255) DEFAULT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`items`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_appointment_id_foreign` (`appointment_id`),
  CONSTRAINT `payments_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,1,350.00,'cash','completed','TXN-6AD9BD579FBE4','[{\"name\":\"Classic Haircut\",\"price\":350,\"quantity\":1}]','2026-10-21 23:37:59','2026-10-21 23:37:59');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `artist_id` bigint(20) unsigned NOT NULL,
  `appointment_id` bigint(20) unsigned DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_user_id_foreign` (`user_id`),
  KEY `reviews_artist_id_foreign` (`artist_id`),
  KEY `reviews_appointment_id_foreign` (`appointment_id`),
  CONSTRAINT `reviews_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reviews_artist_id_foreign` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (1,3,1,1,5.0,'Pedro gave me the sharpest fade I have ever had! Highly recommend.','2026-10-21 23:37:59','2026-10-21 23:37:59');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedule_requests`
--

DROP TABLE IF EXISTS `schedule_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schedule_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `artist_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `time` time DEFAULT NULL,
  `type` enum('open','close') NOT NULL DEFAULT 'close',
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `schedule_requests_artist_id_foreign` (`artist_id`),
  CONSTRAINT `schedule_requests_artist_id_foreign` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedule_requests`
--

LOCK TABLES `schedule_requests` WRITE;
/*!40000 ALTER TABLE `schedule_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `schedule_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `services` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'Barbering',
  `description` text DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,'Classic Haircut','Barbering',NULL,350,'45 min','2026-10-21 23:33:42','2026-10-21 23:33:42'),(2,'Beard Trim','Barbering',NULL,200,'30 min','2026-10-21 23:33:42','2026-10-21 23:33:42'),(3,'Fade Cut','Barbering',NULL,450,'60 min','2026-10-21 23:33:42','2026-10-21 23:33:42'),(4,'Box Braids','Hair Styling',NULL,1500,'180 min','2026-10-21 23:33:42','2026-10-21 23:33:42'),(5,'Cornrows','Hair Styling',NULL,800,'120 min','2026-10-21 23:33:42','2026-10-21 23:33:42'),(6,'Small Tattoo','Tattoo',NULL,1200,'60 min','2026-10-21 23:33:42','2026-10-21 23:33:42'),(7,'Ear Piercing','Piercing',NULL,300,'20 min','2026-10-21 23:33:42','2026-10-21 23:33:42'),(8,'Cap Cleaning','Others',NULL,150,'30 min','2026-10-21 23:33:42','2026-10-21 23:33:42');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
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
  `profile_picture` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'customer',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'System Admin','admin@pedro.shop','09171234567',NULL,'2026-10-21 23:37:56','$2y$10$6Hba49Yoev3Cd8GMWISUKuc49sWOMAB14wMBpwJHJL44c6FAwT7Ma','admin',NULL,'2026-10-21 23:33:42','2026-10-21 23:37:56'),(2,'Regular Staff','staff@pedro.shop','09181234567',NULL,'2026-10-21 23:37:56','$2y$10$VxEQc8ra5mOcejBGgzOl0eZp3CtkM.7TYYuBo/n/qvJF0N29du0GO','staff',NULL,'2026-10-21 23:33:42','2026-10-21 23:37:56'),(3,'Juan Dela Cruz','customer@pedro.shop','09191234567',NULL,'2026-10-21 23:37:57','$2y$10$uF54q35yYrhl/2MY.9X1AeO4jq5XvkikUi9eMJ4vO3LLO/GyyIjDy','customer',NULL,'2026-10-21 23:37:57','2026-10-21 23:37:57'),(4,'Pedro Martinez','pedro.m@artisan.shop',NULL,NULL,'2026-10-21 23:37:57','$2y$10$bjKEqbxH3MxnSC.eMzUix.3gFsgFn5LGhDi1TnAkqyUG3sWfhXB82','staff',NULL,'2026-10-21 23:37:57','2026-10-21 23:37:57'),(5,'Maya Johnson','maya.j@artisan.shop',NULL,NULL,'2026-10-21 23:37:57','$2y$10$EeLa8ietu3HwoRb0vcohY.Aq4.zY5X3kCZgjpEuchxM1z0Z9DTCHW','staff',NULL,'2026-10-21 23:37:57','2026-10-21 23:37:57'),(6,'Rico Santos','rico.s@artisan.shop',NULL,NULL,'2026-10-21 23:37:58','$2y$10$uoikyaBi6I4Kiv6/yntrGO6l80.WPNXOIlIgJr6kJmdtkOHXsqmxC','staff',NULL,'2026-10-21 23:37:58','2026-10-21 23:37:58'),(7,'Lisa Chen','lisa.c@artisan.shop',NULL,NULL,'2026-10-21 23:37:59','$2y$10$5QMzaAPxFSO/ZBTr4yVKBe6tBPUQyndrQ6x6GDxoyGh8EyTWmVypq','staff',NULL,'2026-10-21 23:37:59','2026-10-21 23:37:59'),(8,'Jay Williams','jay.w@artisan.shop',NULL,NULL,'2026-10-21 23:37:59','$2y$10$F1RinmxLSipmBZtTpjnGlOY22aqwKkgpOujlfflkqxh4XsdlAmIca','staff',NULL,'2026-10-21 23:37:59','2026-10-21 23:37:59');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-22 15:41:32
