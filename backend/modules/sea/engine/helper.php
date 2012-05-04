<?php

/**
 * Helper file to make the connection with Google Analytics API
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaHelper
{
	/**
	 * Depending on the current state, we have to renew the access token and redirect to a certain page
	 * @return boolean
	 */
	public static function checkStatus()
	{
		$APISettingsArray = BackendSeaModel::getAPISettings();
		if(($APISettingsArray['client_id'] != '') && ($APISettingsArray['client_secret'] != '') && ($APISettingsArray['table_id'] != ''))
		{
			$accessTCSession = SpoonSession::get('accessTokenCreated');
			if(!isset($accessTCSession))
			{
				return self::renewAccessToken();
			}
			elseif(time() - $accessTCSession > 3600)
			{
				return self::renewAccessToken();
			}
			elseif(($APISettingsArray['access_token'] == '') || ($APISettingsArray['refresh_token'] == ''))
			{
				return self::renewAccessToken();
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
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
		$APISettingsArray = BackendSeaModel::getAPISettings();

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
			$url = BackendModel::createURLForAction('connect') . '&error=' . $authObj->error;
			SpoonHTTP::redirect($url);
		}

		// the returned object should contain at least an access token
		$accessToken = $authObj->access_token;

		if(isset($authObj->refresh_token))
		{
			$refreshToken = $authObj->refresh_token;
			BackendSeaModel::updateTokens($accessToken, $refreshToken);
			return true;
		}

		BackendSeaModel::updateTokens($accessToken);
		SpoonSession::set('accessTokenCreated', time());
		return true;
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
		$APISettingsArray = BackendSeaModel::getAPISettings();

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
		spoonHTTP::redirect($loginUrl);
	}

	/**
	 * function to check whether there is a refresh token
	 * then renew the access token or authenticate with Google
	 *
	 * @return boolean
	 */
	private static function renewAccessToken()
	{
		$APISettingsArray = BackendSeaModel::getAPISettings();
		if($APISettingsArray['refresh_token'] != '')
		{
			if(BackendSeaHelper::getOAuth2Token($APISettingsArray['refresh_token'], true))
			{
				SpoonSession::set('accessTokenCreated', time());
				// it's up to date again, return true
				return true;
			}
		}
		else
		{
			// first time, so no refresh or access token --> (need auth with google)
			self::loginWithOAuth();
		}
	}

}
