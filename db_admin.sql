/*
MySQL Data Transfer
Source Host: localhost
Source Database: db_admin
Target Host: localhost
Target Database: db_admin
Date: 06/10/2021 9:51:32 AM
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for employee
-- ----------------------------
CREATE TABLE `employee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `program` varchar(300) DEFAULT NULL,
  `place` varchar(300) DEFAULT NULL,
  `comments` varchar(300) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `active` int(1) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tbl_roles
-- ----------------------------
CREATE TABLE `tbl_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'role_id',
  `role` varchar(255) DEFAULT NULL COMMENT 'role_text',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tbl_users
-- ----------------------------
CREATE TABLE `tbl_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `mobile` varchar(25) DEFAULT NULL,
  `roleid` tinyint(4) DEFAULT NULL,
  `isActive` tinyint(4) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `employee` VALUES ('67', '2021-06-07', '05:16:00', 'test', 'how are', 'another test', null, null, '2021-06-07 23:46:12');
INSERT INTO `employee` VALUES ('72', '2021-06-07', '04:20:00', 'সেতু ভবনের স্থায়িত্ব', 'মন্তব্য নেই', 'সম্মেলন কক্ষ', null, null, '2021-06-07 23:46:43');
INSERT INTO `tbl_roles` VALUES ('1', 'Admin');
INSERT INTO `tbl_roles` VALUES ('2', 'Editor');
INSERT INTO `tbl_roles` VALUES ('3', 'User');
INSERT INTO `tbl_users` VALUES ('16', 'Abid Ali', 'Abid', 'abid@gmail.com', '188000e1f0fb4075ae1c659697b96296f982cdc4', '01717090233', '1', '1', '2021-06-07 09:27:10', '2020-03-13 05:08:26');
INSERT INTO `tbl_users` VALUES ('19', 'Humayun ', 'Munna1', 'munna@gmail.com', '66c3241204bea40578eb993f41f7c4b1ab982dab', '01717090233', '1', '1', '2021-06-07 21:58:48', '2020-03-13 05:09:49');
INSERT INTO `tbl_users` VALUES ('22', 'Moheuddin Ahmed', 'moheuddin', 'm_uddinit@yahoo.com', '5f4dcc3b5aa765d61d8327deb882cf99', '0182751263', '1', '1', '2021-06-07 23:52:38', '0000-00-00 00:00:00');
