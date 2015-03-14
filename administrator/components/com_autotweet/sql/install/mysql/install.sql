CREATE TABLE IF NOT EXISTS `#__autotweet_requests` (
  `id` int(11) NOT NULL auto_increment,
  `ref_id` varchar(32),
  `plugin` varchar(64), 
  `publish_up` datetime,
  `description` varchar(2560),
  `typeinfo` tinyint(1),
  `url` varchar(512),
  `image_url` varchar(512),
  `native_object` text,

  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` longtext NOT NULL DEFAULT '',
  `published` int(11) NOT NULL DEFAULT '0',

  PRIMARY KEY  (`id`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__autotweet_posts` (
  `id` int(11) NOT NULL auto_increment,
  `ref_id` varchar(32),
  `plugin` varchar(64), 
  `channel_id` int(11),
  `postdate` datetime,
  `pubstate` enum('error', 'success', 'approve', 'cronjob', 'cancelled') NOT NULL default 'error',  
  `resultmsg` varchar(255),
  `message` varchar(2560),
  `url` varchar(512),
  `org_url` varchar(512),
  `image_url` varchar(512),
  `title` varchar(2560),
  `fulltext` varchar(5120),
  `show_url` enum('off', 'beginning_of_message', 'end_of_message') NOT NULL default 'end_of_message', 
  `event_data` varchar(512),

  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` longtext NOT NULL DEFAULT '',

   PRIMARY KEY  (`id`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__autotweet_channels` (
	`id` int(11) NOT NULL auto_increment,
	`published` int(11) NOT NULL DEFAULT '1',
	`channeltype_id` int(11),
	`scope` varchar(1) NOT NULL DEFAULT 'S',
	`autopublish` INT(11) NOT NULL DEFAULT '1',
	`name` varchar(64), 
	`description` varchar(512),

	`media_mode` enum('message', 'attachment', 'both') NOT NULL default 'message',

	`status` enum('verified', 'not-verified', 'error') NOT NULL default 'not-verified',
	`error_message` varchar(512),

	`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`created_by` int(11) NOT NULL DEFAULT '0',
	`modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified_by` int(11) NOT NULL DEFAULT '0',
	`checked_out` int(11) NOT NULL DEFAULT '0',
	`checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`ordering` int(11) NOT NULL DEFAULT '0',
	`params` longtext NOT NULL DEFAULT '',

	 PRIMARY KEY  (`id`)
) DEFAULT CHARACTER SET utf8;

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
	VALUES (2, 'Facebook', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_DESC', 420, 'https://apps.facebook.com/autotweetsvtw/index.php', 'TXktQXBwLUlE', 'TXktQXBwLVNlY3JldA==', 'id_1,id_2', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID1,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID2', '', 0, 'api_key,api_secret,api_authurl', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIKEY,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APISECRET,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIAUTHURL');
	
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
	VALUES (7, 'Facebook Link', 'COM_AUTOTWEET_CHANNEL_FACEBOOKLINK_DESC', 420, 'https://apps.facebook.com/autotweetsvtw/index.php', 'TXktQXBwLUlE', 'TXktQXBwLVNlY3JldA==', 'id_1,id_2', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID1,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID2', '', 0, 'api_key,api_secret,api_authurl', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIKEY,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APISECRET,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIAUTHURL');

INSERT INTO `#__autotweet_channeltypes`
	(`id`, `name`, `description`, `max_chars`, `auth_url`, `auth_key`, `auth_secret`, `field_keys`, `field_names`, `selection_values`, `own_api_allowed`, `api_field_keys`, `api_field_names`) 
	VALUES (8, 'Facebook Photo', 'COM_AUTOTWEET_CHANNEL_FACEBOOKPHOTO_DESC', 420, 'https://apps.facebook.com/autotweetsvtw/index.php', 'TXktQXBwLUlE', 'TXktQXBwLVNlY3JldA==', 'id_1,id_2', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID1,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_ID2', '', 0, 'api_key,api_secret,api_authurl', 'COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIKEY,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APISECRET,COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIAUTHURL');
	
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

CREATE TABLE IF NOT EXISTS `#__autotweet_rules` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64),
  `published` tinyint(1),
  `ruletype_id` int(11),
  `plugin` varchar(64), 
  `channel_id` int(11),
  `cond` varchar(512),
  `autopublish` enum('default','on', 'off', 'cancel') NOT NULL default 'default', 
  `rmc_textpattern` varchar(512),
  `show_url` enum('default', 'off', 'beginning_of_message', 'end_of_message') NOT NULL default 'end_of_message',
  `show_static_text` enum('off', 'beginning_of_message', 'end_of_message') NOT NULL default 'off',
  `statix_text` varchar(64),
  `reg_ex` longtext,
  `reg_replace` longtext,

  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` longtext NOT NULL DEFAULT '',

  PRIMARY KEY  (`id`)
) DEFAULT CHARACTER SET utf8;
	
DROP TABLE IF EXISTS `#__autotweet_ruletypes`;
CREATE TABLE IF NOT EXISTS `#__autotweet_ruletypes` (
  `id` int(11) NOT NULL,
  `name` varchar(64), 
  `description` varchar(512),
  PRIMARY KEY  (`id`)
) DEFAULT CHARACTER SET utf8;

INSERT INTO `#__autotweet_ruletypes`
	(`id`, `name`, `description`) VALUES (1, 'category: IN', 'COM_AUTOTWEET_RULE_CATEGORYIN_DESC');

INSERT INTO `#__autotweet_ruletypes`
	(`id`, `name`, `description`) VALUES (2, 'category: NOT IN', 'COM_AUTOTWEET_RULE_CATEGORYNOTIN_DESC');

INSERT INTO `#__autotweet_ruletypes`
	(`id`, `name`, `description`) VALUES (3, 'term: OR', 'COM_AUTOTWEET_RULE_TERMOR_DESC');

INSERT INTO `#__autotweet_ruletypes`
	(`id`, `name`, `description`) VALUES (4, 'term: AND', 'COM_AUTOTWEET_RULE_TERMAND_DESC');
	
INSERT INTO `#__autotweet_ruletypes`
	(`id`, `name`, `description`) VALUES (5, 'catch all not fits', 'COM_AUTOTWEET_RULE_CATCHALLNOTFITS_DESC');

INSERT INTO `#__autotweet_ruletypes`
	(`id`, `name`, `description`) VALUES (6, 'word term: OR', 'COM_AUTOTWEET_RULE_WORDTERMOR_DESC');

INSERT INTO `#__autotweet_ruletypes`
	(`id`, `name`, `description`) VALUES (7, 'word term: AND', 'COM_AUTOTWEET_RULE_WORDTERMAND_DESC');

INSERT INTO `#__autotweet_ruletypes`
	(`id`, `name`, `description`) VALUES (8, 'regular expression match', 'COM_AUTOTWEET_RULE_REGEX_DESC');

INSERT INTO `#__autotweet_ruletypes`
	(`id`, `name`, `description`) VALUES (9, 'term: NOT IN', 'COM_AUTOTWEET_RULE_TERMNOTIN_DESC');
	
INSERT INTO `#__autotweet_ruletypes`
	(`id`, `name`, `description`) VALUES (10, 'word term: NOT IN', 'COM_AUTOTWEET_RULE_WORDTERMNOTIN_DESC');

INSERT INTO `#__autotweet_ruletypes`
	(`id`, `name`, `description`) VALUES (11, 'author: IN', 'COM_AUTOTWEET_RULE_AUTHORIN_DESC');

INSERT INTO `#__autotweet_ruletypes`
	(`id`, `name`, `description`) VALUES (12, 'author: NOT IN', 'COM_AUTOTWEET_RULE_AUTHORNOTIN_DESC');

INSERT INTO `#__autotweet_ruletypes`
	(`id`, `name`, `description`) VALUES (13, 'catch all', 'COM_AUTOTWEET_RULE_CATCHALL_DESC');

INSERT INTO `#__autotweet_ruletypes`
	(`id`, `name`, `description`) VALUES (14, 'language: IN', 'COM_AUTOTWEET_RULE_LANGUAGEIN_DESC');

INSERT INTO `#__autotweet_ruletypes`
	(`id`, `name`, `description`) VALUES (15, 'language: NOT IN', 'COM_AUTOTWEET_RULE_LANGUAGENOTIN_DESC');

INSERT INTO `#__autotweet_ruletypes`
	(`id`, `name`, `description`) VALUES (16, 'access: IN', 'COM_AUTOTWEET_RULE_ACCESSIN_DESC');

INSERT INTO `#__autotweet_ruletypes`
	(`id`, `name`, `description`) VALUES (17, 'access: NOT IN', 'COM_AUTOTWEET_RULE_ACCESSNOTIN_DESC');
	
INSERT INTO `#__autotweet_ruletypes`
	(`id`, `name`, `description`) VALUES (18, 'source: IS User Channel', 'COM_AUTOTWEET_RULE_SOURCEFRONT_DESC');
	
INSERT INTO `#__autotweet_ruletypes`
	(`id`, `name`, `description`) VALUES (19, 'source: IS NOT User Channel', 'COM_AUTOTWEET_RULE_SOURCEBACK_DESC');		

DROP TABLE IF EXISTS `#__autotweet_automator`;
CREATE TABLE IF NOT EXISTS `#__autotweet_automator` (
  `id` int(11) NOT NULL auto_increment,
  `plugin` varchar(50) NOT NULL, 
  `lastexec` timestamp,
   PRIMARY KEY  (`id`)
  ) DEFAULT CHARACTER SET utf8;
  
INSERT INTO `#__autotweet_automator`
	(`plugin`,`lastexec`) VALUES ('automator', NOW());
INSERT INTO `#__autotweet_automator`
	(`plugin`,`lastexec`) VALUES ('content', NOW());

CREATE TABLE IF NOT EXISTS `#__autotweet_targets` (
  `id` int(11) NOT NULL auto_increment,

  `name` varchar(64),
  `channel_id` int(11), 
  `description` varchar(512),

  `published` int(11) NOT NULL DEFAULT '0',

  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL DEFAULT '',

  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',

  PRIMARY KEY  (`id`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__autotweet_feeds` (
  `id` int(11) NOT NULL auto_increment,

  `name` varchar(64),
  `published` int(11) NOT NULL DEFAULT '0',

  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL DEFAULT '',

  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',

  PRIMARY KEY  (`id`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__autotweet_advanced_attrs` (
  `id` int(11) NOT NULL auto_increment,
  
  `client_id` int(11),
  `option` varchar(32),  
  `controller` varchar(32),
  `task` varchar(32),  
  `view` varchar(32),
  `layout` varchar(32),
  `ref_id` varchar(32),
    
  `params` text NOT NULL DEFAULT '',
  
  `request_id` int(11),
  `evergreentype_id` int(11),
	
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	
  PRIMARY KEY  (`id`),
  UNIQUE KEY `object_attrs` (`option`, `controller`, `task`, `view`, `layout`, `ref_id`),
  INDEX `request_attrs` (`request_id`)
) DEFAULT CHARACTER SET utf8;