CREATE TABLE `users` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `email` varchar(40) NOT NULL,
 `firstname` varchar(20) NOT NULL,
 `lastname` varchar(20) NOT NULL,
 `birth` date NOT NULL,
 `salt` varchar(60) NOT NULL,
 `passwordhash` varchar(60) NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `location` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(50) NOT NULL,
 `description` text NOT NULL,
 `phone` varchar(15) NOT NULL,
 `country` varchar(30) NOT NULL,
 `state` varchar(30) NOT NULL,
 `city` varchar(30) NOT NULL,
 `postal code` varchar(10) NOT NULL,
 `address` tinytext NOT NULL,
 `x` decimal(10,8) NOT NULL,
 `y` decimal(11,8) NOT NULL,
 `picture` varchar(100) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `hours` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `day` int(11) NOT NULL COMMENT '0=Sunday, 6=Saturday',
 `open` time NOT NULL,
 `close` time NOT NULL,
 `locationid` int(11) NOT NULL,
 PRIMARY KEY (`id`),
 KEY `hours and location` (`locationid`),
 CONSTRAINT `hours and location` FOREIGN KEY (`locationid`) REFERENCES `location` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `review` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `userid` int(11) NOT NULL,
 `locationid` int(11) NOT NULL,
 `rating` float NOT NULL,
 `date` date NOT NULL,
 `review` text NOT NULL,
 PRIMARY KEY (`id`),
 KEY `location` (`locationid`),
 KEY `user` (`userid`),
 CONSTRAINT `location` FOREIGN KEY (`locationid`) REFERENCES `location` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
 CONSTRAINT `user` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;