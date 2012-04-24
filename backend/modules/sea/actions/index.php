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
		$this->makeConnection();
		$this->parse();
		$this->display();
	}

	protected function parse()
	{

	}

	private function makeConnection()
	{
	    $url = BackendSeaHelper::loginWithOAuth();
	    $this->tpl->assign('login', $url);
	}
}
