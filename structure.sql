
CREATE TABLE `users` (
  `discordID` varchar(18) NOT NULL,
  `banned` tinyint(1) NOT NULL,
  `name` text NOT NULL,
  `discriminator` varchar(4) NOT NULL,
  `avatar` text NOT NULL,
  `email` text NOT NULL,
  `joinDate` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE blogs (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `author` text NOT NULL,
  `date` text NOT NULL,
  `image` text NOT NULL,  
  `tags` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE siteSettings (
  `id` int NOT NULL AUTO_INCREMENT,
  siteName text NOT NULL,
  siteDescription text NOT NULL,
  siteLogo text NOT NULL,
  siteTheme text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `heartslikes` (
	`userid` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`which` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`blogId` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO siteSettings (siteName, siteDescription, siteLogo, siteTheme) VALUES ('Blog Site', 'A blog site that allows you to share your thoughts with the world.', 'https://pastenow.xyz/images/standard_logo.png', '#2d7cb5');

ALTER TABLE `users`
  ADD PRIMARY KEY (`discordID`),
  ADD UNIQUE KEY `discordID` (`discordID`);
COMMIT;
