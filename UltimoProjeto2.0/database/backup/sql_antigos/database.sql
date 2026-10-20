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
