/*
Database for my company's internal website
*/
CREATE DATABASE IF NOT EXISTS `n2_internal_db`;

USE n2_internal_db;


CREATE TABLE IF NOT EXISTS `users` (
	`userid` INT(6) UNSIGNED AUTO_INCREMENT,
	`username`	VARCHAR(50) NOT NULL UNIQUE,
	`password`	VARCHAR(100) NOT NULL,
	`usertype`	VARCHAR(30) NOT NULL,
	PRIMARY KEY (`userid`)
);

INSERT INTO `users` (`username`,`password`,`usertype`) VALUES ('user1',password('123'),'admin');
INSERT INTO `users` (`username`,`password`,`usertype`) VALUES ('user2',password('1234'),'manager');
INSERT INTO `users` (`username`,`password`,`usertype`) VALUES ('user3',password('12345'),'employee');
INSERT INTO `users` (`username`,`password`,`usertype`) VALUES ('user4',password('123456'),'employee,admin,manager');

CREATE TABLE IF NOT EXISTS `employees` (
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
	`e_salary` INT(6) NOT NULL,
	`e_email` VARCHAR(50) NOT NULL UNIQUE,
	PRIMARY KEY (`employee_id`),
	FOREIGN KEY (`userid`) REFERENCES Users(`userid`)
);

INSERT INTO `employees` (`userid`,`e_first_name`,`e_last_name`,`e_street_addr`,`e_city`,`e_state`,`e_country`,`e_marriage_status`,`e_gender`,`e_dob`,`e_phone`,`e_salary`,`e_email`) 
			VALUES ('4','Nguyen','Tran','123 street','san jose','CA','US','married','male','1991-04-15','98765432','90000','nt@nelshop.com');

INSERT INTO `employees` (`userid`,`e_first_name`,`e_last_name`,`e_street_addr`,`e_city`,`e_state`,`e_country`,`e_marriage_status`,`e_gender`,`e_dob`,`e_phone`,`e_salary`,`e_email`) 
			VALUES ('2','Nelly','Phan','123 NY','san jose','CA','US','married','female','1991-11-23','87873845','50000','np@nelshop.com');

INSERT INTO `employees` (`userid`,`e_first_name`,`e_last_name`,`e_street_addr`,`e_city`,`e_state`,`e_country`,`e_marriage_status`,`e_gender`,`e_dob`,`e_phone`,`e_salary`,`e_email`) 
			VALUES ('1','Chris','Tran','323 WA','san jose','CA','US','single','male','1991-04-15','99999999','70000','ct@nelshop.com');

INSERT INTO `employees` (`userid`,`e_first_name`,`e_last_name`,`e_street_addr`,`e_city`,`e_state`,`e_country`,`e_marriage_status`,`e_gender`,`e_dob`,`e_phone`,`e_salary`,`e_email`) 
			VALUES ('3','Ryan','Phan','1123 NE str','san jose','CA','US','single','male','1995-08-11','13326984','45000','rp@nelshop.com');

			
	
CREATE TABLE IF NOT EXISTS `products` (
	`product_id` INT(10) UNSIGNED AUTO_INCREMENT,
	`product_name` VARCHAR(100) NOT NULL,
	`product_price` DECIMAL(10,2) NOT NULL,
	`product_description` VARCHAR(1000) NOT NULL,
	`ingredients` VARCHAR(500) NOT NULL,
	`recipe` VARCHAR(10000) NOT NULL,
	PRIMARY KEY (`product_id`)
);

INSERT INTO `products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('chicken gizzard','8.5','chicken gizzard roasted','chicken gizzard,pepper','will be added');
INSERT INTO `products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('Pho','7.5','Famous Vietnamese soup noodle','White noodle, beef,...','will be added');
INSERT INTO `products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('Pad Thai','9.5','Spicy Thai food','thin noodle, beef/chicken/shrimp/tofu,...','will be added');
INSERT INTO `products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('beef satay','8.0','Indonesian spicy beef skewers','beef, chilly, peanut,...','will be added');			
INSERT INTO `products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('Khnoer red curry','8.0','Cambodian curry','beef/chicken, chilly, coconut,...','will be added');			
		
INSERT INTO `products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('Spaghetti','10.5','Italian spaghetti','spaghetti squash,lean ground pork, cheese, pepper','will be added');
INSERT INTO `products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('Pasta','9.5','Italian pasta','pasta,lean ground pork, eggs, flour,...','will be added');
INSERT INTO `products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('Macarons','11.5','French desert','almond, egg whites, sugar, flour,...','will be added');
INSERT INTO `products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('Yorkshire Pudding','8.5','English desert','eggs, flour, milk, sugar,...','will be added');


INSERT INTO `products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('Hamburger','8.0','Classic American hamburger','burger bun, round beef, vegetables, cheese,...','will be added');
INSERT INTO `products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('Hot dog','6.5','American hot dog','pork/beef/chicken, bread, sausage,...','will be added');
INSERT INTO `products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('Beef steak','11.5','Beef Steak','beef, onion, sauce,...','will be added');
INSERT INTO `products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('Cinnamon roll','8.5','American desert','yearst, milk, egg, butter, milk,...','will be added');
	
INSERT INTO `products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('Wonton noodle','8.0','Classic Chinese soup','egg noodle,pork/shrimp, flour, eggs,...','will be added');
INSERT INTO `products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('Kimchi','6.0','Korean tradional dish','lettuce, cucumber, chilly sauce,...','will be added');
INSERT INTO `products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('Bibimbap','9.5','Korean mixed rice','beef/pork/shrimp, rice, vegetables, bean sprout, carrot, eggs,...','will be added');
INSERT INTO `products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('Topokki','7.0','Korean street food','rice cake, fish cake, onion, soy sauce, eggs,...','will be added');
INSERT INTO `products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('Sushi','10.5','Japanese world famous food','rice, fish, seaweed, vegetables, alvocado,...','will be added');
INSERT INTO `products` (`product_name`,`product_price`,`product_description`,`ingredients`,`recipe`) 
			VALUES ('Yakisoba','9.0','Japanese egg noodle','yellow noodle, carrot, onion, cabbage, scallion, oyster sauce,...','will be added');
			
