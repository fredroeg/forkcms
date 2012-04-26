<?php
/**
 * Help class to make our life easier
 * Based on the class from Annelies
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaHelp
{

	/**
	 * Get Google Analytics SEA instance
	 *
	 * @return GoogleAnalyticsSea
	 */
	public static function getGoogleAnalyticsInstance()
	{
		$APIsettingArray = BackendSeaModel::getAPISettings();

		$accessToken = $APIsettingArray['access_token'];
		$tableId = $APIsettingArray['table_id'];

		// require the GoogleAnalytics class
		require_once 'external/google_analytics_sea.php';

		// get and return an instance
		return new GoogleAnalyticsSea($accessToken, $tableId);
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
	public static function getKeywords($metrics, $startTimestamp, $endTimestamp, $dimensions, $sort = null, $limit = null, $index = 1)
	{
		$metrics = (array) $metrics;

		// set metrics
		$gaMetrics = array();
		foreach($metrics as $metric) $gaMetrics[] = 'ga:' . $metric;

		// set metrics
		$gaDimensions = array();
		foreach($dimensions as $dimension) $gaDimensions[] = 'ga:' . $dimension;

		// set parameters
		$parameters = array();
		//if(isset($limit)) $parameters['max-results'] = (int) $limit;
		//$parameters['start-index'] = (int) $index;
		//$parameters['filters'] = 'ga:keyword!=(not set)';
		//$parameters['filters'] = 'ga:campaign!=(not set)';

		//With this filter we only get data from SEA-campaigns
		$parameters['filters'] = 'ga:adCost!=0.0';

		// sort if needed
		//if($sort !== null) $parameters['sort'] = '-ga:' . $sort;

		// fetch and return
		return self::getGoogleAnalyticsInstance()->getAnalyticsResults($gaMetrics, $startTimestamp, $endTimestamp, $gaDimensions, $parameters);
	}
}
