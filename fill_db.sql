-- MySQL dump 10.13  Distrib 5.7.24, for Win64 (x86_64)
--
-- Host: localhost    Database: real_estate_manager_db
-- ------------------------------------------------------
-- Server version	5.7.24

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `properties`
--

DROP TABLE IF EXISTS `properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `creator_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `progress_percent` int(11) DEFAULT '0',
  `start_date` date NOT NULL,
  `planned_end_date` date DEFAULT NULL,
  `repository_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `realtor_id` int(11) DEFAULT NULL,
  `realtor_accepted` tinyint(4) DEFAULT '0',
  `buyer_id` int(11) DEFAULT NULL,
  `buyer_approved` tinyint(4) DEFAULT '0',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rooms` int(11) DEFAULT NULL,
  `area` decimal(10,2) DEFAULT NULL,
  `price` decimal(15,2) DEFAULT NULL,
  `floor` int(11) DEFAULT NULL,
  `house_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `build_year` int(11) DEFAULT NULL,
  `renovation` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `creator_id` (`creator_id`),
  KEY `status_id` (`status_id`),
  KEY `fk_properties_realtor` (`realtor_id`),
  KEY `fk_properties_buyer` (`buyer_id`),
  CONSTRAINT `fk_properties_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_properties_realtor` FOREIGN KEY (`realtor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `properties_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `property_statuses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `properties`
--

