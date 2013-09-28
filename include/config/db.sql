
CREATE TABLE `users` (
	`uid`				int(11)			auto_increment,
	`datetime`			datetime		NOT NULL default '0000-00-00 00:00:00',
	`user`				int(11)			NOT NULL default 0,
	`username`			varchar(50)		NOT NULL default '',
	`password`			varchar(50)		NOT NULL default '',
	`first_name`		varchar(50)		NOT NULL default '',
	`last_name` 		varchar(50)		NOT NULL default '',
	`email`				varchar(255)	NOT NULL default '',
	`tel`				varchar(30)		NOT NULL default '',
	`mobile`			varchar(30)		NOT NULL default '',
	`fax`				varchar(30)		NOT NULL default '',
	`notes`				varchar(255)	NOT NULL default '',
	`active`			int(1)			NOT NULL default 1,
	PRIMARY KEY (`uid`)
);

CREATE TABLE `groups` (
	`uid`				int(11)			auto_increment,
	`datetime`			datetime		NOT NULL default '0000-00-00 00:00:00',
	`user`				int(11)			NOT NULL default 0,
	`name`				varchar(100)	NOT NULL default '',
	`active`			int(1)			NOT NULL default 1,
	PRIMARY KEY (`uid`)
);

CREATE TABLE `user_groups` (
	`uid`				int(11)			auto_increment,
	`datetime`			datetime		NOT NULL default '0000-00-00 00:00:00',
	`user`				int(11)			NOT NULL default 0,
	`user_id`			int(11)			NOT NULL default 0,
	`group_id`			int(11)			NOT NULL default 0,
	`active`			int(1)			NOT NULL default 1,
	PRIMARY KEY (`uid`)
);

CREATE TABLE `folders` (
	`uid`				int(11)			auto_increment,
	`datetime`			datetime		NOT NULL default '0000-00-00 00:00:00',
	`user`				int(11)			NOT NULL default 0,
	`name`				varchar(200)	NOT NULL default '',
	`parent`			int(11)			NOT NULL default 0,
	`active`			int(1)			NOT NULL default 1,
	PRIMARY KEY (`uid`)
);

CREATE TABLE `files` (
	`uid`				int(11)			NOT NULL auto_increment,
	`file`				varchar(255)	NOT NULL default '',
	`item`				varchar(255)	NOT NULL default '',
	`datetime`			datetime		NOT NULL default '0000-00-00 00:00:00',
	`user`				int(11)			NOT NULL default '0',
	`revision`			int(5)			NOT NULL default 0,
	`type`				varchar(30)		NOT NULL default 'general',
	`folder`			int(11)			NOT NULL default 0,
	`name`				varchar(255)	NOT NULL default '',
	`active`			int(1)			NOT NULL default 1,
	PRIMARY KEY (`uid`)
);

CREATE TABLE `comments` (
	`uid`				int(11)			NOT NULL auto_increment,
	`datetime`			datetime		NOT NULL default '0000-00-00 00:00:00',
	`item`				varchar(255)	NOT NULL default '',
	`user`				varchar(11)		NOT NULL default '0',
	`comment`			blob,
	`company`			int(11)			NOT NULL,
	`active`			int(1)			NOT NULL default 1,
	PRIMARY KEY (`uid`)
);

CREATE TABLE `functions` (
	`uid`				int(11)			auto_increment,
	`function`			varchar(50)		NOT NULL default '',
	`name`				varchar(255)	NOT NULL default '',
	`category`			varchar(50)		NOT NULL default '',
	PRIMARY KEY (`uid`)
);

CREATE TABLE `group_functions` (
	`uid`				int(11)			auto_increment,
	`function`			varchar(50)		NOT NULL default '',
	`group`				int(11)			NOT NULL default 0,
	PRIMARY KEY(`uid`)
);

INSERT INTO `users` (`datetime`, `username`, `password`, `first_name`, `last_name`, `active`) VALUES(NOW(), 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Admin', 'User', 1);
INSERT INTO `groups` (`datetime`, `user`, `name`) VALUES(NOW(), 1, 'Admin');
INSERT INTO `user_groups` (`datetime`, `user`, `user_id`, `group_id`, `active`) VALUES(NOW(), 1, 1, 1, 1);
INSERT INTO `functions` (`function`, `name`, `category`) VALUES('home', 'Home Page', 'General');
INSERT INTO `functions` (`function`, `name`, `category`) VALUES('admin_menu', 'Admin Menu', 'Admin');
INSERT INTO `functions` (`function`, `name`, `category`) VALUES('admin_users', 'User Administration', 'Admin');
INSERT INTO `group_functions` (`function`, `group`) VALUES('home', 1);
INSERT INTO `group_functions` (`function`, `group`) VALUES('admin_menu', 1);
INSERT INTO `group_functions` (`function`, `group`) VALUES('admin_users', 1);
