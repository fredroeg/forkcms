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
		$this->checkStatus();
		$this->parse();
		$this->display();
	}

	private function checkStatus()
	{
		$redirect = BackendSeaHelper::checkStatus();
		if($redirect != false)
		{
			$this->redirect('showdata');
		}
	}

	public function display($template = null)
	{
		parent::display($template);
		$url = BackendSeaHelper::loginWithOAuth();
		$this->tpl->assign('login', $url);
	}
}
