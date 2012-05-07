<?php

/**
 * This is the validate-action
 * this page will only be accessed during a redirect from Google
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

	/**
	 * Get the redirected code.
	 * With that code we can obtain the OAuth2.0 token
	 */
	private function validateOAuth()
	{
		// Oauth 2.0: exchange token for access-token in the db so multiple calls can be made to api
		if(isset($_GET['code']))
		{
			if(BackendSeaHelper::getOAuth2Token($_GET['code'],false))
			{
				$this->redirect('connect');
			}
		}
		else
		{
			$this->redirect('connect');
		}
	}
}
