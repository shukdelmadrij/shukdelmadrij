INSERT INTO `#__autotweet_channeltypes`
	(`name`, `description`, `max_chars`) 
	VALUES ('VK', 'COM_AUTOTWEET_CHANNEL_VK_DESC', 320);
	
INSERT INTO `#__autotweet_channeltypes`
	(`name`, `description`, `max_chars`) 
	VALUES ('VKGroup', 'COM_AUTOTWEET_CHANNEL_VK_DESC', 320);
	
UPDATE `#__autotweet_channeltypes` SET 
	`auth_url` = 'https://apps.facebook.com/autotweetsisvn/index.php' WHERE `name` LIKE 'Faceboo%';
	