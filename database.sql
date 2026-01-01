-- =============================================
-- /\\OGOS SIMPLE & STABLE
-- Тільки база: 2 пости, стабільна галерея
-- =============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. ЧИСТА СТРУКТУРА
-- ---------------------------------------------
CREATE DATABASE IF NOT EXISTS `logos_db` DEFAULT CHARACTER SET utf8mb4;
USE `logos_db`;

DROP TABLE IF EXISTS `post_tags`;
DROP TABLE IF EXISTS `tags`;
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `settings`;

-- Таблиці
CREATE TABLE `settings` (
  `key` varchar(50) NOT NULL,
  `value` text,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB;

INSERT INTO `settings` (`key`, `value`) VALUES
('blog_title', '/\\ogos'),
('posts_per_page', '5');

CREATE TABLE `posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(128) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `type` enum('text','image','link','quote','code') DEFAULT 'text',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_published` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB;

CREATE TABLE `comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) unsigned NOT NULL,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `author_name` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB;

CREATE TABLE `tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `post_tags` (
  `post_id` int(11) unsigned NOT NULL,
  `tag_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`post_id`,`tag_id`)
) ENGINE=InnoDB;


-- 2. КОНТЕНТ (СТАБІЛЬНИЙ)
-- ---------------------------------------------

-- POST 1: Текст (Typography Test)
INSERT INTO `posts` (`id`, `slug`, `title`, `content`, `type`, `created_at`) VALUES
(1, 'typography-basics', 'Типографіка та текст',
'<p>Це перевірка шрифтів. Текст має читатися легко.</p>
<blockquote>Простота — це не відсутність елементів, а відсутність зайвого.</blockquote>
<ul>
  <li>Пункт списку 1</li>
  <li>Пункт списку 2</li>
</ul>
<pre><code class="language-php">echo "Hello World";</code></pre>',
'text', '2024-01-01 10:00:00');

-- POST 2: Галерея (Fixed Height)
-- Використовуємо data-height, щоб зафіксувати висоту і прибрати "миготіння"
INSERT INTO `posts` (`id`, `slug`, `title`, `content`, `type`, `created_at`) VALUES
(2, 'stable-gallery', 'Стабільна галерея',
'<p>Ця галерея має жорстко задану пропорцію 16:9, тому вона не повинна стрибати при завантаженні.</p>

<div class="fotorama" 
     data-width="100%" 
     data-ratio="16/9" 
     data-allowfullscreen="true" 
     data-nav="thumbs">
     
  <img src="https://images.unsplash.com/photo-1494526585095-c41746248156?ixlib=rb-1.2.1&w=1200&q=80" data-caption="Архітектура 1">
  <img src="https://images.unsplash.com/photo-1486718448742-163732cd1544?ixlib=rb-1.2.1&w=1200&q=80" data-caption="Архітектура 2">
  <img src="https://images.unsplash.com/photo-1485627941502-d2e6429fa8af?ixlib=rb-1.2.1&w=1200&q=80" data-caption="Велосипед">
  
</div>',
'image', '2024-01-02 12:00:00');


-- 3. ТЕГИ ТА КОМЕНТАРІ
-- ---------------------------------------------
INSERT INTO `tags` (`id`, `name`) VALUES (1, 'test'), (2, 'gallery');

INSERT INTO `post_tags` (`post_id`, `tag_id`) VALUES (1, 1), (2, 1), (2, 2);

INSERT INTO `comments` (`id`, `post_id`, `parent_id`, `author_name`, `content`) VALUES
(1, 2, NULL, 'User', 'Тепер працює стабільно?'),
(2, 2, 1, 'Admin', 'Так, фіксована пропорція допомагає.');

SET FOREIGN_KEY_CHECKS = 1;