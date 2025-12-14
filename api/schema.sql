-- ============================================
-- UFUTURO LICENCIADO - Esquema da Base de Dados
-- MySQL / MariaDB (Hostinger)
-- ============================================

-- Usar charset UTF-8 completo
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- Tabela: students (Estudantes)
-- ============================================
CREATE TABLE IF NOT EXISTS `students` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Formato: estudante.nome',
  `full_name` VARCHAR(150) NOT NULL,
  `whatsapp` VARCHAR(20) DEFAULT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `last_login` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_username` (`username`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: lesson_progress (Progresso das Aulas)
-- ============================================
CREATE TABLE IF NOT EXISTS `lesson_progress` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT UNSIGNED NOT NULL,
  `module_id` VARCHAR(50) NOT NULL,
  `lesson_id` VARCHAR(50) NOT NULL,
  `completed` TINYINT(1) DEFAULT 0,
  `completed_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_progress` (`student_id`, `module_id`, `lesson_id`),
  INDEX `idx_student` (`student_id`),
  CONSTRAINT `fk_progress_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: course_reviews (Avaliações do Curso)
-- ============================================
CREATE TABLE IF NOT EXISTS `course_reviews` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT UNSIGNED NOT NULL UNIQUE,
  `rating` TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
  `comment` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_rating` (`rating`),
  CONSTRAINT `fk_review_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: access_keys (Chaves de Acesso - Opcional para futuro)
-- ============================================
CREATE TABLE IF NOT EXISTS `access_keys` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `key_label` VARCHAR(100) NOT NULL COMMENT 'Ex: Lote 1 – Primeiros 50 estudantes',
  `access_password_hash` VARCHAR(255) NOT NULL,
  `active_from` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `active_to` DATETIME DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: student_access (Acesso do Estudante - Opcional para futuro)
-- ============================================
CREATE TABLE IF NOT EXISTS `student_access` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT UNSIGNED NOT NULL,
  `access_key_id` INT UNSIGNED NOT NULL,
  `granted_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_student_key` (`student_id`, `access_key_id`),
  CONSTRAINT `fk_access_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_access_key` FOREIGN KEY (`access_key_id`) REFERENCES `access_keys` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- Inserir estudante de teste (REMOVER EM PRODUÇÃO!)
-- Palavra-passe: teste123
-- ============================================
INSERT INTO `students` (`username`, `full_name`, `whatsapp`, `password_hash`, `is_active`)
VALUES (
  'estudante.teste',
  'Estudante de Teste',
  '+258841234567',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- teste123
  1
) ON DUPLICATE KEY UPDATE `id` = `id`;
