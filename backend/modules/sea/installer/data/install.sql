SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `sea_day_data` (
  `day` date NOT NULL,
  `cost` float DEFAULT NULL,
  `visits` int(11) DEFAULT NULL,
  `impressions` int(11) DEFAULT NULL,
  `clicks` int(11) DEFAULT NULL,
  `click_through_rate` float DEFAULT NULL,
  `cost_per_click` float DEFAULT NULL,
  `cost_per_mimpressions` float DEFAULT NULL,
  `conversions` int(11) DEFAULT NULL,
  `conversion_percentage` float DEFAULT NULL,
  `cost_per_conversion` float DEFAULT NULL,
  UNIQUE KEY `unique` (`day`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `sea_goals` (
  `goal_name` varchar(100) NOT NULL,
  UNIQUE KEY `unique` (`goal_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `sea_period` (
  `period_id` int(11) NOT NULL AUTO_INCREMENT,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  PRIMARY KEY (`period_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `sea_period_data` (
  `period_id` int(10) NOT NULL,
  `visits` int(11) DEFAULT NULL,
  `conversions` int(11) DEFAULT NULL,
  `conversion_percentage` float DEFAULT NULL,
  `cost_per_conversion` float DEFAULT NULL,
  `impressions` int(11) DEFAULT NULL,
  `clicks_amount` int(11) DEFAULT NULL,
  `click_through_rate` float DEFAULT NULL,
  `cost_per_click` float DEFAULT NULL,
  `cost_per_mimpressions` float DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `costs` float DEFAULT NULL,
  UNIQUE KEY `period_id` (`period_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `sea_settings` (
  `name` varchar(50) NOT NULL,
  `value` varchar(100) NOT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `sea_settings` (`name`, `value`, `date`) VALUES
('client_id', '', NULL),
('client_secret', '', NULL),
('redirect_uri', 'http://localhost/private/en/sea/validate', NULL),
('scope', 'https://www.googleapis.com/auth/analytics.readonly', NULL),
('access_type', 'offline', NULL),
('access_token', '', NULL),
('refresh_token', '', NULL),
('table_id', '', NULL);