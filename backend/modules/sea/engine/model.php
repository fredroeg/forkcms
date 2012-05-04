<?php

/**
 * In this file we store all generic functions that we will be using in the sea module
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaModel
{
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
			 FROM sea_period
			 WHERE period_start = ? AND period_end = ?', $period
		);
		$return = ($numRows > 0) ? (true) : (false);
		return $return;
	}

	/**
	 * Delete the profile id
	 */
	public static function deleteProfileId()
	{
		BackendModel::getDB()->update('sea_settings', array('value' => ''), 'name = ?', 'table_id');
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
			 FROM sea_settings'
		 );
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
			 FROM sea_goals'
		);
	}

	/**
	 * Get 1 metric per day
	 *
	 * @param string $metric
	 * @param string $startTimestamp
	 * @param string $endTimestamp
	 * @return array
	 */
	public static function getMetricPerDay($metric, $startTimestamp, $endTimestamp)
	{
		return (array) BackendModel::getDB()->getPairs(
			'SELECT day, ' . $metric . '
			 FROM sea_day_data
			 WHERE day >= ? AND day <= ?',
			 array($startTimestamp, $endTimestamp)
		);
	}

	/**
	 * Get multiple metrics per day
	 *
	 * @param array $metrics
	 * @param string $startTimestamp
	 * @param string $endTimestamp
	 * @return array
	 */
	public static function getMetricsPerDay($metrics, $startTimestamp, $endTimestamp)
	{
		return (array) BackendModel::getDB()->getRecords(
			'SELECT day, ' . implode(",", $metrics) . '
			 FROM sea_day_data
			 WHERE day >= ? AND day <= ?',
			 array($startTimestamp, $endTimestamp)
		);
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
			 FROM sea_period
			 WHERE period_start = ? AND period_end = ?',
			 $period
		);
	}

	/**
	 * Get the SEA Data within a period
	 *
	 * @param int $periodId
	 * @return array
	 */
	public static function getSEAData($periodId)
	{
		return (array) BackendModel::getDB()->getRecord(
			'SELECT *
			 FROM sea_period_data
			 WHERE period_id = ?',
			 $periodId
		);
	}

	/**
	 * Function to get the timestamp of the token.
	 * It's important to check if the access token is still valid
	 *
	 * @return string
	 */
	public static function getTimeStampAccessToken()
	{
		return (string) BackendModel::getDB()->getVar(
			'SELECT UNIX_TIMESTAMP(date) AS date
			 FROM sea_settings
			 WHERE name = ?', 'access_token'
		 );
	}

	/**
	 * Insert the data in the database
	 *
	 * @param int $period
	 * @param array $seaData
	 * @return boolean
	 */
	public static function insertSEAData($period, $seaData)
	{
		// first we insert our period
		$periodId = BackendModel::getDB()->insert('sea_period', array('period_start' => $period[0], 'period_end' => $period[1]));

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

		BackendModel::getDB()->insert('sea_period_data', $data);

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
			    'INSERT IGNORE INTO sea_day_data (day, cost, visits, impressions, clicks, click_through_rate, cost_per_click, cost_per_mimpressions, conversions, conversion_percentage, cost_per_conversion)
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
			$query = 'INSERT IGNORE INTO sea_goals (goal_name) VALUES (:goal_name)';

			$record['goal_name'] = $goal;

			BackendModel::getDB()->execute($query, $record);
		}
	}

	/**
	 * Truncate the tables
	 */
	public static function truncateTables()
	{
		BackendModel::getDB()->truncate(array('sea_period_data', 'sea_period', 'sea_day_data', 'sea_goals'));
	}

	/**
	 * Update the client-id en client-id-secret
	 *
	 * @param array $values
	 * @return boolean
	 */
	public static function updateIds($values)
	{
		$datetime = BackendModel::getUTCDate();
		foreach($values as $name => $value)
		{
			BackendModel::getDB()->update('sea_settings', array('value' => $value, 'date' => $datetime), 'name = ?', $name);
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
		BackendModel::getDB()->update('sea_settings', array('value' => $accessToken, 'date' => $datetime), 'name = ?', 'access_token');
		if(isset($refreshToken))
		{
			BackendModel::getDB()->update('sea_settings', array('value' => $refreshToken, 'date' => $datetime), 'name = ?', 'refresh_token');
		}
		return true;
	}
}
