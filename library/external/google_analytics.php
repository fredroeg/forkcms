<?php

/**
 * GoogleAnalytics class
 *
 * This source file can be used to communicate with Google via AuthSub (http://google.com)
 *
 * The class is documented in the file itself. If you find any bugs help me out and report them. Reporting can be done by sending an email to annelies@netlash.com
 * If you report a bug, make sure you give me enough information (include your code).
 *
 * Based on the classes of Tijs Verkoyen (http://classes.verkoyen.eu).
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 */
class GoogleAnalytics
{
	// internal constant to enable/disable debugging
	const DEBUG = false;

	// api url
	const API_URL = 'https://www.googleapis.com/analytics/v3';

	/**
	 * cURL instance
	 *
	 * @var	resource
	 */
	private $curl;

	/**
	 * The session token
	 *
	 * @var	string
	 */
	private $sessionToken = null;

	/**
	 * The table id
	 *
	 * @var	string
	 */
	private $tableId = null;

	/**
	 * Creates an instance of GoogleAnalytics, setting the session token and table id.
	 *
	 * @param string[optional] $sessionToken		The session token to make calls with.
	 * @param string[optional] $tableId			The table id to get data from.
	 * @return void
	 */
	public function __construct($sessionToken = null, $tableId = null)
	{
		$this->setSessionToken($sessionToken);
		$this->setTableId($tableId);
	}

	/**
	 * Destroy cURL instance.
	 *
	 * @return void
	 */
	public function __destruct()
	{
		if($this->curl != null) curl_close($this->curl);
	}

	/**
	 * Make a call to the given URL with the given token.
	 *
	 * @param string $URL		The url to call.
	 * @param string $token		The token to call with.
	 * @return string
	 */
	private function doCall($URL, $token)
	{
		// redefine parameters
		$URL = (string) $URL;
		$token = (string) $token;

		$this->curl = curl_init($URL);

		// set options
		curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		$curlheader[0] = "Authorization: Bearer " . $this->sessionToken;
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $curlheader);

		// execute
		$response = curl_exec($this->curl);
		$headers = curl_getinfo($this->curl);

		// fetch errors
		$errorNumber = curl_errno($this->curl);
		$errorMessage = curl_error($this->curl);

		// invalid headers
		if($headers['http_code'] == 401)
		{
			// return special code
			return 'UNAUTHORIZED';
		}

		// invalid headers
		if(!in_array($headers['http_code'], array(0, 200)))
		{
			// should we provide debug information
			if(self::DEBUG)
			{
				// open pre
				echo '<pre>';

				// dump the header-information
				var_dump($headers);

				// dump the raw response
				var_dump($response);

				// close pre
				echo '</pre>';

				// stop the script
				exit;
			}

			// throw error
			throw new GoogleAnalyticsException($response, (int) $headers['http_code']);
		}

