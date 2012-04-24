<?php
/**
 * Helper file to make the connection with Google Analytics API
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaHelper
{
    /**
     * At the moment we will still hardcode the loginvalues (to test)Later on i'll refactor the code
     *
     */
    public static function loginWithOAuth()
    {
	    //Values from Google Console AP, source: https://code.google.com/apis/console/
	    $client_id = "464206228211.apps.googleusercontent.com";
	    $client_secret = "xxx";
	    $redirect_uri = "http://localhost/private/en/sea/validate";
	    $scope = "https://www.googleapis.com/auth/analytics.readonly";

	    $loginUrl = sprintf("https://accounts.google.com/o/oauth2/auth?scope=%s&redirect_uri=%s&response_type=code&client_id=%s", $scope, $redirect_uri, $client_id);

	    return $loginUrl;
    }
}
