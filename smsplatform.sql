/*
Navicat MySQL Data Transfer

Source Server         : Divine Developers
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : smsplatform

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2014-06-25 10:31:34
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `clients`
-- ----------------------------
DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `idnum` varchar(255) DEFAULT NULL,
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `stand_id` varchar(255) DEFAULT NULL,
  `sex` varchar(255) DEFAULT NULL,
  `employer` varchar(255) DEFAULT NULL,
  `ecnum` varchar(255) DEFAULT NULL,
  `file_number` varchar(255) DEFAULT NULL,
  `date` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of clients
-- ----------------------------
INSERT INTO `clients` VALUES ('Ernest', 'Muroiwa', '', '', '775936743', '', '9', '', '', '', 'Mr.', '', '24/06/2014');

-- ----------------------------
-- Table structure for `sms_response`
-- ----------------------------
DROP TABLE IF EXISTS `sms_response`;
CREATE TABLE `sms_response` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(50) DEFAULT NULL,
  `message` text,
  `status` int(11) DEFAULT '0',
  `date` varchar(111) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of sms_response
-- ----------------------------
INSERT INTO `sms_response` VALUES ('1', '0774002797', 'Good Day This is a Cloud attendance notice to inform you that you have been red flaged for this module JavaHCS406  Please attend all your lectures from now on or your will not be leigble to sit for your exams', '0', null);
INSERT INTO `sms_response` VALUES ('2', '0774002792', 'Good Day This is a Cloud attendance notice to inform you that you have been red flaged for this module JavaHCS406  Please attend all your lectures from now on or your will not be leigble to sit for your exams', '0', null);
INSERT INTO `sms_response` VALUES ('3', '0774002797', 'Good Day This is your order number.... A1399788164o ..... please keep it safe .   ', '0', null);
INSERT INTO `sms_response` VALUES ('4', '0774002797', 'Good Day This is the progess on your order vdafd.  ', '0', null);
INSERT INTO `sms_response` VALUES ('6', '0774002797', 'Good Day This is a Cloud attendance notice to inform you that you have been red flaged for this module IntroTo ComputersHCS101  Please attend all your lectures from now on or your will not be leigble to sit for your exams', '0', null);
INSERT INTO `sms_response` VALUES ('7', '0774002797', 'Good Day Aicsol will like to inform you that your account A1399104020n has be unpaid for two weeks .Please procced with payments', '0', null);
INSERT INTO `sms_response` VALUES ('8', '0774002797', 'Good Day Aicsol will like to inform you that your account A13991198353 has be unpaid for two weeks .Please procced with payments', '0', null);
INSERT INTO `sms_response` VALUES ('9', '0774002797', 'Good Day This is your order number.... A14004224295 ..... please keep it safe .   ', '0', null);
INSERT INTO `sms_response` VALUES ('10', '00263774002797', 'test', '0', null);
INSERT INTO `sms_response` VALUES ('11', '00263774002797', 'test', '0', null);
INSERT INTO `sms_response` VALUES ('12', '00263774002797', 'test', '0', null);
INSERT INTO `sms_response` VALUES ('13', '00263774002797', 'ffff', '0', null);
INSERT INTO `sms_response` VALUES ('14', '00263774002797', 'ffff', '0', null);
INSERT INTO `sms_response` VALUES ('15', '00263774002797', 'ffff', '0', null);
INSERT INTO `sms_response` VALUES ('16', '00263774002797', 'CREDITS', '0', null);
INSERT INTO `sms_response` VALUES ('17', '00263774002797', 'CREDITS', '0', null);
INSERT INTO `sms_response` VALUES ('18', '00263774002797', 'CREDITS', '0', null);
INSERT INTO `sms_response` VALUES ('19', '774002797', 'test', '0', '06/24/2014');
INSERT INTO `sms_response` VALUES ('20', '775936743', 'test\r\n', '0', '06/24/2014');
INSERT INTO `sms_response` VALUES ('21', '775936743', 'Dear Muroiwa jzdfjsa', '0', '06/24/2014');
INSERT INTO `sms_response` VALUES ('22', '774002797', 'Dear Muroiwa xcmcx', '0', '06/24/2014');
INSERT INTO `sms_response` VALUES ('23', '774002797', 'DearMr. Muroiwa ncZC', '0', '06/24/2014');
INSERT INTO `sms_response` VALUES ('24', '775936743', 'DearMr. Muroiwa dfh', '0', '06/24/2014');
INSERT INTO `sms_response` VALUES ('25', '775936743', 'Dear Mr. Muroiwa etehsdsfsa', '0', '06/24/2014');

-- ----------------------------
-- Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `surname` varchar(40) DEFAULT NULL,
  `sex` varchar(40) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `account` varchar(40) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `idnumber` varchar(100) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `date` varchar(100) DEFAULT NULL,
  `access` varchar(30) DEFAULT NULL,
  `suspend` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', 'Ernest', 'Muroiwa', 'male', 'emuroiwa@gmail.com', null, '23 Harare road', null, 'admin', 'password', '29-20018023-E-82', '1', '10/01/2013', '1', '1');
INSERT INTO `users` VALUES ('2', 'Ernest', 'Muroiwa', 'male', 'emuroiwa@gmail.com1', null, 'test', '', 'staff', 'password', '', '1', '03/21/2014', '2', '1');
