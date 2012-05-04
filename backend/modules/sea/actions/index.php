<?php

/**
 * This is the index-action (default)
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaIndex extends BackendBaseActionIndex
{
	/**
	 * Depending on the state of the tokens & id's there is a different redirect
	 */
	private function checkStatus()
	{
		$redirect = BackendSeaHelper::checkStatus();
		if(!$redirect)
		{
			$this->redirect('connect');
		}
		else
		{
			$this->redirect('showdata');
		}
	}

	public function execute()
	{
		parent::execute();
		$this->checkStatus();
		$this->parse();
		$this->display();
	}
}
