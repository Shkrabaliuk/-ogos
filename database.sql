-- =============================================
-- /\OGOS DATABASE SCHEMA
-- Мінімалістична структура для блог-движка
-- =============================================

SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `logos_db` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `logos_db`;

-- 1. Таблиця налаштувань (Key-Value)
-- Тут зберігатимемо назву блогу, опис, шлях до логотипу
CREATE TABLE `settings` (
  `key` varchar(50) NOT NULL,
  `value` text,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Дефолтні налаштування
INSERT INTO `settings` (`key`, `value`) VALUES
('blog_title', '/\\ogos'),
('blog_description', 'Just another minimal blog'),
('logo_path', '');

-- 2. Таблиця постів
CREATE TABLE `posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(128) NOT NULL, -- URL-адреса (напр. my-first-post)
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `type` enum('text','image','link','quote') DEFAULT 'text',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_published` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Таблиця коментарів
CREATE TABLE `comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) unsigned NOT NULL,
  `parent_id` int(11) unsigned DEFAULT NULL, -- Для вкладених відповідей
  `author_name` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `userpic` varchar(255) DEFAULT NULL, -- URL аватарки
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `fk_post_comment` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_parent_comment` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Таблиця тегів (опціонально, але в Егеї це важливо)
CREATE TABLE `tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Зв'язок постів і тегів
CREATE TABLE `post_tags` (
  `post_id` int(11) unsigned NOT NULL,
  `tag_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`post_id`,`tag_id`),
  CONSTRAINT `fk_pt_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pt_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Тестові дані
INSERT INTO `posts` (`slug`, `title`, `content`, `type`, `is_published`) VALUES
('welcome-to-logos', 'Ласкаво просимо до /\\ogos', 
'<p>Це перший пост у вашому новому блозі.</p>

<h2>Можливості</h2>

<ul>
<li>Мінімалістичний дизайн</li>
<li>Простота та швидкість</li>
<li>Фокус на типографіці</li>
</ul>',
'text', 1),
('hello-world', 'Привіт, /\\ogos!', 
'<p>Це перший пост у новій CMS. Вона працює швидко, як блискавка, і виглядає чисто, як аркуш паперу.</p>
<p>Далі буде...</p>', 
'text', 1);

INSERT INTO `tags` (`name`) VALUES ('мінімалізм'), ('php'), ('блог');
INSERT INTO `post_tags` (`post_id`, `tag_id`) VALUES (1, 1), (1, 2);

-- Тестові коментарі
INSERT INTO `comments` (`post_id`, `parent_id`, `author_name`, `content`) VALUES
(1, NULL, 'Олена', 'Дуже чистий дизайн, подобається!'),
(1, 1, 'Ярослав', 'Дякую! Намагався зробити максимально мінімалістично.'),
(2, NULL, 'Петро', 'Коли вийде повна версія?');

-- =============================================
-- ROSE SEARCH ENGINE TABLES
-- Fulltext пошук з морфологією та ранжуванням
-- =============================================

-- Таблиця Table of Contents (головна таблиця документів)
CREATE TABLE IF NOT EXISTS `rose_toc` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `external_id` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `added_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `url` varchar(500) DEFAULT NULL,
  `hash` varchar(80) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `external_id` (`external_id`),
  KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблиця для fulltext індексу (слова з позиціями)
CREATE TABLE IF NOT EXISTS `rose_fulltext_index` (
  `word` varchar(80) NOT NULL,
  `toc_id` int(11) unsigned NOT NULL,
  `position` int(11) unsigned NOT NULL,
  PRIMARY KEY (`word`, `toc_id`, `position`),
  KEY `toc_id` (`toc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблиця для keywords індексу (заголовки, теги)
CREATE TABLE IF NOT EXISTS `rose_keyword_index` (
  `keyword` varchar(255) NOT NULL,
  `toc_id` int(11) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`keyword`, `toc_id`),
  KEY `toc_id` (`toc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблиця для зберігання контенту (для генерації сніпетів)
CREATE TABLE IF NOT EXISTS `rose_content` (
  `toc_id` int(11) unsigned NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`toc_id`),
  CONSTRAINT `fk_rose_content_toc` FOREIGN KEY (`toc_id`) REFERENCES `rose_toc` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
