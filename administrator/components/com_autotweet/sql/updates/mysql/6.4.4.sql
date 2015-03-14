ALTER TABLE `#__autotweet_rules` CHANGE `autopublish` `autopublish` ENUM( 'default', 'on', 'off', 'cancel' ) NOT NULL DEFAULT 'default';
