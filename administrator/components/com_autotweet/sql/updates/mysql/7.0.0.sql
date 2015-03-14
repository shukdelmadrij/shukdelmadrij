CREATE TABLE IF NOT EXISTS `#__autotweet_advanced_attrs` (
  `id` int(11) NOT NULL auto_increment,
  
  `client_id` int(11),
  `option` varchar(64),  
  `controller` varchar(64),
  `task` varchar(64),  
  `view` varchar(64),
  `layout` varchar(64),
  `ref_id` int(11),
    
  `params` text NOT NULL DEFAULT '',
	
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	
  PRIMARY KEY  (`id`),
  UNIQUE KEY `object_attrs` (`option`, `controller`, `task`, `view`, `layout`, `ref_id`)
) DEFAULT CHARACTER SET utf8;

DROP TABLE IF EXISTS `#__autotweet_channeltypes`;
CREATE TABLE IF NOT EXISTS `#__autotweet_channeltypes` (
  `id` int(11) NOT NULL auto_increment,
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
	(`name`, `description`, `max_chars`) 
	VALUES ('Twitter', 'COM_AUTOTWEET_CHANNEL_TWITTER_DESC', 140);
	
INSERT INTO `#__autotweet_channeltypes`
	(`name`, `description`, `max_chars`, `auth_url`, `auth_key`, `auth_secret`, `field_keys`, `field_names`, `selection_values`, `own_api_allowed`, `api_field_keys`, `api_field_names`) 
	VALUES ('Facebook', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_DESC', 320, 'https://apps.facebook.com/autotweetsvzz/index.php', 'TXktQXBwLUlE', 'TXktQXBwLVNlY3JldA==', 'id_1,id_2', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID1,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID2', '', 0, 'api_key,api_secret,api_authurl', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIKEY,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APISECRET,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIAUTHURL');
	
INSERT INTO `#__autotweet_channeltypes`
	(`name`, `description`, `max_chars`) 
	VALUES ('Mail', 'COM_AUTOTWEET_CHANNEL_MAIL_DESC', 16384);
	
INSERT INTO `#__autotweet_channeltypes`
	(`name`, `description`, `max_chars`, `auth_url`, `auth_key`, `auth_secret`, `field_keys`, `field_names`, `selection_values`, `own_api_allowed`, `api_field_keys`, `api_field_names`)
	VALUES ('Facebook Event - Deprecated', 'COM_AUTOTWEET_CHANNEL_FACEBOOKEVENT_DESC', 420, 'https://apps.facebook.com/autotweetsvzz/index.php', 'TXktQXBwLUlE', 'TXktQXBwLVNlY3JldA==', 'id_1,id_2', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID1,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID2', '', 0, 'api_key,api_secret,api_authurl', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIKEY,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APISECRET,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIAUTHURL');

INSERT INTO `#__autotweet_channeltypes`
	(`name`, `description`, `max_chars`) 
	VALUES ('LinkedIn', 'COM_AUTOTWEET_CHANNEL_LINKEDIN_DESC', 200);

INSERT INTO `#__autotweet_channeltypes`
	(`name`, `description`, `max_chars`) 
	VALUES ('LinkedIn Group', 'COM_AUTOTWEET_CHANNEL_LINKEDINGROUP_DESC', 200);	

INSERT INTO `#__autotweet_channeltypes`
	(`name`, `description`, `max_chars`, `auth_url`, `auth_key`, `auth_secret`, `field_keys`, `field_names`, `selection_values`, `own_api_allowed`, `api_field_keys`, `api_field_names`) 
	VALUES ('Facebook Link', 'COM_AUTOTWEET_CHANNEL_FACEBOOKLINK_DESC', 320, 'https://apps.facebook.com/autotweetsvzz/index.php', 'TXktQXBwLUlE', 'TXktQXBwLVNlY3JldA==', 'id_1,id_2', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID1,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID2', '', 0, 'api_key,api_secret,api_authurl', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIKEY,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APISECRET,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIAUTHURL');

INSERT INTO `#__autotweet_channeltypes`
	(`name`, `description`, `max_chars`, `auth_url`, `auth_key`, `auth_secret`, `field_keys`, `field_names`, `selection_values`, `own_api_allowed`, `api_field_keys`, `api_field_names`) 
	VALUES ('Facebook Photo', 'COM_AUTOTWEET_CHANNEL_FACEBOOKPHOTO_DESC', 320, 'https://apps.facebook.com/autotweetsvzz/index.php', 'TXktQXBwLUlE', 'TXktQXBwLVNlY3JldA==', 'id_1,id_2', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID1,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID2', '', 0, 'api_key,api_secret,api_authurl', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIKEY,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APISECRET,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIAUTHURL');

INSERT INTO `#__autotweet_channeltypes`
	(`name`, `description`, `max_chars`, `auth_url`, `auth_key`, `auth_secret`, `field_keys`, `field_names`, `selection_values`, `own_api_allowed`, `api_field_keys`, `api_field_names`) 
	VALUES ('Facebook Video', 'COM_AUTOTWEET_CHANNEL_FACEBOOKVIDEO_DESC', 320, 'https://apps.facebook.com/autotweetsvzz/index.php', 'TXktQXBwLUlE', 'TXktQXBwLVNlY3JldA==', 'id_1,id_2', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID1,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID2', '', 0, 'api_key,api_secret,api_authurl', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIKEY,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APISECRET,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIAUTHURL');
	
INSERT INTO `#__autotweet_channeltypes`
	(`name`, `description`, `max_chars`) 
	VALUES ('LinkedIn Company', 'COM_AUTOTWEET_CHANNEL_LINKEDINCOMPANY_DESC', 200);
	
INSERT INTO `#__autotweet_channeltypes`
	(`name`, `description`, `max_chars`) 
	VALUES ('VK (Beta)', 'COM_AUTOTWEET_CHANNEL_VK_DESC', 320);
	
INSERT INTO `#__autotweet_channeltypes`
	(`name`, `description`, `max_chars`) 
	VALUES ('VK Group (Beta)', 'COM_AUTOTWEET_CHANNEL_VK_DESC', 320);
	
INSERT INTO `#__autotweet_channeltypes`
	(`name`, `description`, `max_chars`) 
	VALUES ('G+ Moments', 'COM_AUTOTWEET_CHANNEL_GPMOMENTS_DESC', 320);	
