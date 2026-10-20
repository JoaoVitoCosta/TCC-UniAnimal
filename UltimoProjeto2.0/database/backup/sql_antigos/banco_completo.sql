-- ============================================================================
-- UniAnimal — BANCO DE DADOS COMPLETO (estrutura + dados novos)
-- Basta rodar este arquivo inteiro (Execute/Run SQL Script) — ele cria o
-- banco sozinho, não precisa criar nem selecionar nenhum schema antes.
-- Contém: 1) todas as tabelas do sistema  2) um novo conjunto de dados de
--          demonstração (tutores, animais, coleiras, produtos, orçamentos,
--          contatos e depoimentos), totalmente diferente do conjunto anterior.
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `unianimal`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `unianimal`;

-- ═══════════════════════════════════════════════════════════════════════
-- PARTE 1 — ESTRUTURA DAS TABELAS
-- ═══════════════════════════════════════════════════════════════════════

-- ============================================================================
-- UniAnimal — Schema consolidado (MySQL / MariaDB)
-- Gerado a partir das migrations finais do projeto em 2026-08-24.
-- Substitui os arquivos redundantes: BancoNovo_dump.sql, banco2_0_dump.sql,
-- unianimal_mysql_dump.sql e setup_mysql.sql (removidos por duplicação).
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------------------
-- users
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `google_id` VARCHAR(255) DEFAULT NULL,
  `avatar` VARCHAR(255) DEFAULT NULL,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_google_id_unique` (`google_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- tutors  (1 tutor = 1 usuário autenticado)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tutors` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `nome` VARCHAR(150) NOT NULL,
  `telefone` VARCHAR(20) NOT NULL,
  `email` VARCHAR(200) NOT NULL,
  `endereco` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tutors_user_id_unique` (`user_id`),
  UNIQUE KEY `tutors_email_unique` (`email`),
  KEY `tutors_nome_index` (`nome`),
  CONSTRAINT `tutors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- animals
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `animals` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tutor_id` BIGINT UNSIGNED NOT NULL,
  `nome` VARCHAR(100) NOT NULL,
  `especie` VARCHAR(60) NOT NULL,
  `raca` VARCHAR(100) DEFAULT NULL,
  `porte` ENUM('pequeno','medio','grande') NOT NULL,
  `idade` TINYINT UNSIGNED DEFAULT NULL,
  `peso` DECIMAL(6,2) DEFAULT NULL,
  `status` ENUM('saudavel','atencao','emergencia') NOT NULL DEFAULT 'saudavel',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `animals_tutor_id_index` (`tutor_id`),
  KEY `animals_status_index` (`status`),
  CONSTRAINT `animals_tutor_id_foreign` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- coleiras  (dispositivo GPS/bateria, 1:1 com animals)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `coleiras` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `animal_id` BIGINT UNSIGNED DEFAULT NULL COMMENT 'NULL = coleira em estoque, ainda não vinculada',
  `codigo` VARCHAR(50) NOT NULL,
  `latitude` DECIMAL(10,7) DEFAULT NULL,
  `longitude` DECIMAL(10,7) DEFAULT NULL,
  `precisao` DECIMAL(6,2) DEFAULT NULL COMMENT 'Precisão do GPS em metros',
  `localizacao_atualizada_em` TIMESTAMP NULL DEFAULT NULL,
  `bateria_percentual` TINYINT UNSIGNED NOT NULL DEFAULT 100,
  `bateria_status` ENUM('normal','baixa','critica') NOT NULL DEFAULT 'normal',
  `bateria_atualizada_em` TIMESTAMP NULL DEFAULT NULL,
  `status` ENUM('disponivel','ativa','inativa','sem_sinal') NOT NULL DEFAULT 'disponivel',
  `ativada_em` TIMESTAMP NULL DEFAULT NULL COMMENT 'Quando o código foi vinculado a um animal',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coleiras_animal_id_unique` (`animal_id`),
  UNIQUE KEY `coleiras_codigo_unique` (`codigo`),
  KEY `coleiras_codigo_index` (`codigo`),
  KEY `coleiras_status_index` (`status`),
  KEY `coleiras_bateria_status_index` (`bateria_status`),
  CONSTRAINT `coleiras_animal_id_foreign` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- localizacoes  (histórico de posições GPS da coleira)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `localizacoes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `coleira_id` BIGINT UNSIGNED NOT NULL,
  `latitude` DECIMAL(10,7) NOT NULL,
  `longitude` DECIMAL(10,7) NOT NULL,
  `precisao` DECIMAL(6,2) DEFAULT NULL,
  `registrado_em` TIMESTAMP NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `localizacoes_coleira_id_registrado_em_index` (`coleira_id`, `registrado_em`),
  CONSTRAINT `localizacoes_coleira_id_foreign` FOREIGN KEY (`coleira_id`) REFERENCES `coleiras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- api_tokens  (sessões do app Flutter — login real por código da coleira)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `api_tokens` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tutor_id` BIGINT UNSIGNED NOT NULL,
  `coleira_id` BIGINT UNSIGNED DEFAULT NULL,
  `token_hash` VARCHAR(64) NOT NULL COMMENT 'SHA-256 do token; o texto puro nunca é armazenado',
  `nome_dispositivo` VARCHAR(150) DEFAULT NULL,
  `last_used_at` TIMESTAMP NULL DEFAULT NULL,
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `api_tokens_token_hash_unique` (`token_hash`),
  KEY `api_tokens_tutor_id_index` (`tutor_id`),
  CONSTRAINT `api_tokens_tutor_id_foreign` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `api_tokens_coleira_id_foreign` FOREIGN KEY (`coleira_id`) REFERENCES `coleiras` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- depoimentos
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `depoimentos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `telefone` VARCHAR(255) DEFAULT NULL,
  `depoimento` TEXT NOT NULL,
  `estrelas` INT NOT NULL DEFAULT 5,
  `aprovado` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- contatos
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contatos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `telefone` VARCHAR(255) DEFAULT NULL,
  `assunto` VARCHAR(255) DEFAULT NULL,
  `mensagem` TEXT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- orcamentos
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orcamentos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `telefone` VARCHAR(255) NOT NULL,
  `porte` VARCHAR(255) NOT NULL DEFAULT 'medio',
  `quantidade` INT NOT NULL DEFAULT 1,
  `observacoes` TEXT DEFAULT NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'pendente',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- produtos
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `produtos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `porte` VARCHAR(255) NOT NULL,
  `descricao` TEXT NOT NULL,
  `preco` DECIMAL(10,2) NOT NULL,
  `preco_promocional` DECIMAL(10,2) DEFAULT NULL,
  `imagem` VARCHAR(255) DEFAULT NULL,
  `ativo` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Observação: sessions/cache/jobs não são criadas como tabelas porque o