		// error?
		if($errorNumber != '') throw new GoogleAnalyticsException($errorMessage, $errorNumber);
		// return response (json-string)
		return $response;
	}

	/**
	 * Get all website profiles and their account(s).
	 *
	 * @param string $sessionToken	The session token to get accounts from.
	 * @return mixed
	 */
	public function getAnalyticsAccountList($sessionToken)
	{
		// try to make the call
		try
		{
			$response = $this->doCall(self::API_URL . '/management/accounts/~all/webproperties/~all/profiles', $sessionToken);
		}

		// catch possible exception
		catch(Exception $e)
		{
			return array();
		}

		return json_decode($response);
	}

	/**
	 * Makes a call to Google.
	 *
	 * @param mixed $metrics			The metrics as string or as array.
	 * @param int $startTimestamp			The start date from where data must be collected.
	 * @param int $endTimestamp			The end date to where data must be collected.
	 * @param mixed[optional] $dimensions		The optional dimensions as string or as array.
	 * @param array[optional] $parameters		The extra parameters for google.
	 * @return array
	 */
	public function getAnalyticsResults($metrics, $startTimestamp, $endTimestamp, $dimensions = array(), array $parameters = array())
	{
		// check required parameters
		if(!isset($this->sessionToken, $this->tableId, $metrics, $startTimestamp, $endTimestamp)) return array('aggregates' => array(), 'entries' => array());

		// redefine parameters
		$metrics = (array) $metrics;
		$startDate = $startTimestamp;
		$endDate = $endTimestamp;
		$dimensions = (array) $dimensions;
		$parameters = (array) $parameters;

		// build url
		$URL = self::API_URL . '/data/ga?ids=ga:' . $this->tableId;
		$URL .= '&metrics=' . implode(',', $metrics);
		$URL .= '&start-date=' . $startDate;
		$URL .= '&end-date=' . $endDate;
		$URL .= '&dimensions=' . implode(',', $dimensions);

		// add parameters
		if(count($parameters) > 0)
		{
			// loop them and combine key and urlencoded value (but don't encode the colons)
			foreach($parameters as $key => $value) $parameters[$key] = $key . '=' . str_replace(array('%3A', '%3D%3D'), array(':', '=='), urlencode($value));

			// append to array
			$URL .= '&' . implode('&', $parameters);
		}

		// do the call
		$result = $this->doCall($URL, $this->sessionToken);

		// unauthorized
		if($result == 'UNAUTHORIZED') return $result;

		$decodedResult = json_decode($result);

		// init vars
		$results = array();

		// results total, start index and items per page
		$results['totalResults'] = (array) $decodedResult->totalsForAllResults;
		$startIndex = 'start-index';
		$results['startIndex'] = (int) $decodedResult->query->$startIndex;
		$results['itemsPerPage'] = (int) $decodedResult->itemsPerPage;

		// start and end date
		$results['startDate'] = $startDate;
		$results['endDate'] = $endDate;

		$value = array();
		foreach($decodedResult->columnHeaders as $index => $header)
		{
			$value[$index] = str_replace('ga:', '', $header->name);
		}
		foreach($decodedResult->rows as $index => $row)
		{
			foreach($row as $key => $rowVal)
			{
			    $results['aggregates'][$index][$value[$key]] = $rowVal;
			}
		}

		// return the result
		return $results;
	}

	/**
	 * Get a session token based on a one-time token.
	 *
	 * @param string $oneTimeToken	The one-time token to get a session token with.
	 * @return string
	 */
	public function getSessionToken($oneTimeToken)
	{
		// make the call
		$response = $this->doCall('https://www.google.com/accounts/AuthSubSessionToken', $oneTimeToken);

		// a token is given in the response - save it
		if(preg_match('/Token=(.*)/', $response, $matches)) $sessionToken = $matches[1];

		// no token was given - throw an exception
		else throw new GoogleAnalyticsException($response);

		// return the session token
		return $sessionToken;
	}

	/**
	 * Gets the table id
	 *
	 * @return string
	 */
	public function getTableId()
	{
		return $this->tableId;
	}

	/**
	 * Set the session token to make calls with
	 *
	 * @param string $sessionToken	The session token to make calls with.
	 * @return void
	 */
	public function setSessionToken($sessionToken)
	{
		$this->sessionToken = (isset($sessionToken) ? (string) $sessionToken : null);
	}

	/**
	 * Set the table id to get data from
	 *
	 * @param string $tableId		The table id from which data is received.
	 * @return void
	 */
	public function setTableId($tableId)
	{
		$this->tableId = (isset($tableId) ? (string) $tableId : null);
	}
}

/**
 * GoogleAnalyticsException class
 *
 * @author Annelies Van Extergem <annelies@netlash.com>
 */
class GoogleAnalyticsException extends Exception
{
	/**
	 * Http header-codes
	 *
	 * @var	array
	 */
	private $aStatusCodes = array(100 => 'Continue',
									101 => 'Switching Protocols',
									200 => 'OK',
									201 => 'Created',
									202 => 'Accepted',
									203 => 'Non-Authoritative Information',
									204 => 'No Content',
									205 => 'Reset Content',
									206 => 'Partial Content',
									300 => 'Multiple Choices',
									301 => 'Moved Permanently',
									301 => 'Status code is received in response to a request other than GET or HEAD, the user agent MUST NOT automatically redirect the request unless it can be confirmed by the user, since this might change the conditions under which the request was issued.',
									302 => 'Found',
									302 => 'Status code is received in response to a request other than GET or HEAD, the user agent MUST NOT automatically redirect the request unless it can be confirmed by the user, since this might change the conditions under which the request was issued.',
									303 => 'See Other',
									304 => 'Not Modified',
									305 => 'Use Proxy',
									306 => '(Unused)',
									307 => 'Temporary Redirect',
									400 => 'Bad Request',
									401 => 'Unauthorized',
									402 => 'Payment Required',
									403 => 'Forbidden',
									404 => 'Not Found',
									405 => 'Method Not Allowed',
									406 => 'Not Acceptable',
									407 => 'Proxy Authentication Required',
									408 => 'Request Timeout',
									409 => 'Conflict',
									411 => 'Length Required',
									412 => 'Precondition Failed',
									413 => 'Request Entity Too Large',
									414 => 'Request-URI Too Long',
									415 => 'Unsupported Media Type',
									416 => 'Requested Range Not Satisfiable',
									417 => 'Expectation Failed',
									500 => 'Internal Server Error',
									501 => 'Not Implemented',
									502 => 'Bad Gateway',
									503 => 'Service Unavailable',
									504 => 'Gateway Timeout',
									505 => 'HTTP Version Not Supported');


	/**
	 * Class constructor.
	 *
	 * @param string[optional] $message	The errormessage.
	 * @param int[optional] $code		The errornumber.
	 * @return void
	 */
	public function __construct($message = null, $code = null)
	{
		// set message
		if($message === null && isset($this->aStatusCodes[(int) $code])) $message = $this->aStatusCodes[(int) $code];

		// call parent
		parent::__construct((string) $message, $code);
	}
}