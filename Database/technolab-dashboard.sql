-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 18 sep 2025 om 14:47
-- Serverversie: 10.4.32-MariaDB
-- PHP-versie: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `technolab-dashboard`
--
CREATE DATABASE IF NOT EXISTS `technolab-dashboard` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `technolab-dashboard`;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `werknemers`
--

CREATE TABLE `werknemers` (
  `id` int(11) NOT NULL,
  `voornaam` varchar(50) NOT NULL,
  `tussenvoegsel` varchar(20) DEFAULT NULL,
  `achternaam` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `werkdag_ma` tinyint(1) DEFAULT 0,
  `werkdag_di` tinyint(1) DEFAULT 0,
  `werkdag_wo` tinyint(1) DEFAULT 0,
  `werkdag_do` tinyint(1) DEFAULT 0,
  `werkdag_vr` tinyint(1) DEFAULT 0,
  `sector` varchar(50) NOT NULL,
  `BHV` tinyint(1) DEFAULT 0,
  `status` enum('Ziek','Aanwezig','Op de school','Afwezig','Eefetjes Afwezig') DEFAULT 'Aanwezig'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `werknemers`
--

INSERT INTO `werknemers` (`id`, `voornaam`, `tussenvoegsel`, `achternaam`, `email`, `werkdag_ma`, `werkdag_di`, `werkdag_wo`, `werkdag_do`, `werkdag_vr`, `sector`, `BHV`, `status`) VALUES
(22, 'Anwer', 'de', 'Anwer', 'amr@technolableiden.nl', 1, 1, 1, 1, 1, 'ICT', 0, 'Ziek'),
(23, 'martijn', 'de', 'martijn', 'amr@technolableiden.nl', 1, 1, 0, 1, 0, 'Onderwijs', 1, 'Aanwezig'),
(26, 'Amr', '', 'amr', 'amro@gmail.com', 1, 1, 1, 0, 1, 'Techniek', 0, 'Ziek'),
(27, 'test', '', 'test', 'amro@gmail.com', 1, 1, 1, 1, 1, 'ICT', 1, 'Aanwezig');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `werknemers`
--
ALTER TABLE `werknemers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `werknemers`
--
ALTER TABLE `werknemers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
