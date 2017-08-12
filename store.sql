-- MySQL dump 10.13  Distrib 5.7.19, for Linux (x86_64)
--
-- Host: localhost    Database: store
-- ------------------------------------------------------
-- Server version	5.7.19-0ubuntu0.16.04.1

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
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(80) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `company` varchar(255) CHARACTER SET utf8 COLLATE utf8_polish_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `address1` varchar(255) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `address2` varchar(255) CHARACTER SET utf8 COLLATE utf8_polish_ci DEFAULT NULL,
  `postcode` varchar(20) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `city` varchar(255) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `country` varchar(255) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `tax_no` varchar(20) CHARACTER SET utf8 COLLATE utf8_polish_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addresses`
--

LOCK TABLES `addresses` WRITE;
/*!40000 ALTER TABLE `addresses` DISABLE KEYS */;
INSERT INTO `addresses` VALUES (1,'Biuro','Przetwory grzybne Sromotnik spółka z o.o. sp. k.','John Doe','ul. Leśna 12','lokal 12','84-110','Kartoszyno','Polska','225678743','5674325676'),(2,'Magazyn','Przetwory grzybne Sromotnik spółka z o.o. sp. k.','John Doe','ul. Przy Elektrowni 1','pawilon 235 pod kominem','84-110','Kartoszyno','Polska','705-765-456',''),(3,'Jedyny adres jaki mam','','Jan Kowalski','ul. Krowia 1','Mała Kozia Wólka','56-300','Duża Kozia Wólka','Polska','+48 456 654 876',''),(4,'Praca','Kiosk','Zygmunt Nowak','ul. Kościelna 1','','65-600','Poznań','Polska','0048 567 345 755',''),(5,'Dom','','Basia Wiśniewska','al. Niepodległości','','30-566','Kraków','Polska','+48 444 222 666',''),(6,'my address','sfafddsf','Safgd','dgssdfs','','dfgsgd','dsfgs','sfdgs','sdfgfsd',''),(7,'my address','sfafddsf','Safgd','dgssdfs','','dfgsgd','dsfgs','sfdgs','sdfgfsd',''),(8,'my address','','sfasd','asfadf','as','asfasdf','asfdas','sdfa','sdfaf',''),(9,'my address','fasf','asfas','asdfad','asdfasf','asdfa','asdfad','asdfa','asdfa','sadfsa'),(10,'my address','fasf','asfas','asdfad','asdfasf','asdfa','asdfad','asdfa','asdfa','sadfsa'),(11,'my address','','sdfaf','asdfas','','sdfaf','sadfa','safasdf','asdfsdf',''),(12,'my address','','sfsdaf','asdfa','','sadfsa','sdfasdf','sfadfa','sdfadsdf',''),(13,'my address','','Sdafas','asdfsa','asfsa','dsfasdf','asfas','sfdsa','safs',''),(14,'my address','dsfda','asdfasdf','aasf','asfd','adsfa','asdfas','adf','adsfafds',''),(15,'my address','','dsfafd','asdfasfd','asfd','asdfadsf','sadfafsd','asfdfad','asfdsf',''),(16,'my address','','sdfasdf','asdfad','','sdfasf','sdfad','sadfa','adsfas',''),(17,'my address','','sadfsdf','asdfasf','','asfdsfad','asfdds','asfdsf','asfsdf',''),(18,'my address','','sfas','asdfasf','','asdfadf','sadfas','asfdas','asdfa',''),(19,'my address','','sdfasfd','adsfaf','','asdfadsfa','asdfad','asddfa','sadfasdf',''),(20,'my address','','Sdfsa','sdfasdf','','asdfasd','sadfafsd','asdfsd','affdasdf',''),(21,'my address','','fddasfa','sadfa','','asdfasdf','sadfasd','sdfa','sfdasfd',''),(22,'my address','','Nowy użytkownik','sfgsdfg','sgfsdg','sdfgs','sfgfds','sgfsdg','sdfgfds',''),(23,'my address','Firma','Marcin Dżdża','ul. Długa 1','','01-011','Warszawa','Polska','765456789','8765670987');
/*!40000 ALTER TABLE `addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carriers`
--

DROP TABLE IF EXISTS `carriers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carriers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier_name` varchar(80) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_polish_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carriers`
--

