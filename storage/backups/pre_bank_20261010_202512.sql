-- MySQL dump 10.13  Distrib 8.0.46, for Linux (x86_64)
--
-- Host: localhost    Database: laundry_saas_v2
-- ------------------------------------------------------
-- Server version	8.0.46-0ubuntu0.24.04.4

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

--
-- Table structure for table `banks`
--

DROP TABLE IF EXISTS `banks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sandi_bank` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_bank` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banks`
--

LOCK TABLES `banks` WRITE;
/*!40000 ALTER TABLE `banks` DISABLE KEYS */;
/*!40000 ALTER TABLE `banks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cabang`
--

DROP TABLE IF EXISTS `cabang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cabang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` bigint unsigned DEFAULT NULL,
  `kode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_telp` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pemilik_id` bigint unsigned DEFAULT NULL,
  `status` enum('aktif','nonaktif','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan_kaki` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cabang_kode_unique` (`kode`),
  KEY `cabang_pemilik_id_foreign` (`pemilik_id`),
  KEY `cabang_status_index` (`status`),
  KEY `cabang_merchant_id_index` (`merchant_id`),
  CONSTRAINT `cabang_merchant_id_foreign` FOREIGN KEY (`merchant_id`) REFERENCES `merchant` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cabang_pemilik_id_foreign` FOREIGN KEY (`pemilik_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cabang`
--

LOCK TABLES `cabang` WRITE;
/*!40000 ALTER TABLE `cabang` DISABLE KEYS */;
INSERT INTO `cabang` VALUES (1,1,'TBN-01','Cabang Tuban','Jl. Pahlawan 1','08111',NULL,4,'aktif',NULL,NULL,'2026-10-06 22:43:08','2026-10-07 15:09:20',NULL),(2,2,'SBY-01','Cabang Surabaya','Jl. Tunjungan 2','08222',NULL,NULL,'aktif',NULL,NULL,'2026-10-06 22:43:08','2026-10-07 15:09:20',NULL),(3,3,'DMG-01','Demo Cabang Tuban','Tuban, Jawa Timur','+6285196269837','demo@javacom.co.id',10,'aktif',NULL,NULL,'2026-10-07 15:09:20','2026-10-07 15:09:56',NULL),(4,3,'DMG-02','Demo Cabang Surabaya','Surabaya, Jawa Timur','+6285196269837','demo@javacom.co.id',10,'aktif',NULL,NULL,'2026-10-07 15:09:20','2026-10-07 15:09:56',NULL),(5,3,'DMG-03','Demo Cabang Gresik','Gresik, Jawa Timur','+6285196269837','demo@javacom.co.id',10,'aktif',NULL,NULL,'2026-10-07 15:09:20','2026-10-07 15:09:56',NULL),(15,11,'C-LAUND','Cabang Utama','Jl. Contoh No. 1, Surabaya','081200000001','owner@laundry.com',18,'aktif',NULL,NULL,'2026-10-10 07:57:23','2026-10-10 07:57:23',NULL),(16,11,'C-LAUND1','Cabang Kedua','Jl. Contoh No. 2, Sidoarjo','081200000002','owner@laundry.com',NULL,'aktif',NULL,NULL,'2026-10-10 07:57:46','2026-10-10 07:57:46',NULL);
/*!40000 ALTER TABLE `cabang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cabang_user`
--

DROP TABLE IF EXISTS `cabang_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cabang_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `cabang_id` bigint unsigned NOT NULL,
  `peran_di_cabang` enum('owner','admin','karyawan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cabang_user_user_id_cabang_id_unique` (`user_id`,`cabang_id`),
  KEY `cabang_user_cabang_id_index` (`cabang_id`),
  CONSTRAINT `cabang_user_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cabang_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cabang_user`
--

LOCK TABLES `cabang_user` WRITE;
/*!40000 ALTER TABLE `cabang_user` DISABLE KEYS */;
INSERT INTO `cabang_user` VALUES (4,1,1,'admin','2026-10-07 15:09:20','2026-10-07 15:09:20'),(5,2,1,'karyawan','2026-10-07 15:09:20','2026-10-07 15:09:20'),(6,4,1,'admin','2026-10-07 15:09:20','2026-10-07 15:09:20'),(7,5,1,'admin','2026-10-07 15:09:20','2026-10-07 15:09:20'),(8,3,2,'admin','2026-10-07 15:09:20','2026-10-07 15:09:20'),(9,6,2,'karyawan','2026-10-07 15:09:20','2026-10-07 15:09:20'),(10,7,2,'admin','2026-10-07 15:09:20','2026-10-07 15:09:20'),(11,10,3,'owner','2026-10-07 15:09:56','2026-10-07 15:09:56'),(12,10,4,'owner','2026-10-07 15:09:56','2026-10-07 15:09:56'),(13,10,5,'owner','2026-10-07 15:09:56','2026-10-07 15:09:56'),(14,19,15,'karyawan','2026-10-10 07:57:47','2026-10-10 07:57:47'),(15,20,16,'karyawan','2026-10-10 07:57:47','2026-10-10 07:57:47'),(16,18,15,'owner','2026-10-10 07:57:47','2026-10-10 07:57:47');
/*!40000 ALTER TABLE `cabang_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `data_banks`
--

DROP TABLE IF EXISTS `data_banks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_banks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `nama_bank` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_rekening` int NOT NULL,
  `nama_pemilik` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `data_banks_user_id_foreign` (`user_id`),
  CONSTRAINT `data_banks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_banks`
--

LOCK TABLES `data_banks` WRITE;
/*!40000 ALTER TABLE `data_banks` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_banks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `diskon_langganan`
--

DROP TABLE IF EXISTS `diskon_langganan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `diskon_langganan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `min_cabang` int unsigned NOT NULL COMMENT 'Berlaku untuk jumlah cabang >= nilai ini',
  `diskon_persen` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'Diskon 0-100 persen',
  `label` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_aktif` tinyint(1) NOT NULL DEFAULT '1',
  `urutan` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `diskon_langganan_min_cabang_unique` (`min_cabang`),
  KEY `diskon_langganan_is_aktif_min_cabang_index` (`is_aktif`,`min_cabang`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diskon_langganan`
--

LOCK TABLES `diskon_langganan` WRITE;
/*!40000 ALTER TABLE `diskon_langganan` DISABLE KEYS */;
INSERT INTO `diskon_langganan` VALUES (1,1,0.00,'Tanpa diskon',1,1,'2026-10-07 22:46:35','2026-10-10 13:15:02'),(2,2,10.00,'Mulai berkembang',1,2,'2026-10-07 22:46:35','2026-10-10 13:15:02'),(3,3,20.00,'Jaringan kecil',1,3,'2026-10-07 22:46:35','2026-10-10 13:15:02'),(4,4,25.00,'Jaringan menengah',1,4,'2026-10-07 22:46:35','2026-10-10 13:15:02'),(5,5,30.00,'Jaringan besar',1,5,'2026-10-07 22:46:35','2026-10-10 13:15:02');
/*!40000 ALTER TABLE `diskon_langganan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
-- Table structure for table `hargas`
--

DROP TABLE IF EXISTS `hargas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hargas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cabang_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `jenis` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kg` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kg_numeric` decimal(8,2) DEFAULT NULL,
  `harga` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga_numeric` decimal(12,2) DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hari` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hargas_user_id_foreign` (`user_id`),
  KEY `hargas_cabang_id_index` (`cabang_id`),
  CONSTRAINT `hargas_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `hargas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hargas`
--

LOCK TABLES `hargas` WRITE;
/*!40000 ALTER TABLE `hargas` DISABLE KEYS */;
INSERT INTO `hargas` VALUES (1,1,1,'Cuci Kering','5000',1.00,'5000',5000.00,'1','2','2026-10-06 22:43:09','2026-10-06 22:43:09'),(2,1,1,'Cuci Setrika','7000',1.00,'7000',7000.00,'1','3','2026-10-06 22:43:09','2026-10-06 22:43:09'),(3,2,3,'Cuci Kering SBY','6000',1.00,'6000',6000.00,'1','2','2026-10-06 22:43:09','2026-10-06 22:43:09'),(6,1,2,'Setrika Saja','1',1.00,'3000',3000.00,'1','1','2026-10-06 23:03:32','2026-10-06 23:03:32'),(7,3,10,'Demo Cuci','1',1.00,'7000',7000.00,'1','2','2026-10-07 15:27:45','2026-10-07 15:27:45'),(8,4,10,'Cuci Kering','1',1.00,'7000',7000.00,'1','2','2026-10-07 16:01:16','2026-10-07 16:01:16'),(9,5,10,'Cuci Kering','1',1.00,'7000',7000.00,'1','2','2026-10-07 16:01:16','2026-10-07 16:01:16'),(10,15,18,'Cuci Kering','1',NULL,'7000',7000.00,'1','2','2026-10-10 08:06:09','2026-10-10 08:06:09'),(11,15,18,'Cuci Setrika','1',NULL,'9000',9000.00,'1','2','2026-10-10 08:06:09','2026-10-10 08:06:09'),(12,15,18,'Setrika Saja','1',NULL,'5000',5000.00,'1','2','2026-10-10 08:06:09','2026-10-10 08:06:09'),(13,15,18,'Bed Cover','1',NULL,'25000',25000.00,'1','2','2026-10-10 08:06:09','2026-10-10 08:06:09'),(14,16,18,'Cuci Kering','1',NULL,'7000',7000.00,'1','2','2026-10-10 08:06:09','2026-10-10 08:06:09'),(15,16,18,'Cuci Setrika','1',NULL,'9000',9000.00,'1','2','2026-10-10 08:06:09','2026-10-10 08:06:09'),(16,16,18,'Selimut','1',NULL,'15000',15000.00,'1','2','2026-10-10 08:06:09','2026-10-10 08:06:09');
/*!40000 ALTER TABLE `hargas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
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
-- Table structure for table `langganan`
--

DROP TABLE IF EXISTS `langganan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `langganan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cabang_id` bigint unsigned NOT NULL,
  `merchant_id` bigint unsigned DEFAULT NULL,
  `paket_id` bigint unsigned NOT NULL,
  `jumlah_cabang` int unsigned NOT NULL DEFAULT '1' COMMENT 'Basis diskon bertingkat: berapa cabang ditagih sekaligus',
  `siklus` enum('bulanan','tahunan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bulanan',
  `status` enum('trial','aktif','jatuh_tempo','berhenti') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'trial',
  `mulai` date NOT NULL,
  `berakhir` date NOT NULL,
  `trial_berakhir` date DEFAULT NULL,
  `harga_disepakati` decimal(12,2) DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `langganan_paket_id_foreign` (`paket_id`),
  KEY `langganan_cabang_id_status_index` (`cabang_id`,`status`),
  KEY `langganan_berakhir_index` (`berakhir`),
  KEY `langganan_merchant_id_index` (`merchant_id`),
  CONSTRAINT `langganan_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `langganan_merchant_id_foreign` FOREIGN KEY (`merchant_id`) REFERENCES `merchant` (`id`) ON DELETE SET NULL,
  CONSTRAINT `langganan_paket_id_foreign` FOREIGN KEY (`paket_id`) REFERENCES `paket` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `langganan`
--

LOCK TABLES `langganan` WRITE;
/*!40000 ALTER TABLE `langganan` DISABLE KEYS */;
INSERT INTO `langganan` VALUES (1,1,1,1,1,'bulanan','aktif','2026-10-07','2026-11-07',NULL,NULL,NULL,'2026-10-06 23:35:42','2026-10-07 22:46:45'),(2,2,2,2,1,'bulanan','trial','2026-10-07','2026-10-21','2026-10-21',NULL,NULL,'2026-10-06 23:35:42','2026-10-07 22:46:45'),(3,3,3,2,3,'bulanan','aktif','2026-10-08','2026-11-08',NULL,NULL,'Backfill Tahap 4 (langganan per cabang).','2026-10-07 22:46:45','2026-10-07 22:46:45'),(4,4,3,2,3,'bulanan','aktif','2026-10-08','2026-11-08',NULL,NULL,'Backfill Tahap 4 (langganan per cabang).','2026-10-07 22:46:45','2026-10-07 22:46:45'),(5,5,3,2,3,'bulanan','aktif','2026-10-08','2026-11-08',NULL,NULL,'Backfill Tahap 4 (langganan per cabang).','2026-10-07 22:46:45','2026-10-07 22:46:45'),(15,15,11,2,2,'bulanan','trial','2026-10-10','2026-10-24','2026-10-24',NULL,'Trial 14 hari saat onboarding.','2026-10-10 07:57:23','2026-10-10 07:57:46'),(16,16,11,2,2,'bulanan','aktif','2026-10-10','2026-11-10',NULL,NULL,'Cabang tambahan (onboarding).','2026-10-10 07:57:46','2026-10-10 07:57:46');
/*!40000 ALTER TABLE `langganan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `laundry_settings`
--

DROP TABLE IF EXISTS `laundry_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `laundry_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cabang_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `target_day` int NOT NULL DEFAULT '0',
  `target_month` int NOT NULL DEFAULT '0',
  `target_year` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `laundry_settings_user_id_foreign` (`user_id`),
  KEY `laundry_settings_cabang_id_index` (`cabang_id`),
  CONSTRAINT `laundry_settings_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `laundry_settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `laundry_settings`
--

LOCK TABLES `laundry_settings` WRITE;
/*!40000 ALTER TABLE `laundry_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `laundry_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `merchant`
--

DROP TABLE IF EXISTS `merchant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `merchant` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pemilik_id` bigint unsigned DEFAULT NULL,
  `paket_id` bigint unsigned DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_telp` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('aktif','nonaktif','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `merchant_kode_unique` (`kode`),
  UNIQUE KEY `merchant_slug_unique` (`slug`),
  KEY `merchant_pemilik_id_index` (`pemilik_id`),
  KEY `merchant_paket_id_index` (`paket_id`),
  KEY `merchant_status_index` (`status`),
  CONSTRAINT `merchant_paket_id_foreign` FOREIGN KEY (`paket_id`) REFERENCES `paket` (`id`) ON DELETE SET NULL,
  CONSTRAINT `merchant_pemilik_id_foreign` FOREIGN KEY (`pemilik_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `merchant`
--

LOCK TABLES `merchant` WRITE;
/*!40000 ALTER TABLE `merchant` DISABLE KEYS */;
INSERT INTO `merchant` VALUES (1,'M-TBN011','Cabang Tuban','cabang-tuban-1',4,2,NULL,'08111','Jl. Pahlawan 1',NULL,'aktif',NULL,NULL,'2026-10-07 15:09:20','2026-10-07 15:09:45'),(2,'M-SBY012','Cabang Surabaya','cabang-surabaya-2',NULL,2,NULL,'08222','Jl. Tunjungan 2',NULL,'aktif',NULL,NULL,'2026-10-07 15:09:20','2026-10-07 15:09:45'),(3,'M-DEMOGRP','Demo Laundry Group','demo-laundry-group',10,2,'demo@javacom.co.id','+6285196269837','Jl. Pahlawan Gg. Selorejo 2 No. 248C, Tuban',NULL,'aktif','Merchant contoh (3 cabang) untuk pengujian monitoring lintas cabang. Data uji, boleh dihapus.',NULL,'2026-10-07 15:09:20','2026-10-07 15:09:56'),(11,'M-LAUND','Laundry Demo Owner','laundry-demo-owner',18,2,'owner@laundry.com','081200000001','Jl. Contoh No. 1, Surabaya',NULL,'aktif','Akun demo (owner/kasir) — dibuat untuk showcase.',NULL,'2026-10-10 07:57:23','2026-10-10 07:57:46');
/*!40000 ALTER TABLE `merchant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_05_24_091904_create_transaksis_table',1),(4,'2019_05_24_094505_create_hargas_table',1),(5,'2021_03_19_231220_create_page_settings_table',1),(6,'2021_03_21_124956_add_theme_to_users_table',1),(7,'2021_03_22_001021_create_laundry_settings_table',1),(8,'2021_05_07_100208_create_permission_tables',1),(9,'2021_05_07_135323_create_data_banks_table',1),(10,'2021_05_07_155403_add_field_in_transaksi',1),(11,'2021_05_11_130732_create_notifications_settings_table',1),(12,'2021_08_08_100000_create_banks_tables',1),(13,'2021_12_30_231550_add_field_username_telegram_channel_to_notifications_settings_table',1),(14,'2022_01_28_171610_add_field_foto_to_users_table',1),(15,'2022_01_29_185408_add_field_token_wa_to_notifications_settings_table',1),(16,'2022_01_31_105111_update_field_auth_in_users_table',1),(17,'2022_01_31_112034_add_field_karyawan_id_wa_in_users_table',1),(18,'2022_02_02_220553_create_jobs_table',1),(19,'2022_02_02_231121_create_failed_jobs_table',1),(20,'2022_02_03_144826_add_field_point_in_users_table',1),(21,'2022_09_27_125933_create_notifications_table',1),(22,'2026_10_06_210000_create_cabang_table',1),(23,'2026_10_06_210100_add_cabang_id_and_numeric_types',1),(24,'2026_10_06_220000_create_paket_langganan_tagihan_tables',2),(25,'2026_10_08_100000_add_batal_status_to_transaksis',3),(26,'2026_10_07_230000_create_merchant_tables',4),(27,'2026_10_08_001000_add_merchant_id_to_langganan_tagihan',5),(28,'2026_10_08_001100_create_diskon_langganan_table',5),(29,'2026_10_10_120000_add_order_online_pickup_fields',6),(30,'2026_10_10_130000_create_pesanan_online_table',7),(31,'2026_10_10_140000_add_pickup_delivery_status',8);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
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
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(2,'App\\Models\\User',2),(1,'App\\Models\\User',3),(1,'App\\Models\\User',4),(3,'App\\Models\\User',5),(2,'App\\Models\\User',6),(3,'App\\Models\\User',7),(4,'App\\Models\\User',8),(1,'App\\Models\\User',10),(1,'App\\Models\\User',11),(1,'App\\Models\\User',14),(1,'App\\Models\\User',15),(1,'App\\Models\\User',16),(1,'App\\Models\\User',17),(1,'App\\Models\\User',18),(2,'App\\Models\\User',19),(2,'App\\Models\\User',20),(3,'App\\Models\\User',21),(3,'App\\Models\\User',22),(3,'App\\Models\\User',23),(3,'App\\Models\\User',51),(3,'App\\Models\\User',52);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaksi_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `kategori` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,15,21,'info','Pakaian Selesai','Pakaian Sudah Selesai dan Sudah Bisa Diambil :)',0,'2026-10-10 08:06:37','2026-10-10 08:06:37');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications_settings`
--

DROP TABLE IF EXISTS `notifications_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cabang_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `telegram_order_masuk` tinyint(1) NOT NULL,
  `telegram_order_selesai` tinyint(1) NOT NULL,
  `email` tinyint(1) NOT NULL,
  `telegram_channel_masuk` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telegram_channel_selesai` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wa_order_selesai` tinyint(1) NOT NULL,
  `wa_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_settings_user_id_foreign` (`user_id`),
  KEY `notifications_settings_cabang_id_index` (`cabang_id`),
  CONSTRAINT `notifications_settings_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `notifications_settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications_settings`
--

LOCK TABLES `notifications_settings` WRITE;
/*!40000 ALTER TABLE `notifications_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page_settings`
--

DROP TABLE IF EXISTS `page_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_hero` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tentang` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_telp` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_settings`
--

LOCK TABLES `page_settings` WRITE;
/*!40000 ALTER TABLE `page_settings` DISABLE KEYS */;
INSERT INTO `page_settings` VALUES (1,'Javacom Laundry',NULL,'Sistem kasir & manajemen usaha laundry dari Javacom — Digital Growth Partner. Kelola order, pelanggan, karyawan, dan laporan keuangan dalam satu dashboard.','javacom.official','javacom.official','javacomofficial','6285196269837',NULL,'javacom.dev@gmail.com','2026-10-07 14:11:42','2026-10-07 14:48:41');
/*!40000 ALTER TABLE `page_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paket`
--

DROP TABLE IF EXISTS `paket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paket` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `harga_bulanan` decimal(12,2) NOT NULL DEFAULT '0.00',
  `harga_tahunan` decimal(12,2) NOT NULL DEFAULT '0.00',
  `batas_cabang` int NOT NULL DEFAULT '1',
  `batas_user` int NOT NULL DEFAULT '3',
  `batas_transaksi_bulanan` int NOT NULL DEFAULT '-1',
  `fitur` json DEFAULT NULL,
  `is_aktif` tinyint(1) NOT NULL DEFAULT '1',
  `urutan` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paket_kode_unique` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paket`
--

LOCK TABLES `paket` WRITE;
/*!40000 ALTER TABLE `paket` DISABLE KEYS */;
INSERT INTO `paket` VALUES (1,'basic','Basic','Untuk laundry satu cabang yang baru mulai digitalisasi.',150000.00,1500000.00,1,3,500,'{\"api\": false, \"excel\": true, \"telegram\": false, \"whatsapp\": false, \"invoice_pdf\": true, \"multi_cabang\": false, \"laundry_dasar\": true, \"prioritas_support\": false, \"laporan_lintas_cabang\": false}',1,1,'2026-10-06 23:34:01','2026-10-06 23:34:01',NULL),(2,'pro','Pro','Untuk laundry berkembang: notifikasi pelanggan & beberapa cabang.',350000.00,3500000.00,3,15,-1,'{\"api\": false, \"excel\": true, \"telegram\": true, \"whatsapp\": true, \"invoice_pdf\": true, \"multi_cabang\": true, \"laundry_dasar\": true, \"prioritas_support\": false, \"laporan_lintas_cabang\": true}',1,2,'2026-10-06 23:34:01','2026-10-06 23:34:01',NULL),(3,'enterprise','Enterprise','Untuk jaringan laundry: cabang & user tanpa batas, integrasi API.',750000.00,7500000.00,-1,-1,-1,'{\"api\": true, \"excel\": true, \"telegram\": true, \"whatsapp\": true, \"invoice_pdf\": true, \"multi_cabang\": true, \"laundry_dasar\": true, \"prioritas_support\": true, \"laporan_lintas_cabang\": true}',1,3,'2026-10-06 23:34:01','2026-10-06 23:34:01',NULL);
/*!40000 ALTER TABLE `paket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `pesanan_online`
--

DROP TABLE IF EXISTS `pesanan_online`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pesanan_online` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cabang_id` bigint unsigned DEFAULT NULL,
  `merchant_id` bigint unsigned DEFAULT NULL,
  `nama` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telp` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `mode_layanan` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pickup',
  `jenis_pakaian` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estimasi_kg` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `status_online` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Menunggu',
  `kode_pesanan` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaksi_id` bigint unsigned DEFAULT NULL,
  `diproses_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pesanan_online_kode_pesanan_unique` (`kode_pesanan`),
  KEY `pesanan_online_cabang_id_index` (`cabang_id`),
  KEY `pesanan_online_status_online_index` (`status_online`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pesanan_online`
--

LOCK TABLES `pesanan_online` WRITE;
/*!40000 ALTER TABLE `pesanan_online` DISABLE KEYS */;
/*!40000 ALTER TABLE `pesanan_online` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','web','2026-10-06 22:43:08','2026-10-06 22:43:08'),(2,'Karyawan','web','2026-10-06 22:43:08','2026-10-06 22:43:08'),(3,'Customer','web','2026-10-06 22:43:08','2026-10-06 22:43:08'),(4,'SuperAdmin','web','2026-10-06 23:35:42','2026-10-06 23:35:42');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tagihan`
--

DROP TABLE IF EXISTS `tagihan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tagihan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `langganan_id` bigint unsigned NOT NULL,
  `cabang_id` bigint unsigned NOT NULL,
  `merchant_id` bigint unsigned DEFAULT NULL,
  `nomor` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `status` enum('belum_bayar','menunggu_verifikasi','lunas','batal') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum_bayar',
  `jatuh_tempo` date NOT NULL,
  `periode_mulai` date NOT NULL,
  `periode_akhir` date NOT NULL,
  `dibayar_pada` timestamp NULL DEFAULT NULL,
  `metode_bayar` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bukti_bayar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tagihan_nomor_unique` (`nomor`),
  KEY `tagihan_langganan_id_foreign` (`langganan_id`),
  KEY `tagihan_cabang_id_status_index` (`cabang_id`,`status`),
  KEY `tagihan_jatuh_tempo_index` (`jatuh_tempo`),
  KEY `tagihan_merchant_id_index` (`merchant_id`),
  CONSTRAINT `tagihan_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tagihan_langganan_id_foreign` FOREIGN KEY (`langganan_id`) REFERENCES `langganan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tagihan_merchant_id_foreign` FOREIGN KEY (`merchant_id`) REFERENCES `merchant` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tagihan`
--

LOCK TABLES `tagihan` WRITE;
/*!40000 ALTER TABLE `tagihan` DISABLE KEYS */;
INSERT INTO `tagihan` VALUES (1,1,1,1,'SUB-202610-0001',150000.00,'belum_bayar','2026-10-14','2026-10-07','2026-11-07',NULL,NULL,NULL,NULL,'2026-10-06 23:35:42','2026-10-07 22:46:45');
/*!40000 ALTER TABLE `tagihan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaksis`
--

DROP TABLE IF EXISTS `transaksis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transaksis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cabang_id` bigint unsigned DEFAULT NULL,
  `invoice` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_transaksi` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `customer` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_customer` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_order` enum('Dijemput','Process','Done','Diantar','Delivery','Batal') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Process',
  `status_payment` enum('Pending','Success') COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga_id` int NOT NULL,
  `kg` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kg_numeric` decimal(8,2) DEFAULT NULL,
  `hari` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga_numeric` decimal(12,2) DEFAULT NULL,
  `disc` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disc_numeric` decimal(12,2) DEFAULT NULL,
  `harga_akhir` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harga_akhir_numeric` decimal(12,2) DEFAULT NULL,
  `ongkir_numeric` decimal(12,2) DEFAULT '0.00',
  `jarak_km` decimal(8,2) DEFAULT NULL,
  `mode_layanan` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'reguler',
  `alamat_jemput` text COLLATE utf8mb4_unicode_ci,
  `sumber_order` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'kasir',
  `total_numeric` decimal(14,2) DEFAULT NULL,
  `jenis_pembayaran` enum('Tunai','Transfer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bulan` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_ambil` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_ambil` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaksis_cabang_id_index` (`cabang_id`),
  KEY `transaksis_tanggal_masuk_index` (`tanggal_masuk`),
  KEY `transaksis_status_order_index` (`status_order`),
  KEY `transaksis_status_payment_index` (`status_payment`),
  CONSTRAINT `transaksis_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaksis`
--

LOCK TABLES `transaksis` WRITE;
/*!40000 ALTER TABLE `transaksis` DISABLE KEYS */;
INSERT INTO `transaksis` VALUES (1,1,'INV-2413','5','2','07-10-2026','2026-10-07','Budi Customer','cus1@laundry.com','Process','Success',2,'5.5',5.50,'3','7000',7000.00,'10',10.00,'34650',34650.00,0.00,NULL,'reguler',NULL,'kasir',38500.00,'Tunai','7','10','2026',NULL,NULL,'2026-10-06 23:03:11','2026-10-06 23:03:11'),(2,2,'INV-SBY-3937','7','6','07-10-2026','2026-10-07','Siti Customer SBY','cus.sby@laundry.com','Process','Success',3,'3',3.00,'2','6000',6000.00,'0',0.00,'18000',18000.00,0.00,NULL,'reguler',NULL,'kasir',18000.00,'Tunai','7','10','2026',NULL,NULL,'2026-10-06 23:03:26','2026-10-06 23:03:26'),(8,3,'INV-DEMO-DMG-01-001','101','10','2026-10-03','2026-10-03','Siti Aminah','pelanggan1@contoh.id','Done','Success',7,'7',7.00,'2','7000',7000.00,'0',0.00,'49000',49000.00,0.00,NULL,'reguler',NULL,'kasir',49000.00,'Tunai','03','10','2026',NULL,NULL,'2026-10-03 02:00:00','2026-10-03 02:00:00'),(9,3,'INV-DEMO-DMG-01-002','102','10','2026-10-08','2026-10-08','Agus Wijaya','pelanggan2@contoh.id','Delivery','Success',7,'2.5',2.50,'2','7000',7000.00,'0',0.00,'17500',17500.00,0.00,NULL,'reguler',NULL,'kasir',17500.00,'Transfer','08','10','2026',NULL,NULL,'2026-10-08 02:00:00','2026-10-08 02:00:00'),(10,3,'INV-DEMO-DMG-01-003','103','10','2026-10-12','2026-10-12','Dewi Lestari','pelanggan3@contoh.id','Process','Success',7,'9',9.00,'2','7000',7000.00,'10',10.00,'56700',56700.00,0.00,NULL,'reguler',NULL,'kasir',56700.00,'Tunai','12','10','2026',NULL,NULL,'2026-10-12 02:00:00','2026-10-12 02:00:00'),(11,4,'INV-DEMO-DMG-02-004','104','10','2026-10-18','2026-10-18','Rudi Hartono','pelanggan4@contoh.id','Done','Pending',8,'3.5',3.50,'2','7000',7000.00,'0',0.00,'24500',24500.00,0.00,NULL,'reguler',NULL,'kasir',24500.00,'Transfer','18','10','2026',NULL,NULL,'2026-10-18 02:00:00','2026-10-18 02:00:00'),(12,4,'INV-DEMO-DMG-02-005','105','10','2026-10-24','2026-10-24','Budi Santoso','pelanggan5@contoh.id','Delivery','Success',8,'5',5.00,'2','7000',7000.00,'0',0.00,'35000',35000.00,0.00,NULL,'reguler',NULL,'kasir',35000.00,'Tunai','24','10','2026',NULL,NULL,'2026-10-24 02:00:00','2026-10-24 02:00:00'),(13,5,'INV-DEMO-DMG-03-006','106','10','2026-10-03','2026-10-03','Siti Aminah','pelanggan6@contoh.id','Process','Success',9,'6',6.00,'2','7000',7000.00,'10',10.00,'37800',37800.00,0.00,NULL,'reguler',NULL,'kasir',37800.00,'Transfer','03','10','2026',NULL,NULL,'2026-10-03 02:00:00','2026-10-03 02:00:00'),(14,5,'INV-DEMO-DMG-03-007','107','10','2026-10-08','2026-10-08','Agus Wijaya','pelanggan7@contoh.id','Done','Success',9,'4.5',4.50,'2','7000',7000.00,'0',0.00,'31500',31500.00,0.00,NULL,'reguler',NULL,'kasir',31500.00,'Tunai','08','10','2026',NULL,NULL,'2026-10-08 02:00:00','2026-10-08 02:00:00'),(15,15,'3325192026','21','19','10-10-2026','2026-10-10','Pelanggan Demo 1','pelanggan1@laundry.com','Delivery','Success',11,'3.5',3.50,'2','9000',9000.00,NULL,0.00,'31500',31500.00,0.00,NULL,'reguler',NULL,'kasir',31500.00,'Tunai','10','10','2026',NULL,NULL,'2026-10-10 08:06:21','2026-10-10 08:06:38'),(16,16,'4831202026','23','20','10-10-2026','2026-10-10','Pelanggan Demo 3','pelanggan3@laundry.com','Process','Success',15,'5',5.00,'2','9000',9000.00,'10',10.00,'40500',40500.00,0.00,NULL,'reguler',NULL,'kasir',45000.00,'Transfer','10','10','2026',NULL,NULL,'2026-10-10 08:06:48','2026-10-10 08:06:48');
/*!40000 ALTER TABLE `transaksis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cabang_id` bigint unsigned DEFAULT NULL,
  `merchant_id` bigint unsigned DEFAULT NULL,
  `karyawan_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `auth` enum('Admin','Karyawan','Customer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Active','Not Active') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `nama_cabang` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat_cabang` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_telp` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `theme` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `foto` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `point` int NOT NULL DEFAULT '0',
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_karyawan_id_foreign` (`karyawan_id`),
  KEY `users_cabang_id_index` (`cabang_id`),
  KEY `users_merchant_id_index` (`merchant_id`),
  CONSTRAINT `users_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `users_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `users_merchant_id_foreign` FOREIGN KEY (`merchant_id`) REFERENCES `merchant` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,1,NULL,'Admin Tuban','admin.tbn@laundry.com',NULL,'Admin','Active','Cabang Tuban',NULL,NULL,NULL,'0',NULL,0,'$2y$10$zbQQxz5soGRXdgGSZWo8jOSUj2GGwSC8UWf1o/KPm08YkER.OUpz.',NULL,'2026-10-06 22:43:08','2026-10-07 15:09:20'),(2,1,1,NULL,'Karyawan Tuban','kar.tbn@laundry.com',NULL,'Karyawan','Active','Cabang Tuban',NULL,NULL,NULL,'0',NULL,0,'$2y$10$hHjFeM8NHZpkeBRzOcHS..5ZnWTrtDSK9daawzWweuqtJzitYMVcK',NULL,'2026-10-06 22:43:08','2026-10-07 15:09:20'),(3,2,2,NULL,'Admin Surabaya','admin.sby@laundry.com',NULL,'Admin','Active','Cabang Surabaya',NULL,NULL,NULL,'0',NULL,0,'$2y$10$HT4R/rf7LAdv0AHG.2VLku.b5ZF3Q10up6uzkQFWCtthRMY9NaI2C',NULL,'2026-10-06 22:43:09','2026-10-07 15:09:20'),(4,1,1,NULL,'Admin Laundry','admin@laundry.com',NULL,'Admin','Active',NULL,NULL,NULL,NULL,'0',NULL,0,'$2y$10$80fcT.wwcftjoDNkMRf8KuqnVumEm.SMfLr7QglEe16EFmf82kXzO',NULL,'2026-10-06 22:43:24','2026-10-08 10:55:58'),(5,1,1,2,'Budi Customer','cus1@laundry.com',NULL,'Customer','Active',NULL,NULL,NULL,'08123456789','0',NULL,0,'$2y$10$Pk8OXhtCKbcbwWVmJHHLaeJZXvBe3bNRrR0eH3jIEB/hsBwXaRBEm',NULL,'2026-10-06 23:03:11','2026-10-07 15:09:20'),(6,2,2,NULL,'Karyawan Surabaya','kar.sby@laundry.com',NULL,'Karyawan','Active','Cabang Surabaya',NULL,NULL,NULL,'0',NULL,0,'$2y$10$MUD9nMaffUX.SWD3CG/mA..YlKfBh7GHaZx8nyum88onyfzxxXM6i',NULL,'2026-10-06 23:03:18','2026-10-07 15:09:20'),(7,2,2,6,'Siti Customer SBY','cus.sby@laundry.com',NULL,'Customer','Active',NULL,NULL,NULL,'089999','0',NULL,0,'$2y$10$4pRfzrsuvMO6bClPvpAA6OM9HgNfUhjyn.to3GVp2d3u2YnmdPpBK',NULL,'2026-10-06 23:03:26','2026-10-07 15:09:20'),(8,NULL,NULL,NULL,'Super Admin','superadmin@laundry.com',NULL,'Admin','Active',NULL,NULL,NULL,NULL,'0',NULL,0,'$2y$10$Dnk0v7Y0zitHqgyT/DP9T.iKTQRCIQ9UqM0jjU8wljVthmOmosiji',NULL,'2026-10-06 23:35:42','2026-10-07 13:33:01'),(10,NULL,3,NULL,'Owner Demo Group','owner.demo@laundry.com',NULL,'Admin','Active',NULL,NULL,NULL,NULL,'0',NULL,0,'$2y$10$M4NiogFr09CNOsqiBIX1/u.AYA0f0V3gl4fP5fThracT1Tqy0xZCS',NULL,'2026-10-07 15:09:45','2026-10-07 15:09:56'),(18,15,11,NULL,'Owner Laundry Demo','owner@laundry.com',NULL,'Admin','Active',NULL,NULL,NULL,'081200000001','0',NULL,0,'$2y$10$EZ3eoofzt4JT3f.GzJolpuu.iGniMXYOQexcOyFDxCgc40VIyEp/2',NULL,'2026-10-10 07:57:23','2026-10-10 07:57:23'),(19,15,11,NULL,'Kasir 1 (Utama)','kasir1@laundry.com',NULL,'Karyawan','Active','Cabang Utama',NULL,NULL,'081200000011','0',NULL,0,'$2y$10$l8zOfe83LSeaP2H4ZqqaHeyVxQQkKZUhDi/C57AlZ4Pnau00LW1nO',NULL,'2026-10-10 07:57:46','2026-10-10 07:57:46'),(20,16,11,NULL,'Kasir 2 (Kedua)','kasir2@laundry.com',NULL,'Karyawan','Active','Cabang Kedua',NULL,NULL,'081200000012','0',NULL,0,'$2y$10$ZByS37brYrxMlhskXOWI9.tbN0ht.oJe3EtHtXYiAW05dzO5qHBmy',NULL,'2026-10-10 07:57:47','2026-10-10 07:57:47'),(21,15,11,19,'Pelanggan Demo 1','pelanggan1@laundry.com',NULL,'Customer','Active',NULL,NULL,'Jl. Melati No. 10, Surabaya','6281200000001','0',NULL,1,'$2y$10$IT3/wQVKiVEMS/d/YUC0OODbMWy8YGGdmvu0kqLIhvTkEC9YnQPLq',NULL,'2026-10-10 08:06:09','2026-10-10 08:06:37'),(22,15,11,19,'Pelanggan Demo 2','pelanggan2@laundry.com',NULL,'Customer','Active',NULL,NULL,'Jl. Anggrek No. 22, Surabaya','6281200000002','0',NULL,0,'$2y$10$kKA/gon0XC1jld8IZ7zhYOlVuV9EGqCC86TjEFgOzjriJBxrLueA.',NULL,'2026-10-10 08:06:09','2026-10-10 08:06:09'),(23,16,11,20,'Pelanggan Demo 3','pelanggan3@laundry.com',NULL,'Customer','Active',NULL,NULL,'Jl. Kenanga No. 5, Sidoarjo','6281200000003','0',NULL,0,'$2y$10$AxQO5yXQ6Iso62z9ouZAxeUW/vB5.qp19pyKX5M5hy3B6QiNwY3qy',NULL,'2026-10-10 08:06:10','2026-10-10 08:06:10');
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

-- Dump completed on 2026-10-10 20:25:12
