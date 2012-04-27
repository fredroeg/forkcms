<?php

/**
 * This is the showdata-action
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaShowdata extends BackendSeaBase
{
	public function execute()
	{
		parent::execute();
		$this->checkStatus();
		$this->seaDataDump();
		$this->parse();
		$this->display();
	}

	protected function parse()
	{
		parent::parse();
	}

	private function checkStatus()
	{
		$redirect = BackendSeaHelper::checkStatus();
		if(!$redirect)
		{
			$this->redirect('index');
		}
	}

	private function seaDataDump()
	{
		//Define the period
		$startTimestamp = date('Y-m-d', SpoonSession::get('sea_start_timestamp'));
		$endTimestamp = date('Y-m-d', SpoonSession::get('sea_end_timestamp'));
		$period = array($startTimestamp, $endTimestamp);

		//Check if we already stored the data for that period in the database. (if not -> insert it!)
		if(BackendSeaModel::checkPeriod($period))
		{
		    var_dump("ja");
		}
		else
		{
			if(BackendSeaHelp::getAllData($period))
			{
			    var_dump("nee");
			}
		}
	}
}
