<?php
/**
 * Help class to make our life easier
 * Based on the analytics-class from Annelies
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaHelp
{

	public static function getAllData($period)
	{
		$metrics = array('adCost', 'visits', 'impressions', 'adClicks', 'CTR', 'CPC');
		$dimensions = array('medium', 'date');

		$returnedTestKeywords = self::getSEADataPerDay($metrics, $period, $dimensions);
		$decoded = json_decode($returnedTestKeywords, true);

		//Create a new array to store all our valuable information
		$seaDataArray = array();

		//Total Results
		$seaDataArray['visits'] = $decoded['totalsForAllResults']['ga:visits'];
		//Total Costs
		$seaDataArray['costs'] = $decoded['totalsForAllResults']['ga:adCost'];
		//Total Impressions
		$seaDataArray['impressions'] = $decoded['totalsForAllResults']['ga:impressions'];
		//Total Clicks
		$seaDataArray['adClicks'] = $decoded['totalsForAllResults']['ga:adClicks'];
		//Click-Through-Rate
		$seaDataArray['CTR'] = $decoded['totalsForAllResults']['ga:CTR'];
		//Cost-Per-Click
		$seaDataArray['CPC'] = $decoded['totalsForAllResults']['ga:CPC'];

		//Data per day
		foreach ($decoded['rows'] as $key => $row)
		{
			//Visits per day
			$seaDataArray['dayStats'][$row[1]] = array(
				'cost' => $row[2],
				'visits' => $row[3],
				'impressions' => $row[4],
				'adClicks' => $row[5],
				'CTR' => $row[6],
				'CPC' => $row[7],
			 );
		}


		//Insert this data in the database
		BackendSeaModel::insertSEAData($period, $seaDataArray);

		//temp return voor the dump
		return $seaDataArray;
	}

	/**
	 * Get Google Analytics SEA instance
	 *
	 * @return GoogleAnalyticsSea
	 */
	private static function getGoogleAnalyticsInstance()
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
	 * Get sea-data for a certain period
	 *
	 * @param mixed $metrics	The metrics to get for the keywords.
	 * @param int $period		The period for the google call.
	 * @param string[optional]	$sort The metric to sort on.
	 * @param int[optional]		$limit An optional limit of the number of keywords to get.
	 * @param int[optional]		$index The index to start getting data from.
	 * @return array
	 */
	private static function getSEADataPerDay($metrics, $period, $dimensions, $sort = null, $limit = null, $index = 1)
	{
		//set the timestamps from the period
		$startTimestamp = $period[0];
		$endTimestamp = $period[1];

		$metrics = (array) $metrics;

		// set metrics
		$gaMetrics = array();
		foreach($metrics as $metric) $gaMetrics[] = 'ga:' . $metric;

		// set metrics
		$gaDimensions = array();
		foreach($dimensions as $dimension) $gaDimensions[] = 'ga:' . $dimension;

		// set parameters
		$parameters = array();

		//With this filter we only get data from SEA-campaigns
		$parameters['filters'] = 'ga:adCost!=0.0';

		// fetch and return
		return self::getGoogleAnalyticsInstance()->getAnalyticsResults($gaMetrics, $startTimestamp, $endTimestamp, $gaDimensions, $parameters);
	}
}
