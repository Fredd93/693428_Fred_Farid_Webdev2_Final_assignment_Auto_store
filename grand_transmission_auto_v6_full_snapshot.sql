/*M!999999\- enable the sandbox mode */ 

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
DROP TABLE IF EXISTS `appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL AUTO_INCREMENT,
  `car_id` int(11) NOT NULL,
  `purpose` varchar(20) NOT NULL DEFAULT 'test_drive',
  `client_name` varchar(100) DEFAULT NULL,
  `client_email` varchar(100) DEFAULT NULL,
  `client_phone` varchar(20) DEFAULT NULL,
  `appointment_date` datetime NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `employee_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`appointment_id`),
  KEY `fk_appt_car` (`car_id`),
  KEY `fk_appt_emp` (`employee_id`),
  CONSTRAINT `fk_appt_car` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_appt_emp` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `appointments` WRITE;
/*!40000 ALTER TABLE `appointments` DISABLE KEYS */;
INSERT INTO `appointments` VALUES
(2,15,'test_drive','Mohamed El-Tayeb','mtayeb@grandtransmissionsautos.com','01001886677','2026-07-05 13:34:00','confirmed',4,'2026-07-02 07:29:33');
/*!40000 ALTER TABLE `appointments` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `car_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `car_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `car_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_car_images_car` (`car_id`),
  CONSTRAINT `fk_car_images_car` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `car_images` WRITE;
/*!40000 ALTER TABLE `car_images` DISABLE KEYS */;
INSERT INTO `car_images` VALUES
(1,1,'assets/images/Honda_Civic_2003_interior.png',2),
(2,1,'assets/images/Honda_civic_2003_back.jpg',1),
(3,1,'assets/images/Honda_civic_2003_front.jpeg',0),
(4,2,'assets/images/Toyota_corolla_2002_interior.jpg',2),
(5,2,'assets/images/Toyota_corolla_2002_back.jpg',1),
(6,2,'assets/images/Toyota_corolla_2002_front.jpg',0),
(7,3,'assets/images/Mitsubishi-ASX-2020-interior.jpg',2),
(8,3,'assets/images/Mitsubishi-ASX-2020-back.jpg',1),
(9,3,'assets/images/Mitsubishi-ASX-2020-front.jpg',0),
(16,5,'assets/images/car_6a458654db6eb8.63973321_car_6a344842bd1254.30050989_Porsche-911_GT3_RS-2019_back.jpg',0),
(17,5,'assets/images/car_6a458655015816.61618541_car_6a344842c57970.44944587_Porsche-911_GT3_RS-2019_front.jpg',1),
(18,5,'assets/images/car_6a4586550628a3.39639657_car_6a344842ca56d1.54112049_Porsche-911_GT3_RS-2019_interior.jpg',2),
(19,5,'assets/images/car_6a4586550adc80.71132359_car_6a344842cfa909.78624149_Porsche-911_GT3_RS-2019_top.jpg',3),
(20,7,'assets/images/Ford_mustang_2001_front.jpg',0),
(21,7,'assets/images/Ford_mustang_2001_back.jpg',1),
(22,7,'assets/images/Ford_mustang_2001_interior.jpg',2),
(23,8,'assets/images/car_6a341e038b6bc3.98858088_BMW_440i_front.jpg',0),
(24,8,'assets/images/car_6a341e037fa7f7.91653329_BMW_44i_back.jpg',1),
(25,8,'assets/images/car_6a341e03872d71.39727195_BMW_44i_interior.jpg',2),
(26,9,'assets/images/Audi-A4-2020-front.jpg',0),
(27,9,'assets/images/Audi-A4-2020-back.jpg',1),
(28,9,'assets/images/Audi-A4-2020-interior.jpg',2),
(29,10,'assets/images/Volkswagen-Golf_GTI_TCR-2019-front.jpg',0),
(30,10,'assets/images/Volkswagen-Golf_GTI_TCR-2019-back.jpg',1),
(31,10,'assets/images/Volkswagen-Golf_GTI_TCR-2019-interior.jpg',2),
(32,11,'assets/images/Nissan-Altima-2023-front.jpg',0),
(33,11,'assets/images/Nissan-Altima-2023-back.jpg',1),
(34,11,'assets/images/Nissan-Altima-2023-interior.jpg',2),
(35,12,'assets/images/Mercedes-Benz-C-Class-2019-front.jpg',0),
(36,12,'assets/images/Mercedes-Benz-C-Class-2019-back.jpg',1),
(37,12,'assets/images/Mercedes-Benz-C-Class-2019-interior.jpg',2),
(38,13,'assets/images/67f2aec1d4dee_BMW-M135i_xDrive-2022-Front.jpg',0),
(39,14,'assets/images/BMW-X3-2022-front.jpg',0),
(40,14,'assets/images/BMW-X3-2022-back.jpg',1),
(41,14,'assets/images/BMW-X3-2022-Interior.jpg',2),
(42,15,'assets/images/car_6a4611f62814e0.71210840_WhatsApp_Image_2026-07-02_at_09_16_44.jpeg',0),
(43,15,'assets/images/car_6a4611f6319251.77508757_WhatsApp_Image_2026-07-02_at_09_18_40.jpeg',1),
(44,15,'assets/images/car_6a4611f63766e7.75410737_WhatsApp_Image_2026-07-02_at_09_18_47__1_.jpeg',2),
(45,15,'assets/images/car_6a4611f63d1a67.79405375_WhatsApp_Image_2026-07-02_at_09_18_47__2_.jpeg',3),
(46,15,'assets/images/car_6a4611f6423bc5.02425715_WhatsApp_Image_2026-07-02_at_09_18_47__3_.jpeg',4),
(47,15,'assets/images/car_6a4611f6475419.41323345_WhatsApp_Image_2026-07-02_at_09_18_47.jpeg',5);
/*!40000 ALTER TABLE `car_images` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `cars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `transmission` varchar(20) DEFAULT NULL,
  `engine_spec` varchar(100) DEFAULT NULL,
  `car_condition` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `color` varchar(30) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `lease_available` tinyint(1) DEFAULT 0,
  `lease_terms` text DEFAULT NULL,
  `on_sale` tinyint(1) DEFAULT 0,
  `discount` decimal(5,2) DEFAULT 0.00,
  `status` enum('sold','available','reserved') DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cars` WRITE;
/*!40000 ALTER TABLE `cars` DISABLE KEYS */;
INSERT INTO `cars` VALUES
(1,'Honda','Civic',2003,'Manual','1.6L 4-cylinder 110hp','Used','A reliable and fuel-efficient compact that defined a generation. The 2003 Civic offers excellent build quality, low running costs, and easy maintenance — a perfect first car or daily driver.','assets/images/Honda_civic_2003_front.jpeg','Silver',4500.00,0,'',1,12.00,'sold','2026-07-01 20:39:08'),
(2,'Toyota','Corolla',2002,'Manual','1.4L 4-cylinder 97hp','Used','One of the most reliable cars ever made. This 2002 Corolla has stood the test of time with minimal repairs, great fuel economy, and a comfortable ride for everyday use.','assets/images/Toyota_corolla_2002_front.jpg','Blue',3800.00,0,'',1,8.00,'sold','2026-07-01 20:39:08'),
(3,'Mitsubishi','ASX',2020,'Automatic','2.0L 4-cylinder 150hp','Used','A stylish and practical compact SUV with modern safety features, spacious interior, and a smooth automatic gearbox. Great for city driving and weekend getaways.','/assets/images/Mitsubishi-ASX-2020-front.jpg','Red',18500.00,0,'48 months, €320/month, 10% down',0,0.00,'available','2026-07-01 20:39:08'),
(5,'Porsche','911 GT3 RS',2025,'Automatic','','','no introductions needed','/assets/images/car_6a458654db6eb8.63973321_car_6a344842bd1254.30050989_Porsche-911_GT3_RS-2019_back.jpg','',360000.00,1,'',1,20.00,'available','2026-07-01 21:27:49'),
(7,'Ford','Mustang',2001,'Manual','4.6L V8 GT','Used','An icon of American muscle, this GT-spec 2001 Mustang pairs its throaty V8 with a proper manual gearbox. Solid mechanicals and classic styling make it a fun, attention-grabbing daily driver or weekend cruiser.','assets/images/Ford_mustang_2001_front.jpg','Red',9800.00,0,'',0,0.00,'sold','2026-07-01 22:35:23'),
(8,'BMW','440i',2018,'Automatic','3.0L Turbocharged Inline-6 340hp','Used','A refined coupe with real performance behind it — the 440i\'s turbocharged inline-6 delivers effortless power with BMW\'s trademark rear-wheel-drive balance. Comfortable enough for daily use, quick enough for weekend roads.','assets/images/car_6a341e038b6bc3.98858088_BMW_440i_front.jpg','Blue Metallic',31500.00,0,'',1,10.00,'sold','2026-07-01 22:35:23'),
(9,'Audi','A4',2020,'Automatic','2.0L TFSI 190hp','Used','The A4 remains the benchmark for understated executive style — a quiet, efficient turbo four, a beautifully finished cabin, and enough tech to feel current years after launch. A safe, satisfying choice for daily commuting.','assets/images/Audi-A4-2020-front.jpg','Daytona Grey',27200.00,1,'36 months, €290/month, 10% down',0,0.00,'sold','2026-07-01 22:35:23'),
(10,'Volkswagen','Golf GTI TCR',2019,'Automatic','2.0L TSI Turbocharged 290hp','Used','A limited-run homologation special, the Golf GTI TCR bumps the standard hot hatch formula up to 290hp with sharper suspension and unique styling. Rare, track-capable, and still practical enough for everyday driving.','assets/images/Volkswagen-Golf_GTI_TCR-2019-front.jpg','White',33500.00,0,'',0,0.00,'sold','2026-07-01 22:35:23'),
(11,'Nissan','Altima',2023,'Automatic','2.5L 4-cylinder 188hp','Used','A near-new, low-mileage Altima built for comfortable, worry-free commuting. Spacious, efficient, and loaded with modern driver-assist features — an easy recommendation for anyone who just wants a car that works.','assets/images/Nissan-Altima-2023-front.jpg','Silver',23800.00,0,'',0,0.00,'sold','2026-07-01 22:35:23'),
(12,'Mercedes-Benz','C-Class',2019,'Automatic','2.0L Turbocharged 255hp','Used','Three-pointed-star prestige with the performance to match — this C-Class delivers a hushed, premium cabin and a punchy turbocharged engine. A comfortable, image-conscious choice for daily driving or leasing.','assets/images/Mercedes-Benz-C-Class-2019-front.jpg','Black',29900.00,1,'36 months, €310/month, 10% down',0,0.00,'sold','2026-07-01 22:35:23'),
(13,'BMW','M135i xDrive',2022,'Automatic','2.0L Turbocharged 306hp xDrive AWD','Used','BMW\'s performance hatchback brings 306hp and all-wheel drive to a genuinely practical package. Quick in every gear and composed in any weather, it\'s a hot hatch built to be driven hard year-round.','assets/images/67f2aec1d4dee_BMW-M135i_xDrive-2022-Front.jpg','Blue',38200.00,0,'',0,0.00,'sold','2026-07-01 22:35:23'),
(14,'BMW','X3',2022,'Automatic','2.0L Turbocharged 248hp xDrive30i','Used','A do-everything family SUV with genuine BMW driving manners. Spacious, well-equipped, and capable in all conditions thanks to xDrive all-wheel drive — an easy fit for buyers who need room without giving up refinement.','assets/images/BMW-X3-2022-front.jpg','White',42500.00,1,'48 months, €410/month, 10% down',0,0.00,'sold','2026-07-01 22:35:23'),
(15,'Mercedes benz','C 180',2010,'Automatic','','','150,000 km, Factory paint\r\nDownpayment 30%, Installments up to 60','assets/images/car_6a4611f62814e0.71210840_WhatsApp_Image_2026-07-02_at_09_16_44.jpeg','',950000.00,1,'',0,0.00,'available','2026-07-02 07:23:34');
/*!40000 ALTER TABLE `cars` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `lease_installments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lease_installments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `installment_no` int(11) NOT NULL,
  `due_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('due','paid') NOT NULL DEFAULT 'due',
  `paid_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_order` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `lease_installments` WRITE;
/*!40000 ALTER TABLE `lease_installments` DISABLE KEYS */;
/*!40000 ALTER TABLE `lease_installments` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `guest_name` varchar(100) DEFAULT NULL,
  `guest_email` varchar(100) DEFAULT NULL,
  `guest_phone` varchar(20) DEFAULT NULL,
  `car_id` int(11) NOT NULL,
  `order_type` enum('purchase','lease') NOT NULL,
  `status` enum('pending','approved','denied','completed','counter_offer') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `final_price` decimal(10,2) DEFAULT NULL,
  `down_payment` decimal(10,2) DEFAULT NULL,
  `lease_years` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `completed_at` datetime DEFAULT NULL,
  `completed_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_orders_user` (`user_id`),
  KEY `fk_orders_car` (`car_id`),
  CONSTRAINT `fk_orders_car` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES
(5,7,NULL,NULL,NULL,3,'lease','denied','','no',NULL,34000.00,36,'2026-07-01 21:23:30','2026-07-01 21:24:14',NULL,NULL),
(6,7,NULL,NULL,NULL,3,'lease','pending','',NULL,NULL,34000.00,36,'2026-07-01 21:23:31','2026-07-01 21:23:31',NULL,NULL),
(13,7,NULL,NULL,NULL,8,'purchase','pending','',NULL,NULL,NULL,NULL,'2026-07-02 05:51:06','2026-07-02 05:51:06',NULL,NULL),
(14,7,NULL,NULL,NULL,12,'lease','counter_offer','','Minimum starting point for this car',NULL,1000000.00,36,'2026-07-02 05:52:48','2026-07-02 05:58:00',NULL,NULL),
(15,NULL,'faridelksass','faridelksass@gmail.com','01001886677',5,'purchase','pending','',NULL,NULL,NULL,NULL,'2026-07-02 07:11:58','2026-07-02 07:11:58',NULL,NULL),
(22,NULL,'Mona Fathy','mona.fathy616@example.com','01157336387',1,'purchase','completed','',NULL,NULL,NULL,NULL,'2026-07-01 22:09:24','2026-07-01 22:09:24','2026-07-01 22:09:24',14),
(23,NULL,'Sara Nabil','sara.nabil426@example.com','01156574146',2,'purchase','completed','',NULL,NULL,NULL,NULL,'2026-07-02 20:09:24','2026-07-02 20:09:24','2026-07-02 20:09:24',14),
(24,NULL,'Rania Hassan','rania.hassan566@example.com','01584398805',2,'purchase','completed','',NULL,NULL,NULL,NULL,'2026-07-02 12:09:24','2026-07-02 12:09:24','2026-07-02 12:09:24',15),
(25,NULL,'Ahmed Hassan','ahmed.hassan567@example.com','01230467941',7,'purchase','completed','',NULL,NULL,NULL,NULL,'2026-07-02 17:09:24','2026-07-02 17:09:24','2026-07-02 17:09:24',15),
(26,NULL,'Nour Sami','nour.sami820@example.com','01563490769',7,'purchase','completed','',NULL,NULL,NULL,NULL,'2026-07-01 21:09:24','2026-07-01 21:09:24','2026-07-01 21:09:24',15),
(27,NULL,'Laila Fathy','laila.fathy245@example.com','01092900287',8,'purchase','completed','',NULL,NULL,NULL,NULL,'2026-07-02 13:09:24','2026-07-02 13:09:24','2026-07-02 13:09:24',15),
(28,NULL,'Karim Fathy','karim.fathy861@example.com','01512630707',9,'purchase','completed','',NULL,NULL,NULL,NULL,'2026-07-02 10:09:24','2026-07-02 10:09:24','2026-07-02 10:09:24',14),
(29,NULL,'Karim Saeed','karim.saeed401@example.com','01215434000',10,'purchase','completed','',NULL,NULL,NULL,NULL,'2026-07-02 07:09:24','2026-07-02 07:09:24','2026-07-02 07:09:24',6),
(30,NULL,'Youssef Saeed','youssef.saeed928@example.com','01247561558',10,'purchase','completed','',NULL,NULL,NULL,NULL,'2026-07-02 08:09:24','2026-07-02 08:09:24','2026-07-02 08:09:24',5),
(31,NULL,'Nour Saeed','nour.saeed459@example.com','01574096254',10,'purchase','completed','',NULL,NULL,NULL,NULL,'2026-07-02 15:09:24','2026-07-02 15:09:24','2026-07-02 15:09:24',5),
(32,NULL,'Laila Hassan','laila.hassan455@example.com','01189601863',11,'purchase','completed','',NULL,NULL,NULL,NULL,'2026-07-02 00:09:24','2026-07-02 00:09:24','2026-07-02 00:09:24',14),
(33,NULL,'Dina Ibrahim','dina.ibrahim802@example.com','01576970397',11,'purchase','completed','',NULL,NULL,NULL,NULL,'2026-07-02 09:09:24','2026-07-02 09:09:24','2026-07-02 09:09:24',14),
(34,NULL,'Youssef Hassan','youssef.hassan494@example.com','01268941657',11,'purchase','completed','',NULL,NULL,NULL,NULL,'2026-07-02 10:09:24','2026-07-02 10:09:24','2026-07-02 10:09:24',6),
(35,NULL,'Youssef Sami','youssef.sami888@example.com','01000224389',12,'purchase','completed','',NULL,NULL,NULL,NULL,'2026-07-01 21:09:24','2026-07-01 21:09:24','2026-07-01 21:09:24',15),
(36,NULL,'Ahmed Nabil','ahmed.nabil643@example.com','01532580118',12,'purchase','completed','',NULL,NULL,NULL,NULL,'2026-07-01 22:09:24','2026-07-01 22:09:24','2026-07-01 22:09:24',14),
(37,NULL,'Sara Kamal','sara.kamal243@example.com','01571318546',13,'purchase','completed','',NULL,NULL,NULL,NULL,'2026-07-01 22:09:24','2026-07-01 22:09:24','2026-07-01 22:09:24',6),
(38,NULL,'Mona Fathy','mona.fathy307@example.com','01226284637',13,'purchase','completed','',NULL,NULL,NULL,NULL,'2026-07-02 10:09:24','2026-07-02 10:09:24','2026-07-02 10:09:24',14),
(39,NULL,'Mona Nabil','mona.nabil117@example.com','01129239181',13,'purchase','completed','',NULL,NULL,NULL,NULL,'2026-07-02 11:09:24','2026-07-02 11:09:24','2026-07-02 11:09:24',6),
(40,NULL,'Laila Fouad','laila.fouad983@example.com','01040495795',14,'purchase','completed','',NULL,NULL,NULL,NULL,'2026-07-02 20:09:24','2026-07-02 20:09:24','2026-07-02 20:09:24',15),
(41,NULL,'Rania Adel','rania.adel721@example.com','01503667380',14,'purchase','completed','',NULL,NULL,NULL,NULL,'2026-07-02 00:09:24','2026-07-02 00:09:24','2026-07-02 00:09:24',15);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `key` varchar(100) NOT NULL,
  `value` text NOT NULL DEFAULT '',
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES
('motd','Welcome to Grand Transmission Auto! Browse our latest vehicles and find your perfect match.');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','employee','client') NOT NULL DEFAULT 'client',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(4,'Farid Elkassas','farid.elkassas@grandtransmissionsautos.com','01012345678','15 Tahrir Street, Downtown, Cairo, Egypt','$2y$12$fofxLgM2KQJZ5mr/aDiG7uA.O6MXzsQHa/HII7T.lDnFYX1SvKgcC','admin','2026-07-01 20:54:57'),
(5,'Mahmoud Farid','mahmoud.farid@grandtransmissionsautos.com','01123456789','42 Gomhoria Street, Nasr City, Cairo, Egypt','$2y$12$fofxLgM2KQJZ5mr/aDiG7uA.O6MXzsQHa/HII7T.lDnFYX1SvKgcC','employee','2026-07-01 20:54:57'),
(6,'Mohamed Eltayeb','mohamed.eltayeb@grandtransmissionsautos.com','01234567890','7 El-Nasr Road, Maadi, Cairo, Egypt','$2y$12$A9jLMjgxinDE4sAzsqYzCuoSXia1TlE2VR8wNXHwCYQBJOdE1TUhG','employee','2026-07-01 20:57:44'),
(7,'mahmoud farid','mahmoudf_1993@hotmail.com','01512345678','23 Corniche El-Nil, Zamalek, Cairo, Egypt','$2y$12$1AOiSJsjUoyobI0iBUTtUu0xbwRz73UzIesp9sPsA1M8ifGgdhpca','client','2026-07-01 20:58:50'),
(14,'Mohamed Ali','mohamed.ali@grandtransmissionsautos.com',NULL,NULL,'$2y$12$sW6HnXRNXnV1G1ObZe42MOmvrwnhNrKBFncKd2637GFZwPfdBOOly','employee','2026-07-02 19:30:51'),
(15,'Omar Ismail','omar.ismail@grandtransmissionsautos.com',NULL,NULL,'$2y$12$sW6HnXRNXnV1G1ObZe42MOmvrwnhNrKBFncKd2637GFZwPfdBOOly','employee','2026-07-02 19:30:51');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `verification_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `verification_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `code_hash` varchar(255) NOT NULL,
  `purpose` varchar(30) NOT NULL DEFAULT 'guest_order',
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_email_purpose` (`email`,`purpose`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `verification_codes` WRITE;
/*!40000 ALTER TABLE `verification_codes` DISABLE KEYS */;
INSERT INTO `verification_codes` VALUES
(5,'mahmoudf_1993@hotmail.com','$2y$12$KuzY5LTdbl3pdlMXlIxPHOHY5W1gOkR.eXEolJl9B5cxkq9navyX6','guest_order','2026-07-02 06:04:08',NULL,'2026-07-02 05:54:08'),
(6,'faridelksass@gmail.com','$2y$12$Mi2U/xUc.hn9RGt9WZ3IGeAKfGcNJGAYXXrwa/KxOW3y8GqEAFXNq','guest_order','2026-07-02 07:20:53','2026-07-02 07:11:58','2026-07-02 07:10:53');
/*!40000 ALTER TABLE `verification_codes` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

