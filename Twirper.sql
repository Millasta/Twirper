-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Dim 21 Janvier 2018 à 14:59
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `Twirper`
--

-- --------------------------------------------------------

--
-- Structure de la table `HASHTAG`
--

CREATE TABLE IF NOT EXISTS `HASHTAG` (
  `HASHTAGSTRING` char(32) NOT NULL,
  PRIMARY KEY (`HASHTAGSTRING`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `HASHTAG`
--

INSERT INTO `HASHTAG` (`HASHTAGSTRING`) VALUES
('best_actor'),
('karate'),
('uganda');

-- --------------------------------------------------------

--
-- Structure de la table `TWEET`
--

CREATE TABLE IF NOT EXISTS `TWEET` (
  `TWEETID` int(2) NOT NULL AUTO_INCREMENT,
  `USERID` int(2) NOT NULL,
  `TWEETPUBLICATIONDATE` datetime DEFAULT NULL,
  `TWEETISRESPONSETO` int(2) DEFAULT NULL,
  `TWEETCONTENT` char(255) DEFAULT NULL,
  PRIMARY KEY (`TWEETID`),
  KEY `FK_TWEET_USER` (`USERID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `TWEET`
--

INSERT INTO `TWEET` (`TWEETID`, `USERID`, `TWEETPUBLICATIONDATE`, `TWEETISRESPONSETO`, `TWEETCONTENT`) VALUES
(1, 1, '2000-01-01 00:00:00', NULL, 'Is anyone there ?'),
(2, 3, '2018-01-03 16:20:32', NULL, '#ChuckNorris Dew u no de wey ? *clack* *clack* #uganda'),
(3, 1, '2018-01-03 16:20:33', 2, 'http://lmgtfy.com/?q=way+to+uganda'),
(4, 2, '2018-01-21 14:17:30', NULL, '#knuckle Who do you prefer between me and #ChuckNorris ? #best_actor #karate'),
(5, 1, '2018-01-21 14:17:31', 4, 'lol');

-- --------------------------------------------------------

--
-- Structure de la table `USER`
--

CREATE TABLE IF NOT EXISTS `USER` (
  `USERID` int(2) NOT NULL AUTO_INCREMENT,
  `USERUSERNAME` char(32) DEFAULT NULL,
  `USERDISPLAYNAME` char(32) DEFAULT NULL,
  `USERSUBSCRIBINGDATE` datetime DEFAULT NULL,
  `USEREMAILADDRESS` char(64) DEFAULT NULL,
  `USERPASSWORD` char(64) DEFAULT NULL,
  `USERAVATAR` char(255) DEFAULT NULL,
  PRIMARY KEY (`USERID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `USER`
--

INSERT INTO `USER` (`USERID`, `USERUSERNAME`, `USERDISPLAYNAME`, `USERSUBSCRIBINGDATE`, `USEREMAILADDRESS`, `USERPASSWORD`, `USERAVATAR`) VALUES
(1, 'ChuckNorris', 'Chuck Norris', '2000-01-01 00:00:00', 'chucknorris@earth.com', '7682fe272099ea26efe39c890b33675b', 'ChuckNorris.jpg'),
(2, 'JCVD', 'Jean-Claude Van Damme', '2000-01-01 00:00:01', 'jcvd@belgium.com', '9e1a52d3b7387beae058f1dbaa8da096', 'JCVD.jpg'),
(3, 'knuckle', 'knuckle', '2018-01-03 16:20:32', 'knuckle@uganda.com', '111fa7773cce1023627b584dc8099873', 'knuckle.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `_FOLLOW`
--

CREATE TABLE IF NOT EXISTS `_FOLLOW` (
  `USERID_1` int(2) NOT NULL,
  `USERID_2` int(2) NOT NULL,
  `FOLLOWDATE` datetime DEFAULT NULL,
  `READDATE` datetime DEFAULT NULL,
  PRIMARY KEY (`USERID_1`,`USERID_2`),
  KEY `FK__FOLLOW_USER1` (`USERID_2`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `_FOLLOW`
--

INSERT INTO `_FOLLOW` (`USERID_1`, `USERID_2`, `FOLLOWDATE`, `READDATE`) VALUES
(2, 1, '2000-01-01 00:00:01', '2000-01-01 00:00:01'),
(2, 3, '2018-01-03 16:20:32', '2018-01-15 19:10:02'),
(3, 1, '2018-01-03 16:20:32', '2018-01-03 16:20:32');

-- --------------------------------------------------------

--
-- Structure de la table `_LIKE`
--

CREATE TABLE IF NOT EXISTS `_LIKE` (
  `USERID` int(2) NOT NULL,
  `TWEETID` int(2) NOT NULL,
  `LIKEDATE` datetime DEFAULT NULL,
  `READDATE` datetime DEFAULT NULL,
  PRIMARY KEY (`USERID`,`TWEETID`),
  KEY `FK__LIKE_TWEET` (`TWEETID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `_LIKE`
--

INSERT INTO `_LIKE` (`USERID`, `TWEETID`, `LIKEDATE`, `READDATE`) VALUES
(2, 1, '2000-01-01 00:00:01', '2000-01-01 00:00:01'),
(3, 1, '2018-01-03 16:20:32', '2018-01-03 16:20:32'),
(3, 3, '2018-01-03 16:20:33', '2018-01-03 16:20:33'),
(3, 5, '2018-01-21 14:17:31', '2018-01-21 14:17:31');

-- --------------------------------------------------------

--
-- Structure de la table `_LINK`
--

CREATE TABLE IF NOT EXISTS `_LINK` (
  `TWEETID` int(2) NOT NULL,
  `HASHTAGSTRING` char(32) NOT NULL,
  PRIMARY KEY (`TWEETID`,`HASHTAGSTRING`),
  KEY `FK__LINK_HASHTAG` (`HASHTAGSTRING`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `_MENTION`
--

CREATE TABLE IF NOT EXISTS `_MENTION` (
  `USERID` int(2) NOT NULL,
  `TWEETID` int(2) NOT NULL,
  `READDATE` datetime DEFAULT NULL,
  PRIMARY KEY (`USERID`,`TWEETID`),
  KEY `FK__MENTION_TWEET` (`TWEETID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `_MENTION`
--

INSERT INTO `_MENTION` (`USERID`, `TWEETID`, `READDATE`) VALUES
(1, 2, '2018-01-03 16:20:32'),
(1, 4, '2018-01-21 14:17:30'),
(3, 4, '2018-01-21 14:17:30');

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `TWEET`
--
ALTER TABLE `TWEET`
  ADD CONSTRAINT `TWEET_ibfk_1` FOREIGN KEY (`USERID`) REFERENCES `USER` (`USERID`);

--
-- Contraintes pour la table `_FOLLOW`
--
ALTER TABLE `_FOLLOW`
  ADD CONSTRAINT `_FOLLOW_ibfk_1` FOREIGN KEY (`USERID_1`) REFERENCES `USER` (`USERID`),
  ADD CONSTRAINT `_FOLLOW_ibfk_2` FOREIGN KEY (`USERID_2`) REFERENCES `USER` (`USERID`);

--
-- Contraintes pour la table `_LIKE`
--
ALTER TABLE `_LIKE`
  ADD CONSTRAINT `_LIKE_ibfk_1` FOREIGN KEY (`USERID`) REFERENCES `USER` (`USERID`),
  ADD CONSTRAINT `_LIKE_ibfk_2` FOREIGN KEY (`TWEETID`) REFERENCES `TWEET` (`TWEETID`);

--
-- Contraintes pour la table `_LINK`
--
ALTER TABLE `_LINK`
  ADD CONSTRAINT `_LINK_ibfk_1` FOREIGN KEY (`TWEETID`) REFERENCES `TWEET` (`TWEETID`),
  ADD CONSTRAINT `_LINK_ibfk_2` FOREIGN KEY (`HASHTAGSTRING`) REFERENCES `HASHTAG` (`HASHTAGSTRING`);

--
-- Contraintes pour la table `_MENTION`
--
ALTER TABLE `_MENTION`
  ADD CONSTRAINT `_MENTION_ibfk_1` FOREIGN KEY (`USERID`) REFERENCES `USER` (`USERID`),
  ADD CONSTRAINT `_MENTION_ibfk_2` FOREIGN KEY (`TWEETID`) REFERENCES `TWEET` (`TWEETID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
