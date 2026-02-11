-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 16 jan 2026 om 09:27
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
(10, 'Technolab'),
(11, 'technoallday');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `employee`
--

CREATE TABLE `employee` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `middle_name` varchar(20) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `active` tinyint(1) DEFAULT 1,
  `sector` varchar(50) DEFAULT NULL,
  `bhv` tinyint(1) DEFAULT 0,
  `workday_mon` tinyint(1) DEFAULT 0,
  `workday_tue` tinyint(1) DEFAULT 0,
  `workday_wed` tinyint(1) DEFAULT 0,
  `workday_thu` tinyint(1) DEFAULT 0,
  `workday_fri` tinyint(1) DEFAULT 0,
  `status` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') DEFAULT 'Aanwezig',
  `status_mon` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') DEFAULT 'Afwezig',
  `status_tue` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') DEFAULT 'Afwezig',
  `status_wed` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') DEFAULT 'Afwezig',
  `status_thu` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') DEFAULT 'Afwezig',
  `status_fri` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') DEFAULT 'Afwezig',
  `temporarily_until` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `employee`
--

INSERT INTO `employee` (`id`, `name`, `middle_name`, `last_name`, `email`, `active`, `sector`, `bhv`, `workday_mon`, `workday_tue`, `workday_wed`, `workday_thu`, `workday_fri`, `status`, `status_mon`, `status_tue`, `status_wed`, `status_thu`, `status_fri`, `temporarily_until`, `created_at`) VALUES
(1, 'Anna', '', 'Jansen', 'anna.jansen@technolableiden.nl', 1, 'ICT', 1, 1, 1, 1, 1, 1, 'Eefetjes Afwezig', 'Aanwezig', 'Aanwezig', 'Aanwezig', 'Aanwezig', 'Aanwezig', NULL, '2026-01-12 08:11:23'),
(2, 'Robert', NULL, 'de Vries', 'robert.devries@technolableiden.nl', 1, 'Onderwijs', 0, 1, 1, 1, 0, 0, 'Eefetjes Afwezig', 'Aanwezig', 'Aanwezig', 'Aanwezig', 'Afwezig', 'Afwezig', NULL, '2026-01-12 08:11:23'),
(3, 'Chantal', '', 'Bakker', 'chantal.bakker@technolableiden.nl', 1, 'HR', 0, 0, 0, 1, 1, 1, 'Aanwezig', 'Ziek', 'Ziek', 'Ziek', 'Ziek', 'Afwezig', '2026-01-20 00:00:00', '2026-01-12 08:11:23'),
(4, 'Julian', NULL, 'Meijer', 'julian.meijer@technolableiden.nl', 1, 'Onderwijs', 0, 0, 1, 0, 1, 0, 'Eefetjes Afwezig', 'Afwezig', 'Op de school', 'Afwezig', 'Op de school', 'Afwezig', NULL, '2026-01-12 08:11:23'),
(5, 'Maaike', NULL, 'Smit', 'maaike.smit@technolableiden.nl', 1, 'Facilitair', 1, 1, 1, 0, 1, 1, 'Aanwezig', 'Aanwezig', 'Aanwezig', 'Afwezig', 'Aanwezig', 'Aanwezig', NULL, '2026-01-12 08:11:23'),
(6, 'Rosa', NULL, 'Koning', 'rosa.koning@technolableiden.nl', 1, 'Marketing', 0, 0, 0, 0, 0, 0, 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', NULL, '2026-01-12 08:11:23'),
(10, 'Amr', '', 'amr', 'amro@gmail.com', 1, 'HR', 0, 1, 1, 1, 1, 1, 'Aanwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', NULL, '2026-01-16 08:19:05'),
(11, 'Amr', NULL, 'amr', 'amr@technolableiden.nl', 1, 'ICT', 0, 1, 1, 1, 1, 1, 'Aanwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', 'Afwezig', NULL, '2026-01-16 08:22:52');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `scheduled_date` date DEFAULT NULL,
  `finalized_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `sessions`
--

INSERT INTO `sessions` (`id`, `created_at`, `scheduled_date`, `finalized_at`) VALUES
(37, '2025-12-10 11:37:21', NULL, NULL),
(39, '2026-01-13 13:03:50', '2026-01-14', '2026-01-13 14:06:48'),
(40, '2026-01-13 13:06:50', '2026-01-21', '2026-01-13 14:13:35'),
(41, '2026-01-13 13:13:36', NULL, NULL),
(42, '2026-01-13 13:20:52', '2026-01-28', '2026-01-13 14:21:21'),
(43, '2026-01-13 13:21:22', '2026-01-07', '2026-01-13 14:49:49'),
(44, '2026-01-13 13:49:50', NULL, NULL);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `session_country`
--

CREATE TABLE `session_country` (
  `id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `country_name` varchar(100) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `session_country`
--

INSERT INTO `session_country` (`id`, `session_id`, `country_name`, `added_at`) VALUES
(22, 40, 'Nederland', '2026-01-13 13:13:14');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `session_employees`
--

CREATE TABLE `session_employees` (
  `id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `session_employees`
--

INSERT INTO `session_employees` (`id`, `session_id`, `employee_id`, `added_at`) VALUES
(105, 40, 3, '2026-01-13 13:12:45'),
(106, 40, 7, '2026-01-13 13:12:49'),
(107, 40, 2, '2026-01-13 13:12:53'),
(108, 42, 1, '2026-01-13 13:21:03'),
(109, 43, 3, '2026-01-13 13:49:34');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `session_ingredients`
--

CREATE TABLE `session_ingredients` (
  `id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `ingredient_name` varchar(100) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `session_ingredients`
--

INSERT INTO `session_ingredients` (`id`, `session_id`, `ingredient_name`, `added_at`) VALUES
(26, 40, 'kaas', '2026-01-13 13:13:24');

-- --------------------------------------------------------

--
-- Stand-in structuur voor view `v_logbook`
-- (Zie onder voor de actuele view)
--
CREATE TABLE `v_logbook` (
`session_id` int(10) unsigned
,`scheduled_date` date
,`finalized_at` datetime
,`participants` mediumtext
,`country` varchar(100)
,`ingredients` mediumtext
);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `week_planning`
--

CREATE TABLE `week_planning` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `jaar` int(11) NOT NULL,
  `weeknummer` int(11) NOT NULL,
  `dag` enum('ma','di','wo','do','vr') NOT NULL,
  `status` enum('Aanwezig','Afwezig','Ziek','Op de school','Eefetjes Afwezig') NOT NULL DEFAULT 'Aanwezig',
  `tijdelijk_tot` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `week_planning`
--

INSERT INTO `week_planning` (`id`, `employee_id`, `jaar`, `weeknummer`, `dag`, `status`, `tijdelijk_tot`, `created_at`) VALUES
(1, 1, 2026, 2, 'ma', 'Aanwezig', NULL, '2026-01-12 08:30:45'),
(2, 1, 2026, 2, 'di', 'Aanwezig', NULL, '2026-01-12 08:30:45'),
(3, 1, 2026, 2, 'wo', 'Ziek', '2026-01-15 00:00:00', '2026-01-12 08:30:45'),
(4, 1, 2026, 2, 'do', 'Ziek', '2026-01-15 00:00:00', '2026-01-12 08:30:45'),
(5, 1, 2026, 2, 'vr', 'Aanwezig', NULL, '2026-01-12 08:30:45'),
(6, 2, 2026, 2, 'ma', 'Aanwezig', NULL, '2026-01-12 08:30:45'),
(7, 2, 2026, 2, 'di', 'Eefetjes Afwezig', NULL, '2026-01-12 08:30:45'),
(8, 1, 2026, 3, 'ma', 'Eefetjes Afwezig', '2026-01-13 16:01:00', '2026-01-12 08:49:07'),
(9, 2, 2026, 3, 'ma', 'Eefetjes Afwezig', NULL, '2026-01-12 08:49:07'),
(10, 3, 2026, 3, 'ma', 'Aanwezig', NULL, '2026-01-12 08:49:07'),
(11, 4, 2026, 3, 'ma', 'Afwezig', NULL, '2026-01-12 08:49:07'),
(12, 5, 2026, 3, 'ma', 'Afwezig', NULL, '2026-01-12 08:49:07'),
(13, 6, 2026, 3, 'ma', 'Afwezig', NULL, '2026-01-12 08:49:07'),
(16, 1, 2026, 3, 'di', 'Ziek', NULL, '2026-01-12 08:53:20'),
(17, 2, 2026, 3, 'di', 'Afwezig', NULL, '2026-01-12 08:53:20'),
(18, 3, 2026, 3, 'di', 'Afwezig', NULL, '2026-01-12 08:53:20'),
(19, 4, 2026, 3, 'di', 'Eefetjes Afwezig', '2026-01-14 11:03:00', '2026-01-12 08:53:20'),
(20, 5, 2026, 3, 'di', 'Ziek', NULL, '2026-01-12 08:53:20'),
(21, 6, 2026, 3, 'di', 'Afwezig', NULL, '2026-01-12 08:53:20'),
(22, 1, 2026, 3, 'wo', 'Aanwezig', NULL, '2026-01-12 08:53:22'),
(23, 2, 2026, 3, 'wo', 'Afwezig', NULL, '2026-01-12 08:53:22'),
(24, 3, 2026, 3, 'wo', 'Aanwezig', NULL, '2026-01-12 08:53:22'),
(25, 4, 2026, 3, 'wo', 'Afwezig', NULL, '2026-01-12 08:53:22'),
(26, 5, 2026, 3, 'wo', 'Aanwezig', NULL, '2026-01-12 08:53:22'),
(27, 6, 2026, 3, 'wo', 'Afwezig', NULL, '2026-01-12 08:53:22'),
(28, 1, 2026, 3, 'do', 'Afwezig', NULL, '2026-01-12 08:53:23'),
(29, 2, 2026, 3, 'do', 'Afwezig', NULL, '2026-01-12 08:53:23'),
(30, 3, 2026, 3, 'do', 'Afwezig', NULL, '2026-01-12 08:53:23'),
(31, 4, 2026, 3, 'do', 'Afwezig', NULL, '2026-01-12 08:53:23'),
(32, 5, 2026, 3, 'do', 'Afwezig', NULL, '2026-01-12 08:53:23'),
(33, 6, 2026, 3, 'do', 'Afwezig', NULL, '2026-01-12 08:53:23'),
(34, 1, 2026, 3, 'vr', 'Afwezig', NULL, '2026-01-12 08:53:24'),
(35, 2, 2026, 3, 'vr', 'Afwezig', NULL, '2026-01-12 08:53:24'),
(36, 3, 2026, 3, 'vr', 'Afwezig', NULL, '2026-01-12 08:53:24'),
(37, 4, 2026, 3, 'vr', 'Afwezig', NULL, '2026-01-12 08:53:24'),
(38, 5, 2026, 3, 'vr', 'Afwezig', NULL, '2026-01-12 08:53:24'),
(39, 6, 2026, 3, 'vr', 'Afwezig', NULL, '2026-01-12 08:53:24'),
(56, 3, 2026, 4, 'di', 'Ziek', NULL, '2026-01-12 12:40:55'),
(57, 2, 2026, 4, 'ma', 'Eefetjes Afwezig', NULL, '2026-01-12 12:40:59'),
(63, 1, 2026, 4, 'wo', 'Aanwezig', NULL, '2026-01-13 13:58:39'),
(64, 3, 2026, 4, 'wo', 'Aanwezig', NULL, '2026-01-13 13:58:52'),
(65, 4, 2026, 4, 'wo', 'Aanwezig', NULL, '2026-01-13 13:58:55'),
(66, 1, 2026, 7, 'wo', 'Aanwezig', NULL, '2026-01-13 13:59:11'),
(67, 3, 2026, 7, 'wo', 'Aanwezig', NULL, '2026-01-13 13:59:13'),
(68, 4, 2026, 7, 'wo', 'Aanwezig', NULL, '2026-01-13 13:59:16'),
(69, 5, 2026, 7, 'wo', 'Aanwezig', NULL, '2026-01-13 13:59:18'),
(70, 2, 2026, 7, 'wo', 'Aanwezig', NULL, '2026-01-13 13:59:21'),
(71, 6, 2026, 7, 'wo', 'Aanwezig', NULL, '2026-01-13 13:59:24'),
(73, 1, 2026, 7, 'do', 'Aanwezig', NULL, '2026-01-13 13:59:32'),
(74, 1, 2026, 7, 'di', 'Aanwezig', NULL, '2026-01-13 13:59:35'),
(75, 1, 2026, 4, 'di', 'Aanwezig', NULL, '2026-01-13 14:01:13'),
(76, 1, 2026, 4, 'do', 'Aanwezig', NULL, '2026-01-13 14:01:16'),
(78, 10, 2026, 3, 'vr', 'Afwezig', NULL, '2026-01-16 08:19:05'),
(79, 11, 2026, 3, 'vr', 'Afwezig', NULL, '2026-01-16 08:22:52'),
(80, 11, 2026, 4, 'di', 'Aanwezig', NULL, '2026-01-16 08:23:38'),
(81, 11, 2026, 4, 'wo', 'Aanwezig', NULL, '2026-01-16 08:23:40'),
(82, 11, 2026, 4, 'do', 'Aanwezig', NULL, '2026-01-16 08:23:43'),
(83, 11, 2026, 5, 'di', 'Aanwezig', NULL, '2026-01-16 08:23:47'),
(84, 11, 2026, 5, 'wo', 'Aanwezig', NULL, '2026-01-16 08:23:50'),
(85, 11, 2026, 5, 'do', 'Aanwezig', NULL, '2026-01-16 08:23:53'),
(86, 11, 2026, 6, 'di', 'Aanwezig', NULL, '2026-01-16 08:23:56'),
(87, 11, 2026, 6, 'wo', 'Aanwezig', NULL, '2026-01-16 08:23:59'),
(88, 11, 2026, 6, 'do', 'Aanwezig', NULL, '2026-01-16 08:24:01');

-- --------------------------------------------------------

--
-- Structuur voor de view `v_logbook`
--
DROP TABLE IF EXISTS `v_logbook`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_logbook`  AS SELECT `s`.`id` AS `session_id`, `s`.`scheduled_date` AS `scheduled_date`, `s`.`finalized_at` AS `finalized_at`, group_concat(concat(`e`.`name`) order by `e`.`name` ASC separator ', ') AS `participants`, `sc`.`country_name` AS `country`, (select group_concat(`si`.`ingredient_name` order by `si`.`ingredient_name` ASC separator ', ') from `session_ingredients` `si` where `si`.`session_id` = `s`.`id`) AS `ingredients` FROM (((`sessions` `s` left join `session_employees` `se` on(`se`.`session_id` = `s`.`id`)) left join `employee` `e` on(`e`.`id` = `se`.`employee_id`)) left join `session_country` `sc` on(`sc`.`session_id` = `s`.`id`)) WHERE `s`.`finalized_at` is not null OR `s`.`scheduled_date` is not null GROUP BY `s`.`id` ;

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_email` (`email`);

--
-- Indexen voor tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `session_country`
--
ALTER TABLE `session_country`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_sc_session` (`session_id`);

--
-- Indexen voor tabel `session_employees`
--
ALTER TABLE `session_employees`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexen voor tabel `session_ingredients`
--
ALTER TABLE `session_ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_si_session` (`session_id`);

--
-- Indexen voor tabel `week_planning`
--
ALTER TABLE `week_planning`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_employee_week_day` (`employee_id`,`jaar`,`weeknummer`,`dag`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT voor een tabel `employee`
--
ALTER TABLE `employee`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT voor een tabel `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT voor een tabel `session_country`
--
ALTER TABLE `session_country`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT voor een tabel `session_employees`
--
ALTER TABLE `session_employees`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT voor een tabel `session_ingredients`
--
ALTER TABLE `session_ingredients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT voor een tabel `week_planning`
--
ALTER TABLE `week_planning`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `session_country`
--
ALTER TABLE `session_country`
  ADD CONSTRAINT `fk_sc_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Beperkingen voor tabel `session_ingredients`
--
ALTER TABLE `session_ingredients`
  ADD CONSTRAINT `fk_si_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Beperkingen voor tabel `week_planning`
--
ALTER TABLE `week_planning`
  ADD CONSTRAINT `fk_week_planning_employee` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