-- .env usa SESSION_DRIVER=file, CACHE_DRIVER=file e QUEUE_CONNECTION=sync.
-- Caso troque para os drivers "database" no futuro, gere as migrations
-- oficiais do Laravel (`php artisan session:table`, etc.) e adicione aqui.
-- ----------------------------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 1;

-- ═══════════════════════════════════════════════════════════════════════
-- PARTE 2 — DADOS (novo conjunto de demonstração)
-- ═══════════════════════════════════════════════════════════════════════

-- ============================================================================
-- UniAnimal — dados de demonstração (gerados via migrate + seed em ambiente de teste)
-- Importar DEPOIS do schema em database/database.sql
-- ============================================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------------------
-- users
-- ----------------------------------------------------------------------------
TRUNCATE TABLE `users`;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `google_id`, `avatar`) VALUES (4, 'Rafael Nogueira', 'rafael.nogueira@petmail.com', NULL, '$2y$12$wpFBbDf08w7MgFUB2o58BeyEE9tsZB6YnWCUFZZz8AyamMLXAXEs6', NULL, '2026-09-06 20:57:27', '2026-09-06 20:57:27', NULL, NULL);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `google_id`, `avatar`) VALUES (5, 'Juliana Marques', 'juliana.marques@petmail.com', NULL, '$2y$12$F/lSZkf43ukW8AnHUUyttuIM7DxUQwuSai0gUhai0.QVAt9c8z.vu', NULL, '2026-09-06 20:57:27', '2026-09-06 20:57:27', NULL, NULL);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `google_id`, `avatar`) VALUES (6, 'Eduardo Ramos', 'eduardo.ramos@petmail.com', NULL, '$2y$12$zPJvR46Gg5iD153lbviPLeHWmoW3pwnEQQKq39xS3rsoJtLc9/50i', NULL, '2026-09-06 20:57:27', '2026-09-06 20:57:27', NULL, NULL);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `google_id`, `avatar`) VALUES (7, 'Patrícia Gomes', 'patricia.gomes@petmail.com', NULL, '$2y$12$pk/GSvXQDA1NBLuoISWxoO89c/q0yO1RacQseBhIS/uOyisud2dtu', NULL, '2026-09-06 20:57:27', '2026-09-06 20:57:27', NULL, NULL);

