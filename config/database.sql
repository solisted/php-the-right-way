-- MySQL dump 10.13  Distrib 8.4.5-5, for Linux (x86_64)
--
-- Host: localhost    Database: ivan
-- ------------------------------------------------------
-- Server version	8.4.5-5

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*!50717 SELECT COUNT(*) INTO @rocksdb_has_p_s_session_variables FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'performance_schema' AND TABLE_NAME = 'session_variables' */;
/*!50717 SET @rocksdb_get_is_supported = IF (@rocksdb_has_p_s_session_variables, 'SELECT COUNT(*) INTO @rocksdb_is_supported FROM performance_schema.session_variables WHERE VARIABLE_NAME=\'rocksdb_bulk_load\'', 'SELECT 0') */;
/*!50717 PREPARE s FROM @rocksdb_get_is_supported */;
/*!50717 EXECUTE s */;
/*!50717 DEALLOCATE PREPARE s */;
/*!50717 SET @rocksdb_enable_bulk_load = IF (@rocksdb_is_supported, 'SET SESSION rocksdb_bulk_load = 1', 'SET @rocksdb_dummy_bulk_load = 0') */;
/*!50717 PREPARE s FROM @rocksdb_enable_bulk_load */;
/*!50717 EXECUTE s */;
/*!50717 DEALLOCATE PREPARE s */;

--
-- Table structure for table `actions`
--

