<?php

/**
 * This is the showdata-action
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaShowdata extends BackendBaseActionIndex
{
	public function execute()
	{
		parent::execute();
		$this->seaDataDump();
		$this->parse();
		$this->display();
	}

	protected function parse()
	{
		parent::parse();
	}

	private function seaDataDump()
	{
		//Define the period
		$startTimestamp = '2012-03-01';
		$endTimestamp = '2012-03-31';
		$period = array($startTimestamp, $endTimestamp);

		//Check if we already stored the data for that period in the database. (if not -> insert it!)
		if(BackendSeaModel::checkPeriod($period))
		{
			spoon::dump('We already stored this data in the database');
		}
		else
		{
			$seaData = BackendSeaHelp::getAllData($period);
			spoon::dump($seaData);
		}
	}
}
