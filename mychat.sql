-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 18. Okt 2019 um 17:44
-- Server-Version: 10.1.40-MariaDB
-- PHP-Version: 7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `mychat`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_friendlist`
--

CREATE TABLE `mc_friendlist` (
  `fr_id` int(11) NOT NULL,
  `usr_id1` int(11) NOT NULL,
  `usr_id2` int(11) NOT NULL,
  `fr_since` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `mc_friendlist`
--

INSERT INTO `mc_friendlist` (`fr_id`, `usr_id1`, `usr_id2`, `fr_since`) VALUES
(1, 2, 1, '2019-10-18 15:19:12');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_groups`
--

CREATE TABLE `mc_groups` (
  `group_id` int(11) NOT NULL COMMENT 'ID of the group',
  `group_name` text COLLATE utf8_bin NOT NULL COMMENT 'Name of the group',
  `group_privacy` text COLLATE utf8_bin NOT NULL COMMENT 'Privacy Setting of Group (public/private)',
  `group_createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time of creation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `mc_groups`
--

INSERT INTO `mc_groups` (`group_id`, `group_name`, `group_privacy`, `group_createdAt`) VALUES
(1, 'fachsimpelnGroup', 'private', '2019-10-09 19:35:08');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_group_user`
--

CREATE TABLE `mc_group_user` (
  `gu_id` int(11) NOT NULL,
  `usr_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `gu_admin` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `mc_group_user`
--

INSERT INTO `mc_group_user` (`gu_id`, `usr_id`, `group_id`, `gu_admin`) VALUES
(1, 1, 1, 0),
(2, 2, 1, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_logins`
--

CREATE TABLE `mc_logins` (
  `login_id` int(11) NOT NULL,
  `login_userIdentifier` text COLLATE utf8_bin NOT NULL,
  `login_token` text COLLATE utf8_bin NOT NULL,
  `login_expires` text COLLATE utf8_bin,
  `usr_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `mc_logins`
--

INSERT INTO `mc_logins` (`login_id`, `login_userIdentifier`, `login_token`, `login_expires`, `usr_id`) VALUES
(5, '0cf26d972cccdd318844e0326c5f421b89fb75b273853f77d289c67da6d233f8', '72a719aebbd93b8eff3bc81a9368208a888c96836325c13bf2347da98b8dc95dc2deaafb811603d2ae22890b7bd73b0a4f7940ff4ed02e03fbe2ae1c9dfdcee69a736c9f78049dd8afd764de19e89ace1cd2f22c7ec141554f83418b35766c0f7c9dfccc8a3c3130eb44485007292531709bd89c93f67b6d39d2ca8ed203d9a387e36f79aae3ed313d6cf0e4f8bb2a10a9e41d61af9c3cf3155ef734724d964b4d65280f291d1d3b3392bd6a6358f508e4e9b8a99eb1988ea8cdce5ac4b6c16ce694975c8b2d661f2e444ea2b35de956ac2d6d2062a9a6d099c3144bdcaaf9c8985c3194c0e4d3e560db4cab71c6dd3c7cf1c4d66a91430bed99ba7010c9c35d2d173b11636ab13ef686ab3a3c31a381389ba4eceb6dad67503cd739559b67916a0ceea3a24e804c65288deb15c1222507d863b34161a5545c08b36d50cdc083d315932b6ba4b772873c029e64bc8b1b0800ccac253ece7a6e4f2c7e0aef6014700a84b4961f01f806eb0c368b325bb16e3da057d9d98fe837f79ad93cea725eb12761f491100acb24f884b369516689f03eeef4581cf2b436a68bcdc248e631f55b8b09cd171a3b96d272a9ba148a580030964dbdd0f5002cb9b32eb9a33d44b62e00fa27094b54ae994f08138799a0ade90ae9085d7758fe6e556adb3774fe87dcf297e9ca89b7749cdf37a35656586d2193fa69f58374e7955f1b02ece60d', '1570733064', 1),
(6, '06cda36a4449813c67f066557bad74b53a4ffd351d1ed1b6eb593f160cf77ef4', '0877cdcf9847bc862bd718fe0e64c5c6329178e92dee8435ab2bef6f0729effeeefe6fb584933c8f3ba0c66cd14c80fa059b596dc673905791f744dbc4e3a4d51596c94f330612d6cf57c890019ab02321873ca2ae6b2a4e349c153a5ada5b2f824f655330538a45e07229728fc49d0dc2415a781e4180bda0be0059d867a5bee36149fcdb18e55a00bfbfc42780c42763c1f472cedd6c1e5feb7233988be3bb61e6e2065795fa75e70254dbb3eb4e631d6dde926916b980ee310770fdfb325c0430a6b3809633001bb5314efa8c4b280b8097c2c1076d5bc1473893b44dbc628a20178d97da7b03d2c4f2705b10f0c014c4df6abdeccd68b8c4b63c0a43dfed78350cc24d9e5430f68ac404610ba7c7f18726ff3b21efa41c7204f086279553b400b0684401eee7744d12608e3f755c59e9f5367c64526d9299204d9e9a4478efebf7ce763995bbe95d564530a9d33fb9b0ac68decd9591e7f6d31cee559b9492a13809677a8e9c6e59bc2ce5b448ef938855c4155ffe9ba1c2eba4ee731e85570bde2a4c75f6377439f9f93a677aceb62b5b75e8b6958830e399c95a826fc26132811bcbd60d2e116f315e1fce51198cd89bd054d238b43b0640fd5381839606cb11053abcc7a48557149dca5d656c0e61329f4488c7d794a7d309ee4d9bd6b00d2f418a3fbbf37567b4f4b321fbe87ec3136be3a4b865194c47dcc0870cc5', '1570733252', 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_users`
--

CREATE TABLE `mc_users` (
  `usr_id` int(11) NOT NULL,
  `usr_username` varchar(200) COLLATE utf8_bin NOT NULL,
  `usr_email` varchar(200) COLLATE utf8_bin NOT NULL,
  `usr_password` text COLLATE utf8_bin NOT NULL,
  `usr_createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `mc_users`
--

INSERT INTO `mc_users` (`usr_id`, `usr_username`, `usr_email`, `usr_password`, `usr_createdAt`) VALUES
(1, 'fachsimpeln', 'fachsimpeln@example.com', '$2y$10$Sztgk9lAZ3A5M0CGwDIfre8a0q.DBXsUMKXm8JqwWtH32BrnZVk9u', '2019-10-09 17:49:32'),
(2, 'Administrator', 'admin@example.com', '$2y$10$dPOd2JPqiABg.u0UupJe.Onabn6S0sNVKj7AdOViNAyr0.ujjYY.W', '2019-10-09 19:57:29');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `mc_friendlist`
--
ALTER TABLE `mc_friendlist`
  ADD PRIMARY KEY (`fr_id`),
  ADD KEY `usr_id1` (`usr_id1`),
  ADD KEY `usr_id2` (`usr_id2`);

--
-- Indizes für die Tabelle `mc_groups`
--
ALTER TABLE `mc_groups`
  ADD PRIMARY KEY (`group_id`);

--
-- Indizes für die Tabelle `mc_group_user`
--
ALTER TABLE `mc_group_user`
  ADD PRIMARY KEY (`gu_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `usr_id` (`usr_id`) USING BTREE;

--
-- Indizes für die Tabelle `mc_logins`
--
ALTER TABLE `mc_logins`
  ADD PRIMARY KEY (`login_id`),
  ADD UNIQUE KEY `userIdentifier` (`login_userIdentifier`(100)),
  ADD KEY `usr_id` (`usr_id`);

--
-- Indizes für die Tabelle `mc_users`
--
ALTER TABLE `mc_users`
  ADD PRIMARY KEY (`usr_id`),
  ADD UNIQUE KEY `usr_username` (`usr_username`),
  ADD UNIQUE KEY `usr_email` (`usr_email`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `mc_friendlist`
--
ALTER TABLE `mc_friendlist`
  MODIFY `fr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `mc_groups`
--
ALTER TABLE `mc_groups`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of the group', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `mc_group_user`
--
ALTER TABLE `mc_group_user`
  MODIFY `gu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `mc_logins`
--
ALTER TABLE `mc_logins`
  MODIFY `login_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle `mc_users`
--
ALTER TABLE `mc_users`
  MODIFY `usr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `mc_friendlist`
--
ALTER TABLE `mc_friendlist`
  ADD CONSTRAINT `mc_friendlist_ibfk_1` FOREIGN KEY (`usr_id1`) REFERENCES `mc_users` (`usr_id`),
  ADD CONSTRAINT `mc_friendlist_ibfk_2` FOREIGN KEY (`usr_id2`) REFERENCES `mc_users` (`usr_id`);

--
-- Constraints der Tabelle `mc_group_user`
--
ALTER TABLE `mc_group_user`
  ADD CONSTRAINT `GroupID` FOREIGN KEY (`group_id`) REFERENCES `mc_groups` (`group_id`),
  ADD CONSTRAINT `UserID` FOREIGN KEY (`usr_id`) REFERENCES `mc_users` (`usr_id`);

--
-- Constraints der Tabelle `mc_logins`
--
ALTER TABLE `mc_logins`
  ADD CONSTRAINT `mc_logins_ibfk_1` FOREIGN KEY (`usr_id`) REFERENCES `mc_users` (`usr_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
