/*
Database for my company's internal website
*/
CREATE DATABASE IF NOT EXISTS `N2_internal_db`;

USE N2_internal_db;


CREATE TABLE `Users` (
	`username`	VARCHAR(30) NOT NULL,
	`password`	VARCHAR(30) NOT NULL,
	`usertype`	VARCHAR(15) NOT NULL,
	PRIMARY KEY (`username`)
);

INSERT INTO `Users` VALUES ('christ','1234','admin');
INSERT INTO `Users` VALUES ('nellyp','1234','manager');
INSERT INTO `Users` VALUES ('nguyen','1234','employee');