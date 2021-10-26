-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         5.7.33 - MySQL Community Server (GPL)
-- SO del servidor:              Win64
-- HeidiSQL Versión:             11.2.0.6213
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para symfofilms
DROP DATABASE IF EXISTS `symfofilms`;
CREATE DATABASE IF NOT EXISTS `symfofilms` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `symfofilms`;

-- Volcando estructura para tabla symfofilms.doctrine_migration_versions
DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla symfofilms.doctrine_migration_versions: ~3 rows (aproximadamente)
DELETE FROM `doctrine_migration_versions`;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
	('DoctrineMigrations\\Version20211025075943', '2021-10-25 10:19:46', 60),
	('DoctrineMigrations\\Version20211025082717', '2021-10-25 10:32:39', 78),
	('DoctrineMigrations\\Version20211025083921', '2021-10-25 10:39:31', 65);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;

-- Volcando estructura para tabla symfofilms.movie
DROP TABLE IF EXISTS `movie`;
CREATE TABLE IF NOT EXISTS `movie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `duracion` int(11) DEFAULT NULL,
  `director` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `genero` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla symfofilms.movie: ~10 rows (aproximadamente)
DELETE FROM `movie`;
/*!40000 ALTER TABLE `movie` DISABLE KEYS */;
INSERT INTO `movie` (`id`, `titulo`, `duracion`, `director`, `genero`) VALUES
	(1, 'Shine a Light', 128, 'Martin Scorsese', 'Music Live'),
	(2, 'Rocketman', 128, 'Dexter Fletcher', 'BioPic'),
	(3, 'Bohemian Rapsody', 128, 'Bryan Singer', 'BioPic'),
	(4, 'The Song Remains the Same', 128, 'Peter Clifton & Joe Massot', 'Music Live'),
	(5, 'The Last Waltz', 117, 'Martin Scorsese', 'Music Live'),
	(6, 'The Dirt', 107, 'Jeff Tremaine', 'BioPic'),
	(7, 'The Great Rock & Roll Swindle', 103, 'Julien Temple', 'BioPic'),
	(8, 'No Direction Home', 103, 'Martin Scorsese', 'Documentary'),
	(9, 'LennonNYC', 103, 'Michael Epstein', 'Documentary'),
	(10, 'Some Kind of Monster', 103, 'Joe Berlinger & Bruce Sinofsky', 'Documentary');
/*!40000 ALTER TABLE `movie` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
