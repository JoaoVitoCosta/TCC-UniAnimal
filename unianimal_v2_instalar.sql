-- ══════════════════════════════════════════════════════════════
--  UniAnimal — INSTALAÇÃO COMPLETA DO BANCO (unianimal_v2)
--  MariaDB 10.4+ (XAMPP) / MySQL 8 · utf8mb4_unicode_ci · 15 tabelas
--
--  Este arquivo CRIA o banco sozinho. Não precisa criar nada antes.
--    • phpMyAdmin: aba "Importar" (sem selecionar banco) → escolher este arquivo → Executar
--    • Terminal:   C:\xampp\mysql\bin\mysql.exe -uroot < database\unianimal_v2_instalar.sql
--  Depois, no .env do Laravel:  DB_DATABASE=unianimal_v2
--
--  Pode importar quantas vezes quiser: as tabelas são recriadas
--  (DROP TABLE IF EXISTS) e voltam a este estado inicial.
--
--  Logins de demonstração
--    Site: rafael.nogueira@petmail.com / password  (Juliana, Eduardo e Patrícia: mesma senha)
--    App:  código da coleira, ex.: COL-DC9FEAAA (Belinha) · COL-42811A8F (Bento)
--    Coleiras em estoque para testar cadastro: COL-DEMO-0004, COL-DEMO-0005
--    Chaves do hardware (API IoT): storage/app/iot/chaves_coleiras.csv
-- ══════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS `unianimal_v2` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `unianimal_v2`;

-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: unianimal_iot
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
-- Table structure for table `alertas`
--

DROP TABLE IF EXISTS `alertas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alertas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `animal_id` bigint(20) unsigned NOT NULL,
  `coleira_id` bigint(20) unsigned DEFAULT NULL,
  `tipo` enum('batimento','temperatura','bateria','sem_sinal','zona_segura') NOT NULL,
  `nivel` enum('alerta','emergencia') NOT NULL,
  `titulo` varchar(120) NOT NULL,
  `mensagem` varchar(255) NOT NULL,
  `valor` varchar(40) DEFAULT NULL COMMENT 'Valor que disparou o alerta, ex.: "182 bpm"',
  `lido_em` timestamp NULL DEFAULT NULL COMMENT 'Tutor viu o alerta',
  `resolvido_em` timestamp NULL DEFAULT NULL COMMENT 'Situação voltou ao normal',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alertas_coleira_id_foreign` (`coleira_id`),
  KEY `alertas_animal_id_resolvido_em_index` (`animal_id`,`resolvido_em`),
  KEY `alertas_animal_id_created_at_index` (`animal_id`,`created_at`),
  CONSTRAINT `alertas_animal_id_foreign` FOREIGN KEY (`animal_id`) REFERENCES `animais` (`id`) ON DELETE CASCADE,
  CONSTRAINT `alertas_coleira_id_foreign` FOREIGN KEY (`coleira_id`) REFERENCES `coleiras` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alertas`
--

LOCK TABLES `alertas` WRITE;
/*!40000 ALTER TABLE `alertas` DISABLE KEYS */;
INSERT INTO `alertas` VALUES (1,8,8,'bateria','alerta','Bateria baixa','A coleira de Max está com 28% de bateria. Recarregue para não perder o monitoramento.','28%',NULL,NULL,'2026-10-06 18:21:36','2026-10-06 18:21:36'),(2,12,12,'batimento','emergencia','Batimento acima do normal','Bento está com 162 bpm (normal: 70–120 bpm).','162 bpm',NULL,NULL,'2026-10-06 18:21:36','2026-10-06 18:21:36');
/*!40000 ALTER TABLE `alertas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `animais`
--

