-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 07 nov 2025 om 10:07
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
-- Tabelstructuur voor tabel `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `admins`
--

INSERT INTO `admins` (`id`, `password`) VALUES
(9, '1234'),
(10, 'Technolab'),
(11, 'technoallday');

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
  `status` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') DEFAULT 'Afwezig',
  `tijdelijk_tot` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `week_planning`
--

INSERT INTO `week_planning` (`id`, `werknemer_id`, `weeknummer`, `jaar`, `dag`, `status`, `tijdelijk_tot`) VALUES
(777, 43, 44, 2025, 'ma', 'Afwezig', NULL),
(778, 44, 44, 2025, 'ma', 'Aanwezig', NULL),
(780, 43, 44, 2025, 'wo', 'Ziek', NULL),
(781, 44, 44, 2025, 'wo', 'Ziek', NULL),
(784, 43, 44, 2025, 'do', 'Aanwezig', NULL),
(785, 44, 44, 2025, 'do', 'Eefetjes Afwezig', '2025-10-30 15:50:00'),
(787, 43, 44, 2025, 'di', 'Aanwezig', NULL),
(788, 44, 44, 2025, 'di', 'Aanwezig', NULL),
(793, 43, 46, 2025, 'di', 'Afwezig', NULL),
(804, 48, 44, 2025, 'wo', 'Ziek', NULL),
(806, 48, 44, 2025, 'do', 'Afwezig', NULL),
(809, 43, 44, 2025, 'vr', 'Aanwezig', NULL),
(810, 44, 44, 2025, 'vr', 'Aanwezig', NULL),
(812, 48, 44, 2025, 'vr', 'Aanwezig', NULL),
(825, 43, 47, 2025, 'wo', 'Eefetjes Afwezig', NULL),
(828, 52, 44, 2025, 'vr', 'Afwezig', NULL),
(833, 43, 45, 2025, 'ma', 'Aanwezig', NULL),
(834, 44, 45, 2025, 'ma', 'Aanwezig', NULL),
(836, 48, 45, 2025, 'ma', 'Aanwezig', NULL),
(837, 52, 45, 2025, 'ma', 'Afwezig', NULL),
(853, 43, 45, 2025, 'di', 'Aanwezig', NULL),
(854, 44, 45, 2025, 'di', 'Aanwezig', NULL),
(856, 48, 45, 2025, 'di', 'Afwezig', NULL),
(857, 52, 45, 2025, 'di', 'Afwezig', NULL),
(862, 43, 45, 2025, 'wo', 'Aanwezig', NULL),
(863, 44, 45, 2025, 'wo', 'Aanwezig', NULL),
(865, 48, 45, 2025, 'wo', 'Afwezig', NULL),
(866, 52, 45, 2025, 'wo', 'Afwezig', NULL),
(871, 43, 45, 2025, 'do', 'Aanwezig', NULL),
(872, 44, 45, 2025, 'do', 'Afwezig', NULL),
(874, 48, 45, 2025, 'do', 'Afwezig', NULL),
(875, 52, 45, 2025, 'do', 'Afwezig', NULL),
(880, 43, 45, 2025, 'vr', 'Aanwezig', NULL),
(881, 44, 45, 2025, 'vr', 'Aanwezig', NULL),
(883, 48, 45, 2025, 'vr', 'Aanwezig', NULL),
(884, 52, 45, 2025, 'vr', 'Afwezig', NULL),
(897, 57, 45, 2025, 'do', 'Aanwezig', NULL),
(898, 57, 45, 2025, 'vr', 'Afwezig', NULL);

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
  `sector` varchar(50) DEFAULT NULL,
  `BHV` tinyint(1) DEFAULT 0,
  `status` enum('Ziek','Aanwezig','Op de school','Afwezig','Eefetjes Afwezig') DEFAULT 'Aanwezig',
  `status_ma` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') DEFAULT 'Afwezig',
  `status_di` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') DEFAULT 'Afwezig',
  `status_wo` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') DEFAULT 'Afwezig',
  `status_do` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') DEFAULT 'Afwezig',
  `status_vr` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') DEFAULT 'Afwezig',
  `tijdelijk_tot` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `werknemers`
--

INSERT INTO `werknemers` (`id`, `voornaam`, `tussenvoegsel`, `achternaam`, `email`, `werkdag_ma`, `werkdag_di`, `werkdag_wo`, `werkdag_do`, `werkdag_vr`, `sector`, `BHV`, `status`, `status_ma`, `status_di`, `status_wo`, `status_do`, `status_vr`, `tijdelijk_tot`) VALUES
(43, 'Amr', '', 'amr', 'amr@technolableiden.nl', 0, 1, 1, 0, 1, 'TK', 0, 'Eefetjes Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', NULL),
(44, 'Anwer', '', 'martijn', 'amr@technolableiden.nl', 1, 1, 1, 0, 1, 'TK', 1, 'Eefetjes Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', NULL),
(48, 'Anwer', '', 'Anwer', 'amr@technolableiden.nl', 1, 0, 0, 0, 1, 'Keim', 1, 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', NULL),
(52, 'Anwer', '', 'Anwer', 'admin@example.com', 0, 0, 0, 0, 0, 'keim', 1, 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', NULL),
(57, 'Amr', 'de', 'martijn', '302920978@student.rocmondriaan.nl', 0, 0, 0, 1, 0, 'ICt', 1, 'Aanwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', NULL);

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `week_planning`
--
ALTER TABLE `week_planning`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_planning` (`werknemer_id`,`weeknummer`,`jaar`,`dag`);

--
-- Indexen voor tabel `werknemers`
--
ALTER TABLE `werknemers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT voor een tabel `week_planning`
--
ALTER TABLE `week_planning`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=899;

--
-- AUTO_INCREMENT voor een tabel `werknemers`
--
ALTER TABLE `werknemers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

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
