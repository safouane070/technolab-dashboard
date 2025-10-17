-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 17 okt 2025 om 11:33
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
  `status` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') DEFAULT 'Afwezig',
  `tijdelijk_tot` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `week_planning`
--

INSERT INTO `week_planning` (`id`, `werknemer_id`, `weeknummer`, `jaar`, `dag`, `status`, `tijdelijk_tot`) VALUES
(39, 29, 40, 2025, 'di', 'Op de school', NULL),
(42, 29, 40, 2025, 'vr', 'Op de school', NULL),
(47, 29, 40, 2025, 'do', 'Op de school', NULL),
(240, 29, 41, 2025, 'ma', 'Op de school', NULL),
(304, 29, 41, 2025, 'di', 'Op de school', NULL),
(353, 29, 41, 2025, 'we', 'Eefetjes Afwezig', NULL),
(375, 29, 41, 2025, 'do', 'Eefetjes Afwezig', NULL),
(400, 29, 41, 2025, 'wo', 'Eefetjes Afwezig', NULL),
(443, 29, 42, 2025, 'di', 'Ziek', NULL),
(449, 29, 43, 2025, 'ma', 'Afwezig', NULL),
(451, 29, 51, 2025, 'ma', 'Afwezig', NULL),
(460, 29, 41, 2025, 'vr', 'Aanwezig', NULL),
(473, 29, 42, 2025, 'ma', 'Ziek', NULL),
(581, 29, 42, 2025, 'wo', 'Aanwezig', NULL),
(585, 29, 42, 2025, 'vr', 'Afwezig', NULL),
(640, 32, 42, 2025, 'wo', 'Aanwezig', NULL),
(642, 32, 42, 2025, 'do', 'Afwezig', NULL),
(644, 32, 42, 2025, 'vr', 'Eefetjes Afwezig', NULL),
(663, 37, 42, 2025, 'wo', 'Afwezig', NULL),
(664, 38, 42, 2025, 'wo', 'Afwezig', NULL),
(665, 38, 42, 2025, 'do', 'Afwezig', NULL),
(666, 37, 42, 2025, 'do', 'Afwezig', NULL),
(669, 38, 42, 2025, 'vr', 'Eefetjes Afwezig', NULL),
(670, 37, 42, 2025, 'vr', 'Ziek', NULL),
(677, 38, 42, 2025, 'di', 'Eefetjes Afwezig', NULL),
(679, 32, 42, 2025, 'di', 'Eefetjes Afwezig', NULL),
(680, 37, 42, 2025, 'di', 'Eefetjes Afwezig', NULL),
(682, 38, 42, 2025, 'ma', 'Ziek', NULL),
(683, 32, 42, 2025, 'ma', 'Ziek', NULL),
(684, 37, 42, 2025, 'ma', 'Ziek', NULL),
(686, 32, 44, 2025, 'ma', 'Aanwezig', NULL),
(705, 29, 42, 2025, 'do', 'Afwezig', NULL),
(712, 40, 42, 2025, 'vr', 'Aanwezig', NULL),
(713, 41, 42, 2025, 'vr', 'Afwezig', NULL),
(715, 40, 42, 2025, 'ma', 'Aanwezig', NULL),
(716, 41, 42, 2025, 'ma', 'Aanwezig', NULL),
(717, 40, 42, 2025, 'di', 'Aanwezig', NULL),
(718, 41, 42, 2025, 'di', 'Afwezig', NULL),
(719, 40, 42, 2025, 'wo', 'Aanwezig', NULL),
(720, 41, 42, 2025, 'wo', 'Afwezig', NULL);

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
  `status_vr` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') DEFAULT 'Afwezig',
  `tijdelijk_tot` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `werknemers`
--

INSERT INTO `werknemers` (`id`, `voornaam`, `tussenvoegsel`, `achternaam`, `email`, `werkdag_ma`, `werkdag_di`, `werkdag_wo`, `werkdag_do`, `werkdag_vr`, `sector`, `BHV`, `status`, `status_ma`, `status_di`, `status_wo`, `status_do`, `status_vr`, `tijdelijk_tot`) VALUES
(29, 'martijn', 'de', 'amr', 'amro@gmail.com', 1, 1, 1, 0, 1, 'ICT', 1, 'Eefetjes Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', NULL),
(32, 'Robert', '', 'Commerell', 'amr@technolableiden.nl', 0, 1, 1, 1, 1, 'ICT', 1, 'Aanwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', NULL),
(37, 'martijn', '', 'martijn', 'amr@technolableiden.nl', 1, 1, 0, 1, 1, 'ICT', 0, 'Aanwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', NULL),
(38, 'martijn', '', 'Anwer', 'amr@technolableiden.nl', 1, 1, 0, 1, 1, 'ICT', 0, 'Aanwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', NULL),
(40, 'Amr', '', 'amr', 'amro@gmail.com', 1, 1, 1, 1, 0, 'ICT', 1, 'Aanwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', NULL),
(41, 'Anwer', '', 'Commerell', 'amr@technolableiden.nl', 1, 0, 0, 0, 0, 'ICT', 1, 'Aanwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', NULL);

--
-- Indexen voor geëxporteerde tabellen
--

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
-- AUTO_INCREMENT voor een tabel `week_planning`
--
ALTER TABLE `week_planning`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=721;

--
-- AUTO_INCREMENT voor een tabel `werknemers`
--
ALTER TABLE `werknemers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

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
