CREATE TABLE IF NOT EXISTS `#__content_multicats` (
  `content_id` int(11) NOT NULL DEFAULT '0',
  `catid` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`content_id`,`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;