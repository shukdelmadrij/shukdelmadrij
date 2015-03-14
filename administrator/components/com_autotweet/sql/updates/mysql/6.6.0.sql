INSERT INTO `#__autotweet_channeltypes`
	(`name`, `description`, `max_chars`) 
	VALUES ('LinkedInCompany', 'COM_AUTOTWEET_CHANNEL_LINKEDINCOMPANY_DESC', 200);
	
UPDATE `#__autotweet_channeltypes` SET 
	`auth_url` = 'https://apps.facebook.com/autotweetsisi/index.php' WHERE `name` LIKE 'Faceboo%';