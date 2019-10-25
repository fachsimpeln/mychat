-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 25. Okt 2019 um 22:58
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
-- Tabellenstruktur für Tabelle `mc_devicecookies`
--

CREATE TABLE `mc_devicecookies` (
  `dc_id` int(11) NOT NULL COMMENT 'ID of devicecookie',
  `dc_token` text COLLATE utf8_bin NOT NULL COMMENT 'DeviceCookie itself',
  `dc_attempts` int(11) NOT NULL DEFAULT '0' COMMENT 'Attempts on using this devicecookie',
  `dc_locked_until` timestamp NULL DEFAULT NULL COMMENT 'After x tries, locked until TimeStamp',
  `usr_id` int(11) NOT NULL COMMENT 'Forgein Key for user id from mc_users'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `mc_devicecookies`
--

INSERT INTO `mc_devicecookies` (`dc_id`, `dc_token`, `dc_attempts`, `dc_locked_until`, `usr_id`) VALUES
(3, 'b0512898c9d1c471c16b5e2d177764b2c32cc5c6183213c51a438cc98f23bff48d6244f750f76b83e4b41af35f5574ca4bff334088ab53447da08d98603d2d6b', 1, NULL, 1),
(4, 'c543d3c56c8412a9b69179db7d069f7f27628493931112dec1b245393c66b9b630f5daea586bc37e238411b00b82a878232daa96fa9958da5a8a48096e9f0193', 2, NULL, 1),
(5, '6f666c6d0f0ae2e85b726c87b66432a58bc84d1a402907ee56ea00749e93534912be1e189840e4ee452aa0d2b74cc1968ebbdd55db627a9c5a2cffcc28063875', 0, NULL, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_friendlist`
--

CREATE TABLE `mc_friendlist` (
  `fr_id` int(11) NOT NULL COMMENT 'ID of friendship',
  `usr_id1` int(11) NOT NULL COMMENT 'First friend (creator of friend request)',
  `usr_id2` int(11) NOT NULL COMMENT 'Second user',
  `fr_accepted` int(11) NOT NULL DEFAULT '0' COMMENT 'Friendship accepted by second user',
  `fr_since` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp when friendship was created'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `mc_friendlist`
--

INSERT INTO `mc_friendlist` (`fr_id`, `usr_id1`, `usr_id2`, `fr_accepted`, `fr_since`) VALUES
(1, 2, 1, 1, '2019-10-18 15:19:12');

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
  `gu_id` int(11) NOT NULL COMMENT 'ID of Group-User',
  `usr_id` int(11) NOT NULL COMMENT 'User ID of user in group',
  `group_id` int(11) NOT NULL COMMENT 'Group ID of Group the user is in',
  `gu_admin` int(11) NOT NULL DEFAULT '0' COMMENT 'User has admin privileges in group'
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
  `login_id` int(11) NOT NULL COMMENT 'Internal session id',
  `login_userIdentifier` text COLLATE utf8_bin NOT NULL COMMENT 'User Identifier instead of usr_id or username (timing attacks)',
  `login_token` text COLLATE utf8_bin NOT NULL COMMENT '512bit Token for authentication',
  `login_expires` text COLLATE utf8_bin COMMENT 'Session expires on timestamp',
  `usr_id` int(11) NOT NULL COMMENT 'ID of the User'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `mc_logins`
--

INSERT INTO `mc_logins` (`login_id`, `login_userIdentifier`, `login_token`, `login_expires`, `usr_id`) VALUES
(5, '0cf26d972cccdd318844e0326c5f421b89fb75b273853f77d289c67da6d233f8', '72a719aebbd93b8eff3bc81a9368208a888c96836325c13bf2347da98b8dc95dc2deaafb811603d2ae22890b7bd73b0a4f7940ff4ed02e03fbe2ae1c9dfdcee69a736c9f78049dd8afd764de19e89ace1cd2f22c7ec141554f83418b35766c0f7c9dfccc8a3c3130eb44485007292531709bd89c93f67b6d39d2ca8ed203d9a387e36f79aae3ed313d6cf0e4f8bb2a10a9e41d61af9c3cf3155ef734724d964b4d65280f291d1d3b3392bd6a6358f508e4e9b8a99eb1988ea8cdce5ac4b6c16ce694975c8b2d661f2e444ea2b35de956ac2d6d2062a9a6d099c3144bdcaaf9c8985c3194c0e4d3e560db4cab71c6dd3c7cf1c4d66a91430bed99ba7010c9c35d2d173b11636ab13ef686ab3a3c31a381389ba4eceb6dad67503cd739559b67916a0ceea3a24e804c65288deb15c1222507d863b34161a5545c08b36d50cdc083d315932b6ba4b772873c029e64bc8b1b0800ccac253ece7a6e4f2c7e0aef6014700a84b4961f01f806eb0c368b325bb16e3da057d9d98fe837f79ad93cea725eb12761f491100acb24f884b369516689f03eeef4581cf2b436a68bcdc248e631f55b8b09cd171a3b96d272a9ba148a580030964dbdd0f5002cb9b32eb9a33d44b62e00fa27094b54ae994f08138799a0ade90ae9085d7758fe6e556adb3774fe87dcf297e9ca89b7749cdf37a35656586d2193fa69f58374e7955f1b02ece60d', '1570733064', 1),
(6, '06cda36a4449813c67f066557bad74b53a4ffd351d1ed1b6eb593f160cf77ef4', '0877cdcf9847bc862bd718fe0e64c5c6329178e92dee8435ab2bef6f0729effeeefe6fb584933c8f3ba0c66cd14c80fa059b596dc673905791f744dbc4e3a4d51596c94f330612d6cf57c890019ab02321873ca2ae6b2a4e349c153a5ada5b2f824f655330538a45e07229728fc49d0dc2415a781e4180bda0be0059d867a5bee36149fcdb18e55a00bfbfc42780c42763c1f472cedd6c1e5feb7233988be3bb61e6e2065795fa75e70254dbb3eb4e631d6dde926916b980ee310770fdfb325c0430a6b3809633001bb5314efa8c4b280b8097c2c1076d5bc1473893b44dbc628a20178d97da7b03d2c4f2705b10f0c014c4df6abdeccd68b8c4b63c0a43dfed78350cc24d9e5430f68ac404610ba7c7f18726ff3b21efa41c7204f086279553b400b0684401eee7744d12608e3f755c59e9f5367c64526d9299204d9e9a4478efebf7ce763995bbe95d564530a9d33fb9b0ac68decd9591e7f6d31cee559b9492a13809677a8e9c6e59bc2ce5b448ef938855c4155ffe9ba1c2eba4ee731e85570bde2a4c75f6377439f9f93a677aceb62b5b75e8b6958830e399c95a826fc26132811bcbd60d2e116f315e1fce51198cd89bd054d238b43b0640fd5381839606cb11053abcc7a48557149dca5d656c0e61329f4488c7d794a7d309ee4d9bd6b00d2f418a3fbbf37567b4f4b321fbe87ec3136be3a4b865194c47dcc0870cc5', '1570733252', 2),
(7, '3d9b65ecaf0293118c1d72530b2c6759b1d556a83c5351a3b78232865a06f590', 'd4573ecf512382aa8878bae30112edc67bd2cb0418f103787d804112f57c4a155b7d0f44035f79718a846308f42aa186133a0dfbe1300b3c560d80de8c23410ecc396710303f0e74cffdbe7eb18ee1eec002d805d80e2217fc200ce498d559bcba34c667791f5cda0423aacfd2fd88c93a523287a74c52bc1260f49101fb2934efd195d205139b0603e6eec1a97d408bd19f740f7c0a63c09f2f5fe74898882526b5ba0ce5716e3de52061bbb84943bf8eeaa85210a3f832cb1b2f0b0a47705be26c4b971d4040e1637d416b00c3edcfd9d5f44e3d20849ef11b00d5397b365e619ef50a38881a077222dfe617ac8745dcac44d4008c1f6c8297fcc9039143d6ca6020a02e57bdc9eb47df59a711937db7d2ad1d3fb5592ea533c0e1cdb82f88d29bc4f1162e245ad5c2d811511a768fe3302b92677d05ffdb67f33966217561c268cc948ef5eba9387deee140a3c26b2214d4ba582887ee090f7e0dcb78fcbd23a8c91dd59d56eeee0572ac7ebbdb023e431d3be6145b69dddf7cd17471aa5ac8a486a226cff11cbfe4664cb0cb2cf3d1594708f76319592dd63e692ff42d78e27f61d277878c14f2f9c046c435a530f34504aeeeafb9fec10d1916b4105810696fe122d213f6d61fe1f157ebeb11e8afb97facf449530bb84ba128674d0b56a7a4360cd027b663310a14650754c7f27e05576a7fda72fb2e06bd216601a3ef', '1571766599', 1),
(8, '736360ef333c3b7509e5978e69389e4afd2c97ea34a9d52455bd191aa7e768df', 'b1cd67dca5438ac3f4b47749478053222cc988ace6dc6472d3d6471cc5d4fd73abbb6b2b5d1bff4ecff7dc0d051436e49002975be6f9701765b31951f7e3e9c603a4622f41512265493f05d81a645acfe4ee9b3050fae93788ea1598ab615434b63ce7166c879396ac8c7560e875c79363444233c0fc0fc41a3e7ff2014994da928c1fb2bde6e908b469f12a50f37b38de93bfa5e7ea00c32c831b6cf5ce76fceb2c34664f76c6457e77a3539d72803795f3e7eb3b0cb94b525ae47190de7efc96a5c6381837253746f816939dab893c552f5933d462e67a9c818ff16338a37dde455f6f0dcdc5e01dcf38ac62ae51575075987aeaddd600d29ad2c6fbdce360ac20e520ce8bfcb2faf60df2779959fe4a75db7eeeebc74a4bc078b89d2b81c3fa39bed061355d4924a92d62e1ce5009983f19beaf9b5a7f08a5d035f4629d1a89ef8d3e3732a26c2e8d8dc0e99a6bf9dc2a2cd7c98d8b2efc4e59b3ae094a0119051624035304dc1989bbbf96fb2c55a77fca212fd6eea9dd6aef8ac58eacc3ec409fd60092e54ade081ca87d6d8912d6d08f574b23c4433e9648cc457d02d8647283ca59c1694f0cda02b7f5406db82caae548c9116b604ec1c2fef2587874d4fa2e0476b97a300e52171704b604348a0b59ae915e0c735a349ed725e17ab7395d6c6d01979beee37dbe30cfe72d93ef0858c229fe50036719c15af8d54136', '1571766916', 1),
(9, '39be2c982f3cc0b26e5d9b3902317ec1850b229cf063f504ca72583a3f11b652', 'e0c2ec7cbda95cb898fa14252454bf4050b3098017d71c433acff37fbca9a7f6aa807fe322dfb82226d02f54f8f207163837a7a81f842d60539e3f3ee360114b6df436f519bdf800a8af93cd9d498861e665e8ab52e739a7cfb6cf91c6e454e7059581b98e76dce5b60306b6d3814c3c96ee39ac59d424b3119d6d78e310b9532111afb069f272aa2efbcec4c6877d1bea170811411942c9af367194ed2f9e30295bbb5180a2fe92d739a4153e0e685b437ac3b88818beaa7428aa38e1c2f7b4a7fd1eb94919a4e8aebc89fba6b54bcd2e5d10ca19fad8856b80224a9a09bc75d79114baa65d7570c1e2334dd69ceca1c99d32ffde40d65cc9490b96d273c14aa57367aec42a4405d3b0ebe8b08ee0823f6bf9a85beb0e2d0cb8a1755fdd3dbe195b3470021be4b2efbf9b2384bc0d9ec42cb24f595f3ad4498d18eb469d87978596cd523033f71b55ae9e81dd5178db65554af771c203ebb39d1b5659b38275693cf2e97d90705580e10678de3e3e8ec341c1164190078d42155fe4e709f381a85d4aa73e2da2cfc47c869da1a6bc0321cf1e7be9bc77932aee7aacc8811961de3c5d35012d333c91b0bd04251d18471829f9956963d082d46a91fac88be5344b545e905c87ffe70f35bfa9bd3b1e0b665109d333eea2a4f7c288d09faf42004d0df42611dc03c705effdde12f0b1bfbd8fb1025ee482178b33dead04bb4cfd', '1571767416', 1),
(10, '990cbdb49f05c4c46ef516ddc9e75b1d25d2bfb25915b8867a9e91238750f29c', 'c0b4bee54bd22c667583b655aed9d77937d586be8c0cdd7fdb0a8659652fdacfed7a51366e0d622d4705dc263e5e37c34de04ea9c852e294d9bda2613ea2fed1fc6f8584f4c01da28d9d8e0ce316c998e7a57b55523628c9742eb84eeaa1e5ef8e64bc617b0457145af74cbb0d40751d34314d8a9b54f017d16b3bdc4a52dc2c3cb46145c8946308cfb3d9bafa2d890e9757bba8bdbc536d368954113bd460e995729794f168ca90f0c9f19baae6f880c74f32844f55fbb50f8ed0b61bb4919854a4df0ba5d0134a2ea8d222e6859b64130fefdd565be1fb22c160f8d3b29038447061912e3760b8b69397ef51fada8be147abb45a1966f888577b0a5f54a3c2a400ea107386e3dbea722353eb7b731f7602660cc73d0906bc60f8bd4b8109d273fd9a027d790fcf0907cb9a41b5dce63c7731ee12ce1b711917a1c45aceb0c3b9ad336aa5a86e824d1ef144e9ae20c8cf24d2585d93d6882bad152ea1643ed0e568aeee32c5524bc9a09e221f980f71799e2cc178bc9f555e29458dc92273ae9ab5623bf102bf9186203c26c59407ebd353b72720f992b747f4c3365a169b6335f32b92cb0c23c04ef93198b0046d586e12c4d3f4f7b2f235826ec40460c3e833a3af7dbffbb8a7ceaf6296888820692d46a8b1e31b3cdc0d77f7d7cfe217982f45b46e8c77ee41d99099eee7f8b39f757d63e14ab719a47cb7b1adadb9d389', '1571852907', 1),
(11, 'd795faf4c9aac085a0b0387cca2f2f844ee82d0caf32f854ce99cf8cf5f707a4', '296c8ff6ac715fef6d9d827ccc34697383e573548047d2ee3fef8978e4fe0819dddfd82c3eb1546a283d53559a9d01869ad0d832700f234c2c2b309ab9e27a3a484c8e01c33b7873736a51a03d8519b286c917c6fe2cfe8498d7e7c875e887c48753f1590b7d2d9f151a3ee4eae5ba2ec728f933113bb96bc9b59aa9c0b1940568104ca02c855d07509688c7a0295e73f5e2e3f771e35a0e8a2d1af960fbc684c4fd43ad2c3096ef92d9bb681d57c6fd90023883e7e5428a360f0d5ecfb72b8630999803148281db84a840839e7b904a0db4ffeffb575b6f585722a8e3a4076650cee3aad27e102a983bde724763d1ace29bebfce3c47de2da433061b5d9de11f9adee9cca98b921548cac8054ddbc651d47ae7fe38cd2dff8ac5630c2a342a8fd9e12a501a371abf1c58d2c002476ed4601ad55b07c030215ca32cb2d1726f0fbb4811a1f25b560e14fdb3bbf82b58b46f02505e8c1813e8d9c487bdbbd21b18eabdfc6cffc0c06b677a397cfbfcaf17bf8018290d7761161206b21b6770b55c613ba46983bbb6cc9a36626435066fd1a682fe420c286c4bf4ea5ef5eacbf807cee50d88623bf7c5a2ec3f158187489d1fda0ee1154e65974987144b0808e42a76b5bc4c2609694a8272bb9ee40d7378739e315454cf5f4edb4c3acac2c0a3bd6c27360f209e5b14ac6546615cfecdb05848cef821bf54b8f9ab247e5fa04a9', '1571944320', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_loginsfailed`
--

CREATE TABLE `mc_loginsfailed` (
  `fl_id` int(11) NOT NULL COMMENT 'Failed login ID',
  `fl_user` varchar(200) COLLATE utf8_bin NOT NULL COMMENT 'Supplied username/email/string',
  `fl_attempts` int(11) NOT NULL DEFAULT '1' COMMENT 'Attempts on using this username',
  `fl_locked_until` timestamp NULL DEFAULT NULL COMMENT 'User/Email/String blocked for login until'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `mc_loginsfailed`
--

INSERT INTO `mc_loginsfailed` (`fl_id`, `fl_user`, `fl_attempts`, `fl_locked_until`) VALUES
(12, 'fachsimpeln@example.com', 5, '2019-10-23 19:15:46'),
(13, 'fachsimpeln', 5, '2019-10-23 19:15:06'),
(14, 'abc', 2, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_users`
--

CREATE TABLE `mc_users` (
  `usr_id` int(11) NOT NULL COMMENT 'User ID',
  `usr_username` varchar(200) COLLATE utf8_bin NOT NULL COMMENT 'Username of user',
  `usr_email` varchar(200) COLLATE utf8_bin NOT NULL COMMENT 'E-Mail (unique) of user',
  `usr_password` text COLLATE utf8_bin NOT NULL COMMENT 'Password of user (created in PHP with password_hash)',
  `usr_createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation date/time of user'
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
-- Indizes für die Tabelle `mc_devicecookies`
--
ALTER TABLE `mc_devicecookies`
  ADD PRIMARY KEY (`dc_id`),
  ADD KEY `usr_id` (`usr_id`);

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
-- Indizes für die Tabelle `mc_loginsfailed`
--
ALTER TABLE `mc_loginsfailed`
  ADD PRIMARY KEY (`fl_id`),
  ADD UNIQUE KEY `fl_user` (`fl_user`);

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
-- AUTO_INCREMENT für Tabelle `mc_devicecookies`
--
ALTER TABLE `mc_devicecookies`
  MODIFY `dc_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of devicecookie', AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT für Tabelle `mc_friendlist`
--
ALTER TABLE `mc_friendlist`
  MODIFY `fr_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of friendship', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `mc_groups`
--
ALTER TABLE `mc_groups`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of the group', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `mc_group_user`
--
ALTER TABLE `mc_group_user`
  MODIFY `gu_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of Group-User', AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `mc_logins`
--
ALTER TABLE `mc_logins`
  MODIFY `login_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal session id', AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT für Tabelle `mc_loginsfailed`
--
ALTER TABLE `mc_loginsfailed`
  MODIFY `fl_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Failed login ID', AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT für Tabelle `mc_users`
--
ALTER TABLE `mc_users`
  MODIFY `usr_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'User ID', AUTO_INCREMENT=3;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `mc_devicecookies`
--
ALTER TABLE `mc_devicecookies`
  ADD CONSTRAINT `mc_devicecookies_ibfk_1` FOREIGN KEY (`usr_id`) REFERENCES `mc_users` (`usr_id`);

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
