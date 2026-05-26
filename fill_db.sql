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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `properties`
--

LOCK TABLES `properties` WRITE;
/*!40000 ALTER TABLE `properties` DISABLE KEYS */;
INSERT INTO `properties` VALUES (16,'Новостройка в центре','Новый дом, бетон, хороший вид',15,4,100,'2026-05-26',NULL,NULL,16,1,17,1,'ул. Калараша, 60',4,120.00,19000000.00,11,'панельный','Центральный',2024,'черновая отделка'),(17,'1-комнатная квартира, 38 кв.м, ул. Пионерская, 15','Уютная однокомнатная квартира в спальном районе. Рядом школа, детский сад, магазины.',29,1,10,'2026-05-27',NULL,NULL,NULL,0,NULL,0,'ул. Пионерская, д. 15, кв. 42',1,38.50,3200000.00,3,'панельный','Индустриальный',1995,'косметический'),(18,'2-комнатная квартира, 54 кв.м, ул. Карла Маркса, 120','Двухкомнатная квартира на среднем этаже кирпичного дома. Требуется косметический ремонт.',30,1,10,'2026-05-27',NULL,NULL,NULL,0,NULL,0,'ул. Карла Маркса, д. 120, кв. 18',2,54.20,5400000.00,5,'кирпичный','Центральный',1988,'требует ремонта'),(19,'3-комнатная квартира, 72 кв.м, ул. Серышева, 34','Просторная квартира для большой семьи. Отличная планировка, все комнаты раздельные.',31,1,10,'2026-05-27',NULL,NULL,NULL,0,NULL,0,'ул. Серышева, д. 34, кв. 99',3,72.00,8100000.00,2,'панельный','Кировский',2002,'евроремонт'),(20,'Квартира-студия, 26 кв.м, пер. Молдавский, 5','Современная студия в новом монолитном доме. Видовой этаж, встроенная мебель.',32,1,10,'2026-05-27',NULL,NULL,37,0,NULL,0,'пер. Молдавский, д. 5, кв. 142',1,26.80,2900000.00,14,'монолитный','Индустриальный',2018,'евроремонт'),(21,'1-комнатная квартира, 42 кв.м, ул. Воронежская, 28','Просторная однушка в тихом районе. Застекленный балкон, новая сантехника.',29,1,10,'2026-05-27',NULL,NULL,38,0,NULL,0,'ул. Воронежская, д. 28, кв. 3',1,42.10,3900000.00,1,'панельный','Железнодорожный',2010,'косметический'),(22,'2-комнатная квартира, 60 кв.м, ул. Краснореченская, 157','Улучшенная планировка. Окна выходят на две стороны. Установлены кондиционеры.',30,1,10,'2026-05-27',NULL,NULL,39,0,NULL,0,'ул. Краснореченская, д. 157, кв. 77',2,60.50,5950000.00,7,'кирпичный','Индустриальный',1991,'дизайнерский'),(23,'2-комнатная квартира, 48 кв.м, ул. Ленина, 50','Отличное предложение в самом центре города. Квартира освобождена, документы готовы.',31,5,50,'2026-05-27',NULL,NULL,37,1,33,0,'ул. Ленина, д. 50, кв. 11',2,48.00,6200000.00,4,'кирпичный','Центральный',1974,'косметический'),(24,'3-комнатная квартира, 80 кв.м, ул. Дикопольцева, 10','Уютная квартира с окнами во двор. Закрытая территория, видеонаблюдение.',32,5,50,'2026-05-27',NULL,NULL,38,1,34,0,'ул. Дикопольцева, д. 10, кв. 5',3,80.20,9500000.00,6,'панельный','Центральный',1999,'евроремонт'),(25,'1-комнатная квартира, 35 кв.м, ул. Шеронова, 103','Студийная планировка. Качественные материалы отделки. Остается вся мебель и техника.',29,5,50,'2026-05-27',NULL,NULL,39,1,35,0,'ул. Шеронова, д. 103, кв. 81',1,35.40,4300000.00,8,'монолитный','Центральный',2012,'евроремонт'),(26,'2-комнатная квартира, 56 кв.м, ул. Флегонтова, 2','Хороший кирпичный дом от надежного застройщика. Развитый микрорайон, чистый подъезд.',30,5,50,'2026-05-27',NULL,NULL,37,1,36,0,'ул. Флегонтова, д. 2, кв. 199',2,56.70,7100000.00,9,'кирпичный','Индустриальный',2014,'дизайнерский'),(27,'3-комнатная квартира, 90 кв.м, ул. Калинина, 8','Элитный дом в историческом центре. Высокие потолки 3м. Подземный паркинг.',31,3,80,'2026-05-27',NULL,NULL,38,1,33,1,'ул. Калинина, д. 8, кв. 14',3,90.00,14000000.00,3,'кирпичный','Центральный',2005,'дизайнерский'),(28,'1-комнатная квартира, 40 кв.м, ул. Вахова, 7','Уютная квартира с красивым видом на Амур. Солнечная сторона.',32,3,80,'2026-05-27',NULL,NULL,39,1,34,1,'ул. Вахова, д. 7, кв. 53',1,40.50,4900000.00,12,'панельный','Индустриальный',2011,'косметический'),(29,'2-комнатная квартира, 52 кв.м, ул. Запарина, 87','Прекрасное состояние. Новая электропроводка и радиаторы. Готовы быстро выйти на сделку.',29,3,80,'2026-05-27',NULL,NULL,37,1,35,1,'ул. Запарина, д. 87, кв. 26',2,52.30,6400000.00,5,'кирпичный','Центральный',1985,'евроремонт'),(30,'4-комнатная квартира, 105 кв.м, ул. Тургенева, 48','Огромная квартира для ценителей пространства. Два санузла, прачечная.',30,3,80,'2026-05-27',NULL,NULL,38,1,36,1,'ул. Тургенева, д. 48, кв. 17',4,105.00,16500000.00,7,'монолитный','Центральный',2010,'дизайнерский'),(31,'1-комнатная квартира, 30 кв.м, ул. Суворова, 64','Компактная однокомнатная квартира. Рядом торговый центр ЭВР и рынок.',31,4,100,'2026-05-27',NULL,NULL,39,1,33,1,'ул. Суворова, д. 64, кв. 102',1,30.10,2850000.00,2,'панельный','Индустриальный',1978,'косметический'),(32,'2-комнатная квартира, 44 кв.м, ул. Гамарника, 12','Уютная хрущевка в отличном районе. Зеленый двор, доброжелательные соседи.',32,4,100,'2026-05-27',NULL,NULL,37,1,34,1,'ул. Гамарника, д. 12, кв. 45',2,44.00,4100000.00,3,'панельный','Центральный',1968,'косметический'),(33,'3-комнатная квартира, 68 кв.м, ул. Павла Морозова, 93','Квартира в современном молодежном микрорайоне. Большая кухня, раздельный санузел.',29,4,100,'2026-05-27',NULL,NULL,38,1,35,1,'ул. Павла Морозова, д. 93, кв. 214',3,68.40,7800000.00,8,'панельный','Индустриальный',2013,'евроремонт'),(34,'2-комнатная квартира, 58 кв.м, ул. Дзержинского, 22','Сталинка в центре города. Высокие потолки, железобетонные перекрытия.',30,4,100,'2026-05-27',NULL,NULL,39,1,36,1,'ул. Дзержинского, д. 22, кв. 6',2,58.00,6900000.00,2,'кирпичный','Центральный',1957,'косметический'),(35,'1-комнатная квартира, 33 кв.м, пер. Озерный, 4','Уютная теплая квартира. Вся инфраструктура в шаговой доступности.',31,4,100,'2026-05-27',NULL,NULL,37,1,33,1,'пер. Озерный, д. 4, кв. 19',1,33.00,3300000.00,5,'кирпичный','Центральный',1980,'косметический'),(36,'2-комнатная квартира, 50 кв.м, ул. Орджоникидзе, 7','Хороший спальный район. Комнаты изолированные. Один собственник.',32,4,100,'2026-05-27',NULL,NULL,38,1,34,1,'ул. Орджоникидзе, д. 7, кв. 84',2,50.10,4500000.00,4,'панельный','Кировский',1984,'косметический');
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
INSERT INTO `property_agents` VALUES (16,15,'seller'),(16,16,'lead_agent'),(16,17,'buyer'),(17,29,'seller'),(18,30,'seller'),(19,31,'seller'),(20,32,'seller'),(20,37,'lead_agent'),(21,29,'seller'),(21,38,'lead_agent'),(22,30,'seller'),(22,39,'lead_agent'),(23,31,'seller'),(23,33,'buyer'),(23,37,'lead_agent'),(24,32,'seller'),(24,34,'buyer'),(24,38,'lead_agent'),(25,29,'seller'),(25,35,'buyer'),(25,39,'lead_agent'),(26,30,'seller'),(26,36,'buyer'),(26,37,'lead_agent'),(27,31,'seller'),(27,33,'buyer'),(27,38,'lead_agent'),(28,32,'seller'),(28,34,'buyer'),(28,39,'lead_agent'),(29,29,'seller'),(29,35,'buyer'),(29,37,'lead_agent'),(30,30,'seller'),(30,36,'buyer'),(30,38,'lead_agent'),(31,31,'seller'),(31,33,'buyer'),(31,39,'lead_agent'),(32,32,'seller'),(32,34,'buyer'),(32,37,'lead_agent'),(33,29,'seller'),(33,35,'buyer'),(33,38,'lead_agent'),(34,30,'seller'),(34,36,'buyer'),(34,39,'lead_agent'),(35,31,'seller'),(35,33,'buyer'),(35,37,'lead_agent'),(36,32,'seller'),(36,34,'buyer'),(36,38,'lead_agent');
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
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `property_status_history`
--

