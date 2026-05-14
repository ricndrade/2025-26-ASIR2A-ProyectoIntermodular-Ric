-- Adminer 5.4.2 MariaDB 11.8.6-MariaDB-ubu2404 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP DATABASE IF EXISTS `museo`;
CREATE DATABASE `museo` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci */;
USE `museo`;

DROP TABLE IF EXISTS `photos`;
CREATE TABLE `photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `caption` varchar(220) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_public` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_photos_user` (`user_id`),
  CONSTRAINT `fk_photos_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

INSERT INTO `photos` (`id`, `user_id`, `caption`, `image_path`, `is_public`, `created_at`, `updated_at`) VALUES
(12,	3,	'escultura',	'photo_6a05a4106676f8.12260298.webp',	1,	'2026-05-14 10:29:36',	'2026-05-14 12:05:07'),
(13,	3,	'hola',	'photo_6a05a415cb1990.88580965.webp',	1,	'2026-05-14 10:29:41',	'2026-05-14 12:05:04');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `bio` varchar(160) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

INSERT INTO `users` (`id`, `username`, `display_name`, `email`, `password_hash`, `profile_image`, `bio`, `created_at`, `updated_at`) VALUES
(1,	'admin',	'Admin',	'admin@museo.local',	'$2y$10$7Uqj8L0VY08NP2s6KGudPOMY1FGxQWUEAW6aQXuX/LvfG/NaFxzf6',	NULL,	'',	'2026-05-14 00:33:56',	'2026-05-14 06:25:03'),
(3,	'ric',	'Ricardo',	'ric@example.com',	'$2y$10$248dGFMQ1R7EoeDFhJery.Vm0o32Ng1HRJsvDAD0UdQ1KPabCQzCu',	'avatar_3_6a05a9bca65ea.png',	'esto no es una biografía.',	'2026-05-14 01:35:31',	'2026-05-14 11:58:22');

-- 2026-05-14 12:14:18 UTC