/*
Database for my company's internal website
*/
CREATE DATABASE IF NOT EXISTS `N2_internal_db`;

USE N2_internal_db;


CREATE TABLE IF NOT EXISTS `Users` (
	`userid` INT(6) UNSIGNED AUTO_INCREMENT,
	`username`	VARCHAR(50) NOT NULL UNIQUE,
	`password`	VARCHAR(100) NOT NULL,
	`usertype`	VARCHAR(30) NOT NULL,
	PRIMARY KEY (`userid`)
);

INSERT INTO `Users` (`username`,`password`,`usertype`) VALUES ('christ',password('12345'),'admin');
INSERT INTO `Users` (`username`,`password`,`usertype`) VALUES ('nellyp',password('1234'),'manager');
INSERT INTO `Users` (`username`,`password`,`usertype`) VALUES ('nguyen',password('1234'),'employee');


CREATE TABLE IF NOT EXISTS `Employees` (
	`employee_id` INT(6) UNSIGNED AUTO_INCREMENT,
	`userid` INT(6) UNSIGNED NOT NULL,
	`e_first_name` VARCHAR(30) NOT NULL,
	`e_last_name` VARCHAR(30) NOT NULL,
	`e_street_addr` VARCHAR(50) NOT NULL,
	`e_city` VARCHAR(30) NOT NULL,
	`e_state` VARCHAR(30) NOT NULL,
	`e_country` VARCHAR(30) NOT NULL,
	`e_marriage_status` VARCHAR(20) NOT NULL,
	`e_gender` VARCHAR(30) NOT NULL,
	`e_dob` DATE NOT NULL,
	`e_phone` VARCHAR(40) NOT NULL,
	`e_salary` VARCHAR(50) NOT NULL,
	`e_email` VARCHAR(50) NOT NULL UNIQUE,
	PRIMARY KEY (`employee_id`),
	FOREIGN KEY (`userid`) REFERENCES Users(`userid`)
);

INSERT INTO `Employees` (`userid`,`e_first_name`,`e_last_name`,`e_street_addr`,`e_city`,`e_state`,`e_country`,`e_marriage_status`,`e_gender`,`e_dob`,`e_phone`,`e_salary`,`e_email`) 
			VALUES ('3','Nguyen','Tran','123 street','san jose','CA','US','married','male','1991-04-15','98765432','90000','nt@nelshop.com');

INSERT INTO `Employees` (`userid`,`e_first_name`,`e_last_name`,`e_street_addr`,`e_city`,`e_state`,`e_country`,`e_marriage_status`,`e_gender`,`e_dob`,`e_phone`,`e_salary`,`e_email`) 
			VALUES ('2','Nelly','Phan','123 NY','san jose','CA','US','married','female','1991-11-23','87873845','50000','np@nelshop.com');

INSERT INTO `Employees` (`userid`,`e_first_name`,`e_last_name`,`e_street_addr`,`e_city`,`e_state`,`e_country`,`e_marriage_status`,`e_gender`,`e_dob`,`e_phone`,`e_salary`,`e_email`) 
			VALUES ('1','Chris','Tran','323 WA','san jose','CA','US','single','male','1991-04-15','99999999','70000','ct@nelshop.com');

	
CREATE TABLE IF NOT EXISTS `Products` (
	`product_id` INT(10) UNSIGNED AUTO_INCREMENT,
	`product_name` VARCHAR(100) NOT NULL,
	`product_price` DECIMAL(10,2) NOT NULL,
	`product_description` VARCHAR(1000) NOT NULL,
	`ingredients` VARCHAR(500) NOT NULL,
	`recipe` VARCHAR(10000) NOT NULL,
	PRIMARY KEY (`product_id`)
);

INSERT INTO `Products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('chicken gizzard','8.5','chicken gizzard roasted','chicken gizzard,pepper','will be added');

INSERT INTO `Products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('spaghetti','10.5','Italian spaghetti','spaghetti squash,lean ground pork,cheese,pepper','will be added');

			
CREATE TABLE IF NOT EXISTS `Product_categories` (
	`category_id` INT(10) UNSIGNED AUTO_INCREMENT,
	`category_name` VARCHAR(100) NOT NULL,
	`category_description` VARCHAR(1000) NOT NULL,
	PRIMARY KEY (`category_id`)
);

INSERT INTO `Product_categories` (`category_name`,`category_description`) VALUES ('SouthEast Asia','Vietnam, Thailand,...');
INSERT INTO `Product_categories` (`category_name`,`category_description`) VALUES ('Europe','Italy, England, Germany');
INSERT INTO `Product_categories` (`category_name`,`category_description`) VALUES ('America','United States, Canada,...');

CREATE TABLE IF NOT EXISTS `Product_and_Category` (
	`product_id` INT(10) UNSIGNED NOT NULL,
	`category_id` INT(10) UNSIGNED NOT NULL,
	PRIMARY KEY (`product_id`,`category_id`),
	FOREIGN KEY (`product_id`) REFERENCES Products(`product_id`),
	FOREIGN KEY (`category_id`) REFERENCES Product_categories(`category_id`)
);


CREATE TABLE IF NOT EXISTS `Special_Sales` (
	`special_sale_id` INT(10) UNSIGNED AUTO_INCREMENT,
	`start_date` DATE NOT NULL,
	`end_date` DATE NOT NULL,
	`percentage_discount` DECIMAL(4,2) NOT NULL,
	PRIMARY KEY (`special_sale_id`)
);

INSERT INTO `Special_Sales` (`start_date`,`end_date`,`percentage_discount`)
			VALUES ('2015-06-10','2015-06-17','5');

INSERT INTO `Special_Sales` (`start_date`,`end_date`,`percentage_discount`)
			VALUES ('2015-01-11','2015-02-20','10');

CREATE TABLE IF NOT EXISTS `Special_Sales_and_product` (
	`special_sale_id` INT(10) UNSIGNED NOT NULL,
	`product_id` INT(10) UNSIGNED NOT NULL UNIQUE,
	PRIMARY KEY (`special_sale_id`,`product_id`),
	FOREIGN KEY (`product_id`) REFERENCES Products(`product_id`)
);