LOCK TABLES `property_status_history` WRITE;
/*!40000 ALTER TABLE `property_status_history` DISABLE KEYS */;
INSERT INTO `property_status_history` VALUES (39,16,1,15,'2026-05-26 10:43:05'),(40,16,2,15,'2026-05-26 10:46:23'),(41,16,5,16,'2026-05-26 11:14:47'),(42,16,3,17,'2026-05-26 11:15:22'),(43,16,4,16,'2026-05-26 11:15:48'),(44,17,1,29,'2026-05-26 14:00:00'),(45,18,1,30,'2026-05-26 14:00:00'),(46,19,1,31,'2026-05-26 14:00:00'),(47,20,1,32,'2026-05-26 14:00:00'),(48,21,1,29,'2026-05-26 14:00:00'),(49,22,1,30,'2026-05-26 14:00:00'),(50,23,1,31,'2026-05-26 14:00:00'),(51,23,2,31,'2026-05-26 15:00:00'),(52,23,5,37,'2026-05-26 16:00:00'),(53,24,1,32,'2026-05-26 14:00:00'),(54,24,2,32,'2026-05-26 15:00:00'),(55,24,5,38,'2026-05-26 16:00:00'),(56,25,1,29,'2026-05-26 14:00:00'),(57,25,2,29,'2026-05-26 15:00:00'),(58,25,5,39,'2026-05-26 16:00:00'),(59,26,1,30,'2026-05-26 14:00:00'),(60,26,2,30,'2026-05-26 15:00:00'),(61,26,5,37,'2026-05-26 16:00:00'),(62,27,1,31,'2026-05-26 14:00:00'),(63,27,2,31,'2026-05-26 15:00:00'),(64,27,5,38,'2026-05-26 16:00:00'),(65,27,3,33,'2026-05-26 17:00:00'),(66,28,1,32,'2026-05-26 14:00:00'),(67,28,2,32,'2026-05-26 15:00:00'),(68,28,5,39,'2026-05-26 16:00:00'),(69,28,3,34,'2026-05-26 17:00:00'),(70,29,1,29,'2026-05-26 14:00:00'),(71,29,2,29,'2026-05-26 15:00:00'),(72,29,5,37,'2026-05-26 16:00:00'),(73,29,3,35,'2026-05-26 17:00:00'),(74,30,1,30,'2026-05-26 14:00:00'),(75,30,2,30,'2026-05-26 15:00:00'),(76,30,5,38,'2026-05-26 16:00:00'),(77,30,3,36,'2026-05-26 17:00:00'),(78,31,1,31,'2026-05-26 14:00:00'),(79,31,2,31,'2026-05-26 15:00:00'),(80,31,5,39,'2026-05-26 16:00:00'),(81,31,3,33,'2026-05-26 17:00:00'),(82,31,4,39,'2026-05-26 18:00:00'),(83,32,1,32,'2026-05-26 14:00:00'),(84,32,2,32,'2026-05-26 15:00:00'),(85,32,5,37,'2026-05-26 16:00:00'),(86,32,3,34,'2026-05-26 17:00:00'),(87,32,4,37,'2026-05-26 18:00:00'),(88,33,1,29,'2026-05-26 14:00:00'),(89,33,2,29,'2026-05-26 15:00:00'),(90,33,5,38,'2026-05-26 16:00:00'),(91,33,3,35,'2026-05-26 17:00:00'),(92,33,4,38,'2026-05-26 18:00:00'),(93,34,1,30,'2026-05-26 14:00:00'),(94,34,2,30,'2026-05-26 15:00:00'),(95,34,5,39,'2026-05-26 16:00:00'),(96,34,3,36,'2026-05-26 17:00:00'),(97,34,4,39,'2026-05-26 18:00:00'),(98,35,1,31,'2026-05-26 14:00:00'),(99,35,2,31,'2026-05-26 15:00:00'),(100,35,5,37,'2026-05-26 16:00:00'),(101,35,3,33,'2026-05-26 17:00:00'),(102,35,4,37,'2026-05-26 18:00:00'),(103,36,1,32,'2026-05-26 14:00:00'),(104,36,2,32,'2026-05-26 15:00:00'),(105,36,5,38,'2026-05-26 16:00:00'),(106,36,3,34,'2026-05-26 17:00:00'),(107,36,4,38,'2026-05-26 18:00:00');
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

-- Dump completed on 2026-05-27  0:52:44
