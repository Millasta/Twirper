-- MySQL dump 10.13  Distrib 5.7.20, for Linux (x86_64)
--
-- Host: localhost    Database: dbproject_app
-- ------------------------------------------------------
-- Server version	5.7.20-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `_FOLLOW`
--

DROP TABLE IF EXISTS `_FOLLOW`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_FOLLOW` (
  `USERID_1` int(2) NOT NULL,
  `USERID_2` int(2) NOT NULL,
  `FOLLOWDATE` datetime DEFAULT NULL,
  `READDATE` datetime DEFAULT NULL,
  PRIMARY KEY (`USERID_1`,`USERID_2`),
  KEY `FK__FOLLOW_USER1` (`USERID_2`),
  CONSTRAINT `_FOLLOW_ibfk_1` FOREIGN KEY (`USERID_1`) REFERENCES `USER` (`USERID`),
  CONSTRAINT `_FOLLOW_ibfk_2` FOREIGN KEY (`USERID_2`) REFERENCES `USER` (`USERID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `HASHTAG`
--

DROP TABLE IF EXISTS `HASHTAG`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `HASHTAG` (
  `HASHTAGSTRING` char(32) NOT NULL,
  PRIMARY KEY (`HASHTAGSTRING`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TWEET`
--

DROP TABLE IF EXISTS `TWEET`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TWEET` (
  `TWEETID` int(2) NOT NULL AUTO_INCREMENT,
  `USERID` int(2) NOT NULL,
  `TWEETPUBLICATIONDATE` datetime DEFAULT NULL,
  `TWEETISRESPONSETO` int(2) DEFAULT NULL,
  `TWEETCONTENT` char(255) DEFAULT NULL,
  PRIMARY KEY (`TWEETID`),
  KEY `FK_TWEET_USER` (`USERID`),
  CONSTRAINT `TWEET_ibfk_1` FOREIGN KEY (`USERID`) REFERENCES `USER` (`USERID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `USER`
--

DROP TABLE IF EXISTS `USER`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `USER` (
  `USERID` int(2) NOT NULL AUTO_INCREMENT,
  `USERUSERNAME` char(32) DEFAULT NULL,
  `USERDISPLAYNAME` char(32) DEFAULT NULL,
  `USERSUBSCRIBINGDATE` datetime DEFAULT NULL,
  `USEREMAILADDRESS` char(64) DEFAULT NULL,
  `USERPASSWORD` char(64) DEFAULT NULL,
  `USERAVATAR` char(255) DEFAULT NULL,
  PRIMARY KEY (`USERID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `_LIKE`
--

DROP TABLE IF EXISTS `_LIKE`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_LIKE` (
  `USERID` int(2) NOT NULL,
  `TWEETID` int(2) NOT NULL,
  `LIKEDATE` datetime DEFAULT NULL,
  `READDATE` datetime DEFAULT NULL,
  PRIMARY KEY (`USERID`,`TWEETID`),
  KEY `FK__LIKE_TWEET` (`TWEETID`),
  CONSTRAINT `_LIKE_ibfk_1` FOREIGN KEY (`USERID`) REFERENCES `USER` (`USERID`),
  CONSTRAINT `_LIKE_ibfk_2` FOREIGN KEY (`TWEETID`) REFERENCES `TWEET` (`TWEETID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `_LINK`
--

DROP TABLE IF EXISTS `_LINK`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_LINK` (
  `TWEETID` int(2) NOT NULL,
  `HASHTAGSTRING` char(32) NOT NULL,
  PRIMARY KEY (`TWEETID`,`HASHTAGSTRING`),
  KEY `FK__LINK_HASHTAG` (`HASHTAGSTRING`),
  CONSTRAINT `_LINK_ibfk_1` FOREIGN KEY (`TWEETID`) REFERENCES `TWEET` (`TWEETID`),
  CONSTRAINT `_LINK_ibfk_2` FOREIGN KEY (`HASHTAGSTRING`) REFERENCES `HASHTAG` (`HASHTAGSTRING`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `_MENTION`
--

DROP TABLE IF EXISTS `_MENTION`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_MENTION` (
  `USERID` int(2) NOT NULL,
  `TWEETID` int(2) NOT NULL,
  `READDATE` datetime DEFAULT NULL,
  PRIMARY KEY (`USERID`,`TWEETID`),
  KEY `FK__MENTION_TWEET` (`TWEETID`),
  CONSTRAINT `_MENTION_ibfk_1` FOREIGN KEY (`USERID`) REFERENCES `USER` (`USERID`),
  CONSTRAINT `_MENTION_ibfk_2` FOREIGN KEY (`TWEETID`) REFERENCES `TWEET` (`TWEETID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-12-13 15:24:24
