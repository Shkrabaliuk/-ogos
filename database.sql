-- =============================================
-- /\OGOS CMS DATABASE
-- Повна схема для швидкого розгортання
-- Пароль адміна: sex
-- =============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Використовуємо існуючу базу
USE `logos_db`;

-- Видаляємо всі таблиці для чистого перезавантаження
DROP TABLE IF EXISTS `post_tags`;
DROP TABLE IF EXISTS `tags`;
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `admin_users`;
DROP TABLE IF EXISTS `rose_toc`;
DROP TABLE IF EXISTS `rose_content`;
DROP TABLE IF EXISTS `rose_fulltext_index`;
DROP TABLE IF EXISTS `rose_keyword_index`;
DROP TABLE IF EXISTS `rose_metadata`;
DROP TABLE IF EXISTS `rose_snippet`;
DROP TABLE IF EXISTS `rose_word`;

-- ============================================
-- ОСНОВНІ ТАБЛИЦІ
-- ============================================

-- Налаштування сайту
CREATE TABLE `settings` (
  `key` varchar(50) NOT NULL,
  `value` text,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `settings` (`key`, `value`) VALUES
('blog_title', '/\\ogos'),
('posts_per_page', '5');

-- Адміністратори
CREATE TABLE `admin_users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Пароль: sex
INSERT INTO `admin_users` (`username`, `password_hash`) VALUES
('admin', '$2y$12$zPJhQn5UWOd0t/Ai8.fq0uMQax3B2H0tFcugJKXVQA.Ct/Ya4CH1e');

-- Пости
CREATE TABLE `posts` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` varchar(128) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `type` enum('text','image','link','quote','code') DEFAULT 'text',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_published` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Коментарі
CREATE TABLE `comments` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) UNSIGNED DEFAULT NULL,
  `author_name` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `fk_comments_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Теги
CREATE TABLE `tags` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Зв'язок постів і тегів
CREATE TABLE `post_tags` (
  `post_id` int(11) UNSIGNED NOT NULL,
  `tag_id` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`post_id`,`tag_id`),
  KEY `fk_pt_tag` (`tag_id`),
  CONSTRAINT `fk_pt_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pt_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- ТЕСТОВИЙ КОНТЕНТ
-- ============================================

-- Тестовий пост
INSERT INTO `posts` (`id`, `slug`, `title`, `content`, `type`, `created_at`) VALUES
(1, 'hello-world', 'Привіт, світе!', '<p>Це тестовий пост для перевірки роботи /\\ogos CMS.</p>\n\n<p>Система працює коректно. Можна додавати нові пости через <a href="/admin/">/admin/</a></p>', 'text', '2026-01-02 12:00:00');

-- Тестові теги
INSERT INTO `tags` (`id`, `name`) VALUES
(1, 'тест'),
(2, 'система');

-- Прив'язуємо теги до посту
INSERT INTO `post_tags` (`post_id`, `tag_id`) VALUES
(1, 1),
(1, 2);

-- ============================================
-- ПОШУКОВІ ТАБЛИЦІ (S2\Rose)
-- ============================================

CREATE TABLE `rose_toc` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `external_id` varchar(255) NOT NULL,
  `instance_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `added_at` datetime DEFAULT NULL,
  `url` text NOT NULL,
  `hash` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `instance_external` (`instance_id`, `external_id`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `rose_word` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `rose_fulltext_index` (
  `word_id` int(11) UNSIGNED NOT NULL,
  `toc_id` int(11) UNSIGNED NOT NULL,
  `position` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`word_id`, `toc_id`, `position`),
  KEY `toc_id` (`toc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `rose_keyword_index` (
  `keyword` varchar(255) NOT NULL,
  `toc_id` int(11) UNSIGNED NOT NULL,
  `type` int(11) UNSIGNED NOT NULL,
  KEY `keyword` (`keyword`(191)),
  KEY `toc_id` (`toc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `rose_keyword_multiple_index` (
  `keyword` varchar(255) NOT NULL,
  `toc_id` int(11) UNSIGNED NOT NULL,
  `type` int(11) UNSIGNED NOT NULL,
  KEY `keyword` (`keyword`(191)),
  KEY `toc_id` (`toc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- ЗАВЕРШЕННЯ
-- ============================================

SET FOREIGN_KEY_CHECKS = 1;