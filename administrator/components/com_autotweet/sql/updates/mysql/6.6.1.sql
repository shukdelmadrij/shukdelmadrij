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

UPDATE `#__autotweet_channeltypes` SET 
	`auth_url` = 'https://apps.facebook.com/autotweetsione/index.php' WHERE `name` LIKE 'Faceboo%';