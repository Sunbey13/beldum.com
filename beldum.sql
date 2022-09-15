-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               8.0.24 - MySQL Community Server - GPL
-- Операционная система:         Win64
-- HeidiSQL Версия:              12.0.0.6468
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Дамп структуры базы данных beldum
CREATE DATABASE IF NOT EXISTS `beldum` /*!40100 DEFAULT CHARACTER SET utf8 */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `beldum`;

-- Дамп структуры для таблица beldum.car
CREATE TABLE IF NOT EXISTS `car` (
  `car_id` int NOT NULL AUTO_INCREMENT,
  `driver_id` int NOT NULL,
  `mark` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `model` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `number` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `carcass_w` float DEFAULT NULL,
  `carcass_h` float DEFAULT NULL,
  `carcass_l` float DEFAULT NULL,
  `carcass_weight` float DEFAULT NULL,
  PRIMARY KEY (`car_id`) USING BTREE,
  KEY `car_driver_id` (`driver_id`),
  CONSTRAINT `car_driver_id` FOREIGN KEY (`driver_id`) REFERENCES `driver` (`driver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Дамп данных таблицы beldum.car: ~2 rows (приблизительно)

-- Дамп структуры для таблица beldum.driver
CREATE TABLE IF NOT EXISTS `driver` (
  `driver_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `surname` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `last_name` varchar(30) DEFAULT NULL,
  `initials` varchar(20) NOT NULL,
  `phone` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `licenses` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `date_of_birth` date NOT NULL,
  `passport_series_number` varchar(10) NOT NULL,
  `passport_issue_date` date NOT NULL,
  `passport_issued_by` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `contract_number` varchar(15) NOT NULL,
  `contract_date` date NOT NULL,
  `ie_name` varchar(30) NOT NULL,
  `bank_iban` varchar(50) NOT NULL,
  `bank_bik` varchar(15) NOT NULL,
  PRIMARY KEY (`driver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Дамп данных таблицы beldum.driver: ~2 rows (приблизительно)

-- Дамп структуры для таблица beldum.organization
CREATE TABLE IF NOT EXISTS `organization` (
  `organ_id` int NOT NULL AUTO_INCREMENT,
  `spec` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `organ_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `organ_address` json NOT NULL,
  PRIMARY KEY (`organ_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Дамп данных таблицы beldum.organization: ~1 rows (приблизительно)

-- Дамп структуры для таблица beldum.poa_settings
CREATE TABLE IF NOT EXISTS `poa_settings` (
  `settings_id` int NOT NULL AUTO_INCREMENT,
  `poa_eng` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `poa_acc` varchar(30) NOT NULL,
  `bank_data` json DEFAULT NULL,
  PRIMARY KEY (`settings_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Дамп данных таблицы beldum.poa_settings: ~1 rows (приблизительно)
INSERT INTO `poa_settings` (`settings_id`, `poa_eng`, `poa_acc`, `bank_data`) VALUES
	(1, 'Не назначен', 'Не назначен', NULL);

-- Дамп структуры для таблица beldum.task
CREATE TABLE IF NOT EXISTS `task` (
  `task_id` int NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `task_type` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `organ_id` int NOT NULL,
  `organ_address` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `curator_organ` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `curator_organ_tel` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `customer_id` int NOT NULL,
  `cargo_name` text NOT NULL,
  `cargo_w` float DEFAULT NULL,
  `cargo_h` float DEFAULT NULL,
  `cargo_l` float DEFAULT NULL,
  `cargo_weight` float DEFAULT NULL,
  `curator_remeza` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `notes` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `driver` varchar(50) DEFAULT NULL,
  `state` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`task_id`),
  KEY `task_user_id` (`customer_id`),
  KEY `task_organ_id` (`organ_id`),
  CONSTRAINT `task_organ_id` FOREIGN KEY (`organ_id`) REFERENCES `organization` (`organ_id`),
  CONSTRAINT `task_user_id` FOREIGN KEY (`customer_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Дамп данных таблицы beldum.task: ~7 rows (приблизительно)

-- Дамп структуры для таблица beldum.task_list
CREATE TABLE IF NOT EXISTS `task_list` (
  `task_list_id` int NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `car_id` int NOT NULL,
  `tasks` json NOT NULL,
  `created_by` int NOT NULL,
  `route` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `task_list_state` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`task_list_id`),
  KEY `task_list_car_id` (`car_id`),
  KEY `task_list_created_by` (`created_by`),
  CONSTRAINT `task_list_car_id` FOREIGN KEY (`car_id`) REFERENCES `car` (`car_id`),
  CONSTRAINT `task_list_created_by` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Дамп данных таблицы beldum.task_list: ~4 rows (приблизительно)

-- Дамп структуры для таблица beldum.user
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `login` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `password` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `surname` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `last_name` varchar(30) DEFAULT NULL,
  `initials` varchar(30) DEFAULT NULL,
  `role` int NOT NULL DEFAULT '0',
  `permission_number` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `permission_date` date DEFAULT NULL,
  `driver_id` int DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Дамп данных таблицы beldum.user: ~8 rows (приблизительно)
INSERT INTO `user` (`user_id`, `login`, `password`, `name`, `surname`, `last_name`, `initials`, `role`, `permission_number`, `permission_date`, `driver_id`) VALUES
	(1, 'admin', '$2y$10$ziJPb.fHmCOo1OesnK3bwuZaHppS5vEGLeI/iXvT7Xu65knsshLUq', 'admin', NULL, NULL, NULL, 0, NULL, NULL, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