CREATE TABLE IF NOT EXISTS `product_categories` (
	`category_id` INT(10) UNSIGNED AUTO_INCREMENT,
	`category_name` VARCHAR(100) NOT NULL,
	`category_description` VARCHAR(1000) NOT NULL,
	PRIMARY KEY (`category_id`)
);

INSERT INTO `product_categories` (`category_name`,`category_description`) VALUES ('SouthEast Asia cuisine','Vietnam, Thailand, Cambodia...');
INSERT INTO `product_categories` (`category_name`,`category_description`) VALUES ('European cuisine','Italy, France, England, Germany...');
INSERT INTO `product_categories` (`category_name`,`category_description`) VALUES ('American cuisine','United States, Canada, Mexico...');
INSERT INTO `product_categories` (`category_name`,`category_description`) VALUES ('NorthEast Asian cuisine','China, Japan, Korea...');


CREATE TABLE IF NOT EXISTS `product_and_category` (
	`product_id` INT(10) UNSIGNED NOT NULL,
	`category_id` INT(10) UNSIGNED NOT NULL,
	PRIMARY KEY (`product_id`,`category_id`),
	FOREIGN KEY (`product_id`) REFERENCES Products(`product_id`),
	FOREIGN KEY (`category_id`) REFERENCES Product_categories(`category_id`)
);
INSERT INTO `product_and_category` VALUES ('1','1');
INSERT INTO `product_and_category` VALUES ('2','1');
INSERT INTO `product_and_category` VALUES ('3','1');
INSERT INTO `product_and_category` VALUES ('4','1');
INSERT INTO `product_and_category` VALUES ('5','1');

INSERT INTO `product_and_category` VALUES ('6','2');
INSERT INTO `product_and_category` VALUES ('7','2');
INSERT INTO `product_and_category` VALUES ('8','2');
INSERT INTO `product_and_category` VALUES ('9','2');
INSERT INTO `product_and_category` VALUES ('13','2');

INSERT INTO `product_and_category` VALUES ('10','3');
INSERT INTO `product_and_category` VALUES ('11','3');
INSERT INTO `product_and_category` VALUES ('12','3');
INSERT INTO `product_and_category` VALUES ('13','3');

INSERT INTO `product_and_category` VALUES ('14','4');
INSERT INTO `product_and_category` VALUES ('15','4');
INSERT INTO `product_and_category` VALUES ('16','4');
INSERT INTO `product_and_category` VALUES ('17','4');
INSERT INTO `product_and_category` VALUES ('18','4');
INSERT INTO `product_and_category` VALUES ('19','4');
INSERT INTO `product_and_category` VALUES ('1','4');

CREATE TABLE IF NOT EXISTS `special_sales` (
	`special_sale_id` INT(10) UNSIGNED AUTO_INCREMENT,
	`start_date` DATE NOT NULL,
	`end_date` DATE NOT NULL,
	`percentage_discount` DECIMAL(4,2) NOT NULL,
	PRIMARY KEY (`special_sale_id`)
);

INSERT INTO `special_sales` (`start_date`,`end_date`,`percentage_discount`)
			VALUES ('2015-06-10','2015-06-17','5');
INSERT INTO `special_sales` (`start_date`,`end_date`,`percentage_discount`)
			VALUES ('2015-01-11','2015-02-20','10');
INSERT INTO `special_sales` (`start_date`,`end_date`,`percentage_discount`)
			VALUES ('2015-07-10','2015-07-17','5');
INSERT INTO `special_sales` (`start_date`,`end_date`,`percentage_discount`)
			VALUES ('2015-07-13','2015-07-20','5');
INSERT INTO `special_sales` (`start_date`,`end_date`,`percentage_discount`)
			VALUES ('2015-06-29','2015-07-05','15');
INSERT INTO `special_sales` (`start_date`,`end_date`,`percentage_discount`)
			VALUES ('2015-07-25','2015-07-31','5');
INSERT INTO `special_sales` (`start_date`,`end_date`,`percentage_discount`)
			VALUES ('2015-08-10','2015-08-17','5');
INSERT INTO `special_sales` (`start_date`,`end_date`,`percentage_discount`)
			VALUES ('2015-08-20','2015-08-22','15');
			
CREATE TABLE IF NOT EXISTS `special_sales_and_product` (
	`special_sale_id` INT(10) UNSIGNED NOT NULL,
	`product_id` INT(10) UNSIGNED NOT NULL UNIQUE,
	PRIMARY KEY (`special_sale_id`,`product_id`),
	FOREIGN KEY (`product_id`) REFERENCES Products(`product_id`)
);
INSERT INTO `special_sales_and_product` VALUES ('1','1');
INSERT INTO `special_sales_and_product` VALUES ('5','10');
INSERT INTO `special_sales_and_product` VALUES ('7','5');
INSERT INTO `special_sales_and_product` VALUES ('3','14');
INSERT INTO `special_sales_and_product` VALUES ('1','19');
