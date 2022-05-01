-- 2022-05-01T05:19:19+03:00 - mysql:dbname=items_test;host=127.0.0.1

-- Table structure for table `items`

DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_uuid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_index` int(11) NOT NULL DEFAULT 1,
  `title` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `src` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `likes` int(11) NOT NULL DEFAULT 0,
  `tags` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `items_uuid_unique` (`uuid`),
  KEY `items_uuid_parent_uuid_index` (`uuid`,`parent_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `items`

LOCK TABLES `items` WRITE;
INSERT INTO `items` VALUES ('778f4f71-769d-4760-8e0a-3428946ff100',NULL,'category',1,'Hello world!','This is test server for MS!',NULL,0,'[]','2022-05-01 01:55:40','2022-05-01 01:55:40');
UNLOCK TABLES;

-- Table structure for table `migrations`

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `migrations`

LOCK TABLES `migrations` WRITE;
UNLOCK TABLES;

-- Table structure for table `revisions`

DROP TABLE IF EXISTS `revisions`;
CREATE TABLE `revisions` (
  `deleted` int(11) NOT NULL DEFAULT 0,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_uuid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_index` int(11) NOT NULL DEFAULT 1,
  `title` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `src` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `likes` int(11) NOT NULL DEFAULT 0,
  `tags` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `revision_at` timestamp NULL DEFAULT NULL,
  KEY `uuid` (`uuid`),
  KEY `parent_uuid` (`parent_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `revisions`

LOCK TABLES `revisions` WRITE;
INSERT INTO `revisions` VALUES (1,'640a7439-ea6a-43dd-b03d-b3285323bd4d',NULL,'category',1,'first',11,NULL,0,'[]','2022-04-28 03:53:25','2022-04-28 03:53:25','2022-04-28 03:53:44');
INSERT INTO `revisions` VALUES (1,'1b351179-44fc-4db7-9417-15b65d2f5366',NULL,'category',1,1,NULL,NULL,0,'[]','2022-04-28 03:53:48','2022-04-28 03:53:48','2022-04-28 03:55:32');
INSERT INTO `revisions` VALUES (1,'6776ac54-7899-4519-ac38-00c6525044cf',NULL,'category',1,11,NULL,NULL,0,'[]','2022-04-28 03:55:35','2022-04-28 03:55:35','2022-04-28 03:55:40');
UNLOCK TABLES;

-- Completed on: 2022-05-01T05:19:19+03:00
