/*
Navicat MySQL Data Transfer

Source Server         : 192.168.4.56
Source Server Version : 50536
Source Host           : 192.168.4.56:3306
Source Database       : hvtable

Target Server Type    : MYSQL
Target Server Version : 50536
File Encoding         : 65001

Date: 2016-09-12 10:17:06
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for column_table
-- ----------------------------
DROP TABLE IF EXISTS `column_table`;
CREATE TABLE `column_table` (
  `column_id` int(10) NOT NULL AUTO_INCREMENT,
  `table_id` int(10) NOT NULL,
  `column_name` varchar(255) DEFAULT NULL,
  `merge` tinyint(1) DEFAULT '1',
  `grade` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`table_id`,`column_id`)
) ENGINE=MyISAM AUTO_INCREMENT=116 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for row_column_value
-- ----------------------------
DROP TABLE IF EXISTS `row_column_value`;
CREATE TABLE `row_column_value` (
  `row_id` int(10) NOT NULL,
  `column_id` int(10) NOT NULL,
  `table_id` int(10) NOT NULL,
  `value` varchar(1024) DEFAULT NULL,
  `postil` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`row_id`,`column_id`,`table_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for row_table
-- ----------------------------
DROP TABLE IF EXISTS `row_table`;
CREATE TABLE `row_table` (
  `row_id` int(10) NOT NULL AUTO_INCREMENT,
  `table_id` int(10) NOT NULL,
  `row_name_1` varchar(255) DEFAULT NULL,
  `row_name_2` varchar(255) DEFAULT NULL,
  `row_name_3` varchar(255) DEFAULT NULL,
  `merge_1` tinyint(1) DEFAULT '1',
  `merge_2` tinyint(1) DEFAULT '1',
  `merge_3` tinyint(1) DEFAULT '1',
  `grade` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`table_id`,`row_id`)
) ENGINE=MyISAM AUTO_INCREMENT=98 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for table_info
-- ----------------------------
DROP TABLE IF EXISTS `table_info`;
CREATE TABLE `table_info` (
  `table_id` int(10) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(255) DEFAULT NULL,
  `notes` varchar(2000) DEFAULT '备注',
  PRIMARY KEY (`table_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
