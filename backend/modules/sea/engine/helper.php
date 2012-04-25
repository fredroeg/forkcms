<?php
/**
 * Helper file to make the connection with Google Analytics API
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaHelper
{
	/**
	 *
	 * @return string
	 */
	public static function loginWithOAuth()
	{
		$APISettingsArray = BackendSeaModel::getAPISettings();

		$client_id = $APISettingsArray['client_id'];
		$redirect_uri = $APISettingsArray['redirect_uri'];
		$scope = $APISettingsArray['scope'];

		$loginUrl = sprintf(
			"https://accounts.google.com/o/oauth2/auth?scope=%s&redirect_uri=%s&response_type=code&client_id=%s",
			$scope,
			$redirect_uri,
			$client_id);

		return $loginUrl;
	}

	/**
	 *
	 *
	 * @param string $code
	 * @return string
	 */
	public static function getOAuth2Token($code)
	{
		$APISettingsArray = BackendSeaModel::getAPISettings();

		$oauth2tokenUrl = "https://accounts.google.com/o/oauth2/token";

		$clienttokenPost = array(
				    "code"	    =>	$code,
				    "client_id"	    =>	$APISettingsArray['client_id'],
				    "client_secret" =>	$APISettingsArray['client_secret'],
				    "redirect_uri"  =>	$APISettingsArray['redirect_uri'],
				    "grant_type"    =>	"authorization_code"
					);

		$curl = curl_init($oauth2tokenUrl);

		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $clienttokenPost);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$json_response = curl_exec($curl);

		curl_close($curl);

		$authObj = json_decode($json_response);

		/*if (isset($authObj->refresh_token))
		{
			//refresh token only granted on first authorization for offline access
			//save to db for future use (db saving not included in example)
			global $refreshToken;
			$refreshToken = $authObj->refresh_token;
		}*/

		$accessToken = $authObj->access_token;
		$_SESSION['access_token'] = $accessToken;

		BackendSeaModel::updateAccessToken($accessToken);

		return true;
	}
}
