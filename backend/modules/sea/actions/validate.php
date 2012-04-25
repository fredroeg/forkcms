<?php

/**
 * This is the validate-action (default)
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaValidate extends BackendBaseActionIndex
{
	public function execute()
	{
		parent::execute();
		$this->validateOAuth();
	}

	private function validateOAuth()
	{
		//Oauth 2.0: exchange token for token in the db so multiple calls can be made to api
		if(isset($_REQUEST['code']))
		{
		    if(BackendSeaHelper::getOAuth2Token($_REQUEST['code'], false))
		    {
			$this->redirect('showdata');
		    }
		}
	}
}
