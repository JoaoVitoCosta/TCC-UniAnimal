-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: unianimal
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
-- Table structure for table `animal`
--

DROP TABLE IF EXISTS `animal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `animal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_historico` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_historico` (`id_historico`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `animal_ibfk_1` FOREIGN KEY (`id_historico`) REFERENCES `historico` (`id`),
  CONSTRAINT `animal_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `animal`
--

LOCK TABLES `animal` WRITE;
/*!40000 ALTER TABLE `animal` DISABLE KEYS */;
/*!40000 ALTER TABLE `animal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `animals`
--

DROP TABLE IF EXISTS `animals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `animals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tutor_id` bigint(20) unsigned NOT NULL,
  `nome` varchar(100) NOT NULL,
  `especie` varchar(60) NOT NULL,
  `raca` varchar(100) DEFAULT NULL,
  `porte` enum('pequeno','medio','grande') NOT NULL,
  `idade` tinyint(3) unsigned DEFAULT NULL,
  `peso` decimal(6,2) DEFAULT NULL,
  `status` enum('saudavel','atencao','emergencia') NOT NULL DEFAULT 'saudavel',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `animals_tutor_id_index` (`tutor_id`),
  KEY `animals_status_index` (`status`),
  CONSTRAINT `animals_tutor_id_foreign` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `animals`
--

LOCK TABLES `animals` WRITE;
/*!40000 ALTER TABLE `animals` DISABLE KEYS */;
INSERT INTO `animals` VALUES (7,4,'Belinha','Cachorro','Beagle','pequeno',3,9.50,'saudavel','2026-09-06 23:57:27','2026-09-06 23:57:27'),(8,4,'Max','Cachorro','Bulldog Francês','pequeno',5,12.00,'atencao','2026-09-06 23:57:27','2026-09-06 23:57:27'),(9,5,'Simba','Gato','Maine Coon','grande',4,7.20,'saudavel','2026-09-06 23:57:27','2026-09-06 23:57:27'),(10,6,'Amora','Cachorro','Poodle','pequeno',2,6.00,'saudavel','2026-09-06 23:57:27','2026-09-06 23:57:27'),(11,6,'Zeca','Cavalo','Quarto de Milha','grande',8,420.00,'saudavel','2026-09-06 23:57:27','2026-09-06 23:57:27'),(12,7,'Bento','Cachorro','Vira-lata','medio',5,18.00,'emergencia','2026-09-06 23:57:27','2026-09-06 23:57:27'),(14,8,'Sonia','Coelho','Pincher','medio',44,88.00,'saudavel','2026-10-06 16:34:58','2026-10-06 16:34:58'),(15,8,'Thor','Coelho','Goldem','medio',2,22.00,'saudavel','2026-10-06 16:54:04','2026-10-06 16:54:04');
/*!40000 ALTER TABLE `animals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_tokens`
--

DROP TABLE IF EXISTS `api_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tutor_id` bigint(20) unsigned NOT NULL,
  `coleira_id` bigint(20) unsigned DEFAULT NULL,
  `token_hash` varchar(64) NOT NULL COMMENT 'SHA-256 do token; o texto puro nunca é armazenado',
  `nome_dispositivo` varchar(150) DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `api_tokens_token_hash_unique` (`token_hash`),
  KEY `api_tokens_tutor_id_index` (`tutor_id`),
  KEY `api_tokens_coleira_id_foreign` (`coleira_id`),
  CONSTRAINT `api_tokens_coleira_id_foreign` FOREIGN KEY (`coleira_id`) REFERENCES `coleiras` (`id`) ON DELETE SET NULL,
  CONSTRAINT `api_tokens_tutor_id_foreign` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_tokens`
--

LOCK TABLES `api_tokens` WRITE;
/*!40000 ALTER TABLE `api_tokens` DISABLE KEYS */;
INSERT INTO `api_tokens` VALUES (2,8,13,'da6c132aabaa17c1ecd38a2a9a1c62b7bd5ef3bdac290159b661c47283ed186f','curl/8.6.0',NULL,'2027-01-04 11:39:03','2026-10-06 11:39:03','2026-10-06 11:39:03'),(6,4,7,'d2ebd941ff62623c5e5d6c62d2eaa48e939392a88973858833e717b47dfc05af','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:14:11','2027-01-04 11:59:12','2026-10-06 11:59:12','2026-10-06 12:14:11'),(10,8,15,'6938f6bbcfd36e89060e874238e1f7fb17771ac0e41c8cd11b5801e0cb1bbffc','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 17:19:04','2027-01-04 17:07:17','2026-10-06 17:07:17','2026-10-06 17:19:04');
/*!40000 ALTER TABLE `api_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coleiras`
--

DROP TABLE IF EXISTS `coleiras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coleiras` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `animal_id` bigint(20) unsigned DEFAULT NULL COMMENT 'NULL = coleira em estoque, ainda não vinculada',
  `codigo` varchar(50) NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `precisao` decimal(6,2) DEFAULT NULL COMMENT 'Precisão do GPS em metros',
  `localizacao_atualizada_em` timestamp NULL DEFAULT NULL,
  `bateria_percentual` tinyint(3) unsigned NOT NULL DEFAULT 100,
  `bateria_status` enum('normal','baixa','critica') NOT NULL DEFAULT 'normal',
  `bateria_atualizada_em` timestamp NULL DEFAULT NULL,
  `status` enum('disponivel','ativa','inativa','sem_sinal') NOT NULL DEFAULT 'disponivel',
  `ativada_em` timestamp NULL DEFAULT NULL COMMENT 'Quando o código foi vinculado a um animal',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coleiras_codigo_unique` (`codigo`),
  UNIQUE KEY `coleiras_animal_id_unique` (`animal_id`),
  KEY `coleiras_codigo_index` (`codigo`),
  KEY `coleiras_status_index` (`status`),
  KEY `coleiras_bateria_status_index` (`bateria_status`),
  CONSTRAINT `coleiras_animal_id_foreign` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coleiras`
--

LOCK TABLES `coleiras` WRITE;
/*!40000 ALTER TABLE `coleiras` DISABLE KEYS */;
INSERT INTO `coleiras` VALUES (7,7,'COL-DC9FEAAA',-23.5078000,-46.6600000,11.00,'2026-09-06 23:57:27',48,'normal','2026-09-06 23:57:27','ativa',NULL,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(8,8,'COL-69CE7CC6',-23.5995000,-46.6968000,5.00,'2026-09-06 23:57:27',54,'normal','2026-09-06 23:57:27','ativa',NULL,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(9,9,'COL-2A1FD091',-23.5187000,-46.5522000,5.00,'2026-09-06 23:57:27',89,'normal','2026-09-06 23:57:27','ativa',NULL,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(10,10,'COL-E348E3A4',-23.4910000,-46.5907000,3.00,'2026-09-06 23:57:27',63,'normal','2026-09-06 23:57:27','ativa',NULL,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(11,11,'COL-AB7F406E',-23.5320000,-46.6533000,9.00,'2026-09-06 23:57:27',97,'normal','2026-09-06 23:57:27','ativa',NULL,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(12,12,'COL-42811A8F',-23.5190000,-46.6464000,6.00,'2026-09-06 23:57:27',94,'normal','2026-09-06 23:57:27','ativa',NULL,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(13,15,'COL-DEMO-0001',-23.5504000,-46.6332800,8.00,'2026-10-06 17:15:43',100,'normal','2026-10-06 17:15:43','ativa','2026-10-06 16:54:04','2026-09-06 23:57:27','2026-10-06 17:15:43'),(14,NULL,'COL-DEMO-0002',NULL,NULL,NULL,NULL,100,'normal',NULL,'disponivel',NULL,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(15,14,'COL-DEMO-0003',-23.5501200,-46.6335100,6.00,'2026-10-06 16:46:51',100,'normal','2026-10-06 16:46:51','ativa','2026-10-06 16:34:58','2026-09-06 23:57:27','2026-10-06 16:46:51');
/*!40000 ALTER TABLE `coleiras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contatos`
--

DROP TABLE IF EXISTS `contatos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contatos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefone` varchar(255) DEFAULT NULL,
  `assunto` varchar(255) DEFAULT NULL,
  `mensagem` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contatos`
--

LOCK TABLES `contatos` WRITE;
/*!40000 ALTER TABLE `contatos` DISABLE KEYS */;
INSERT INTO `contatos` VALUES (1,'Fernanda Castro','fernanda.castro@gmail.com','(19) 99887-1122','Dúvida sobre produto','Gostaria de saber se o UniCollar Pro funciona sem sinal de internet no local.','2026-09-06 23:57:27','2026-09-06 23:57:27'),(2,'Marcelo Teixeira','marcelo.teixeira@outlook.com','(31) 98877-2233','Suporte técnico','Minha coleira não está atualizando a localização desde ontem, podem ajudar?','2026-09-06 23:57:27','2026-09-06 23:57:27'),(3,'Beatriz Andrade','bea.andrade@yahoo.com',NULL,'Parceria comercial','Sou tutora de um canil e gostaria de conversar sobre parceria para divulgação.','2026-09-06 23:57:27','2026-09-06 23:57:27'),(4,'João Vitor Vieira Da Costa','joao.v.costa89@aluno.senai.br','(34) 44444-4444','oooo','ojojojjjjjjjjjjjjjjjjjjjj','2026-10-06 16:55:24','2026-10-06 16:55:24'),(5,'João Vitor Vieira Da Costa','joao.v.costa89@aluno.senai.br','(34) 44444-4444','oooo','aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa','2026-10-06 17:00:15','2026-10-06 17:00:15'),(6,'João Vitor Vieira Da Costa','joao.v.costa89@aluno.senai.br','(34) 44444-4444','oooo','lllllllllllllllllllllllllllllllllllllllllll','2026-10-06 17:02:50','2026-10-06 17:02:50');
/*!40000 ALTER TABLE `contatos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `depoimento`
--

DROP TABLE IF EXISTS `depoimento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `depoimento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `depoimento` varchar(255) DEFAULT NULL,
  `id_animal` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_animal` (`id_animal`),
  CONSTRAINT `depoimento_ibfk_1` FOREIGN KEY (`id_animal`) REFERENCES `animal` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `depoimento`
--

LOCK TABLES `depoimento` WRITE;
/*!40000 ALTER TABLE `depoimento` DISABLE KEYS */;
/*!40000 ALTER TABLE `depoimento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `depoimentos`
--

DROP TABLE IF EXISTS `depoimentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `depoimentos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefone` varchar(255) DEFAULT NULL,
  `depoimento` text NOT NULL,
  `estrelas` int(11) NOT NULL DEFAULT 5,
  `aprovado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `depoimentos`
--

LOCK TABLES `depoimentos` WRITE;
/*!40000 ALTER TABLE `depoimentos` DISABLE KEYS */;
INSERT INTO `depoimentos` VALUES (1,'Rafael Nogueira','rafael.nogueira@petmail.com','(11) 91234-5678','A UniCollar me avisou que o Max estava com a temperatura alta antes mesmo de eu notar qualquer sintoma. Levei ao veterinário na hora.',5,1,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(2,'Juliana Marques','juliana.marques@petmail.com',NULL,'O Simba é um gato que ama fugir de casa. Com o GPS em tempo real, encontrei ele em minutos na primeira vez que sumiu.',5,1,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(3,'Eduardo Ramos','eduardo.ramos@petmail.com','(31) 98765-1234','Uso a coleira rural no Zeca há 6 meses. A bateria dura muito e o sinal chega até nas partes mais distantes do sítio.',4,1,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(4,'Patrícia Gomes','patricia.gomes@petmail.com',NULL,'Suporte muito atencioso quando tive dúvidas na instalação. Recomendo para quem tem cães mais velhos como o Bento.',5,1,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(5,'Ricardo','Ricard@g','199958897058','Amei utilizar o UniAnimal',5,1,'2026-10-06 16:55:51','2026-10-06 16:55:51'),(6,'Ricardo','Ricard@ga','199958897058','Muitooo bommmmaaaaaaaaaaaaaaaa',5,1,'2026-10-06 16:59:59','2026-10-06 16:59:59'),(7,'yasmin','yasmin@barbosa','199958897058','kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk',5,1,'2026-10-06 17:02:33','2026-10-06 17:02:33');
/*!40000 ALTER TABLE `depoimentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historico`
--

DROP TABLE IF EXISTS `historico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historico` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) DEFAULT NULL,
  `localizacao` varchar(100) DEFAULT NULL,
  `batimento_cardiaco` int(11) DEFAULT NULL,
  `animal_id` bigint(20) unsigned NOT NULL,
  `registrado_em` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `historico_animal_id_registrado_em_index` (`animal_id`,`registrado_em`),
  CONSTRAINT `historico_animal_id_foreign` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=182 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historico`
--

LOCK TABLES `historico` WRITE;
/*!40000 ALTER TABLE `historico` DISABLE KEYS */;
INSERT INTO `historico` VALUES (170,'Belinha','-23.5034, -46.6601',88,7,'2026-10-06 14:11:13','2026-10-06 16:26:13','2026-10-06 16:26:13'),(171,'Belinha','-23.5041, -46.6587',92,7,'2026-10-06 14:26:13','2026-10-06 16:26:13','2026-10-06 16:26:13'),(172,'Belinha','-23.5052, -46.6573',85,7,'2026-10-06 14:41:13','2026-10-06 16:26:13','2026-10-06 16:26:13'),(173,'Belinha','-23.5067, -46.6522',110,7,'2026-10-06 14:56:13','2026-10-06 16:26:13','2026-10-06 16:26:13'),(174,'Belinha','-23.5101, -46.6489',124,7,'2026-10-06 15:11:13','2026-10-06 16:26:13','2026-10-06 16:26:13'),(175,'Belinha','-23.5143, -46.6461',118,7,'2026-10-06 15:26:13','2026-10-06 16:26:13','2026-10-06 16:26:13'),(176,'Belinha','-23.5209, -46.6456',97,7,'2026-10-06 15:41:13','2026-10-06 16:26:13','2026-10-06 16:26:13'),(177,'Belinha','-23.5067, -46.6522',90,7,'2026-10-06 15:56:13','2026-10-06 16:26:13','2026-10-06 16:26:13'),(178,'Belinha','-23.4943, -46.6513',84,7,'2026-10-06 16:11:13','2026-10-06 16:26:13','2026-10-06 16:26:13'),(179,'Belinha','-23.5098, -46.6744',95,7,'2026-10-06 16:26:13','2026-10-06 16:26:13','2026-10-06 16:26:13'),(180,'Belinha',NULL,101,7,'2026-10-06 16:26:31','2026-10-06 16:26:31','2026-10-06 16:26:31'),(181,'Belinha',NULL,94,7,'2026-10-06 16:26:41','2026-10-06 16:26:41','2026-10-06 16:26:41');
/*!40000 ALTER TABLE `historico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `localizacoes`
--

DROP TABLE IF EXISTS `localizacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `localizacoes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `coleira_id` bigint(20) unsigned NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `precisao` decimal(6,2) DEFAULT NULL,
  `registrado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `localizacoes_coleira_id_registrado_em_index` (`coleira_id`,`registrado_em`),
  CONSTRAINT `localizacoes_coleira_id_foreign` FOREIGN KEY (`coleira_id`) REFERENCES `coleiras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `localizacoes`
--

LOCK TABLES `localizacoes` WRITE;
/*!40000 ALTER TABLE `localizacoes` DISABLE KEYS */;
INSERT INTO `localizacoes` VALUES (67,7,-23.5119000,-46.6632000,10.00,'2026-09-06 21:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(68,7,-23.5087000,-46.6474000,7.00,'2026-09-06 21:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(69,7,-23.5142000,-46.6699000,5.00,'2026-09-06 21:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(70,7,-23.5200000,-46.6515000,4.00,'2026-09-06 22:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(71,7,-23.5191000,-46.6620000,5.00,'2026-09-06 22:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(72,7,-23.4968000,-46.6606000,8.00,'2026-09-06 22:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(73,7,-23.5062000,-46.6450000,8.00,'2026-09-06 22:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(74,7,-23.5209000,-46.6456000,3.00,'2026-09-06 23:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(75,7,-23.5067000,-46.6522000,8.00,'2026-09-06 23:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(76,7,-23.4943000,-46.6513000,5.00,'2026-09-06 23:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(77,7,-23.5098000,-46.6744000,3.00,'2026-09-06 23:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(78,8,-23.6141000,-46.6914000,11.00,'2026-09-06 21:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(79,8,-23.5870000,-46.6827000,10.00,'2026-09-06 21:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(80,8,-23.5866000,-46.7118000,3.00,'2026-09-06 21:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(81,8,-23.5952000,-46.6893000,12.00,'2026-09-06 22:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(82,8,-23.6014000,-46.6999000,3.00,'2026-09-06 22:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(83,8,-23.5995000,-46.7094000,4.00,'2026-09-06 22:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(84,8,-23.6057000,-46.6970000,4.00,'2026-09-06 22:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(85,8,-23.5923000,-46.6990000,10.00,'2026-09-06 23:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(86,8,-23.5981000,-46.6874000,12.00,'2026-09-06 23:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(87,8,-23.5847000,-46.7114000,12.00,'2026-09-06 23:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(88,8,-23.5868000,-46.7058000,5.00,'2026-09-06 23:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(89,9,-23.5170000,-46.5626000,3.00,'2026-09-06 21:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(90,9,-23.5100000,-46.5642000,11.00,'2026-09-06 21:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(91,9,-23.5225000,-46.5396000,6.00,'2026-09-06 21:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(92,9,-23.5333000,-46.5577000,6.00,'2026-09-06 22:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(93,9,-23.5225000,-46.5425000,12.00,'2026-09-06 22:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(94,9,-23.5131000,-46.5575000,12.00,'2026-09-06 22:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(95,9,-23.5200000,-46.5391000,10.00,'2026-09-06 22:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(96,9,-23.5153000,-46.5429000,5.00,'2026-09-06 23:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(97,9,-23.5198000,-46.5469000,6.00,'2026-09-06 23:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(98,9,-23.5177000,-46.5432000,6.00,'2026-09-06 23:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(99,9,-23.5137000,-46.5582000,3.00,'2026-09-06 23:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(100,10,-23.4893000,-46.5912000,5.00,'2026-09-06 21:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(101,10,-23.4879000,-46.6039000,7.00,'2026-09-06 21:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(102,10,-23.4930000,-46.5884000,5.00,'2026-09-06 21:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(103,10,-23.4972000,-46.5838000,8.00,'2026-09-06 22:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(104,10,-23.4935000,-46.5879000,7.00,'2026-09-06 22:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(105,10,-23.4769000,-46.5922000,7.00,'2026-09-06 22:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(106,10,-23.4781000,-46.6016000,4.00,'2026-09-06 22:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(107,10,-23.4832000,-46.5952000,7.00,'2026-09-06 23:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(108,10,-23.4821000,-46.5985000,8.00,'2026-09-06 23:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(109,10,-23.4817000,-46.5855000,5.00,'2026-09-06 23:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(110,10,-23.4820000,-46.5865000,11.00,'2026-09-06 23:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(111,11,-23.5354000,-46.6519000,6.00,'2026-09-06 21:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(112,11,-23.5467000,-46.6606000,12.00,'2026-09-06 21:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(113,11,-23.5391000,-46.6441000,12.00,'2026-09-06 21:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(114,11,-23.5236000,-46.6636000,10.00,'2026-09-06 22:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(115,11,-23.5307000,-46.6642000,6.00,'2026-09-06 22:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(116,11,-23.5417000,-46.6562000,8.00,'2026-09-06 22:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(117,11,-23.5313000,-46.6398000,9.00,'2026-09-06 22:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(118,11,-23.5270000,-46.6424000,5.00,'2026-09-06 23:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(119,11,-23.5268000,-46.6414000,5.00,'2026-09-06 23:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(120,11,-23.5186000,-46.6392000,7.00,'2026-09-06 23:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(121,11,-23.5376000,-46.6453000,5.00,'2026-09-06 23:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(122,12,-23.5189000,-46.6384000,4.00,'2026-09-06 21:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(123,12,-23.5087000,-46.6444000,3.00,'2026-09-06 21:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(124,12,-23.5238000,-46.6567000,6.00,'2026-09-06 21:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(125,12,-23.5224000,-46.6514000,8.00,'2026-09-06 22:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(126,12,-23.5324000,-46.6344000,8.00,'2026-09-06 22:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(127,12,-23.5179000,-46.6509000,9.00,'2026-09-06 22:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(128,12,-23.5115000,-46.6457000,12.00,'2026-09-06 22:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(129,12,-23.5149000,-46.6329000,9.00,'2026-09-06 23:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(130,12,-23.5225000,-46.6349000,3.00,'2026-09-06 23:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(131,12,-23.5172000,-46.6563000,7.00,'2026-09-06 23:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(132,12,-23.5330000,-46.6460000,12.00,'2026-09-06 23:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(133,15,-23.5501200,-46.6335100,6.00,'2026-10-06 16:46:51','2026-10-06 16:46:51','2026-10-06 16:46:51'),(134,13,-23.5504000,-46.6332800,8.00,'2026-10-06 17:15:43','2026-10-06 17:15:43','2026-10-06 17:15:43');
/*!40000 ALTER TABLE `localizacoes` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2024_01_03_000001_vincular_historico_ao_animal',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orcamentos`
--

DROP TABLE IF EXISTS `orcamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orcamentos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefone` varchar(255) NOT NULL,
  `porte` varchar(255) NOT NULL DEFAULT 'medio',
  `quantidade` int(11) NOT NULL DEFAULT 1,
  `observacoes` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pendente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orcamentos`
--

LOCK TABLES `orcamentos` WRITE;
/*!40000 ALTER TABLE `orcamentos` DISABLE KEYS */;
INSERT INTO `orcamentos` VALUES (1,'Clínica Veterinária Vida Animal','compras@vidaanimalvet.com.br','(19) 3322-4455','medio',25,'Interesse em coleiras para monitoramento de pacientes internados.','pendente','2026-09-06 23:57:27','2026-09-06 23:57:27'),(2,'Pet Shop Amigo Fiel','contato@amigofiel.com.br','(11) 4455-6677','pequeno',40,'Revenda para clientes da loja, gostaríamos de condições especiais.','aprovado','2026-09-06 23:57:27','2026-09-06 23:57:27'),(3,'Fazenda Santa Rita','administrativo@santarita.agr.br','(16) 3211-0099','grande',60,'Monitoramento de rebanho equino e bovino em área rural extensa.','pendente','2026-09-06 23:57:27','2026-09-06 23:57:27');
/*!40000 ALTER TABLE `orcamentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produtos`
--

DROP TABLE IF EXISTS `produtos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produtos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `porte` varchar(255) NOT NULL,
  `descricao` text NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `preco_promocional` decimal(10,2) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produtos`
--

LOCK TABLES `produtos` WRITE;
/*!40000 ALTER TABLE `produtos` DISABLE KEYS */;
INSERT INTO `produtos` VALUES (1,'UniCollar Mini','pequeno','Coleira inteligente para pets de pequeno porte, com GPS, ECG e sensor de temperatura.',799.00,NULL,NULL,1,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(2,'UniCollar Pro','medio','Nosso modelo mais vendido: GPS, ECG, monitor de sono e alertas com IA.',879.00,799.00,NULL,1,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(3,'UniCollar Max','grande','Para animais de grande porte e uso rural, com alcance de GPS de até 5km.',1099.00,NULL,NULL,1,'2026-09-06 23:57:27','2026-09-06 23:57:27');
/*!40000 ALTER TABLE `produtos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tutors`
--

DROP TABLE IF EXISTS `tutors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tutors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `nome` varchar(150) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `email` varchar(200) NOT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tutors_user_id_unique` (`user_id`),
  UNIQUE KEY `tutors_email_unique` (`email`),
  KEY `tutors_nome_index` (`nome`),
  CONSTRAINT `tutors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tutors`
--

LOCK TABLES `tutors` WRITE;
/*!40000 ALTER TABLE `tutors` DISABLE KEYS */;
INSERT INTO `tutors` VALUES (4,4,'Rafael Nogueira','(11) 91234-5678','rafael.nogueira@petmail.com','São Paulo, SP','2026-09-06 23:57:27','2026-09-06 23:57:27'),(5,5,'Juliana Marques','(21) 99876-5432','juliana.marques@petmail.com','Rio de Janeiro, RJ','2026-09-06 23:57:27','2026-09-06 23:57:27'),(6,6,'Eduardo Ramos','(31) 98765-1234','eduardo.ramos@petmail.com','Belo Horizonte, MG','2026-09-06 23:57:27','2026-09-06 23:57:27'),(7,7,'Patrícia Gomes','(41) 99222-3344','patricia.gomes@petmail.com','Curitiba, PR','2026-09-06 23:57:27','2026-09-06 23:57:27'),(8,8,'João Vitor Vieira Da Costa','','joooooo@gmail.com',NULL,'2026-10-06 16:34:28','2026-10-06 16:34:28');
/*!40000 ALTER TABLE `tutors` ENABLE KEYS */;
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
  `google_id` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_google_id_unique` (`google_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (4,'Rafael Nogueira','rafael.nogueira@petmail.com',NULL,NULL,NULL,'$2y$12$wpFBbDf08w7MgFUB2o58BeyEE9tsZB6YnWCUFZZz8AyamMLXAXEs6',NULL,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(5,'Juliana Marques','juliana.marques@petmail.com',NULL,NULL,NULL,'$2y$12$F/lSZkf43ukW8AnHUUyttuIM7DxUQwuSai0gUhai0.QVAt9c8z.vu',NULL,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(6,'Eduardo Ramos','eduardo.ramos@petmail.com',NULL,NULL,NULL,'$2y$12$zPJvR46Gg5iD153lbviPLeHWmoW3pwnEQQKq39xS3rsoJtLc9/50i',NULL,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(7,'Patrícia Gomes','patricia.gomes@petmail.com',NULL,NULL,NULL,'$2y$12$pk/GSvXQDA1NBLuoISWxoO89c/q0yO1RacQseBhIS/uOyisud2dtu',NULL,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(8,'João Vitor Vieira Da Costa','joooooo@gmail.com',NULL,NULL,NULL,'$2y$12$sBfzbgT1s9lM65PpAWpLQ.AFv4Z/ASlgZuINt/AGaDyuMUk7l1//S',NULL,'2026-10-06 16:34:28','2026-10-06 16:34:28');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `senha` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'unianimal'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-06 14:19:09