DROP TABLE IF EXISTS `animais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `animais` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tutor_id` bigint(20) unsigned NOT NULL,
  `nome` varchar(100) NOT NULL,
  `especie` varchar(60) NOT NULL COMMENT 'Cachorro, Gato, Coelho, Cavalo, Bovino ou Outro',
  `raca` varchar(100) DEFAULT NULL,
  `porte` enum('pequeno','medio','grande') NOT NULL,
  `idade` tinyint(3) unsigned DEFAULT NULL COMMENT 'Anos',
  `peso` decimal(6,2) DEFAULT NULL COMMENT 'Kg',
  `status` enum('saudavel','atencao','emergencia') NOT NULL DEFAULT 'saudavel',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `animais_tutor_id_foreign` (`tutor_id`),
  KEY `animais_status_index` (`status`),
  CONSTRAINT `animais_tutor_id_foreign` FOREIGN KEY (`tutor_id`) REFERENCES `tutores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `animais`
--

LOCK TABLES `animais` WRITE;
/*!40000 ALTER TABLE `animais` DISABLE KEYS */;
INSERT INTO `animais` VALUES (7,4,'Belinha','Cachorro','Beagle','pequeno',3,9.50,'saudavel','2026-09-06 23:57:27','2026-09-06 23:57:27'),(8,4,'Max','Cachorro','Bulldog Francês','pequeno',5,12.00,'atencao','2026-09-06 23:57:27','2026-09-06 23:57:27'),(9,5,'Simba','Gato','Maine Coon','grande',4,7.20,'saudavel','2026-09-06 23:57:27','2026-09-06 23:57:27'),(10,6,'Amora','Cachorro','Poodle','pequeno',2,6.00,'saudavel','2026-09-06 23:57:27','2026-09-06 23:57:27'),(11,6,'Zeca','Cavalo','Quarto de Milha','grande',8,420.00,'saudavel','2026-09-06 23:57:27','2026-09-06 23:57:27'),(12,7,'Bento','Cachorro','Vira-lata','medio',5,18.00,'emergencia','2026-09-06 23:57:27','2026-09-06 23:57:27'),(14,8,'Sonia','Coelho','Pincher','medio',44,88.00,'saudavel','2026-10-06 16:34:58','2026-10-06 16:34:58'),(15,8,'Thor','Coelho','Goldem','medio',2,22.00,'saudavel','2026-10-06 16:54:04','2026-10-06 16:54:04'),(18,10,'João Vitor Vieira Da Costa','Coelho','Albino','pequeno',2,2.00,'saudavel','2026-10-06 18:07:11','2026-10-06 18:07:11');
/*!40000 ALTER TABLE `animais` ENABLE KEYS */;
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
  `coleira_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Coleira usada no login: o token só enxerga o animal dela',
  `token_hash` varchar(64) NOT NULL COMMENT 'SHA-256; o texto puro nunca é armazenado',
  `nome_dispositivo` varchar(150) DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `api_tokens_token_hash_unique` (`token_hash`),
  KEY `api_tokens_tutor_id_foreign` (`tutor_id`),
  KEY `api_tokens_coleira_id_foreign` (`coleira_id`),
  CONSTRAINT `api_tokens_coleira_id_foreign` FOREIGN KEY (`coleira_id`) REFERENCES `coleiras` (`id`) ON DELETE SET NULL,
  CONSTRAINT `api_tokens_tutor_id_foreign` FOREIGN KEY (`tutor_id`) REFERENCES `tutores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_tokens`
--

