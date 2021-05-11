-- --------------------------------------------------------
-- Hostitel:                     127.0.0.1
-- Verze serveru:                10.4.11-MariaDB - mariadb.org binary distribution
-- OS serveru:                   Win64
-- HeidiSQL Verze:               11.0.0.5919
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Exportování struktury databáze pro
CREATE DATABASE IF NOT EXISTS `qaudit` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci */;
USE `qaudit`;

-- Exportování struktury pro tabulka qaudit.ab
CREATE TABLE IF NOT EXISTS `ab` (
  `model` varchar(100) COLLATE cp1250_czech_cs DEFAULT NULL,
  `color` varchar(30) COLLATE cp1250_czech_cs DEFAULT NULL,
  `code` varchar(3) COLLATE cp1250_czech_cs DEFAULT NULL,
  `asma` varchar(4) COLLATE cp1250_czech_cs DEFAULT NULL,
  `my16` varchar(6) COLLATE cp1250_czech_cs NOT NULL,
  `fa3d` varchar(12) COLLATE cp1250_czech_cs NOT NULL,
  PRIMARY KEY (`my16`,`fa3d`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1250 COLLATE=cp1250_czech_cs;

-- Export dat nebyl vybrán.

-- Exportování struktury pro tabulka qaudit.defects
CREATE TABLE IF NOT EXISTS `defects` (
  `iddefect` int(11) NOT NULL AUTO_INCREMENT,
  `idnok` int(11) NOT NULL DEFAULT 0,
  `idscan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`iddefect`),
  KEY `idscan` (`idscan`),
  KEY `iddefect_idnok_idscan` (`iddefect`,`idnok`,`idscan`),
  KEY `FK_defects_noklist` (`idnok`),
  CONSTRAINT `FK_defects_noklist` FOREIGN KEY (`idnok`) REFERENCES `noklist` (`idnok`),
  CONSTRAINT `FK_defects_scans` FOREIGN KEY (`idscan`) REFERENCES `scans` (`idscan`)
) ENGINE=InnoDB AUTO_INCREMENT=176846 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- Export dat nebyl vybrán.

-- Exportování struktury pro tabulka qaudit.noklist
CREATE TABLE IF NOT EXISTS `noklist` (
  `idnok` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `priority` int(11) NOT NULL,
  `active` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`idnok`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- Export dat nebyl vybrán.

-- Exportování struktury pro tabulka qaudit.operatorlog
CREATE TABLE IF NOT EXISTS `operatorlog` (
  `idoperatorlog` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `gate` varchar(2) COLLATE utf8_czech_ci NOT NULL DEFAULT '0',
  `operator` int(10) NOT NULL DEFAULT 0,
  `action` varchar(1) COLLATE utf8_czech_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`idoperatorlog`),
  KEY `time` (`time`),
  KEY `time_operator_action` (`time`,`operator`,`action`)
) ENGINE=InnoDB AUTO_INCREMENT=1257107 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- Export dat nebyl vybrán.

-- Exportování struktury pro tabulka qaudit.operators
CREATE TABLE IF NOT EXISTS `operators` (
  `idoperator` int(11) NOT NULL AUTO_INCREMENT,
  `login` char(4) COLLATE utf8_czech_ci NOT NULL DEFAULT '0',
  `name` varchar(30) COLLATE utf8_czech_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`idoperator`),
  KEY `idoperator` (`idoperator`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- Export dat nebyl vybrán.

-- Exportování struktury pro tabulka qaudit.pairs
CREATE TABLE IF NOT EXISTS `pairs` (
  `idpairs` int(11) NOT NULL AUTO_INCREMENT,
  `product1` int(11) NOT NULL DEFAULT 0,
  `product2` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`idpairs`),
  KEY `FK_pairs_products` (`product1`),
  KEY `FK_pairs_products_2` (`product2`),
  CONSTRAINT `FK_pairs_products` FOREIGN KEY (`product1`) REFERENCES `products` (`idproducts`),
  CONSTRAINT `FK_pairs_products_2` FOREIGN KEY (`product2`) REFERENCES `products` (`idproducts`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- Export dat nebyl vybrán.

-- Exportování struktury pro tabulka qaudit.products
CREATE TABLE IF NOT EXISTS `products` (
  `idproducts` int(11) NOT NULL AUTO_INCREMENT,
  `product` varchar(13) COLLATE utf8_czech_ci NOT NULL DEFAULT '0',
  `type` varchar(6) COLLATE utf8_czech_ci NOT NULL DEFAULT '0',
  `description` varchar(100) COLLATE utf8_czech_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`idproducts`),
  UNIQUE KEY `product` (`product`),
  KEY `idproducts_product_description` (`idproducts`,`product`,`description`),
  KEY `idproducts_type` (`idproducts`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=596 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- Export dat nebyl vybrán.

-- Exportování struktury pro tabulka qaudit.scans
CREATE TABLE IF NOT EXISTS `scans` (
  `idscan` int(11) NOT NULL AUTO_INCREMENT,
  `sdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `product` int(12) NOT NULL,
  `pserial` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `gate` char(2) COLLATE utf8_czech_ci NOT NULL,
  `state` char(1) COLLATE utf8_czech_ci NOT NULL,
  `operator` int(11) NOT NULL,
  PRIMARY KEY (`idscan`),
  KEY `sdate` (`sdate`),
  KEY `pserial_product_idscan_sdate` (`pserial`,`product`,`idscan`,`sdate`),
  KEY `FK_scans_products` (`product`),
  KEY `FK_scans_operators` (`operator`),
  CONSTRAINT `FK_scans_operators` FOREIGN KEY (`operator`) REFERENCES `operators` (`idoperator`),
  CONSTRAINT `FK_scans_products` FOREIGN KEY (`product`) REFERENCES `products` (`idproducts`)
) ENGINE=InnoDB AUTO_INCREMENT=1856059 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- Export dat nebyl vybrán.

-- Exportování struktury pro tabulka qaudit.users
CREATE TABLE IF NOT EXISTS `users` (
  `iduser` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) COLLATE utf8_czech_ci DEFAULT '0',
  `password` char(64) COLLATE utf8_czech_ci DEFAULT NULL,
  `name` varchar(30) COLLATE utf8_czech_ci DEFAULT '0',
  `rights` enum('L','A') COLLATE utf8_czech_ci DEFAULT 'L',
  PRIMARY KEY (`iduser`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- Export dat nebyl vybrán.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
