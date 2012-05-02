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
		if (!$redirect)
		{
			$this->redirect('connect');
		}
		else
		{
			$this->redirect('showdata');
		}
	}
}
