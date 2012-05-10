<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Helper class to make our life easier
 *
 * @author Dieter Van den Eynde <dieter.vandeneynde@netlash.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class BackendAnalyticsHelper
{
	/**
	 * Get aggregates
	 *
	 * @param int $startTimestamp The start timestamp for the google call.
	 * @param int $endTimestamp The end timestamp for the google call.
	 * @return array
	 */
	public static function getAggregates($startTimestamp, $endTimestamp)
	{
		$aggregates = array();

		/*
		 * STANDARD AGGREGATES
		 */
		// get all metrics
		$visitorMetrics = array('ga:bounces', 'ga:entrances', 'ga:exits', 'ga:newVisits', 'ga:pageviews', 'ga:timeOnPage', 'ga:timeOnSite', 'ga:visitors', 'ga:visits');
		$contentMetrics = 'ga:uniquePageviews';

		// get results
		$visitorResults = self::getGoogleAnalyticsInstance()->getAnalyticsResults($visitorMetrics, $startTimestamp, $endTimestamp);
		$contentResults = self::getGoogleAnalyticsInstance()->getAnalyticsResults($contentMetrics, $startTimestamp, $endTimestamp);

		// put in array
		$results = $visitorResults['aggregates'][0];
		$results += $contentResults['aggregates'][0];

		/*
		 * CUSTOM AGGREGATES
		 */
		// build filter for pageviews generated by keywords
		$parameters = array();
		$parameters['filters'] = 'ga:keyword!=(not set)';
		$parameters['max-results'] = 1; // no results are needed only the aggregate

		// get results for the pageviews generated by keywords
		$keywordResults = self::getGoogleAnalyticsInstance()->getAnalyticsResults('ga:pageviews', $startTimestamp, $endTimestamp, 'ga:keyword', $parameters);

		$pageviews['keywordPageviews'] = $keywordResults['totalResults']['ga:pageviews'];

		$results += $pageviews;

		// build filter for aggregates on all pages
		$parameters = array();
		$parameters['max-results'] = 1; // no results are needed only the aggregate

		// get results for the all pages
		$allPagesResults = self::getGoogleAnalyticsInstance()->getAnalyticsResults(array('ga:pageviews', 'ga:uniquePageviews'), $startTimestamp, $endTimestamp, 'ga:pagePath', $parameters);

		$allPaResults['allPagesPageviews'] = $allPagesResults['totalResults']['ga:pageviews'];
		$allPaResults['allPagesUniquePageviews'] = $allPagesResults['totalResults']['ga:uniquePageviews'];

		// add to the results
		$results += $allPaResults;


		// build filter for aggregates on exit pages
		$parameters = array();
		$parameters['filters'] = 'ga:exits>0';
		$parameters['max-results'] = 1; // no results are needed only the aggregate

		// get results for the exit pages
		$exitPagesResults = self::getGoogleAnalyticsInstance()->getAnalyticsResults(array('ga:exits', 'ga:pageviews'), $startTimestamp, $endTimestamp, 'ga:pagePath', $parameters);

		$exitPaResults['exitPagesExit'] = $exitPagesResults['totalResults']['ga:exits'];
		$exitPaResults['exitPagesPageviews'] = $exitPagesResults['totalResults']['ga:pageviews'];

		// add to the results
		$results += $exitPaResults;

		// build filter for aggregates on landing pages
		$parameters = array();
		$parameters['filters'] = 'ga:entrances>0';
		$parameters['max-results'] = 1; // no results are needed only the aggregate

		// get results for the landing pages
		$landingPagesResults = self::getGoogleAnalyticsInstance()->getAnalyticsResults(array('ga:entrances', 'ga:bounces'), $startTimestamp, $endTimestamp, 'ga:pagePath', $parameters);

		$landingPaResults['landingPagesEntrances'] = $landingPagesResults['totalResults']['ga:entrances'];
		$landingPaResults['landingPagesBounces'] = $landingPagesResults['totalResults']['ga:bounces'];

		// add to the results
		$results += $landingPaResults;

		/*
		 * PUT THEM ALL TOGETHER
		 */
		foreach($results as $key => $value)
		{
			// format value
			if($key == 'timeOnPage' || $key == 'timeOnSite') $value = (int) ceil($value);
			else $value = (int) $value;

			// save
			$aggregates[$key] = $value;
		}

		return $aggregates;
	}

	/**
	 * Get all the data, put it in the array and hand it over to the model
	 *
	 * @param string $startTimestamp
	 * @param string $endTimestamp
	 * @return true
	 */
	public static function getAllData($startTimestamp, $endTimestamp)
	{
	    $periodBoolean = BackendAnalyticsModel::checkPeriod(array($startTimestamp, $endTimestamp));
	    if(!$periodBoolean)
	    {
		$periodId = BackendAnalyticsModel::insertPeriod(array($startTimestamp, $endTimestamp));
	    }
	    else
	    {
		$periodId = BackendAnalyticsModel::getPeriodId(array($startTimestamp, $endTimestamp));
	    }

	    // $aggregatesData = self::getAggregates($startTimestamp, $endTimestamp);
	    // $keywordsData = self::getKeywords('pageviews', $startTimestamp, $endTimestamp, 'pageviews');
	    // $dashBoardData = self::getDashboardData($startTimestamp, $endTimestamp);
	    // $metricsPerDay = self::getMetricsPerDay($startTimestamp, $endTimestamp);

	    // BackendAnalyticsModel::insertAggregatesData($periodId, $aggregatesData);
	    // BackendAnalyticsModel::insertKeywordsData($periodId, $keywordsData);
	    // BackendAnalyticsModel::insertMetricsPerDay($metricsPerDay);

	    // self::getMetricsPerDay($startTimestamp, $endTimestamp);
	}

	/**
	 * Get all needed dashboard data for certain dates
	 *
	 * @param int $startTimestamp The start timestamp for the google call.
	 * @param int $endTimestamp The end timestamp for the google call.
	 * @return array
	 */
	public static function getDashboardData($startTimestamp, $endTimestamp)
	{
		// get metrics
		$metrics = array('pageviews', 'visitors');
		$gaMetrics = array();
		foreach($metrics as $metric) $gaMetrics[] = 'ga:' . $metric;

		$dimensions = 'ga:date';
		$results = self::getGoogleAnalyticsInstance()->getAnalyticsResults($gaMetrics, $startTimestamp, $endTimestamp, $dimensions);
		$entries = array();

		// loop visitor results
		foreach($results['aggregates'] as $result)
		{
			$timestamp = gmmktime(12, 0, 0, substr($result['date'], 4, 2), substr($result['date'], 6, 2), substr($result['date'], 0, 4));

			// store metrics in correct format
			$entry = array();
			$entry['timestamp'] = $timestamp;

			foreach($metrics as $metric)
			{
				$entry[$metric] = (int) $result[$metric];
			}

			$entries[] = $entry;
		}

		return $entries;
	}

	/**
	 * Get all needed metrics for certain dates
	 *
	 * @param int $pageId The id of the page to collect data from.
	 * @param int $startTimestamp The start timestamp for the google call.
	 * @param int $endTimestamp The end timestamp for the google call.
	 * @return array
	 */
	public static function getDataForPage($pageId, $startTimestamp, $endTimestamp)
	{
		// get page
		$page = BackendModel::getDB(false)->getVar(
			'SELECT page
			 FROM analytics_pages
			 WHERE id = ?',
			array((int) $pageId)
		);

		$data = array();
		$data['hostname'] = SITE_URL;
		$data['aggregates'] = array();
		$data['metrics_per_day'] = array();
		$data['sources'] = array();
		$data['sources_grouped'] = array();

		// get metrics and dimensions
		$metrics = 'ga:visits';
		$dimensions = 'ga:hostname';

		// get parameters
		$parameters = array();
		$parameters['max-results'] = 1;
		$parameters['sort'] = '-ga:visits';

		// get results
		$results = self::getGoogleAnalyticsInstance()->getAnalyticsResults($metrics, mktime(0, 0, 0, 1, 1, 2005), $endTimestamp, $dimensions, $parameters);

		// loop page results and add hostname to data array
		foreach($results['entries'] as $result) $data['hostname'] = $result['hostname'];

		// get metrics
		$metrics = array('bounces', 'entrances', 'exits', 'newVisits', 'pageviews', 'timeOnPage', 'timeOnSite', 'visits');
		$gaMetrics = array();
		foreach($metrics as $metric) $gaMetrics[] = 'ga:' . $metric;

		// get dimensions
		$dimensions = 'ga:date';

		// get parameters
		$parameters = array();
		$parameters['filters'] = 'ga:pagePath==' . $page;

		// get results
		$results = self::getGoogleAnalyticsInstance()->getAnalyticsResults($gaMetrics, $startTimestamp, $endTimestamp, $dimensions, $parameters);

		// get aggregates
		$data['aggregates'] = $results['aggregates'];

		// loop page results
		foreach($results['entries'] as $result)
		{
			// get timestamp
			$timestamp = gmmktime(12, 0, 0, substr($result['date'], 4, 2), substr($result['date'], 6, 2), substr($result['date'], 0, 4));

			// store metrics in correct format
			$entry = array();
			$entry['timestamp'] = $timestamp;

			// loop metrics
			foreach($metrics as $metric) $entry[$metric] = (int) $result[$metric];

			// add to entries array
			$data['metrics_per_day'][] = $entry;
		}

		// get metrics
		$metrics = array('bounces', 'entrances', 'exits', 'newVisits', 'pageviews', 'timeOnPage', 'timeOnSite', 'visits');
		$gaMetrics = array();
		foreach($metrics as $metric) $gaMetrics[] = 'ga:' . $metric;

		// get dimensions
		$dimensions = array('ga:source', 'ga:referralPath', 'ga:keyword');

		// get parameters
		$parameters = array();
		$parameters['max-results'] = 50;
		$parameters['filters'] = 'ga:pagePath==' . $page . ';ga:pageviews>0';
		$parameters['sort'] = '-ga:pageviews';

		// get results
		$results = self::getGoogleAnalyticsInstance()->getAnalyticsResults($gaMetrics, $startTimestamp, $endTimestamp, $dimensions, $parameters);

		// loop page results
		foreach($results['entries'] as $result)
		{
			// store dimension in correct format
			$entry = array();
			if($result['keyword'] != '(not set)') $entry['source'] = $result['keyword'];
			elseif($result['source'] == '(direct)') $entry['source'] = BL::lbl('DirectTraffic');
			elseif($result['referralPath'] != '(not set)') $entry['source'] = $result['source'] . $result['referralPath'];
			else $entry['source'] = $result['source'];

			// get metrics
			$entry['pageviews'] = (int) $result['pageviews'];
			$entry['pages_per_visit'] = ($result['visits'] == 0 ? 0 : number_format(((int) $result['pageviews'] / $result['visits']), 2));
			$entry['time_on_site'] = BackendAnalyticsModel::getTimeFromSeconds(($result['entrances'] == 0 ? 0 : number_format(((int) $result['timeOnSite'] / $result['entrances']), 2)));
			$entry['new_visits'] = ($result['visits'] == 0 ? 0 : number_format(((int) $result['newVisits'] / $result['visits']) * 100, 2)) . '%';
			$entry['bounce_rate'] = ($result['entrances'] == 0 ? 0 : number_format(((int) $result['bounces'] / $result['entrances']) * 100, 2)) . '%';

			// add to entries array
			$data['sources'][] = $entry;
		}

		// set parameters
		$parameters = array();
		$parameters['filters'] = 'ga:pagePath==' . $page;
		$parameters['sort'] = '-ga:pageviews';

		// get results for sources grouped
		$results = self::getGoogleAnalyticsInstance()->getAnalyticsResults('ga:pageviews', $startTimestamp, $endTimestamp, 'ga:medium', $parameters);

		// get total pageviews
		$totalPageviews = (isset($results['aggregates']['pageviews']) ? (int) $results['aggregates']['pageviews'] : 0);

		// loop entries
		foreach($results['entries'] as $i => $result)
		{
			// add to sources array
			$data['sources_grouped'][$i]['label'] = $result['medium'];
			$data['sources_grouped'][$i]['value'] = $result['pageviews'];
			$data['sources_grouped'][$i]['percentage'] = ($totalPageviews == 0 ? 0 : number_format(((int) $result['pageviews'] / $totalPageviews) * 100, 2)) . '%';
		}

		return $data;
	}

	/**
	 * Get the exit pages for some metrics
	 *
	 * @param mixed $metrics The metrics to get for the exit pages.
	 * @param int $startTimestamp The start timestamp for the google call.
	 * @param int $endTimestamp The end timestamp for the google call.
	 * @param string[optional] $sort The metric to sort on.
	 * @param int[optional] $limit An optional limit of the number of exit pages to get.
	 * @param int[optional] $index The index to start getting data from.
	 * @return array
	 */
	public static function getExitPages($metrics, $startTimestamp, $endTimestamp, $sort = null, $limit = null, $index = 1)
	{
		$metrics = (array) $metrics;

		// set metrics
		$gaMetrics = array();
		foreach($metrics as $metric) $gaMetrics[] = 'ga:' . $metric;

		// set parameters
		$parameters = array();
		if(isset($limit)) $parameters['max-results'] = (int) $limit;
		$parameters['start-index'] = (int) $index;
		$parameters['filters'] = 'ga:exits>0';

		// sort if needed
		if($sort !== null) $parameters['sort'] = '-ga:' . $sort;

		// return results
		return self::getGoogleAnalyticsInstance()->getAnalyticsResults($gaMetrics, $startTimestamp, $endTimestamp, 'ga:pagePath', $parameters);
	}

	/**
	 * Get Google Analytics instance
	 *
	 * @return GoogleAnalytics
	 */
	public static function getGoogleAnalyticsInstance()
	{
		$record = BackendAnalyticsModel::getAPISettings();

		// get session token and table id
		$sessionToken = $record['access_token'];
		$tableId = $record['table_id'];

		// require the GoogleAnalytics class
		require_once 'external/google_analytics.php';

		// get and return an instance
		return new GoogleAnalytics($sessionToken, $tableId);
	}

	/**
	 * Get the keywords for certain dates
	 *
	 * @param mixed $metrics The metrics to get for the keywords.
	 * @param int $startTimestamp The start timestamp for the google call.
	 * @param int $endTimestamp The end timestamp for the google call.
	 * @param string[optional] $sort The metric to sort on.
	 * @param int[optional] $limit An optional limit of the number of keywords to get.
	 * @param int[optional] $index The index to start getting data from.
	 * @return array
	 */
	public static function getKeywords($metrics, $startTimestamp, $endTimestamp, $sort = null, $limit = null, $index = 1)
	{
		$metrics = (array) $metrics;

		// set metrics
		$gaMetrics = array();
		foreach($metrics as $metric) $gaMetrics[] = 'ga:' . $metric;

		// set parameters
		$parameters = array();
		if(isset($limit)) $parameters['max-results'] = (int) $limit;
		$parameters['start-index'] = (int) $index;
		$parameters['filters'] = 'ga:keyword!=(not set)';

		// sort if needed
		if($sort !== null) $parameters['sort'] = '-ga:' . $sort;

		$results = self::getGoogleAnalyticsInstance()->getAnalyticsResults($gaMetrics, $startTimestamp, $endTimestamp, 'ga:keyword', $parameters);

		// fetch and return
		return $results['aggregates'];
	}

	/**
	 * Get all needed metrics for a certain page
	 *
	 * @param string $page The page to get some metrics from.
	 * @param int $startTimestamp The start timestamp for the google call.
	 * @param int $endTimestamp The end timestamp for the google call.
	 * @return array
	 */
	public static function getMetricsForPage($page, $startTimestamp, $endTimestamp)
	{
		// get metrics
		$metrics = array('bounces', 'entrances');
		$gaMetrics = array();
		foreach($metrics as $metric) $gaMetrics[] = 'ga:' . $metric;

		// get parameters
		$parameters = array();
		$parameters['filters'] = 'ga:pagePath==' . $page;
		$parameters['max-results'] = 1; // no results are needed only the aggregate

		// get results
		$results = self::getGoogleAnalyticsInstance()->getAnalyticsResults($gaMetrics, $startTimestamp, $endTimestamp, 'ga:pagePath', $parameters);

		// return metrics
		return $results['aggregates'];
	}

	/**
	 * Get all needed metrics for certain dates
	 *
	 * @param int $startTimestamp The start timestamp for the google call.
	 * @param int $endTimestamp The end timestamp for the google call.
	 * @return array
	 */
	public static function getMetricsPerDay($startTimestamp, $endTimestamp)
	{
		// get metrics
		$metrics = array('bounces', 'entrances', 'exits', 'pageviews', 'visits', 'visitors');
		$gaMetrics = array();
		foreach($metrics as $metric) $gaMetrics[] = 'ga:' . $metric;

		// get dimensions
		$dimensions = 'ga:date';

		// get results
		$results = self::getGoogleAnalyticsInstance()->getAnalyticsResults($gaMetrics, $startTimestamp, $endTimestamp, $dimensions);

		return $results['aggregates'];
	}

	/**
	 * With this function we get the OAuth2.0 Token, generated by Google.
	 * This token is stored in the database and in a session
	 *
	 * @param string $code
	 * @param boolean $boolean
	 * @return string
	 */
	public static function getOAuth2Token($code, $boolean)
	{
		// We obtain all the settings from the database
		$APISettingsArray = BackendAnalyticsModel::getAPISettings();

		// the base_url for the curl
		$oauth2tokenUrl = 'https://accounts.google.com/o/oauth2/token';

		// Normally the client ID and client Secret never changes
		$clienttokenPost = array(
			'client_id' => $APISettingsArray['client_id'],
			'client_secret' => $APISettingsArray['client_secret']
		);

		// If boolean == true, it means that we can access google's data with the refresh code
		// This means we weren't redirected to google anymore
		if($boolean)
		{
			$clienttokenPost['refresh_token'] = $code;
			$clienttokenPost['grant_type'] = 'refresh_token';
		}
		else
		{
			$clienttokenPost['code'] = $code;
			$clienttokenPost['redirect_uri'] = $APISettingsArray['redirect_uri'];
			$clienttokenPost['grant_type'] = 'authorization_code';
		}

		// we use curl to do the authorization
		$curl = curl_init($oauth2tokenUrl);

		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $clienttokenPost);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$json_response = curl_exec($curl);
		curl_close($curl);

		// a json string is returned.
		$authObj = json_decode($json_response);


		if(isset($authObj->error))
		{
			$url = BackendModel::createURLForAction('settings') . '&error=' . $authObj->error;
			SpoonHTTP::redirect($url);
		}

		// the returned object should contain at least an access token
		$accessToken = $authObj->access_token;

		if(isset($authObj->refresh_token))
		{
			$refreshToken = $authObj->refresh_token;
			BackendAnalyticsModel::updateTokens($accessToken, $refreshToken);
			return true;
		}

		BackendAnalyticsModel::updateTokens($accessToken);
		SpoonSession::set('accessTokenCreated', time());
		return true;
	}

	/**
	 * Get the pages for some metrics
	 *
	 * @param mixed $metrics The metrics to get for the pages.
	 * @param int $startTimestamp The start timestamp for the google call.
	 * @param int $endTimestamp The end timestamp for the google call.
	 * @param string[optional] $sort The metric to sort on.
	 * @param int[optional] $limit An optional limit of the number of pages to get.
	 * @param int[optional] $index The index to start getting data from.
	 * @return array
	 */
	public static function getPages($metrics, $startTimestamp, $endTimestamp, $sort = null, $limit = null, $index = 1)
	{
		$metrics = (array) $metrics;

		// set metrics
		$gaMetrics = array();
		foreach($metrics as $metric) $gaMetrics[] = 'ga:' . $metric;

		// set parameters
		$parameters = array();
		if(isset($limit)) $parameters['max-results'] = (int) $limit;
		$parameters['start-index'] = (int) $index;

		// sort if needed
		if($sort !== null) $parameters['sort'] = '-ga:' . $sort;

		return self::getGoogleAnalyticsInstance()->getAnalyticsResults($gaMetrics, $startTimestamp, $endTimestamp, 'ga:pagePath', $parameters);
	}

	/**
	 * Get the keywords for certain dates
	 *
	 * @return array
	 */
	public static function getRecentKeywords()
	{
		// set metrics and dimensions
		$gaMetrics = 'ga:entrances';
		$gaDimensions = 'ga:keyword';

		// set parameters
		$parameters = array();
		$parameters['max-results'] = 10;
		$parameters['filters'] = 'ga:medium==organic';
		$parameters['sort'] = '-ga:entrances';

		// get results
		$results = self::getGoogleAnalyticsInstance()->getAnalyticsResults($gaMetrics, mktime(0, 0, 0), mktime(23, 59, 59), $gaDimensions, $parameters);

		// no results - try the same query but for yesterday
		if(empty($results)) $results = self::getGoogleAnalyticsInstance()->getAnalyticsResults($gaMetrics, strtotime('-1day', mktime(0, 0, 0)), strtotime('-1day', mktime(23, 59, 59)), $gaDimensions, $parameters);

		// init vars
		$insertArray = array();

		// loop keywords
		foreach($results['entries'] as $entry)
		{
			// build insert record
			$insertRecord = array();
			$insertRecord['keyword'] = $entry['keyword'];
			$insertRecord['entrances'] = $entry['entrances'];
			$insertRecord['date'] = $results['startDate'] . ' 00:00:00';

			// add record to insert array
			$insertArray[] = $insertRecord;
		}

		// there are some records to be inserted
		if(!empty($insertArray))
		{
			$db = BackendModel::getDB(true);

			// remove old data and insert array into database
			$db->truncate('analytics_keywords');
			$db->insert('analytics_keywords', $insertArray);
		}
	}

	/**
	 * Get the referrers for certain dates
	 *
	 * @return array
	 */
	public static function getRecentReferrers()
	{
		// set metrics and dimensions
		$gaMetrics = 'ga:entrances';
		$gaDimensions = array('ga:source', 'ga:referralPath');

		// set parameters
		$parameters = array();
		$parameters['max-results'] = 10;
		$parameters['filters'] = 'ga:medium==referral';
		$parameters['sort'] = '-ga:entrances';

		// get results
		$results = self::getGoogleAnalyticsInstance()->getAnalyticsResults($gaMetrics, mktime(0, 0, 0), mktime(23, 59, 59), $gaDimensions, $parameters);

		// no results - try the same query but for yesterday
		if(empty($results)) $results = self::getGoogleAnalyticsInstance()->getAnalyticsResults($gaMetrics, strtotime('-1day', mktime(0, 0, 0)), strtotime('-1day', mktime(23, 59, 59)), $gaDimensions, $parameters);

		// init vars
		$insertArray = array();

		// loop referrers
		foreach($results['entries'] as $entry)
		{
			// build insert record
			$insertRecord = array();
			$insertRecord['referrer'] = $entry['source'] . $entry['referralPath'];
			$insertRecord['entrances'] = $entry['entrances'];
			$insertRecord['date'] = $results['startDate'] . ' 00:00:00';

			// add record to insert array
			$insertArray[] = $insertRecord;
		}

		// there are some records to be inserted
		if(!empty($insertArray))
		{
			$db = BackendModel::getDB(true);

			// remove old data and insert array into database
			$db->truncate('analytics_referrers');
			$db->insert('analytics_referrers', $insertArray);
		}
	}

	/**
	 * Get the referrers for certain dates
	 *
	 * @param mixed $metrics The metrics to get for the referrals.
	 * @param int $startTimestamp The start timestamp for the google call.
	 * @param int $endTimestamp The end timestamp for the google call.
	 * @param string[optional] $sort The metric to sort on.
	 * @param int[optional] $limit An optional limit of the number of referrals to get.
	 * @param int[optional] $index The index to start getting data from.
	 * @return array
	 */
	public static function getReferrals($metrics, $startTimestamp, $endTimestamp, $sort = null, $limit = null, $index = 1)
	{
		$metrics = (array) $metrics;

		// set metrics
		$gaMetrics = array();
		foreach($metrics as $metric) $gaMetrics[] = 'ga:' . $metric;

		// set parameters
		$parameters = array();
		if(isset($limit)) $parameters['max-results'] = (int) $limit;
		$parameters['start-index'] = (int) $index;
		$parameters['filters'] = 'ga:medium==referral';

		// sort if needed
		if($sort !== null) $parameters['sort'] = '-ga:' . $sort;

		return self::getGoogleAnalyticsInstance()->getAnalyticsResults($gaMetrics, $startTimestamp, $endTimestamp, array('ga:source', 'ga:referralPath'), $parameters);
	}

	/**
	 * Get the status by doing a simple call
	 *
	 * @return array
	 */
	public static function getStatus()
	{
		// set metrics and dimensions
		$gaMetrics = 'ga:visits';

		// set parameters
		$parameters = array();
		$parameters['max-results'] = 10;

		return self::getGoogleAnalyticsInstance()->getAnalyticsResults($gaMetrics, mktime(0, 0, 0), mktime(23, 59, 59), array(), $parameters);
	}

	/**
	 * Get the referrers for certain dates
	 *
	 * @param mixed $metrics The metrics to get for the traffic sources.
	 * @param int $startTimestamp The start timestamp for the google call.
	 * @param int $endTimestamp The end timestamp for the google call.
	 * @param string[optional] $sort The metric to sort on.
	 * @param int[optional] $limit An optional limit of the number of traffic sources to get.
	 * @param int[optional] $index The index to start getting data from.
	 * @return array
	 */
	public static function getTrafficSources($metrics, $startTimestamp, $endTimestamp, $sort = null, $limit = null, $index = 1)
	{
		$metrics = (array) $metrics;

		// set metrics
		$gaMetrics = array();
		foreach($metrics as $metric) $gaMetrics[] = 'ga:' . $metric;

		// set parameters
		$parameters = array();
		if(isset($limit)) $parameters['max-results'] = (int) $limit;
		$parameters['start-index'] = (int) $index;

		// sort if needed
		if($sort !== null) $parameters['sort'] = '-ga:' . $sort;

		return self::getGoogleAnalyticsInstance()->getAnalyticsResults($gaMetrics, $startTimestamp, $endTimestamp, array('ga:source', 'ga:medium', 'ga:keyword'), $parameters);
	}

	/**
	 * Get the referrers for certain dates
	 *
	 * @param mixed $metrics The metrics to get for the traffic sources.
	 * @param int $startTimestamp The start timestamp for the google call.
	 * @param int $endTimestamp The end timestamp for the google call.
	 * @param string[optional] $sort The metric to sort on.
	 * @param int[optional] $limit An optional limit of the number of traffic sources to get.
	 * @param int[optional] $index The index to start getting data from.
	 * @return array
	 */
	public static function getTrafficSourcesGrouped($metrics, $startTimestamp, $endTimestamp, $sort = null, $limit = null, $index = 1)
	{
		$metrics = (array) $metrics;
		$items = array();

		// set metrics
		$gaMetrics = array();
		foreach($metrics as $metric) $gaMetrics[] = 'ga:' . $metric;

		// set parameters
		$parameters = array();
		if(isset($limit)) $parameters['max-results'] = (int) $limit;
		$parameters['start-index'] = (int) $index;

		// sort if needed
		if($sort !== null) $parameters['sort'] = '-ga:' . $sort;

		// fetch
		$gaResults = self::getGoogleAnalyticsInstance()->getAnalyticsResults($gaMetrics, $startTimestamp, $endTimestamp, 'ga:medium', $parameters);

		// get total pageviews
		$totalPageviews = (isset($gaResults['aggregates']['pageviews']) ? (int) $gaResults['aggregates']['pageviews'] : 0);

		// add entries to items
		foreach($gaResults['entries'] as $entry)
		{
			// get traffic source type
			$trafficSource = $entry['medium'];
			if($trafficSource == '(none)') $trafficSource = 'direct_traffic';
			if($trafficSource == 'organic') $trafficSource = 'search_engines';
			if($trafficSource == 'referral') $trafficSource = 'referring_sites';

			$items[] = array(
				'label' => $trafficSource,
				'value' => $entry['pageviews'],
				'percentage' => ($totalPageviews == 0 ? 0 : number_format(((int) $entry['pageviews'] / $totalPageviews) * 100, 2)) . '%'
			);
		}

		return $items;
	}

	/**
	 * This helperfunction returns a URL.
	 * This URL will redirect us to google (button to grant access)
	 *
	 * @return string
	 */
	public static function loginWithOAuth()
	{
		// We obtain all the settings from the database
		$APISettingsArray = BackendAnalyticsModel::getAPISettings();

		$clientId = $APISettingsArray['client_id'];
		$redirectUri = $APISettingsArray['redirect_uri'];
		$scope = $APISettingsArray['scope'];
		$accessType = $APISettingsArray['access_type'];
		$approvalPrompt = 'force';

		$loginUrl = sprintf(
			'https://accounts.google.com/o/oauth2/auth?scope=%s&redirect_uri=%s&response_type=code&client_id=%s&access_type=%s&approval_prompt=%s',
			$scope,
			$redirectUri,
			$clientId,
			$accessType,
			$approvalPrompt);

		// redirect immediately to google
		return $loginUrl;
	}

	/**
	 * Form for periodpicker
	 *
	 * @param BackendTemplate $tpl The template to parse the period picker in.
	 * @param int $startTimestamp The start timestamp for the google call.
	 * @param int $endTimestamp The end timestamp for the google call.
	 * @param array[optional] $parameters The extra GET parameters to set on redirect.
	 */
	public static function parsePeriodPicker(BackendTemplate $tpl, $startTimestamp, $endTimestamp, $parameters = array())
	{
		$startTimestamp = (int) $startTimestamp;
		$endTimestamp = (int) $endTimestamp;

		// assign
		$tpl->assign('startTimestamp', $startTimestamp);
		$tpl->assign('endTimestamp', $endTimestamp);

		// create form
		$frm = new BackendForm('periodPickerForm');

		// create datepickers
		$frm->addDate('start_date', $startTimestamp, 'range', mktime(0, 0, 0, 1, 1, 2005), time(), 'noFocus');
		$frm->addDate('end_date', $endTimestamp, 'range', mktime(0, 0, 0, 1, 1, 2005), time(), 'noFocus');

		// submitted
		if($frm->isSubmitted())
		{
			// show the form
			$tpl->assign('showForm', true);

			// cleanup fields
			$frm->cleanupFields();

			// shorten fields
			$txtStartDate = $frm->getField('start_date');
			$txtEndDate = $frm->getField('end_date');

			// required fields
			$txtStartDate->isFilled(BL::err('StartDateIsInvalid'));
			$txtEndDate->isFilled(BL::err('EndDateIsInvalid'));

			// dates within valid range
			if($txtStartDate->isFilled() && $txtEndDate->isFilled())
			{
				// valid dates
				if($txtStartDate->isValid(BL::err('StartDateIsInvalid')) && $txtEndDate->isValid(BL::err('EndDateIsInvalid')))
				{
					// get timestamps
					$newStartDate = BackendModel::getUTCTimestamp($txtStartDate);
					$newEndDate = BackendModel::getUTCTimestamp($txtEndDate);

					// init valid
					$valid = true;

					// startdate cannot be before 2005 (earliest valid google startdate)
					if($newStartDate < mktime(0, 0, 0, 1, 1, 2005)) $valid = false;

					// end can not be today or in the future
					elseif($newEndDate >= mktime(0, 0, 0)) $valid = false;

					// enddate cannot be before the startdate
					elseif($newStartDate > $newEndDate) $valid = false;

					// invalid range
					if(!$valid) $txtStartDate->setError(BL::err('DateRangeIsInvalid'));
				}
			}

			if($frm->isCorrect())
			{
				// parameters
				$parameters['start_timestamp'] = $newStartDate;
				$parameters['end_timestamp'] = $newEndDate;

				// build redirect string
				$redirect = html_entity_decode(BackendModel::createURLForAction(null, null, null, $parameters));

				// redirect
				SpoonHTTP::redirect($redirect);
			}
		}

		// parse
		$frm->parse($tpl);
	}

	/**
	 * Set the dates based on GET and SESSION
	 * GET has priority and overwrites SESSION
	 */
	public static function setDates()
	{
		// init vars with session data
		$startTimestamp = (SpoonSession::exists('analytics_start_timestamp') ? SpoonSession::get('analytics_start_timestamp') : null);
		$endTimestamp = (SpoonSession::exists('analytics_end_timestamp') ? SpoonSession::get('analytics_end_timestamp') : null);

		// overwrite with get data if needed
		if(isset($_GET['start_timestamp']) && $_GET['start_timestamp'] != '' && isset($_GET['end_timestamp']) && $_GET['end_timestamp'] != '')
		{
			// get dates
			$startTimestamp = (int) $_GET['start_timestamp'];
			$endTimestamp = (int) $_GET['end_timestamp'];
		}

		// dates are set
		if($startTimestamp > 0 && $endTimestamp > 0)
		{
			// init valid
			$valid = true;

			// check startTimestamp (valid year/month/day)
			if(!checkdate((int) date('n', $startTimestamp), (int) date('j', $startTimestamp), (int) date('Y', $startTimestamp))) $valid = false;

			// check endTimestamp (valid year/month/day)
			elseif(!checkdate((int) date('n', $endTimestamp), (int) date('j', $endTimestamp), (int) date('Y', $endTimestamp))) $valid = false;

			// we have valid formats but we like to dig deeper
			else
			{
				// start needs to be before end
				if($startTimestamp > $endTimestamp) $valid = false;

				// startTimestamp cannot be before 2005 (earliest valid google startdate)
				elseif($startTimestamp < mktime(0, 0, 0, 1, 1, 2005)) $valid = false;

				// end can not be today or in the future
				elseif($endTimestamp >= mktime(0, 0, 0)) $valid = false;
			}

			// valid dates
			if($valid)
			{
				// set sessions
				SpoonSession::set('analytics_start_timestamp', $startTimestamp);
				SpoonSession::set('analytics_end_timestamp', $endTimestamp);
			}
		}

		// dates are not set
		else
		{
			// get interval
			$interval = BackendModel::getModuleSetting('analytics', 'interval', 'month');
			if($interval == 'month') $interval .= ' -1 days';

			// set sessions
			SpoonSession::set('analytics_start_timestamp', strtotime('-1' . $interval, mktime(0, 0, 0)));
			SpoonSession::set('analytics_end_timestamp', mktime(0, 0, 0, date('m'), date('d') -1, date('Y')));
		}
	}
}
