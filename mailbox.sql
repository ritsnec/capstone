--
-- Table structure for table `pm`
--

CREATE TABLE `mailbox` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`subject` varchar(256) NOT NULL,
	`sender` bigint(20) NOT NULL,
	`recipient` bigint(20) NOT NULL,
	`message` text NOT NULL,
	`timestamp` int(10) NOT NULL,
	`tag` varchar(255) NOT NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`sender`) REFERENCES users(`id`),
	FOREIGN KEY (`recipient`) REFERENCES users(`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
