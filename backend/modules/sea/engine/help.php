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
		// first data collection
		$metrics = array('adCost', 'visits', 'impressions', 'adClicks', 'CTR', 'CPC');
		$dimensions = array('medium', 'date');
		$returnedData = self::getData($metrics, $period, $dimensions);
		$decoded = json_decode($returnedData, true);

		// second data collection
		$metrics = array('goalCompletionsAll', 'goalConversionRateAll', 'costPerConversion');
		$dimensions = array('date');
		$returnedData = self::getData($metrics, $period, $dimensions);
		$decodedConversions = json_decode($returnedData, true);
		//spoon::dump($decodedConversions);

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
		//Conversions
		$seaDataArray['conversions'] = $decodedConversions['totalsForAllResults']['ga:goalCompletionsAll'];
		//Conversion rate (or conv. percentage)
		$seaDataArray['conversion_percentage'] = $decodedConversions['totalsForAllResults']['ga:goalConversionRateAll'];
		//Cost per conversion
		$seaDataArray['cost_per_conversion'] = $decodedConversions['totalsForAllResults']['ga:costPerConversion'];


		//Data per day
		foreach ($decoded['rows'] as $key => $row)
		{
			$seaDataArray['dayStats'][$row[1]] = array(
				'cost' => $row[2],
				'visits' => $row[3],
				'impressions' => $row[4],
				'adClicks' => $row[5],
				'CTR' => $row[6],
				'CPC' => $row[7],
			 );
		}
		foreach ($decodedConversions['rows'] as $key => $row)
		{
			$seaDataArray['dayStats'][$row[0]] += array(
				'conversions' => $row[1],
				'conversion_percentage' => $row[2],
				'cost_per_conversion' => $row[3]
			 );
		}

		//Insert this data in the database
		if(BackendSeaModel::insertSEAData($period, $seaDataArray))
		{
			return true;
		}
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
	private static function getData($metrics, $period, $dimensions = null, $sort = null, $limit = null, $index = 1)
	{
		//set the timestamps from the period
		$startTimestamp = $period[0];
		$endTimestamp = $period[1];

		$metrics = (array) $metrics;

		// set metrics
		$gaMetrics = array();
		foreach($metrics as $metric) $gaMetrics[] = 'ga:' . $metric;

		$gaDimensions = array();
		// set dimensions
		if(isset($dimensions))
		{
			foreach($dimensions as $dimension) $gaDimensions[] = 'ga:' . $dimension;
		}

		// set parameters
		$parameters = array();

		//With this filter we only get data from SEA-campaigns
		$parameters['filters'] = 'ga:adCost!=0.0';

		// fetch and return
		return self::getGoogleAnalyticsInstance()->getAnalyticsResults($gaMetrics, $startTimestamp, $endTimestamp, $gaDimensions, $parameters);
	}
}
