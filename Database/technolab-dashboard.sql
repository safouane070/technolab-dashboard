-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 24 sep 2025 om 10:23
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
-- Tabelstructuur voor tabel `week_planning`
--

CREATE TABLE `week_planning` (
  `id` int(11) NOT NULL,
  `werknemer_id` int(11) NOT NULL,
  `weeknummer` int(11) NOT NULL,
  `jaar` int(11) NOT NULL,
  `dag` varchar(2) NOT NULL,
  `status` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') DEFAULT 'Afwezig'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `week_planning`
--

INSERT INTO `week_planning` (`id`, `werknemer_id`, `weeknummer`, `jaar`, `dag`, `status`) VALUES
(1, 26, 39, 2025, 'ma', 'Op de school'),
(2, 26, 39, 2025, 'di', 'Aanwezig'),
(3, 28, 39, 2025, 'di', 'Op de school'),
(4, 22, 39, 2025, 'di', 'Eefetjes Afwezig'),
(5, 23, 39, 2025, 'wo', 'Ziek'),
(6, 27, 39, 2025, 'wo', 'Op de school'),
(7, 23, 39, 2025, 'ma', 'Aanwezig'),
(8, 26, 39, 2025, 'do', 'Aanwezig'),
(9, 22, 39, 2025, 'do', 'Aanwezig'),
(10, 23, 39, 2025, 'do', 'Aanwezig'),
(11, 27, 39, 2025, 'do', 'Aanwezig'),
(12, 26, 39, 2025, 'vr', 'Op de school'),
(13, 26, 39, 2025, 'wo', 'Op de school'),
(14, 26, 40, 2025, 'di', 'Op de school'),
(15, 26, 40, 2025, 'wo', 'Aanwezig'),
(16, 22, 40, 2025, 'wo', 'Ziek'),
(17, 23, 40, 2025, 'di', 'Aanwezig'),
(18, 26, 39, 2025, 'di', 'Eefetjes Afwezig'),
(19, 26, 39, 2025, 'di', 'Afwezig'),
(20, 28, 40, 2025, 'di', 'Ziek'),
(21, 23, 40, 2025, 'do', 'Op de school'),
(22, 22, 41, 2025, 'wo', 'Op de school');

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
  `status` enum('Ziek','Aanwezig','Op de school','Afwezig','Eefetjes Afwezig') DEFAULT 'Aanwezig',
  `status_ma` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') DEFAULT 'Afwezig',
  `status_di` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') DEFAULT 'Afwezig',
  `status_wo` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') DEFAULT 'Afwezig',
  `status_do` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') DEFAULT 'Afwezig',
  `status_vr` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') DEFAULT 'Afwezig'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `werknemers`
--

INSERT INTO `werknemers` (`id`, `voornaam`, `tussenvoegsel`, `achternaam`, `email`, `werkdag_ma`, `werkdag_di`, `werkdag_wo`, `werkdag_do`, `werkdag_vr`, `sector`, `BHV`, `status`, `status_ma`, `status_di`, `status_wo`, `status_do`, `status_vr`) VALUES
(22, 'Anwer', 'de', 'Anwer', 'amr@technolableiden.nl', 1, 1, 1, 1, 1, 'ICT', 0, 'Aanwezig', 'Aanwezig', 'Afwezig', 'Op de school', 'Afwezig', 'Eefetjes Afwezig'),
(23, 'martijn', 'de', 'martijn', 'amr@technolableiden.nl', 1, 1, 0, 1, 0, 'Onderwijs', 1, 'Eefetjes Afwezig', 'Aanwezig', 'Afwezig', 'Op de school', 'Afwezig', 'Afwezig'),
(26, 'Amr', '', 'amr', 'amro@gmail.com', 1, 1, 1, 0, 1, 'Techniek', 0, 'Aanwezig', 'Ziek', 'Ziek', 'Aanwezig', 'Afwezig', 'Afwezig'),
(27, 'test', '', 'test', 'amro@gmail.com', 1, 1, 1, 1, 1, 'ICT', 1, 'Ziek', 'Eefetjes Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig'),
(28, 'Amr', '', 'Anwer', 'amro@gmail.com', 0, 1, 1, 1, 0, 'ICT', 1, 'Eefetjes Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `week_planning`
--
ALTER TABLE `week_planning`
  ADD PRIMARY KEY (`id`),
  ADD KEY `werknemer_id` (`werknemer_id`);

--
-- Indexen voor tabel `werknemers`
--
ALTER TABLE `werknemers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `week_planning`
--
ALTER TABLE `week_planning`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT voor een tabel `werknemers`
--
ALTER TABLE `werknemers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `week_planning`
--
ALTER TABLE `week_planning`
  ADD CONSTRAINT `week_planning_ibfk_1` FOREIGN KEY (`werknemer_id`) REFERENCES `werknemers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
