<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic data communication functions
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Dieter Van den Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendAnalyticsModel
{
	/**
	 * Google authentication url and scope
	 *
	 * @var	string
	 */
	const GOOGLE_ACCOUNT_AUTHENTICATION_URL = 'https://www.google.com/accounts/AuthSubRequest?next=%1$s&amp;scope=%2$s&amp;secure=0&amp;session=1';
	const GOOGLE_ACCOUNT_AUTHENTICATION_SCOPE = 'https://www.google.com/analytics/feeds/';

	/**
	 * Google analytics url
	 *
	 * @var	string
	 */
	const GOOGLE_ANALYTICS_URL = 'https://www.google.com/analytics/reporting';


	/**
	 * Check in the database if we already stored the data from that period
	 *
	 * @param array $period
	 * @return boolean
	 */
	public static function checkPeriod($period)
	{
		$numRows = BackendModel::getDB()->getNumRows(
			'SELECT *
			 FROM analytics_period
			 WHERE period_start = ? AND period_end = ?', $period
		);
		$return = ($numRows > 0) ? (true) : (false);
		return $return;
	}

	/**
	 * Checks the settings and optionally returns an array with warnings
	 *
	 * @return array
	 */
	public static function checkSettings()
	{
		$APISettingsArray = BackendAnalyticsModel::getAPISettings();

		$warnings = array();

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('settings', 'analytics'))
		{
			// analytics session token
			if($APISettingsArray['access_token'] == '')
			{
				// add warning
				$warnings[] = array('message' => sprintf(BL::err('AnalyseNoSessionToken', 'analytics'), BackendModel::createURLForAction('settings', 'analytics')));
			}

			// analytics table id (only show this error if no other exist)
			if(empty($warnings) && $APISettingsArray['table_id'] == '')
			{
				// add warning
				$warnings[] = array('message' => sprintf(BL::err('AnalyseNoTableId', 'analytics'), BackendModel::createURLForAction('settings', 'analytics')));
			}
		}

		return $warnings;
	}

	/**
	 * Clear tables
	 */
	public static function clearTables()
	{
		BackendModel::getDB(true)->truncate(
			array(
				'analytics_aggregates',
				'analytics_aggregates_total',
				'analytics_exit_pages',
				'analytics_keywords',
				'analytics_landing_pages',
				'analytics_metrics_per_day',
				'analytics_pages',
				'analytics_period',
				'analytics_referrals',
				'analytics_traffic_sources'
			)
		);
	}

	/**
	 * Delete one or more landing pages
	 *
	 * @param mixed $ids The ids to delete.
	 */
	public static function deleteLandingPage($ids)
	{
		BackendModel::getDB(true)->delete('analytics_landing_pages', 'id IN (' . implode(',', (array) $ids) . ')');
	}

	/**
	 * Checks if a landing page exists
	 *
	 * @param int $id The id of the landing page to check for existence.
	 * @return bool
	 */
	public static function existsLandingPage($id)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT 1
			 FROM analytics_landing_pages
			 WHERE id = ?
			 LIMIT 1',
			array((int) $id)
		);
	}

	/**
	 * Get an aggregate
	 *
	 * @param string $name The name of the aggregate to look for.
	 * @param int $periodId
	 * @return string
	 */
	public static function getAggregate($name, $periodId)
	{
		$aggregates = self::getAggregates($periodId);

		// aggregate exists
		if(isset($aggregates[0][$name])) return $aggregates[0][$name];

		// doesn't exist
		return '';
	}

	/**
	 * Get the aggregates between 2 dates
	 *
	 * @param int $periodId
	 * @return array
	 */
	public static function getAggregates($periodId)
	{
		// get current action
		$action = Spoon::get('url')->getAction();

		$aggregates = self::getDataFromDbByType('analytics_aggregates', $periodId);

		return $aggregates;
	}

	/**
	 * Get the sites total aggregates
	 *
	 * startTimestamp and endTimestamp are needed so we can fetch the correct cache file
	 * They are not used when fetching the data from google.
	 *
	 * @param int $periodId
	 * @return array
	 */
	public static function getAggregatesTotal($periodId)
	{
		// get data from cache
		$aggregates = self::getDataFromDbByType('analytics_aggregates_total');

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($aggregates === false) self::redirectToLoadingPage($action);

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		return $aggregates;
	}

	/**
	 * Get all the authentication settings to access the Google API's
	 *
	 * @return array
	 */
	public static function getAPISettings()
	{
		return BackendModel::getDB()->getPairs(
			'SELECT name, value
			 FROM analytics_settings'
		 );
	}

	/**
	 * Fetch dashboard data grouped by day
	 *
	 * @param array $metrics The metrics to collect.
	 * @param int $periodId
	 * @return array
	 */
	public static function getDashboardData(array $metrics, $periodId)
	{
		$metrics = (array) $metrics;

		return self::getDataFromDbByType('analytics_aggregates', $periodId);
	}

	/**
	 * Get the top exit pages
	 *
	 * @param string $page The page.
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 * @return array
	 */
	public static function getDataForPage($page, $startTimestamp, $endTimestamp)
	{
		$db = BackendModel::getDB();

		// get id for this page
		$id = (int) $db->getVar(
			'SELECT id
			 FROM analytics_pages
			 WHERE page = ?',
			array((string) $page)
		);

		// no id? insert this page
		if($id === 0) $id = $db->insert('analytics_pages', array('page' => (string) $page));

		// get data from cache
		$items = array();
		$items['aggregates'] = self::getAggregatesFromCacheByType('page_' . $id, $startTimestamp, $endTimestamp);
		$items['entries'] = self::getDataFromCacheByType('page_' . $id, $startTimestamp, $endTimestamp);

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($items['aggregates'] === false || $items['entries'] === false) self::redirectToLoadingPage($action, array('page_id' => $id));

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		// update date_viewed for this page
		BackendAnalyticsModel::updatePageDateViewed($id);

		return $items;
	}

	/**
	 *
	 * @param string $type
	 * @param int $periodId
	 * @return array
	 */
	public static function getDataFromDbByType($type, $periodId = 0)
	{
	    if($periodId != 0)
	    {
		return (array) BackendModel::getDB()->getRecords(
			'SELECT *
			 FROM ' . $type . ' WHERE period_id = ?',
			 $periodId
		);
	    }
	    else
	    {
		return (array) BackendModel::getDB()->getRecord(
			'SELECT *
			 FROM ' . $type);
	    }

	}

	/**
	 *
	 * @param string $type
	 * @param int $startTimestamp
	 * @param int $endTimestamp
	 * @return array
	 */
	public static function getDayDataFromDbByType($type, $startTimestamp, $endTimestamp)
	{
		if(is_int($startTimestamp))
		{
			$startTimestamp = date('Y-m-d', $startTimestamp);
			$endTimestamp = date('Y-m-d', $endTimestamp);
		}
		return (array) BackendModel::getDB()->getRecords(
			'SELECT *
			 FROM ' . $type . ' WHERE day >= ? AND day <= ?',
			 array($startTimestamp, $endTimestamp)
		);
	}

	/**
	 * Get the exit pages
	 *
	 * @param int $periodId
	 * @return array
	 */
	public static function getExitPages($periodId)
	{
		// get data from cache
		$items = self::getDataFromDbByType('analytics_exit_pages', $periodId);

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($items === false) self::redirectToLoadingPage($action);

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		// init vars
		$results = array();

		// build top pages
		foreach($items as $i => $pageData)
		{
			// build array
			$results[$i] = array();
			$results[$i]['page'] = $pageData['page_path'];
			$results[$i]['page_encoded'] = urlencode($pageData['page_path']);
			$results[$i]['exits'] = (int) $pageData['exits'];
			$results[$i]['pageviews'] = (int) $pageData['pageviews'];
			$results[$i]['exit_rate'] = ($pageData['pageviews'] == 0 ? 0 : number_format(((int) $pageData['exits'] / $pageData['pageviews']) * 100, 2)) . '%';
		}

		return $results;
	}

	/**
	 * Get all the goals from the db
	 *
	 * @return array
	 */
	public static function getGoals()
	{
		return (array) BackendModel::getDB()->getRecords(
			'SELECT *
			 FROM analytics_sea_goals'
		);
	}

	/**
	 * Fetch landing pages
	 *
	 * @param int $periodId
	 * @param int[optional] $limit An optional limit of the number of landing pages to get.
	 * @return array
	 */
	public static function getLandingPages($periodId, $limit = null)
	{
		$results = array();
		$db = BackendModel::getDB();

		// get data from database
		if($limit === null) $items = (array) $db->getRecords(
			'SELECT *, UNIX_TIMESTAMP(updated_on) AS updated_on
			 FROM analytics_landing_pages
			 ORDER BY entrances DESC'
		);

		else $items = (array) $db->getRecords(
			'SELECT *, UNIX_TIMESTAMP(updated_on) AS updated_on
			 FROM analytics_landing_pages
			 ORDER BY entrances DESC
			 LIMIT ?',
			array((int) $limit)
		);

		foreach($items as $item)
		{
			$result = array();
			$startDate = date('Y-m-d', $startTimestamp) . ' 00:00:00';
			$endDate = date('Y-m-d', $endTimestamp) . ' 00:00:00';

			// no longer up to date, not for the period we need - get new one
			if($item['updated_on'] < time() - 43200 || $item['start_date'] != $startDate || $item['end_date'] != $endDate)
			{
				// get metrics
				$metrics = BackendAnalyticsHelper::getMetricsForPage($item['page_path'], $startTimestamp, $endTimestamp);

				// build item
				$result['page_path'] = $item['page_path'];
				$result['entrances'] = (isset($metrics['entrances']) ? $metrics['entrances'] : 0);
				$result['bounces'] = (isset($metrics['bounces']) ? $metrics['bounces'] : 0);
				$result['bounce_rate'] = ($metrics['entrances'] == 0 ? 0 : number_format(((int) $metrics['bounces'] / $metrics['entrances']) * 100, 2)) . '%';
				$result['start_date'] = $startDate;
				$result['end_date'] = $endDate;
				$result['updated_on'] = date('Y-m-d H:i:s');

				// update record
				$db->update('analytics_landing_pages', $result, 'id = ?', $item['id']);
			}

			// correct data
			else $result = $item;

			// add encoded page path
			$result['page_encoded'] = urlencode($result['page_path']);

			// save record in results array
			$results[] = $result;
		}

		return $results;
	}

	/**
	 *
	 * @return arrat
	 */
	public static function getLatestPeriod()
	{
		return BackendModel::getDB()->getRecord(
			'SELECT *
			 FROM analytics_period
			 ORDER BY period_id
			 DESC LIMIT 1');
	}

	/**
	 * Get all data for a given revision.
	 *
	 * @param string[optional] $language The language to use.
	 * @return array
	 */
	public static function getLinkList($language = null)
	{
		$language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// there is no cache file
		if(!SpoonFile::exists(FRONTEND_CACHE_PATH . '/navigation/tinymce_link_list_' . $language . '.js')) return array();

		// read the cache file
		$cacheFile = SpoonFile::getContent(FRONTEND_CACHE_PATH . '/navigation/tinymce_link_list_' . $language . '.js');

		// get the array
		preg_match('/new Array\((.*)\);$/s', $cacheFile, $matches);

		// no matched
		if(empty($matches)) return array();

		// create array
		$matches = explode('],', str_replace('[', '', $matches[count($matches) - 1]));

		// init vars
		$cacheList = array();

		// loop list
		foreach($matches as $item)
		{
			// trim item
			$item = explode('", "', trim($item," \n\r\t\"]"));

			// build cache list
			$cacheList[$item[1]] = $item[0];
		}

		return $cacheList;
	}

	/**
	 * Fetch metrics grouped by day
	 *
	 * @param array $metrics The metrics to collect.
	 * @param date $startTimestamp
	 * @param date $endTimestamp
	 * @param string $table
	 * @return array
	 */
	public static function getMetricsPerDay(array $metrics, $startTimestamp, $endTimestamp, $table = null)
	{
		$metrics = (array) $metrics;

		// get data from cache
		if(!$table)
		{
			$items = self::getDayDataFromDbByType('analytics_metrics_per_day', $startTimestamp, $endTimestamp);

		}
		elseif($table == 'analytics_sea_day_data')
		{
			$items = self::getDayDataFromDbByType($table, $startTimestamp, $endTimestamp);

		}

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($items === false) self::redirectToLoadingPage($action);

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		return $items;
	}

	/**
	 * Fetch page by its path
	 *
	 * @param string $path The path of the page.
	 * @return array
	 */
	public static function getPageByPath($path)
	{
		return (array) BackendModel::getDB()->getRecord(
			'SELECT *
			 FROM analytics_pages
			 WHERE page = ?',
			array((string) $path)
		);
	}

	/**
	 * Get the page for a certain id
	 *
	 * @param int $pageId The page id to get the page for.
	 * @return string
	 */
	public static function getPageForId($pageId)
	{
		return (string) BackendModel::getDB()->getVar(
			'SELECT page
			 FROM analytics_pages
			 WHERE id = ?',
			array((int) $pageId)
		);
	}

	/**
	 * Get pages
	 *
	 * @param int $periodId
	 * @return array
	 */
	public static function getPages($periodId)
	{
		// get data from cache
		$items = self::getDataFromDbByType('analytics_pages', $periodId);

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($items === false) self::redirectToLoadingPage($action);

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		// init vars
		$results = array();

		// build pages array
		foreach($items as $i => $item)
		{
			// build array
			$results[$i] = array();
			$results[$i]['page'] = $item['page_path'];
			$results[$i]['page_encoded'] = urlencode($item['page_path']);
			$results[$i]['pageviews'] = (int) $item['pageviews'];
			$results[$i]['pages_per_visit'] = ($item['visits'] == 0 ? 0 : number_format(((int) $item['pageviews'] / $item['visits']), 2));
			$results[$i]['time_on_site'] = BackendAnalyticsModel::getTimeFromSeconds(($item['entrances'] == 0 ? 0 : number_format(((int) $item['time_on_site'] / $item['entrances']), 2)));
			$results[$i]['new_visits_percentage'] = ($item['visits'] == 0 ? 0 : number_format(((int) $item['new_visits'] / $item['visits']) * 100, 2)) . '%';
			$results[$i]['bounce_rate'] = ($item['entrances'] == 0 ? 0 : number_format(((int) $item['bounces'] / $item['entrances']) * 100, 2)) . '%';
		}

		return $results;
	}

	/**
	 * Get the id from a certain period
	 *
	 * @param array $period
	 * @return int
	 */
	public static function getPeriodId($period)
	{
		return (int) BackendModel::getDB()->getVar(
			'SELECT period_id
			 FROM analytics_period
			 WHERE period_start = ? AND period_end = ?',
			 $period
		);
	}

	/**
	 * Get the most recent keywords
	 *
	 * @param int $periodId
	 * @return string
	 */
	public static function getRecentKeywords($periodId)
	{
		return  (array) BackendModel::getDB()->getRecords(
			'SELECT keyword, pageviews
			 FROM analytics_keywords
			 WHERE period_id = ?
			 ORDER BY pageviews
			 DESC
			 LIMIT 10', $periodId);
	}

	/**
	 * Get the most recent referrers
	 *
	 * @param int $periodId
	 * @return string
	 */
	public static function getRecentReferrers($periodId)
	{
		$items = (array) BackendModel::getDB()->getRecords(
			'SELECT referrer, pageviews
			 FROM analytics_referrals
			 WHERE period_id = ?
			 ORDER BY pageviews
			 DESC
			 LIMIT 10', $periodId);

		foreach($items as $key => $item)
		{
			// assign URL
			$items[$key]['url'] = 'http://' . $item['referrer'];

			// wordwrap referrer
			$items[$key]['referrer'] = wordwrap($item['referrer'], 40, ' ', true);
		}

		return $items;
	}

	/**
	 * Get the SEA Data within a period
	 *
	 * @param int $periodId
	 * @return array
	 */
	public static function getSEAData($periodId)
	{
		return self::getDataFromDbByType('analytics_sea_data', $periodId);
	}

	/**
	 * Get the selected table id
	 *
	 * @return string
	 */
	public static function getTableId()
	{
		return (string) BackendAnalyticsHelper::getGoogleAnalyticsInstance()->getTableId();
	}

	/**
	 * Get time from seconds
	 *
	 * @param int $seconds The seconds to format.
	 * @return string H:i:s
	 */
	public static function getTimeFromSeconds($seconds)
	{
		$seconds = (int) ceil($seconds);

		// get seconds
		$timeHours = (int) floor($seconds / 3600);
		$timeMinutes = (int) floor(($seconds - ($timeHours * 3600)) / 60);
		$timeSeconds = (int) floor($seconds - ($timeHours * 3600) - ($timeMinutes * 60));

		// return formatted time
		return str_pad($timeHours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($timeMinutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad($timeSeconds, 2, '0', STR_PAD_LEFT);
	}

	/**
	 * Get the top exit pages
	 *
	 * @param int $periodId
	 * @param int[optional] $limit An optional limit of the number of exit pages to get.
	 * @return array
	 */
	public static function getTopExitPages($periodId, $limit = 5)
	{
		// get data from db
		$items = self::getDataFromDbByType('analytics_exit_pages', $periodId);

		// limit data
		if(!empty($items)) $items = array_slice($items, 0, $limit, true);

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($items === false) self::redirectToLoadingPage($action);

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		// init vars
		$results = array();

		// build top pages
		foreach($items as $i => $pageData)
		{
			// build array
			$results[$i] = array();
			$results[$i]['page'] = $pageData['page_path'];
			$results[$i]['page_encoded'] = urlencode($pageData['page_path']);
			$results[$i]['exits'] = (int) $pageData['exits'];
			$results[$i]['pageviews'] = (int) $pageData['pageviews'];
		}

		return $results;
	}

	/**
	 * Get the top keywords
	 *
	 * @param int $periodId
	 * @param int[optional] $limit An optional limit of the number of keywords to get.
	 * @return array
	 */
	public static function getTopKeywords($periodId, $limit = 5)
	{
		// get data from db
		$items = self::getDataFromDbByType('analytics_keywords', $periodId);

		// limit data
		if(!empty($items)) $items = array_slice($items, 0, $limit, true);

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($items === false) self::redirectToLoadingPage($action);

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		$results = array();

		// get total pageviews
		$totalPageviews = (int) self::getAggregate('keyword_pageviews', $periodId);

		// build top keywords
		foreach($items as $i => $keywordData)
		{
			// build array
			$results[$i] = array();
			$results[$i]['keyword'] = (mb_strlen($keywordData['keyword']) <= 45 ? $keywordData['keyword'] : mb_substr($keywordData['keyword'], 0, 45) . '…');
			$results[$i]['pageviews'] = (int) $keywordData['pageviews'];
			$results[$i]['pageviews_percentage'] = ($totalPageviews == 0 ? '0' : number_format(((int) $keywordData['pageviews'] / $totalPageviews) * 100, 2)) . '%';
		}

		return $results;
	}

	/**
	 * Get the top pages
	 *
	 * @param int $periodId
	 * @param int[optional] $limit An optional limit of the number of pages to get.
	 * @return array
	 */
	public static function getTopPages($periodId, $limit = 5)
	{
		// get data from cache
		$items = self::getDataFromDbByType('analytics_pages', $periodId);

		// limit data
		if(!empty($items)) $items = array_slice($items, 0, $limit, true);

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($items === false) self::redirectToLoadingPage($action);

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		// init vars
		$results = array();

		// get total pageviews
		$totalPageviews = (int) self::getAggregate('pageviews',$periodId);

		// build top pages
		foreach($items as $i => $pageData)
		{
			// build array
			$results[$i] = array();
			$results[$i]['page'] = $pageData['page_path'];
			$results[$i]['page_encoded'] = urlencode($pageData['page_path']);
			$results[$i]['pageviews'] = (int) $pageData['pageviews'];
			$results[$i]['pageviews_percentage'] = ($totalPageviews == 0 ? '0' : number_format(($pageData['pageviews'] / $totalPageviews) * 100, 2)) . '%';
		}

		return $results;
	}

	/**
	 * Get the top referrals
	 *
	 * @param int $periodId
	 * @param int[optional] $limit An optional limit of the number of referrals to get.
	 * @return array
	 */
	public static function getTopReferrals($periodId, $limit = 5)
	{
		// get data from cache
		$items = self::getDataFromDbByType('analytics_referrals', $periodId);

		// limit data
		if(!empty($items)) $items = array_slice($items, 0, $limit, true);

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($items === false) self::redirectToLoadingPage($action);

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		// init
		$results = array();

		// get total pageviews
		$totalPageviews = (int) self::getAggregate('pageviews', $periodId);

		// build top keywords
		foreach($items as $i => $referrerData)
		{
			// build array
			$results[$i] = array();
			$results[$i]['referral'] = (mb_strlen($referrerData['referrer']) <= 45 ? trim($referrerData['referrer'], '/') : trim(mb_substr($referrerData['referrer'], 0, 45), '/') . '…');
			$results[$i]['referral_long'] = trim($referrerData['referrer'], '/');
			$results[$i]['pageviews'] = (int) $referrerData['pageviews'];
			$results[$i]['pageviews_percentage'] = ($totalPageviews == 0 ? '0' : number_format(((int) $referrerData['pageviews'] / $totalPageviews) * 100, 2)) . '%';
		}

		return $results;
	}

	/**
	 * Get the traffic sources grouped by medium
	 *
	 * @param int $periodId
	 * @return array
	 */
	public static function getTrafficSourcesGrouped($periodId)
	{
		// get data from cache
		$items = self::getDataFromDbByType('analytics_traffic_sources', $periodId);

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($items === false) self::redirectToLoadingPage($action);

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		return $items;
	}

	/**
	 * Insert the aggregates in the array with the period id
	 *
	 * @param int $periodId
	 * @param array $data
	 * @param boolean $total
	 */
	public static function insertAggregatesData($periodId, $data, $total = false)
	{
		$aggregatesDataArray = array(
			'bounces' => $data['bounces'],
			'entrances' => $data['entrances'],
			'exits' => $data['exits'],
			'new_visits' => $data['newVisits'],
			'pageviews' => $data['pageviews'],
			'time_on_page' => $data['timeOnPage'],
			'time_on_site' => $data['timeOnSite'],
			'visitors' => $data['visitors'],
			'visits' => $data['visits'],
			'unique_pageviews' => $data['uniquePageviews'],
			'keyword_pageviews' => $data['keywordPageviews'],
			'all_pages_pageviews' => $data['allPagesPageviews'],
			'all_pages_unique_pageviews' => $data['allPagesUniquePageviews'],
			'exit_pages_exits' => $data['exitPagesExit'],
			'exit_pages_pageviews' => $data['exitPagesPageviews'],
			'landing_pages_entrances' => $data['landingPagesEntrances'],
			'landing_pages_bounces' => $data['landingPagesBounces']
		);

		if(!$total)
		{
			$periodArray = array('period_id' => $periodId);
			$aggregatesDataArray = array_merge($periodArray, $aggregatesDataArray);
			BackendModel::getDB(true)->insert('analytics_aggregates', $aggregatesDataArray);
		}
		elseif($total)
		{
		    BackendModel::getDB(true)->truncate('analytics_aggregates_total');
		    BackendModel::getDB(true)->insert('analytics_aggregates_total', $aggregatesDataArray);
		}
	}

	/**
	 * Insert the top exit pages in the db
	 *
	 * @param int $periodId
	 * @param array $exitpages
	 */
	public static function insertExitpages($periodId, $exitpages)
	{
		foreach($exitpages as $datarow)
		{
			$record = array(
				'period_id' => $periodId,
				'page_path' => $datarow['pagePath'],
				'exits' => $datarow['exits'],
				'pageviews' => $datarow['pageviews']
			);
			BackendModel::getDB(true)->insert('analytics_exit_pages', $record);
		}
	}

	/**
	 * Inserts the keywords into the database
	 *
	 * @param int $periodId
	 * @param array $data
	 */
	public static function insertKeywordsData($periodId, $data)
	{
		$period = array('period_id' => $periodId);
		foreach($data as $datarow)
		{
			$insertData = array_merge($period, $datarow);
			BackendModel::getDB(true)->insert('analytics_keywords', $insertData);
		}
	}

	/**
	 * Inserts a landingpage into the database
	 *
	 * @param array $item The data to insert.
	 * @return int
	 */
	public static function insertLandingPage(array $item)
	{
		return (int) BackendModel::getDB(true)->insert('analytics_landing_pages', $item);
	}

	/**
	 * Insert the metrics per day
	 * Only those days that aren't inserted yet
	 *
	 * @param array $metrics
	 * @return boolean
	 */
	public static function insertMetricsPerDay($metrics)
	{
		foreach($metrics as $dayMetric)
		{
			$query =
			    'INSERT IGNORE INTO analytics_metrics_per_day (day, bounces, entrances, exits, pageviews, visits, visitors)
			     VALUES (:day, :bounces, :entrances, :exits, :pageviews, :visits, :visitors)';

			$record = array(
			    'day' => $dayMetric['date'],
			    'bounces' => $dayMetric['bounces'],
			    'entrances' => $dayMetric['entrances'],
			    'exits' => $dayMetric['exits'],
			    'pageviews' => $dayMetric['pageviews'],
			    'visits' => $dayMetric['visits'],
			    'visitors' => $dayMetric['visitors']
			);

			BackendModel::getDB()->execute($query, $record);
		}

		return true;
	}

	/**
	 *
	 * @param int $periodId
	 * @param array $pages
	 */
	public static function insertPages($periodId, $pages)
	{
		foreach($pages as $page)
		{
			$record = array(
				'period_id' => $periodId,
				'page_path' => $page['pagePath'],
				'bounces' => $page['bounces'],
				'entrances' => $page['entrances'],
				'exits' => $page['exits'],
				'new_visits' => $page['newVisits'],
				'pageviews' => $page['pageviews'],
				'time_on_site' => $page['timeOnSite'],
				'visits' => $page['visits']
			);
			BackendModel::getDB(true)->insert('analytics_pages', $record);
		}
	}

	/**
	 * Insert the period in the database, and return the id
	 *
	 * @param array $period
	 * @return period
	 */
	public static function insertPeriod($period)
	{
		return (int) BackendModel::getDB(true)->insert('analytics_period', array('period_id' => $period[0], 'period_start' => $period[1], 'period_end' => $period[2]));
	}

	/**
	 * Insert the top referrals in the db
	 *
	 * @param int $periodId
	 * @param array $referrals
	 */
	public static function insertReferrals($periodId, $referrals)
	{
		foreach($referrals as $datarow)
		{
			$record = array(
				'period_id' => $periodId,
				'referrer' => $datarow['referrer'],
				'pageviews' => $datarow['pageviews']
			);
			BackendModel::getDB(true)->insert('analytics_referrals', $record);
		}
	}

	/**
	 * Insert the data in the database
	 *
	 * @param int $period
	 * @param array $seaData
	 * @return boolean
	 */
	public static function insertSEAData($periodId, $seaData)
	{
		// then we insert all the data from that period
		$data['period_id'] = $periodId;
		$data['visits'] = $seaData['visits'];
		$data['impressions'] = $seaData['impressions'];
		$data['clicks_amount'] = $seaData['adClicks'];
		$data['click_through_rate'] = $seaData['CTR'];
		$data['cost_per_click'] = $seaData['CPC'];
		$data['cost_per_mimpressions'] = $seaData['CPM'];
		$data['costs'] = $seaData['costs'];
		$data['conversions'] = $seaData['conversions'];
		$data['conversion_percentage'] = $seaData['conversion_percentage'];
		$data['cost_per_conversion'] = $seaData['cost_per_conversion'];

		BackendModel::getDB()->insert('analytics_sea_data', $data);

		// at last we insert day-related data
		self::insertSEADayData($seaData['dayStats']);
		self::insertSEAGoalData($seaData['goals']);

		return true;
	}

	/**
	 * Insert all the SEA-related data per day
	 *
	 * @param array $dayData
	 * @return boolean
	 */
	private static function insertSEADayData($dayData)
	{
		foreach($dayData as $day => $data)
		{
			$query =
			    'INSERT IGNORE INTO analytics_sea_day_data (day, cost, visits, impressions, clicks, click_through_rate, cost_per_click, cost_per_mimpressions, conversions, conversion_percentage, cost_per_conversion)
			     VALUES (:day, :cost, :visits, :impressions, :clicks, :click_through_rate, :cost_per_click, :cost_per_mimpressions, :conversions, :conversion_percentage, :cost_per_conversion)';

			$record = array(
			    'day' => $day,
			    'cost' => $data['cost'],
			    'visits' => $data['visits'],
			    'impressions' => $data['impressions'],
			    'clicks' => $data['adClicks'],
			    'click_through_rate' => $data['CTR'],
			    'cost_per_click' => $data['CPC'],
			    'cost_per_mimpressions' => $data['CPM'],
			    'conversions' => $data['conversions'],
			    'conversion_percentage' => $data['conversion_percentage'],
			    'cost_per_conversion' => $data['cost_per_conversion']
			);

			BackendModel::getDB()->execute($query, $record);
		}

		return true;
	}

	/**
	 * Insert all the goals (if they aren't inserted yet)
	 *
	 * @param array $goals
	 */
	private static function insertSEAGoalData($goals)
	{
		foreach($goals as $goal)
		{
			$query = 'INSERT IGNORE INTO analytics_sea_goals (goal_name) VALUES (:goal_name)';
			$record['goal_name'] = $goal;
			BackendModel::getDB()->execute($query, $record);
		}
	}

	/**
	 *
	 * @param int $periodId
	 * @param array $trafficsources
	 */
	public static function insertTrafficSources($periodId, $trafficSources)
	{
		foreach($trafficSources as $source)
		{
			$record = array(
				'period_id' => $periodId,
				'label' => $source['label'],
				'value' => $source['value'],
				'percentage' => $source['percentage']
			);
			BackendModel::getDB(true)->insert('analytics_traffic_sources', $record);
		}
	}

	/**
	 * Redirect to the loading page after checking for infinite loops.
	 *
	 * @param string $action The action to check for infinite loops.
	 * @param array[optional] $extraParameters The extra parameters to append to the redirect url.
	 */
	public static function redirectToLoadingPage($action, array $extraParameters = array())
	{
		// put parameters into a string
		$extraParameters = (empty($extraParameters) ? '' : '&' . http_build_query($extraParameters));

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('loading', 'analytics'))
		{
			// redirect to loading page which will get the needed data based on the current action
			SpoonHTTP::redirect(BackendModel::createURLForAction('loading') . '&redirect_action=' . $action . $extraParameters);
		}
	}

	/**
	 * Update the api settings
	 *
	 * @param array $values
	 * @return boolean
	 */
	public static function updateIds($values)
	{
		$datetime = BackendModel::getUTCDate();
		foreach($values as $name => $value)
		{
			BackendModel::getDB()->update('analytics_settings', array('value' => $value, 'date' => $datetime), 'name = ?', $name);
		}
		return true;
	}

	/**
	 * Update the access token (and the refresh token) we achieved from Google
	 *
	 * @param string $accessToken
	 * @param string $refreshToken
	 * @return boolean
	 */
	public static function updateTokens($accessToken, $refreshToken = null)
	{
		$datetime = BackendModel::getUTCDate();
		BackendModel::getDB()->update('analytics_settings', array('value' => $accessToken, 'date' => $datetime), 'name = ?', 'access_token');
		if(isset($refreshToken))
		{
			BackendModel::getDB()->update('analytics_settings', array('value' => $refreshToken, 'date' => $datetime), 'name = ?', 'refresh_token');
		}
		return true;
	}
}