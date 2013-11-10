/*CREATE DATABASE reatlas;*/
/*
CREATE USER 'sec_user'@'localhost' IDENTIFIED BY 'eKcGZr59zAa2BEWU';
GRANT SELECT, INSERT, UPDATE, DELETE ON `secure_sessions`.* TO 'sec_user'@'localhost';


CREATE TABLE `sessions` (
  `id` char(128) NOT NULL,
  `set_time` char(10) NOT NULL,
  `data` text NOT NULL,
  `session_key` char(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE `members` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
  `username` VARCHAR(30) NOT NULL, 
  `email` VARCHAR(50) NOT NULL, 
  `password` CHAR(128) NOT NULL, 
  `salt` CHAR(128) NOT NULL
) ENGINE = MyISAM;

CREATE TABLE `login_attempts` (
  `user_id` int(11) NOT NULL,
  `time` VARCHAR(30) NOT NULL 
) ENGINE=MyISAM;


INSERT INTO `members` VALUES(1, 'manila', 'manila.nair@gmail.com', '00807432eae173f652f2064bdca1b61b290b52d40e429a7d295d76a71084aa96c0233b82f1feac45529e0726559645acaed6f3ae58a286b9f075916ebf66cacc', 'f9aab579fc1b41ed0c44fe4ecdbfcdb4cb99b9023abb241a6db833288f4eea3c02f76e0d35204a8695077dcf81932aa59006423976224be0390395bae152d4ef');
*/
/*
MySQL Data Transfer
Source Host: localhost
Source Database: 3g
Target Host: localhost
Target Database: 3g
Date: 10/8/2008 12:05:52 AM
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for active_guests
-- ----------------------------
CREATE TABLE `active_guests` (
  `ip` varchar(15) NOT NULL,
  `timestamp` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for active_users
-- ----------------------------
CREATE TABLE `active_users` (
  `username` varchar(30) NOT NULL,
  `timestamp` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for banned_users
-- ----------------------------
CREATE TABLE `banned_users` (
  `username` varchar(30) NOT NULL,
  `timestamp` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `login_attempts` (
    `username` varchar(30) NOT NULL,
    `count` int(11) DEFAULT 0,
    `timestamp` int(11) unsigned NOT NULL,
    UNIQUE KEY  (`username`)
) ENGINE=MyISAM;

-- ----------------------------
-- Table structure for users
-- ----------------------------
CREATE TABLE `users` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
  `username` varchar(30) NOT NULL,
  `password` varchar(32) default NULL,
  `userkey` varchar(32) default NULL,
  `userlevel` tinyint(1) unsigned NOT NULL,
  `email` varchar(50) default NULL,
  `timestamp` int(11) unsigned NOT NULL,
  `parent_directory` varchar(30) NOT NULL,
   `active` tinyint(1) NOT NULL default 0,
   `aulogin` varchar(30) default NULL,
  UNIQUE KEY  (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records 
-- ----------------------------
/*--INSERT INTO `active_users` VALUES ('master1agent2', '1223395479');*/
INSERT INTO `users` (`username`,`password`,`userkey`,`userlevel`,`email`,`timestamp`,`parent_directory`,`active`) 
VALUES ('admin', '21232f297a57a5a743894a0e4a801fc3', '0361ff977498f2e522260477fdf61f2d', '9', 'manila.nair@gmail.com', '1223392229', 'admin','1');
/*--INSERT INTO `users` VALUES ('master1', 'd5802d05bbf0881de2fd823c9560619e', '96aa24b6d80163cde5e06afd56560907', '8', 'master1@3g.com', '1223394759', 'admin');
--INSERT INTO `users` VALUES ('master1agent1', '83a87fd756ab57199c0bb6d5e11168cb', '726226d035b310bcc4e3b260d6ecd5e1', '1', 'master1agent1@3g.com', '1223395385', 'master1');
--INSERT INTO `users` VALUES ('master1agent1member1', '83a87fd756ab57199c0bb6d5e11168cb', '4225cc4e55da0196a7c84a1810b45748', '2', 'master1agent1member1@3g.com', '1223395319', 'master1agent1');
--INSERT INTO `users` VALUES ('master1agent2', 'b1a4a6b01cc297d4677c4ca6656e14d7', 'c73446d1f91c4dc8d0e527202a1f3e7e', '1', 'master1agent2@3g.com', '1223395479', 'master1');
--INSERT INTO `users` VALUES ('master1agent2member1', 'c7764cfed23c5ca3bb393308a0da2306', '0', '2', 'master1agent2member1@3g.com', '1223395477', 'master1agent2');
--INSERT INTO `users` VALUES ('master2', '5b9de42bf3fa2534e0d7ae695b12aeab', 'd33d6eab248fb2d160f1cecd905a1809', '8', 'master2@3g.com', '1223395358', 'admin');
--INSERT INTO `users` VALUES ('master2agent1', '83a87fd756ab57199c0bb6d5e11168cb', '695489db84c39d7eb71ab7fcdf889490', '1', 'master2agent1@3g.com', '1223394946', 'master2');
--INSERT INTO `users` VALUES ('master2agent1member1', 'c7764cfed23c5ca3bb393308a0da2306', 'c40ac57540370897eab305fca804fc2c', '2', 'master2agent1member1@3g.com', '1223395328', 'master2agent1');
--INSERT INTO `users` VALUES ('master2agent2', 'b1a4a6b01cc297d4677c4ca6656e14d7', '4632fb111729f5e1a363b715702030a6', '1', 'master2agent2@3g.com', '1223395017', 'master2');
*/

-- ----------------------------
-- Table structure for profile
-- ----------------------------
CREATE TABLE `users_profile` (
    `profileid` int(10) NOT NULL AUTO_INCREMENT,
    `fullname` varchar(100) NOT NULL,
    `organization` varchar(100) default NULL,
    `address` varchar(100) default NULL,
    `address2` varchar(100) default NULL,
    `region` varchar(100) default NULL,
    `postalcode` varchar(12) default NULL,
    `city` varchar(50) default NULL,
    `country` varchar(50) default NULL,
    `phone` varchar(50) default NULL,
    `image` varchar(50) default NULL,
    `website` varchar(50) default NULL,
    `userid` int(10) NOT NULL,
    PRIMARY KEY  (`profileid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
