<?php

/**
 * This is the connect-action (default)
 * When the user uses this module for the first time, he has to provide the necessary id's and tokens
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaIndex extends BackendBaseActionIndex
{
	public function execute()
	{
		parent::execute();
		$this->tempFunction();
		$this->checkStatus();
		$this->parse();
		$this->display();
	}

	private function checkStatus()
	{
		$redirect = BackendSeaHelper::checkStatus();
		if($redirect != false)
		{
			//$this->redirect('showdata');
		}
	}

	private function tempFunction()
	{

	}

	public function display($template = null)
	{
		parent::display($template);
		$url = BackendSeaHelper::loginWithOAuth();
		$this->tpl->assign('login', $url);
	}
}
