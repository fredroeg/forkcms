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

					// enddate cannot be in the future
					elseif($newEndDate > time()) $valid = false;

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

		// we only allow live data fetching when the end date is today, no point in fetching and older range because it will never change
		if($endTimestamp == mktime(0, 0, 0, date('n'), date('j'), date('Y')))
		{
			// check if this action is allowed
			if(BackendAuthentication::isAllowedAction('loading', 'analytics'))
			{
				// url of current action
				$liveDataUrl = BackendModel::createURLForAction('loading') . '&amp;redirect_action=' . Spoon::get('url')->getAction();

				// page id set
				if(isset($_GET['page_id']) && $_GET['page_id'] != '') $liveDataUrl .= '&amp;page_id=' . (int) $_GET['page_id'];

				// page path set
				if(isset($_GET['page_path']) && $_GET['page_path'] != '') $liveDataUrl .= '&amp;page_path=' . (string) $_GET['page_path'];

				// assign
				$tpl->assign('liveDataURL', $liveDataUrl);
			}
		}
	}

	/**
	 * Set the dates based on GET and SESSION
	 * GET has priority and overwrites SESSION
	 */
	public static function setDates()
	{
		// init vars with session data
		$startTimestamp = (SpoonSession::exists('sea_start_timestamp') ? SpoonSession::get('sea_start_timestamp') : null);
		$endTimestamp = (SpoonSession::exists('sea_end_timestamp') ? SpoonSession::get('sea_end_timestamp') : null);

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

				// end can not be in the future
				elseif($endTimestamp > time()) $valid = false;
			}

			// valid dates
			if($valid)
			{
				// set sessions
				SpoonSession::set('sea_start_timestamp', $startTimestamp);
				SpoonSession::set('sea_end_timestamp', $endTimestamp);
			}
		}

		// dates are not set
		else
		{
			// get interval
			$interval = BackendModel::getModuleSetting('analytics', 'interval', 'month');
			if($interval == 'month') $interval .= ' -1 days';

			// set sessions
			SpoonSession::set('sea_start_timestamp', strtotime('-1' . $interval, mktime(0, 0, 0)));
			SpoonSession::set('sea_end_timestamp', mktime(0, 0, 0));
		}
	}
}