DROP TABLE IF EXISTS `actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `actions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actions`
--

LOCK TABLES `actions` WRITE;
/*!40000 ALTER TABLE `actions` DISABLE KEYS */;
INSERT INTO `actions` VALUES (1,'CreateUser','Add new user'),(2,'ReadUser','Retrieve user information'),(3,'UpdateUser','Edit user information'),(4,'DeleteUser','Mark user as deleted'),(5,'ListUsers','Retrieve list of all users'),(6,'CreateRole','Add new role'),(7,'ReadRole','Retrieve role information'),(8,'UpdateRole','Edit role information'),(9,'DeleteRole','Mark role as deleted'),(10,'ListRoles','Retrieve list of all roles'),(11,'CreateAction','Add new action'),(12,'ReadAction','Retrieve action information'),(13,'UpdateAction','Edit action information'),(14,'DeleteAction','Mark action as deleted'),(15,'ListActions','Retrieve list of all actions'),(16,'CreateAttribute','Add new attribute'),(17,'ReadAttribute','Retrieve attribute information'),(18,'UpdateAttribute','Edit attribute information'),(19,'DeleteAttribute','Delete attribute information'),(20,'ListAttributes','Retrieve list of all attributes'),(21,'CreateCategory','Add new category'),(22,'ReadCategory','Retrieve category information'),(23,'UpdateCategory','Edit category information'),(24,'DeleteCategory','Delete category information'),(25,'ListCategories','Retrieve list of all categories'),(26,'CreateProduct','Add new product'),(27,'ReadProduct','Retrieve product information'),(28,'UpdateProduct','Edit product information'),(29,'DeleteProduct','Delete product information'),(30,'ListProducts','Retrieve list of all products'),(31,'CreateCustomer','Add new customer'),(32,'ReadCustomer','Retrieve customer information'),(33,'UpdateCustomer','Edit customer information'),(34,'DeleteCustomer','Delete customer information'),(35,'ListCustomers','Retrieve list of all customers'),(36,'CreateOrder','Add new order'),(37,'ReadOrder','Retrieve order information'),(38,'UpdateOrder','Edit order information'),(39,'DeleteOrder','Mark order as deleted'),(40,'ListOrders','Retrieve list of all orders');
/*!40000 ALTER TABLE `actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attributes`
--

DROP TABLE IF EXISTS `attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attributes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attributes`
--

LOCK TABLES `attributes` WRITE;
/*!40000 ALTER TABLE `attributes` DISABLE KEYS */;
INSERT INTO `attributes` VALUES (5,'Brand'),(10,'Capacity'),(6,'Family'),(9,'Memory Size'),(7,'Model'),(8,'Number Of Cores');
/*!40000 ALTER TABLE `attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `lft` bigint unsigned NOT NULL,
  `rgt` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `lft` (`lft`,`rgt`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Computers',1,22),(2,'Computer Parts',2,9),(3,'Processors',3,4),(4,'Memory',5,6),(5,'Peripherals',10,15),(6,'Keyboards',11,12),(7,'Mouse',13,14),(8,'Storage',16,21),(9,'Solid State Drives',17,18),(16,'Hard Drives',19,20),(18,'Graphics Adapters',7,8);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories_attributes`
--

DROP TABLE IF EXISTS `categories_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories_attributes` (
  `category_id` bigint unsigned NOT NULL,
  `attribute_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`category_id`,`attribute_id`),
  KEY `attribute_id` (`attribute_id`),
  CONSTRAINT `categories_attributes_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `categories_attributes_ibfk_2` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories_attributes`
--

LOCK TABLES `categories_attributes` WRITE;
/*!40000 ALTER TABLE `categories_attributes` DISABLE KEYS */;
INSERT INTO `categories_attributes` VALUES (3,5),(4,5),(9,5),(16,5),(18,5),(3,6),(4,6),(9,6),(16,6),(18,6),(3,7),(4,7),(9,7),(16,7),(18,7),(3,8),(4,9),(9,10),(16,10);
/*!40000 ALTER TABLE `categories_attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(32) NOT NULL,
  `last_name` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  FULLTEXT KEY `first_name` (`first_name`,`last_name`,`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'Test','Tester','tester@example.com'),(2,'Order','Orderer','orderer@example.com'),(3,'Shipment','Shipmenter','shipmenter@example.com');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(64) NOT NULL,
  `orig_filename` varchar(64) NOT NULL,
  `mime_type` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `filename` (`filename`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `images`
--

LOCK TABLES `images` WRITE;
/*!40000 ALTER TABLE `images` DISABLE KEYS */;
INSERT INTO `images` VALUES (3,'/home/ivan/www/images/f2173bbe8e12c3d3c8eb252685fee74b.jpeg','cpu1.jpeg','image/jpeg'),(4,'/home/ivan/www/images/93210b3275467d7dab2ebfeb71708a4f.jpeg','cpu2.jpeg','image/jpeg'),(13,'/home/ivan/www/images/b886b961a7b36cf6afdd3c64b484376e.jpeg','cpu1.jpeg','image/jpeg'),(14,'/home/ivan/www/images/c6cc1716697c05d5451b4487c759a69c.jpeg','cpu2.jpeg','image/jpeg');
/*!40000 ALTER TABLE `images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_history`
--

DROP TABLE IF EXISTS `order_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `status_id` bigint unsigned NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `status_id` (`status_id`),
  KEY `created` (`created`),
  CONSTRAINT `order_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `order_history_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `order_statuses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_history`
--

LOCK TABLES `order_history` WRITE;
/*!40000 ALTER TABLE `order_history` DISABLE KEYS */;
INSERT INTO `order_history` VALUES (1,1,1,'2025-07-04 18:30:24'),(2,2,1,'2025-07-04 18:30:32'),(3,1,2,'2025-07-04 18:35:18'),(4,1,3,'2025-07-12 19:14:31'),(5,2,2,'2025-07-12 19:24:16'),(6,2,3,'2025-07-12 19:38:35'),(7,2,4,'2025-07-12 19:38:45'),(8,1,1,'2025-07-16 19:33:16'),(9,5,1,'2025-08-05 20:38:57'),(10,5,2,'2025-08-05 20:41:34');
/*!40000 ALTER TABLE `order_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_price_id` bigint unsigned NOT NULL,
  `quantity` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_price_id` (`product_price_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_price_id`) REFERENCES `product_prices` (`id`),
  CONSTRAINT `order_items_chk_1` CHECK ((`quantity` > 0))
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (3,2,14,1),(4,2,7,1),(7,1,7,8),(10,1,14,6),(11,1,16,4),(12,5,14,2),(13,5,7,2);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_statuses`
--

DROP TABLE IF EXISTS `order_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_statuses`
--

LOCK TABLES `order_statuses` WRITE;
/*!40000 ALTER TABLE `order_statuses` DISABLE KEYS */;
INSERT INTO `order_statuses` VALUES (5,'Cancelled'),(1,'Created'),(4,'Delivered'),(2,'Paid'),(3,'Shipped');
/*!40000 ALTER TABLE `order_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `number` char(16) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `number` (`number`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,2,'4570932750293475'),(2,1,'9234523948752934'),(5,3,'1726211534945585');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_tokens`
--

DROP TABLE IF EXISTS `password_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `token` char(32) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `created` (`created`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `password_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_tokens`
--

LOCK TABLES `password_tokens` WRITE;
/*!40000 ALTER TABLE `password_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_prices`
--

DROP TABLE IF EXISTS `product_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_prices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `price` int unsigned NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_prices_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_prices`
--

LOCK TABLES `product_prices` WRITE;
/*!40000 ALTER TABLE `product_prices` DISABLE KEYS */;
INSERT INTO `product_prices` VALUES (1,1,29999,'2025-06-30 20:07:24'),(2,1,28999,'2025-06-30 20:07:36'),(3,12,9999,'2025-06-30 20:10:28'),(4,12,19999,'2025-06-30 20:10:36'),(5,1,28995,'2025-07-02 19:17:33'),(6,12,9999,'2025-07-02 19:18:00'),(7,12,12300,'2025-07-02 19:20:34'),(8,1,12300,'2025-07-02 19:20:47'),(9,1,12500,'2025-07-02 19:21:01'),(14,1,12000,'2025-07-12 19:43:35'),(15,32,30000,'2025-07-24 18:37:37'),(16,33,25000,'2025-07-24 18:38:40');
/*!40000 ALTER TABLE `product_prices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `sku` varchar(16) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` varchar(4096) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `sku` (`sku`),
  KEY `category_id` (`category_id`),
  FULLTEXT KEY `sku_2` (`sku`,`name`,`description`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,3,'CPU000001','Intel Core Ultra 9 288V','Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.\r\n\r\nLorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.'),(12,4,'RAM000001','CORSAIR Vengeance 64GB (2 x 32GB) 288-Pin DDR5 6400','Phasellus fermentum malesuada phasellus netus dictum aenean placerat egestas amet. Ornare taciti semper dolor tristique morbi. Sem leo tincidunt aliquet semper eu lectus scelerisque quis. Sagittis vivamus mollis nisi mollis enim fermentum laoreet.\r\n\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Proin tortor purus platea sit eu id nisi litora libero. Neque vulputate consequat ac amet augue blandit maximus aliquet congue. Pharetra vestibulum posuere ornare faucibus fusce dictumst orci aenean eu facilisis ut volutpat commodo senectus purus himenaeos fames primis convallis nisi.'),(32,3,'CPU000002','Intel Core Ultra 9 285H','Intel Core Ultra processors (Series 2) are built to make you a leader in AI. From supercharged productivity to heightened security and speed, Intel&rsquo;s AI is the key to next-level processor performance.'),(33,3,'CPU000003','Intel Core Ultra 7 266V','Intel Core Ultra processors (Series 2) are built to make you a leader in AI. From supercharged productivity to heightened security and speed, Intel&rsquo;s AI is the key to next-level processor performance.');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products_attributes`
--

DROP TABLE IF EXISTS `products_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products_attributes` (
  `product_id` bigint unsigned NOT NULL,
  `attribute_id` bigint unsigned NOT NULL,
  `value` varchar(128) NOT NULL,
  PRIMARY KEY (`product_id`,`attribute_id`),
  KEY `attribute_id` (`attribute_id`),
  CONSTRAINT `products_attributes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `products_attributes_ibfk_2` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products_attributes`
--

LOCK TABLES `products_attributes` WRITE;
/*!40000 ALTER TABLE `products_attributes` DISABLE KEYS */;
INSERT INTO `products_attributes` VALUES (1,5,'Intel'),(1,6,'Core Ultra 9'),(1,7,'288V'),(1,8,'8'),(12,5,'CORSAIR'),(12,6,'Vengeance'),(12,7,'Vengeance 64GB');
/*!40000 ALTER TABLE `products_attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products_images`
--

DROP TABLE IF EXISTS `products_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products_images` (
  `product_id` bigint unsigned NOT NULL,
  `image_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`product_id`,`image_id`),
  KEY `image_id` (`image_id`),
  CONSTRAINT `products_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `products_images_ibfk_2` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products_images`
--

LOCK TABLES `products_images` WRITE;
/*!40000 ALTER TABLE `products_images` DISABLE KEYS */;
INSERT INTO `products_images` VALUES (12,3),(12,4),(1,13),(1,14);
/*!40000 ALTER TABLE `products_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_history`
--

DROP TABLE IF EXISTS `role_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `status_id` bigint unsigned NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `created` (`created`),
  KEY `role_id` (`role_id`),
  KEY `status_id` (`status_id`),
  CONSTRAINT `role_history_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `role_history_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `role_statuses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_history`
--

LOCK TABLES `role_history` WRITE;
/*!40000 ALTER TABLE `role_history` DISABLE KEYS */;
INSERT INTO `role_history` VALUES (1,1,1,'2025-08-27 20:05:06'),(2,2,1,'2025-08-27 20:05:06'),(3,3,1,'2025-08-27 20:05:06'),(4,5,1,'2025-08-27 20:51:01'),(5,1,2,'2025-08-31 17:17:14'),(6,1,1,'2025-08-31 17:17:22'),(7,5,2,'2025-08-31 17:20:48'),(8,5,1,'2025-08-31 17:20:58'),(9,3,2,'2025-08-31 17:21:06'),(10,3,1,'2025-08-31 17:21:10'),(12,5,2,'2025-08-31 17:37:33'),(13,5,1,'2025-08-31 17:41:37'),(14,5,2,'2025-08-31 17:43:15'),(15,5,1,'2025-08-31 18:02:24'),(16,5,2,'2025-08-31 18:03:59'),(17,5,1,'2025-08-31 18:04:43'),(18,5,2,'2025-08-31 19:06:33'),(19,5,1,'2025-08-31 19:07:49'),(20,5,2,'2025-08-31 19:18:13');
/*!40000 ALTER TABLE `role_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_statuses`
--

DROP TABLE IF EXISTS `role_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_statuses`
--

LOCK TABLES `role_statuses` WRITE;
/*!40000 ALTER TABLE `role_statuses` DISABLE KEYS */;
INSERT INTO `role_statuses` VALUES (1,'Active'),(2,'Deleted');
/*!40000 ALTER TABLE `role_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrator','Full permissions'),(2,'Readonly','Read only permissions'),(3,'Guest','Role without permissions'),(5,'Manager','Permissions to work with orders');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles_actions`
--

DROP TABLE IF EXISTS `roles_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles_actions` (
  `role_id` bigint unsigned NOT NULL,
  `action_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`action_id`),
  KEY `action_id` (`action_id`),
  CONSTRAINT `roles_actions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `roles_actions_ibfk_2` FOREIGN KEY (`action_id`) REFERENCES `actions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles_actions`
--

LOCK TABLES `roles_actions` WRITE;
/*!40000 ALTER TABLE `roles_actions` DISABLE KEYS */;
INSERT INTO `roles_actions` VALUES (1,1),(1,2),(2,2),(1,3),(1,4),(1,5),(2,5),(3,5),(1,6),(1,7),(2,7),(1,8),(1,9),(1,10),(2,10),(3,10),(1,11),(1,12),(2,12),(1,13),(1,14),(1,15),(2,15),(3,15),(1,16),(1,17),(2,17),(1,18),(1,19),(1,20),(2,20),(3,20),(1,21),(1,22),(2,22),(1,23),(1,24),(1,25),(2,25),(3,25),(1,26),(1,27),(2,27),(1,28),(1,29),(1,30),(2,30),(3,30),(1,31),(1,32),(2,32),(1,33),(1,34),(1,35),(2,35),(3,35),(1,36),(5,36),(1,37),(2,37),(5,37),(1,38),(5,38),(1,39),(5,39),(1,40),(2,40),(3,40),(5,40);
/*!40000 ALTER TABLE `roles_actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_history`
--

DROP TABLE IF EXISTS `user_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `status_id` bigint unsigned NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `created` (`created`),
  KEY `user_id` (`user_id`),
  KEY `status_id` (`status_id`),
  CONSTRAINT `user_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_history_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `user_statuses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_history`
--

LOCK TABLES `user_history` WRITE;
/*!40000 ALTER TABLE `user_history` DISABLE KEYS */;
INSERT INTO `user_history` VALUES (1,1,1,'2025-08-09 20:53:26'),(2,2,1,'2025-08-09 20:53:26'),(3,3,1,'2025-08-09 20:53:26'),(7,4,1,'2025-08-17 18:12:30'),(8,1,2,'2025-08-17 18:35:52'),(9,1,1,'2025-08-17 18:36:00'),(10,1,3,'2025-08-17 18:36:03'),(11,1,1,'2025-08-17 18:36:08'),(12,4,3,'2025-08-17 18:39:08'),(13,4,1,'2025-08-17 18:39:35'),(14,4,2,'2025-08-17 18:40:02'),(15,4,1,'2025-08-17 18:40:34'),(16,4,2,'2025-08-17 18:43:50'),(17,3,3,'2025-08-23 18:55:02'),(18,4,1,'2025-08-23 18:55:57'),(19,3,1,'2025-08-23 18:56:08'),(20,4,2,'2025-08-23 19:00:23'),(21,4,1,'2025-08-23 19:00:25'),(22,4,2,'2025-08-23 19:00:29'),(23,4,1,'2025-08-23 19:00:34'),(24,1,3,'2025-08-23 19:00:38'),(25,1,1,'2025-08-23 19:00:42'),(26,1,3,'2025-08-23 19:00:56'),(27,1,1,'2025-08-23 19:01:00'),(28,4,2,'2025-08-23 19:01:05'),(29,4,1,'2025-08-23 19:01:11'),(30,4,2,'2025-08-23 19:01:44'),(31,4,1,'2025-08-23 19:01:46'),(32,1,3,'2025-08-23 19:01:48'),(33,1,1,'2025-08-23 19:01:53'),(34,4,2,'2025-08-23 19:03:29'),(35,4,1,'2025-08-23 19:03:31'),(36,4,3,'2025-08-23 19:03:32'),(37,4,1,'2025-08-23 19:03:33'),(38,4,2,'2025-08-23 19:03:53'),(39,4,1,'2025-08-23 19:03:56'),(40,1,3,'2025-08-23 19:03:58'),(41,1,1,'2025-08-23 19:03:59'),(42,4,3,'2025-08-23 19:04:08'),(43,4,1,'2025-08-23 19:04:10'),(44,1,3,'2025-08-23 19:04:12'),(45,1,1,'2025-08-23 19:04:13'),(46,4,3,'2025-08-23 19:04:30'),(47,4,1,'2025-08-23 19:04:32'),(48,4,2,'2025-08-23 19:04:33'),(49,4,1,'2025-08-23 19:04:37'),(50,4,3,'2025-08-23 19:04:53'),(51,4,1,'2025-08-23 19:04:54'),(52,4,3,'2025-08-23 19:04:55'),(53,4,1,'2025-08-23 19:04:56'),(54,3,3,'2025-08-23 19:04:57'),(55,2,3,'2025-08-23 19:04:58'),(56,4,3,'2025-08-23 19:04:59'),(57,2,1,'2025-08-23 19:05:02'),(58,3,1,'2025-08-23 19:05:04'),(59,4,1,'2025-08-23 19:05:05'),(60,2,2,'2025-08-23 19:05:06'),(61,3,2,'2025-08-23 19:05:07'),(62,4,2,'2025-08-23 19:05:08'),(63,2,1,'2025-08-23 19:05:13'),(64,3,1,'2025-08-23 19:05:14'),(65,4,1,'2025-08-23 19:05:15');
/*!40000 ALTER TABLE `user_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_statuses`
--

DROP TABLE IF EXISTS `user_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_statuses`
--

LOCK TABLES `user_statuses` WRITE;
/*!40000 ALTER TABLE `user_statuses` DISABLE KEYS */;
INSERT INTO `user_statuses` VALUES (1,'Active'),(3,'Deleted'),(2,'Locked');
/*!40000 ALTER TABLE `user_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `first_name` varchar(32) NOT NULL,
  `last_name` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` char(60) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `first_name` (`first_name`,`last_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'testtest','Test','Test','isolisted@gmail.com','$2y$12$wqEpuVoyFemkY.HXKNzn2ORcU.bOz59LyJKPIUPjDpUUMPx6lpKya'),(2,'userone','One','One','userone@example.com','$2y$12$Ho1H5naL48HO1s6547rcP.QzsSy/6cF1gsS2lftod/pXTkYLjpHty'),(3,'usertwo','Two','Two','usertwo@example.com','$2y$12$FimRJ7lvgQA1AAJobx6dXO4CZXXvtRWALrbnom.Dv58nktKyo6Xea'),(4,'userthree','Three','Three','userthree@example.com','$2y$12$Lyghn3/xgpoaPYS9bsYhMONBVNKj9Qzx32zbqyXQtI5B9Oc0QFYjK');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_roles`
--

DROP TABLE IF EXISTS `users_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_roles` (
  `user_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `users_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_roles`
--

LOCK TABLES `users_roles` WRITE;
/*!40000 ALTER TABLE `users_roles` DISABLE KEYS */;
INSERT INTO `users_roles` VALUES (1,1),(2,2),(4,2),(3,3),(4,5);
/*!40000 ALTER TABLE `users_roles` ENABLE KEYS */;
UNLOCK TABLES;
/*!50112 SET @disable_bulk_load = IF (@is_rocksdb_supported, 'SET SESSION rocksdb_bulk_load = @old_rocksdb_bulk_load', 'SET @dummy_rocksdb_bulk_load = 0') */;
/*!50112 PREPARE s FROM @disable_bulk_load */;
/*!50112 EXECUTE s */;
/*!50112 DEALLOCATE PREPARE s */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-31 21:36:16