LOCK TABLES `properties` WRITE;
/*!40000 ALTER TABLE `properties` DISABLE KEYS */;
INSERT INTO `properties` VALUES (16,'Новостройка в центре','Новый дом, бетон, хороший вид',15,4,100,'2026-05-26',NULL,NULL,16,1,17,1,'ул. Калараша, 60',4,120.00,19000000.00,11,'панельный','Центральный',2024,'черновая отделка');
/*!40000 ALTER TABLE `properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `property_agents`
--

DROP TABLE IF EXISTS `property_agents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `property_agents` (
  `property_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`property_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `property_agents_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  CONSTRAINT `property_agents_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `property_agents`
--

LOCK TABLES `property_agents` WRITE;
/*!40000 ALTER TABLE `property_agents` DISABLE KEYS */;
INSERT INTO `property_agents` VALUES (16,15,'seller'),(16,16,'lead_agent'),(16,17,'buyer');
/*!40000 ALTER TABLE `property_agents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `property_status_history`
--

DROP TABLE IF EXISTS `property_status_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `property_status_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `changed_by` int(11) NOT NULL,
  `changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `property_id` (`property_id`),
  KEY `status_id` (`status_id`),
  KEY `changed_by` (`changed_by`),
  CONSTRAINT `property_status_history_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  CONSTRAINT `property_status_history_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `property_statuses` (`id`),
  CONSTRAINT `property_status_history_ibfk_3` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `property_status_history`
--

LOCK TABLES `property_status_history` WRITE;
/*!40000 ALTER TABLE `property_status_history` DISABLE KEYS */;
INSERT INTO `property_status_history` VALUES (39,16,1,15,'2026-05-26 10:43:05'),(40,16,2,15,'2026-05-26 10:46:23'),(41,16,5,16,'2026-05-26 11:14:47'),(42,16,3,17,'2026-05-26 11:15:22'),(43,16,4,16,'2026-05-26 11:15:48');
/*!40000 ALTER TABLE `property_status_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `property_statuses`
--

DROP TABLE IF EXISTS `property_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `property_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `property_statuses`
--

LOCK TABLES `property_statuses` WRITE;
/*!40000 ALTER TABLE `property_statuses` DISABLE KEYS */;
INSERT INTO `property_statuses` VALUES (2,'Готов к просмотру'),(3,'Забронировано'),(5,'На просмотре'),(4,'Продано'),(1,'Создано');
/*!40000 ALTER TABLE `property_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (4,'admin'),(2,'buyer'),(1,'realtor'),(3,'seller');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES (16,1),(37,1),(38,1),(39,1),(17,2),(33,2),(34,2),(35,2),(36,2),(15,3),(29,3),(30,3),(31,3),(32,3),(14,4);
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_info` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (14,'Администратор Системы','admin@example.com','$2y$10$j.ElFo5RV/K62ussC9xWq.z00AGVzL4LoDq6bp8BVPwfD63jU9j1y','Администрация',1),(15,'Петров Продавец Петрович','petrov@example.com','$2y$10$lRk722zs9xGQeqMDI4hQCexwOkafR.eCpwLVF84W1cNOyclVY0z8.','+7 (914) 492-99-21',1),(16,'Иванов Риелтор Иванович','ivanov@example.com','$2y$10$BHyZvlVYkIFyrCxEEA.2euhbCCvif41Cg4S2IxAkvsrGJiNR2gxJq','+7 (914) 412-91-04',1),(17,'Семен Покупатель Семенович','semenov@example.com','$2y$10$o2BIEN4G2rdMQ3Q0ba3wZu.fLt.5vLvvoFcB/WGU0xuvJhGkFASmq','+7 (914) 482-84-23',1),(29,'Продавец Александр','seller_alex@example.com','$2y$10$fB29plEmT.bJPNYm4aLuQORVuHTYROarToRJeq8hIIk8ZIZ06HHWy','+7 (900) 100-01-01, Продавец',1),(30,'Продавец Борис','seller_boris@example.com','$2y$10$pxEZMxZ732W7zePsTZUMSOwDrCZAu6cavL6d4CXMxC47V.AobAH1K','+7 (900) 100-02-02, Продавец',1),(31,'Продавец Виктория','seller_vik@example.com','$2y$10$Q8kIp010gHThQUyMDH336eF1Nes19zcLY7rbXV82dMyCQVnRRUefO','+7 (900) 100-03-03, Продавец',1),(32,'Продавец Галина','seller_galina@example.com','$2y$10$gaExbnqI6IAGm0O29RGcE.wbEep8MTHcuhvplCKd3VfgAfhW8.ZpK','+7 (900) 100-04-04, Продавец',1),(33,'Покупатель Дмитрий','buyer_dmitry@example.com','$2y$10$HA669FGAQGtUaUpC1WwN3ubOjdBvws5nLY3TRDt8jpDOkjhCb22j6','+7 (900) 200-01-01, Покупатель',1),(34,'Покупатель Елена','buyer_elena@example.com','$2y$10$NSiibkTnmqgugcLLTbJv..9c1bE5BQkb2Krf42wdehgQMm35.SKgC','+7 (900) 200-02-02, Покупатель',1),(35,'Покупатель Жанна','buyer_zhanna@example.com','$2y$10$sXj9BytBhLVF8QtzcfAwm.9tWISIIoEcr9AF5K0e6uTHmTTsyitNi','+7 (900) 200-03-03, Покупатель',1),(36,'Покупатель Захар','buyer_zakhar@example.com','$2y$10$AzD31UBmI0OoBkVIHGJPfOtfm7KVWVwTPE19hBmGkI4PsRd1P2xR2','+7 (900) 200-04-04, Покупатель',1),(37,'Риелтор Кирилл','realtor_kirill@example.com','$2y$10$lAX0.CKxVAdjtBJyS/PIcOtkDuFVx2zxw9dQZ4w6zCcVGXADgyuEW','+7 (900) 300-01-01, Агентство \"Инград\"',1),(38,'Риелтор Леонид','realtor_leonid@example.com','$2y$10$uIs2i5vl5s1HleOKc69pZerU6/5Fga.6W3rg2/0Mhy.5vp8fXqHgW','+7 (900) 300-02-02, Агентство \"Миэль\"',1),(39,'Риелтор Мария','realtor_maria@example.com','$2y$10$Yki6Sz1UAvMa5crci4dHOOJuax31VsCJ9JelnTQoWq2mssDbW9ZHW','+7 (900) 300-03-03, Частный риелтор',1);
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

-- Dump completed on 2026-05-27  0:45:12
