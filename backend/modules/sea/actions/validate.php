<?php

/**
 * This is the validate-action (default)
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaValidate extends BackendBaseActionIndex
{
	private $clientId = "464206228211.apps.googleusercontent.com";
	private $clientSecret = "xxx";
	private $redirectUri = "http://localhost/private/en/sea/validate";
	private $scope = "https://www.googleapis.com/auth/analytics.readonly";
	private $accessType = "offline";

	public function execute()
	{
		parent::execute();
		$this->validateOAuth();
		$this->showFields();
		$this->parse();
		$this->display();
	}

	protected function parse()
	{

	}

	private function validateOAuth()
	{
		//Oauth 2.0: exchange token for session token so multiple calls can be made to api
		if(!$_SESSION['accessToken'])
		{
		    if(isset($_REQUEST['code']))
		    {
			    $_SESSION['accessToken'] = $this->getOAuth2Token($_REQUEST['code']);
		    }
		}
	}

	private function getOAuth2Token($code)
	{
		$oauth2tokenUrl = "https://accounts.google.com/o/oauth2/token";
		$clienttokenPost = array(
		"code" => $code,
		"client_id"	    => $this->clientId,
		"client_secret" => $this->clientSecret,
		"redirect_uri"  => $this->redirectUri,
		"grant_type"    => "authorization_code"
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

		if (isset($authObj->refresh_token))
		    {
			//refresh token only granted on first authorization for offline access
			//save to db for future use (db saving not included in example)
			global $refreshToken;
			$refreshToken = $authObj->refresh_token;
		}

		$accessToken = $authObj->access_token;

		return $accessToken;
	}

	private function showFields()
	{
		$analyticsObj = $this->callApi($_SESSION['accessToken'], "https://www.googleapis.com/analytics/v3/data/ga?ids=ga:51537978&start-date=2011-10-01&end-date=2012-10-31&metrics=ga:visits,ga:bounces&dimensions=ga:keyword");

		//$totals = (array) $analyticsObj->totalsForAllResults;
		$totals = (array) $analyticsObj;
		spoon::dump($totals);
		//$this->tpl->assign('fields', $totals);
	}

	private function callApi($accessToken, $url)
	{
		$curl = curl_init($url);

		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$curlheader[0] = "Authorization: Bearer " . $accessToken;
		curl_setopt($curl, CURLOPT_HTTPHEADER, $curlheader);

		$json_response = curl_exec($curl);
		curl_close($curl);

		$responseObj = json_decode($json_response);

		return $responseObj;
	}
}
