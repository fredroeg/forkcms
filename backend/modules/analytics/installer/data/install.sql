DROP TABLE IF EXISTS `analytics_aggregates`;
CREATE TABLE IF NOT EXISTS `analytics_aggregates` (
  `period_id` int(11) unsigned NOT NULL,
  `bounces` int(11) DEFAULT NULL,
  `entrances` int(11) DEFAULT NULL,
  `exits` int(11) DEFAULT NULL,
  `new_visits` int(11) DEFAULT NULL,
  `pageviews` int(11) DEFAULT NULL,
  `time_on_page` int(11) DEFAULT NULL,
  `time_on_site` int(11) DEFAULT NULL,
  `visitors` int(11) DEFAULT NULL,
  `visits` int(11) DEFAULT NULL,
  `unique_pageviews` int(11) DEFAULT NULL,
  `keyword_pageviews` int(11) DEFAULT NULL,
  `all_pages_pageviews` int(11) DEFAULT NULL,
  `all_pages_unique_pageviews` int(11) DEFAULT NULL,
  `exit_pages_exits` int(11) DEFAULT NULL,
  `exit_pages_pageviews` int(11) DEFAULT NULL,
  `landing_pages_entrances` int(11) DEFAULT NULL,
  `landing_pages_bounces` int(11) DEFAULT NULL,
  PRIMARY KEY (`period_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `analytics_aggregates_total`;
CREATE TABLE IF NOT EXISTS `analytics_aggregates_total` (
  `bounces` int(11) DEFAULT NULL,
  `entrances` int(11) DEFAULT NULL,
  `exits` int(11) DEFAULT NULL,
  `new_visits` int(11) DEFAULT NULL,
  `pageviews` int(11) DEFAULT NULL,
  `time_on_page` int(11) DEFAULT NULL,
  `time_on_site` int(11) DEFAULT NULL,
  `visitors` int(11) DEFAULT NULL,
  `visits` int(11) DEFAULT NULL,
  `unique_pageviews` int(11) DEFAULT NULL,
  `keyword_pageviews` int(11) DEFAULT NULL,
  `all_pages_pageviews` int(11) DEFAULT NULL,
  `all_pages_unique_pageviews` int(11) DEFAULT NULL,
  `exit_pages_exits` int(11) DEFAULT NULL,
  `exit_pages_pageviews` int(11) DEFAULT NULL,
  `landing_pages_entrances` int(11) DEFAULT NULL,
  `landing_pages_bounces` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `analytics_exit_pages`;
CREATE TABLE IF NOT EXISTS `analytics_exit_pages` (
  `period_id` int(11) unsigned NOT NULL,
  `page_path` varchar(100) DEFAULT NULL,
  `exits` int(11) DEFAULT NULL,
  `pageviews` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `analytics_keywords`;
CREATE TABLE IF NOT EXISTS `analytics_keywords` (
  `period_id` int(11) unsigned NOT NULL,
  `keyword` varchar(100) DEFAULT NULL,
  `pageviews` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `analytics_landing_pages`;
CREATE TABLE IF NOT EXISTS `analytics_landing_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `entrances` int(11) NOT NULL,
  `bounces` int(11) NOT NULL,
  `bounce_rate` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `analytics_metrics_per_day`;
CREATE TABLE IF NOT EXISTS `analytics_metrics_per_day` (
  `day` date NOT NULL,
  `bounces` int(11) DEFAULT NULL,
  `entrances` int(11) DEFAULT NULL,
  `exits` int(11) DEFAULT NULL,
  `pageviews` int(11) DEFAULT NULL,
  `visits` int(11) DEFAULT NULL,
  `visitors` int(11) DEFAULT NULL,
  PRIMARY KEY (`day`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `analytics_pages`;
CREATE TABLE IF NOT EXISTS `analytics_pages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `period_id` int(11) unsigned NOT NULL,
  `page_path` varchar(100) DEFAULT NULL,
  `bounces` int(11) DEFAULT NULL,
  `entrances` int(11) DEFAULT NULL,
  `exits` int(11) DEFAULT NULL,
  `new_visits` int(11) DEFAULT NULL,
  `pageviews` int(11) DEFAULT NULL,
  `time_on_site` int(11) DEFAULT NULL,
  `visits` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `analytics_period`;
CREATE TABLE IF NOT EXISTS `analytics_period` (
  `period_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  PRIMARY KEY (`period_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `analytics_referrals`;
CREATE TABLE IF NOT EXISTS `analytics_referrals` (
  `period_id` int(11) unsigned NOT NULL,
  `referrer` varchar(100) DEFAULT NULL,
  `pageviews` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `analytics_sea_data`;
CREATE TABLE IF NOT EXISTS `analytics_sea_data` (
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

DROP TABLE IF EXISTS `analytics_sea_day_data`;
CREATE TABLE IF NOT EXISTS `analytics_sea_day_data` (
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

DROP TABLE IF EXISTS `analytics_sea_goals`;
CREATE TABLE IF NOT EXISTS `analytics_sea_goals` (
  `goal_name` varchar(100) NOT NULL,
  UNIQUE KEY `unique` (`goal_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `analytics_settings`;
CREATE TABLE IF NOT EXISTS `analytics_settings` (
  `name` varchar(50) NOT NULL,
  `value` varchar(100) NOT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `analytics_settings` (`name`, `value`, `date`) VALUES
('client_id', '', NULL),
('client_secret', '', NULL),
('redirect_uri', '', NULL),
('scope', 'https://www.googleapis.com/auth/analytics.readonly', NULL),
('access_type', 'offline', NULL),
('access_token', '', NULL),
('refresh_token', '', NULL),
('table_id', '', NULL),
('account_name', '', NULL),
('profile_name', '', NULL),
('web_property_id', '', NULL);

DROP TABLE IF EXISTS `analytics_traffic_sources`;
CREATE TABLE IF NOT EXISTS `analytics_traffic_sources` (
  `period_id` int(11) unsigned NOT NULL,
  `label` varchar(100) DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  `percentage` float DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;