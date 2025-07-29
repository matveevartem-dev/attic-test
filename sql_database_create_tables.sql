-- Создает таблицу пользователей
CREATE TABLE IF NOT EXISTS `identity` (
    `id` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
    `uid` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`),
UNIQUE KEY `identity_uid` (`uid`),
UNIQUE KEY `identity_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Создает таблицу постов
CREATE TABLE IF NOT EXISTS `post` (
    `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(200) COLLATE utf8mb4_general_ci,
    `body` TEXT COLLATE utf8mb4_general_ci,
PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`)
    REFERENCES `identity`(`uid`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Создает таблицу комментариев
CREATE TABLE IF NOT EXISTS `comment` (
    `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    `post_id` INT UNSIGNED NOT NULL,
    `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
    `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
    `body` text COLLATE utf8mb4_general_ci,
PRIMARY KEY (`id`),
FOREIGN KEY (`post_id`)
REFERENCES `post`(`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
-- Для поиска слова только в теле комментария
FULLTEXT KEY `body_ft` (`body`) WITH PARSER ngram 
-- Для поиска слова в заголовке и теле комментария
-- FULLTEXT KEY name_body_ft (`name`, `body`) WITH PARSER ngram 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Заполняет таблицу пользователей
DROP PROCEDURE IF EXISTS add_users;
DELIMITER $$
CREATE PROCEDURE add_users()
BEGIN
    DECLARE idx int unsigned;
    SET idx = 0;
    item: LOOP
        SET idx = idx +1;
        INSERT INTO `identity` (`id`, `email`, `password`)
        VALUES (UUID(), CONCAT("user", idx, "@e.mail"), LEFT(MD5(RAND()), 32));
        IF idx > (:max_user - 1) THEN
            LEAVE item;
        END IF;
    END LOOP item;
END $$
DELIMITER
CALL add_users();
