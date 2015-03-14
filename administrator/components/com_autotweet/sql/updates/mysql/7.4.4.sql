
ALTER TABLE `#__autotweet_advanced_attrs` CHANGE `option` `option` VARCHAR(32);
ALTER TABLE `#__autotweet_advanced_attrs` CHANGE `controller` `controller` VARCHAR(32);
ALTER TABLE `#__autotweet_advanced_attrs` CHANGE `task` `task` VARCHAR(32);
ALTER TABLE `#__autotweet_advanced_attrs` CHANGE `view` `view` VARCHAR(32);
ALTER TABLE `#__autotweet_advanced_attrs` CHANGE `layout` `layout` VARCHAR(32);
ALTER TABLE `#__autotweet_advanced_attrs` CHANGE `ref_id` `ref_id` VARCHAR(32);

ALTER TABLE `#__autotweet_requests` CHANGE `description` `description` VARCHAR(2560);

ALTER TABLE `#__autotweet_posts` CHANGE `message` `message` VARCHAR(2560);
ALTER TABLE `#__autotweet_posts` CHANGE `title` `title` VARCHAR(2560);
ALTER TABLE `#__autotweet_posts` CHANGE `fulltext` `fulltext` VARCHAR(5120);
