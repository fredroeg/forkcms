<?php

/**
 * This is the index-action (default)
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaIndex extends BackendBaseActionIndex
{
	public function execute()
	{
		parent::execute();
		$this->analyzeTokenPresent();
		$this->parse();
		$this->display();
	}

	/**
	 * This function checks
	 *	- if the access token is older than 3600 seconds
	 *	- if we already have got a refresh token
	 *
	 * depending on the situation there will be a different action
	 *
	 */
	private function analyzeTokenPresent()
	{
	    $timestamp = strtotime('+1 hour', strtotime(BackendSeaModel::getTimeStampAccessToken()));
	    if($timestamp > strtotime('now') && isset($_SESSION['access_token']))
	    {
		    $this->redirect('showdata');
	    }
	    else
	    {
		    $APISettingsArray = BackendSeaModel::getAPISettings();
		    if(isset($APISettingsArray['refresh_token']) && $APISettingsArray['refresh_token'] != null)
		    {
			    if(BackendSeaHelper::getOAuth2Token($APISettingsArray['refresh_token'], true))
			    {
				    $this->redirect('showdata');
			    }
		    }
		    else
		    {
			    $url = BackendSeaHelper::loginWithOAuth();
			    $this->tpl->assign('login', $url);
		    }
	    }
	}
}