LOCK TABLES `carriers` WRITE;
/*!40000 ALTER TABLE `carriers` DISABLE KEYS */;
INSERT INTO `carriers` VALUES (1,'In-store pickup','Visit our store and pick up your order free of charge',0.00,1),(2,'DHL','Delivery within 48h',14.00,1),(3,'Pocztex','Delivery within 48h',18.50,1),(4,'Siódemka','Delivery within 48h',15.50,0);
/*!40000 ALTER TABLE `carriers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_products`
--

DROP TABLE IF EXISTS `order_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_products_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_products`
--

LOCK TABLES `order_products` WRITE;
/*!40000 ALTER TABLE `order_products` DISABLE KEYS */;
INSERT INTO `order_products` VALUES (1,1,3,2,11.40),(2,1,2,5,0.75),(3,1,1,1,2.40),(4,2,3,1,11.40),(8,3,1,17,2.40),(11,3,2,4,0.80),(12,3,3,2,12.00),(13,5,1,1,2.40),(14,5,2,3,0.80),(15,5,3,2,12.00),(16,6,1,1,2.40),(17,6,2,1,0.80),(18,6,3,1,12.00),(19,7,1,1,2.40),(20,7,2,1,0.80),(21,7,3,1,12.00),(22,8,1,1,2.40),(23,9,1,1,2.40),(24,10,1,1,2.40),(25,11,1,1,2.40),(26,11,2,1,0.80),(27,12,1,1,2.40),(28,12,2,3,0.80),(29,12,3,1,12.00),(30,13,1,1,2.40),(31,13,2,1,0.80),(32,13,3,1,12.00),(33,14,1,3,2.40),(34,14,2,2,0.80),(35,14,3,1,12.00),(36,15,1,7,2.40),(38,15,3,3,12.00),(39,15,2,3,0.80),(40,16,1,1,2.40),(41,16,2,1,0.80);
/*!40000 ALTER TABLE `order_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_statuses`
--

DROP TABLE IF EXISTS `order_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `status_id` (`status_id`),
  CONSTRAINT `order_statuses_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_statuses_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_statuses`
--

LOCK TABLES `order_statuses` WRITE;
/*!40000 ALTER TABLE `order_statuses` DISABLE KEYS */;
INSERT INTO `order_statuses` VALUES (1,1,1,'2017-07-14 08:00:00'),(2,1,2,'2017-07-14 12:30:00'),(3,1,4,'2017-07-14 15:30:20'),(4,2,1,'2017-07-18 15:30:20'),(5,2,2,'2017-07-19 15:30:20'),(6,3,1,'2017-07-20 15:30:20'),(7,4,1,'2017-07-19 15:30:20'),(8,4,2,'2017-07-20 15:30:20'),(9,2,4,'2017-07-21 15:30:20'),(10,5,1,'2017-08-11 19:15:46'),(11,5,2,'2017-08-11 19:18:02'),(12,6,1,'2017-08-11 19:19:09'),(13,6,2,'2017-08-11 19:19:26'),(14,7,1,'2017-08-11 19:24:23'),(15,7,2,'2017-08-11 19:24:36'),(16,8,1,'2017-08-11 19:25:27'),(17,8,2,'2017-08-11 19:25:31'),(18,9,1,'2017-08-11 19:25:59'),(19,9,2,'2017-08-11 19:26:06'),(20,10,1,'2017-08-11 19:26:48'),(21,10,2,'2017-08-11 19:26:53'),(22,11,1,'2017-08-11 19:31:30'),(23,11,2,'2017-08-11 19:31:38'),(24,12,1,'2017-08-11 19:35:34'),(25,12,2,'2017-08-11 19:35:56'),(26,13,1,'2017-08-11 19:36:56'),(27,13,2,'2017-08-11 19:37:10'),(28,14,1,'2017-08-11 19:37:21'),(29,14,2,'2017-08-11 19:43:05'),(30,15,1,'2017-08-11 21:52:34'),(31,15,2,'2017-08-11 23:57:51'),(32,16,1,'2017-08-11 23:58:38');
/*!40000 ALTER TABLE `order_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `billing_address` int(11) DEFAULT NULL,
  `shipping_address` int(11) DEFAULT NULL,
  `carrier_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_polish_ci DEFAULT NULL,
  `shipping_cost` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `billing_address` (`billing_address`),
  KEY `shipping_address` (`shipping_address`),
  KEY `carrier_id` (`carrier_id`),
  KEY `payment_id` (`payment_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`billing_address`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`shipping_address`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_ibfk_4` FOREIGN KEY (`carrier_id`) REFERENCES `carriers` (`id`),
  CONSTRAINT `orders_ibfk_5` FOREIGN KEY (`payment_id`) REFERENCES `payment_methods` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,1,1,2,3,2,'Towary potrzebne na wczoraj',20.00,46.95),(2,1,1,2,3,2,'Towary potrzebne na wczoraj',20.00,31.40),(3,1,1,2,2,2,'Proszę o przesłanie pustej paczki 2 ble fdsafdada dddfs',14.00,82.00),(4,2,1,2,3,2,'Proszę o przesłanie pustej paczki',20.00,20.00),(5,22,23,23,2,3,'Mój komentarz',14.00,42.80),(6,22,23,23,3,2,'fsdfs',18.50,33.70),(7,22,23,23,1,1,'',0.00,15.20),(8,22,23,23,1,1,'',0.00,2.40),(9,22,23,23,1,1,'',0.00,2.40),(10,22,23,23,1,1,'',0.00,2.40),(11,22,23,23,1,1,'',0.00,3.20),(12,22,23,23,2,2,'Komentarz',14.00,30.80),(13,22,23,23,2,1,'fdsafd',14.00,29.20),(14,22,23,23,2,2,'fsafd',14.00,34.80),(15,22,23,23,1,2,'gfdsfgsdfgds',0.00,55.20),(16,22,23,23,1,1,'',0.00,3.20);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_methods`
--

DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_name` varchar(80) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_polish_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_methods`
--

LOCK TABLES `payment_methods` WRITE;
/*!40000 ALTER TABLE `payment_methods` DISABLE KEYS */;
INSERT INTO `payment_methods` VALUES (1,'Cash','Pay with cash upon receival',1),(2,'Bank transfer','Pay with bank transfer',1),(3,'Cheque','Pay with cheque',0);
/*!40000 ALTER TABLE `payment_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phinxlog`
--

DROP TABLE IF EXISTS `phinxlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phinxlog` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phinxlog`
--

LOCK TABLES `phinxlog` WRITE;
/*!40000 ALTER TABLE `phinxlog` DISABLE KEYS */;
INSERT INTO `phinxlog` VALUES (20170702125629,'CreateProductTable','2017-07-02 13:05:48','2017-07-02 13:05:48',0),(20170702130723,'CreatePhotosTable','2017-07-12 05:18:49','2017-07-12 05:18:49',0),(20170703133948,'CreateUsersTable','2017-07-03 14:00:19','2017-07-03 14:00:19',0),(20170705074050,'AlterUsersTable','2017-07-05 12:44:04','2017-07-05 12:44:04',0),(20170712051210,'AlterProductsPriceType','2017-07-12 05:19:28','2017-07-12 05:19:29',0),(20170712063334,'CreateAddressesTable','2017-07-13 07:12:50','2017-07-13 07:12:50',0),(20170712230054,'AddAddressColsToUsersTable','2017-07-13 08:16:26','2017-07-13 08:16:28',0),(20170714065721,'CreateStatusesTable','2017-07-14 13:22:22','2017-07-14 13:22:23',0),(20170714070021,'InsertDefaultValsToStatuses','2017-07-14 21:25:52','2017-07-14 21:25:52',0),(20170714074418,'CreateCarriersTable','2017-07-14 21:25:52','2017-07-14 21:25:52',0),(20170714074513,'InsertDefaultValsToCarriers','2017-07-14 21:25:52','2017-07-14 21:25:52',0),(20170714075825,'CreatePaymentMethodsTable','2017-07-14 21:25:53','2017-07-14 21:25:53',0),(20170714075858,'InsertDefaultValsToPaymentMethods','2017-07-14 21:25:53','2017-07-14 21:25:53',0),(20170714080049,'CreateOrdersTable','2017-07-14 21:25:53','2017-07-14 21:25:53',0),(20170714080416,'CreateOrderProductsTable','2017-07-14 21:25:53','2017-07-14 21:25:54',0),(20170714210710,'CreateOrderStatusesTable','2017-07-14 21:25:54','2017-07-14 21:25:54',0);
/*!40000 ALTER TABLE `phinxlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) COLLATE utf8_polish_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8_polish_ci,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Mydło',2.40,'Znakomity środek czystości dla kobiet i mężczyzn',2),(2,'Szydło',0.80,'Duża igła',5),(3,'Powidło',12.00,'Przetworzone śliwki, na kanapkę albo jako nadzienie.',40);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statuses`
--

DROP TABLE IF EXISTS `statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statuses`
--

LOCK TABLES `statuses` WRITE;
/*!40000 ALTER TABLE `statuses` DISABLE KEYS */;
INSERT INTO `statuses` VALUES (1,'Basket'),(2,'Submitted'),(3,'Paid'),(4,'Shipped'),(5,'Delivered'),(6,'Canceled');
/*!40000 ALTER TABLE `statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `password` varchar(60) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `date_created` datetime NOT NULL,
  `billing_address` int(11) NOT NULL,
  `shipping_address` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `billing_address` (`billing_address`),
  KEY `shipping_address` (`shipping_address`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`billing_address`) REFERENCES `addresses` (`id`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`shipping_address`) REFERENCES `addresses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'John Doe','john@doe.com','$2y$11$KEf2QAk/Mpw2nR8OUKc0N.pZlC/d.zoZoX.lCqKph/Gy9Ejz6aUKu','2017-07-01 12:20:15',1,2),(2,'Jane Doe','jane@doe.com','$2y$11$KEf2QAk/Mpw2nR8OUKc0N.pZlC/d.zoZoX.lCqKph/Gy9Ejz6aUKu','2017-06-15 08:30:45',3,3),(3,'Jan Kowalski','jan@kowalski.pl','$2y$11$KEf2QAk/Mpw2nR8OUKc0N.pZlC/d.zoZoX.lCqKph/Gy9Ejz6aUKu','2017-08-14 11:30:18',4,4),(4,'Ania Nowak','ania@nowak.pl','$2y$11$KEf2QAk/Mpw2nR8OUKc0N.pZlC/d.zoZoX.lCqKph/Gy9Ejz6aUKu','2017-08-09 07:30:18',5,5),(5,'Beata Marczak','beata@marczak.pl','$2y$10$LyTZWE4N7b2QbRy6KxO/puBmHoBI.c824ccIHOs8v..bVlruilcTq','2017-07-26 19:26:52',1,2),(6,'','ble@doe.com','doefdsF73','2017-08-10 18:15:57',7,7),(7,'','rootsfs@op.pl','$2y$10$WDB8sx0l3ehrOmlmjaeHoe/dYAbTfuG8p7AwlrHKNRVvCz2GRgYQ6','2017-08-10 18:26:49',8,8),(8,'','root@bleb.pl','$2y$10$wkZ3fUmAbnj87Y1pZVqM8OAtlHmBa6PKrZuvAMH8pPr9uXnFws9rO','2017-08-10 18:30:39',9,9),(9,'','root@bdleb.pl','$2y$10$jjsF7fziFN6uUEnC5z1BkO1kBaQV5NU2Fj.119yejElZm6vVJU1om','2017-08-10 18:31:03',10,10),(10,'','rootf@kfd.pl','$2y$10$F/STI8XUsBDSpmUlMSLiXu4rO9XsRKj20aszQnOKOZOy2fUyl2LLy','2017-08-10 18:31:42',11,11),(11,'','root@fdjkfsl.pl','$2y$10$5KfoBdDNOhFr0ug5RwDLduI17UhGopXgbvkmEzuAimx7vniCrt.8e','2017-08-10 18:32:55',12,12),(12,'','root@fkjlsdfjks.pl','$2y$10$T0wyNk3kzcbT2sGAPsL2aOVVGaR4qBXgwsKtugDsKNHeXWDHHk4PC','2017-08-10 18:34:01',13,13),(13,'','john@doe.comfsdf','$2y$10$Mpmiz1fkQme12xJMAGBWrO6hPvjYN5VCGSioAMbqTXozcMCgcLuy2','2017-08-10 18:36:49',14,14),(14,'','root@fjdklsjfdl.pl','$2y$10$1AlYWifYY2C2xvhVx3t33unsiZySJwGLiz4OlBuoWSnq0Hjk2gra.','2017-08-10 18:37:35',15,15),(15,'','root@fopsdf.pl','$2y$10$3b3I/koy5YOFFOnHlHyuHeJZ0dIx7yJL6esw9liPPv.oaJRfj0FGy','2017-08-10 18:38:17',16,16),(16,'','root@flfs','$2y$10$C3kXn4hlXR.Nfwj6zzKGb.TmVA1kJxaWqmuV5uZtewb1VuEQ11eoC','2017-08-10 18:41:42',17,17),(17,'','root@fklsdfs','$2y$10$.yQVP31t5nxtMXnA720qEOjmKvVkgHjkeDrxqsEaNQ9ruftfbQKUe','2017-08-10 18:43:25',18,18),(18,'','root@fkdls','$2y$10$9QRl/GHQsBQOxULk2pvUIuxoNazjaC5IXnDKCzH74WBSg/IIZBfEC','2017-08-10 18:50:52',19,19),(19,'','root@fkslfds','$2y$10$rvAQsaJD44eRT1NEpOg5r.OFwYyfZNl5e4MVqM4s0PhrQXL5Q.i4G','2017-08-10 18:55:52',20,20),(20,'','root@fsfds','$2y$10$J7lij01R5ZPVqMivP42g8.wgtG0FA6NHgxwNCA7c/KpSs/OcxHsW.','2017-08-10 19:03:03',21,21),(21,'','john@doge.com','$2y$10$sBhus/FSVee98XG6nfeo6usAuZIsgM/SvWR49/Efh8kbvoVUWBZPy','2017-08-11 17:35:37',22,22),(22,'','marcin.dzdza@gmail.com','$2y$10$enxZNoA2BS7SGQyn4Cx07uGh26I8e0ZAE0l1JRm7TAmdFsJG1Gck.','2017-08-11 19:15:31',23,23);
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

-- Dump completed on 2017-08-12  2:23:18
