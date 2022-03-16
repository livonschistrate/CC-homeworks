-- --------------------------------------------------------
-- Host:                         www.univtt.ro
-- Server version:               10.3.31-MariaDB-0+deb10u1 - Debian 10
-- Server OS:                    debian-linux-gnu
-- HeidiSQL Version:             11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for cloud4
CREATE DATABASE IF NOT EXISTS `cloud4` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `cloud4`;

-- Dumping structure for table cloud4.books
CREATE TABLE IF NOT EXISTS `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `author` varchar(45) DEFAULT NULL,
  `pages` int(11) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table cloud4.books: ~1 rows (approximately)
/*!40000 ALTER TABLE `books` DISABLE KEYS */;
REPLACE INTO `books` (`id`, `name`, `author`, `pages`, `type`) VALUES
	(1, 'Amintiri din copilarie', 'Ion Creanga', 186, 'Stories'),
	(2, 'Fahrenheit 451', 'Ray Bradbury', 141, 'Fiction'),
	(3, 'The Art of War', 'Sun Tzu', 127, 'Self-improvement'),
	(4, 'The Political Philosophy of AI', 'Mark Coeckelbergh', 176, 'Philosophy'),
	(5, 'Object-Oriented Python', 'Irv Kalb', 450, 'Technology');
/*!40000 ALTER TABLE `books` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
