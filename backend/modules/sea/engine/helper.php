<?php
/**
 * Helper file to make the connection with Google Analytics API
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaHelper
{
	/**
	 * This helperfunction returns a URM.
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
			"https://accounts.google.com/o/oauth2/auth?scope=%s&redirect_uri=%s&response_type=code&client_id=%s&access_type=%s&approval_prompt=%s",
			$scope,
			$redirectUri,
			$clientId,
			$accessType,
			$approvalPrompt);

		return $loginUrl;
	}

	/**
	 * With this function we get the OAuth2.0 Token, generated by Google.
	 * This token is stored in the database and in a session
	 *
	 * @param string $code
	 * @return string
	 */
	public static function getOAuth2Token($code, $boolean)
	{
		// We obtain all the settings from the database
		$APISettingsArray = BackendSeaModel::getAPISettings();

		// the base_url for the curl
		$oauth2tokenUrl = "https://accounts.google.com/o/oauth2/token";

		//Normally the client ID and client Secret never changes
		$clienttokenPost = array(
			"client_id"	    =>	$APISettingsArray['client_id'],
			"client_secret" =>	$APISettingsArray['client_secret']
		);

		//If boolean == true, it means that we can access google's data with the refresh code
		//This means we weren't redirected to google anymore
		if($boolean)
		{
			$clienttokenPost["refresh_token"] = $code;
			$clienttokenPost["grant_type"] = "refresh_token";
		}
		else
		{
			$clienttokenPost["code"] = $code;
			$clienttokenPost["redirect_uri"] = $APISettingsArray['redirect_uri'];
			$clienttokenPost["grant_type"] = "authorization_code";
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

		// the returned object should contain at least an access token
		$accessToken = $authObj->access_token;
		$_SESSION['access_token'] = $accessToken;

		if (isset($authObj->refresh_token))
		{
			$refreshToken = $authObj->refresh_token;
			BackendSeaModel::updateTokens($accessToken, $refreshToken);
			return true;
		}

		BackendSeaModel::updateTokens($accessToken, null);
		return true;
	}

	/**
	 * This function checks
	 *	- if the access token is older than 3600 seconds
	 *	- if we already have got a refresh token
	 *
	 * depending on the situation there will be a different action
	 *
	 */
	public static function checkStatus()
	{
		$timestamp = strtotime('+1 hour', BackendSeaModel::getTimeStampAccessToken());
		// stored time + 1 hour is greater than this time => the access token is still up to date
		if($timestamp > BackendModel::getUTCDate())
		{
			// still oke, return true
			return true;
		}
		else
		{
			$APISettingsArray = BackendSeaModel::getAPISettings();
			if(isset($APISettingsArray['refresh_token']) && $APISettingsArray['refresh_token'] != null)
			{
				if(BackendSeaHelper::getOAuth2Token($APISettingsArray['refresh_token'], true))
				{
					// it's up to date again, return true
					return true;
				}
			}
			else
			{
				// first time, so no refresh or access token, return false (need auth with google)
				return false;
			}
		}
	}

}
