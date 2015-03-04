DROP TABLE IF EXISTS `#__multicats`;

CREATE TABLE `#__multicats` (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  myvalue VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id)
) DEFAULT CHARSET=utf8;

INSERT INTO `#__multicats` (id,myvalue) VALUES
(1,'2.5.12'),
(2,'2.5.13'),
(3,'2.5.14');

ALTER TABLE `#__content` CHANGE `catid` `catid` varchar( 254 );

CREATE TABLE IF NOT EXISTS `#__content_multicats` (
  `content_id` int(11) NOT NULL DEFAULT '0',
  `catid` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`content_id`,`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;