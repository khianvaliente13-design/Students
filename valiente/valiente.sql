-- ============================================================
--  Valiente Student Management System — Database Setup
--  Database: data
-- ============================================================

CREATE DATABASE IF NOT EXISTS `data`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `data`;

-- ------------------------------------------------------------
--  TABLE: users  (for login / signup)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `username`   VARCHAR(60)      NOT NULL,
  `email`      VARCHAR(191)     NOT NULL,
  `password`   VARCHAR(255)     NOT NULL,
  `created_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_username` (`username`),
  UNIQUE KEY `uq_email`    (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  TABLE: data  (student records)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `data` (
  `id`      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`    VARCHAR(191) NOT NULL,
  `age`     TINYINT      NOT NULL,
  `address` TEXT         NOT NULL,
  `course`  VARCHAR(191) NOT NULL,
  `school`  VARCHAR(191) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  SEED: admin account
--  Username : admin
--  Email    : khian
--  Password : khianix13  (bcrypt hash below)
-- ------------------------------------------------------------
INSERT INTO `users` (`username`, `email`, `password`)
SELECT 'admin', 'khian', '$2y$10$examplehashREPLACETHIS'
WHERE NOT EXISTS (
  SELECT 1 FROM `users` WHERE `username` = 'admin' OR `email` = 'khian'
);

-- ⚠️  IMPORTANT: The password hash above is a placeholder.
--     Run seed_admin.php in your browser once so PHP generates
--     the real bcrypt hash, OR replace the hash manually by
--     running this PHP snippet and copying the output:
--
--       <?php echo password_hash('khianix13', PASSWORD_BCRYPT); ?>
--
--     Then UPDATE users SET password='<your_hash>' WHERE username='admin';
