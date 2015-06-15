/*
Database for my company's internal website
*/
CREATE DATABASE IF NOT EXISTS `N2_internal_db`;

USE N2_internal_db;


CREATE TABLE IF NOT EXISTS `Users` (
	`userid` INT(6) UNSIGNED AUTO_INCREMENT,
	`username`	VARCHAR(30) NOT NULL UNIQUE,
	`password`	VARCHAR(30) NOT NULL,
	`usertype`	VARCHAR(15) NOT NULL,
	PRIMARY KEY (`userid`)
);

INSERT INTO `Users` (`username`,`password`,`usertype`) VALUES ('christ','12345','admin');
INSERT INTO `Users` (`username`,`password`,`usertype`) VALUES ('nellyp','1234','manager');
INSERT INTO `Users` (`username`,`password`,`usertype`) VALUES ('nguyen','1234','employee');
INSERT INTO `Users` (`username`,`password`,`usertype`) VALUES ('nguyen','1234','manager');

CREATE TABLE IF NOT EXISTS `Employees` (
	`employee_id` INT(6) UNSIGNED AUTO_INCREMENT,
	`e_first_name` VARCHAR(20) NOT NULL,
	`e_last_name` VARCHAR(20) NOT NULL,
	`e_street_addr` VARCHAR(30) NOT NULL,
	`e_city` VARCHAR(20) NOT NULL,
	`e_state` VARCHAR(15) NOT NULL,
	`e_country` VARCHAR(20) NOT NULL,
	`e_marriage_status` VARCHAR(10) NOT NULL,
	`e_gender` VARCHAR(10) NOT NULL,
	`e_dob` DATE NOT NULL,
	`e_phone` VARCHAR(30) NOT NULL,
	`e_salary` VARCHAR(30) NOT NULL,
	`e_email` VARCHAR(30) NOT NULL UNIQUE,
	PRIMARY KEY (`employee_id`)
);
INSERT INTO `Employees` (`e_first_name`,`e_last_name`,`e_street_addr`,`e_city`,`e_state`,`e_country`,`e_marriage_status`,`e_gender`,`e_dob`,`e_phone`,`e_salary`,`e_email`) 
			VALUES ('Nguyen','Tran','123 street','san jose','CA','US','married','male','1991-04-15','98765432','90000','nt@nelshop.com');

INSERT INTO `Employees` (`e_first_name`,`e_last_name`,`e_street_addr`,`e_city`,`e_state`,`e_country`,`e_marriage_status`,`e_gender`,`e_dob`,`e_phone`,`e_salary`,`e_email`) 
			VALUES ('Nelly','Phan','123 NY','san jose','CA','US','married','female','1991-11-23','87873845','50000','np@nelshop.com');

INSERT INTO `Employees` (`e_first_name`,`e_last_name`,`e_street_addr`,`e_city`,`e_state`,`e_country`,`e_marriage_status`,`e_gender`,`e_dob`,`e_phone`,`e_salary`,`e_email`) 
			VALUES ('Chris','Tran','323 WA','san jose','CA','US','single','male','1991-04-15','99999999','70000','ct@nelshop.com');


CREATE TABLE IF NOT EXISTS `UserandEmployee` (
	`userid` INT(6) UNSIGNED NOT NULL,
	`employee_id` INT(6) UNSIGNED NOT NULL,
	PRIMARY KEY (`userid`,`employee_id`),
	FOREIGN KEY (`userid`) REFERENCES Users(`userid`),
	FOREIGN KEY (`employee_id`) REFERENCES Employees(`employee_id`)
);
INSERT INTO `UserandEmployee` VALUES('1','3');
INSERT INTO `UserandEmployee` VALUES('2','2');
INSERT INTO `UserandEmployee` VALUES('3','1');
INSERT INTO `UserandEmployee` VALUES('4','1');

