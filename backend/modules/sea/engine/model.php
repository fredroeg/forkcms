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
	 * Update the access token we achieved from Google
	 *
	 * @param string $accessToken
	 * @return boolean
	 */
	public static function updateAccessToken($accessToken)
	{
		BackendModel::getDB()->update('sea_settings', array('value' => $accessToken), 'name = ?', 'access_token');
		return true;
	}
}