LOCK TABLES `api_tokens` WRITE;
/*!40000 ALTER TABLE `api_tokens` DISABLE KEYS */;
INSERT INTO `api_tokens` VALUES (2,8,13,'da6c132aabaa17c1ecd38a2a9a1c62b7bd5ef3bdac290159b661c47283ed186f','curl/8.6.0',NULL,'2027-01-04 11:39:03','2026-10-06 11:39:03','2026-10-06 11:39:03'),(6,4,7,'d2ebd941ff62623c5e5d6c62d2eaa48e939392a88973858833e717b47dfc05af','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:14:11','2027-01-04 11:59:12','2026-10-06 11:59:12','2026-10-06 12:14:11'),(10,8,15,'6938f6bbcfd36e89060e874238e1f7fb17771ac0e41c8cd11b5801e0cb1bbffc','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 17:40:56','2027-01-04 17:07:17','2026-10-06 17:07:17','2026-10-06 17:40:56'),(13,10,14,'49617b5969bfb70d0da990ba3ad78b0e475b7975900f4cb74d99328988b1016f','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 18:15:29','2027-01-04 18:07:28','2026-10-06 18:07:28','2026-10-06 18:15:29');
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
  `chave_hash` varchar(64) DEFAULT NULL,
  `firmware_versao` varchar(30) DEFAULT NULL,
  `intervalo_envio_seg` smallint(5) unsigned NOT NULL DEFAULT 60 COMMENT 'De quantos em quantos segundos a coleira deve enviar telemetria',
  `ultimo_contato_em` timestamp NULL DEFAULT NULL COMMENT 'Última telemetria recebida',
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
  UNIQUE KEY `coleiras_chave_hash_unique` (`chave_hash`),
  KEY `coleiras_status_index` (`status`),
  KEY `coleiras_ultimo_contato_em_index` (`ultimo_contato_em`),
  CONSTRAINT `coleiras_animal_id_foreign` FOREIGN KEY (`animal_id`) REFERENCES `animais` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coleiras`
--

LOCK TABLES `coleiras` WRITE;
/*!40000 ALTER TABLE `coleiras` DISABLE KEYS */;
INSERT INTO `coleiras` VALUES (7,7,'COL-DC9FEAAA','8e8529fca4949e480d0d26a8bfa7e69e06b1d1c1384b55e92f634adcc4027027',NULL,60,'2026-10-06 16:26:41',-23.5078000,-46.6600000,11.00,'2026-09-06 23:57:27',48,'normal','2026-09-06 23:57:27','ativa','2026-09-06 23:57:27','2026-09-06 23:57:27','2026-10-06 18:21:36'),(8,8,'COL-69CE7CC6','b32b9503cc97b66aac0e55a64f185b9324c9cceb7e388ab8211b529098da95ab',NULL,60,'2026-10-06 18:21:36',-23.5995000,-46.6968000,5.00,'2026-09-06 23:57:27',28,'baixa','2026-10-06 18:21:36','ativa','2026-09-06 23:57:27','2026-09-06 23:57:27','2026-10-06 18:21:36'),(9,9,'COL-2A1FD091','41a412f09e97050ba8fc525307e9f1360a78cb019e27c3af27e78fbed3e3e151',NULL,60,'2026-10-06 17:24:52',-23.5187000,-46.5522000,5.00,'2026-09-06 23:57:27',89,'normal','2026-09-06 23:57:27','ativa','2026-09-06 23:57:27','2026-09-06 23:57:27','2026-10-06 18:21:36'),(10,10,'COL-E348E3A4','3875b9cf156fa92067ee4a078ca4eacd24024b8c3bd93557da214b71df77b70d',NULL,60,'2026-10-06 17:24:52',-23.4910000,-46.5907000,3.00,'2026-09-06 23:57:27',63,'normal','2026-09-06 23:57:27','ativa','2026-09-06 23:57:27','2026-09-06 23:57:27','2026-10-06 18:21:36'),(11,11,'COL-AB7F406E','7d28a963522b3881032ace30d3ae952fa4aa4693bb33caac2ddf1ff59fd10635',NULL,60,'2026-10-06 17:24:52',-23.5320000,-46.6533000,9.00,'2026-09-06 23:57:27',97,'normal','2026-09-06 23:57:27','ativa','2026-09-06 23:57:27','2026-09-06 23:57:27','2026-10-06 18:21:36'),(12,12,'COL-42811A8F','daa35e8dc355dce09b3aede5c285aa50255c2961395999b09f22c724387078ab',NULL,60,'2026-10-06 18:21:36',-23.5190000,-46.6464000,6.00,'2026-09-06 23:57:27',94,'normal','2026-09-06 23:57:27','ativa','2026-09-06 23:57:27','2026-09-06 23:57:27','2026-10-06 18:21:36'),(13,15,'COL-DEMO-0001','2a1c986552a3dae2e39b40c66a0901a4d7b3d13202106e752447dd858d8052e8',NULL,60,'2026-10-06 17:24:52',-23.5504000,-46.6332800,8.00,'2026-10-06 17:15:43',100,'normal','2026-10-06 17:15:43','ativa','2026-10-06 16:54:04','2026-09-06 23:57:27','2026-10-06 18:21:36'),(14,18,'COL-DEMO-0002','bffb16b1e1438c9f7c45a328595ed501698a60670fc353e75bfaaf82372ff5df',NULL,60,'2026-10-06 18:21:36',-21.7056800,-47.2731600,4.00,'2026-10-06 18:21:36',100,'normal',NULL,'ativa','2026-10-06 18:07:11','2026-09-06 23:57:27','2026-10-06 18:21:36'),(15,14,'COL-DEMO-0003','e3efda1f165cbb4bb922df4c35951baa3ed886f9b5343190537ed6b5486b0086',NULL,60,'2026-10-06 17:24:52',-23.5501200,-46.6335100,6.00,'2026-10-06 16:46:51',100,'normal','2026-10-06 16:46:51','ativa','2026-10-06 16:34:58','2026-09-06 23:57:27','2026-10-06 18:21:36'),(16,NULL,'COL-DEMO-0004','86e8d36cf5cf76608a366a80f291a77fdf3cd2358a24940bab17f280e06ab077',NULL,60,NULL,NULL,NULL,NULL,NULL,100,'normal',NULL,'disponivel',NULL,'2026-10-06 17:24:52','2026-10-06 18:21:36'),(17,NULL,'COL-DEMO-0005','fdaae88dd8cc64d5c64fd46007d185ee0ff4595e7c292f98ae87881fe2cf2545',NULL,60,NULL,NULL,NULL,NULL,NULL,100,'normal',NULL,'disponivel',NULL,'2026-10-06 17:24:52','2026-10-06 18:21:36');
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
  `nome` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `assunto` varchar(150) DEFAULT NULL,
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
-- Table structure for table `depoimentos`
--

DROP TABLE IF EXISTS `depoimentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `depoimentos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `depoimento` text NOT NULL,
  `estrelas` tinyint(3) unsigned NOT NULL DEFAULT 5 COMMENT '1 a 5',
  `aprovado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `depoimentos_aprovado_created_at_index` (`aprovado`,`created_at`)
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
-- Table structure for table `leituras_saude`
--

DROP TABLE IF EXISTS `leituras_saude`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leituras_saude` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `animal_id` bigint(20) unsigned NOT NULL,
  `coleira_id` bigint(20) unsigned DEFAULT NULL,
  `batimento_cardiaco` smallint(5) unsigned DEFAULT NULL COMMENT 'bpm',
  `temperatura` decimal(4,1) DEFAULT NULL COMMENT '°C',
  `registrado_em` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Momento da medição na coleira',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leituras_saude_coleira_id_foreign` (`coleira_id`),
  KEY `leituras_saude_animal_id_registrado_em_index` (`animal_id`,`registrado_em`),
  CONSTRAINT `leituras_saude_animal_id_foreign` FOREIGN KEY (`animal_id`) REFERENCES `animais` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leituras_saude_coleira_id_foreign` FOREIGN KEY (`coleira_id`) REFERENCES `coleiras` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=279 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leituras_saude`
--