-- ----------------------------------------------------------------------------
-- tutors
-- ----------------------------------------------------------------------------
TRUNCATE TABLE `tutors`;
INSERT INTO `tutors` (`id`, `user_id`, `nome`, `telefone`, `email`, `endereco`, `created_at`, `updated_at`) VALUES (4, 4, 'Rafael Nogueira', '(11) 91234-5678', 'rafael.nogueira@petmail.com', 'São Paulo, SP', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `tutors` (`id`, `user_id`, `nome`, `telefone`, `email`, `endereco`, `created_at`, `updated_at`) VALUES (5, 5, 'Juliana Marques', '(21) 99876-5432', 'juliana.marques@petmail.com', 'Rio de Janeiro, RJ', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `tutors` (`id`, `user_id`, `nome`, `telefone`, `email`, `endereco`, `created_at`, `updated_at`) VALUES (6, 6, 'Eduardo Ramos', '(31) 98765-1234', 'eduardo.ramos@petmail.com', 'Belo Horizonte, MG', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `tutors` (`id`, `user_id`, `nome`, `telefone`, `email`, `endereco`, `created_at`, `updated_at`) VALUES (7, 7, 'Patrícia Gomes', '(41) 99222-3344', 'patricia.gomes@petmail.com', 'Curitiba, PR', '2026-09-06 20:57:27', '2026-09-06 20:57:27');

-- ----------------------------------------------------------------------------
-- animals
-- ----------------------------------------------------------------------------
TRUNCATE TABLE `animals`;
INSERT INTO `animals` (`id`, `tutor_id`, `nome`, `especie`, `raca`, `porte`, `idade`, `peso`, `status`, `created_at`, `updated_at`) VALUES (7, 4, 'Belinha', 'Cachorro', 'Beagle', 'pequeno', 3, 9.5, 'saudavel', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `animals` (`id`, `tutor_id`, `nome`, `especie`, `raca`, `porte`, `idade`, `peso`, `status`, `created_at`, `updated_at`) VALUES (8, 4, 'Max', 'Cachorro', 'Bulldog Francês', 'pequeno', 5, 12, 'atencao', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `animals` (`id`, `tutor_id`, `nome`, `especie`, `raca`, `porte`, `idade`, `peso`, `status`, `created_at`, `updated_at`) VALUES (9, 5, 'Simba', 'Gato', 'Maine Coon', 'grande', 4, 7.2, 'saudavel', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `animals` (`id`, `tutor_id`, `nome`, `especie`, `raca`, `porte`, `idade`, `peso`, `status`, `created_at`, `updated_at`) VALUES (10, 6, 'Amora', 'Cachorro', 'Poodle', 'pequeno', 2, 6, 'saudavel', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `animals` (`id`, `tutor_id`, `nome`, `especie`, `raca`, `porte`, `idade`, `peso`, `status`, `created_at`, `updated_at`) VALUES (11, 6, 'Zeca', 'Cavalo', 'Quarto de Milha', 'grande', 8, 420, 'saudavel', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `animals` (`id`, `tutor_id`, `nome`, `especie`, `raca`, `porte`, `idade`, `peso`, `status`, `created_at`, `updated_at`) VALUES (12, 7, 'Bento', 'Cachorro', 'Vira-lata', 'medio', 5, 18, 'emergencia', '2026-09-06 20:57:27', '2026-09-06 20:57:27');

-- ----------------------------------------------------------------------------
-- coleiras
-- ----------------------------------------------------------------------------
TRUNCATE TABLE `coleiras`;
INSERT INTO `coleiras` (`id`, `animal_id`, `codigo`, `latitude`, `longitude`, `precisao`, `localizacao_atualizada_em`, `bateria_percentual`, `bateria_status`, `bateria_atualizada_em`, `status`, `created_at`, `updated_at`) VALUES (7, 7, 'COL-DC9FEAAA', -23.5078, -46.66, 11, '2026-09-06 20:57:27', 48, 'normal', '2026-09-06 20:57:27', 'ativa', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `coleiras` (`id`, `animal_id`, `codigo`, `latitude`, `longitude`, `precisao`, `localizacao_atualizada_em`, `bateria_percentual`, `bateria_status`, `bateria_atualizada_em`, `status`, `created_at`, `updated_at`) VALUES (8, 8, 'COL-69CE7CC6', -23.5995, -46.6968, 5, '2026-09-06 20:57:27', 54, 'normal', '2026-09-06 20:57:27', 'ativa', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `coleiras` (`id`, `animal_id`, `codigo`, `latitude`, `longitude`, `precisao`, `localizacao_atualizada_em`, `bateria_percentual`, `bateria_status`, `bateria_atualizada_em`, `status`, `created_at`, `updated_at`) VALUES (9, 9, 'COL-2A1FD091', -23.5187, -46.5522, 5, '2026-09-06 20:57:27', 89, 'normal', '2026-09-06 20:57:27', 'ativa', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `coleiras` (`id`, `animal_id`, `codigo`, `latitude`, `longitude`, `precisao`, `localizacao_atualizada_em`, `bateria_percentual`, `bateria_status`, `bateria_atualizada_em`, `status`, `created_at`, `updated_at`) VALUES (10, 10, 'COL-E348E3A4', -23.491, -46.5907, 3, '2026-09-06 20:57:27', 63, 'normal', '2026-09-06 20:57:27', 'ativa', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `coleiras` (`id`, `animal_id`, `codigo`, `latitude`, `longitude`, `precisao`, `localizacao_atualizada_em`, `bateria_percentual`, `bateria_status`, `bateria_atualizada_em`, `status`, `created_at`, `updated_at`) VALUES (11, 11, 'COL-AB7F406E', -23.532, -46.6533, 9, '2026-09-06 20:57:27', 97, 'normal', '2026-09-06 20:57:27', 'ativa', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `coleiras` (`id`, `animal_id`, `codigo`, `latitude`, `longitude`, `precisao`, `localizacao_atualizada_em`, `bateria_percentual`, `bateria_status`, `bateria_atualizada_em`, `status`, `created_at`, `updated_at`) VALUES (12, 12, 'COL-42811A8F', -23.519, -46.6464, 6, '2026-09-06 20:57:27', 94, 'normal', '2026-09-06 20:57:27', 'ativa', '2026-09-06 20:57:27', '2026-09-06 20:57:27');

-- Estoque de códigos AINDA NÃO vinculados a nenhum animal — usados para
-- testar (a) cadastro de animal com um código válido em estoque e
-- (b) o app tentando logar com um código que ainda não foi vinculado.
INSERT INTO `coleiras` (`id`, `animal_id`, `codigo`, `bateria_percentual`, `status`, `created_at`, `updated_at`) VALUES (13, NULL, 'COL-DEMO-0001', 100, 'disponivel', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `coleiras` (`id`, `animal_id`, `codigo`, `bateria_percentual`, `status`, `created_at`, `updated_at`) VALUES (14, NULL, 'COL-DEMO-0002', 100, 'disponivel', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `coleiras` (`id`, `animal_id`, `codigo`, `bateria_percentual`, `status`, `created_at`, `updated_at`) VALUES (15, NULL, 'COL-DEMO-0003', 100, 'disponivel', '2026-09-06 20:57:27', '2026-09-06 20:57:27');

-- ----------------------------------------------------------------------------
-- localizacoes
-- ----------------------------------------------------------------------------
TRUNCATE TABLE `localizacoes`;
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (67, 7, -23.5119, -46.6632, 10, '2026-09-06 18:27:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (68, 7, -23.5087, -46.6474, 7, '2026-09-06 18:42:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (69, 7, -23.5142, -46.6699, 5, '2026-09-06 18:57:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (70, 7, -23.52, -46.6515, 4, '2026-09-06 19:12:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (71, 7, -23.5191, -46.662, 5, '2026-09-06 19:27:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (72, 7, -23.4968, -46.6606, 8, '2026-09-06 19:42:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (73, 7, -23.5062, -46.645, 8, '2026-09-06 19:57:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (74, 7, -23.5209, -46.6456, 3, '2026-09-06 20:12:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (75, 7, -23.5067, -46.6522, 8, '2026-09-06 20:27:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (76, 7, -23.4943, -46.6513, 5, '2026-09-06 20:42:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (77, 7, -23.5098, -46.6744, 3, '2026-09-06 20:57:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (78, 8, -23.6141, -46.6914, 11, '2026-09-06 18:27:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (79, 8, -23.587, -46.6827, 10, '2026-09-06 18:42:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (80, 8, -23.5866, -46.7118, 3, '2026-09-06 18:57:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (81, 8, -23.5952, -46.6893, 12, '2026-09-06 19:12:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (82, 8, -23.6014, -46.6999, 3, '2026-09-06 19:27:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (83, 8, -23.5995, -46.7094, 4, '2026-09-06 19:42:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (84, 8, -23.6057, -46.697, 4, '2026-09-06 19:57:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (85, 8, -23.5923, -46.699, 10, '2026-09-06 20:12:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (86, 8, -23.5981, -46.6874, 12, '2026-09-06 20:27:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (87, 8, -23.5847, -46.7114, 12, '2026-09-06 20:42:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (88, 8, -23.5868, -46.7058, 5, '2026-09-06 20:57:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (89, 9, -23.517, -46.5626, 3, '2026-09-06 18:27:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (90, 9, -23.51, -46.5642, 11, '2026-09-06 18:42:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (91, 9, -23.5225, -46.5396, 6, '2026-09-06 18:57:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (92, 9, -23.5333, -46.5577, 6, '2026-09-06 19:12:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (93, 9, -23.5225, -46.5425, 12, '2026-09-06 19:27:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (94, 9, -23.5131, -46.5575, 12, '2026-09-06 19:42:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (95, 9, -23.52, -46.5391, 10, '2026-09-06 19:57:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (96, 9, -23.5153, -46.5429, 5, '2026-09-06 20:12:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (97, 9, -23.5198, -46.5469, 6, '2026-09-06 20:27:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (98, 9, -23.5177, -46.5432, 6, '2026-09-06 20:42:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (99, 9, -23.5137, -46.5582, 3, '2026-09-06 20:57:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (100, 10, -23.4893, -46.5912, 5, '2026-09-06 18:27:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (101, 10, -23.4879, -46.6039, 7, '2026-09-06 18:42:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (102, 10, -23.493, -46.5884, 5, '2026-09-06 18:57:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (103, 10, -23.4972, -46.5838, 8, '2026-09-06 19:12:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (104, 10, -23.4935, -46.5879, 7, '2026-09-06 19:27:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (105, 10, -23.4769, -46.5922, 7, '2026-09-06 19:42:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (106, 10, -23.4781, -46.6016, 4, '2026-09-06 19:57:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (107, 10, -23.4832, -46.5952, 7, '2026-09-06 20:12:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (108, 10, -23.4821, -46.5985, 8, '2026-09-06 20:27:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (109, 10, -23.4817, -46.5855, 5, '2026-09-06 20:42:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (110, 10, -23.482, -46.5865, 11, '2026-09-06 20:57:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (111, 11, -23.5354, -46.6519, 6, '2026-09-06 18:27:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (112, 11, -23.5467, -46.6606, 12, '2026-09-06 18:42:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (113, 11, -23.5391, -46.6441, 12, '2026-09-06 18:57:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (114, 11, -23.5236, -46.6636, 10, '2026-09-06 19:12:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (115, 11, -23.5307, -46.6642, 6, '2026-09-06 19:27:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (116, 11, -23.5417, -46.6562, 8, '2026-09-06 19:42:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (117, 11, -23.5313, -46.6398, 9, '2026-09-06 19:57:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (118, 11, -23.527, -46.6424, 5, '2026-09-06 20:12:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (119, 11, -23.5268, -46.6414, 5, '2026-09-06 20:27:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (120, 11, -23.5186, -46.6392, 7, '2026-09-06 20:42:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (121, 11, -23.5376, -46.6453, 5, '2026-09-06 20:57:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (122, 12, -23.5189, -46.6384, 4, '2026-09-06 18:27:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (123, 12, -23.5087, -46.6444, 3, '2026-09-06 18:42:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (124, 12, -23.5238, -46.6567, 6, '2026-09-06 18:57:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (125, 12, -23.5224, -46.6514, 8, '2026-09-06 19:12:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (126, 12, -23.5324, -46.6344, 8, '2026-09-06 19:27:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (127, 12, -23.5179, -46.6509, 9, '2026-09-06 19:42:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (128, 12, -23.5115, -46.6457, 12, '2026-09-06 19:57:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (129, 12, -23.5149, -46.6329, 9, '2026-09-06 20:12:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (130, 12, -23.5225, -46.6349, 3, '2026-09-06 20:27:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (131, 12, -23.5172, -46.6563, 7, '2026-09-06 20:42:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `localizacoes` (`id`, `coleira_id`, `latitude`, `longitude`, `precisao`, `registrado_em`, `created_at`, `updated_at`) VALUES (132, 12, -23.533, -46.646, 12, '2026-09-06 20:57:27', '2026-09-06 20:57:27', '2026-09-06 20:57:27');

-- ----------------------------------------------------------------------------
-- produtos
-- ----------------------------------------------------------------------------
TRUNCATE TABLE `produtos`;
INSERT INTO `produtos` (`id`, `nome`, `porte`, `descricao`, `preco`, `preco_promocional`, `imagem`, `ativo`, `created_at`, `updated_at`) VALUES (1, 'UniCollar Mini', 'pequeno', 'Coleira inteligente para pets de pequeno porte, com GPS, ECG e sensor de temperatura.', 799, NULL, NULL, 1, '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `produtos` (`id`, `nome`, `porte`, `descricao`, `preco`, `preco_promocional`, `imagem`, `ativo`, `created_at`, `updated_at`) VALUES (2, 'UniCollar Pro', 'medio', 'Nosso modelo mais vendido: GPS, ECG, monitor de sono e alertas com IA.', 879, 799, NULL, 1, '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `produtos` (`id`, `nome`, `porte`, `descricao`, `preco`, `preco_promocional`, `imagem`, `ativo`, `created_at`, `updated_at`) VALUES (3, 'UniCollar Max', 'grande', 'Para animais de grande porte e uso rural, com alcance de GPS de até 5km.', 1099, NULL, NULL, 1, '2026-09-06 20:57:27', '2026-09-06 20:57:27');

-- ----------------------------------------------------------------------------
-- orcamentos
-- ----------------------------------------------------------------------------
TRUNCATE TABLE `orcamentos`;
INSERT INTO `orcamentos` (`id`, `nome`, `email`, `telefone`, `porte`, `quantidade`, `observacoes`, `status`, `created_at`, `updated_at`) VALUES (1, 'Clínica Veterinária Vida Animal', 'compras@vidaanimalvet.com.br', '(19) 3322-4455', 'medio', 25, 'Interesse em coleiras para monitoramento de pacientes internados.', 'pendente', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `orcamentos` (`id`, `nome`, `email`, `telefone`, `porte`, `quantidade`, `observacoes`, `status`, `created_at`, `updated_at`) VALUES (2, 'Pet Shop Amigo Fiel', 'contato@amigofiel.com.br', '(11) 4455-6677', 'pequeno', 40, 'Revenda para clientes da loja, gostaríamos de condições especiais.', 'aprovado', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `orcamentos` (`id`, `nome`, `email`, `telefone`, `porte`, `quantidade`, `observacoes`, `status`, `created_at`, `updated_at`) VALUES (3, 'Fazenda Santa Rita', 'administrativo@santarita.agr.br', '(16) 3211-0099', 'grande', 60, 'Monitoramento de rebanho equino e bovino em área rural extensa.', 'pendente', '2026-09-06 20:57:27', '2026-09-06 20:57:27');

-- ----------------------------------------------------------------------------
-- contatos
-- ----------------------------------------------------------------------------
TRUNCATE TABLE `contatos`;
INSERT INTO `contatos` (`id`, `nome`, `email`, `telefone`, `assunto`, `mensagem`, `created_at`, `updated_at`) VALUES (1, 'Fernanda Castro', 'fernanda.castro@gmail.com', '(19) 99887-1122', 'Dúvida sobre produto', 'Gostaria de saber se o UniCollar Pro funciona sem sinal de internet no local.', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `contatos` (`id`, `nome`, `email`, `telefone`, `assunto`, `mensagem`, `created_at`, `updated_at`) VALUES (2, 'Marcelo Teixeira', 'marcelo.teixeira@outlook.com', '(31) 98877-2233', 'Suporte técnico', 'Minha coleira não está atualizando a localização desde ontem, podem ajudar?', '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `contatos` (`id`, `nome`, `email`, `telefone`, `assunto`, `mensagem`, `created_at`, `updated_at`) VALUES (3, 'Beatriz Andrade', 'bea.andrade@yahoo.com', NULL, 'Parceria comercial', 'Sou tutora de um canil e gostaria de conversar sobre parceria para divulgação.', '2026-09-06 20:57:27', '2026-09-06 20:57:27');

-- ----------------------------------------------------------------------------
-- depoimentos
-- ----------------------------------------------------------------------------
TRUNCATE TABLE `depoimentos`;
INSERT INTO `depoimentos` (`id`, `nome`, `email`, `telefone`, `depoimento`, `estrelas`, `aprovado`, `created_at`, `updated_at`) VALUES (1, 'Rafael Nogueira', 'rafael.nogueira@petmail.com', '(11) 91234-5678', 'A UniCollar me avisou que o Max estava com a temperatura alta antes mesmo de eu notar qualquer sintoma. Levei ao veterinário na hora.', 5, 1, '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `depoimentos` (`id`, `nome`, `email`, `telefone`, `depoimento`, `estrelas`, `aprovado`, `created_at`, `updated_at`) VALUES (2, 'Juliana Marques', 'juliana.marques@petmail.com', NULL, 'O Simba é um gato que ama fugir de casa. Com o GPS em tempo real, encontrei ele em minutos na primeira vez que sumiu.', 5, 1, '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `depoimentos` (`id`, `nome`, `email`, `telefone`, `depoimento`, `estrelas`, `aprovado`, `created_at`, `updated_at`) VALUES (3, 'Eduardo Ramos', 'eduardo.ramos@petmail.com', '(31) 98765-1234', 'Uso a coleira rural no Zeca há 6 meses. A bateria dura muito e o sinal chega até nas partes mais distantes do sítio.', 4, 1, '2026-09-06 20:57:27', '2026-09-06 20:57:27');
INSERT INTO `depoimentos` (`id`, `nome`, `email`, `telefone`, `depoimento`, `estrelas`, `aprovado`, `created_at`, `updated_at`) VALUES (4, 'Patrícia Gomes', 'patricia.gomes@petmail.com', NULL, 'Suporte muito atencioso quando tive dúvidas na instalação. Recomendo para quem tem cães mais velhos como o Bento.', 5, 1, '2026-09-06 20:57:27', '2026-09-06 20:57:27');

SET FOREIGN_KEY_CHECKS = 1;