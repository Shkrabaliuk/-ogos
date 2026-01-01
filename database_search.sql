-- Rose Search Engine Tables
-- Додаткові таблиці для fulltext пошуку

USE `logos_db`;

-- Таблиця для fulltext індексу
CREATE TABLE IF NOT EXISTS `rose_fulltext_index` (
  `word` varchar(80) NOT NULL,
  `toc_id` int(11) unsigned NOT NULL,
  `position` int(11) unsigned NOT NULL,
  PRIMARY KEY (`word`, `toc_id`, `position`),
  KEY `toc_id` (`toc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблиця для keywords індексу
CREATE TABLE IF NOT EXISTS `rose_keyword_index` (
  `keyword` varchar(255) NOT NULL,
  `toc_id` int(11) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`keyword`, `toc_id`),
  KEY `toc_id` (`toc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблиця Table of Contents (зв'язок між external_id та internal_id)
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

-- Таблиця для зберігання контенту (для snippet generation)
CREATE TABLE IF NOT EXISTS `rose_content` (
  `toc_id` int(11) unsigned NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`toc_id`),
  CONSTRAINT `fk_rose_content_toc` FOREIGN KEY (`toc_id`) REFERENCES `rose_toc` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