LOCK TABLES `leituras_saude` WRITE;
/*!40000 ALTER TABLE `leituras_saude` DISABLE KEYS */;
INSERT INTO `leituras_saude` VALUES (170,7,7,88,38.1,'2026-10-06 14:11:13','2026-10-06 16:26:13','2026-10-06 18:21:36'),(171,7,7,92,38.1,'2026-10-06 14:26:13','2026-10-06 16:26:13','2026-10-06 18:21:36'),(172,7,7,85,38.2,'2026-10-06 14:41:13','2026-10-06 16:26:13','2026-10-06 18:21:36'),(173,7,7,110,38.4,'2026-10-06 14:56:13','2026-10-06 16:26:13','2026-10-06 18:21:36'),(174,7,7,124,38.2,'2026-10-06 15:11:13','2026-10-06 16:26:13','2026-10-06 18:21:36'),(175,7,7,118,38.2,'2026-10-06 15:26:13','2026-10-06 16:26:13','2026-10-06 18:21:36'),(176,7,7,97,38.1,'2026-10-06 15:41:13','2026-10-06 16:26:13','2026-10-06 18:21:36'),(177,7,7,90,38.2,'2026-10-06 15:56:13','2026-10-06 16:26:13','2026-10-06 18:21:36'),(178,7,7,84,38.3,'2026-10-06 16:11:13','2026-10-06 16:26:13','2026-10-06 18:21:36'),(179,7,7,95,38.3,'2026-10-06 16:26:13','2026-10-06 16:26:13','2026-10-06 18:21:36'),(180,7,7,101,38.4,'2026-10-06 16:26:31','2026-10-06 16:26:31','2026-10-06 18:21:36'),(181,7,7,94,38.4,'2026-10-06 16:26:41','2026-10-06 16:26:41','2026-10-06 18:21:36'),(182,8,8,107,39.0,'2026-10-06 14:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(183,8,8,106,38.8,'2026-10-06 14:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(184,8,8,109,39.0,'2026-10-06 15:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(185,8,8,108,38.9,'2026-10-06 15:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(186,8,8,113,38.7,'2026-10-06 15:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(187,8,8,112,38.5,'2026-10-06 15:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(188,8,8,112,38.5,'2026-10-06 16:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(189,8,8,114,38.6,'2026-10-06 16:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(190,8,8,118,38.6,'2026-10-06 16:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(191,8,8,118,38.7,'2026-10-06 16:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(192,8,8,115,38.6,'2026-10-06 17:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(193,8,8,112,38.6,'2026-10-06 17:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(194,9,9,174,38.3,'2026-10-06 14:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(195,9,9,168,38.1,'2026-10-06 14:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(196,9,9,170,38.1,'2026-10-06 15:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(197,9,9,172,38.3,'2026-10-06 15:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(198,9,9,175,38.5,'2026-10-06 15:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(199,9,9,178,38.6,'2026-10-06 15:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(200,9,9,179,38.7,'2026-10-06 16:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(201,9,9,178,38.6,'2026-10-06 16:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(202,9,9,178,38.7,'2026-10-06 16:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(203,9,9,175,38.7,'2026-10-06 16:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(204,9,9,173,38.9,'2026-10-06 17:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(205,9,9,177,38.8,'2026-10-06 17:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(206,10,10,106,38.3,'2026-10-06 14:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(207,10,10,110,38.2,'2026-10-06 14:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(208,10,10,111,38.3,'2026-10-06 15:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(209,10,10,110,38.2,'2026-10-06 15:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(210,10,10,112,38.3,'2026-10-06 15:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(211,10,10,117,38.1,'2026-10-06 15:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(212,10,10,122,38.3,'2026-10-06 16:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(213,10,10,124,38.2,'2026-10-06 16:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(214,10,10,128,38.2,'2026-10-06 16:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(215,10,10,130,38.0,'2026-10-06 16:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(216,10,10,131,38.2,'2026-10-06 17:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(217,10,10,128,38.3,'2026-10-06 17:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(218,11,11,38,38.2,'2026-10-06 14:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(219,11,11,40,38.3,'2026-10-06 14:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(220,11,11,40,38.3,'2026-10-06 15:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(221,11,11,39,38.3,'2026-10-06 15:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(222,11,11,37,38.2,'2026-10-06 15:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(223,11,11,39,38.3,'2026-10-06 15:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(224,11,11,41,38.1,'2026-10-06 16:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(225,11,11,43,38.1,'2026-10-06 16:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(226,11,11,42,38.0,'2026-10-06 16:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(227,11,11,43,38.0,'2026-10-06 16:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(228,11,11,43,37.8,'2026-10-06 17:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(229,11,11,44,38.0,'2026-10-06 17:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(230,12,12,94,38.7,'2026-10-06 14:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(231,12,12,92,38.6,'2026-10-06 14:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(232,12,12,91,38.4,'2026-10-06 15:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(233,12,12,93,38.3,'2026-10-06 15:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(234,12,12,94,38.4,'2026-10-06 15:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(235,12,12,92,38.5,'2026-10-06 15:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(236,12,12,89,38.5,'2026-10-06 16:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(237,12,12,86,38.3,'2026-10-06 16:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(238,12,12,90,38.3,'2026-10-06 16:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(239,12,12,93,38.3,'2026-10-06 16:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(240,12,12,91,38.5,'2026-10-06 17:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(241,12,12,91,38.4,'2026-10-06 17:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(242,15,13,229,39.2,'2026-10-06 14:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(243,15,13,221,39.3,'2026-10-06 14:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(244,15,13,214,39.3,'2026-10-06 15:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(245,15,13,198,39.3,'2026-10-06 15:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(246,15,13,188,39.1,'2026-10-06 15:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(247,15,13,183,39.3,'2026-10-06 15:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(248,15,13,177,39.3,'2026-10-06 16:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(249,15,13,182,39.2,'2026-10-06 16:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(250,15,13,181,39.4,'2026-10-06 16:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(251,15,13,173,39.6,'2026-10-06 16:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(252,15,13,162,39.5,'2026-10-06 17:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(253,15,13,175,39.3,'2026-10-06 17:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(254,14,15,237,39.5,'2026-10-06 14:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(255,14,15,252,39.5,'2026-10-06 14:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(256,14,15,248,39.3,'2026-10-06 15:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(257,14,15,254,39.5,'2026-10-06 15:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(258,14,15,269,39.5,'2026-10-06 15:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(259,14,15,263,39.5,'2026-10-06 15:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(260,14,15,247,39.4,'2026-10-06 16:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(261,14,15,262,39.5,'2026-10-06 16:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(262,14,15,264,39.5,'2026-10-06 16:39:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(263,14,15,280,39.3,'2026-10-06 16:54:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(264,14,15,296,39.5,'2026-10-06 17:09:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(265,14,15,289,39.4,'2026-10-06 17:24:52','2026-10-06 17:24:52','2026-10-06 18:21:36'),(266,12,12,162,38.2,'2026-10-06 18:21:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(267,18,14,238,39.1,'2026-10-06 15:36:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(268,18,14,247,39.1,'2026-10-06 15:51:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(269,18,14,246,39.3,'2026-10-06 16:06:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(270,18,14,261,39.3,'2026-10-06 16:21:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(271,18,14,271,39.2,'2026-10-06 16:36:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(272,18,14,278,39.1,'2026-10-06 16:51:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(273,18,14,280,38.9,'2026-10-06 17:06:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(274,18,14,266,38.8,'2026-10-06 17:21:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(275,18,14,259,38.7,'2026-10-06 17:36:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(276,18,14,275,38.9,'2026-10-06 17:51:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(277,18,14,274,38.9,'2026-10-06 18:06:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(278,18,14,289,38.8,'2026-10-06 18:21:36','2026-10-06 18:21:36','2026-10-06 18:21:36');
/*!40000 ALTER TABLE `leituras_saude` ENABLE KEYS */;
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
  `precisao` decimal(6,2) DEFAULT NULL COMMENT 'Metros',
  `registrado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `localizacoes_coleira_id_registrado_em_index` (`coleira_id`,`registrado_em`),
  CONSTRAINT `localizacoes_coleira_id_foreign` FOREIGN KEY (`coleira_id`) REFERENCES `coleiras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `localizacoes`
--

LOCK TABLES `localizacoes` WRITE;
/*!40000 ALTER TABLE `localizacoes` DISABLE KEYS */;
INSERT INTO `localizacoes` VALUES (67,7,-23.5119000,-46.6632000,10.00,'2026-09-06 21:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(68,7,-23.5087000,-46.6474000,7.00,'2026-09-06 21:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(69,7,-23.5142000,-46.6699000,5.00,'2026-09-06 21:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(70,7,-23.5200000,-46.6515000,4.00,'2026-09-06 22:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(71,7,-23.5191000,-46.6620000,5.00,'2026-09-06 22:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(72,7,-23.4968000,-46.6606000,8.00,'2026-09-06 22:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(73,7,-23.5062000,-46.6450000,8.00,'2026-09-06 22:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(74,7,-23.5209000,-46.6456000,3.00,'2026-09-06 23:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(75,7,-23.5067000,-46.6522000,8.00,'2026-09-06 23:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(76,7,-23.4943000,-46.6513000,5.00,'2026-09-06 23:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(77,7,-23.5098000,-46.6744000,3.00,'2026-09-06 23:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(78,8,-23.6141000,-46.6914000,11.00,'2026-09-06 21:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(79,8,-23.5870000,-46.6827000,10.00,'2026-09-06 21:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(80,8,-23.5866000,-46.7118000,3.00,'2026-09-06 21:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(81,8,-23.5952000,-46.6893000,12.00,'2026-09-06 22:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(82,8,-23.6014000,-46.6999000,3.00,'2026-09-06 22:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(83,8,-23.5995000,-46.7094000,4.00,'2026-09-06 22:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(84,8,-23.6057000,-46.6970000,4.00,'2026-09-06 22:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(85,8,-23.5923000,-46.6990000,10.00,'2026-09-06 23:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(86,8,-23.5981000,-46.6874000,12.00,'2026-09-06 23:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(87,8,-23.5847000,-46.7114000,12.00,'2026-09-06 23:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(88,8,-23.5868000,-46.7058000,5.00,'2026-09-06 23:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(89,9,-23.5170000,-46.5626000,3.00,'2026-09-06 21:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(90,9,-23.5100000,-46.5642000,11.00,'2026-09-06 21:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(91,9,-23.5225000,-46.5396000,6.00,'2026-09-06 21:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(92,9,-23.5333000,-46.5577000,6.00,'2026-09-06 22:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(93,9,-23.5225000,-46.5425000,12.00,'2026-09-06 22:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(94,9,-23.5131000,-46.5575000,12.00,'2026-09-06 22:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(95,9,-23.5200000,-46.5391000,10.00,'2026-09-06 22:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(96,9,-23.5153000,-46.5429000,5.00,'2026-09-06 23:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(97,9,-23.5198000,-46.5469000,6.00,'2026-09-06 23:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(98,9,-23.5177000,-46.5432000,6.00,'2026-09-06 23:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(99,9,-23.5137000,-46.5582000,3.00,'2026-09-06 23:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(100,10,-23.4893000,-46.5912000,5.00,'2026-09-06 21:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(101,10,-23.4879000,-46.6039000,7.00,'2026-09-06 21:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(102,10,-23.4930000,-46.5884000,5.00,'2026-09-06 21:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(103,10,-23.4972000,-46.5838000,8.00,'2026-09-06 22:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(104,10,-23.4935000,-46.5879000,7.00,'2026-09-06 22:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(105,10,-23.4769000,-46.5922000,7.00,'2026-09-06 22:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(106,10,-23.4781000,-46.6016000,4.00,'2026-09-06 22:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(107,10,-23.4832000,-46.5952000,7.00,'2026-09-06 23:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(108,10,-23.4821000,-46.5985000,8.00,'2026-09-06 23:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(109,10,-23.4817000,-46.5855000,5.00,'2026-09-06 23:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(110,10,-23.4820000,-46.5865000,11.00,'2026-09-06 23:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(111,11,-23.5354000,-46.6519000,6.00,'2026-09-06 21:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(112,11,-23.5467000,-46.6606000,12.00,'2026-09-06 21:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(113,11,-23.5391000,-46.6441000,12.00,'2026-09-06 21:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(114,11,-23.5236000,-46.6636000,10.00,'2026-09-06 22:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(115,11,-23.5307000,-46.6642000,6.00,'2026-09-06 22:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(116,11,-23.5417000,-46.6562000,8.00,'2026-09-06 22:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(117,11,-23.5313000,-46.6398000,9.00,'2026-09-06 22:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(118,11,-23.5270000,-46.6424000,5.00,'2026-09-06 23:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(119,11,-23.5268000,-46.6414000,5.00,'2026-09-06 23:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(120,11,-23.5186000,-46.6392000,7.00,'2026-09-06 23:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(121,11,-23.5376000,-46.6453000,5.00,'2026-09-06 23:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(122,12,-23.5189000,-46.6384000,4.00,'2026-09-06 21:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(123,12,-23.5087000,-46.6444000,3.00,'2026-09-06 21:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(124,12,-23.5238000,-46.6567000,6.00,'2026-09-06 21:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(125,12,-23.5224000,-46.6514000,8.00,'2026-09-06 22:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(126,12,-23.5324000,-46.6344000,8.00,'2026-09-06 22:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(127,12,-23.5179000,-46.6509000,9.00,'2026-09-06 22:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(128,12,-23.5115000,-46.6457000,12.00,'2026-09-06 22:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(129,12,-23.5149000,-46.6329000,9.00,'2026-09-06 23:12:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(130,12,-23.5225000,-46.6349000,3.00,'2026-09-06 23:27:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(131,12,-23.5172000,-46.6563000,7.00,'2026-09-06 23:42:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(132,12,-23.5330000,-46.6460000,12.00,'2026-09-06 23:57:27','2026-09-06 23:57:27','2026-09-06 23:57:27'),(133,15,-23.5501200,-46.6335100,6.00,'2026-10-06 16:46:51','2026-10-06 16:46:51','2026-10-06 16:46:51'),(134,13,-23.5504000,-46.6332800,8.00,'2026-10-06 17:15:43','2026-10-06 17:15:43','2026-10-06 17:15:43'),(135,13,-23.5509100,-46.6332700,10.00,'2026-10-06 17:00:43','2026-10-06 17:24:52','2026-10-06 17:24:52'),(136,13,-23.5517500,-46.6332600,6.00,'2026-10-06 16:45:43','2026-10-06 17:24:52','2026-10-06 17:24:52'),(137,13,-23.5527200,-46.6341700,12.00,'2026-10-06 16:30:43','2026-10-06 17:24:52','2026-10-06 17:24:52'),(138,13,-23.5533900,-46.6336600,4.00,'2026-10-06 16:15:43','2026-10-06 17:24:52','2026-10-06 17:24:52'),(139,13,-23.5524200,-46.6344300,8.00,'2026-10-06 16:00:43','2026-10-06 17:24:52','2026-10-06 17:24:52'),(140,13,-23.5529100,-46.6335900,4.00,'2026-10-06 15:45:43','2026-10-06 17:24:52','2026-10-06 17:24:52'),(141,13,-23.5536100,-46.6327000,6.00,'2026-10-06 15:30:43','2026-10-06 17:24:52','2026-10-06 17:24:52'),(142,13,-23.5532800,-46.6331300,5.00,'2026-10-06 15:15:43','2026-10-06 17:24:52','2026-10-06 17:24:52'),(143,13,-23.5538600,-46.6339400,8.00,'2026-10-06 15:00:43','2026-10-06 17:24:52','2026-10-06 17:24:52'),(144,15,-23.5492200,-46.6338500,6.00,'2026-10-06 16:31:51','2026-10-06 17:24:52','2026-10-06 17:24:52'),(145,15,-23.5486700,-46.6346400,6.00,'2026-10-06 16:16:51','2026-10-06 17:24:52','2026-10-06 17:24:52'),(146,15,-23.5484900,-46.6338400,12.00,'2026-10-06 16:01:51','2026-10-06 17:24:52','2026-10-06 17:24:52'),(147,15,-23.5483700,-46.6329000,6.00,'2026-10-06 15:46:51','2026-10-06 17:24:52','2026-10-06 17:24:52'),(148,15,-23.5483900,-46.6337900,8.00,'2026-10-06 15:31:51','2026-10-06 17:24:52','2026-10-06 17:24:52'),(149,15,-23.5474300,-46.6335300,11.00,'2026-10-06 15:16:51','2026-10-06 17:24:52','2026-10-06 17:24:52'),(150,15,-23.5469100,-46.6343200,11.00,'2026-10-06 15:01:51','2026-10-06 17:24:52','2026-10-06 17:24:52'),(151,15,-23.5467100,-46.6339800,4.00,'2026-10-06 14:46:51','2026-10-06 17:24:52','2026-10-06 17:24:52'),(152,15,-23.5466600,-46.6332300,5.00,'2026-10-06 14:31:51','2026-10-06 17:24:52','2026-10-06 17:24:52'),(153,14,-21.7056800,-47.2731600,4.00,'2026-10-06 18:21:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(154,14,-21.7059800,-47.2723600,8.00,'2026-10-06 18:06:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(155,14,-21.7062400,-47.2729600,7.00,'2026-10-06 17:51:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(156,14,-21.7063100,-47.2735700,11.00,'2026-10-06 17:36:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(157,14,-21.7056300,-47.2741900,11.00,'2026-10-06 17:21:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(158,14,-21.7065600,-47.2744300,10.00,'2026-10-06 17:06:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(159,14,-21.7065700,-47.2735900,12.00,'2026-10-06 16:51:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(160,14,-21.7069100,-47.2731500,9.00,'2026-10-06 16:36:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(161,14,-21.7059700,-47.2728800,12.00,'2026-10-06 16:21:36','2026-10-06 18:21:36','2026-10-06 18:21:36'),(162,14,-21.7056300,-47.2736500,5.00,'2026-10-06 16:06:36','2026-10-06 18:21:36','2026-10-06 18:21:36');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026_10_07_000001_create_contas_tables',1),(2,'2026_10_07_000002_create_animais_coleiras_tables',1),(3,'2026_10_07_000003_create_monitoramento_tables',1),(4,'2026_10_07_000004_create_site_tables',1);
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
  `nome` varchar(150) NOT NULL,
  `email` varchar(200) DEFAULT NULL,
  `telefone` varchar(20) NOT NULL,
  `porte` varchar(60) NOT NULL DEFAULT 'medio',
  `quantidade` int(10) unsigned NOT NULL DEFAULT 1,
  `observacoes` text DEFAULT NULL,
  `status` enum('pendente','aprovado','recusado') NOT NULL DEFAULT 'pendente',
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
  `nome` varchar(100) NOT NULL,
  `porte` enum('pequeno','medio','grande') NOT NULL,
  `descricao` text NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `preco_promocional` decimal(10,2) DEFAULT NULL COMMENT 'Preço da página Oferta',
  `imagem` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `produtos_porte_unique` (`porte`)
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
-- Table structure for table `telemetrias`
--

DROP TABLE IF EXISTS `telemetrias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `telemetrias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `coleira_id` bigint(20) unsigned DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
  `ip` varchar(45) DEFAULT NULL,
  `situacao` enum('processada','rejeitada') NOT NULL DEFAULT 'processada',
  `erro` varchar(255) DEFAULT NULL,
  `recebido_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `telemetrias_coleira_id_recebido_em_index` (`coleira_id`,`recebido_em`),
  CONSTRAINT `telemetrias_coleira_id_foreign` FOREIGN KEY (`coleira_id`) REFERENCES `coleiras` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telemetrias`
--

LOCK TABLES `telemetrias` WRITE;
/*!40000 ALTER TABLE `telemetrias` DISABLE KEYS */;
/*!40000 ALTER TABLE `telemetrias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tutores`
--

DROP TABLE IF EXISTS `tutores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tutores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `nome` varchar(150) NOT NULL,
  `telefone` varchar(20) NOT NULL DEFAULT '',
  `email` varchar(200) NOT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tutores_user_id_unique` (`user_id`),
  UNIQUE KEY `tutores_email_unique` (`email`),
  KEY `tutores_nome_index` (`nome`),
  CONSTRAINT `tutores_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tutores`
--

LOCK TABLES `tutores` WRITE;
/*!40000 ALTER TABLE `tutores` DISABLE KEYS */;
INSERT INTO `tutores` VALUES (4,4,'Rafael Nogueira','(11) 91234-5678','rafael.nogueira@petmail.com','São Paulo, SP','2026-09-06 23:57:27','2026-09-06 23:57:27'),(5,5,'Juliana Marques','(21) 99876-5432','juliana.marques@petmail.com','Rio de Janeiro, RJ','2026-09-06 23:57:27','2026-09-06 23:57:27'),(6,6,'Eduardo Ramos','(31) 98765-1234','eduardo.ramos@petmail.com','Belo Horizonte, MG','2026-09-06 23:57:27','2026-09-06 23:57:27'),(7,7,'Patrícia Gomes','(41) 99222-3344','patricia.gomes@petmail.com','Curitiba, PR','2026-09-06 23:57:27','2026-09-06 23:57:27'),(8,8,'João Vitor Vieira Da Costa','','joooooo@gmail.com',NULL,'2026-10-06 16:34:28','2026-10-06 16:34:28'),(9,9,'João Vitor Vieira Da Costa','','j652431@gmail.com',NULL,'2026-10-06 17:33:32','2026-10-06 17:33:32'),(10,10,'João Vitor Vieira Da Costa','','joao.v.costa89@aluno.senai.br',NULL,'2026-10-06 18:06:45','2026-10-06 18:06:45');
/*!40000 ALTER TABLE `tutores` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (4,'Rafael Nogueira','rafael.nogueira@petmail.com',NULL,NULL,NULL,'$2y$12$wpFBbDf08w7MgFUB2o58BeyEE9tsZB6YnWCUFZZz8AyamMLXAXEs6',NULL,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(5,'Juliana Marques','juliana.marques@petmail.com',NULL,NULL,NULL,'$2y$12$F/lSZkf43ukW8AnHUUyttuIM7DxUQwuSai0gUhai0.QVAt9c8z.vu',NULL,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(6,'Eduardo Ramos','eduardo.ramos@petmail.com',NULL,NULL,NULL,'$2y$12$zPJvR46Gg5iD153lbviPLeHWmoW3pwnEQQKq39xS3rsoJtLc9/50i',NULL,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(7,'Patrícia Gomes','patricia.gomes@petmail.com',NULL,NULL,NULL,'$2y$12$pk/GSvXQDA1NBLuoISWxoO89c/q0yO1RacQseBhIS/uOyisud2dtu',NULL,'2026-09-06 23:57:27','2026-09-06 23:57:27'),(8,'João Vitor Vieira Da Costa','joooooo@gmail.com',NULL,NULL,NULL,'$2y$12$sBfzbgT1s9lM65PpAWpLQ.AFv4Z/ASlgZuINt/AGaDyuMUk7l1//S',NULL,'2026-10-06 16:34:28','2026-10-06 16:34:28'),(9,'João Vitor Vieira Da Costa','j652431@gmail.com',NULL,NULL,NULL,'$2y$12$40UYaUWny7z4QzV40/b2lu9XZb2ne1cVo/mNW/3g0OIs7xg2ouGam',NULL,'2026-10-06 17:33:32','2026-10-06 17:33:32'),(10,'João Vitor Vieira Da Costa','joao.v.costa89@aluno.senai.br',NULL,NULL,NULL,'$2y$12$cunxOZk0H4s/8nHmR1OMnuThDyxkecFkunv3hHPzOQSvutmkY35X.',NULL,'2026-10-06 18:06:45','2026-10-06 18:06:45');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `zonas_seguras`
--

DROP TABLE IF EXISTS `zonas_seguras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zonas_seguras` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `animal_id` bigint(20) unsigned NOT NULL,
  `nome` varchar(80) NOT NULL DEFAULT 'Casa',
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `raio_metros` int(10) unsigned NOT NULL DEFAULT 200,
  `ativa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `zonas_seguras_animal_id_foreign` (`animal_id`),
  CONSTRAINT `zonas_seguras_animal_id_foreign` FOREIGN KEY (`animal_id`) REFERENCES `animais` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `zonas_seguras`
--

LOCK TABLES `zonas_seguras` WRITE;
/*!40000 ALTER TABLE `zonas_seguras` DISABLE KEYS */;
INSERT INTO `zonas_seguras` VALUES (1,7,'Casa',-23.5078000,-46.6600000,2150,1,'2026-10-06 18:21:36','2026-10-06 18:21:36'),(2,8,'Casa',-23.5995000,-46.6968000,2300,1,'2026-10-06 18:21:36','2026-10-06 18:21:36'),(3,9,'Casa',-23.5187000,-46.5522000,1800,1,'2026-10-06 18:21:36','2026-10-06 18:21:36'),(4,10,'Casa',-23.4910000,-46.5907000,1900,1,'2026-10-06 18:21:36','2026-10-06 18:21:36'),(5,11,'Casa',-23.5320000,-46.6533000,2150,1,'2026-10-06 18:21:36','2026-10-06 18:21:36'),(6,12,'Casa',-23.5190000,-46.6464000,2000,1,'2026-10-06 18:21:36','2026-10-06 18:21:36'),(7,15,'Casa',-23.5504000,-46.6332800,450,1,'2026-10-06 18:21:36','2026-10-06 18:21:36'),(8,18,'Casa',-21.7056800,-47.2731600,300,1,'2026-10-06 18:21:36','2026-10-06 18:21:36'),(9,14,'Casa',-23.5501200,-46.6335100,450,1,'2026-10-06 18:21:36','2026-10-06 18:21:36');
/*!40000 ALTER TABLE `zonas_seguras` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
