DROP TABLE IF EXISTS `#__autotweet_channeltypes`;
CREATE TABLE IF NOT EXISTS `#__autotweet_channeltypes` (
  `id` int(11) NOT NULL,
  `name` varchar(64), 
  `description` varchar(1024),
  `max_chars` int(4),
  `auth_url` varchar(255),
  `auth_key` varchar(255),
  `auth_secret` varchar(255),
  `field_keys` varchar(255),
  `field_names` varchar(255),
  `selection_values` varchar(255),
  `own_api_allowed` tinyint(1),
  `api_field_keys` varchar(255),
  `api_field_names` varchar(255),
  PRIMARY KEY  (`id`)
) DEFAULT CHARACTER SET utf8;

INSERT INTO `#__autotweet_channeltypes`
	(`id`, `name`, `description`, `max_chars`) 
	VALUES (1, 'Twitter', 'COM_AUTOTWEET_CHANNEL_TWITTER_DESC', 140);
	
INSERT INTO `#__autotweet_channeltypes`
	(`id`, `name`, `description`, `max_chars`, `auth_url`, `auth_key`, `auth_secret`, `field_keys`, `field_names`, `selection_values`, `own_api_allowed`, `api_field_keys`, `api_field_names`) 
	VALUES (2, 'Facebook', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_DESC', 320, 'https://apps.facebook.com/autotweetsvtw/index.php', 'TXktQXBwLUlE', 'TXktQXBwLVNlY3JldA==', 'id_1,id_2', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID1,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID2', '', 0, 'api_key,api_secret,api_authurl', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIKEY,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APISECRET,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIAUTHURL');
	
INSERT INTO `#__autotweet_channeltypes`
	(`id`, `name`, `description`, `max_chars`) 
	VALUES (3, 'Mail', 'COM_AUTOTWEET_CHANNEL_MAIL_DESC', 16384);
	
INSERT INTO `#__autotweet_channeltypes`
	(`id`, `name`, `description`, `max_chars`) 
	VALUES (5, 'LinkedIn', 'COM_AUTOTWEET_CHANNEL_LINKEDIN_DESC', 200);

INSERT INTO `#__autotweet_channeltypes`
	(`id`, `name`, `description`, `max_chars`) 
	VALUES (6, 'LinkedIn Group', 'COM_AUTOTWEET_CHANNEL_LINKEDINGROUP_DESC', 200);	

INSERT INTO `#__autotweet_channeltypes`
	(`id`, `name`, `description`, `max_chars`, `auth_url`, `auth_key`, `auth_secret`, `field_keys`, `field_names`, `selection_values`, `own_api_allowed`, `api_field_keys`, `api_field_names`) 
	VALUES (7, 'Facebook Link', 'COM_AUTOTWEET_CHANNEL_FACEBOOKLINK_DESC', 320, 'https://apps.facebook.com/autotweetsvtw/index.php', 'TXktQXBwLUlE', 'TXktQXBwLVNlY3JldA==', 'id_1,id_2', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID1,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID2', '', 0, 'api_key,api_secret,api_authurl', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIKEY,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APISECRET,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIAUTHURL');

INSERT INTO `#__autotweet_channeltypes`
	(`id`, `name`, `description`, `max_chars`, `auth_url`, `auth_key`, `auth_secret`, `field_keys`, `field_names`, `selection_values`, `own_api_allowed`, `api_field_keys`, `api_field_names`) 
	VALUES (8, 'Facebook Photo', 'COM_AUTOTWEET_CHANNEL_FACEBOOKPHOTO_DESC', 320, 'https://apps.facebook.com/autotweetsvtw/index.php', 'TXktQXBwLUlE', 'TXktQXBwLVNlY3JldA==', 'id_1,id_2', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID1,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID2', '', 0, 'api_key,api_secret,api_authurl', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIKEY,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APISECRET,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIAUTHURL');
	
INSERT INTO `#__autotweet_channeltypes`
	(`id`, `name`, `description`, `max_chars`) 
	VALUES (10, 'LinkedIn Company', 'COM_AUTOTWEET_CHANNEL_LINKEDINCOMPANY_DESC', 200);
	
INSERT INTO `#__autotweet_channeltypes`
	(`id`, `name`, `description`, `max_chars`) 
	VALUES (11, 'VK (Beta)', 'COM_AUTOTWEET_CHANNEL_VK_DESC', 320);
	
INSERT INTO `#__autotweet_channeltypes`
	(`id`, `name`, `description`, `max_chars`) 
	VALUES (12, 'VK Group (Beta)', 'COM_AUTOTWEET_CHANNEL_VK_DESC', 320);
	
INSERT INTO `#__autotweet_channeltypes`
	(`id`, `name`, `description`, `max_chars`) 
	VALUES (13, 'Google+ Moments', 'COM_AUTOTWEET_CHANNEL_GPMOMENTS_DESC', 320);