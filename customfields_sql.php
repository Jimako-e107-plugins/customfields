CREATE TABLE IF NOT EXISTS `customfields` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`plugin_name` varchar(200) NOT NULL,
`plugin_table` varchar(200) NOT NULL,
`plugin_event` varchar(200) NOT NULL,
`config_fields` text NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `datafields` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`plugin_name` varchar(200) NOT NULL,
`plugin_table` varchar(200) NOT NULL,
`plugin_id` int(11) NOT NULL,
`data_fields` text NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `plugin_id` (`plugin_table`,`plugin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;