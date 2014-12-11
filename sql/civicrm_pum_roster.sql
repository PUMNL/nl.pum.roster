CREATE TABLE IF NOT EXISTS `civicrm_pum_roster` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `type` text NOT NULL,
  `value` text COMMENT 'use , for concatenation',
  `min_interval` int(3) NOT NULL DEFAULT '1',
  `last_run` date DEFAULT NULL,
  `next_run` date NOT NULL,
  `privilege` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;