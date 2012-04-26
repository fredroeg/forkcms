<?php
/**
 * In this file we store all generic functions that we will be using in the sea module
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaModel
{
	/**
	 * Get all the authentication settings to access the Google API's
	 *
	 * @return array
	 */
	public static function getAPISettings()
	{
		$APISettings = BackendModel::getDB()->getPairs(
			'SELECT name, value
			 FROM sea_settings
			    ');
		return $APISettings;
	}

	/**
	 * Function to get the timestamp of the token.
	 * It's important to check if the access token is still valid
	 *
	 * @return timestamp
	 */
	public static function getTimeStampAccessToken()
	{
		$timeStampAT = BackendModel::getDB()->getVar(
			'SELECT timestamp
			 FROM sea_settings
			 WHERE name = ?', 'access_token'
			 );
		return $timeStampAT;
	}

	/**
	 * Update the access token (and the refresh token) we achieved from Google
	 *
	 * @param string $accessToken
	 * @return boolean
	 */
	public static function updateTokens($accessToken, $refreshToken)
	{
		BackendModel::getDB()->update('sea_settings', array('value' => $accessToken), 'name = ?', 'access_token');
		if($refreshToken != null)
		{
		    BackendModel::getDB()->update('sea_settings', array('value' => $refreshToken), 'name = ?', 'refresh_token');
		}
		return true;
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
		$periodId = BackendModel::getDB()->insert('sea_period', array('period_start' => $period[0], 'period_end' => $period[1]));

		$data['period_id'] = $periodId;
		$data['visits'] = $seaData['visits'];
		$data['impressions_amount'] = $seaData['impressions'];
		$data['clicks_amount'] = $seaData['adClicks'];
		$data['click_through_rate'] = $seaData['CTR'];
		$data['cost_per_click'] = $seaData['CPC'];
		$data['cost_total'] = $seaData['costs'];

		BackendModel::getDB()->insert('sea_data', $data);

		return true;
	}

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
}
